<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Eloquent;

class Telecom extends BaseModel
{

	protected $table = 'telecom';
    protected $fillable = [
        'shop_id',
        'title',
        'image',
        'key',
        'ratio',
        'type_charge',
        'seri',
        'order',
        'gate_id',
        'note',
        'status',
    ];



    public function telecom_value(){
        return $this->hasMany('App\Models\TelecomValue','telecom_id','id');
    }
    //nhớ thêm select('id','domain') để bảo mật key nạp thẻ và mua thẻ
    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }
    public static function boot()
    {
        parent::boot();
        // static::addGlobalScope('global_scope', function (Builder $model){
        //     if(session('shop_id')){
        //         $model->where('telecom.shop_id', session('shop_id'));
        //     }
        // });
        // static::saving(function ($model) {
        //     $model->shop_id = session('shop_id');
        // });
    }
}
