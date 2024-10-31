<?php

namespace App\Console\Commands;
use App\Library\Helpers;
use App\Models\NickComplete;
use App\Models\OrderDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Command;
use App\Jobs\DeleteImageNickCompleteS3Job;

class DeleteImageNickCompleteS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deleteImageNickCompleteS3:cron';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa ảnh nick đã bán';

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

            $path = storage_path() ."/logs/delete-serivce-complete/";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $txt = Carbon::now().":chạy cronjob: log-job-delete-service-complete sucsess";
            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

            $order_details = OrderDetail::query()
                ->where('status',85)
                ->whereNotNull('content')
                ->whereNotNull('idkey')
                ->where('created_at', '<', Carbon::now()->subDays(7))
                ->orderBy('created_at','asc')
                ->limit(500)
                ->get();

            foreach ($order_details??[] as $order_detail){
                if (isset($order_detail)){
                    DeleteImageNickCompleteS3Job::dispatch($order_detail);
                }
            }

        }catch (\Exception $e) {
            Log::error($e );

            $path = storage_path() ."/logs/delete-service-complete/";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $txt = Carbon::now().":chạy cronjob: log-job-delete-service-complete error: ".$e->getLine()." - ".$e->getMessage();
            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

        }
    }


}
