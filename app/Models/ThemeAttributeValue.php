<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeAttributeValue extends Model
{
    protected $table = 'theme_attribute_value';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $fillable = [
        'theme_id',
        'theme_attribute_id',
        'order',
        'status'
    ];

}
