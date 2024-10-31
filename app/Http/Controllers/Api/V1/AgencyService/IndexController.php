<?php

namespace App\Http\Controllers\Api\V1\AgencyService;

use App\Http\Controllers\Controller;
use App\Jobs\CallbackOrderRobloxBuyGemPet;
use App\Jobs\ServiceAuto\RobloxJob;
use App\Library\ChargeGameGateway\RobloxGate;
use App\Library\Helpers;

use App\Library\MediaHelpers;
use App\Models\Bot;
use App\Models\Bot_UserNap;
use App\Models\Item;
use App\Models\KhachHang;
use App\Models\LangLaCoin_KhachHang;
use App\Models\NinjaXu_KhachHang;
use App\Models\Nrogem_AccBan;
use App\Models\Nrogem_GiaoDich;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Roblox_Bot;
use App\Models\Roblox_Order;
use App\Models\SubItem;
use App\Models\Txns;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

use Log;


class IndexController extends Controller
{

    //    protected sign='afbhfjdjdjemcme';
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    //api nhận đơn dich vụ bán ngọc... từ các shop
    public function buy(Request $request)
    {

        //debug thì mở cái này
//        $myfile = fopen(storage_path() . "/logs/callback-services-auto-shop".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
//        $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
//        fwrite($myfile, $txt . "\n");
//        fclose($myfile);

        if(!$request->filled('request_id')){
            return response()->json([
                'status'=>0,
                'message' => __('Vui lòng cung cấp request id đối soát'),
            ]);
        }

        $tele_order_id = 'N/A';
        $khothanhvien = 'N/A';
        $tele_service = 'N/A';
        $serviceword = 'N/A';
        $price_service = 'N/A';

        //Start transaction!
        DB::beginTransaction();
        try {

            $userTransaction = User::where('id',$request->user_id)->lockForUpdate()->first();

            if(!$userTransaction){
                DB::rollback();
                return response()->json([
                    'status'=>0,
                    'message' => __('Tài khoản đại lý không tồn tại'),
                ]);
            }
            $khothanhvien = $userTransaction->username;
            if( $userTransaction->partner_key_service != $request->sign){
                DB::rollback();
                return response()->json([
                    'status'=>0,
                    'message' => __('Tài khoản hoặc key mã hóa dữ liệu ko đúng'),
                ]);
            }

            $checkTranid = Order::where('module', config('module.service-purchase.key'))
                ->where('author_id',$userTransaction->id)
                ->where('request_id_customer',$request->get('request_id'))->first();

            if($checkTranid){

                DB::rollback();
                return response()->json([
                    'status'=>0,
                    'message' => __('Transaction ID đã tồn tại'),
                ]);
            }

            //idkey nrogem ninjaxu nrocoin roblox_gem_pet roblox_buyserver roblox_buygamepass

            $service = Item::where('idkey', $request->idkey)
                ->where('status', '=', 1)
                ->where('module', '=', config('module.service.key'))
                ->firstOrFail();

            $tele_service = $service->title??'N/A';

            $filter_type = Helpers::DecodeJson('filter_type', $service->params);

            //////////////////////////Điền tiền///////////////////////////////////

            if ($filter_type == 7)
            {
                $amount=(int)$request->amount;
                $server = (int)$request->get('server')-1;
                $server_data=Helpers::DecodeJson("server_data" , $service->params);
                $input_pack_rate=Helpers::DecodeJson("input_pack_rate", $service->params);

                if( Helpers::DecodeJson("server_mode", $service->params)==1 )
                {
                    if( (strpos($server_data[$server], '[DELETE]') === true)){
                        DB::rollback();
                        return response()->json([
                            'status'=>0,
                            'message' => __('Máy chủ bạn đã chọn không hợp lệ.Xin vui lòng chọn lại máy chủ'),
                        ]);
                    }
                }

                //tính số tiền cần trừ
                if($userTransaction->is_agency_buygem == 1 && $service->idkey == 'nrogem'){

                    //////check hệ số mua ngọc cho đại lý//////
                    $agency_discount=json_decode($userTransaction->buygem_discount);

                    if(isset($agency_discount[$server]) && $agency_discount[$server]>0){
                        /// số tiền= số ngọc/ hệ số của user  đại lý *1000
                        $discount_final=$agency_discount[$server];
                        $price=$amount / $discount_final*1000/$input_pack_rate;

                    }
                    else{

                        DB::rollback();
                        return response()->json([
                            'status'=>0,
                            'message' => __('Chưa cấu hình hệ số cho đại lý'),
                        ]);
                    }
                }
                elseif($userTransaction->is_agency_ninjaxu==1 && $service->idkey== 'ninjaxu'){

                    //////check hệ số mua ngọc cho đại lý//////
                    $agency_discount=json_decode($userTransaction->ninjaxu_discount);

                    if(isset($agency_discount[$server]) && $agency_discount[$server]>0){

                        $discount_final=$agency_discount[$server];
                        /// số tiền= số ngọc/ hệ số của user  đại lý *1000
                        $price=$amount / $discount_final*1000/$input_pack_rate;

                    }
                    else{

                        DB::rollback();
                        return response()->json([
                            'status'=>0,
                            'message' => __('Chưa cấu hình hệ số cho đại lý'),
                        ]);
                    }
                }
                elseif($userTransaction->is_agency_nrocoin==1 && $service->idkey== 'nrocoin'){

                    //////check hệ số mua ngọc cho đại lý//////
                    $agency_discount=json_decode($userTransaction->nrocoin_discount);

                    if(isset($agency_discount[$server]) && $agency_discount[$server]>0){

                        $discount_final=$agency_discount[$server];
                        /// số tiền= số ngọc/ hệ số của user  đại lý *1000
                        $price=$amount / $discount_final*1000/$input_pack_rate;

                    }
                    else{

                        DB::rollback();
                        return response()->json([
                            'status'=>0,
                            'message' => __('Chưa cấu hình hệ số cho đại lý'),
                        ]);
                    }
                }
                //nếu tài khoản không dùng chiết khấu đại lý
                else{

                    if (Helpers::DecodeJson("server_mode", $service->params) == 1 && Helpers::DecodeJson("server_price", $service->params) == 1) {
                        $s_price = Helpers::DecodeJson("price" . $server, $service->params);
                        $s_discount = Helpers::DecodeJson("discount" . $server, $service->params);

                    } else {
                        $s_price = Helpers::DecodeJson("price", $service->params);
                        $s_discount = Helpers::DecodeJson("discount", $service->params);
                    }
                    //lấy hệ số đầu tiên của server
                    $discount_final=$s_discount[0];

                    if($discount_final <=0 || str_contains($discount_final,',')){
                        DB::rollback();
                        return response()->json([
                            'status'=>0,
                            'message' => __('Hệ số cấu hình cho dịch vụ này không đúng.Vui lòng kiểm tra lại'),
                        ]);
                    }

                    /// số tiền= số ngọc/ hệ số của user  đại lý *1000
                    $price=$amount / $discount_final*1000/$input_pack_rate;

                }

                //end tính số tiền cần trừ
                $price= ceil($price);

                if($price <=0 ){

                    DB::rollback();
                    return response()->json([
                        'status'=>0,
                        'message' => __('Số tiền thanh toán không hợp lệ'),
                    ]);
                }


                //check số tiền nhỏ  và lớn nhất
                $input_pack_min = Helpers::DecodeJson("input_pack_min", $service->params);
                $input_pack_max = Helpers::DecodeJson('input_pack_max', $service->params);

                if($price < (int)$input_pack_min || $price > (int)$input_pack_max){

                    DB::rollback();
                    return response()->json([
                        'status'=>0,
                        'message' => __('Vui lòng thanh toán dịch vụ trong khoảng tiền từ ').number_format($input_pack_min).__(' đến ').number_format($input_pack_max),
                    ]);
                }

                //end check

                //tính giá của dịch vụ
                $total = $amount;//số vật phẩm

                //Kiểm tra thông tin nhập lên
                $send_name=Helpers::DecodeJson("send_name" , $service->params);
                $send_type=Helpers::DecodeJson("send_type" , $service->params);

                $customer_info=[];

                if(!empty($send_name) && count($send_name)>0){
                    for ($i = 0; $i < count($send_name); $i++) {

                        if($send_type[$i]==4){ //nếu  nó là kiểu upload ảnh

                            $info= MediaHelpers::imageBase64($request->get('customer_data'.$i));
                            $customer_info['customer_data' . $i] = $info;

                        }
                        else{
                            if($request->get('customer_data'.$i)=="" ||$request->get('customer_data'.$i)==null){

                                DB::rollback();
                                return response()->json([
                                    'status'=>0,
                                    'message' => __('Vui lòng điền đầy đủ thông tin yêu cầu để thanh toán'),
                                ]);
                            }
                            $customer_info['customer_data'.$i]=$request->get('customer_data'.$i);
                        }
                    }
                }

                if( Helpers::DecodeJson("server_mode", $service->params)==1 )
                {
                    $customer_info['server']=$server_data[$server];

                }

                if ($userTransaction->checkBalanceValid() == false) {

                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => __('Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý'),
                    ]);

                }

                if ($userTransaction->balance < $price) {

                    $message="[" . Carbon::now() . "] "."[" . $request->root() . "] Tạo đơn thất bại số dư tài khoản không đủ tiền thanh toán dịch vụ - " . $userTransaction->username . " đã tạo lệnh dịch vụ ".$service->title." - "."Tổng tiền: ".number_format($price)." (Số dư hiện tại: ".number_format($userTransaction->balance).")" ;
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_id_balance_daily'));
                    DB::rollback();
                    return response()->json([
                        'status'=>0,
                        'message' => __('Bạn không đủ tiền để thanh toán.Vui lòng nạp thêm tiền vào tài khoản'),
                    ]);

                }

                //trừ tiền user
                $userTransaction->balance = $userTransaction->balance - $price;
                $userTransaction->balance_out = $userTransaction->balance_out + $price;
                $userTransaction->save();

                //check giao dich tự động && custom service
                $input_auto = $service->gate_id;

                if ($input_auto == 1){

                    //thêm mới log lịch sử service
                    $order = Order::create([
                        'idkey' => $service->idkey,
                        'ref_id' => $service->id,
                        'title'=>$service->title,
                        'params'=>json_encode($customer_info,JSON_UNESCAPED_UNICODE),
                        'price'=>$price,
                        'price_base'=>$total,//lưu thông tin số giá trị vật phẩm: ngọc,xu,....
                        'sticky'=>1,//lưu thông tin nếu mua bằng api
                        'author_id'=>$userTransaction->id,
                        'status'=>1,
                        'ratio'=>$discount_final,
                        'position'=>$server,
                        'position1'=>$request->rut,//lưu thông tin đơn rút hay mua
                        'module'=> config('module.service-purchase.key'),
                        'url'=>$request->callback,
                        'app_client'=>$request->getHost(),
                        'process_at'=>Carbon::now(),
                        'request_id_customer'=>$request->request_id,
                        'request_id_provider'=>$request->request_id,
                        'gate_id' => $input_auto == 1 ?? 0,
                    ]);

                    $tele_order_id = $order->id;

                    //set tiến độ
                    OrderDetail::create([
                        'order_id'=>$order->id,
                        'module' => config('module.service-workflow.key'),
                        'author_id'=>$userTransaction->id,
                        'status' => 1,
                    ]);

                    //set tên công việc
                    OrderDetail::create([
                        'order_id'=>$order->id,
                        'module' => config('module.service-workname.key'),
                        'title' => number_format($total) ." ".Helpers::DecodeJson("filter_name", $service->params),
                        'price' => $price,
                    ]);

                    if($input_auto==1 && $service->idkey == 'nrocoin'){

                        $khachhang = KhachHang::create([
                            'server'=>$server+1,
                            'order_id'=>$order->id,
                            'uname'=>$request->customer_data0,
                            'money'=>$total,
                            'status'=>"chuanhan",
                        ]);
                    }
                    elseif($input_auto==1 && $service->idkey == 'nrogem'){

                        //lẩy random bot xử lý

                        $dataBot= Nrogem_AccBan::where('server', $request->get('server'))
                            ->where(function($q){
                                $q->orWhere('ver','!=','');
                                $q->orWhereNotNull('ver');
                            })
                            ->where('status','on')
                            ->inRandomOrder()
                            ->first();

                        if(!$dataBot){

                            DB::rollback();
                            return response()->json([
                                'status'=>0,
                                'message' => __('Không có bot bán Ngọc hoạt động.Vui lòng thử lại'),
                            ]);
                        }

                        //lưu thông tin bot ver để hiển thị cho dễ
                        $order->info_plus=$dataBot->ver;
                        $order->save();

                        $nrogem_GiaoDich = Nrogem_GiaoDich::create([
                            'order_id'=>$order->id,
                            'acc'=>$request->customer_data0,
                            'pass'=>$request->customer_data1,
                            'server'=>$server+1,
                            'gem'=>$total,
                            'status'=>"chualogin",
                            'ver'=>$dataBot->ver,
                        ]);

                    }
                    elseif($input_auto==1 && $service->idkey == 'langlacoin'){

                        $langla_khachhang = LangLaCoin_KhachHang::create([
                            'server'=>$server+1,
                            'order_id'=>$order->id,
                            'uname'=>$request->customer_data0,
                            'coin'=>$total,
                            'status'=>"chuanhan",
                        ]);

                    }
                    elseif($input_auto==1 && $service->idkey == 'ninjaxu'){

                        $ninjaxu_khachhang = NinjaXu_KhachHang::create([
                            'server'=>$server+1,
                            'order_id'=>$order->id,
                            'uname'=>$request->customer_data0,
                            'coin'=>$total,
                            'status'=>"chuanhan",
                        ]);

                    }
                    elseif($input_auto==1 && $service->idkey == 'roblox_buyserver'){
//RBX
                        if (isset($service->url_type) && in_array($service->url_type,config('module.service-purchase-auto.rbx_api'))){
                            $order->payment_type = $service->url_type;
                        }

                        $order->idkey = $service->idkey;
                        $order->save();
                        $placeId = null;
                        $server = $request->get('server')??'';
                        if ($request->filled('placeId')){
                            $placeId = $request->get('placeId')??'';
                        }

                        $roblox_order = Roblox_Order::create([
                            'order_id'=>$order->id,
                            'server'=>$server,
                            'uname'=>$request->customer_data0,
                            'money'=>$total,
                            'phone'=>$placeId??'',
                            'type_order'=>3,
                            'status'=>"chuanhan",
                            'shop_id' => $request->shop_id,
                        ]);

                        //tạo tnxs
                        $txns = Txns::create([
                            'trade_type' => 'service_purchase',//Thanh toán dịch vụ
                            'is_add' => '0',//Trừ tiền
                            'user_id' => $userTransaction->id,
                            'amount' => $price,
                            'real_received_amount' => $price,
                            'last_balance' => $userTransaction->balance,
                            'description' => "Thanh toán dịch vụ #" . $order->id,
                            'ip' => $request->getClientIp(),
                            'order_id' => $order->id,
                            'status' => 1
                        ]);

                        DB::commit();

                        $this->dispatch(new RobloxJob($order->id));

                        if($userTransaction->balance<1000000){

                            $message="[" . Carbon::now() . "] "."[" . $request->root() . "] " . $userTransaction->username . " đã tạo lệnh dịch vụ ".$service->title." - "."Tổng tiền: ".number_format($price)." (Số dư hiện tại: ".number_format($userTransaction->balance).")" ;
                            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_id_balance_daily'));

                        }

                        return response()->json([
                            'status'=>2,
                            'message' => __('Thực hiện thanh toán thành công.Chờ xử hệ thống xử lý'),
                        ]);

                        //check xem có đúng link mua server ko
//                        $server_id=RobloxGate::detectLink($request->customer_data0);
//
//                        if($server_id!=""){
//
//
//
//                        }
//                        else{
//                            DB::rollBack();
//                            return response()->json([
//                                'status' => 0,
//                                'message' => __('Link server roblox không hợp lệ.Vui lòng thử lại'),
//                            ]);
//
//                        }

                    }
                    elseif($input_auto==1 && $service->idkey == 'roblox_buygamepass'){
//RBX
                        if (isset($service->url_type) && in_array($service->url_type,config('module.service-purchase-auto.rbx_api'))){
                            $order->payment_type = $service->url_type;
                        }

                        $order->idkey = $service->idkey;
                        $order->save();

                        $ver = 1;
                        $server = $request->get('server')??'';
                        if ($request->filled('placeId')){
                            $server = $request->get('placeId')??'';
                        }
                        if ($request->filled('ver')){
                            $ver = 2;
                        }

                        $roblox_order = Roblox_Order::create([
                            'ver'=>$ver,
                            'order_id'=>$order->id,
                            'server'=>$server,
                            'uname'=>$request->customer_data0,
                            'money'=>$total,
                            'phone'=>"",
                            'type_order'=>3,
                            'status'=>"chuanhan"
                        ]);

                        //tạo tnxs
                        $txns = Txns::create([
                            'trade_type' => 'service_purchase',//Thanh toán dịch vụ
                            'is_add' => '0',//Trừ tiền
                            'user_id' => $userTransaction->id,
                            'amount' => $price,
                            'real_received_amount' => $price,
                            'last_balance' => $userTransaction->balance,
                            'description' => "Thanh toán dịch vụ #" . $order->id,
                            'ip' => $request->getClientIp(),
                            'order_id' => $order->id,
                            'status' => 1
                        ]);

                        DB::commit();

                        $this->dispatch(new RobloxJob($order->id));

                        if($userTransaction->balance<1000000){
                            $message="[" . Carbon::now() . "] "."[" . $request->root() . "] " . $userTransaction->username . " đã tạo lệnh dịch vụ ".$service->title." - "."Tổng tiền: ".number_format($price)." (Số dư hiện tại: ".number_format($userTransaction->balance).")" ;
                            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_id_balance_daily'));
                        }

                        return response()->json([
                            'status'=>2,
                            'message' => __('Thực hiện thanh toán thành công.Chờ xử hệ thống xử lý'),
                        ]);

                        //check xem có đúng link mua server ko
//                        $aBot = Roblox_Bot::where('status',6)
//                            ->where('account_type',1)
//                            ->orderBy('ver','asc')
//                            ->first();

//                        $result = RobloxGate::detectUsernameRoblox($request->customer_data0,null,$aBot->cookies??'');

//                    $result = RobloxGate::detectUsernameRobloxV2($request->customer_data0,$total,null,$aBot->cookies??'');
//
//                    if($result &&  $result->status==1){
//
//
//
//                    }
//                    else{
//
//                        DB::rollBack();
//                        return response()->json([
//                            'status' => 0,
//                            'message' => __('Link server roblox không hợp lệ.Vui lòng thử lại'),
//                        ]);
//
//                    }

                    }
                    elseif($input_auto==1 && $service->idkey == 'robux_premium_auto'){

                        $order->idkey = $service->idkey;
                        $order->save();

                        $roblox_order = Roblox_Order::create([
                            'order_id'=>$order->id,
                            'server'=>$request->get('server'),
                            'uname'=>$request->customer_data0,
                            'money'=>$total,
                            'phone'=>"",
                            'type_order'=>11,
                            'status'=>"chuanhan"
                        ]);

                        //tạo tnxs
                        $txns = Txns::create([
                            'trade_type' => 'service_purchase',//Thanh toán dịch vụ
                            'is_add' => '0',//Trừ tiền
                            'user_id' => $userTransaction->id,
                            'amount' => $price,
                            'real_received_amount' => $price,
                            'last_balance' => $userTransaction->balance,
                            'description' => "Thanh toán dịch vụ #" . $order->id,
                            'ip' => $request->getClientIp(),
                            'order_id' => $order->id,
                            'status' => 1
                        ]);

                        DB::commit();

//                        $this->dispatch(new RobloxJob($order->id));

                        if($userTransaction->balance<1000000){
                            $message="[" . Carbon::now() . "] "."[" . $request->root() . "] " . $userTransaction->username . " đã tạo lệnh dịch vụ ".$service->title." - "."Tổng tiền: ".number_format($price)." (Số dư hiện tại: ".number_format($userTransaction->balance).")" ;
                            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_id_balance_daily'));
                        }

                        return response()->json([
                            'status'=>2,
                            'message' => __('Thực hiện thanh toán thành công.Chờ xử hệ thống xử lý'),
                        ]);

                        //check xem có đúng link mua server ko
//                        $aBot = Roblox_Bot::where('status',6)
//                            ->where('account_type',1)
//                            ->orderBy('ver','asc')
//                            ->first();

//                        $result = RobloxGate::detectUsernameRoblox($request->customer_data0,null,$aBot->cookies??'');

//                    $result = RobloxGate::detectUsernameRobloxV2($request->customer_data0,$total,null,$aBot->cookies??'');
//
//                    if($result &&  $result->status==1){
//
//
//
//                    }
//                    else{
//
//                        DB::rollBack();
//                        return response()->json([
//                            'status' => 0,
//                            'message' => __('Link server roblox không hợp lệ.Vui lòng thử lại'),
//                        ]);
//
//                    }

                    }
                    elseif($input_auto==1 && $service->idkey == 'roblox_gem_pet'){

                        //bỏ dấu @ đầu chuỗi ký tự.
                        $uname = $request->customer_data0;

                        if (substr($uname, 0, 1) === '@') {
                            $uname = substr_replace($uname, '', 0, 1);
                        }

                        $order->idkey = $service->idkey;
                        $order->save();
                        $roblox_order = Roblox_Order::create([
                            'order_id'=>$order->id,
                            'server'=>$uname,
                            'uname'=>$uname,
                            'money'=>$total,
                            'phone'=>"",
                            'type_order'=>4,
                            'status'=>"chuanhan"
                        ]);

                    }
                    else{
                        DB::rollback();
                        return response()->json([
                            'status'=>0,
                            'message' => __('Loại dịch vụ không hợp lệ'),
                        ]);
                    }
                    //tạo tnxs
                    $txns = Txns::create([
                        'trade_type' => 'service_purchase',//Thanh toán dịch vụ
                        'is_add' => '0',//Trừ tiền
                        'user_id' => $userTransaction->id,
                        'amount' => $price,
                        'real_received_amount' => $price,
                        'last_balance' => $userTransaction->balance,
                        'description' => "Thanh toán dịch vụ #" . $order->id,
                        'ip' => $request->getClientIp(),
                        'order_id' => $order->id,
                        'status' => 1
                    ]);
                }
                else{
//Số tính tiền cho cộng tác viên bằng với số tiền
                    $priceCTV = $price;
                    $provider = $service->idkey;
                    $type_refund = $request->get('type_refund');

                    //thêm mới log lịch sử service
                    $order = Order::create([
                        'idkey' => $provider??'',
                        'request_id' => $userTransaction->id.time() . rand(10000, 99999),
                        'ref_id' => $service->id,
                        'title' => $service->title,
                        'params' => $customer_info,
                        'price_base' => $total,
                        'price' => $price,
                        'price_ctv' => !empty($provider) ? $priceCTV : 0,
                        'author_id' => $userTransaction->id,
                        'status' => 1,
                        'position' => $server,
                        'module' => config('module.service-purchase.key'),
                        'gate_id' => $input_auto == 1 ?? 0,
                        'sticky'=>1,//lưu thông tin nếu mua bằng api
                        'ratio'=>$discount_final,
                        'position1'=>$request->rut,//lưu thông tin đơn rút hay mua
                        'url'=>$request->callback,
                        'app_client'=>$request->getHost(),
                        'process_at'=>Carbon::now(),
                        'request_id_customer'=>$request->request_id,
                        'request_id_provider'=>$request->request_id,
                        'type_refund' => $type_refund == 1 ?? 0,
                    ]);

                    $tele_order_id = $order->id;

                    $order->txns()->create([
                        'trade_type' => 'service_purchase', //Thanh toán dịch vụ
                        'user_id' => $userTransaction->id,
                        'is_add' => '0',//Trừ tiền
                        'amount' => $price,
                        'real_received_amount' => $price,
                        'last_balance' => $userTransaction->balance,
                        'description' => "Thanh toán dịch vụ #" . $order->id .' ('.$service->title.')',
                        'ip' => $request->getClientIp(),
                        'order_id' => $order->id,
                        'status' => 1

                    ]);

                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'author_id' => $userTransaction->id,
                        'status' => 1,

                    ]);
                    //set tên công việc
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workname.key'),
                        'title' => number_format($total) . " " . Helpers::DecodeJson("filter_name", $service->params),
                        'unit_price' => $price,
                        'unit_price_ctv' => !empty($provider) ? $priceCTV : 0,
                    ]);

                    $serviceword = number_format($total) . " " . Helpers::DecodeJson("filter_name", $service->params);
                }
            }
            elseif ($filter_type == 4){

                $type_refund = $request->get('type_refund')??0;
                $keyword = $request->amount;
                $server = $request->get('server');

                $provider = $service->idkey;
                //check giao dich tự động && custom service
                $input_auto = $service->gate_id;
                $s_service_idkey = [];
                //check keyword dùng server và tính giá khác nhau
                if (Helpers::DecodeJson("server_mode", $service->params) == 1 && Helpers::DecodeJson("server_price", $service->params) == 1) {
                    $s_keyword = Helpers::DecodeJson("keyword" . $server, $service->params);
                    if (!empty(Helpers::DecodeJson("service_idkey" . $server, $service->params))){
                        $s_service_idkey = Helpers::DecodeJson("service_idkey" . $server, $service->params);
                    }
                }
                else {
                    $s_keyword = Helpers::DecodeJson("keyword", $service->params);
                    if (!empty(Helpers::DecodeJson("service_idkey", $service->params))){
                        $s_service_idkey = Helpers::DecodeJson("service_idkey", $service->params);
                    }
                }

                if (empty($s_keyword)){
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => __('Dịch vụ chưa cấu hình keyword'),
                    ]);
                }

                //Tìm vị trí index.

                $selectedCustomer = array_search($keyword, $s_keyword);

                if ($selectedCustomer === false){
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => __('Không tìm thấy keyword'),
                    ]);
                }

                //check option dùng server và tính giá khác nhau
                if (Helpers::DecodeJson("server_mode", $service->params) == 1 && Helpers::DecodeJson("server_price", $service->params) == 1) {
                    $s_price = Helpers::DecodeJson("price" . $server, $service->params);//giá
                    $s_praise_price = Helpers::DecodeJson("praise_price" . $server, $service->params);
                } else {
                    $s_price = Helpers::DecodeJson("price", $service->params);
                    $s_praise_price = Helpers::DecodeJson("praise_price", $service->params);
                }

                //lấy thông tin dịch vụ gốc
                if (Helpers::DecodeJson("server_mode", $service->params) == 1 && Helpers::DecodeJson("server_price", $service->params) == 1) {
                    $s_price_ctv = Helpers::DecodeJson("price" . $server, $service->params);
                    $s_praise_price_ctv = Helpers::DecodeJson("praise_price" . $server, $service->params);
                } else {
                    $s_price_ctv = Helpers::DecodeJson("price",$service->params);
                    $s_praise_price_ctv = Helpers::DecodeJson("praise_price", $service->params);
                }

                ////*******Tính tiền cho KHÁCH *****/////////

                //check giá trị tiền phù hợp với cấu hình dịch vụ
                if (isset($s_price[$selectedCustomer]) && $s_price[$selectedCustomer] > 0) {
                    $price = $s_price[$selectedCustomer];
                    $praise_price = $s_praise_price[$selectedCustomer];
                }
                else {

                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => __('Lựa chọn dịch vụ không hợp lệ')
                    ]);
                }


                //ốp tỉ giá theo cấu hình
