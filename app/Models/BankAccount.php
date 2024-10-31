<?php

namespace App\Models;


use Eloquent;

class BankAccount extends BaseModel
{

	protected $table = 'bank_account';
	protected $dates = [
		'updated_at',
		'deleted_at'
	];
	protected $fillable = [
		'user_id',
		'holder_name',
		'account_number',
		'account_vi',
		'bank_id',
		'brand',
	];



    public function bank(){
        return $this->belongsTo(Bank::class);

    }

    //foreign key of user(user_id)
    public function user()
    {
        return $this->belongsTo(User::class)->select('id','username','email');
    }
}
