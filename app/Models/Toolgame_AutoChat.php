<?php

namespace App\Models;


use DateTimeInterface;
use Eloquent;

class Toolgame_AutoChat extends BaseModel
{

	protected $table = 'toolgame_autochat';
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
		'text',
		'status',
	];

    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }

}