//                $price=floor($price*$ratioOfShop->ratio_percent/100)+$ratioOfShop->additional_amount;

                //check số tiền âm
                if ($price <= 0) {

                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => __('Số tiền thanh toán không hợp lệ')
                    ]);
                }
                ////*******END Tính tiền cho KHÁCH*****/////////
                $priceCTV = 0;

                ////*******Tính tiền cho CTV*****/////////
                if ($input_auto != 1 ) {

                    //check giá trị tiền phù hợp với cấu hình dịch vụ gốc và tính tiền cho ctv

                    if (isset($s_price_ctv[$selectedCustomer]) && $s_price_ctv[$selectedCustomer] > 0) {
                        $priceCTV = $s_price_ctv[$selectedCustomer];
                        $praise_price_ctv = $s_praise_price_ctv[$selectedCustomer];
                    } else {
                        DB::rollback();
                        return response()->json([
                            'status' => 0,
                            'message' => __('Lựa chọn dịch vụ không hợp lệ (Lỗi cấu hình số tiền CTV)')
                        ]);
                    }

                    if ($priceCTV <= 0) {

                        DB::rollback();
                        return response()->json([
                            'status' => 0,
                            'message' => __('Lựa chọn dịch vụ CTV không hợp lệ.Vui lòng chọn lại')
                        ]);
                    }

                    ////*******END tính tiền CTV *****/////////

                }

                $namePacket = Helpers::DecodeJson("name", $service->params);

                $praisePricePacket = Helpers::DecodeJson("praise_price", $service->params);
                //Kiểm tra thông tin nhập lên
                $send_name = Helpers::DecodeJson("send_name", $service->params);
                $send_type = Helpers::DecodeJson("send_type", $service->params);
                $server_data = Helpers::DecodeJson("server_data", $service->params);

                if (Helpers::DecodeJson("server_mode", $service->params) == 1) {

                    if(!($server_data[$server]??null)){
                        return response()->json([
                            'status' => 0,
                            'message' => __('Vui lòng chọn máy chủ của dịch vụ thanh toán')
                        ]);
                    }

                    if ((strpos($server_data[$server], '[DELETE]') === true)) {

                        DB::rollback();
                        return response()->json([
                            'status' => 0,
                            'message' => 'Máy chủ bạn đã đóng hoặc không hợp lệ.Xin vui lòng chọn lại máy chủ'
                        ]);
                    }
                }

                $customer_info = [];

                if (!empty($send_name) && count($send_name) > 0) {
                    for ($i = 0; $i < count($send_name); $i++) {

                        if ($send_type[$i] == 4) { //nếu  nó là kiểu upload ảnh

                            $info= MediaHelpers::imageBase64($request->get('customer_data'.$i));

                            $customer_info['customer_data'.$i]=$info;
                        } else {

                            if ($request->get('customer_data' . $i) == "" || $request->get('customer_data' . $i) == null) {

                            }else{
                                if ($send_type[$i] == 8){
                                    if (preg_match('/[^\x00-\x7F]+/u', $request->get('customer_data' . $i)) ||  strpos($request->get('customer_data' . $i), ' ') !== false) {
                                        DB::rollback();
                                        return response()->json([
                                            'status' => 0,
                                            'message' => __('Quý khách vui lòng điền đúng định dạng tài khoản là dạng viết liền không dấu')
                                        ]);
                                    }
                                }

                                $customer_info['customer_data' . $i] = htmlentities($request->get('customer_data' . $i));
                            }
                        }
                    }
                }

                if (Helpers::DecodeJson("server_mode", $service->params) == 1) {
                    $customer_info['server'] = $server_data[$server];
                }

                //trừ tiền user

                if ($userTransaction->checkBalanceValid() == false) {
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => __('Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý')
                    ]);

                }

                if ($userTransaction->balance < $price) {

                    $message="[" . Carbon::now() . "] "."[" . $request->root() . "] Tạo đơn thất bại số dư tài khoản không đủ tiền thanh toán dịch vụ - " . $userTransaction->username . " đã tạo lệnh dịch vụ ".$service->title." - "."Tổng tiền: ".number_format($price)." (Số dư hiện tại: ".number_format($userTransaction->balance).")" ;
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_id_balance_daily'));

                    DB::rollBack();
                    return response()->json([
                        'status' => 0,
                        'message' => __('Bạn không đủ tiền để thanh toán.Vui lòng nạp thêm tiền vào tài khoản')
                    ]);

                }
                //trừ tiền user
                $userTransaction->balance = $userTransaction->balance - $price;
                $userTransaction->balance_out = $userTransaction->balance_out + $price;
                $userTransaction->save();
                $price_service = $price;

                $customer_info['index_name'] = $selectedCustomer;
                $customer_info['keyword'] = $keyword;
                if (count($s_service_idkey) > 0){
                    $customer_info['service_idkey'] = $s_service_idkey[$selectedCustomer];
                }

                //thêm mới order service
                $order = Order::create([
                    'idkey' => $provider??'',
                    'request_id' => $userTransaction->id.time() . rand(10000, 99999),
                    'ref_id' => $service->id,
                    'title' => $service->title,
                    'description' => (isset($namePacket[$selectedCustomer]) ? $namePacket[$selectedCustomer] : ""),
                    'params' => json_encode($customer_info,JSON_UNESCAPED_UNICODE),
                    'price' => $price,
                    'price_ctv' => $priceCTV,
                    'author_id' => $userTransaction->id,
                    'position' => $server,
                    'status' => 1, // 'Đang chờ xử lý'
                    'module' => config('module.service-purchase.key'),
                    'gate_id' => $input_auto == 1 ?? 0,
                    'sticky'=>1,//lưu thông tin nếu mua bằng api
                    'position1'=>$request->rut,//lưu thông tin đơn rút hay mua
                    'url'=>$request->callback,
                    'app_client'=>$request->getHost(),
                    'process_at'=>Carbon::now(),
                    'request_id_customer'=>$request->request_id,
                    'request_id_provider'=>$request->request_id,
                    'type_refund' => $type_refund == 1 ?? 0,
                ]);

                $tele_order_id = $order->id;

                $order->txns()->create([
                    'trade_type' => 'service_purchase', //Thanh toán dịch vụ
                    'user_id' => $userTransaction->id,
                    'is_add' => '0',//Trừ tiền
                    'amount' => $price,
                    'real_received_amount' => $price,
                    'last_balance' => $userTransaction->balance,
                    'description' =>  "Thanh toán dịch vụ #" . $order->id .' ('.$service->title.')',
                    'ip' => $request->getClientIp(),
                    'order_id' => $order->id,
                    'status' => 1
                ]);

                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'author_id' => $userTransaction->id,
                    'status' => 1,

                ]);

                //set tên công việc
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workname.key'),
                    'title' => (isset($namePacket[$selectedCustomer]) ? $namePacket[$selectedCustomer] : ""),
                    'unit_price' => $price,
                    'unit_price_ctv' => !empty($provider) ? $priceCTV : 0,
                ]);

                $serviceword = (isset($namePacket[$selectedCustomer]) ? $namePacket[$selectedCustomer] : "");

                if($input_auto==1){

                    if ($service->idkey == 'huge_psx_auto'){
                        //bỏ dấu @ đầu chuỗi ký tự.
                        $uname = $request->customer_data0;

                        if (substr($uname, 0, 1) === '@') {
                            $uname = substr_replace($uname, '', 0, 1);
                        }

                        $order->idkey = $service->idkey;
                        $order->save();

                        $roblox_order = Roblox_Order::create([
                            'order_id'=>$order->id,
                            'server'=>$uname,
                            'uname'=>$uname,
                            'money'=>$selectedCustomer,
                            'phone'=>isset($namePacket[$selectedCustomer]) ? $namePacket[$selectedCustomer] : "",
                            'type_order'=>5,
                            'status'=>"chuanhan"
                        ]);

                    }
                    elseif ($service->idkey == 'huge_99_auto'){
                        //bỏ dấu @ đầu chuỗi ký tự.
                        $uname = $request->customer_data0;

                        if (substr($uname, 0, 1) === '@') {
                            $uname = substr_replace($uname, '', 0, 1);
                        }

                        $order->idkey = $service->idkey;
                        $order->save();

                        $roblox_order = Roblox_Order::create([
                            'order_id'=>$order->id,
                            'server'=>$uname,
                            'uname'=>$uname,
                            'money'=>$selectedCustomer,
                            'phone'=>isset($namePacket[$selectedCustomer]) ? $namePacket[$selectedCustomer] : "",
                            'type_order'=>7,
                            'status'=>"chuanhan"
                        ]);
                    }
                    elseif ($service->idkey == 'robux_premium_auto'){
                        //bỏ dấu @ đầu chuỗi ký tự.
                        $uname = $request->customer_data0;

                        if (substr($uname, 0, 1) === '@') {
                            $uname = substr_replace($uname, '', 0, 1);
                        }

                        $order->idkey = $service->idkey;
                        $order->save();

                        $roblox_order = Roblox_Order::create([
                            'order_id'=>$order->id,
                            'server'=>$uname,
                            'uname'=>$uname,
                            'money'=>$praise_price??'',
                            'phone'=>isset($s_praise_price[$selectedCustomer]) ? $s_praise_price[$selectedCustomer] : "",
                            'type_order'=>11,
                            'status'=>"chuanhan"
                        ]);
                    }
                    elseif ($service->idkey == 'gem_unist_auto'){

                        if (!$request->filled('customer_data0')) {
                            DB::rollback();
                            return response()->json([
                                'status' => 0,
                                'message' => __('Vui lòng gửi thông tin tài khoản')
                            ]);
                        }

                        //bỏ dấu @ đầu chuỗi ký tự.
                        $uname = $request->customer_data0;

                        if (substr($uname, 0, 1) === '@') {
                            $uname = substr_replace($uname, '', 0, 1);
                        }

                        $praise_price = 0;
                        if (isset($s_praise_price[$selectedCustomer])){
                            $ver = 0;
                            $praise_price =  $s_praise_price[$selectedCustomer];
                            $order->price_base = $praise_price??0;
                        }else{
                            $ver = 1;
                            $praise_price = isset($namePacket[$selectedCustomer]) ? $namePacket[$selectedCustomer] : "";
                        }

                        $order->idkey = $service->idkey;
                        $order->save();

                        $roblox_order = Roblox_Order::create([
                            'ver'=>$ver,
                            'order_id'=>$order->id,
                            'server'=>$uname,
                            'uname'=>$uname,
                            'money'=>$selectedCustomer,
                            'phone'=>$praise_price,
                            'bot_handle'=>null,
                            'type_order'=>8,
                            'status'=>"chuanhan"
                        ]);
                    }
                    elseif ($service->idkey == 'unist_auto'){
                        //bỏ dấu @ đầu chuỗi ký tự.
                        $uname = $request->customer_data0;

                        if (substr($uname, 0, 1) === '@') {
                            $uname = substr_replace($uname, '', 0, 1);
                        }

                        $praise_price = 0;
                        if (isset($s_praise_price[$selectedCustomer])){
                            $ver = 0;
                            $praise_price =  $s_praise_price[$selectedCustomer];
                        }else{
                            $ver = 1;
                            $praise_price = isset($namePacket[$selectedCustomer]) ? $namePacket[$selectedCustomer] : "";
                        }

                        $order->idkey = $service->idkey;
                        $order->save();

                        $roblox_order = Roblox_Order::create([
                            'ver'=>$ver,
                            'order_id'=>$order->id,
                            'server'=>$uname,
                            'uname'=>$uname,
                            'money'=>$selectedCustomer,
                            'bot_handle'=>null,
                            'phone'=>$praise_price,
                            'type_order'=>9,
                            'status'=>"chuanhan"
                        ]);
                    }
                    elseif ($service->idkey == 'roblox_gem_pet'){
                        //bỏ dấu @ đầu chuỗi ký tự.
                        $uname = $request->customer_data0;

                        if (substr($uname, 0, 1) === '@') {
                            $uname = substr_replace($uname, '', 0, 1);
                        }

                        $order->idkey = $service->idkey;

                        $praise_price_packet = 0;
                        $price_input = 0;
                        if (isset($praisePricePacket[$selectedCustomer])){
                            $praise_price_packet = $praisePricePacket[$selectedCustomer];
//                            isset($praisePricePacket[$selectedCustomer]) ? $praisePricePacket[$selectedCustomer] : "";
                            // Loại bỏ ký tự "B" và chuyển đổi thành số
                            $valueInBillion = (float) str_replace('B', '', $praise_price_packet);

                            // Quy định B = 1000000000
                            $price_input = $valueInBillion * 1000000000;

                            $order->price_base = $price_input;

                        }

                        $order->save();

                        $roblox_order = Roblox_Order::create([
                            'order_id'=>$order->id,
                            'server'=>$uname,
                            'uname'=>$uname,
                            'money'=>$price_input,
                            'phone'=>$praise_price_packet,
                            'type_order'=>4,
                            'status'=>"chuanhan"
                        ]);
                    }
                    elseif ($service->idkey == 'pet_99_auto'){
                        //bỏ dấu @ đầu chuỗi ký tự.
                        $uname = $request->customer_data0;

                        if (substr($uname, 0, 1) === '@') {
                            $uname = substr_replace($uname, '', 0, 1);
                        }

                        $order->idkey = $service->idkey;

                        $praise_price_packet = 0;
                        $price_input = 0;
                        if (isset($praisePricePacket[$selectedCustomer])){
                            $praise_price_packet = $praisePricePacket[$selectedCustomer];
                            $price_input = 0;
                            if (strpos($praise_price_packet, 'B') !== false) {
                                // Loại bỏ ký tự "B" và chuyển đổi thành số
                                $valueInBillion = (float) str_replace('B', '', $praise_price_packet);
                                // Quy định B = 1000000000
                                $price_input = $valueInBillion * 1000000000;
                            }elseif (strpos($praise_price_packet, 'K') !== false){
                                // Loại bỏ ký tự "B" và chuyển đổi thành số
                                $valueInBillion = (float) str_replace('K', '', $praise_price_packet);
                                // Quy định B = 1000000000
                                $price_input = $valueInBillion * 1000;
                            }elseif (strpos($praise_price_packet, 'M') !== false){
                                // Loại bỏ ký tự "B" và chuyển đổi thành số
                                $valueInBillion = (float) str_replace('M', '', $praise_price_packet);
                                // Quy định B = 1000000000
                                $price_input = $valueInBillion * 1000000;
                            }
                            else{
                                $price_input = (int)$praise_price_packet;
                            }
                            $order->price_base = $price_input;

                        }

                        $order->save();

                        $roblox_order = Roblox_Order::create([
                            'order_id'=>$order->id,
                            'server'=>$uname,
                            'uname'=>$uname,
                            'money'=>$price_input,
                            'phone'=>$praise_price_packet,
                            'type_order'=>6,
                            'status'=>"chuanhan"
                        ]);
                    }
                    elseif ($service->idkey == 'item_pet_go_auto'){
                        //bỏ dấu @ đầu chuỗi ký tự.
                        $uname = $request->customer_data0;

                        if (substr($uname, 0, 1) === '@') {
                            $uname = substr_replace($uname, '', 0, 1);
                        }

                        $order->idkey = $service->idkey;

                        $praise_price_packet = 0;
                        $price_input = 0;
                        if (isset($praisePricePacket[$selectedCustomer])){
                            $praise_price_packet = $praisePricePacket[$selectedCustomer];
                            $price_input = 0;
                            if (strpos($praise_price_packet, 'B') !== false) {
                                // Loại bỏ ký tự "B" và chuyển đổi thành số
                                $valueInBillion = (float) str_replace('B', '', $praise_price_packet);
                                // Quy định B = 1000000000
                                $price_input = $valueInBillion * 1000000000;
                            }elseif (strpos($praise_price_packet, 'K') !== false){
                                // Loại bỏ ký tự "B" và chuyển đổi thành số
                                $valueInBillion = (float) str_replace('K', '', $praise_price_packet);
                                // Quy định B = 1000000000
                                $price_input = $valueInBillion * 1000;
                            }elseif (strpos($praise_price_packet, 'M') !== false){
                                // Loại bỏ ký tự "B" và chuyển đổi thành số
                                $valueInBillion = (float) str_replace('M', '', $praise_price_packet);
                                // Quy định B = 1000000000
                                $price_input = $valueInBillion * 1000000;
                            }
                            else{
                                $price_input = (int)$praise_price_packet;
                            }
                            $order->price_base = $price_input;

                        }

                        $order->save();

                        $roblox_order = Roblox_Order::create([
                            'order_id'=>$order->id,
                            'server'=>$uname,
                            'uname'=>$uname,
                            'money'=>$price_input,
                            'phone'=>$praise_price_packet,
                            'type_order'=>12,
                            'status'=>"chuanhan"
                        ]);
                    }
                    elseif ($service->idkey == 'anime_defenders_auto'){
                        //bỏ dấu @ đầu chuỗi ký tự.
                        $uname = $request->customer_data0;

                        if (substr($uname, 0, 1) === '@') {
                            $uname = substr_replace($uname, '', 0, 1);
                        }

                        $praise_price = 0;

                        if (isset($s_praise_price[$selectedCustomer])){
                            $praise_price =  $s_praise_price[$selectedCustomer];
                            $order->price_base = $praise_price??0;
                        }

                        $phone = isset($namePacket[$selectedCustomer]) ? $namePacket[$selectedCustomer] : "";

                        $type_item = null;

                        //lấy thông tin dịch vụ gốc
                        if (Helpers::DecodeJson("server_mode", $service->params) == 1 && Helpers::DecodeJson("server_price", $service->params) == 1) {
                            $s_type_item = Helpers::DecodeJson("type_item" . $server, $service->params);
                        } else {
                            $s_type_item = Helpers::DecodeJson("type_item",$service->params);
                        }

                        if (isset($s_type_item[$selectedCustomer])){
                            $type_item =  $s_type_item[$selectedCustomer];
                        }

                        if (!isset($type_item)) {
                            DB::rollback();
                            return response()->json([
                                'status' => 0,
                                'message' => __('Vui lòng chọn loại vật phẩm cho sản phẩm')
                            ]);
                        }

                        $order->idkey = $service->idkey;
                        $order->save();

                        $roblox_order = Roblox_Order::create([
                            'ver'=>$type_item,//Loại vật phẩm
                            'order_id'=>$order->id,
                            'server'=>$uname,
                            'uname'=>$uname,//Tên tài khoản
                            'money'=>$praise_price,//Số lượng vật phẩm
                            'phone'=>$phone,//Tên vật phẩm
                            'bot_handle'=>null,
                            'type_order'=>10,
                            'status'=>"chuanhan"
                        ]);
                    }
                    else{
                        DB::rollback();
                        return response()->json([
                            'status' => 0,
                            'message' => __('Loại dịch vụ không hợp lệ'),
                        ]);
                    }
                }

            }
            elseif ($filter_type == 5){

                $type_refund = $request->get('type_refund')??0;
                $keyword = $request->get('amount');
                $keyword = explode('|', $keyword);
                $server = $request->get('server');
                $s_service_idkey = [];
                //check giao dich tự động && custom service
                $input_auto = $service->gate_id;

                //check keyword dùng server và tính giá khác nhau
                if (Helpers::DecodeJson("server_mode", $service->params) == 1 && Helpers::DecodeJson("server_price", $service->params) == 1) {
                    $s_keyword = Helpers::DecodeJson("keyword" . $server, $service->params);
                    if (!empty(Helpers::DecodeJson("service_idkey" . $server, $service->params))){
                        $s_service_idkey = Helpers::DecodeJson("service_idkey" . $server, $service->params);
                    }
                }
                else {
                    $s_keyword = Helpers::DecodeJson("keyword", $service->params);
                    if (!empty(Helpers::DecodeJson("service_idkey", $service->params))){
                        $s_service_idkey = Helpers::DecodeJson("service_idkey", $service->params);
                    }
                }

                if (empty($s_keyword)){
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => __('Dịch vụ chưa cấu hình keyword'),
                    ]);
                }

                $selectedCustomer = [];

                foreach ($keyword as $key){
                    $index = array_search($key, $s_keyword);

                    if ($index === false){
                        DB::rollback();
                        return response()->json([
                            'status' => 0,
                            'message' => __('Không tìm thấy keyword'),
                        ]);
                    }
                    array_push($selectedCustomer,$index);
                }

                if (count($selectedCustomer) != count($keyword)){
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => __('Lỗi dịch vụ vui lòng kiểm tra lại'),
                    ]);
                }

                //check option dùng server và tính giá khác nhau
                if (Helpers::DecodeJson("server_mode", $service->params) == 1 && Helpers::DecodeJson("server_price", $service->params) == 1) {
                    $s_price = Helpers::DecodeJson("price" . $server, $service->params);
                } else {
                    $s_price = Helpers::DecodeJson("price", $service->params);
                }

                //lấy thông tin dịch vụ gốc
                if (Helpers::DecodeJson("server_mode", $service->params) == 1 && Helpers::DecodeJson("server_price", $service->params) == 1) {
                    $s_price_ctv = Helpers::DecodeJson("price" . $server, $service->params);
                } else {
                    $s_price_ctv = Helpers::DecodeJson("price", $service->params);
                }

                //Tính tiền cho khách
                $price = 0;

                if (!empty($selectedCustomer) && count($selectedCustomer) > 0) {

                    foreach ($selectedCustomer as $aSelect) {

                        //ốp tỉ giá theo cấu hình theo từng option chọn
                        $priceRaw=isset($s_price[$aSelect]) ? $s_price[$aSelect] : 0;
                        $price += floor($priceRaw);
                    }
                }

                //check giá trị tiền phù hợp với cấu hình dịch vụ

                if ($price <= 0) {

                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => __('Lựa chọn dịch vụ không hợp lệ.Vui lòng chọn lại')
                    ]);
                }

                //END tính tiền khách


                $priceCTV = 0;
                //nếu là dịch vụ thủ công
                if ($input_auto != 1 ) {
                    ///////Tính tiền cho CTV
                    if (!empty($selectedCustomer) && count($selectedCustomer) > 0) {
                        foreach ($selectedCustomer as $aSelect) {
                            $priceCTV += isset($s_price_ctv[$aSelect]) ? $s_price_ctv[$aSelect] : 0;
                        }
                    }

                    //check giá trị tiền phù hợp với cấu hình dịch vụ
                    if ($priceCTV <= 0) {

                        DB::rollback();
                        return response()->json([
                            'status' => 0,
                            'message' => __('Lựa chọn dịch vụ CTV không hợp lệ.Vui lòng chọn lại')
                        ]);
                    }
                    ///////END tính tiền CTV
                }

                $namePacket = Helpers::DecodeJson("name", $service->params);
                //Kiểm tra thông tin nhập lên
                $send_name = Helpers::DecodeJson("send_name", $service->params);
                $send_type = Helpers::DecodeJson("send_type", $service->params);
                $server_data = Helpers::DecodeJson("server_data", $service->params);

                if (Helpers::DecodeJson("server_mode", $service->params) == 1) {

                    if(!($server_data[$server]??null)){
                        return response()->json([
                            'status' => 0,
                            'message' => __('Vui lòng chọn máy chủ của dịch vụ thanh toán')
                        ]);
                    }

                    if ((strpos($server_data[$server], '[DELETE]') === true)) {

                        DB::rollback();
                        return response()->json([
                            'status' => 0,
                            'message' => __('Máy chủ bạn đã đóng hoặc không hợp lệ.Xin vui lòng chọn lại máy chủ')
                        ]);

                    }
                }

                $customer_info = [];

                if (!empty($send_name) && count($send_name) > 0) {
                    for ($i = 0; $i < count($send_name); $i++) {

                        if ($send_type[$i] == 4) { //nếu  nó là kiểu upload ảnh

                            $info= MediaHelpers::imageBase64($request->get('customer_data'.$i));

                            $customer_info['customer_data'.$i]=$info;

                        } else {

                            if ($request->get('customer_data' . $i) == "" || $request->get('customer_data' . $i) == null) {

                            }else{
                                if ($send_type[$i] == 8){
                                    if (preg_match('/[^\x00-\x7F]+/u', $request->get('customer_data' . $i)) ||  strpos($request->get('customer_data' . $i), ' ') !== false) {
                                        DB::rollback();
                                        return response()->json([
                                            'status' => 0,
                                            'message' => __('Quý khách vui lòng điền đúng định dạng tài khoản là dạng viết liền không dấu')
                                        ]);
                                    }
                                }

                                $customer_info['customer_data' . $i] = $request->get('customer_data' . $i);
                            }

                        }
                    }
                }

                if (Helpers::DecodeJson("server_mode", $service->params) == 1) {
                    $customer_info['server'] = $server_data[$server];
                }

                $provider = $service->idkey;

                if ($userTransaction->checkBalanceValid() == false) {

                    $message="[" . Carbon::now() . "] "."[" . $request->root() . "] Tạo đơn thất bại số dư tài khoản không đủ tiền thanh toán dịch vụ - " . $userTransaction->username . " đã tạo lệnh dịch vụ ".$service->title." - "."Tổng tiền: ".number_format($price)." (Số dư hiện tại: ".number_format($userTransaction->balance).")" ;
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_id_balance_daily'));

                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => __('Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý')
                    ]);

                }

                if ($userTransaction->balance < $price) {

                    $message="[" . Carbon::now() . "] "."[" . $request->root() . "] Tạo đơn thất bại số dư tài khoản không đủ tiền thanh toán dịch vụ - " . $userTransaction->username . " đã tạo lệnh dịch vụ ".$service->title." - "."Tổng tiền: ".number_format($price)." (Số dư hiện tại: ".number_format($userTransaction->balance).")" ;
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_id_balance_daily'));

                    DB::rollBack();
                    return response()->json([
                        'status' => 0,
                        'message' => __('Bạn không đủ tiền để thanh toán.Vui lòng nạp thêm tiền vào tài khoản')
                    ]);
                }

                $userTransaction->balance = $userTransaction->balance - $price;
                $userTransaction->balance_out = $userTransaction->balance_out + $price;
                $userTransaction->save();

                $price_service = $price;

                $customer_info['index_name'] = $selectedCustomer;
                $customer_info['keyword'] = $keyword;
                $service_idkey = [];
                if (count($s_service_idkey) > 0){
                    if (count($selectedCustomer) > 0){
                        foreach ($selectedCustomer as $c_selected){
                            array_push($service_idkey,$s_service_idkey[$c_selected]);
                        }
                        $customer_info['service_idkey'] = $service_idkey;
                    }
                }

                //thêm mới order service
                $order = Order::create([
                    'request_id' => $userTransaction->id.time() . rand(10000, 99999),
                    'ref_id' => $service->id,
                    'idkey' => $provider??'',
                    'title' => $service->title,
                    'description' => '',
                    'params' => $customer_info,
                    'price' => $price,
                    'price_ctv' => $priceCTV,
                    'author_id' => $userTransaction->id,
                    'position' => $server,
                    'shop_id' => $request->shop_id,
                    'status' => 1, // 'Đang chờ xử lý'
                    'module' => config('module.service-purchase.key'),
                    'gate_id' => $input_auto == 1 ?? 0,
                    'sticky'=>1,//lưu thông tin nếu mua bằng api
                    'position1'=>$request->rut,//lưu thông tin đơn rút hay mua
                    'url'=>$request->callback,
                    'app_client'=>$request->getHost(),
                    'process_at'=>Carbon::now(),
                    'request_id_customer'=>$request->request_id,
                    'request_id_provider'=>$request->request_id,
                    'type_refund' => $type_refund == 1 ?? 0,
                ]);

                $tele_order_id = $order->id;

                $order->txns()->create([
                    'trade_type' => 'service_purchase', //Thanh toán dịch vụ
                    'user_id' => $userTransaction->id,
                    'is_add' => '0',//Trừ tiền
                    'amount' => $price,
                    'real_received_amount' => $price,
                    'last_balance' => $userTransaction->balance,
                    'description' =>  "Thanh toán dịch vụ #" . $order->id .' ('.$service->title.')',
                    'ip' => $request->getClientIp(),
                    'order_id' => $order->id,
                    'status' => 1

                ]);

                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'author_id' => $userTransaction->id,
                    'status' => 1,

                ]);

                //set tên công việc
                if (!empty($selectedCustomer) && count($selectedCustomer) > 0) {
                    foreach ($selectedCustomer as $selei => $aSelect) {
                        if (isset($namePacket[$aSelect])) {

                            //ốp tỉ giá theo cấu hình theo từng option chọn
                            $priceRaw= isset($s_price[$aSelect])?$s_price[$aSelect]:0;
                            $price = $priceRaw;

                            // tính tiền cho price_CTV
                            $priceCTV = isset($s_price_ctv[$aSelect])?$s_price_ctv[$aSelect]:0;

                            if ($selei == 0){
                                $serviceword = (isset($namePacket[$aSelect]) ? $namePacket[$aSelect] : "");
                            }else{
                                $serviceword = $serviceword.','.(isset($namePacket[$aSelect]) ? $namePacket[$aSelect] : "");
                            }
                            //set tên công việc
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workname.key'),
                                'title' => (isset($namePacket[$aSelect]) ? $namePacket[$aSelect] : ""),
                                'unit_price' => $price,
                                'unit_price_ctv' => !empty($provider) ? $priceCTV : 0,
                            ]);

                        }

                    }

                    $order->description = $serviceword;
                    $order->save();
                }

            }
            elseif ($filter_type == 6){
                $type_refund = $request->get('type_refund')??0;
                $selectedCustomer = explode('|',$request->get('amount'));

                $rankto = $selectedCustomer[0]??'';
                $rankfrom = $selectedCustomer[1]??'';

                $server = $request->get('server');

                //check giao dich tự động && custom service
                $input_auto = $service->gate_id;

                //check option dùng server và tính giá khác nhau
                if (Helpers::DecodeJson("server_mode", $service->params) == 1 && Helpers::DecodeJson("server_price", $service->params) == 1) {
                    $s_price = Helpers::DecodeJson("price" . $server, $service->params);
                } else {
                    $s_price = Helpers::DecodeJson("price", $service->params);
                }

                //lấy thông tin dịch vụ gốc
                if (Helpers::DecodeJson("server_mode", $service->params) == 1 && Helpers::DecodeJson("server_price", $service->params) == 1) {
                    $s_price_ctv = Helpers::DecodeJson("price" . $server, $service->params);
                } else {
                    $s_price_ctv = Helpers::DecodeJson("price", $service->params);
                }

                //Tính tiền cho khách
                $price = 0;
                $price = $s_price[$rankto] - $s_price[$rankfrom];

                //check giá trị tiền phù hợp với cấu hình dịch vụ
                if ($price <= 0) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 0,
                        'message' => __('Lựa chọn dịch vụ không hợp lệ')
                    ]);
                }
                //END tính tiền khách

                $priceCTV = 0;
                //nếu là dịch vụ thủ công
                if ($input_auto != 1 ) {
                    ///////Tính tiền cho CTV
                    $priceCTV = $s_price_ctv[$rankto] - $s_price_ctv[$rankfrom];

                    //check giá trị tiền phù hợp với cấu hình dịch vụ
                    if ($priceCTV <= 0) {

                        DB::rollback();
                        return response()->json([
                            'status' => 0,
                            'message' => __('Lựa chọn dịch vụ CTV không hợp lệ.Vui lòng chọn lại')
                        ]);
                    }
                    ///////END tính tiền CTV
                }

                $namePacket = Helpers::DecodeJson("name", $service->params);

                //Kiểm tra thông tin nhập lên
                $send_name = Helpers::DecodeJson("send_name", $service->params);
                $send_type = Helpers::DecodeJson("send_type", $service->params);
                $server_data = Helpers::DecodeJson("server_data", $service->params);

                if (Helpers::DecodeJson("server_mode", $service->params) == 1) {
                    if ((strpos($server_data[$server], '[DELETE]') === true)) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 0,
                            'message' => __('Máy chủ bạn đã đóng hoặc không hợp lệ.Xin vui lòng chọn lại máy chủ')
                        ]);
                    }
                }

                $customer_info = [];

                if (!empty($send_name) && count($send_name) > 0) {
                    for ($i = 0; $i < count($send_name); $i++) {

                        if ($send_type[$i] == 4) { //nếu  nó là kiểu upload ảnh

                            $info= MediaHelpers::imageBase64($request->get('customer_data'.$i));

                            $customer_info['customer_data'.$i]=$info;

                        } else {

                            if ($request->get('customer_data' . $i) == "" || $request->get('customer_data' . $i) == null) {

//                                DB::rollback();
//                                return response()->json([
//                                    'status' => 0,
//                                    'message' => __('Vui lòng điền đầy đủ thông tin yêu cầu để thanh toán')
//                                ]);

                            }else{
                                if ($send_type[$i] == 8){
                                    if (preg_match('/[^\x00-\x7F]+/u', $request->get('customer_data' . $i)) ||  strpos($request->get('customer_data' . $i), ' ') !== false) {
                                        DB::rollback();
                                        return response()->json([
                                            'status' => 0,
                                            'message' => 'Quý khách vui lòng điền đúng định dạng tài khoản là dạng viết liền không dấu'
                                        ]);
                                    }
                                }

                                $customer_info['customer_data' . $i] = $request->get('customer_data' . $i);
                            }

                        }
                    }
                }

                if (Helpers::DecodeJson("server_mode", $service->params) == 1) {
                    $customer_info['server'] = $server_data[$server];
                }

                //check giao dich tự động && custom service
                $input_auto = $service->gate_id;

                $provider = $service->idkey;

                //trừ tiền user
                if ($userTransaction->checkBalanceValid() == false) {

                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => __('Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý')
                    ]);

                }

                if ($userTransaction->balance < $price) {

                    $message="[" . Carbon::now() . "] "."[" . $request->root() . "] Tạo đơn thất bại số dư tài khoản không đủ tiền thanh toán dịch vụ - " . $userTransaction->username . " đã tạo lệnh dịch vụ ".$service->title." - "."Tổng tiền: ".number_format($price)." (Số dư hiện tại: ".number_format($userTransaction->balance).")" ;
                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_id_balance_daily'));

                    return response()->json([
                        'status' => 0,
                        'message' => __('Bạn không đủ tiền để thanh toán.Vui lòng nạp thêm tiền vào tài khoản')
                    ]);
                }

                //trừ tiền user
                $userTransaction->balance = $userTransaction->balance - $price;
                $userTransaction->balance_out = $userTransaction->balance_out + $price;
                $userTransaction->save();
                $price_service = $price;

                //thêm mới order service
                $order = Order::create([
                    'request_id' => $userTransaction->id.time() . rand(10000, 99999),
                    'ref_id' => $service->id,
                    'idkey' => $provider??'',
                    'title' => $service->title,
                    'params' => $customer_info,
                    'price' => $price,
                    'price_ctv' => $priceCTV,
                    'author_id' => $userTransaction->id,
                    'position' => $server,
                    'status' => 1, // 'Đang chờ xử lý'
                    'module' => config('module.service-purchase.key'),
                    'gate_id' => $input_auto == 1 ?? 0,
                    'sticky'=>1,//lưu thông tin nếu mua bằng api
                    'position1'=>$request->rut,//lưu thông tin đơn rút hay mua
                    'url'=>$request->callback,
                    'app_client'=>$request->getHost(),
                    'process_at'=>Carbon::now(),
                    'request_id_customer'=>$request->request_id,
                    'request_id_provider'=>$request->request_id,
                    'type_refund' => $type_refund == 1 ?? 0,
                ]);

                $tele_order_id = $order->id;

                $order->txns()->create([
                    'trade_type' => 'service_purchase', //Thanh toán dịch vụ
                    'user_id' => $userTransaction->id,
                    'is_add' => '0',//Trừ tiền
                    'amount' => $price,
                    'real_received_amount' => $price,
                    'last_balance' => $userTransaction->balance,
                    'description' =>  "Thanh toán dịch vụ #" . $order->id .' ('.$service->title.')',
                    'ip' => $request->getClientIp(),
                    'order_id' => $order->id,
                    'status' => 1

                ]);
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'author_id' => $userTransaction->id,
                    'status' => 1,

                ]);

                //set tên công việc
                if (!empty($rankfrom) && !empty($rankto)) {
                    if (isset($namePacket[$rankfrom]) && isset($namePacket[$rankto])) {
                        $serviceword = ((isset($namePacket[$rankfrom]) && isset($namePacket[$rankto])) ? ($namePacket[$rankfrom] . "->" . $namePacket[$rankto]) : "");
                        //set tên công việc
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workname.key'),
                            'title' => ((isset($namePacket[$rankfrom]) && isset($namePacket[$rankto])) ? ($namePacket[$rankfrom] . "->" . $namePacket[$rankto]) : ""),
                            'unit_price' => $price,
                            'unit_price_ctv' => !empty($provider) ? $priceCTV : 0,
                        ]);

                    }
                }

            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            throw $e;
            return response()->json([
                'status'=>0,
                'message' => __('Có lỗi phát sinh.Xin vui lòng thử lại !'),
            ]);
        }
        // Commit the queries!
        DB::commit();

        if ($input_auto == 0 && (int)$price_service >= 1000000){
            $message = '';
            $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
            $message .= "\n";
            $message .= "Tài khoản thành viên <b>".$khothanhvien."</b> giao dịch thành công dịch vụ thủ công:";
            $message .= "\n";
            $message .= '- Dịch vụ: <b>'.$tele_service.'</b>';
            $message .= "\n";
            $message .= '- Tên công việc: <b>'.$serviceword.'</b>';
            $message .= "\n";
            $message .= '- Số tiền: <b>'.number_format($price_service).'</b>';
            $message .= "\n";
            $message .= " - Link xử lý đơn: ".config('app.url')."/admin/service-purchase/".$tele_order_id;
            $message .= "\n";
            $message .= "Thông báo từ: ".config('app.url');
            $message .= "\n";

            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_price_service'));
        }

        if($userTransaction->balance<1000000){
            $message="[" . Carbon::now() . "] "."[" . $request->root() . "] " . $userTransaction->username . " đã tạo lệnh dịch vụ ".$service->title." - "."Tổng tiền: ".number_format($price)." (Số dư hiện tại: ".number_format($userTransaction->balance).")" ;
            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_id_balance_daily'));
        }

        return response()->json([
            'status'=>2,
            'message' => __('Thực hiện thanh toán thành công.Chờ xử hệ thống xử lý'),
        ]);
    }

    public function editInfo(Request $request){


        //user_id
        //sign
        //service_id=1802,1795: ninja xu
        //tranid=id tran giao dịch của shop


        DB::beginTransaction();
        try {


            $userTransaction = User::where('id',$request->user_id)->lockForUpdate()->firstOrFail();

            if($userTransaction->partner_key_service!=$request->sign){
                DB::rollback();
                return response()->json([
                    'status'=>0,
                    'message' => __('Key mã hóa dữ liệu ko đúng'),
                ]);
            }


            $data = Order::where('module', '=', config('module.service-purchase.key'))
                ->with('item_ref')
                ->where(function ($query) {
                    $query->orWhere('status', "1");
                    $query->orWhere('status', "2");
                })
                ->where('author_id', '=', $userTransaction->id)
                ->where('request_id_customer', '=', $request->request_id)
                ->lockForUpdate()->first();

            if(!$data){
                DB::rollback();
                return response()->json([
                    'status'=>0,
                    'message' => __('Không tìm thấy request id giao dịch để chỉnh sửa'),
                ]);
            }


            //check dịch vụ auto sms thì không cho chỉnh sửa auto
            $input_auto = $data->gate_id;

            if($input_auto==1){
                DB::rollback();
                return response()->json([
                    'status'=>0,
                    'message' => __('Các dịch vụ tự động SMS không thể chỉnh sửa. Vui lòng liên hệ admin để xử lý !'),
                ]);
            }

            if( $data->sticky>=3){
                DB::rollback();
                return response()->json([
                    'status'=>0,
                    'message' => __('Bạn đã sửa quá giới hạn 3 lần. Vui lòng hủy yêu cầu để thực hiện lại giao dịch'),
                ]);

            }

            if ($data->expired_lock != null && $data->expired_lock > Carbon::now()) {
                DB::rollback();
                return response()->json([
                    'status'=>0,
                    'message' => __('Dịch vụ đã được thực hiện. Vui lòng thử lại trong vòng 5 phút'),
                ]);
            }

            //Kiểm tra thông tin nhập lên
            $send_name = Helpers::DecodeJson("send_name", $data->item_ref->params);
            $send_type = Helpers::DecodeJson("send_type", $data->item_ref->params);

            $customer_info = [];

            if (!empty($send_name) && count($send_name) > 0) {
                for ($i = 0; $i < count($send_name); $i++) {

                    if ($send_type[$i] == 4 && $request->hasFile('customer_data' . $i)) { //nếu  nó là kiểu upload ảnh

                        $info= MediaHelpers::imageBase64($request->get('customer_data'.$i));
                        $customer_info['customer_data' . $i] = $info;

                    } else {

                        if ($request->get('customer_data' . $i) == "" || $request->get('customer_data' . $i) == null) {

//                            DB::rollback();
//                            return response()->json([
//                                'status' => 0,
//                                'message' => __('Vui lòng điền đầy đủ thông tin yêu cầu để thanh toán'),
//                            ]);

                        }else{
                            if ($send_type[$i] == 8){
                                if (preg_match('/[^\x00-\x7F]+/u', $request->get('customer_data' . $i)) ||  strpos($request->get('customer_data' . $i), ' ') !== false) {
                                    DB::rollback();
                                    return response()->json([
                                        'status' => 0,
                                        'message' => __('Quý khách vui lòng điền đúng định dạng tài khoản là dạng viết liền không dấu'),
                                    ]);
                                }
                            }

                            $customer_info['customer_data' . $i] = $request->get('customer_data' . $i);
                        }


                    }
                }
            }

            //update info cho purchase
            $data->params = json_encode($customer_info, JSON_UNESCAPED_UNICODE);
            $data->sticky= $data->sticky+1;
            $data->save();


        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json([
                'status'=>0,
                'message' => __('Có lỗi phát sinh.Xin vui lòng thử lại !'),
            ]);

        }
        // Commit the queries!
        DB::commit();
        return response()->json([
            'status'=>1,
            'message' => __('Chỉnh sửa thông tin thành công'),
        ]);
    }

    public function showBotCheckAccountInformation(Request $request){
        try {

            $sign = "456trtyt88888%@ttt";

            if($request->sign != $sign){
                return response()->json([
                    'status'=>0,
                    'message' => __('Không được truy cập thông tin bot check thông tin tài khoản khách hàng!'),
                ]);
            }

            $aBot = Roblox_Bot::query()
                ->select('id','cookies','status','account_type')
                ->where('status',6)
                ->where('type_order',1)
                ->where('account_type',1)
                ->orderBy('ver','asc')
                ->first();

            if (!isset($aBot)){
                return response()->json([
                    'status'=>0,
                    'message' => __('Không tìm thấy bot !'),
                ]);
            }

            return response()->json([
                'status'=>1,
                'data'=>$aBot,
                'message' => __('Lấy thông tin bot thành công !'),
            ]);


        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json([
                'status'=>0,
                'message' => __('Có lỗi phát sinh.Xin vui lòng thử lại !'),
            ]);

        }
    }

    public function destroyOrder(Request $request){

        // Start transaction!
        DB::beginTransaction();
        try {

            $userTransaction = User::where('id',$request->user_id)->lockForUpdate()->firstOrFail();

            if($userTransaction->partner_key_service!=$request->sign){
                DB::rollback();
                return response()->json([
                    'status'=>0,
                    'message' => __('Key mã hóa dữ liệu ko đúng'),
                ]);
            }

            $data = Order::query()
                ->where('module', config('module.service-purchase'))
                ->where('status', "1")
                ->where('author_id', '=', $userTransaction->id)
                ->where('request_id_customer', '=', $request->request_id)
                ->lockForUpdate()->first();

            if (!isset($data)) {
                return response()->json([
                    'status' => 0,
                    'message' => __('Không tìm thấy yêu cầu dịch vụ!')
                ]);
            }

            $input_auto = $data->gate_id;

            if ($input_auto == 1) {
                return response()->json([
                    'status' => 0,
                    'message' => __('Các dịch vụ tự động SMS không thể hủy bỏ. Vui lòng liên hệ admin để xử lý !')
                ]);
            }


            if ($data->expired_lock != null && $data->expired_lock > Carbon::now()) {

                return response()->json([
                    'status' => 0,
                    'message' => 'Dịch vụ đã được thực hiện. Vui lòng thử lại trong vòng 5 phút'
                ]);
            }

            $data->update([
                'status' => 0,
            ]);//trạng thái hủy


            //set tiến độ hủy
            OrderDetail::create([
                'order_id' => $data->id,
                'module' => config('module.service-workflow.key'),
                'status' => 0,
                'content' => __('Khách hàng hủy yêu cầu dịch vụ')
            ]);

            if ($userTransaction->checkBalanceValid() == false) {
                DB::rollback();
                return response()->json([
                    'status' => 0,
                    'message' => __('Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý')
                ]);

            }

            $userTransaction->balance = $userTransaction->balance + $data->price;
            $userTransaction->balance_in = $userTransaction->balance_in + $data->price;
            $userTransaction->save();

            //tạo tnxs
            $txns = Txns::create([
                'trade_type' => 'refund',//Hoàn tiền
                'is_add' => '1',//Công tiền
                'user_id' => $userTransaction->id,
                'amount' => $data->price,
                'real_received_amount' => $data->price,
                'last_balance' => $userTransaction->balance,
                'description' => __('Hoàn tiền hủy yêu cầu dich vụ #') . $data->id,
                'order_id' => $data->id,
                'ip' => $request->getClientIp(),
                'status' => 1
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error( $e);
            return response()->json([
                'message' => __('Có lỗi phát sinh.Xin vui lòng thử lại !'),
                'status' => 0
            ], 500);
        }
        // Commit the queries!
        DB::commit();
        return response()->json([
            'status' =>1,
            'message' => __(__("Đã hủy thành công yêu cầu dịch vụ #") . $data->id),
        ]);
    }

    public function postRefundOrder(Request $request){
        DB::beginTransaction();
        try {

            $userTransaction = User::where('id',$request->user_id)->lockForUpdate()->firstOrFail();

            if($userTransaction->partner_key_service!=$request->sign){
                DB::rollback();
                return response()->json([
                    'status'=>0,
                    'message' => __('Key mã hóa dữ liệu ko đúng'),
                ]);
            }

            $content = $request->get('content');

            $data = Order::with('item_ref')
                ->with('order_refund')
                ->where('module', config('module.service-purchase.key'))
                ->where('status', 10)
                ->where('author_id', '=', $userTransaction->id)
                ->where('request_id_customer', '=', $request->request_id)
                ->lockForUpdate()->first();

            if(!isset($data)){
                DB::rollback();
                return response()->json([
                    'status' =>0,
                    'message' => __('Không tìm thấy yêu cầu dịch vụ'),
                ]);
            }

            //check dịch vụ auto sms thì không cho chỉnh sửa auto
            $input_auto = $data->gate_id;

            if ($input_auto == 1) {
                DB::rollback();
                return response()->json([
                    'message' => __('yêu cầu dịch vụ tự động vui lòng kiểm tra lại'),
                    'status' => 0,
                ], 200);

            }

            if (isset($data->order_refund)) {
                DB::rollback();
                return response()->json([
                    'status' =>0,
                    'message' => __("Bạn đã gửi yêu cầu hoàn tiền."),
                ]);
            }

            //Người tạo sticky

            if ($userTransaction->checkBalanceValid() == false) {

                DB::rollback();
                return response()->json([
                    'status' => 0,
                    'message' => __('Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý')
                ]);

            }

            //update trạng thái cho đơn hàng

            $data->status = 11;
            $data->save();
            //tạo ticksy

            $images = json_decode($request->get('images'),JSON_UNESCAPED_UNICODE);

            $array_images = [];
            if (count($images)){
                foreach ($images as $image){

                    $info = MediaHelpers::imageBase64($image);

                    array_push($array_images,$info);
                }
            }

            $params['image_customer'] = $array_images;

            OrderDetail::create([
                'order_id' => $data->id,
                'description' => $content,
                'module' => 'service-refund',
                'content' => json_encode($params,JSON_UNESCAPED_UNICODE),
                'status' => 2,//chờ xử lý
            ]);

            //set tiến độ hoan tien
            OrderDetail::create([
                'order_id' => $data->id,
                'module' => config('module.service-workflow.key'),
                'status' => 11,
                'content' => $content,
            ]);

//            $provider = $data->idkey;
//
//            if (!empty($provider) && $input_auto == 0){
//
//            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error( $e);
            return response()->json([
                'message' => __('Có lỗi phát sinh.Xin vui lòng thử lại !'),
                'status' => 0
            ], 500);

        }
        // Commit the queries!
        DB::commit();
        return response()->json([
            'status' =>1,
            'message' => __('Yêu cầu hoàn tiền thành công'),
        ]);
    }

    public function deleteRefundOrder(Request $request){

        DB::beginTransaction();
        try {

            $userTransaction = User::where('id',$request->user_id)->lockForUpdate()->firstOrFail();

            if($userTransaction->partner_key_service!=$request->sign){
                DB::rollback();
                return response()->json([
                    'status'=>0,
                    'message' => __('Key mã hóa dữ liệu ko đúng'),
                ]);
            }

            $data = Order::with('itemconfig_ref')
                ->with('order_refund')
                ->where('module', config('module.service-purchase.key'))
                ->where('status', 11)
                ->where('author_id', '=', $userTransaction->id)
                ->where('request_id_customer', '=', $request->request_id)
                ->lockForUpdate()->first();

            if(!isset($data)){
                return response()->json([
                    'status' =>0,
                    'message' => __('Không tìm thấy yêu cầu dịch vụ'),
                ]);
            }

            //check dịch vụ auto sms thì không cho chỉnh sửa auto
            $input_auto = $data->gate_id;

            if ($input_auto == 1) {

                return response()->json([
                    'message' => __('Yêu cầu dịch vụ tự động vui lòng kiểm tra lại'),
                    'status' => 0,
                ], 200);

            }

            $order_refund = OrderDetail::query()
                ->where('module','service-refund')
                ->where('order_id',$data->id)
                ->where('status',2)->first();

            if (!$order_refund){
                return response()->json([
                    'status' =>0,
                    'message' => __("Không tim thấy yêu cầu hoàn tiền"),
                ]);
            }

            //Cập nhật trạng thái yêu cầu hoàn tiền.
            $order_refund->status = 0;
            $order_refund->save();

            //Cập nhật trạng thái đơn hàng về chờ đối soát

            $data->status = 10;
//            $data->process_at = Carbon::now();//Thời gian xác nhận đơn hàng
            $data->save();

            //set tiến độ tiếp nhận
            OrderDetail::create([
                'order_id'=>$data->id,
                'module' => config('module.service-workflow.key'),
                'content' => __('Khách hàng hủy yêu cầu hoàn tiền'),
                'status' => 10,
            ]);

            $provider = $data->idkey;

            if (!empty($provider) && $input_auto == 0){

            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error( $e);
            return response()->json([
                'message' => __('Có lỗi phát sinh.Xin vui lòng thử lại !'),
                'status' => 0
            ], 500);

        }
        // Commit the queries!
        DB::commit();
        return response()->json([
            'status' =>1,
            'message' => __('Yêu cầu hoàn tiền thành công'),

        ]);
    }

    public function checkOrder(Request $request){

        $data = Order::query()
            ->where('gate_id',0)
            ->where('module', config('module.service-purchase.key'))
            ->whereIn('status',[3,4,5,0])
            ->where('request_id_customer', '=', $request->request_id)->first();

        if(!isset($data)){
            return response()->json([
                'status' =>0,
                'message' => __('Không tìm thấy yêu cầu dịch vụ'),
            ]);
        }

        if(empty($data->url)){
            return response()->json([
                'status' =>0,
                'message' => __('Không tìm thấy link url'),
            ]);
        }

        $message = config('module.service-purchase.status.'.$data->status);

        if($data->url!=""){
            $this->callbackToShop($data,$message,null,null);

        }

        //lưu log gọi curl
//        $path = storage_path() ."/logs/services-auto-recallback/";
//        $filename=$path."listen_callback_recallback_".Carbon::now()->format('Y-m-d').".txt";
//        if(!\File::exists($path)){
//            \File::makeDirectory($path, $mode = "0755", true, true);
//        }
//
//        $contentText =  $txt = Carbon::now() . ": Recalback lai don hang" . $data->id;
//        \File::append($filename,$contentText."\n");

        return response()->json([
            'status' =>1,
            'message' => __('Recallback thành công'),
        ]);
    }

    public function checkAutoOrder(Request $request){

        $data = Order::query()
            ->where('gate_id',1)
            ->where('module',config('module.service-purchase.key'))
            ->whereIn('status',[4,5,3,0])
            ->where('request_id_customer', '=', $request->request_id)->first();

        if(!isset($data)){
            return response()->json([
                'status' =>0,
                'message' => __('Không tìm thấy yêu cầu dịch vụ'),
            ]);
        }

        if(empty($data->url)){
            return response()->json([
                'status' =>0,
                'message' => __('Không tìm thấy link url'),
            ]);
        }

        if($data->url!=""){
            $messageBot = config('module.service-purchase-auto.status.'.$data->status);
            if ($data->idkey == 'roblox_gem_pet' || $data->idkey == 'huge_99_auto' || $data->idkey == 'gem_unist_auto'
                || $data->idkey == 'unist_auto' || $data->idkey == 'item_pet_go_auto'
                || $data->idkey == 'huge_psx_auto' || $data->idkey == 'pet_99_auto'){
                $roblox_order = Roblox_Order::query()->with('order')->where('order_id',$data->id)->first();

                $message = '';
                if ($data->status == 4){
                    $message = 'Thành công';
                }elseif ($data->status == 5){
                    $message = 'Không thành công - Can not found player';
                }elseif ($data->status == 3){
                    $message = 'Đơn hàng bị từ chối';
                }

                $this->dispatch(new CallbackOrderRobloxBuyGemPet($data,$roblox_order->status,$message));

            }else{
                $this->callbackAutoToShop($data,$messageBot);
            }

        }

        //lưu log gọi curl
//        $path = storage_path() ."/logs/services-auto-recallback/";
//        $filename=$path."listen_callback_recallback_".Carbon::now()->format('Y-m-d').".txt";
//        if(!\File::exists($path)){
//            \File::makeDirectory($path, $mode = "0755", true, true);
//        }
//
//        $contentText =  $txt = Carbon::now() . ": Recalback lai don hang" . $data->id;
//        \File::append($filename,$contentText."\n");

        return response()->json([
            'status' =>1,
            'message' => __('Recallback thành công'),
        ]);
    }

    public function getSMSDaily(Request $request){
        try {

            $sign = "456trtyt88888%@ttt";

            if($request->sign != $sign){
                return response()->json([
                    'status'=>0,
                    'message' => __('Không được truy cập thông tin SMS!'),
                ]);
            }

            $data = (array)config('module.service.idkey');

            if (!isset($data)){
                return response()->json([
                    'status'=>0,
                    'message' => __('Không tìm thấy SMS !'),
                ]);
            }

            return response()->json([
                'status'=>1,
                'data'=>$data,
                'message' => __('Lấy thông tin SMS thành công !'),
            ]);


        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json([
                'status'=>0,
                'message' => __('Có lỗi phát sinh.Xin vui lòng thử lại !'),
            ]);

        }
    }

    public function getService(Request $request,$slug){
        try {

            $sign = "456trtyt88888%@ttt";

            if($request->sign != $sign){
                return response()->json([
                    'status'=>0,
                    'message' => __('Không được truy cập thông tin SMS!'),
                ]);
            }

            $data = Item::query()
                ->where('module','service')
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(JSON_UNQUOTE(params), '$.filter_type')) = 4")
                ->where('idkey',$slug)
                ->first();

            if (!isset($data)){
                return response()->json([
                    'status'=>0,
                    'message' => __('Không tìm thấy SMS !'),
                ]);
            }

            return response()->json([
                'status'=>1,
                'data'=>$data,
                'message' => __('Lấy thông tin dịch vụ thành công !'),
            ]);


        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json([
                'status'=>0,
                'message' => __('Có lỗi phát sinh.Xin vui lòng thử lại !'),
            ]);

        }
    }

    public function getPriceService(Request $request){
        try {

            $sign = "456trtyt88888%@ttt";

            if($request->sign != $sign){
                return response()->json([
                    'status'=>0,
                    'message' => __('Không được truy cập thông tin SMS!'),
                ]);
            }

            if (!$request->filled('idkey')){
                return response()->json([
                    'status'=>0,
                    'message' => __('Vui lòng gửi mã idkey!'),
                ]);
            }

            $idkey = $request->get('idkey');

            if (!$request->filled('order_id')){
                return response()->json([
                    'status'=>0,
                    'message' => __('Vui lòng gửi mã order_id!'),
                ]);
            }

            $order_id = $request->get('order_id');

            if (!$request->filled('url')){
                return response()->json([
                    'status'=>0,
                    'message' => __('Vui lòng gửi mã url!'),
                ]);
            }

            $url = $request->get('url');

            if (!$request->filled('keyword')){
                return response()->json([
                    'status'=>0,
                    'message' => __('Vui lòng gửi mã keyword!'),
                ]);
            }

            $s_keyword = $request->get('keyword');

            $data = Item::query()
                ->where('module','service')
                ->where('idkey',$idkey)
                ->first();

            if (!isset($data)){
                return response()->json([
                    'status'=>0,
                    'message' => __('Không tìm thấy data !'),
                ]);
            }

            if (empty($data->params)){
                return response()->json([
                    'status'=>0,
                    'message' => __('Không tìm thấy params !'),
                ]);
            }

            $params = json_decode($data->params);

            if (empty($params->filter_type)){
                return response()->json([
                    'status'=>0,
                    'message' => __('Không tìm thấy filter_type !'),
                ]);
            }

            $filter_type = $params->filter_type;

            if ($filter_type == 4){
                if (empty($params->keyword)){
                    return response()->json([
                        'status'=>0,
                        'message' => __('Không tìm thấy SMS !'),
                    ]);
                }

                $keywords = $params->keyword;

                //Tìm vị trí index.

                $selectedCustomer = array_search($s_keyword, $keywords);

                if ($selectedCustomer === false){
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => __('Không tìm thấy keyword'),
                    ]);
                }

                if (empty($params->price)){
                    return response()->json([
                        'status'=>0,
                        'message' => __('Không tìm thấy SMS !'),
                    ]);
                }

                $prices = $params->price;

                $price = $prices[$selectedCustomer];

                $this->callbackPriceToShop($url,$order_id,$price);

            }
            elseif ($filter_type == 7){

                $amount=(int)$s_keyword;

                if (empty($params->input_pack_rate)){
                    return response()->json([
                        'status'=>0,
                        'message' => __('Không tìm thấy input_pack_rate !'),
                    ]);
                }

                $input_pack_rate= $params->input_pack_rate;

                if (empty($params->discount)){
                    return response()->json([
                        'status'=>0,
                        'message' => __('Không tìm thấy discount !'),
                    ]);
                }

                $discounts = $params->discount;
                //lấy hệ số đầu tiên của server
                $discount_final= $discounts[0];

                if($discount_final <=0 || str_contains($discount_final,',')){
                    return response()->json([
                        'status'=>0,
                        'message' => __('Hệ số cấu hình cho dịch vụ này không đúng.Vui lòng kiểm tra lại'),
                    ]);
                }

                /// số tiền= số ngọc/ hệ số của user  đại lý *1000
                $price = $amount / $discount_final*1000/$input_pack_rate;

                //end tính số tiền cần trừ
                $price= ceil($price);

                if($price <=0 ){
                    return response()->json([
                        'status'=>0,
                        'message' => __('Số tiền thanh toán không hợp lệ'),
                    ]);
                }

                //check số tiền nhỏ  và lớn nhất

                if (empty($params->input_pack_min)){
                    return response()->json([
                        'status'=>0,
                        'message' => __('Không tìm thấy SMS !'),
                    ]);
                }

                $input_pack_min = $params->input_pack_min;
                if (empty($params->input_pack_max)){
                    return response()->json([
                        'status'=>0,
                        'message' => __('Không tìm thấy SMS !'),
                    ]);
                }
                $input_pack_max = $params->input_pack_max;

                if($price < (int)$input_pack_min || $price > (int)$input_pack_max){
                    return response()->json([
                        'status'=>0,
                        'message' => __('Vui lòng thanh toán dịch vụ trong khoảng tiền từ ').number_format($input_pack_min).__(' đến ').number_format($input_pack_max),
                    ]);
                }

                $this->callbackPriceToShop($url,$order_id,$price);
            }
            else{
                return response()->json([
                    'status'=>0,
                    'message' => __('Loại dịch vụ không hợp lệ !'),
                ]);
            }


        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json([
                'status'=>0,
                'message' => __('Có lỗi phát sinh.Xin vui lòng thử lại !'),
            ]);

        }
    }

    public function callbackPriceToShop($url,$order_id,$price)
    {

        $data = array();

        $data['order_id'] = $order_id;

        $data['price'] = $price;

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
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);

                $resultRaw=curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                //debug thì mở cái này
