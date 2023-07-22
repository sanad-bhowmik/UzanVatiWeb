<?php

namespace App\Http\Controllers\Campaign;

use Datatables;
use DB;
use App\Models\Campaign;
use App\Models\Time;
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
    public function datatables($status)
    {
       // dd($status);
         $datas = Campaign::where('status',$status)->orderBy('id','desc')->get();
         //--- Integrating This Collection Into Datatables
         return Datatables::of($datas)
                            ->editColumn('banner', function(Campaign $data) {
                                $photo = $data->banner ? url('assets/images/banners/'.$data->banner):url('assets/images/noimage.png');
                                return '<img src="' . $photo . '" alt="Image">';
                            })
                            ->addColumn('action', function(Campaign $data) {
                                return '<div class="action-list"><a data-href="' . route('admin-campaign-edit',$data->id) . '" class="edit" data-toggle="modal" data-target="#modal1"> <i class="fas fa-edit"></i>Edit</a><a href="javascript:;" data-href="' . route('admin-campaign-delete',$data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-eye-slash">Active/Inactive</i></a></div>';
                            }) 
                            ->rawColumns(['banner','action'])
                            ->toJson(); //--- Returning Json Data To Client Side
    }

    //*** GET Request
    public function index()
    {
        return view('campaign.index');
    }

    public function active()
    {
        return view('campaign.index');
    }

    public function inactive()
    {
        return view('campaign.index-inactive');
    }


    //*** GET Request
    public function create()
    {
        $times = Time::orderBy('time','asc')->get();
        return view('campaign.create',compact('times'));
    }

   
    //*** POST Request
    public function store(Request $request)
    {
        //--- Validation Section
        $rules = [
               'photo'      => 'required|mimes:jpeg,jpg,png,svg',
                ];

        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //--- Logic Section
        $data = new Campaign();
        $input = $request->all();
        if ($file = $request->file('photo')) 
         {      
            $name = time().str_replace(' ', '', $file->getClientOriginalName());
            $file->move('assets/images/banners',$name);           
            $input['banner'] = $name;
        } 

        $code= base64_encode(str_replace('%','',str_replace(' ', '', $input['title'])).time());
        $input['code'] = $code;
        $data->fill($input)->save();
        //--- Logic Section Ends

        //--- Redirect Section        
        $msg = 'New Data Added Successfully.';
        return response()->json($msg);      
        //--- Redirect Section Ends    
    }

    //*** GET Request
    public function edit($id)
    {
        $times = Time::orderBy('time','asc')->get();
        $data = Campaign::findOrFail($id);
        return view('campaign.edit',compact('data','times'));
    }



    //*** POST Request
    public function update(Request $request, $id)
    {
        //--- Validation Section
        $rules = [
               'photo'      => ['mimes:jpeg,jpg,png,svg','max:5120'],
               'file'      => ['mimes:pdf,doc,docx','max:5120'],
                ];

        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //--- Logic Section
        $data = Campaign::findOrFail($id);
        $input = $request->all();
            if ($file = $request->file('photo')) 
            {              
                $name = time().str_replace(' ', '', $file->getClientOriginalName());
                $file->move('assets/images/banners',$name);
                if($data->photo != null)
                {
                    if (file_exists(public_path().'/assets/images/banners/'.$data->photo)) {
                        unlink(public_path().'/assets/images/banners/'.$data->photo);
                    }
                }            
            $input['banner'] = $name;
            } 


            if ($file = $request->file('file')) 
            {              
                $name = time().str_replace(' ', '', $file->getClientOriginalName());
                $file->move('assets/files/',$name);
                if($data->file != null)
                {
                    if (file_exists(public_path().'/assets/files/'.$data->file)) {
                        unlink(public_path().'/assets/files/'.$data->file);
                    }
                }            
            $input['file'] = $name;
            } 

        $data->update($input);
        //--- Logic Section Ends

        //--- Redirect Section     
        $msg = 'Data Updated Successfully.';
        return response()->json($msg);      
        //--- Redirect Section Ends            
    }

    //*** GET Request Delete
    public function destroy($id)
    {
        $data = Campaign::findOrFail($id);
        //If Photo Doesn't Exist
        // if($data->photo == null){
        //     $data->delete();
        //     //--- Redirect Section     
        //     $msg = 'Data Deleted Successfully.';
        //     return response()->json($msg);      
        //     //--- Redirect Section Ends     
        // }
        // //If Photo Exist
        // if (file_exists(public_path().'/assets/images/banners/'.$data->photo)) {
        //     unlink(public_path().'/assets/images/banners/'.$data->photo);
        // }
        // $data->delete();

        $data->status==0 ? $data->status=1 : $data->status=0;
        $data->save();

        
        //--- Redirect Section     
        $msg = 'Successfull.';
        return response()->json($msg);      
        //--- Redirect Section Ends     
    }







}
