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


 //Route::middleware('auth:api')->get('/user', function (Request $request) {
 //    return $request->user();
 //});





//API FOR CALLBACK
Route::any('/charging/callback/{site}', 'Api\ListenCallbackController@getCallback');
Route::any('/hub/callback/{site}', 'Api\ListenCallbackController@getHubCallback');
Route::any('/webhook/callback/github','Api\Webhook\GitHubController@getCallback');



require __DIR__.'/api_toolgame.php';
require __DIR__.'/api_agency.php';
//require __DIR__.'/api_truongdev19.php';
//require __DIR__.'/api_tan.php';
//require __DIR__.'/api_son.php';
//require __DIR__.'/api_phap.php';
//require __DIR__.'/api_vong.php';
//require __DIR__.'/api_nam.php';

require __DIR__.'/api_google_sheet.php';
