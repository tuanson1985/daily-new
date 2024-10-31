<?php

namespace App\Library;


use Carbon\Carbon;


class HelpSpinContent
{


    public  static  function spinPaid($text){
        $url = "https://api.tiengviet.io/my/spin/";

        $data = array();
        $data['token'] = "1a33d183ea_GmEIccNnpmKGlMhpHdLiW9W598942";
        $data['text'] = $text;

        if(is_array($data)){
            $dataPost = json_encode($data,JSON_UNESCAPED_UNICODE );
        }else{
            $dataPost = $data;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);

        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $resultRaw = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = json_decode($resultRaw);

        $resultChange = new \stdClass();
        if($result){
            if($result->code==200){
                $resultChange->status=1;
                $resultChange->message=ltrim($result->message,". ");

            }
            else{
                $resultChange->status=0;
                $resultChange->message=$result->message??"";
            }
            return $resultChange;
        }
        else{
            return $resultChange;
        }


    }
    public  static  function spinFree($text,$cookiesSender=null){



        $dataReturn = new \stdClass();
        $url = "https://my.tiengviet.io/";



        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $resultRaw=curl_exec($ch);
        curl_close($ch);




       echo $resultRaw;

        preg_match('/<input type="hidden" name="secretKey".*value="(.*?)"/', $resultRaw, $matches);
        dd($matches);
        if (isset($matches[1]) && $matches[1] != '') {
            $auth_key = $matches[1];
            dd($auth_key);

        }
        else {

            $resultCheck = -1;
            return $resultCheck;
        }
    }


    public  static  function login(){



        $dataReturn = new \stdClass();
        $url = "https://my.tiengviet.io/ajax/login.php";

        $data = array();
        $data['email'] = "dinhan.hqgroup@gmail.com";
        $data['password'] = "dinhan123@123";
        $data['checkbox'] = 0;
        $data['code'] ="rxZeDvBEObbKGhuxkYUY";
        $data['secretKey'] ="915395";
        $data['time'] ="1661420539";
        $data['secretT'] ="0000000009e72065";

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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $resultRaw=curl_exec($ch);
        curl_close($ch);
        dd($resultRaw);



        echo $resultRaw;

    }


}
