<?php

namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Builder;

class TelecomValue extends BaseModel
{

	protected $table = 'telecom_value';
    protected $fillable = [
        'shop_id',
        'telecom_id',
        'amount',
        'telecom_key',
        'ratio_true_amount',
        'ratio_false_amount',
        'agency_ratio_true_amount',
        'agency_ratio_false_amount',
        'type_charge',
        'status'
    ];
    //nhớ thêm select('id','domain') để bảo mật key nạp thẻ và mua thẻ
    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }
    public static function boot()
    {
        parent::boot();
        // static::addGlobalScope('global_scope', function (Builder $model){
        //     if(session('shop_id')){
        //         $model->where('telecom_value.shop_id', session('shop_id'));
        //     }
        // });
        // static::saving(function ($model) {
        //     $model->shop_id = session('shop_id');
        // });
    }




}
