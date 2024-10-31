<?php


//***************************************AREA FOR  MODULE IS NOT COMMON**************************************

//----------------------------------------- module minigame -----------------------------------------//


Route::resource('minigame-statitics','Admin\Minigame\Module\MinigameStatiticsController');
Route::resource('withdraw-statitics','Admin\Minigame\Module\WithdrawStatiticsController');
Route::resource('withdraw-package-statitics','Admin\Minigame\Module\WithdrawPackageStatiticsController');
Route::resource('package','Admin\Minigame\Module\PackageController');
Route::resource('package-config','Admin\Minigame\Module\PackageConfigController');
Route::post('package-config/{id}/update-config-base', 'Admin\Minigame\Module\PackageConfigController@update_config_base')->name('package-config.update-config-base');

Route::post('package-config/update-sticky','Admin\Minigame\Module\PackageController@updateSticky')->name('package-config.update-sticky');

Route::post('package-config/update-async-one','Admin\Minigame\Module\PackageController@updateAsyncOne')->name('package-config.update-async-one');
Route::post('package-config/update-async-two','Admin\Minigame\Module\PackageController@updateAsyncTwo')->name('package-config.update-async-two');
Route::post('package-config/update-async-three','Admin\Minigame\Module\PackageController@updateAsyncThree')->name('package-config.update-async-three');

Route::resource('gametype','Admin\Minigame\Module\GametypeController');
Route::post('withdraw-item/changestatus','Admin\Minigame\Module\WithdrawLogController@changeStatus')->name('withdraw-item-changestatus');
Route::post('withdraw-item-auto/changestatus','Admin\Minigame\Module\WithdrawLogAutoController@changeStatus')->name('withdraw-item-changestatus-auto');
Route::post('withdraw-item/recharge','Admin\Minigame\Module\WithdrawLogController@recharge')->name('withdraw-item-recharge');

Route::post('withdraw-item/rechargetimeout','Admin\Minigame\Module\WithdrawLogController@rechargeTimeOut')->name('withdraw-item-rechargetimeout');

Route::post('withdraw-item/delete-recharge','Admin\Minigame\Module\WithdrawLogController@deleteRecharge')->name('withdraw-item-delete-recharge');

Route::resource('withdraw-item','Admin\Minigame\Module\WithdrawLogController');
Route::resource('withdraw-item-auto','Admin\Minigame\Module\WithdrawLogAutoController');
Route::post('withdrawlog-export', 'Admin\Minigame\Module\WithdrawLogController@exportExcel')->name('withdrawlog-export.index');
Route::get('/withdraw-item/{id}/shows','Admin\Minigame\Module\WithdrawLogController@showReportWithdrawItem')->name('withdraw-item.shows');
Route::post('withdrawlog-export-auto', 'Admin\Minigame\Module\WithdrawLogAutoController@exportExcel')->name('withdrawlog-export-auto.index');
Route::get('/withdraw-item-auto/{id}/shows','Admin\Minigame\Module\WithdrawLogAutoController@showReportWithdrawItem')->name('withdraw-item-auto.shows');

Route::post('minigame/updatefield','Admin\Minigame\Module\ItemController@update_field')->name('updatefield');

Route::post('{module}/import', 'Admin\Minigame\Module\AccController@import')->name('minigame-acc.import');
//minigame
Route::post('minigame-category/order', 'Admin\Minigame\Module\CategoryController@order')->name('minigame-category.order');
Route::get('minigame-category/{id}/setitem','Admin\Minigame\Module\CategoryController@setitem')->name('minigame-category.setitem');
Route::post('minigame-category/setcustom','Admin\Minigame\Module\CategoryController@setcustom')->name('minigame-category.setcustom');
Route::post('minigame-category/{id}/setitem','Admin\Minigame\Module\CategoryController@updateitem');
Route::post('minigame-category/updatefield','Admin\Minigame\Module\CategoryController@update_field')->name('minigame-category.updatefield');
Route::post('minigame-category/updatefieldcat','Admin\Minigame\Module\CategoryController@update_fieldcat')->name('minigame-category.updatefieldcat');
Route::resource('minigame-category','Admin\Minigame\Module\CategoryController');
Route::post('minigame-category/convert-content','Admin\Minigame\Module\CategoryController@convertContent')->name('minigame-category.convert-content');
Route::get('minigame-value-item','Admin\Minigame\Module\CategoryController@valueItem')->name('minigame-value-item.index');

