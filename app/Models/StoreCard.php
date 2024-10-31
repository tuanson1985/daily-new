<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreCard extends Model
{
    protected $table = 'store_card';


    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected $fillable = [
        'shop_id',
        'key',
        'title',
        'amount',
        'serial',
        'pin',
        'user_id',
        'buy_at',
        'ratio',
        'order_id',
        'status',
        'email',
        'expiryDate'
    ];
    public function user(){
        return $this->belongsTo(User::class)->select(['id','fullname_display','email']);

    }
}
