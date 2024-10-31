<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends BaseModel
{
    protected $table = 'order_detail';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }



    protected $fillable = [
        'idkey',
        'shop_id',
        'title',
        'description',
        'content',
        'module',
        'order_id',
        'item_id',
        'quantity',
        'unit_price',
        'unit_price_ctv',
        'discount_percentage',
        'discount_price',
        'author_id',
        'status'
    ];



    public function author()
    {
        return $this->belongsTo(User::class,'author_id','id')->select(['id','username','email']);
    }

    public function order(){
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

}
