<?php
namespace App\Library;

use Request;
use Carbon\Carbon;
use App\Models\Shop;
use Illuminate\Support\Facades\Log;

class HelpItemAdd
{

    //Gọi APi cho bên rút kim cương,...




    public static function ITEMADD_CALLBACK($provider,$username,$password,$id,$item,$server_id,$tranid,$shopid,$payment_gateways)
    {
        //include_once(app_path() . '/Library/WithdrawItemGateWay/config.php');
        $url = config('app.app_url_api_tichhop_minigame'); //url API auto add item
//        $actual_domain = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $shop = Shop::where('status', 1)->where('id',$shopid)->first();
        $data = array();
        $data['secret'] = $shop->tichhop_key;
        $data['provider'] = $provider;
        $data['username'] = $username;
        $data['password'] = $password;
        $data['id'] = $id;
        $data['server_id'] = $server_id;
        $data['item'] = $item;
        $data['callback'] = config('app.url')."/api/v1/minigame/tichhop-callback/".$tranid;
        $data['tranid'] = $tranid;
        $data['net'] = $payment_gateways;

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
        $resultRaw = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = json_decode($resultRaw);


        //lưu log gọi curl
        $path = storage_path() ."/logs/minigame-auto/";
        $filename=$path."fire_tichhop_".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw;
        \File::append( $filename,$contentText."\n");

        $resultChange = new \stdClass();

        if ($result && isset($result->status)) {

            //Chờ xử lý
            if ($result->status==0) {// chờ xử lý
                $resultChange->status=0;
                $resultChange->tranid = $tranid;
            }
            //Giao dịch thất bại (được hoàn tiền)
            elseif($result->status==2){
                $resultChange->status = 3;
            }
            //Không đăng nhập được hoặc tài khoản sai (được hoàn tiền)
            elseif($result->status==3){
                $resultChange->status = 3;
            }
            else{
                $resultChange->status=null;
            }

            $message=$result->message??"";
            if(strpos($message,'Tài khoản shop không đủ số dư')>-1){
                $resultChange->status = -1;// Tài khoản hết tiền
            }
            $resultChange->user_balance=$result->user_balance??0;
            $resultChange->message = $message;

        }
        else {
            //debug thì mở cái này
            $myfile = fopen(storage_path() ."/logs/log_itemadd_noresponse.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now()." : lỗi [" .$httpcode."] : [tranid: ".$tranid."] : ".$resultRaw;
            fwrite($myfile, $txt ."\n");
            fclose($myfile);
            return null;
        }
        return $resultChange;
    }

    //Gọi APi cho bên dịch vụ mua kim cương,...
    public static function SERVICES_ITEMADD_CALLBACK($partner_key,$provider,$username,$password,$id,$item,$server_id,$request_id)
    {

        $resultChange = new \stdClass();

        try {
            $url = 'http://tichhop.net/auto-api-3ji231h'; //url API auto add item
            //$secretkey = 'dailydichvume_603367406bc30';

            $secretkey =$partner_key;


            $data = array();
            $data['secret'] = $secretkey;
            $data['provider'] = $provider;
            $data['username'] = $username;
            $data['password'] = $password;
            $data['id'] = $id;
            $data['server_id'] = $server_id;
            $data['item'] = $item;
            //$data['callback'] = "http://s-apinew.nick.vn/api/services-tichhop-callback/".$request_id;
            $data['tranid'] = $request_id;
            $data['callback'] = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]"."/api/services-auto-callback/". strtolower("SERVICES_ITEMADD_CALLBACK")."?tranid=".$request_id."&sign=3857gjfhnj51";;




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
            $resultRaw = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $result = json_decode($resultRaw);


            //lưu log gọi curl
            $path = storage_path() ."/logs/services-auto/";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".http_build_query($data) ."] : ".$resultRaw;
            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$contentText."\n");


            $resultChange = new \stdClass();

            if ($result && isset($result->status)) {

                //Chờ xử lý
                if ($result->status==0) {
                    $resultChange->status=2;
                }
                //Giao dịch thất bại (được hoàn tiền)
                elseif($result->status==2){
                    $resultChange->status = 3;
                }
                //Không đăng nhập được hoặc tài khoản sai (được hoàn tiền)
                elseif($result->status==3){
                    $resultChange->status = 3;
                }
                else{
                    $resultChange->status=null;
                }

                $message=$result->message??"";
                if(strpos($message,'Tài khoản shop không đủ số dư')>-1){
                    $resultChange->status = -1;// Tài khoản hết tiền
                }
                $resultChange->message = $message;

            } else {
                //debug thì mở cái này
                return null;
            }
            //dd($resultChange);
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


    public static function createUser($username,$password){
        try{
            $url = 'http://tichhop.net/api/create-user';
            $secretkey = 'dailydichvume_603367406bc30';
            $email = $username.'_khotaptrung@gmail.com';
            $data = array();
            $data['name'] = $username;
            $data['email'] = $email;
            $data['password'] = $password;
            $data['key'] = $secretkey;
            // $data['test'] = 1;
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
            $resultRaw = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $result = json_decode($resultRaw);
            $myfile = fopen(storage_path() ."/logs/log-create_user_tichhop-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().' - '.$username.' - '.$resultRaw;
            fwrite($myfile, $txt ."\n");
            fclose($myfile);
            $resultChange = new \stdClass();
            if(isset($result) && isset($result->status)){
                if($result->status == 1){
                    $resultChange->status = 1;
                    $resultChange->message = $result->message;
                    $resultChange->username = $username;
                    $resultChange->tichhop_key = $result->user->secret;
                    $resultChange->id = $result->user->id;
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