Route::post('minigame-seedingpackage/updatefield','Admin\Minigame\Module\SeedingpackageController@update_field')->name('minigame-seedingpackage.updatefield');
Route::resource('minigame-seedingpackage','Admin\Minigame\Module\SeedingpackageController');
Route::resource('minigame-seedingchat','Admin\Minigame\Module\SeedingchatController');
Route::post('minigame-seedingchat/duplicate', 'Admin\Minigame\Module\SeedingchatController@cloneItem')->name('minigame-seedingchat.clone');

Route::resource('minigame-log','Admin\Minigame\minigame\LogController');
Route::get('minigame/{id}/duplicate', 'Admin\Minigame\Module\ItemController@duplicate')->name('minigame.duplicate');
Route::resource('minigame-type','Admin\Minigame\Module\ItemTypeController');
Route::resource('minigame','Admin\Minigame\Module\ItemController');
Route::resource('minigame-acc','Admin\Minigame\Module\AccController');
Route::resource('minigame-log','Admin\Minigame\Module\LogController');
Route::resource('minigame-logacc','Admin\Minigame\Module\LogAccController');
Route::post('minigame-export', 'Admin\Minigame\Module\LogController@exportExcel')->name('minigame-export.index');

Route::get('minigame-category/{id}/revision/{slug}', 'Admin\Minigame\Module\CategoryController@revision')->name('minigame-category.revision');
Route::post('minigame-category/{id}/revision/{slug}', 'Admin\Minigame\Module\CategoryController@postRevision')->name('minigame-category.postrevision');

//Nhân bản.

Route::post('minigame-category/replication','Admin\Minigame\Module\CategoryController@replication')->name('minigame-category.replication');
Route::post('minigame-category/{id}/distribution','Admin\Minigame\Module\CategoryController@distribution')->name('minigame-category.distribution');

//phân phối

Route::post('minigame-category/deletegroupshop','Admin\Minigame\Module\CategoryController@deletegroupshop')->name('minigame-category.deletegroupshop');
Route::post('minigame-category/activegroupshop','Admin\Minigame\Module\CategoryController@activegroupshop')->name('minigame-category.activegroupshop');
Route::post('minigame-category/activeshop','Admin\Minigame\Module\CategoryController@activeshop')->name('minigame-category.activeshop');
Route::post('minigame-category/deleteitem','Admin\Minigame\Module\CategoryController@deleteitem')->name('minigame-category.deleteitem');
Route::post('minigame-category/{id}/clonegiaithuong','Admin\Minigame\Module\CategoryController@cloneGiaiThuong')->name('minigame-category.clonegiaithuong');


// Route::resource('minigame-statitics','Admin\Minigame\Module\StatiticsController');

// //flip
// Route::post('flip-category/order', 'Admin\Minigame\Module\CategoryController@order')->name('flip-category.order');
// Route::resource('flip-category','Admin\Minigame\Module\CategoryController');
// Route::resource('flip-statitics','Admin\Minigame\flip\LogController@statitics');
// Route::resource('flip-log','Admin\Minigame\flip\LogController');
// Route::get('flip/{id}/duplicate', 'Admin\Minigame\Module\ItemController@duplicate')->name('flip.duplicate');
// Route::resource('flip','Admin\Minigame\Module\ItemController');
// Route::resource('flip-acc','Admin\Minigame\Module\AccController');
// Route::resource('flip-log','Admin\Minigame\Module\LogController');
// Route::resource('flip-logacc','Admin\Minigame\Module\LogAccController');
// Route::resource('flip-statitics','Admin\Minigame\Module\StatiticsController');

// //slotmachine
// Route::post('slotmachine-category/order', 'Admin\Minigame\Module\CategoryController@order')->name('slotmachine-category.order');
// Route::resource('slotmachine-category','Admin\Minigame\Module\CategoryController');
// Route::resource('slotmachine-statitics','Admin\Minigame\slotmachine\LogController@statitics');
// Route::resource('slotmachine-log','Admin\Minigame\slotmachine\LogController');
// Route::get('slotmachine/{id}/duplicate', 'Admin\Minigame\Module\ItemController@duplicate')->name('slotmachine.duplicate');
// Route::resource('slotmachine','Admin\Minigame\Module\ItemController');
// Route::resource('slotmachine-acc','Admin\Minigame\Module\AccController');
// Route::resource('slotmachine-log','Admin\Minigame\Module\LogController');
// Route::resource('slotmachine-logacc','Admin\Minigame\Module\LogAccController');
// Route::resource('slotmachine-statitics','Admin\Minigame\Module\StatiticsController');

