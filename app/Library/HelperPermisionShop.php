<?php

namespace App\Library;
use App\Models\Group;
use App\Models\GroupShop;
use App\Models\Shop;
use Auth;
use Request;
use App\Library\Helpers;

class HelperPermisionShop
{
    public static function VeryShop(){

        $dataShop = Shop::orderBy('id','desc');
        $shop_access_user = Auth::user()->shop_access;
        if(isset($shop_access_user) && $shop_access_user !== "all"){
            $shop_access_user = json_decode($shop_access_user);
            $dataShop = $dataShop->whereIn('id',$shop_access_user);
        }

        $dataShop = $dataShop->pluck('id')->toArray();

        return $dataShop;
    }

}
