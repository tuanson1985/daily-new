<?php


if (! function_exists('currency_format')) {

    function currency_format($number)
    {
        if($number.""==""){
            return "0";
        }
        return number_format($number, 0,',','.');

    }
}

if (! function_exists('percent_format')) {

    function percent_format($number,$decimals=1)
    {
        if($number.""==""){
            return "0";
        }
        return number_format($number, $decimals,',',',');

    }
}

if (! function_exists('datetime_format')) {

    function datetime_format($datetime,$format="d/m/Y H:i:s")
    {

        $result = date($format, strtotime($datetime));
        if ($result != "01/01/1970") {
            return $result;
        } else {
            return "";
        }
    }
}



if (! function_exists('agotime_format')) {

    function agotime_format($time)
    {
        $time = strtotime($time);

        $time = time() - $time; // to get the time since that moment

        if ($time == 0) {
            return "Vừa xong";
        }
        $tokens = array(
            31536000 => 'năm',
            2592000 => 'tháng',
            604800 => 'tuần trước',
            86400 => 'ngày trước',
            3600 => 'giờ trước',
            60 => 'phút trước',
            1 => 'giây trước',

        );
        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? '' : '');
        }
    }
}
