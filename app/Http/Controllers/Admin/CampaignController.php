<?php

namespace App\Http\Controllers\Admin;

use Datatables;
use DB;
use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CampaignJoinRequest;
use Illuminate\Support\Facades\Input;
use Validator;

class CampaignController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }




	    //*** JSON Request
	public function joinrequestdatatable()
	    {
            $datas = DB::table('campaign_join_requests')
            ->Join('users','users.id','=','campaign_join_requests.vendor_id')
            ->Join('campaigns','campaigns.id','=','campaign_join_requests.campaign_id')
            ->where('campaigns.status',1)
            ->where('campaign_join_requests.status','=','pending')
            ->selectRaw('campaign_join_requests.id,campaigns.name,campaign_join_requests.status,users.shop_name,users.shop_number ')
            ->orderBy('campaigns.id','desc')->get();
        //   dd($datas );
 //--- Integrating This Collection Into Datatables
            return Datatables::of($datas)
                    
            ->addColumn('action', function($data) {
                return '<div class="action-list"><a data-href="' . route('admin-campaign-join',$data->id) . '" class="edit" data-toggle="modal" data-target="#modal1"> <i class="fas fa-book"></i>Details</a></div>';
            }) 
                    ->rawColumns(['banner','action'])
                    ->toJson(); //--- Returning Json Data To Client Side
	}



    public function joinrequestdatatabletype($status,$type)
    {
      //  dd($status . $type);
        $datas = DB::table('campaign_join_requests')
        ->Join('users','users.id','=','campaign_join_requests.vendor_id')
        ->Join('campaigns','campaigns.id','=','campaign_join_requests.campaign_id')
        ->where('campaigns.status',$status)
        ->where('campaign_join_requests.status','=',''.$type.'')
        ->selectRaw('campaign_join_requests.id,campaigns.name,campaign_join_requests.status,users.shop_name,users.shop_number ')
        ->orderBy('campaigns.id','desc')->get();
    //   dd($datas );
//--- Integrating This Collection Into Datatables
        return Datatables::of($datas)
                
        ->addColumn('action', function($data) {
            return '<div class="action-list"><a data-href="' . route('admin-campaign-join',$data->id) . '" class="edit" data-toggle="modal" data-target="#modal1"> <i class="fas fa-book"></i>Details</a></div>';
        }) 
                ->rawColumns(['banner','action'])
                ->toJson(); //--- Returning Json Data To Client Side
}

	//*** GET Request
    public function joinrequest()
    {
        return view('admin.campaign.index');
    }

    public function joinactive()
    {
        
        return view('admin.campaign.list-active');
    }
    public function joininactive()
    {
        return view('admin.campaign.list-inactive');
    }


    public function join($id)
    {
        $data = CampaignJoinRequest::findOrFail($id);
        return view('admin.campaign.join',compact('data'));
    }



    public function joinupdate(Request $request, $id)
    {
        //--- Validation Section
        // $rules = [
        //        'photo'      => ['mimes:jpeg,jpg,png,svg','max:5120'],
        //        'file'      => ['mimes:pdf,doc,docx','max:5120'],
        //         ];

        // $validator = Validator::make($request->all(), $rules);
        
        // if ($validator->fails()) {
        //   return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        // }
        //--- Validation Section Ends

        //--- Logic Section

            // if ($file = $request->file('photo')) 
            // {              
            //     $name = time().str_replace(' ', '', $file->getClientOriginalName());
            //     $file->move('assets/images/banners',$name);
            //     if($data->photo != null)
            //     {
            //         if (file_exists(public_path().'/assets/images/banners/'.$data->photo)) {
            //             unlink(public_path().'/assets/images/banners/'.$data->photo);
            //         }
            //     }            
            // $input['banner'] = $name;
            // } 


            // if ($file = $request->file('file')) 
            // {              
            //     $name = time().str_replace(' ', '', $file->getClientOriginalName());
            //     $file->move('assets/files/',$name);
            //     if($data->file != null)
            //     {
            //         if (file_exists(public_path().'/assets/files/'.$data->file)) {
            //             unlink(public_path().'/assets/files/'.$data->file);
            //         }
            //     }            
            // $input['file'] = $name;
            // } 
        $data = CampaignJoinRequest::findOrFail($id);
        $input = $request->all();
        $data->update($input);
        //--- Logic Section Ends

        //--- Redirect Section     
        $msg = 'Data Updated Successfully.';
        return response()->json($msg);      
        //--- Redirect Section Ends            
    }

}
