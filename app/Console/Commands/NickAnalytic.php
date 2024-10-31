<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Nick;
use App\Models\AnalyticNick;
use App\Library\NickHelper;

class NickAnalytic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nick:analytic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Thống kê nick';

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
        $continue = true;
        $start = time();
        while (time()-$start<55 && $continue) {
            $continue = NickHelper::create_timeline();
            if ($continue) {
                echo $continue."\n";
                // sleep(1);
            }
        }
        if (time()-$start<50) {
            $last = Carbon::now()->subMonth()->format('Y-m-d');
            $continue = true;
            while (time()-$start<55 && $continue) {
                $continue = NickHelper::create_timeline(['live' => $last]);
                if ($continue) {
                    echo $continue."\n";
                    sleep(1);
                }
            }
        }
    }
}
