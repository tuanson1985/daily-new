<?php

namespace App\Jobs\ServiceAuto;

use App\Library\ChargeGameGateway\RobloxGate;
use App\Library\Helpers;
use App\Models\Roblox_Bot;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class DefragmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $timeout = 3600;
    public $tries = 1;


    public $bot_id;
    public function __construct($bot_id) {
        $this->bot_id=$bot_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        // Start transaction!
        try {


            $bot_id = $this->bot_id;
            $bot = Roblox_Bot::query()
                ->where('status',1)
                ->where('id',$bot_id)
                ->where('coin','<=',100)->first();

            // Tạo số ngẫu nhiên từ 1 đến 1000
            $randomNumber = rand(1, 1000);

            // Lấy thời gian hiện tại theo dấu thời gian
            $currentTimestamp = time(); // Được tính bằng giây
            $order_id = $randomNumber.$currentTimestamp;

            $uname = config('roblox_bot.nick');

            if ((int)$bot->coin >= 10){
                $coin = (int)(round($bot->coin / 10) * 10);
            }else{
                $coin = (int)$bot->coin??0;
            }

            $result = RobloxGate::ProcessBuyGamePassNewJob($uname,$coin,$bot->cookies,$order_id);
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
                            $bot->save();
                            DB::commit();
                            if ($bot->coin <= 0){
                                $message = '';
                                $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                                $message .= "\n";
                                $message .= "<b>Dồn robux tài khoản : ".$bot->acc." thành công</b>";
                                $message .= "\n";

                                Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_defragment_roblox'));
                            }
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
                    $message .= "<b>Lý do : IE COOKIE</b>";
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

}

