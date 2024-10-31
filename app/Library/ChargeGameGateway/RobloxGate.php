<?php

namespace App\Library\ChargeGameGateway;
use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11/18/2016
 * Time: 3:14 PM
 */
class RobloxGate
{

//    public static $proxyList = [
//        "103.147.189.73|5555",
//    ];
    public static $proxyList = [
        "103.68.249.148|5555",
        "103.68.250.13|5555",
        "103.68.250.87|5555",
        "103.68.249.12|5555",
        "103.68.249.18|5555",
        "103.68.250.96|5555",
        "103.68.250.108|5555",
        "103.68.249.95|5555",
        "103.68.249.109|5555",
        "103.68.249.172|5555",
        "103.68.249.182|5555",
        "103.68.249.190|5555",
        "103.68.249.198|5555",
        "103.68.249.232|5555",
        "103.68.250.157|5555",
        "103.68.250.171|5555",
        "103.68.250.173|5555",
        "103.68.250.186|5555",
        "103.68.248.49|5555",
        "103.68.248.11|5555",
        "103.68.248.163|5555",
        "103.68.248.148|5555",
        "103.147.189.162|5555",
        "103.147.189.132|5555",
        "103.147.189.42|5555",
        "103.147.189.202|5555",
        "103.147.189.93|5555",
        "103.147.189.233|5555",
        "103.147.189.189|5555",
        "103.147.189.143|5555",
        "103.147.189.12|5555",
        "103.147.188.100|5555",
        "103.147.188.5|5555",
        "103.147.188.217|5555",
        "103.147.188.111|5555",
        "103.147.188.137|5555",
        "103.147.188.131|5555",
        "103.147.188.101|5555",
        "103.147.188.49|5555",
        "103.147.188.39|5555",
        "103.147.188.134|5555",
        "103.147.188.152|5555",
        "103.147.188.197|5555",
        "103.147.188.31|5555",
        "103.147.188.211|5555",
        "103.147.188.42|5555",
        "103.147.188.26|5555",
        "103.147.188.58|5555",
        "103.147.188.195|5555",
        "103.147.188.149|5555"
    ];

    public static function checkUserInGroup($username,$group_id)
    {
        $dataReturn = new \stdClass();

        $data = array(
            'keyword' => $username,
            'limit' => 10,
        );
        $dataPost = http_build_query($data);

        $url = "https://users.roblox.com/v1/users/search";

        $ch = curl_init();


        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url . "?" . $dataPost);

        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result=json_decode($resultRaw);


        $roblox_id=null;
        if($result && isset($result->data)){


            foreach ($result->data as $item){
                if($item->name==$username){
                    $roblox_id=$item->id;
                    break;
                }
            }
        }
        if($roblox_id==null){
            $dataReturn->status=0;
            $dataReturn->message= __('Không tìm thấy user trên hệ thống roblox');
            $dataReturn->info=$roblox_id;
            return $dataReturn;

        }


        //////////////////////// check user in group /////////////////////////////



        $url = "https://groups.roblox.com/v2/users/{$roblox_id}/groups/roles";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result=json_decode($resultRaw);

        if($result && isset($result->data)){
            //check data user trong group
            $isInGroup=false;
            foreach ($result->data as $item){

                if($item->group->id==$group_id){
                    $isInGroup=true;

                    $dataReturn->status=1;
                    $dataReturn->message= __("User có trong group");
                    $dataReturn->info=$roblox_id;
                    return $dataReturn;
                }
            }
            if($isInGroup==false){
                $dataReturn->status=0;
                $dataReturn->message= __("User không có trong group");
                $dataReturn->info=$roblox_id;
                return $dataReturn;
            }
        }
    }
    //active link function
    public static function checkCookieLogin($cookiesSender)
    {



        $dataReturn = new \stdClass();
        //////////////////////////Check  Cookie ////////////////////////////////////////
        // $cookies="Cookie: __utmz=200924205.1586115275.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); RBXViralAcquisition=time=4/5/2020 10:51:49 PM&referrer=&originatingsite=; RBXSource=rbx_acquisition_time=4/5/2020 10:51:49 PM&rbx_acquisition_referrer=&rbx_medium=Direct&rbx_source=&rbx_campaign=&rbx_adgroup=&rbx_keyword=&rbx_matchtype=&rbx_send_info=1; GuestData=UserID=-1858346830; RBXImageCache=timg=32323234386638312D666539642D343538382D626362362D383539643065626634316132253131332E32332E3130342E31343425342F352F3230323020363A32383A313720504DDC385AB8D96F7CF1E8603BE028E9E5A8C7F0AF54; __utmc=200924205; gig_canary=false; gig_canary_ver=10832-1-26435745; gig_bootstrap_3_OsvmtBbTg6S_EUbwTPtbbmoihFY5ON6v6hbVrTbuqpBs7SyF_LQaJwtwKJ60sY1p=_gigya_ver3; .ROBLOSECURITY=_|WARNING:-DO-NOT-SHARE-THIS.--Sharing-this-will-allow-someone-to-log-in-as-you-and-to-steal-your-ROBUX-and-items.|_63F355C7C1C74D0DCB8D39B206BD428D80979DD0F9C6F8D6D7FF603235717DD93E9AB7A74DEFC3F55463DF3C01E40A62DEBC852E560060CCD03C7A4472A2D360F624EA5A30809335853E238B23C20529904BB7A58F66EBF46D01E238FBCF09A8B00606343915F388219F47F47F9A142DC821EB034FF821D117F54E3AE5CE2B5DAEA4D8867463BFEF663C6F0F0A092A57ED6B5C4D2E308EEC993CB524A5FEF2C97D59E19CA2399569ED8C098D12F176DE25FDC23A7CF17FC63215BB7804FCC3914088775F0C9F3779BDC1A1FECC12E52727CEE1D2C501D4E889519B201FBAE29EB82003A0DDEB8FC053D9A6A549FE6BE6F12CA5F7E42BBF68D78118ECFCB0CEC5A7149CF19C8A8F953389F7BAEDA0A48B6CC511FEC72ABD006F40B198CE9A21851466E0CD; RBXEventTrackerV2=CreateDate=4/5/2020 10:55:26 PM&rbxid=1183037070&browserid=49835145566; .RBXID=_|WARNING:-DO-NOT-SHARE-THIS.--Sharing-this-will-allow-someone-to-log-in-as-you-and-to-steal-your-ROBUX-and-items.|_eyJhbGciOiJIUzI1NiJ9.eyJqdGkiOiIxZDA3NDE1ZC0wNjI3LTQ5MDYtOWFlNS0zY2VkZWZhYzdkMzYiLCJzdWIiOjI5NzIzMjQ4Nn0.ljgF8fDxNRtY_pL9NeSAp8ygMuG64GIh2SuwK7rOu7w; RBXSessionTracker=sessionid=caadbb0a-5392-4b58-82cd-8e0d6a49460b; __utma=200924205.704255875.1586115275.1586179573.1586181942.6; __utmb=200924205.0.10.1586181942";

        $cookies=str_replace("Cookie: ","",$cookiesSender);
        $url = "https://www.roblox.com/home";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        //return $resultRaw;
        preg_match('/data-token=(.*?)\>/', $resultRaw, $matches);

        if (isset($matches[1]) && $matches[1] != '') {
            $auth_key = $matches[1];
            $dataReturn->status=1;
            $dataReturn->message= __("User có trong group");
            $dataReturn->info=$auth_key;
            return $dataReturn;


        } else {

            $dataReturn->status=2;
            $dataReturn->message= __("Nick chủ group chưa đăng nhập");
            $dataReturn->info=null;
            return $dataReturn;
        }
    }

    public static function ProcessTranfer($username,$amount,$group_id,$cookiesSender)
    {

        $dataReturn = new \stdClass();
        //check user in group
        $result=self::checkUserInGroup($username,$group_id);
        if($result->status!=1){
            return $result;
        }
        $recipientId=$result->info;

        //check cookie sender
        $result=self::checkCookieLogin($cookiesSender);

        if($result->status!=1){
            return $result;
        }
        $auth_key=$result->info;

        //////////////////////////Tranfer  roblox ////////////////////////////////////////
        $url = "https://groups.roblox.com/v1/groups/{$group_id}/payouts";
        $data = array();
        $data['PayoutType'] = "FixedAmount";
        $recipients=[
            'recipientId'=>$recipientId,
            'recipientType'=>"User",
            'amount'=>$amount,
        ];
        $data['Recipients'] =[$recipients];
        $dataPost = json_encode($data);

        $ch = curl_init();

        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-CSRF-TOKEN: '.$auth_key,
            'Content-Type: application/json;charset=UTF-8',

        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpcode==200 && $resultRaw=="{}"){
            $dataReturn->status=1;
            $dataReturn->message= __("Giao dịch thành công");
            $dataReturn->info=null;
            return $dataReturn;
        }
        else{
            $result=json_decode($resultRaw);
            $dataReturn->status=0;
            $dataReturn->message= isset($result->errors[0]->message)?$result->errors[0]->message:null;
            $dataReturn->info=null;
            return $dataReturn;

        }

        //debug thì mở cái này
        $myfile = fopen(storage_path() . "/logs/log_no_response.txt", "a") or die("Unable to open file!");
        $txt = Carbon::now() . " :" . " [" . $httpcode . "] - " . $username . " - " . $amount . " - " . $group_id. " : " . $resultRaw;
        fwrite($myfile, $txt . "\n");
        fclose($myfile);

        return $resultRaw;

    }

    public static function getCurrency($group_id){

        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, "https://economy.roblox.com/v1/groups/{$group_id}/currency" );

        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json;charset=UTF-8',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result=json_decode($resultRaw);
        if(isset($result->robux))
        {
            return $result->robux;
        }
        else
        {
            return 0;
        }

    }

    public static function ProcessBuyServer($server_id,$amount,$cookiesSender,$request_id,$proxyCustomString=null){

        try {

            if($proxyCustomString==null){
                $proxyCustomString=self::$proxyList[array_rand(self::$proxyList)];
                $proxy=explode("|",$proxyCustomString);
            }
            elseif($proxyCustomString!=""){
                $proxy=explode("|",$proxyCustomString);
            }
            else{
                $proxy="";
            }


            $dataReturn = new \stdClass();
            //check thông tin số roblox của server cần mua
            $url = "https://roblox.com/games/{$server_id}";

            //$url = "https://www.roblox.com/home";

            $ch = curl_init();
            //data dạng get
            curl_setopt($ch, CURLOPT_URL, $url );
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
            curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);

            if(isset($proxy[0]) && $proxy[1]){
                curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
                curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
                if(isset($proxy[2]) && isset($proxy[3])){
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                    curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
                }
            }

            $resultRaw=curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);


            //lưu log gọi curl
