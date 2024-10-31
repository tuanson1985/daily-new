<?php
namespace App\Library\ChargeGateway;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Library\Helpers;
Class PAYPAYPAY{
    public static function API($partner_id,$partner_key,$card_type,$card_code,$card_serial,$amount,$request_id,$domain){
        try{
            $url = config('charge.paypaypay.url');
            $card_type = self::convertCardType($card_type);
            if($card_type === null){
                return "CARD_TYPE_NOT_WORKING";
            }
            $telco = $card_type;
            $code =  $card_code;
            $serial =$card_serial;
            // tạo chữ kí
            $sign = md5($partner_id . $partner_key . $telco . $code . $serial . $amount . $request_id);
            $data = array();
            $data['partner_id'] = $partner_id;
            $data['type'] = $telco;
            $data['pin'] = $code;
            $data['serial'] = $serial;
            $data['amount'] = $amount;
            $data['tranid'] = $request_id;
            $data['request_id'] = $request_id;
            $data['sign'] = $sign;
            if(is_array($data)){
                $dataPost = http_build_query($data);
            }else{
                $dataPost = $data;
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
            $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            curl_setopt($ch, CURLOPT_REFERER, $actual_link);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            $resultRaw = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $result = json_decode($resultRaw);
            $resultChange = new \stdClass();
            if ($result && isset($result->status)) {
                $path = storage_path() ."/logs/charge_ppp/";
                if(!\File::exists($path)){
                    \File::makeDirectory($path, $mode = "0755", true, true);
                }
                $txt = Carbon::now()." : [ tranid:" .$request_id."] : [ telecom:" .$card_type."] : [ pin:" .$card_code."] : [ serial:" .$card_serial."] : [ amount:" .$amount."] : ".json_encode($result,JSON_UNESCAPED_UNICODE);
                \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");
                $response_code=$result->status;
                $message=$result->message;
                $tranid=isset($result->tranid) ? $result->tranid : null;
                $amount=isset($result->amount) ? $result->amount : 0;
                if ($response_code==2) {
                    $resultChange->status=2; // chờ xử lý /thẻ trễ
                    $resultChange->response_code =$response_code;
                    $resultChange->message = $message;
                    $resultChange->tranid = $tranid;
                    $resultChange->amount=$amount;
                }
                else if($response_code==77) {
                    $resultChange->status=77; // Bị block nạp thẻ
                    $resultChange->response_code =$response_code;
                    $resultChange->message = $message;
                    $resultChange->tranid = $tranid;
                    $resultChange->amount=$amount;
                }
                else{
                    if($response_code == 996){
                        $text_tele = "API NẠP THẺ PPP: ".$domain.' - '.$message;
                        Helpers::TelegramNotify($text_tele,config('telegram.bots.mybot.channel_notify_ncc'));
                    }
                    $resultChange->status = 0;// Thẻ sai
                    $resultChange->response_code =$response_code;
                    $resultChange->message = $message;
                    $resultChange->tranid = $tranid;
                }
                return $resultChange;
            }
            else{
                //debug thì mở cái này
                $path = storage_path() ."/logs/charge_ppp/";
                if(!\File::exists($path)){
                    \File::makeDirectory($path, $mode = "0755", true, true);
                }
                $txt = Carbon::now()." : [" .$httpcode."] : [ tranid:" .$request_id."] : [ telecom:" .$card_type."] : [ pin:" .$card_code."] : [ serial:" .$card_serial."] : [ amount:" .$amount."] : ".$resultRaw;
                \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");
                return null;
            }
        }catch(\Exception $e){
            Log::error($e);
            return "ERROR";
        }
    }
    public static function convertCardType($card_type){
        if (strtoupper($card_type) == 'VIETTEL') {
            $card_type = 'VIETTEL'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'MOBIFONE') {
            $card_type = 'MOBIFONE'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'VINAPHONE') {
            $card_type = 'VINAPHONE'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'GATE') {
            $card_type = 'GATE'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'ZING') {
            $card_type = 'ZING'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'SCOIN') {
            $card_type = 'SCOIN'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'VCOIN') {
            $card_type = 'VCOIN'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'BIT') {
            $card_type = 'BIT'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'VIETNAMMOBILE') {
            $card_type = 'VIETNAMMOBILE'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'GARENA') {
            $card_type = 'GARENA'; //set theo code cổng nạp tích hợp
        }
        else{
            $card_type = null;
        }
        return $card_type;
    }
    public static function createUser($username,$password){
        try{
            $url = config('charge.paypaypay.url_create_user');
            $data = array();
            $data['username'] = $username;
            $data['email'] = null;
            $data['phone'] = null;
            $data['password'] = $password;
            $data['sign'] = config('module.charge.key_sign');
            if(is_array($data)){
                $dataPost = http_build_query($data);
            }else{
                $dataPost = $data;
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
            $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            curl_setopt($ch, CURLOPT_REFERER, $actual_link);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            $resultRaw = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $result = json_decode($resultRaw);
            $myfile = fopen(storage_path() ."/logs/log-create_user_ppp-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().' - '.$username.' - '.$resultRaw;
            fwrite($myfile, $txt ."\n");
            fclose($myfile);
            $resultChange = new \stdClass();
            if(isset($result) && isset($result->status)){
                if($result->status == 1){
                    $resultChange->status = 1;
                    $resultChange->message = $result->message;
                    $resultChange->username = $result->data->username;
                    $resultChange->partner_key = $result->data->partner_key;
                    $resultChange->partner_key_card = $result->data->partner_key_card;
                    $resultChange->id = $result->data->id;
                }
                else{
                    $resultChange->status = 0;
                    $resultChange->message = $result->message;
                }
            }
            else{
                return "ERROR";
            }
            return $resultChange;
        }catch(\Exception $e){
            Log::error($e);
            return "ERROR";
        }
    }
}