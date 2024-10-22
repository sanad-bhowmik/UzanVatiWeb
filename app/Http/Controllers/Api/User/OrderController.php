<?php

namespace App\Http\Controllers\Api\User;


use App\{
  Models\Order,
  Http\Controllers\Controller,
  Http\Resources\OrderResource,
  Http\Resources\OrderDetailsResource
};
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{


  public function orders(Request $request)
  {
    try {
      if ($request->view) {
        $paginate = $request->view;
      } else {
        $paginate = 12;
      }

      $orders = Order::where('user_id', '=', auth()->user()->id)->orderBy('id', 'desc')->paginate($paginate);

      $formattedOrders = [];
      foreach ($orders as $order) {
        $decodedCart = json_decode($order->cart, true);

        $totalPrice = 0;

        foreach ($decodedCart as $cartItem) {
          $product = Product::find($cartItem['product_id']);
          if ($product) {
            $totalPrice += $product->price * $cartItem['totalQty'];
          }
        }

        $orderData = $order->toArray();
        $orderData['cart'] = $decodedCart;
        $orderData['totalPrice'] = $totalPrice;

        $formattedOrders[] = $orderData;
      }

      return response()->json(['status' => true, 'data' => $formattedOrders, 'error' => []]);
    } catch (\Exception $e) {
      return response()->json(['status' => false, 'data' => [], 'error' => ['message' => $e->getMessage()]]);
    }
  }

  public function orderProducts(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'product_id' => 'required|exists:products,id',
      'product_quantity' => 'required|integer|min:1',
      'payment_method' => 'required|in:Cash On Delivery,Bkash',
      'shipping_address' => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json(['status' => false, 'error' => $validator->errors()], 400);
    }

    $userId = $request->user()->id;

    \DB::beginTransaction();
    $orderNumber = 'UZV' . mt_rand(100000000000, 999999999999);
    try {
      // Retrieve product details based on the provided product ID
      $product = Product::find($request->product_id);

      if (!$product) {
        \DB::rollback();
        return response()->json(['status' => false, 'error' => 'Product not found'], 404);
      }

      // Calculate total amount to be paid
      $totalAmount = $product->price * $request->product_quantity;

      // Construct order data including product details
      $orderData = [
        'product_id' => $product->id,
        'user_id' => $userId,
        'method' => $request->payment_method,
        'shipping' => $request->shipping_address,
        'pickup_location' => $request->shipping_address,
        'totalQty' => $request->product_quantity,
        'order_number' => $orderNumber,
        'payment_status' => 'pending',
        'customer_email' => $request->user()->email,
        'customer_name' => $request->user()->name,
        'customer_phone' => $request->user()->phone,
        'customer_address' => $request->user()->address,
        'customer_city' => 'Bangladesh',
        'customer_zip' => '',
        'shipping_name' => $request->user()->name,
        'shipping_email' => $request->user()->email,
        'shipping_phone' => $request->user()->phone,
        'shipping_address' => $request->shipping_address,
        'shipping_city' => 'Bangladesh',
        'shipping_zip' => '',
        'order_note' => '',
        'status' => 'pending',
        'paid_amount' => 0,
        'remain_amount' => 0,
        'customer_country' => $request->customer_country,
        'product' => $product, // Include product details
        'pay_amount' => $totalAmount, // Include total amount to be paid
      ];

      // Convert order data to JSON
      $cartData = json_encode([$orderData]); // Wrap order data in an array

      // Create the order with individual column values and the JSON data
      $order = Order::create([
        'user_id' => $userId,
        'method' => $request->payment_method,
        'shipping' => $request->shipping_address,
        'pickup_location' => $request->shipping_address,
        'totalQty' => $request->product_quantity,
        'order_number' => $orderNumber,
        'payment_status' => 'pending',
        'customer_email' => $request->user()->email,
        'customer_name' => $request->user()->name,
        'customer_phone' => $request->user()->phone,
        'customer_address' => $request->user()->address,
        'customer_city' => 'Bangladesh',
        'customer_zip' => '',
        'shipping_name' => $request->user()->name,
        'shipping_email' => $request->user()->email,
        'shipping_phone' => $request->user()->phone,
        'shipping_address' => $request->shipping_address,
        'shipping_city' => 'Bangladesh',
        'shipping_zip' => '',
        'order_note' => '',
        'status' => 'pending',
        'paid_amount' => 0,
        'remain_amount' => 0,
        'customer_country' => $request->customer_country,
        'cart' => $cartData,
        'pay_amount' => $totalAmount, // Include total amount to be paid
      ]);

      // Decrement product stock
      $product->decrement('stock', $request->product_quantity);

      \DB::commit();

      // Return response with order details
      return response()->json([
        'status' => true,
        'message' => 'Order placed successfully',
        'order' => $order,
      ], 200);
    } catch (\Exception $e) {
      \DB::rollback();
      return response()->json(['status' => false, 'error' => 'Failed to place order'], 500);
    }
  }



  public function order($id)
  {
    try {
      $order = Order::findOrfail($id);
      return response()->json(['status' => true, 'data' => new OrderDetailsResource($order), 'error' => []]);
    } catch (\Exception $e) {
      return response()->json(['status' => true, 'data' => [], 'error' => ['message' => $e->getMessage()]]);
    }
  }

  public function updateTransaction(Request $request)
  {
    try {
      $rules = [
        'order_id' => 'required',
        'transaction_id' => 'required'
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['status' => false, 'data' => [], 'error' => $validator->errors()]);
      }

      $order = Order::find($request->order_id);
      $order->txnid = $request->transaction_id;
      $order->save();

      return response()->json(['status' => true, 'data' => new OrderDetailsResource($order), 'error' => []]);
    } catch (\Exception $e) {
      return response()->json(['status' => true, 'data' => [], 'error' => ['message' => $e->getMessage()]]);
    }
  }

  public function cancelOrder($id)
  {
    try {
      // Find the order
      $order = Order::findOrFail($id);

      if ($order->user_id !== auth()->user()->id) {
        return response()->json(['status' => false, 'error' => 'Unauthorized'], 401);
      }

      if (!$order->isCancellable()) {
        return response()->json(['status' => false, 'error' => 'Order cannot be cancelled'], 400);
      }

      $order->status = 'declined';
      $order->save();

      return response()->json(['status' => true, 'message' => 'Order declined successfully']);
    } catch (\Exception $e) {
      return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
    }
  }


  public function Multipleorder(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'products' => 'required|json',
      'discount' => 'required',
      'delivery_fee' => 'required',
      'address' => 'required',
      'method' => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json(['status' => false, 'error' => $validator->errors()], 400);
    }

    $products = json_decode($request->input('products'), true);

    if (!is_array($products)) {
      return response()->json(['status' => false, 'error' => 'Invalid product data'], 400);
    }

    $userId = $request->user()->id;

    \DB::beginTransaction();
    $orderNumber = 'UZV' . mt_rand(100000000000, 999999999999);
    try {
      $orderData = [];
      $totalQty = 0;
      $totalPrice = 0;

      foreach ($products as $product) {
        if (!isset($product['id'])) {
          \DB::rollback();
          return response()->json(['status' => false, 'error' => 'Product ID is required'], 400);
        }

        $productDetails = Product::find($product['id']);

        if (!$productDetails) {
          \DB::rollback();
          return response()->json(['status' => false, 'error' => 'Product not found'], 404);
        }

        $orderData[] = [
          'user_id' => $userId,
          'product_id' => $product['id'],
          'method' => $request->method,
          'shipping' => $request->shipping_address,
          'pickup_location' => $request->address,
          'totalQty' => $product['totalQty'],
          'order_number' => $orderNumber,
          'payment_status' => 'pending',
          'product' => [
            'id' => $productDetails->id,
            'user_id' => $productDetails->user_id,
            'category_id' => $productDetails->category_id,
            'campaign_product' => $productDetails->campaign_product,
            'campaign_id' => $productDetails->campaign_id,
            'product_type' => $productDetails->product_type,
            'affiliate_link' => $productDetails->affiliate_link,
            'sku' => $productDetails->sku,
            'subcategory_id' => $productDetails->subcategory_id,
            'childcategory_id' => $productDetails->childcategory_id,
            'attributes' => $productDetails->attributes,
            'name' => $productDetails->name,
            'photo' => $productDetails->photo,
            'size' => $productDetails->size,
            'size_qty' => $productDetails->size_qty,
            'size_price' => $productDetails->size_price,
            'color' => $productDetails->color,
            'details' => $productDetails->details,
            'price' => $productDetails->price,
            'previous_price' => $productDetails->previous_price,
            'stock' => $productDetails->stock,
            'policy' => $productDetails->policy,
            'status' => $productDetails->status,
            'views' => $productDetails->views,
            'tags' => $productDetails->tags,
            'featured' => $productDetails->featured,
            'best' => $productDetails->best,
            'top' => $productDetails->top,
            'hot' => $productDetails->hot,
            'latest' => $productDetails->latest,
            'big' => $productDetails->big,
            'trending' => $productDetails->trending,
            'sale' => $productDetails->sale,
            'features' => $productDetails->features,
            'colors' => $productDetails->colors,
            'product_condition' => $productDetails->product_condition,
            'ship' => $productDetails->ship,
            'meta_tag' => $productDetails->meta_tag,
            'meta_description' => $productDetails->meta_description,
            'youtube' => $productDetails->youtube,
            'type' => $productDetails->type,
            'file' => $productDetails->file,
            'license' => $productDetails->license,
            'license_qty' => $productDetails->license_qty,
            'link' => $productDetails->link,
            'platform' => $productDetails->platform,
            'region' => $productDetails->region,
            'licence_type' => $productDetails->licence_type,
            'measure' => $productDetails->measure,
            'discount_date' => $productDetails->discount_date,
            'is_discount' => $productDetails->is_discount,
            'whole_sell_qty' => $productDetails->whole_sell_qty,
            'whole_sell_discount' => $productDetails->whole_sell_discount,
            'catalog_id' => $productDetails->catalog_id,
            'slug' => $productDetails->slug,
            'brand_id' => $productDetails->brand_id,
            'discount_percent' => $productDetails->discount_percent,
            // Include more fields as needed
          ],
        ];



        $totalQty += $product['totalQty'];
        $totalPrice += $productDetails->price * $product['totalQty'];
      }
      $cartData = json_encode($orderData);

      $order = Order::create([
        'user_id' => $userId,
        'cart' => $cartData,
        'method' => $request->method,
        'shipping' => $request->shipping_address,
        'pickup_location' => $request->address,
        'totalQty' => $totalQty,
        'order_number' => $orderNumber,
        'payment_status' => 'pending',
        'shipping' => 'shipto',
        'shipping_country' => $request->shipping_country ?? 'Bangladesh',
        'pickup_location' => 'Bangladesh',
        'customer_email' => $request->user()->email,
        'customer_name' => $request->user()->name,
        'customer_country' => 'Bangladesh',
        'customer_phone' => $request->user()->phone,
        'customer_address' => $request->user()->address,
        'currency_sign' => '৳',
        'shipping_title' => 'Shipping Charge Not Applicable Up To 5KG',
        'packing_title' => 'Default Packaging',
        'pay_amount' => $totalPrice,
      ]);

      \DB::commit();

      return response()->json([
        'status' => true,
        'message' => 'Order placed successfully',
        'order' => $order,
      ], 200);
    } catch (\Exception $e) {
      \DB::rollback();
      return response()->json(['status' => false, 'error' => 'Failed to place order'], 500);
    }
  }
}
