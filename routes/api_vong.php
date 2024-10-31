<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Api\V1','prefix' => 'v1','as'=>'api.', 'middleware' => 'verify_shop'],function(){
    //Minigame
    Route::get('/minigame/get-minigame-info','MinigameController@getMiniGameInfo');
    Route::get('/minigame/get-list-minigame','MinigameController@getListMiniGame');
	Route::get('/minigame/bonus', 'MinigameController@getBonus');

    Route::group(['middleware' => 'auth_api','activity_log'],function(){
	    Route::get('/minigame/get-log','MinigameController@getLog');
	    Route::get('/minigame/get-logacc','MinigameController@getLogAcc');
	    Route::post('/minigame/post-minigame','MinigameController@postMinigame');
	    Route::post('/minigame/post-minigamebonus','MinigameController@postMinigameBonus');
	    Route::get('/minigame/get-withdraw-item','MinigameController@getWithdrawItem');
	    Route::post('/minigame/post-withdraw-item','MinigameController@postWithdrawItem');
	    Route::get('/minigame/tichhop-callback/{tranid}', 'MinigameController@getCallback');
	    Route::post('/minigame/bonus', 'MinigameController@postBonus');
	});

});

Route::group(['namespace' => 'Api\V1','prefix' => 'v1','as'=>'api.'],function(){
	Route::get('/minigame/tichhop-callback/{tranid}', 'MinigameController@getCallback');
});
