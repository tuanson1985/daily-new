<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeClient extends Model
{
    protected $table = 'theme_client';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $fillable = [
        'client_name',
        'client_id',
        'theme_id',
        'param_attribute',
        'order',
        'status'
    ];

    public function shop(){
        return $this->belongsTo(Shop::class,'client_id','id');
    }

}
