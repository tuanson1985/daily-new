<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeAttribute extends Model
{
    protected $table = 'theme_attribute';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $fillable = [
        'key',
        'title',
        'param_attribute',
        'order',
        'status',
        'link',
        'is_image'
    ];

}
