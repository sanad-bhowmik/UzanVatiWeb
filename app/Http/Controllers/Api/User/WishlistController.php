<?php

namespace App\Http\Controllers\Api\User;


use App\{
    Models\Product,
    Models\Wishlist,
    Http\Controllers\Controller,
    Http\Resources\ProductlistResource
};

use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class WishlistController extends Controller
{


    public function wishlists(Request $request)
    {

        try{

            $sort = '';
            $user = Auth::guard('api')->user();
            $wishes = Wishlist::where('user_id','=',$user->id)->pluck('product_id');

            // Search By Sort

            if(!empty($request->sort))
            {
                $sort = $request->sort;

                if($sort == "date_desc")
                {
                    $prods = Product::where('status','=',1)->whereIn('id',$wishes)->orderBy('id','desc')->get();
                }
                else if($sort == "date_asc")
                {
                    $prods = Product::where('status','=',1)->whereIn('id',$wishes)->get();
                }
                else if($sort == "price_asc")
                {
                    $prods = Product::where('status','=',1)->whereIn('id',$wishes)->orderBy('price','asc')->get();
                }
                else if($sort == "price_desc")
                {
                    $prods = Product::where('status','=',1)->whereIn('id',$wishes)->orderBy('price','desc')->get();
                }

                return response()->json(['status' => true, 'data' => ProductlistResource::collection($prods), 'error' => []]);
                
            }

            $prods = Product::where('status','=',1)->whereIn('id',$wishes)->get();
            
            return response()->json(['status' => true, 'data' => ProductlistResource::collection($prods), 'error' => []]);

        }
        catch(\Exception $e){
            return response()->json(['status' => true, 'data' => [], 'error' => $e->getMessage()]);
        }


    }

    public function addwish(Request $request)
    {
        try {
            $input = $request->all();
            $user = Auth::guard('api')->user();
            $ck = Wishlist::where('user_id', $user->id)->where('product_id', $input['product_id'])->exists();
    
            if (!$ck) {
                $wish = new Wishlist();
                $wish->user_id = $user->id;
                $wish->product_id = $input['product_id'];
                $wish->save();
    
                return response()->json(['status' => true, 'data' => ['message' => 'Successfully Added To Wishlist.']]);
            } else {
                return response()->json(['status' => false, 'data' => [], 'message' => 'Already Added To Wishlist.'], Response::HTTP_UNAUTHORIZED);
            }
    
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => [], 'error' => ['message' => $e->getMessage()]]);
        }
    }

    public function removewish($id)
{
    try {
        $wish = Wishlist::where('product_id', $id)->where('user_id', Auth::user()->id)->first();

        if ($wish) {
            $wish->delete();
            return response()->json(['status' => true, 'data' => [], 'message' => 'Successfully Removed From Wishlist.']);
        } else {
            return response()->json(['status' => false, 'data' => [], 'message' => 'Product not found in wishlist.'], Response::HTTP_UNAUTHORIZED);
        }
    } catch (\Exception $e) {
        return response()->json(['status' => false, 'data' => [], 'error' => $e->getMessage()]);
    }
}
}
