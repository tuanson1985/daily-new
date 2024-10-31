<?php

namespace App\Http\Controllers\Api\V1\Service;

use App\Http\Controllers\Controller;



use App\Jobs\ServiceAuto\RobloxJob;
use App\Library\ChargeGameGateway\GarenaGate_Phap;
use App\Library\ChargeGameGateway\RobloxGate;
use App\Library\HelperItemDaily;
use App\Library\Helpers;


use App\Library\MediaHelpers;
use App\Library\RatioCommon\ServiceRatio;
use App\Models\Bot;

use App\Models\Conversation;
use App\Models\Inbox;
use App\Models\ItemConfig;
use App\Models\KhachHang;
use App\Models\LangLaCoin_KhachHang;
use App\Models\LangLaCoin_User;
use App\Models\NinjaXu_KhachHang;
use App\Models\NinjaXu_User;
use App\Models\Nrogem_AccBan;
use App\Models\Nrogem_GiaoDich;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Roblox_Order;
use App\Models\Shop;
use App\Models\Txns;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Validator;


class ServiceController extends Controller
{

    public function __construct()
    {

        //$this->middleware('auth:api');
    }


    public function index(Request $request)
    {

        $datatable = ItemConfig::with(array('items' => function ($query) {
            $query->with(array('groups' => function ($q) {
                $q->select('groups.id', 'title', 'slug');
            }));
        }))->where('module', config('module.service.key'))
            ->where('status', '=', 1)
            ->where('shop_id', $request->shop_id);

        $datatable->orderByRaw('ISNULL(`order`), `order` ASC')->orderBy('id','desc');


        if ($request->filled('order'))  {
            $datatable->orderBy('order', $request->get('order') );
        }
        else{
            $datatable->orderBy('id', 'desc');
        }

        if ($request->filled('id_option'))  {
            $datatable->whereIn('id', $request->get('id_option'));
        }

        if ($request->filled('id_not_option'))  {
            $datatable->whereNotIn('id', $request->get('id_not_option'));
        }

        if ($request->filled('search'))  {
            $datatable->where('title', 'LIKE', '%' . $request->get('search') . '%');
        }


        $datatable = $datatable->paginate($request->limit ?? 20);

        return response()->json([
            'status' => 1,
            'data' => $datatable,
            'message' => 'Lấy dữ liệu thành công'
        ]);
//        return response()->json($datatable);
    }

    public function show(Request $request, $slug)
    {

        $data = ItemConfig::with(array('items' => function ($query) {

            $query->with(array('groups' => function ($q) {
                $q->select('groups.id', 'title', 'slug');
            }));

        }))->where('module', config('module.service.key'))
            ->where('status', '=', 1)
            ->where('slug', '=', $slug)
            ->where('shop_id', $request->shop_id)
            ->firstOrFail();

        //lấy ratio của shop
        $ratioOfShop=ServiceRatio::get($request->shop_id);
        if($ratioOfShop==false){

            return response()->json([
                'status' => 0,
                'message' => 'Shop chưa được cấu hình tỉ giá'
            ]);
        }

        // lấy giá price dịch vụ rồi chỉnh sửa theo ratio shop
        $dataParamsEdit =  json_decode($data->params);

        //nếu dạng điền thì thì tính công thức theo HỆ SỐ
        if(($dataParamsEdit->filter_type??"")== 7){

            //nếu dịch vụ dùng nhiều server và tính giá khác nhau
            if(($dataParamsEdit->server_mode??"")==1 &&   ($dataParamsEdit->server_price??"")==1  ){

                $server_data=$dataParamsEdit->server_data??[];
                foreach ($server_data  as $p=> $item) {

                    if($server_data[$p]!=null){
                        $discountArrayServer=$dataParamsEdit->{'discount'.$p}??[];
                        foreach ($discountArrayServer as $pIn =>$discountEdit) {
                            $dataParamsEdit->{'discount'.$p}[$pIn]= bcdiv(floatval($discountEdit/$ratioOfShop->ratio_percent*100),1,1);
                        }

                    }
                }
            }
            //tính giá giống nhau
            else{

                foreach($dataParamsEdit->discount??[] as $index=>$discountEdit){

                    $dataParamsEdit->discount[$index]=bcdiv(floatval($discountEdit/$ratioOfShop->ratio_percent*100),1,1);
                }
            }
        }
        //nếu dạng chọn 1 , dạng chọn nhiều,dạng khoảng thì thì tính công thức theo TIỀN
        else{

            //nếu dịch vụ dùng nhiều server và tính giá khác nhau
            if(($dataParamsEdit->server_mode??"")==1 &&   ($dataParamsEdit->server_price??"")==1  ){

                $server_data=$dataParamsEdit->server_data??[];
                foreach ($server_data  as $p=> $item) {

                    if($server_data[$p]!=null){
                        $discountArrayServer=$dataParamsEdit->{'price'.$p}??[];
                        foreach ($discountArrayServer as $pIn =>$priceEdit) {
                            $dataParamsEdit->{'price'.$p}[$pIn]=floor($priceEdit*$ratioOfShop->ratio_percent/100)+$ratioOfShop->additional_amount;
                        }

                    }
                }


            }
            //tính giá giống nhau
            else{

                foreach($dataParamsEdit->price??[] as $index=>$priceEdit){

                    $dataParamsEdit->price[$index]=floor($priceEdit*$ratioOfShop->ratio_percent/100)+$ratioOfShop->additional_amount;
                }
            }
        }

        //set ngược price kèm ratio trở lại data
        $data->params=json_encode($dataParamsEdit);

        $bot=null;

        if($data->idkey=='nrocoin'){

            $bot=Bot::orderBy('server')->orderBy('ver')
                ->get(['id','server','ver','acc','active','uname','coin','zone','updated_at']);
        }
        elseif($data->idkey=='langla_coin'){
            $bot=LangLaCoin_User::orderBy('server')->orderBy('ver')
                ->get(['id','server','ver','acc','active','uname','coin','zone','updated_at']);

        }
        elseif($data->idkey=='ninjaxu'){
            $bot=NinjaXu_User::orderBy('server')->orderBy('ver')
                ->get(['id','server','ver','acc','active','uname','coin','zone','updated_at']);
        }
        if($bot!=null){
            $bot->map(function($row) {

                if ((time() - strtotime($row->updated_at)) > 30) {
                    $row->active= "off";
                } else {
                    $row->active= "on";
                }
                return $row;
            });
        }


        return response()->json([
            'status' => 1,
            'data' => $data,
            'data_bot' => $bot,
            'message' => 'Lấy dữ liệu thành công'
        ]);

    }

    public function  listBot(Request $request, $slug){


        $data = ItemConfig::with(array('items' => function ($query) {

            $query->with(array('groups' => function ($q) {
                $q->select('groups.id', 'title', 'slug');
            }));

        }))->where('module', config('module.service.key'))
            ->where('status', '=', 1)
            ->where('slug', '=', $slug)
            ->where('shop_id', $request->shop_id)
            ->first();

        if(!$data){
            return response()->json([
                'status' => 0,
                'data_bot' => null,
                'message' => 'Không tìm thấy dịch vụ '
            ]);
        }

        $bot=null;

        if($data->idkey=='nrocoin_internal'){

            $bot=Bot::orderBy('server')->orderBy('ver')
                ->where('shop_id',$request->shop_id)
                ->get(['id','server','ver','acc','active','uname','coin','zone','updated_at']);
        }
        elseif($data->idkey=='langla_coin_internal'){
            $bot=LangLaCoin_User::orderBy('server')->orderBy('ver')
                ->where('shop_id',$request->shop_id)
                ->get(['id','server','ver','acc','active','uname','coin','zone','updated_at']);

        }
        elseif($data->idkey=='ninjaxu_internal'){

            $bot=NinjaXu_User::orderBy('server')->orderBy('ver')
                ->where('shop_id',$request->shop_id)
                ->get(['id','server','ver','acc','active','uname','coin','zone','updated_at']);
        }
        elseif($data->idkey=='nrocoin' ||$data->idkey=='langla_coin' ||$data->idkey=='ninjaxu' ){

           $result=HelperItemDaily::getListBot($data->idkey);
           return response()->json([
               'status' => 1,
               'idkey' => $data->idkey,
               'data_bot' => $result,
               'message' => 'Lấy dữ liệu thành công',
           ]);

        }

        if($bot!=null){
            $bot->map(function($row) {

                if ((time() - strtotime($row->updated_at)) > 30) {
                    $row->active= "off";
                } else {
                    $row->active= "on";
                }
                return $row;
            });
        }


        return response()->json([
            'status' => 1,
            'idkey' => $data->idkey,
            'data_bot' => $bot,
            'message' => 'Lấy dữ liệu thành công'
        ]);

    }

