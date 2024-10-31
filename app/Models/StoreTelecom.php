<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class StoreTelecom extends Model
{
    protected $table = 'store_telecom';

    protected $casts = [
        'params' => 'object',
    ];

    protected $fillable = [
        'shop_id',
        'title',
        'key',
        'params',
        'image',
        'order',
        'status',
        'gate_id',
    ];
    //nhớ thêm select('id','domain') để bảo mật key nạp thẻ và mua thẻ
    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope('global_scope', function (Builder $model){
            if(session('shop_id')){
                // $model->where('store_telecom.shop_id', session('shop_id'));
            }
        });
    }
}
