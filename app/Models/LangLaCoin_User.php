<?php

namespace App\Models;


use Eloquent;

class LangLaCoin_User extends BaseModel
{

    protected $table = 'langlacoin_user';
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
        'active',
        'uname',
        'coin',
        'zone',
    ];


}
