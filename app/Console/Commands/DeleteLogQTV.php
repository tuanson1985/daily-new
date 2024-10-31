<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Cache;
use Log;
use Carbon\Carbon;
use App\Models\ActivityLog;

class DeleteLogQTV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DeleteLogQTV:crom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete log truy cap thanh vien sau thoi gian 2 thang';

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
            // cách thời gian hiện tại 2 tháng
            $time = Carbon::now()->subMonths(2); 
            $log = ActivityLog::where('created_at','<',$time)->delete();
        }catch (\Exception $e) {
            Log::error($e);
            $myfile = fopen(storage_path() ."/logs/log-deleteActivityLog.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().":chạy cronjob: update deleteActivityLog that bai";
            fwrite($myfile, $txt ."\n");
            fclose($myfile);
        }

    }
}
