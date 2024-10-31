<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
	protected $table = 'voucher';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
	protected $fillable = [
		'idkey',
		'shop_id',
		'module',
		'title',
		'description',
		'code',
		'uses',
		'max_uses',
		'max_uses_user',
		'max_uses_device',
		'type',
        'discount_percentage',
        'discount_amount',
        'is_fixed',
        'params',
        'status',
        'started_at',
        'ended_at'
	];

	public function voucher_user(){
        return $this->belongsTo(VoucherUser::class,'id','voucher_id');
    }
}
