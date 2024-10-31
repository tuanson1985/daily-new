<?php

namespace App\Http\Middleware;

use Closure;

use Debugbar;
use Illuminate\Support\Facades\Auth;


class DebugbarAllow
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
         //if(Auth::check() && Auth::user()->id==1 ) {
         //     Debugbar::enable();
         //}
         //else{
         //    Debugbar::enable();
         //}


        return $next($request);
    }
}
