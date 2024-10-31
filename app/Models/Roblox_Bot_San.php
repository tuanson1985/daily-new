<?php

namespace App\Models;

class Roblox_Bot_San extends BaseModel
{

	protected $table = 'roblox_bot_san';
	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at'
	];
    protected $casts = [
        'params' => 'object',
    ];
	protected $fillable = [
        'password',
        'params',
        'acc',
		'cookies',
        'price',
        'rate',
		'coin',
		'status',
        'id_pengiriman',
	];

}
