<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LanguageNation extends BaseModel
{
    protected $table = "language_nation";

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'title',
        'description',
        'locale',
        'image',
        'order',
        'app_client',
        'is_default',
        'status',
        'created_at',
    ];




    public function language_key(){
        return $this->belongsToMany(LanguageKey::class,'language_mapping','language_nation_id', 'language_key_id')->withPivot('title');
    }


    public static function getAllLanguageNation()
    {

        return \Cache::remember('language_nation',5, function() {
            return self::all();
        });

//         return \Cache::rememberForever('language_nation', function() {
//             return self::all();
//         });
    }

    public static function flushCache()
    {
        \Cache::forget('language_nation');
    }

    public static function boot()
    {
        parent::boot();

        //set default auto add  shop_id to query
        static::addGlobalScope('global_scope', function (Builder $model) {
            $model->where('language_nation.shop_id', session('shop_id',1));
        });
        static::saving(function ($model) {
            $model->shop_id = session('shop_id',1);
        });
        //end set default auto add  shop_id to query

        static::updated(function () {
            self::flushCache();
        });

        static::deleting(function($model) {
            $model->language_key()->sync([]);
            self::flushCache();
            return true;
        });
    }

}
