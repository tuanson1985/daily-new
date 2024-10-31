<?php

namespace App\Models;

class Roblox_Bot_Item extends BaseModel
{

	protected $table = 'roblox_bot_item';
	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at'
	];
    protected $casts = [
        'params' => 'object',
    ];
	protected $fillable = [
        'idkey',
        'module',
        'params',
        'quantity',
        'bot_id',
        'status',
		'title',
        'description',
        'type_item',
	];

    public function bot(){
        return $this->hasOne(Roblox_Bot::class,'bot_id');
    }

}
