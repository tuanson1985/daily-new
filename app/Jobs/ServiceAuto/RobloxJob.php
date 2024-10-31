<?php

namespace App\Jobs\ServiceAuto;

use App;
use App\Library\ChargeGameGateway\RobloxGate;
use App\Library\DirectAPI;
use App\Library\Helpers;
use App\Models\OrderDetail;
use App\Models\Roblox_Bot;
use App\Models\Roblox_Order;
use App\Models\Txns;
use App\Models\TxnsVp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Item;
use App\Models\Order;

class RobloxJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $timeout = 3600;
    public $tries = 1;


    public $order_id;
    public function __construct($order_id) {

        $this->order_id=$order_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        // Start transaction!
        DB::beginTransaction();
        try {

            $module = config('module.service-workflow.key');

            $order = Order::where('id',$this->order_id)
                ->with('item_ref')
                ->where('module', '=',config('module.service-purchase.key'))
                ->where('status', 1) //đơn đang chờ
                ->lockForUpdate()
                ->first();

            if(!$order){
                DB::rollBack();
                return "Không tìm thấy đơn chưa nhận";
            }

            $roblox_order = Roblox_Order::where('status',"chuanhan")
                ->where('order_id',$order->id)
                ->where('type_order',3)
                ->lockForUpdate()
                ->first();

            $ver = 1;

            if (isset($roblox_order->ver) && $roblox_order->ver == 2){
                $ver = 2;
            }

            if(!$roblox_order){
                DB::rollBack();
                return "Không tìm thấy đơn chưa nhận";
            }

            //lấy thông tin bot theo shop
            $roblox_bot = Roblox_Bot::where('status',1)
                ->where('type_order',1)
                ->where('coin',">=",$roblox_order->money)
                ->inRandomOrder()
                ->get();

            //kiểm tra có đúng bot ko,hoặc ko đúng bot với đơn
            if(count($roblox_bot)<0){

                DB::rollBack();
                return "Không có bot";

            }

            $order->status=2;
            $order->save();
            DB::commit();

            if (isset($order->payment_type) && in_array($order->payment_type,config('module.service-purchase-auto.rbx_api'))){
                $url = '/orders/gamepass';
                if ($order->idkey == "roblox_buyserver"){
                    $url = '/orders/vip-server';
                }
                $method = "POST";
                $dataSend = array();
                if ($order->idkey == "roblox_buyserver"){
                    $dataSend['robloxUsername'] = $roblox_order->phone??'';
                }else{
                    $dataSend['robloxUsername'] = $roblox_order->uname;
                }

                $dataSend['orderId'] = $order->request_id_customer;
                $dataSend['robuxAmount'] = (int)$roblox_order->money;
                $dataSend['placeId'] = (int)$roblox_order->server??'';
                $dataSend['isPreOrder'] = true;
                if ($order->idkey == "roblox_buygamepass"){

                }
                if ($order->idkey == "roblox_buyserver"){
                    $result_Api = DirectAPI::_buyServer($url,$dataSend,$method,$order->payment_type);
                }else{
                    $result_Api = DirectAPI::_buyGamepass($url,$dataSend,$method,$order->payment_type);
                }

                if (isset($result_Api) && isset($result_Api->status) && $result_Api->status == 1){
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => $module,
                        'content' => "Đã gửi đơn sang RBX API",
                        'status' => 2,
                    ]);
                    $method = "GET";
                    $url_balance = '/shared/balance';
                    $dataBalanceSend = array();
                    $result_balance_Api = DirectAPI::_getBalance($url_balance,$dataBalanceSend,$method);
                    if (isset($result_balance_Api) && isset($result_balance_Api->status) && $result_balance_Api->status == 1){
                        $balance = $result_balance_Api->balance??0;
                        if ($balance <= 5){
                            $message="[" . Carbon::now() . "] " . config('module.service-purchase-auto.rbx_rate.'.$order->payment_type) . " - Số dư tài khoản RBX còn dưới 5$.Vui lòng nạp thêm - Số dư hiện tại: ".$balance ."$";
                            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_rbx_balance_roblox'));
                        }
                    }
                }
                else{

                    $message="[" . Carbon::now() . "] " . "daily.tichhop.pro  - " . "Gửi đơn RBX thất bại (".$order->title. " MÃ ĐƠN #".$order->request_id_customer.") - ";
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_check_cookie_roblox'));

                    $order->status= 9;
                    $order->save();

                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => $module,
                        'content' => "Gửi đơn sang RBX API thất bại - Lý do: ".$result_Api->message??"",
                        'status' => 9,
                    ]);
                }

            }else{
                foreach ($roblox_bot as $aBot) {
                    if($order->idkey == 'roblox_buygamepass'){
                        if ($ver == 2){
                            $result=RobloxGate::ProcessBuyGamePassNew($roblox_order->uname,$roblox_order->money,$aBot->cookies,$order->id,null,$roblox_order->server);
                        }else{
                            $result=RobloxGate::ProcessBuyGamePass($roblox_order->uname,$roblox_order->money,$aBot->cookies,$order->id);
                        }
                    }
                    else{
                        $result=RobloxGate::ProcessBuyServer($roblox_order->server,$roblox_order->money,$aBot->cookies,$order->id);
                    }

                    DB::beginTransaction();
                    try {

                        if (isset($result)){
                            if (isset($result->status)){
                                //Giao dịch thành công
                                if($result->status==1){

                                    $roblox_order->status = "danhan";
                                    $roblox_order->ver = $aBot->ver;
                                    $roblox_order->bot_handle = $aBot->id??'';
                                    $roblox_order->save();
                                    //cập nhật lại số dư cho bot
                                    $aBot->coin=$result->last_balance_bot;
                                    $aBot->save();
                                    //cập nhật trạng thái thành công của đơn
                                    $order->status = 4;
                                    $order->process_at = Carbon::now();//thời gian xác nhận đơn hàng
                                    $order->save();
                                    //set tiến độ

                                    OrderDetail::create([
                                        'order_id' => $order->id,
                                        'module' => $module,
                                        'content' => "Giao dịch thành công",
                                        'status' => 4,
                                    ]);

                                    OrderDetail::create([
                                        'order_id' => $order->id,
                                        'module' => 'pengiriman',
                                        'title' => $aBot->id_pengiriman??'',
                                        'description' => $aBot->acc??'',
                                        'content' => "Nhập hàng",
                                        'status' => 4,
                                    ]);

                                    DB::commit();

                                    if($result->last_balance_bot<1000){
                                        $message="[" . Carbon::now() . "] " . "daily.dichvu.me  - " . " Số dư bot roblox còn dưới 1.000.Vui lòng chuẩn bị thay bot - Số dư hiện tại: ".$result->last_balance_bot." - (". "ID bot #".$aBot->id."  )" ;
                                        Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_balance_roblox'));
                                    }

                                    if($order->url!=""){
                                        $this->callbackToShop($order,__("Giao dịch thành công"));
                                    }

                                    return "Giao dịch thành công giao dịch #".$order->id ." - Request ID:".$order->request_id_customer;
                                }
                                //Bot hết cookie hoặc ko hoạt động
                                elseif($result->status==2){

                                    $aBot->status=2;
                                    $aBot->save();

                                    $roblox_order->ver = $aBot->ver;
                                    $roblox_order->bot_handle = $aBot->id??'';
                                    $roblox_order->save();

                                    DB::commit();

                                    $message="[" . Carbon::now() . "] " . "daily.dichvu.me  - " . "DIE COOKIE 1 (".$order->title. " MÃ ĐƠN #".$order->request_id_customer.") - "." BOT: - #ID: ".$aBot->id." - TÀI KHOẢN: - ".$aBot->acc.' LÝ DO: '.$result->message;
                                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_check_cookie_roblox'));

                                    continue;

                                }
                                //bot hết số dư
                                elseif($result->status==33){
                                    $aBot->status=3;
                                    $aBot->save();

                                    $roblox_order->ver = $aBot->ver;
                                    $roblox_order->bot_handle = $aBot->id??'';
                                    $roblox_order->save();

                                    DB::commit();
                                    continue;
                                }
                                //Hoàn tiền theo status = 0
                                elseif($result->status==0){

                                    $roblox_order->status = "dahoantien";
                                    $roblox_order->save();
                                    //cập nhật trạng thái thất bại của đơn
                                    $order->status = 5;
                                    $order->save();

                                    $roblox_order->ver = $aBot->ver;
                                    $roblox_order->bot_handle = $aBot->id??'';
                                    $roblox_order->save();

                                    if (isset($result->message) && $result->message != '') {
                                        if (strpos($result->message, "The amount is invalid") > -1) {
                                            $result->message = __("Group đã hết roblox");
                                        }
                                    }else{
                                        $result->message= __("Giao dịch thất bại - không có mesage chả về");
                                    }

                                    //set tiến độ
                                    OrderDetail::create([
                                        'order_id' => $order->id,
                                        'module' => $module,
                                        'content' =>  $result->message??''." : ".$roblox_order->money,
                                        'status' => 5,

                                    ]);

                                    //refund
                                    $userTransaction = User::where('id',$order->author_id)->lockForUpdate()->firstOrFail();

                                    if($order->price<=0){
                                        DB::rollBack();
                                        return "Giao dịch thất bại.Số tiền giao dịch không phù hợp";
                                    }

                                    $userTransaction->balance = $userTransaction->balance + $order->price;
                                    $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $order->price;
                                    $userTransaction->save();

                                    Txns::create([
                                        'trade_type' => 'refund', //Hoàn tiền dịch vụ
                                        'user_id' => $userTransaction->id,
                                        'is_add' => '1',//Công tiền
                                        'amount' => $order->price,
                                        'real_received_amount' => $order->price,
                                        'last_balance' => $userTransaction->balance,
                                        'description' => "Hoàn tiền thanh toán thất bại dịch vụ " . $order->title . " #".$order->id ,
                                        'order_id' => $order->id,
                                        'status' => 1
                                    ]);

                                    DB::commit();
                                    if($order->url!=""){
                                        $this->callbackToShop($order,$result->message??"");
                                    }

                                    return $result->message??"";
                                }
                                //chờ xử lý thủ công
                                elseif($result->status==999){

                                    $order->status = 9;
                                    $order->save();

                                    //set tiến độ
                                    OrderDetail::create([
                                        'order_id' => $order->id,
                                        'module' => $module,
                                        'content' => "Giao dịch chờ kiểm tra thủ công",
                                        'status' => 9,
                                    ]);

                                    $roblox_order->ver = $aBot->ver;
                                    $roblox_order->bot_handle = $aBot->id??'';
                                    $roblox_order->save();

                                    DB::commit();


                                    $message="[" . Carbon::now() . "] " . "daily.dichvu.me  - " . "Giao dịch thất bại cần check thủ công (".$order->title. " Mã đơn #".$order->id.")" ;
                                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_check_cookie_roblox'));

                                    return "Giao dịch thất bại cần check thủ công (".$result->message.")";
                                }
                                else{

                                    $aBot->status=2;
                                    $aBot->save();

                                    $roblox_order->ver = $aBot->ver;
                                    $roblox_order->bot_handle = $aBot->id??'';
                                    $roblox_order->save();

                                    Log::error("Trạng thái không hợp lệ");
                                    DB::commit();

                                    $message="[" . Carbon::now() . "] " . "daily.dichvu.me  - " . "DIE COOKIE 2 (".$order->title. " MÃ ĐƠN #".$order->request_id_customer.") - "." BOT: - #ID: ".$aBot->id." - TÀI KHOẢN: - ".$aBot->acc.' LÝ DO: Trạng thái trả về không hợp lệ';
                                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_check_cookie_roblox'));

                                    continue;
                                }
                            }else{
                                $aBot->status=2;
                                $aBot->save();

                                $roblox_order->ver = $aBot->ver;
                                $roblox_order->bot_handle = $aBot->id??'';
                                $roblox_order->save();

                                $message="[" . Carbon::now() . "] " . "daily.dichvu.me  - " . "DIE COOKIE 3 (".$order->title. " MÃ ĐƠN #".$order->request_id_customer.") - "." BOT: - #ID: ".$aBot->id." - TÀI KHOẢN: - ".$aBot->acc.' LÝ DO: Không có trạng thái trả về';
                                Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_check_cookie_roblox'));

                                Log::error("Không có status trả về");
                                DB::commit();
                                continue;
                            }
                        }else{
                            $aBot->status=2;
                            $aBot->save();

                            $roblox_order->ver = $aBot->ver;
                            $roblox_order->bot_handle = $aBot->id??'';
                            $roblox_order->save();

                            $message="[" . Carbon::now() . "] " . "daily.dichvu.me  - " . "DIE COOKIE 4 (".$order->title. " MÃ ĐƠN #".$order->request_id_customer.") - "." BOT: - #ID: ".$aBot->id." - TÀI KHOẢN: - ".$aBot->acc.' LÝ DO: Không có result trả về';
                            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_check_cookie_roblox'));

                            Log::error("Không có result trả về");

                            DB::commit();
                            continue;
                        }

                    }
                    catch (\Exception $e) {
                        DB::rollback();
                        Log::error($e);
                        return "Lỗi bán roblox:".$e->getMessage();
                    }
                }

                $order->status = 7;
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => $module,
                    'content' => "Bot không đủ số dư để giao dịch",
                    'status' => 7,
                ]);

                if(!(\Cache::has("cache_roblox_bot"))){
                    $message="[" . Carbon::now() . "] " . "daily.dichvu.me  - " . " Bot không đủ số dư để giao dịch (".$order->title.")" ;
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_balance_roblox'));
                    \Cache::put("cache_roblox_bot" ,true,5);
                    return "Bot không đủ số dư để giao dịch";

                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return "Lỗi bán roblox:".$e->getMessage();
        }
    }

    public function failed(Exception $exception)
    {
        Log::error($exception);
        echo "CurlJob lỗi";
    }



    function callbackToShop(Order $dataPurchase,$messageBot)
    {

        $url=$dataPurchase->url;

        $data = array();

        $data['status'] = $dataPurchase->status;

        $data['message'] = $messageBot;

        if (strpos($url, 'https://backend-th.tichhop.pro') > -1 || strpos($url, 'http://s-api.backend-th.tichhop.pro') > -1){
            $data['message'] = config('lang.'.$messageBot)??$messageBot;
        }

        $data['price'] = $dataPurchase->price;
        $data['price_base'] = $dataPurchase->price_base;
        $data['input_auto'] = 1;

        if ($dataPurchase->status == 4){
            $data['process_at'] = strtotime($dataPurchase->process_at);
        }

        $dataPost = http_build_query($data);

        try{

            for ($i=0;$i<3;$i++){
                $ch = curl_init();

                //data dạng get
                if (strpos($url, '?') !== FALSE) {
                    $url = $url . "&" . $dataPost;
                } else {
                    $url = $url . "?" . $dataPost;
                }

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                $resultRaw=curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                //debug thì mở cái này
//                $myfile = fopen(storage_path() . "/logs/curl_callback-service-to-shop-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
//                $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
//                fwrite($myfile, $txt);
//                fclose($myfile);

                if($httpcode==200){
                    if(strpos($resultRaw, __("Có lỗi phát sinh.Xin vui lòng thử lại.")) > -1){
                        continue;
                    }
                    break;
                }
            }
        }
        catch (\Exception $e){
            \Log::error($e);
        }

    }
}

