<?php

namespace App\Http\Controllers\Api\V1\AgencyService;

use App;
use App\Http\Controllers\Controller;

use App\Jobs\CallbackOrderRobloxBuyGemPet;
use App\Library\Helpers;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Roblox_Order;
use App\Models\SubItem;
use App\Models\Txns;
use App\Models\User;
use Auth;
use Cache;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;
use Validator;
use App\Models\Setting;

class ServiceAutoListenCallbackController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('throttle:1000,1', ['except' => '']);

    }

    public function getCallback(Request $request){

        //debug thì mở cái này
        $myfile = fopen(storage_path() . "/logs/callback-services-auto-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
        $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
        fwrite($myfile, $txt . "\n");
        fclose($myfile);

        if( $request->get('sign')!= '3857gjfhnj51' ){

            return "Không đúng mã bí mật. Xin thử lại ";

            //debug thì mở cái này
            $myfile = fopen(storage_path() . "/logs/wrong-sign-callback-services-auto-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
            $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
            fwrite($myfile, $txt . "\n");
            fclose($myfile);
        }

        if(strtolower($request->site) == strtolower("naptiendienthoai") || strtolower($request->site) == strtolower("napsms9029")) {
            return $this->SERVICE_HANDLE($request);
        }

    }

    public function  SERVICE_HANDLE(Request $request){


        try {

            $result_final = false;

            DB::transaction(function () use ($request, &$result_final) {
                $result_final = false;

                $status = $request->get('status');
                $message = $request->message;

                //tìm lệnh rút
                $order = Order::where('request_id_customer',$request->tranid)
                    ->where('module', config('module.service-purchase.key'))->lockForUpdate()->first();

                if (!$order) {
                    //debug thì mở cái này
                    $myfile = fopen(storage_path() . "/logs/notfound-services-auto.txt", "a") or die("Unable to open file!");
                    $txt = Carbon::now() . ":" . $request->fullUrl();
                    fwrite($myfile, $txt);
                    fclose($myfile);
                    $result_final = "[Not found]:" . $txt."\n\n";
                    throw new \Exception($result_final,44);
                }
                else{

                    if($order->status==0 || $order->status==3 || $order->status==4 || $order->status==5){

                        $result_final = "Giao dịch đã được xử lý thành công trước đó";
                        throw new \Exception($result_final,44);

                    }
                }
                //tìm user nạp
                $userTransaction=User::where('username',$order->author_id)->lockForUpdate()->firstOrFail();


                if ($userTransaction->checkBalanceValid() == false) {
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => 'Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý'
                    ]);

                }

                if ($userTransaction->balance < $order->price) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 0,
                        'message' => 'Bạn không đủ tiền để thanh toán.Vui lòng nạp thêm tiền vào tài khoản'
                    ]);
                }

                if($request->get('status')==1){
                    // Update lại trạng thái
                    $order->status = 4;//hoàn thành
                    $order->content = "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!";
                    $order->process_at = Carbon::now();
                    $order->save();

                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 4,
                        'content' =>  "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!",
                    ]);
                    $result_final=true;

                }
                elseif($request->get('status')==3){
                    //hoàn tiền cho user
                    $userTransaction->balance = $userTransaction->balance + $order->price;
                    $userTransaction->balance_out = $userTransaction->balance_out + $order->price;
                    $userTransaction->save();
                    // Update lại trạng thái
                    $order->status = 5;
                    $order->content = "Giao dịch thất bại. Hệ thống đã hoàn tiền vào tài khoản, Bạn vui lòng mua lại!";
                    $order->process_at = Carbon::now();
                    $order->save();

                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 5,
                        'content' =>  "Giao dịch thất bại. Hệ thống đã hoàn tiền vào tài khoản, Bạn vui lòng mua lại!",
                    ]);
                    $result_final=true;

                }

            });
            if ($result_final === true) {

                return 'Xử lý giao dịch thành công #' . $request->tranid;
            }
            else{
                return '[Lỗi] Xử lý thất bại#' . $request->tranid;
            }


        }catch (\Exception $e) {
            if($e->getCode()==44){
                return $e->getMessage();
            }

            $myfile = fopen(storage_path() . "/logs/loicallback-services-auto.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now() . " - " . 'id:' . $request->id . ' - ' . 'status:' . $request->get('status') . ' - ' . 'amount:' . $request->amount;
            fwrite($myfile, $txt);
            fclose($myfile);

            Log::error("Lỗi callback services-auto: ".$request->tranid." - ".$e);

            return 'Có lỗi phát sinh.Xin vui lòng thử lại !';
        }
    }

    //List Callback ROBLOX call về
    public function getCallbackBotRoblox(Request $request)
    {
        Cache::put('CHECK_TOOL_GAME_PET_ROBLOX',true,now()->addMinutes(5));

        //lưu log gọi curl
        $path = storage_path() ."/logs/services-auto/";
        $filename=$path."listen_callback_bot_roblox".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }

        if ($request->get('image')){

            // Giả sử base64Image được gửi lên từ form
            $base64Image = $request->get('image');

            $formattedDate = Carbon::now()->format('d-m-Y');

            $fileName = 'images/daily/service/'.$formattedDate.'/' . uniqid() . '.png'; // Tạo tên file duy nhất
            $url = App\Library\MediaHelpers::saveBase64ImageToS3($base64Image, $fileName);
            $md5Hash = md5($request->get('image'));
            $data = new \stdClass();
            $data->secretkey = $request->get('secretkey');
            $data->request_id = $request->get('request_id');
            $data->status = $request->get('status');
            $data->message = $request->get('message');
            $data->md5Hash = $md5Hash;
            $request['md5Hash'] = $md5Hash;
            if (isset($url)){
                $data->image = $url;
                $request['imageS3'] = $url;
            }else{
                $data->image = "Ảnh lỗi";
            }

            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($data);
            \File::append($filename,$contentText."\n");
        }else{
            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
            \File::append($filename,$contentText."\n");
        }

        if ($request->get('secretkey') != config('roblox_bot.sign')) {
            return "Không đúng mã bí mật. Xin thử lại ";
        }


        return $this->handleRobloxBot($request);
    }

    public function handleRobloxBot(Request $request)
    {

        DB::beginTransaction();
        try {

            $order = Order::query()
                ->where(function($q){
                    $q->orWhere('status', '=',1);
                    $q->orWhere('status', '=',2);
                })
                ->where('module',config('module.service-purchase.key'))
                ->where('idkey','roblox_gem_pet')
                ->where('request_id_customer',$request->get('request_id'))
                ->lockForUpdate()
                ->first();

            if(!$order){
                return "Đơn hàng đã xử lý trước đó";
            }

            $data = Roblox_Order::query()->with('order')
                ->where('order_id',$order->id)
                ->where('type_order',4)->where('status','chuanhan')
                ->lockForUpdate()->first();

            if(!$data){
                return "Không tìm thấy đơn hàng Roblox Order";
            }
            $image = null;
            if ($request->filled('status')){
                if($request->get('status')== 1 ){
                    //cập nhật trạng thái của purchase
                    $order->status = 4;
                    $order->process_at = Carbon::now();//thời gian xác nhận đơn hàng
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 4,
                        'content' => "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!",
                    ]);

                    //tiến độ lưu ảnh.
                    if ($request->get('imageS3')){
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 85,
                            'idkey' => $request->get('md5Hash'),
                            'content' => $request->get('imageS3'),
                        ]);
                        $image = $request->get('imageS3');
                    }

                    $data->status = 'danhan';
                    $data->save();

                    DB::commit();

                    if ($request->get('imageS3')){
                        //Kiểm tra trùng ảnh.
                        $order_image = OrderDetail::query()
                            ->where('module',config('module.service-workflow.key'))
                            ->select('id','idkey','status','content','order_id')
                            ->where('order_id','!=',$order->id)
                            ->where('idkey',$request->get('md5Hash'))
                            ->where('status',85)
                            ->whereDate('created_at', Carbon::today()) // Kiểm tra ngày hôm nay
                            ->first();

                        if (isset($order_image)){

                            $message="[" . Carbon::now() . "] Lỗi trùng ảnh: đơn hàng ".$order->id." trùng ảnh đơn hàng:".$order_image->order_id.' Thông báo từ '.config('app.url');
                            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
                        }
                    }

                }
                else if($request->get('status') == 0 ){
                    if ($request->filled('message')){
                        if ($request->get('message')){
                            $order->status = 5;
                            $order->save();
                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workflow.key'),
                                'status' => 5,
                                'content' => $request->get('message'),
                            ]);

                            $data->status = 'dahuybo';
                            $data->save();
                            //tìm user nạp
                            $userTransaction = User::where('id', $order->author_id)->lockForUpdate()->firstOrFail();

                            //hoàn tiền cho user
                            $userTransaction->balance = $userTransaction->balance + $order->price;
                            $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $order->price;
                            $userTransaction->save();

                            $order->txns()->create([
                                'trade_type' => 'refund',//Thanh toán dịch vụ
                                'is_add' => '1',//Cộng tiền
                                'user_id' => $userTransaction->id,
                                'amount' => $order->price,
                                'real_received_amount' => $order->price,
                                'last_balance' => $userTransaction->balance,
                                'description' => "Hoàn tiền dịch vụ #" . $order->id,
                                'ip' => $request->getClientIp(),
                                'order_id' => $order->id,
                                'status' => 1
                            ]);
                        }
                        else{
                            $order->status = 9;
                            $order->save();
                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workflow.key'),
                                'status' => 9,
                                'content' => 'Không có message trả về',
                            ]);

                            $data->status = 'thaotacthucong';
                            $data->save();
                        }
                    }
                    else{
                        $order->status = 9;
                        $order->save();
                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 9,
                            'content' => 'Không có message trả về',
                        ]);

                        $data->status = 'thaotacthucong';
                        $data->save();
                    }
                }
                else if ($request->get('status') == 2){

                    $message="[" . Carbon::now() . "] Lỗi trong quá trình giao dịch: ".$request->get('message').' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 9,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();

                }
                else if ($request->get('status') == 3){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();

                    $message="[" . Carbon::now() . "] Bot roblox không đủ số dư. Vui lòng nạp thêm tiền".' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                }
                else if ($request->get('status') == 5){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message')??'',
                    ]);

                    $data->status = 'recharge';
                    $data->save();

                    $message="[" . Carbon::now() . "] error: Run out of Battery. Check it.".' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                }
                else{
                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 9,
                        'content' => 'Không có trạng thái trả về',
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();
                }
            }
            else{
                $order->status = 9;
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'status' => 9,
                    'content' => 'Không có trạng thái trả về',
                ]);

                $data->status = 'thaotacthucong';
                $data->save();
            }

            $data->save();
            DB::commit();

            //callback trả shop
            if($order->status==4 || $order->status==5){
                $statusBot = $data->status;
                $messageDaily = $request->get('message')??'';
                if($order->url!=""){
                    $this->dispatch(new CallbackOrderRobloxBuyGemPet($order,$statusBot,$messageDaily,$image??''));
                }
            }

            return 'Xử lý giao dịch thành công #' . $request->request_id;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return "Lỗi:".$e->getMessage();
        }

    }

    //List Callback ROBLOX call về
    public function getCallbackBotRobloxPremium(Request $request)
    {
        Cache::put('CHECK_TOOL_GAME_PET_ROBLOX_PR',true,now()->addMinutes(5));

        //lưu log gọi curl
        $path = storage_path() ."/logs/services-auto/";
        $filename=$path."listen_callback_bot_roblox_pr".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }

        if ($request->get('image')){

            // Giả sử base64Image được gửi lên từ form
            $base64Image = $request->get('image');

            $formattedDate = Carbon::now()->format('d-m-Y');

            $fileName = 'images/daily/service/'.$formattedDate.'/' . uniqid() . '.png'; // Tạo tên file duy nhất
            $url = App\Library\MediaHelpers::saveBase64ImageToS3($base64Image, $fileName);
            $md5Hash = md5($request->get('image'));
            $data = new \stdClass();
            $data->secretkey = $request->get('secretkey');
            $data->request_id = $request->get('request_id');
            $data->status = $request->get('status');
            $data->message = $request->get('message');
            $data->md5Hash = $md5Hash;
            $request['md5Hash'] = $md5Hash;
            if (isset($url)){
                $data->image = $url;
                $request['imageS3'] = $url;
            }else{
                $data->image = "Ảnh lỗi";
            }

            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($data);
            \File::append($filename,$contentText."\n");
        }else{
            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
            \File::append($filename,$contentText."\n");
        }

        if ($request->get('secretkey') != config('roblox_bot_unit.sign')) {

            return response()->json([
                'status' => 0,
                'message' => "Không đúng mã bí mật. Xin thử lại",
            ]);
        }


        return $this->handleRobloxBotPremium($request);
    }

    public function handleRobloxBotPremium(Request $request)
    {

        DB::beginTransaction();
        try {

            $order = Order::query()
                ->where(function($q){
                    $q->orWhere('status', '=',1);
                    $q->orWhere('status', '=',2);
                })
                ->where('module',config('module.service-purchase.key'))
                ->where('idkey','robux_premium_auto')
                ->where('request_id_customer',$request->get('request_id'))
                ->lockForUpdate()
                ->first();

            if(!$order){

                return response()->json([
                    'status' => 0,
                    'message' => "Đơn hàng đã xử lý trước đó",
                ]);
            }

            $data = Roblox_Order::query()->with('order')
                ->where('order_id',$order->id)
                ->where('type_order',11)->where('status','chuanhan')
                ->lockForUpdate()->first();

            if(!$data){

                return response()->json([
                    'status' => 0,
                    'message' => "Không tìm thấy đơn hàng Roblox Order",
                ]);
            }
            $image = null;
            if ($request->filled('status')){
                if($request->get('status')== 1 ){
                    //cập nhật trạng thái của purchase
                    $order->status = 4;
                    $order->process_at = Carbon::now();//thời gian xác nhận đơn hàng
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 4,
                        'content' => "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!",
                    ]);

                    //tiến độ lưu ảnh.
                    if ($request->get('imageS3')){
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 85,
                            'idkey' => $request->get('md5Hash'),
                            'content' => $request->get('imageS3'),
                        ]);
                        $image = $request->get('imageS3');
                    }

                    $data->status = 'danhan';
                    $data->save();

                    DB::commit();

                    if ($request->get('imageS3')){
                        //Kiểm tra trùng ảnh.
                        $order_image = OrderDetail::query()
                            ->where('module',config('module.service-workflow.key'))
                            ->select('id','idkey','status','content','order_id')
                            ->where('order_id','!=',$order->id)
                            ->where('idkey',$request->get('md5Hash'))
                            ->where('status',85)
                            ->whereDate('created_at', Carbon::today()) // Kiểm tra ngày hôm nay
                            ->first();

                        if (isset($order_image)){

                            $message="[" . Carbon::now() . "] Lỗi trùng ảnh: đơn hàng robux chính hãng".$order->id." trùng ảnh đơn hàng:".$order_image->order_id.' Thông báo từ '.config('app.url');
                            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
                        }
                    }

                }
                else if($request->get('status') == 0 ){
                    $order->status = 5;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 5,
                        'content' => $request->get('message')??'',
                    ]);

                    $data->status = 'dahuybo';
                    $data->save();
                    //tìm user nạp
                    $userTransaction = User::where('id', $order->author_id)->lockForUpdate()->firstOrFail();

                    //hoàn tiền cho user
                    $userTransaction->balance = $userTransaction->balance + $order->price;
                    $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $order->price;
                    $userTransaction->save();

                    $order->txns()->create([
                        'trade_type' => 'refund',//Thanh toán dịch vụ
                        'is_add' => '1',//Cộng tiền
                        'user_id' => $userTransaction->id,
                        'amount' => $order->price,
                        'real_received_amount' => $order->price,
                        'last_balance' => $userTransaction->balance,
                        'description' => "Hoàn tiền dịch vụ #" . $order->id,
                        'ip' => $request->getClientIp(),
                        'order_id' => $order->id,
                        'status' => 1
                    ]);
                }
                else if ($request->get('status') == 2){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();
                }
                else{
                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 9,
                        'content' => 'Không có trạng thái trả về',
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();
                }
            }
            else{
                $order->status = 9;
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'status' => 9,
                    'content' => 'Không có trạng thái trả về',
                ]);

                $data->status = 'thaotacthucong';
                $data->save();
            }

            $data->save();
            DB::commit();

            //callback trả shop
            if($order->status==4 || $order->status==5){
                $statusBot = $data->status;
                $messageDaily = $request->get('message')??'';
                if($order->url!=""){
                    $this->dispatch(new CallbackOrderRobloxBuyGemPet($order,$statusBot,$messageDaily,$image??''));
                }
            }

            return response()->json([
                'status' => 1,
                'message' => 'Xử lý giao dịch thành công #' . $request->request_id,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return response()->json([
                'status' => 0,
                'message' => "Lỗi:".$e->getMessage(),
            ]);
        }

    }

    public function getCallbackBotHugePsxRoblox(Request $request)
    {
        Cache::put('CHECK_TOOL_GAME_HUGE_PSX_ROBLOX',true,now()->addMinutes(5));

        //lưu log gọi curl
        $path = storage_path() ."/logs/services-auto/";
        $filename=$path."listen_callback_bot_roblox".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }

        if ($request->get('image')){

            // Giả sử base64Image được gửi lên từ form
            $base64Image = $request->get('image');

            $formattedDate = Carbon::now()->format('d-m-Y');

            $fileName = 'images/daily/service/'.$formattedDate.'/' . uniqid() . '.png'; // Tạo tên file duy nhất
            $url = App\Library\MediaHelpers::saveBase64ImageToS3($base64Image, $fileName);
            $md5Hash = md5($request->get('image'));
            $data = new \stdClass();
            $data->secretkey = $request->get('secretkey');
            $data->request_id = $request->get('request_id');
            $data->status = $request->get('status');
            $data->message = $request->get('message');
            $data->md5Hash = $md5Hash;
            $request['md5Hash'] = $md5Hash;
            if (isset($url)){
                $data->image = $url;
                $request['imageS3'] = $url;
            }else{
                $data->image = "Ảnh lỗi";
            }

            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($data);
            \File::append($filename,$contentText."\n");
        }else{
            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
            \File::append($filename,$contentText."\n");
        }

        if ($request->get('secretkey') != config('roblox_bot.sign')) {
            return "Không đúng mã bí mật. Xin thử lại ";
        }


        return $this->handleRobloxBotHugeRoblox($request);
    }

    public function handleRobloxBotHugeRoblox(Request $request)
    {

        DB::beginTransaction();
        try {

            $order = Order::query()
                ->where(function($q){
                    $q->orWhere('status', '=',1);
                    $q->orWhere('status', '=',2);
                })
                ->where('module',config('module.service-purchase.key'))
                ->where('idkey','huge_psx_auto')
                ->where('request_id_customer',$request->get('request_id'))
                ->lockForUpdate()
                ->first();

            if(!$order){
                return "Đơn hàng đã xử lý trước đó";
            }

            $data = Roblox_Order::query()->with('order')->where('order_id',$order->id)
                ->where('type_order',5)->where('status','chuanhan')
                ->lockForUpdate()->first();

            if(!$data){
                return "Không tìm thấy đơn hàng Roblox Order";
            }
            $image = null;
            if ($request->filled('status')){
                if($request->get('status')== 1 ){
                    //cập nhật trạng thái của purchase
                    $order->status = 4;
                    $order->process_at = Carbon::now();//thời gian xác nhận đơn hàng
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 4,
                        'content' => "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!",
                    ]);

                    //tiến độ lưu ảnh.
                    if ($request->get('imageS3')){
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 85,
                            'idkey' => $request->get('md5Hash'),
                            'content' => $request->get('imageS3'),
                        ]);
                        $image = $request->get('imageS3');
                    }

                    $data->status = 'danhan';
                    $data->save();

                    DB::commit();

                    if ($request->get('imageS3')){
                        //Kiểm tra trùng ảnh.
                        $order_image = OrderDetail::query()
                            ->where('module',config('module.service-workflow.key'))
                            ->select('id','idkey','status','content','order_id')
                            ->where('order_id','!=',$order->id)
                            ->where('idkey',$request->get('md5Hash'))
                            ->where('status',85)
                            ->whereDate('created_at', Carbon::today()) // Kiểm tra ngày hôm nay
                            ->first();

                        if (isset($order_image)){

                            $message="[" . Carbon::now() . "] Lỗi trùng ảnh: đơn hàng huge psx ".$order->id." trùng ảnh đơn hàng:".$order_image->order_id.' Thông báo từ '.config('app.url');
                            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
                        }
                    }

                }
                else if($request->get('status') == 0 ){
                    if ($request->filled('message')){
                        if ($request->get('message')){
                            $order->status = 5;
                            $order->save();
                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workflow.key'),
                                'status' => 5,
                                'content' => $request->get('message'),
                            ]);

                            $data->status = 'dahuybo';
                            $data->save();
                            //tìm user nạp
                            $userTransaction = User::where('id', $order->author_id)->lockForUpdate()->firstOrFail();

                            //hoàn tiền cho user
                            $userTransaction->balance = $userTransaction->balance + $order->price;
                            $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $order->price;
                            $userTransaction->save();

                            $order->txns()->create([
                                'trade_type' => 'refund',//Thanh toán dịch vụ
                                'is_add' => '1',//Cộng tiền
                                'user_id' => $userTransaction->id,
                                'amount' => $order->price,
                                'real_received_amount' => $order->price,
                                'last_balance' => $userTransaction->balance,
                                'description' => "Hoàn tiền dịch vụ #" . $order->id,
                                'ip' => $request->getClientIp(),
                                'order_id' => $order->id,
                                'status' => 1
                            ]);
                        }
                        else{
                            $order->status = 9;
                            $order->save();
                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workflow.key'),
                                'status' => 9,
                                'content' => 'Không có message trả về',
                            ]);

                            $data->status = 'thaotacthucong';
                            $data->save();
                        }
                    }
                    else{
                        $order->status = 9;
                        $order->save();
                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 9,
                            'content' => 'Không có message trả về',
                        ]);

                        $data->status = 'thaotacthucong';
                        $data->save();
                    }
                }
                else if ($request->get('status') == 2){

                    $message="[" . Carbon::now() . "] Lỗi trong quá trình giao dịch huge psx: ".$request->get('message').' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_huge_psx_roblox'));

                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 9,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();

                }
                else if ($request->get('status') == 3){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();
                    $message = "[" . Carbon::now() . "] Không tìm thấy pet huge psx: "."<b>".$data->phone."</b>".' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_huge_psx_roblox'));

                }
                else{
                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 9,
                        'content' => 'Không có trạng thái trả về',
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();
                }
            }
            else{
                $order->status = 9;
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'status' => 9,
                    'content' => 'Không có trạng thái trả về',
                ]);

                $data->status = 'thaotacthucong';
                $data->save();
            }

            $data->save();

            DB::commit();

            //callback trả shop
            if($order->status==4 || $order->status==5){
                $statusBot = $data->status;
                $messageDaily = $request->get('message')??'';
                if($order->url!=""){
                    $this->dispatch(new CallbackOrderRobloxBuyGemPet($order,$statusBot,$messageDaily,$image??''));
                }
            }

            return 'Xử lý giao dịch thành công #' . $request->request_id;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return "Lỗi:".$e->getMessage();
        }

    }

    public function getCallbackBotGempet99(Request $request)
    {
        Cache::put('CHECK_TOOL_GAME_PET99_ROBLOX',true,now()->addMinutes(5));

        //lưu log gọi curl
        $path = storage_path() ."/logs/services-auto/";
        $filename=$path."listen_callback_bot_gem99_roblox".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }

        if ($request->get('image')){
            try {
                // Giả sử base64Image được gửi lên từ form
                $base64Image = $request->get('image');

                $formattedDate = Carbon::now()->format('d-m-Y');

                $fileName = 'images/daily/service/'.$formattedDate.'/' . uniqid() . '.png'; // Tạo tên file duy nhất
                $url = App\Library\MediaHelpers::saveBase64ImageToS3($base64Image, $fileName);
                $md5Hash = md5($request->get('image'));
                $data = new \stdClass();
                $data->secretkey = $request->get('secretkey');
                $data->request_id = $request->get('request_id');
                $data->status = $request->get('status');
                $data->message = $request->get('message');
                $data->md5Hash = $md5Hash;
                $request['md5Hash'] = $md5Hash;
                if (isset($url)){
                    $data->image = $url;
                    $request['imageS3'] = $url;
                }else{
                    $data->image = "Ảnh lỗi";
                }

                $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($data);
                \File::append($filename,$contentText."\n");
            } catch (\Exception $e) {
                $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
                \File::append($filename,$contentText."\n");
            }
        }else{
            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
            \File::append($filename,$contentText."\n");
        }

        if ($request->get('secretkey') != config('roblox_bot.sign')) {
            return "Không đúng mã bí mật. Xin thử lại ";
        }

        return $this->handleRobloxBotGempet99($request);
    }

    public function handleRobloxBotGempet99(Request $request)
    {

        DB::beginTransaction();
        try {

            $order = Order::query()
                ->where(function($q){
                    $q->orWhere('status', '=',1);
                    $q->orWhere('status', '=',2);
                })
                ->where('module',config('module.service-purchase.key'))
                ->where('idkey','pet_99_auto')
                ->where('request_id_customer',$request->get('request_id'))
                ->lockForUpdate()
                ->first();

            if(!$order){
                return "Đơn hàng đã xử lý trước đó";
            }

            $data = Roblox_Order::query()->with('order')
                ->where('order_id',$order->id)
                ->where('type_order',6)->where('status','chuanhan')
                ->lockForUpdate()->first();

            if(!$data){
                return "Không tìm thấy đơn hàng Roblox Order";
            }
            $image = null;
            if ($request->filled('status')){
                if($request->get('status')== 1 ){
                    //cập nhật trạng thái của purchase
                    $order->status = 4;
                    $order->process_at = Carbon::now();//thời gian xác nhận đơn hàng
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 4,
                        'content' => "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!",
                    ]);

                    //tiến độ lưu ảnh.
                    if ($request->get('imageS3')){
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 85,
                            'idkey' => $request->get('md5Hash'),
                            'content' => $request->get('imageS3'),
                        ]);

                        $image = $request->get('imageS3');
                    }

                    $data->status = 'danhan';
                    $data->save();

                    DB::commit();

                    if ($request->get('imageS3')){
                        //Kiểm tra trùng ảnh.
                        $order_image = OrderDetail::query()
                            ->where('module',config('module.service-workflow.key'))
                            ->select('id','idkey','status','content','order_id')
                            ->where('order_id','!=',$order->id)
                            ->where('idkey',$request->get('md5Hash'))
                            ->where('status',85)
                            ->whereDate('created_at', Carbon::today()) // Kiểm tra ngày hôm nay
                            ->first();

                        if (isset($order_image)){

                            $path = storage_path() ."/logs/services-auto/";
                            $filename=$path."trung_anh_bot_gem99_roblox".Carbon::now()->format('Y-m-d').".txt";
                            if(!\File::exists($path)){
                                \File::makeDirectory($path, $mode = "0755", true, true);
                            }

                            $message="[" . Carbon::now() . "] Gem pet 99 Lỗi trùng ảnh: đơn hàng ".$order->id." trùng ảnh đơn hàng:".$order_image->order_id.' Thông báo từ '.config('app.url');
                            \File::append($filename,$message."\n");
//                            $message="[" . Carbon::now() . "] Gem pet 99 Lỗi trùng ảnh: đơn hàng ".$order->id." trùng ảnh đơn hàng:".$order_image->order_id.' Thông báo từ '.config('app.url');
//                            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
                        }
                    }

                }
                else if($request->get('status') == 0 ){
                    if ($request->filled('message')){
                        if ($request->get('message')){
                            $order->status = 5;
                            $order->save();
                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workflow.key'),
                                'status' => 5,
                                'content' => $request->get('message'),
                            ]);

                            $data->status = 'dahuybo';
                            $data->save();
                            //tìm user nạp
                            $userTransaction = User::where('id', $order->author_id)->lockForUpdate()->firstOrFail();

                            //hoàn tiền cho user
                            $userTransaction->balance = $userTransaction->balance + $order->price;
                            $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $order->price;
                            $userTransaction->save();

                            $order->txns()->create([
                                'trade_type' => 'refund',//Thanh toán dịch vụ
                                'is_add' => '1',//Cộng tiền
                                'user_id' => $userTransaction->id,
                                'amount' => $order->price,
                                'real_received_amount' => $order->price,
                                'last_balance' => $userTransaction->balance,
                                'description' => "Hoàn tiền dịch vụ #" . $order->id,
                                'ip' => $request->getClientIp(),
                                'order_id' => $order->id,
                                'status' => 1
                            ]);
                        }
                        else{
                            $order->status = 9;
                            $order->save();
                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workflow.key'),
                                'status' => 9,
                                'content' => 'Không có message trả về',
                            ]);

                            $data->status = 'thaotacthucong';
                            $data->save();

                            if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                                $order_global_id = $order->request_id_customer;
                                $sticky_global = "Không có message trả về";
                                $tele_global_service = $order->title??'';
                                $message_global = '';
                                $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                                $message_global .= "\n";
                                $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG";
                                $message_global .= "\n";
                                $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                                $message_global .= "\n";
                                $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                                $message_global .= "\n";
                                $message_global .= '- Thông báo từ: '.config('app.url');
                                $message_global .= "\n";

                                Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                            }
                        }
                    }
                    else{
                        $order->status = 9;
                        $order->save();
                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 9,
                            'content' => 'Không có message trả về',
                        ]);

                        $data->status = 'thaotacthucong';
                        $data->save();

                        if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                            $order_global_id = $order->request_id_customer??'';
                            $sticky_global = "Không có message trả về";
                            $tele_global_service = $order->title??'';
                            $message_global = '';
                            $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                            $message_global .= "\n";
                            $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG";
                            $message_global .= "\n";
                            $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                            $message_global .= "\n";
                            $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                            $message_global .= "\n";
                            $message_global .= '- Thông báo từ: '.config('app.url');
                            $message_global .= "\n";

                            Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                        }
                    }
                }
                else if ($request->get('status') == 2){

                    $message="[" . Carbon::now() . "] Lỗi trong quá trình giao dịch gem pet 99: ".$request->get('message').' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 9,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = $request->get('message')??"Lỗi trong quá trình giao dịch gem pet 99";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }

                }
                else if ($request->get('status') == 3){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();

                    $message="[" . Carbon::now() . "] Bot roblox gem pet 99 không đủ số dư. Vui lòng nạp thêm tiền".' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                    $message="[" . Carbon::now() . "] Bot roblox gem pet 99 không đủ số dư. Vui lòng nạp thêm tiền".' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox_pet99_san'));

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = $request->get('message')??"BOT KHÔNG ĐỦ SỐ DƯ";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - KẾT NỐI NCC THẤT BẠI";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }
                }
                else if ($request->get('status') == 5){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();

                    $message="[" . Carbon::now() . "] Error: Run out of Battery. Check it.".' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = $request->get('message')??"Error: Run out of Battery. Check it";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - KẾT NỐI NCC THẤT BẠI.";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }

                }
                else if ($request->get('status') == 6){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();

                    $message = "[" . Carbon::now() . "] Warning: Bot gem pet99 hết gem vui lòng nạp thêm gem để tool tiếp tục xử lý.".' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = $request->get('message')??"Bot hết gem";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - KẾT NỐI NCC THẤT BẠI.";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }

                }
                else{
                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 9,
                        'content' => 'Không có trạng thái trả về',
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = "Không có trạng thái trả về";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG.";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }
                }
            }
            else{
                $order->status = 9;
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'status' => 9,
                    'content' => 'Không có trạng thái trả về',
                ]);

                $data->status = 'thaotacthucong';
                $data->save();

                if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                    $order_global_id = $order->request_id_customer??'';
                    $sticky_global = "Không có trạng thái trả về";
                    $tele_global_service = $order->title??'';
                    $message_global = '';
                    $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                    $message_global .= "\n";
                    $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG.";
                    $message_global .= "\n";
                    $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                    $message_global .= "\n";
                    $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                    $message_global .= "\n";
                    $message_global .= '- Thông báo từ: '.config('app.url');
                    $message_global .= "\n";

                    Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                }
            }

            $data->save();
            DB::commit();

            //callback trả shop
            if($order->status==4 || $order->status==5){
                $statusBot = $data->status;
                $messageDaily = $request->get('message')??'';
                if($order->url!=""){
                    $this->dispatch(new CallbackOrderRobloxBuyGemPet($order,$statusBot,$messageDaily,$image??''));
                }
            }

            return 'Xử lý giao dịch thành công #' . $request->request_id;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return "Lỗi:".$e->getMessage();
        }

    }

    public function getCallbackBotItemPetGo(Request $request)
    {
        Cache::put('CHECK_TOOL_GAME_PET99_ROBLOX',true,now()->addMinutes(5));

        //lưu log gọi curl
        $path = storage_path() ."/logs/services-auto/";
        $filename=$path."listen_callback_bot_item_pet_go_roblox".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }

        if ($request->get('image')){
            try {
                // Giả sử base64Image được gửi lên từ form
                $base64Image = $request->get('image');

                $formattedDate = Carbon::now()->format('d-m-Y');

                $fileName = 'images/daily/service/'.$formattedDate.'/' . uniqid() . '.png'; // Tạo tên file duy nhất
                $url = App\Library\MediaHelpers::saveBase64ImageToS3($base64Image, $fileName);
                $md5Hash = md5($request->get('image'));
                $data = new \stdClass();
                $data->secretkey = $request->get('secretkey');
                $data->request_id = $request->get('request_id');
                $data->status = $request->get('status');
                $data->message = $request->get('message');
                $data->md5Hash = $md5Hash;
                $request['md5Hash'] = $md5Hash;
                if (isset($url)){
                    $data->image = $url;
                    $request['imageS3'] = $url;
                }else{
                    $data->image = "Ảnh lỗi";
                }

                $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($data);
                \File::append($filename,$contentText."\n");
            } catch (\Exception $e) {
                $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
                \File::append($filename,$contentText."\n");
            }
        }else{
            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
            \File::append($filename,$contentText."\n");
        }

        if ($request->get('secretkey') != config('roblox_bot.sign')) {
            return "Không đúng mã bí mật. Xin thử lại ";
        }

        return $this->handleRobloxBotItemPetGo($request);
    }

    public function handleRobloxBotItemPetGo(Request $request)
    {

        DB::beginTransaction();
        try {

            $order = Order::query()
                ->where(function($q){
                    $q->orWhere('status', '=',1);
                    $q->orWhere('status', '=',2);
                })
                ->where('module',config('module.service-purchase.key'))
                ->where('idkey','item_pet_go_auto')
                ->where('request_id_customer',$request->get('request_id'))
                ->lockForUpdate()
                ->first();

            if(!$order){
                return "Đơn hàng đã xử lý trước đó";
            }

            $data = Roblox_Order::query()->with('order')
                ->where('order_id',$order->id)
                ->where('type_order',12)->where('status','chuanhan')
                ->lockForUpdate()->first();

            if(!$data){
                return "Không tìm thấy đơn hàng Roblox Order";
            }
            $image = null;
            if ($request->filled('status')){
                if($request->get('status')== 1 ){
                    //cập nhật trạng thái của purchase
                    $order->status = 4;
                    $order->process_at = Carbon::now();//thời gian xác nhận đơn hàng
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 4,
                        'content' => "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!",
                    ]);

                    //tiến độ lưu ảnh.
                    if ($request->get('imageS3')){
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 85,
                            'idkey' => $request->get('md5Hash'),
                            'content' => $request->get('imageS3'),
                        ]);

                        $image = $request->get('imageS3');
                    }

                    $data->status = 'danhan';
                    $data->save();

                    DB::commit();

                    if ($request->get('imageS3')){
                        //Kiểm tra trùng ảnh.
                        $order_image = OrderDetail::query()
                            ->where('module',config('module.service-workflow.key'))
                            ->select('id','idkey','status','content','order_id')
                            ->where('order_id','!=',$order->id)
                            ->where('idkey',$request->get('md5Hash'))
                            ->where('status',85)
                            ->whereDate('created_at', Carbon::today()) // Kiểm tra ngày hôm nay
                            ->first();

                        if (isset($order_image)){

                            $path = storage_path() ."/logs/services-auto/";
                            $filename=$path."trung_anh_bot_item_pet_go_roblox".Carbon::now()->format('Y-m-d').".txt";
                            if(!\File::exists($path)){
                                \File::makeDirectory($path, $mode = "0755", true, true);
                            }

                            $message="[" . Carbon::now() . "] Item pet go Lỗi trùng ảnh: đơn hàng ".$order->id." trùng ảnh đơn hàng:".$order_image->order_id.' Thông báo từ '.config('app.url');
                            \File::append($filename,$message."\n");
                        }
                    }

                }
                else if($request->get('status') == 0 ){
                    if ($request->filled('message')){
                        if ($request->get('message')){
                            $order->status = 5;
                            $order->save();
                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workflow.key'),
                                'status' => 5,
                                'content' => $request->get('message'),
                            ]);

                            $data->status = 'dahuybo';
                            $data->save();
                            //tìm user nạp
                            $userTransaction = User::where('id', $order->author_id)->lockForUpdate()->firstOrFail();

                            //hoàn tiền cho user
                            $userTransaction->balance = $userTransaction->balance + $order->price;
                            $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $order->price;
                            $userTransaction->save();

                            $order->txns()->create([
                                'trade_type' => 'refund',//Thanh toán dịch vụ
                                'is_add' => '1',//Cộng tiền
                                'user_id' => $userTransaction->id,
                                'amount' => $order->price,
                                'real_received_amount' => $order->price,
                                'last_balance' => $userTransaction->balance,
                                'description' => "Hoàn tiền dịch vụ #" . $order->id,
                                'ip' => $request->getClientIp(),
                                'order_id' => $order->id,
                                'status' => 1
                            ]);
                        }
                        else{
                            $order->status = 9;
                            $order->save();
                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workflow.key'),
                                'status' => 9,
                                'content' => 'Không có message trả về',
                            ]);

                            $data->status = 'thaotacthucong';
                            $data->save();

                            if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                                $order_global_id = $order->request_id_customer;
                                $sticky_global = "Không có message trả về";
                                $tele_global_service = $order->title??'';
                                $message_global = '';
                                $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                                $message_global .= "\n";
                                $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG";
                                $message_global .= "\n";
                                $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                                $message_global .= "\n";
                                $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                                $message_global .= "\n";
                                $message_global .= '- Thông báo từ: '.config('app.url');
                                $message_global .= "\n";

                                Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                            }
                        }
                    }
                    else{
                        $order->status = 9;
                        $order->save();
                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 9,
                            'content' => 'Không có message trả về',
                        ]);

                        $data->status = 'thaotacthucong';
                        $data->save();

                        if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                            $order_global_id = $order->request_id_customer??'';
                            $sticky_global = "Không có message trả về";
                            $tele_global_service = $order->title??'';
                            $message_global = '';
                            $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                            $message_global .= "\n";
                            $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG";
                            $message_global .= "\n";
                            $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                            $message_global .= "\n";
                            $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                            $message_global .= "\n";
                            $message_global .= '- Thông báo từ: '.config('app.url');
                            $message_global .= "\n";

                            Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                        }
                    }
                }
                else if ($request->get('status') == 2){

                    $message="[" . Carbon::now() . "] Lỗi trong quá trình giao dịch item pet go: ".$request->get('message').' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 9,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = $request->get('message')??"Lỗi trong quá trình giao dịch gem pet 99";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }

                }
                else if ($request->get('status') == 3){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();

                    $message="[" . Carbon::now() . "] Bot roblox item pet go không đủ số dư. Vui lòng nạp thêm tiền".' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                    $message="[" . Carbon::now() . "] Bot roblox item pet go không đủ số dư. Vui lòng nạp thêm tiền".' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox_pet99_san'));

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = $request->get('message')??"BOT KHÔNG ĐỦ SỐ DƯ";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - KẾT NỐI NCC THẤT BẠI";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }
                }
                else if ($request->get('status') == 5){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();

                    $message="[" . Carbon::now() . "] Error: Run out of Battery. Check it.".' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = $request->get('message')??"Error: Run out of Battery. Check it";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - KẾT NỐI NCC THẤT BẠI.";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }

                }
                else if ($request->get('status') == 6){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();

                    $message = "[" . Carbon::now() . "] Warning: Bot hết gem vui lòng nạp thêm gem để tool tiếp tục xử lý.".' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = $request->get('message')??"Bot hết gem";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - KẾT NỐI NCC THẤT BẠI.";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }

                }
                else{
                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 9,
                        'content' => 'Không có trạng thái trả về',
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = "Không có trạng thái trả về";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG.";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }
                }
            }
            else{
                $order->status = 9;
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'status' => 9,
                    'content' => 'Không có trạng thái trả về',
                ]);

                $data->status = 'thaotacthucong';
                $data->save();

                if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                    $order_global_id = $order->request_id_customer??'';
                    $sticky_global = "Không có trạng thái trả về";
                    $tele_global_service = $order->title??'';
                    $message_global = '';
                    $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                    $message_global .= "\n";
                    $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG.";
                    $message_global .= "\n";
                    $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                    $message_global .= "\n";
                    $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                    $message_global .= "\n";
                    $message_global .= '- Thông báo từ: '.config('app.url');
                    $message_global .= "\n";

                    Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                }
            }

            $data->save();
            DB::commit();

            //callback trả shop
            if($order->status==4 || $order->status==5){
                $statusBot = $data->status;
                $messageDaily = $request->get('message')??'';
                if($order->url!=""){
                    $this->dispatch(new CallbackOrderRobloxBuyGemPet($order,$statusBot,$messageDaily,$image??''));
                }
            }

            return 'Xử lý giao dịch thành công #' . $request->request_id;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return "Lỗi:".$e->getMessage();
        }

    }

    public function getCallbackBotGemHuge99(Request $request)
    {
        Cache::put('CHECK_TOOL_GAME_HUGE_99_ROBLOX',true,now()->addMinutes(5));

        //lưu log gọi curl
        $path = storage_path() ."/logs/services-auto/";
        $filename=$path."listen_callback_bot_roblox".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }

        if ($request->get('image')){

            // Giả sử base64Image được gửi lên từ form
            $base64Image = $request->get('image');

            $formattedDate = Carbon::now()->format('d-m-Y');

            $fileName = 'images/daily/service/'.$formattedDate.'/' . uniqid() . '.png'; // Tạo tên file duy nhất
            $url = App\Library\MediaHelpers::saveBase64ImageToS3($base64Image, $fileName);
            $md5Hash = md5($request->get('image'));
            $data = new \stdClass();
            $data->secretkey = $request->get('secretkey');
            $data->request_id = $request->get('request_id');
            $data->status = $request->get('status');
            $data->message = $request->get('message');
            $data->md5Hash = $md5Hash;
            $request['md5Hash'] = $md5Hash;
            if (isset($url)){
                $data->image = $url;
                $request['imageS3'] = $url;
            }else{
                $data->image = "Ảnh lỗi";
            }

            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($data);
            \File::append($filename,$contentText."\n");
        }else{
            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
            \File::append($filename,$contentText."\n");
        }

        if ($request->get('secretkey') != config('roblox_bot.sign')) {
            return "Không đúng mã bí mật. Xin thử lại ";
        }


        return $this->handleRobloxBotHuge99Roblox($request);
    }

    public function handleRobloxBotHuge99Roblox(Request $request)
    {

        DB::beginTransaction();
        try {

            $order = Order::query()
                ->where(function($q){
                    $q->orWhere('status', '=',1);
                    $q->orWhere('status', '=',2);
                })
                ->where('module',config('module.service-purchase.key'))
                ->where('idkey','huge_99_auto')
                ->where('request_id_customer',$request->get('request_id'))
                ->lockForUpdate()
                ->first();

            if(!$order){
                return "Đơn hàng đã xử lý trước đó";
            }

            $data = Roblox_Order::query()->with('order')
                ->where('order_id',$order->id)
                ->where('type_order',7)->where('status','chuanhan')
                ->lockForUpdate()->first();

            if(!$data){
                return "Không tìm thấy đơn hàng Roblox Order";
            }
            $image = null;
            if ($request->filled('status')){
                if($request->get('status')== 1 ){
                    //cập nhật trạng thái của purchase
                    $order->status = 4;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 4,
                        'content' => "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!",
                    ]);

                    //tiến độ lưu ảnh.
                    if ($request->get('imageS3')){
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 85,
                            'idkey' => $request->get('md5Hash'),
                            'content' => $request->get('imageS3'),
                        ]);

                        $image = $request->get('imageS3');
                    }

                    $data->status = 'danhan';
                    $data->save();

                    DB::commit();

                    if ($request->get('imageS3')){
                        //Kiểm tra trùng ảnh.
                        $order_image = OrderDetail::query()
                            ->where('module',config('module.service-workflow.key'))
                            ->select('id','idkey','status','content','order_id')
                            ->where('order_id','!=',$order->id)
                            ->where('idkey',$request->get('md5Hash'))
                            ->where('status',85)
                            ->whereDate('created_at', Carbon::today()) // Kiểm tra ngày hôm nay
                            ->first();

                        if (isset($order_image)){
                            $message="[" . Carbon::now() . "] Lỗi trùng ảnh: đơn hàng <b>HUGE 99</b>".$order->id." trùng ảnh đơn hàng:".$order_image->order_id.' Thông báo từ '.config('app.url');
                            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_huge_psx_roblox'));
                        }
                    }

                }
                else if($request->get('status') == 0 ){
                    if ($request->filled('message')){
                        if ($request->get('message')){
                            $order->status = 5;
                            $order->save();
                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workflow.key'),
                                'status' => 5,
                                'content' => $request->get('message'),
                            ]);

                            $data->status = 'dahuybo';
                            $data->save();
                            //tìm user nạp
                            $userTransaction = User::where('id', $order->author_id)->lockForUpdate()->firstOrFail();

                            //hoàn tiền cho user
                            $userTransaction->balance = $userTransaction->balance + $order->price;
                            $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $order->price;
                            $userTransaction->save();

                            $order->txns()->create([
                                'trade_type' => 'refund',//Thanh toán dịch vụ
                                'is_add' => '1',//Cộng tiền
                                'user_id' => $userTransaction->id,
                                'amount' => $order->price,
                                'real_received_amount' => $order->price,
                                'last_balance' => $userTransaction->balance,
                                'description' => "Hoàn tiền dịch vụ #" . $order->id,
                                'ip' => $request->getClientIp(),
                                'order_id' => $order->id,
                                'status' => 1
                            ]);
                        }
                        else{
                            $order->status = 9;
                            $order->save();
                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workflow.key'),
                                'status' => 9,
                                'content' => 'Không có message trả về',
                            ]);

                            $data->status = 'thaotacthucong';
                            $data->save();
                        }
                    }
                    else{
                        $order->status = 9;
                        $order->save();
                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 9,
                            'content' => 'Không có message trả về',
                        ]);

                        $data->status = 'thaotacthucong';
                        $data->save();
                    }
                }
                else if ($request->get('status') == 2){

                    $message="[" . Carbon::now() . "] Lỗi trong quá trình giao dịch đơn hàng <b>HUGE 99</b>: ".$request->get('message').' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_huge_psx_roblox'));

                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 9,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();

                }
                else if ($request->get('status') == 3){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();
                    $message = "[" . Carbon::now() . "] Không tìm thấy pet đơn hàng <b>HUGE 99</b>: "."<b>".$data->phone."</b>".' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_huge_psx_roblox'));

                }
                else{
                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 9,
                        'content' => 'Không có trạng thái trả về',
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();
                }
            }
            else{
                $order->status = 9;
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'status' => 9,
                    'content' => 'Không có trạng thái trả về',
                ]);

                $data->status = 'thaotacthucong';
                $data->save();
            }

            $data->save();

            DB::commit();

            //callback trả shop
            if($order->status==4 || $order->status==5){
                $statusBot = $data->status;
                $messageDaily = $request->get('message')??'';
                if($order->url!=""){
                    $this->dispatch(new CallbackOrderRobloxBuyGemPet($order,$statusBot,$messageDaily,$image??''));
                }
            }

            return 'Xử lý giao dịch thành công #' . $request->request_id;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return "Lỗi:".$e->getMessage();
        }

    }

    public function getCallbackBotGemUnist(Request $request)
    {
        Cache::put('CHECK_TOOL_GAME_UNIST_ROBLOX',true,now()->addMinutes(5));

        //lưu log gọi curl
        $path = storage_path() ."/logs/services-auto/";
        $filename=$path."listen_callback_bot_roblox".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }

        if ($request->get('image')){

            // Giả sử base64Image được gửi lên từ form
            $base64Image = $request->get('image');

            $formattedDate = Carbon::now()->format('d-m-Y');

            $fileName = 'images/daily/service/'.$formattedDate.'/' . uniqid() . '.png'; // Tạo tên file duy nhất
            $url = App\Library\MediaHelpers::saveBase64ImageToS3($base64Image, $fileName);
            $md5Hash = md5($request->get('image'));
            $data = new \stdClass();
            $data->secretkey = $request->get('secretkey');
            $data->request_id = $request->get('request_id');
            $data->status = $request->get('status');
            $data->message = $request->get('message');
            $data->md5Hash = $md5Hash;
            $request['md5Hash'] = $md5Hash;
            if (isset($url)){
                $data->image = $url;
                $request['imageS3'] = $url;
            }else{
                $data->image = "Ảnh lỗi";
            }

            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($data);
            \File::append($filename,$contentText."\n");
        }else{
            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
            \File::append($filename,$contentText."\n");
        }

        if ($request->get('secretkey') != config('roblox_bot.sign')) {
            return "Không đúng mã bí mật. Xin thử lại ";
        }

        return $this->handleRobloxBotGemUnitRoblox($request);
    }

    public function handleRobloxBotGemUnitRoblox(Request $request)
    {

        DB::beginTransaction();
        try {

            $order = Order::query()
                ->where(function($q){
                    $q->orWhere('status', '=',1);
                    $q->orWhere('status', '=',2);
                })
                ->where('module',config('module.service-purchase.key'))
                ->where('idkey','gem_unist_auto')
                ->where('request_id_customer',$request->get('request_id'))
                ->lockForUpdate()
                ->first();

            if(!$order){
                return "Đơn hàng đã xử lý trước đó";
            }

            $data = Roblox_Order::query()->with('order')
                ->where('order_id',$order->id)
                ->where('type_order',8)
                ->where('status','chuanhan')
                ->lockForUpdate()->first();

            if(!$data){
                return "Không tìm thấy đơn hàng Roblox Order";
            }
            $image = null;
            if ($request->filled('status')){
                if($request->get('status')== 1 ){
                    //cập nhật trạng thái của purchase
                    $order->status = 4;
                    $order->process_at = Carbon::now();//thời gian xác nhận đơn hàng
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 4,
                        'content' => "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!",
                    ]);

                    //tiến độ lưu ảnh.
                    if ($request->get('imageS3')){
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 85,
                            'idkey' => $request->get('md5Hash'),
                            'content' => $request->get('imageS3'),
                        ]);

                        $image = $request->get('imageS3');
                    }

                    $data->status = 'danhan';
                    $data->save();

                    DB::commit();

                    if ($request->get('imageS3')){
                        //Kiểm tra trùng ảnh.
                        $order_image = OrderDetail::query()
                            ->where('module',config('module.service-workflow.key'))
                            ->select('id','idkey','status','content','order_id')
                            ->where('order_id','!=',$order->id)
                            ->where('idkey',$request->get('md5Hash'))
                            ->where('status',85)
                            ->whereDate('created_at', Carbon::today()) // Kiểm tra ngày hôm nay
                            ->first();

                        if (isset($order_image)){
                            $message="[" . Carbon::now() . "] Lỗi trùng ảnh: đơn hàng <b>Gem Unist</b>".$order->id." trùng ảnh đơn hàng:".$order_image->order_id.' Thông báo từ '.config('app.url');
                            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
                        }
                    }

                }
                else if($request->get('status') == 0 || $request->get('status') == 4){
                    if ($request->filled('message')){
                        if ($request->get('message')){
                            $order->status = 5;
                            $order->save();
                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workflow.key'),
                                'status' => 5,
                                'content' => $request->get('message'),
                            ]);

                            $data->status = 'dahuybo';
                            $data->save();
                            //tìm user nạp
                            $userTransaction = User::where('id', $order->author_id)->lockForUpdate()->firstOrFail();

                            //hoàn tiền cho user
                            $userTransaction->balance = $userTransaction->balance + $order->price;
                            $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $order->price;
                            $userTransaction->save();

                            $order->txns()->create([
                                'trade_type' => 'refund',//Thanh toán dịch vụ
                                'is_add' => '1',//Cộng tiền
                                'user_id' => $userTransaction->id,
                                'amount' => $order->price,
                                'real_received_amount' => $order->price,
                                'last_balance' => $userTransaction->balance,
                                'description' => "Hoàn tiền dịch vụ #" . $order->id,
                                'ip' => $request->getClientIp(),
                                'order_id' => $order->id,
                                'status' => 1
                            ]);

                            $message = "[" . Carbon::now() . "] Giao dịch thất bại <b>Dịch vụ gem unit auto</b>: "."<b>".$order->id."</b> Lý do: ".$request->get('message').' Thông báo từ '.config('app.url');
                            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
                        }
                        else{
                            $order->status = 9;
                            $order->save();
                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workflow.key'),
                                'status' => 9,
                                'content' => 'Không có message trả về',
                            ]);

                            $data->status = 'thaotacthucong';
                            $data->save();

                            if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                                $order_global_id = $order->request_id_customer??'';
                                $sticky_global = "Không có message trả về";
                                $tele_global_service = $order->title??'';
                                $message_global = '';
                                $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                                $message_global .= "\n";
                                $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG.";
                                $message_global .= "\n";
                                $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                                $message_global .= "\n";
                                $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                                $message_global .= "\n";
                                $message_global .= '- Thông báo từ: '.config('app.url');
                                $message_global .= "\n";

                                Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                            }
                        }
                    }
                    else{
                        $order->status = 9;
                        $order->save();
                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 9,
                            'content' => 'Không có message trả về',
                        ]);

                        $data->status = 'thaotacthucong';
                        $data->save();

                        if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                            $order_global_id = $order->request_id_customer??'';
                            $sticky_global = "Không có message trả về";
                            $tele_global_service = $order->title??'';
                            $message_global = '';
                            $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                            $message_global .= "\n";
                            $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG.";
                            $message_global .= "\n";
                            $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                            $message_global .= "\n";
                            $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                            $message_global .= "\n";
                            $message_global .= '- Thông báo từ: '.config('app.url');
                            $message_global .= "\n";

                            Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                        }
                    }
                }
                else if ($request->get('status') == 2){

                    $message="[" . Carbon::now() . "] Lỗi trong quá trình giao dịch <b>Gem Unist</b>: ".$request->get('message').' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 9,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = $request->get('message')??"Lỗi trong quá trình giao dịch";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG.";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }

                }
                else if ($request->get('status') == 3){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();

                    $message = "[" . Carbon::now() . "] Không Đủ coin <b>Dịch vụ gem unit auto</b>: "."<b>".$data->phone."</b>".' Thông báo từ '.config('app.url');

                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = $request->get('message')??"Không Đủ coin";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - KẾT NỐI NCC THẤT BẠI.";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }

                }
                else if ($request->get('status') == 5){

                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 9,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();

                    $message = "[" . Carbon::now() . "] Lỗi trong quá trình giao dịch <b>Dịch vụ gem unit auto</b>: "."<b>".$order->id."</b> Lý do: ".$request->get('message').' Thông báo từ '.config('app.url');
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = $request->get('message')??"Lỗi trong quá trình giao dịch";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG.";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }

                }
                else if ($request->get('status') == 6){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();

                    $message = "[" . Carbon::now() . "] Không Đủ gem <b>Dịch vụ gem unit auto</b>: "."<b>".$data->phone."</b>".' Thông báo từ '.config('app.url');

                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = $request->get('message')??"Không Đủ gem";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - KẾT NỐI NCC THẤT BẠI.";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }

                }
                else{
                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 9,
                        'content' => 'Không có trạng thái trả về',
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = "Không có trạng thái trả về";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG.";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }
                }
            }
            else{
                $order->status = 9;
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'status' => 9,
                    'content' => 'Không có trạng thái trả về',
                ]);

                $data->status = 'thaotacthucong';
                $data->save();

                if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                    $order_global_id = $order->request_id_customer??'';
                    $sticky_global = "Không có trạng thái trả về";
                    $tele_global_service = $order->title??'';
                    $message_global = '';
                    $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                    $message_global .= "\n";
                    $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG.";
                    $message_global .= "\n";
                    $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                    $message_global .= "\n";
                    $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                    $message_global .= "\n";
                    $message_global .= '- Thông báo từ: '.config('app.url');
                    $message_global .= "\n";

                    Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                }
            }

            $data->save();

            DB::commit();

            //callback trả shop
            if($order->status==4 || $order->status==5){
                $statusBot = $data->status;
                $messageDaily = $request->get('message')??'';
                if($order->url!=""){
                    $this->dispatch(new CallbackOrderRobloxBuyGemPet($order,$statusBot,$messageDaily,$image??''));
                }
            }

            return 'Xử lý giao dịch thành công #' . $request->request_id;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return "Lỗi:".$e->getMessage();
        }

    }

    public function getCallbackBotUnist(Request $request)
    {
        Cache::put('CHECK_TOOL_UNIST_ROBLOX',true,now()->addMinutes(5));

        //lưu log gọi curl
        $path = storage_path() ."/logs/services-auto/";
        $filename=$path."listen_callback_bot_roblox".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }

        if ($request->get('image')){

            // Giả sử base64Image được gửi lên từ form
            $base64Image = $request->get('image');

            $formattedDate = Carbon::now()->format('d-m-Y');

            $fileName = 'images/daily/service/'.$formattedDate.'/' . uniqid() . '.png'; // Tạo tên file duy nhất
            $url = App\Library\MediaHelpers::saveBase64ImageToS3($base64Image, $fileName);
            $md5Hash = md5($request->get('image'));
            $data = new \stdClass();
//            $data->image_callback = $request->get('image');
            $data->secretkey = $request->get('secretkey');
            $data->request_id = $request->get('request_id');
            $data->status = $request->get('status');
            $data->message = $request->get('message');
            $data->md5Hash = $md5Hash;
            $request['md5Hash'] = $md5Hash;
            if (isset($url)){
                $data->image = $url;
                $request['imageS3'] = $url;
            }else{
                $data->image = "Ảnh lỗi";
            }

            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($data);
            \File::append($filename,$contentText."\n");
        }else{
            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
            \File::append($filename,$contentText."\n");
        }

        if ($request->get('secretkey') != config('roblox_bot_unit.sign')) {
            return "Không đúng mã bí mật. Xin thử lại ";
        }

        return $this->handleRobloxBotUnitRoblox($request);
    }

    public function handleRobloxBotUnitRoblox(Request $request)
    {

        DB::beginTransaction();
        try {

            $order = Order::query()
                ->where(function($q){
                    $q->orWhere('status', '=',1);
                    $q->orWhere('status', '=',2);
                })
                ->where('module',config('module.service-purchase.key'))
                ->where('idkey','unist_auto')
                ->where('request_id_customer',$request->get('request_id'))
                ->lockForUpdate()
                ->first();

            if(!$order){
                DB::rollBack();
                return response()->json([
                    'status' => 1,
                    'message' => 'Đơn hàng đã xử lý trước đó',
                ]);
            }

            $data = Roblox_Order::query()->with('order')
                ->where('order_id',$order->id)
                ->where('type_order',9)
                ->where('status','dangxuly')
                ->lockForUpdate()->first();

            if(!$data){
                DB::rollBack();
                return response()->json([
                    'status' => 1,
                    'message' => 'Không tìm thấy đơn hàng Roblox Order',
                ]);
            }
            $image = null;
            if ($request->filled('status')){
                if($request->get('status')== 1 ){
                    //cập nhật trạng thái của purchase
                    $order->status = 4;
                    $order->process_at = Carbon::now();//thời gian xác nhận đơn hàng
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 4,
                        'content' => "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!",
                    ]);

                    //tiến độ lưu ảnh.
                    if ($request->get('imageS3')){
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 85,
                            'idkey' => $request->get('md5Hash'),
                            'content' => $request->get('imageS3'),
                        ]);

                        $image = $request->get('imageS3');
                    }

                    $data->status = 'danhan';
                    $data->save();

                    DB::commit();

                    if ($request->get('imageS3')){
                        //Kiểm tra trùng ảnh.
                        $order_image = OrderDetail::query()
                            ->where('module',config('module.service-workflow.key'))
                            ->select('id','idkey','status','content','order_id')
                            ->where('order_id','!=',$order->id)
                            ->where('idkey',$request->get('md5Hash'))
                            ->where('status',85)
                            ->whereDate('created_at', Carbon::today()) // Kiểm tra ngày hôm nay
                            ->first();

                        if (isset($order_image)){
                            $message="[" . Carbon::now() . "] Lỗi trùng ảnh: đơn hàng <b> Unit auto</b>".$order->id." trùng ảnh đơn hàng:".$order_image->order_id.' Thông báo từ '.config('app.url');
                            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
                        }
                    }

                }
                else if($request->get('status') == 0 || $request->get('status') == 2){
                    if ($request->filled('message')){
                        if ($request->get('message')){
                            $order->status = 5;
                            $order->save();
                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workflow.key'),
                                'status' => 5,
                                'content' => $request->get('message'),
                            ]);

                            $data->status = 'dahuybo';
                            $data->save();
                            //tìm user nạp
                            $userTransaction = User::where('id', $order->author_id)->lockForUpdate()->firstOrFail();

                            //hoàn tiền cho user
                            $userTransaction->balance = $userTransaction->balance + $order->price;
                            $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $order->price;
                            $userTransaction->save();

                            $order->txns()->create([
                                'trade_type' => 'refund',//Thanh toán dịch vụ
                                'is_add' => '1',//Cộng tiền
                                'user_id' => $userTransaction->id,
                                'amount' => $order->price,
                                'real_received_amount' => $order->price,
                                'last_balance' => $userTransaction->balance,
                                'description' => "Hoàn tiền dịch vụ #" . $order->id,
                                'ip' => $request->getClientIp(),
                                'order_id' => $order->id,
                                'status' => 1
                            ]);

                            $message = "[" . Carbon::now() . "] Giao dịch thất bại <b>Dịch vụ unit auto</b>: "."<b>".$order->id."</b> Lý do: ".$request->get('message').' Thông báo từ '.config('app.url');
                            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
                        }
                        else{
                            $order->status = 9;
                            $order->save();
                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workflow.key'),
                                'status' => 9,
                                'content' => 'Không có message trả về',
                            ]);

                            $data->status = 'thaotacthucong';
                            $data->save();
                        }
                    }
                    else{
                        $order->status = 9;
                        $order->save();
                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 9,
                            'content' => 'Không có message trả về',
                        ]);

                        $data->status = 'thaotacthucong';
                        $data->save();
                    }
                }
                else if ($request->get('status') == 3){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();

                    $message = "[" . Carbon::now() . "] Không có vật phẩm để gif <b>Dịch vụ unit auto</b>: "."<b>".$data->phone."</b>".' Thông báo từ '.config('app.url');

                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                }
                else if ($request->get('status') == 4){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();

                    $message = "[" . Carbon::now() . "] Không đủ coin để gửi <b>Dịch vụ unit auto</b>: "."<b>".$data->phone."</b>".' Thông báo từ '.config('app.url');

                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                }
                else if ($request->get('status') == 6){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();

                    $message = "[" . Carbon::now() . "] Lỗi vật phẩm cần tìm không có sẵn <b>Dịch vụ unit auto</b>: "."<b>".$data->phone."</b>".' Thông báo từ '.config('app.url');

                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                }
                else{
                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 9,
                        'content' => 'Không có trạng thái trả về',
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();
                }
            }
            else{
                $order->status = 9;
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'status' => 9,
                    'content' => 'Không có trạng thái trả về',
                ]);

                $data->status = 'thaotacthucong';
                $data->save();
            }

            $data->save();

            DB::commit();

            //callback trả shop
            if($order->status==4 || $order->status==5){
                $statusBot = $data->status;
                $messageDaily = $request->get('message')??'';
                if($order->url!=""){
                    $this->dispatch(new CallbackOrderRobloxBuyGemPet($order,$statusBot,$messageDaily,$image??''));
                }
            }

            return response()->json([
                'status' => 1,
                'message' => 'Xử lý giao dịch thành công',
                'data' =>$request->request_id
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json([
                'status' => 0,
                'message' => 'Lỗi code',
            ]);
            return "Lỗi:".$e->getMessage();
        }

    }

    public function getCallbackBotUnistV2(Request $request)
    {
        Cache::put('CHECK_TOOL_UNIST_ROBLOX',true,now()->addMinutes(5));

        //lưu log gọi curl
        $path = storage_path() ."/logs/services-auto/";
        $filename=$path."listen_callback_bot_roblox".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }

        if ($request->get('image')){

            // Giả sử base64Image được gửi lên từ form
            $base64Image = $request->get('image');

            $formattedDate = Carbon::now()->format('d-m-Y');

            $fileName = 'images/daily/service/'.$formattedDate.'/' . uniqid() . '.png'; // Tạo tên file duy nhất
            $url = App\Library\MediaHelpers::saveBase64ImageToS3($base64Image, $fileName);
            $md5Hash = md5($request->get('image'));
            $data = new \stdClass();
//            $data->image_callback = $request->get('image');
            $data->secretkey = $request->get('secretkey');
            $data->request_id = $request->get('request_id');
            $data->status = $request->get('status');
            $data->message = $request->get('message');
            $data->md5Hash = $md5Hash;
            $request['md5Hash'] = $md5Hash;
            if (isset($url)){
                $data->image = $url;
                $request['imageS3'] = $url;
            }else{
                $data->image = "Ảnh lỗi";
            }

            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($data);
            \File::append($filename,$contentText."\n");
        }else{
            $contentText =  $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
            \File::append($filename,$contentText."\n");
        }

        if ($request->get('secretkey') != config('roblox_bot_unit.sign')) {
            return "Không đúng mã bí mật. Xin thử lại ";
        }

        return $this->handleRobloxBotUnitRobloxV2($request);
    }

    public function handleRobloxBotUnitRobloxV2(Request $request)
    {

        DB::beginTransaction();
        try {

            $order = Order::query()
                ->where(function($q){
                    $q->orWhere('status', '=',1);
                    $q->orWhere('status', '=',2);
                })
                ->where('module',config('module.service-purchase.key'))
                ->where('idkey','gem_unist_auto')
                ->where('request_id_customer',$request->get('request_id'))
                ->lockForUpdate()
                ->first();

            if(!$order){
                DB::rollBack();
                return response()->json([
                    'status' => 1,
                    'message' => 'Đơn hàng đã xử lý trước đó',
                ]);
            }

            $data = Roblox_Order::query()
                ->with('order')
                ->where('order_id',$order->id)
                ->where('type_order',8)
                ->where('status','dangxuly')
                ->lockForUpdate()->first();

            if(!$data){
                DB::rollBack();
                return response()->json([
                    'status' => 1,
                    'message' => 'Không tìm thấy đơn hàng Roblox Order',
                ]);
            }
            $image = null;
            if ($request->filled('status')){
                if($request->get('status')== 1 ){
                    //cập nhật trạng thái của purchase
                    $order->status = 4;
                    $order->process_at = Carbon::now();//thời gian xác nhận đơn hàng
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 4,
                        'content' => "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!",
                    ]);

                    //tiến độ lưu ảnh.
                    if ($request->get('imageS3')){
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 85,
                            'idkey' => $request->get('md5Hash'),
                            'content' => $request->get('imageS3'),
                        ]);

                        $image = $request->get('imageS3');
                    }

                    $data->status = 'danhan';
                    $data->save();

                    DB::commit();

