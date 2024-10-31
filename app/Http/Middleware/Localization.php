<?php

namespace App\Http\Middleware;

use App;
use Closure;
use Illuminate\Http\Request;

class Localization
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
        $language = 'vi';
        if ($request->get('language'))
        {
            $language = $request->get('language');
            switch ($language) {
                case 'thai':
                    $language = 'thai';
                    break;

                default:
                    $language = 'vi';
                    break;
            }

        }
        App::setLocale($language);

        return $next($request);
    }
}
