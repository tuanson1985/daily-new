<?php

namespace App\Models;


use App\Traits\Metable;
use Illuminate\Database\Eloquent\Builder;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogEdit extends BaseModel
{
    use SoftDeletes;

    protected $table = 'log_edit';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'params' => 'object',
    ];


    protected $fillable = [
        'idkey',
        'shop_id',
        'module',
        'author_id',
        'table_id',
        'table_name',
        'title_before',
        'title_after',
        'description_before',
        'description_after',
        'content_before',
        'content_after',
        'seo_title_before',
        'seo_title_after',
        'seo_description_after',
        'seo_description_before',
        'type',
        'params',
        'params_before',
        'params_after',
        'status',
        'started_at',
        'ended_at',
        'published_at',
    ];


    public function author(){
        return $this->hasOne(User::class,'id','author_id');
    }

}