// //slotmachine5
// Route::post('slotmachine5-category/order', 'Admin\Minigame\Module\CategoryController@order')->name('slotmachine5-category.order');
// Route::resource('slotmachine5-category','Admin\Minigame\Module\CategoryController');
// Route::resource('slotmachine5-statitics','Admin\Minigame\slotmachine5\LogController@statitics');
// Route::resource('slotmachine5-log','Admin\Minigame\slotmachine5\LogController');
// Route::get('slotmachine5/{id}/duplicate', 'Admin\Minigame\Module\ItemController@duplicate')->name('slotmachine5.duplicate');
// Route::resource('slotmachine5','Admin\Minigame\Module\ItemController');
// Route::resource('slotmachine5-acc','Admin\Minigame\Module\AccController');
// Route::resource('slotmachine5-log','Admin\Minigame\Module\LogController');
// Route::resource('slotmachine5-logacc','Admin\Minigame\Module\LogAccController');
// Route::resource('slotmachine5-statitics','Admin\Minigame\Module\StatiticsController');

// //squarewheel
// Route::post('squarewheel-category/order', 'Admin\Minigame\Module\CategoryController@order')->name('squarewheel-category.order');
// Route::resource('squarewheel-category','Admin\Minigame\Module\CategoryController');
// Route::resource('squarewheel-statitics','Admin\Minigame\squarewheel\LogController@statitics');
// Route::resource('squarewheel-log','Admin\Minigame\squarewheel\LogController');
// Route::get('squarewheel/{id}/duplicate', 'Admin\Minigame\Module\ItemController@duplicate')->name('squarewheel.duplicate');
// Route::resource('squarewheel','Admin\Minigame\Module\ItemController');
// Route::resource('squarewheel-acc','Admin\Minigame\Module\AccController');
// Route::resource('squarewheel-log','Admin\Minigame\Module\LogController');
// Route::resource('squarewheel-logacc','Admin\Minigame\Module\LogAccController');
// Route::resource('squarewheel-statitics','Admin\Minigame\Module\StatiticsController');

// //smashwheel
// Route::post('smashwheel-category/order', 'Admin\Minigame\Module\CategoryController@order')->name('smashwheel-category.order');
// Route::resource('smashwheel-category','Admin\Minigame\Module\CategoryController');
// Route::resource('smashwheel-statitics','Admin\Minigame\smashwheel\LogController@statitics');
// Route::resource('smashwheel-log','Admin\Minigame\smashwheel\LogController');
// Route::get('smashwheel/{id}/duplicate', 'Admin\Minigame\Module\ItemController@duplicate')->name('smashwheel.duplicate');
// Route::resource('smashwheel','Admin\Minigame\Module\ItemController');
// Route::resource('smashwheel-acc','Admin\Minigame\Module\AccController');
// Route::resource('smashwheel-log','Admin\Minigame\Module\LogController');
// Route::resource('smashwheel-logacc','Admin\Minigame\Module\LogAccController');
// Route::resource('smashwheel-statitics','Admin\Minigame\Module\StatiticsController');

// //dicewheel
// Route::post('dicewheel-category/order', 'Admin\Minigame\Module\CategoryController@order')->name('dicewheel-category.order');
// Route::resource('dicewheel-category','Admin\Minigame\Module\CategoryController');
// Route::resource('dicewheel-statitics','Admin\Minigame\dicewheel\LogController@statitics');
// Route::resource('dicewheel-log','Admin\Minigame\dicewheel\LogController');
// Route::get('dicewheel/{id}/duplicate', 'Admin\Minigame\Module\ItemController@duplicate')->name('dicewheel.duplicate');
// Route::resource('dicewheel','Admin\Minigame\Module\ItemController');
// Route::resource('dicewheel-acc','Admin\Minigame\Module\AccController');
// Route::resource('dicewheel-log','Admin\Minigame\Module\LogController');
// Route::resource('dicewheel-logacc','Admin\Minigame\Module\LogAccController');
// Route::resource('dicewheel-statitics','Admin\Minigame\Module\StatiticsController');

// //rungcay
// Route::post('rungcay-category/order', 'Admin\Minigame\Module\CategoryController@order')->name('rungcay-category.order');
// Route::resource('rungcay-category','Admin\Minigame\Module\CategoryController');
// Route::resource('rungcay-statitics','Admin\Minigame\rungcay\LogController@statitics');
// Route::resource('rungcay-log','Admin\Minigame\rungcay\LogController');
// Route::get('rungcay/{id}/duplicate', 'Admin\Minigame\Module\ItemController@duplicate')->name('rungcay.duplicate');
// Route::resource('rungcay','Admin\Minigame\Module\ItemController');
// Route::resource('rungcay-acc','Admin\Minigame\Module\AccController');
// Route::resource('rungcay-log','Admin\Minigame\Module\LogController');
// Route::resource('rungcay-logacc','Admin\Minigame\Module\LogAccController');
// Route::resource('rungcay-statitics','Admin\Minigame\Module\StatiticsController');

