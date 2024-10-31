<?php

namespace App\Models;
class PlusMoney extends BaseModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'plus_money';

	/**
	 * The fillable fields for the model.
	 *
	 * @var    array
	 */
	protected $fillable = [

		'shop_id',
		'module',
		'user_id',
        'amount',
        'source_type',
        'source_bank',
        'status',
        'processor_id',
        'is_add',
        'description',
        'created_at',

	];


	//nhớ thêm select('id','domain') để bảo mật key nạp thẻ và mua thẻ
    public function shop(){
        return $this->belongsTo(Shop::class,'shop_id')->select('id','domain');
    }
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id')->select(['id','username','email','shop_id','type_information']);
	}

    public function processor()
    {
        return $this->belongsTo(User::class, 'processor_id')->select(['id','username','email','type_information'])->withDefault(['username' => '']);;
    }


    public function txns()
    {
        return $this->morphOne(Txns::class, 'txnsable');
    }


    public static function boot()
    {
        parent::boot();

        //set default auto add  scope to query
        // static::addGlobalScope('global_scope', function (Builder $model){
        //     $model->where('plus_money.shop_id', session('shop_id'));

        // });
        static::saving(function ($model) {

        });
        //end set default auto add  scope to query

        static::deleting(function($model) {

        });
    }

}
