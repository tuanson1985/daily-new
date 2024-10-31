<?php

namespace App\Models;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

class TxnsVp extends BaseModel
{

    //Bảng biến động số dư vật phẩm của user
	protected $table = 'txnsvp';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
	protected $fillable = [
		'shop_id',
		'trade_type',
		'user_id',
		'order_id',
		'amount',
		'last_balance',
		'description',
		'ip',
		'is_add',
		'is_refund',
		'status',
        'txnsable_id',
        'txnsable_type',
        'item_type'
	];

	//foreign key of user(user_id)
    public function user()
    {
        return $this->belongsTo(User::class)->select('id','shop_id','username','email','fullname_display','fullname','account_type');
    }


    //nhớ thêm select('id','domain') để bảo mật key nạp thẻ và mua thẻ
    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }


    public function txnsable()
    {
        return $this->morphTo();
    }



    public static function boot()
    {
        parent::boot();

        //set default auto add  scope to query
        static::addGlobalScope('global_scope', function (Builder $model) {
            //$model->where('txns.shop_id', session('shop_id'));
        });
        static::saving(function ($model) {
            $model->ip = \Request::getClientIp();
        });
        //end set default auto add  scope to query

    }



}
