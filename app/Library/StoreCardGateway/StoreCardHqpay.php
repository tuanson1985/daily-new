<?php
namespace App\Library\StoreCardGateway;
use Carbon\Carbon;
use App\Models\Setting;
use Html;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11/18/2016
 * Time: 3:14 PM
 */
class StoreCardHqpay
{
    public static function convertTelecom($card_type){
        if (strtoupper($card_type) == 'VIETTEL') {
            $telecom = 'VIETTEL'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'MOBIFONE') {
            $telecom = 'MOBIFONE'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'VINAPHONE') {
            $telecom = 'VINAPHONE'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'GATE') {
            $telecom = 'GATE'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'GARENA') {
            $telecom = 'GARENA'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'ZING') {
            $telecom = 'ZING'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'SCOIN') {
            $telecom = 'SCOIN'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'APPOTA') {
            $telecom = 'APPOTA'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'VCOIN') {
            $telecom = 'VCOIN'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'FUNCARD') {
            $telecom = 'FUNCARD'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'SOHACOIN') {
            $telecom = 'SOHACOIN'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'BIT') {
            $telecom = 'BIT'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'VIETNAMMOBILE') {
            $telecom = 'VIETNAMOBILE'; //set theo code cổng nạp tích hợp
        }
        else{
            $telecom = false;
        }
        return $telecom;
    }

    public static function convertService($telecom){
        if($telecom == "VIETTEL" || $telecom == "VINAPHONE" || $telecom == "MOBIFONE" || $telecom == "VIETNAMOBILE"){
            $service_id = 1001;
        }
        else{
            $service_id = 1004;
        }
        return $service_id;
    }

    public static function decrypt($encrypt){
        $secret_key = config('hqpay.hash_id');
        $output = "";
        $encrypt_method = "AES-256-CBC";
        $secret_iv = 'hash';
        $key = hash('sha256', $secret_key);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $output = openssl_decrypt(base64_decode($encrypt), $encrypt_method, $key, 0, $iv);
        if($output==false){
            return "";
        }
        return $output;
    }



    public static function BuyCard($telecom,$amount,$quantity,$request_id){
        $url =  config('hqpay.url');
        $hash_id =  config('hqpay.hash_id');
        $partner_key =  config('hqpay.partner_key');

        $telecom = self::convertTelecom($telecom);
        if($telecom === false){
            return "WRONG_GATEWAY";
        }
        $service_id = self::convertService($telecom);
        $data = array();
        $data['service_id'] = $service_id;
        $data['telecom_key'] = $telecom;
        $data['amount'] = $amount;
        $data['quantity'] = $quantity;
        $data['request_id'] = $request_id;
        $data['hash_id'] = $hash_id;
        $data['sign'] = md5($hash_id.$partner_key.$service_id.$request_id);
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
        if($result && $result != null && isset($result->status)){
            if($result->status == 1){
                $data_card = null;
                if($result->data_card){
                    foreach($result->data_card as $item){
                        $pin= self::decrypt($item->pin);
                        $serial = $item->serial;
                        $expiryDate = $item->expiryDate;

                        $data_card[] = [
                            'telecom_key' => $telecom,
                            'pin' => $pin,
                            'serial' => $serial,
                            'amount' => $amount,
                            'expiryDate' => $expiryDate
                        ];
                    }
                    $data_card = json_decode(json_encode($data_card), FALSE);
                }
                $resultChange->status = 1;
                $resultChange->message = $result->message;
                $resultChange->request_id = $request_id;
                $resultChange->amount = $amount;
                $resultChange->data_card = $data_card;
            }
            else if($result->status == 2){
                $resultChange->status = 2;
                $resultChange->message = $result->message;
                $resultChange->request_id = $request_id;
                $resultChange->amount = $amount;
            }
            else{
                $resultChange->status = 0;
                $resultChange->message = $result->message;
                $resultChange->request_id = $request_id;
                $resultChange->amount = $amount;
            }
            $path = storage_path() ."/logs/store_card_hqpay/";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $txt = Carbon::now()." : [id: ".$request_id."] : [code: ".$result->status."] : [message: ".$result->message."] : [url: ".json_encode($result,JSON_UNESCAPED_UNICODE)."]";
            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");
            return $resultChange;
        }
        else{

            $path = storage_path() ."/logs/store_card_hqpay/";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $txt = Carbon::now()." :".$url.'<-->'. " - " .$amount. " - [" .$quantity .']'. " - " .$request_id;
            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");
            return null;
        }
    }

}