// //gieoque
// Route::post('gieoque-category/order', 'Admin\Minigame\Module\CategoryController@order')->name('gieoque-category.order');
// Route::resource('gieoque-category','Admin\Minigame\Module\CategoryController');
// Route::resource('gieoque-statitics','Admin\Minigame\gieoque\LogController@statitics');
// Route::resource('gieoque-log','Admin\Minigame\gieoque\LogController');
// Route::get('gieoque/{id}/duplicate', 'Admin\Minigame\Module\ItemController@duplicate')->name('gieoque.duplicate');
// Route::resource('gieoque','Admin\Minigame\Module\ItemController');
// Route::resource('gieoque-acc','Admin\Minigame\Module\AccController');
// Route::resource('gieoque-log','Admin\Minigame\Module\LogController');
// Route::resource('gieoque-logacc','Admin\Minigame\Module\LogAccController');
// Route::resource('gieoque-statitics','Admin\Minigame\Module\StatiticsController');

//----------------------------------------- module minigame -----------------------------------------//

//----------------------------------------- module article -----------------------------------------//
//
////article-category
//Route::post('article-category/order', 'Admin\Module\CategoryController@order')->name('article-category.order');
//Route::resource('article-category','Admin\Module\CategoryController');
//
//Route::post('menu-profile/switchurl', 'Admin\Module\CategoryController@switchUrl')->name('menu-profile.switchurl');
//
//Route::post('menu-profile/switchurlnick', 'Admin\Module\CategoryController@switchUrlNick')->name('menu-profile.switchurlnick');
//
//Route::post('article-category/switchrouter', 'Admin\Module\CategoryController@switchRouter')->name('article-category.switchrouter');
////Auto link
////Route::post('auto-link/order', 'Admin\Module\CategoryController@order')->name('auto-link.order');
//Route::resource('auto-link','Admin\AutoLink\AutoLinkController');
//Route::post('auto-link/update-stt', 'Admin\AutoLink\AutoLinkController@updateStatus')->name('auto-link.update-stt');
//Route::post('auto-link/show-url', 'Admin\AutoLink\AutoLinkController@showUrl')->name('auto-link.show-url');
////article-group
//Route::get('article-group/{id}/duplicate', 'Admin\Module\GroupController@duplicate')->name('article-group.duplicate');
//Route::get('article-group/search', 'Admin\Module\GroupController@search')->name('article-group.search');
//Route::get('article-group/show-item','Admin\Module\GroupController@showItemGroup')->name('article-group.show-item');
//Route::get('article-group/update-item','Admin\Module\GroupController@updateItemGroup')->name('article-group.update-item');
//Route::post('article-group/delete-item','Admin\Module\GroupController@deleteItemGroup')->name('article-group.delete-item');
//Route::resource('article-group','Admin\Module\GroupController');
//
//
////article
//Route::get('article/{id}/duplicate', 'Admin\Module\ItemController@duplicate')->name('article.duplicate');
//Route::resource('article','Admin\Module\ItemController');
//Route::get('article/{id}/revision/{slug}', 'Admin\Module\ItemController@revision')->name('article.revision');
//Route::post('article/{id}/revision/{slug}', 'Admin\Module\ItemController@postRevision')->name('article.postrevision');
//Route::post('article/duplicate', 'Admin\Module\ItemController@cloneItem')->name('article.clone');
//Route::post('article/autosave-content', 'Admin\Module\ItemController@autosaveContent')->name('article.autosave-content');
//
//Route::post('article/zip', 'Admin\Module\ItemController@zipItem')->name('article.zip');
//
//Route::post('article-category/duplicate', 'Admin\Module\CategoryController@duplicate')->name('article-category.duplicate');
//
//Route::post('article/zip', 'Admin\Module\ItemController@zipItem')->name('article.zip');
//Route::post('article/zipsetting', 'Admin\Module\ItemController@zipSetting')->name('article.zipsetting');
//
//Route::post('article/zipv1', 'Admin\Module\ItemController@zipItemV1')->name('article.zipv1');
//Route::post('article/zipsettingv1', 'Admin\Module\ItemController@zipSettingV1')->name('article.zipsettingv1');
//
//Route::post('article/switchimage', 'Admin\Module\ItemController@switchImage')->name('article.switchimage');
//
//
////----------------------------------------- game -----------------------------------------//
//
////game-category
//Route::post('game-category/order', 'Admin\Module\CategoryController@order')->name('game-category.order');
//Route::resource('game-category','Admin\Module\CategoryController');
//
////game-group
//Route::post('game-group/order', 'Admin\Module\GroupController@order')->name('game-group.order');
//Route::get('game-group/{id}/duplicate', 'Admin\Module\GroupController@duplicate')->name('game-group.duplicate');
//Route::get('game-group/search', 'Admin\Module\GroupController@search')->name('game-group.search');
//Route::get('game-group/show-item','Admin\Module\GroupController@showItemGroup')->name('game-group.show-item');
//Route::get('game-group/update-item','Admin\Module\GroupController@updateItemGroup')->name('game-group.update-item');
//Route::post('game-group/delete-item','Admin\Module\GroupController@deleteItemGroup')->name('game-group.delete-item');
//Route::resource('game-group','Admin\Module\GroupController');

