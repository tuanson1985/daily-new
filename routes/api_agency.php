<?php


use Illuminate\Support\Facades\Route;








Route::group(['namespace' => 'Api\V1','prefix' => 'v1','as'=>'api.','middleware' => 'localization'],function(){

//Agency trade --- Buy NROGEM,NROCOIN
    Route::any('/agency-service', 'AgencyService\IndexController@buy');

    Route::any('/agency-service/edit-info', 'AgencyService\IndexController@editInfo');

    Route::any('/agency-service/show-sms', 'AgencyService\IndexController@getSMSDaily');

    Route::any('/agency-service/show-service/{slug}', 'AgencyService\IndexController@getService');

    Route::any('/agency-service/get-price-service', 'AgencyService\IndexController@getPriceService');

    Route::any('/agency-service/destroy-order', 'AgencyService\IndexController@destroyOrder');

    Route::any('/agency-service/post-refund-order', 'AgencyService\IndexController@postRefundOrder');

    Route::any('/agency-service/delete-refund-order', 'AgencyService\IndexController@deleteRefundOrder');

    Route::any('/agency-service/check-order', 'AgencyService\IndexController@checkOrder');

    Route::any('/agency-service/check-order-auto', 'AgencyService\IndexController@checkAutoOrder');

    Route::any('/agency-service/check-account-information', 'AgencyService\IndexController@showBotCheckAccountInformation');
    ////callback service auto
    Route::any('/services-auto-callback/{site}', 'AgencyService\ServiceAutoListenCallbackController@getCallback');

    //ODP api
    Route::any('/odp-verify', 'AgencyService\OdpController@OdpVerify');

//report
    Route::any('/report/money', 'AgencyService\ReportController@money');

    Route::any('/report/bomvang', 'AgencyService\ReportController@bomvang');
    Route::any('/report/rutvang', 'AgencyService\ReportController@rutvang');
    Route::any('/report/muavang', 'AgencyService\ReportController@muavang');
    //tạo user
    Route::any('/user/store', 'AgencyService\UserController@store');
    Route::any('/user/show', 'AgencyService\UserController@show');

});


//listen callback service auto
Route::group(['namespace' => 'Api\V1','prefix' => 'v1','as'=>'api.'],function(){



});

