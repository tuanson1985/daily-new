<?php

namespace App\Library;

class CheckLogin {
    static function nroblue($input){
        $fileCookie = public_path("temp/nroblue-cookies-{$input['username']}.txt");
        $resultObject = new \stdClass();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,fr-FR;q=0.6,fr;q=0.5',
            'Cache-Control: max-age=0',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36'

        ));
        curl_setopt($ch, CURLOPT_URL, "https://nroblue.com/dang-nhap");
        curl_setopt($ch, CURLOPT_COOKIEFILE, $fileCookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $fileCookie);
        $ketqua = curl_exec($ch);
        $field = [
            'accept' => 'Ok', 'user' => $input['username'], 'pass' => $input['password']
        ];
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, 'https://nroblue.com/controller/login');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($field));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: */*',
            'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,fr-FR;q=0.6,fr;q=0.5',
            'Connection: keep-alive',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Origin: https://nroblue.com',
            'Referer: https://nroblue.com/dang-nhap',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Site: same-origin',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36',
            'X-Requested-With: XMLHttpRequest',
            'sec-ch-ua: " Not A;Brand";v="99", "Chromium";v="100", "Google Chrome";v="100"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Windows"'
        ]);
        $ketqua = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($ketqua);
        @unlink($fileCookie);
        $resultObject->status = $json->status == 'success'? 1: 0;
        $resultObject->message = $json->message;
        return $resultObject;
    }

    static function teamobi($input){
        $fileCookie = public_path("temp/teamobi-cookies-{$input['username']}.txt");
        $resultObject = new \stdClass();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,fr-FR;q=0.6,fr;q=0.5',
            'Cache-Control: max-age=0',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36'

        ));
        curl_setopt($ch, CURLOPT_URL, "http://my.teamobi.com/app/login.php");
        curl_setopt($ch, CURLOPT_COOKIEFILE, $fileCookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $fileCookie);
        $ketqua = curl_exec($ch);
        $url_post = self::get_str($ketqua, '<form action="', '"');
        $field = [
            'nav' => '', 'user' => $input['username'], 'pass' => $input['password'], 'submit' => 'Đăng+nhập', 
            'checkru' => self::get_str($ketqua, 'name="checkru" value="', '"')
        ];
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, $url_post);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($field));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,fr-FR;q=0.6,fr;q=0.5',
            'Cache-Control: max-age=0',
            'Connection: keep-alive',
            'Content-Type: application/x-www-form-urlencoded',
            'Origin: http://my.teamobi.com',
            'Referer: http://my.teamobi.com/app/login.php',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36'
        ]);
        $ketqua = curl_exec($ch);
        curl_close($ch);
        
        @unlink($fileCookie);
        if (strpos($ketqua, "Đăng nhập không thành công") > 0) {
            $resultObject->status=0;
            $resultObject->message="Đăng nhập không thành công";
        }else{
            $resultObject->status=1;
            $resultObject->message="Đăng nhập thành công";
        }
        return $resultObject;
    }

    static function nro($input){
        $json = Helpers::curl([
            'url' => env('URL_CHECK_LOGIN_NRO', 'http://nick.tichhop.pro')."/api/nro?username=".urlencode($input['username'])."&password=".urlencode($input['password'])
        ]);
        return json_decode($json);
    }

    static function vtc($input){
        $fileCookie = public_path("temp/vtc-cookies-{$input['username']}.txt");
        $resultObject = new \stdClass();

        $data="";
        $domainRef="https://vtcmobile.vn/oauth/accounts/sso/login/default.aspx?sid=330002&ur=http%3A%2F%2Fmy.vtcmobile.vn&m=1&continue=http%3A%2F%2Fmy.vtcmobile.vn%2Fbilling%2Fsso%2Flogin.aspx&v=m&loginmode=";
        $domainLogin="https://vtcmobile.vn/oauth/accounts/sso/login/default.aspx?sid=330002&ur=http%3A%2F%2Fmy.vtcmobile.vn&m=1&continue=http%3A%2F%2Fmy.vtcmobile.vn%2Fbilling%2Fsso%2Flogin.aspx&v=m&loginmode=";
        $domainCheck="https://scoin.vn/nap-game";

        $resultCheck = -1;
        $ch = curl_init();
        $tranid = time() . rand(10000, 99999); // Tối đa 20 ký tự
        $field='&ctl00%24MainContent%24ctl00%24txtUserName='.urlencode($input['username']).'&ctl00%24MainContent%24ctl00%24txtPass='.urlencode($input['password']).'&ctl00%24MainContent%24ctl00%24btnDangNhap=%C4%90%C4%83ng+nh%E1%BA%ADp&ctl00%24MainContent%24ctl00%24rememberMeCheckBox=on';

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36',
            'Accept-Language: en-US,en;q=0.9,vi;q=0.8',
            'Connection: keep-alive',
            'Content-Type: application/x-www-form-urlencoded',

        ));
        curl_setopt($ch, CURLOPT_URL, $domainRef);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $fileCookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $fileCookie);
        $ketqua = curl_exec($ch);
        preg_match('/<input type="hidden" name="__EVENTVALIDATION" .* value="(.*?)"/', $ketqua, $matches);

        if (isset($matches[1]) && $matches[1] != '') {
            $auth_key = $matches[1];
            $field = "&__EVENTVALIDATION=" . urlencode( $auth_key ).$field;

        } else {
            curl_close($ch);
            @unlink($fileCookie);
            $resultObject->status=2;
            $resultObject->message="Không có phản hồi từ cổng đăng nhập";
            return $resultObject;
        }

        preg_match('/<input type="hidden" name="__VIEWSTATE" .* value="(.*?)"/', $ketqua, $matches);
        if (isset($matches[1]) && $matches[1] != '') {
            $auth_key = $matches[1];
            $field =  "__VIEWSTATE=" . urlencode( $auth_key ).$field;

        } else {
            curl_close($ch);
            @unlink($fileCookie);
            $resultObject->status=2;
            $resultObject->message="Không có phản hồi từ cổng đăng nhập";
            return $resultObject;
        }


        //giả lập post
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING ,"");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36',
            'Accept-Language: en-US,en;q=0.9,vi;q=0.8',
            'Connection: keep-alive',
            'Content-Type: application/x-www-form-urlencoded',
            'Referer: ' . 'https://vtcmobile.vn/oauth/accounts/sso/login/?sid=330035&ur=http%3a%2f%2fgraph.vtcmobile.vn%2foauth%2fauthorize%3fclient_id%3db41f9e5a37b3ec9a021382b36946dbec%26redirect_uri%3dhttp%253a%252f%252fscoin.vn%252f%26urllogin%3d%26m%3d%26agencyid%3d1%26imei%3d&m=1&continue=http%3a%2f%2fgraph.vtcmobile.vn%2foauth%2fauthorize%3fclient_id%3db41f9e5a37b3ec9a021382b36946dbec%26redirect_uri%3dhttp%253a%252f%252fscoin.vn%252f%26urllogin%3d%26m%3d%26agencyid%3d1%26imei%3d&agencyid=1&imei=',
        ));
        curl_setopt($ch, CURLOPT_URL, $domainLogin);
        $ketqua = curl_exec($ch);
        curl_close($ch);
        @unlink($fileCookie);
        die($ketqua);
        if (strpos($ketqua, "mật khẩu không đúng") > 0) {
            $resultObject->status=0;
            $resultObject->message="Tên đăng nhập hoặc mật khẩu không đúng";
        }else{
            $resultObject->status=1;
            $resultObject->message="Đăng nhập thành công";
        }
        return $resultObject;
    }

    static function get_str($string, $find_start, $find_end = false) {
        $start = $find_start == ''? 0: stripos($string, $find_start);

        if($start === false) return false;

        $length = strlen($find_start);

        $end = $find_end? stripos(substr($string, $start+$length), $find_end): $find_end;

        if($end !== false) {
            $rs = substr($string, $start+$length, $end);
        } else {
            $rs = substr($string, $start+$length);
        }

        return $rs ? $rs : null;
    }
}
