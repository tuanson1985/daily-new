<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Library\CreateMenuCustom;
use App\Models\Group;
use App\Models\Item;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\Txns;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;

class SettingController extends Controller
{
    public function getSetting(Request $request){
        try {

            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }
            $data = Setting::getAllSettingsShopId($shop->id);
            return response()->json([
                'message' => __('Thành công'),
                'status' => 1,
                'data' => $data
            ], 200);
        }
        catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => "Có lỗi phát sinh.Xin vui lòng thử lại !",
                'status' => 0
            ],500);
        }
    }

    public function index(Request $request)
    {
        $datatable = Group::with(array('items'))->where('module', config('module.service-category'))
            ->where('status', '=', 1)->whereIn('id',[109,110,111]);

        $datatable= $datatable->paginate( '20');
        return response()->json($datatable);
    }


}
