<?php



namespace App\Library;
use Carbon\Carbon;
use Cookie;
use Illuminate\Support\Facades\Log;
use Session;

class DirectAPI{

    public static function _getStock($url, array $data, $method){
        $resultChange = new \stdClass();
        $api_key = config('rbxapi.api_key');
        $headers = array();
        $headers[] = "api-key: $api_key";
        $headers[] = 'Content-Type: application/json';

        if(is_array($data)){
            $dataPost = http_build_query($data);
        }else{
            $dataPost = $data;
        }

        $url = config('rbxapi.url').$url;

        if($method == "GET"){
            $url = $url.'?'.$dataPost;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if($method == "POST"){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
            $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            curl_setopt($ch, CURLOPT_REFERER, $actual_link);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $resultRaw = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result = json_decode($resultRaw);

        //lưu log gọi curl
        $path = storage_path() ."/logs/rbx-api/";
        $filename=$path."file_get_stock_".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw;
        \File::append( $filename,$contentText."\n");

        $resultChange->response_code = $httpcode;
        $resultChange->response_data = $result;
        curl_close($ch);
        $dataReturn = new \stdClass();
        if (!$httpcode == 200){
            $dataReturn->status = 0;
            $dataReturn->message= "Lỗi không gọi được nhà cung cấp";
            return $dataReturn;
        }
        if (!isset($result)){
            $dataReturn->status= 0;
            $dataReturn->message= "Lỗi không gọi được nhà cung cấp";
            return $dataReturn;
        }

        $robuxAvailable = 0;
        $maxRobuxAvailable = 0;
        $dataReturn->status = 1;
        $response_data = $resultChange->response_data;
        if (isset($response_data->robuxAvailable)){
            $robuxAvailable = $response_data->robuxAvailable??0;
        }
        if (isset($response_data->maxRobuxAvailable)){
            $maxRobuxAvailable = $response_data->maxRobuxAvailable??0;
        }
        $dataReturn->robuxAvailable = $robuxAvailable;
        $dataReturn->maxRobuxAvailable = $maxRobuxAvailable;
        return $dataReturn;

    }

    public static function _getStockDetail($url, array $data, $method){
        $resultChange = new \stdClass();
        $api_key = config('rbxapi.api_key');
        $headers = array();
        $headers[] = "api-key: $api_key";
        $headers[] = 'Content-Type: application/json';

        if(is_array($data)){
            $dataPost = http_build_query($data);
        }else{
            $dataPost = $data;
        }

        $url = config('rbxapi.url').$url;

        if($method == "GET"){
            $url = $url.'?'.$dataPost;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if($method == "POST"){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
            $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            curl_setopt($ch, CURLOPT_REFERER, $actual_link);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $resultRaw = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result = json_decode($resultRaw);

        //lưu log gọi curl
        $path = storage_path() ."/logs/rbx-api/";
        $filename=$path."file_get_stock_detail".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw;
        \File::append( $filename,$contentText."\n");

        $resultChange->response_code = $httpcode;
        $resultChange->response_data = $result;
        curl_close($ch);
        $dataReturn = new \stdClass();
        if (!$httpcode == 200){
            $dataReturn->status = 0;
            $dataReturn->message= "Lỗi không gọi được nhà cung cấp";
            return $dataReturn;
        }
        if (!isset($result)){
            $dataReturn->status= 0;
            $dataReturn->message= "Lỗi không gọi được nhà cung cấp";
            return $dataReturn;
        }

        $dataReturn->status = 1;
        $response_data = $resultChange->response_data??[];
        $dataReturn->data = $response_data;
        return $dataReturn;

    }

    public static function _getBalance($url, array $data, $method){
        $resultChange = new \stdClass();
        $api_key = config('rbxapi.api_key');
        $headers = array();
        $headers[] = "api-key: $api_key";
        $headers[] = 'Content-Type: application/json';

        if(is_array($data)){
            $dataPost = http_build_query($data);
        }else{
            $dataPost = $data;
        }

        $url = config('rbxapi.url').$url;

        if($method == "GET"){
            $url = $url.'?'.$dataPost;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if($method == "POST"){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
            $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            curl_setopt($ch, CURLOPT_REFERER, $actual_link);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $resultRaw = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result = json_decode($resultRaw);

        //lưu log gọi curl
        $path = storage_path() ."/logs/rbx-api/";
        $filename=$path."file_get_stock_".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw;
        \File::append( $filename,$contentText."\n");

        $resultChange->response_code = $httpcode;
        $resultChange->response_data = $result;
        curl_close($ch);
        $dataReturn = new \stdClass();
        if (!$httpcode == 200){
            $dataReturn->status = 0;
            $dataReturn->message= "Lỗi không gọi được nhà cung cấp";
            return $dataReturn;
        }
        if (!isset($result)){
            $dataReturn->status= 0;
            $dataReturn->message= "Lỗi không gọi được nhà cung cấp";
            return $dataReturn;
        }

        if (!isset($resultChange->response_data)){
            $dataReturn->status= 0;
            $dataReturn->message= "Lỗi không gọi được nhà cung cấp";
            return $dataReturn;
        }

        $response_data = $resultChange->response_data;
        $balance = 0;
        if (isset($response_data->balance)){
            $balance = $response_data->balance??0;
        }

        $dataReturn->status = 1;
        $dataReturn->balance = $balance;
        return $dataReturn;

    }

    public static function _buyGamepass($url, array $data, $method,$payment_type){
        $resultChange = new \stdClass();
        $api_key = config('rbxapi.api_key_'.$payment_type);
        $headers = array();
        $headers[] = "api-key: $api_key";
        $headers[] = 'Content-Type: application/json';

// Directly encode the array or string to JSON if it's an array
        if (is_array($data)) {
            $dataPost = json_encode($data);
        } else {
            $dataPost = $data; // Assuming it's already a valid JSON string if not an array
        }

        $url = config('rbxapi.url') . $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost); // Send JSON data

        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://";
        $actual_link .= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'daily.tichhop.pro';
        $actual_link .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        curl_setopt($ch, CURLOPT_REFERER, $actual_link);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $resultRaw = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result = json_decode($resultRaw);

        //lưu log gọi curl
        $path = storage_path() ."/logs/buy-rbx-api/";
        $filename=$path."file_buy_rbx_api_".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw;
        \File::append( $filename,$contentText."\n");

        $resultChange->response_code = $httpcode;
        $resultChange->response_data = $result;
        curl_close($ch);
        $dataReturn = new \stdClass();
        if ($httpcode !== 201){
            $dataReturn->status = 0;
            $dataReturn->message= $result->message??"Lỗi không gọi được nhà cung cấp";
            return $dataReturn;
        }

        if (!isset($result)){
            $dataReturn->status= 0;
            $dataReturn->message= $result->message??"Lỗi không gọi được nhà cung cấp";
            return $dataReturn;
        }

        if (!isset($result->success)){
            $dataReturn->status= 0;
            $dataReturn->message= $result->message??"Lỗi không gọi được nhà cung cấp";
            return $dataReturn;
        }

        if ($result->success !== true){
            $dataReturn->status= 0;
            $message = "Lỗi không gọi được nhà cung cấp";
            if (isset($result->message)){
                $message = $result->message??'';
                if (is_array($message)) {
                    $message = implode(", ", $result->message);
                }
            }
            $dataReturn->message= $message;
            return $dataReturn;
        }

        $dataReturn->status = 1;
        $dataReturn->message= "Đã gửi đơn sang RBX API";
        return $dataReturn;

    }

    public static function _buyServer($url, array $data, $method,$payment_type){
        $resultChange = new \stdClass();
        $api_key = config('rbxapi.api_key_'.$payment_type);
        $headers = array();
        $headers[] = "api-key: $api_key";
        $headers[] = 'Content-Type: application/json';

// Directly encode the array or string to JSON if it's an array
        if (is_array($data)) {
            $dataPost = json_encode($data);
        } else {
            $dataPost = $data; // Assuming it's already a valid JSON string if not an array
        }

        $url = config('rbxapi.url') . $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost); // Send JSON data

        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://";
        $actual_link .= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'daily.tichhop.pro';
        $actual_link .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        curl_setopt($ch, CURLOPT_REFERER, $actual_link);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $resultRaw = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result = json_decode($resultRaw);

        //lưu log gọi curl
        $path = storage_path() ."/logs/buy-rbx-api/";
        $filename=$path."file_buy_rbx_api_".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw;
        \File::append( $filename,$contentText."\n");

        $resultChange->response_code = $httpcode;
        $resultChange->response_data = $result;
        curl_close($ch);
        $dataReturn = new \stdClass();
        if ($httpcode !== 201){
            $dataReturn->status = 0;
            $dataReturn->message= $result->message??"Lỗi không gọi được nhà cung cấp";
            return $dataReturn;
        }

        if (!isset($result)){
            $dataReturn->status= 0;
            $dataReturn->message= $result->message??"Lỗi không gọi được nhà cung cấp";
            return $dataReturn;
        }

        if (!isset($result->success)){
            $dataReturn->status= 0;
            $dataReturn->message= $result->message??"Lỗi không gọi được nhà cung cấp";
            return $dataReturn;
        }

        if ($result->success !== true){
            $dataReturn->status= 0;
            $message = "Lỗi không gọi được nhà cung cấp";
            if (isset($result->message)){
                $message = $result->message??'';
                if (is_array($message)) {
                    $message = implode(", ", $result->message);
                }
            }
            $dataReturn->message= $message;
            return $dataReturn;
        }

        $dataReturn->status = 1;
        $dataReturn->message= "Đã gửi đơn sang RBX API";
        return $dataReturn;

    }

    public static function _cancelProduct($url, array $data, $method,$payment_type){

        $resultChange = new \stdClass();
        $api_key = config('rbxapi.api_key_'.$payment_type);
        $headers = array();
        $headers[] = "api-key: $api_key";
        $headers[] = 'Content-Type: application/json';

// Directly encode the array or string to JSON if it's an array
        if (is_array($data)) {
            $dataPost = json_encode($data);
        } else {
            $dataPost = $data; // Assuming it's already a valid JSON string if not an array
        }

        $url = config('rbxapi.url') . $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost); // Send JSON data

        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://";
        $actual_link .= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'daily.tichhop.pro';
        $actual_link .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        curl_setopt($ch, CURLOPT_REFERER, $actual_link);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $resultRaw = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result = json_decode($resultRaw);

        //lưu log gọi curl
        $path = storage_path() ."/logs/cancel-rbx-api/";
        $filename=$path."file_cancel_rbx_api_".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw;
        \File::append( $filename,$contentText."\n");

        $resultChange->response_code = $httpcode;
        $resultChange->response_data = $result;
        curl_close($ch);
        $dataReturn = new \stdClass();
        if (!$httpcode == 200){
            $dataReturn->status = 0;
            $dataReturn->message= $result->message??"Lỗi không gọi được nhà cung cấp";
            return $dataReturn;
        }

        if (!isset($result)){
            $dataReturn->status= 0;
            $dataReturn->message= "Lỗi không gọi được nhà cung cấp";
            return $dataReturn;
        }

        if ($result->status !== "Cancelled"){
            $dataReturn->status= 0;
            $message = "Lỗi không gọi được nhà cung cấp";
            if (isset($result->message)){
                $message = $result->message??'';
            }
            $dataReturn->message= $message;
            return $dataReturn;
        }

        $dataReturn->status = 1;
        $dataReturn->message= "Đã gửi từ chối đơn hàng RBX API";
        return $dataReturn;

    }

    public static function _checkBalanceRoblox($url, array $data, $method,$log = false){
        $resultChange = new \stdClass();
        $headers = array();
        $headers[] = 'Content-Type: application/json';

// Directly encode the array or string to JSON if it's an array
        if (is_array($data)) {
            $dataPost = json_encode($data);
        } else {
            $dataPost = $data; // Assuming it's already a valid JSON string if not an array
        }

        $url = config('proxy.url') . $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost); // Send JSON data

        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://";
        $actual_link .= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'daily.tichhop.pro';
        $actual_link .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        curl_setopt($ch, CURLOPT_REFERER, $actual_link);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $resultRaw = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result = json_decode($resultRaw);
        $resultChange->response_code = $httpcode;
        $resultChange->response_data = $result;
        curl_close($ch);
        $dataReturn = new \stdClass();
        if (!$httpcode == 200){
            $dataReturn->status= 0;
            $dataReturn->message= "Lỗi không gọi được nhà cung cấp";
            return $dataReturn;
        }
        if (!isset($result)){
            $dataReturn->status= 0;
            $dataReturn->message= "Lỗi không gọi được nhà cung cấp";
            return $dataReturn;
        }
        if ($result->status == 1){

            if (!$result->data) {
                $dataReturn->status= 0;
                $dataReturn->message= "Lỗi không gọi được nhà cung cấp";
                return $dataReturn;
            }


            $dataReturn->status= 1;
            $dataReturn->message= $result->message;
            $dataReturn->balance = $result->data->balance;
            return $dataReturn;
        }elseif ($result->status == 0){
            $dataReturn->status= 0;
            $dataReturn->message= $result->message;
            return $dataReturn;
        }else{
            $dataReturn->status= 2;
            $dataReturn->message= $result->message??"Không có dữ liệu trả về";
            return $dataReturn;
        }
    }
}
