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

class FosterController extends Controller
{
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
                    return redirect()->back()->with('unsuccess',"This Email Already Exist.");  
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
        //making 10% pay only of total
        $user = User::findOrFail(Auth::id());
        if($user->email==""){
            $user->state =$request->state;
            $user->city = $request->city;
            $user->zip = $request->zip;
            $user->address = $request->address;
            $user->email = $request->email;
            $user->update();
        }
      

        $item_amount = round($item_amount * ($settings->pay_percent/100));

        $txnid = "FSTR".uniqid().time();
        $order['customer_state'] = $request->state;
        $order['shipping_state'] = $request->shipping_state;
        $order['user_id'] = $request->user_id;
        $order['cart'] = utf8_encode(bzcompress(serialize($cart), 9));
        $order['totalQty'] = $request->totalQty;
       // dd($request->totalQty);
        $wallet = $request->wallet_price;
        $order['pay_amount'] = round($request->total / $curr->value, 2);
        $order['method'] = "Online/FosterPayment";
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
        $order['payment_status'] = "Pending";
        $order['currency_sign'] = $curr->sign;
        $order['currency_value'] = $curr->value;
        $order['shipping_cost'] = $request->shipping_cost;
        $order['packing_cost'] = $request->packing_cost;
        $order['shipping_title'] = $request->shipping_title;
        $order['packing_title'] = $request->packing_title;
        $order['tax'] = $request->tax;
        $order['dp'] = $request->dp;
        $order['txnid'] = $txnid; 
        $order['vendor_shipping_id'] = $request->vendor_shipping_id;
        $order['vendor_packing_id'] = $request->vendor_packing_id;
        $order['wallet_price'] = round($wallet / $curr->value, 2);
    
        //new by das

        $gs = Generalsetting::first();
            if($order['dp'] == 1)
            {
                $order['status'] = 'completed';
            }
            if (Session::has('affilate')) 
            {
                $val = $request->total / $curr->value;
                $val = $val / 100;
                $sub = $val * $settings->affilate_charge;
                $user = User::findOrFail(Session::get('affilate'));
                if($user){
                    if($order['dp'] == 1)
                    {
                        $user->affilate_income += $sub;
                        $user->update();
                    }

                    $order['affilate_user'] = $user->id;
                    $order['affilate_charge'] = $sub;
                }
            }
            $order->save();

            if(Auth::check()){
                Auth::user()->update(['balance' => (Auth::user()->balance - $order->wallet_price)]);
            }


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

                        // if($gs->is_smtp == 1)
                        // {
                        //     $maildata = [
                        //         'to' => $product->user->email,
                        //         'subject' => 'Out of Stock Alert!',
                        //         'body' => "One of your product is almost out of stock (less or equal to 5).\n<strong>Product Link: </strong> <a target='_blank' href='".url('/').'/'.'item/'.$product->slug."'>".$product->name."</a>",
                        //     ];
                        //     $mailer = new DasMailer();
                        //     $mailer->sendCustomMail($maildata);
                        // }
                        // else
                        // {
                        //     $to = $product->user->email;
                        //     $subject = 'Out of Stock Alert!';
                        //     $msg = "One of your product is almost out of stock (less or equal to 5).\n<strong>Product Link: </strong> <a target='_blank' href='".url('/').'/'.'item/'.$product->slug."'>".$product->name."</a>";
                        //     $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
                        //     mail($to,$subject,$msg,$headers);
                        // }
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
                           
