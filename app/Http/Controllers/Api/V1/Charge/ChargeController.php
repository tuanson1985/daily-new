<?php

namespace App\Http\Controllers\Api\V1\Charge;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shop;
use App\Models\Telecom;
use App\Models\TelecomValue;
use App\Models\Charge;
use Validator;
use Carbon\Carbon;
use Cache;
use DB;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\Order;
use App\Library\Helpers;
use App\Library\ChargeGateway\NAPTHENHANH;
use App\Library\ChargeGateway\CANCAUCOM;
use App\Library\ChargeGateway\PAYPAYPAY;

class ChargeController extends Controller
{
    public function getTopCharge(Request $request){
        try {
            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }
            $top_charge = array();
            $data = Cache::get('cache_top_charge_'.$shop->id);
            $sys_top_charge = setting('sys_top_charge',null,$shop->id);
            if(empty($sys_top_charge)){
                return response()->json([
                    'message' => __('Thành công'),
                    'status' => 1,
                    'data' => $data
                ], 200);
            }
            if($sys_top_charge != ''){
                $sys_top_charge = json_decode($sys_top_charge);
            }
            $data_fake_top_charge = array();
            foreach($sys_top_charge as $key => $item){
                $data_fake_top_charge[] = [
                    'user_id' => time() + $key,
                    'amount' => $item->amount,
                    'username' => $item->user,
                    'fullname' => null,
                ];
            }
            if(isset($data) && count($data) > 0){
                $top_charge = array_merge($data,$data_fake_top_charge);
                usort($top_charge, function ($a, $b) {return $a['amount'] < $b['amount'];});
            }
            else{
                $top_charge = $data_fake_top_charge;
            }
            
            return response()->json([
                'message' => __('Thành công'),
                'status' => 1,
                'data' => $top_charge
            ], 200);
        }catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }

    public function getHistory(Request $request){
        try {
            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }
            $user = Auth::guard('api')->user();
            $data = Charge::where('shop_id',$shop->id)
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc');

            if ($request->filled('serial')){
                $data = $data->where('status','LIKE', '%' . $request->get('serial') . '%');
            }
            if ($request->filled('key')){
                $data = $data->where('telecom_key',$request->get('key'));
            }

            if ($request->filled('status')){
                $data = $data->where('status',$request->get('status'));
            }

            if ($request->filled('started_at')) {
                $data = $data->where('created_at', '>=', $request->started_at);
            }

            if ($request->filled('ended_at')) {
                $data = $data->where('created_at', '<=', $request->ended_at);
            }
            $paginate = 20;
            if($request->filled('paginate')){
                $paginate = $request->get('paginate');
            }
            $data = $data->select('id', 'type_charge','telecom_key','pin','serial','process_at','declare_amount','amount','ratio','real_received_amount','txns_id','response_mess','description','status','created_at')
            ->paginate($paginate);
            return response()->json([
                'message' => __('Thành công'),
                'status' => 1,
                'data' => $data
            ], 200);
        }
        catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => "Có lỗi phát sinh.Xin vui lòng thử lại !",
                'status' => 0
            ],500);
        }
    }
    public function getDetails(Request $request,$id){
        try {
            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }
            $user = Auth::guard('api')->user();
            $data = Charge::where('shop_id',$shop->id)
            ->where('user_id', $user->id)
            ->where('id',$id)
            ->select('id', 'type_charge','telecom_key','pin','serial','process_at','declare_amount','amount','ratio','real_received_amount','txns_id','response_mess','description','status','created_at')
            ->first();
            if(!$data){
                return response()->json([
                    'message' => __('Không tìm thấy dữ liệu yêu cầu.'),
                    'status' => 0,
                ], 200);
            }
            return response()->json([
                'message' => __('Thành công'),
                'status' => 1,
                'data' => $data
            ], 200);
        }
        catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => "Có lỗi phát sinh.Xin vui lòng thử lại !",
                'status' => 0
            ],500);
        }
    }
    public function getTelecomDepositAuto(Request $request){
        try {
            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }

            $data = Telecom::where('shop_id',$shop->id)
            ->where('status',1)
            ->orderBy('order', 'asc')
            ->select('id','key','title')
            ->get();
            return response()->json([
                'message' => __('Thành công'),
                'data' => $data,
                'status' => 1
            ], 200);
        }catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }

    public function getAmountDepositAuto(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'telecom' => 'required',
            ],[
                'telecom.required' => __('Nhà mạng bị thiếu'),
            ]);
            if($validator->fails()){
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'status' => 0
                ],422);
            }
            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }
            $telecom = Telecom::where('shop_id',$shop->id)
            ->where('status', 1)
            ->where('key', $request->telecom)
            ->first();
            if(!$telecom){
                return response()->json([
                    'message' => __('Nhà mạng không tồn tại hoặc bị khóa bởi Admin'),
                    'status' => 0,
                ], 200);
            }
            $telecom_value = TelecomValue::where('shop_id',$shop->id)
            ->where('telecom_id', $telecom->id)
            ->where('shop_id', $shop->id)
            ->where('status', 1)
            ->select('id','amount','telecom_key','ratio_true_amount','ratio_false_amount','agency_ratio_true_amount','agency_ratio_false_amount','created_at')
            ->get();
            return response()->json([
                'message' => __('Thành công'),
                'status' => 1,
                'data' => $telecom_value
            ], 200);

        }catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0,
            ], 500);
        }
    }


    public function postDepositAuto(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required|regex:/^([A-Za-z0-9])+$/i',
            'amount' => 'required|integer|in:10000,20000,30000,50000,100000,200000,300000,500000,1000000,2000000,3000000,5000000',
            'pin' => 'required|between::9,22|regex:/^([A-Za-z0-9])+$/i',
            'serial' => 'required|between:9,22|regex:/^([A-Za-z0-9])+$/i',
        ],[
            'type.required' => __("Vui lòng chọn loại thẻ"),
            'type.regex' => __('Loại thẻ không được có ký tự đặc biệt'),
            'amount.required' => __("Vui lòng chọn mệnh giá"),
            'amount.in' => __("Mệnh giá không đúng định dạng"),
            'amount.integer' => __("Mệnh giá không đúng định dạng"),
            'pin.required' => __("Vui lòng nhập mã thẻ"),
            'pin.between' => __("Mã thẻ phải từ 9 - 16 ký tự"),
            'pin.regex' => __('Mã thẻ không được có ký tự đặc biệt'),
            'serial.required' => __("Vui lòng nhập số serial"),
            'serial.between' => __("Serial thẻ phải từ 9 - 16 ký tự"),
            'serial.regex' => __('Serial thẻ không được có ký tự đặc biệt'),
        ]);
        if($validator->fails()){
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 0
            ],422);
        }
        try{
            DB::beginTransaction();
            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }
            $user = Auth::guard('api')->user();
            if (Cache::get('fail_charge_' . $user->id) >= 5) {
                return response()->json([
                    'message' => "Bạn đã bị khóa nạp thẻ 5 phút !",
                    'status' => 0
                ],200);
            }
            //check spam
            $checkSpam = Charge::where('serial',$request->serial)
            ->where('pin',Helpers::Encrypt($request->pin,config('module.charge.key_encrypt')))
            ->where('type_charge', 0)
            ->select(
                DB::raw('SUM(IF(status = 1, 1, 0)) as pin_true'),
                DB::raw('SUM(IF(status = 0, 1, 0)) as pin_false'),
                DB::raw('SUM(IF(status = 2, 1, 0)) as pin_wait'),
                DB::raw('SUM(IF(status = 998, 1, 0)) as pin_wait_998')
            )
            ->where(function ($q) {
                $q->orWhere('status', 0);
                $q->orWhere('status', 1);
                $q->orWhere('status', 2);
                $q->orWhere('status', 998);
            })
            ->get();
            if ($checkSpam[0]->pin_true >= 1 || $checkSpam[0]->pin_false >= 3) {
                return response()->json([
                    'message' => "Thẻ này đã nạp trước đó !",
                    'status' => 0
                ],200);
            }
            elseif ($checkSpam[0]->pin_wait >= 1) {
                return response()->json([
                    'message' => "Thẻ này đang chờ xử lý.Bạn sẽ được cộng tiền ngay sau khi kiểm tra thành công nếu thẻ đúng !",
                    'status' => 0
                ],200);
            }
            elseif ($checkSpam[0]->pin_wait_998 >= 1) {
                return response()->json([
                    'message' => "Thẻ này đang chờ xử lý.Bạn sẽ được cộng tiền ngay sau khi kiểm tra thành công nếu thẻ đúng !",
                    'status' => 0
                ],200);
            }
            ActivityLog::add($request, 'Nạp thẻ '.$request->type.'mệnh giá '.$request->amount.' VNĐ');
            $type_charge = 0;
            $telecom = Telecom::where('type_charge', $type_charge)
            ->where('shop_id',$shop->id)
            ->where('status', 1)
            ->where('key', $request->type)
            ->first();
            if(!$telecom){
                return response()->json([
                    'message' => __("Nhà mạng không hợp lệ."),
                    'status' => 0
                ],200);
            }
            $telecom_value = TelecomValue::where('telecom_id', $telecom->id)
            ->where('shop_id',$shop->id)
            ->where('amount', $request->amount)
            ->where('status', 1)
            ->first();
            if(!$telecom_value){
               return response()->json([
                    'message' => __("Mệnh giá không hợp lệ."),
                    'status' => 0
                ],200);
            }
            $gate_id = $telecom->gate_id;
            if(empty($gate_id)){
                return response()->json([
                    'message' => __("Cổng thẻ không hợp lệ, vui lòng liên hệ Admin để xử lý."),
                    'status' => 0
                ],200);
            }
            $request_id = time() . rand(10000, 99999) . $user->id;
            $data_charge = Charge::create([
                'shop_id' => $shop->id,
                'type_charge' => $type_charge,
                'user_id' => $user->id,
                'gate_id' => $telecom->gate_id,
                'telecom_key' => $telecom->key,
                'pin' => Helpers::Encrypt($request->pin,config('module.charge.key_encrypt')),
                'serial' => $request->serial,
                'declare_amount' => $request->amount,
                'amount' => 0,
                'request_id' => $request_id,
                'ip' => $request->getClientIp(),
                'status' => 2
            ]);
            DB::commit();
            // Trường hợp chạy cổng NTN
            if($gate_id == 1){
                $result = NAPTHENHANH::API($shop->ntn_partner_id,$shop->ntn_partner_key,$telecom->key, $request->pin, $request->serial, $telecom_value->amount,$request_id,$shop->domain);
            }
            // Trường hợp chạy cổng CCC
            elseif($gate_id == 2){
                $result = CANCAUCOM::API($shop->ccc_partner_id,$shop->ccc_partner_key,$telecom->key, $request->pin, $request->serial, $telecom_value->amount,$request_id,$shop->domain);
            }
            // Trường hợp chạy cổng ppp
            elseif($gate_id == 3){
                $result = PAYPAYPAY::API($shop->ppp_partner_id,$shop->ppp_partner_key,$telecom->key, $request->pin, $request->serial, $telecom_value->amount,$request_id,$shop->domain);
            }
            else{
                $result="WRONG_GATEWAY";
            }
            DB::beginTransaction();
            try{
                $charge = Charge::where('id',$data_charge->id)->where('status',2)->lockForUpdate()->first();
                if(!$charge){
                    return response()->json([
                        'message' => __("Dữ liệu không hợp lệ, vui lòng báo Admin để kịp thời xử lý."),
                        'status' => 0
                    ],200);
                }
                if($result==="WRONG_GATEWAY"){
                    $charge->status = -999;
                    $charge->response_code = -1;
                    $charge->response_mess = "Không tìm thấy cổng gạch thẻ";
                    $charge->save();
                    DB::commit();
                    return response()->json([
                        'message' => __("Cổng thẻ không hợp lệ, vui lòng liên hệ Admin để xử lý."),
                        'status' => 0
                    ],200);
                }
                if($result && $result === "CARD_TYPE_NOT_WORKING"){
                    $charge->status = -999;
                    $charge->response_code = -1;
                    $charge->response_mess = "Không convert được nhà mạng gạch thẻ, vui lòng liên hệ Admin để xử lý";
                    $charge->save();
                    DB::commit();
                    return response()->json([
                        'message' => __("Không convert được nhà mạng gạch thẻ, vui lòng liên hệ Admin để xử lý."),
                        'status' => 0
                    ],200);
                }
                if($result && $result === "ERROR"){
                    $charge->status = -999;
                    $charge->response_code = -1;
                    $charge->response_mess = "Hệ thống gửi dữ liệu gạch thẻ bị lỗi, vui lòng liên hệ Admin để xử lý.";
                    $charge->save();
                    DB::commit();
                    return response()->json([
                        'message' => __("Hệ thống gửi dữ liệu gạch thẻ bị lỗi, vui lòng liên hệ Admin để xử lý."),
                        'status' => 0
                    ],200);
                }
                if ($result && isset($result->status)) {
                    if ($result->status == 2) {
                        $charge->status = 2;
                        $charge->response_code = $result->response_code??null;
                        $charge->response_mess = $result->message??null;
                        $charge->tranid = $result->tranid??null;
                        $charge->save();
                        DB::commit();
                        return response()->json([
                            'message' => 'Thẻ cào của bạn đang được kiểm tra.Bạn sẽ được cộng tiền ngay sau khi kiểm tra thành công nếu thẻ đúng ' . Cache::get('fail_charge_' . $user->id),
                            'status' => 1,
                        ],200);
                    }
                    elseif($result->status == 77) {
                        $charge->status = 0;
                        $charge->response_code = $result->response_code??null;
                        $charge->response_mess = $result->message??null;
                        $charge->tranid = $result->tranid??null;
                        $charge->save();
                        DB::commit();
                        return response()->json([
                            'message' => "Nạp thẻ thất bại ! " . $result->message,
                            'status' => 0
                        ],200);
                    }
                    else{
                        $charge->status = 0;
                        $charge->response_code = $result->response_code;
                        $charge->response_mess = $result->message;
                        $charge->tranid = $result->tranid??null;
                        $charge->save();
                        DB::commit();
                        return response()->json([
                            'message' => "Nạp thẻ thất bại ! " . $result->message,
                            'status' => 0
                        ],200);
                    }
                }
                else{
                    $charge->status = 998;
                    $charge->response_code = -1;
                    $charge->response_mess = "Không tìm thấy phản hồi từ máy chủ gạch thẻ, vui lòng liên hệ Admin để xử lý.";
                    $charge->save();
                    DB::commit();
                    return response()->json([
                        'message' => "Không tìm thấy phản hồi từ máy chủ gạch thẻ, vui lòng liên hệ Admin để xử lý.",
                        'status' => 0
                    ],200);
                }
            }
            catch(\Exception $e){
                DB::rollBack();
                Log::error($e);
                $charge->status = -999;
                $charge->response_code = -1;
                $charge->response_mess = "Lỗi hệ thống";
                $charge->save();
                return response()->json([
                    'message' => __('Có lỗi phát sinh trong quá trình xử lý dữ liệu gạch thẻ, vui lòng báo QTV để kịp thời xử lý.'),
                    'status' => 0,
                ], 500);
            }
        }catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0,
            ], 500);
        }
    }
}
