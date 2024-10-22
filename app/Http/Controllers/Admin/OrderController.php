<?php

namespace App\Http\Controllers\Admin;

use Excel;
use Datatables;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Classes\DasMailer;
use App\Models\OrderTrack;
use App\Models\VendorOrder;
use Illuminate\Http\Request;
use App\Models\Generalsetting;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\VendorWiseOrderNumber;
use Maatwebsite\Excel\Concerns\ToArray;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    //*** JSON Request
    public function datatables($status)
    {
        if($status == 'pending'){
            $datas = Order::where('status','=','pending')->where('user_id','=',0)->orderBy('id','desc')->get();
        }
        elseif($status == 'confirmed'){
            $datas = Order::where('status','=','confirmed')->where('user_id','=',0)->orderBy('id','desc')->get();
        }
        
        elseif($status == 'shipped'){
            $datas = Order::where('status','=','shipped')->where('user_id','=',0)->orderBy('id','desc')->get();
        }
        elseif($status == 'processing') {
            $datas = Order::where('status','=','processing')->where('user_id','=',0)->orderBy('id','desc')->get();
        }
        elseif($status == 'completed') {
            $datas = Order::where('status','=','completed')->where('user_id','=',0)->orderBy('id','desc')->get();
        }
        elseif($status == 'declined') {
            $datas = Order::where('status','=','declined')->where('user_id','=',0)->orderBy('id','desc')->get();
        }
        else{
          $datas = Order::orderBy('id','desc')->where('user_id','=',0)->get();  
        }
         
         //--- Integrating This Collection Into Datatables
         return Datatables::of($datas)
                            ->editColumn('id', function(Order $data) {
                                $id = '<a href="'.route('admin-order-invoice',$data->id).'">'.$data->order_number.'</a>';
                                return $id;
                            })
                            ->editColumn('pay_amount', function (Order $data) {
                                return $data->currency_sign . round(($data->pay_amount + $data->wallet_price) * $data->currency_value, 2);
                            })
                            ->addColumn('action', function(Order $data) {
                                $orders = '<a href="javascript:;" data-href="'. route('admin-order-edit',$data->id) .'" class="delivery" data-toggle="modal" data-target="#modal1"><i class="fas fa-dollar-sign"></i> Delivery Status</a>';
                                return '<div class="godropdown"><button class="go-dropdown-toggle"> Actions<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-order-show',$data->id) . '" > <i class="fas fa-eye"></i> Details</a><a href="javascript:;" class="send" data-email="'. $data->customer_email .'" data-toggle="modal" data-target="#vendorform"><i class="fas fa-envelope"></i> Send</a><a href="javascript:;" data-href="'. route('admin-order-track',$data->id) .'" class="track" data-toggle="modal" data-target="#modal1"><i class="fas fa-truck"></i> Track Order</a>'.$orders.'</div></div>';
                            }) 
                            ->rawColumns(['id','action'])
                            ->toJson(); //--- Returning Json Data To Client Side
    }


        //*** JSON Request
        public function datatablesShop($status)
        {
            if($status == 'pending'){
                $datas = Order::where('status','=','pending')->where('user_id','!=',0)->orderBy('id','desc')->get();
            }

            elseif($status == 'confirmed'){
                $datas = Order::where('status','=','confirmed')->where('user_id','!=',0)->orderBy('id','desc')->get();
            }

            elseif($status == 'picked'){
                $datas = Order::where('status','=','picked')->where('user_id','!=',0)->orderBy('id','desc')->get();
            }

            elseif($status == 'shipped'){
                $datas = Order::where('status','=','shipped')->where('user_id','!=',0)->orderBy('id','desc')->get();
            }

            elseif($status == 'processing') {
                $datas = Order::where('status','=','processing')->where('user_id','!=',0)->orderBy('id','desc')->get();
            }
            elseif($status == 'completed') {
                $datas = Order::where('status','=','completed')->where('user_id','!=',0)->orderBy('id','desc')->get();
            }
            elseif($status == 'declined') {
                $datas = Order::where('status','=','declined')->where('user_id','!=',0)->orderBy('id','desc')->get();
            }
            else{
               
              $datas = Order::orderBy('id','desc')->orderBy('id','desc')->get(); 
            }
             
             //--- Integrating This Collection Into Datatables
             return Datatables::of($datas)
                                ->editColumn('id', function(Order $data) {
                                    $id = '<a href="'.route('admin-order-invoice',$data->id).'">'.$data->order_number.'</a>';
                                    return $id;
                                })
                                ->editColumn('status', function (Order $data) {
                                    
                                   
                                    if($data->status=="picked"){
                                        return 'picked';
                                    }
                                    else if($data->status=="completed"){
                                        return 'delivered';
                                    }else if($data->status=="declined"){
                                        return 'cancel';
                                    }else{
                                        return $data->status; 
                                    }
                                    
                                })
                                ->addColumn('shop_name', function (Order $data) {
                                  
                                //    $vendor = VendorWiseOrderNumber::where('order_number',$data->order_number)->first();
                                //     return $vendor->shop_name;
                                return '';
                                })
                                ->editColumn('created_at', function (Order $data) {
                                   
                                    return date('d/m/Y h:i:s A', strtotime($data->created_at));
                                })
                                ->editColumn('pay_amount', function (Order $data) {
                                    return $data->currency_sign . round(($data->pay_amount + $data->wallet_price) * $data->currency_value, 2);
                                })
                                ->addColumn('action', function(Order $data) {
                                    $orders = '<a href="javascript:;" data-href="'. route('admin-order-edit',$data->id) .'" class="delivery" data-toggle="modal" data-target="#modal1"><i class="fas fa-dollar-sign"></i> Delivery Status</a>';
                                    return '<div class="godropdown"><button class="go-dropdown-toggle"> Actions<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-order-show',$data->id) . '" > <i class="fas fa-eye"></i> Details</a><a href="javascript:;" class="send" data-email="'. $data->customer_email .'" data-toggle="modal" data-target="#vendorform"><i class="fas fa-envelope"></i> Send</a><a href="javascript:;" data-href="'. route('admin-order-track',$data->id) .'" class="track" data-toggle="modal" data-target="#modal1"><i class="fas fa-truck"></i> Track Order</a>'.$orders.'</div></div>';
                                }) 
                                ->rawColumns(['id','action'])
                                ->toJson(); //--- Returning Json Data To Client Side
        }






    public function index()
    {
        return view('admin.order.index');
    }

    public function indexShop()
    {
        return view('admin.order.indexShop');
    }

    public function edit($id)
    {
        $data = Order::find($id);
        return view('admin.order.delivery',compact('data'));
    }


    //*** POST Request
    public function update(Request $request, $id)
    {
        
        //--- Logic Section
        $data = Order::findOrFail($id);

        $input = $request->all();

        if ($data->status == "completed"){

        // Then Save Without Changing it.
            $input['status'] = "completed";
            $data->update($input);
            //--- Logic Section Ends
    

        //--- Redirect Section          
        $msg = 'Status Updated Successfully.';
        return response()->json($msg);    
        //--- Redirect Section Ends     

    
            }else{
           
                
            if ($input['status'] == "completed" || $input['status'] == "delivered"){
                $gs = Generalsetting::find(1);

                
                if($data->method != "Cash On Delivery"){
                    foreach($data->vendororders as $vorder)
                    {
                        $uprice = User::findOrFail($vorder->user_id);
                        $uprice->current_balance = $uprice->current_balance + round($vorder->price * ($gs->pay_percent/100) );
                        $uprice->update();
                    }
        
                }
              

                // if( User::where('id', $data->affilate_user)->exists() ){
                //     $auser = User::where('id', $data->affilate_user)->first();
                //     $auser->affilate_income += $data->affilate_charge;
                //     $auser->update();
                // }

               
                

                $gs = Generalsetting::findOrFail(1);
                // if($gs->is_smtp == 1)
                // {
                //     $maildata = [
                //         'to' => $data->customer_email,
                //         'subject' => 'Your order '.$data->order_number.' is Confirmed!',
                //         'body' => "Hello ".$data->customer_name.","."\n Thank you for shopping with us. We are looking forward to your next visit.",
                //     ];
    
                //     $mailer = new DasMailer();
                //     $mailer->sendCustomMail($maildata);                
                // }
                // else
                // {
                //    $to = $data->customer_email;
                //    $subject = 'Your order '.$data->order_number.' is Confirmed!';
                //    $msg = "Hello ".$data->customer_name.","."\n Thank you for shopping with us. We are looking forward to your next visit.";
                // $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
                //    mail($to,$subject,$msg,$headers);                
                // }

            } // end if completed

            if ($input['status'] == "declined"){

                if($data->user_id != 0){
                    if($data->wallet_price != 0){
                        $user = User::find($data->user_id);
                        if( $user ){
                            $user->balance = $user->balance + $data->wallet_price;
                            $user->save();
                        }
                    }
                }


                $cart = unserialize(bzdecompress(utf8_decode($data->cart)));

                foreach($cart->items as $prod)
                {
                    $x = (string)$prod['stock'];
                    if($x != null)
                    {
        
                        $product = Product::findOrFail($prod['item']['id']);
                        $product->stock = $product->stock + $prod['qty'];
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
                        $temp = $product->size_qty;
                        $temp[$prod['size_key']] = $x;
                        $temp1 = implode(',', $temp);
                        $product->size_qty =  $temp1;
                        $product->update();               
                    }
                }


                $gs = Generalsetting::findOrFail(1);
                // if($gs->is_smtp == 1)
                // {
                //     $maildata = [
                //         'to' => $data->customer_email,
                //         'subject' => 'Your order '.$data->order_number.' is Declined!',
                //         'body' => "Hello ".$data->customer_name.","."\n We are sorry for the inconvenience caused. We are looking forward to your next visit.",
                //     ];
                // $mailer = new DasMailer();
                // $mailer->sendCustomMail($maildata);
                // }
                // else
                // {
                //    $to = $data->customer_email;
                //    $subject = 'Your order '.$data->order_number.' is Declined!';
                //    $msg = "Hello ".$data->customer_name.","."\n We are sorry for the inconvenience caused. We are looking forward to your next visit.";
                //    $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
                //    mail($to,$subject,$msg,$headers);
                // }
    
            } // end if declined

            $data->update($input);

            if($request->track_text)
            {
                    $title = ucwords($request->status);
                    $ck = OrderTrack::where('order_id','=',$id)->where('title','=',$title)->first();
                    if($ck){
                        $ck->order_id = $id;
                        $ck->title = $title;
                        $ck->text = $request->track_text;
                        $ck->update();  
                    }
                    else {
                        $data = new OrderTrack;
                        $data->order_id = $id;
                        $data->title = $title;
                        $data->text = $request->track_text;
                        $data->save();            
                    }
    
    
            } 


        $order = VendorOrder::where('order_id','=',$id)->update(['status' => $input['status']]);

         //--- Redirect Section          
         $msg = 'Status Updated Successfully.';
         return response()->json($msg);    
         //--- Redirect Section Ends    
    
            }



        //--- Redirect Section          
        $msg = 'Status Updated Successfully.';
        return response()->json($msg);    
        //--- Redirect Section Ends  


    }



    public function pending()
    {
        return view('admin.order.pending');
    }
    public function shipped()
    {
        return view('admin.order.shipped');
    }
    public function confirmed()
    {
        return view('admin.order.confirmed');
    }
    public function picked()
    {
        return view('admin.order.picked');
    }
    public function processing()
    {
        return view('admin.order.processing');
    }
    public function completed()
    {
        return view('admin.order.completed');
    }
    public function declined()
    {
        return view('admin.order.declined');
    }

