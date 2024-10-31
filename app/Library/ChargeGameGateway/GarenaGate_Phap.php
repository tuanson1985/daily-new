<?php
namespace App\Library\ChargeGameGateway;

use Carbon\Carbon;
class GarenaGate_Phap
{





    //Gọi APi cho bên dịch vụ mua kim cương,...
    public static function fire($partner_key,$provider,$username,$password,$id,$item,$server_id,$request_id,$net="SMS")
    {




        $resultChange = new \stdClass();

        try {
            $url = 'http://tichhop.net/auto-api-3ji231h'; //url API auto add item


            $secretkey =$partner_key;



            $data = array();
            $data['secret'] = $secretkey;
            $data['provider'] = $provider;
            $data['username'] = $username;
            $data['password'] = $password;
            $data['id'] = $id;
            $data['server_id'] = $server_id;
            $data['item'] = $item;
            $data['tranid'] = $request_id;
            $data['net'] = $net;
            $data['callback'] = "http://s-api.tichhop.pro/api/v1/services-auto-callback"."?request_id=".$request_id."&sign=3857gjfhnj51";;

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
            $filename=$path."fire_tichhop_".Carbon::now()->format('Y-m-d').".txt";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw;
            \File::append( $filename,$contentText."\n");

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
                $resultChange->user_balance=$result->user_balance??0;
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

}
