<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:api')->post('/user', function (Request $request) {
    return $request->user();
});
//user auth
Route::post('loginUser', 'API\UserController@login');
Route::post('registerUser', 'API\UserController@register');
Route::post('logoutUser', 'API\UserController@logout');

//restaurants
Route::post('getShopNames','API\DBController@restaurants');
// Route::post('loginRestaurant', 'API\UserController@login');
// Route::post('registerUser', 'API\UserController@register');
// Route::post('logoutUser', 'API\UserController@logout');

//categories
//earlier getAllCategories was written as route name
Route::post('getCategories','API\DBController@categories');
Route::post('getAllSubCategoriesForCategory','API\DBController@subCategories');

//category wise product
Route::post('getAllProductsForCategory','API\DBController@allCategoryProducts');
Route::post('getAllProductsForSubCategory','API\DBController@allSubCategoryProducts');

//products
Route::post('getProductDetails','API\DBController@productDetails');
Route::post('getPopularProducts','API\DBController@allPopularProducts');
Route::post('getAllPopularProducts','API\DBController@popularProducts');
Route::post('getRecommendedProducts','API\DBController@recommendedProducts');
Route::post('getOffers','API\DBController@offerProducts');
Route::post('getGoodQualityProducts','API\DBController@goodQualityProducts');
Route::post('getAllGoodQualityProducts','API\DBController@allGoodQualityProducts');
Route::post('getProducts',"API\DBController@products");//subcategory

//coupons
Route::post('getAllActiveCoupons','API\DBController@allActiveCoupons');

//product image
Route::get('getProductImage','API\DBController@getProductImage');

//coupon&voucher
Route::post('checkVoucher','API\DBController@checkCoupon');

//deliveryfee
Route::post('getDeliveryFee','API\DeliveryController@deliveryFee');

//search
//earlier route name was searchProducts
Route::post('getProduct','API\DBController@search');
Route::post('searchProducts','API\DBController@search');

// forget password
Route::post('forgotPassword', 'Auth\ForgotPasswordController@getResetToken');

// //reset password
// Route::post('password/reset', 'Auth\ResetPasswordController@reset');
// //user verification
// Route::get('email/verify/{token}', 'Auth\VerificationController@verify');

Route::group(['middleware' => 'auth:api'], function(){
    //details edit
    Route::post('updateProfile', 'API\UserController@updateProfile');
    Route::post('uploadProfileImage', 'API\UserController@uploadProfileImage');

    //feedback
    Route::post('submitFeedback','API\DBController@submitFeedback');
    //orders
    Route::post('getAllProductsInOrder','API\DBController@orderProducts');
    Route::post('placeOrder','API\DBController@placeOrder');
    Route::post('getAllOrdersOfUser','API\DBController@allOrdersUser');
    //deliver location
    Route::post('getDeliveryLocation','API\DBController@deliveryLocation');
});
Route::get('getUserImage', 'API\UserController@getUserImage');
Route::get('getCategoryImage', 'API\DBController@getCategoryImage');

// DELIVERY SIDE APIS
Route::group(['middleware' => 'auth:api'], function(){
    Route::post('getOrdersCountForDeliveryGuy','API\DeliveryController@getOrdersCountForDeliveryGuy');

    //status
    Route::post('getAllPendingOrders','API\DeliveryController@getAllPendingOrders');
    Route::post('getAllProcessingOrders','API\DeliveryController@getAllProcessingOrders');
    Route::post('getAllCompletedOrders','API\DeliveryController@getAllCompletedOrders');
    Route::post('changeStatusOfOrder','API\DeliveryController@changeStatusOfOrder');
//location
    Route::post('updateDeliveryLocation','API\DeliveryController@updateDeliveryLocation');
//code verify
    Route::post('verifyCode','API\DeliveryController@verifyCode');
});
//signature upload
Route::post('uploadSignature','API\DeliveryController@uploadSignature');
Route::post('addOrder','API\OrderController@saveOrder');


