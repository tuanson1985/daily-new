<?php

namespace App\Models;


use DateTimeInterface;
use Eloquent;

class Toolgame_Config extends BaseModel
{

	protected $table = 'toolgame_config';
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
		'charname',
		'coin',
		'map',
		'zone',
		'px',
		'py',
		'server',
		'subchars', // danh sách nhân vật bơm vàng
		'items',
		'autochat_id',
		'status',
	];

    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }

    public function autoChat(){

        return $this->hasOne(Toolgame_AutoChat::class,"id","autochat_id",);
    }

}
