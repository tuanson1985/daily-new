<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

class ShopPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(session('shop_id')){
            $shop_id = session('shop_id');
            $shop = Shop::where('id', $shop_id)->with(['group' => function($query){
                $query->select('id','title','status', 'params');
            }])->first();
            $ratio = null;
            if(!empty($shop->group->params->nick->ratio_percent)){
               $ratio = $shop->group->params->nick;
            }elseif (!empty($shop->group->params->all->ratio_percent)) {
                $ratio = $shop->group->params->all;
            }
            config(['etc.shop_id' => $shop->id]);
            config(['etc.shop_ratio' => $ratio]);
        }

        return $next($request);
    }
}
