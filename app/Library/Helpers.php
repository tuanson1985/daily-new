<?php
namespace App\Library;
use Telegram\Bot\Laravel\Facades\Telegram;


use Html;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11/18/2016
 * Time: 3:14 PM
 */
class Helpers
{


    static function curl($data){
        if (!defined('MAX_FILE_SIZE')) {
            @define('MAX_FILE_SIZE', 10000000);
        }
        $curl = curl_init();
        if (isset($data['params'])) {
            CURL_SETOPT($curl,CURLOPT_POST, True);
            CURL_SETOPT($curl,CURLOPT_POSTFIELDS, http_build_query($data['params']));
        }
        CURL_SETOPT($curl,CURLOPT_URL, $data['url']);
        CURL_SETOPT($curl,CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36");
        // CURL_SETOPT($curl,CURLOPT_COOKIEFILE, $cookie_file);
        // CURL_SETOPT($curl,CURLOPT_COOKIEJAR, $cookie_file);
        CURL_SETOPT($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        CURL_SETOPT($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        CURL_SETOPT($curl,CURLOPT_RETURNTRANSFER, True);
        CURL_SETOPT($curl,CURLOPT_FOLLOWLOCATION, True);
        CURL_SETOPT($curl,CURLOPT_CONNECTTIMEOUT, $data['timeout']??180);
        CURL_SETOPT($curl,CURLOPT_TIMEOUT, $data['timeout']??180);

        if(!empty($data['proxy'][0])) {
            $proxy_set = "http://{$data['proxy'][0]}:{$data['proxy'][1]}";
            curl_setopt($curl, CURLOPT_PROXY, $proxy_set);
            if (!empty($data['proxy'][2])) {
                curl_setopt($curl, CURLOPT_PROXYUSERPWD, "{$data['proxy'][2]}:{$data['proxy'][3]}");
            }
        }

        $exec = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $httpeffect = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        curl_close($curl);
        if ($httpcode != 200) {
            \Log::error("curl: {$httpcode} | $exec| {$data['url']}");
        }
        return $exec;
    }

    public static function Encrypt($string,$secret_key="") {
        $output = "";

        $encrypt_method = "AES-256-CBC";
        if($secret_key==null ||$secret_key==""){
            $secret_key = 'keymahoa';
        }
        $secret_iv = 'hash';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
        return $output;
    }

    public static function Decrypt($string,$secret_key="") {
        $output = "";

        $encrypt_method = "AES-256-CBC";
        if($secret_key==null ||$secret_key==""){
            $secret_key = 'keymahoa';
        }
        $secret_iv = 'hash';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        if($output==false){
            return "";
        }
        return $output;
    }

    public static function EncryptTokenSocket($string,$secret_key="") {
        $encryptionMethod = 'aes-256-cbc';
        $iv_size = openssl_cipher_iv_length($encryptionMethod);
        $iv = openssl_random_pseudo_bytes($iv_size);

        //To encrypt
        $encryptedMessage = openssl_encrypt($string, $encryptionMethod, $secret_key, 0, $iv);

        //Concatenate iv with data
        $encryptedMessageWithIv = bin2hex($iv) . $encryptedMessage;

        //To Decrypt
        $iv_size = openssl_cipher_iv_length($encryptionMethod);
        $iv = hex2bin(substr($encryptedMessageWithIv, 0, $iv_size * 2));

        $decryptedMessage = openssl_decrypt(substr($encryptedMessageWithIv, $iv_size * 2), $encryptionMethod, $secret_key, 0, $iv);

        return $encryptedMessageWithIv;

    }

    //active link function
    public static function SetActiveLink($path, $active = 'active')
    {

        return call_user_func_array('Request::is', (array)$path) ? $active : '';
//        		if(is_array($route))
//        		{
//        			return in_array(Request::path(), $route) ? 'active' : '';
//        		}
//        		return Request::path() == $route ? 'active' : '';
    }

    public static function rand_string($length)
    {
        $str = '';
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) {

            $str .= $chars[rand(0, $size - 1)];
        }

        return $str;
    }



    public static function rand_num_string($length)
    {
        $str = '';
        $chars = "0123456789";

        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) {

            $str .= $chars[rand(0, $size - 1)];
        }

        return $str;
    }

    public static function convert_vi_to_en($str) {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
        $str = preg_replace("/(đ)/", "d", $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
        $str = preg_replace("/(Đ)/", "D", $str);
        $str = str_replace(" ", " ", str_replace("&*#39;","",$str));
        return $str;
    }


    public static function FormatDateTime($format, $value)
    {
        //'d/m/Y'
        //'d/m/Y H:i:s'

        $result = date($format, strtotime($value));
        if ($result != "01/01/1970") {
            return $result;
        } else {
            return "";
        }
    }

    public static function DecodeJson($key, $data)
    {

        $objectJson = json_decode($data);
        if (isset($objectJson->$key)) {
            return $objectJson->$key;
        } else {
            return '';
        }

    }

    public static function LimitString($text, $limit)
    {

        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limit]) . '...';
        }
        return $text;
    }

    public static function getNWords($string, $n = 5, $withDots = true)
    {
        $excerpt = explode(' ', strip_tags($string), $n + 1);
        $wordCount = count($excerpt);
        if ($wordCount >= $n) {
            array_pop($excerpt);
        }
        $excerpt = implode(' ', $excerpt);
        if ($withDots && $wordCount >= $n) {
            $excerpt .= '...';
        }
        return $excerpt;
    }

    //build for dropdownlist
    public static function buildMenuDropdownList($dataCategory, $selected, $idparent = 0, $stringSpecial = "")
    {
        $result = null;
        // $result .= "<option value=''>-- Không chọn --</option>";
        foreach ($dataCategory as $item) {
            if ($item->parent_id == $idparent) {
                $checked = "";
                foreach ((array)$selected as $key => $value) {
                    if ($value == $item->id) {
                        $checked = "selected";
                        break;
                    }
                }
                $result .= "<option data-idkey='" . $item->idkey . "' value='" . $item->id . "'" . $checked . ">" . Html::entities($stringSpecial . ' ' . $item->title) . "</option>";

                $result .= self::buildMenuDropdownList($dataCategory, $selected, $item->id, $stringSpecial . "¦– – ");
            }
        }
        return $result;
    }
    //build for dropdownlist
    public static function buildMenuDropdownListNotIdParent0($dataCategory, $selected, $idparent = 0, $stringSpecial = "")
    {
        $result = null;
        // $result .= "<option value=''>-- Không chọn --</option>";
        foreach ($dataCategory as $item) {
            if ($item->parent_id == $idparent) {
                $checked = "";
                foreach ((array)$selected as $key => $value) {
                    if ($value == $item->id) {
                        $checked = "selected";
                        break;
                    }
                }
                $result .= "<option value='" . $item->id . "'" . $checked . ">" . Html::entities($stringSpecial . ' ' . $item->title) . "</option>";

                $result .= self::buildMenuDropdownList($dataCategory, $selected, $item->id, $stringSpecial . "¦– – ");
            }
            else{
                $checked_e = "";
                foreach ((array)$selected as $key => $value) {
                    if ($value == $item->id) {
                        $checked_e = "selected";
                        break;
                    }
                }
                $result .= "<option value='" . $item->id . "'" . $checked_e . ">" . Html::entities($stringSpecial . ' ' . $item->title) . "</option>";
            }
        }
        return $result;
    }
    //build shop for dropdownlist
    public static function buildShopDropdownList($dataCategory, $selected, $idparent = 0, $stringSpecial = "")
    {
        $result = null;
        foreach ($dataCategory as $item) {
                $checked = "";
                foreach ((array)$selected as $key => $value) {
                    if ($value == $item->id) {
                        $checked = "selected";
                        break;
                    }
                }
                $result .= "<option value='" . $item->id . "'" . $checked . ">" . Html::entities($stringSpecial . ' ' . $item->domain) . "</option>";
        }
        return $result;
    }

    public static function GetChildrenShop($menu, $parent_id)
    {

        $result = null;
        foreach ($menu as $item)
            if ($item->parent_id == $parent_id) {
                $result .= ',' . $item->id;
                $result .= self::GetChildrenCategory($menu, $item->id);

            }
        return $result ? "$result" : null;
    }

    public static function GetChildrenCategory($menu, $parent_id)
    {

        $result = null;
        foreach ($menu as $item)
            if ($item->parent_id == $parent_id) {
                $result .= ',' . $item->id;
                $result .= self::GetChildrenCategory($menu, $item->id);

            }
        return $result ? "$result" : null;
    }

    public static function TelegramNotify($content,$channel_id = ""){
        try{
            if($channel_id == "" || $channel_id == null){
                $channel_id = config('telegram.bots.mybot.channel_id');
            }
            Telegram::sendMessage([
                'chat_id' => $channel_id,
                'parse_mode' => 'HTML',
                'text' => $content
            ]);
        }
        catch (\Exception $e) {
            return false;
        }
    }
    static function encoder_num_str(){
        return ['q','s','e','r','t','y','u','i','o','p'];
    }
    static function encodeItemID($id, $shop_id = 0){
        $num_str = ['q','s','e','r','t','y','u','i','o','p'];
        if (!$shop_id) {
            $shop_id = config('etc.shop_id')??0;
            if (session('shop_id')) {
                $shop_id = session('shop_id');
            }
        }
        $alpha_id = '';
        foreach (str_split($shop_id) as $i) {
            $alpha_id .= self::encoder_num_str()[intval($i)];
        }
        return strtoupper($alpha_id).($id+$shop_id*2);
    }

    static function decodeItemID($str){
        $str = strtolower($str);
        $shop_id = '';
        $alpha = '';
        foreach (str_split($str) as $char) {
            if (!is_numeric($char)) {
                $alpha .= $char;
                $shop_id .= array_search($char, self::encoder_num_str());
            }else{
                break;
            }
        }
        if (empty($alpha)) {
            return $str;
        }
        if (empty($shop_id)) {
            $shop_id = 0;
        }
        $id = str_replace($alpha, '', $str) - ($shop_id*2);
        return intval($id);
    }

    public static function formatPrice($price){
        if ($price == null || !is_int($price)){
            return '';
        }

        return str_replace(',','.',number_format($price));
    }

    static function encryptProxy($dataSend,$secretkey){
        $json_endcode = json_encode($dataSend);
        $encodedString = preg_replace('/[^a-zA-Z0-9À-ỹ]/u', '', $json_endcode);;
        $base64_encode = base64_encode($encodedString);
        $base64_encode_secretkey = $base64_encode.$secretkey;
        $sign = md5($base64_encode_secretkey);
        return $sign;
    }

    function convertPricePacket($praise_price_packet) {
        $unitMultipliers = [
            'B' => 1000000000,
            'M' => 1000000,
            'K' => 1000
        ];

        // Kiểm tra từng ký tự cuối cùng của chuỗi và áp dụng nhân tương ứng
        $unit = substr($praise_price_packet, -1);
        $value = (float) str_replace($unit, '', $praise_price_packet);

        if (empty($unitMultipliers[$unit])) {
            return $value;
        }
        return $value * $unitMultipliers[$unit];
    }
//$price_input = convertPricePacket($praise_price_packet);

}
