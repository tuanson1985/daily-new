<?php

namespace App\Console\Commands;
use App\Library\Helpers;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Log;

use Illuminate\Console\Command;

class CheckBalanceAllUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckBalanceAllUser:cron';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check so du tai khoan toan bo user';

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

            $listUserInvalid=User::with('shop')->whereRaw('IF(balance_in - balance_out + balance_in_refund - balance != 0, 1, 0) = 1')->get();

            foreach ($listUserInvalid as $user) {
                if($user->account_type==2){
                    $mesage = "[" . Carbon::now() . "] "."Hệ thống check: Thành viên "."<b>".$user->username."</b>"." - của shop ".$user->shop->domain." biến động số dư bị chênh lệch, vui lòng kiểm tra lại. Số tiền vào: ".currency_format($user->balance_in).". - Số tiền chi tiêu: ".currency_format($user->balance_out).". - Số tiền hoàn: ".currency_format($user->balance_in_refund).". - Số dư hiện tại: ".currency_format($user->balance).". - Chênh lệch: ".currency_format($user->balance_in - $user->balance_out + $user->balance_in_refund - $user->balance)." VNĐ";
                }
                else{
                    $mesage = "[" . Carbon::now() . "] "."Hệ thống check: Quản trị viên (Nội bộ) / CTV: "."<b>".$user->username."</b>"." biến động số dư bị chênh lệch, vui lòng kiểm tra lại. Số tiền vào: ".currency_format($user->balance_in).". - Số tiền chi tiêu: ".currency_format($user->balance_out).". - Số tiền hoàn: ".currency_format($user->balance_in_refund).". - Số dư hiện tại: ".currency_format($user->balance).". - Chênh lệch: ".currency_format($user->balance_in - $user->balance_out + $user->balance_in_refund - $user->balance)." VNĐ";
                }
                if(!Cache::has('CheckInvalid'.$user->username)){
                    Cache::put('CheckInvalid'.$user->username,true,now()->addMinutes(5));
                    Helpers::TelegramNotify($mesage,config('telegram.bots.mybot.channel_notify_check_balance_user'));
                }


            }


        }catch (\Exception $e) {
            Log::error($e);
        }
    }


}
