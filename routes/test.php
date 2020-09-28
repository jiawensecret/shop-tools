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

Route::group(['middleware' => ['test']],function (){
    Route::resources([
        'orders' => 'OrderController',
        'transports' => 'TransportController',
        'person' => 'PersonController',
        'shops' => 'ShopController',
        'accounts' => 'AccountController',
        'ad-prices' => 'AdPriceController',
        'sale-volumes'=>'SaleVolumeController'
    ]);

    Route::post('/excel/upload','CommonController@uploadExcel');

    Route::post('/profit-report','SaleVolumeController@profitReport');//计算利润

    Route::get('/show-excel-job','CommonController@showExcelJob');
    Route::get('/show-calculate-job','CommonController@showCalculateJob');

    Route::get('/month-list','CommonController@monthList');
    Route::get('/shop-list','CommonController@shopList');

    Route::get('/sale-volume-order/{id}','SaleVolumeOrderController@index');

    Route::post('/login/account','AdminController@login');
    Route::get('/currentUser','AdminController@getAdminInfo');

});

