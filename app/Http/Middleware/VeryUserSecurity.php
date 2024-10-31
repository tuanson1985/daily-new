<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VeryUserSecurity
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
        $user = Auth::user();
        if($user->google2fa_enable != 1){
            return $next($request);
        }
//        if(!session()->has('security_2fa_web_'.md5($user->id))){
//            return redirect()->route('admin.security-2fa.very');
//        }
//        if(session()->get('security_2fa_web_'.md5($user->id)) != $user->id){
//            Auth::logout();
//            $request->session()->flush();
//            $request->session()->invalidate();
//            $request->session()->regenerateToken();
//            return redirect(route('admin.login'));
//        }
        return $next($request);
    }
}
