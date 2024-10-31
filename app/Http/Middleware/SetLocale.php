<?php

namespace App\Http\Middleware;

use App\Models\Language;
use App\Models\LanguageNation;
use Closure;
use Session;
use App;
use Config;



class SetLocale
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

        $locale=null;
        if(session()->has('locale')){

            $locale=session()->get('locale');

        }
        else{

            $cookieLang=$request->cookie('locale');

            $language= LanguageNation::getAllLanguageNation()->sortBy('order');

            foreach($language as $item){
                if($cookieLang==$item->locale){
                    $locale=$item->locale;
                }
            }

            if(!$language && $locale==null){
                $locale=$language[0]->locale;
            }
            else{
                $locale=config('app.fallback_locale');
            }
            session()->put('locale', $locale);
        }


        app()->setLocale($locale);
        return $next($request);

    }
}
