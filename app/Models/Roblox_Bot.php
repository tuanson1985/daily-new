<?php

namespace App\Models;

class Roblox_Bot extends BaseModel
{

	protected $table = 'roblox_bot';
	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at'
	];
    protected $casts = [
        'params' => 'object',
    ];
	protected $fillable = [
        'shop_id',
        'ver',
        'server',
        'params',
        'acc',
		'cookies',
        'uid',
        'type_order',
		'coin',
        'zone',
		'status',
        'account_type',
        'type_bot',
        'id_pengiriman',
	];

    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }

    public function roblox_bot_item(){
        return $this->hasMany(Roblox_Bot_Item::class,'bot_id');
    }

}
