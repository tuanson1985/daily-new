<?php

namespace App\Models;


use Eloquent;

class NinjaXu_KhachHang extends BaseModel
{

	protected $table = 'ninjaxu_khachhang';
	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at'
	];
	protected $fillable = [
        'shop_id',
		'ver',
		'server',
		'order_id',
		'uname',
		'coin',
        'c_truoc',
        'c_sau',
		'status',
        'bot_handle'
	];

    public function order()
    {
        return $this->hasOne(Order::class,'id','order_id');
    }

}
