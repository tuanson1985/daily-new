<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Log;
use Cache;
use App\Library\Report;

class ReportTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ReportTelegram:crom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command daily report to telegram';

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
            new Report(Carbon::today()->subDay(1),config('telegram.bots.mybot.channel_id_report'));
            $myfile = fopen(storage_path() ."/logs/log-report-telegram.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().":chạy cronjob: send message telegram ok";
            fwrite($myfile, $txt ."\n");
            fclose($myfile);

        }catch (\Exception $e) {
            Log::error($e );
            $myfile = fopen(storage_path() ."/logs/log-report-telegram.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().":chạy cronjob: send message telegram error";
            fwrite($myfile, $txt ."\n");
            fclose($myfile);
        }
    }
}
