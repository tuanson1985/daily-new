<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon, DB;
use App\Models\Nick;
use App\Models\Item;

class UpNick extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'up:nick';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'up nick tu bang tam nicks_queue';

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
        $start = time();
        if (cache()->has('up_nick_cronding')) {
            echo "deline\n";
            return true;
        }
        echo "running\n";
        cache(['up_nick_cronding' => time()], 60);
        /*update status nick quá 3 tháng không cập nhật*/
        if (!cache()->has('clean_nick_old_longtime')) {
            Nick::where(['status' => 1])->where(function($query){
                $query->where('started_at', '<', Carbon::now()->subDays(90)->format('Y-m-d 00:00:00'))->orWhere(function($query){
                    $query->whereNull('started_at')->where('created_at', '<', Carbon::now()->subDays(90)->format('Y-m-d 00:00:00'));
                });
            })->whereHas('category', function($query){
                $query->where('display_type', 1);
            })->update(['status' => 11]);
            cache(['clean_nick_old_longtime' => time()], 3600);
        }
        /*Xoá nick dup*/
        $dup = DB::table('nicks')
        ->select('id','title','parent_id', 'status', DB::raw('COUNT(*) as `count`'))
        ->groupBy('title', 'parent_id', 'status')
        ->havingRaw('COUNT(*) > 1')->where('status', 1)
        ->get();
        foreach ($dup as $key => $value) {
            DB::table('nicks')->where('id', '<>', $value->id)->where(['status' => $value->status, 'title' => $value->title, 'parent_id' => $value->parent_id])->delete();
        }
        /*Upnick*/
        $continue = true;
        while (time()-$start < 55 && $continue) {
            $value = null;
            // $model = new Nick();
            // $fillable = $model->getFillable();
            // $last = Nick::orderBy('id', 'desc')->first();
            // $last_id = $last->id??0;
            // $complete = (new Nick(['table' => 'nicks_completed']))->orderBy('id', 'desc')->first();
            // if ($complete && $complete->id > $last_id) {
            //     $last_id = $complete->id;
            // }
            // $old = Item::orderBy('id', 'asc')->doesntHave('nick')->doesntHave('nick_complete')->where('module', 'acc')->with(['acc_txns', 'txns_order', 'groups' => function($query){
            //     $query->select('groups.id')->withPivot('order');
            // }])->take(500)->get();
            // $completed = [];
            // $selling = [];
            // $pivots = [];
            // $value = null;
            // foreach ($old as $key => $value) {
            //     $data = $value->only($fillable);
            //     if (!empty($value->txns_order->price)) {
            //         $data['amount'] = $value->txns_order->price;
            //         $data['amount_ctv'] = $value->acc_txns->sortByDesc('id')->where('is_add', 1)->where('is_refund', 0)->first()->amount??0;
            //     }
            //     $data['params'] = json_encode($data['params']);
            //     if (!empty($value->sticky) && $value->status != 1) {
            //         $completed[] = $data;
            //     }else{
            //         $selling[] = $data;
            //     }
            //     foreach ($value->groups as $group) {
            //         $pivots[] = ['group_id' => $group->id, 'nick_id' => $data['id']];
            //     }
            // }
            // if (!empty($completed)) {
            //     \DB::table('nicks_completed')->insert($completed);
            // }
            // if (!empty($selling)){
            //     \DB::table('nicks')->insert($selling);
            // }
            // if (!empty($pivots)){
            //     \DB::table('groups_nicks')->whereIn('nick_id', $old->pluck('id')->toArray())->delete();
            //     \DB::table('groups_nicks')->insert($pivots);
            // }
            // if ($old->count() < 500) {
            //     $recent = Item::where('published_at', '>', date('Y-m-d H:i:s', time()-36000))->whereHas('nick')->whereNotNull('sticky')->get();
            //     if ($recent->count()) {
            //         $completed = [];
            //         foreach ($recent as $key => $value) {
            //             $data = $value->only($fillable);
            //             if (!empty($value->txns_order->price)) {
            //                 $data['amount'] = $value->txns_order->price;
            //                 $data['amount_ctv'] = $value->acc_txns->sortByDesc('id')->where('is_add', 1)->where('is_refund', 0)->first()->amount??0;
            //             }
            //             $data['params'] = json_encode($data['params']);
            //             $completed[] = $data;
            //         }
            //         \DB::table('nicks_completed')->insert($completed);
            //         \DB::table('nicks')->whereIn('id', $recent->pluck('id')->toArray())->delete();
            //         \Log::error("nicks completed ".$recent->count());
            //     }

            //     $complete = (new Nick(['table' => 'nicks_completed']))->where('status', 3)->select('status', 'id')->get();
            //     $change = Item::whereIn('id', $complete->pluck('id')->toArray())->whereNotIn('status', [1,3])->select('id', 'status')->get();
            //     if ($change->count()) {
            //         foreach ($change as $key => $value) {
            //             \DB::table('nicks_completed')->where('id', $value->id)->update(['status' => $value->status]);
            //         }
            //         \Log::error("status changed ".$change->count());
            //     }

            // }

            $complete = (new Nick(['table' => 'nicks_completed']))->whereIn('status', [0])->where(function($query){
                $query->where('amount', 0)->orWhereNull('amount')->orWhere(function($query){
                    $query->whereHas('acc_txns', function($query){
                        $query->where('is_add', 1)->where('is_refund', 0)->where('amount', '>', 0);
                    })->where(function($query){
                        $query->whereNull('amount_ctv')->orWhere('amount_ctv', 0);
                    });
                });
            })->whereHas('txns_order', function($query){
                $query->where('price', '>', 0);
            })->select('status', 'id', 'amount', 'amount_ctv')->with('acc_txns', 'txns_order')->limit(100)->get();
            foreach ($complete as $key => $value) {
                $data['amount'] = !empty($value->txns_order->price)? $value->txns_order->price: 0;
                $data['amount_ctv'] = $value->acc_txns->sortByDesc('id')->where('is_add', 1)->where('is_refund', 0)->first()->amount??0;
                $value->fill($data)->save();
            }

            $queue = (new Nick(['table' => 'nicks_queue']))->limit(100)->get();
            foreach ($queue as $key => $value) {
                $data = $value->toArray();
                unset($data['meta'], $data['id'], $data['image'], $data['image_extension']);
                $data['started_at'] = date('Y-m-d H:i:s');
                $nick = (new Nick(['table' => 'nicks']))->updateOrCreate(['title' => $data['title'], 'author_id' => $data['author_id'], 'parent_id' => $data['parent_id']], $data);
                if (!empty($value->meta['groups'])) {
                    try {
                        $nick->groups()->sync($value->meta['groups']);
                    } catch (\Exception $e) {}
                }
                if ($nick->status == 9 && !empty($nick->image)) {
                    $nick->fill(['status' => 1])->save();
                }
                \DB::table('nicks_queue')->where('id', $value->id)->delete();
            }
            if (!empty($value)) {
                echo $value->id."\n";
                sleep(1);
            }else{
                echo "idle 5s\n";
                sleep(5);
            }
        }
        cache()->forget('up_nick_cronding');
    }
}
