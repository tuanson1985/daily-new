<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Route::group(['namespace' => 'Api\V1','prefix' => 'v1','as'=>'api.'],function(){
    Route::post('/very-shop','Auth\LoginController@veryShop');
});
Route::group(['namespace' => 'Api\V1','prefix' => 'v1','as'=>'api.','middleware' => 'verify_shop'],function(){
    Route::post('/login','Auth\LoginController@login');
    Route::post('/refresh-token-remember','Auth\LoginController@refreshTokenRemember');
    Route::post('/register','Auth\RegisterController@register');
    Route::post('/token','UserController@postCheckToken');
    Route::get('/system/setting','SettingController@getSetting');
    Route::post('/loginfacebook', 'Auth\LoginController@loginfacebook');
    Route::get('/top-charge','Charge\ChargeController@getTopCharge');
    // nạp thẻ
    Route::get('/deposit-auto/get-telecom','Charge\ChargeController@getTelecomDepositAuto');
    Route::get('/deposit-auto/get-amount','Charge\ChargeController@getAmountDepositAuto');

    Route::get('/store-card/get-telecom','StoreCard\StoreCardController@getTelecomStoreCard');
    Route::get('/store-card/get-amount','StoreCard\StoreCardController@getAmount');

    // Route cần Auth
    Route::group(['middleware' => 'auth_api','api'],function(){
        Route::post('/refresh','Auth\LoginController@refresh_token');
        Route::post('/logout','Auth\LoginController@logout');
        Route::get('/profile','UserController@getProfile');
        Route::post('/current-password','UserController@postChangeCurrentPassword');

        // cộng tiền tự động
        Route::get('/transfer/history','Transfer\TransferController@getHistory'); // lịch sử
        Route::get('/transfer/history/{id}','Transfer\TransferController@getDetails'); // chi tiết
        Route::get('/transfer/get-code','Transfer\TransferController@getCode'); // Lấy mã nạp



        Route::post('/deposit-auto','Charge\ChargeController@postDepositAuto');
        Route::get('/deposit-auto/history','Charge\ChargeController@getHistory');
        Route::get('/deposit-auto/history/{id}','Charge\ChargeController@getDetails');

        // mua thẻ
        Route::get('/store-card/history','StoreCard\StoreCardController@getHistory');
        Route::get('/store-card/history/{id}','StoreCard\StoreCardController@getDetails');
        Route::post('/store-card','StoreCard\StoreCardController@postStoreCard');
    });
});
