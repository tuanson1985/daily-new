<?php

namespace App\Http\Controllers\Api\V1\Service;

use App\Http\Controllers\Controller;
use App\Library\Helpers;
use App\Library\MediaHelpers;
use App\Models\Item;
use App\Models\KhachHang;
use App\Models\NinjaXu_KhachHang;
use App\Models\Nrogem_GiaoDich;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Roblox_Order;
use App\Models\SubItem;
use App\Models\Txns;
use App\Models\TxnsVp;
use App\Models\User;
use Auth;
use Cache;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Log;
use Validator;
use App\Models\Setting;

class ServiceAutoListenCallbackController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('throttle:1000,1', ['except' => '']);

    }

    //List Callback Tichhop call về
    public function getCallback(Request $request)
    {
        //lưu log gọi curl
        $path = storage_path() ."/logs/services-auto/";
        $filename=$path."listen_callback_".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
        \File::append($filename,$contentText."\n");


        if ($request->get('sign') != '3857gjfhnj51') {
            return "Không đúng mã bí mật. Xin thử lại ";
        }
        return $this->handle($request);
    }

    public function handle(Request $request)
    {

        try {

            $result_final = false;

            DB::transaction(function () use ($request, &$result_final) {
                $result_final = false;

                $status = $request->get('status');
                $message = $request->message;
                $request_id=$request->request_id;
                //tìm lệnh rút
                $order = Order::where('request_id',$request_id)
                    ->where('module', '=',config('module.service-purchase.key'))
                    ->lockForUpdate()->first();

                if (!$order) {
                    //debug thì mở cái này
                    $txt = Carbon::now() . ":" . $request->fullUrl();
                    $result_final = "[Not found]:" . $txt . "\n\n";
                    throw new \Exception($result_final, 44);
                } else {

                    if ($order->status == 0 || $order->status == 3 || $order->status == 4 || $order->status == 5) {

                        $result_final = "Giao dịch đã được xử lý thành công trước đó";
                        throw new \Exception($result_final, 44);

                    }
                }

                //tìm user nạp
                $userTransaction = User::where('id', $order->author_id)->lockForUpdate()->firstOrFail();

                if ($request->get('status') == 1) {
                    // Update lại trạng thái
                    $order->status = 4;//hoàn thành
                    $order->content = $message;
                    $order->updated_at = Carbon::now();
                    $order->price_input = $request->amount;
                    $order->save();

                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 4,
                        'content' => "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!",

                    ]);
                    $result_final = true;

                }
                elseif ($request->get('status') == 3) {

                    //hoàn tiền cho user
                    $userTransaction->balance = $userTransaction->balance + $order->price;
                    $userTransaction->balance_in = $userTransaction->balance_in + $order->price;
                    $userTransaction->save();
                    // Update lại trạng thái
                    $order->status = 5;
                    $order->content = "Giao dịch thất bại. ".$message;
                    $order->updated_at = Carbon::now();
                    $order->save();

                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 5,
                        'content' => "Giao dịch thất bại. ".$message

                    ]);
                    $order->txns()->create([
                        'trade_type' => 'refund', //Hoàn tiền
                        'user_id' => $userTransaction->id,
                        'is_add' => '1',//Cộng tiền
                        'amount' => $order->price,
                        'real_received_amount' => $order->price,
                        'last_balance' => $userTransaction->balance,
                        'description' => "Hoàn tiền giao dịch lỗi dịch vụ #" . $order->id,
                        'ip' => $request->getClientIp(),
                        'status' => 1,
                        'shop_id' => $order->shop_id,
                    ]);
                    $result_final = true;


                }

            });
            if ($result_final === true) {

                return 'Xử lý giao dịch thành công #' . $request->request_id;
            } else {
                return '[Lỗi] Xử lý thất bại#' . $request->request_id;
            }


        } catch (\Exception $e) {
            if ($e->getCode() == 44) {
                return $e->getMessage();
            }
            Log::error($e);
            return 'Có lỗi phát sinh.Xin vui lòng thử lại !';
        }
    }


    //List Callback DAILY call về
    public function getCallbackDaily(Request $request)
    {

        //lưu log gọi curl
        $path = storage_path() ."/logs/services-auto/";
        $filename=$path."listen_callback_daily".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
        \File::append($filename,$contentText."\n");

        if ($request->get('sign') != config('daily.sign')) {
            return "Không đúng mã bí mật. Xin thử lại ";
        }
        return $this->handleDaily($request);
    }

    public function handleDaily(Request $request)
    {

        DB::beginTransaction();
        try {

            $order = Order::where('status',1)
                ->where(function($q){
                    $q->orWhere('module', '=',config('module.minigame.module.withdraw-service-item'));
                    $q->orWhere('module', '=',config('module.service-purchase.key'));
                })
                ->where('request_id',$request->get('request_id'))
                ->lockForUpdate()
                ->first();

            if(!$order){
                return "Đơn hàng đã xử lý trước đó";
            }
            if($order->idkey == 'nrogem'){
                $data = Nrogem_GiaoDich::where('order_id',$order->id)->lockForUpdate()->first();
            }else if($order->idkey == 'ninjaxu'){
                $data = NinjaXu_KhachHang::where('order_id',$order->id)->lockForUpdate()->first();
            }
            else if($order->idkey == 'nrocoin'){
                $data = KhachHang::where('order_id',$order->id)->lockForUpdate()->first();
            }
            else if($order->idkey == 'roblox_buyserver' || $order->idkey == 'roblox_buygamepass' || $order->idkey == 'roblox_gem_pet'){
                $data = Roblox_Order::query()->with('order')->where('order_id',$order->id)->lockForUpdate()->first();
            }

            $module = config('module.service-workflow.key');

            if ($order->module == config('module.minigame.module.withdraw-service-item')){
                $module = config('module.withdraw-service-workflow.key');
            }

            if($request->get('status')==4 ){
                //cập nhật trạng thái của purchase
                $order->status = 4;
                $order->price_input = $request->price;
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => $module,
                    'status' => 4,
                    'content' => "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!",

                ]);

                //tiến độ lưu ảnh.
                if ($request->get('image')){

                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => $module,
                        'status' => 85,
                        'content' => $request->get('image'),

                    ]);
                }

                $data->status = $request->get('message');
                $data->save();
            }
            else if($request->get('status') == 5 || $request->get('status') == 3 ){
                if($request->get('status') == 3){
                    $order->status = 3;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => $module,
                        'status' => $request->get('status'),
                        'content' => $request->get('message'),
                    ]);
                    $data->status = $request->get('message');
                    $data->save();
                }
                else if($request->get('status') == 5){
                    $order->status = 5;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => $module,
                        'status' => $request->get('status'),
                        'content' => $request->get('message'),
                    ]);
                    $data->status = $request->get('message');
                    $data->save();
                }

                //tìm user nạp
                $userTransaction = User::where('id', $order->author_id)->lockForUpdate()->firstOrFail();

                if ($order->module == config('module.service-purchase.key')){
                    //hoàn tiền cho user
                    $userTransaction->balance = $userTransaction->balance + $order->price;
                    $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $order->price;
                    $userTransaction->save();

                    $order->txns()->create([
                        'trade_type' => 'refund', //Hoàn tiền
                        'user_id' => $userTransaction->id,
                        'is_add' => '1',//Cộng tiền
                        'amount' => $order->price,
                        'real_received_amount' => $order->price,
                        'last_balance' => $userTransaction->balance,
                        'description' => "Hoàn tiền giao dịch lỗi dịch vụ #" . $order->id,
                        'ip' => $request->getClientIp(),
                        'status' => 1,
                        'shop_id' => $order->shop_id,
                    ]);
                }
                elseif ($order->module == config('module.minigame.module.withdraw-service-item')){

                    $type = $order->payment_type;
                    $amount = $order->price;
                    if($order->idkey == 'nrogem'){
                        $balance_item_txns = $userTransaction->gem_num;
                        $userTransaction->gem_num = $userTransaction->gem_num + $amount;
                        $userTransaction->save();
                    }else if($order->idkey == 'ninjaxu'){
                        $balance_item_txns = $userTransaction->xu_num;
                        $userTransaction->xu_num = $userTransaction->xu_num + $amount;
                        $userTransaction->save();
                    }
                    else if($order->idkey == 'nrocoin'){
                        $balance_item_txns = $userTransaction->coin_num;
                        $userTransaction->coin_num = $userTransaction->coin_num + $amount;
                        $userTransaction->save();
                    }else if($order->idkey == 'roblox_buyserver' || $order->idkey == 'roblox_buygamepass' || $order->idkey == 'roblox_gem_pet'){
                        $balance_item_txns = $userTransaction->robux_num;
                        $userTransaction->robux_num = $userTransaction->robux_num + $amount;
                        $userTransaction->save();
                    }

                    //tạo tnxs vp
                    $txns = TxnsVp::create([
                        'trade_type' => config('module.txnsvp.trade_type.refund'),
                        'is_add' => '1',
                        'user_id' => $userTransaction->id,
                        'amount' => $amount,
                        'last_balance' => $balance_item_txns + $amount,
                        'description' => "Hoàn ".$order->idkey." rút vật phẩm thất bại gói rút" . $order->ref_id . " - "."dịch vụ ".$order->sticky." #".$order->id ,
                        'ref_id' => $order->id,
                        'status' => 1,
                        'shop_id' => $order->shop_id,
                        'order_id' => $order->id,
                        'item_type' => $type
                    ]);
                }
            }

            //77 là mất item không hoàn tiền
            else if($request->get('status') == 77  ){
                $order->status = 77;
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => $module,
                    'status' => $request->get('status'),
                    'content' => $request->get('message'),
                ]);
                $data->status = $request->get('message');
                $data->save();
            }

            else if($request->get('status') == 88 ){

                $order->status = 88;
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => $module,
                    'status' => $request->get('status'),
                    'content' => $request->get('message'),
                ]);
                $data->status = $request->get('message');
                $data->save();
                //tìm user nạp
                $userTransaction = User::where('id', $order->author_id)->lockForUpdate()->firstOrFail();

                if ($order->module == config('module.service-purchase.key')){

                    //hoàn tiền cho user
                    $userTransaction->balance = $userTransaction->balance + $order->price;
                    $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $order->price;
                    $userTransaction->save();

                    $order->txns()->create([
                        'trade_type' => 'refund', //Hoàn tiền
                        'user_id' => $userTransaction->id,
                        'is_add' => '1',//Cộng tiền
                        'amount' => $order->price,
                        'real_received_amount' => $order->price,
                        'last_balance' => $userTransaction->balance,
                        'description' => "Hoàn tiền mất item dịch vụ #" . $order->id,
                        'ip' => $request->getClientIp(),
                        'status' => 1,
                        'shop_id' => $order->shop_id,
                    ]);
                }
                elseif ($order->module == config('module.minigame.module.withdraw-service-item')){
                    $type = $order->payment_type;
                    $amount = $order->price;
                    if($order->idkey == 'nrogem'){
                        $balance_item_txns = $userTransaction->gem_num;
                        $userTransaction->gem_num = $userTransaction->gem_num + $amount;
                        $userTransaction->save();
                    }else if($order->idkey == 'ninjaxu'){
                        $balance_item_txns = $userTransaction->xu_num;
                        $userTransaction->xu_num = $userTransaction->xu_num + $amount;
                        $userTransaction->save();
                    }
                    else if($order->idkey == 'nrocoin'){
                        $balance_item_txns = $userTransaction->coin_num;
                        $userTransaction->coin_num = $userTransaction->coin_num + $amount;
                        $userTransaction->save();
                    }else if($order->idkey == 'roblox_buyserver' || $order->idkey == 'roblox_buygamepass' || $order->idkey == 'roblox_gem_pet'){
                        $balance_item_txns = $userTransaction->robux_num;
                        $userTransaction->robux_num = $userTransaction->robux_num + $amount;
                        $userTransaction->save();
                    }

                    //tạo tnxs vp
                    $txns = TxnsVp::create([
                        'trade_type' => config('module.txnsvp.trade_type.refund'),
                        'is_add' => '1',
                        'user_id' => $userTransaction->id,
                        'amount' => $amount,
                        'last_balance' => $balance_item_txns + $amount,
                        'description' => "Hoàn tiền mất item dịch vụ ".$order->idkey." rút vật phẩm thất bại gói rút" . $order->ref_id . " - "."dịch vụ ".$order->sticky." #".$order->id ,
                        'ref_id' => $order->id,
                        'status' => 1,
                        'shop_id' => $order->shop_id,
                        'order_id' => $order->id,
                        'item_type' => $type
                    ]);
                }
            }


            $data->save();
            DB::commit();
            return 'Xử lý giao dịch thành công #' . $request->request_id;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return "Lỗi:".$e->getMessage();
        }

    }

    //List Callback ROBLOX call về
    public function getCallbackBotRoblox(Request $request)
    {

        //Lưu cache.

        Cache::put('CHECK_TOOL_GAME_ROBLOX',true,now()->addMinutes(5));
        Cache::put('CHECK_TIME_GAME_ROBLOX', 5);
        //lưu log gọi curl
        $path = storage_path() ."/logs/services-auto/";
        $filename=$path."listen_callback_bot_roblox".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }

        if ($request->get('image')){
            $data = new \stdClass();
            $data->secretkey = $request->get('secretkey');
            $data->request_id = $request->get('request_id');
            $data->status = $request->get('status');
            $data->message = $request->get('message');
            $data->image = "Có ảnh gửi sang";

            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($data);
            \File::append($filename,$contentText."\n");
        }else{
            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
            \File::append($filename,$contentText."\n");
        }

        if ($request->get('secretkey') != config('roblox_bot.sign')) {
            return "Không đúng mã bí mật. Xin thử lại ";
        }


        return $this->handleRobloxBot($request);
    }

    public function handleRobloxBot(Request $request)
    {

        DB::beginTransaction();
        try {

            $order = Order::where('status',1)
                ->where(function($q){
                    $q->orWhere('module', '=',config('module.minigame.module.withdraw-service-item'));
                    $q->orWhere('module', '=',config('module.service-purchase.key'));
                })
                ->where('request_id',$request->get('request_id'))
                ->lockForUpdate()
                ->first();

            if(!$order){
                return "Đơn hàng đã xử lý trước đó";
            }

            $data = Roblox_Order::query()->with('order')->where('order_id',$order->id)->where('status','chuanhan')->lockForUpdate()->first();

            if(!$data){
                return "Không tìm thấy đơn hàng Roblox Order";
            }

            $module = config('module.service-workflow.key');

            if ($order->module == config('module.minigame.module.withdraw-service-item')){
                $module = config('module.withdraw-service-workflow.key');
            }

            if ($request->filled('status')){
                if($request->get('status')== 1 ){
                    //cập nhật trạng thái của purchase
                    $order->status = 4;
                    $order->price_input = $request->price??'';

                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => $module,
                        'status' => 4,
                        'content' => "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!",
                    ]);

                    //tiến độ lưu ảnh.
                    if ($request->get('image')){

                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => $module,
                            'status' => 85,
                            'content' => $request->get('image'),

                        ]);
                    }

                    $data->status = 'danhan';
                    $data->save();

                }
                else if($request->get('status') == 0 ){

                    $order->status = 5;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => $module,
                        'status' => 5,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'dahuybo';
                    $data->save();
                    //tìm user nạp
                    $userTransaction = User::where('id', $order->author_id)->lockForUpdate()->firstOrFail();

                    if ($order->module == config('module.service-purchase.key')){

                        //hoàn tiền cho user
                        $userTransaction->balance = $userTransaction->balance + $order->price;
                        $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $order->price;
                        $userTransaction->save();

                        $order->txns()->create([
                            'trade_type' => 'refund', //Hoàn tiền
                            'user_id' => $userTransaction->id,
                            'is_add' => '1',//Cộng tiền
                            'amount' => $order->price,
                            'real_received_amount' => $order->price,
                            'last_balance' => $userTransaction->balance,
                            'description' => "Hoàn tiền đơn hàng dịch vụ #" . $order->id,
                            'ip' => $request->getClientIp(),
                            'status' => 1,
                            'shop_id' => $order->shop_id,
                        ]);
                    }
                    elseif ($order->module == config('module.minigame.module.withdraw-service-item')){

                        $type = $order->payment_type;
                        $amount = $order->price;
                        $balance_item_txns = $userTransaction->robux_num;
                        $userTransaction->robux_num = $userTransaction->robux_num + $amount;
                        $userTransaction->save();

                        //tạo tnxs vp
                        $txns = TxnsVp::create([
                            'trade_type' => config('module.txnsvp.trade_type.refund'),
                            'is_add' => '1',
                            'user_id' => $userTransaction->id,
                            'amount' => $amount,
                            'last_balance' => $balance_item_txns + $amount,
                            'description' => "Hoàn tiền mất item dịch vụ ".$order->idkey." rút vật phẩm thất bại gói rút" . $order->ref_id . " - "."dịch vụ ".$order->sticky." #".$order->id ,
                            'ref_id' => $order->id,
                            'status' => 1,
                            'shop_id' => $order->shop_id,
                            'order_id' => $order->id,
                            'item_type' => $type
                        ]);
                    }
                }
                else if ($request->get('status') == 2){

                    $message="[" . Carbon::now() . "] Lỗi trong quá trình giao dịch: ".$request->get('message');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => $module,
                        'status' => 9,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();

                }
                else if ($request->get('status') == 3){

                    $order->status = 89;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => $module,
                        'status' => 89,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();

                    $message="[" . Carbon::now() . "] Bot roblox không đủ số dư. Vui lòng nạp thêm tiền" ;
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

//                    if(!Cache::has('WRONG_PACKAGE_ROBLOX_NOT_MONEY')){
//
//                        Cache::put('WRONG_PACKAGE_ROBLOX_NOT_MONEY',true,now()->addMinutes(2));
//
//                    }
                }
                else{
                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => $module,
                        'status' => 9,
                        'content' => 'Không có trạng thái trả về',
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();
                }
            }
            else{
                $order->status = 9;
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => $module,
                    'status' => 9,
                    'content' => 'Không có trạng thái trả về',
                ]);

                $data->status = 'thaotacthucong';
                $data->save();
            }

            $data->save();
            DB::commit();
            return 'Xử lý giao dịch thành công #' . $request->request_id;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return "Lỗi:".$e->getMessage();
        }

    }

}