//            $path = storage_path() ."/logs/services-auto/";
//            $filename=$path."fire_".Carbon::now()->format('Y-m-d').".txt";
//            if(!\File::exists($path)){
//                \File::makeDirectory($path, $mode = "0755", true, true);
//            }
//            $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode(['request_id'=>$request_id]) ."] : ".$resultRaw;
//            \File::append( $filename,$contentText."\n");



            preg_match('/data-displayName=(.*?)\s/', $resultRaw, $matches);

            if (isset($matches[1]) && $matches[1] != '') {
                $username_roblox=$matches[1]."";
            } else {

                $dataReturn->status=2;
                $dataReturn->message= __("Nick bot chưa đăng nhập 1");
                $dataReturn->info=null;
                return $dataReturn;
            }


            preg_match('/data-userid=\"(.*?)\"/', $resultRaw, $matches);

            if (isset($matches[1]) && $matches[1] != '') {
                $data_userid = $matches[1]."";

            } else {
                $dataReturn->status=2;
                $dataReturn->message= __("Nick bot chưa đăng nhập 2");
                $dataReturn->info=null;
                return $dataReturn;
            }



            preg_match('/data-token=\"(.*?)\"/', $resultRaw, $matches);

            if (isset($matches[1]) && $matches[1] != '') {
                $auth_key = $matches[1]."";
                $auth_key=str_replace( "\"","",$auth_key);
            } else {

                $dataReturn->status=2;
                $dataReturn->message= __("Nick bot chưa đăng nhập 3");
                $dataReturn->info=null;
                return $dataReturn;
            }

            preg_match('/data-userid=\"(.*?)\"/', $resultRaw, $matches);


            if (isset($matches[1]) && $matches[1] != '') {
                $user_id=$matches[1]."";
            } else {

                $dataReturn->status=2;
                $dataReturn->message= __("Nick bot chưa đăng nhập 4");
                $dataReturn->info=null;
                return $dataReturn;
            }

            $botInfo=self::checkLiveAndBalanceBot($cookiesSender,$proxyCustomString);
            $currencyUser=$botInfo->balance??0;
            if((int)$currencyUser<$amount){
                $dataReturn->status=33;
                $dataReturn->currencyUser=$currencyUser;
                $dataReturn->message= __("Bot không đủ số dư để thực hiện order");
                $dataReturn->info=null;
                return $dataReturn;
            }

            $expectedPrice="";//&expectedPrice=15
            $__RequestVerificationToken=""; //<input name="__RequestVerificationToken" type="hidden" value="qtgaSz_TXpuAlQlMgA3kJ4Ig-0iaI1d0xzLJPYCLugnTH99tWBuhoOmbrkd-7OKEqMuRah8OLF5GAFMqaKJ48my8Fnc1">
            $universeId=""; //data-universeid=1591581981
            $privateServerName="ID_".$request_id;

            $productId=""; //data-product-id="1163409812"
            $expectedCurrency="";//data-expected-currency="1"
            $expectedSellerId="";//data-expected-seller-id="2064137198"


            //lấy thông số $expectedPrice của server cần mua ( Số roblox cần mua )
            preg_match(  '/data-private-server-price=\"(.*?)\"/', $resultRaw, $matches);
            if (isset($matches[1]) && $matches[1] != '') {
                $expectedPrice = $matches[1];
            } else {

                $dataReturn->status=0;
                $dataReturn->message= __("Thông số parram (EP) không đúng");
                $dataReturn->info=null;
                return $dataReturn;
            }


            //check số roblox với với yêu cầu
            if($amount!=intval($expectedPrice)){

                $dataReturn->status=0;
                $dataReturn->message= __("Số Robux mua không đúng với yêu cầu dịch vụ");
                $dataReturn->info=null;
                return $dataReturn;
            }




            //lấy thông số $__RequestVerificationToken của server cần mua
            preg_match('/<input name="__RequestVerificationToken" .* value="(.*?)"/', $resultRaw, $matches);