    public function postPurchase(Request $request)
    {


        DB::beginTransaction();
        try {

            $service = ItemConfig::with('items')->where('id', $request->id)
                ->where('status', '=', 1)
                ->where('module', '=', config('module.service.key'))
                ->where('shop_id', $request->shop_id)
                ->first();


            if(!$service){
                return response()->json([
                    'status' => 0,
                    'message' => 'Không tìm thấy dịch vụ'
                ]);
            }


            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }

            $service->total_order= $service->total_order+1;
            $service->save();

            //lấy ratio của shop
            $ratioOfShop=ServiceRatio::get($request->shop_id);
            if($ratioOfShop==false){

                return response()->json([
                    'status' => 0,
                    'message' => 'Shop chưa được cấu hình tỉ giá'
                ]);
            }
            //END ratio của shop

            if (!$service) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Không tìm thấy dịch vụ phù hợp'
                ]);
            }

            if ($service->idkey == "nrogem") {

                $check5Min = Order::where('author_id', auth()->user()->id)
                    ->where('module', config('module.service-purchase.key'))
                    ->where('ref_id', $service->id)
                    ->where('shop_id', $request->shop_id)
                    ->orderBy('created_at', 'desc')->first();

                if ($check5Min) {

                    if (strtotime($check5Min->created_at) < strtotime("-5 minutes")) {

                    } else {
                        DB::rollback();
                        return response()->json([
                            'status' => 0,
                            'message' => 'Vui lòng chờ khoảng 5 phút để tạo thêm order mua ngọc'
                        ]);
                    }
                }


                $checkOrderFinish = Order::where('author_id', auth()->user()->id)
                    ->where('module', config('module.service-purchase.key'))
                    ->where('ref_id', $service->id)
                    ->where('status', 1)
                    ->where('shop_id', $request->shop_id)
                    ->orderBy('created_at', 'desc')->first();

                if ($checkOrderFinish) {
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => 'Hiện tại hệ thống đang xử lý yêu cầu trước của bạn. Vui lòng thử lại sau'
                    ]);
                }
            }
            $filter_type = Helpers::DecodeJson('filter_type', $service->params);
            //////////////////////////Điền tiền///////////////////////////////////

            if ($filter_type == 7) {

                $price = (int)$request->selected;
                if ($price <= 0) {
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => 'Số tiền thanh toán không hợp lệ'
                    ]);
                }

                $server = intval($request->get('server'));

                //check số tiền nhỏ  và lớn nhất
                $input_pack_min = Helpers::DecodeJson("input_pack_min", $service->params);
                $input_pack_max = Helpers::DecodeJson('input_pack_max', $service->params);


                if ($price < (int)$input_pack_min || $price > (int)$input_pack_max) {
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => 'Vui lòng thanh toán dịch vụ trong khoảng tiền từ ' . number_format($input_pack_min) . " đến " . number_format($input_pack_max)
                    ]);
                }
                //end check


                if (Helpers::DecodeJson("server_mode", $service->params) == 1 && Helpers::DecodeJson("server_price", $service->params) == 1) {
                    $s_price = Helpers::DecodeJson("price" . $server, $service->params);
                    $s_discount = Helpers::DecodeJson("discount" . $server, $service->params);

                } else {
                    $s_price = Helpers::DecodeJson("price", $service->params);
                    $s_discount = Helpers::DecodeJson("discount", $service->params);
                }

                //tính giá của dịch vụ
                $total = 0;
                $index = 0;
                $current = 0;

                if (!empty($s_price) && !empty($s_discount)) {

                    for ($i = 0; $i < count($s_price); $i++) {

                        if ($price >= $s_price[$i] && $s_price[$i] != null) {

                            $current = $s_price[$i];
                            $index = $i;
                            //Tính số ngọc theo hệ số đã chia tỉ giá
                            $discountFinal= floatval($s_discount[$i]/$ratioOfShop->ratio_percent*100);
                            $discountFinal=  floor($discountFinal *10)/10;

                            $total = $price * $discountFinal;

                        }
                    }
                }


                //tính rate quy đổi
                $input_pack_rate = Helpers::DecodeJson("input_pack_rate", $service->params);

                $total = $total / 1000 * $input_pack_rate;
                $total = (int)$total;
                $total_old = $total;
                $random_gem = 0;
                if ($service->idkey == "nrogem") {
                    $random_gem = rand(1, 3);
                    $total = $total + $random_gem;
                }

                //Kiểm tra thông tin nhập lên
                $send_name = Helpers::DecodeJson("send_name", $service->params);
                $send_type = Helpers::DecodeJson("send_type", $service->params);
                $server_data = Helpers::DecodeJson("server_data", $service->params);


                $server_name_current=$server_data[$server]??"";


                if (Helpers::DecodeJson("server_mode", $service->params) == 1) {

                    if($server_name_current=="" || $server_name_current ==null){
                        return response()->json([
                            'status' => 0,
                            'message' => 'Vui lòng chọn máy chủ của dịch vụ thanh toán'
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

                        }
                        else {

                            if ($request->get('customer_data' . $i) == "" || $request->get('customer_data' . $i) == null) {

                                DB::rollback();
                                return response()->json([
                                    'status' => 0,
                                    'message' => 'Vui lòng điền đầy đủ thông tin yêu cầu để thanh toán'
                                ]);

                            }

                            if ($send_type[$i] == 8){
                                if (preg_match('/[^\x00-\x7F]+/u', $request->get('customer_data' . $i)) ||  strpos($request->get('customer_data' . $i), ' ') !== false) {
                                    DB::rollback();
                                    return response()->json([
                                        'status' => 0,
                                        'message' => 'Quý khách vui lòng điền đúng định dạng tài khoản là dạng viết liền không dấu'
                                    ]);
                                }
                            }

                            $customer_info['customer_data' . $i] = htmlentities($request->get('customer_data' . $i));
                        }
                    }
                }
                if (Helpers::DecodeJson("server_mode", $service->params) == 1) {

                }

                $customer_info['server'] =$server_name_current;

                //trừ tiền user
                $userTransaction = User::where('id', Auth::guard('api')->user()->id)->lockForUpdate()->firstOrFail();

                if ($userTransaction->checkBalanceValid() == false) {
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => 'Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý'
                    ]);

                }

                if ($userTransaction->balance < $price) {
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => 'Bạn không đủ tiền để thanh toán.Vui lòng nạp thêm tiền vào tài khoản'
                    ]);
                }

                $userTransaction->balance = $userTransaction->balance - $price;
                $userTransaction->balance_out = $userTransaction->balance_out + $price;
                $userTransaction->save();

                //check giao dich tự động && custom service
                $input_auto = $service->gate_id;

                if ($input_auto == 1){
                    //thêm mới log lịch sử service
                    $order = Order::create([
                        'request_id' => $userTransaction->id.time() . rand(10000, 99999),
                        'ref_id' => $service->id,
                        'title' => $service->title,
                        'params' => $customer_info,
                        'price_base' => $total,
                        'price' => $price,
                        'additional_amount' => $ratioOfShop->additional_amount,
                        'ratio_exchange_rate' => $ratioOfShop->ratio_percent,
                        'author_id' => $userTransaction->id,
                        'status' => 1,
                        'position' => $server,
                        'module' => config('module.service-purchase.key'),
                        'shop_id' => $request->shop_id,
                        'gate_id' => $input_auto == 1 ?? 0,
                    ]);

                    $order->txns()->create([
                        'trade_type' => 'service_purchase', //Thanh toán dịch vụ
                        'user_id' => $userTransaction->id,
                        'is_add' => '0',//Trừ tiền
                        'amount' => $price,
                        'real_received_amount' => $price,
                        'last_balance' => $userTransaction->balance,
                        'description' => "Thanh toán dịch vụ #" . $order->id .' ('.$service->title.')',
                        'ip' => $request->getClientIp(),
                        'shop_id' => $request->shop_id,
                        'ref_id' => $order->id,
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
                    ]);

                    if ($input_auto == 1 && (
                            $service->idkey == 'nrocoin'||
                            $service->idkey == 'nrogem' ||
                            $service->idkey == 'langlacoin' ||
                            $service->idkey == 'ninjaxu' ||
                            $service->idkey == 'roblox_buyserver'||
                            $service->idkey == 'roblox_buygamepass'||
                            $service->idkey == 'roblox_gem_pet'

                        )) {


                        if($service->idkey == 'nrocoin'){



                            $order->idkey=$service->idkey;
                            $order->save();

                            $khachhang = KhachHang::create([
                                'server' => $server + 1,
                                'order_id' => $order->id,
                                'uname' => $request->customer_data0,
                                'money' => $total,
                                'status' => "chuanhan",
                            ]);
                        }
                        elseif($service->idkey == 'nrogem'){

                            $order->idkey=$service->idkey;
                            $order->save();
                            $nrogem_GiaoDich = Nrogem_GiaoDich::create([
                                'order_id' => $order->id,
                                'acc' => $request->customer_data0,
                                'pass' => $request->customer_data1,
                                'server' =>  ($server + 1),
                                'gem' => $total,
                                'gem_base' => $total_old,
                                'gem_rand' => $random_gem,
                                'status' => "chualogin",
                                'shop_id' => $request->shop_id,

                            ]);
                        }
                        elseif ( $service->idkey == 'langlacoin') {

                            $order->idkey=$service->idkey;
                            $order->save();


                            $langla_khachhang = LangLaCoin_KhachHang::create([
                                'server' => $server + 1,
                                'order_id' => $order->id,
                                'uname' => $request->customer_data0,
                                'coin' => $total,
                                'status' => "chuanhan",
                                'shop_id' => $request->shop_id,
                            ]);
                        }
                        elseif ($service->idkey == 'ninjaxu') {

                            $order->idkey=$service->idkey;
                            $order->save();

                            $ninjaxu_khachhang = NinjaXu_KhachHang::create([
                                'server' => $server + 1,
                                'order_id' => $order->id,
                                'uname' => $request->customer_data0,
                                'coin' => $total,
                                'status' => "chuanhan",
                                'shop_id' => $request->shop_id,
                            ]);

                        }
                        elseif ( $service->idkey == 'roblox_buyserver') {


                            $order->idkey=$service->idkey;
                            $order->save();

                            //check xem có đúng link mua server ko
                            $server_id=RobloxGate::detectLink($request->customer_data0);

                            if($server_id!=""){

                                $order->idkey=$service->idkey;
                                $order->save();
                                $roblox_order = Roblox_Order::create([
                                    'order_id'=>$order->id,
                                    'server'=>$server_id,
                                    'uname'=>$request->customer_data0,
                                    'money'=>$total,
                                    'phone'=>"",
                                    'type_order'=>3,
                                    'status'=>"chuanhan",
                                    'shop_id' => $request->shop_id,
                                ]);


                            }
                            else{
                                DB::rollBack();
                                return response()->json([
                                    'status' => 0,
                                    'message' => 'Link server roblox không hợp lệ.Vui lòng thử lại'
                                ]);

                            }
                        }
                        elseif ($service->idkey == 'roblox_buygamepass') {
                            $order->idkey=$service->idkey;
                            $order->save();

                            //check xem có đúng link mua server ko
                            $result=RobloxGate::detectUsernameRoblox($request->customer_data0);
                            if($result &&  $result->status==1){
                                $order->idkey=$service->idkey;
                                $order->save();
                                $roblox_order = Roblox_Order::create([
                                    'order_id'=>$order->id,
                                    'server'=>$result->user_id,
                                    'uname'=>$request->customer_data0,
                                    'money'=>$total,
                                    'phone'=>"",
                                    'type_order'=>3,
                                    'status'=>"chuanhan",
                                    'shop_id' => $request->shop_id,
                                ]);
                            }
                            else{
                                DB::rollBack();
                                return response()->json([
                                    'status' => 0,
                                    'message' => 'Tài khoản roblox của bạn không đúng.Vui lòng kiểm tra lại'
                                ]);

                            }
                        }
                        elseif ($service->idkey == 'roblox_gem_pet'){
                            $order->idkey=$service->idkey;
                            $order->save();
                            $roblox_order = Roblox_Order::create([
                                'order_id'=>$order->id,
                                'server'=>$result->user_id??'',
                                'uname'=>$request->customer_data0,
                                'money'=>$total,
                                'phone'=>"",
                                'type_order'=>4,
                                'status'=>"chuanhan",
                                'shop_id' => $request->shop_id,
                            ]);
                        }
                        DB::commit();
                        //Gọi api bắn qua daily

                        $result = HelperItemDaily::fire($shop->daily_partner_id,$shop->daily_partner_key_service,$order->id,$server+1, $request->customer_data0, $request->customer_data1,$total,$service->idkey,null,$order->request_id);

                        if($result && isset($result->status)){
                            if($result->status == 2){

                                //set tiến độ
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'module' => config('module.service-workflow.key'),
                                    'status' => 1,
                                    'content' => "Đại lý đã tiếp nhận (Code:2)",
                                ]);

                                return response()->json([
                                    'status' => 1,
                                    'message' => 'Thực hiện thanh toán thành công'
                                ]);
                            }
                            if($result->status == 0){

                                $order->status=7;
                                $order->save();

                                //set tiến độ
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'module' => config('module.service-workflow.key'),
                                    'status' => 1,
                                    'content' => $result->message??"",
                                ]);


                                return response()->json([
                                    'status' => 1,
                                    'message' => 'Thực hiện thanh toán thành công'
                                ]);
                            }
                        }
                        else{

                            $order->status=9;
                            $order->save();
                            return response()->json([
                                'status' => 1,
                                'message' => 'Thực hiện thanh toán thành công'
                            ]);
                        }

                    }


                    if ($input_auto == 1 && (
                            $service->idkey == 'nrocoin_internal'||
                            $service->idkey == 'nrogem_internal' ||
                            $service->idkey == 'langlacoin_internal' ||
                            $service->idkey == 'ninjaxu_internal' ||
                            $service->idkey == 'roblox_internal' ||
                            $service->idkey == 'roblox_buyserver_internal' ||
                            $service->idkey == 'ruby_internal'
                        )) {

                        if ($service->idkey == 'nrocoin_internal') {
                            $order->idkey=$service->idkey;
                            $order->save();

                            $khachhang = KhachHang::create([
                                'server' => $server + 1,
                                'order_id' => $order->id,
                                'uname' => $request->customer_data0,
                                'money' => $total,
                                'status' => "chuanhan",
                                'shop_id' => $request->shop_id,
                            ]);
                        }
                        elseif($service->idkey == 'nrogem_internal'){
                            //lẩy random bot xử lý
                            $dataBot= Nrogem_AccBan::where('server', ($server + 1))
                                ->where(function($q){
                                    $q->orWhere('ver','!=','');
                                    $q->orWhereNotNull('ver');
                                })
                                ->where('status','on')
                                ->inRandomOrder()
                                ->first();

                            if(!$dataBot){
                                return response()->json([
                                    'status'=>0,
                                    'message'=>'Không có bot bán Ngọc hoạt động.Vui lòng thử lại'
                                ]);
                            }
                            //lưu thông tin bot ver để hiển thị cho dễ
                            $order->idkey=$service->idkey;
                            $order->acc_id=$dataBot->ver;
                            $order->save();

                            $nrogem_GiaoDich = Nrogem_GiaoDich::create([
                                'order_id' => $order->id,
                                'acc' => $request->customer_data0,
                                'pass' => $request->customer_data1,
                                'server' =>  ($server + 1),
                                'gem' => $total,
                                'gem_base' => $total_old,
                                'gem_rand' => $random_gem,
                                'status' => "chualogin",
                                'ver'=>$dataBot->ver,
                                'shop_id' => $request->shop_id,
                            ]);
                        }
                        elseif ($service->idkey == 'roblox_internal'){
                            $order->idkey=$service->idkey;
                            $order->save();
                            $roblox_order = Roblox_Order::create([
                                'order_id'=>$order->id,
                                'server'=>$result->user_id??'',
                                'uname'=>$request->customer_data0,
                                'money'=>$total,
                                'phone'=>"",
                                'type_order'=>4,
                                'status'=>"chuanhan",
                                'shop_id' => $request->shop_id,
                            ]);

                            DB::commit();
                            return response()->json([
                                'status' => 1,
                                'message' => 'Thực hiện thanh toán thành công'
                            ]);
                        }
                        elseif( $service->idkey == 'langlacoin_internal') {

                            $order->idkey=$service->idkey;
                            $order->save();


                            $langla_khachhang = LangLaCoin_KhachHang::create([
                                'server' => $server + 1,
                                'order_id' => $order->id,
                                'uname' => $request->customer_data0,
                                'coin' => $total,
                                'status' => "chuanhan",
                                'shop_id' => $request->shop_id,
                            ]);

                        }
                        elseif ($service->idkey == 'ninjaxu_internal') {

                            $order->idkey=$service->idkey;
                            $order->save();

                            $ninjaxu_khachhang = NinjaXu_KhachHang::create([
                                'server' => $server + 1,
                                'order_id' => $order->id,
                                'uname' => $request->customer_data0,
                                'coin' => $total,
                                'status' => "chuanhan",
                                'shop_id' => $request->shop_id,
                            ]);
                        }
                        elseif ( $service->idkey == 'roblox_buyserver_internal')
                        {

                            $order->idkey=$service->idkey;
                            $order->save();

                            //check xem có đúng link mua server ko
                            $server_id=RobloxGate::detectLink($request->customer_data0);

                            if($server_id!=""){

                                $order->idkey=$service->idkey;
                                $order->save();

                                $roblox_order = Roblox_Order::create([
                                    'order_id'=>$order->id,
                                    'server'=>$server_id,
                                    'uname'=>$request->customer_data0,
                                    'money'=>$total,
                                    'phone'=>"",
                                    'type_order'=>3,
                                    'status'=>"chuanhan",
                                    'shop_id' => $request->shop_id,
                                ]);
                                DB::commit();
                                $this->dispatch(new RobloxJob($order->id));
                                return response()->json([
                                    'status' => 1,
                                    'message' => 'Thực hiện thanh toán thành công'
                                ]);
                            }
                            else{
                                DB::rollBack();
                                return response()->json([
                                    'status' => 0,
                                    'message' => 'Link server roblox không hợp lệ.Vui lòng thử lại'
                                ]);

                            }
                        }
                        elseif ($service->idkey == 'roblox_buygamepass_internal') {


                            $order->idkey=$service->idkey;
                            $order->save();

                            //check xem có đúng link mua server ko
                            $result=RobloxGate::detectUsernameRoblox($request->customer_data0);
                            if($result &&  $result->status==1){
                                $order->idkey=$service->idkey;
                                $order->save();
                                $roblox_order = Roblox_Order::create([
                                    'order_id'=>$order->id,
                                    'server'=>$result->user_id,
                                    'uname'=>$request->customer_data0,
                                    'money'=>$total,
                                    'phone'=>"",
                                    'type_order'=>3,
                                    'status'=>"chuanhan",
                                    'shop_id' => $request->shop_id,
                                ]);

                                DB::commit();
                                $this->dispatch(new RobloxJob($order->id));
                                return response()->json([
                                    'status' => 1,
                                    'message' => 'Thực hiện thanh toán thành công'
                                ]);

                            }
                            else{
                                DB::rollBack();
                                return response()->json([
                                    'status' => 0,
                                    'message' => 'Link server roblox không hợp lệ.Vui lòng thử lại'
                                ]);

                            }
                        }
                        elseif ($service->idkey == 'ruby_internal') {

                            $order->idkey=$service->idkey;
                            $order->save();


                        }
                    }
                }
                else{
                    //Số tính tiền cho cộng tác viên bằng với số tiền
                    $priceCTV = $price;

                    //thêm mới log lịch sử service
                    $order = Order::create([
                        'request_id' => $userTransaction->id.time() . rand(10000, 99999),
                        'ref_id' => $service->id,
                        'title' => $service->title,
                        'params' => $customer_info,
                        'price_base' => $total,
                        'price' => $price,
                        'price_ctv' => $priceCTV,
                        'additional_amount' => $ratioOfShop->additional_amount,
                        'ratio_exchange_rate' => $ratioOfShop->ratio_percent,
                        'author_id' => $userTransaction->id,
                        'status' => 1,
                        'position' => $server,
                        'module' => config('module.service-purchase.key'),
                        'shop_id' => $request->shop_id,
                        'gate_id' => $input_auto == 1 ?? 0,
                    ]);

                    $order->txns()->create([
                        'trade_type' => 'service_purchase', //Thanh toán dịch vụ
                        'user_id' => $userTransaction->id,
                        'is_add' => '0',//Trừ tiền
                        'amount' => $price,
                        'real_received_amount' => $price,
                        'last_balance' => $userTransaction->balance,
                        'description' => "Thanh toán dịch vụ #" . $order->id .' ('.$service->title.')',
                        'ip' => $request->getClientIp(),
                        'shop_id' => $request->shop_id,
                        'ref_id' => $order->id,
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
                        'unit_price_ctv' => $priceCTV,
                    ]);
                }

            }
            //////////////////////////Loại Chọn một option///////////////////////////////////
            elseif ($filter_type == 4) {

                $selectedCustomer = $request->selected;
                $server = $request->get('server');

                $provider = $service->idkey;
                //check giao dich tự động && custom service
                $input_auto = $service->gate_id;

                //check option dùng server và tính giá khác nhau
                if (Helpers::DecodeJson("server_mode", $service->params) == 1 && Helpers::DecodeJson("server_price", $service->params) == 1) {
                    $s_price = Helpers::DecodeJson("price" . $server, $service->params);//giá
                    $s_praise_price = Helpers::DecodeJson("praise_price" . $server, $service->params);
                } else {
                    $s_price = Helpers::DecodeJson("price", $service->params);
                    $s_praise_price = Helpers::DecodeJson("praise_price", $service->params);
                }

                //lấy thông tin dịch vụ gốc
                if (Helpers::DecodeJson("server_mode", $service->items->params) == 1 && Helpers::DecodeJson("server_price", $service->items->params) == 1) {
                    $s_price_ctv = Helpers::DecodeJson("price" . $server, $service->items->params);
                    $s_praise_price_ctv = Helpers::DecodeJson("praise_price" . $server, $service->items->params);
                } else {
                    $s_price_ctv = Helpers::DecodeJson("price",$service->items->params);
                    $s_praise_price_ctv = Helpers::DecodeJson("praise_price", $service->items->params);
                }

                //Kiểm tra cách tính hòa hồng cho CTV commission_type = 1 tính theo giá config.
                if (Helpers::DecodeJson("commission_type", $service->params) == 1){
                    $s_price_ctv = $s_price;
                    $s_praise_price_ctv = $s_praise_price;
                }

                ////*******Tính tiền cho KHÁCH *****/////////

                //check giá trị tiền phù hợp với cấu hình dịch vụ
                if (isset($s_price[$selectedCustomer]) && $s_price[$selectedCustomer] > 0) {
                    $price = $s_price[$selectedCustomer];
                    $praise_price = $s_praise_price[$selectedCustomer];
                } else {

                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => 'Lựa chọn dịch vụ không hợp lệ'
                    ]);
                }


                //ốp tỉ giá theo cấu hình
                $price=floor($price*$ratioOfShop->ratio_percent/100)+$ratioOfShop->additional_amount;

                //check số tiền âm
                if ($price <= 0) {

                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => 'Số tiền thanh toán không hợp lệ'
                    ]);
                }
                ////*******END Tính tiền cho KHÁCH*****/////////


                ////*******Tính tiền cho CTV*****/////////
                if ($input_auto != 1 ) {

                    $priceCTV = 0;

                    //check giá trị tiền phù hợp với cấu hình dịch vụ gốc và tính tiền cho ctv

                    if (isset($s_price_ctv[$selectedCustomer]) && $s_price_ctv[$selectedCustomer] > 0) {
                        $priceCTV = $s_price_ctv[$selectedCustomer];
                        $praise_price_ctv = $s_praise_price_ctv[$selectedCustomer];

                    } else {
                        DB::rollback();
                        return response()->json([
                            'status' => 0,
                            'message' => 'Lựa chọn dịch vụ không hợp lệ (Lỗi cấu hình số tiền CTV)'
                        ]);
                    }

                    if ($priceCTV <= 0) {

                        DB::rollback();
                        return response()->json([
                            'status' => 0,
                            'message' => 'Lựa chọn dịch vụ CTV không hợp lệ.Vui lòng chọn lại'
                        ]);
                    }

                    ////*******END tính tiền CTV *****/////////

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
                            'message' => 'Vui lòng chọn máy chủ của dịch vụ thanh toán'
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

                                DB::rollback();
                                return response()->json([
                                    'status' => 0,
                                    'message' => 'Vui lòng điền đầy đủ thông tin yêu cầu để thanh toán'
                                ]);

                            }

                            if ($send_type[$i] == 8){
                                if (preg_match('/[^\x00-\x7F]+/u', $request->get('customer_data' . $i)) ||  strpos($request->get('customer_data' . $i), ' ') !== false) {
                                    DB::rollback();
                                    return response()->json([
                                        'status' => 0,
                                        'message' => 'Quý khách vui lòng điền đúng định dạng tài khoản là dạng viết liền không dấu'
                                    ]);
                                }
                            }

                            $customer_info['customer_data' . $i] = htmlentities($request->get('customer_data' . $i));
                        }
                    }
                }
                if (Helpers::DecodeJson("server_mode", $service->params) == 1) {
                    $customer_info['server'] = $server_data[$server];
                }

                //trừ tiền user
                $userTransaction = User::where('id', Auth::guard('api')->user()->id)->lockForUpdate()->firstOrFail();

                if ($userTransaction->checkBalanceValid() == false) {
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => 'Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý'
                    ]);

                }

                if ($userTransaction->balance < $price) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 0,
                        'message' => 'Bạn không đủ tiền để thanh toán.Vui lòng nạp thêm tiền vào tài khoản'
                    ]);


                }


                // Dịch vụ tự động nạp garena bên a Pháp
                if ($input_auto == 1 && $provider != "") {

                    if ($provider == "freefire" ) {
                        $id = $request->get('customer_data' . '0');
                        $username = "";
                        $password = "";
                    } elseif ($provider == "pubgm") {
                        $id = $request->get('customer_data' . '0');
                        $username = "";
                        $password = "";
                    } else {
                        $id = "";
                        $username = $request->get('customer_data' . '0');
                        $password = $request->get('customer_data' . '1');

                    }

                    $packPrice=explode("|",$praise_price);
                    $praise_price=$packPrice[0]??null;
                    $net=$packPrice[1]??"";

                    if(strtoupper($net)=="GARENA"){
                        $net="GARENA";
                        $payment_type = 0;
                    }
                    else{
                        $net="SMS";
                        $payment_type = 1;
                    }

                    //lấy giá của gói rút,bắn
                    $itemAmountFire = $praise_price;

                    if(empty($itemAmountFire) ){
                        DB::rollBack();

                        if(!Cache::has('WRONG_PACKAGE'.$shop->domain)){

                            Cache::put('WRONG_PACKAGE'.$shop->domain,true,now()->addMinutes(5));
                            $message="[" . Carbon::now() . "] "."[" . $request->root() . "] " . $shop->domain . " Hệ thống cấu hình gói rút không đúng. Vui lòng cấu hình lại dịch vụ(".$service->title.")" ;
                            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_notify_balance_tichhop_net'));
                        }
                        return response()->json([
                            'status' => 0,
                            'message' => 'Hệ thống cấu hình gói rút không đúng. Vui lòng liên hệ admin để xử lý'
                        ]);
                    }
                    //trừ tiền user
                    $userTransaction->balance = $userTransaction->balance - $price;
                    $userTransaction->balance_out = $userTransaction->balance_out + $price;
                    $userTransaction->save();


                    //thêm mới order service
                    $order = Order::create([
                        'request_id' => $userTransaction->id.time() . rand(10000, 99999),
                        'idkey'=>$service->idkey,
                        'ref_id' => $service->id,
                        'title' => $service->title,
                        'description' => (isset($namePacket[$selectedCustomer]) ? $namePacket[$selectedCustomer] : ""),
                        'params' => $customer_info,
                        'price_base' => $itemAmountFire,
                        'additional_amount' => $ratioOfShop->additional_amount,
                        'ratio_exchange_rate' => $ratioOfShop->ratio_percent,
                        'price' => $price,
                        'author_id' => $userTransaction->id,
                        'position' => $server,
                        'shop_id' => $request->shop_id,
                        'status' => 1, // 'Đang chờ xử lý'
                        'module' => config('module.service-purchase.key'),
                        'gate_id' => $input_auto == 1 ?? 0,
                        'payment_type' =>$payment_type,

                    ]);

                    $order->txns()->create([
                        'trade_type' => 'service_purchase', //Thanh toán dịch vụ
                        'user_id' => $userTransaction->id,
                        'is_add' => '0',//Trừ tiền
                        'amount' => $price,
                        'real_received_amount' => $price,
                        'last_balance' => $userTransaction->balance,
                        'description' =>  "Thanh toán dịch vụ #" . $order->id .' ('.$service->title.')',
                        'ip' => $request->getClientIp(),
                        'shop_id' => $request->shop_id,
                        'ref_id' => $order->id,
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
                    ]);
                    DB::commit();


                    //gọi api
                    $result =  GarenaGate_Phap::fire($shop->tichhop_key,$provider, $username, $password, $id, $itemAmountFire, "",$order->request_id,$net);

                    if ($result &&  isset($result->status)) {
                        if($result->status == 2){
                            if($result->user_balance<1000000){
                                $message="[" . Carbon::now() . "] "."[" . $request->root() . "] " . $shop->domain . " đã mua bắn kim cương và tài khoản tichhop.net còn dưới 1 triệu (Số dư hiện tại: ".number_format($result->user_balance).")" ;
                                Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_notify_balance_tichhop_net'));
                            }

                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workflow.key'),
                                'status' => 1,
                                'content' => "Đại lý đã tiếp nhận (Code:2)",
                            ]);

                            return response()->json([
                                'status' => 1,
                                'message' => 'Đang xử lý. tài khoản game của bạn sẽ được cộng vật phẩm sau khi kiểm tra!'
                            ]);
                        }
                        elseif ($result->status == -1 ||$result->status == 3 )  {
                            //nếu Tài khoản đại lý không đủ tiền thì hoàn lại tiền cho khách
                            if($result->status == -1){
                                $message="[" . Carbon::now() . "] "."[" . $request->root() . "] " . $shop->domain . " đã bắn kim cương và tài khoản tichhop.net còn dưới 1 triệu (Số dư hiện tại: ".number_format($result->user_balance).")" ;
                                Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_notify_balance_tichhop_net'));
                                $message_response="Tài khoản đại lý không đủ số dư";
                            }
                            else{
                                $message_response=$result->message??__('Kết nối với nhà cung cấp thất bại');
                                $message="[" . Carbon::now() . "] "."[" . $request->root() . "] " . $shop->domain . " đã bắn kim cương trên tichhop.net kết nối thất bại:".$message_response."Vui lòng xử lý thủ công hoặc nạp lại";
                                Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_notify_balance_tichhop_net'));
                            }



                            // Start transaction!
                            DB::beginTransaction();
                            try {
                                $order = Order::lockForUpdate()->findOrFail($order->id);
                                $order->update([
                                    'status' => 7,
                                ]);//trạng thái hủy

                                //set tiến độ hủy
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'module' => config('module.service-workflow.key'),
                                    'content' => $message_response,
                                    'status' => 7, //Đã hủy
                                ]);
                            } catch (\Exception $e) {
                                DB::rollback();
                                Log::error( $e);
                                return response()->json([
                                    'message' => __('Có lỗi phát sinh.Xin vui lòng liên hệ admin để xử lý !'),
                                    'status' => 0
                                ], 500);
                            }



                            // Commit the queries!
                            DB::commit();
                            return response()->json([
                                'status' => 0,
                                'message' =>$message_response
                            ]);
                        }
                        else {
                            $order->update([
                                'status' => 7, //Kết nối NCC thất bại
                            ]);
                            return response()->json([
                                'status' => 1,
                                'message' => 'Đang xử lý. tài khoản game của bạn sẽ được cộng vật phẩm sau khi kiểm tra!'
                            ]);
                        }
                    }
                    else {
                        $order->update([
                            'status' => 9, //Xử lý thủ công
                        ]);
                        return response()->json([
                            'status' => 1,
                            'message' => 'Đang xử lý. tài khoản game của bạn sẽ được cộng vật phẩm sau khi kiểm tra!'
                        ]);

                    }


                }
                else {

                    //trừ tiền user
                    $userTransaction->balance = $userTransaction->balance - $price;
                    $userTransaction->balance_out = $userTransaction->balance_out + $price;
                    $userTransaction->save();


                    //thêm mới order service
                    $order = Order::create([
                        'request_id' => $userTransaction->id.time() . rand(10000, 99999),
                        'ref_id' => $service->id,
                        'title' => $service->title,
                        'description' => (isset($namePacket[$selectedCustomer]) ? $namePacket[$selectedCustomer] : ""),
                        'params' => $customer_info,
                        'price' => $price,
                        'price_ctv' => $priceCTV,
                        'additional_amount' => $ratioOfShop->additional_amount,
                        'ratio_exchange_rate' => $ratioOfShop->ratio_percent,
                        'author_id' => $userTransaction->id,
                        'position' => $server,
                        'shop_id' => $request->shop_id,
                        'status' => 1, // 'Đang chờ xử lý'
                        'module' => config('module.service-purchase.key'),
                        'gate_id' => $input_auto == 1 ?? 0,

                    ]);

                    $order->txns()->create([
                        'trade_type' => 'service_purchase', //Thanh toán dịch vụ
                        'user_id' => $userTransaction->id,
                        'is_add' => '0',//Trừ tiền
                        'amount' => $price,
                        'real_received_amount' => $price,
                        'last_balance' => $userTransaction->balance,
                        'description' =>  "Thanh toán dịch vụ #" . $order->id .' ('.$service->title.')',
                        'ip' => $request->getClientIp(),
                        'shop_id' => $request->shop_id,
                        'ref_id' => $order->id,
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
                        'unit_price_ctv' => $priceCTV,
                    ]);


                }
            }
            //End Loại Chọn 1
            //////////////////////////Loại Chọn nhiều option///////////////////////////////////
            elseif ($filter_type == 5) {
                //
                $selectedCustomer = $request->get('selected');
                $selectedCustomer = explode('|', $selectedCustomer);
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
                if (Helpers::DecodeJson("server_mode", $service->items->params) == 1 && Helpers::DecodeJson("server_price", $service->items->params) == 1) {
                    $s_price_ctv = Helpers::DecodeJson("price" . $server, $service->items->params);
                } else {
                    $s_price_ctv = Helpers::DecodeJson("price", $service->items->params);
                }

                //Kiểm tra cách tính hòa hồng cho CTV commission_type = 1 tính theo giá config.
                if (Helpers::DecodeJson("commission_type", $service->params) == 1){
                    $s_price_ctv = $s_price;
                }

                //Tính tiền cho khách
                $price = 0;

                if (!empty($selectedCustomer) && count($selectedCustomer) > 0) {


                    foreach ($selectedCustomer as $aSelect) {

                        //ốp tỉ giá theo cấu hình theo từng option chọn
                        $priceRaw=isset($s_price[$aSelect]) ? $s_price[$aSelect] : 0;
                        $price += floor($priceRaw*$ratioOfShop->ratio_percent/100)+$ratioOfShop->additional_amount;
                    }
                }

                //check giá trị tiền phù hợp với cấu hình dịch vụ
                if ($price <= 0) {

                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => 'Lựa chọn dịch vụ không hợp lệ.Vui lòng chọn lại'
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
                            'message' => 'Lựa chọn dịch vụ CTV không hợp lệ.Vui lòng chọn lại'
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
                            'message' => 'Vui lòng chọn máy chủ của dịch vụ thanh toán'
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

                                DB::rollback();
                                return response()->json([
                                    'status' => 0,
                                    'message' => 'Vui lòng điền đầy đủ thông tin yêu cầu để thanh toán'
                                ]);

                            }

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
                if (Helpers::DecodeJson("server_mode", $service->params) == 1) {
                    $customer_info['server'] = $server_data[$server];
                }



                //trừ tiền user
                $userTransaction = User::where('id', Auth::guard('api')->user()->id)->lockForUpdate()->firstOrFail();

                if ($userTransaction->checkBalanceValid() == false) {
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => 'Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý'
                    ]);

                }

                if ($userTransaction->balance < $price) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 0,
                        'message' => 'Bạn không đủ tiền để thanh toán.Vui lòng nạp thêm tiền vào tài khoản'
                    ]);
                }

                $userTransaction->balance = $userTransaction->balance - $price;
                $userTransaction->balance_out = $userTransaction->balance_out + $price;
                $userTransaction->save();

                //thêm mới order service
                $order = Order::create([
                    'request_id' => $userTransaction->id.time() . rand(10000, 99999),
                    'ref_id' => $service->id,
                    'title' => $service->title,
                    'description' => '',
                    'params' => $customer_info,
                    'price' => $price,
                    'price_ctv' => $priceCTV,
                    'additional_amount' => $ratioOfShop->additional_amount,
                    'ratio_exchange_rate' => $ratioOfShop->ratio_percent,
                    'author_id' => $userTransaction->id,
                    'position' => $server,
                    'shop_id' => $request->shop_id,
                    'status' => 1, // 'Đang chờ xử lý'
                    'module' => config('module.service-purchase.key'),
                    'gate_id' => $input_auto == 1 ?? 0,

                ]);

                $order->txns()->create([
                    'trade_type' => 'service_purchase', //Thanh toán dịch vụ
                    'user_id' => $userTransaction->id,
                    'is_add' => '0',//Trừ tiền
                    'amount' => $price,
                    'real_received_amount' => $price,
                    'last_balance' => $userTransaction->balance,
                    'description' =>  "Thanh toán dịch vụ #" . $order->id .' ('.$service->title.')',
                    'ip' => $request->getClientIp(),
                    'shop_id' => $request->shop_id,
                    'ref_id' => $order->id,
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
                    foreach ($selectedCustomer as $aSelect) {
                        if (isset($namePacket[$aSelect])) {

                            //ốp tỉ giá theo cấu hình theo từng option chọn
                            $priceRaw= isset($s_price[$aSelect])?$s_price[$aSelect]:0;
                            $price = ($priceRaw*$ratioOfShop->ratio_percent/100)+$ratioOfShop->additional_amount;

                            // tính tiền cho price_CTV
                            $priceCTV = isset($s_price_ctv[$aSelect])?$s_price_ctv[$aSelect]:0;
                            //set tên công việc
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.service-workname.key'),
                                'title' => (isset($namePacket[$aSelect]) ? $namePacket[$aSelect] : ""),
                                'unit_price' => $price,
                                'unit_price_ctv' => $priceCTV,
                            ]);
                        }

                    }
                }

            }
            //////////////////////////Loại Chọn a->b///////////////////////////////////
            elseif ($filter_type == 6) {

                $rankfrom = $request->get('rank_from');
                $rankto = $request->get('rank_to');
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
                if (Helpers::DecodeJson("server_mode", $service->items->params) == 1 && Helpers::DecodeJson("server_price", $service->items->params) == 1) {
                    $s_price_ctv = Helpers::DecodeJson("price" . $server, $service->items->params);
                } else {
                    $s_price_ctv = Helpers::DecodeJson("price", $service->items->params);
                }

                //Kiểm tra cách tính hòa hồng cho CTV commission_type = 1 tính theo giá config.
                if (Helpers::DecodeJson("commission_type", $service->params) == 1){
                    $s_price_ctv = $s_price;
                }

                //Tính tiền cho khách
                $price = 0;
                $price = $s_price[$rankto] - $s_price[$rankfrom];

                //ốp tỉ giá theo cấu hình
                $price=floor($price*$ratioOfShop->ratio_percent/100)+$ratioOfShop->additional_amount;

                //check giá trị tiền phù hợp với cấu hình dịch vụ
                if ($price <= 0) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 0,
                        'message' => 'Lựa chọn dịch vụ không hợp lệ'
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
                            'message' => 'Lựa chọn dịch vụ CTV không hợp lệ.Vui lòng chọn lại'
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

                                DB::rollback();
                                return response()->json([
                                    'status' => 0,
                                    'message' => 'Vui lòng điền đầy đủ thông tin yêu cầu để thanh toán'
                                ]);

                            }

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
                if (Helpers::DecodeJson("server_mode", $service->params) == 1) {
                    $customer_info['server'] = $server_data[$server];
                }
                //check giao dich tự động && custom service
                $input_auto = $service->gate_id;

                //trừ tiền user
                $userTransaction = User::where('id', Auth::guard('api')->user()->id)->lockForUpdate()->firstOrFail();
                if ($userTransaction->checkBalanceValid() == false) {
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'message' => 'Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý'
                    ]);

                }

                if ($userTransaction->balance < $price) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Bạn không đủ tiền để thanh toán.Vui lòng nạp thêm tiền vào tài khoản'
                    ]);
                }

                //trừ tiền user
                $userTransaction->balance = $userTransaction->balance - $price;
                $userTransaction->balance_out = $userTransaction->balance_out + $price;
                $userTransaction->save();


                //thêm mới order service
                $order = Order::create([
                    'request_id' => $userTransaction->id.time() . rand(10000, 99999),
                    'ref_id' => $service->id,
                    'title' => $service->title,
                    'params' => $customer_info,
                    'price' => $price,
                    'price_ctv' => $priceCTV,
                    'additional_amount' => $ratioOfShop->additional_amount,
                    'ratio_exchange_rate' => $ratioOfShop->ratio_percent,
                    'author_id' => $userTransaction->id,
                    'position' => $server,
                    'shop_id' => $request->shop_id,
                    'status' => 1, // 'Đang chờ xử lý'
                    'module' => config('module.service-purchase.key'),
                    'gate_id' => $input_auto == 1 ?? 0,

                ]);

                $order->txns()->create([
                    'trade_type' => 'service_purchase', //Thanh toán dịch vụ
                    'user_id' => $userTransaction->id,
                    'is_add' => '0',//Trừ tiền
                    'amount' => $price,
                    'real_received_amount' => $price,
                    'last_balance' => $userTransaction->balance,
                    'description' =>  "Thanh toán dịch vụ #" . $order->id .' ('.$service->title.')',
                    'ip' => $request->getClientIp(),
                    'shop_id' => $request->shop_id,
                    'ref_id' => $order->id,
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

                        //set tên công việc
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workname.key'),
                            'title' => ((isset($namePacket[$rankfrom]) && isset($namePacket[$rankto])) ? ($namePacket[$rankfrom] . "->" . $namePacket[$rankto]) : ""),
                            'unit_price' => $price,
                            'unit_price_ctv' => $priceCTV,
                        ]);

                    }
                }

            }


        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            $order->status=999;
            $order->save();
            //set tiến độ
            OrderDetail::create([
                'order_id' => $order->id,
                'module' => config('module.service-workflow.key'),
                'status' => 0,
                'content' => "Lỗi logic xử lý",
            ]);
            return response()->json([
                'status' => 1,
                'message' => 'Thực hiện thanh toán thành công'
            ]);
        }

        // Commit the queries!
        DB::commit();
        return response()->json([
            'status' => 1,
            'message' => 'Thực hiện thanh toán thành công'
        ]);


    }

    public function getLog(Request $request)
    {


        try {

            $shop = Shop::where('secret_key', $request->secret_key)->where('id', $request->shop_id)->where('status', 1)->first();
            if (!$shop) {
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }

            $categoryservice = ItemConfig::with(array('items' => function ($query) {
                $query->select('id', 'title', 'slug', 'description');
            }))->where('module', config('module.service'))
                ->where('shop_id', $shop->id)
                ->select('id', 'title', 'slug', 'description', 'content', 'image')->get();

            $datatable = Order::with('itemconfig_ref')
                ->with(array('workflow' => function ($query) {
                    $query->where('module', config('module.service-workflow.key'))
                        ->orderBy('id', 'asc');

                }))
                ->with(array('workname' => function ($query) {
                    $query->where('module', config('module.service-workname.key'))
                        ->orderBy('id', 'asc');

                }))
                ->where('module', config('module.service-purchase'))
                ->select('id', 'title', 'description', 'gate_id', 'content', 'params', 'status', 'created_at', 'price', 'ratio', 'module', 'payment_type', 'ref_id', 'author_id', 'position');

            if ($request->filled('author_id')) {
                $author_id = $request->author_id;
                $datatable->where('author_id', $author_id);
            }

            if ($request->filled('id')) {
                $id = $request->id;
                $datatable->where('id', $id);
            }

            if ($request->filled('status')) {
                $status = $request->status;
                $datatable->where('status', $status);
            }

            if ($request->filled('slug_category')) {
                $slug = $request->slug_category;
                $datatable->whereHas('itemconfig_ref', function ($query) use ($slug) {
                    $query->where('id', '=', $slug);
                });
            }

            if ($request->filled('started_at')) {
                $datatable->where('created_at', '>=', $request->started_at);

            }
            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', $request->ended_at);
            }

            if ($request->filled('sort')) {
                if ($request->sort == 'random') {
                    $datatable->inRandomOrder();
                } elseif (in_array($request->sort, ['asc', 'desc'])) {
                    $datatable->orderBy($request->sort_by ?? 'id', $request->sort);
                }
            } else {
                $datatable->orderBy('created_at', 'desc');
            }


            $datatable = $datatable->paginate(10);

            return response()->json([
                'message' => __('Thành công'),
                'status' => 1,
                'categoryservice' => $categoryservice,
                'datatable' => $datatable,
            ], 200);

        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }

    public function getLogDetail(Request $request)
    {

        $datatable = Order::query()
            ->where('module', config('module.service-purchase'))
            ->where('id', '=', $request->get('id'))
            ->where('author_id', '=', Auth::guard('api')->user()->id)
            ->with('itemconfig_ref')
            ->with(array('workflow' => function ($query) {
                $query->where('module', config('module.service-workflow.key'))
                    ->orderBy('id', 'asc');

            }))
            ->with(array('workname' => function ($query) {
                $query->where('module', config('module.service-workname.key'))
                    ->orderBy('id', 'asc');

            }))
            ->with('order_refund')
            ->select('id', 'title', 'description', 'gate_id', 'content', 'params', 'status', 'created_at', 'price', 'ratio', 'module', 'payment_type', 'ref_id', 'author_id', 'position')
            ->first();

        if($datatable){

            $conversation=  Conversation::where( 'ref_id' , $datatable->id)->first();
            if($conversation){
                $inbox= Inbox::with('user')->where('conversation_id',$conversation->id)->get();
            }
            else{
                $inbox=[];
            }

            return response()->json([
                'status' =>1,
                'message' => __('Thành công'),
                'data'=>$datatable,
                'conversation'=>$conversation,
                'inbox'=>$inbox,
            ]);
        }else{
            return response()->json([
                'message' => __('Không tìm thấy thông tin'),
                'status' => 0
            ], 404);
        }
    }

    public function postEditInfo(Request $request, $id)
    {

        DB::beginTransaction();
        try {


            $data = Order::with('itemconfig_ref')
                ->where('module', config('module.service-purchase.key'))
                ->where('status', 1)
                ->where('author_id', '=', Auth::guard('api')->user()->id)
                ->lockForUpdate()->find($id);


            if(!$data){
                return response()->json([
                    'status' =>0,
                    'message' => __('Không tìm thấy đơn giao dịch'),
                ]);
            }

            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }



            //check dịch vụ auto sms thì không cho chỉnh sửa auto
            $input_auto = $data->gate_id;

            $idkey = $data->itemconfig_ref->idkey??"";
            if ($input_auto == 1) {

                if($idkey != 'roblox_internal' && $idkey != 'roblox_gem_pet' && $idkey != 'nrocoin' && $idkey != 'langlacoin' && $idkey != 'ninjaxu' && $idkey != 'nrocoin_internal' && $idkey != 'langlacoin_internal' && $idkey != 'ninjaxu_internal' ){
                    return response()->json([
                        'status' =>0,
                        'message' => __('Các dịch vụ tự động SMS không thể chỉnh sửa. Vui lòng liên hệ admin để xử lý !'),
                    ]);
                }

            }

            if ($data->sticky >= 3) {

                return response()->json([
                    'status' =>0,
                    'message' => __("Bạn đã sửa quá giới hạn 3 lần. Vui lòng hủy yêu cầu để thực hiện lại giao dịch"),
                ]);

            }
            if ($data->expired_lock != null && $data->expired_lock > Carbon::now()) {
                return response()->json([
                    'status' =>0,
                    'message' => __("Dịch vụ đã được thực hiện. Vui lòng thử lại trong vòng 5 phút"),
                ]);
            }

            //Kiểm tra thông tin nhập lên
            $send_name = Helpers::DecodeJson("send_name", $data->itemconfig_ref->params);
            $send_type = Helpers::DecodeJson("send_type", $data->itemconfig_ref->params);
            $customer_info = [];
            if (!empty($send_name) && count($send_name) > 0) {
                for ($i = 0; $i < count($send_name); $i++) {

                    if ($send_type[$i] == 4 && $request->hasFile('customer_data' . $i)) { //nếu  nó là kiểu upload ảnh

                        $info= MediaHelpers::imageBase64($request->get('customer_data'.$i));

                        $customer_info['customer_data'.$i]=$info;

                    } else {

                        if ($request->get('customer_data' . $i) == "" || $request->get('customer_data' . $i) == null) {

                            DB::rollback();
                            return response()->json([
                                'status' => 0,
                                'message' => 'Vui lòng điền đầy đủ thông tin yêu cầu để thanh toán'
                            ]);

                        }

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
            $oldServerName=$data->params->server;
            $customer_info['server']=$oldServerName;
            //update info cho purchase
            $data->params = $customer_info;
            $data->sticky = $data->sticky + 1;
            $data->save();

            //check và edit thông tin giao dich tự động && custom service

            //set tiến độ
            OrderDetail::create([
                'order_id' => $data->id,
                'module' => config('module.service-workflow.key'),
                'status' => 1,
                'content' => "Chỉnh sửa thông tin thành công",
            ]);

            if ($input_auto == 1) {

                if( $idkey == 'nrocoin_internal'){

                    $khachhang = KhachHang::where('order_id', $data->id)->lockForUpdate()->firstOrFail();
                    $khachhang->uname = $request->customer_data0;
                    $khachhang->save();
                }

                elseif ( $idkey == 'langlacoin_internal' ) {

                    $langla_khachhang = LangLaCoin_KhachHang::where('order_id', $data->id)->lockForUpdate()->firstOrFail();
                    $langla_khachhang->uname = $request->customer_data0;
                    $langla_khachhang->save();
                }
                elseif (  $idkey == 'ninjaxu_internal' ) {

                    $ninjaxu_khachhang = NinjaXu_KhachHang::where('order_id', $data->id)->lockForUpdate()->firstOrFail();
                    $ninjaxu_khachhang->uname = $request->customer_data0;
                    $ninjaxu_khachhang->save();

                }

                //check gửi thông tin daily
                if ( $idkey == 'nrocoin' || $idkey == 'langlacoin' || $idkey == 'ninjaxu' ) {


                        $result=HelperItemDaily::editInfo($shop->daily_partner_id,$shop->daily_partner_key_service,$data->id,$request->customer_data0,$idkey,$data->request_id);

                        if($result  ){
                            if($result->status==1){
                                DB::commit();
                                return response()->json([
                                    'status' =>1,
                                    'message' => __('Chỉnh sửa thông tin thành công'),

                                ]);
                            }
                            else{
                                DB::rollBack();
                                return response()->json([
                                    'status' =>0,
                                    'message' => __('Chỉnh sửa thông tin thất bại'),

                                ]);
                            }

                        }
                        else{
                            DB::rollBack();
                            return response()->json([
                                'status' =>0,
                                'message' => __('Chỉnh sửa thông tin thất bại'),

                            ]);
                        }
                }
                else{
                    return response()->json([
                        'status' =>0,
                        'message' => __('Dịch vụ này không được quyền chỉnh sửa thông tin'),

                    ]);
                }
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
            'message' => __('Chỉnh sửa thông tin thành công'),

        ]);
    }


    public function postDestroy(Request $request, $id)
    {

        $this->validate($request, [
            'mistake_by' => 'required',
        ], [
            'mistake_by.required' => "Vui lòng chọn lỗi thuộc về",

        ]);

        // Start transaction!
        DB::beginTransaction();
        try {


            $data = Order::query()
                ->where('module', config('module.service-purchase'))
                ->where('status', "1")
                ->where('author_id', '=', Auth::guard('api')->user()->id)
                ->with('itemconfig_ref')
                ->with('order_refund')
                ->lockForUpdate()->findOrFail($id);
            $userTransaction = User::where('id', Auth::guard('api')->user()->id)->lockForUpdate()->firstOrFail();

            $input_auto = $data->gate_id;
            if ($input_auto == 1) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Các dịch vụ tự động SMS không thể hủy bỏ. Vui lòng liên hệ admin để xử lý !'
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
                'author_id' => $userTransaction->id,
                'status' => 0,
            ]);

            //check và edit thông tin giao dich tự động && custom service
            $idkey = $data->itemconfig_ref->idkey??"";

            if ($input_auto == 1 && $idkey == 'nrocoin') {

                $khachhang = KhachHang::where('order_id', $data->id)->lockForUpdate()->firstOrFail();
                $khachhang->status = "dahuybo";
                $khachhang->save();
            } elseif ($input_auto == 1 && $idkey == 'nrogem') {
                $nrogem_GiaoDich = Nrogem_GiaoDich::where('order_id', $data->id)->lockForUpdate()->firstOrFail();
                $nrogem_GiaoDich->status = "dahuybo";
                $nrogem_GiaoDich->save();

            } elseif ($input_auto == 1 && $idkey == 'langlacoin' ) {
                $langla_khachhang = LangLaCoin_KhachHang::where('order_id', $data->id)->lockForUpdate()->firstOrFail();
                $langla_khachhang->status = "dahuybo";
                $langla_khachhang->save();
            } elseif ($input_auto == 1 && $idkey == 'ninjaxu' ) {
                $ninjaxu_khachhang = NinjaXu_KhachHang::where('order_id', $data->id)->lockForUpdate()->firstOrFail();
                $ninjaxu_khachhang->status = "dahuybo";
                $ninjaxu_khachhang->save();
            }elseif ($input_auto == 1 && $data->item_ref->idkey == 'roblox_internal'){
                $roblox_order = Roblox_Order::where('order_id', $data->id)->lockForUpdate()->firstOrFail();
                $roblox_order->status = "dahuybo";
                $roblox_order->save();
            }elseif ($input_auto == 1 && $data->item_ref->idkey == 'roblox_gem_pet'){
                $roblox_order = Roblox_Order::where('order_id', $data->id)->lockForUpdate()->firstOrFail();
                $roblox_order->status = "dahuybo";
                $roblox_order->save();
            }

            if ($userTransaction->checkBalanceValid() == false) {
                DB::rollback();
                return response()->json([
                    'status' => 0,
                    'message' => 'Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý'
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
                'description' => 'Hoàn tiền từ chối yêu cầu dich vụ #' . $data->id,
                'ref_id' => $data->id,
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
            'message' => __("Đã hủy thành công yêu cầu dịch vụ #" . $data->id),
        ]);


    }

    public function postRefundOrder(Request $request,$id){

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'content' => 'required',
            ],[
                'content.required' => __("Vui lòng nhập nội dung hoàn tiền"),
            ]);
            if($validator->fails()){
                DB::rollback();
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'status' => 0
                ],422);
            }

            $content = $request->get('content');

            $data = Order::with('itemconfig_ref')
                ->with('order_refund')
                ->where('module', config('module.service-purchase.key'))
                ->where('status', 10)
                ->where('author_id', '=', Auth::guard('api')->user()->id)
                ->lockForUpdate()->find($id);

            if(!isset($data)){
                DB::rollback();
                return response()->json([
                    'status' =>0,
                    'message' => __('Không tìm thấy đơn giao dịch'),
                ]);
            }

            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!isset($shop)){
                DB::rollback();
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }

            //check dịch vụ auto sms thì không cho chỉnh sửa auto
            $input_auto = $data->gate_id;

            if ($input_auto == 1) {
                DB::rollback();
                return response()->json([
                    'message' => __('Đơn hàng tự động vui lòng kiểm tra lại'),
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
            $userTransaction = User::where('id', Auth::guard('api')->user()->id)->lockForUpdate()->firstOrFail();
            if ($userTransaction->checkBalanceValid() == false) {

                DB::rollback();
                return response()->json([
                    'status' => 0,
                    'message' => 'Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý'
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
                'author_id' => $userTransaction->id,
                'content' => json_encode($params,JSON_UNESCAPED_UNICODE),
                'status' => 2,//chờ xử lý
            ]);

            //set tiến độ hoan tien
            OrderDetail::create([
                'order_id' => $data->id,
                'module' => config('module.service-workflow.key'),
                'author_id' => $userTransaction->id,
                'status' => 11,
                'content' => $content,
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
            'message' => __('Yêu cầu hoàn tiền thành công'),

        ]);
    }

    public function postDeleteRefundOrder(Request $request,$id){

        DB::beginTransaction();
        try {

            $data = Order::with('itemconfig_ref')
                ->with('order_refund')
                ->where('module', config('module.service-purchase.key'))
                ->where('status', 11)
                ->where('author_id', '=', Auth::guard('api')->user()->id)
                ->lockForUpdate()->find($id);

            if(!isset($data)){
                return response()->json([
                    'status' =>0,
                    'message' => __('Không tìm thấy đơn giao dịch'),
                ]);
            }

            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();

            if(!isset($shop)){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }

            //check dịch vụ auto sms thì không cho chỉnh sửa auto
            $input_auto = $data->gate_id;

            if ($input_auto == 1) {

                return response()->json([
                    'message' => __('Đơn hàng tự động vui lòng kiểm tra lại'),
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
            $data->process_at = Carbon::now();//Thời gian xác nhận đơn hàng
            $data->save();

            //set tiến độ tiếp nhận
            OrderDetail::create([
                'order_id'=>$data->id,
                'module' => config('module.service-workflow.key'),
                'author_id' =>  Auth::guard()->user()->id,
                'content' => 'Khách hàng hủy yêu cầu hoàn tiền',
                'status' => 10,
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
            'message' => __('Yêu cầu hoàn tiền thành công'),

        ]);
    }
}
