<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $table = 'server';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',

    ];
    protected $fillable = [
        'ipaddress',
        'title',
        'description',
        'content',
        'image',
        'order',
        'status',
        'type',
        'shop_name',
        'price',
        'parrent_id',
        'register_date',
        'ended_at',
        'server_category_id',
        'type_category_id',
        'cf_account',
        'purchase_link',
        'cf_status'
    ];

}