//            if (isset($matches[1]) && $matches[1] != '') {
//                $__RequestVerificationToken = $matches[1];
//            } else {
//
//                $dataReturn->status=0;
//                $dataReturn->message= __("Thông số parram (RVT) không đúng");
//                $dataReturn->info=null;
//                return $dataReturn;
//            }


            //lấy thông số $universeId của server cần mua

            preg_match(  '/data-universe-id=\"(.*?)\"/', $resultRaw, $matches);

            if (isset($matches[1]) && $matches[1] != '') {
                $universeId = $matches[1];
            } else {

                $dataReturn->status=0;
                $dataReturn->message= __("Thông số parram (UI) không đúng");
                $dataReturn->info=null;
                return $dataReturn;
            }


            //lấy thông số $productId của server cần mua

            preg_match(  '/data-private-server-product-id=\"(.*?)\"/', $resultRaw, $matches);
            if (isset($matches[1]) && $matches[1] != '') {
                $productId = $matches[1];
            } else {

                $dataReturn->status=0;
                $dataReturn->message= __("Thông số parram (PI) không đúng");
                $dataReturn->info=null;
                return $dataReturn;
            }


            //lấy thông số $expectedCurrency của server cần mua

            //preg_match(  '/data-expected-currency=\"(.*?)\"/', $resultRaw, $matches);
            //if (isset($matches[1]) && $matches[1] != '') {
            //    $expectedCurrency = $matches[1];
            //} else {
            //
            //    $dataReturn->status=0;
            //    $dataReturn->message= "Thông số parram (EC) không đúng";
            //    $dataReturn->info=null;
            //    return $dataReturn;
            //}
            $expectedCurrency=1;
            //lấy thông số $expectedSellerId của server cần mua

            preg_match(  '/data-seller-id=\"(.*?)\"/', $resultRaw, $matches);
            if (isset($matches[1]) && $matches[1] != '') {
                $expectedSellerId = $matches[1];
            } else {

                $dataReturn->status=0;
                $dataReturn->message= __("Thông số parram (ESI) không đúng");
                $dataReturn->info=null;
                return $dataReturn;
            }


            ////////////////////////////mua server ////////////////////////////////////////

            $url = "https://games.roblox.com/v1/games/vip-servers/{$universeId}";

            $data = array();
            //$data['__RequestVerificationToken'] =$__RequestVerificationToken;
            //$data['universeId'] =$universeId;
            //$data['privateServerName'] =$privateServerName;
            $data['name'] =$privateServerName;
            //$data['productId'] =$productId;
            //$data['expectedCurrency'] =$expectedCurrency;
            $data['expectedPrice'] =$expectedPrice;
            $data['request_id'] =$request_id;
            //$data['expectedSellerId'] =$expectedSellerId;



            if(is_array($data)){
                $dataPost = http_build_query($data);
            }else{
                $dataPost = $data;
            }



            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
            curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
            curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);

            if(isset($proxy[0]) && $proxy[1]){
                curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
                curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
                if(isset($proxy[2]) && isset($proxy[3])){
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                    curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
                }
            }


            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'x-csrf-token: '.$auth_key,
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json, text/plain, */*',
                'Origin: https://roblox.com',


            ]);
            $resultRaw=curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $result = json_decode($resultRaw);


            //lưu log gọi curl
            $path = storage_path() ."/logs/services-auto/";
            $filename=$path."fire_".Carbon::now()->format('Y-m-d').".txt";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw;
            \File::append( $filename,$contentText."\n");



            if($result){
                //Đơn thành công
                if(isset($result->accessCode)){

                    self::cancelSubscription($result->vipServerId??"",$cookiesSender,$auth_key);
                    $dataReturn->status=1;
                    $dataReturn->message= __("Giao dịch thành công");
                    $dataReturn->info=null;
                    $dataReturn->last_balance_bot=$currencyUser;
                    return $dataReturn;
                }
                //Đơn thất bại
                elseif( isset($result->errors)){

                    //check nếu tài khoản bot hết tiền thì check message trả về cụ thể hơn
                    //if(($result->errors[0]->code??null)==16){
                    //    $dataReturn->status=0;
                    //    $dataReturn->message= "Giao dịch thất bại";
                    //    $dataReturn->info=null;
                    //    return $dataReturn;
                    //}

                    $dataReturn->status=0;
                    $dataReturn->message= __("Giao dịch thất bại.");
                    $dataReturn->info=null;
                    return $dataReturn;
                }
                //trạng thái check thủ công Check thủ công
                else{
                    $dataReturn->status=999;
                    $dataReturn->message= isset($result->errorMsg)?$result->errorMsg:null;
                    $dataReturn->info=null;
                    return $dataReturn;
                }
            }
            else{
                $dataReturn->status=999;
                $dataReturn->message= __("Không có phản hồi từ máy chủ.Check thủ công");
                $dataReturn->info=null;
                return $dataReturn;
            }

        }
        catch (\Exception $e){
            $dataReturn = new \stdClass();
            $dataReturn->status=999;
            $dataReturn->message= __("Lỗi hệ thống xử lý.Check thủ công");
            $dataReturn->info=null;
            return $dataReturn;
        }
    }

    public static  function cancelSubscription($PrivateServerId,$cookiesSender,$auth_key){

        for ($i=0;$i<3;$i++){

            $url = "https://games.roblox.com/v1/vip-servers/{$PrivateServerId}/subscription";
            $data = array();
            $data['active'] =false;
            $dataPost = json_encode($data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url );
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
            curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
            curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'X-CSRF-TOKEN: '.$auth_key,
                'Content-Type: application/json;charset=UTF-8',

            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);

            $resultRaw=curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $result = json_decode($resultRaw);
            if($httpcode==200 && isset($result) && $result->active==false ){
                break;
            }
        }
    }

    public static function detectLink($url){

        $server_id="";
        if(strpos($url,"https://www.roblox.com/games")>-1){
            preg_match('/\/(\d+?)\//', $url, $matches);

            if (isset($matches[1]) && $matches[1] != '') {
                $server_id=$matches[1];
            }
        }
        else{
            if(is_numeric($url)){
                $server_id=$url;
            }
        }

        return $server_id;
    }

    //https://economy.roblox.com/v1/users/712980913/currency

    public static function getCurrencyUser($user_id,$cookiesSender){

        $ch = curl_init();
        //data dạng get
        $url="https://economy.roblox.com/v1/users/{$user_id}/currency";
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json;charset=UTF-8',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result=json_decode($resultRaw);

        if(isset($result->robux))
        {
            return $result->robux;
        }
        else
        {
            return null;
        }
    }

    public static function checkLiveAndBalanceBot($cookiesSender,$proxyCustom=null){

        if($proxyCustom==null){
            $proxy=explode("|",self::$proxyList[array_rand(self::$proxyList)]);
        }
        elseif($proxyCustom!=""){
            $proxy=explode("|",$proxyCustom);
        }
        else{
            $proxy="";
        }

        try {

            $dataReturn = new \stdClass();
            //check thông tin số roblox của server cần mua
            $url = "https://www.roblox.com/home";

            //$url = "https://www.roblox.com/home";

            $ch = curl_init();
            //data dạng get
            curl_setopt($ch, CURLOPT_URL, $url );
            curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
            curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);

            if(isset($proxy[0]) && $proxy[1]){
                curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
                curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
                if(isset($proxy[2]) && isset($proxy[3])){
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                    curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
                }
            }
            $resultRaw=curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            //lưu log gọi curl
//            $path = storage_path() ."/logs/services-auto/";
//            $filename=$path."fire_check_live_bot_roblox".Carbon::now()->format('Y-m-d').".txt";
//            if(!\File::exists($path)){
//                \File::makeDirectory($path, $mode = "0755", true, true);
//            }
//            $contentText = Carbon::now()." :".$url." [" .$httpcode."] -" ." : ".$resultRaw;
//            \File::append( $filename,$contentText."\n");



            preg_match('/data-displayName=(.*?)\s/', $resultRaw, $matches);

            if (isset($matches[1]) && $matches[1] != '') {
                $username_roblox=$matches[1]."";
            } else {

                $dataReturn->status=2;
                $dataReturn->message= __("Nick bot chưa đăng nhập 5");
                $dataReturn->info=null;

                //lưu log gọi curl
//                $path = storage_path() ."/logs/services-auto/";
//                $filename=$path."fire_gamepass_checkcooki".Carbon::now()->format('Y-m-d').".txt";
//                if(!\File::exists($path)){
//                    \File::makeDirectory($path, $mode = "0755", true, true);
//                }
//                $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($dataReturn) ."] : ".$resultRaw;
//                \File::append( $filename,$contentText."\n");

                return $dataReturn;
            }


            preg_match('/data-userid=\"(.*?)\"/', $resultRaw, $matches);

            if (isset($matches[1]) && $matches[1] != '') {
                $data_userid = $matches[1]."";

            } else {

                $dataReturn->status=2;
                $dataReturn->message= __("Nick bot chưa đăng nhập 6");
                $dataReturn->info=null;

                //lưu log gọi curl
//                $path = storage_path() ."/logs/services-auto/";
//                $filename=$path."fire_gamepass_checkcooki".Carbon::now()->format('Y-m-d').".txt";
//                if(!\File::exists($path)){
//                    \File::makeDirectory($path, $mode = "0755", true, true);
//                }
//                $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($dataReturn) ."] : ".$resultRaw;
//                \File::append( $filename,$contentText."\n");

                return $dataReturn;
            }



            preg_match('/data-token=\"(.*?)\"/', $resultRaw, $matches);

            if (isset($matches[1]) && $matches[1] != '') {
                $auth_key = $matches[1]."";
                $auth_key=str_replace( "\"","",$auth_key);
            } else {

                $dataReturn->status=2;
                $dataReturn->message= __("Nick bot chưa đăng nhập 7");
                $dataReturn->info=null;

                //lưu log gọi curl
/*                $path = storage_path() ."/logs/services-auto/";
                $filename=$path."fire_gamepass_checkcooki".Carbon::now()->format('Y-m-d').".txt";
                if(!\File::exists($path)){
                    \File::makeDirectory($path, $mode = "0755", true, true);
                }
                $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($dataReturn) ."] : ".$resultRaw;
                \File::append( $filename,$contentText."\n");*/

                return $dataReturn;
            }

            preg_match('/data-userid=\"(.*?)\"/', $resultRaw, $matches);

            $user_id=null;

            if (isset($matches[1]) && $matches[1] != '') {
                $user_id=$matches[1]."";
            } else {

                $dataReturn->status=2;
                $dataReturn->message= __("Nick bot chưa đăng nhập 8");
                $dataReturn->info=null;

                //lưu log gọi curl
//                $path = storage_path() ."/logs/services-auto/";
//                $filename=$path."fire_gamepass_checkcooki".Carbon::now()->format('Y-m-d').".txt";
//                if(!\File::exists($path)){
//                    \File::makeDirectory($path, $mode = "0755", true, true);
//                }
//                $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($dataReturn) ."] : ".$resultRaw;
//                \File::append( $filename,$contentText."\n");

                return $dataReturn;
            }


            if (!isset($user_id) || $user_id == ''){
                $dataReturn->status=2;
                $dataReturn->message= __("Nick bot chưa đăng nhập 9");
                $dataReturn->info=null;

                //lưu log gọi curl
//                $path = storage_path() ."/logs/services-auto/";
//                $filename=$path."fire_gamepass_checkcooki".Carbon::now()->format('Y-m-d').".txt";
//                if(!\File::exists($path)){
//                    \File::makeDirectory($path, $mode = "0755", true, true);
//                }
//                $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($dataReturn) ."] : ".$resultRaw;
//                \File::append( $filename,$contentText."\n");

                return $dataReturn;
            }


            //////////////////////check balance bot//////////////////////

            $ch = curl_init();
            //data dạng get
            $url="https://economy.roblox.com/v1/users/{$user_id}/currency";
            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
            curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json;charset=UTF-8',
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);

            if(isset($proxy[0]) && $proxy[1]){
                curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
                curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
                if(isset($proxy[2]) && isset($proxy[3])){
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                    curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
                }
            }

            $resultRaw=curl_exec($ch);

            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result=json_decode($resultRaw);

            if($result && isset($result->robux))
            {
                $currencyUser= $result->robux;
            }
            else
            {
                //die
                $dataReturn->status=2;
                $dataReturn->message= __("Nick bot chưa đăng nhập 10");
                $dataReturn->info=null;

                //lưu log gọi curl
//                $path = storage_path() ."/logs/services-auto/";
//                $filename=$path."fire_gamepass_checkcooki".Carbon::now()->format('Y-m-d').".txt";
//                if(!\File::exists($path)){
//                    \File::makeDirectory($path, $mode = "0755", true, true);
//                }
//                $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($dataReturn) ."] : ".$resultRaw;
//                \File::append( $filename,$contentText."\n");

                return $dataReturn;
            }


            if($currencyUser!==null){

                //live
                $dataReturn->status=1;
                $dataReturn->balance=$currencyUser;
                $dataReturn->auth_key=$auth_key;
            }
            else{
                //die
                $dataReturn->status=0;
                $dataReturn->balance=null;

            }

            return $dataReturn;
        }
        catch (\Exception $e){
            \Log::error( $e);
            return null;
        }
    }

    public static function detectUsernameRoblox($server_id,$proxy=null,$cookiesSender=null){

        if($proxy==null){
            $proxy=explode("|",self::$proxyList[array_rand(self::$proxyList)]);
        }
        elseif($proxy!=""){
            $proxy=explode("|",$proxy);
        }


        $dataReturn = new \stdClass();
        //search user
        $url = "https://www.roblox.com/search/users/results?keyword={$server_id}&maxRows=12&startIndex=0";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");

        if (isset($cookiesSender)){
            curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }


        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result=json_decode($resultRaw);

        $user_id=null;
        if(isset($result->UserSearchResults)){

            foreach ($result->UserSearchResults??[] as $aSearch){

                if($aSearch->Name==$server_id){
                    $dataReturn->status=1;
                    $dataReturn->user_id=$aSearch->UserId;
                    return $dataReturn;
                }

            }
            if($user_id==null){
                $dataReturn->status=0;
                $dataReturn->message= __("Không tìm thấy tên tài khoản roblox");
                return $dataReturn;
            }

        }
        else{
            $dataReturn->status=0;
            $dataReturn->message= __("Không tìm thấy tên tài khoản");
            return $dataReturn;
        }


    }

    public static function detectUsernameRobloxV2($server_id,$amount,$proxy=null,$cookiesSender=null){

        if($proxy==null){
            $proxy=explode("|",self::$proxyList[array_rand(self::$proxyList)]);
        }
        elseif($proxy!=""){
            $proxy=explode("|",$proxy);
        }

        //Log tìm kiếm user
//        $myfile = fopen(storage_path() . "/logs/curl_detect_user_name_roblox_data-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
//        $txt = Carbon::now() . " :" . json_encode($server_id)." - ".json_encode($amount)." - " .json_encode($proxy). " - ". json_encode($cookiesSender) . "\r\n";
//        fwrite($myfile, $txt);
//        fclose($myfile);

        //Cach 1

        $dataReturn = new \stdClass();
        //search user

        $url = 'https://www.roblox.com/users/profile?username='.$server_id;

        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");

        if (isset($cookiesSender)){
            curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }


        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result=json_decode($resultRaw);

//        $myfile = fopen(storage_path() . "/logs/curl_detect_user_name_roblox_search_user-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
//        $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
//        fwrite($myfile, $txt);
//        fclose($myfile);

        $user_id= '';

        preg_match('/data-profileuserid=\"(.*?)\"/', $resultRaw, $matches);

        if (isset($matches[1]) && $matches[1] != '') {
            $user_id = $matches[1];
        } else {

            $dataReturn->status=0;
            $dataReturn->message= "Không tìm thấy tên tài khoản";
            return $dataReturn;
        }

        if(!isset($user_id) || $user_id == ''){
            $dataReturn->status=0;
            $dataReturn->message="Không tìm thấy tên tài khoản roblox";
            return $dataReturn;
        }

        //Cach 2
//        $dataReturn = new \stdClass();
//        //search user
//        $url = "https://www.roblox.com/search/users/results?keyword={$server_id}&maxRows=12&startIndex=0";
//        $ch = curl_init();
//        //data dạng get
//        curl_setopt($ch, CURLOPT_URL, $url );
//        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
//
//        if (isset($cookiesSender)){
//            curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
//        }
//
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
//
//        if(isset($proxy[0]) && $proxy[1]){
//            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
//            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
//            if(isset($proxy[2]) && isset($proxy[3])){
//                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
//                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
//            }
//        }
//
//
//        $resultRaw=curl_exec($ch);
//        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//
//        //Log tìm kiếm user
//        $myfile = fopen(storage_path() . "/logs/curl_detect_user_name_roblox_search_user-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
//        $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
//        fwrite($myfile, $txt);
//        fclose($myfile);
//
//        curl_close($ch);
//        $result=json_decode($resultRaw);
//
//        $user_id= '';
//
//        if(empty($result->UserSearchResults) || count($result->UserSearchResults) ==0){
//            $dataReturn->status=0;
//            $dataReturn->message= __("Không tìm thấy tên tài khoản");
//            return $dataReturn;
//        }
//
//        foreach ($result->UserSearchResults??[] as $aSearch){
//
//            if($aSearch->Name==$server_id) {
//                $user_id = $aSearch->UserId;
//                break;
//            }
//        }
//
//        if(!isset($user_id) || $user_id == ''){
//            $dataReturn->status=0;
//            $dataReturn->message= __("Không tìm thấy tên tài khoản roblox");
//            return $dataReturn;
//        }

        //Lấy thông tin game pass cần mua
        $dataReturn = new \stdClass();
        $url = "https://games.roblox.com/v2/users/{$user_id}/games?accessFilter=Public&limit=50";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }


        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        $result=json_decode($resultRaw);

        //Log tìm kiếm user
