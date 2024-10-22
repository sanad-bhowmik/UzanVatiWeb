<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/foster/notify', 'Front\FosterController@notify')->name('foster.notify');
Route::post('/foster/sub/notify', 'User\FosterController@notify')->name('user.foster.notify');
Route::post('/foster/vendor/notify', 'Vendor\FosterController@notify')->name('vendor.foster.notify');


Route::post('/bkash/sub/notify', 'User\FosterController@notify')->name('user.foster.notify');
Route::post('/bkash/vendor/notify', 'Vendor\FosterController@notify')->name('vendor.foster.notify');
 

// |--------------------------------------------------------------------------
// | API Routes
// |--------------------------------------------------------------------------
// |
// | Here is where you can register API routes for your application. These
// | routes are loaded by the RouteServiceProvider within a group which
// | is assigned the "api" middleware group. Enjoy building your API!
// |


Route::group(['prefix' => 'vendor'], function () {
    
    Route::post('register', 'Api\Auth\VendorAuthController@register');
    Route::post('login', 'Api\Auth\VendorAuthController@login');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::group(['middleware' => 'vendor'], function () {


            // --------------------- VENDOR DASHBOARD ---------------------
            
            Route::get('/dashboard', 'Api\Vendor\VendorController@dashboard');
    
            // --------------------- VENDOR DASHBOARD ENDS ---------------------


            // ---------------------VENDOR CONTROLLER ---------------------

            Route::get('/details', 'Api\Vendor\VendorController@details');
            Route::post('/verify', 'Api\Vendor\VendorController@verifysubmit');
            Route::post('/profile/update', 'Api\Vendor\VendorController@profileupdate');
            Route::post('/social/link/update', 'Api\Vendor\VendorController@social_link_update');
 

            // ---------------------VENDOR CONTROLLER ---------------------

            // ---------------------NOTIFICATION CONTROLLER ---------------------

            Route::get('/order/notf/show', 'Api\Vendor\NotificationController@order_notf_show');
            Route::get('/order/notf/count','Api\Vendor\NotificationController@order_notf_count');
            Route::get('/order/notf/clear','Api\Vendor\NotificationController@order_notf_clear');

            // ---------------------NOTIFICATION CONTROLLER ENDS ---------------------

            // ---------------------ORDER CONTROLLER ---------------------

            Route::get('/orders', 'Api\Vendor\OrderController@index');
            Route::get('/order/{slug}/show', 'Api\Vendor\OrderController@show')->name('vorder-show');
            Route::get('/order/{slug}/status/{status}', 'Api\Vendor\OrderController@status');
            Route::post('/order/{slug}/license', 'Api\Vendor\OrderController@license');

            // ---------------------ORDER CONTROLLER ENDS ---------------------

            // ---------------------PRODUCT CONTROLLER ---------------------

            Route::get('/products', 'Api\Vendor\ProductController@index');
            Route::get('/product/input', 'Api\Vendor\ProductController@formInput');
            Route::post('/product/store', 'Api\Vendor\ProductController@store');
            Route::post('/product/update/{id}', 'Api\Vendor\ProductController@update');
            Route::post('/product/catalog/{id}', 'Api\Vendor\ProductController@catalogupdate');
            Route::get('/product/status/{id1}/{id2}', 'Api\Vendor\ProductController@status');
            Route::get('/product/delete/{id}', 'Api\Vendor\ProductController@destroy');
            Route::get('/product/getattributes', 'Api\Vendor\ProductController@getAttributes');

            // ---------------------PRODUCT CONTROLLER ENDS ---------------------

            // ---------------------GALLERY CONTROLLER ---------------------

            Route::get('/gallery/show/{id}', 'Api\Vendor\GalleryController@show');
            Route::post('/gallery/store', 'Api\Vendor\GalleryController@store');
            Route::get('/gallery/delete/{id}', 'Api\Vendor\GalleryController@destroy');

            // ---------------------GALLERY CONTROLLER ENDS ---------------------

            // ---------------------PACKAGE CONTROLLER ---------------------

            Route::get('/packages', 'Api\Vendor\PackageController@index');
            Route::post('/package/store', 'Api\Vendor\PackageController@store');
            Route::post('/package/update/{id}', 'Api\Vendor\PackageController@update');
            Route::get('/package/delete/{id}', 'Api\Vendor\PackageController@destroy');

            // ---------------------PACKAGE CONTROLLER ENDS ---------------------

            // ---------------------SHIPPING CONTROLLER ---------------------

            Route::get('/shippings', 'Api\Vendor\ShippingController@index');
            Route::post('/shipping/store', 'Api\Vendor\ShippingController@store');
            Route::post('/shipping/update/{id}', 'Api\Vendor\ShippingController@update');
            Route::get('/shipping/delete/{id}', 'Api\Vendor\ShippingController@destroy');

            // ---------------------SHIPPING CONTROLLER ENDS ---------------------

            // ---------------------SERVICE CONTROLLER ---------------------

            Route::get('/services', 'Api\Vendor\ServiceController@index');
            Route::post('/service/store', 'Api\Vendor\ServiceController@store');
            Route::post('/service/update/{id}', 'Api\Vendor\ServiceController@update');
            Route::get('/service/delete/{id}', 'Api\Vendor\ServiceController@destroy');

            // ---------------------SERVICE CONTROLLER ENDS ---------------------

            // ---------------------WITHDRAW CONTROLLER ---------------------

            Route::get('/withdraws', 'Api\Vendor\WithdrawController@index');
            Route::post('/withdraw/create', 'Api\Vendor\WithdrawController@store');

            // ---------------------WITHDRAW CONTROLLER ENDS ---------------------

        });
    });

});

