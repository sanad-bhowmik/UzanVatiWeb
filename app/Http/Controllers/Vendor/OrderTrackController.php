<?php

namespace App\Http\Controllers\Vendor;

use Validator;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderTrack;
use Illuminate\Http\Request;
use App\Models\Generalsetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Http;
use App\Models\SmsLog;
class OrderTrackController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }


   //*** GET Request
    public function index($id)
    {
    	$order = Order::findOrFail($id);
        return view('vendor.order.track',compact('order'));
    }
    public function indexVendor($id)
    {
    	$order = Order::findOrFail($id);
        return view('vendor.order.track',compact('order'));
    }

   //*** GET Request
    public function load($id)
    {
        $order = Order::findOrFail($id);
        return view('vendor.order.track-load',compact('order'));
    }


    public function add()
    {


        //--- Logic Section

        $title = $_GET['title'];

        $ck = OrderTrack::where('order_id','=',$_GET['id'])->where('title','=',$title)->first();
        if($ck){
            $ck->order_id = $_GET['id'];
            $ck->title = $_GET['title'];
            $ck->text = $_GET['text'];
            $ck->update();  
        }
        else {
            $data = new OrderTrack;
            $data->order_id = $_GET['id'];
            $data->title = $_GET['title'];
            $data->text = $_GET['text'];
            $data->save();            
        }


        //--- Logic Section Ends


    }


    //*** POST Request
    public function store(Request $request)
    {
        //--- Validation Section
        // $rules = [
        //        'title' => 'unique:order_tracks',
        //         ];
        // $customs = [
        //        'title.unique' => 'This title has already been taken.',
        //            ];
        // $validator = Validator::make($request->all(), $rules, $customs);
        // if ($validator->fails()) {
        //   return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        // }
        //--- Validation Section Ends

        //--- Logic Section
        $gs = Generalsetting::first();
        $title = $request->title;
        $customer = User::where('id',$request->user_id)->first();
        if($gs->is_sms_notify==1 ){


            $msg="Dear Customer,\nHere is an update for your order.\n".$request->order_number.".\nNote: ".$request->title."\nDetails: ".$request->text."\nThanks www.uzanvati.com ";
               
                    $response = Http::get('http://portal.metrotel.com.bd/smsapi', [
                        'api_key' => 'R20000475dda47691ef6c0.75040283',
                        'type' => 'text',
                        'contacts' =>$customer->phone,
                        'senderid' =>'8809612440465',
                        'msg' => $msg
                        ]); 
                        
                
                        SmsLog::create([
                            'from' => 'OrderTrack',
                            'to' => $customer->phone,
                            'message' =>$msg ,
                            'status'=> $response->body(),
                            'sent_by'=>"System"
                            ]
                          );
        
                  //  return response()->json( ['data'=>true,'message'=>'sent','success'=>1],200);

            } //end if sms notify

      //  return response()->json($customer->phone);  
        $ck = OrderTrack::where('order_id','=',$request->order_id)->where('title','=',$title)->first();
        if($ck) {
            $ck->order_id = $request->order_id;
            $ck->title = $request->title;
            $ck->text = $request->text;
            $ck->update();  

        //--- Redirect Section  
        $msg = 'Data Updated Successfully.';
        return response()->json($msg);      
        //--- Redirect Section Ends  
            
        }
        else {
            $data = new OrderTrack;
            $input = $request->all();
            $data->fill($input)->save();            
        }

        //--- Logic Section Ends

        //--- Redirect Section  
        $msg = 'New Track Added Successfully.';
        return response()->json($msg);      
        //--- Redirect Section Ends  
    }


    //*** POST Request
    public function update(Request $request, $id)
    {
        //--- Validation Section
        $rules = [
               'title' => 'unique:order_tracks,title,'.$id
                ];
        $customs = [
               'title.unique' => 'This title has already been taken.',
                   ];
        $validator = Validator::make($request->all(), $rules, $customs);
        if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //--- Logic Section
        $data = OrderTrack::findOrFail($id);
        $input = $request->all();
        $data->update($input);
        //--- Logic Section Ends

        //--- Redirect Section          
        $msg = 'Data Updated Successfully.';
        return response()->json($msg);    
        //--- Redirect Section Ends  

    }

    //*** GET Request
    public function delete($id)
    {
        $data = OrderTrack::findOrFail($id);
        $data->delete();
        //--- Redirect Section     
        $msg = 'Data Deleted Successfully.';
        return response()->json($msg);      
        //--- Redirect Section Ends   
    }

}
