<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SubItem extends BaseModel
{
    protected $table = 'subitems';

    protected $guarded = [];

    protected $attributes = [
        'locale' => 'vi',
    ];

    public function subitem(){
        return $this->belongsTo(Item::class);
    }

    public static function boot()
    {
        parent::boot();

        //set default auto add  scope to query
        static::addGlobalScope('global_scope', function (Builder $model){
            $model->where('subitems.shop_id', session('shop_id',1));
            $model->where('locale', session('locale'));
        });
        static::saving(function ($model) {
            $model->shop_id = session('shop_id');
            $model->locale = app()->getLocale();
        });
        //end set default auto add  scope to query

        static::deleting(function($model) {
            $model->groups()->sync([]);
            return true;
        });
    }
}
