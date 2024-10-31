<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop_Group extends Model
{
    use HasFactory;
    protected $table = 'shop_group';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $casts = [
        'params' => 'object',
    ];

    protected $fillable = [
        'key',
        'title',
        'description',
        'language',
        'rate',
        'rate_money',
        'timezone',
        'currency',
        'status',
        'params'
    ];

    // public function shop(){
    //     return $this->belongsToMany(Shop::class,'shop_group_shop','shop_id','shop_group_id')->withPivot('order');
    // }
    public function shop()
    {
        return $this->hasMany(Shop::class,'group_id','id');
    }
}
