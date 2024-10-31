<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Nick;

class ReorderNick extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reorder:nick';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Thay doi thu tu hien thi tren frontend';

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
        $limit = 100;
        while (time()-$start<55 && $continue) {
            $items = Nick::where(['status' => 1, 'module' => 'acc'])->where(function($query){
                $query->whereNull('order')->orWhereNull('updated_at')->orWhere('updated_at', '<', date('Y-m-d H:i:s', time()-180));
            })->whereHas('category', function($query){
                // $query->where('display_type', '<>' , 2);
            })->take($limit)->orderBy('updated_at', 'asc')->select('id', 'order', 'status', 'module', 'updated_at')->get();
            foreach ($items as $key => $item) {
                if (!$item->order || $item->order < 100) {
                    $order = rand(1000, 10000);
                }elseif($item->order < 1000){
                    $order = rand(100, 7000);
                }elseif($item->order < 2000){
                    $order = rand(50, 5000);
                }else{
                    $order = rand(1, 2000);
                }
                Nick::where('id', $item->id)->update(['order' => $order]);
            }
            echo ($key??0)."\n";
            if (($key??0) < $limit-1) {
                $continue = false;
            }else{
                sleep(2);
            }
        }
    }
}
