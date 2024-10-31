<?php

use App\Library\ChargeGameGateway\RobloxGate;
use App\Library\DirectAPI;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Roblox_Order;
use App\Models\ServiceAccess;
use App\Models\Txns;
use App\Models\User;
use Carbon\Carbon;
use App\Library\Helpers;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace' => 'Frontend'], function () {
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
});

Route::group(['middleware' => 'auth_frontend'], function () {
    Route::get('/', 'HomeController@index')->name('frontend.index');
    Route::get('merchant/profile', 'Frontend\ProfileController@profile')->name('frontend.profile');
    Route::post('merchant/profile', 'Frontend\ProfileController@postChangeCurrentPassword')->name('frontend.postChangeCurrentPassword');
    Route::get('merchant/txns-report/{id}/show', 'Frontend\Txns\ReportController@show')->name('frontend.txns-report.show');
    Route::get('merchant/txns-report', 'Frontend\Txns\ReportController@index')->name('frontend.txns-report.index');

    //service-purchase
    Route::get('merchant/service-purchase/count', ['uses' => 'Frontend\Service\PurchaseController@getCount', 'as' => 'frontend.service-purchase.count']);

    Route::get('merchant/service-purchase', 'Frontend\Service\PurchaseController@index')->name('frontend.service-purchase.index');
    Route::get('merchant/service-purchase/{id}', 'Frontend\Service\PurchaseController@show')->name('frontend.service-purchase.show');
    Route::get('merchant/service-purchase-auto', 'Frontend\Service\PurchaseAutoController@index')->name('frontend.service-purchase-auto.index');
    Route::get('merchant/service-purchase-auto/{id}', 'Frontend\Service\PurchaseAutoController@show')->name('frontend.service-purchase-auto.show');

});


//Route::group(array('middleware' => ['throttle:3,1']),function(){
//    Route::get('/test-throttle', function (\Illuminate\Http\Request $request) {
//       return "ok";
//    });
//});
//
//Route::group(array('middleware' => ['auth','2fa','clean_xss','activity_log','shop_permisson']),function(){
//    Route::get('nickclone/{id}',[AccountCloneController::class,'show']);
//});
//
Route::get('/test',function(Illuminate\Http\Request $request){


    $title = "tuanson85";
    $message = '';
    $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
    $message .= "\n";
    $message .= "<b>Trả nick robux:</b>";
    $message .= "\n";
    $message .= '- Tài khoản: '.$title;
    $message .= "\n";

    Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_defragment_roblox'));

    return 11111111111;

    return view('frontend.index');
});

Route::get('/test-key/{id}',function(Illuminate\Http\Request $request,$id){

    $service = \App\Models\Item::query()->where('id',$id)->first();

    if (isset($service->params)){
        $params = json_decode($service->params);

        $keywords = $params->keyword;
        $slugs = [];

        foreach ($keywords as $keyword){
            $slug = Str::slug($keyword);
            array_push($slugs,$slug);
        }

        $countValues = array_count_values($slugs);

// Lọc ra những giá trị có số lần xuất hiện lớn hơn 1
        $duplicates = array_filter($countValues, function ($count) {
            return $count > 1;
        });

        return $duplicates;
    }
    return $service;

    return view('frontend.index');
});

