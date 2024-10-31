<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use App\Models\Charge;
use App\Models\User;
use App\Models\Shop;
use Cache;
use Log;

use Illuminate\Console\Command;

class TopCharge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TopCharge:crom';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update top nap the user theo tung phut';

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
            $shop = Shop::all();
            foreach($shop as $shop_item){
                $nameCahe = 'cache_top_charge_'.$shop_item->id.'';
                Cache::forget($nameCahe);
                Cache::rememberForever($nameCahe, function () use ($shop_item){
                    $top = Charge::with('user')
                    ->selectRaw('SUM(amount) as sum,user_id')
                    ->where('shop_id',$shop_item->id)
                    ->groupBy('user_id')
                    ->orderByRaw('SUM(amount) DESC')
                    ->where(function ($query){
                        $query->where('status',1);
                        $query->orWhere('status',3);
                    })
                    ->where('created_at', '>=', Carbon::now()->startOfMonth())
                    ->where('created_at', '<=', Carbon::now()->endOfMonth())
                    ->limit(10)
                    ->get();
                    $data = array();
                    foreach($top as $key => $item){
                        $data[] = [
                            'user_id' => $item->user_id,
                            'amount' => $item->sum,
                            'username' => $item->user->username,
                            'fullname' => $item->user->fullname,
                        ];
                    }
                    return $data;
                });
                continue;
            }
            $myfile = fopen(storage_path() ."/logs/log-top-charge.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().":chạy cronjob: update top nap the thanh cong";
            fwrite($myfile, $txt ."\n");
            fclose($myfile);

        }catch (\Exception $e) {
            Log::error($e);
            $myfile = fopen(storage_path() ."/logs/log-top-charge.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().":chạy cronjob: update top nap the that bai";
            fwrite($myfile, $txt ."\n");
            fclose($myfile);
        }
    }
}
