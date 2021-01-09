<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/', function () {
    return redirect('admin');
});

Route::group(['prefix' => 'admin'], function () {
    Route::get('orders/assign','OrderController@assign')->name('orders.assign');
    Voyager::routes();
});
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'admin','middleware' => 'auth'], function () {
    
    Route::post('deliveryFeeModify','HomeController@deliveryFeeModify');
    Route::get('reports','ChartController@index');
    Route::get('daily-report','ChartController@daily_report');
}); 
Route::get('sendsms/{order_id}','SMSandNotificationController@sendUpdate')->name('sendSMS');

