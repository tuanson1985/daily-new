<?php

namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends BaseModel
{

    protected $table = 'medias';

    protected $fillable = [
        'table',
        'table_id',
        'type',
        'path',
        'base_path',
    ];
}
