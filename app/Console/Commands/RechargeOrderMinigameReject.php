<?php

namespace App\Console\Commands;

use App\Library\Helpers;
use App\Library\HelpItemAdd;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shop;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Log;

class RechargeOrderMinigameReject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rechargeOrderMinigameReject:crom';

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
            $minutes = config('module.minigame.minute_crom_order.recharge')*60;

            if (!isset($minutes) && $minutes <= 0){
                $myfile = fopen(storage_path() ."/logs/log-job-minigame-crom-order-recharge.txt", "a") or die("Unable to open file!");
                $txt = Carbon::now().":chạy cronjob: minigame-crom-order-recharge chưa cấu hình thời gian hủy";
                fwrite($myfile, $txt ."\n");
                fclose($myfile);
                return false;
            }

            $orders = Order::with('author')
                ->with('shop')
                ->where('status',7)
                ->where('paided_at','>', Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->subMinutes($minutes)
                    ->format('d/m/Y H:i:s')))
                ->where('paided_at','<=', Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->subMinutes(1)
                    ->format('d/m/Y H:i:s')))
                ->where('module','withdraw-item')->get();

            if (isset($orders) && count($orders)){
                foreach ($orders as $order){
                    DB::beginTransaction();

                    $order = Order::with('author')
                        ->with('shop')
                        ->where('status',7)
                        ->where('paided_at','>', Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->subMinutes($minutes)
                            ->format('d/m/Y H:i:s')))
                        ->where('paided_at','<=', Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->subMinutes(1)
                            ->format('d/m/Y H:i:s')))
                        ->where('module','withdraw-item')
                        ->where('id',$order->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$order) {
                        $myfile = fopen(storage_path() ."/logs/log-job-minigame-crom-order-recharge.txt", "a") or die("Unable to open file!");
                        $txt = Carbon::now().":chạy cronjob: minigame-crom-order-recharge không tìm thấy order";
                        fwrite($myfile, $txt ."\n");
                        fclose($myfile);
                        DB::rollBack();
                        continue;
                    }

                    //Kiểm tra thời gian gọi lại.

                    $timenow = Carbon::now();

                    if (isset($order->recheck_count) && (int)$order->recheck_count > 0){
                        if ((int)$order->recheck_count <= 4){
                            $minutes = config('module.minigame.fibonacci_recheck_time_order.'.$order->recheck_count.'');
                        }else{
                            $minutes = config('module.minigame.fibonacci_recheck_time_order.5');
                        }

                        $paided_at = $order->recheck_at;
                        $paided_at = strtotime($paided_at);
                        $timenow = $timenow->subMinutes($minutes);
                        $timenow = strtotime($timenow);

                        if ($timenow < $paided_at){
                            // không được gọi.
                            DB::rollBack();
                            continue;
                        }
                    }

                    $shop= Shop::where('id',$order->shop_id)->first();

                    if (!$shop) {
                        $myfile = fopen(storage_path() ."/logs/log-job-minigame-crom-order-recharge.txt", "a") or die("Unable to open file!");
                        $txt = Carbon::now().":chạy cronjob: minigame-crom-order-recharge không tìm thấy shop truy cập";
                        fwrite($myfile, $txt ."\n");
                        fclose($myfile);
                        DB::rollBack();
                        continue;
                    }

                    if (!isset($order->payment_type)){
                        $myfile = fopen(storage_path() ."/logs/log-job-minigame-crom-order-recharge.txt", "a") or die("Unable to open file!");
                        $txt = Carbon::now().":chạy cronjob: minigame-crom-order-recharge không tìm thấy id loại game";
                        fwrite($myfile, $txt ."\n");
                        fclose($myfile);
                        DB::rollBack();
                        continue;
                    }

                    $type = $order->payment_type;

                    $package_sticky = 1;

                    if (isset($order->sticky)){
                        $package_sticky = $order->sticky;
                    }

                    $payment_gateways = config('module.minigame.payment_gateway.'.$package_sticky);

                    if (!isset($payment_gateways)){
                        $payment_gateways = 'SMS';
                    }

                    $provider = config('module.minigame.game_type_map.'.$type);

                    if (!isset($provider)){
                        $myfile = fopen(storage_path() ."/logs/log-job-minigame-crom-order-recharge.txt", "a") or die("Unable to open file!");
                        $txt = Carbon::now().":chạy cronjob: minigame-crom-order-recharge không tìm thấy loại game";
                        fwrite($myfile, $txt ."\n");
                        fclose($myfile);
                        DB::rollBack();
                        continue;
                    }

                    if ($provider == 'lienquan' || $provider == 'freefire' || $provider == 'lienminh' || $provider == 'bns' ||
                        $provider == 'ads' || $provider == 'fo4m' || $provider == 'fo4' || $provider == 'pubgm' || $provider == 'codm'){
                        if ($provider == "freefire" || $provider == "pubgm") {

                            $id = $order->idkey;
                            $username = "";
                            $password = "";
                            if(empty($id)){
                                $myfile = fopen(storage_path() ."/logs/log-job-minigame-crom-order-recharge.txt", "a") or die("Unable to open file!");
                                $txt = Carbon::now().":chạy cronjob: minigame-crom-order-recharge không tìm thấy thông tin game";
                                fwrite($myfile, $txt ."\n");
                                fclose($myfile);
                                DB::rollBack();
                                continue;
                            }
                        }
                        else {

                            $id = "";
                            $username = $order->idkey;
                            $password = $order->title;
                            if(empty($username) || empty($password)){
                                $myfile = fopen(storage_path() ."/logs/log-job-minigame-crom-order-recharge.txt", "a") or die("Unable to open file!");
                                $txt = Carbon::now().":chạy cronjob: minigame-crom-order-recharge không tìm thấy mật khẩu password game";
                                fwrite($myfile, $txt ."\n");
                                fclose($myfile);
                                DB::rollBack();
                                continue;
                            }
                        }

                        $amount = $order->price;

                        $item = 0;
                        if($provider == "lienminh"){
                            if ($payment_gateways == 'SMS'){
                                if($amount == "16"){
                                    $item = 10;
                                }else if($amount == "32"){
                                    $item = 20;
                                }else if($amount == "84"){
                                    $item = 50;
                                }else if($amount == "168"){
                                    $item = 100;
                                }else if($amount == "340"){
                                    $item = 200;
                                }else if($amount == "856"){
                                    $item = 500;
                                }else{
                                    DB::rollback();
                                    continue;
                                }
                            }else{
                                DB::rollback();
                                continue;
                            }

                        }
                        else if($provider == "lienquan"){
                            if ($payment_gateways == 'SMS'){
                                if($amount == "16"){
                                    $item = 10;
                                }else if($amount == "32"){
                                    $item = 20;
                                }else if($amount == "80"){
                                    $item = 50;
                                }else if($amount == "160"){
                                    $item = 100;
                                }else if($amount == "320"){
                                    $item = 200;
                                }else if($amount == "800"){
                                    $item = 500;
                                }else{
                                    DB::rollback();
                                    continue;
                                }
                            }
                            elseif ($payment_gateways == 'GARENA'){
                                if($amount == "40"){
                                    $item = 20;
                                }else if($amount == "100"){
                                    $item = 50;
                                }else if($amount == "200"){
                                    $item = 100;
                                }else if($amount == "400"){
                                    $item = 200;
                                }else if($amount == "1000"){
                                    $item = 500;
                                }else{
                                    DB::rollback();
                                    continue;
                                }
                            }
                            else{
                                DB::rollback();
                                continue;
                            }

                        }
                        else if($provider == "freefire"){
                            if ($payment_gateways == 'SMS'){
                                if($amount == "40"){
                                    $item = 10;
                                }else if($amount == "88"){
                                    $item = 20;
                                }else if($amount == "220"){
                                    $item = 50;
                                }else if($amount == "440"){
                                    $item = 100;
                                }else if($amount == "880"){
                                    $item = 200;
                                }else if($amount == "2200"){
                                    $item = 500;
                                }
                                else{
                                    DB::rollback();
                                    continue;
                                }
                            }
                            elseif ($payment_gateways == 'GARENA'){
                                if($amount == "110"){
                                    $item = 20;
                                }else if($amount == "275"){
                                    $item = 50;
                                }else if($amount == "550"){
                                    $item = 100;
                                }else if($amount == "1100"){
                                    $item = 200;
                                }else if($amount == "2750"){
                                    $item = 500;
                                }
                                else{
                                    DB::rollback();
                                    continue;
                                }
                            }
                            else{
                                DB::rollback();
                                continue;
                            }

                        }
                        else if($provider == "pubgm"){
                            if ($payment_gateways == 'SMS'){
                                if($amount == "48"){
                                    $item = 20;
                                }else if($amount == "119"){
                                    $item = 50;
                                }else if($amount == "246"){
                                    $item = 100;
                                }else if($amount == "252"){
                                    $item = 200;
                                }else{
                                    DB::rollback();
                                    continue;
                                }
                            }else{
                                DB::rollback();
                                continue;
                            }

                        }
                        else if($provider == "bns"){
                            if ($payment_gateways == 'SMS'){
                                if($amount == "800"){
                                    $item = 10;
                                }else if($amount == "1600"){
                                    $item = 20;
                                }else if($amount == "4000"){
                                    $item = 50;
                                }else if($amount == "8000"){
                                    $item = 100;
                                }else if($amount == "16000"){
                                    $item = 200;
                                }else{
                                    DB::rollback();
                                    continue;
                                }
                            }else{
                                DB::rollback();
                                continue;
                            }

                        }
                        else if($provider == "ads"){
                            if ($payment_gateways == 'SMS'){
                                if($amount == "800"){
                                    $item = 10;
                                }else if($amount == "1600"){
                                    $item = 20;
                                }else if($amount == "4000"){
                                    $item = 50;
                                }else if($amount == "8000"){
                                    $item = 100;
                                }else if($amount == "16000"){
                                    $item = 200;
                                }
                                else{
                                    DB::rollback();
                                    continue;
                                }
                            }
                            else{
                                DB::rollback();
                                continue;
                            }
                        }
                        else if($provider == "fo4m"){
                            if ($payment_gateways == 'SMS'){
                                if($amount == "16"){
                                    $item = 10;
                                }else if($amount == "32"){
                                    $item = 20;
                                }else if($amount == "80"){
                                    $item = 50;
                                }else if($amount == "168"){
                                    $item = 100;
                                }else if($amount == "340"){
                                    $item = 200;
                                }else{
                                    DB::rollback();
                                    continue;
                                }
                            }
                            else{
                                DB::rollback();
                                continue;
                            }
                        }
                        else if($provider == "fo4"){
                            if ($payment_gateways == 'SMS'){
                                if($amount == "16"){
                                    $item = 10;
                                }else if($amount == "32"){
                                    $item = 20;
                                }else if($amount == "80"){
                                    $item = 50;
                                }else if($amount == "168"){
                                    $item = 100;
                                }else if($amount == "340"){
                                    $item = 200;
                                }
                                else{
                                    DB::rollback();
                                    continue;
                                }
                            }
                            else{
                                DB::rollback();
                                continue;
                            }

                        }
                        else{
                            $provider ='';
                        }

                        //Kiểm tra thời gian gọi lại.

                        if (isset($order->recheck_count) && (int)$order->recheck_count > 0){
                            $recheck_count = (int)$order->recheck_count + 1;
                        }else{
                            $recheck_count = 1;
                        }

                        $order->status=0;
                        $order->recheck_count=$recheck_count;
                        $order->save();

                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.minigame.module.withdraw-item'),
                            'status' => 0,
                            'content' => "Hệ thống tự động gọi lại đơn hàng qua NCC",
                        ]);

                        DB::commit();

                        $result = HelpItemAdd::ITEMADD_CALLBACK($provider, $username, $password, $id, $item, "", $order->request_id, $shop->id);

                        if ($result &&  isset($result->status)) {
                            if($result->status==0){
                                // Update lại dữ liệu
                                $order->content = $result->message;
                                $order->save();

                                if (isset($result->user_balance)){
                                    if($result->user_balance<1000000){
                                        $message="[" . Carbon::now() . "] " . $shop->domain . " đã mua bắn kim cương và tài khoản tichhop.net còn dưới 1 triệu (Số dư hiện tại: ".number_format($result->user_balance).")" ;
                                        Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_notify_balance_tichhop_net'));
                                    }
                                }

                                //set tiến độ
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'module' => config('module.minigame.module.withdraw-item'),
                                    'status' => 0,
                                    'content' => "NCC tích hợp đã nhận lại đơn(CR)",
                                ]);

                                // Commit the queries!
                                DB::commit();
                                continue;
                            }
                            elseif ($result->status == 3){

                                if($result->status == -1){
                                    $message="[" . Carbon::now() . "] ".$shop->domain . " đã bắn kim cương và tài khoản tichhop.net còn dưới 1 triệu (Số dư hiện tại: ".number_format($result->user_balance).")" ;
                                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_notify_balance_tichhop_net'));
                                    $message_response="Tài khoản đại lý không đủ số dư";
                                }
                                else{
                                    $message_response=$result->message??__('Kết nối với nhà cung cấp thất bại');
                                    $message="[" . Carbon::now() . "] ".$shop->domain . " đã bắn kim cương trên tichhop.net kết nối thất bại:".$message_response." ";
                                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_notify_balance_tichhop_net'));
                                }

                                // Start transaction!
                                DB::beginTransaction();
                                try {

                                    $order = Order::lockForUpdate()->findOrFail($order->id);

                                    $order->status=7;
                                    $order->recheck_at=Carbon::now();
                                    $order->save();

                                    //set tiến độ hủy
                                    OrderDetail::create([
                                        'order_id' => $order->id,
                                        'module' => config('module.minigame.module.withdraw-item'),
                                        'content' => "CR-".$message_response,
                                        'status' => 7, //Đã hủy
                                    ]);

                                } catch (\Exception $e) {
                                    DB::rollback();
                                    Log::error( $e);
                                    continue;
                                }

                                DB::commit();
                                continue;
                            }
                            else{

                                $order->status=7;
                                $order->recheck_at=Carbon::now();
                                $order->save();

                                //set tiến độ hủy
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'module' => config('module.minigame.module.withdraw-item'),
                                    'content' => "Kết nối lại NCC thất bại (CR7) - ".$result->message??'',
                                    'status' => 7, //Đã hủy
                                ]);

                                DB::commit();
                                continue;
                            }
                        }
                        else{
                            $order->status=9;
                            $order->recheck_at=Carbon::now();
                            $order->save();

                            //set tiến độ hủy
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.minigame.module.withdraw-item'),
                                'content' => "Kết nối lại NCC thất bại (CR9)",
                                'status' => 9, //Đã hủy
                            ]);

                            DB::commit();
                            continue;
                        }

                    }else{
                        DB::rollback();
                        continue;
                    }
                }
            }

        }catch (\Exception $e) {
            Log::error($e );
            $myfile = fopen(storage_path() ."/logs/log-job-minigame-crom-order-recharge.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().":chạy cronjob: minigame-crom-order-recharge error: ".$e->getLine()." - ".$e->getMessage();
            fwrite($myfile, $txt ."\n");
            fclose($myfile);
        }
    }
}
