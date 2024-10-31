<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Middlewares\PermissionMiddleware;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PermissionMiddlewareCustom
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $permission, $guard = null)
    {
        if (app('auth')->guard($guard)->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }
        if(session('shop_id') && !Auth::user()->hasRole('admin')){
            $shop = Shop::find(session('shop_id'));
            $check_user = User::where('id', app('auth')->guard($guard)->user()->id)->where(function($query) use($shop){
                $query->where('shop_access', 'all')->orWhereHas('access_shops', function($query) use($shop){
                    $query->where('id', $shop->id??null);
                })->orWhereHas('access_shop_groups', function($query) use($shop){
                    $query->where('id', $shop->group_id??null);
                });
            })->exists();
            if(!$check_user){
                return redirect()->route('admin.index');
            }
        }
        $permissions = is_array($permission) ? $permission : explode('|', $permission);
        if(!is_array($permissions)){
            throw UnauthorizedException::forPermissions($permissions);
        }
        foreach ($permissions as $permission) {
            if (Auth::user()->can($permission)) {
                $user = Auth::user();
                $roles = $user->roles;
                if(!session('shop_id')){
                    return $next($request);
                }
                foreach($roles as $key_roles => $roles_item){
                    if($roles_item->name === "admin"){
                        return $next($request);
                    }
                    $roles_check = Role::with('permissions')->where('id',$roles_item->id)->first();
                    $All_permission_in_roles = $roles_check->permissions;
                    foreach($All_permission_in_roles as $item_all_permission_in_roles){
                        if($permission === $item_all_permission_in_roles->name){
                            $roles_shop_access = $roles_item->shop_access;
                            if($roles_shop_access == null || $roles_shop_access === 'all'){
                                return $next($request);
                            }
                            $roles_shop_access = json_decode($roles_shop_access);
                            if(is_object($roles_shop_access)){
                                $roles_shop_access = (array)$roles_shop_access;
                            }
                            $shop_id = session('shop_id');
                            if(in_array($shop_id,$roles_shop_access)){
                                return $next($request);
                            }
                        }
                    }
                }
            }
        }
        throw UnauthorizedException::forPermissions($permissions);
        // return redirect()->route('admin.index');
    }
}
