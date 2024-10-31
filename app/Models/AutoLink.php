<?php

namespace App\Models;


use App\Traits\Metable;
use Illuminate\Database\Eloquent\Builder;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutoLink extends BaseModel
{
    use SoftDeletes;

    protected $table = 'auto_link';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'params' => 'object',
        'params_access' => 'object',
    ];


    protected $fillable = [
        'idkey',
        'shop_id',
        'module',
        'shop_id',
        'module',
        'author_id',
        'group_id',
        'parent_id',
        'title',
        'slug',
        'duplicate',
        'target',
        'url',
        'link_type',
        'params_access',
        'dofollow',
        'params',
        'percent_dofollow',
        'status',
        'started_at',
        'ended_at',
        'published_at',
    ];


    public function author(){
        return $this->hasOne(User::class,'id','author_id');
    }

    //nhớ thêm select('id','domain') để bảo mật key nạp thẻ và mua thẻ
    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }

}
