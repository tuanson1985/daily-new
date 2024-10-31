<?php

namespace App\Models;


use DateTimeInterface;
use Eloquent;

class Toolgame_Order extends BaseModel
{

	protected $table = 'toolgame_order';
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
		'web_order_id',
		'module',
		'server',
		'charname',
		'coin',
		'data',
        'bot_id',
        'trans_type',
		'status',
		'game_message',
	];


    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }

    public function web_order(){

        return $this->hasOne(Order::class,"id","web_order_id",);

    }



}
