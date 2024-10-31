<?php

namespace App\Console\Commands;

use App;
use App\Library\Helpers;
use App\Library\HelpItemAdd;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ServiceAccess;
use App\Models\Shop;
use App\Models\Txns;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Log;

class CompleteManualServiceOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'completeManualServiceOrders:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try{

//            $path = storage_path() ."/logs/service-auto-completed/";
//            if(!\File::exists($path)){
//                \File::makeDirectory($path, $mode = "0755", true, true);
//            }
//            $txt = Carbon::now().":chạy cronjob: chạy job thành công";
//            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

            $hour = config('module.service-purchase.minute_order_cron.complete');
            $minutes = config('module.service-purchase.minute_order_cron.complete')*60;

            //Danh sách đơn hàng đang chờ đối soát có thời gian quá 3 ngày
            $orders = Order::query()
                ->where('status', 10)
                ->where('gate_id',0)
                ->where('type_refund',1)
                ->where('module', '=', config('module.service-purchase.key'))
                ->whereNull('type_version')
                ->whereNotNull('idkey')
                ->with('item_ref','author')
                ->where('process_at','<=', Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->subMinutes($minutes)->format('d/m/Y H:i:s')))
                ->get();

            if (isset($orders) && count($orders)){
                foreach ($orders as $orderid){
                    DB::beginTransaction();

                    $order = Order::query()
                        ->where('gate_id',0)
                        ->whereNotNull('idkey')
                        ->where('module', '=', config('module.service-purchase.key'))
                        ->where('id',$orderid->id)
                        ->where('status', 10)
                        ->with('item_ref','author')
                        ->whereNull('type_version')
                        ->where('process_at','<=', Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->subMinutes($minutes)->format('d/m/Y H:i:s')))
                        ->lockForUpdate()
                        ->first();

                    if (!isset($order)) {

                        $path = storage_path() ."/logs/service-auto-completed/";
                        if(!\File::exists($path)){
                            \File::makeDirectory($path, $mode = "0755", true, true);
                        }
                        $txt = Carbon::now().":chạy cronjob: complete-manual-service-orders không tìm thấy đơn hàng";
                        \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

                        DB::rollBack();
                        continue;
                    }

                    //check nếu là dịch vu auto thì không thể tiếp nhận
                    if ($order->gate_id == "1") {

                        $path = storage_path() ."/logs/service-auto-completed/";
                        if(!\File::exists($path)){
                            \File::makeDirectory($path, $mode = "0755", true, true);
                        }
                        $txt = Carbon::now().":chạy cronjob: complete-manual-service-orders đơn hàng tự động ".$order->id;
                        \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

                        DB::rollBack();
                        continue;
                    }

                    $userTransaction = User::where('id', $order->processor_id)->lockForUpdate()->first();

                    if (!isset($userTransaction)){

                        $path = storage_path() ."/logs/service-auto-completed/";
                        if(!\File::exists($path)){
                            \File::makeDirectory($path, $mode = "0755", true, true);
                        }
                        $txt = Carbon::now().":chạy cronjob: complete-manual-service-orders không tìm thấy ctv ".$order->id;
                        \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

                        DB::rollBack();
                        continue;
                    }

                    if($userTransaction->checkBalanceValid() == false){

                        $path = storage_path() ."/logs/service-auto-completed/";
                        if(!\File::exists($path)){
                            \File::makeDirectory($path, $mode = "0755", true, true);
                        }
                        $txt = Carbon::now().":chạy cronjob: complete-manual-service-orders ctv đơn hàng có giao dịch bất minh ".$order->id;
                        \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

                        DB::rollBack();
                        continue;
                    }

                    //tính chiết khấu cho người bán
                    $ratio = 80;
                    //lấy các quyền được tiếp nhận yêu cầu dịch vụ
                    $service_access = ServiceAccess::query()->with('user')->where('user_id', $userTransaction->id)
                        ->first();

                    if (isset($service_access)){
                        if (isset($service_access->params)){
                            $param = json_decode(isset($service_access->params) ? $service_access->params : "");

                            if(isset($param->{'ratio_' . ($order->item_ref->id??null)})){
                                $ratio= $param->{'ratio_' . ($order->item_ref->id??null)};
                            }
                            else{
                                $ratio=$ratio;
                            }
                        }
                    }

                    //cộng tiền user
                    $total_price_ctv = (float)$ratio*$order->price_ctv;

                    $real_received_amount = $total_price_ctv/100;

                    //Cập nhật trạng thái đơn hàng
                    $order->status = 4;
                    $order->ratio_ctv = $ratio;
                    $order->real_received_price_ctv = $real_received_amount;
                    $order->save();

                    //Cộng tiền cho CYV
                    $userTransaction->balance = $userTransaction->balance + $real_received_amount;
                    $userTransaction->balance_in = $userTransaction->balance_in + $real_received_amount;
                    $userTransaction->save();

                    //set tiến độ hoàn tất
                    OrderDetail::create([
                        'order_id'=>$order->id,
                        'module' => config('module.service-workflow.key'),
                        'title' =>  'Thành công',
                        'status' => 4,
                    ]);

                    //Lưu biến động số dư
                    Txns::create([
                        'trade_type'=>'service_completed', //Thanh toán dịch vụ
                        'user_id'=>$userTransaction->id,
                        'is_add' => '1',//Cộng tiền
                        'amount' => $real_received_amount,
                        'real_received_amount' => $real_received_amount,
                        'last_balance' => $userTransaction->balance,
                        'description' => "Hệ thông tự động hoàn tất đơn hàng sau '.$hour.' giờ #" . $order->id,
                        'order_id' => $order->id,
                        'status' => 1,
                        'shop_id'=>$order->shop_id??''
                    ]);

                    DB::commit();

                    $this->callbackToShop($order,__('Thành công'));

                }
            }

        }catch (\Exception $e) {
            Log::error($e );

            $path = storage_path() ."/logs/service-auto-completed/";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $txt = Carbon::now().":chạy cronjob: log-job-complete-manual-service-orders error: ".$e->getLine()." - ".$e->getMessage();
            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

        }
    }

    public function callbackToShop(Order $order,$message,$refund = null)
    {

        $url = $order->url;

        $data = array();
        $data['status'] = $order->status;
        $data['refund'] = $refund;
        $data['message'] = $message;

        if (strpos($url, 'https://backend-th.tichhop.pro') > -1 || strpos($url, 'http://s-api.backend-th.tichhop.pro') > -1){
            $data['message'] = config('lang.'.$message)??$message;
        }

        $data['input_auto'] = 0;
        if ($order->status == 4){
            $data['price'] = $order->real_received_price_ctv;
        }

        if ($order->status == 4){
            $data['process_at'] = strtotime($order->process_at);
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
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);

                $resultRaw=curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                //debug thì mở cái này
                $myfile = fopen(storage_path() . "/logs/curl_callback-service-to-shop-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
                $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
                fwrite($myfile, $txt);
                fclose($myfile);

                if($httpcode==200){

                    if(strpos($resultRaw, __("Có lỗi phát sinh.Xin vui lòng thử lại")) > -1){
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