Route::group(['prefix' => 'user'], function () {

    Route::post('registration', 'Api\Auth\AuthController@register');
    Route::post('login', 'Api\Auth\AuthController@login');
    Route::post('logout', 'Api\Auth\AuthController@logout');
    Route::post('forgot', 'Api\Auth\AuthController@forgot');
    Route::get('getUserData', 'Api\Auth\AuthController@getUserData');
    Route::post('forgot/submit', 'Api\Auth\AuthController@forgot_submit');
    Route::post('social/login', 'Api\Auth\AuthController@social_login');
    Route::post('refresh/token', 'Api\Auth\AuthController@refresh');
    Route::get('details', 'Api\Auth\AuthController@details');
    Route::post('verify/otp', 'Api\Auth\AuthController@verifyOtp');
    Route::post('deleteAccount', 'Api\Auth\AuthController@deleteAccount');
    
    Route::group(['middleware' => 'auth:api'], function () {


        // --------------------- USER DASHBOARD ---------------------
        
        Route::get('/dashboard', 'Api\User\ProfileController@dashboard');

        // --------------------- USER DASHBOARD ENDS ---------------------


        // --------------------- USER PROFILE ---------------------

       Route::post('/profile/update', 'Api\User\ProfileController@update');
        Route::post('/profile/updateImg', 'Api\User\ProfileController@updateImg');
        Route::post('/password/update', 'Api\User\ProfileController@updatePassword');
          Route::post('/address/update', 'Api\User\ProfileController@updateadd');
        Route::post('/address/add', 'Api\User\ProfileController@add');
        Route::post('/address/delete', 'Api\User\ProfileController@delete');

        // --------------------- USER PROFILE ENDS ---------------------

        // --------------------- USER FAVORITE ---------------------

        Route::get('/favorite/vendors', 'Api\User\ProfileController@favorites');
        Route::post('/favorite/store', 'Api\User\ProfileController@favorite');
        Route::get('/favorite/delete/{id}', 'Api\User\ProfileController@favdelete');

        // --------------------- USER FAVORITE ENDS ---------------------


        // --------------------- TICKET & DISPUTE ---------------------

        Route::get('/tickets', 'Api\User\TicketDisputeController@tickets');
        Route::get('/disputes', 'Api\User\TicketDisputeController@disputes');
        Route::post('/ticket-dispute/store', 'Api\User\TicketDisputeController@store');
        Route::get('/ticket-dispute/{id}/delete', 'Api\User\TicketDisputeController@delete');
        Route::post('/ticket-dispute/message/store', 'Api\User\TicketDisputeController@messageStore');

        // --------------------- TICKET & DISPUTE ENDS ---------------------

        // ---------------------MESSAGE CONTROLLER ---------------------

        Route::post('/message/store', 'Api\User\MessageController@usercontact');
        Route::post('/message/post', 'Api\User\MessageController@postmessage');
        Route::get('/messages', 'Api\User\MessageController@messages');
        Route::get('/message/{id}/delete', 'Api\User\MessageController@messagedelete');

        // ---------------------MESSAGE CONTROLLER ENDS ---------------------


        // ---------------------PRODUCT CONTROLLER ---------------------

        Route::post('/reviewsubmit', 'Api\User\ProductController@reviewsubmit');
        Route::post('/commentstore', 'Api\User\ProductController@commentstore');
        Route::post('/commentupdate', 'Api\User\ProductController@commentupdate');
        Route::post('/replystore', 'Api\User\ProductController@replystore');
        Route::post('/replyupdate', 'Api\User\ProductController@replyupdate');
        Route::post('/reportstore', 'Api\User\ProductController@reportstore');
        Route::get('/comment/{id}/delete', 'Api\User\ProductController@commentdelete');
        Route::get('/reply/{id}/delete', 'Api\User\ProductController@replydelete');

        // ---------------------PRODUCT CONTROLLER ENDS ---------------------

        // ---------------------ORDER CONTROLLER ---------------------

        Route::get('/orders', 'Api\User\OrderController@orders')->name('orders');
        Route::get('/order/{id}/details', 'Api\User\OrderController@order')->name('order');
        Route::post('/update/transactionid', 'Api\User\OrderController@updateTransaction');
        Route::post('/order/{id}/cancel', 'Api\User\OrderController@cancelOrder')->name('order.cancel');
        Route::post('/order', 'Api\User\OrderController@orderProducts')->name('order.products');
        Route::post('/cartorder', 'Api\User\OrderController@Multipleorder')->name('order.multiple');
        // ---------------------ORDER CONTROLLER ENDS ---------------------

        // ---------------------WITHDRAW CONTROLLER ---------------------

        Route::get('/withdraws', 'Api\User\WithdrawController@index');
        Route::get('/withdraw/methods/field', 'Api\User\WithdrawController@methods_field');
        Route::post('/withdraw/create', 'Api\User\WithdrawController@store');

        // ---------------------WITHDRAW CONTROLLER ENDS ---------------------
        
        
        // ---------------------WISHLIST CONTROLLER ---------------------

        Route::get('/wishlists','Api\User\WishlistController@wishlists');
        Route::post('/wishlist/add','Api\User\WishlistController@addwish');
        Route::get('/wishlist/remove/{id}','Api\User\WishlistController@removewish');

        // ---------------------WISHLIST CONTROLLER ---------------------        
        

        // ---------------------PACKAGE CONTROLLER ---------------------

        Route::get('/packages', 'Api\User\PackageController@packages');
        Route::get('/package/details', 'Api\User\PackageController@packageDetails');
        Route::post('/package/store', 'Api\User\PackageController@store');

        // ---------------------PACKAGE CONTROLLER ENDS ---------------------

        // ---------------------DEPOSIT CONTROLLER ---------------------

        Route::get('/deposits', 'Api\User\DepositController@deposits');
        Route::post('/deposit/store', 'Api\User\DepositController@store');
        Route::get('/transactions', 'Api\User\DepositController@transactions');
        Route::get('/transaction/details', 'Api\User\DepositController@transactionDetails');

        // ---------------------DEPOSIT CONTROLLER ENDS ---------------------

    });

});


