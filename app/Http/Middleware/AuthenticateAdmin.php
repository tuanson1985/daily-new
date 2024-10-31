<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AuthenticateAdmin extends Middleware
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
        if (\Auth::check()){
            $user = User::query()
                ->where('status',1)
                ->where('id',\Auth::user()->id)
                ->first();
            if (isset($user) && isset($user->account_type)){
                if ($user->account_type == 1 || $user->account_type == 3){
                    return $next($request);
                }
                elseif ($user->account_type == 2){
                    return redirect('/');
                }
                else{
                    return abort('404');
                }
            }else{
                return abort('404');
            }
        }else{
            return redirect('/admin/login');
        }
    }
}
