<?php

namespace App\Models;


use Eloquent;

class Bot extends BaseModel
{

	protected $table = 'nrocoin_bot';
	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at'
	];
	protected $fillable = [
        'shop_id',
		'ver',
		'server',
		'acc',
		'pass',
		'active',
		'uname',
		'coin',
		'zone',
		'app_client',
	];


    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }


    public function shop_has_model()
    {
        return $this->morphOne(ShopHasModel::class, 'shop_has_modelable');
    }
}
