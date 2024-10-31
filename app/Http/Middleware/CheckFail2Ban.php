<?php

namespace App\Http\Middleware;

use Closure;
class CheckFail2Ban
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
        //check banned
        if(auth()->check() && (auth()->user()->status == 0)){
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->with('error', __('Tài khoản của bạn đã bị khóa'));

        }

        if(auth()->check() && (auth()->user()->status == 2)){
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->with('error', __('Tài khoản của bạn đang chờ QTV phê duyệt'));

        }
        //check fail too many verify password2
        if(auth()->check() && session('fail_password2')>=2){

            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->with('error', __('Bạn đã nhập sai mật khẩu cấp 2 quá số lần cho phép'));

        }

        return $next($request);
    }
}
