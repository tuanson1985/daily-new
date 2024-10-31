<?php

namespace App\Models;


use DateTimeInterface;
use Eloquent;

class Toolgame_Account extends BaseModel
{

	protected $table = 'toolgame_account';
	protected $dates = [
		'updated_at',
		'deleted_at'
	];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }


	protected $fillable = [
		'shop_id',
		'module',
		'username',
		'password',
		'server',
		'image',
		'order',
		'status',
		'info',
		'account_type',
		'config_id',
		'created_at',
	];


    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }

    public function config(){
        return $this->hasOne(Toolgame_Config::class,"id","config_id");
    }


}
