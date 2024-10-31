<?php

namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Builder;

class ShopHasModel extends BaseModel
{

    //Bảng biến động số dư của user
	protected $table = 'shop_has_model';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
	protected $fillable = [
		'shop_id',
        'modelable_id',
        'modelable_type'
	];



    //nhớ thêm select('id','domain') để bảo mật key nạp thẻ và mua thẻ
    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }


    public function shop_has_modelable()
    {
        return $this->morphTo();
    }



    public static function boot()
    {
        parent::boot();

        //set default auto add  scope to query
        static::addGlobalScope('global_scope', function (Builder $model) {

        });
        static::saving(function ($model) {

        });
        //end set default auto add  scope to query

    }



}
