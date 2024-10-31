<?php

namespace App\Models;


class Roblox_Order extends BaseModel
{

	protected $table = 'roblox_order';
	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at'
	];
	protected $fillable = [

		'ver',
		'order_id',
		'acc',
		'pass',
		'uname',
		'money',
		'server',
		'status',
        'c_truoc',
        'c_sau',
		'item',
		'info_item',
		'phone',
		'type_order',
        'bot_handle'
	];

    public function item_rel()
    {
        return $this->belongsTo('App\Models\Item', 'item_id');
    }

    public function order(){
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function bot(){
        return $this->hasOne(Roblox_Bot::class, 'id', 'bot_handle');
    }
}
