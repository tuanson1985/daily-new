<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;
use App\Library\Helpers;
use App\Library\HelperShopClient;

class VerifyShop
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $shop = Shop::where('domain',$request->domain)
            ->where('secret_key',$request->secret_key)
            ->where('status',1)->with(['group' => function($query){
                $query->select('id','title','status', 'params');
            }])->first();

        if(!$shop){
            return response()->json([
                'message' => 'Domain chưa được đăng kí',
                'status' => 406,
            ], 200);
        }
        $ratio = null;
        if(!empty($shop->group->params->nick->ratio_percent)){
           $ratio = $shop->group->params->nick;
        }elseif (!empty($shop->group->params->all->ratio_percent)) {
            $ratio = $shop->group->params->all;
        }
        config(['etc.shop' => $shop]);
        config(['etc.shop_id' => $shop->id]);
        config(['etc.shop_ratio' => $ratio]);

        //cái này định danh shop.Vui lòng ko chỉnh sửa dòng dưới
        $request->merge(["shop_id" => $shop->id, 'shop_group_id' => $shop->group_id]);
        return $next($request);

        // if(!$request->filled('shop_token')){
        //     return response()->json([
        //         'message' => 'Trường shop_token là bắt buộc.',
        //         'status' => -999
        //     ], 401);
        // }
        // $very_token = HelperShopClient::VeryShopId($request->shop_token);
        // dd($very_token);
        //cái này định danh shop.Vui lòng ko chỉnh sửa dòng dưới

    }
}
