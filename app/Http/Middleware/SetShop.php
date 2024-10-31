<?php

namespace App\Http\Middleware;


use Closure;




class SetShop
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        //Condition teancy here
        //Default set shop_id =1
        // if(!session()->has('shop_id')){
        //     session()->put('shop_id', null);
        // }
        return $next($request);

    }
}
