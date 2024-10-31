<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Api\V1','prefix' => 'v1','as'=>'api.',],function(){
    Route::get('/theme/get-theme-config','Theme\ThemeController@getThemeConfig');
});
