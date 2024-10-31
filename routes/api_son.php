<?php


use Illuminate\Support\Facades\Route;


Route::group(['namespace' => 'Api\V1','prefix' => 'v1','as'=>'api.','middleware' => 'verify_shop'],function(){
    //Build menu
    Route::post('/menu-category','Advertise\AdvertiseController@postMenuCategory');

    Route::get('/menu-category/{id}','Advertise\AdvertiseController@getMenuCategory');

    Route::post('/menu-profile','Advertise\AdvertiseController@postMenuProfile');

    Route::post('/menu-transaction','Advertise\AdvertiseController@postMenuTransaction');

    Route::get('/get-slider-banner','Advertise\AdvertiseController@getBannerHome');

    Route::get('/get-slider-banner-service','Advertise\AdvertiseController@getBannerService');

    Route::get('/get-slider-banner-nick','Advertise\AdvertiseController@getBannerNick');

    Route::get('/get-slider-banner-minigame','Advertise\AdvertiseController@getBannerMinigame');

    Route::get('/get-slider-banner-article','Advertise\AdvertiseController@getBannerArticle');

    Route::get('/get-slider-banner-change','Advertise\AdvertiseController@getBannerChange');

    Route::get('/get-slider-banner-ads','Advertise\AdvertiseController@getBannerAds');

    Route::get('/get-dich-vu-noibat','Advertise\AdvertiseController@getDichVuNoiBat');

    Route::get('/get-show-service','Advertise\AdvertiseController@getShowService');

    Route::get('/get-relate-acc','Advertise\AdvertiseController@getRelatedAcc');

    Route::get('/get-recommend','Advertise\AdvertiseController@getRecommend');

    Route::get('/get-random-acc','Advertise\AdvertiseController@random_category_list');

    Route::resource('/article','ArticleController');

    Route::get('/get-category','ArticleController@getCategory');

    Route::get('/show-category-article','ArticleController@getShowCategory');

    Route::get('/get-home-position','Advertise\AdvertiseController@getHomePosition');


    // Route cần Auth

    Route::group(['middleware' => 'auth_api','activity_log'],function(){

        Route::resource('/get-txns','Txns\TxnsController');

        Route::get('/service-history','Advertise\AdvertiseController@showServiceHistory');

    });

});

