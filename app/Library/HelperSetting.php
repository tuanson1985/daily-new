<?php


use App\Library\Helpers;

if (! function_exists('setting')) {

    function setting($key, $default = null, $shop_id = null)
    {
        if (is_null($key)) {
            return new \App\Models\Setting();
        }
        if (is_array($key)) {
            return \App\Models\Setting::set($key[0], $key[1]);
        }
        if(isset($shop_id)){
            $value = \App\Models\Setting::getSettingShop($key,null,$shop_id);
            return is_null($value) ? value($default) : $value;
        }
        $value = \App\Models\Setting::get($key);
        return is_null($value) ? value($default) : $value;
    }
}

if (! function_exists('formatPrice')) {

    function formatPrice($price)
    {
        return Helpers::formatPrice($price);
    }
}

if (! function_exists('encodeItemID')) {

    function encodeItemID($id)
    {
        return Helpers::encodeItemID($id);
    }
}

if (! function_exists('decodeItemID')) {

    function decodeItemID($str)
    {
        return Helpers::decodeItemID($str);
    }
}
