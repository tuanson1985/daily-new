<?php

namespace App\Http\Middleware;

use Closure;
class CleanXSS
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
        //$input = $request->all();
        //
        //array_walk_recursive($input, function(&$input) {
        //
        //    $input = strip_tags($input);
        //
        //});
        //
        //$request->merge($input);
        //dd($request->all());
        return $next($request);
    }
}
