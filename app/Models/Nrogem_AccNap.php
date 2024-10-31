<?php

namespace App\Models;


use Eloquent;

class Nrogem_AccNap extends BaseModel
{

    protected $table = 'nrogem_accnap';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $fillable = [
        'shop_id',
        'server',
        'uname',
    ];


    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }

}
