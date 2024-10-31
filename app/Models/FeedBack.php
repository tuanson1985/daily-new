<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedBack extends Model
{
    use HasFactory;
    protected $table = 'feedback';

    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $fillable = [
        'type',
        'author_id',
        'complain',
        'status',
        'seen',
        'contents',
        'files',
        'title',
        'params',
        'parent_id',
        'author_comment_id',
        'un_read',
        'au_comment_un_read'
    ];

}
