<?php

namespace App\Http\Controllers\Vendor;

use Auth;
use Excel;
use App\Models\Order;
use App\Models\OrderTrack;
use App\Models\VendorOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $orders = VendorOrder::where('user_id','=',$user->id)->orderBy('id','desc')->get()->groupBy('order_number');
      
       // dd($orders);
        return view('vendor.order.index',compact('user','orders'));
    }


    public function pending()
    {
        $user = Auth::user();
        $orders = VendorOrder::where('user_id','=',$user->id)
        ->where('status','pending')                
        ->orderBy('id','desc')->get()->groupBy('order_number');
      //  dd("hi");
        return view('vendor.order.index',compact('user','orders'));
    }


    public function confirmed()
    {
        $user = Auth::user();
        $orders = VendorOrder::where('user_id','=',$user->id)
        ->where('status','confirmed')                
        ->orderBy('id','desc')->get()->groupBy('order_number');
        return view('vendor.order.index',compact('user','orders'));
    }


    public function processing()
    {
        $user = Auth::user();
        $orders = VendorOrder::where('user_id','=',$user->id)
        ->where('status','processing')                
        ->orderBy('id','desc')->get()->groupBy('order_number');
        return view('vendor.order.index',compact('user','orders'));
    }

    public function completed()
    {
        $user = Auth::user();
        $orders = VendorOrder::where('user_id','=',$user->id)
        ->where('status','completed')                
        ->orderBy('id','desc')->get()->groupBy('order_number');
        return view('vendor.order.index',compact('user','orders'));
    }


    public function canceled()
    {
        $user = Auth::user();
        $orders = VendorOrder::where('user_id','=',$user->id)
        ->where('status','declined')                
        ->orderBy('id','desc')->get()->groupBy('order_number');
        return view('vendor.order.index',compact('user','orders'));
    }


    public function shipped()
    {
        $user = Auth::user();
        $orders = VendorOrder::where('user_id','=',$user->id)
        ->where('status','shipped')                
        ->orderBy('id','desc')->get()->groupBy('order_number');
        return view('vendor.order.index',compact('user','orders'));
    }

    

    public function excel(Request $request){

        $user = Auth::user();
        $status = $request->status;

        $orders = VendorOrder::where('user_id','=',$user->id)
                    ->where('status','=',$status)
                    ->orderBy('id','desc')->get()->groupBy('order_number');
    
            $order_details[] = array('Order_number','Qty','Price','Status','DateTime');
            //dd($orders);
            foreach($orders as $order){
               // dd($order[0]['order_number']);
                
                $order_details[] = array(
                    'Order_number' => $order[0]['order_number'],
                    'Quantity' => $order[0]['qty'],
                    'Price' => $order[0]['price'],
                    'Status' => $order[0]['status'],
                    'DateTime' => $order[0]['created_at']
               );
            
            }

            $export = new ExportController ([
                $order_details,
            ]);

            return Excel::download($export, 'invoices.xlsx');


        // return (new ExportController)->download('invoices.xlsx');

    }

    public function show($slug)
    {  // dd(Auth::user()->id);
        VendorOrder::where('flag','visited')->update(['flag' => '']);
        $user = Auth::user();
        $order = Order::where('order_number','=',$slug)->first();
        $vorder = VendorOrder::where('order_number','=',$slug)->where('user_id','=',$user->id)->orderBy('id','desc')->groupBy('order_number')->first();
 
        if($vorder->flag !="visited"){
            $vorder->flag="visited";
            $vorder->save();
        }


        //dd($order);

        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        return view('vendor.order.details',compact('user','order','cart','vorder'));
    }

    public function license(Request $request, $slug)
    {
        $order = Order::where('order_number','=',$slug)->first();
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        $cart->items[$request->license_key]['license'] = $request->license;
        $order->cart = utf8_encode(bzcompress(serialize($cart), 9));
        $order->update();         
        $msg = 'Successfully Changed The License Key.';
        return response()->json($msg);
    }



    public function invoice($slug)
    {
        Order::where('flag','visited')->update(['flag' => '']);
        $user = Auth::user();
        $order = Order::where('order_number','=',$slug)->first();
        if($order->flag !="visited"){
            $order->flag="visited";
            $order->save();
        }

       // dd($order);
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        return view('vendor.order.invoice',compact('user','order','cart'));
    }

    public function printpage($slug)
    {
        $user = Auth::user();
        $order = Order::where('order_number','=',$slug)->first();
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        return view('vendor.order.print',compact('user','order','cart'));
    }

    public function status($slug,$status)
    {
        $mainorder = VendorOrder::where('order_number','=',$slug)->first();
        if ($mainorder->status == "completed" || $mainorder->status == "Completed"){
           // return redirect()->back()->with('success','This Order is Already Completed');
           return 1;
        }else{

        if($status != "completed"){

            $title = ucwords($status);
            $order = Order::where('order_number','=',$slug)->first();
            $order->update(['status' => $status]);
            $ck = OrderTrack::where('order_id','=',$order->id)->where('title','=',$title)->first();
            if($status=='confirmed'){
                $title='Confirmed';
            }


            
            if($ck) {
                
                $ck->title = $title;
                $ck->text = 'updated by seller';
                $ck->update();  

                
            }
            else {
                $data = new OrderTrack;
                $data->order_id = $order->id;
                $data->title = $title;
                $data->text = 'updated by seller';
                $data->save();            
            }
    
        
        }

        $user = Auth::user();
        VendorOrder::where('order_number','=',$slug)->where('user_id','=',$user->id)->update(['status' => $status]);
       // return redirect()->route('vendor-order-index')->with('success','Order Status Updated Successfully');
        return 2;
        }
    }

}