//        $myfile = fopen(storage_path() . "/logs/curl_detect_user_name_roblox_search_palce_id-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
//        $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
//        fwrite($myfile, $txt);
//        fclose($myfile);

//        if($httpcode==200){}
//        else{
//            $dataReturn->status= 2;
//            $dataReturn->message= "Tìm kiếm tài khoản Place ID thất bại";
//            return $dataReturn;
//        }

        if(isset($result->data[0]->rootPlace->id)){
            $placeID= $result->data[0]->rootPlace->id;
        }
        else{
            $dataReturn->status=0;
            $dataReturn->message = __("Không tìm thấy Place ID");
            return $dataReturn;
        }

        //Lấy thông tin game pass cần mua
        $dataReturn = new \stdClass();
        $url = "https://www.roblox.com/games/getgamepassesinnerpartial?startIndex=0&maxRows=50&placeId={$placeID}";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);


        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }

        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        //Log tìm kiếm user
//        $myfile = fopen(storage_path() . "/logs/curl_detect_user_name_roblox_search_gamepass-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
//        $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
//        fwrite($myfile, $txt);
//        fclose($myfile);

//        if($httpcode==200){}
//        else{
//            $dataReturn->status= 2;
//            $dataReturn->message= "Tìm kiếm tài khoản gamepass thất bại";
//            return $dataReturn;
//        }

        preg_match('/data-expected-seller-id=\"(.*?)\"/', $resultRaw, $matches);

        if (isset($matches[1]) && $matches[1] != '') {} else {

            $dataReturn->status=0;
            $dataReturn->message= __("Tài khoản chưa tạo game pass");
            return $dataReturn;
        }

        preg_match_all('/data-expected-price=\"(.*?)\"/', $resultRaw, $matches);

        if (isset($matches[1]) && $matches[1] != '') {

            //check số robux order với robux gamepass trong game
            $checkAmountWithOrder=false;
            $rGamepass = '';
            foreach ($matches[1]??[] as $index=> $rubuxNumber){
                if ($index == 0){
                    $rGamepass = $rubuxNumber;
                }else{
                    $rGamepass = $rGamepass.','.$rubuxNumber;
                }

                if(intval($rubuxNumber)==$amount){
                    $checkAmountWithOrder=true;
                    $indexBuyGamePass=$index;
                    $dataReturn->status=1;
                    $dataReturn->user_id= $user_id;
                    $dataReturn->message= __('Kiểm tra thành công');
                    break;
                }
            }

            if($checkAmountWithOrder==false){
                $dataReturn->status=0;
                $dataReturn->message= __('Số Robux mua là '.$amount.' không khớp với robux của gamepass là: '.$rGamepass);
                return $dataReturn;
            }
        } else {

            $dataReturn->status=2;
            $dataReturn->message= __("Nick bot chưa đăng nhập");
            return $dataReturn;
        }

        return $dataReturn;
    }

    public static function detectUsernameRobloxNew($server_id,$amount,$proxy=null,$cookiesSender=null){

        if($proxy==null){
            $proxy=explode("|",self::$proxyList[array_rand(self::$proxyList)]);
        }
        elseif($proxy!=""){
            $proxy=explode("|",$proxy);
        }

        //Cach 1

        $dataReturn = new \stdClass();
        //search user

        $url = 'https://www.roblox.com/users/profile?username='.$server_id;

        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");

        if (isset($cookiesSender)){
            curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }


        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result=json_decode($resultRaw);

        $user_id= '';

        preg_match('/data-profileuserid=\"(.*?)\"/', $resultRaw, $matches);

        if (isset($matches[1]) && $matches[1] != '') {
            $user_id = $matches[1];
        } else {

            $dataReturn->status=0;
            $dataReturn->message= "Không tìm thấy tên tài khoản";
            return $dataReturn;
        }

        if(!isset($user_id) || $user_id == ''){
            $dataReturn->status=0;
            $dataReturn->message="Không tìm thấy tên tài khoản roblox";
            return $dataReturn;
        }

        $dataReturn->status=1;
        $dataReturn->user_id= $user_id;
        $dataReturn->message= __('Kiểm tra thành công');

        return $dataReturn;
    }

    public static function detectUsernameRobloxV3($server_id,$amount,$proxy=null,$cookiesSender=null){

        if($proxy==null){
            $proxy=explode("|",self::$proxyList[array_rand(self::$proxyList)]);
        }
        elseif($proxy!=""){
            $proxy=explode("|",$proxy);
        }

        $dataReturn = new \stdClass();
        //search user

        $url = 'https://www.roblox.com/users/profile?username='.$server_id;

        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");

        if (isset($cookiesSender)){
            curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }


        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result=json_decode($resultRaw);

        $user_id= '';

        preg_match('/data-profileuserid=\"(.*?)\"/', $resultRaw, $matches);

        if (isset($matches[1]) && $matches[1] != '') {
            $user_id = $matches[1];
        } else {

            $dataReturn->status=0;
            $dataReturn->message= "Không tìm thấy tên tài khoản";
            return $dataReturn;
        }

        if(!isset($user_id) || $user_id == ''){
            $dataReturn->status=0;
            $dataReturn->message="Không tìm thấy tên tài khoản roblox";
            return $dataReturn;
        }

        //Lấy thông tin game pass cần mua
        $dataReturn = new \stdClass();
        $url = "https://games.roblox.com/v2/users/{$user_id}/games?accessFilter=Public&limit=50";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);


        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }


        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result=json_decode($resultRaw);

        if(isset($result->data[0]->rootPlace->id)){
            $placeID= $result->data[0]->rootPlace->id;
        }
        else{
            $dataReturn->status=0;
            $dataReturn->message ="Không tìm thấy Place ID";
            return $dataReturn;
        }

        //Lấy thông tin game pass cần mua
        $dataReturn = new \stdClass();
        $url = "https://www.roblox.com/games/getgamepassesinnerpartial?startIndex=0&maxRows=50&placeId={$placeID}";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);


        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }

        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        preg_match('/data-expected-seller-id=\"(.*?)\"/', $resultRaw, $matches);

        if (isset($matches[1]) && $matches[1] != '') {} else {

            $dataReturn->status=0;
            $dataReturn->message= "Tài khoản chưa tạo game pass";
            return $dataReturn;
        }

        preg_match_all('/data-expected-price=\"(.*?)\"/', $resultRaw, $matches);
