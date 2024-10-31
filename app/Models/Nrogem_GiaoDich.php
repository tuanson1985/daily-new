<?php

namespace App\Models;


use Eloquent;

class Nrogem_GiaoDich extends BaseModel
{

	protected $table = 'nrogem_giaodich';
	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at'
	];
	protected $fillable = [

        'shop_id',
		'ver',
		'order_id',
		'acc',
		'pass',
		'uname',
		'gem',
		'server',
		'status',
        'c_truoc',
        'c_sau',
		'item',
		'info_item',
        'bot_handle'
	];

    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }

    public function order()
    {
        return $this->hasOne(Order::class,'id','order_id');
    }



}
