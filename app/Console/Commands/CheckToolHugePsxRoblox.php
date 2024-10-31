<?php

namespace App\Console\Commands;
use App\Library\Helpers;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Log;

use Illuminate\Console\Command;

class CheckToolHugePsxRoblox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkToolHugePsxRoblox:cron';


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
//            $txt = Carbon::now().":chạy cronjob: chạy log-job-service-auto-check-tool-huge-psx thành công";
//            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

            if(!Cache::has('CHECK_TOOL_GAME_HUGE_PSX_ROBLOX')){

                $message="[" . Carbon::now() . "] Tool HUGE PSX lỗi vui lòng kiểm tra lại! - ".' Thông báo từ '.config('app.url');
                Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_huge_psx_roblox'));

                Cache::put('CHECK_TOOL_GAME_HUGE_PSX_ROBLOX',true,now()->addMinutes(5));
            }

        }catch (\Exception $e) {
            Log::error($e );

            $path = storage_path() ."/logs/service-auto-check-tool/";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $txt = Carbon::now().":chạy cronjob: log-job-service-auto-check-tool-huge-psx error: ".$e->getLine()." - ".$e->getMessage();
            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

        }

    }

}
