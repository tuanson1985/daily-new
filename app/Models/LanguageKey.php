<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LanguageKey extends BaseModel
{
    protected $table = "language_key";

    protected $fillable = [
        'title',
        'description',
        'app_client',
        'status',
        'created_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function language_nation(){
        return $this->belongsToMany(LanguageNation::class,'language_mapping','language_key_id', 'language_nation_id')
            ->withPivot('title');
    }

    public static function boot()
    {
        parent::boot();

        //set default auto add  shop_id to query
        static::addGlobalScope('global_scope', function (Builder $model) {
            $model->where('language_key.shop_id', session('shop_id'));
        });
        static::saving(function ($model) {
            $model->shop_id = session('shop_id');
        });
        //end set default auto add  shop_id to query


        static::deleting(function($model) {
            $model->language_nation()->sync([]);
            return true;
        });
    }



}