                                $response = Http::get('http://portal.metrotel.com.bd/smsapi', [
                                    'api_key' => 'R20000475dda47691ef6c0.75040283',
                                    'type' => 'text',
                                    'contacts' =>$vorder->user->phone,
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
                
                    $response = Http::get('http://portal.metrotel.com.bd/smsapi', [
                        'api_key' => 'R20000475dda47691ef6c0.75040283',
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



            // if($gs->is_smtp == 1)
            // {
            //     $data = [
            //         'to' => $request->email,
            //         'type' => "new_order",
            //         'cname' => $request->name,
            //         'oamount' => "",
            //         'aname' => "",
            //         'aemail' => "",
            //         'wtitle' => "",
            //         'onumber' => $order->order_number,
            //     ];

            //     $mailer = new DasMailer();
            //     $mailer->sendAutoOrderMail($data,$order->id);            
            // }
            // else
            // {
            //     $to = $request->email;
            //     $subject = "Your Order Placed!!";
            //     $msg = "Hello ".$request->name."!\nYou have placed a new order.\nYour order number is ".$order->order_number.".Please wait for your delivery. \nThank you.";
            //     $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
            //     mail($to,$subject,$msg,$headers);            
            // }
            // //Sending Email To Admin
            // if($gs->is_smtp == 1)
            // {
            //     $data = [
            //         'to' => $gs->header_email,
            //         'subject' => "New Order Recieved!!",
            //         'body' => "Hello Admin!<br>Your store has received a new order.<br>Order Number is ".$order->order_number.".Please login to your panel to check. <br>Thank you.",
            //     ];

            //     $mailer = new DasMailer();
            //     $mailer->sendCustomMail($data);            
            // }
            // else
            // {
            //     $to = $gs->from_email;
            //     $subject = "New Order Recieved!!";
            //     $msg = "Hello Admin!\nYour store has recieved a new order.\nOrder Number is ".$order->order_number.".Please login to your panel to check. \nThank you.";
            //     $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
            //     mail($to,$subject,$msg,$headers);
            // }




            Session::put('tempcart',$cart);
            Session::forget('cart');
            Session::forget('pickup_text');
            Session::forget('pickup_cost');
            Session::forget('pickup_costshow');

            Session::put('temporder',$order);


     # $post_data['multi_card_name'] = "mastercard,visacard,amexcard";  # DISABLE TO DISPLAY ALL AVAILABLE

     $input = $request->all();


    // dd($input);
    
     //foster

     $redirect_url =url("api/foster/notify") ;
     //$redirect_url= action('Front\FosterController@notify');

     //dd($redirect_url);
     $cancel_url = action('Front\FosterController@cancel');
     $fail_url = action('Front\FosterController@cancel');
     $urlparamForHash = http_build_query(array(
         'mcnt_AccessCode' => '190331053509',
         'mcnt_TxnNo' => $txnid, //Ymdhmsu//PNR 
         'mcnt_ShortName' => 'FosterTest',
         'mcnt_OrderNo' => $item_number,
         'mcnt_ShopId' => '104',
         'mcnt_Amount' => $item_amount,
         'mcnt_Currency' => 'BDT'
     ));
     $secretkey = 'b5b50bcefaa3140c5775ed49469983da';
     $secret = strtoupper($secretkey);
     $hashinput = hash_hmac('SHA256', $urlparamForHash, $secret);
    
     $domain =   $_SERVER["SERVER_NAME"]; // or Manually put your domain name  
     $ip =request()->server('SERVER_ADDR');  //domain ip  
    // $ip = "23.227.186.26";
     //echo $ip."======================";

     //dd($hashinput);


     $urlparam = array(
         'mcnt_TxnNo' => $txnid,
         'mcnt_ShortName' => 'FosterTest', //No Need to Change       
         'mcnt_OrderNo' =>   $item_number,
         'mcnt_ShopId' => '104', //No Need to Change 
         'mcnt_Amount' => $item_amount,
         'mcnt_Currency' => 'BDT',
         'cust_InvoiceTo' => $input['name'],
         'cust_CustomerServiceName' => 'E-commarce', //must
         'cust_CustomerName' => $input['name'], //must 
         'cust_CustomerEmail' => $input['email'], //must  
         'cust_CustomerAddress' => $input['address'],
         'cust_CustomerContact' => $input['phone'], //must 
         'cust_CustomerGender' => 'N/A',
         'cust_CustomerCity' => $input['city'], //must 
         'cust_CustomerState' => $input['state'],
         'cust_CustomerPostcode' => $input['zip'],
         'cust_CustomerCountry' => 'Bangladesh',
         'cust_Billingaddress' => 'Bangladesh', //must if not put ‘N/A’ 
         'cust_ShippingAddress' => 'Bangladesh',
         'cust_orderitems' => 1, //must  
         'GW' => '', //optional        
         'CardType' => '', //optional
         'success_url' => $redirect_url, //must   
         'cancel_url' => $cancel_url, //must   
         'fail_url' => $fail_url, //must  
         'emi_amout_per_month' => '', //optional 
         'emi_duration' => '', //optional                           
         'merchentdomainname' => $domain, // must           
         'merchentip' => $ip,
         'mcnt_SecureHashValue' => $hashinput
     );
     $url = 'https://demo.fosterpayments.com.bd/fosterpayments/paymentrequest.php';
    //  $url ='https://payment.fosterpayments.com.bd/fosterpayments/paymentrequest.php';


   // dd($hashinput);

     $data_string = json_encode($urlparam);
     $handle = curl_init();
     curl_setopt($handle, CURLOPT_URL, $url);
     curl_setopt($handle, CURLOPT_TIMEOUT, 30);
     curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
     curl_setopt($handle, CURLOPT_POST, 1);
     curl_setopt($handle, CURLOPT_POSTFIELDS, $data_string);
     curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC
     curl_setopt(
         $handle,
         CURLOPT_HTTPHEADER,
         array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string))
     );
     $content = curl_exec($handle);

     $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);


     // dd($content);
     if ($code == 200 && !(curl_errno($handle))) {
         curl_close($handle);
         $response = $content;
     } else {
         curl_close($handle);
         return redirect()->back()->with('unsuccess', "FAILED TO CONNECT WITH FOSTER API");
         exit;
     }
     
     $responsedate = json_decode($response, true);

    // dd($responsedate);
     //echo $response ;
     $data = $responsedate['data'];
     
     //  die;
     $redirect_url = $data['redirect_url'];
     $payment_id = $data['payment_id'];
     $cancel_url = action('Front\PaymentController@paycancle');


     if($redirect_url==""){
        return redirect($cancel_url);
     }
     
     
     
     $url = $redirect_url . "?payment_id=" . $payment_id;
     //dd($url);
     //header("Location:" . $url);
    echo "<meta http-equiv='refresh' content='0;url=" . $url . "'>";
    exit;
     //dd($url);
     // echo $url;

     //end foster


     } // end store



//new by das
public function cancel(Request $request)
{
   //dd("hello");
    return redirect()->route('front.checkout')->with('unsuccess', 'Payment Cancelled.');
}

public function notify(Request $request){
   // dd("hello");
    $success_url = action('User\OrderController@ordersPayComplete');
   // $success_url = action('Front\PaymentController@payreturn');
   // $success_url = action('User\OrderController@orders');
    $cancel_url = action('Front\PaymentController@paycancle');
    $input = $request->all();
    // dd($input);
    // dd($response);
    if($input['TxnResponse'] == '2'){

        $order = Order::where('order_number',$input['OrderNo'])->first();
        $data['payment_status'] = 'partial_paid';
        $data['paid_amount'] = $input['TxnAmount'];
        $data['remain_amount'] = $order->pay_amount - $input['TxnAmount'];
 
        $data['status'] = 'confirmed';
        $data['charge_id'] = $input['fosterid'];
        

        $order->update($data);

        if($order->wallet_price != 0)
        {
            $user = User::find($order->user_id);
            $user->balance -= $order->wallet_price;
            $user->update();
        }

       
            $track = new OrderTrack;
            $track->title = 'Confirmed';
            $track->text = 'You have successfully placed your order.';
            $track->order_id = $order->id;
            $track->save();

            $track2 = new OrderTrack;
            $track2->title = 'Payment Received';
            $track2->text = $input['TxnAmount'] .$input['Currency']." TXN:".$input['MerchantTxnNo'];
            $track2->order_id = $order->id;
            $track2->save();
            
            $history = new OrderPayHistory();
            $history->order_id = $order->id;
            $history->response = $input['TxnResponse'];
            $history->pay_amount = $input['TxnAmount'];
            $history->txn_id =  $input['MerchantTxnNo']; 
            $history->method =  $input['fosterid']; 
            $history->currency = $input['Currency'];
           // dd(Auth::id()); 
            $history->paid_by =  "Customer";
            $history->remarks = "payment received";
            $history->order_number = $input['OrderNo'];
            $history->save();
       

        // if ($order->user_id != 0 && $order->wallet_price != 0) {
        //     $transaction = new \App\Models\Transaction;
        //     $transaction->txn_number = Str::random(3).substr(time(), 6,8).Str::random(3);
        //     $transaction->user_id = $order->user_id;
        //     $transaction->amount = $order->wallet_price;
        //     $transaction->currency_sign = $order->currency_sign;
        //     $transaction->currency_code = \App\Models\Currency::where('sign',$order->currency_sign)->first()->name;
        //     $transaction->currency_value= $order->currency_value;
        //     $transaction->details = 'Payment Via Wallet';
        //     $transaction->type = 'minus';
        //     $transaction->save();
        // }



        $notification = new Notification;
        $notification->order_id = $order->id;
        $notification->save();

        $tempcart = unserialize(bzdecompress(utf8_decode($order->cart)));
        
       
       return redirect($success_url);
      // return redirect($success_url)->with(['tempcart' => $tempcart,'temporder' => $order]);
    } // if valid
    else {
        $order = Order::where('order_number',$input['OrderNo'])->first();
        $order->delete();
        return redirect($cancel_url);
    }
}


// new buy das store buy now
public function buynowstore(Request $request){
    if (Session::has('currency')) 
        {
            $curr = Currency::find(Session::get('currency'));
        }
    else
        {
            $curr = Currency::where('is_default','=',1)->first();
        }

    if (!Session::has('buynowcart')) {
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
                return redirect()->back()->with('unsuccess',"This Email Already Exist.");  
            }
        }