//        return $matches;
        if (isset($matches[1]) && $matches[1] != '') {

            //check số robux order với robux gamepass trong game
            $checkAmountWithOrder=false;
            $rGamepass = '';

            foreach ($matches[1]??[] as $index=> $rubuxNumber){

                if ($index == 0){
                    $rGamepass = $rubuxNumber;
                }else{
                    $rGamepass = $rGamepass.','.$rubuxNumber;
                }

                if(intval($rubuxNumber)==$amount){
                    $checkAmountWithOrder=true;
                    $indexBuyGamePass=$index;
                    $dataReturn->status=1;
                    $dataReturn->user_id= $user_id;
                    $dataReturn->message= 'Kiểm tra thành công';
                    break;
                }
            }

            if($checkAmountWithOrder==false){
                $dataReturn->status=0;
                $dataReturn->message= 'Số Robux mua là '.$amount.' không khớp với robux của gamepass là: '.$rGamepass;
                return $dataReturn;
            }
        } else {

            $dataReturn->status=2;
            $dataReturn->message= "Nick bot chưa đăng nhập";
            return $dataReturn;
        }

        return $dataReturn;
    }

    public static function GetTransactions($server_id,$cookiesSender,$proxyCustomString=null){

        //Tạo lệnh bắn
        $dataReturn = new \stdClass();
        $url = "https://economy.roblox.com/v2/users/{$server_id}/transactions?cursor=&limit=100&transactionType=Purchase&itemPricingType=All";

        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");

        if (isset($cookiesSender)){
            curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }

        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //Log tìm kiếm user
        $myfile = fopen(storage_path() . "/logs/curl_detect_user_name_roblox_search_user-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
        $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
        fwrite($myfile, $txt);
        fclose($myfile);

        curl_close($ch);
        $result=json_decode($resultRaw);

        return $result;
    }

    public static function detectUserIdRoblox($server_id,$proxy=null,$cookiesSender=null){
        if($proxy==null){
            $proxy=explode("|",self::$proxyList[array_rand(self::$proxyList)]);
        }
        elseif($proxy!=""){
            $proxy=explode("|",$proxy);
        }

        $dataReturn = new \stdClass();
        //search user

        $url = 'https://www.roblox.com/users/profile?username='.$server_id;

        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");

        if (isset($cookiesSender)){
            curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }


        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result=json_decode($resultRaw);

        $user_id= '';

        preg_match('/data-profileuserid=\"(.*?)\"/', $resultRaw, $matches);

        if (isset($matches[1]) && $matches[1] != '') {
            $user_id = $matches[1];
        } else {

            $dataReturn->status=0;
            $dataReturn->message= "Không tìm thấy tên tài khoản";
            return $dataReturn;
        }

        if(!isset($user_id) || $user_id == ''){
            $dataReturn->status=0;
            $dataReturn->message="Không tìm thấy tên tài khoản roblox";
            return $dataReturn;
        }

        $dataReturn->status=1;
        $dataReturn->user_id=$user_id;
        $dataReturn->message="Lấy user id thành công";

        return $dataReturn;

    }

    public static function ProcessBuyGamePass($server_id,$amount,$cookiesSender,$request_id,$proxyCustomString=null){

        if($proxyCustomString==null){
            $proxyCustomString=self::$proxyList[array_rand(self::$proxyList)];
            $proxy=explode("|",$proxyCustomString);
        }
        elseif($proxyCustomString!=""){
            $proxy=explode("|",$proxyCustomString);
        }
        else{
            $proxy="";
        }

        $dataReturn = new \stdClass();
        //check live bot
        $result=self::checkLiveAndBalanceBot($cookiesSender,$proxyCustomString);

        if($result &&  $result->status==1){
            $currencyUser=$result->balance;
            $auth_key=$result->auth_key;
        }
        else{
            $dataReturn->status=2;
            $dataReturn->message= __("Nick bot chưa đăng nhập 7");
            $dataReturn->info=null;
            return $dataReturn;
        }

        if((int)$currencyUser<$amount){
            $dataReturn->status=33;
            $dataReturn->currencyUser=$currencyUser;
            $dataReturn->message= __("Bot không đủ số dư để thực hiện order");
            $dataReturn->info=null;
            return $dataReturn;
        }
        //search user
        $user_id=null;

        //search user
        $result=self::detectUsernameRobloxV2($server_id,$amount,null,$cookiesSender??'');

        //Lưu log
//        $path = storage_path() ."/logs/service-auto-check-cookies/";
//        if(!\File::exists($path)){
//            \File::makeDirectory($path, $mode = "0755", true, true);
//        }
//        $txt = Carbon::now().": ".json_encode($result,JSON_UNESCAPED_UNICODE);
//        \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

//        return $result;
        if($result &&  $result->status==1){
            $user_id= $result->user_id??'';
        }
        else{
            $dataReturn->status=0;
            $dataReturn->message= __($result->message)??__("Không tìm thấy tên tài khoản");
            return $dataReturn;
        }

        if (!isset($user_id) || $user_id == ''){
            $dataReturn->status=0;
            $dataReturn->message=__("Không tìm thấy tên tài khoản");
            return $dataReturn;
        }


        //Lấy thông tin game pass cần mua
        $dataReturn = new \stdClass();
        $url = "https://games.roblox.com/v2/users/{$user_id}/games?accessFilter=Public&limit=50";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);


        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }


        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result=json_decode($resultRaw);


        if(isset($result->data[0]->rootPlace->id)){
            $placeID= $result->data[0]->rootPlace->id;
        }
        else{
            $dataReturn->status=0;
            $dataReturn->message=__("Không tìm thấy Place ID");
            return $dataReturn;
        }


        if (!isset($placeID) || $placeID == ''){
            $dataReturn->status=0;
            $dataReturn->message=__("Không tìm thấy Place ID");
            return $dataReturn;
        }


        //Lấy thông tin game pass cần mua
        $dataReturn = new \stdClass();
        $url = "https://www.roblox.com/games/getgamepassesinnerpartial?startIndex=0&maxRows=50&placeId={$placeID}";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);


        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }

        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        preg_match('/data-expected-seller-id=\"(.*?)\"/', $resultRaw, $matches);

        if (isset($matches[1]) && $matches[1] != '') {
            $seller_id=$matches[1]."";

        } else {

            $dataReturn->status=0;
            $dataReturn->message= __("Tài khoản chưa tạo game pass");
            $dataReturn->info=null;
            return $dataReturn;
        }


        preg_match_all('/data-expected-price=\"(.*?)\"/', $resultRaw, $matches);

        $indexBuyGamePass=null;
        $rGamepass = '';
        if (isset($matches[1]) && $matches[1] != '') {

            //check số robux order với robux gamepass trong game
            $checkAmountWithOrder=false;
            foreach ($matches[1]??[] as $index=> $rubuxNumber){

                if ($index == 0){
                    $rGamepass = $rubuxNumber;
                }else{
                    $rGamepass = $rGamepass.','.$rubuxNumber;
                }

                if(intval($rubuxNumber)==$amount){
                    $checkAmountWithOrder=true;
                    $indexBuyGamePass=$index;
                    break;
                }
            }

            if($checkAmountWithOrder==false){
                $dataReturn->status=0;
                $dataReturn->message= __('Số Robux mua là '.$amount.' không khớp với robux của gamepass là: '.$rGamepass);
                $dataReturn->info=null;
                return $dataReturn;
            }
        } else {

            $dataReturn->status=2;
            $dataReturn->message= __("Nick bot chưa đăng nhập 8");
            $dataReturn->info=null;

            //lưu log gọi curl
//            $path = storage_path() ."/logs/services-auto/";
//            $filename=$path."fire_gamepass_checkcooki".Carbon::now()->format('Y-m-d').".txt";
//            if(!\File::exists($path)){
//                \File::makeDirectory($path, $mode = "0755", true, true);
//            }
//            $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($dataReturn) ."] : ".$resultRaw;
//            \File::append( $filename,$contentText."\n");

            return $dataReturn;
        }


        //tìm product_id
        preg_match_all('/data-product-id=\"(.*?)\"/', $resultRaw, $matches);


        if (isset($matches[1]) && $matches[1] != '') {
            $produdctID=$matches[1][$indexBuyGamePass];

        } else {

            $dataReturn->status=2;
            $dataReturn->message= __("Nick bot chưa đăng nhập 9");
            $dataReturn->info=null;

            //lưu log gọi curl
//            $path = storage_path() ."/logs/services-auto/";
//            $filename=$path."fire_gamepass_checkcooki".Carbon::now()->format('Y-m-d').".txt";
//            if(!\File::exists($path)){
//                \File::makeDirectory($path, $mode = "0755", true, true);
//            }
//            $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($dataReturn) ."] : ".$resultRaw;
//            \File::append( $filename,$contentText."\n");

            return $dataReturn;
        }


        //Tạo lệnh bắn
        $dataReturn = new \stdClass();
        $url = "https://economy.roblox.com/v1/purchases/products/{$produdctID}";

        $data = array();
        $data['expectedCurrency'] =1;
        $data['expectedPrice'] =$amount;
        $data['expectedSellerId'] =$seller_id;
        $data['request_id'] =$request_id;

        if(is_array($data)){
            $dataPost = http_build_query($data);
        }else{
            $dataPost = $data;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Requested-With' => 'XMLHttpRequest',
            'x-csrf-token: '.$auth_key,
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json, text/plain, */*',
            'Origin: https://roblox.com',
        ]);
        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = json_decode($resultRaw);

        //lưu log gọi curl
//        $path = storage_path() ."/logs/services-auto/";
//        $filename=$path."fire_buy_product_roblox".Carbon::now()->format('Y-m-d').".txt";
//        if(!\File::exists($path)){
//            \File::makeDirectory($path, $mode = "0755", true, true);
//        }
//        if(isset($proxy[0]) && $proxy[1]){
//            $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw." - proxy: ".$proxy[0];
//        }else{
//            $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw;
//        }
//
//        \File::append( $filename,$contentText."\n");

        if(isset($result) && isset($result->purchased)){

            //Đơn thành công
            if($result->purchased==true){

                $dataReturn->status=1;
                $dataReturn->message= __("Giao dịch thành công");
                $dataReturn->info=null;
                $dataReturn->last_balance_bot=$currencyUser-$amount;
                return $dataReturn;
            }
            //Đơn thất bại
            else{


                $dataReturn->status=0;
                $dataReturn->message= __("Giao dịch thất bại");
                $dataReturn->info=null;
                return $dataReturn;
            }
        }
        //trạng thái check thủ công Check thủ công
        else{

            //lưu log error gọi curl
            $path = storage_path() ."/logs/services-auto/";
            $filename=$path."fire_buy_product_error_roblox".Carbon::now()->format('Y-m-d').".txt";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw;
            \File::append( $filename,$contentText."\n");

            $dataReturn->status=999;
            $dataReturn->message= isset($result->errorMsg)?$result->errorMsg:null;
            $dataReturn->info=null;
            return $dataReturn;
        }
    }

    public static function ProcessBuyGamePassV2($server_id,$account,$amount,$cookiesSender,$request_id,$proxyCustomString=null){

        if($proxyCustomString==null){
            $proxyCustomString=self::$proxyList[array_rand(self::$proxyList)];
            $proxy=explode("|",$proxyCustomString);
        }
        elseif($proxyCustomString!=""){
            $proxy=explode("|",$proxyCustomString);
        }
        else{
            $proxy="";
        }

        $dataReturn = new \stdClass();
        //check live bot
        $result=self::checkLiveAndBalanceBot($cookiesSender,$proxyCustomString);

        if($result &&  $result->status==1){
            $currencyUser=$result->balance;
            $auth_key=$result->auth_key;
        }
        else{
            $dataReturn->status=2;
            $dataReturn->message= __("Nick bot chưa đăng nhập");
            $dataReturn->info=null;
            return $dataReturn;
        }

        if((int)$currencyUser<$amount){
            $dataReturn->status=33;
            $dataReturn->currencyUser=$currencyUser;
            $dataReturn->message= __("Bot không đủ số dư để thực hiện order");
            $dataReturn->info=null;
            return $dataReturn;
        }

        $dataReturn = new \stdClass();
        //search user
        $url = "https://www.roblox.com/search/users/results?keyword={$account}&maxRows=12&startIndex=0";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");

        if (isset($cookiesSender)){
            curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }

        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //Log tìm kiếm user
        $myfile = fopen(storage_path() . "/logs/curl_detect_user_name_roblox_search_user-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
        $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
        fwrite($myfile, $txt);
        fclose($myfile);

        curl_close($ch);
        $result=json_decode($resultRaw);

        $check_user_id= '';

        if(empty($result->UserSearchResults) || count($result->UserSearchResults) ==0){
            $dataReturn->status=0;
            $dataReturn->message= __("Không tìm thấy tên tài khoản");
            return $dataReturn;
        }

        foreach ($result->UserSearchResults??[] as $aSearch){

            if($aSearch->Name==$account) {
                $check_user_id = $aSearch->UserId;
                break;
            }
        }

        if(!isset($check_user_id) || $check_user_id == ''){
            $dataReturn->status=0;
            $dataReturn->message= __("Không tìm thấy tên tài khoản roblox");
            return $dataReturn;
        }

        if ($check_user_id === $server_id){}else{
            $dataReturn->status=0;
            $dataReturn->message= __("Id user không đúng");
            return $dataReturn;
        }

        //Lấy thông tin game pass cần mua
        $dataReturn = new \stdClass();
        $url = "https://games.roblox.com/v2/users/{$server_id}/games?accessFilter=Public&limit=50";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);


        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }


        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result=json_decode($resultRaw);


        if(isset($result->data[0]->rootPlace->id)){
            $placeID= $result->data[0]->rootPlace->id;
        }
        else{
            $dataReturn->status=0;
            $dataReturn->message=__("Không tìm thấy Place ID");
            return $dataReturn;
        }


        if (!isset($placeID) || $placeID == ''){
            $dataReturn->status=0;
            $dataReturn->message=__("Không tìm thấy Place ID");
            return $dataReturn;
        }


        //Lấy thông tin game pass cần mua
        $dataReturn = new \stdClass();
        $url = "https://www.roblox.com/games/getgamepassesinnerpartial?startIndex=0&maxRows=50&placeId={$placeID}";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);


        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }

        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        preg_match('/data-expected-seller-id=\"(.*?)\"/', $resultRaw, $matches);

        if (isset($matches[1]) && $matches[1] != '') {
            $seller_id=$matches[1]."";

        } else {

            $dataReturn->status=0;
            $dataReturn->message= __("Tài khoản chưa tạo game pass");
            $dataReturn->info=null;
            return $dataReturn;
        }


        preg_match_all('/data-expected-price=\"(.*?)\"/', $resultRaw, $matches);

        $indexBuyGamePass=null;
        $rGamepass = '';
        if (isset($matches[1]) && $matches[1] != '') {

            //check số robux order với robux gamepass trong game
            $checkAmountWithOrder=false;
            foreach ($matches[1]??[] as $index=> $rubuxNumber){

                if ($index == 0){
                    $rGamepass = $rubuxNumber;
                }else{
                    $rGamepass = $rGamepass.','.$rubuxNumber;
                }

                if(intval($rubuxNumber)==$amount){
                    $checkAmountWithOrder=true;
                    $indexBuyGamePass=$index;
                    break;
                }
            }

            if($checkAmountWithOrder==false){
                $dataReturn->status=0;
                $dataReturn->message= __('Số Robux mua là '.$amount.' không khớp với robux của gamepass là: '.$rGamepass);
                $dataReturn->info=null;
                return $dataReturn;
            }
        } else {

            $dataReturn->status=2;
            $dataReturn->message= __("Nick bot chưa đăng nhập");
            $dataReturn->info=null;
            return $dataReturn;
        }


        //tìm product_id
        preg_match_all('/data-product-id=\"(.*?)\"/', $resultRaw, $matches);


        if (isset($matches[1]) && $matches[1] != '') {
            $produdctID=$matches[1][$indexBuyGamePass];

        } else {

            $dataReturn->status=2;
            $dataReturn->message= __("Nick bot chưa đăng nhập");
            $dataReturn->info=null;
            return $dataReturn;
        }


        //Tạo lệnh bắn
        $dataReturn = new \stdClass();
        $url = "https://economy.roblox.com/v1/purchases/products/{$produdctID}";

        $data = array();
        $data['expectedCurrency'] =1;
        $data['expectedPrice'] =$amount;
        $data['expectedSellerId'] =$seller_id;
        $data['request_id'] =$request_id;

        if(is_array($data)){
            $dataPost = http_build_query($data);
        }else{
            $dataPost = $data;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Requested-With' => 'XMLHttpRequest',
            'x-csrf-token: '.$auth_key,
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json, text/plain, */*',
            'Origin: https://roblox.com',
        ]);

        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = json_decode($resultRaw);

        //lưu log gọi curl
        $path = storage_path() ."/logs/services-auto/";
        $filename=$path."fire_".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw;
        \File::append( $filename,$contentText."\n");


        if(isset($result) && isset($result->purchased)){

            //Đơn thành công
            if($result->purchased==true){


                $dataReturn->status=1;
                $dataReturn->message= __("Giao dịch thành công");
                $dataReturn->info=null;
                $dataReturn->last_balance_bot=$currencyUser-$amount;
                return $dataReturn;
            }
            //Đơn thất bại
            else{
                $dataReturn->status=0;
                $dataReturn->message= __("Giao dịch thất bại");
                $dataReturn->info=null;
                return $dataReturn;
            }
        }
        //trạng thái check thủ công Check thủ công
        else{

//            if (strpos($resultRaw, 'Token Validation Failed') !== false) {
//                // Xử lý thông báo lỗi
//                $dataReturn->status = 888; // hoặc mã lỗi bạn muốn đặt
//                $dataReturn->message = 'Token Validation Failed';
//                $dataReturn->info = null;
//                return $dataReturn;
//            }

            //Lưu log
            $path = storage_path() ."/logs/service-auto-check-proxy/";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $txt = Carbon::now().": ".json_encode($result,JSON_UNESCAPED_UNICODE).'-'.json_encode($proxy,JSON_UNESCAPED_UNICODE);
            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

            $dataReturn->status=999;
            $dataReturn->message= isset($result->errorMsg)?$result->errorMsg:null;
            $dataReturn->info=null;
            return $dataReturn;
        }
    }

    public static function detectUserId($server_id,$amount,$proxy=null,$cookiesSender=null){

        if($proxy==null){
            $proxy=explode("|",self::$proxyList[array_rand(self::$proxyList)]);
        }
        elseif($proxy!=""){
            $proxy=explode("|",$proxy);
        }

        //Log tìm kiếm user
//        $myfile = fopen(storage_path() . "/logs/curl_detect_user_name_roblox_data-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
//        $txt = Carbon::now() . " :" . json_encode($server_id)." - ".json_encode($amount)." - " .json_encode($proxy). " - ". json_encode($cookiesSender) . "\r\n";
//        fwrite($myfile, $txt);
//        fclose($myfile);

        //Cach 1

        $dataReturn = new \stdClass();
        //search user

        $url = 'https://www.roblox.com/users/profile?username='.$server_id;

        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");

        if (isset($cookiesSender)){
            curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }


        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result=json_decode($resultRaw);

//        $myfile = fopen(storage_path() . "/logs/curl_detect_user_name_roblox_search_user-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
//        $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
//        fwrite($myfile, $txt);
//        fclose($myfile);

        $user_id= '';

        preg_match('/data-profileuserid=\"(.*?)\"/', $resultRaw, $matches);

        if (isset($matches[1]) && $matches[1] != '') {
            $user_id = $matches[1];
            $dataReturn->status=1;
            $dataReturn->message= "Không tìm thấy tên tài khoản";
            $dataReturn->user_id=$user_id;
        } else {

            $dataReturn->status=0;
            $dataReturn->message= "Không tìm thấy tên tài khoản";
            return $dataReturn;
        }

        if(!isset($user_id) || $user_id == ''){
            $dataReturn->status=0;
            $dataReturn->message="Không tìm thấy tên tài khoản roblox";
            return $dataReturn;
        }

        return $dataReturn;
    }

    public static function detectPlaceId($user_id,$amount,$proxy=null,$cookiesSender=null){
        //Lấy thông tin game pass cần mua
        $dataReturn = new \stdClass();
        $url = "https://games.roblox.com/v2/users/{$user_id}/games?accessFilter=Public&limit=50";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }


        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        $result=json_decode($resultRaw);

        //Log tìm kiếm user