//                $myfile = fopen(storage_path() . "/logs/curl_callback_price-service-to-shop-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
//                $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
//                fwrite($myfile, $txt);
//                fclose($myfile);

                if($httpcode==200){

                    if(strpos($resultRaw, __("Có lỗi phát sinh.Xin vui lòng thử lại")) > -1){
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

    public function callbackToShop(Order $order,$message,$refund = null,$mistake_by = null)
    {

        $url = $order->url;

        $data = array();

        $data['status'] = $order->status;

        $data['refund'] = $refund;

        $data['message'] = $message;

        $data['mistake_by'] = $mistake_by;

        if ($order->status == 4 || $order->status == 10){
            $data['process_at'] = strtotime($order->process_at);
        }

        if ($order->status == 4){
            $data['price'] = $order->real_received_price_ctv;
        }

        $data['input_auto'] = 0;

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
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);

                $resultRaw=curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                //debug thì mở cái này
                $myfile = fopen(storage_path() . "/logs/curl_callback-service-to-shop-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
                $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
                fwrite($myfile, $txt);
                fclose($myfile);

                if($httpcode==200){

                    if(strpos($resultRaw, __("Có lỗi phát sinh.Xin vui lòng thử lại")) > -1){
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

    public function callbackAutoToShop(Order $order,$messageBot)
    {

        $url = $order->url;

        $data = array();

        $data['status'] = $order->status;

        $data['message'] = $messageBot;

        $data['input_auto'] = 1;

        if ($order->status ==4){
            $data['process_at'] = strtotime($order->process_at);
        }

        //debug thì mở cái này
        $myfile = fopen(storage_path() . "/logs/check_order-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
        $txt = Carbon::now() . " :" . $order;
        fwrite($myfile, $txt);
        fclose($myfile);

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
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);

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
