<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Library\HelpChangeGate;
use App\Models\Charge;
use App\Models\Telecom;
use App\Models\TelecomValue;
use App\Models\TelecomValueAgency;
use App\Models\Txns;
use App\Models\User;
use Auth;
use Cache;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Log;
use Validator;


class ListenCallbackController extends Controller
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
        $myfile = fopen(storage_path() . "/logs/callback-to-ntn-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
        $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
        fwrite($myfile, $txt . "\n");
        fclose($myfile);

        if (strtolower($request->site) == strtolower("napthenhanh")) {

            return $this->callback_Napthenhanh($request);

        }
        elseif (strtolower($request->site) == strtolower("cancaucom")) {


            return $this->callback_CanCauCom($request);
        }


    }
    public function callback_Napthenhanh(Request $request){

        DB::beginTransaction();

        try {

            //debug thì mở cái này
            $myfile = fopen(storage_path() ."/logs/log_callback.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().":".$request->fullUrl().json_encode($request->all());
            fwrite($myfile, $txt ."\n");
            fclose($myfile);

            //kiểu nạp auto
            $type_charge = 0;

            include_once(app_path() . '/GatewayCharging/napthenhanh_com_withcallback/config.php');
            $callback_sign = md5($partner_key.$request->tranid.$request->pin.$request->serial);

            if($request->callback_sign!=$callback_sign){
                //return;
                return "Dữ liệu gửi lên không đúng";
            }

            //check nếu thẻ đó đã gạch rồi
            $chargeCheck=Charge::where('tranid',$request->tranid)
                ->where('pin',$request->pin)
                ->where('serial',$request->serial)
                ->where(function($q){
                    $q->orWhere('status',1);
                    $q->orWhere('status',3);
                })->first();

            if($chargeCheck){
                return "Thẻ đã bị gạch trước đó";
            }

            //tìm thẻ nạp
            $charge=Charge::where('tranid',$request->tranid)
                ->where('pin',$request->pin)
                ->where('status','!=',1)
                ->where('status','!=',3)
                ->lockForUpdate()
                ->firstOrFail();


            //tìm user nạp
            $userTransaction=User::where('username',$charge->username)->lockForUpdate()->firstOrFail();


            if($request->get('status')==1){

                //lấy chiết khấu nhà mạng
                $telecom=Telecom::where('type_charge',0)
                    ->where('gate_id',$charge->gate_id)
                    ->where('key',$charge->telecom_key)
                    ->first();
                if(!$telecom){
                    return;
                    return 'Mệnh giá bạn chọn không tìm thấy hoặc bị khóa bởi Admin';
                }

                //ratio
                $telecom_value = TelecomValue::where('telecom_id', $telecom->id)
                    ->where('amount', $request->amount)->first();


                if(!$telecom_value){
                    //return ;
                    return 'Mệnh giá bạn chọn không tìm thấy hoặc bị khóa bởi Admin';
                }
                //set trạng thái thẻ đúng
                $charge->amount=$request->amount;
                $charge->response_mess= 'Nạp thành công thẻ ' . $charge->telecom_key . ' mệnh giá ' . number_format($charge->amount) . ' đ';
                $charge->status=1;

                if ($userTransaction->is_agency_charge == 1) {
                    $telecom_value_agency = TelecomValueAgency::where('telecom_id', $telecom->id)
                        ->where('username', $userTransaction->username)
                        ->where('amount', $request->amount)->first();

                    if ($telecom_value_agency) {
                        $ratio = $telecom_value_agency->ratio;
                    }
                    else {
                        $ratio = $telecom_value->agency_ratio_true_amount;
                    }

                }
                else {
                    $ratio = $telecom_value->ratio_true_amount;
                }
                //tính tiền thực nhận
                $real_received_amount=($ratio*$request->amount)/100;

                //cộng tiền cho user
                if ($real_received_amount < 0) {
                    //return ;
                    //return 'Số tiền thanh toán không hợp lệ';
                }
                $userTransaction->balance=$userTransaction->balance+$real_received_amount;
                $userTransaction->save();

                //lưu thông tin nạp thẻ
                $charge->ratio=$ratio;
                $charge->real_received_amount=$real_received_amount;
                $charge->money_received=$request->money_received;
                $charge->response_code= $request->get('status');
                $charge->response_mess= $request->message;
                $charge->description="[API xử lý]";
                $charge->process_at=Carbon::now();
                $charge->save();
                //tạo tnxs
                $txns=Txns::create([

                    'trade_type' => '1',//Nạp thẻ tự động
                    'is_add'=>'1',//Công tiền
                    'username'=>$userTransaction->username,
                    'amount'=>$telecom_value->amount,
                    'ratio'=>$ratio,
                    'real_received_amount'=>$real_received_amount,
                    'profit'=>$telecom_value->amount-$real_received_amount,
                    'last_balance'=>$userTransaction->balance,
                    'description'=>$request->description,
                    'ip'=>$request->getClientIp(),
                    'processor_username'=>'',
                    'ref_id'=>$charge->id,
                    'status'=>1
                ]);
                //cập nhật txns_id cho charge
                $charge->txns_id=$txns->id;
                $charge->save();




            }
            elseif ($request->get('status') == 3) {
                //lấy chiết khấu nhà mạng
                $telecom=Telecom::where('type_charge',0)
                    ->where('gate_id',$charge->gate_id)
                    ->where('key',$charge->telecom_key)
                    ->first();
                if(!$telecom){
                    //return;
                    return 'Mệnh giá bạn chọn không tìm thấy hoặc bị khóa bởi Admin';
                }

                //ratio
                $telecom_value = TelecomValue::where('telecom_id', $telecom->id)
                    ->where('amount', $request->amount)->first();


                if(!$telecom_value){
                    //return ;
                    return 'Mệnh giá bạn chọn không tìm thấy hoặc bị khóa bởi Admin';
                }

                //set trạng thái thẻ sai mệnh giá
                $charge->status=3;
                $charge->response_mess="Nạp thẻ sai mệnh giá";
                $charge->amount=$request->amount;

                if ($userTransaction->is_agency_charge == 1) {

                    $ratio = $telecom_value->agency_ratio_false_amount;
                }
                else {
                    $ratio = $telecom_value->ratio_false_amount;
                }


                //tính tiền thực nhận
                $real_received_amount=($ratio*$request->amount)/100;

                //cộng tiền cho user
                if ($real_received_amount < 0) {
                    //return;
                    return 'Số tiền thanh toán không hợp lệ';
                }
                $userTransaction->balance=$userTransaction->balance+$real_received_amount;
                $userTransaction->save();

                //lưu thông tin nạp thẻ
                $charge->ratio=$ratio;
                $charge->real_received_amount=$real_received_amount;
                $charge->money_received=$request->money_received;
                $charge->response_code= $request->get('status');
                $charge->response_mess= $request->message;
                $charge->description="[API xử lý]";
                $charge->process_at=Carbon::now();
                $charge->save();

                //tạo tnxs
                $txns=Txns::create([

                    'trade_type' => '1',//Nạp thẻ tự động
                    'is_add'=>'1',//Công tiền
                    'username'=>$userTransaction->username,
                    'amount'=>$telecom_value->amount,
                    'ratio'=>$ratio,
                    'real_received_amount'=>$real_received_amount,
                    'profit'=>$telecom_value->amount-$real_received_amount,
                    'last_balance'=>$userTransaction->balance,
                    'description'=>$request->description,
                    'ip'=>$request->getClientIp(),
                    'processor_username'=>'',
                    'ref_id'=>$charge->id,
                    'status'=>1
                ]);
                //cập nhật txns_id cho charge
                $charge->txns_id=$txns->id;
                $charge->save();

            }
            if($request->get('status')==0){
                //set trạng thái thẻ sai

                $charge->response_code= $request->get('status');
                $charge->response_mess= $request->message;
                $charge->description="[API xử lý]";
                $charge->amount=0;
                $charge->status=0;
                $charge->save();
            }


        }catch(\Exception $e)
        {
            DB::rollback();
            Log::error($e);
            return 'Có lỗi phát sinh.Xin vui lòng thử lại !';


        }
        // Commit the queries!
        DB::commit();
        //return;
        return 'Xử lý giao dịch thành công #'.$charge->id;




    }
    public function callback_CanCauCom(Request $request){




        DB::beginTransaction();

        try {




            //kiểu nạp auto
            $type_charge = 0;

            include_once(app_path() . '/GatewayCharging/cancaucom_withcallback/config.php');
            $callback_sign = md5($partner_key.$request->tranid.$request->pin.$request->serial);




            if($request->callback_sign!=$callback_sign){
                //return;
                return "Dữ liệu gửi lên không đúng";
            }

            //check nếu thẻ đó đã gạch rồi
            $chargeCheck=Charge::where('tranid',$request->tranid)
                ->where('pin',$request->pin)
                ->where('serial',$request->serial)
                ->where(function($q){
                    $q->orWhere('status',1);
                    $q->orWhere('status',3);
                })->first();

            if($chargeCheck){
                return "Thẻ đã bị gạch trước đó";
            }

            //tìm thẻ nạp
            $charge=Charge::where('tranid',$request->tranid)
                ->where('pin',$request->pin)
                ->where('status','!=',1)
                ->where('status','!=',3)
                ->lockForUpdate()
                ->firstOrFail();


            //tìm user nạp
            $userTransaction=User::where('username',$charge->username)->lockForUpdate()->firstOrFail();


            if($request->get('status')==1){

                //lấy chiết khấu nhà mạng
                $telecom=Telecom::where('type_charge',0)
                    ->where('gate_id',$charge->gate_id)
                    ->where('key',$charge->telecom_key)
                    ->first();
                if(!$telecom){
                    return;
                    return 'Mệnh giá bạn chọn không tìm thấy hoặc bị khóa bởi Admin';
                }

                //ratio
                $telecom_value = TelecomValue::where('telecom_id', $telecom->id)
                    ->where('amount', $request->amount)->first();


                if(!$telecom_value){
                    //return ;
                    return 'Mệnh giá bạn chọn không tìm thấy hoặc bị khóa bởi Admin';
                }
                //set trạng thái thẻ đúng
                $charge->amount=$request->amount;
                $charge->response_mess= 'Nạp thành công thẻ ' . $charge->telecom_key . ' mệnh giá ' . number_format($charge->amount) . ' đ';
                $charge->status=1;

                if ($userTransaction->is_agency_charge == 1) {
                    $telecom_value_agency = TelecomValueAgency::where('telecom_id', $telecom->id)
                        ->where('username', $userTransaction->username)
                        ->where('amount', $request->amount)->first();

                    if ($telecom_value_agency) {
                        $ratio = $telecom_value_agency->ratio;
                    }
                    else {
                        $ratio = $telecom_value->agency_ratio_true_amount;
                    }

                }
                else {
                    $ratio = $telecom_value->ratio_true_amount;
                }
                //tính tiền thực nhận
                $real_received_amount=($ratio*$request->amount)/100;

                //cộng tiền cho user
                if ($real_received_amount < 0) {
                    //return ;
                    //return 'Số tiền thanh toán không hợp lệ';
                }

                Cache::delete('fail_charge_' . $userTransaction->username);


                $userTransaction->balance=$userTransaction->balance+$real_received_amount;
                $userTransaction->save();

                //lưu thông tin nạp thẻ
                $charge->ratio=$ratio;
                $charge->real_received_amount=$real_received_amount;
                $charge->money_received=$request->money_received;
                $charge->response_code= $request->get('status');
                $charge->response_mess= $request->message;
                $charge->description="[API xử lý]";
                $charge->process_at=Carbon::now();
                $charge->save();
                //tạo tnxs
                $txns=Txns::create([

                    'trade_type' => '1',//Nạp thẻ tự động
                    'is_add'=>'1',//Công tiền
                    'username'=>$userTransaction->username,
                    'amount'=>$telecom_value->amount,
                    'ratio'=>$ratio,
                    'real_received_amount'=>$real_received_amount,
                    'profit'=>$telecom_value->amount-$real_received_amount,
                    'last_balance'=>$userTransaction->balance,
                    'description'=>$request->description,
                    'ip'=>$request->getClientIp(),
                    'processor_username'=>'',
                    'ref_id'=>$charge->id,
                    'status'=>1
                ]);
                //cập nhật txns_id cho charge
                $charge->txns_id=$txns->id;
                $charge->save();



            }
            elseif ($request->get('status') == 3) {
                //Set limit
                $fail_charge = Cache::get('fail_charge_' . $userTransaction->username);
                $fail_charge = $fail_charge + 1;
                Cache::put('fail_charge_' . $userTransaction->username, $fail_charge, 15);
                //lấy chiết khấu nhà mạng
                $telecom=Telecom::where('type_charge',0)
                    ->where('gate_id',$charge->gate_id)
                    ->where('key',$charge->telecom_key)
                    ->first();
                if(!$telecom){
                    //return;
                    return 'Mệnh giá bạn chọn không tìm thấy hoặc bị khóa bởi Admin';
                }

                //ratio
                $telecom_value = TelecomValue::where('telecom_id', $telecom->id)
                    ->where('amount', $request->amount)->first();


                if(!$telecom_value){
                    //return ;
                    return 'Mệnh giá bạn chọn không tìm thấy hoặc bị khóa bởi Admin';
                }

                //set trạng thái thẻ sai mệnh giá
                $charge->status=3;
                $charge->response_mess="Nạp thẻ sai mệnh giá";
                $charge->amount=$request->amount;

                if ($userTransaction->is_agency_charge == 1) {

                    $ratio = $telecom_value->agency_ratio_false_amount;
                }
                else {
                    $ratio = $telecom_value->ratio_false_amount;
                }


                //tính tiền thực nhận
                $real_received_amount=($ratio*$request->amount)/100;

                //cộng tiền cho user
                if ($real_received_amount < 0) {
                    //return;
                    return 'Số tiền thanh toán không hợp lệ';
                }
                $userTransaction->balance=$userTransaction->balance+$real_received_amount;
                $userTransaction->save();

                //lưu thông tin nạp thẻ
                $charge->ratio=$ratio;
                $charge->real_received_amount=$real_received_amount;
                $charge->money_received=$request->money_received;
                $charge->response_code= $request->get('status');
                $charge->response_mess= $request->message;
                $charge->description="[API xử lý]";
                $charge->process_at=Carbon::now();
                $charge->save();

                //tạo tnxs
                $txns=Txns::create([

                    'trade_type' => '1',//Nạp thẻ tự động
                    'is_add'=>'1',//Công tiền
                    'username'=>$userTransaction->username,
                    'amount'=>$telecom_value->amount,
                    'ratio'=>$ratio,
                    'real_received_amount'=>$real_received_amount,
                    'profit'=>$telecom_value->amount-$real_received_amount,
                    'last_balance'=>$userTransaction->balance,
                    'description'=>$request->description,
                    'ip'=>$request->getClientIp(),
                    'processor_username'=>'',
                    'ref_id'=>$charge->id,
                    'status'=>1
                ]);
                //cập nhật txns_id cho charge
                $charge->txns_id=$txns->id;
                $charge->save();

            }
            if($request->get('status')==0){
                //Set limit
                $fail_charge = Cache::get('fail_charge_' . $userTransaction->username);
                $fail_charge = $fail_charge + 1;
                Cache::put('fail_charge_' . $userTransaction->username, $fail_charge, 15);
                //set trạng thái thẻ sai

                $charge->response_code= $request->get('status');
                $charge->response_mess= $request->message;
                $charge->description="[API xử lý]";
                $charge->amount=0;
                $charge->status=0;
                $charge->save();
            }


        }catch(\Exception $e)
        {
            DB::rollback();
            Log::error($e);
            return 'Có lỗi phát sinh.Xin vui lòng thử lại !';


        }
        // Commit the queries!
        DB::commit();
        //return;
        return 'Xử lý giao dịch thành công #'.$charge->id;

    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

}
