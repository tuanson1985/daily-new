<?php

namespace App\Models;


use Eloquent;

class NinjaXu_User extends BaseModel
{

    protected $table = 'ninjaxu_user';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $fillable = [
        'shop_id',
        'ver',
        'server',
        'acc',
        'pass',
        'uname',
        'coin',
        'zone',
        'igbanxu',
        'active'
    ];


}