// new by das
    public function pendingShop()
    {
        return view('admin.order.pendingShop');
    }
    public function processingShop()
    {
        return view('admin.order.processingShop');
    }

    public function shippedShop()
    {
        return view('admin.order.shippedShop');
    }
    public function confirmedShop()
    {
        return view('admin.order.confirmedShop');
    }
    public function pickedShop()
    {
        return view('admin.order.pickedShop');
    }
    public function completedShop()
    {
        return view('admin.order.completedShop');
    }
    public function declinedShop()
    {
        return view('admin.order.declinedShop');
    }


    public function show($id)
    {
        Order::where('admin_flag','visited')->update(['admin_flag' => '']);
        $order = Order::findOrFail($id);
        if($order->admin_flag !="visited"){
            $order->admin_flag="visited";
            $order->save();
        }
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        return view('admin.order.details',compact('order','cart'));
    }
    public function invoice($id)
    {
        Order::where('admin_flag','visited')->update(['admin_flag' => '']);
        $order = Order::findOrFail($id);
        if($order->admin_flag !="visited"){
            $order->admin_flag="visited";
            $order->save();
        }
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        return view('admin.order.invoice',compact('order','cart'));
    }


    public function excel(Request $request){

        
        $status = $request->status;

        $orders = DB::table('orders')
                    ->join('vendor_orders','vendor_orders.order_id','=','orders.id')
                    ->join('users','users.id','=','vendor_orders.user_id')
                    ->where('orders.status','=',$status)
                    ->where('orders.user_id','!=',0)->orderBy('orders.id','desc')
                    ->selectRaw('orders.*, users.name,users.phone,users.shop_name,users.shop_number')
                    ->groupBy('orders.order_number')
                    ->get()->toArray();
    
            $order_details[] = array('order_number','customer_phone','shipping_phone','status','method','payment_status','pay_amount','paid_amount','remain_amount','txnid','vendor_name','vendor_phone','vendor_shop','vendor_shop_number','created_at');
         
            foreach($orders as $order){

               // dd($order->order_number);
                
                $order_details[] = array(

                    'order_number' => $order->order_number,
                    'customer_phone' =>$order->customer_phone,
                    'shipping_phone' =>$order->shipping_phone,
                    'status' => $order->status,
                    'method'=> $order->method,
                    'payment_status'=>$order->payment_status,
                    'pay_amount'=>$order->pay_amount,
                    'paid_amount'=>$order->paid_amount,
                    'remain_amount'=>$order->remain_amount,
                    'txnid'=>$order->txnid,
                    'vendor_name'=>$order->name,
                    'vendor_phone'=>$order->phone,
                    'vendor_shop'=>$order->shop_name,
                    'vendor_shop_number'=>$order->shop_number,
                    'created_at'=>$order->created_at
               );
            
            }

            $export = new ExportController ([
                $order_details,
            ]);

            return Excel::download($export, 'shop_invoices.xlsx');


        // return (new ExportController)->download('invoices.xlsx');

    }




    public function emailsub(Request $request)
    {
        $gs = Generalsetting::findOrFail(1);
        if($gs->is_smtp == 1)
        {
            $data = 0;
            $datas = [
                    'to' => $request->to,
                    'subject' => $request->subject,
                    'body' => $request->message,
            ];

            $mailer = new DasMailer();
            $mail = $mailer->sendCustomMail($datas);
            if($mail) {
                $data = 1;
            }
        }
        else
        {
            $data = 0;
            $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
            $mail = mail($request->to,$request->subject,$request->message,$headers);
            if($mail) {
                $data = 1;
            }
        }

        return response()->json($data);
    }

    public function printpage($id)
    {
        $order = Order::findOrFail($id);
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        return view('admin.order.print',compact('order','cart'));
    }

    public function license(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        $cart->items[$request->license_key]['license'] = $request->license;
        $order->cart = utf8_encode(bzcompress(serialize($cart), 9));
        $order->update();       
        $msg = 'Successfully Changed The License Key.';
        return response()->json($msg);
    }

    public function status($id,$status)
    {
         //--- Logic Section
         $data = Order::where('order_number',$id)->first();
         $trackOrder = $data;

        
 
         if ($data->status == "completed"){
 

         //--- Redirect Section          
         $msg = 'Already Completed ';
         return response()->json($msg);    
         //--- Redirect Section Ends     
 
     
        }else{
            
                 
             if ($status == "completed" || $status == "delivered"){
                 $gs = Generalsetting::find(1);
 
                 
                 if($data->method != "Cash On Delivery"){
                     foreach($data->vendororders as $vorder)
                     {
                         $uprice = User::findOrFail($vorder->user_id);
                         $uprice->current_balance = $uprice->current_balance + round($vorder->price * ($gs->pay_percent/100) );
                         $uprice->update();
                     }
         
                 }
               
 
                 // if( User::where('id', $data->affilate_user)->exists() ){
                 //     $auser = User::where('id', $data->affilate_user)->first();
                 //     $auser->affilate_income += $data->affilate_charge;
                 //     $auser->update();
                 // }
 
                
                 
 
                 $gs = Generalsetting::findOrFail(1);
                 // if($gs->is_smtp == 1)
                 // {
                 //     $maildata = [
                 //         'to' => $data->customer_email,
                 //         'subject' => 'Your order '.$data->order_number.' is Confirmed!',
                 //         'body' => "Hello ".$data->customer_name.","."\n Thank you for shopping with us. We are looking forward to your next visit.",
                 //     ];
     
                 //     $mailer = new DasMailer();
                 //     $mailer->sendCustomMail($maildata);                
                 // }
                 // else
                 // {
                 //    $to = $data->customer_email;
                 //    $subject = 'Your order '.$data->order_number.' is Confirmed!';
                 //    $msg = "Hello ".$data->customer_name.","."\n Thank you for shopping with us. We are looking forward to your next visit.";
                 // $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
                 //    mail($to,$subject,$msg,$headers);                
                 // }
 
             } // end if completed
 
             if ($status == "declined"){
 
                 if($data->user_id != 0){
                     if($data->wallet_price != 0){
                         $user = User::find($data->user_id);
                         if( $user ){
                             $user->balance = $user->balance + $data->wallet_price;
                             $user->save();
                         }
                     }
                 }
 
 
                 $cart = unserialize(bzdecompress(utf8_decode($data->cart)));
 
                 foreach($cart->items as $prod)
                 {
                     $x = (string)$prod['stock'];
                     if($x != null)
                     {
         
                         $product = Product::findOrFail($prod['item']['id']);
                         $product->stock = $product->stock + $prod['qty'];
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
                         $temp = $product->size_qty;
                         $temp[$prod['size_key']] = $x;
                         $temp1 = implode(',', $temp);
                         $product->size_qty =  $temp1;
                         $product->update();               
                     }
                 }
 
 
                // $gs = Generalsetting::findOrFail(1);
                 // if($gs->is_smtp == 1)
                 // {
                 //     $maildata = [
                 //         'to' => $data->customer_email,
                 //         'subject' => 'Your order '.$data->order_number.' is Declined!',
                 //         'body' => "Hello ".$data->customer_name.","."\n We are sorry for the inconvenience caused. We are looking forward to your next visit.",
                 //     ];
                 // $mailer = new DasMailer();
                 // $mailer->sendCustomMail($maildata);
                 // }
                 // else
                 // {
                 //    $to = $data->customer_email;
                 //    $subject = 'Your order '.$data->order_number.' is Declined!';
                 //    $msg = "Hello ".$data->customer_name.","."\n We are sorry for the inconvenience caused. We are looking forward to your next visit.";
                 //    $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
                 //    mail($to,$subject,$msg,$headers);
                 // }
     
             } // end if declined

            // dd($status);
             
             $data->update(['status' => $status]);
             
                $title = ucwords($status);
                     $ck = OrderTrack::where('order_id','=',$data->id)->where('title','=',$title)->first();
                     if($ck){
                         $ck->title = $title;
                         $ck->text = 'updated by uzanvati';
                         $ck->update();  
                     }
                     else {
                         $orderTrack = new OrderTrack;
                         $orderTrack ->order_id =$data->id;
                         $orderTrack ->title = $title;
                         $orderTrack ->text = 'updated by uzanvati';
                         $orderTrack ->save();            
                     }
     
     
        
 
 
         $order = VendorOrder::where('order_id','=',$data->id)->update(['status' => $status]);
 
          //--- Redirect Section          
          $msg = 'Status Updated Successfully.';
          return response()->json($msg);    
          //--- Redirect Section Ends    
     
        }
 
 
 
         //--- Redirect Section          
         $msg = 'Status update failed.';
         return response()->json($msg);    
         //--- Redirect Section Ends  

    }
}