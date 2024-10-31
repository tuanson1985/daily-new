<?php 
namespace App\Library\StoreCardGateway;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Library\Helpers;

Class StoreCardNapTheNhanh
{
    public static function API($partner_id,$partner_key,$card_type,$amount,$quantity,$request_id,$domain){
        try{
            $url = config('store_card.napthenhanh.url');
            $telco = self::convertCardType($card_type);
            // Tạo chữ ký
            $sign = md5($partner_id . $partner_key . $telco . $amount . $quantity . $request_id);
    
            $data = array();
            $data['partner_id'] = $partner_id;
            $data['partner_key_card'] = $partner_key;
            $data['quantity'] = $quantity;
            $data['type'] = $telco;
            $data['amount'] = $amount;
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
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            $resultRaw = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $httpcode1 = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);
            $result = json_decode($resultRaw);
            $resultChange = new \stdClass();
            if ($result && isset($result->status)) {
                $response_code=$result->status;
                $message=$result->message;
                $data_card = [];
                if(isset($result->data_card) && $result->data_card != null){
                    foreach($result->data_card as $item){
                        $data_card[] = [
                            'pin' => $item->pin,
                            'serial' => $item->serial,
                            'amount' => $amount,
                            'telecom_key' => $card_type,
                            // 'expiryDate' => $item->expired_at
                        ];
                    }
                    $data_card = json_decode(json_encode($data_card), FALSE);
                }
                if ($response_code == 1) {
                    $resultChange->status = 1; //thành công
                    $resultChange->response_code =$response_code;
                    $resultChange->message = $message;
                    $resultChange->request_id = $request_id;
                    $resultChange->total_price = $result->total_price??null;
                    $resultChange->data_card =$data_card;
                }
                elseif($response_code == 2){
                    $resultChange->status = 2; // Đang chờ
                    $resultChange->response_code =$response_code;
                    $resultChange->message = $message;
                    $resultChange->request_id = $request_id;
                    $resultChange->data_card=null;
                }
                elseif($response_code == 995){
                    $text_tele = "API MUA THẺ NTN: ".$domain.' - '.$message;
                    Helpers::TelegramNotify($text_tele,config('telegram.bots.mybot.channel_notify_ncc'));
                    $resultChange->status = 2; // Đang chờ
                    $resultChange->response_code =$response_code;
                    $resultChange->message = $message;
                    $resultChange->request_id = $request_id;
                    $resultChange->data_card=null;
                }
                else {
                    $resultChange->status = 0;// Thất bại
                    $resultChange->response_code =$response_code;
                    $resultChange->message = $message;
                    $resultChange->request_id = $request_id;
                    $resultChange->data_card=null;
                }
                
                $path = storage_path() ."/logs/store_card_ntn/";
                if(!\File::exists($path)){
                    \File::makeDirectory($path, $mode = "0755", true, true);
                }
                $txt = Carbon::now()." : [" .$httpcode."] : [request_id: ".$request_id."] : [code: ".$result->status."] : [message: ".$result->message."] : ".$resultRaw;
                \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");
                return $resultChange;
            }
            else {
                //debug thì mở cái này
                $path = storage_path() ."/logs/store_card_ntn/";
                if(!\File::exists($path)){
                    \File::makeDirectory($path, $mode = "0755", true, true);
                }
                $txt = Carbon::now()." :".$url.'<-->'.$httpcode1." [" .$httpcode."] - ".$card_type. " - " .$amount. " - [" .$quantity .']'. " - " .$request_id." : ".$resultRaw;
                \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");
                return null;
            }
        }catch(\Exception $e){
            //debug thì mở cái này
            $path = storage_path() ."/logs/store_card_ntn/";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $txt = Carbon::now()." :".$url.'<-->'.$httpcode1." [" .$httpcode."] - ".$card_type. " - " .$amount. " - [" .$quantity .']'. " - " .$request_id." : ".$resultRaw;
            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");
            Log::error($e);
            return null;
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
        elseif(strtoupper($card_type) == 'GARENA') {
            $card_type = 'GARENA'; //set theo code cổng nạp tích hợp
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
        elseif(strtoupper($card_type) == 'GOSU') {
            $card_type = 'GOSU'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'APPOTA') {
            $card_type = 'APPOTA'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'FUNCARD') {
            $card_type = 'FUNCARD'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'SOHACOIN') {
            $card_type = 'SOHACOIN'; //set theo code cổng nạp tích hợp
        }
        elseif(strtoupper($card_type) == 'CAROT') {
            $card_type = 'CAROT'; //set theo code cổng nạp tích hợp
        }
        else{
            $card_type = null;
        }
        return $card_type;
    }
    public static function detailOrder($partner_id,$partner_key,$request_id,$domain){
        try{
            $url = config('store_card.napthenhanh.url_detail_order');
            $sign = md5($partner_id . $partner_key . $request_id);
            $data = array();
            $data['partner_id'] = $partner_id;
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
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            $resultRaw = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $httpcode1 = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);
            $result = json_decode($resultRaw);
            $resultChange = new \stdClass();
            if ($result && isset($result->status)) {
                $response_code=$result->status;
                $message=$result->message;
                $data_card = [];
                if(isset($result->data_card) && $result->data_card != null){
                    foreach($result->data_card as $item){
                        $data_card[] = [
                            'pin' => $item->pin,
                            'serial' => $item->serial,
                            'amount' => $item->amount,
                            'telecom_key' => $item->telecom_key,
                        ];
                    }
                    $data_card = json_decode(json_encode($data_card), FALSE);
                }
                if ($response_code == 1) {
                    $resultChange->status = 1; //thành công
                    $resultChange->response_code =$response_code;
                    $resultChange->message = $message;
                    $resultChange->request_id = $request_id;
                    $resultChange->total_price = $result->total_price??null;
                    $resultChange->data_card =$data_card;
                }
                elseif($response_code == 2){
                    $resultChange->status = 2; // Đang chờ
                    $resultChange->response_code =$response_code;
                    $resultChange->message = $message;
                    $resultChange->request_id = $request_id;
                    $resultChange->data_card=null;
                }
                elseif($response_code == 0 || $response_code == 77) {
                    $resultChange->status = 0;// Thất bại
                    $resultChange->response_code =$response_code;
                    $resultChange->message = $message;
                    $resultChange->request_id = $request_id;
                    $resultChange->data_card=null;
                }
                else{
                    $resultChange->status = 2; // Đang chờ
                    $resultChange->response_code =$response_code;
                    $resultChange->message = $message;
                    $resultChange->request_id = $request_id;
                    $resultChange->data_card=null;
                }
                $path = storage_path() ."/logs/store_card_ntn/";
                if(!\File::exists($path)){
                    \File::makeDirectory($path, $mode = "0755", true, true);
                }
                $txt = Carbon::now()." : [" .$httpcode."] : [request_id: ".$request_id."] : [code: ".$result->status."] : [message: ".$result->message."] : ".$resultRaw;
                \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");
                return $resultChange;
            }
            else {
                //debug thì mở cái này
                $path = storage_path() ."/logs/store_card_ntn/";
                if(!\File::exists($path)){
                    \File::makeDirectory($path, $mode = "0755", true, true);
                }
                $txt = Carbon::now()." :".$url.'<-->'.$httpcode1." [" .$httpcode."] - " .$request_id." : ".$resultRaw;
                \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");
                return null;
            }
        }
        catch(\Exception $e){
            //debug thì mở cái này
            $path = storage_path() ."/logs/store_card_ntn/";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $txt = Carbon::now()." :".$url.'<-->'.$httpcode1." [" .$httpcode."] - " .$request_id." : ".$resultRaw;
            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");
            Log::error($e);
            return null;
        }
    }
}