<?php

namespace App\Models;
class KhachHang extends BaseModel
{

	protected $table = 'nrocoin_khachhang';
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
		'money',
		'c_truoc',
		'c_sau',
		'status',
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
