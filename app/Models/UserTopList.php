<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class UserTopList extends BaseModel
{
    protected $table = 'user_toplist';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $fillable = [
        'user_id',
        'ref_id',
        'amount',
        'comment',
        'status',
        'action',
    ];


    //foreign key of user(user_id)
    public function user()
    {
        return $this->belongsTo(User::class)->select('id','username','email');
    }

    //foreign key of user(ref_id)
    public function ref_id()
    {
        return $this->belongsTo(User::class,'ref_id','id')->select('id','username','email');
    }
}