//Group idol

//Route::resource('group-idol','Admin\Module\GroupController');
//Route::get('group-idol/search', 'Admin\Module\GroupController@search')->name('group-idol.search');
//Route::get('group-idol/show-item','Admin\Module\GroupController@showItemGroup')->name('group-idol.show-item');
//Route::get('group-idol/update-item','Admin\Module\GroupController@updateItemGroup')->name('group-idol.update-item');
//Route::post('group-idol/delete-item','Admin\Module\GroupController@deleteItemGroup')->name('group-idol.delete-item');
//Route::get('group-idol/{id}/duplicate', 'Admin\Module\GroupController@duplicate')->name('group-idol.duplicate');

////article
//Route::get('game/{id}/duplicate', 'Admin\Module\ItemController@duplicate')->name('game.duplicate');
//Route::resource('game','Admin\Module\ItemController');
//
//
//
////----------------------------------------- sticky -----------------------------------------//
////sticky
//Route::get('sticky/{id}/duplicate', 'Admin\Module\ItemController@duplicate')->name('sticky.duplicate');
//Route::resource('sticky','Admin\Module\ItemController');
//
////----------------------------------------- sticky -----------------------------------------//
////sticky
//Route::get('audio/{id}/duplicate', 'Admin\Module\ItemController@duplicate')->name('audio.duplicate');
//Route::resource('audio','Admin\Module\ItemController');
//
//
////----------------------------------------- module menu -----------------------------------------//
//
////menu-category
//Route::post('menu-category/order', 'Admin\Module\CategoryController@order')->name('menu-category.order');
//Route::resource('menu-category','Admin\Module\CategoryController');
//Route::post('menu-category/duplicate', 'Admin\Module\CategoryController@duplicate')->name('menu-category.duplicate');
//
////menu-profile
//Route::post('menu-profile/order', 'Admin\Module\CategoryController@order')->name('menu-profile.order');
//Route::resource('menu-profile','Admin\Module\CategoryController');
//Route::post('menu-profile/duplicate', 'Admin\Module\CategoryController@duplicate')->name('menu-profile.duplicate');
//
////menu-profile
//Route::post('menu-transaction/order', 'Admin\Module\CategoryController@order')->name('menu-transaction.order');
//Route::resource('menu-transaction','Admin\Module\CategoryController');
//Route::post('menu-transaction/duplicate', 'Admin\Module\CategoryController@duplicate')->name('menu-transaction.duplicate');
//
//// Sort game
//Route::post('sort-game/order', 'Admin\Module\SortGameController@order')->name('sort-game.order');
//Route::resource('sort-game','Admin\Module\SortGameController');
//
////----------------------------------------- module page -----------------------------------------//
//
////page
//Route::get('page/{id}/duplicate', 'Admin\Module\ItemController@duplicate')->name('page.duplicate');
//Route::resource('page','Admin\Module\ItemController');
//
//
//
////----------------------------------------- module advertise -----------------------------------------//
//
////advertise-category
//Route::post('advertise-category/order', 'Admin\Module\CategoryController@order')->name('advertise-category.order');
//Route::resource('advertise-category','Admin\Module\CategoryController');
//
////advertise
//Route::get('advertise/{id}/duplicate', 'Admin\Module\ItemController@duplicate')->name('advertise.duplicate');
//Route::resource('advertise','Admin\Module\ItemController');
//Route::post('advertise/duplicate', 'Admin\Module\ItemController@cloneItem')->name('advertise.clone');
//
////advertise-category
//Route::post('advertise-category/order', 'Admin\Module\CategoryController@order')->name('advertise-category.order');
//Route::resource('advertise-category','Admin\Module\CategoryController');
//
//Route::resource('advertise-ads','Admin\Module\AdsController');
//Route::post('advertise-ads/duplicate', 'Admin\Module\AdsController@cloneItem')->name('advertise-ads.clone');


