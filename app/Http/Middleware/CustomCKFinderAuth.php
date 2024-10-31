<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomCKFinderAuth
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
        //config(['ckfinder.authentication' => function() use ($request) {
        //    return true;
        //}] );
        if (\Auth::check()) {

            config(['ckfinder.authentication' => function() use ($request) {
                return true;
            }] );
        } else {
            config(['ckfinder.authentication' => function() use ($request) {
                return false;
            }] );
        }



        return $next($request);
    }
}
