<?php

namespace App\Library;
use App\Models\Group;
use App\Models\GroupShop;
use App\Models\Shop;
use Auth;
use Request;
use App\Library\Helpers;

class HelperPermisionShopMinigame
{
    public static function VeryShopMinigame(){

        $dataShop = Shop::orderBy('id','desc');
        $shop_access_user = Auth::user()->shop_access;
        if(isset($shop_access_user) && $shop_access_user !== "all"){
            $shop_access_user = json_decode($shop_access_user);
            $dataShop = $dataShop->whereIn('id',$shop_access_user);
        }

        $dataShop = $dataShop->pluck('id')->toArray();

        return $dataShop;
    }


    public static function VeryShopaAccess($id,$cat_id){
        $flag = false;
        $providers = Group::where('module', 'acc_provider')->with(['childs' => function($query) use($id){
            $query->with(['custom' => function($query) use($id){
                $query->where('groups_shops.shop_id', $id);
            }])->orderBy('order');
        }])->orderBy('order')->get();

        foreach ($providers as $key => $provider) {
            if($provider->childs->count()){
                foreach ($provider->childs as $cat) {
                    if (isset($cat->custom->status)){
                        if ($cat->custom->status == 1){
                            if ($cat->custom->group_id == $cat_id){
                                $flag = true;
                            }
                        }
                    }
                }
            }
        }

        return $flag;
    }
}
