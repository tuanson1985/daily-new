<?php


use Illuminate\Support\Facades\Route;








Route::group(['namespace' => 'Api\V1','prefix' => 'v1','as'=>'api.','middleware' => 'verify_shop'],function(){

    //Service
    Route::group(['middleware' => 'auth_api','activity_log'],function(){

        Route::post('/service/purchase','Service\ServiceController@postPurchase');
        Route::get('/service/log','Service\ServiceController@getLog');
        Route::get('/service/log/detail', 'Service\ServiceController@getLogDetail');
        Route::post('/service/log/detail/edit-info/{id}', 'Service\ServiceController@postEditInfo');
        Route::post('/service/log/detail/edit-info/{id}', 'Service\ServiceController@postEditInfo');
        Route::post('/service/log/detail/destroy/{id}', 'Service\ServiceController@postDestroy');
        Route::post('/service/log/detail/refund-order/{id}', 'Service\ServiceController@postRefundOrder');
        Route::post('/service/log/detail/delete-refund-order/{id}', 'Service\ServiceController@postDeleteRefundOrder');
        //Route::get('/service/log/process', 'Frontend\ServiceController@getProcess');
    });
    Route::get('/service/list-bot/{slug}','Service\ServiceController@listBot');
    Route::resource('/service','Service\ServiceController')->only(['index','show']);
    //END Service


    //Inbox
    Route::group(['middleware' => 'auth_api','activity_log'],function(){
        //Inbox
        Route::get('/inbox', 'Inbox\InboxController@getlist');
        Route::get('/inbox/{id}/send', 'Inbox\InboxController@getSend');
        Route::post('/inbox/{id}/send', 'Inbox\InboxController@postSend');
        Route::post('/inbox/{id}/seen', 'Inbox\InboxController@postSeen');


    });
    //END Inbox





});


//listen callback service auto
Route::group(['namespace' => 'Api\V1','prefix' => 'v1','as'=>'api.'],function(){

    Route::any('/services-auto-callback', 'Service\ServiceAutoListenCallbackController@getCallback');
    Route::any('/services-auto-callback-daily', 'Service\ServiceAutoListenCallbackController@getCallbackDaily');
    Route::any('/services-auto-callback-bot-roblox','Service\ServiceAutoListenCallbackController@getCallbackBotRoblox');

});

