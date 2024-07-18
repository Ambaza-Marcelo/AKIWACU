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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
 });

Route::group(['prefix' => 'admin'], function () {

	//table api routes
	Route::post('akwacu-api/getTable', 'Backend\TableController@getTable')->name('akiwacu_api.getTable');
	Route::post('akwacu-api/addTransaction', 'Backend\TableController@addTransaction')->name('akiwacu_api.addTransaction');

	//drink order api routes
	Route::post('akwacu-api/getDrinkOrder', 'Backend\OrderDrinkController@getDrinkOrder')->name('akiwacu_api.getDrinkOrder');
	Route::post('akwacu-api/getDrinkOrderDetail', 'Backend\OrderDrinkController@getDrinkOrderDetail')->name('akiwacu_api.getDrinkOrderDetail');
	Route::post('akwacu-api/addDrinkOrder', 'Backend\OrderDrinkController@addDrinkOrder')->name('akiwacu_api.addDrinkOrder');

	//food orders api routes
	Route::post('akwacu-api/getFoodOrder', 'Backend\OrderKitchenController@getFoodOrder')->name('akiwacu_api.getFoodOrder');
	Route::post('akwacu-api/getFoodOrderDetail', 'Backend\OrderKitchenController@getFoodOrderDetail')->name('akiwacu_api.getFoodOrderDetail');
	Route::post('akwacu-api/addFoodOrder', 'Backend\OrderKitchenController@addFoodOrder')->name('akiwacu_api.addFoodOrder');

	//barrista order api routes
	Route::post('akwacu-api/getBarristaOrder', 'Backend\BarristOrderController@getBarristaOrder')->name('akiwacu_api.getBarristaOrder');
	Route::post('akwacu-api/getBarristaOrderDetail', 'Backend\BarristOrderController@getBarristaOrderDetail')->name('akiwacu_api.getBarristaOrderDetail');
	Route::post('akwacu-api/addBarristaOrder', 'Backend\BarristOrderController@addBarristaOrder')->name('akiwacu_api.addBarristaOrder');

	//bartender order api routes
	Route::post('akwacu-api/getBartenderOrder', 'Backend\BartenderOrderController@getBartenderOrder')->name('akiwacu_api.getBartenderOrder');
	Route::post('akwacu-api/getBartenderOrderDetail', 'Backend\BartenderOrderController@getBartenderOrderDetail')->name('akiwacu_api.getBartenderOrderDetail');
	Route::post('akwacu-api/addBartenderOrder', 'Backend\BartenderOrderController@addBartenderOrder')->name('akiwacu_api.addBartenderOrder');
	//invoice api routes

	Route::post('akwacu-api/addDrinkInvoice', 'Backend\FactureController@addDrinkInvoice')->name('akiwacu_api.addDrinkInvoice');
	Route::post('akwacu-api/addFoodInvoice', 'Backend\FactureRestaurantController@addFoodInvoice')->name('akiwacu_api.addFoodInvoice');
	Route::post('akwacu-api/addBarristaInvoice', 'Backend\FactureBarristController@addBarristaInvoice')->name('akiwacu_api.addBarristaInvoice');
	Route::post('akwacu-api/addBartenderInvoice', 'Backend\FactureBartenderController@addBartenderInvoice')->name('akiwacu_api.addBartenderInvoice');
	Route::post('akwacu-api/addBookingInvoice', 'Backend\FactureBookingController@addBookingInvoice')->name('akiwacu_api.addBookingInvoice');

});

