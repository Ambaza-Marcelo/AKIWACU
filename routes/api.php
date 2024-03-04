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

Route::post('ebms_api/login', 'Backend\FactureController@login')->name('ebms_api.login');
	Route::post('ebms_api/getInvoice', 'Backend\FactureController@getInvoice')->name('ebms_api.getInvoice');
	Route::post('ebms_api/addInvoiceConfirm/{invoice_number}', 'Backend\FactureController@addInvoiceConfirm')->name('ebms_api.addInvoice');
	Route::post('ebms_api/checkTIN', 'Backend\FactureController@checkTIN')->name('ebms_api.checkTIN');
	Route::post('ebms_api/cancelInvoice/{invoice_number}', 'Backend\FactureController@cancelInvoice')->name('ebms_api.cancelInvoice');
	Route::post('ebms_api/addStockMovement', 'Backend\FactureController@addStockMovement')->name('ebms_api.addStockMovement');

	Route::post('musumba-steel-ebp-factures/login', 'Backend\FactureController@login')->name('musumba-steel-ebp-factures.login');
	Route::post('musumba-steel-ebp-factures/getInvoice', 'Backend\FactureController@getInvoice')->name('musumba-steel-ebp-factures.getInvoice');
	Route::post('musumba-steel-factures/checkTIN', 'Backend\FactureController@checkTIN')->name('musumba-steel-factures.checkTIN');
	Route::post('musumba-steel-factures/cancelInvoice/{invoice_number}', 'Backend\FactureController@cancelInvoice')->name('musumba-steel-factures.cancelInvoice');
