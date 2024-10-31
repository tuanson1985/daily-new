<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherUser extends Model
{
    protected $table = 'voucher_user';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
	protected $fillable = [
		'shop_id',
		'voucher_id',
		'user_id',
		'order',
        'discount',
	];

    public function author()
    {
        return $this->belongsTo(User::class,'user_id','id')->select(['id','username','fullname_display','email']);
    }
    public function voucher()
    {
        return $this->belongsTo(Voucher::class,'voucher_id','id')->select(['id','code','type','title']);
    }
}
