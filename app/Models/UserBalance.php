<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBalance extends BaseModel
{
    protected $table = 'user_balance';
	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at'
	];

	protected $fillable = [
		'title',
        'balance',
        'type',
	];

}
