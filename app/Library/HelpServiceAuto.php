<?php

namespace App\Library;


use Carbon\Carbon;


class HelpServiceAuto
{

    public static function CheckAmountSyntax9029($code){

        $syntaxs=['DK','NAP'];
        $amounts=[1000,500,300,200,100,50,30,20,10];

        $amountReturn=0;
        foreach($syntaxs as $syntax){
            foreach($amounts as $amount){
                $strFull=$syntax.$amount;
                if(strpos($code,$strFull)>-1){
                    $amountReturn=$amount;
                    return $amountReturn*1000;
                }
            }
        }
        return $amountReturn*1000;
    }

    public static function NAPTIENDIENTHOAI($numberphone,$amount,$telecom,$request_id){

        $resultChange = new \stdClass();
        try {

            $secretkey="5e25bb01cedaf";
            $url="";
            if($telecom==0){
                //api bắn tiền viettel trả trước 136
                $url="http://vt-tt-612.tichhop.net:8080/api/136";

            }
            elseif($telecom==1){
                //api bắn tiền vinaphone trả trước 999
                $url="http://vn-tt-612.tichhop.net:8080/api/999";
            }


            //check điều kiện loại nhà mạng cần bắn

            $data = array();
            $data['secret_token'] = $secretkey;
            $data['tranid'] = $request_id;
            $data['code'] = $numberphone;
            $data['amount'] = $amount ;//nhỏ nhất 5000 lớn nhất 300000
            $data['callback '] = "http://s-api.".\Request::getHost()."/api/services-auto-callback/". strtolower("naptiendienthoai")."?tranid=".$request_id."&sign=3857gjfhnj51";;
            //$data['callback'] = "http://daily.dichvu.me/api/services-auto-callback/". strtolower("naptiendienthoai")."?tranid=".$request_id."&sign=3857gjfhnj51";;
            $dataPost = http_build_query($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            $resultRaw = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            //lưu log gọi curl
            $myfile = fopen(storage_path() ."/logs/services-auto-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
            $txt = Carbon::now()." :".$url." [" .$httpcode."] - [".http_build_query($data) ."] : ".$resultRaw;
            fwrite($myfile, $txt ."\n");
            fclose($myfile);


            $result = json_decode($resultRaw);
            $resultChange = new \stdClass();
            if($result && isset($result->status)){

                if($result->status==2){
                    $resultChange->status=2; //Chờ nạp (đợi callback)
                    $resultChange->response_code =$result->status;
                    $resultChange->message=$result->message;
                }
                elseif($result->status==10 ){
                    $resultChange->status=10; //Bảo trì DV
                    $resultChange->response_code =$result->status;
                    $resultChange->message=$result->message;
                }
                elseif($result->status==3 ){
                    $resultChange->status=3; //Thất bại
                    $resultChange->response_code =$result->status;
                    $resultChange->message=$result->message;
                }
            }
            else{
                return null;
            }
            return $resultChange;

        }
        catch (\Exception $e){

            $resultChange->status=500;
            $resultChange->data="";
            $resultChange->message="Lỗi logic xử lý";
            $resultChange->response_code="500";
            $resultChange->response_message="Lỗi logic xử lý";
            \Log::error($e);
            return $resultChange;
        }
    }
    public static function NAPSMS9029($code,$amount,$telecom){

        $resultChange = new \stdClass();
        try {
            $tranid = time() . rand(10000, 99999);  /// Cái này có thể mà mã order của bạn, nó là duy nhất (enique) để phân biệt giao dịch.
            $secretkey="5e25bb01cedaf";
            $url="";
            if($telecom==0){
                //api bắn tiền viettel trả trước 136
                $url="http://vt-tt-612.tichhop.net:8080/api/9029";

            }
            elseif($telecom==1){
                //api bắn tiền vinaphone trả trước 999
                $url="http://mb-tt-612.tichhop.net:8080/api/9029";
            }

            //check điều kiện loại nhà mạng cần bắn

            $data = array();
            $data['secret_token'] = $secretkey;
            $data['tranid'] = $tranid;
            $data['code'] = $code;
            $data['amount'] = $amount ;//nhỏ nhất 5000 lớn nhất 300000
            //$data['callback '] = "http://s-api.".\Request::getHost()."/api/services-auto-callback/". strtolower("napsms9029")."?id=".$tranid."&sign=3857gjfhnj51";;
            $data['callback '] = "http://s-api.".\Request::getHost()."/api/services-auto-callback/". strtolower("napsms9029")."?tranid=".$tranid."&sign=3857gjfhnj51";;

            $dataPost = http_build_query($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            $resultRaw = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);


            //lưu log gọi curl
            $myfile = fopen(storage_path() ."/logs/services-auto-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
            $txt = Carbon::now()." :".$url." [" .$httpcode."] - [".http_build_query($data) ."] : ".$resultRaw;
            fwrite($myfile, $txt ."\n");
            fclose($myfile);


            $result = json_decode($resultRaw);
            if($result && isset($result->status)){

                if($result->status==2){
                    $resultChange->status=2; //Chờ nạp (đợi callback)
                    $resultChange->tranid = $tranid;
                    $resultChange->response_code =$result->status;
                    $resultChange->message=$result->message;
                }
                elseif($result->status==10 ){
                    $resultChange->status=10; //Bảo trì DV
                    $resultChange->tranid = $tranid;
                    $resultChange->response_code =$result->status;
                    $resultChange->message=$result->message;
                }
                elseif($result->status==3 ){
                    $resultChange->status=3; //Thất bại
                    $resultChange->tranid = $tranid;
                    $resultChange->response_code =$result->status;
                    $resultChange->message=$result->message;
                }
            }
            else{
                return null;
            }
            return $resultChange;

        }
        catch (\Exception $e){

            $resultChange->status=500;
            $resultChange->data="";
            $resultChange->message="Lỗi logic xử lý";
            $resultChange->response_code="500";
            $resultChange->response_message="Lỗi logic xử lý";
            \Log::error($e);
            return $resultChange;
        }

    }



}
