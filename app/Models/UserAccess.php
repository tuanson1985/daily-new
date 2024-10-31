<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    use HasFactory;
    protected $table = 'user_access';

    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'service_access_id',
        'module',
        'user_id',
        'params',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id')->select(['id','username','email','account_type']);
    }

    public function service_access(){
        return $this->belongsTo(ServiceAccess::class,'service_access_id','id')->where('module','service');
    }

}
