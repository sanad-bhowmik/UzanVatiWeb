<?php

namespace App\Http\Controllers\Vendor;

use DB;
use Auth;
use Validator;
use Datatables;
use App\Models\Banner;
use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Models\CampaignJoinRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use phpDocumentor\Reflection\Types\Null_;

class CampaignController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //*** JSON Request
    public function datatables($status)
    {
        // dd($status);
        $datas = DB::table('campaigns')
            ->leftJoin('campaign_join_requests', function ($join) {

                $join->on('campaign_join_requests.campaign_id', '=', 'campaigns.id')->where('campaign_join_requests.vendor_id', '=', Auth::user()->id);
            })
            ->where('campaigns.status', 1)
            ->where('campaign_join_requests.vendor_id', '=', Null)
            ->selectRaw('campaigns.*')
            ->orderBy('campaigns.id', 'desc')->get();
        //   dd($datas );
        //--- Integrating This Collection Into Datatables
        return Datatables::of($datas)

            ->addColumn('action', function ($data) {

                return '<div class="action-list"><a data-href="' . route('vendor-campaign-details-apply', $data->id) . '" class="edit" data-toggle="modal" data-target="#modal1"> <i class="fas fa-eye"></i>Details</a></div>';
            })
            ->rawColumns(['banner', 'action'])
            ->toJson(); //--- Returning Json Data To Client Side
    }
    public function joinrequestdatatable($status, $type)
    {
        //  dd($status . $type);
        $datas = DB::table('campaign_join_requests')
            ->Join('users', 'users.id', '=', 'campaign_join_requests.vendor_id')
            ->Join('campaigns', 'campaigns.id', '=', 'campaign_join_requests.campaign_id')
            ->where('campaign_join_requests.vendor_id', '=', Auth::user()->id)
            ->selectRaw('campaign_join_requests.id,campaigns.name,campaigns.title,campaigns.name,campaign_join_requests.status,users.shop_name,users.shop_number ')
            ->orderBy('campaigns.id', 'desc')->get();
        //   dd($datas );
        //--- Integrating This Collection Into Datatables
        return Datatables::of($datas)

            ->addColumn('action', function ($data) {
                return '<div class="action-list"><a data-href="' . route('vendor-campaign-details', $data->id) . '" class="edit" data-toggle="modal" data-target="#modal1"> <i class="fas fa-book"></i>Details</a></div>';
            })
            ->rawColumns(['banner', 'action'])
            ->toJson(); //--- Returning Json Data To Client Side
    }
    //*** GET Request
    public function index()
    {
        return view('vendor.campaign.joined');
    }


    public function details($id)
    {

        $data = DB::table('campaign_join_requests')
        ->Join('users', 'users.id', '=', 'campaign_join_requests.vendor_id')
        ->Join('campaigns', 'campaigns.id', '=', 'campaign_join_requests.campaign_id')
        ->where('campaign_join_requests.id', '=', $id)
        ->where('campaign_join_requests.vendor_id', '=', Auth::user()->id)
        ->selectRaw('campaigns.title,campaigns.banner,campaigns.name,campaigns.start_date,campaigns.end_date,campaigns.file,campaign_join_requests.*')

        ->orderBy('campaign_join_requests.id', 'desc')->limit(1)->first();
       
       // dd($data);

        return view('vendor.campaign.details', compact('data'));
    }


    public function applied()
    {
        return view('vendor.campaign.applied');
    }

    public function active()
    {
        return view('vendor.campaign.index');
    }

    public function inactive()
    {
        return view('vendor.campaign.index-inactive');
    }


    //*** GET Request
    public function detailsapply($id)
    {

        $data = Campaign::findOrFail($id);

        return view('vendor.campaign.apply', compact('data'));
    }



    //*** POST Request
    public function join(Request $request)
    {

        $join = new CampaignJoinRequest();
        $input = $request->all();
        $input['vendor_id'] = Auth::user()->id;
        if ($join->fill($input)->save()) {
            $msg = 'Requested Successfully.';
        } else {
            $msg = 'Somthing went worng.';
        }

        //--- Redirect Section     

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

        $data->status == 0 ? $data->status = 1 : $data->status = 0;
        $data->save();


        //--- Redirect Section     
        $msg = 'Successfull.';
        return response()->json($msg);
        //--- Redirect Section Ends     
    }
}
