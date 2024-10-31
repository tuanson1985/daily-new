<?php

namespace App\Http\Middleware;
use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;

class EncryptCookies
{


    public function handle(Request $request, Closure $next)
    {


        config(['ckfinder.authentication' => function() {
            //xử lý  auth ở đây
            return true;
        }]);
        return $next($request);


    }

    public function terminate( $request, $response) {
    }





}
