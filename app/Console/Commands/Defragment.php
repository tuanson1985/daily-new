<?php

namespace App\Console\Commands;
use App\Jobs\DeleteImageNickCompleteS3Job;
use App\Jobs\ServiceAuto\DefragmentJob;
use App\Library\ChargeGameGateway\RobloxGate;
use App\Library\Helpers;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Roblox_Bot;
use App\Models\Txns;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Cache;
use Log;

use Illuminate\Console\Command;

class Defragment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'defragment:cron';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'dồn nick';

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
        $path = storage_path() ."/logs/defragment/";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $txt = Carbon::now().":chạy cronjob: log-job-defragment chạy job defragment thành công";
        \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

        $bot = Roblox_Bot::query()
            ->where('status',1)
            ->where('coin','<=',100)->first();

        if (!isset($bot)){
            $path = storage_path() ."/logs/defragment/";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $txt = Carbon::now().":chạy cronjob: log-job-defragment error không tìm thấy bot cần xử lý";
            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

            DB::rollBack();
            return  "không tìm thấy bot cần xử lý";
        }
        $last_coin = (int)$bot->coin??0;
        if ((int)$bot->coin >= 10) {
            $coin = (int)(floor((int)$bot->coin / 10) * 10);
        } else {
            $coin = (int)($bot->coin ?? 0);
        }

        if ($coin < 1){
            $bot->status = 0;
            $bot->save();
            $id_pengiriman = $bot->id_pengiriman??'N/A';
            $account = $bot->acc??"N/A";
            $message = '';
            $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
            $message .= "\n";
            $message .= "<b>Dồn robux tài khoản : ".$account." thành công</b>";
            $message .= "\n";
            $message .= "<b>ID đơn hàng : ".$id_pengiriman." </b>";
            $message .= "\n";
            $message .= "<b>Số robux ban đầu: 0 </b>";
            $message .= "\n";
            $message .= "<b>Số robux dồn: 0 </b>";
            $message .= "\n";
            Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_defragment_roblox'));
            return  "Dồn robux tài khoản thành công";
        }

        // Tạo số ngẫu nhiên từ 1 đến 1000
        $randomNumber = rand(1, 1000);

        // Lấy thời gian hiện tại theo dấu thời gian
        $currentTimestamp = time(); // Được tính bằng giây
        $order_id = $randomNumber.$currentTimestamp;

        $uname = config('roblox_bot.nick');

        $cookies = $bot->cookies;

        // Tạo số ngẫu nhiên từ 1 đến 1000
        $randomNumber = rand(1, 1000);

        // Lấy thời gian hiện tại theo dấu thời gian
        $currentTimestamp = time(); // Được tính bằng giây
        $order_id = $randomNumber.$currentTimestamp;

        $result = RobloxGate::ProcessBuyGamePassNewJob($uname,$coin,$cookies,$order_id);
        DB::beginTransaction();
        try {

            if (isset($result)){
                if (isset($result->status)){
                    //Giao dịch thành công
                    if($result->status==1){
                        //cập nhật lại số dư cho bot
                        $bot->coin = (int)$result->last_balance_bot;
                        if ((int)$result->last_balance_bot < 1){
                            $bot->status = 0;
                        }

                        if (isset($bot->params)){
                            $params = json_decode($bot->params);
                            if (isset($params->defragment) && count($params->defragment)){
                                $defragment = $params->defragment;
                                $time = Carbon::now();
                                $dataReturn = new \stdClass();
                                $dataReturn->time = $time;
                                $dataReturn->last_coin = $last_coin;
                                $dataReturn->coin = $coin;
                                $dataReturn->username = $uname;
                                array_push($defragment,$dataReturn);
                                $params->defragment = $defragment;
                                $bot->params = json_encode($params);
                            }
                            else{
                                $time = Carbon::now();
                                $defragment = [];
                                $dataReturn = new \stdClass();
                                $dataReturn->time = $time;
                                $dataReturn->last_coin = $last_coin;
                                $dataReturn->coin = $coin;
                                $dataReturn->username = $uname;
                                array_push($defragment,$dataReturn);
                                $params->defragment = $defragment;
                                $bot->params = json_encode($params);
                            }
                        }
                        else{
                            $time = Carbon::now();
                            $defragment = [];
                            $dataReturn = new \stdClass();
                            $dataReturn->time = $time;
                            $dataReturn->last_coin = $last_coin;
                            $dataReturn->coin = $coin;
                            $dataReturn->username = $uname;
                            array_push($defragment,$dataReturn);
                            $params = new \stdClass();
                            $params->defragment = $defragment;
                            $bot->params = json_encode($params);
                        }

                        $bot->save();
                        DB::commit();

                        $id_pengiriman = $bot->id_pengiriman??'N/A';
                        $account = $bot->acc??"N/A";
                        $message = '';
                        $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message .= "\n";
                        $message .= "<b>Dồn robux tài khoản : ".$account." thành công</b>";
                        $message .= "\n";
                        $message .= "<b>ID đơn hàng : ".$id_pengiriman." </b>";
                        $message .= "\n";
                        $message .= "<b>Số robux ban đầu: ".$last_coin." </b>";
                        $message .= "\n";
                        $message .= "<b>Số robux dồn: ".$coin." </b>";
                        $message .= "\n";

                        Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_defragment_roblox'));
                        return "Lý do : giao dịch thành công";
                    }
                    //Bot hết cookie hoặc ko hoạt động
                    elseif($result->status==2){
                        $bot->status=2;
                        $bot->save();
                        DB::commit();
                        $message = '';
                        $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message .= "\n";
                        $message .= "<b>Dồn robux tài khoản : ".$bot->acc." thất bại</b>";
                        $message .= "\n";
                        $message .= "<b>Lý do : ".$result->message."</b>";
                        $message .= "\n";
                        Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_defragment_roblox'));
                        return "Lý do : giao dịch thất bại";

                    }
                    //bot hết số dư
                    elseif($result->status==33){
                        $bot->status=3;
                        $bot->save();

                        DB::commit();
                        $message = '';
                        $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message .= "\n";
                        $message .= "<b>Dồn robux tài khoản : ".$bot->acc." thất bại</b>";
                        $message .= "\n";
                        $message .= "<b>Lý do : Bot hết số dư</b>";
                        $message .= "\n";
                        Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_defragment_roblox'));
                        return "Lý do : giao dịch thất bại";
                    }
                    //Hoàn tiền theo status = 0
                    elseif($result->status==0){
                        DB::commit();
                        $message = '';
                        $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message .= "\n";
                        $message .= "<b>Dồn robux tài khoản : ".$bot->acc." thất bại</b>";
                        $message .= "\n";
                        $message .= "<b>Lý do : giao dịch thất bại</b>".$result->message??"";
                        $message .= "\n";
                        Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_defragment_roblox'));
                        return "Lý do : giao dịch thất bại";
                    }
                    //chờ xử lý thủ công
                    elseif($result->status==999){
                        DB::commit();
                        $message = '';
                        $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message .= "\n";
                        $message .= "<b>Dồn robux tài khoản : ".$bot->acc." thất bại</b>".$result->message??"";
                        $message .= "\n";
                        $message .= "<b>Lý do :GIAO DỊCH THỦ CÔNG</b>";
                        $message .= "\n";
                        Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_defragment_roblox'));
                        return "Lý do : GIAO DỊCH THỦ CÔNG";
                    }
                    else{
                        $bot->status=2;
                        $bot->save();
                        DB::commit();
                        $message = '';
                        $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message .= "\n";
                        $message .= "<b>Dồn robux tài khoản : ".$bot->acc." thất bại</b>".$result->message??"";
                        $message .= "\n";
                        $message .= "<b>Lý do : DIE COOKIE</b>";
                        $message .= "\n";
                        Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_defragment_roblox'));

                        return "Lý do : DIE COOKIE";
                    }
                }else{
                    $bot->status=2;
                    $bot->save();
                    DB::commit();
                    $message = '';
                    $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                    $message .= "\n";
                    $message .= "<b>Dồn robux tài khoản : ".$bot->acc." thất bại</b>";
                    $message .= "\n";
                    $message .= "<b>Lý do : DIE COOKIE</b>";
                    $message .= "\n";
                    Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_defragment_roblox'));
                    DB::commit();
                    return "Lý do : DIE COOKIE";
                }
            }
            else{
                $bot->status=2;
                $bot->save();
                DB::commit();
                $message = '';
                $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                $message .= "\n";
                $message .= "<b>Dồn robux tài khoản : ".$bot->acc." thất bại</b>";
                $message .= "\n";
                $message .= "<b>Lý do : DIE COOKIE</b>";
                $message .= "\n";
                Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_defragment_roblox'));
                Log::error("Không có result trả về");
                DB::commit();

                return "Không có result trả về";
            }

        }
        catch (\Exception $e) {
            Log::error($e );
            DB::rollBack();
            $path = storage_path() ."/logs/defragment/";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $txt = Carbon::now().":chạy cronjob: log-job-defragment error: ".$e->getLine()." - ".$e->getMessage();
            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");
            return "Lôixxxxxxxxxxx";
        }

        return  "Chạy job thành công";
    }

}