Route::group(['prefix' => 'front'], function () {

    //------------ Frontend Controller ------------
    Route::get('/section-customization', 'Api\Front\FrontendController@section_customization');
    Route::get('/sliders', 'Api\Front\FrontendController@sliders');
    Route::get('/default/language', 'Api\Front\FrontendController@defaultLanguage');
    Route::get('/language/{id}', 'Api\Front\FrontendController@language');
    Route::get('/languages', 'Api\Front\FrontendController@languages');
    Route::get('/default/currency', 'Api\Front\FrontendController@defaultCurrency');
    Route::get('/currency/{id}', 'Api\Front\FrontendController@currency');
    Route::get('/currencies', 'Api\Front\FrontendController@currencies');
    Route::get('/featured-links', 'Api\Front\FrontendController@featuredLinks');
    Route::get('/featured-banners', 'Api\Front\FrontendController@featuredBanners');
    Route::get('/services', 'Api\Front\FrontendController@services');
    Route::get('/banners', 'Api\Front\FrontendController@banners');
    Route::get('/partners', 'Api\Front\FrontendController@partners');
    Route::get('/products', 'Api\Front\FrontendController@products');
    Route::get('/vendor/products/{id}', 'Api\Front\FrontendController@vendor_products');
    Route::get('/settings', 'Api\Front\FrontendController@settings');
    Route::get('/faqs', 'Api\Front\FrontendController@faqs');
    Route::get('/blogs', 'Api\Front\FrontendController@blogs');
    Route::get('/pages', 'Api\Front\FrontendController@pages');
    Route::get('/ordertrack','Api\Front\FrontendController@ordertrack');
    Route::get('/orders/filter', 'Api\Front\FrontendController@filterOrdersByStatus');
    Route::post('/contactmail', 'Api\Front\FrontendController@contactmail');
    Route::get('/brands', 'Api\Front\FrontendController@getBrands');
    Route::get('/vendorlist', 'Api\Front\FrontendController@vendorlist');
    Route::get('/ShopProducts', 'Api\Front\FrontendController@getVendorProducts');
    Route::get('/order/product', 'Api\Front\FrontendController@orderProduct');
    Route::get('/brand/products', 'Api\Front\FrontendController@getBrandProducts');

    //------------ Frontend Controller Ends ------------

    //------------ Search Controller ------------

    Route::get('/search','Api\Front\SearchController@search');
    Route::get('/categories', 'Api\Front\SearchController@categories');
    Route::post('/categories/{id?}', 'Api\Front\SearchController@categories1');
    Route::get('{id}/category', 'Api\Front\SearchController@category');
    Route::get('/{id}/subcategories', 'Api\Front\SearchController@subcategories')->name('subcategories');
    Route::get('/{id}/childcategories', 'Api\Front\SearchController@childcategories')->name('childcategories');
    Route::get('/attributes/{id}', 'Api\Front\SearchController@attributes')->name('attibutes');
    Route::get('/attributeoptions/{id}', 'Api\Front\SearchController@attributeoptions')->name('attibute.options');

    //------------ Search Controller Ends ------------

    //------------ Product Controller ------------

    Route::get('/product/{id}/details', 'Api\Front\ProductController@productDetails');
    Route::get('/product/{id}/ratings', 'Api\Front\ProductController@ratings');
    Route::get('/product/{id}/comments', 'Api\Front\ProductController@comments');
    Route::get('/product/{id}/replies', 'Api\Front\ProductController@replies');

    //------------ Product Controller Ends ------------

    //------------ Vendor Controller ------------

    Route::get('/store/{shop_name}','Api\Front\VendorController@index')->name('front.vendor');
    Route::post('/store/contact','Api\Front\VendorController@vendorcontact');

    //------------ Vendor Controller ------------


    //------------ Checkout Controller ------------

    Route::post('/checkout','Api\Front\CheckoutController@checkout');
    Route::get('/order/details','Api\Front\CheckoutController@orderDetails');
    Route::get('/get/coupon-code','Api\Front\CheckoutController@getCoupon');
    Route::post('/checkout/update/{id}','Api\Front\CheckoutController@update');
    Route::get('/checkout/delete/{id}','Api\Front\CheckoutController@delete');
    
    //------------ Checkout Controller ------------

});

Route::fallback(function () {
    return response()->json(['status' => false, 'data' => [], 'error' => ['message' => 'Not Found!']], 404);
});