     $oldCart = Session::get('buynowcart');
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
                     $oldCart = Session::has('buynowcart') ? Session::get('buynowcart') : null;
                     $cart = new Cart($oldCart);
                     $cart->updateLicense($prod['item']['id'],$license);  
                     Session::put('buynowcart',$cart);
                    break;
                }                    
            }
    }
    }


    $settings = Generalsetting::findOrFail(1);
    $order = new Order;
    $randomNumber = random_int(1,9) ;
    $item_number = "UZV".(int)Auth::id().$randomNumber.time(); // order number
    $item_amount = $request->total;
    //making 10% pay only of total
    $user = User::findOrFail(Auth::id());
    if($user->email==""){
        $user->state =$request->state;
        $user->city = $request->city;
        $user->zip = $request->zip;
        $user->address = $request->address;
        $user->email = $request->email;
        $user->update();
    }
  

    $item_amount = round($item_amount * ($settings->pay_percent/100));

    $txnid = "FSTR".uniqid().time();
    $order['customer_state'] = $request->state;
    $order['shipping_state'] = $request->shipping_state;
    $order['user_id'] = $request->user_id;
    $order['cart'] = utf8_encode(bzcompress(serialize($cart), 9));
    $order['totalQty'] = $request->totalQty;
   // dd($request->totalQty);
    $wallet = $request->wallet_price;
    $order['pay_amount'] = round($request->total / $curr->value, 2);
    $order['method'] = "Online/FosterPayment";
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
    $order['payment_status'] = "Pending";
    $order['currency_sign'] = $curr->sign;
    $order['currency_value'] = $curr->value;
    $order['shipping_cost'] = $request->shipping_cost;
    $order['packing_cost'] = $request->packing_cost;
    $order['shipping_title'] = $request->shipping_title;
    $order['packing_title'] = $request->packing_title;
    $order['tax'] = $request->tax;
    $order['dp'] = $request->dp;
    $order['txnid'] = $txnid; 
    $order['vendor_shipping_id'] = $request->vendor_shipping_id;
    $order['vendor_packing_id'] = $request->vendor_packing_id;
    $order['wallet_price'] = round($wallet / $curr->value, 2);

    //new by das

    $gs = Generalsetting::first();
        if($order['dp'] == 1)
        {
            $order['status'] = 'completed';
        }
        if (Session::has('affilate')) 
        {
            $val = $request->total / $curr->value;
            $val = $val / 100;
            $sub = $val * $settings->affilate_charge;
            $user = User::findOrFail(Session::get('affilate'));
            if($user){
                if($order['dp'] == 1)
                {
                    $user->affilate_income += $sub;
                    $user->update();
                }

                $order['affilate_user'] = $user->id;
                $order['affilate_charge'] = $sub;
            }
        }
        $order->save();

        if(Auth::check()){
            Auth::user()->update(['balance' => (Auth::user()->balance - $order->wallet_price)]);
        }


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


                        $msg="One of your product is almost out of stock (less or equal to 5).\n<strong>Product Link: </strong> <a target='_blank' href='".url('/').'/'.'item/'.$product->slug."'>".$product->name."</a>";
                       
                            $response = Http::get('http://portal.metrotel.com.bd/smsapi', [
                                'api_key' => 'R20000475dda47691ef6c0.75040283',
                                'type' => 'text',
                                'contacts' =>$product->user->phone,
                                'senderid' =>'8809612440465',
                                'msg' => $msg
                                ]); 
                        
                               
                                
                        
                                SmsLog::create([
                                    'from' => 'vendor alert',
                                    'to' => $product->user->phone,
                                    'message' =>$msg ,
                                    'status'=> $response->body(),
                                    'sent_by'=>"System"
                                    ]
                                  );
                
                          //  return response()->json( ['data'=>true,'message'=>'sent','success'=>1],200);
                        




                    } //end if sms notify
                    // if($gs->is_smtp == 1)
                    // {
                    //     $maildata = [
                    //         'to' => $product->user->email,
                    //         'subject' => 'Out of Stock Alert!',
                    //         'body' => "One of your product is almost out of stock (less or equal to 5).\n<strong>Product Link: </strong> <a target='_blank' href='".url('/').'/'.'item/'.$product->slug."'>".$product->name."</a>",
                    //     ];
                    //     $mailer = new DasMailer();
                    //     $mailer->sendCustomMail($maildata);
                    // }
                    // else
                    // {
                    //     $to = $product->user->email;
                    //     $subject = 'Out of Stock Alert!';
                    //     $msg = "One of your product is almost out of stock (less or equal to 5).\n<strong>Product Link: </strong> <a target='_blank' href='".url('/').'/'.'item/'.$product->slug."'>".$product->name."</a>";
                    //     $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
                    //     mail($to,$subject,$msg,$headers);
                    // }
                }              
            }
        }


        $notf = null;
        $i=1;
        foreach($cart->items as $prod)
        {   
            if($prod['item']['user_id'] != 0)
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


                    $msg="Dear Seller,\nAn order has been palced at your shop.\nOrderNo:".$vorder->order_number.".\nTake necessary steps if order is confirmed/paid.\nThanks www.uzanvati.com ";
                   
                        $response = Http::get('http://portal.metrotel.com.bd/smsapi', [
                            'api_key' => 'R20000475dda47691ef6c0.75040283',
                            'type' => 'text',
                            'contacts' =>$vorder->user->phone,
                            'senderid' =>'8809612440465',
                            'msg' => $msg
                            ]); 
                            
                           
                            
                    
                            SmsLog::create([
                                'from' => 'vendor alert',
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
        }

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


        //Sending Email To Buyer

        if($gs->is_sms_notify==1){


            $msg="Hello ".$request->name."!\nYou have placed a new order.\nYour order number is ".$order->order_number."\nPlease wait for your delivery. \nThank you. www.uzanvati.com";
            
                $response = Http::get('http://portal.metrotel.com.bd/smsapi', [
                    'api_key' => 'R20000475dda47691ef6c0.75040283',
                    'type' => 'text',
                    'contacts' =>$request->phone,
                    'senderid' =>'8809612440465',
                    'msg' => $msg
                    ]); 
            
                   
                    
            
                    SmsLog::create([
                        'from' => 'customer order place',
                        'to' => $request->phone,
                        'message' =>$msg ,
                        'status'=> $response->body(),
                        'sent_by'=>"System"
                        ]
                      );
    
              //  return response()->json( ['data'=>true,'message'=>'sent','success'=>1],200);
        



        }// is notify
        // if($gs->is_smtp == 1)
        // {
        //     $data = [
        //         'to' => $request->email,
        //         'type' => "new_order",
        //         'cname' => $request->name,
        //         'oamount' => "",
        //         'aname' => "",
        //         'aemail' => "",
        //         'wtitle' => "",
        //         'onumber' => $order->order_number,
        //     ];

        //     $mailer = new DasMailer();
        //     $mailer->sendAutoOrderMail($data,$order->id);            
        // }
        // else
        // {
        //     $to = $request->email;
        //     $subject = "Your Order Placed!!";
        //     $msg = "Hello ".$request->name."!\nYou have placed a new order.\nYour order number is ".$order->order_number.".Please wait for your delivery. \nThank you.";
        //     $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
        //     mail($to,$subject,$msg,$headers);            
        // }
        // //Sending Email To Admin
        // if($gs->is_smtp == 1)
        // {
        //     $data = [
        //         'to' => $gs->header_email,
        //         'subject' => "New Order Recieved!!",
        //         'body' => "Hello Admin!<br>Your store has received a new order.<br>Order Number is ".$order->order_number.".Please login to your panel to check. <br>Thank you.",
        //     ];

        //     $mailer = new DasMailer();
        //     $mailer->sendCustomMail($data);            
        // }
        // else
        // {
        //     $to = $gs->from_email;
        //     $subject = "New Order Recieved!!";
        //     $msg = "Hello Admin!\nYour store has recieved a new order.\nOrder Number is ".$order->order_number.".Please login to your panel to check. \nThank you.";
        //     $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
        //     mail($to,$subject,$msg,$headers);
        // }




        Session::put('tempcart',$cart);
        Session::forget('cart');
        Session::forget('pickup_text');
        Session::forget('pickup_cost');
        Session::forget('pickup_costshow');

        Session::put('temporder',$order);


 # $post_data['multi_card_name'] = "mastercard,visacard,amexcard";  # DISABLE TO DISPLAY ALL AVAILABLE

 $input = $request->all();


// dd($input);

 //foster

 $redirect_url =url("api/foster/notify") ;
 //$redirect_url= action('Front\FosterController@notify');

 //dd($redirect_url);
 $cancel_url = action('Front\FosterController@cancel');
 $fail_url = action('Front\FosterController@cancel');
 $urlparamForHash = http_build_query(array(
     'mcnt_AccessCode' => $gs->foster_access_code,
     'mcnt_TxnNo' => $txnid, //Ymdhmsu//PNR 
     'mcnt_ShortName' => $gs->foster_short_name,
     'mcnt_OrderNo' => $item_number,
     'mcnt_ShopId' => $gs->foster_shop_id,
     'mcnt_Amount' => $item_amount,
     'mcnt_Currency' => 'BDT'
 ));
 $secretkey = $gs->foster_secretkey;
 $secret = strtoupper($secretkey);
 $hashinput = hash_hmac('SHA256', $urlparamForHash, $secret);

 $domain =   $_SERVER["SERVER_NAME"]; // or Manually put your domain name  
 $ip =request()->server('SERVER_ADDR');  //domain ip  
// $ip = "23.227.186.26";
 //echo $ip."======================";

 //dd($hashinput);


 $urlparam = array(
     'mcnt_TxnNo' => $txnid,
     'mcnt_ShortName' =>$gs->foster_short_name , //No Need to Change       
     'mcnt_OrderNo' =>   $item_number,
     'mcnt_ShopId' => $gs->foster_shop_id, //No Need to Change 
     'mcnt_Amount' => $item_amount,
     'mcnt_Currency' => 'BDT',
     'cust_InvoiceTo' => $input['name'],
     'cust_CustomerServiceName' => 'E-commarce', //must
     'cust_CustomerName' => $input['name'], //must 
     'cust_CustomerEmail' => $input['email'], //must  
     'cust_CustomerAddress' => $input['address'],
     'cust_CustomerContact' => $input['phone'], //must 
     'cust_CustomerGender' => 'N/A',
     'cust_CustomerCity' => $input['city'], //must 
     'cust_CustomerState' => $input['state'],
     'cust_CustomerPostcode' => $input['zip'],
     'cust_CustomerCountry' => 'Bangladesh',
     'cust_Billingaddress' => 'Bangladesh', //must if not put ‘N/A’ 
     'cust_ShippingAddress' => 'Bangladesh',
     'cust_orderitems' => 1, //must  
     'GW' => '', //optional        
     'CardType' => '', //optional
     'success_url' => $redirect_url, //must   
     'cancel_url' => $cancel_url, //must   
     'fail_url' => $fail_url, //must  
     'emi_amout_per_month' => '', //optional 
     'emi_duration' => '', //optional                           
     'merchentdomainname' => $domain, // must           
     'merchentip' => $ip,
     'mcnt_SecureHashValue' => $hashinput
 );

 if($gs->foster_sandbox_check==1){
    $url = 'https://demo.fosterpayments.com.bd/fosterpayments/paymentrequest.php';
 }else{
    $url ='https://payment.fosterpayments.com.bd/fosterpayments/paymentrequest.php';
 }

  


// dd($hashinput);

 $data_string = json_encode($urlparam);
 $handle = curl_init();
 curl_setopt($handle, CURLOPT_URL, $url);
 curl_setopt($handle, CURLOPT_TIMEOUT, 30);
 curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
 curl_setopt($handle, CURLOPT_POST, 1);
 curl_setopt($handle, CURLOPT_POSTFIELDS, $data_string);
 curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC
 curl_setopt(
     $handle,
     CURLOPT_HTTPHEADER,
     array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string))
 );
 $content = curl_exec($handle);

 $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);


 // dd($content);
 if ($code == 200 && !(curl_errno($handle))) {
     curl_close($handle);
     $response = $content;
 } else {
     curl_close($handle);
     return redirect()->back()->with('unsuccess', "FAILED TO CONNECT WITH FOSTER API");
     exit;
 }
 
 $responsedate = json_decode($response, true);

// dd($responsedate);
 //echo $response ;
 $data = $responsedate['data'];
 
 //  die;
 $redirect_url = $data['redirect_url'];
 $payment_id = $data['payment_id'];
 $cancel_url = action('Front\PaymentController@paycancle');


 if($redirect_url==""){
    return redirect($cancel_url);
 }
 
 
 
 $url = $redirect_url . "?payment_id=" . $payment_id;
 //dd($url);
 //header("Location:" . $url);
echo "<meta http-equiv='refresh' content='0;url=" . $url . "'>";
exit;
 //dd($url);
 // echo $url;

 //end foster


 } // end store







//end new buynow store













    
 
}