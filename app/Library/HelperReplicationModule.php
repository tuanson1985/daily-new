<?php
namespace App\Library;
use App\Models\Group;
use App\Models\Item;
use App\Models\ItemConfig;
use App\Models\Setting;
use App\Models\StoreTelecom;
use App\Models\StoreTelecomValue;
use App\Models\Telecom;
use App\Models\TelecomValue;
use App\Models\Theme;
use App\Models\ThemeClient;
use Carbon\Carbon;


class HelperReplicationModule{

    // $shop_id_replication: shop cần nhân bản
    // $shop_id: shop sẽ nhân bản
    public static function __moduleCharge($shop_id,$shop_id_replication, $telecom_replication = null){
        if($telecom_replication == null){
            $telecom_replication = Telecom::where('shop_id',$shop_id_replication)->get();
        }
        if(isset($telecom_replication) && count($telecom_replication) > 0){
            foreach($telecom_replication as $item){
                $item_new = $item->replicate()->fill(
                    [
                        'shop_id' => $shop_id,
                        'created_at' => Carbon::now(),
                    ]
                );
                $item_new->save();
                $telecom_value = TelecomValue::where('shop_id',$shop_id_replication)->where('telecom_id',$item->id)->get();
                foreach($telecom_value as $item_telecom_value){
                    $telecom_value_new = $item_telecom_value->replicate()->fill(
                        [
                            'shop_id' => $shop_id,
                            'telecom_id' => $item_new->id,
                            'created_at' => Carbon::now(),
                        ]
                    );
                    $telecom_value_new->save();
                }
            }
        }
    }

    public static function __moduleStoreCard($shop_id,$shop_id_replication, $store_telecom_replication = null){
        if($store_telecom_replication == null){
            $store_telecom_replication = StoreTelecom::where('shop_id',$shop_id_replication)->get();
        }
        if(isset($store_telecom_replication) && count($store_telecom_replication) > 0){
            foreach($store_telecom_replication as $item){
                $item_new = $item->replicate()->fill(
                    [
                        'shop_id' => $shop_id,
                        'created_at' => Carbon::now(),
                    ]
                );
                $item_new->save();
                $store_telecom_value = StoreTelecomValue::where('shop_id',$shop_id_replication)->where('telecom_id',$item->id)->get();
                foreach($store_telecom_value as $item_store_telecom_value){
                    $store_telecom_value = $item_store_telecom_value->replicate()->fill(
                        [
                            'shop_id' => $shop_id,
                            'telecom_id' => $item_new->id,
                            'created_at' => Carbon::now(),
                        ]
                    );
                    $store_telecom_value->save();
                }
            }
        }
    }


    public static function __moduleService($shop_id,$shop_id_replication ){

       $dataShopFrom= ItemConfig::where('shop_id',$shop_id_replication)->get();
        foreach($dataShopFrom as $item){

            $item_new = $item->replicate()->fill(
                [
                    'shop_id' => $shop_id,
                    'created_at' => Carbon::now(),
                ]
            );
            $item_new->save();
        }
    }

    public static function __moduleMenuCateogy($shop_id,$shop_id_replication ){

        $dataShopFrom = Group::where('module','=','menu-category')->where('shop_id',$shop_id_replication)->get();

        foreach($dataShopFrom as $item){

            $val = Group::where('module','=','menu-category')->where('id',$item->id)->first();

            if ($val->parent_id == 0){
                $item_new = $item->replicate()->fill(
                    [
                        'shop_id' => $shop_id,
                        'created_at' => Carbon::now(),
                        'author_id' => auth()->user()->id,
                    ]
                );
                $item_new->save();
            }

        }

        foreach($dataShopFrom as $item){

            $val = Group::where('module','=','menu-category')->where('id',$item->id)->first();

            if ($val->parent_id != 0){
                $item_new = $item->replicate()->fill(
                    [
                        'shop_id' => $shop_id,
                        'created_at' => Carbon::now(),
                        'author_id' => auth()->user()->id,
                    ]
                );
                $groupcheck = Group::where('module','=','menu-category')->where('id',$val->parent_id)->first();
                $itemcheck = Group::where('module','=','menu-category')->where('shop_id',$shop_id)->where('slug',$groupcheck->slug)->first();
                $item_new->parent_id=$itemcheck->id;
            }

            $item_new->save();

        }
    }

    public static function __moduleMenuProfile($shop_id,$shop_id_replication ){

        $dataShopFrom = Group::where('module','=','menu-profile')->where('shop_id',$shop_id_replication)->get();

        foreach($dataShopFrom as $item){

            $val = Group::where('module','=','menu-profile')->where('id',$item->id)->first();

            if ($val->parent_id == 0){

                $item_new = $item->replicate()->fill(
                    [
                        'shop_id' => $shop_id,
                        'created_at' => Carbon::now(),
                        'author_id' => auth()->user()->id,
                    ]
                );

                $item_new->save();
            }



        }

        foreach($dataShopFrom as $item){

            $val = Group::where('module','=','menu-profile')->where('id',$item->id)->first();



            if ($val->parent_id != 0){
                $item_new = $item->replicate()->fill(
                    [
                        'shop_id' => $shop_id,
                        'created_at' => Carbon::now(),
                        'author_id' => auth()->user()->id,
                    ]
                );
                $groupcheck = Group::where('module','=','menu-profile')->where('id',$val->parent_id)->first();
                $itemcheck = Group::where('module','=','menu-profile')->where('shop_id',$shop_id)->where('slug',$groupcheck->slug)->first();
                $item_new->parent_id=$itemcheck->id;

                $item_new->save();
            }



        }
    }