//                    if ($request->get('imageS3')){
//                        //Kiểm tra trùng ảnh.
//                        $order_image = OrderDetail::query()
//                            ->where('module',config('module.service-workflow.key'))
//                            ->select('id','idkey','status','content','order_id')
//                            ->where('order_id','!=',$order->id)
//                            ->where('idkey',$request->get('md5Hash'))
//                            ->where('status',85)
//                            ->whereDate('created_at', Carbon::today()) // Kiểm tra ngày hôm nay
//                            ->first();
//
//                        if (isset($order_image)){
//                            $message="[" . Carbon::now() . "] Lỗi trùng ảnh: đơn hàng <b> Unit auto</b>".$order->id." trùng ảnh đơn hàng:".$order_image->order_id.' Thông báo từ '.config('app.url');
//                            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
//                        }
//                    }

                }
                else if($request->get('status') == 0 || $request->get('status') == 2){
                    if ($request->filled('message')){
                        if ($request->get('message')){
                            $order->status = 5;
                            $order->save();
                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workflow.key'),
                                'status' => 5,
                                'content' => $request->get('message'),
                            ]);

                            $data->status = 'dahuybo';
                            $data->save();
                            //tìm user nạp
                            $userTransaction = User::where('id', $order->author_id)->lockForUpdate()->firstOrFail();

                            //hoàn tiền cho user
                            $userTransaction->balance = $userTransaction->balance + $order->price;
                            $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $order->price;
                            $userTransaction->save();

                            $order->txns()->create([
                                'trade_type' => 'refund',//Thanh toán dịch vụ
                                'is_add' => '1',//Cộng tiền
                                'user_id' => $userTransaction->id,
                                'amount' => $order->price,
                                'real_received_amount' => $order->price,
                                'last_balance' => $userTransaction->balance,
                                'description' => "Hoàn tiền dịch vụ #" . $order->id,
                                'ip' => $request->getClientIp(),
                                'order_id' => $order->id,
                                'status' => 1
                            ]);

                            $message = "[" . Carbon::now() . "] Giao dịch thất bại <b>Dịch vụ unit auto</b>: "."<b>".$order->id."</b> Lý do: ".$request->get('message').' Thông báo từ '.config('app.url');
                            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
                        }
                        else{
                            $order->status = 9;
                            $order->save();
                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workflow.key'),
                                'status' => 9,
                                'content' => 'Không có message trả về',
                            ]);

                            $data->status = 'thaotacthucong';
                            $data->save();

                            if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                                $order_global_id = $order->request_id_customer??'';
                                $sticky_global = "Không có trạng thái trả về";
                                $tele_global_service = $order->title??'';
                                $message_global = '';
                                $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                                $message_global .= "\n";
                                $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG.";
                                $message_global .= "\n";
                                $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                                $message_global .= "\n";
                                $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                                $message_global .= "\n";
                                $message_global .= '- Thông báo từ: '.config('app.url');
                                $message_global .= "\n";

                                Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                            }
                        }
                    }
                    else{
                        $order->status = 9;
                        $order->save();
                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 9,
                            'content' => 'Không có message trả về',
                        ]);

                        $data->status = 'thaotacthucong';
                        $data->save();

                        if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                            $order_global_id = $order->request_id_customer??'';
                            $sticky_global = "Không có trạng thái trả về";
                            $tele_global_service = $order->title??'';
                            $message_global = '';
                            $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                            $message_global .= "\n";
                            $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG.";
                            $message_global .= "\n";
                            $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                            $message_global .= "\n";
                            $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                            $message_global .= "\n";
                            $message_global .= '- Thông báo từ: '.config('app.url');
                            $message_global .= "\n";

                            Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                        }
                    }
                }
                else if ($request->get('status') == 3){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();

                    $message = "[" . Carbon::now() . "] Không có vật phẩm để gif <b>Dịch vụ unit auto</b>: "."<b>".$data->phone."</b>".' Thông báo từ '.config('app.url');

                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = $request->get('message')??"Không có vật phẩm để gif";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - KẾT NỐI NCC THẤT BẠI.";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }

                }
                else if ($request->get('status') == 4){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();

                    $message = "[" . Carbon::now() . "] Không đủ coin để gửi <b>Dịch vụ unit auto</b>: "."<b>".$data->phone."</b>".' Thông báo từ '.config('app.url');

                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = $request->get('message')??"Không đủ coin để gửi";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - KẾT NỐI NCC THẤT BẠI.";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }

                }
                else if ($request->get('status') == 6){

                    $order->status = 7;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 7,
                        'content' => $request->get('message'),
                    ]);

                    $data->status = 'recharge';
                    $data->save();

                    $message = "[" . Carbon::now() . "] Lỗi vật phẩm cần tìm không có sẵn <b>Dịch vụ unit auto</b>: "."<b>".$data->phone."</b>".' Thông báo từ '.config('app.url');

                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = $request->get('message')??"Lỗi vật phẩm cần tìm không có sẵn";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - KẾT NỐI NCC THẤT BẠI.";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }

                }
                else{
                    $order->status = 9;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 9,
                        'content' => 'Không có trạng thái trả về',
                    ]);

                    $data->status = 'thaotacthucong';
                    $data->save();

                    if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                        $order_global_id = $order->request_id_customer??'';
                        $sticky_global = "Không có trạng thái trả về";
                        $tele_global_service = $order->title??'';
                        $message_global = '';
                        $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                        $message_global .= "\n";
                        $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG.";
                        $message_global .= "\n";
                        $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                        $message_global .= "\n";
                        $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                        $message_global .= "\n";
                        $message_global .= '- Thông báo từ: '.config('app.url');
                        $message_global .= "\n";

                        Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                    }
                }
            }
            else{
                $order->status = 9;
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'status' => 9,
                    'content' => 'Không có trạng thái trả về',
                ]);

                $data->status = 'thaotacthucong';
                $data->save();

                if (in_array($order->author_id,[198777,198751,198723,198564,198449])){
                    $order_global_id = $order->request_id_customer??'';
                    $sticky_global = "Không có trạng thái trả về";
                    $tele_global_service = $order->title??'';
                    $message_global = '';
                    $message_global = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                    $message_global .= "\n";
                    $message_global .= "- Đơn hàng: <b>".$order_global_id."</b> - THAO TÁC THỦ CÔNG.";
                    $message_global .= "\n";
                    $message_global .= "- Lý do: <b>".$sticky_global."</b> ";
                    $message_global .= "\n";
                    $message_global .= '- Dịch vụ: <b>'.$tele_global_service.'</b>';
                    $message_global .= "\n";
                    $message_global .= '- Thông báo từ: '.config('app.url');
                    $message_global .= "\n";

                    Helpers::TelegramNotify($message_global,config('telegram.bots.mybot.channel_bot_global'));
                }
            }

            $data->save();

            DB::commit();

            //callback trả shop
            if($order->status==4 || $order->status==5){
                $statusBot = $data->status;
                $messageDaily = $request->get('message')??'';
                if($order->url!=""){
                    $this->dispatch(new CallbackOrderRobloxBuyGemPet($order,$statusBot,$messageDaily,$image??''));
                }
            }

            return response()->json([
                'status' => 1,
                'message' => 'Xử lý giao dịch thành công',
                'data' =>$request->request_id
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json([
                'status' => 0,
                'message' => 'Lỗi code',
            ]);
            return "Lỗi:".$e->getMessage();
        }

    }

    public function getCallbackRbxApi(Request $request)
    {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
//        $merchantApiKey = config('rbxapi.api_key');
        //lưu log gọi curl
        $path = storage_path() ."/logs/rbx-api/";
        $filename=$path."listen_callback_rbx_api".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText =  $txt = Carbon::now() . ":" . json_encode($data) . '?' . http_build_query($request->all());
        \File::append($filename,$contentText."\n");

        // Lấy giá trị của 'sign' từ dữ liệu
        $sign = $data['sign'];

        // Loại bỏ 'sign' khỏi dữ liệu để chuẩn bị tạo chữ ký
        unset($data['sign']);

        $rbx_apis = config('module.service-purchase-auto.rbx_api');
        $isSign = false;
        foreach ($rbx_apis as $rbx_api){
            $merchantApiKey = config('rbxapi.api_key_'.$rbx_api);
            if (isset($merchantApiKey)){
                $hash = md5(base64_encode(json_encode($data, JSON_UNESCAPED_UNICODE)) . $merchantApiKey);
                if (hash_equals($hash, $sign)) {
                    // Nếu chữ ký không khớp, trả về lỗi
                    $isSign = true;
                }
            }
        }

        if ($isSign !== true){

            $message = '';
            $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
            $message .= "\n";
            $message .= "<b>[RBX] Không đúng mã bí mật</b>";
            $message .= "\n";
            Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_warning_rpx'));

            return "Không đúng mã bí mật. Xin thử lại ";
        }

        if (empty($data['orderId'])){

            $message = '';
            $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
            $message .= "\n";
            $message .= "<b>[RBX] Không đúng mã bí mật</b>";
            $message .= "\n";
            Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_warning_rpx'));

            return "Không tìm thấy mã đơn hàng ";
        }

        return $this->handleRbxApi($data);
    }

    public function handleRbxApi($data)
    {

        $orderId = $data['orderId']??'';

        DB::beginTransaction();
        try {
            $module = config('module.service-workflow.key');
            $order = Order::query()
                ->where(function($q){
                    $q->orWhere('status', '=',2);
                    $q->orWhere('status', '=',7);
                    $q->orWhere('status', '=',9);
                })
                ->where('module',config('module.service-purchase.key'))
                ->whereIn('idkey',['roblox_buyserver','roblox_buygamepass'])
                ->where('request_id_customer',$orderId)
                ->lockForUpdate()
                ->first();

            if(!$order){
                DB::rollBack();

                $message = '';
                $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                $message .= "\n";
                $message .= "<b>[RBX] Không tìm thấy đơn hàng: ".$orderId."</b>";
                $message .= "\n";
                Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_warning_rpx'));
                return response()->json([
                    'status' => 1,
                    'message' => 'Đơn hàng đã xử lý trước đó',
                ]);
            }

            $roblox_order = Roblox_Order::query()
                ->where('order_id',$order->id)
                ->where('type_order',3)
                ->where('status','chuanhan')
                ->lockForUpdate()->first();

            if(!$roblox_order){
                DB::rollBack();

                $message = '';
                $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                $message .= "\n";
                $message .= "<b>[RBX] Không tìm thấy đơn hàng: ".$order->id."</b>";
                $message .= "\n";
                Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_warning_rpx'));
                return response()->json([
                    'status' => 1,
                    'message' => 'Không tìm thấy đơn hàng Roblox Order',
                ]);
            }

            if (empty($data['status'])){
                DB::rollBack();

                $message = '';
                $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                $message .= "\n";
                $message .= "<b>[RBX] Không có trạng thái trả về: ".$order->id."</b>";
                $message .= "\n";
                Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_warning_rpx'));

                return response()->json([
                    'status' => 1,
                    'message' => 'Không có trạng thái trả về',
                ]);
            }

            $resultChange = new \stdClass();
            $resultChange->uuid = $data['uuid']??''; //id đơn hàng
            $resultChange->type = $data['type']??''; //Loại đơn hàng.
            $resultChange->price = $data['price']??''; //(float) Giá trị đơn hàng (USD)
            $resultChange->rate = $data['rate']??''; // (float) Tỷ giá của đơn hàng (USD)
            $resultChange->vendorId = $data['vendorId']??'';  //(string) ID của nhà cung cấp đã hoàn thành đơn hàng
            $resultChange->robuxAmount = $data['robuxAmount']??'';  // (int) Số lượng Robux mà khách hàng của bạn đã mua
            $resultChange->status = $data['status']??'';  //Trạng thái của đơn hàng
            $resultChange->robloxUserId = $data['robloxUserId']??'';  //(int) ID Roblox của khách hàng của bạn
            $resultChange->robloxUsername = $data['robloxUsername']??''; //(string) Tên người dùng Roblox của khách hàng của bạn
            $resultChange->buyerRobloxId = $data['buyerRobloxId']??''; //int (nullable) ID Roblox của tài khoản đã hoàn thành đơn hàng
            $resultChange->buyerRobloxUsername = $data['buyerRobloxUsername']??''; //string (nullable) Tên người dùng Roblox của tài khoản đã hoàn thành đơn hàng
            $resultChange->error = $data['error']??''; //orderError (Object) or null Lỗi đơn hàng (có thể null)

            $content = json_encode($resultChange);

            $status = $data['status'];
            if ($status == "Completed"){
                $roblox_order->status = "danhan";
                $roblox_order->save();
                //cập nhật lại số dư cho bot
                //cập nhật trạng thái thành công của đơn
                $order->status = 4;
                $order->request_id_provider = $data['uuid']??'';
                $order->process_at = Carbon::now();//thời gian xác nhận đơn hàng
                $order->save();
                //set tiến độ

                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => $module,
                    'content' => "Giao dịch thành công",
                    'status' => 4,
                ]);

                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => 'rbx_api',
                    'title' => $data['uuid']??'',//id đơn hàng
                    'description' => $data['type']??'',//Loại đơn hàng.
                    'content' => $content,
                    'status' => 4,
                ]);

                DB::commit();

                $message = "Giao dịch thành công";
                $messagee = '';
                $messagee = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                $messagee .= "\n";
                $messagee .= "<b>[RBX] Giao dịch thành công: ".$order->request_id_customer."</b>";
                $messagee .= "\n";
                Helpers::TelegramNotify($messagee, config('telegram.bots.mybot.channel_bot_warning_rpx'));

            }
            elseif ($status == "Error"){

                //cập nhật trạng thái thành công của đơn
                $order->status = 899;
                $order->request_id_provider = $data['uuid']??'';
                $order->save();
                //set tiến độ

                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => $module,
                    'content' => "Giao dịch lỗi",
                    'status' => 9,
                ]);

                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => 'rbx_api',
                    'title' => $data['uuid']??'',//id đơn hàng
                    'description' => $data['type']??'',//Loại đơn hàng.
                    'content' => $content,
                    'status' => 9,
                ]);

                $messagee = '';
                $messagee = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                $messagee .= "\n";
                $messagee .= "<b>[RBX] Giao dịch lỗi: ".$order->id."</b>";
                $messagee .= "\n";
                Helpers::TelegramNotify($messagee, config('telegram.bots.mybot.channel_bot_warning_rpx'));
            }
            elseif ($status == "Cancelled"){
                $roblox_order->status = "dahoantien";
                $roblox_order->save();
                //cập nhật trạng thái thất bại của đơn
                $order->status = 5;
                $order->request_id_provider = $data['uuid']??'';
                $order->save();

                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => $module,
                    'content' =>  $data['error']??'',
                    'status' => 5,

                ]);

                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => 'rbx_api',
                    'title' => $data['uuid']??'',//id đơn hàng
                    'description' => $data['type']??'',//Loại đơn hàng.
                    'content' => $content,
                    'status' => 9,
                ]);

                //refund
                $userTransaction = User::where('id',$order->author_id)->lockForUpdate()->firstOrFail();

                if($order->price<=0){
                    DB::rollBack();
                    return "Giao dịch thất bại.Số tiền giao dịch không phù hợp";
                }

                $userTransaction->balance = $userTransaction->balance + $order->price;
                $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $order->price;
                $userTransaction->save();

                Txns::create([
                    'trade_type' => 'refund', //Hoàn tiền dịch vụ
                    'user_id' => $userTransaction->id,
                    'is_add' => '1',//Công tiền
                    'amount' => $order->price,
                    'real_received_amount' => $order->price,
                    'last_balance' => $userTransaction->balance,
                    'description' => "Hoàn tiền thanh toán thất bại dịch vụ " . $order->title . " #".$order->id ,
                    'order_id' => $order->id,
                    'status' => 1
                ]);

                $message = "Giao dịch thất bại: ".$data['error']??'';

                $messagee = '';
                $messagee = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                $messagee .= "\n";
                $messagee .= "<b>[RBX] Giao dịch thất bại: ".$order->id."</b>";
                $messagee .= "\n";
                Helpers::TelegramNotify($messagee, config('telegram.bots.mybot.channel_bot_warning_rpx'));
            }


            DB::commit();

            //callback trả shop
            if($order->status==4 || $order->status==5){
                if($order->url!=""){
                    $this->callbackToShop($order,$message??"");
                }
            }

            return response()->json([
                'status' => 1,
                'message' => 'Xử lý giao dịch thành công',
                'data' =>$orderId
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json([
                'status' => 0,
                'message' => 'Lỗi code',
            ]);
            return "Lỗi:".$e->getMessage();
        }

    }

    public function callbackToShop(Order $order,$messageBot)
    {

        $url = $order->url;

        $data = array();
        $data['status'] = $order->status;
        $data['message'] = $messageBot;
        $data['price'] = $order->price;
        $data['price_base'] = $order->price_base;
        $data['input_auto'] = 1;
        if ($order->status == 4){
            $data['process_at'] = strtotime($order->process_at);
        }

        $dataPost = http_build_query($data);
        try{
            for ($i=0;$i<3;$i++){
                $ch = curl_init();


                //data dạng get
                if (strpos($url, '?') !== FALSE) {
                    $url = $url . "&" . $dataPost;
                } else {
                    $url = $url . "?" . $dataPost;
                }

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);

                $resultRaw=curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                //debug thì mở cái này
                $myfile = fopen(storage_path() . "/logs/curl_callback-service-to-shop-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
                $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
                fwrite($myfile, $txt);
                fclose($myfile);

                if($httpcode==200){

                    if(strpos($resultRaw, "Có lỗi phát sinh.Xin vui lòng thử lại") > -1){
                        continue;
                    }
                    break;
                }
            }
        }
        catch (\Exception $e){
            \Log::error($e);
        }

    }

    public function callbackToShopRoblox(Order $order,$statusBot,$message= '',$image = false)
    {

        $url = $order->url;

        $data = array();
        $data['status'] = $order->status;
        $data['message'] = $statusBot;

        $data['message_daily'] = $message;

        if (strpos($url, 'https://backend-th.tichhop.pro') > -1 || strpos($url, 'http://s-api.backend-th.tichhop.pro') > -1){
            $data['message_daily'] = config('lang.'.$message)??$message;
        }

        $data['price'] = $order->price;
        $data['price_base'] = $order->price_base;

        $data['input_auto'] = 1;
        if ($order->status == 4){
            $data['process_at'] = strtotime($order->process_at);
        }

        $dataPost = http_build_query($data);
        try{

            for ($i=0;$i<3;$i++){
                $ch = curl_init();


                //data dạng get
                if (strpos($url, '?') !== FALSE) {
                    $url = $url . "&" . $dataPost;
                } else {
                    $url = $url . "?" . $dataPost;
                }

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);

                $resultRaw=curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                //debug thì mở cái này
                $myfile = fopen(storage_path() . "/logs/curl_callback-service-to-shop-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
                $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
                fwrite($myfile, $txt);
                fclose($myfile);

                if($httpcode==200){

                    if(strpos($resultRaw, "Có lỗi phát sinh.Xin vui lòng thử lại") > -1){
                        continue;
                    }
                    break;
                }
            }
        }
        catch (\Exception $e){
            \Log::error($e);
        }

    }

}
