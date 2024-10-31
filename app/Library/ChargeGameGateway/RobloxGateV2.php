<?php

namespace App\Library\ChargeGameGateway;
use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11/18/2016
 * Time: 3:14 PM
 */
class RobloxGateV2
{
    public static $proxyList=[
        "45.118.145.47|3128|hqplayproxy|hqplay12",
        "45.122.220.186|3128|hqplayproxy|hqplay12",
        "45.122.221.194|3128|hqplayproxy|hqplay12",
        "45.122.220.206|3128|hqplayproxy|hqplay12",
        "45.122.221.139|3128|hqplayproxy|hqplay12",
//        "45.122.221.160|3128|hqplayproxy|hqplay12",
        "45.122.221.181|3128|hqplayproxy|hqplay12",
        "45.122.222.112|3128|hqplayproxy|hqplay12",
        "45.122.222.120|3128|hqplayproxy|hqplay12",
    ];

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

}