//
//Route::get('/testapi',function(Illuminate\Http\Request $request){
//
//
//    return \App\Library\ChargeGameGateway\RobloxGate::ProcessBuyGamePass("mainthor",5,"GuestData=UserID=-1645132267; _gcl_au=1.1.1247895004.1671343983; RBXcb=RBXViralAcquisition=true&RBXSource=true&GoogleAnalytics=true; __utmz=200924205.1671348011.2.2.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=(not provided); _ga_BK4ZY0C59K=GS1.1.1673465786.1.0.1673465786.0.0.0; _ga=GA1.2.137043577.1673465786; RBXSource=rbx_acquisition_time=1/18/2023 5:27:29 AM&rbx_acquisition_referrer=&rbx_medium=Direct&rbx_source=&rbx_campaign=&rbx_adgroup=&rbx_keyword=&rbx_matchtype=&rbx_send_info=1; lightstep_guid/Web=1141a862702cbad1; lightstep_session_id=077d1f357b2e341a; __utma=200924205.482934699.1671345980.1675823136.1675934879.20; __utmb=200924205.0.10.1675934879; __utmc=200924205; RBXImageCache=timg=SwwkpN-FU1SBW9CIVpdweRYKl2b9qWtyzmaOexWCa4o0SF8BJQpwEZ2tkUKR-zSkh21Ai-1lCFe6kTeYKkhp6jGwYhuayVYeIWEGPUoSUdCSwnlZ3-KtDF0cO1epp_psL7QEKlq-qyPXnXK5TAL7pxbvC2NSZLH8vw2wJcQfAftzdCsCOVzzFGTc0vRk0bwKhxygAiiYoUeo-CCMHg7Sxg; .ROBLOSECURITY=_|WARNING:-DO-NOT-SHARE-THIS.--Sharing-this-will-allow-someone-to-log-in-as-you-and-to-steal-your-ROBUX-and-items.|_78338F57300535EA4D091D3F62A6A0B06123B2B4ACC75931294020B1C239B7FF3456E2E0FDB5A10A78567C23E9A2F87F6F4BA399ACE9F2F735C1E3077B3120170D41A357BFD87B4BF00DDB78BD574BF9CF1B2B03675EFAE4BACA01F8188F4F6BBBFEFE1F944CA9BC3C6D5B6C19AB641B482EAEF56B2D1838A397ABE24A080FF5F1B1859E118A892DB4BE74DD4A55218EDB540537C62336AD2269C7E6D7227F3D70829EC13B01963A8BCD6B9A48340D915167B3F5D894D959FEF900564FA47BD3DD88BCC6D665E57245E4F8E5FAD0457188D1A3B4497D65256E6CFF887295176794664EC58926A7C4ED35E872A3AC6EF5DEED8493155DAA6FF659DD2246729A3F1EB2A57D9D1B435CD0752A89794F648C1863153B7A4C67317BE1D65A6E3C552DBEB68EF363904BCC57A74D3992C53D324A21EF8436122E552F32E9D89F665680F486A35DF8D6783B4A00015E034F32AAC00EA3630B04D397EDCEC1ABACA4BE6937A9EDB09D0686C9651309915B859034D678D338; RBXEventTrackerV2=CreateDate=2/9/2023 3:28:35 AM&rbxid=4294371713&browserid=156889587801; RBXSessionTracker=sessionid=2a20468b-2692-445c-8251-4923c1329cf7","");
//
//
//    //tét roblox
//   // $job = (new \App\Jobs\ServiceAuto\RobloxJob(40463));
//   //return  dispatch_now($job);
//
//
//
//    //$listUserInvalid=User::with('shop')->whereRaw('IF(balance_in - balance_out + balance_in_refund - balance != 0, 1, 0) = 1')->get();
//    //
//    //foreach ($listUserInvalid as $user) {
//    //    if($user->account_type==2){
//    //        $mesage = "[" . Carbon::now() . "] "."Hệ thống check: Thành viên "."<b>".$user->username."</b>"." - của shop ".$user->shop->domain." biến động số dư bị chênh lệch, vui lòng kiểm tra lại. Số tiền vào: ".currency_format($user->balance_in).". - Số tiền chi tiêu: ".currency_format($user->balance_out).". - Số tiền hoàn: ".currency_format($user->balance_in_refund).". - Số dư hiện tại: ".currency_format($user->balance).". - Chênh lệch: ".currency_format($user->balance_in - $user->balance_out + $user->balance_in_refund - $user->balance)." VNĐ";
//    //    }
//    //    else{
//    //        $mesage = "[" . Carbon::now() . "] "."Hệ thống check: Quản trị viên (Nội bộ) / CTV: "."<b>".$user->username."</b>"." biến động số dư bị chênh lệch, vui lòng kiểm tra lại. Số tiền vào: ".currency_format($user->balance_in).". - Số tiền chi tiêu: ".currency_format($user->balance_out).". - Số tiền hoàn: ".currency_format($user->balance_in_refund).". - Số dư hiện tại: ".currency_format($user->balance).". - Chênh lệch: ".currency_format($user->balance_in - $user->balance_out + $user->balance_in_refund - $user->balance)." VNĐ";
//    //    }
//    //    if(!Cache::has('CheckInvalid'.$user->username)){
//    //        Cache::put('CheckInvalid'.$user->username,true,now()->addMinutes(5));
//    //        Helpers::TelegramNotify($mesage,'-755605148');
//    //    }
//    //
//    //
//    //}
//    //return 1111;
//    //return view('vendor.l5-swagger.master');
//
//});
