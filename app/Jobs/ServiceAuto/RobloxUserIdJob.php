<?php

namespace App\Jobs\ServiceAuto;

use App;
use App\Library\ChargeGameGateway\RobloxGate;
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

class RobloxUserIdJob implements ShouldQueue
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

            $order = Order::where('id',$this->order_id)
                ->with('item_ref')
                ->where('module', '=',config('module.service-purchase.key'))
                ->where('status', 7) //đơn kết nối nhà cung cấp thất bại
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

            if(!$roblox_order){
                DB::rollBack();
                return "Không tìm thấy đơn chưa nhận";
            }

            //lấy thông tin bot theo shop
            $aBot = Roblox_Bot::where('status',6)
                ->where('account_type',1)
                ->orderBy('ver','asc')
                ->first();

            //kiểm tra có đúng bot ko,hoặc ko đúng bot với đơn
            if(!isset($aBot)){

                DB::rollBack();
                return "Không có bot";

            }

            $result = RobloxGate::detectUserIdRoblox($roblox_order->uname,null,$aBot->cookies??'');

            if($result &&  $result->status==1){

                $roblox_order->server = $result->user_id;
                $roblox_order->save();

                DB::commit();
                return "Lấy user id thành công";
            }
            else{

                DB::rollBack();
                return "Không thấy user id";

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
                $myfile = fopen(storage_path() . "/logs/curl_callback-service-to-shop-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
                $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
                fwrite($myfile, $txt);
                fclose($myfile);

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
