<?php

namespace App\Console\Commands;

use App\Library\ReportShop;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;

class ReportTelegramShop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ReportTelegramShop:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command daily report shop to telegram';

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
        $shops = Shop::query()->where('status',1)->get();
        if ($shops) {
            try{
                foreach ($shops as $shop) {
                    new ReportShop(Carbon::today()->subDay(1),$shop->id);

                    $myfile = fopen(storage_path() ."/logs/log-report-telegram.txt", "a") or die("Unable to open file!");
                    $txt = Carbon::now().":chạy cronjob: send message telegram ok";
                    fwrite($myfile, $txt ."\n");
                    fclose($myfile);
                }

            }catch (\Exception $e) {
                Log::error($e );
                $myfile = fopen(storage_path() ."/logs/log-report-telegram.txt", "a") or die("Unable to open file!");
                $txt = Carbon::now().":chạy cronjob: send message telegram error";
                fwrite($myfile, $txt ."\n");
                fclose($myfile);
            }

        }
    }
}
