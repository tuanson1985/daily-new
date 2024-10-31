<?php


use Illuminate\Support\Facades\Auth;
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

//Route::get('login', 'HomeController@index')->name('login');
Route::group(array('as' => 'admin.'),function(){


    //Auth::routes(['verify' => true]); //nếu dùng thì thêm middleware check ở route.(->middleware('verified');) và implements MustVerifyEmail trong model user
    //Auth::routes(['verify' => false]);

    //Route::get('login', 'HomeController@index')->name('login');
    Route::get('login', 'Admin\Auth\LoginController@showLoginForm')->name('login');
    Route::get('login/gmail', 'Admin\Auth\LoginController@loginGmail')->name('login.gmail');
    Route::get('callback-login/gmail/{token}', 'Admin\Auth\LoginController@callbackLoginGmail');

    Route::post('login', 'Admin\Auth\LoginController@login');
    Route::post('logout', 'Admin\Auth\LoginController@logout')->name('logout');

    // Registration Routes...
    if (true)
    {
        Route::get('register', 'Admin\Auth\RegisterController@showRegistrationForm')->name('register');
        Route::post('register', 'Admin\Auth\RegisterController@register');
    }
    // Password Reset Routes...
    if (true)
    {
        Route::get('password/reset', 'Admin\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
        Route::post('password/email', 'Admin\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
        Route::get('password/reset/{token}', 'Admin\Auth\ResetPasswordController@showResetForm')->name('password.reset');
        Route::post('password/reset', 'Admin\Auth\ResetPasswordController@reset')->name('password.update');
    }
    // Email Verification Routes...
    if (true)
    {
        Route::get('email/verify', 'Admin\Auth\VerificationController@show')->name('verification.notice');
        Route::get('email/verify/{id}', 'Admin\Auth\VerificationController@verify')->name('verification.verify');
        Route::get('email/resend', 'Admin\Auth\VerificationController@resend')->name('verification.resend');
    }


});


// 2FA admin
//Route::group(array('as' => 'admin.','middleware' => ['auth','auth_admin','activity_log']),function(){
//    Route::get('/2fa','Admin\Auth\TwoFactorAuthentication@show2faForm');
//    Route::post('/generate2faSecret','Admin\Auth\TwoFactorAuthentication@generate2faSecret')->name('generate2faSecret');
//    Route::post('/2fa','Admin\Auth\TwoFactorAuthentication@enable2fa')->name('enable2fa');
//    Route::post('/disable2fa','Admin\Auth\TwoFactorAuthentication@disable2fa')->name('disable2fa');
//
//});

Route::group(array('as' => 'admin.','middleware' => ['auth','2fa','clean_xss','activity_log','shop_permisson','very_ip']),function(){
    Route::get('/security-2fa/very','Admin\User\Security2FAController@getVery')->name('security-2fa.very');
    Route::post('/security-2fa/very','Admin\User\Security2FAController@postVery');
});


Route::group(array('as' => 'admin.','middleware' => ['auth','auth_admin','2fa','clean_xss','activity_log','shop_permisson','very_ip','very_security']),function(){
    require __DIR__.'/backend_module_route.php';

    Route::get('/api-document', 'Admin\ApiDocument@index')->name('api.document');

//    Route::get('/dashboard', 'Admin\IndexController@index')->name('dashboard');

    Route::get('/logs-errors', 'Admin\LogViewerController@index')->name('log.viewer');

    Route::any('/ckfinder/connector', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')->name('ckfinder_connector');
    Route::any('/ckfinder/browser', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')->name('ckfinder_browser');

    Route::any('/ckfinder/product-acc-connector/{id}', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')->name('ckfinder_connector_acc')->middleware('ckfinder');
    Route::any('/ckfinder/product-acc-browser/{id}', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')->name('ckfinder_browser_acc')->middleware('ckfinder');

    Route::any('/ckfinder/custom-folder-connector/{folder}/{id}', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')->name('ckfinder_connector_folder_id')->middleware('ckfinder');
    Route::any('/ckfinder/custom-folder-browser/{folder}/{id}', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')->name('ckfinder_browser_folder_id')->middleware('ckfinder');

//    Dịch vụ.
    Route::any('/ckfinder/service-config-folder-connector/{folder}/{id}', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')->name('ckfinder_connector_service_config')->middleware('ckfinder');
    Route::any('/ckfinder/service-config-folder-browser/{folder}/{id}', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')->name('ckfinder_browser_service_config')->middleware('ckfinder');
//Quảng cáo
    Route::any('/ckfinder/advertise-folder-connector/{folder}/{id}', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')->name('ckfinder_connector_advertise')->middleware('ckfinder');
    Route::any('/ckfinder/advertise-folder-browser/{folder}/{id}', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')->name('ckfinder_browser_advertise')->middleware('ckfinder');

    //Minigame
    Route::any('/ckfinder/minigame-folder-connector/{folder}/{id}', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')->name('ckfinder_connector_minigame')->middleware('ckfinder');
    Route::any('/ckfinder/minigame-folder-browser/{folder}/{id}', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')->name('ckfinder_browser_minigame')->middleware('ckfinder');

    //Setting
    Route::any('/ckfinder/setting-folder-connector/{folder}/{id}', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')->name('ckfinder_connector_setting')->middleware('ckfinder');
    Route::any('/ckfinder/setting-folder-browser/{folder}/{id}', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')->name('ckfinder_browser_setting')->middleware('ckfinder');

    //article
    Route::any('/ckfinder/article-folder-connector/{folder}/{id}', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')->name('ckfinder_connector_article')->middleware('ckfinder');
    Route::any('/ckfinder/article-folder-browser/{folder}/{id}', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')->name('ckfinder_browser_article')->middleware('ckfinder');

    // 2fa setting
    Route::get('/security-2fa','Admin\User\Security2FAController@index')->name('security-2fa.index');
    Route::get('/security-2fa/setup','Admin\User\Security2FAController@setup')->name('security-2fa.setup');
    Route::post('/security-2fa/setup','Admin\User\Security2FAController@enable2fa');
    Route::post('/security-2fa/disable2fa','Admin\User\Security2FAController@disable2fa')->name('security-2fa.disable2fa');

    Route::get('/security-2fa/recovery-code','Admin\User\Security2FAController@getRecoveryCode')->name('security-2fa.recovery-code');
    Route::post('/security-2fa/recovery-code','Admin\User\Security2FAController@postRecoveryCode');

    //index
    Route::get('/test', 'Admin\TestController@index');

    Route::get('/get-user-id/{uname}', 'Admin\TestController@getUname');

    Route::get('/get-place-id/{user_id}', 'Admin\TestController@getPlaceId');

    Route::get('/get-game-pass/{place_id}', 'Admin\TestController@getGamepass');

    Route::get('/', 'Admin\IndexController@index')->name('index');

    Route::get('/dashboard-export', 'Admin\IndexController@exportDashboard')->name('dashboard.export');

    Route::post('/dashboard-export-excel', 'Admin\IndexController@dashboardExportExcel')->name('dashboard.export-excel');

    //----------------------------------------- module txns -----------------------------------------//
    //txns-report
    Route::post('txns-report/export-excel', ['uses' => 'Admin\Txns\ReportController@exportExcel', 'as' => 'txns-report.export-excel']);

//    Route::get('/dashboard', 'Admin\DashboardController@index')->name('dashboard');

    Route::get('/classify/shop-group','Admin\IndexController@classifyShopGroup')->name('classify.shop-group');
    Route::get('/growth/shop','Admin\IndexController@GrowthShop')->name('growth.shop');
    Route::get('/growth/user','Admin\IndexController@GrowthUser')->name('growth.user');
    Route::get('/growth/ctv','Admin\IndexController@GrowthCTV')->name('growth.ctv');
    Route::get('/chart/report-charge','Admin\IndexController@ReportCharge')->name('chart.report-charge');
    Route::get('/chart/report-store-card','Admin\IndexController@ReportStoreCard')->name('chart.report-store-card');
    Route::get('/classify/user','Admin\IndexController@ClassifyUser')->name('classify.user');
    Route::get('/growth/topup-card','Admin\IndexController@GrowthTopupCard')->name('growth.topup-card');
    Route::get('/growth/store-card','Admin\IndexController@GrowthStoreCard')->name('growth.store-card');
    Route::get('/growth/topup-bank','Admin\IndexController@GrowthTopupBank')->name('growth.topup-bank');
    Route::get('/growth/donate','Admin\IndexController@GrowthDonate')->name('growth.donate');
    Route::post('/growth/export/topup-bank','Admin\IndexController@ExportDepositBank')->name('growth.export.topup-bank');
    Route::post('/growth/export/charge','Admin\IndexController@ExportCharge')->name('growth.export.charge');
    Route::post('/growth/export/store-card','Admin\IndexController@ExportStoreCard')->name('growth.export.store-card');
    Route::post('/growth/export/donate','Admin\IndexController@ExportDonate')->name('growth.export.donate');
    Route::get('/user/export/birthday','Admin\IndexController@ExportUserBirthday')->name('user.export.birthday');
    Route::get('/idol/export/bookingtime','Admin\IndexController@ExportIdolBookingTime')->name('idol.export.bookingtime');
    Route::get('/growth/tranfer','Admin\IndexController@GrowthTranfer')->name('growth.tranfer');
    Route::get('/chart/report-tranfer','Admin\IndexController@ReportTranfer')->name('chart.report-tranfer');

    Route::post('/charge/report','Admin\IndexController@ReportCharge2')->name('charge.report');
    Route::post('/store-card/report','Admin\IndexController@ReportStoreCard2')->name('store-card.report');
    Route::post('/plus-money/report','Admin\IndexController@ReportMoney')->name('plus-money.report');
    Route::post('/withdraw/report','Admin\IndexController@ReportWithdraw')->name('withdraw.report');
    Route::post('/service/report','Admin\IndexController@ReportService')->name('service.report');
    Route::post('/service-auto/report','Admin\IndexController@ReportServiceAuto')->name('service-auto.report');
    Route::post('/transfer2/report','Admin\IndexController@ReportTransfer2')->name('transfer2.report');
    Route::post('/minigame/report','Admin\IndexController@ReportMinigame')->name('mini.report');

    Route::get('/user/report','Admin\IndexController@ReportUser')->name('user.report');
    Route::post('/transaction-user/report','Admin\IndexController@ReportTransactionUser')->name('transaction-user.report');
    Route::post('/surplus-user/report','Admin\IndexController@ReportSurplusUser')->name('surplus-user.report');
    Route::post('/top-user/report','Admin\IndexController@ReportTopMoney')->name('top-user.report');
    Route::post('/txns-biggest/report','Admin\IndexController@ReportTxnsBiggest')->name('txns-biggest.report');
    Route::post('/point-of-sale/report','Admin\IndexController@ReportPointOfSale')->name('point-of-sale.report');
    Route::post('/withdraw-item/report','Admin\IndexController@ReportWithdrawItem')->name('withdraw-item.report');

    Route::post('/general-turnover/report','Admin\IndexController@ReportGeneralTurnover')->name('general-turnover.report');
    Route::post('/general-density-turnover/report','Admin\IndexController@ReportGeneralDensityTurnover')->name('general-density-turnover.report');
    Route::post('/growth-account/report','Admin\IndexController@ReportAccount')->name('growth-account.report');


    //Route::get('/getLogsView', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

    Route::get('/profile', 'Admin\ProfileController@profile')->name('profile');
    // Route::post('/profile', 'Admin\ProfileController@post_profile')->name('post_profile');
    Route::post('/profile', 'Admin\ProfileController@postChangeCurrentPassword')->name('postChangeCurrentPassword');

    Route::post('/profile2', 'Admin\ProfileController@postChangeCurrentPassword2')->name('postChangeCurrentPassword2');
    //Route::get('/profile',  ['uses' => 'Admin\User\ProfileController@getProfile', 'as' => 'get-profile']);


    //Route::get('set_language/{locale}','Admin\LanguageController@setLocale');

    //-category
    Route::post('provider/order', 'Admin\Provider\ProviderController@order')->name('provider.order');
    Route::resource('provider','Admin\Provider\ProviderController');

    //set-permission-user
    // Route::resource('set-permission-user','Admin\Permission\SetPermissionUserController');



    Route::post('permission/order', 'Admin\PermissionController@order')->name('permission.order');
    Route::resource('permission','Admin\PermissionController');
    //role
    Route::post('role/order', 'Admin\RoleController@order')->name('role.order');
    Route::resource('role','Admin\RoleController');
    //setting
    Route::get('setting', 'Admin\SettingController@index')->name('setting.index');
    Route::post('setting', 'Admin\SettingController@store')->name('setting.store');

    //activity-log
    Route::resource('activity-log', 'Admin\ActivityLogController');
    //language-nation
    Route::post('language-nation/update_field', 'Admin\LanguageNationController@update_field')->name('language-nation.update_field');
    Route::resource('language-nation','Admin\LanguageNationController');
    //language-key
    Route::post('language-key/update_field', 'Admin\LanguageKeyController@update_field')->name('language-key.update_field');
    Route::resource('language-key','Admin\LanguageKeyController');

    //language-mapping
    Route::resource('language-mapping','Admin\LanguageMappingController');


    //user-qtv
    Route::get('/view-profile', 'Admin\User\UserQTVController@view_profile')->name('view-profile');
    Route::post('user-qtv/lock', 'Admin\User\UserQTVController@lock')->name('user-qtv.lock');
    Route::post('user-qtv/unlock', 'Admin\User\UserQTVController@unlock')->name('user-qtv.unlock');
    Route::resource('user-qtv','Admin\User\UserQTVController');
    Route::get('/user-qtv/{id}/show-shop', 'Admin\User\UserQTVController@showShop')->name('user-qtv.show-shop');

    Route::resource('user-ctv','Admin\User\UserCTVController');


    //money for user
    Route::get('money', 'Admin\User\UserQTVController@getMoney')->name('get_money');
    Route::get('money-qtv', 'Admin\User\UserQTVController@getMoneyQTV')->name('get_money_qtv');
    Route::get('user-to-money', 'Admin\User\UserQTVController@getUserToMoney')->name('get_user_to_money');
    Route::get('user-to-money-qtv', 'Admin\User\UserQTVController@getUserToMoneyQTV')->name('get_user_to_money_qtv');
    Route::post('money', 'Admin\User\UserQTVController@postMoney')->name('post_money');
    Route::post('money-qtv', 'Admin\User\UserQTVController@postMoneyQTV')->name('post_money_qtv');

    Route::get('get-vp',['uses' => 'Admin\User\UserQTVController@getVP', 'as' => 'get_vp']);
    Route::get('user-to-vp', 'Admin\User\UserQTVController@getUserToVP')->name('get_user_to_vp');
    Route::post('post-vp',['uses' => 'Admin\User\UserQTVController@postVP', 'as' => 'post_vp']);

    Route::post('access',['uses' => 'Admin\User\UserQTVController@AccessUser', 'as' => 'access_user']);

    Route::get('user-ctv/{id}/set-permission', 'Admin\User\UserCTVController@set_permission')->name('user-ctv.set_permission');
    Route::post('user/user-ctv-export', 'Admin\User\UserCTVController@exportExcel')->name('user-ctv-export.index');

    //user-qtv ---- set permission
    Route::get('user-qtv/{id}/set-permission', 'Admin\User\UserQTVController@set_permission')->name('user-qtv.set_permission');
    Route::post('user-qtv/set-permission/{id}', 'Admin\User\UserQTVController@post_set_permission')->name('user-qtv.post_set_permission');

    Route::post('user-ctv/set-permission-speed', 'Admin\User\UserCTVController@post_set_permission_speed')->name('user-ctv.post_set_permission_speed');

    Route::post('user/user-qtv-export', 'Admin\User\UserQTVController@exportExcel')->name('user-qtv-export.index');

    //user
    Route::post('user/lock', 'Admin\User\UserController@lock')->name('user.lock');
    Route::post('user/unlock', 'Admin\User\UserController@unlock')->name('user.unlock');
    Route::resource('user','Admin\User\UserController');

    Route::post('user/user-export', 'Admin\User\UserController@exportExcel')->name('user-export.index');


    Route::get('user/buff/{id}','Admin\User\UserController@getBuffIdol')->name('user.buff');
    Route::post('user/buff/{id}','Admin\User\UserController@postBuffIdol');

    //----------------------------------------- module telecom -----------------------------------------//
    //telecom
    Route::get('telecom/{id}/set-value', 'Admin\Telecom\ItemController@getSetValue')->name('telecom.set-value');
    Route::post('telecom/{id}/set-value', 'Admin\Telecom\ItemController@postSetValue');
    Route::post('telecom/replication', 'Admin\Telecom\ItemController@postReplication')->name('telecom.replication');
    Route::resource('telecom','Admin\Telecom\ItemController');

    // bank-transfer
    Route::resource('transfer-bank','Admin\Transfer\BankController');
    Route::get('transfer','Admin\Transfer\ItemController@index')->name('transfer.index');
    Route::resource('transfer','Admin\Transfer\ItemController');
    Route::post('transfer/update-order/{id}','Admin\Transfer\ItemController@updateOrder');
    Route::post('transfer/report/','Admin\Transfer\ItemController@Report')->name('transfer.report');
    Route::post('transfer/export','Admin\Transfer\ItemController@exportExcel')->name('transfer.export');


    // bank-deposit
    Route::resource('deposit-bank-setting','Admin\DepositBank\SettingController');
    Route::resource('deposit-bank-report','Admin\DepositBank\ReportController');


    //----------------------------------------- module charge -----------------------------------------//
    //charge-report
    // Route::post('charge-report/callback', 'Admin\Charge\ReportController@postCallback')->name('charge-report.callback');
    Route::get('charge-report', 'Admin\Charge\ReportController@index')->name('charge-report.index');
    Route::post('charge-export', 'Admin\Charge\ReportController@exportExcel')->name('charge-export.index');
    Route::post('charge-recharge', 'Admin\Charge\ReportController@reCharge')->name('charge-recharge.index');

    //----------------------------------------- module txnsvp -----------------------------------------//
    //txnsvp-report

    Route::get('txnsvp-report/{id}/show', 'Admin\TxnsVp\ReportController@show')->name('txnsvp-report.show');
    Route::get('txnsvp-report', 'Admin\TxnsVp\ReportController@index')->name('txnsvp-report.index');
    Route::get('txnsvp-qtv-report', 'Admin\TxnsVpQTV\ReportController@index')->name('txnsvp-qtv-report.index');
    Route::get('txnsvp-qtv-report/{id}/show', 'Admin\TxnsVpQTV\ReportController@show')->name('txnsvp-qtv-report.show');
    Route::post('txnsvp-export', 'Admin\TxnsVp\ReportController@exportExcel')->name('txnsvp-export.index');
    //----------------------------------------- module txns -----------------------------------------//
    //txns-report

    Route::get('txns-report/{id}/show', 'Admin\Txns\ReportController@show')->name('txns-report.show');
    Route::get('txns-report', 'Admin\Txns\ReportController@index')->name('txns-report.index');

    //----------------------------------------- module plusmoney -----------------------------------------//
    //plusmoney-report
    Route::get('plusmoney-report', 'Admin\PlusMoney\ReportController@index')->name('plusmoney-report.index');
    Route::get('plusmoney-report-qtv', 'Admin\PlusMoney\ReportQTVController@index')->name('plusmoney-report-qtv.index');

    //----------------------------------------- module booking -----------------------------------------//
    //booking-report
    Route::get('booking-report', 'Admin\Booking\ReportController@index')->name('booking-report.index');
    //booking-setting
    Route::resource('booking-setting','Admin\Booking\SettingController');

    //----------------------------------------- module donate -----------------------------------------//
    //donate-report
    Route::get('donate-report', 'Admin\Donate\ReportController@index')->name('donate-report.index');
    //donate-setting
    Route::get('donate-setting/key-word','Admin\Donate\SettingController@getKeyWord')->name('donate-setting.key-word');
    Route::post('donate-setting/key-word','Admin\Donate\SettingController@postKeyWord');
    Route::resource('donate-setting', 'Admin\Donate\SettingController');


    Route::get('store-telecom/{id}/set-value', ['uses' => 'Admin\StoreTelecom\ItemController@SetValue', 'as' => 'store-telecom.set-value']);
    Route::post('store-telecom/{id}/set-value', ['uses' => 'Admin\StoreTelecom\ItemController@postSetValue', 'as' => 'store-telecom.post-set-value']);
    Route::post('store-telecom/replication', 'Admin\StoreTelecom\ItemController@postReplication')->name('store-telecom.replication');

    Route::post('store-telecom/setting', 'Admin\StoreTelecom\ItemController@postSetting')->name('store-telecom.setting');
    Route::resource('store-telecom', 'Admin\StoreTelecom\ItemController');

    Route::post('store-telecom/post-setting', ['uses' => 'Admin\StoreTelecom\ItemController@postSetting', 'as' => 'store-telecom.post-setting']);

    Route::resource('gift-code', 'Admin\GifCode\ItemController');
    Route::post('store-telecom/post-setting', ['uses' => 'Admin\StoreTelecom\ItemController@postSetting', 'as' => 'store-telecom.post-setting']);
    Route::post('store-card/recheck/{id}', 'Admin\StoreTelecom\ReportController@reCheckOrder')->name('store-card.recheck');
    Route::post('store-card-export', 'Admin\StoreTelecom\ReportController@exportExcel')->name('store-card-export.index');
    Route::resource('store-card-report', 'Admin\StoreTelecom\ReportController');


    Route::resource('gift-code', 'Admin\GifCode\ItemController');
    Route::resource('gift-code-report', 'Admin\GifCode\ReportController');

    //// rút tiền
    // Route::post('withdraw/update-item/{id}','Admin\Withdraw\ItemController@updateItem')->name('withdraw.update-item');
    // Route::resource('withdraw-bank', 'Admin\Withdraw\BankController');
    //Route::resource('withdraw', 'Admin\Withdraw\ItemController');


    Route::post('/shop/switch','Admin\Shop\ClientSwitchController@ClientSwitch')->name('shop.switch')->middleware('throttle:100,1');
    Route::get('/shop/cache','Admin\Shop\ClientSwitchController@DeleteCache')->name('shop.cache');
    //shop
    Route::any('shop/access/{id}', 'Admin\Shop\ItemController@access')->name('shop.access');
    Route::any('shop/access/{id}/{cat}', 'Admin\Shop\ItemController@access_custom')->name('shop.access.custom');
    Route::post('shop/secret_key', 'Admin\Shop\ItemController@RenderSecretKey')->name('shop.secret_key');
    Route::post('shop/order', 'Admin\Shop\ItemController@order')->name('shop.order');
    Route::get('shop/partner/{id}', 'Admin\Shop\ItemController@getPartNer')->name('shop.partner');
    Route::post('shop/partner/{partner}', 'Admin\Shop\ItemController@postPartNer');
    Route::post('shop/autosave-content', 'Admin\Shop\ItemController@autosaveContent')->name('shop.autosave-content');

    Route::get('shop/check-partner/{id}', 'Admin\Shop\ItemController@getCheckPartNer')->name('shop.check-partner');

    Route::post('shop/update-stt', 'Admin\Shop\ItemController@UpdateStatus')->name('shop.update-stt')->middleware('throttle:10,1');
    Route::resource('shop', 'Admin\Shop\ItemController');
    Route::get('acc/{id}/revision/{slug}', 'Admin\Shop\ItemController@revision')->name('acc.revision');
    Route::post('acc/{id}/revision/{slug}', 'Admin\Shop\ItemController@postRevision')->name('acc.postrevision');

    Route::get('/git-pull','Admin\Shop\GitPullController@index')->name('shop-git.index');
    Route::post('/git-pull','Admin\Shop\GitPullController@portShop');
    // Kho acc
    Route::group(['namespace' => 'Admin\Product'], function () {
        Route::group(['prefix' => 'acc'], function () {
            Route::any('/quick', 'AccController@quick')->name('acc.quick');
        });
        Route::get('acc-type-1/', 'AccController@index_1')->name('acc_type_1'); /*Nick thường*/
        Route::get('acc-type-2/', 'AccController@index_2')->name('acc_type_2'); /*Nick random*/
        Route::any('acc-type-{type}/edit-{id}', 'AccController@edit')->name('acc.edit');

        Route::group(['prefix' => 'acc-property'], function () {
            Route::get('/', 'AccController@property')->name('acc.property');
            Route::any('/view-auto/{id}', 'AccController@auto_detail')->name('acc.auto_detail');
            Route::any('/auto/{table}/{id}', 'AccController@property_auto')->name('acc.cat-auto-edit');
            Route::post('/order', 'AccController@property_order')->name('acc.property.order');
            Route::any('/{module}/{parent}/{id}', 'AccController@property_edit')->name('acc.property.edit');
        });
        Route::group(['prefix' => 'acc-report'], function () {
            Route::get('/analytic', 'AccController@analytic')->name('acc.analytic');
            Route::get('/history', 'AccController@history')->name('acc.history');
            Route::get('/history/show/{id}', 'AccController@historyShow')->name('acc.history.show');
            Route::post('/history/{id}/reject-refund', 'AccController@rejectRefund')->name('acc.history.reject-refund');
            Route::post('/history/{id}/completed-refund', 'AccController@completedRefund')->name('acc.history.completed-refund');
        });
    });

    //service-category
    Route::post('service-category/order', 'Admin\Service\CategoryController@order')->name('service-category.order');
    Route::resource('service-category','Admin\Service\CategoryController');
    //service-group
    Route::get('service-group/{id}/duplicate', 'Admin\Service\GroupController@duplicate')->name('service-group.duplicate');
    Route::get('service-group/search', 'Admin\Service\GroupController@search')->name('service-group.search');
    Route::get('service-group/show-item','Admin\Service\GroupController@showItemGroup')->name('service-group.show-item');
    Route::get('service-group/update-item','Admin\Service\GroupController@updateItemGroup')->name('service-group.update-item');
    Route::post('service-group/delete-item','Admin\Service\GroupController@deleteItemGroup')->name('service-group.delete-item');
    Route::resource('service-group','Admin\Service\GroupController');
    //service
    Route::get('service/{id}/duplicate', 'Admin\Service\ItemController@duplicate')->name('service.duplicate');
    Route::post('service/export-excel', 'Admin\Service\ItemController@exportExcel')->name('service.export-excel');
    Route::get('service/get-shop-update-config', 'Admin\Service\ItemController@getShopUpdateConfig')->name('service.get-shop-update-config');
    Route::post('service/sync-update-config', 'Admin\Service\ItemController@postSyncUpdateConfig')->name('service.post-sync-update-config');
    Route::post('service/remove-sync-config', 'Admin\Service\ItemController@postRemoveSyncConfig')->name('service.post-remove-sync-config');

    Route::post('service/rechange', 'Admin\Service\ItemController@rechange')->name('service.rechange');

    Route::resource('service','Admin\Service\ItemController');

    //user-qtv ---- set permission
    Route::get('service/{id}/set-permission', 'Admin\Service\ItemController@set_permission')->name('service.set_permission');
    Route::post('service/set-permission/{id}', 'Admin\Service\ItemController@post_set_permission')->name('service.post_set_permission');
    Route::post('service/set-permission-user/{id}', 'Admin\Service\ItemController@post_set_permission_user')->name('service.post_set_permission_user');

    Route::post('service/set-permission-detail-user/{id}', 'Admin\Service\ItemController@post_set_permission_detail_user')->name('service.post_set_permission_detail_user');

    Route::post('service/get-log-edit', 'Admin\Service\ItemController@getLogEdit')->name('service.get-log-edit');
    Route::post('service/get-log-edit-detail', 'Admin\Service\ItemController@getLogEditDetail')->name('service.get-log-edit-detail');

    //service-config
    Route::post('service-config/{id}/update-config', 'Admin\Service\ConfigController@update_config')->name('service-config.update-config');
    Route::post('service-config/{id}/update-config-base', 'Admin\Service\ConfigController@update_config_base')->name('service-config.update-config-base');
    Route::get('service-config/{id}/duplicate', 'Admin\Service\ConfigController@duplicate')->name('service-config.duplicate');
    Route::resource('service-config','Admin\Service\ConfigController');
    Route::get('service/{id}/revision/{slug}', 'Admin\Service\ConfigController@revision')->name('service.revision');
    Route::post('service/{id}/revision/{slug}', 'Admin\Service\ConfigController@postRevision')->name('service.postrevision');

    //service-purchase
    Route::get('service-purchase/count', ['uses' => 'Admin\Service\PurchaseController@getCount', 'as' => 'service-purchase.count']);
    Route::post('service-purchase/inbox/{id}', ['uses' => 'Admin\Service\PurchaseController@postInbox', 'as' => 'service-purchase.inbox']);
    Route::post('service-purchase/recallback', ['uses' => 'Admin\Service\PurchaseController@postRecallback', 'as' => 'service-purchase.recallback']);
    Route::post('service-purchase/rechang/{id}', ['uses' => 'Admin\Service\PurchaseController@postRechang', 'as' => 'service-purchase.rechang']);
    Route::post('service-purchase/delete-all', ['uses' => 'Admin\Service\PurchaseController@postDeleteAll', 'as' => 'service-purchase.delete-all']);
    Route::get('service-purchase/load-top-attribute', 'Admin\Service\PurchaseController@loadTopAttribute')->name('service-purchase.load-top-attribute');
    Route::get('service-purchase/load-attribute-tk', 'Admin\Service\PurchaseController@loadAttributeTk')->name('service-purchase.load-attribute-tk');

    Route::post('service-purchase/reception/{id}', ['uses' => 'Admin\Service\PurchaseController@postReception', 'as' => 'service-purchase.reception']);
    Route::post('service-purchase/pengiriman/{id}', ['uses' => 'Admin\Service\PurchaseController@postPengiriman', 'as' => 'service-purchase.pengiriman']);
    Route::post('service-purchase/pengiriman-all', ['uses' => 'Admin\Service\PurchaseController@postPengirimanAll', 'as' => 'service-purchase.pengiriman-all']);
    Route::post('service-purchase/edit-info/{id}', ['uses' => 'Admin\Service\PurchaseController@postEditInfo', 'as' => 'service-purchase.edit-info']);
    Route::post('service-purchase/completed/{id}', ['uses' => 'Admin\Service\PurchaseController@postCompleted', 'as' => 'service-purchase.completed']);
    Route::post('service-purchase/refund/{id}', ['uses' => 'Admin\Service\PurchaseController@postRefund', 'as' => 'service-purchase.refund']);
    Route::post('service-purchase/refund-delete/{id}', ['uses' => 'Admin\Service\PurchaseController@postRefundDelete', 'as' => 'service-purchase.refund-delete']);

    Route::post('service-purchase/reject-refund/{id}', ['uses' => 'Admin\Service\PurchaseController@postRejectRefund', 'as' => 'service-purchase.reject-refund']);
    Route::post('service-purchase/completed-refund/{id}', ['uses' => 'Admin\Service\PurchaseController@postCompletedRefund', 'as' => 'service-purchase.completed-refund']);

//    Route::middleware(['throttle.auth.ip:10,1'])->resource('service-purchase', 'Admin\Service\PurchaseController');
    Route::resource('service-purchase','Admin\Service\PurchaseController');
//    Route::middleware(['throttle:20,1'])->resource('service-purchase','Admin\Service\PurchaseController');

    Route::post('service-purchase/export-excel', ['uses' => 'Admin\Service\PurchaseController@exportExcel', 'as' => 'service-purchase.export-excel']);

    //service-purchase-auto
    Route::post('service-purchase-auto/refund/{id}', ['uses' => 'Admin\Service\PurchaseAutoController@postRefund', 'as' => 'service-purchase-auto.refund']);
    Route::post('service-purchase-auto/roblox-user-id/{id}', ['uses' => 'Admin\Service\PurchaseAutoController@robloxUserid', 'as' => 'service-purchase-auto.roblox-user-id']);
    Route::post('service-purchase-auto/roblox-psx/{id}', ['uses' => 'Admin\Service\PurchaseAutoController@robloxPsx', 'as' => 'service-purchase-auto.roblox-psx']);
    Route::post('service-purchase-auto/roblox-unit/{id}', ['uses' => 'Admin\Service\PurchaseAutoController@robloxUnit', 'as' => 'service-purchase-auto.roblox-unit']);

    Route::post('service-purchase-auto/recharge/{id}', ['uses' => 'Admin\Service\PurchaseAutoController@recharge', 'as' => 'service-purchase-auto.recharge']);
    Route::post('service-purchase-auto/recharge-rbx/{id}', ['uses' => 'Admin\Service\PurchaseAutoController@rechargeRbx', 'as' => 'service-purchase-auto.recharge-rbx']);
    Route::get('service-purchase-auto/load-attribute-tk', 'Admin\Service\PurchaseAutoController@loadAttributeTk')->name('service-purchase-auto/load-attribute-tk');
    Route::post('service-purchase-auto/recharge-gamepass/{id}', ['uses' => 'Admin\Service\PurchaseAutoController@rechargeGamepass', 'as' => 'service-purchase-auto.recharge-gamepass']);
    Route::post('service-purchase-auto/delete-all-auto', ['uses' => 'Admin\Service\PurchaseAutoController@postDeleteAllAuto', 'as' => 'service-purchase-auto.delete-all-auto']);
    Route::post('service-purchase-auto/pengiriman', ['uses' => 'Admin\Service\PurchaseAutoController@postPengiriman', 'as' => 'service-purchase-auto.pengiriman']);
    Route::post('service-purchase-auto/pengiriman-all', ['uses' => 'Admin\Service\PurchaseAutoController@postPengirimanAll', 'as' => 'service-purchase-auto.pengiriman-all']);

    Route::post('service-purchase-auto/delete-desc-auto', ['uses' => 'Admin\Service\PurchaseAutoController@postDeleteDescAuto', 'as' => 'service-purchase-auto.delete-desc-auto']);

    Route::post('service-purchase-auto/switch-rbx-api', ['uses' => 'Admin\Service\PurchaseAutoController@postSwichRbxApi', 'as' => 'service-purchase-auto.switch-rbx-api']);
    Route::post('service-purchase-auto/switch-daily', ['uses' => 'Admin\Service\PurchaseAutoController@postSwichDaily', 'as' => 'service-purchase-auto.switch-daily']);


    Route::post('service-purchase-auto/inbox/{id}', ['uses' => 'Admin\Service\PurchaseAutoController@postInbox', 'as' => 'service-purchase-auto.inbox']);
    Route::post('service-purchase-auto/edit-info/{id}', ['uses' => 'Admin\Service\PurchaseAutoController@postEditInfo', 'as' => 'service-purchase-auto.edit-info']);
    Route::post('service-purchase-auto/success/{id}', ['uses' => 'Admin\Service\PurchaseAutoController@postSuccess', 'as' => 'service-purchase-auto.success']);
    Route::post('service-purchase-auto/recallback', ['uses' => 'Admin\Service\PurchaseAutoController@postRecallback', 'as' => 'service-purchase-auto.recallback']);
    Route::post('service-purchase-auto/lostitem/{id}', ['uses' => 'Admin\Service\PurchaseAutoController@postLostItem', 'as' => 'service-purchase-auto.lostitem']);
    Route::post('service-purchase-auto/completed/{id}', ['uses' => 'Admin\Service\PurchaseAutoController@postCompleted', 'as' => 'service-purchase-auto.completed']);
    Route::post('service-purchase-auto/completed-roblox/{id}', ['uses' => 'Admin\Service\PurchaseAutoController@postCompletedRoblox', 'as' => 'service-purchase-auto.completed-roblox']);
    Route::resource('service-purchase-auto','Admin\Service\PurchaseAutoController');

    Route::post('service-purchase-auto/export-excel', ['uses' => 'Admin\Service\PurchaseAutoController@exportExcel', 'as' => 'service-purchase-auto.export-excel']);

//    Route::post('service-purchase-auto/success/{id}', ['uses' => 'Admin\Service\PurchaseAutoController@postSuccess', 'as' => 'service-purchase-auto.success']);
//    Route::post('service-purchase-auto/recallback', ['uses' => 'Admin\Service\PurchaseAutoController@postRecallback', 'as' => 'service-purchase-auto.recallback']);


    //Tool game
    //Nro Coin
    Route::resource('toolgame/nrocoin-info-bot','Admin\ToolGame\NroCoin\InfoBotController');
    Route::resource('toolgame/nrocoin-usernap','Admin\ToolGame\NroCoin\UserNapController');
    Route::resource('toolgame/nrocoin-logtransaction','Admin\ToolGame\NroCoin\LogTransactionController');
    Route::post('toolgame/nrocoin-logtransaction/nrocoin-export', 'Admin\ToolGame\NroCoin\LogTransactionController@exportExcel')->name('nrocoin-export.index');

    //làng lá Coin
    Route::resource('toolgame/langlacoin-info-bot','Admin\ToolGame\LangLaCoin\InfoBotController');
    Route::resource('toolgame/langlacoin-usernap','Admin\ToolGame\LangLaCoin\UserNapController');
    Route::resource('toolgame/langlacoin-logtransaction','Admin\ToolGame\LangLaCoin\LogTransactionController');


    //Ninja xu
    Route::resource('toolgame/ninjaxu-info-bot','Admin\ToolGame\NinjaXu\InfoBotController');
    Route::resource('toolgame/ninjaxu-usernap','Admin\ToolGame\NinjaXu\UserNapController');
    Route::resource('toolgame/ninjaxu-logtransaction','Admin\ToolGame\NinjaXu\LogTransactionController');
    Route::post('toolgame/ninjaxu-logtransaction/ninjaxu-export', 'Admin\ToolGame\NinjaXu\LogTransactionController@exportExcel')->name('ninjaxu-export.index');


    //Bán ngọc gem
    Route::resource('toolgame/nrogem-info-bot','Admin\ToolGame\NroGem\InfoBotController');
    Route::resource('toolgame/nrogem-usernap','Admin\ToolGame\NroGem\UserNapController');
    Route::resource('toolgame/nrogem-logtransaction','Admin\ToolGame\NroGem\LogTransactionController');
    Route::post('toolgame/nrogem-logtransaction/nrogem-export', 'Admin\ToolGame\NroGem\LogTransactionController@exportExcel')->name('nrogem-export.index');

    //roblox
    Route::resource('toolgame/roblox-info-bot','Admin\ToolGame\Roblox\InfoBotController');

    Route::resource('toolgame/roblox-logtransaction','Admin\ToolGame\Roblox\LogTransactionController');
    Route::post('toolgame/roblox-logtransaction/roblox-export', 'Admin\ToolGame\Roblox\LogTransactionController@exportExcel')->name('roblox-export.index');
    Route::resource('toolgame/roblox-info-bot-san','Admin\ToolGame\Roblox\InfoBotSanController');

    //roblox
    Route::resource('toolgame/roblox-gem-info-bot','Admin\ToolGame\RobloxGem\InfoBotController');
    Route::resource('toolgame/roblox-gem-logtransaction','Admin\ToolGame\RobloxGem\LogTransactionController');
    Route::post('toolgame/roblox-gem-logtransaction/roblox-gem-export', 'Admin\ToolGame\RobloxGem\LogTransactionController@exportExcel')->name('roblox-gem-export.index');

    Route::resource('toolgame/roblox-gem-info-bot','Admin\ToolGame\RobloxGem\InfoBotController');
    Route::resource('toolgame/roblox-gem-logtransaction','Admin\ToolGame\RobloxGem\LogTransactionController');

    Route::post('toolgame/roblox-anime-defender/{id}', ['uses' => 'Admin\ToolGame\RobloxGem\LogTransactionController@robloxAnimeDefender', 'as' => 'toolgame.roblox-anime-defender']);


    Route::post('toolgame/roblox-gem-logtransaction/roblox-gem-export', 'Admin\ToolGame\RobloxGem\LogTransactionController@exportExcel')->name('roblox-gem-export.index');
    Route::get('roblox-gem-info-bot/add-units-{id}', 'Admin\ToolGame\RobloxGem\InfoBotController@addUnits')->name('toolgame.roblox-gem-info-bot.add-units');
    Route::get('roblox-gem-info-bot/add-units-{id}/{id_units}', 'Admin\ToolGame\RobloxGem\InfoBotController@editUnits')->name('toolgame.roblox-gem-info-bot.edit-units');

    Route::post('roblox-gem-info-bot/add-units/store', 'Admin\ToolGame\RobloxGem\InfoBotController@storeUnits')->name('toolgame.roblox-gem-info-bot.add-units.store');
    Route::put('roblox-gem-info-bot/add-units/update/{id}', 'Admin\ToolGame\RobloxGem\InfoBotController@updateUnits')->name('toolgame.roblox-gem-info-bot.add-units.update');
    Route::post('roblox-gem-info-bot/add-units/update-push-quantity', 'Admin\ToolGame\RobloxGem\InfoBotController@updatePushQuantity')->name('toolgame.roblox-gem-info-bot.add-units.update-push-quantity');
    Route::post('roblox-gem-info-bot/add-units/update-minus-quantity', 'Admin\ToolGame\RobloxGem\InfoBotController@updateMinusQuantity')->name('toolgame.roblox-gem-info-bot.add-units.update-minus-quantity');
    Route::post('roblox-gem-info-bot/add-units/update-status-quantity', 'Admin\ToolGame\RobloxGem\InfoBotController@updateStatusQuantity')->name('toolgame.roblox-gem-info-bot.add-units.update-status-quantity');


    //roblox
    Route::resource('toolgame/rbxapi-info-bot','Admin\ToolGame\Rbxapi\InfoBotController');
    Route::resource('toolgame/rbxapi-logtransaction','Admin\ToolGame\Rbxapi\LogTransactionController');
    Route::post('toolgame/rbxapi-logtransaction/rbxapi-export', 'Admin\ToolGame\Rbxapi\LogTransactionController@exportExcel')->name('rbxapi-export.index');

    Route::resource('toolgame/rbxapi-info-bot','Admin\ToolGame\Rbxapi\InfoBotController');
    Route::resource('toolgame/rbxapi-logtransaction','Admin\ToolGame\Rbxapi\LogTransactionController');

    Route::post('toolgame/rbxapi-logtransaction/rbxapi-export', 'Admin\ToolGame\Rbxapi\LogTransactionController@exportExcel')->name('rbxapi-export.index');
    Route::get('rbxapi-info-bot/add-units-{id}', 'Admin\ToolGame\Rbxapi\InfoBotController@addUnits')->name('toolgame.rbxapi-info-bot.add-units');
    Route::get('rbxapi-info-bot/add-units-{id}/{id_units}', 'Admin\ToolGame\Rbxapi\InfoBotController@editUnits')->name('toolgame.rbxapi-info-bot.edit-units');

    Route::post('rbxapi-info-bot/add-units/store', 'Admin\ToolGame\Rbxapi\InfoBotController@storeUnits')->name('toolgame.rbxapi-info-bot.add-units.store');
    Route::put('rbxapi-info-bot/add-units/update/{id}', 'Admin\ToolGame\Rbxapi\InfoBotController@updateUnits')->name('toolgame.rbxapi-info-bot.add-units.update');
    Route::post('rbxapi-info-bot/add-units/update-push-quantity', 'Admin\ToolGame\Rbxapi\InfoBotController@updatePushQuantity')->name('toolgame.rbxapi-info-bot.add-units.update-push-quantity');
    Route::post('rbxapi-info-bot/add-units/update-minus-quantity', 'Admin\ToolGame\Rbxapi\InfoBotController@updateMinusQuantity')->name('toolgame.rbxapi-info-bot.add-units.update-minus-quantity');
    Route::post('rbxapi-info-bot/add-units/update-status-quantity', 'Admin\ToolGame\Rbxapi\InfoBotController@updateStatusQuantity')->name('toolgame.rbxapi-info-bot.add-units.update-status-quantity');



    Route::resource('point', 'Admin\Point\ItemController');
    Route::resource('point-report', 'Admin\Point\ReportController');
    Route::resource('theme', 'Admin\Theme\ItemController');
    Route::resource('theme-attribute', 'Admin\ThemeAttribute\ItemController');

    Route::get('theme/{id}/attribute-value', 'Admin\Theme\ItemController@set_attribute')->name('theme.set_attribute');
    Route::post('theme/attribute-value/{id}', 'Admin\Theme\ItemController@post_set_attribute')->name('theme.post_set_attribute');


    Route::resource('theme-client', 'Admin\ThemeClient\ItemController');

    Route::post('/theme/getAttribute', 'Admin\ThemeClient\ItemController@getAttribute');

    Route::post('/theme-client/pageBuild', 'Admin\ThemeClient\ItemController@postPageBuild')->name('theme-client.build');
    Route::post('/theme-client/pageBuild/order', 'Admin\ThemeClient\ItemController@order')->name('theme-client.build.order');
    Route::post('/theme-client/pageBuild/edit', 'Admin\ThemeClient\ItemController@postEditTitle')->name('theme-client.edit-title');
    Route::post('/theme-client/pageBuild/destroy', 'Admin\ThemeClient\ItemController@destroyPageBuild')->name('theme-client.destroy-page-build');

    Route::post('/theme-client/pageBuild/indestroy', 'Admin\ThemeClient\ItemController@inDestroyPageBuild')->name('theme-client.indestroy-page-build');

    Route::post('/theme-client/pageBuild/duplicate', 'Admin\ThemeClient\ItemController@duplicatePageBuild')->name('theme-client.duplicate-page-build');
    Route::post('/theme-client/pageBuild/status', 'Admin\ThemeClient\ItemController@updateStatus')->name('theme-client.build.updatestatus');

    Route::post('/theme-client/pageBuild/module', 'Admin\ThemeClient\ItemController@updateModule')->name('theme-client.build.module');

    Route::post('/theme-client/pageBuild/display-price', 'Admin\ThemeClient\ItemController@displayPrice')->name('theme-client.build.display-price');

    Route::post('/theme-client/server/image', 'Admin\ThemeClient\ItemController@serverImage')->name('theme-client.server.image');

    Route::post('/theme-client/server/api', 'Admin\ThemeClient\ItemController@serverApi')->name('theme-client.server.api');

    Route::post('/theme-client/category-option', 'Admin\ThemeClient\ItemController@categoryOption')->name('theme-client.category-option');

    Route::post('/theme-client/category-custom-option', 'Admin\ThemeClient\ItemController@categoryCustomOption')->name('theme-client.category-custom-option');

    Route::post('/theme-client/pageBuild/background', 'Admin\ThemeClient\ItemController@updateBackground')->name('theme-client.build.background');

//    Route::post('/theme-client/choice-category-option', 'Admin\ThemeClient\ItemController@choiceCategoryOption')->name('theme-client.choice-category-option');

    //////////////////////////////Bank//////////////////////////////////////////////////////////////
    ///
    Route::post('bank-setting/update_price', ['uses' => 'Admin\Bank\SettingController@UpdatePrice', 'as' => 'bank-setting.update_price']);
    Route::post('bank-setting/post-setting',  ['uses' => 'Admin\Bank\SettingController@postSetting', 'as' => 'bank-setting.post-setting']);
    Route::resource('bank-setting','Admin\Bank\SettingController');

    //bank_account
    Route::resource('bank-account','Admin\Bank\AccountController');
    //withdraw
    Route::get('withdraw/load-info', ['uses' => 'Admin\Bank\WithdrawController@getLoadInfo', 'as' => 'withdraw.load-info']);
    Route::resource('withdraw','Admin\Bank\WithdrawController');

    //withdraw-history
    Route::post('withdraw-history/deny',  ['uses' => 'Admin\Bank\WithdrawHistoryController@postDeny', 'as' => 'withdraw-history.post-deny']);
    Route::resource('withdraw-history','Admin\Bank\WithdrawHistoryController');

    //confirm-withdraw
    Route::get('confirm-withdraw/count', ['uses' => 'Admin\Bank\ConfirmWithdrawController@getCount', 'as' => 'confirm-withdraw.count']);
    Route::post('confirm-withdraw/confirm',  ['uses' => 'Admin\Bank\ConfirmWithdrawController@postConfirm', 'as' => 'confirm-withdraw.post-confirm']);
    Route::post('confirm-withdraw/deny',  ['uses' => 'Admin\Bank\ConfirmWithdrawController@postDeny', 'as' => 'confirm-withdraw.post-deny']);
    Route::resource('confirm-withdraw','Admin\Bank\ConfirmWithdrawController');

    Route::post('confirm-withdraw/export-excel', ['uses' => 'Admin\Bank\ConfirmWithdrawController@exportExcel', 'as' => 'confirm-withdraw.export-excel']);


    Route::resource('server', 'Admin\Server\ItemController');
    Route::resource('server-category', 'Admin\Server\CategoryController');
    Route::post('server/server_updatefield','Admin\Server\ItemController@update_field')->name('server_updatefield');
    Route::post('server/server_gettotal_price','Admin\Server\ItemController@gettotal_price')->name('server_gettotal_price');
    Route::resource('server-catalog', 'Admin\ServerCategory\ItemController');
    Route::post('server-catalog/order', 'Admin\ServerCategory\ItemController@order')->name('server-catalog.order');
    //// Nhóm shop
    Route::post('shop-group/order-shop-in-group', 'Admin\ShopGroup\ItemController@UpdateStatus')->name('shop-group.update-stt')->middleware('throttle:10,1');
    Route::post('shop-group/update-stt', 'Admin\ShopGroup\ItemController@UpdateStatus')->middleware('throttle:10,1');
    Route::get('shop-group/get-shop-in-group','Admin\ShopGroup\ItemController@getShopInGroup')->name('shop-group.get-shop-in-group');
    Route::get('shop-group/search','Admin\ShopGroup\ItemController@getSearchShop')->name('shop-group.get-search');
    Route::post('shop-group/update-shop-in-group','Admin\ShopGroup\ItemController@updateShopInGroup')->name('shop-group.update-shop-in-group');
    Route::post('shop-group/delete-shop-in-group','Admin\ShopGroup\ItemController@deleteShopInGroup')->name('shop-group.delete-shop-in-group');
    Route::resource('shop-group','Admin\ShopGroup\ItemController');


    Route::resource('server-type', 'Admin\ServerType\ItemController');
    Route::post('server-type/order', 'Admin\ServerType\ItemController@order')->name('server-type.order');
    Route::post('/server/loadSubCateServer', 'Admin\Server\ItemController@load_DrdArrSvc');
    Route::post('/server/loadSubCateServerIndex', 'Admin\Server\ItemController@load_DrdArrSvcIndex');
//
//    Route::get('feedback', 'Admin\FeedBack\ItemController@createFeebBack')->name('feedback');
//    Route::delete('feedback/destroy/{id}', 'Admin\FeedBack\ItemController@destroy')->name('feedback.destroy');
//    Route::get('feedback/{id}/edit', 'Admin\FeedBack\ItemController@edit')->name('feedback.edit');
//    Route::put('feedback/update/{id}', 'Admin\FeedBack\ItemController@update')->name('feedback.update');
//    Route::get('feedback-list', ['uses' => 'Admin\FeedBack\ItemController@feedbackList', 'as' => 'feedback-list']);
//    Route::post('feedback/postfeedback','Admin\FeedBack\ItemController@post_feedback')->name('feedback.post_feedback');
//
//    Route::post('/feedback/getComment', 'Admin\FeedBack\ItemController@get_Comment');
//    Route::post('/feedback/postComment','Admin\FeedBack\ItemController@post_comment');
//
//    Route::resource('feedback-config', 'Admin\FeedBackConfig\ItemController');
//    Route::post('/feedback/getInfoFeedBack', 'Admin\FeedBack\ItemController@getInfoFeedBack')->name('feedback.getInfoFeedBack');
//    Route::post('/feedback/countComment', 'Admin\FeedBack\ItemController@countComment')->name('feedback.countComment');

    Route::resource('/cloudflare','Admin\Cloudflare\ItemController');
    Route::post('cloudflare/exposs-excel', 'Admin\Cloudflare\ItemController@exPostExcel')->name('cloudflare.exposs-excel');

    Route::post('shop/update-server', 'Admin\Shop\ItemController@UpdateServer')->name('shop.update-server')->middleware('throttle:10,1');
    Route::post('server/update-server', 'Admin\Server\ItemController@UpdateServer')->name('server.update-server')->middleware('throttle:10,1');
    Route::post('server/update-shop', 'Admin\Server\ItemController@UpdateShop')->name('server.update-shop')->middleware('throttle:10,1');



    //seo - spincontent

    Route::post('/seo/spin-content', 'Admin\Seo\SpinContentController@index');
    //END seo - spincontent


    //config telegram
    Route::post('/telegram-update-config','Admin\Telegram\ConfigController@updateConfig')->name('telegram-config.update');
    Route::post('/telegram-store-group','Admin\Telegram\ConfigController@store')->name('telegram-group.store');
    Route::put('/telegram-update-group','Admin\Telegram\ConfigController@update')->name('telegram-group.update');
    Route::delete('/telegram-delete-group','Admin\Telegram\ConfigController@destroy')->name('telegram-group.delete');
    Route::get('/telegram-send-msg-demo/{shop_id}/{order_group}','Admin\Telegram\ConfigController@sendMessageDemo')->name('telegram-group.send-msg');


});
