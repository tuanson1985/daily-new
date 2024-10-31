<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Cache;
use Carbon\Carbon;
use App\Models\Shop;
use App\Library\Helpers;

class ExpiredTimeDomain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ExpiredTimeDomain:crom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command daily report expired time domain';

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
        $channel_id = config('telegram.bots.mybot.channel_noty_expired_time_domain');
        try{
            $time = Carbon::now();
            $message = '';
            $message .= "<b>THÔNG BÁO TÊN MIỀN SẮP HẾT HẠN NGÀY ".$time->format('d-m-Y')."</b>";
            $message .= "\n";
            $message .= "\n";
            $check = false;
            $shop_not_config_expired = Shop::whereNull('expired_time')->where('status',1)->count();
            if($shop_not_config_expired > 0){
                $check = true;
                $message .= '* Số tên miền chưa được cấu hình ngày hết hạn: <b>'.$shop_not_config_expired."</b>";
                $message .= "\n";
                $message .= "\n";
            }
            $shop = Shop::where('status',1)->whereNotNull('expired_time')->where('expired_time', '<=',$time->addDay(10))->get();
            if(isset($shop) && count($shop) > 0){
                $check = true;
                $message .= "* Số tên miền sắp sửa hết hạn: <b>".count($shop)."</b>";
                $message .= "\n";
                $message .= "\n";
                foreach($shop as $item){
                    $message .= "<b>- ".$item->domain."</b>";
                    $message .= "\n";
                    $message .= "\n";
                }
            }
            if($check == true){
                $message .= "Vui lòng xử lý thông tin.";
                $message .= "\n";
                $message .= "\n";
                Helpers::TelegramNotify($message,$channel_id);
            }
        }
        catch (\Exception $e) {
            $message = "Đã xảy ra lỗi trong quá trình kiểm tra tên miền hết hạn. ERROR ".$e->getMessage();
            Helpers::TelegramNotify($message,$channel_id);
        }
    }
}
