<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Api\V1','prefix' => 'v1','as'=>'api.',],function(){
	// Acc
	Route::any('/acc-callback-1ght5', 'AccController@callback_login');
	Route::any('/acc', 'AccController@index')->middleware('verify_shop');
	Route::get('/acc-analytic', 'AccController@analytic');
	// Route::any('/acc', 'AccController@index');
});
Route::group(['namespace' => 'Api\V1\Upnick','prefix' => 'v1/upnick'],function(){
	Route::any('/lienminh', 'ToolController@lienminh');
	Route::any('/ninjaschool', 'ToolController@ninjaschool');
	Route::any('/nro', 'ToolController@nro');
	Route::any('/lienquan', 'ToolController@lienquan');
});

Route::group(['domain' => env('API_DOMAIN', 'http://s-api.tichhop.pro'), 'protocol' => strpos(env('API_DOMAIN', 'http://s-api.tichhop.pro'), 'https') > -1? 'https': 'http'], function(){
	Route::group(['namespace' => 'Api\V1','prefix' => 'v1','as'=>'api.'],function(){
		// Acc
		Route::any('/acc-callback-1ght5', 'AccController@callback_login')->name('acc.callback_login');
		Route::any('/acc', 'AccController@index')->middleware('verify_shop');
		Route::get('/acc-analytic', 'AccController@analytic');
		// Route::any('/acc', 'AccController@index');
	});
	Route::group(['namespace' => 'Api\V1\Upnick','prefix' => 'v1/upnick', 'as'=>'api.'],function(){
		Route::any('/lienminh', 'ToolController@lienminh')->name('upnick.lienminh');
		Route::any('/ninjaschool', 'ToolController@ninjaschool')->name('upnick.ninjaschool');
		Route::any('/nro', 'ToolController@nro')->name('upnick.nro');
		Route::any('/lienquan', 'ToolController@lienquan')->name('upnick.lienquan');
	});
});

