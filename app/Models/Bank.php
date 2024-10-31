<?php

namespace App\Models;


use DateTimeInterface;
use Eloquent;

class Bank extends BaseModel
{

	protected $table = 'bank';
	protected $dates = [
		'updated_at',
		'deleted_at'
	];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }


	protected $fillable = [
		'title',
		'idkey',
		'key',
		'image',
		'bank_type',
		'fee',
		'fee_type',
		'status',
	];

}
