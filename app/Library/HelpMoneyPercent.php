<?php
namespace App\Library;
use App\Models\Shop;
use App\Models\Shop_Group;
use App\Models\Shop_Group_Shop;
use Log;

class HelpMoneyPercent{
    public static function _makeMoney($shop_id,$price,$module){
        try{
            // kiểm tra shop_id
            $shop = Shop::where('id',$shop_id)->where('status',1)->first();
            if(!$shop){
                return [
                    'status' => false,
                    'message' => 'Không tìm thấy shop yêu cầu'
                ];
            }
            //tìm nhóm shop
            $shopGroup = Shop_Group::with(['shop' => function($query) use ($shop){
                $query->where('shop_id',$shop->id);
            }])
            ->where('status', 1)
            ->first();
            if(!$shopGroup){
                return [
                    'status' => false,
                    'message' => 'Không tìm thấy nhóm shop phù hợp'
                ];
            }
              // lấy thông tin tỷ giá (nếu có thông tin cấu hình tỷ giá theo từng module thì lấy theo module nếu không thì lấy theo tỷ giá của nhóm (all)) 
            $params = $shopGroup->params;
            $additional_amount = false;
            $ratio_percent = false;
            // kiểm tra xem có cấu hình riêng cho dịch vụ hay không
            if(isset($params->$module) && $params->$module->additional_amount != "" && $params->$module->ratio_percent != ""){
                $additional_amount = $params->$module->additional_amount;
                $ratio_percent = $params->$module->ratio_percent;
            }
            // nếu không có cấu hình riêng cho từng dịch vụ thì kiểm tra cấu hình của toàn nhóm
            if($additional_amount === false || $ratio_percent === false){
                if(isset($params->all) && $params->all->additional_amount != "" && $params->all->ratio_percent != ""){
                    $additional_amount = $params->all->additional_amount;
                    $ratio_percent = $params->all->ratio_percent;
                }
            }
            if($additional_amount === false || $ratio_percent === false){
                return [
                    'status' => false,
                    'message' => 'Không thể quy đổi tỷ giá cho giao dịch này, vui lòng liên hệ QTV để kịp thời xử lý.'
                ];
            }

            if((int)$additional_amount < 0){
                return [
                    'status' => false,
                    'message' => 'Số tiền cộng thêm cho 1 đơn hàng phải lớn hơn 0, vui lòng liên hệ QTV để kịp thời xử lý.'
                ];
            }
            if((int)$ratio_percent < 70 || (int)$ratio_percent > 130){
                return [
                    'status' => false,
                    'message' => 'Tỷ giá phải trong khoảng từ 70 - 130, vui lòng liên hệ QTV để kịp thời xử lý.'
                ];
            }
            // tính số tiền sau tỷ giá theo công thức Số tiền thực = ((Số tiền * tỷ lệ ) / 100) + Số tiền cộng thêm của đơn hàng
            $real_received_amount = ($price * ($ratio_percent / 100)) + $additional_amount;
            return [
                'status' => true,
                'message' => 'OK',
                'price' => $real_received_amount
            ];     
        }
        catch(\Exception $e){
            Log::error($e);
            return null;
        }
        

    }

    static function shop_price($price){
        $ratio = config('etc.shop_ratio');
        if (empty($ratio)) {
            return $price;
        }
        $result = ($price*$ratio->ratio_percent/100)+($ratio->additional_amount??0);
        return intval(ceil($result/1000)*1000);
    }

    static function shop_de_price($price){
        $ratio = config('etc.shop_ratio');
        if (empty($ratio)) {
            return $price;
        }
        $result = ($price*100/$ratio->ratio_percent)-($ratio->additional_amount??0);
        return intval(ceil($result/1000)*1000);
    }

    static function shop_price_atm($price){ 
        $shop = config('etc.shop');
        $ratio = config('etc.shop_ratio');
        if (empty($ratio)) {
            return $price;
        }
        $result = $price + ($ratio->additional_amount??0);
        $rate = $ratio->ratio_percent - ($shop->ratio_atm??0);
        if ($rate > -100 && $rate < 100) {
            $result += $price*$rate/100;
        }
        return intval(ceil($result/1000)*1000);
    }
}