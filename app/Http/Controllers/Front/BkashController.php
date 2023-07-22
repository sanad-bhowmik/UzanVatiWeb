<?php

namespace App\Http\Controllers\Front;

use Auth;
use Session;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\SmsLog;
use App\Models\Product;
use App\Models\Currency;
use App\Classes\DasMailer;
use App\Models\OrderTrack;
use App\Models\Pagesetting;
use App\Models\VendorOrder;
use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Generalsetting;
use App\Models\OrderPayHistory;
use App\Models\UserNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class BkashController extends Controller
{

    private $base_url;

    public function __construct()
    {
       // $this->base_url = 'https://tokenized.sandbox.bka.sh/v1.2.0-beta';
          $this->base_url = 'https://tokenized.pay.bka.sh/v1.2.0-beta';
    }

    public function authHeaders(){
        return array(
            'Content-Type:application/json',
            'Authorization:' .Session::get('bkash_token'),
            'X-APP-Key:'.env('BKASH_CHECKOUT_URL_APP_KEY')
        );
    }

    public function grant()
    {
        $header = array(
                'Content-Type:application/json',
                'username:'.env('BKASH_CHECKOUT_URL_USER_NAME'),
                'password:'.env('BKASH_CHECKOUT_URL_PASSWORD')
                );
        $header_data_json=json_encode($header);

        $body_data = array('app_key'=> env('BKASH_CHECKOUT_URL_APP_KEY'), 'app_secret'=>env('BKASH_CHECKOUT_URL_APP_SECRET'));
        $body_data_json=json_encode($body_data);
       // dd($body_data_json);
        $response = $this->curlWithBody('/tokenized/checkout/token/grant',$header,'POST',$body_data_json);
     //  dd($response);
        $token = json_decode($response)->id_token;
        
        //$this->storeLog('/tokenized/checkout/token/grant',$header,$body_data,$response);

        return $token;
    }
         
    public function curlWithBody($url,$header,$method,$body_data_json){
        $curl = curl_init($this->base_url.$url);
        curl_setopt($curl,CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_POSTFIELDS, $body_data_json);
        curl_setopt($curl,CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

 
    public function store(Request $request){
        if (Session::has('currency')) 
            {
                $curr = Currency::find(Session::get('currency'));
            }
        else
            {
                $curr = Currency::where('is_default','=',1)->first();
            }

        if (!Session::has('cart')) {
            return redirect()->route('front.cart')->with('success',"You don't have any product to checkout.");
         }
    
            if($request->pass_check) {
                $users = User::where('email','=',$request->personal_email)->get();
                if(count($users) == 0) {
                    if ($request->personal_pass == $request->personal_confirm){
                        $user = new User;
                        $user->name = $request->personal_name; 
                        $user->email = $request->personal_email;   
                        $user->password = bcrypt($request->personal_pass);
                        $token = md5(time().$request->personal_name.$request->personal_email);
                        $user->verification_link = $token;
                        $user->affilate_code = md5($request->name.$request->email);
                        $user->email_verified = 'Yes';
                        $user->save();
                        Auth::guard('web')->login($user);                     
                    }else{
                        return redirect()->back()->with('unsuccess',"Confirm Password Doesn't Match.");     
                    }
                }
                else {
                    return redirect()->back()->with('unsuccess',"This Number Already Exist.");  
                }
            }
    
    
         $oldCart = Session::get('cart');
         $cart = new Cart($oldCart);
        foreach($cart->items as $key => $prod)
        {
        if(!empty($prod['item']['license']) && !empty($prod['item']['license_qty']))
        {
                foreach($prod['item']['license_qty']as $ttl => $dtl)
                {
                    if($dtl != 0)
                    {
                        $dtl--;
                        $produc = Product::findOrFail($prod['item']['id']);
                        $temp = $produc->license_qty;
                        $temp[$ttl] = $dtl;
                        $final = implode(',', $temp);
                        $produc->license_qty = $final;
                        $produc->update();
                        $temp =  $produc->license;
                        $license = $temp[$ttl];
                         $oldCart = Session::has('cart') ? Session::get('cart') : null;
                         $cart = new Cart($oldCart);
                         $cart->updateLicense($prod['item']['id'],$license);  
                         Session::put('cart',$cart);
                        break;
                    }                    
                }
        }
        }


        $settings = Generalsetting::findOrFail(1);
        $order = new Order;
        $randomNumber = random_int(1,99) ;
        $item_number = "UZV".(int)Auth::id().time(); // order number
       // dd($item_number);
        $item_amount = $request->total;
      
        $user = User::findOrFail(Auth::id());
        if($user->email==""){
            $user->state =$request->state;
            $user->city = $request->city;
            $user->zip = $request->zip;
            $user->address = $request->address;
            $user->email = $request->email;
            $user->update();
        }
      

       // $item_amount = round($item_amount * ($settings->pay_percent/100));

        $txnid = "BKASH".uniqid().time();
        $order['customer_state'] = $request->state;
        $order['shipping_state'] = $request->shipping_state;
        $order['user_id'] = $request->user_id;
        $order['cart'] = utf8_encode(bzcompress(serialize($cart), 9));
        $order['totalQty'] = $request->totalQty;
       // dd($request->totalQty);
        $wallet = $request->wallet_price;
        $order['pay_amount'] = round($request->total / $curr->value, 2);
        $order['method'] = "BKASH";
        $order['customer_email'] = $request->email;
        $order['customer_name'] = $request->name;
        $order['customer_phone'] = $request->phone;
        $order['order_number'] = $item_number;
        $order['shipping'] = $request->shipping;
        $order['pickup_location'] = $request->pickup_location;
        $order['customer_address'] = $request->address;
        $order['customer_country'] = $request->customer_country;
        $order['customer_city'] = $request->city;
        $order['customer_zip'] = $request->zip;
        $order['shipping_email'] = $request->shipping_email;
        $order['shipping_name'] = $request->shipping_name;
        $order['shipping_phone'] = $request->shipping_phone;
        $order['shipping_address'] = $request->shipping_address;
        $order['shipping_country'] = $request->shipping_country;
        $order['shipping_city'] = $request->shipping_city;
        $order['shipping_zip'] = $request->shipping_zip;
        $order['order_note'] = $request->order_notes;
        $order['coupon_code'] = $request->coupon_code;
        $order['coupon_discount'] = $request->coupon_discount;
        $order['payment_status'] = "pending";
        $order['currency_sign'] = $curr->sign;
        $order['currency_value'] = $curr->value;
        $order['shipping_cost'] = $request->shipping_cost;
        $order['packing_cost'] = $request->packing_cost;
        $order['shipping_title'] = $request->shipping_title;
        $order['packing_title'] = $request->packing_title;
        $order['tax'] = $request->tax;
        $order['dp'] = $request->dp;
       
        $order['vendor_shipping_id'] = $request->vendor_shipping_id;
        $order['vendor_packing_id'] = $request->vendor_packing_id;
        $order['wallet_price'] = round($wallet / $curr->value, 2);

        //bkash
      //  $input = $request->all();


        $redirect_url =url("bkash/notify") ;
        Session::forget('bkash_token');
        $token = $this->grant();
        Session::put('bkash_token', $token);
        $header =$this->authHeaders();

        $body_data = array(
            'mode' => '0011',
            'payerReference' => ' ',
            'callbackURL' => $redirect_url,
            'amount' => $request->total,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $item_number
        );
        $body_data_json=json_encode($body_data);

        $bkashResponse = $this->curlWithBody('/tokenized/checkout/create',$header,'POST',$body_data_json);
      //  dd($response);

        Session::forget('invoiceID');
        Session::put('invoiceID', json_decode($bkashResponse)->merchantInvoiceNumber);
        Session::forget('paymentID');
        Session::put('paymentID', json_decode($bkashResponse)->paymentID);


        //

        $order['txnid'] = json_decode($bkashResponse)->paymentID; 
        $order->save();

         //  dd($order);
        //new by das

        $gs = Generalsetting::first();
     

            if(Auth::check()){
                Auth::user()->update(['balance' => (Auth::user()->balance - $order->wallet_price)]);
            }

            $track = new OrderTrack;
            $track->title = 'Pending';
            $track->text = 'You have successfully placed your order.';
            $track->order_id = $order->id;
            $track->save();
    
            $notification = new Notification;
            $notification->order_id = $order->id;
            $notification->save();


            if($request->coupon_id != "")
            {
                $coupon = Coupon::findOrFail($request->coupon_id);
                $coupon->used++;

                if($coupon->times != null)
                {
                    $i = (int)$coupon->times;
                    $i--;
                    $coupon->times = (string)$i;
                }
                $coupon->update();
            }

            foreach($cart->items as $prod)
            {
                $x = (string)$prod['stock'];
                if($x != null)
                {
                    $product = Product::findOrFail($prod['item']['id']);
                    $product->stock =  $prod['stock'];
                    $product->update();                
                }
            }

            foreach($cart->items as $prod)
            {
                $x = (string)$prod['size_qty'];
                if(!empty($x))
                {
                    $product = Product::findOrFail($prod['item']['id']);
                    $x = (int)$x;
                    $x = $x - $prod['qty'];
                    $temp = $product->size_qty;
                    $temp[$prod['size_key']] = $x;
                    $temp1 = implode(',', $temp);
                    $product->size_qty =  $temp1;
                    $product->update();               
                }
            }

            foreach($cart->items as $prod)
            {
                $x = (string)$prod['stock'];
                if($x != null)
                {
                    $product = Product::findOrFail($prod['item']['id']);
                    $product->stock =  $prod['stock'];
                    $product->update();  
                    if($product->stock <= 5)
                    {
                        $notification = new Notification;
                        $notification->product_id = $product->id;
                        $notification->save();    
                        
                        

                        if($gs->is_sms_notify==1){


                            $msg="One of your product is almost out of stock (less or equal to 5).\nProduct Link:".url('/').'/'.'item/'.$product->slug."\n Product: ".$product->name."";
                           
                                $response = Http::get('http://portal.metrotel.com.bd/smsapi', [
                                    'api_key' => 'R20000475dda47691ef6c0.75040283',
                                    'type' => 'text',
                                    'contacts' =>$product->user->phone,
                                    'senderid' =>'8809612440465',
                                    'msg' => $msg
                                    ]); 
                            
                                   
                                    
                            
                                    SmsLog::create([
                                        'from' => 'VendorStockAlert',
                                        'to' => $product->user->phone,
                                        'message' =>$msg ,
                                        'status'=> $response->body(),
                                        'sent_by'=>"System"
                                        ]
                                      );
                    
                              //  return response()->json( ['data'=>true,'message'=>'sent','success'=>1],200);

                        } //end if sms notify

                       
                    }              
                }
            }


            $notf = null;
            $i=1;
            foreach($cart->items as $prod)
            {
                if($prod['item']['user_id'] != 0 )
                {
                    $vorder =  new VendorOrder;
                    $vorder->order_id = $order->id;
                    $vorder->user_id = $prod['item']['user_id'];
                    $notf[] = $prod['item']['user_id'];
                    $vorder->qty = $prod['qty'];
                    $vorder->price = $prod['price'];
                    $vorder->order_number = $order->order_number;             
                    $vorder->save();
                    if($order->dp == 1){
                        $vorder->user->update(['current_balance' => $vorder->user->current_balance += $prod['price']]);
                    }

                    if($gs->is_sms_notify==1 && $i==1){


                        $msg="Dear Seller,\nAn order has been palced at your shop.\nOrderNo:".$vorder->order_number.".\nTake necessary steps if order is confirmed/paid/cod.\nThanks www.uzanvati.com ";
                           
                        $response = Http::get('http://joy.metrotel.com.bd/smspanel/smsapi', [
                            'api_key' => '$2y$10$UGffpZIkHXi7k1xI5T6KoOSoPahpz8Kj3FvbK05JGQA0h2yr4/b62501',
                            'type' => 'text',
                            'contacts' =>$request->phone,
                            'senderid' =>'8809612440465',
                            'msg' => $msg
                            ]); 
                                    
                                   
                                    
                            
                                    SmsLog::create([
                                        'from' => 'VendorOrderAlert',
                                        'to' => $vorder->user->phone,
                                        'message' =>$msg ,
                                        'status'=> $response->body(),
                                        'sent_by'=>"System"
                                        ]
                                      );
                    
                              //  return response()->json( ['data'=>true,'message'=>'sent','success'=>1],200);

                        } //end if sms notify




                }
                $i++;

            }// end foreach

            if(!empty($notf))
            {
                $users = array_unique($notf);
                foreach ($users as $user) {
                    $notification = new UserNotification;
                    $notification->user_id = $user;
                    $notification->order_number = $order->order_number;
                    $notification->save();    
                }
            }


            $gs = Generalsetting::find(1);


            //Sending Email/sms To Buyer

            if($gs->is_sms_notify==1){


                $msg="Hello ".$request->name."!\nYou have placed a new order.\nYour order number is ".$order->order_number."\nPlease wait for your delivery. \nThank you. www.uzanvati.com";
                
                $response = Http::get('http://joy.metrotel.com.bd/smspanel/smsapi', [
                    'api_key' => '$2y$10$UGffpZIkHXi7k1xI5T6KoOSoPahpz8Kj3FvbK05JGQA0h2yr4/b62501',
                    'type' => 'text',
                    'contacts' =>$request->phone,
                    'senderid' =>'8809612440465',
                    'msg' => $msg
                    ]); 
                
                       
                        
                
                        SmsLog::create([
                            'from' => 'CustomerOrderAlert',
                            'to' => $request->phone,
                            'message' =>$msg ,
                            'status'=> $response->body(),
                            'sent_by'=>"System"
                            ]
                          );
        
                  //  return response()->json( ['data'=>true,'message'=>'sent','success'=>1],200);
            



            }// is notify

            Session::forget('temporder');
            Session::put('temporder',$order);
           
            Session::put('tempcart',$cart);
            Session::forget('cart');
            Session::forget('pickup_text');
            Session::forget('pickup_cost');
            Session::forget('pickup_costshow');
            Session::forget('already');
            Session::forget('coupon');
            Session::forget('coupon_total');
            Session::forget('coupon_total1');
            Session::forget('coupon_percentage');




        // started bkash payment operation
       // $order = Session::get('temporder');
      //  dd($order);
         



        return redirect((json_decode($bkashResponse)->bkashURL));

    

} // end store



//new by das
public function cancel(Request $request)
{
   //dd($request);
    return redirect()->route('front.checkout')->with('unsuccess', 'Payment Cancelled.');
}

public function notify(Request $request){
  
    $success_url = action('Front\PaymentController@payreturn');
    $cancel_url = action('Front\PaymentController@paycancle');

    $input = $request->all();

    //dd($input);
    
    if($input['status'] == 'success'){

        $order = Order::where('order_number',Session::get('invoiceID'))->first();
      // $order = Session::get('temporder');
       // dd($order);
        $checkresponse = $this->execute($input['paymentID']);

         $arr = json_decode($checkresponse,true);
        
     
    
        if(array_key_exists("statusCode",$arr) && $arr['statusCode'] == '0000'){
            
        $data['payment_status'] = 'paid';
        $data['paid_amount'] = $order->pay_amount;
        $data['remain_amount'] = $order->pay_amount - $order->pay_amount;
 
        $data['status'] = 'processing';
        $data['charge_id'] = $arr['paymentID'];
        $data['txnid'] = $arr['trxID'];
        $order->update($data);
        $track = new OrderTrack;
        $track->title = 'Processing';
        $track->text = 'You have successfully placed your order.';
        $track->order_id = $order->id;
        $track->save();

        $track2 = new OrderTrack;
        $track2->title = 'Payment Received (Bkash)';
        $track2->text = "total paid :".$order->pay_amount ;
        $track2->order_id = $order->id;
        $track2->save();

        $notification = new Notification;
        $notification->order_id = $order->id;
        $notification->save();
        $history = new OrderPayHistory();
        $history->order_id = $order->id;
        $history->statusCode = $arr['statusCode'];
        $history->statusMessage = $arr['statusMessage'];
        $history->paymentID = $arr['paymentID'];
        $history->payerReference = $arr['payerReference'];
        $history->response = $arr['transactionStatus'];
        $history->pay_amount = $arr['amount'];
        $history->txn_id =  $arr['trxID'];
        $history->method =  'BKASH'; 
        $history->currency = $arr['currency'];
       // dd(Auth::id()); 
        $history->paid_by = $arr['customerMsisdn'];
        $history->paymentExecuteTime = $arr['paymentExecuteTime'];
        $history->remarks = $arr['intent'];
        $history->order_number = $arr['merchantInvoiceNumber'];
        $history->save();

        
        }else{

        $history = new OrderPayHistory();
        $history->order_id = $order->id;
        $history->statusCode = $arr['statusCode'];
        $history->statusMessage = $arr['statusMessage'];
        $history->save();

        }
    
          


      //  $tempcart = unserialize(bzdecompress(utf8_decode($order->cart)));
       // Session::forget('temporder');
       
       return redirect($success_url);
      // return redirect($success_url)->with(['tempcart' => $tempcart,'temporder' => $order]);
    }else {
        // $order =  Session::get('temporder');
         //$order->delete();
        Session::forget('temporder');
        return redirect($cancel_url);
    }
}
public function execute($paymentID)
{

    $header =$this->authHeaders();

    $body_data = array(
        'paymentID' => $paymentID
    );
    $body_data_json=json_encode($body_data);

    $response = $this->curlWithBody('/tokenized/checkout/execute',$header,'POST',$body_data_json);

    //$this->storeLog('/tokenized/checkout/execute',$header,$body_data,$response);
    // your database operation
    return $response;
}



function paynow(Request $request ){

    $order = Order::where('order_number',$request->invoiceID)->first();
    //dd($order->pay_amount);

    if($order->payment_status=='pending' && $order->method=='BKASH' && $order->status=='pending'){
    Session::forget('orderid');
    Session::put('orderid',$order->id);
    $redirect_url =url("bkash/notify/paynow") ;
    Session::forget('bkash_token');
    $token = $this->grant();
    Session::put('bkash_token', $token);
    $header =$this->authHeaders();

    $body_data = array(
        'mode' => '0011',
        'payerReference' => ' ',
        'callbackURL' => $redirect_url,
        'amount' => $order->pay_amount,
        'currency' => 'BDT',
        'intent' => 'sale',
        'merchantInvoiceNumber' => $order->order_number
    );
    $body_data_json=json_encode($body_data);

    $bkashResponse = $this->curlWithBody('/tokenized/checkout/create',$header,'POST',$body_data_json);
  //  dd($response);

    Session::forget('invoiceID');
    Session::put('invoiceID', json_decode($bkashResponse)->merchantInvoiceNumber);
    Session::forget('paymentID');
    Session::put('paymentID', json_decode($bkashResponse)->paymentID);


    return redirect((json_decode($bkashResponse)->bkashURL));
}else{
   
    $cancel_url = action('Front\PaymentController@paycancle');

    return redirect($cancel_url);
}


}// end pay now

// start notify paynow
public function notifypaynow(Request $request){
  
    

    $input = $request->all();

   // dd($input);
    
    if($input['status'] == 'success'){

     $order = Order::where('order_number',Session::get('invoiceID'))->first();
     $checkresponse = $this->execute($input['paymentID']);

     $arr = json_decode($checkresponse,true);
 
       
    if(array_key_exists("statusCode",$arr) && $arr['statusCode'] == '0000'){
        
    $data['payment_status'] = 'paid';
    $data['paid_amount'] = $order->pay_amount;
    $data['remain_amount'] = $order->pay_amount - $order->pay_amount;

    $data['status'] = 'processing';
    $data['charge_id'] = $arr['paymentID'];
    $data['txnid'] = $arr['trxID'];
    $order->update($data);
    $track = new OrderTrack;
    $track->title = 'Processing';
    $track->text = 'You have successfully placed your order.';
    $track->order_id = $order->id;
    $track->save();

    $track2 = new OrderTrack;
    $track2->title = 'Payment Received (Bkash)';
    $track2->text = "total paid :".$order->pay_amount ;
    $track2->order_id = $order->id;
    $track2->save();

    $notification = new Notification;
    $notification->order_id = $order->id;
    $notification->save();
    $history = new OrderPayHistory();
        $history->order_id = $order->id;
        $history->statusCode = $arr['statusCode'];
        $history->statusMessage = $arr['statusMessage'];
        $history->paymentID = $arr['paymentID'];
        $history->payerReference = $arr['payerReference'];
        $history->response = $arr['transactionStatus'];
        $history->pay_amount = $arr['amount'];
        $history->txn_id =  $arr['trxID'];
        $history->method =  'BKASH'; 
        $history->currency = $arr['currency'];
       // dd(Auth::id()); 
        $history->paid_by = $arr['customerMsisdn'];
        $history->paymentExecuteTime = $arr['paymentExecuteTime'];
        $history->remarks = $arr['intent'];
        $history->order_number = $arr['merchantInvoiceNumber'];
        $history->save();
    
    }else{
      //  dd($arr);
        $history = new OrderPayHistory();
        $history->order_id = $order->id;
        $history->order_number= $order->order_number;
        $history->statusCode = $arr['statusCode'];
        $history->statusMessage = $arr['statusMessage'];
        $history->save();
    }

        
    if($arr['statusCode']=='0000'){
     return redirect()->route('user-order',Session::get('orderid'))->with('success', $arr['statusMessage']);
    }else{

    }return redirect()->route('user-order',Session::get('orderid'))->with('unsuccess', $arr['statusMessage']);
      
     
    }else {
      
        Session::forget('invoiceID');
        return redirect()->route('user-order',Session::get('orderid'))->with('unsuccess','Payment '.$input['status'] );
    }
}

// end paynow







    
 
}