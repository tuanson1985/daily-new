<?php

namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Builder;

class MoneySpent extends BaseModel
{

	protected $table = 'money_spent';
    protected $fillable = [
        'shop_id',
        'user_id',
        'spent',
    ];


    public static function boot()
    {
        parent::boot();
        ////set default auto add  scope to query
        static::addGlobalScope('global_scope', function (Builder $model) {
            $model->where('shop_id', session('shop_id'));
        });
        static::saving(function ($model) {
            $model->shop_id = session('shop_id');
        });

    }

}
