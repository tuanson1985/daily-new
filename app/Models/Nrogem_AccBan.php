<?php

namespace App\Models;


use Eloquent;

class Nrogem_AccBan extends BaseModel
{

	protected $table = 'nrogem_accban';
	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at'
	];
	protected $fillable = [
        'shop_id',
		'ver',
		'acc',
		'pass',
		'uname',
		'gem',
		'server',
		'item',
		'map',
		'bag',
		'status',
	];


    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }
}
