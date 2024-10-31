<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;


class CkFinder
{


    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        if (\Str::of($request->route()->getName())->endsWith('_acc')) {
            $id = $request->route('id');
            /*Set folder ckfinder*/

            config(['ckfinder.backends.default.root' => storage_path("app/public/upload/product-acc/{$id}")]);
            config(['ckfinder.backends.default.baseUrl' => env('MEDIA_URL')."/storage/upload/product-acc/{$id}/"]);
        }
        if (\Str::of($request->route()->getName())->endsWith('_folder_id')) {
            $id = $request->route('id');
            $folder = $request->route('folder');
            /*Set folder ckfinder*/
            config(['ckfinder.backends.default.root' => storage_path("app/public/upload/{$folder}/{$id}")]);
            config(['ckfinder.backends.default.baseUrl' => env('MEDIA_URL')."/storage/upload/{$folder}/{$id}/"]);
        }

        if (\Str::of($request->route()->getName())->endsWith('_service_config')) {
            $id = $request->route('id');
            $folder = $request->route('folder');
            /*Set folder ckfinder*/
            config(['ckfinder.backends.default.root' => storage_path("app/public/upload/{$folder}/{$id}")]);
            config(['ckfinder.backends.default.baseUrl' => env('MEDIA_URL')."/storage/upload/{$folder}/{$id}/"]);
        }

        if (\Str::of($request->route()->getName())->endsWith('_advertise')) {
            $id = $request->route('id');
            $folder = $request->route('folder');
            /*Set folder ckfinder*/
            config(['ckfinder.backends.default.root' => storage_path("app/public/upload/{$folder}/")]);
            config(['ckfinder.backends.default.baseUrl' => env('MEDIA_URL')."/storage/upload/{$folder}/"]);
        }

        if (\Str::of($request->route()->getName())->endsWith('_minigame')) {
            $id = $request->route('id');
            $folder = $request->route('folder');
            /*Set folder ckfinder*/
            config(['ckfinder.backends.default.root' => storage_path("app/public/upload/{$folder}")]);
            config(['ckfinder.backends.default.baseUrl' => env('MEDIA_URL')."/storage/upload/{$folder}/"]);
        }
        if (\Str::of($request->route()->getName())->endsWith('_setting')) {
            $id = $request->route('id');
            $folder = $request->route('folder');
            /*Set folder ckfinder*/
            config(['ckfinder.backends.default.root' => storage_path("app/public/upload/{$folder}")]);
            config(['ckfinder.backends.default.baseUrl' => env('MEDIA_URL')."/storage/upload/{$folder}/"]);
        }
        if (\Str::of($request->route()->getName())->endsWith('_article')) {
            $id = $request->route('id');
            $folder = $request->route('folder');
            /*Set folder ckfinder*/
            config(['ckfinder.backends.default.root' => storage_path("app/public/upload/{$folder}")]);
            config(['ckfinder.backends.default.baseUrl' => env('MEDIA_URL')."/storage/upload/{$folder}/"]);
        }
        return $next($request);
    }


    public function terminate( $request, $response) {
    }


}