    public static function __moduleMenuTransaction($shop_id,$shop_id_replication ){

        $dataShopFrom = Group::where('module','=','menu-transaction')->where('shop_id',$shop_id_replication)->get();

        foreach($dataShopFrom as $item){

            $val = Group::where('module','=','menu-transaction')->where('id',$item->id)->first();

            if ($val->parent_id == 0){

                $item_new = $item->replicate()->fill(
                    [
                        'shop_id' => $shop_id,
                        'created_at' => Carbon::now(),
                        'author_id' => auth()->user()->id,
                    ]
                );

                $item_new->save();
            }

        }

        foreach($dataShopFrom as $item){

            $val = Group::where('module','=','menu-transaction')->where('id',$item->id)->first();

            if ($val->parent_id != 0){
                $item_new = $item->replicate()->fill(
                    [
                        'shop_id' => $shop_id,
                        'created_at' => Carbon::now(),
                        'author_id' => auth()->user()->id,
                    ]
                );

                $groupcheck = Group::where('module','=','menu-transaction')->where('id',$val->parent_id)->first();
                $itemcheck = Group::where('module','=','menu-transaction')->where('shop_id',$shop_id)->where('slug',$groupcheck->slug)->first();
                $item_new->parent_id=$itemcheck->id;

                $item_new->save();
            }

        }
    }

    public static function __moduleArticle($shop_id,$shop_id_replication ){

//        Lưu danh muc bài viết.

        $dataShopFrom = Group::where('module','=','article-category')->where('shop_id',$shop_id_replication)->get();

        foreach($dataShopFrom as $item){

            $val = Group::where('module','=','article-category')->where('id',$item->id)->first();

            $item_new = $item->replicate()->fill(
                [
                    'shop_id' => $shop_id,
                    'created_at' => Carbon::now(),
                    'author_id' => auth()->user()->id,
                ]
            );

            if ($val->parent_id != 0){


                $itemcheck = Group::where('module','=','article-category')->where('shop_id',$shop_id)->where('parent_id',0)->orderBy('id','desc')->first();
                if (isset($itemcheck)){
                    $item_new->parent_id=$itemcheck->id;
                }else{
                    $item_new->parent_id=0;
                }
            }

            $item_new->save();

        }

//        Lưu chi tiết bài viết.

        $dataShopItemFrom = Item::with(array('groups' => function ($query) {
            $query->select('groups.id','title','slug');
        }))->where('module','=','article')->where('shop_id',$shop_id_replication)->get();

        foreach ($dataShopItemFrom as $itemi){
            $vali = Item::with(array('groups' => function ($query) {
                $query->select('groups.id','title','slug');
            }))->where('module','=','article')->where('id',$itemi->id)->where('shop_id',$shop_id_replication)->first();

            $item_newi = $vali->replicate()->fill(
                [
                    'shop_id' => $shop_id,
                    'created_at' => Carbon::now(),
                    'author_id' => auth()->user()->id,
                ]
            );

            if (isset($vali->groups[0])){
                $checkgroup = Group::where('module','=','article-category')->where('slug',$vali->groups[0]->slug??'')->where('shop_id',$shop_id)->first();

                if (isset($checkgroup)){
                    $item_newi->save();

                    $item_newi->groups()->attach($checkgroup->id);
                }
            }

        }
    }

    public static function __moduleTheme($shop_id,$shop_id_replication ){
        $key = 'sys_theme_ver_page_build';
//        Lưu danh muc bài viết.
        $themeclient = ThemeClient::where('client_id',$shop_id_replication)->first();

        if (isset($themeclient)){
//            Cấu hình theme
            $themeclient_new = $themeclient->replicate()->fill(
                [
                    'shop_id' => session('shop_id'),
                    'created_at' => Carbon::now(),
                ]
            );
            $themeclient_new->save();

            $c_theme = Theme::where('id',$themeclient->theme_id)->where('status',1)->first();
            $key_theme = $c_theme->key;

            $group = Group::where('module','=','page-build')->where('shop_id',$shop_id_replication)->where('idkey',$key_theme)->where('status',1)->orderBy('order')->get();

            if (isset($group) && count($group)){
                $group_new = $themeclient->replicate()->fill(
                    [
                        'shop_id' => session('shop_id'),
                        'created_at' => Carbon::now(),
                        'author_id' => auth()->user()->id,
                    ]
                );
                $group_new->save();
            }

            $slugselect = null;
            $titleselect = null;
            $itemselect = null;

            foreach ($group as $val){
                if (isset($slugselect)) {
                    $slugselect = $slugselect.'|';
                }

                $slugselect = $slugselect.$val->slug;

                if (isset($titleselect)) {
                    $titleselect = $titleselect.'|';
                }

                $titleselect = $titleselect.$val->title;
            }

            if (isset($titleselect) && isset($slugselect)){
                $itemselect = $titleselect.','.$slugselect;
            }

            if (isset($itemselect)){

                Setting::add($key, $itemselect, Setting::getDataType($key));
            }
        }
    }

    public static function __moduleSetting($shop_id,$shop_id_replication ){
        $setting_build = Setting::getAllSettingsShopId($shop_id_replication);

        foreach ($setting_build as $key => $value){

            Setting::add($value->name, $value->val, Setting::getDataType($value->name));
        }

    }
}
