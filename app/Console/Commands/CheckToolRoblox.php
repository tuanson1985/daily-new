<?php

namespace App\Console\Commands;
use App\Library\Helpers;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Log;

use Illuminate\Console\Command;

class CheckToolRoblox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkToolRoblox:cron';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check hoạt động tool roblox';

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

//            $path = storage_path() ."/logs/service-auto-check-tool/";
//            if(!\File::exists($path)){
//                \File::makeDirectory($path, $mode = "0755", true, true);
//            }
//            $txt = Carbon::now().":chạy cronjob: chạy log-job-service-auto-check-tool thành công";
//            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

            if(!Cache::has('CHECK_TOOL_GAME_PET_ROBLOX')){

                $message="[" . Carbon::now() . "] Tool lỗi vui lòng kiểm tra lại! - ".' Thông báo từ '.config('app.url');
                Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                Cache::put('CHECK_TOOL_GAME_PET_ROBLOX',true,now()->addMinutes(5));
            }

//            $path = storage_path() ."/logs/service-auto-check-tool/";
//            if(!\File::exists($path)){
//                \File::makeDirectory($path, $mode = "0755", true, true);
//            }
//
//            $minutes = 5;
//
//            $txt = Carbon::now().":chạy cronjob: chạy log-job-service-auto-check-tool thành công";
//            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");
//
//            $count = Order::query()
//                ->where('status', 1)
//                ->where('idkey','roblox_gem_pet')
//                ->where('module', '=', config('module.service-purchase.key'))
//                ->where('created_at','<=', Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->subMinutes($minutes)->format('d/m/Y H:i:s')))
//                ->count();
//
//            if ((int)$count > 0){
//                $message="[" . Carbon::now() . "] Tool lỗi vui lòng kiểm tra lại! - hiện tại có ".$count.' đơn hàng chờ xử lý,Thông báo từ '.config('app.url');
//                Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
//            }

        }catch (\Exception $e) {
            Log::error($e );

            $path = storage_path() ."/logs/service-auto-check-tool/";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $txt = Carbon::now().":chạy cronjob: log-job-service-auto-check-tool error: ".$e->getLine()." - ".$e->getMessage();
            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

        }

    }

}
