<?php

namespace App\Http\Controllers\Api\ToolGame;

use App\Http\Controllers\Controller;


use App\Library\ChargeGameGateway\RobloxGate;
use App\Library\DirectAPI;
use App\Library\Helpers;
use App\Models\Group_Item;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Roblox_Bot;
use App\Models\Roblox_Bot_San;
use App\Models\Roblox_Order;
use App\Models\SubItem;
use App\Models\Txns;
use App\Models\User;
use Cache;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Log;


class RobloxApiController extends Controller
{
    private $secretkey = "456trtyt88888%@ttt";
    private $ip_array = ['45.118.145.145', '103.237.144.44'];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }


    public function getProcess(Request $request)
    {

        // return "";
        // Start transaction!
        DB::beginTransaction();
        try {

            $roblox_order = Roblox_Order::where('status',"chuanhan")
                ->where(function($q){
                    $q->orWhere('type_order',1);
                    $q->orWhere('type_order',2);
                })
                ->lockForUpdate()
                ->first();

            if(!$roblox_order){
                return "Không tìm thấy đơn chưa nhận";
            }
            //foreach ($roblox_orders as $roblox_order) {


            $dataPurchase = Item::where('id',$roblox_order->item_id)
                ->where('module', '=', config('constants.module.service.key_purchase'))
                ->lockForUpdate()
                ->firstOrFail();


            $roblox_bot = Roblox_Bot::where('server',$roblox_order->server)
                ->whereHas('shop', function ($query) use ($request,$dataPurchase) {
                    $query->where('id', $dataPurchase->shop_id);
                })->lockForUpdate()->first();

            if($dataPurchase->status != '1'){
                $roblox_order->status = 'dahuy';
                $roblox_order->save();
                DB::commit();
                return "Đã hủy đơn #".$dataPurchase->id;
            }

            //kiểm tra có đúng bot ko,hoặc ko đúng bot với đơn
            if(!$roblox_bot){

                //hoàn tiền khi ko tìm thấy bot
                //refund
                $userTransaction = User::where('username',$dataPurchase->author)->lockForUpdate()->firstOrFail();

                $roblox_order->status = "dahoantien";
                $roblox_order->save();

                $dataPurchase->status = 5;
                $dataPurchase->save();
                //set tiến độ
                SubItem::create([
                    'item_id' => $dataPurchase->id,
                    'module' => config('constants.module.service.key_workflow'),
                    'content' => "Giao dịch thất bại :". "Không tìm thấy group bot phù hợp với đơn đã order",
                    'status' => 5,
                ]);

                if($roblox_order->type_order==2){

                    if($dataPurchase->price_base<=0){
                        return "Giao dịch thất bại.Số robux dịch không phù hợp";
                    }
                    $userTransaction['roblox_num'] = $userTransaction['roblox_num'] + $dataPurchase->price_base;
                    $userTransaction->save();

                    //tạo tnxs
                    $txns = Txns::create([
                        'trade_type' => '11',//Hoàn tiền
                        'is_add' => '1',//Công tiền
                        'username' => $userTransaction->username,
                        'amount' => $dataPurchase->price,
                        'real_received_amount' => $dataPurchase->price_base,
                        'last_balance' => $userTransaction->robux_num,
                        'description' => 'Hoàn robux giao dịch lỗi Không tìm thấy group bot phù hợp với đơn đã order #' . $dataPurchase->id .'('.$dataPurchase->title.')',
                        'ref_id' => $dataPurchase->id,
                        'ip' => $request->getClientIp(),
                        'status' => 1
                    ]);

                }
                else
                {


                    if($dataPurchase->price<=0){
                        return "Giao dịch thất bại.Số tiền giao dịch không phù hợp";
                    }
                    $userTransaction->balance = $userTransaction->balance + $dataPurchase->price;
                    $userTransaction->save();


                    //tạo tnxs
                    $txns = Txns::create([
                        'trade_type' => '11',//Hoàn tiền
                        'is_add' => '1',//Công tiền
                        'username' => $userTransaction->username,
                        'amount' => $dataPurchase->price,
                        'real_received_amount' => $dataPurchase->price,
                        'last_balance' => $userTransaction->balance,
                        'description' => 'Hoàn tiền giao dịch lỗi Không tìm thấy group bot phù hợp với đơn đã order #' . $dataPurchase->id .'('.$dataPurchase->title.')',
                        'ref_id' => $dataPurchase->id,
                        'ip' => $request->getClientIp(),
                        'status' => 1
                    ]);

                }
                DB::commit();
                return "Đã hủy lệnh ".$dataPurchase->id." do không đúng group của bot hiện tại";

            }





            $result=RobloxGate::ProcessTranfer($roblox_order->uname,$roblox_order->money,$roblox_order->server,$roblox_bot->cookies);
            $myfile = fopen(storage_path() ."/logs/log_roblox.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now()." Id: ".$dataPurchase->id."- status: ".$result->status;
            fwrite($myfile, $txt ."\n");
            fclose($myfile);

            if($result->status==1){

                $roblox_order->status = "danhan";
                $roblox_order->save();

                //cập nhật trạng thái của purchase

                $dataPurchase->status = 4;
                $dataPurchase->save();
                SubItem::create([
                    'item_id' => $dataPurchase->id,
                    'module' => config('constants.module.service.key_workflow'),
                    'content' => "Giao dịch thành công",
                    'status' => 4,
                ]);
                DB::commit();
                return "Giao dịch thành công giao dịch #".$dataPurchase->id;;

            }
            elseif($result->status==2){
                //Trạng thái bot die cookie
                $roblox_bot->status=2;
                $roblox_bot->save();
                //Hoàn robux

                DB::commit();
                return "Giao dịch thất bại";

            }
            else{

                $roblox_order->status = "dahoantien";
                $roblox_order->save();

                $dataPurchase->status = 5;
                $dataPurchase->save();

                if (strpos($result->message, "The amount is invalid") > -1) {
                    $result->message="Group đã hết roblox ";
                }

                //set tiến độ
                SubItem::create([
                    'item_id' => $dataPurchase->id,
                    'module' => config('constants.module.service.key_workflow'),
                    'content' => "Giao dịch thất bại ". $result->message." : ".$roblox_order->money,
                    'status' => 5,
                ]);




                //refund
                $userTransaction = User::where('username',$dataPurchase->author)->lockForUpdate()->firstOrFail();

                if($roblox_order->type_order==2){

                    if($dataPurchase->price_base<=0){
                        return "Giao dịch thất bại.Số robux dịch không phù hợp";
                    }
                    $userTransaction['roblox_num'] = $userTransaction['roblox_num'] + $dataPurchase->price_base;
                    $userTransaction->save();

                    //tạo tnxs
                    $txns = Txns::create([
                        'trade_type' => '11',//Hoàn tiền
                        'is_add' => '1',//Công tiền
                        'username' => $userTransaction->username,
                        'amount' => $dataPurchase->price,
                        'real_received_amount' => $dataPurchase->price_base,
                        'last_balance' => $userTransaction->robux_num,
                        'description' => 'Hoàn robux giao dịch lỗi dich vụ #' . $dataPurchase->id .'('.$dataPurchase->title.')',
                        'ref_id' => $dataPurchase->id,
                        'ip' => $request->getClientIp(),
                        'status' => 1
                    ]);

                }
                else
                {
                    if($dataPurchase->price<=0){
                        return "Giao dịch thất bại.Số tiền giao dịch không phù hợp";
                    }
                    $userTransaction->balance = $userTransaction->balance + $dataPurchase->price;
                    $userTransaction->save();


                    //tạo tnxs
                    $txns = Txns::create([
                        'trade_type' => '11',//Hoàn tiền
                        'is_add' => '1',//Công tiền
                        'username' => $userTransaction->username,
                        'amount' => $dataPurchase->price,
                        'real_received_amount' => $dataPurchase->price,
                        'last_balance' => $userTransaction->balance,
                        'description' => 'Hoàn tiền giao dịch lỗi dich vụ #' . $dataPurchase->id .'('.$dataPurchase->title.')',
                        'ref_id' => $dataPurchase->id,
                        'ip' => $request->getClientIp(),
                        'status' => 1
                    ]);

                }
                DB::commit();
                return "Giao dịch thất bại ".$result->message;
            }
            //}
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('[Lỗi bán roblox]' . $e->getMessage());
            return "Lỗi bán roblox:".$e->getMessage();
        }


    }

    public function getProcessBuyServer(Request $request)
    {

        $job = (new \App\Jobs\ServiceAuto\RobloxJob($request->id));
        $this->dispatchNow($job);



        //
        //// Start transaction!
        //DB::beginTransaction();
        //try {
        //
        //    $roblox_order = Roblox_Order::where('status',"chuanhan")
        //        ->where('type_order',3)
        //        ->lockForUpdate()
        //        ->first();
        //
        //    if(!$roblox_order){
        //        return "Không tìm thấy đơn chưa nhận";
        //    }
        //
        //    //foreach ($roblox_orders as $roblox_order) {
        //    $roblox_bot = Roblox_Bot::where('status',1)
        //        ->where('server',-1)
        //        ->first();
        //
        //    $dataPurchase = Order::where('id',$roblox_order->order_id)
        //        ->where('module', '=',config('module.service-purchase.key'))
        //        ->lockForUpdate()
        //        ->firstOrFail();
        //
        //    if($dataPurchase->status != 1 ){
        //        $roblox_order->status = 'dahuy';
        //        $roblox_order->save();
        //        DB::commit();
        //        return "Đơn #".$dataPurchase->id ." đã hủy";
        //    }
        //
        //    //kiểm tra có đúng bot ko,hoặc ko đúng bot với đơn
        //    if(!$roblox_bot){
        //
        //
        //        //hoàn tiền khi ko tìm thấy bot
        //        //refund
        //        $userTransaction = User::where('id',$dataPurchase->author_id)->lockForUpdate()->firstOrFail();
        //
        //        $roblox_order->status = "dahoantien";
        //        $roblox_order->save();
        //
        //        $dataPurchase->status = 5;
        //        $dataPurchase->save();
        //
        //        //set tiến độ
        //        OrderDetail::create([
        //            'order_id' =>$dataPurchase->id,
        //            'module' => config('module.service-workflow.key'),
        //            'content' => "Giao dịch thất bại :". "Không tìm thấy group bot phù hợp với đơn đã order",
        //            'status' => 5
        //
        //        ]);
        //
        //        if($roblox_order->type_order==2){
        //
        //            //Phần rút chưa hoạt động comment lại
        //            //if($dataPurchase->price_base<=0){
        //            //    return "Giao dịch thất bại.Số robux dịch không phù hợp";
        //            //}
        //            //$userTransaction['roblox_num'] = $userTransaction['roblox_num'] + $dataPurchase->price_base;
        //            //$userTransaction->save();
        //            //
        //            ////tạo tnxs
        //            //$txns = Txns::create([
        //            //    'trade_type' => '11',//Hoàn tiền
        //            //    'is_add' => '1',//Công tiền
        //            //    'username' => $userTransaction->username,
        //            //    'amount' => $dataPurchase->price,
        //            //    'real_received_amount' => $dataPurchase->price_base,
        //            //    'last_balance' => $userTransaction->robux_num,
        //            //    'description' => 'Hoàn robux giao dịch lỗi Không tìm thấy group bot phù hợp với đơn đã order #' . $dataPurchase->id .'('.$dataPurchase->title.')',
        //            //    'ref_id' => $dataPurchase->id,
        //            //    'ip' => $request->getClientIp(),
        //            //    'status' => 1
        //            //]);
        //
        //        }
        //        else
        //        {
        //
        //
        //            if($dataPurchase->price<=0){
        //                return "Giao dịch thất bại.Số tiền giao dịch không phù hợp";
        //            }
        //            $userTransaction->balance = $userTransaction->balance + $dataPurchase->price;
        //            $userTransaction->save();
        //
        //
        //            //tạo tnxs
        //            $txns = Txns::create([
        //                'trade_type' => '11',//Hoàn tiền
        //                'is_add' => '1',//Công tiền
        //                'user_id' => $userTransaction->id,
        //                'amount' => $dataPurchase->price,
        //                'real_received_amount' => $dataPurchase->price,
        //                'last_balance' => $userTransaction->balance,
        //                'description' => 'Hoàn tiền giao dịch lỗi Không tìm thấy group bot phù hợp với đơn đã order #' . $dataPurchase->id .'('.$dataPurchase->title.')',
        //                'ref_id' => $dataPurchase->id,
        //                'ip' => $request->getClientIp(),
        //                'status' => 1
        //            ]);
        //
        //        }
        //        DB::commit();
        //        return "Đã hủy lệnh ".$dataPurchase->id." do không đúng group của bot hiện tại";
        //
        //    }
        //
        //
        //
        //    $result=RobloxGate::ProcessBuyServer($roblox_order->server,$roblox_order->money,$roblox_bot->cookies,$dataPurchase->request_id);
        //
        //    //Giao dịch thành công
        //    if($result->status==1){
        //        $roblox_order->status = "danhan";
        //        $roblox_order->save();
        //
        //        //cập nhật trạng thái thành công của đơn
        //        $dataPurchase->status = 4;
        //        $dataPurchase->save();
        //        //set tiến độ
        //        OrderDetail::create([
        //            'order_id' => $dataPurchase->id,
        //            'module' => config('module.service-workflow.key'),
        //            'content' => "Giao dịch thành công",
        //            'status' => 4,
        //
        //        ]);
        //        DB::commit();
        //        return "Giao dịch thành công giao dịch #".$dataPurchase->id ." - Request ID:".$dataPurchase->request_id;
        //
        //    }
        //
        //    //Giao dịch không đúng params
        //    elseif($result->status==2){
        //
        //        $roblox_bot->status=2;
        //        $roblox_bot->save();
        //        //Hoàn robux
        //
        //        DB::commit();
        //        return "Giao dịch thất bại ".$result->message;
        //    }
        //
        //    //Hoàn tiền theo status = 0
        //    elseif($result->status==0){
        //
        //        $roblox_order->status = "dahoantien";
        //        $roblox_order->save();
        //        //cập nhật trạng thái thất bại của đơn
        //        $dataPurchase->status = 5;
        //        $dataPurchase->save();
        //
        //        if (strpos($result->message, "The amount is invalid") > -1) {
        //            $result->message="Group đã hết roblox ";
        //        }
        //
        //
        //        //set tiến độ
        //        OrderDetail::create([
        //            'order_id' => $dataPurchase->id,
        //            'module' => config('module.service-workflow.key'),
        //            'content' =>  $result->message." : ".$roblox_order->money,
        //            'status' => 5,
        //
        //        ]);
        //
        //        //refund
        //        $userTransaction = User::where('id',$dataPurchase->author_id)->lockForUpdate()->firstOrFail();
        //
        //        //các lệnh rút thì xử lý ở đây
        //        if($roblox_order->type_order==2){
        //
        //        }
        //        else
        //        {
        //            if($dataPurchase->price<=0){
        //                return "Giao dịch thất bại.Số tiền giao dịch không phù hợp";
        //            }
        //            $userTransaction->balance = $userTransaction->balance + $dataPurchase->price;
        //            $userTransaction->save();
        //
        //            Txns::create([
        //                'trade_type' => 'refund', //THoàn tiền dịch vụ
        //                'user_id' => $userTransaction->id,
        //                'is_add' => '1',//Công tiền
        //                'amount' => $dataPurchase->price,
        //                'real_received_amount' => $dataPurchase->price,
        //                'last_balance' => $userTransaction->balance,
        //                'description' => "Hoàn tiền thanh toán thất bại dịch vụ " . $dataPurchase->title . " #".$dataPurchase->id ,
        //                'ip' => $request->getClientIp(),
        //                'shop_id' => $userTransaction->shop_id,
        //                'ref_id' => $dataPurchase->id,
        //                'status' => 1
        //
        //            ]);
        //        }
        //        DB::commit();
        //        return $result->message??"";
        //    }
        //    //chờ xử lý thủ công
        //    elseif($result->status==999){
        //
        //        $dataPurchase->status = 6;
        //        $dataPurchase->save();
        //
        //        //set tiến độ
        //        OrderDetail::create([
        //            'order_id' => $dataPurchase->id,
        //            'module' => config('module.service-workflow.key'),
        //            'content' => "Giao dịch chờ kiểm tra thủ công",
        //            'status' => 6,
        //
        //        ]);
        //        DB::commit();
        //        return "Giao dịch thất bại cần check thủ công (".$result->message.")";
        //    }
        //    //}
        //} catch (\Exception $e) {
        //    DB::rollback();
        //    Log::error($e);
        //    return "Lỗi bán roblox:".$e->getMessage();
        //}


    }

    public function getOrder(Request $request){

        try {
            if ($request->secretkey != config('roblox_bot.sign')) {
                return "không được truy cập!";
            }

            $roblox_order = Roblox_Order::query()
                ->with('order',function ($query){
                    $query->select('id','request_id_customer','idkey')->where('idkey','roblox_gem_pet')->where('status',1);
                })
                ->whereHas('order', function ($querysub) use ($request){
                    $querysub->where('idkey','roblox_gem_pet')->where('status',1);
                })
                ->select('id','status','type_order','order_id','uname','money','phone')
                ->where('status',"chuanhan")
                ->where('type_order',4)
                ->limit(20)
                ->get()->map(function ($item) {

                    $item->request_id = $item->order->request_id_customer;
                    $item->money = $item->phone;

                    $order = Order::query()->where('id',$item->order_id)->first();
                    $order->status = 2;
                    $count = (int)$order->order??0;

                    $order->order = $count + 1;

                    if ($order->order > 2){
                        $message = "[" . Carbon::now() . "] Đơn hàng: "."<b>".$order->request_id_customer."</b> được gọi <b> lần thứ: ".$order->order.'</b> Vui lòng kiểm tra có bug không.Thông báo từ '.config('app.url');
                        Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
                    }

                    $order->save();

                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 2,
                        'content' =>  "Tool đã lấy đơn !",
                    ]);
                    unset($item->phone);
                    unset($item->order);
                    return $item;
                });

            //Lưu cache.

            Cache::put('CHECK_TOOL_GAME_PET_ROBLOX',true,now()->addMinutes(5));

            return response()->json([
                'status' => 1,
                'message' => 'Lấy dữ liệu thành công',
                'data' =>$roblox_order
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('[Lỗi lấy thông tin đơn hàng roblox]' . $e->getMessage());
            return "Lỗi lấy thông tin đơn hàng roblox:".$e->getMessage();
        }
    }

    public function getOrderHugePsx(Request $request){

        try {

            if ($request->secretkey != config('roblox_bot.sign')) {
                return "không được truy cập!";
            }

            $roblox_order = Roblox_Order::query()
                ->with('order',function ($query){
                    $query->select('id','request_id_customer','idkey')->where('idkey','huge_psx_auto')->where('status',1);
                })
                ->whereHas('order', function ($querysub) use ($request){
                    $querysub->where('idkey','huge_psx_auto')->where('status',1);
                })
                ->select('id','status','type_order','order_id','uname','phone')
                ->where('status',"chuanhan")
                ->where('type_order',5)
                ->limit(20)
                ->get()->map(function ($item) {
                    $item->request_id = $item->order->request_id_customer;

                    $order = Order::query()->where('id',$item->order_id)->first();
                    $order->status = 2;

                    $count = (int)$order->order??0;

                    $order->order = $count + 1;
                    if ($order->order > 2){
                        $message = "[" . Carbon::now() . "] Đơn hàng huge psx: "."<b>".$order->request_id_customer."</b> được gọi <b> lần thứ: ".$order->order.'</b> Vui lòng kiểm tra có bug không.Thông báo từ '.config('app.url');
                        Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_huge_psx_roblox'));
                    }

                    $order->save();

                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 2,
                        'content' =>  "Tool đã lấy đơn !",
                    ]);

                    unset($item->order);
                    return $item;
                });

            //Lưu cache.

            Cache::put('CHECK_TOOL_GAME_HUGE_PSX_ROBLOX',true,now()->addMinutes(5));

            return response()->json([
                'status' => 1,
                'message' => 'Lấy dữ liệu thành công',
                'data' =>$roblox_order
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('[Lỗi lấy thông tin đơn hàng roblox]' . $e->getMessage());
            return "Lỗi lấy thông tin đơn hàng roblox:".$e->getMessage();
        }
    }

    public function getOrderGempet99(Request $request){

        try {

            if ($request->secretkey != config('roblox_bot.sign')) {
                return "không được truy cập!";
            }

            $roblox_order = Roblox_Order::query()
                ->with('order',function ($query){
                    $query->select('id','request_id_customer','idkey')->where('idkey','pet_99_auto')->where('status',1);
                })
                ->whereHas('order', function ($querysub) use ($request){
                    $querysub->where('idkey','pet_99_auto')->where('status',1);
                })
                ->select('id','status','type_order','order_id','uname','money','phone')
                ->where('status',"chuanhan")
                ->where('type_order',6)
                ->limit(20)
                ->get()->map(function ($item) {

                    $item->request_id = $item->order->request_id_customer;

                    $order = Order::query()->where('id',$item->order_id)->first();
                    $order->status = 2;

                    $count = (int)$order->order??0;

                    $order->order = $count + 1;
                    if ($order->order > 2){
                        $message = "[" . Carbon::now() . "] Đơn hàng gempet 99: "."<b>".$order->request_id_customer."</b> được gọi <b> lần thứ: ".$order->order.'</b> Vui lòng kiểm tra có bug không.Thông báo từ '.config('app.url');
                        Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
                    }

                    $order->save();

                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 2,
                        'content' =>  "Tool đã lấy đơn !",
                    ]);
                    unset($item->money);
                    unset($item->order);
                    return $item;
                });

            //Lưu cache.

            Cache::put('CHECK_TOOL_GAME_PET99_ROBLOX',true,now()->addMinutes(5));

            return response()->json([
                'status' => 1,
                'message' => 'Lấy dữ liệu thành công',
                'data' =>$roblox_order
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('[Lỗi lấy thông tin đơn hàng roblox]' . $e->getMessage());
            return "Lỗi lấy thông tin đơn hàng roblox:".$e->getMessage();
        }
    }

    public function getOrderItemPetGo(Request $request){

        try {

            if ($request->secretkey != config('roblox_bot.sign')) {
                return "không được truy cập!";
            }

            $roblox_order = Roblox_Order::query()
                ->with('order',function ($query){
                    $query->select('id','request_id_customer','idkey')->where('idkey','item_pet_go_auto')->where('status',1);
                })
                ->whereHas('order', function ($querysub) use ($request){
                    $querysub->where('idkey','item_pet_go_auto')->where('status',1);
                })
                ->select('id','status','type_order','order_id','uname','money','phone')
                ->where('status',"chuanhan")
                ->where('type_order',12)
                ->limit(20)
                ->get()->map(function ($item) {

                    $item->request_id = $item->order->request_id_customer;

//                    $order = Order::query()->where('id',$item->order_id)->first();
//                    $order->status = 2;
//
//                    $count = (int)$order->order??0;
//
//                    $order->order = $count + 1;
//                    if ($order->order > 2){
//                        $message = "[" . Carbon::now() . "] Đơn hàng gempet 99: "."<b>".$order->request_id_customer."</b> được gọi <b> lần thứ: ".$order->order.'</b> Vui lòng kiểm tra có bug không.Thông báo từ '.config('app.url');
//                        Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
//                    }
//
//                    $order->save();
//
//                    //set tiến độ
//                    OrderDetail::create([
//                        'order_id' => $order->id,
//                        'module' => config('module.service-workflow.key'),
//                        'status' => 2,
//                        'content' =>  "Tool đã lấy đơn !",
//                    ]);
                    unset($item->money);
                    unset($item->order);
                    return $item;
                });

            //Lưu cache.

            Cache::put('CHECK_TOOL_GAME_PETGO_ROBLOX',true,now()->addMinutes(5));

            return response()->json([
                'status' => 1,
                'message' => 'Lấy dữ liệu thành công',
                'data' =>$roblox_order
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('[Lỗi lấy thông tin đơn hàng roblox]' . $e->getMessage());
            return "Lỗi lấy thông tin đơn hàng roblox:".$e->getMessage();
        }
    }

    public function getOrderGemHuge99(Request $request){
        try {

            if ($request->secretkey != config('roblox_bot.sign')) {
                return "không được truy cập!";
            }

            $roblox_order = Roblox_Order::query()
                ->with('order',function ($query){
                    $query->select('id','request_id_customer','idkey')->where('idkey','huge_99_auto')->where('status',1);
                })
                ->whereHas('order', function ($querysub) use ($request){
                    $querysub->where('idkey','huge_99_auto')->where('status',1);
                })
                ->select('id','status','type_order','order_id','uname','phone')
                ->where('status',"chuanhan")
                ->where('type_order',7)
                ->limit(20)
                ->get()->map(function ($item) {
                    $item->request_id = $item->order->request_id_customer;
                    $item->phone = str_replace(' ', '', $item->phone);

                    $order = Order::query()->where('id',$item->order_id)->first();
                    $order->status = 2;

                    $count = (int)$order->order??0;

                    $order->order = $count + 1;
                    if ($order->order > 2){
                        $message = "[" . Carbon::now() . "] Đơn hàng <b>Huge 99</b>: "."<b>".$order->request_id_customer."</b> được gọi <b> lần thứ: ".$order->order.'</b> Vui lòng kiểm tra có bug không.Thông báo từ '.config('app.url');
                        Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_huge_psx_roblox'));
                    }

                    $order->save();

                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 2,
                        'content' =>  "Tool đã lấy đơn !",
                    ]);

                    unset($item->order);
                    return $item;
                });

            //Lưu cache.

            Cache::put('CHECK_TOOL_GAME_HUGE_99_ROBLOX',true,now()->addMinutes(5));

            return response()->json([
                'status' => 1,
                'message' => 'Lấy dữ liệu thành công',
                'data' =>$roblox_order
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('[Lỗi lấy thông tin đơn hàng roblox]' . $e->getMessage());
            return "Lỗi lấy thông tin đơn hàng roblox:".$e->getMessage();
        }

    }

    public function getOrderGemUnist(Request $request){

        try {

            if ($request->secretkey != config('roblox_bot.sign')) {
                return "không được truy cập!";
            }

            if (!$request->filled('type_order')) {
                return "Vui lòng gửi loại đơn hàng!";
            }

            $type_order = $request->get('type_order');

            if (!in_array($type_order,[0,1])){
                return "Loại đơn hàng không hợp lệ!";
            }

            $roblox_order = Roblox_Order::query()
                ->with('order',function ($query){
                    $query->select('id','request_id_customer','idkey')->where('idkey','gem_unist_auto')->where('status',1);
                })
                ->whereHas('order', function ($querysub) use ($request){
                    $querysub->where('idkey','gem_unist_auto')->where('status',1);
                })
                ->whereNotNull('ver')
                ->where('ver',0)
                ->select('id','status','type_order','order_id','uname','phone','ver')
                ->where('status',"chuanhan")
                ->where('type_order',8);

            if ($type_order == 0){
                $roblox_order = $roblox_order->where('ver',0);
            }else{
                $roblox_order = $roblox_order->where('ver',1);
            }

            $roblox_order = $roblox_order->limit(20)
                ->get()->map(function ($item) {
                    $item->request_id = $item->order->request_id_customer;
                    $item->phone = str_replace(' ', '', $item->phone);
                    $order = Order::query()->where('id',$item->order_id)->first();
                    $order->status = 2;

                    $count = (int)$order->order??0;

                    $order->order = $count + 1;
                    if ($order->order > 2){
                        $message = "[" . Carbon::now() . "] Đơn hàng <b>Gem Unist</b>: "."<b>".$order->request_id_customer."</b> được gọi <b> lần thứ: ".$order->order.'</b> Vui lòng kiểm tra có bug không.Thông báo từ '.config('app.url');
                        Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
                    }

                    $order->save();

                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 2,
                        'content' =>  "Tool đã lấy đơn !",
                    ]);
                    unset($item->type_order);
                    unset($item->order);
                    return $item;
                });

            //Lưu cache.

            Cache::put('CHECK_TOOL_GAME_UNIST_ROBLOX',true,now()->addMinutes(5));

            return response()->json([
                'status' => 1,
                'message' => 'Lấy dữ liệu thành công',
                'data' =>$roblox_order
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('[Lỗi lấy thông tin đơn hàng roblox]' . $e->getMessage());
            return "Lỗi lấy thông tin đơn hàng roblox:".$e->getMessage();
        }
    }

    public function getOrderUnist(Request $request){
        DB::beginTransaction();
        try {

            if ($request->secretkey != config('roblox_bot_unit.sign')) {
                DB::rollBack();
                return response()->json([
                    'status' => 0,
                    'message' => 'Không được phép truy cập',
                ]);
            }

            if (!$request->filled('id')){
                DB::rollBack();
                return response()->json([
                    'status' => 0,
                    'message' => 'Vui long gui id bot',
                ]);
            }

            $id_bot = $request->get('id');
            if (!in_array($id_bot,config('module.service.bot_units_gem'))){
                DB::rollBack();
                return response()->json([
                    'status' => 0,
                    'message' => 'Không tìm thấy id bot',
                ]);
            }

            $units = config('units.'.$id_bot);

            //Kiem tra xem bot co dang xu ly don nao khon

            $check_roblox_order = Roblox_Order::query()
                ->where('bot_handle',$id_bot)
                ->select('id','status','type_order','order_id','uname','phone','bot_handle')
                ->where('status',"dangxuly")
                ->whereIn('phone',$units)
                ->where('type_order',9)
                ->first();

            if (isset($check_roblox_order)){

                $order = Order::query()->where('id',$check_roblox_order->order_id)->first();
                $count = (int)$order->order??0;

                $order->order = $count + 1;
                if ($order->order >= 1){
                    $message = "[" . Carbon::now() . "] Đơn hàng <b>Unist</b>: "."<b>".$order->request_id_customer."</b> được bot ".$id_bot." gọi <b> lần thứ: ".$order->order.'</b> Vui lòng kiểm tra có bug không.Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
                }
                $order->save();

                $check_roblox_order->request_id = $order->request_id_customer;

                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'status' => 2,
                    'content' =>  "Tool đã vào lại lấy đơn lần thứ: ".$count,
                    'title' =>  $id_bot,
                ]);

                //Lưu cache.
                DB::commit();

                unset($check_roblox_order->bot_handle);
                unset($check_roblox_order->id);
                unset($check_roblox_order->type_order);
                unset($check_roblox_order->order);
                unset($check_roblox_order->status);
                unset($check_roblox_order->updated_at);
                unset($check_roblox_order->order_id);

                return response()->json([
                    'status' => 1,
                    'message' => 'Lấy dữ liệu thành công',
                    'data' =>$check_roblox_order
                ]);
            }

            $roblox_order = Roblox_Order::query()
                ->with('order',function ($query){
                    $query->select('id','request_id_customer','idkey')->where('idkey','unist_auto')->where('status',1);
                })
                ->whereHas('order', function ($querysub) use ($request){
                    $querysub->where('idkey','unist_auto')->where('status',1);
                })
                ->whereNull('bot_handle')
                ->whereIn('phone',$units)
                ->select('id','status','type_order','order_id','uname','phone','bot_handle')
                ->where('status',"chuanhan")
                ->where('type_order',9)->lockForUpdate()->first();

            if (!isset($roblox_order)){

                DB::rollBack();
                return response()->json([
                    'status' => 0,
                    'message' => 'Không có đơn hàng!',
                ]);
            }

            $roblox_order->bot_handle = $id_bot;
            $roblox_order->status = 'dangxuly';
            $roblox_order->save();

            $roblox_order->request_id = $roblox_order->order->request_id_customer;

            $order = Order::query()->where('id',$roblox_order->order_id)->first();
            $order->status = 2;
            $order->save();

            //set tiến độ
            OrderDetail::create([
                'order_id' => $order->id,
                'module' => config('module.service-workflow.key'),
                'status' => 2,
                'content' =>  "Tool đã lấy đơn !",
                'title' =>  $id_bot,
            ]);

            //Lưu cache.
            DB::commit();

            unset($roblox_order->bot_handle);
            unset($roblox_order->id);
            unset($roblox_order->type_order);
            unset($roblox_order->order);
            unset($roblox_order->status);
            unset($roblox_order->updated_at);
            unset($roblox_order->order_id);

            $message = "[" . Carbon::now() . "] Tool <b>Dịch vụ unit auto</b>: "."<b>".$id_bot."</b>"." - Đơn hàng: ".$roblox_order->request_id.' Thông báo từ '.config('app.url');
            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_update_unit'));

            Cache::put('CHECK_TOOL_UNIST_ROBLOX',true,now()->addMinutes(5));

            return response()->json([
                'status' => 1,
                'message' => 'Lấy dữ liệu thành công',
                'data' =>$roblox_order
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('[Lỗi lấy thông tin đơn hàng roblox]' . $e->getMessage());
            return "Lỗi lấy thông tin đơn hàng roblox:".$e->getMessage();
        }
    }

    public function getOrderUnistV2(Request $request){


//        $message = "[" . Carbon::now() . "] Tool <b>Dịch vụ unit auto</b>: "."<b>".$request->get('id')."</b>"." - Vào lấy đơn hàng: ";
//        Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_update_unit'));

        DB::beginTransaction();
        try {

            if ($request->secretkey != config('roblox_bot_unit.sign')) {
                DB::rollBack();
                return response()->json([
                    'status' => 0,
                    'message' => 'Không được phép truy cập',
                ]);
            }

            if (!$request->filled('id')){
                DB::rollBack();
                return response()->json([
                    'status' => 0,
                    'message' => 'Vui long gui id bot',
                ]);
            }

            $id_bot = $request->get('id');
            if (!in_array($id_bot,config('module.service.bot_units_gem'))){
                DB::rollBack();
                return response()->json([
                    'status' => 0,
                    'message' => 'Không tìm thấy id bot',
                ]);
            }

            $units = config('units.'.$id_bot);

            //Kiem tra xem bot co dang xu ly don nao khon

            $check_roblox_order = Roblox_Order::query()
                ->where('bot_handle',$id_bot)
                ->select('id','status','ver','type_order','order_id','uname','phone','bot_handle')
                ->where('status',"dangxuly")
                ->whereNotNull('ver')
                ->whereIn('phone',$units)
                ->where('ver',1)
                ->where('type_order',8)
                ->first();

            if (isset($check_roblox_order)){

                $order = Order::query()->where('id',$check_roblox_order->order_id)->first();
                $count = (int)$order->order??0;

                $order->order = $count + 1;
                if ($order->order >= 1){
                    $message = "[" . Carbon::now() . "] Đơn hàng <b>Unist</b>: "."<b>".$order->request_id_customer."</b> được bot ".$id_bot." gọi <b> lần thứ: ".$order->order.'</b> Vui lòng kiểm tra có bug không.Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
                }
                if ($order->order >= 5){
                    $message = "[" . Carbon::now() . "] Đơn hàng <b>Unist</b>: "."<b>".$order->request_id_customer."</b> được bot ".$id_bot." gọi <b> lần thứ: ".$order->order.'</b> Vui lòng kiểm tra lại tool.Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_update_unit'));
                }

                $order->save();

                $check_roblox_order->request_id = $order->request_id_customer;

                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'status' => 2,
                    'content' =>  "Tool đã vào lại lấy đơn lần thứ: ".$count,
                    'title' =>  $id_bot,
                ]);

                //Lưu cache.
                DB::commit();
                unset($check_roblox_order->ver);
                unset($check_roblox_order->bot_handle);
                unset($check_roblox_order->id);
                unset($check_roblox_order->type_order);
                unset($check_roblox_order->order);
                unset($check_roblox_order->status);
                unset($check_roblox_order->updated_at);
                unset($check_roblox_order->order_id);

                return response()->json([
                    'status' => 1,
                    'message' => 'Lấy dữ liệu thành công',
                    'data' =>$check_roblox_order
                ]);
            }

            $roblox_order = Roblox_Order::query()
                ->with('order',function ($query){
                    $query->select('id','request_id_customer','idkey')->where('idkey','gem_unist_auto')->where('status',1);
                })
                ->whereHas('order', function ($querysub) use ($request){
                    $querysub->where('idkey','gem_unist_auto')->where('status',1);
                })
                ->whereNull('bot_handle')
                ->whereIn('phone',$units)
                ->select('id','status','ver','type_order','order_id','uname','phone','bot_handle')
                ->where('status',"chuanhan")
                ->where('ver',1)
                ->where('type_order',8)
                ->lockForUpdate()->first();

            if (!isset($roblox_order)){

                DB::rollBack();
                return response()->json([
                    'status' => 0,
                    'message' => 'Không có đơn hàng!',
                ]);
            }

            $roblox_order->bot_handle = $id_bot;
            $roblox_order->status = 'dangxuly';
            $roblox_order->save();

            $roblox_order->request_id = $roblox_order->order->request_id_customer;

            $order = Order::query()->where('id',$roblox_order->order_id)->first();
            $order->status = 2;
            $order->save();

            //set tiến độ
            OrderDetail::create([
                'order_id' => $order->id,
                'module' => config('module.service-workflow.key'),
                'status' => 2,
                'content' =>  "Tool đã lấy đơn !",
                'title' =>  $id_bot,
            ]);

            //Lưu cache.
            DB::commit();
            unset($roblox_order->ver);
            unset($roblox_order->bot_handle);
            unset($roblox_order->id);
            unset($roblox_order->type_order);
            unset($roblox_order->order);
            unset($roblox_order->status);
            unset($roblox_order->updated_at);
            unset($roblox_order->order_id);

//            $message = "[" . Carbon::now() . "] Tool <b>Dịch vụ unit auto</b>: "."<b>".$id_bot."</b>"." - Đơn hàng: ".$roblox_order->request_id.' Thông báo từ '.config('app.url');
//            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_update_unit'));

            Cache::put('CHECK_TOOL_UNIST_ROBLOX',true,now()->addMinutes(5));

            return response()->json([
                'status' => 1,
                'message' => 'Lấy dữ liệu thành công',
                'data' =>$roblox_order
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('[Lỗi lấy thông tin đơn hàng roblox]' . $e->getMessage());
            return "Lỗi lấy thông tin đơn hàng roblox:".$e->getMessage();
        }
    }

    public function getOrderRobloxPremium(Request $request){

        try {
            if ($request->secretkey != config('roblox_bot_unit.sign')) {
                return response()->json([
                    'status' => 0,
                    'message' => 'không được truy cập!',
                ]);
            }

            $roblox_order = Roblox_Order::query()
                ->with('order',function ($query){
                    $query->select('id','request_id_customer','idkey')->where('idkey','robux_premium_auto')->where('status',1);
                })
                ->whereHas('order', function ($querysub) use ($request){
                    $querysub->where('idkey','robux_premium_auto')->where('status',1);
                })
                ->select('id','status','type_order','order_id','uname','money','phone')
                ->where('status',"chuanhan")
                ->where('type_order',11)
                ->limit(20)
                ->get()->map(function ($item) {

                    $item->request_id = $item->order->request_id_customer;
                    $item->url = config('app.url').'/admin/service-purchase-auto/'.$item->order_id;
//                    $item->money = $item->phone;

                    $order = Order::query()->where('id',$item->order_id)->first();
                    $order->status = 2;
                    $count = (int)$order->order??0;

                    $order->order = $count + 1;

                    if ($order->order > 2){
                        $message = "[" . Carbon::now() . "] Đơn hàng tool robux chinh hang: "."<b>".$order->request_id_customer."</b> được gọi <b> lần thứ: ".$order->order.'</b> Vui lòng kiểm tra có bug không.Thông báo từ '.config('app.url');
                        Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
                    }

                    $order->save();

                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 2,
                        'content' =>  "Tool đã lấy đơn !",
                    ]);

                    unset($item->type_order);
                    unset($item->status);
                    unset($item->id);
                    unset($item->phone);
                    unset($item->order);
                    return $item;
                });

            //Lưu cache.

            Cache::put('CHECK_TOOL_GAME_PET_ROBLOX_PR',true,now()->addMinutes(5));

            return response()->json([
                'status' => 1,
                'message' => 'Lấy dữ liệu thành công',
                'data' =>$roblox_order
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('[Lỗi lấy thông tin đơn hàng roblox]' . $e->getMessage());

            return response()->json([
                'status' => 0,
                'message' => "Lỗi lấy thông tin đơn hàng roblox:".$e->getMessage(),
            ]);
        }

    }

    public function checkOrderRobloxPremium(Request $request){

        try {
            if ($request->secretkey != config('roblox_bot_unit.sign')) {
                return response()->json([
                    'status' => 0,
                    'message' => 'không được truy cập!',
                ]);
            }

            if (!$request->filled('request_id')) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Vui lòng gửi mã đơn!',
                ]);
            }

            $request_id = $request->get('request_id');

            $order = Order::query()->where('request_id_customer',$request_id)->first();

            if (!isset($order)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Không tìm thấy đơn hàng cần kiểm tra!',
                ]);
            }

            $roblox_order = Roblox_Order::query()
                ->where('order_id',$order->id)
                ->select('id','status','type_order','order_id','uname','money','phone')
                ->where('type_order',11)
                ->first();

            if (!isset($roblox_order)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Không tìm thấy đơn hàng cần kiểm tra!',
                ]);
            }
            //Lưu cache.
            $status = 0;
            if ($roblox_order->status == 'chuanhan'){
                if ($order->status == 2){
                    $status = 1;
                }else if ($order->status == 3 || $order->status == 5 || $order->status == 0){
                    $status = 3;
                }else if ($order->status == 4 || $order->status == 9 || $order->status == 11 || $order->status == 7 || $order->status == 12){
                    $status = 2;
                }
            }else if ($roblox_order->status == 'dahuybo'){
                $status = 3;
            }else if ($roblox_order->status == 'danhan'){
                $status = 2;
            }else if ($roblox_order->status == 'thaotacthucong' || $roblox_order->status == 'recharge'){
                $status = 4;
            }

            $resultChange = new \stdClass();
            $resultChange->request_id = $request_id;
            $resultChange->status = $status;

            return response()->json([
                'status' => 1,
                'message' => 'Lấy dữ liệu thành công',
                'data' =>$resultChange
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('[Lỗi lấy thông tin đơn hàng roblox]' . $e->getMessage());

            return response()->json([
                'status' => 0,
                'message' => "Lỗi lấy thông tin đơn hàng roblox:".$e->getMessage(),
            ]);
        }

    }

    public function getAllNick(Request $request)
    {

        // Lấy giá trị của header 'Authorization'
        if (!$request->header('sign')){
            return response()->json([
                'status' => 1,
                'message' => 'Vui lòng gửi mã sign'
            ]);
        }

        $sign = $request->header('sign');

        if ($sign != config('roblox_bot_san.sign')) {
            return response()->json([
                'status' => 0,
                'message' => 'không được truy cập!',
            ]);
        }

        $data = Roblox_Bot_San::query()
            ->where('status',1)
            ->select('id','acc','price','rate','coin','status','id_pengiriman','cookies')
            ->get();

        foreach ($data as $item){
            DB::beginTransaction();
            try {

                $url = '/check-balance';
                $method = "POST";
                $dataSend = array();
                $dataSend['cookies'] = $item->cookies;
                $secretkey = config('proxy.sign');
                $sign = Helpers::encryptProxy($dataSend, $secretkey);
                $dataSend['sign'] = $sign;
                $result = DirectAPI::_checkBalanceRoblox($url, $dataSend, $method);

                if (!$result) {
                    $item->status = 0;
                    $item->save();
                    continue;
                }

                if ($result->status !== 1) {
                    $item->status = 0;
                    $item->save();
                    continue;
                }

                $balance = (int)$result->balance;
                $item->coin = $balance;
                $item->save();

                DB::commit();
            }catch (\Exception $e){
                continue;
            }
        }

        $data = Roblox_Bot_San::query()
            ->where('status',1)
            ->select('id','acc','price','rate','coin','status','id_pengiriman')
            ->get();

        return response()->json([
            'status' => 1,
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công'
        ]);
    }

    public function postNickDetail(Request $request)
    {
        // Lấy giá trị của header 'Authorization'
        $input = file_get_contents('php://input');
        $input = json_decode($input, true);

        // Lấy giá trị của 'sign' từ dữ liệu
        $sign = $input['sign'];

        // Loại bỏ 'sign' khỏi dữ liệu để chuẩn bị tạo chữ ký
        unset($input['sign']);

        $sign_roblox_bot_san = config('roblox_bot_san.sign');
        $hash = Helpers::encryptProxy($input,$sign_roblox_bot_san);
        if (!hash_equals($hash, $sign)) {
            // Nếu chữ ký không khớp, trả về lỗi
            return response()->json([
                'status' => 0,
                'message' => 'Không được phép truy cập'
            ]);
        }

        if (empty($input['id'])){
            return response()->json([
                'status' => 0,
                'message' => 'Vui lòng gửi giá tài khoản',
            ]);
        }

        DB::beginTransaction();
        try {
            $defragment_type = 1;
            $id = $input['id']??'';

            $data = Roblox_Bot_San::query()
                ->where('status',1)
                ->where('id_pengiriman',$id)
                ->first();

            if (!isset($data)){
                return response()->json([
                    'status' => 0,
                    'message' => 'không tìm thấy thông tin tài khoản!',
                ]);
            }

            $url = '/check-balance';
            $method = "POST";
            $dataSend = array();
            $dataSend['cookies'] = $data->cookies;
            $secretkey = config('proxy.sign');
            $sign = Helpers::encryptProxy($dataSend, $secretkey);
            $dataSend['sign'] = $sign;
            $result = DirectAPI::_checkBalanceRoblox($url, $dataSend, $method);

            if (!$result) {
                if (!empty($input['back'])){
                    $message = '';
                    $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                    $message .= "\n";
                    $message .= "<b>[SAN] Tài khoản robux trả die cookie : ".$data->acc." thất bại</b>";
                    $message .= "\n";
                    $message .= "<b>Lý do : ".$result->message."</b>";
                    $message .= "\n";
                    Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_defragment_roblox'));
                }
                return response()->json([
                    'status' => 0,
                    'message' => 'không tìm thấy thông tin tài khoản!',
                ]);
            }

            if ($result->status !== 1) {
                if (!empty($input['back'])){
                    $message = '';
                    $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                    $message .= "\n";
                    $message .= "<b>[SAN] Tài khoản robux trả die cookie : ".$data->acc." thất bại</b>";
                    $message .= "\n";
                    $message .= "<b>Lý do : ".$result->message."</b>";
                    $message .= "\n";
                    Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_defragment_roblox'));
                }

                return response()->json([
                    'status' => 0,
                    'message' => 'không tìm thấy thông tin tài khoản!',
                ]);
            }

            $balance = (int)$result->balance;
            $data->coin = $balance;
            if ($balance < 100){
                $defragment_type = 2;
            }
            if (isset($data->params)){
                $params = json_decode($data->params);
                if (isset($params->get_nick_detail) && count($params->get_nick_detail)){
                    $time = Carbon::now();
                    $get_nick_detail = $params->get_nick_detail;
                    $dataReturn = new \stdClass();
                    $dataReturn->time = $time;
                    $dataReturn->coin = $balance;
                    array_push($get_nick_detail,$dataReturn);
                    $params->get_nick_detail = $get_nick_detail;
                    $data->params = json_encode($params);
                }else{
                    $time = Carbon::now();
                    $get_nick_detail = [];
                    $dataReturn = new \stdClass();
                    $dataReturn->time = $time;
                    $dataReturn->coin = $balance;
                    array_push($get_nick_detail,$dataReturn);
                    $params->get_nick_detail = $get_nick_detail;
                    $data->params = json_encode($params);
                }
            }
            else{
                $time = Carbon::now();
                $get_nick_detail = [];
                $dataReturn = new \stdClass();
                $dataReturn->time = $time;
                $dataReturn->coin = $balance;
                array_push($get_nick_detail,$dataReturn);
                $params = new \stdClass();
                $params->get_nick_detail = $get_nick_detail;
                $data->params = json_encode($params);
            }

            $back = 1;
            if (!empty($input['back'])){
                $back = 2;
                if (isset($data->params)){
                    $params = json_decode($data->params);
                    if (isset($params->defragment_type) && count($params->defragment_type)){
                        $time = Carbon::now();
                        $defragment = $params->defragment_type;
                        $dataReturn = new \stdClass();
                        $dataReturn->time = $time;
                        $dataReturn->coin = $balance;
                        $dataReturn->defragment_type = $defragment_type;
                        array_push($defragment,$dataReturn);
                        $params->defragment_type = $defragment;
                        $data->params = json_encode($params);
                    }
                    else{
                        $time = Carbon::now();
                        $defragment = [];
                        $dataReturn = new \stdClass();
                        $dataReturn->time = $time;
                        $dataReturn->coin = $balance;
                        $dataReturn->defragment_type = $defragment_type;
                        array_push($defragment,$dataReturn);
                        $params->defragment_type = $defragment;
                        $data->params = json_encode($params);
                    }
                }
                else{
                    $time = Carbon::now();
                    $defragment = [];
                    $dataReturn = new \stdClass();
                    $dataReturn->time = $time;
                    $dataReturn->coin = $balance;
                    $dataReturn->defragment_type = $defragment_type;
                    array_push($defragment,$dataReturn);
                    $params = new \stdClass();
                    $params->defragment_type = $defragment;
                    $data->params = json_encode($params);
                }

                if ($balance < 100){
                    // Chuyển trạng thái dồn nick
                    $data->status = 6;
                }
                else{
                    $bot_daily = Roblox_Bot::query()
                        ->where('type_order',1)
                        ->where('acc',$data->acc)
                        ->first();
                    if (isset($bot_daily)){
                        $bot_daily->coin = $balance;
                        $bot_daily->cookies = $data->cookies;
                        $bot_daily->save();
                    }
                    else{
                        $bot_daily = Roblox_Bot::create([
                            'ver' => 1,
                            'acc' => $data->acc,
                            'cookies' => $data->cookies,
                            'id_pengiriman' => $data->id_pengiriman,
                            'status' => 1,
                            'coin' => $balance,
                            'type_order' => 1,
                        ]);
                    }

                    if (isset($data->params)){
                        $params = json_decode($data->params);
                        if (isset($params->daily_120h) && count($params->daily_120h)){
                            $time = Carbon::now();
                            $daily_120h = $params->daily_120h;
                            $dataReturn = new \stdClass();
                            $dataReturn->time = $time;
                            $dataReturn->coin = $balance;
                            $dataReturn->daily = $bot_daily->id;
                            array_push($daily_120h,$dataReturn);
                            $params->daily_120h = $daily_120h;
                            $data->params = json_encode($params);
                        }
                        else{
                            $time = Carbon::now();
                            $daily_120h = [];
                            $dataReturn = new \stdClass();
                            $dataReturn->time = $time;
                            $dataReturn->coin = $balance;
                            $dataReturn->daily = $bot_daily->id;
                            array_push($daily_120h,$dataReturn);
                            $params->daily_120h = $daily_120h;
                            $data->params = json_encode($params);
                        }
                    }
                    else{
                        $time = Carbon::now();
                        $daily_120h = [];
                        $dataReturn = new \stdClass();
                        $dataReturn->time = $time;
                        $dataReturn->coin = $balance;
                        $dataReturn->daily = $bot_daily->id;
                        array_push($daily_120h,$dataReturn);
                        $params = new \stdClass();
                        $params->daily_120h = $daily_120h;
                        $data->params = json_encode($params);
                    }

                    $data->status = 0;
                }
            }

            $data->save();

            $input = array();
            $input['account'] = $data->acc??'';
            $input['price'] = $data->price??'';
            $input['pengiriman_id'] = $data->id_pengiriman??'';
            $input['password'] = $data->password??'';
            $input['rate'] = $data->rate??'';
            $input['coin'] = $data->coin??'';

            // Commit the queries!
            DB::commit();
            if ($back == 2){
                return response()->json([
                    'status' => 1,
                    'username' => config('roblox_bot_san.nick'),
                    'defragment_type' => $defragment_type,
                    'data' => $input,
                    'message' => 'Lấy dữ liệu thành công'
                ]);
            }
            else{
                return response()->json([
                    'status' => 1,
                    'data' => $input,
                    'message' => 'Lấy dữ liệu thành công'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error( $e);
            return response()->json([
                'status' => 0,
                'message' => 'Lỗi code'
            ]);
        }
    }

    public function portNick(Request $request)
    {

        // Lấy giá trị của header 'Authorization'
        $input = file_get_contents('php://input');
        $input = json_decode($input, true);

        // Lấy giá trị của 'sign' từ dữ liệu
        $sign = $input['sign'];

        // Loại bỏ 'sign' khỏi dữ liệu để chuẩn bị tạo chữ ký
        unset($input['sign']);

        $sign_roblox_bot_san = config('roblox_bot_san.sign');
        $hash = Helpers::encryptProxy($input,$sign_roblox_bot_san);
        if (!hash_equals($hash, $sign)) {
            // Nếu chữ ký không khớp, trả về lỗi
            return response()->json([
                'status' => 0,
                'message' => 'Không được phép truy cập'
            ]);
        }

        if (empty($input['account'])){
            return response()->json([
                'status' => 0,
                'message' => 'Vui lòng gửi tên tài khoản',
            ]);
        }

        if (empty($input['cookies'])){
            return response()->json([
                'status' => 0,
                'message' => 'Vui lòng gửi tên cookies',
            ]);
        }

        if (empty($input['pengiriman_id'])){
            return response()->json([
                'status' => 0,
                'message' => 'Vui lòng gửi id đơn hàng',
            ]);
        }

        if (empty($input['password'])){
            return response()->json([
                'status' => 0,
                'message' => 'Vui lòng gửi tài khoản robux',
            ]);
        }

        if (empty($input['rate'])){
            return response()->json([
                'status' => 0,
                'message' => 'Vui lòng gửi tỷ giá',
            ]);
        }

        $account = $input['account']??'';
        $cookies = $input['cookies']??'';
        $price = $input['price']??'';
        $pengiriman_id = $input['pengiriman_id']??'';
        $password = $input['password']??'';
        $rate = $input['rate']??'';
        //Check cookie.
        $url = '/check-balance';
        $method = "POST";
        $dataSend = array();
        $dataSend['cookies'] = $cookies;
        $secretkey = config('proxy.sign');
        $sign = Helpers::encryptProxy($dataSend,$secretkey);
        $dataSend['sign'] = $sign;
        $result = DirectAPI::_checkBalanceRoblox($url,$dataSend,$method);

        if (!$result){
            return response()->json([
                'status' => 0,
                'message' => 'Check thông tin thất bại',
            ]);
        }

        if ($result->status !==1 ){
            return response()->json([
                'status' => 0,
                'message' => $result->message??'Check thông tin thất bại',
            ]);
        }

        $balance = (int)$result->balance;

        $roblox_bot_san_pengiriman = Roblox_Bot_San::query()->where('id_pengiriman',$pengiriman_id)->first();
        if (isset($roblox_bot_san_pengiriman)){
            return response()->json([
                'status' => 0,
                'message' => 'ID đơn hàng đã tồn tại',
            ]);
        }

        $roblox_bot_san_account = Roblox_Bot_San::query()->where('acc',$account)->first();
        if (isset($roblox_bot_san_account)){
            return response()->json([
                'status' => 0,
                'message' => 'Tài khoản roblox đã tồn tại',
            ]);
        }

        $cfpassword = null;
        if (!empty($password)){
            $cfpassword =  Helpers::Encrypt($password, config('roblox_bot_san.encrypt_bot'));
        }

        Roblox_Bot_San::create([
            'acc' => $account,
            'cookies' => $cookies,
            'price' => $price??'',
            'id_pengiriman' => $pengiriman_id,
            'password' => $password??'',
            'rate' => $rate,
            'status' => 1,
            'coin' => $balance,
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Thêm bot thành công'
        ]);
    }

}
