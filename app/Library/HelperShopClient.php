<?php

namespace App\Library;
use App\Models\Setting;
use App\Models\Shop;
use Request;
use App\Library\Helpers;

class HelperShopClient
{
    public static function VeryShopId($dataSign){
        try{
            $data = Helpers::Decrypt($dataSign,config('module.shop.secret_key_very_client'));
            // if($data == ""){
            //     return false;
            // }
            // $data = explode(',',$data);
            // if(!is_array($data)){
            //     return false;
            // }
            return true;



        }
        catch (\Exception $e) {
            return false;
        }


    }

    public static function getSettingKitioShop($kitio){
        $data = [];

        $shops = Shop::where('status',1)->pluck('id')->toArray();

        if (isset($shops) && count($shops)){
            foreach ($shops as $shop){
                $key = Setting::getSettingShop('sys_footer_kitio',null,$shop);
                if ($kitio == 1){
                    if ($key != '' && $key == 1){
                        array_push($data,$shop);
                    }
                }else{
                    if ($key != '' && $key == 1){}else{
                        array_push($data,$shop);
                    }
                }

            }
        }

        return $data;
    }
}
