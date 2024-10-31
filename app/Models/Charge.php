<?php

namespace App\Models;


use DateTimeInterface;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{

	protected $table = 'charge';
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
		'shop_id',
		'type_charge',
		'user_id',
		'gate_id',
		'telecom_key',
		'pin',
		'serial',
        'declare_amount',
		'amount',
		'ratio',
		'real_received_amount',
		'tnxs_id',
		'response_code',
		'response_mess',
		'tranid',
		'description',
		'ip',
		'processor_username',
		'process_at',
		'process_log',
		'api_type',
        'request_at',
		'request_id',
        'finish_at',
		'status',
		'status_callback',
		'money_received',
	];


    public function user(){
        return $this->belongsTo(User::class)->select(['id','username','email','fullname']);

    }
    public function processor(){
        return $this->belongsTo(User::class,'processor_id','id')->select(['id','username','email','fullname_display']);

    }

    public function txns()
    {
        return $this->morphOne(Txns::class, 'txnsable');
    }
	public function shop(){
        return $this->belongsTo(Shop::class);
    }

    public static function boot()
    {
        parent::boot();
        ////set default auto add  scope to query
        // static::addGlobalScope('global_scope', function (Builder $model) {
        //     $model->where('charge.shop_id', session('shop_id'));
        // });
        // static::saving(function ($model) {
        //     $model->shop_id = session('shop_id');
        // });

    }

}
