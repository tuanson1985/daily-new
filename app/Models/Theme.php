<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $table = 'theme';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected $fillable = [
        'key',
        'title',
        'description',
        'content',
        'image',
        'order',
        'status'
    ];

    public function themes(){
        return $this->hasMany(ThemeClient::class, 'theme_id', 'id');
    }

}
