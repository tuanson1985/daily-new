<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop_Group_Shop extends Model
{
    use HasFactory;
    protected $table = 'shop_group_shop';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $fillable = [
        'shop_id',
        'shop_group_id',
        'order',
    ];

    public function shop_group()
    {
        return $this->belongsTo(Shop_Group::class,'shop_group_id','id')->select('id','title','status');
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class,'shop_id','id')->select('id','title','status');
    }


}
