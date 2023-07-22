<?php

namespace App\Http\Controllers\Front;

use Datatables;
use DB;
use App\Models\Campaign;
use App\Models\Time;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Support\Facades\Input;
use App\Models\Product;
use Validator;
use App\Models\Currency;
use App\Models\ProductClick;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class CampaignController extends Controller
{
    public function __construct()
    {
      //  $this->auth_guests();
    }


       //campaign section
       public function campaigns()
       {


        $campaigns = Campaign::where('status',1)
                             ->where('end_date','>=',date('Y-m-d'))
                             ->orderBy('id','desc')
                             ->get();


        return view('front.campaign.index',compact('campaigns'));
       
    
        }
        public function campaign($code)
        {
           // dd($name);
 
         $campaign = Campaign::where('status',1)
                              ->where('end_date','>=',date('Y-m-d'))
                              ->where('code',$code)
                              ->firstOrFail();

        $datas  = Product::where('status',1)
                        ->where('campaign_product',1)
                        ->where('campaign_id',$campaign->id)
						->orderBy('id','desc')
                        ->get();
 
         return view('front.campaign.campaign-details',compact('campaign','datas'));
        
     
         }

       //end campign section

     public function campaignitems($campaignCode,Request $request){

                $campaign = Campaign::where('code',$campaignCode)->firstOrFail();
    
                if($request->for=='products'){
                 $datas =Product::leftJoin('campaigns','campaigns.id','products.campaign_id')
                ->where('products.status',1)
                ->where('campaigns.status',1)
                ->where('campaigns.id',$campaign->id)
                ->selectRaw('products.* ')
				->orderBy('products.id','desc')
                ->get();

                return view('front.campaign.includes.product-list',compact('datas','campaign'));
            
                }

                if($request->for=='shops'){
                    $datas =DB::table('campaign_join_requests')
                    ->join('campaigns','campaigns.id','campaign_join_requests.campaign_id')
                    ->join('users','users.id','campaign_join_requests.vendor_id')
                    ->where('campaign_join_requests.status','approved')
                    ->where('campaigns.status',1)
                    ->where('campaigns.id',$campaign->id)
                    ->selectRaw(' users.* ')
                    ->get();
                    return view('front.campaign.includes.shop-list',compact('datas','campaign'));
                
                }


                if($request->for=='brands'){
                    $datas =DB::table('products')
                    ->join('campaigns','campaigns.id','products.campaign_id')
                    ->join('brands','brands.id','products.brand_id')
                    ->where('campaigns.status',1)
                    ->where('products.status',1)
                    ->where('brands.status',1)
                    ->where('campaigns.id',$campaign->id)
                    ->selectRaw(' distinct brands.brand_name,brands.photo,brands.brand_code ')
                    ->get();
                    return view('front.campaign.includes.brand-list',compact('datas','campaign'));
                
                }
            



         }

         public function brandproducts(Request $request,$bcode,$ccode){

          $campaign = Campaign::where('code',$ccode)->firstOrFail();
          $brand = Brand::where('brand_code',$bcode)->firstOrFail();
          $datas =Product::where('campaign_product',1)
          ->where('products.status',1)
          ->where('brand_id',$brand->id)
          ->where('campaign_id',$campaign->id)
          ->selectRaw('products.* ')
          ->get();

          return view('front.campaign.product-list',compact('datas','campaign'));


         }

    public function product($slug)
    {
       // $this->code_image();
        $productt = Product::where('slug','=',$slug)->where('status',1)
        ->firstOrFail();


        if($productt->campaign->status !=1){

            return redirect()->to('/');
        }
        $productt->views+=1;
        $productt->update();
        if (Session::has('currency'))
        {
            $curr = Currency::find(Session::get('currency'));
        }
        else
        {
            $curr = Currency::where('is_default','=',1)->first();
        }
        $product_click =  new ProductClick;
        $product_click->product_id = $productt->id;
        $product_click->date = Carbon::now()->format('Y-m-d');
        $product_click->save();

        if($productt->user_id != 0)
        {
            $vendors = Product::where('status','=',1)->where('user_id','=',$productt->user_id)->take(8)->get();
        }
        else
        {
            $vendors = Product::where('status','=',1)->where('user_id','=',0)->take(8)->get();
        }
        return view('front.campaign.product',compact('productt','curr','vendors'));

    }


}
