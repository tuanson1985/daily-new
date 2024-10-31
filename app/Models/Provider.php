<?php

namespace App\Models;
use App\Traits\Metable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends BaseModel
{
    use SoftDeletes;
    use Metable;

    protected $table = 'providers';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'title',
        'status',
        'created_at',
    ];

}
