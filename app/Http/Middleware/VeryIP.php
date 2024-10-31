<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class VeryIP
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
        $ip = $request->getClientIp();
        $user = User::where('id',Auth::user()->id)->whereIn('account_type',[1,3])->where('status',1)->first();
        if(!$user){
            Auth::logout();
            $request->session()->flush();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect(route('admin.login'));
        }
        // $ip_login = $user->getMetaValue('ip_login');
        // if(empty($ip_login)){
        //     Auth::logout();
        //     $request->session()->flush();
        //     $request->session()->invalidate();
        //     $request->session()->regenerateToken();
        //     return redirect(route('admin.login'));
        // }
        // if($ip_login != $ip){
        //     Auth::logout();
        //     $request->session()->flush();
        //     $request->session()->invalidate();
        //     $request->session()->regenerateToken();
        //     return redirect(route('admin.login'));
        // }
        // if(!session()->has('ip_login_'.md5($user->id)) || session()->get('ip_login_'.md5($user->id)) != $ip){
        //     Auth::logout();
        //     $request->session()->flush();
        //     $request->session()->invalidate();
        //     $request->session()->regenerateToken();
        //     return redirect(route('admin.login'));
        // }
        // if(isset($user->ip_allow) && $user->ip_allow != 'all'){
        //     if (strpos($user->ip_allow, "all,") > -1 || strpos($user->ip_allow, $request->getClientIp() . ",") > -1) {
               
        //     } 
        //     else {
        //         Auth::logout();
        //         $request->session()->flush();
        //         $request->session()->invalidate();
        //         $request->session()->regenerateToken();
        //         return redirect(route('admin.login'));
        //     }
        // }
        return $next($request);
    }
}
