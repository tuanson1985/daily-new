<?php

namespace App\Console\Commands;

use App\Library\Helpers;
use App\Library\HelpItemAdd;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shop;
use App\Models\TxnsVp;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Log;

class DeleteOrderMinigameRecharge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deleteOrderMinigameRecharge:crom';

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

            $minutes = config('module.minigame.minute_crom_order.delete')*60;

            if (!isset($minutes) && $minutes <= 0){
                $myfile = fopen(storage_path() ."/logs/log-job-minigame-crom-order-delete.txt", "a") or die("Unable to open file!");
                $txt = Carbon::now().":chạy cronjob: minigame-crom-order-delete chưa cấu hình thời gian hủy";
                fwrite($myfile, $txt ."\n");
                fclose($myfile);
                return false;
            }

            $orders = Order::with('author')
                ->with('shop')
                ->where('status',7)
                ->where('paided_at','<=', Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->subMinutes($minutes)
                    ->format('d/m/Y H:i:s')))
                ->where('module','withdraw-item')->get();

            if (isset($orders) && count($orders)){
                foreach ($orders as $order){
                    DB::beginTransaction();

                    $order = Order::where('id',$order->id)->where('status',7)->lockForUpdate()->first();

                    if (!isset($order)){
                        $myfile = fopen(storage_path() ."/logs/log-job-minigame-crom-order-delete.txt", "a") or die("Unable to open file!");
                        $txt = Carbon::now().":chạy cronjob: minigame-crom-order-recharge không tìm thấy order";
                        fwrite($myfile, $txt ."\n");
                        fclose($myfile);
                        DB::rollBack();
                        continue;
                    }

                    if (!isset($order->payment_type)){
                        $myfile = fopen(storage_path() ."/logs/log-job-minigame-crom-order-delete.txt", "a") or die("Unable to open file!");
                        $txt = Carbon::now().":chạy cronjob: minigame-crom-order-delete không tìm thấy id loại game";
                        fwrite($myfile, $txt ."\n");
                        fclose($myfile);
                        DB::rollBack();
                        continue;
                    }

                    $type = $order->payment_type;

                    $provider = config('module.minigame.game_type_map.'.$type);

                    if (!isset($provider)){
                        $myfile = fopen(storage_path() ."/logs/log-job-minigame-crom-order-delete.txt", "a") or die("Unable to open file!");
                        $txt = Carbon::now().":chạy cronjob: minigame-crom-order-delete không tìm thấy loại game";
                        fwrite($myfile, $txt ."\n");
                        fclose($myfile);
                        DB::rollBack();
                        continue;
                    }

                    if ($provider == 'lienquan' || $provider == 'freefire' || $provider == 'lienminh' || $provider == 'bns' ||
                        $provider == 'ads' || $provider == 'fo4m' || $provider == 'fo4' || $provider == 'pubgm' || $provider == 'codm'){

                        if (!isset($order->author_id)){
                            $myfile = fopen(storage_path() ."/logs/log-job-minigame-crom-order-delete.txt", "a") or die("Unable to open file!");
                            $txt = Carbon::now().":chạy cronjob: minigame-crom-order-delete không tìm thấy id khách hàng";
                            fwrite($myfile, $txt ."\n");
                            fclose($myfile);
                            DB::rollBack();
                            continue;
                        }

                        $userid = $order->author_id;

                        $userTransaction = User::where('id',$userid)->lockForUpdate()->firstOrFail();

                        if (!isset($userTransaction)){
                            $myfile = fopen(storage_path() ."/logs/log-job-minigame-crom-order-delete.txt", "a") or die("Unable to open file!");
                            $txt = Carbon::now().":chạy cronjob: minigame-crom-order-delete không tìm thấy khách hàng";
                            fwrite($myfile, $txt ."\n");
                            fclose($myfile);
                            DB::rollBack();
                            continue;
                        }

                        $order->update([
                            'status' => 3,
                        ]);//trạng thái hủy

                        //set tiến độ hủy
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.minigame.module.withdraw-item'),
                            'content' => "Giao dịch lỗi (xem tiến độ CR)",
                            'status' => 3, //Đã hủy
                        ]);

                        $balance_item_txns = $userTransaction['ruby_num'.$type];
                        $amount =  $order->price;
                        $userTransaction['ruby_num'.$type] = $userTransaction['ruby_num'.$type] + $order->price;
                        $userTransaction->save();

                        $txns = TxnsVp::create([
                            'trade_type' => config('module.txnsvp.trade_type.refund'),
                            'is_add' => '1',
                            'user_id' => $userTransaction->id,
                            'amount' => $amount,
                            'last_balance' => $balance_item_txns + $amount,
                            'description' => "Hoàn ".$amount." rút vật phẩm thất bại gói rút" . $order->ref_id ,
                            'ref_id' => $order->id,
                            'status' => 1,
                            'shop_id' => $userTransaction->shop_id,
                            'order_id' => $order->id,
                            'item_type' => $type
                        ]);

                        $mesage = 'Hoàn vật phẩm cho khách thành công CR';

                        //set tiến độ hủy
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.minigame.module.withdraw-item'),
                            'content' => $mesage,
                            'status' => 3, //Đã hủy
                        ]);

                        DB::commit();
                        continue;
                    }else{
                        DB::rollBack();
                        continue;
                    }
                }
            }

        }catch (\Exception $e) {
            Log::error($e );
            $myfile = fopen(storage_path() ."/logs/log-job-minute-crom-order-delete.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().":chạy cronjob: minute-crom-order-delete error";
            fwrite($myfile, $txt ."\n");
            fclose($myfile);
        }
    }
}
