<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server_Category extends Model
{
    protected $table = 'server_category';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $fillable = [
        'title',
        'description',
        'content',
        'image',
        'order',
        'status',
        'parent_id',
        'module'
    ];

}
