<?php

namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Meta extends BaseModel
{
    public $timestamps = false;

    protected $table = 'meta';

    protected $fillable = [
        'metable_id',
        'metable_type',
        'key',
        'value',
    ];

    protected $attributes = [
        'value' => '',
    ];

    public function metable(): MorphTo
    {
        return $this->morphTo();
    }


}
