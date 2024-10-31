<?php

namespace App\Models;


use Eloquent;

class Bot_UserNap extends BaseModel
{

	protected $table = 'nrocoin_bot_usernap';
	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at'
	];
	protected $fillable = [
        'shop_id',
		'server',
		'uname',

	];

    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }


}
