<?php
namespace App\Library;

use Request;
use Carbon\Carbon;
use App\Models\Shop;
use Illuminate\Support\Facades\Log;

class HelperItemDaily
{


    public static function fire($partner_id,$partner_key,$id,$server,$customer_data0,$customer_data1,$amount,$service_id,$params,$request_id=null)
    {



        $resultChange = new \stdClass();
        try {
            $url = config('daily.url')."/api/agency-service";
            $user_id= $partner_id;
            $sign =$partner_key;

            $data = array();
            $data['tranid'] = $id;
            $data['user_id'] = $user_id;
            $data['sign'] = $sign;
            if($service_id =='nrogem' ){
                $data['service_id'] = 1802;
            } else if($service_id =='ninjaxu' ){
                $data['service_id'] = 1795;
            }else if($service_id == 'nrocoin'){
                $data['service_id'] = 1801;
            }else if($service_id == 'roblox_gem_pet'){
                $data['service_id'] = 1813;
            }
            else if($service_id == 'roblox_buyserver'){
                $data['service_id'] = 1811;
            }
            else if($service_id == 'roblox_buygamepass'){
                $data['service_id'] = 1812;
            }

            $data['server'] = $server;
            $data['customer_data0'] = $customer_data0;
            $data['customer_data1'] = $customer_data1;
            $data['amount'] = $amount;
            $data['request_id'] = $request_id;
            $callback = config('app.url_api')."/api/v1/services-auto-callback-daily?id=".$id."&sign=".config('daily.sign')."&request_id=".$request_id;


            $data['callback'] = $callback;
            if($params == "1"){
                $data['rut'] = $params;
            }

            if(is_array($data)){
                $dataPost = http_build_query($data);
            }else{
                $dataPost = $data;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url."?".$dataPost);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0);
            $resultRaw = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);
            $result = json_decode($resultRaw);

            //lưu log gọi curl
            $path = storage_path() ."/logs/services-auto/";
            $filename=$path."fire_daily_".Carbon::now()->format('Y-m-d').".txt";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }

            $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw;
            \File::append( $filename,$contentText."\n");

            if($result && isset($result->status)){
                if($result->status == 2){
                    $resultChange->status=2;
                    $resultChange->message=$result->message??"";
                    $resultChange->response_code=$result->status;
                    $resultChange->response_message=$result->message??"";
                }
                else{
                    $resultChange->status=0;
                    $resultChange->message=$result->message??"";
                    $resultChange->response_code=$result->status;
                    $resultChange->response_message=$result->message??"";
                }
                return $resultChange;
            }else{
                $resultChange->status=9;
                $resultChange->message="Kết nối với nhà cung cấp thất bại";
                return null;
            }

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




    public static function editInfo($partner_id,$partner_key,$id,$customer_data0,$service_id,$request_id=null){


        $url = config('daily.url') ."/api/agency-service/edit-info";
        $user_id= $partner_id;
        $sign =$partner_key;

        $data = array();
        $data['tranid'] = $id;
        $data['user_id'] = $user_id;
        $data['sign'] = $sign;

        if($service_id =='nrogem' ){
            $data['service_id'] = 1802;
        } else if($service_id =='ninjaxu' ){
            $data['service_id'] = 1795;
        }else if($service_id == 'nrocoin'){
            $data['service_id'] = 1801;
        }
        $data['service_id'] = $service_id;
        $data['customer_data0'] = $customer_data0;
        $data['request_id'] = $request_id;
        if(is_array($data)){
            $dataPost = http_build_query($data);
        }else{
            $dataPost = $data;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url."?".$dataPost);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        // dd($ch);

        $resultRaw = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        $result = json_decode($resultRaw);


        //lưu log gọi curl
        $path = storage_path() ."/logs/services-auto/";
        $filename=$path."edit_info_daily_".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw;
        \File::append( $filename,$contentText."\n");

        $resultChange = new \stdClass();
        if($result && isset($result->status)){
            if($result->status == 1){

                $resultChange->status=1; // chỉnh sửa thành công
            }else{
                $resultChange->status=0; // xảy ra lỗi
            }
            return $resultChange;
        }else{
            return null;
        }
        return $resultChange;
    }

    public static function getListBot($botType){


        if($botType =='ninjaxu' ){
            $type = "ninjaxu";
        }else if($botType == 'nrocoin'){
            $type = "nro";
        }


        $url = config('daily.url') ."/api/{$type}/list-bot";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $resultRaw = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = json_decode($resultRaw);
        return $result;
    }

    public static function createUser($username,$password){
        try{
            $url = config('daily.url').'/api/user/store';
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
            $myfile = fopen(storage_path() ."/logs/log-create_user_daily.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().' - '.$username.' - '.$resultRaw;
            fwrite($myfile, $txt ."\n");
            fclose($myfile);
            $resultChange = new \stdClass();
            if(isset($result) && isset($result->status)){
                if($result->status == 1){
                    $resultChange->status = 1;
                    $resultChange->message = $result->message;
                    $resultChange->username = $result->data->username;
                    $resultChange->partner_key_service = $result->data->partner_key_service;
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
