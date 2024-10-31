<?php

Route::post('toolgame/getTotalSt','Admin\ToolGame\NroCoin\InfoBotController@getTotalSt');
//bán vàng
Route::get('/nro/get-nick', 'Api\ToolGame\SellCoinController@getNick');
Route::get('/nro/update', 'Api\ToolGame\SellCoinController@getUpdate');
Route::get('/nro/api', 'Api\ToolGame\SellCoinController@getKhachHang');
Route::get('/nro/get-uname', 'Api\ToolGame\SellCoinController@getUname');
Route::get('/nro/check', 'Api\ToolGame\SellCoinController@getCheck');
Route::get('/nro/nap', 'Api\ToolGame\SellCoinController@getNap');
Route::get('/nro/list-bot', 'Api\ToolGame\SellCoinController@listBot');

//bán ngoc
Route::get('/nrogem/main', 'Api\ToolGame\SellGemController@getMain');
Route::get('/nrogem/check-giaodich', 'Api\ToolGame\SellGemController@CheckGiaoDich');
Route::get('/nrogem/check-status', 'Api\ToolGame\SellGemController@CheckStatus');
Route::get('/nrogem/save', 'Api\ToolGame\SellGemController@getSave');
Route::get('/nrogem/update', 'Api\ToolGame\SellGemController@Update');
//bán ngọc - giao dịch mua
Route::get('/nrogem/get-giaodich', 'Api\ToolGame\SellGemController@getGiaoDich');
Route::get('/nrogem/get-uname', 'Api\ToolGame\SellGemController@GetUname');
Route::get('/nrogem/check', 'Api\ToolGame\SellGemController@getCheck');

//Làng lá bán bạc

Route::get('/langlacoin/get-nick', 'Api\ToolGame\LangLaCoinController@getNick');
Route::get('/langlacoin/update', 'Api\ToolGame\LangLaCoinController@getUpdate');
Route::get('/langlacoin/api', 'Api\ToolGame\LangLaCoinController@getKhachHang');
Route::get('/langlacoin/get-uname', 'Api\ToolGame\LangLaCoinController@getUname');
Route::get('/langlacoin/check', 'Api\ToolGame\LangLaCoinController@getCheck');
Route::get('/langlacoin/nap', 'Api\ToolGame\LangLaCoinController@getNap');

//bán xu ninja
Route::get('/ninjaxu/get-nick', 'Api\ToolGame\NinjaXuController@getNick');
Route::get('/ninjaxu/update', 'Api\ToolGame\NinjaXuController@getUpdate');
Route::get('/ninjaxu/api', 'Api\ToolGame\NinjaXuController@getKhachHang');
Route::get('/ninjaxu/get-uname', 'Api\ToolGame\NinjaXuController@getUname');
Route::get('/ninjaxu/check', 'Api\ToolGame\NinjaXuController@getCheck');
Route::get('/ninjaxu/nap', 'Api\ToolGame\NinjaXuController@getNap');
Route::get('/ninjaxu/list-bot', 'Api\ToolGame\NinjaXuController@listBot');

Route::any('/roblox-process', 'Api\ToolGame\RobloxApiController@getProcess');
Route::any('/roblox-process-buy-server', 'Api\ToolGame\RobloxApiController@getProcessBuyServer');
Route::get('/roblox/get-order-roblox', 'Api\ToolGame\RobloxApiController@getOrder');
Route::get('/roblox/get-nick', 'Api\ToolGame\RobloxApiController@getAllNick');
Route::post('/roblox/post-nick-detail', 'Api\ToolGame\RobloxApiController@postNickDetail');
Route::post('/roblox/post-nick','Api\ToolGame\RobloxApiController@portNick');
Route::any('/services-auto-callback-bot-roblox','Api\V1\AgencyService\ServiceAutoListenCallbackController@getCallbackBotRoblox');

Route::get('/roblox/get-order-roblox-premium', 'Api\ToolGame\RobloxApiController@getOrderRobloxPremium');
Route::get('/roblox/check-order-roblox-premium', 'Api\ToolGame\RobloxApiController@checkOrderRobloxPremium');
Route::any('/services-auto-callback-bot-roblox-premium','Api\V1\AgencyService\ServiceAutoListenCallbackController@getCallbackBotRobloxPremium');

Route::get('/roblox/get-order-huge-psx-roblox', 'Api\ToolGame\RobloxApiController@getOrderHugePsx');
Route::any('/services-auto-callback-bot-huge-psx-roblox','Api\V1\AgencyService\ServiceAutoListenCallbackController@getCallbackBotHugePsxRoblox');

Route::get('/roblox/get-order-gem-pet-99', 'Api\ToolGame\RobloxApiController@getOrderGempet99');
Route::any('/services-auto-callback-bot-gem-pet-99','Api\V1\AgencyService\ServiceAutoListenCallbackController@getCallbackBotGempet99');

Route::get('/roblox/get-order-item-pet-go', 'Api\ToolGame\RobloxApiController@getOrderItemPetGo');
Route::any('/services-auto-callback-bot-item-pet-go','Api\V1\AgencyService\ServiceAutoListenCallbackController@getCallbackBotItemPetGo');

Route::get('/roblox/get-order-gem-huge-99', 'Api\ToolGame\RobloxApiController@getOrderGemHuge99');
Route::any('/services-auto-callback-bot-gem-huge-99','Api\V1\AgencyService\ServiceAutoListenCallbackController@getCallbackBotGemHuge99');


Route::get('/roblox/get-order-gem-unist', 'Api\ToolGame\RobloxApiController@getOrderGemUnist');
Route::any('/services-auto-callback-bot-gem-unist','Api\V1\AgencyService\ServiceAutoListenCallbackController@getCallbackBotGemUnist');

Route::get('/roblox/get-order-unist', 'Api\ToolGame\RobloxApiController@getOrderUnist');
Route::any('/services-auto-callback-bot-unist','Api\V1\AgencyService\ServiceAutoListenCallbackController@getCallbackBotUnist');

Route::get('/roblox/get-order-unist-v2', 'Api\ToolGame\RobloxApiController@getOrderUnistV2');
Route::any('/services-auto-callback-bot-unist-v2','Api\V1\AgencyService\ServiceAutoListenCallbackController@getCallbackBotUnistV2');

Route::any('/services-auto-rbx-api','Api\V1\AgencyService\ServiceAutoListenCallbackController@getCallbackRbxApi');