//        $myfile = fopen(storage_path() . "/logs/curl_detect_user_name_roblox_search_palce_id-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
//        $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
//        fwrite($myfile, $txt);
//        fclose($myfile);

        if(isset($result->data[0]->rootPlace->id)){
            $placeID= $result->data[0]->rootPlace->id;
            $dataReturn->placeID=$placeID;
            $dataReturn->result=$result;
            $dataReturn->status=1;
            $dataReturn->message = __("Tìm thấy Place ID");
            return $dataReturn;
        }
        else{
            $dataReturn->status=0;
            $dataReturn->result=$result;
            $dataReturn->message = __("Không tìm thấy Place ID");
            return $dataReturn;
        }

    }

    public static function detectGamepass($placeID,$amount,$proxy=null,$cookiesSender=null){

        //Lấy thông tin game pass cần mua
        $dataReturn = new \stdClass();
        $url = "https://www.roblox.com/games/getgamepassesinnerpartial?startIndex=0&maxRows=50&placeId={$placeID}";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);


        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }

        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        //Log tìm kiếm user
//        $myfile = fopen(storage_path() . "/logs/curl_detect_user_name_roblox_search_gamepass-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
//        $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
//        fwrite($myfile, $txt);
//        fclose($myfile);

        preg_match('/data-expected-seller-id=\"(.*?)\"/', $resultRaw, $matches);

        return $resultRaw;

        return $dataReturn;
    }

    public static function ProcessBuyGamePassNew($server_id,$amount,$cookiesSender,$request_id,$proxyCustomString=null,$placeID=null){

        if($proxyCustomString==null){
            $proxyCustomString=self::$proxyList[array_rand(self::$proxyList)];
            $proxy=explode("|",$proxyCustomString);
        }
        elseif($proxyCustomString!=""){
            $proxy=explode("|",$proxyCustomString);
        }
        else{
            $proxy="";
        }

        $dataReturn = new \stdClass();
        //check live bot
        $result=self::checkLiveAndBalanceBot($cookiesSender,$proxyCustomString);

        if($result &&  $result->status==1){
            $currencyUser=$result->balance;
            $auth_key=$result->auth_key;
        }
        else{
            $dataReturn->status=2;
            $dataReturn->message= __("Nick bot chưa đăng nhập 7");
            $dataReturn->info=null;
            return $dataReturn;
        }

        if((int)$currencyUser<$amount){
            $dataReturn->status=33;
            $dataReturn->currencyUser=$currencyUser;
            $dataReturn->message= __("Bot không đủ số dư để thực hiện order");
            $dataReturn->info=null;
            return $dataReturn;
        }
        //search user
        $user_id=null;

        //search user
        $result=self::detectUsernameRobloxNew($server_id,$amount,null,$cookiesSender??'');

        if($result &&  $result->status==1){
            $user_id= $result->user_id??'';
        }
        else{
            $dataReturn->status=0;
            $dataReturn->message= __($result->message)??__("Không tìm thấy tên tài khoản");
            return $dataReturn;
        }

        if (!isset($user_id) || $user_id == ''){
            $dataReturn->status=0;
            $dataReturn->message=__("Không tìm thấy tên tài khoản");
            return $dataReturn;
        }


        //Lấy thông tin game pass cần mua
        $dataReturn = new \stdClass();
        $url = "https://games.roblox.com/v2/users/{$user_id}/games?accessFilter=Public&limit=50";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);


        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }


        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result=json_decode($resultRaw);

        $places = [];
        if(isset($result->data[0]->rootPlace->id)){
            if (!isset($placeID)){
                $placeID= $result->data[0]->rootPlace->id;
            }

            foreach ($result->data??[] as $item){
                $place_id = $item->rootPlace->id;
                array_push($places,$place_id);
            }
        }
        else{
            $dataReturn->status=0;
            $dataReturn->message=__("Không tìm thấy Place ID");
            return $dataReturn;
        }


        if (!isset($placeID) || $placeID == ''){
            $dataReturn->status=0;
            $dataReturn->message=__("Không tìm thấy Place ID");
            return $dataReturn;
        }

        if (!in_array($placeID,$places)){
            $dataReturn->status=0;
            $dataReturn->message=__("Không tìm thấy Place ID");
            return $dataReturn;
        }


        //Lấy thông tin game pass cần mua
        $dataReturn = new \stdClass();
        $url = "https://www.roblox.com/games/getgamepassesinnerpartial?startIndex=0&maxRows=50&placeId={$placeID}";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);


        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }

        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        preg_match('/data-expected-seller-id=\"(.*?)\"/', $resultRaw, $matches);

        if (isset($matches[1]) && $matches[1] != '') {
            $seller_id=$matches[1]."";

        } else {

            $dataReturn->status=0;
            $dataReturn->message= __("Tài khoản chưa tạo game pass");
            $dataReturn->info=null;
            return $dataReturn;
        }


        preg_match_all('/data-expected-price=\"(.*?)\"/', $resultRaw, $matches);

        $indexBuyGamePass=null;
        $rGamepass = '';
        if (isset($matches[1]) && $matches[1] != '') {

            //check số robux order với robux gamepass trong game
            $checkAmountWithOrder=false;
            foreach ($matches[1]??[] as $index=> $rubuxNumber){

                if ($index == 0){
                    $rGamepass = $rubuxNumber;
                }else{
                    $rGamepass = $rGamepass.','.$rubuxNumber;
                }

                if(intval($rubuxNumber)==$amount){
                    $checkAmountWithOrder=true;
                    $indexBuyGamePass=$index;
                    break;
                }
            }

            if($checkAmountWithOrder==false){
                $dataReturn->status=0;
                $dataReturn->message= __('Số Robux mua là '.$amount.' không khớp với robux của gamepass là: '.$rGamepass);
                $dataReturn->info=null;
                return $dataReturn;
            }
        } else {

            $dataReturn->status=2;
            $dataReturn->message= __("Nick bot chưa đăng nhập 8");
            $dataReturn->info=null;

            return $dataReturn;
        }


        //tìm product_id
        preg_match_all('/data-product-id=\"(.*?)\"/', $resultRaw, $matches);


        if (isset($matches[1]) && $matches[1] != '') {
            $produdctID=$matches[1][$indexBuyGamePass];

        } else {

            $dataReturn->status=2;
            $dataReturn->message= __("Nick bot chưa đăng nhập 9");
            $dataReturn->info=null;
            return $dataReturn;
        }


        //Tạo lệnh bắn
        $dataReturn = new \stdClass();
        $url = "https://economy.roblox.com/v1/purchases/products/{$produdctID}";

        $data = array();
        $data['expectedCurrency'] =1;
        $data['expectedPrice'] =$amount;
        $data['expectedSellerId'] =$seller_id;
        $data['request_id'] =$request_id;

        if(is_array($data)){
            $dataPost = http_build_query($data);
        }else{
            $dataPost = $data;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Requested-With' => 'XMLHttpRequest',
            'x-csrf-token: '.$auth_key,
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json, text/plain, */*',
            'Origin: https://roblox.com',
        ]);
        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = json_decode($resultRaw);

        if(isset($result) && isset($result->purchased)){

            //Đơn thành công
            if($result->purchased==true){

                $dataReturn->status=1;
                $dataReturn->message= __("Giao dịch thành công");
                $dataReturn->info=null;
                $dataReturn->last_balance_bot=$currencyUser-$amount;
                return $dataReturn;
            }
            //Đơn thất bại
            else{


                $dataReturn->status=0;
                $dataReturn->message= __("Giao dịch thất bại");
                $dataReturn->info=null;
                return $dataReturn;
            }
        }
        //trạng thái check thủ công Check thủ công
        else{

            //lưu log error gọi curl
            $path = storage_path() ."/logs/services-auto/";
            $filename=$path."fire_buy_product_error_roblox".Carbon::now()->format('Y-m-d').".txt";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw;
            \File::append( $filename,$contentText."\n");

            $dataReturn->status=999;
            $dataReturn->message= isset($result->errorMsg)?$result->errorMsg:null;
            $dataReturn->info=null;
            return $dataReturn;
        }
    }

    public static function detectDola(){

        //Lấy thông tin game pass cần mua
        $dataReturn = new \stdClass();
        $url = "https://portal.vietcombank.com.vn/Usercontrols/TVPortal.TyGia/pXML.aspx?b=10";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);


        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }

        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        //Log tìm kiếm user
//        $myfile = fopen(storage_path() . "/logs/curl_detect_user_name_roblox_search_gamepass-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
//        $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
//        fwrite($myfile, $txt);
//        fclose($myfile);

        preg_match_all('/CurrencyCode=\"(.*?)\"/', $resultRaw, $matches);

        preg_match('/<Exrate CurrencyCode="USD"[^>]+Sell="([^"]+)"/', $resultRaw, $matches);

        if (isset($matches[1])) {
            return str_replace(',', '', $matches[1]);;
        }

        return 25500;
    }

    public static function ProcessBuyGamePassNewJob($server_id,$amount,$cookiesSender,$request_id,$proxyCustomString=null,$placeID=null){

        //lưu log error gọi curl
        $path = storage_path() ."/logs/DefragmentJob/";
        $filename=$path."fire_buy_product_error_roblox".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText = Carbon::now()." :".json_encode($cookiesSender)." [" .$amount." ";
        \File::append( $filename,$contentText."\n");

        if($proxyCustomString==null){
            $proxyCustomString=self::$proxyList[array_rand(self::$proxyList)];
            $proxy=explode("|",$proxyCustomString);
        }
        elseif($proxyCustomString!=""){
            $proxy=explode("|",$proxyCustomString);
        }
        else{
            $proxy="";
        }

        $dataReturn = new \stdClass();
        //check live bot
        $result=self::checkLiveAndBalanceBotJob($cookiesSender,$proxyCustomString);

        //lưu log error gọi curl
        $path = storage_path() ."/logs/DefragmentJob/";
        $filename=$path."fire_buy_product_error_roblox_checkbalance".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText = Carbon::now()." :".json_encode($result);
        \File::append( $filename,$contentText."\n");

        if($result &&  $result->status==1){
            $currencyUser=$result->balance;
            $auth_key=$result->auth_key;
        }
        else{
            $dataReturn->status=2;
            $dataReturn->message= __("Nick bot chưa đăng nhập 7");
            $dataReturn->info=null;
            return $dataReturn;
        }

        if((int)$currencyUser<$amount){
            $dataReturn->status=33;
            $dataReturn->currencyUser=$currencyUser;
            $dataReturn->message= __("Bot không đủ số dư để thực hiện order");
            $dataReturn->info=null;
            return $dataReturn;
        }
        //search user
        $user_id=null;

        //search user
        $result=self::detectUsernameRobloxNew($server_id,$amount,null,$cookiesSender??'');

        //lưu log error gọi curl
        $path = storage_path() ."/logs/DefragmentJob/";
        $filename=$path."fire_buy_product_error_roblox_checkname".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText = Carbon::now()." :".json_encode($result);
        \File::append( $filename,$contentText."\n");


        if($result &&  $result->status==1){
            $user_id= $result->user_id??'';
        }
        else{
            $dataReturn->status=0;
            $dataReturn->message= __($result->message)??__("Không tìm thấy tên tài khoản");
            return $dataReturn;
        }

        if (!isset($user_id) || $user_id == ''){
            $dataReturn->status=0;
            $dataReturn->message=__("Không tìm thấy tên tài khoản");
            return $dataReturn;
        }


        //Lấy thông tin game pass cần mua
        $dataReturn = new \stdClass();
        $url = "https://games.roblox.com/v2/users/{$user_id}/games?accessFilter=Public&limit=50";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);


        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }


        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);



        $result=json_decode($resultRaw);

        $places = [];
        if(isset($result->data[0]->rootPlace->id)){
            if (!isset($placeID)){
                $placeID= $result->data[0]->rootPlace->id;
            }

            foreach ($result->data??[] as $item){
                $place_id = $item->rootPlace->id;
                array_push($places,$place_id);
            }
        }
        else{
            $dataReturn->status=0;
            $dataReturn->message=__("Không tìm thấy Place ID");
            return $dataReturn;
        }


        if (!isset($placeID) || $placeID == ''){
            $dataReturn->status=0;
            $dataReturn->message=__("Không tìm thấy Place ID");
            return $dataReturn;
        }

        if (!in_array($placeID,$places)){
            $dataReturn->status=0;
            $dataReturn->message=__("Không tìm thấy Place ID");
            return $dataReturn;
        }

        //lưu log error gọi curl
        $path = storage_path() ."/logs/DefragmentJob/";
        $filename=$path."fire_buy_product_error_roblox_checkuser".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText = Carbon::now()." :".$url." [" .$httpcode."] - ".json_encode($resultRaw);
        \File::append( $filename,$contentText."\n");


        //Lấy thông tin game pass cần mua
        $dataReturn = new \stdClass();
        $url = "https://www.roblox.com/games/getgamepassesinnerpartial?startIndex=0&maxRows=50&placeId={$placeID}";
        $ch = curl_init();
        //data dạng get
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);


        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }

        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        preg_match('/data-expected-seller-id=\"(.*?)\"/', $resultRaw, $matches);

        if (isset($matches[1]) && $matches[1] != '') {
            $seller_id=$matches[1]."";

        } else {

            $dataReturn->status=0;
            $dataReturn->message= __("Tài khoản chưa tạo game pass");
            $dataReturn->info=null;
            return $dataReturn;
        }

        //lưu log error gọi curl
        $path = storage_path() ."/logs/DefragmentJob/";
        $filename=$path."fire_buy_product_error_roblox_checkplaceid".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText = Carbon::now()." :".$url." [" .$httpcode."] - ".json_encode($resultRaw);
        \File::append( $filename,$contentText."\n");


        preg_match_all('/data-expected-price=\"(.*?)\"/', $resultRaw, $matches);

        $indexBuyGamePass=null;
        $rGamepass = '';
        if (isset($matches[1]) && $matches[1] != '') {

            //check số robux order với robux gamepass trong game
            $checkAmountWithOrder=false;
            foreach ($matches[1]??[] as $index=> $rubuxNumber){

                if ($index == 0){
                    $rGamepass = $rubuxNumber;
                }else{
                    $rGamepass = $rGamepass.','.$rubuxNumber;
                }

                if(intval($rubuxNumber)==$amount){
                    $checkAmountWithOrder=true;
                    $indexBuyGamePass=$index;
                    break;
                }
            }

            if($checkAmountWithOrder==false){
                $dataReturn->status=0;
                $dataReturn->message= __('Số Robux mua là '.$amount.' không khớp với robux của gamepass là: '.$rGamepass);
                $dataReturn->info=null;
                return $dataReturn;
            }
        } else {

            $dataReturn->status=2;
            $dataReturn->message= __("Nick bot chưa đăng nhập 8");
            $dataReturn->info=null;

            return $dataReturn;
        }


        //tìm product_id
        preg_match_all('/data-product-id=\"(.*?)\"/', $resultRaw, $matches);


        if (isset($matches[1]) && $matches[1] != '') {
            $produdctID=$matches[1][$indexBuyGamePass];

        } else {

            $dataReturn->status=2;
            $dataReturn->message= __("Nick bot chưa đăng nhập 9");
            $dataReturn->info=null;
            return $dataReturn;
        }


        //Tạo lệnh bắn
        $dataReturn = new \stdClass();
        $url = "https://economy.roblox.com/v1/purchases/products/{$produdctID}";

        $data = array();
        $data['expectedCurrency'] =1;
        $data['expectedPrice'] =$amount;
        $data['expectedSellerId'] =$seller_id;
        $data['request_id'] =$request_id;

        if(is_array($data)){
            $dataPost = http_build_query($data);
        }else{
            $dataPost = $data;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Requested-With' => 'XMLHttpRequest',
            'x-csrf-token: '.$auth_key,
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json, text/plain, */*',
            'Origin: https://roblox.com',
        ]);
        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = json_decode($resultRaw);

        if(isset($result) && isset($result->purchased)){

            //Đơn thành công
            if($result->purchased==true){

                $dataReturn->status=1;
                $dataReturn->message= __("Giao dịch thành công");
                $dataReturn->info=null;
                $dataReturn->last_balance_bot=$currencyUser-$amount;
                return $dataReturn;
            }
            //Đơn thất bại
            else{


                $dataReturn->status=0;
                $dataReturn->message= __("Giao dịch thất bại");
                $dataReturn->info=null;
                return $dataReturn;
            }
        }
        //trạng thái check thủ công Check thủ công
        else{

            //lưu log error gọi curl
            $path = storage_path() ."/logs/DefragmentJob/";
            $filename=$path."fire_buy_product_error_roblox".Carbon::now()->format('Y-m-d').".txt";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".json_encode($resultRaw);
            \File::append( $filename,$contentText."\n");

            $dataReturn->status=999;
            $dataReturn->message= isset($result->errorMsg)?$result->errorMsg:null;
            $dataReturn->info=null;
            return $dataReturn;
        }
    }

    public static function checkLiveAndBalanceBotJob($cookiesSender,$proxyCustom=null){

        if($proxyCustom==null){
            $proxy=explode("|",self::$proxyList[array_rand(self::$proxyList)]);
        }
        elseif($proxyCustom!=""){
            $proxy=explode("|",$proxyCustom);
        }
        else{
            $proxy="";
        }

        try {

            $dataReturn = new \stdClass();
            //check thông tin số roblox của server cần mua
            $url = "https://www.roblox.com/home";

            //$url = "https://www.roblox.com/home";

            $ch = curl_init();
            //data dạng get
            curl_setopt($ch, CURLOPT_URL, $url );
            curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
            curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);

            if(isset($proxy[0]) && $proxy[1]){
                curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
                curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
                if(isset($proxy[2]) && isset($proxy[3])){
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                    curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
                }
            }
            $resultRaw=curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            //lưu log gọi curl
            $path = storage_path() ."/logs/DefragmentJob/";
            $filename=$path."fire_check_live_bot_roblox".Carbon::now()->format('Y-m-d').".txt";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $contentText = Carbon::now()." :".$url." [" .$httpcode."] -" ." : ".$resultRaw;
            \File::append( $filename,$contentText."\n");



            preg_match('/data-displayName=(.*?)\s/', $resultRaw, $matches);

            if (isset($matches[1]) && $matches[1] != '') {
                $username_roblox=$matches[1]."";
            } else {

                $dataReturn->status=2;
                $dataReturn->message= __("Nick bot chưa đăng nhập 5");
                $dataReturn->info=null;

                //lưu log gọi curl
//                $path = storage_path() ."/logs/services-auto/";
//                $filename=$path."fire_gamepass_checkcooki".Carbon::now()->format('Y-m-d').".txt";
//                if(!\File::exists($path)){
//                    \File::makeDirectory($path, $mode = "0755", true, true);
//                }
//                $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($dataReturn) ."] : ".$resultRaw;
//                \File::append( $filename,$contentText."\n");

                return $dataReturn;
            }


            preg_match('/data-userid=\"(.*?)\"/', $resultRaw, $matches);

            if (isset($matches[1]) && $matches[1] != '') {
                $data_userid = $matches[1]."";

            } else {

                $dataReturn->status=2;
                $dataReturn->message= __("Nick bot chưa đăng nhập 6");
                $dataReturn->info=null;

                //lưu log gọi curl
                $path = storage_path() ."/logs/DefragmentJob/";
                $filename=$path."fire_gamepass_checkcooki".Carbon::now()->format('Y-m-d').".txt";
                if(!\File::exists($path)){
                    \File::makeDirectory($path, $mode = "0755", true, true);
                }
                $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($dataReturn) ."] : ".$resultRaw;
                \File::append( $filename,$contentText."\n");

                return $dataReturn;
            }



            preg_match('/data-token=\"(.*?)\"/', $resultRaw, $matches);

            if (isset($matches[1]) && $matches[1] != '') {
                $auth_key = $matches[1]."";
                $auth_key=str_replace( "\"","",$auth_key);
            } else {

                $dataReturn->status=2;
                $dataReturn->message= __("Nick bot chưa đăng nhập 7");
                $dataReturn->info=null;

                //lưu log gọi curl
                $path = storage_path() ."/logs/DefragmentJob/";
                $filename=$path."fire_gamepass_checkcooki".Carbon::now()->format('Y-m-d').".txt";
                if(!\File::exists($path)){
                    \File::makeDirectory($path, $mode = "0755", true, true);
                }
                $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($dataReturn) ."] : ".$resultRaw;
                \File::append( $filename,$contentText."\n");

                return $dataReturn;
            }

            preg_match('/data-userid=\"(.*?)\"/', $resultRaw, $matches);

            $user_id=null;

            if (isset($matches[1]) && $matches[1] != '') {
                $user_id=$matches[1]."";
            } else {

                $dataReturn->status=2;
                $dataReturn->message= __("Nick bot chưa đăng nhập 8");
                $dataReturn->info=null;

                //lưu log gọi curl
//                $path = storage_path() ."/logs/services-auto/";
//                $filename=$path."fire_gamepass_checkcooki".Carbon::now()->format('Y-m-d').".txt";
//                if(!\File::exists($path)){
//                    \File::makeDirectory($path, $mode = "0755", true, true);
//                }
//                $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($dataReturn) ."] : ".$resultRaw;
//                \File::append( $filename,$contentText."\n");

                return $dataReturn;
            }


            if (!isset($user_id) || $user_id == ''){
                $dataReturn->status=2;
                $dataReturn->message= __("Nick bot chưa đăng nhập 9");
                $dataReturn->info=null;

                //lưu log gọi curl
//                $path = storage_path() ."/logs/services-auto/";
//                $filename=$path."fire_gamepass_checkcooki".Carbon::now()->format('Y-m-d').".txt";
//                if(!\File::exists($path)){
//                    \File::makeDirectory($path, $mode = "0755", true, true);
//                }
//                $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($dataReturn) ."] : ".$resultRaw;
//                \File::append( $filename,$contentText."\n");

                return $dataReturn;
            }


            //////////////////////check balance bot//////////////////////

            $ch = curl_init();
            //data dạng get
            $url="https://economy.roblox.com/v1/users/{$user_id}/currency";
            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
            curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json;charset=UTF-8',
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);

            if(isset($proxy[0]) && $proxy[1]){
                curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
                curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
                if(isset($proxy[2]) && isset($proxy[3])){
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                    curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
                }
            }

            $resultRaw=curl_exec($ch);

            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result=json_decode($resultRaw);

            if($result && isset($result->robux))
            {
                $currencyUser= $result->robux;
            }
            else
            {
                //die
                $dataReturn->status=2;
                $dataReturn->message= __("Nick bot chưa đăng nhập 10");
                $dataReturn->info=null;

                //lưu log gọi curl
                $path = storage_path() ."/logs/DefragmentJob/";
                $filename=$path."fire_gamepass_checkcooki".Carbon::now()->format('Y-m-d').".txt";
                if(!\File::exists($path)){
                    \File::makeDirectory($path, $mode = "0755", true, true);
                }
                $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($dataReturn) ."] : ".$resultRaw;
                \File::append( $filename,$contentText."\n");

                return $dataReturn;
            }


            if($currencyUser!==null){

                //live
                $dataReturn->status=1;
                $dataReturn->balance=$currencyUser;
                $dataReturn->auth_key=$auth_key;
            }
            else{
                //die
                $dataReturn->status=0;
                $dataReturn->balance=null;

            }

            return $dataReturn;
        }
        catch (\Exception $e){
            \Log::error( $e);
            return null;
        }
    }

    public static function ProcessBuyGamePassProduct($produdctID,$sell_id,$amount,$cookiesSender,$request_id,$proxyCustomString=null){

        if($proxyCustomString==null){
            $proxyCustomString=self::$proxyList[array_rand(self::$proxyList)];
            $proxy=explode("|",$proxyCustomString);
        }
        elseif($proxyCustomString!=""){
            $proxy=explode("|",$proxyCustomString);
        }
        else{
            $proxy="";
        }

        $dataReturn = new \stdClass();
        //check live bot
        $result=self::checkLiveAndBalanceBot($cookiesSender,$proxyCustomString);

        if($result &&  $result->status==1){
            $currencyUser=$result->balance;
            $auth_key=$result->auth_key;
        }
        else{
            $dataReturn->status=2;
            $dataReturn->message= __("Nick bot chưa đăng nhập 7");
            $dataReturn->info=null;
            return $dataReturn;
        }

        if((int)$currencyUser<$amount){
            $dataReturn->status=33;
            $dataReturn->currencyUser=$currencyUser;
            $dataReturn->message= __("Bot không đủ số dư để thực hiện order");
            $dataReturn->info=null;
            return $dataReturn;
        }

        //Tạo lệnh bắn
        $dataReturn = new \stdClass();
        $url = "https://economy.roblox.com/v1/purchases/products/{$produdctID}";

        $data = array();
        $data['expectedCurrency'] =1;
        $data['expectedPrice'] =$amount;
        $data['expectedSellerId'] =$sell_id;
        $data['request_id'] =$request_id;

        if(is_array($data)){
            $dataPost = http_build_query($data);
        }else{
            $dataPost = $data;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesSender);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if(isset($proxy[0]) && $proxy[1]){
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
            if(isset($proxy[2]) && isset($proxy[3])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[2] .":".$proxy[3]);
                curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Requested-With' => 'XMLHttpRequest',
            'x-csrf-token: '.$auth_key,
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json, text/plain, */*',
            'Origin: https://roblox.com',
        ]);
        $resultRaw=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = json_decode($resultRaw);

        //lưu log error gọi curl
        $path = storage_path() ."/logs/services-auto/";
        $filename=$path."fire_buy_product_error_roblox".Carbon::now()->format('Y-m-d').".txt";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw;
        \File::append( $filename,$contentText."\n");

        $dataReturn->status=999;
        $dataReturn->message= isset($result->errorMsg)?$result->errorMsg:null;
        $dataReturn->info=null;
        return $dataReturn;

        if(isset($result) && isset($result->purchased)){

            //Đơn thành công
            if($result->purchased==true){

                $dataReturn->status=1;
                $dataReturn->message= __("Giao dịch thành công");
                $dataReturn->info=null;
                $dataReturn->last_balance_bot=$currencyUser-$amount;
                return $dataReturn;
            }
            //Đơn thất bại
            else{


                $dataReturn->status=0;
                $dataReturn->message= __("Giao dịch thất bại");
                $dataReturn->info=null;
                return $dataReturn;
            }
        }
        //trạng thái check thủ công Check thủ công
        else{

            //lưu log error gọi curl
            $path = storage_path() ."/logs/services-auto/";
            $filename=$path."fire_buy_product_error_roblox".Carbon::now()->format('Y-m-d').".txt";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $contentText = Carbon::now()." :".$url." [" .$httpcode."] - [".json_encode($data) ."] : ".$resultRaw;
            \File::append( $filename,$contentText."\n");

            $dataReturn->status=999;
            $dataReturn->message= isset($result->errorMsg)?$result->errorMsg:null;
            $dataReturn->info=null;
            return $dataReturn;
        }
    }

}
