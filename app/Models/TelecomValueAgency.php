<?php

namespace App\Models;


use Eloquent;

class TelecomValueAgency extends BaseModel
{

	protected $table = 'telecom_value_agency';
    protected $fillable = [

        'username',
        'telecom_id',
        'telecom_key',
        'amount',
        'ratio',
    ];
}
