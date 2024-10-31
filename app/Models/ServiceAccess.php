<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceAccess extends Model
{
    use HasFactory;
    protected $table = 'service_access';

    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'shop_id',
        'module',
        'user_id',
        'params',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service_user_access(){
        return $this->hasMany(UserAccess::class,'service_access_id');
    }
}
