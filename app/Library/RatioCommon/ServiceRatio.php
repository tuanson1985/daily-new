<?php
namespace App\Library\RatioCommon;


use App\Models\Shop;
use Html;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11/18/2016
 * Time: 3:14 PM
 */
class ServiceRatio
{

    public static function get($shop_id){

        $shop=Shop::with('group')->where('id',$shop_id)->first();


        $ratioConfigShop = null;

        if(!empty($shop->group->params->service->ratio_percent) && ((float)$shop->group->params->service->ratio_percent>60 && (float)$shop->group->params->service->ratio_percent <=160))
        {
            $ratioConfigShop = new \stdClass();
            $ratioConfigShop->ratio_percent=$shop->group->params->service->ratio_percent;
            $ratioConfigShop->additional_amount= $shop->group->params->service->additional_amount??0;
            if($ratioConfigShop->additional_amount<0){
                $ratioConfigShop->additional_amount=$ratioConfigShop->additional_amount;
            }
        }
        else{

            if(!empty($shop->group->params->all->ratio_percent) && ((float)$shop->group->params->all->ratio_percent > 60 && (float)$shop->group->params->all->ratio_percent <= 160)){

                $ratioConfigShop = new \stdClass();
                $ratioConfigShop->ratio_percent=$shop->group->params->all->ratio_percent;
                $ratioConfigShop->additional_amount= $shop->group->params->all->additional_amount??0;
                if($ratioConfigShop->additional_amount<0){
                    $ratioConfigShop->additional_amount=$ratioConfigShop->additional_amount;
                }
            }

        }

        if($ratioConfigShop==null) {
            return false;
        }
        return $ratioConfigShop;
    }




}
