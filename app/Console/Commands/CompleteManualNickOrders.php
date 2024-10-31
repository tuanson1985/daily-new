<?php

namespace App\Console\Commands;

use App\Library\Helpers;
use App\Library\HelpItemAdd;
use App\Models\GameAccess;
use App\Models\Nick;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ServiceAccess;
use App\Models\Shop;
use App\Models\Txns;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Log;

class CompleteManualNickOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'completeManualNickOrders:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

             $path = storage_path() ."/logs/job-nick-completed/";
             if(!\File::exists($path)){
                 \File::makeDirectory($path, $mode = "0755", true, true);
             }
             $txt = Carbon::now().":chạy cronjob: chạy log-job-complete-manual-nick-orders thành công";
             \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

            $hour = config('etc.minute_order_cron.complete');
            $minutes = config('etc.minute_order_cron.complete')*60;

            $minutes = 5;
            //Danh sách nick ở trạng thái đã mua chờ xử lý
            $accounts = (new Nick(['table' => 'nicks_completed']))
                ->where('module','acc')
                ->select('id','author_id','amount_ctv','amount','price_old','price','status','sticky','shop_id','percent_sale')
                ->where('status',12)
                ->where('published_at','<=', Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->subMinutes($minutes)->format('d/m/Y H:i:s')))
                ->get();

            if (isset($accounts) && count($accounts)){
                foreach ($accounts as $account){
                    DB::beginTransaction();
                    //Nick ở trạng thái đã mua chờ xử lý
                    $acc = (new Nick(['table' => 'nicks_completed']))
                        ->select('id','module','author_id','parent_id','amount_ctv','amount','price_old','price','status','sticky','shop_id','percent_sale')
                        ->where('module','acc')
                        ->where('status',12)
                        ->where('id',$account->id)
                        ->where('published_at','<=', Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->subMinutes($minutes)->format('d/m/Y H:i:s')))
                        ->lockForUpdate()
                        ->first();

                    if (!isset($acc)){
                        $path = storage_path() ."/logs/job-nick-completed/";
                        if(!\File::exists($path)){
                            \File::makeDirectory($path, $mode = "0755", true, true);
                        }
                        $txt = Carbon::now().":chạy cronjob: log-job-complete-manual-nick-orders error không tìm thấy nick cần xử lý";
                        \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

                        DB::rollBack();
                        continue;
                    }

                    if (!isset($acc->price) && $acc->price <= 0){
                        $path = storage_path() ."/logs/job-nick-completed/";
                        if(!\File::exists($path)){
                            \File::makeDirectory($path, $mode = "0755", true, true);
                        }
                        $txt = Carbon::now().":chạy cronjob: log-job-complete-manual-nick-orders nick lỗi giá vui lòng kiểm tra lại";
                        \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

                        DB::rollBack();
                        continue;
                    }

                    //Đơn hàng.
                    $order = Order::query()
                        ->select('id','module','price','real_received_price','ratio')
                        ->where('module','buy_acc')
                        ->where('ref_id',$acc->id)
                        ->where('shop_id',$acc->shop_id)
                        ->lockForUpdate()
                        ->first();

                    if (!isset($order)) {

                        $path = storage_path() ."/logs/job-nick-completed/";
                        if(!\File::exists($path)){
                            \File::makeDirectory($path, $mode = "0755", true, true);
                        }
                        $txt = Carbon::now().":chạy cronjob: log-job-complete-manual-nick-orders không tìm thấy đơn hàng";
                        \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

                        DB::rollBack();
                        continue;
                    }

                    $add_price = $acc->price;


                    /*Tính lại chiết khấu*/
                    $discount = GameAccess::where(['user_id' => $acc->author_id, 'group_id' => $acc->parent_id])->first();

                    if (!empty($discount)) {
                        $step = 0;
                        $ratio = 0;
                        foreach ($discount->ratio as $key => $value) {
                            if ($key > 0) {
                                $step = $key;
                                if ($add_price <= $key) {
                                    $ratio = $value;
                                    break;
                                }
                            }elseif ($key == 'over' && $add_price > $step) {
                                $ratio = $value;
                            }
                        }
                        if ((empty($ratio) || $ratio == 0) && !empty($discount['default'])) {
                            $ratio = $discount['default'];
                        }
                        if ($ratio > 0) {
                            $add_price = $add_price - $add_price*$ratio/100;
                        }
                    }

                    //Thông tin cộng tác viên.

                    $author = User::where('id', $acc->author_id)->lockForUpdate()->first();

                    if (!isset($author)){
                        $path = storage_path() ."/logs/job-nick-completed/";
                        if(!\File::exists($path)){
                            \File::makeDirectory($path, $mode = "0755", true, true);
                        }
                        $txt = Carbon::now().":chạy cronjob: log-job-complete-manual-nick-orders không tìm thấy ctv.";
                        \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

                        DB::rollBack();
                        continue;
                    }

                    if($author->checkBalanceValid() == false){

                        $path = storage_path() ."/logs/job-nick-completed/";
                        if(!\File::exists($path)){
                            \File::makeDirectory($path, $mode = "0755", true, true);
                        }
                        $txt = Carbon::now().":chạy cronjob: log-job-complete-manual-nick-orders ctv đơn hàng có giao dịch bất minh ".$order->id;
                        \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

                        DB::rollBack();
                        continue;
                    }

                    if (Txns::where(['trade_type' => 'buy_acc','order_id' => $order->id,'is_add' => 1,'is_refund' => 0,'status' => 1])->exists()) {
                        $path = storage_path() ."/logs/job-nick-completed/";
                        if(!\File::exists($path)){
                            \File::makeDirectory($path, $mode = "0755", true, true);
                        }
                        $txt = Carbon::now().":chạy cronjob: log-job-complete-manual-nick-orders author_trans exists for nick";
                        \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

                        DB::rollBack();
                        continue;
                    }

                    //Cộng tiền cho CYV
                    $author->balance = $author->balance + $add_price;
                    $author->balance_in = $author->balance_in + $add_price;
                    $author->save();

                    $author_txns = Txns::create([
                        'shop_id' => $acc->shop_id, 'trade_type' => 'buy_acc', 'user_id' => $author->id, 'order_id' => $order->id, 'amount' => $add_price,
                        'last_balance' => $author->balance, 'is_add' => 1, 'is_refund' => 0, 'status' => 1, 'txnsable_type' => 'App\Models\Nick', 'txnsable_id' => $acc->id,
                        'description' => "Hệ thống tự động cộng tiền bán acc #{$acc->id} sau {$hour} giờ",
                    ]);

                    $real_received_price = $order->price - $add_price;

                    $order->fill(['real_received_price' => $real_received_price, 'ratio' => $real_received_price*100/$order->price])->save();

                    \DB::table('nicks_completed')->where('id', $acc->id)
                        ->update(['amount_ctv' => $add_price,'status'=>0]);

                    DB::commit();
                }
            }


        }catch (\Exception $e) {
            Log::error($e );

            $path = storage_path() ."/logs/job-nick-completed/";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $txt = Carbon::now().":chạy cronjob: log-job-complete-manual-nick-orders error: ".$e->getLine()." - ".$e->getMessage();
            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

        }
    }


}
