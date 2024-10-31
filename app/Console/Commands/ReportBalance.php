<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Log;
use Cache;
use App\Models\UserBalance;
use App\Models\Shop;
use App\Models\User;
class ReportBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ReportBalance:crom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command daily report data balance ctv and shop';

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
            $shop = Shop::orderBy('id','asc')->get();
            if(isset($shop) && count($shop) > 0){
                foreach($shop as $item){
                    $balance = null;
                    $check_data = UserBalance::whereDate('created_at', Carbon::today())->where('title',$item->domain)->where('type','shop_balance')->first();
                    if(!$check_data){
                        $balance = User::where('shop_id',$item->id)->where('account_type',2)->sum('balance');
                        UserBalance::create([
                            'title' => $item->domain,
                            'balance' => $balance,
                            'type' => 'shop_balance'
                        ]);
                    }
                    continue;
                }
            }
            $ctv = User::where('account_type',3)->get();
            if(isset($ctv) && count($ctv) > 0){
                foreach($ctv as $item){
                    $check_data_ctv = UserBalance::whereDate('created_at', Carbon::today())->where('title',$item->username)->where('type','ctv_balance')->first();
                    if(!$check_data_ctv){
                        UserBalance::create([
                            'title' => $item->username,
                            'balance' => $item->balance,
                            'type' => 'ctv_balance'
                        ]);
                    }
                    continue;
                }
            }
            $myfile = fopen(storage_path() ."/logs/log-report-balance.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().":chạy cronjob:  update balance ok";
            fwrite($myfile, $txt ."\n");
            fclose($myfile);
        }catch (\Exception $e) {
            Log::error($e );
            $myfile = fopen(storage_path() ."/logs/log-report-balance.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().":chạy cronjob: update balance error";
            fwrite($myfile, $txt ."\n");
            fclose($myfile);
        }
    }
}
