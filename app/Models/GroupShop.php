<?php

namespace App\Models;


use App\Traits\Metable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupShop extends BaseModel
{
    use SoftDeletes;

    protected $table = 'groups_shops';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'meta' => 'array', /*Dùng array dễ modify, lưu key dạng int...*/
        'params' => 'object',
    ];

    protected $fillable = [
        'group_id', 'shop_id', 'title', 'slug', 'description', 'content', 'image', 'image_banner', 'image_icon', 'account_fake', 'seo_title', 'seo_description',
        'seo_robots', 'meta', 'status', 'order', 'params'
    ];

    public function group(){
        return $this->hasOne(Group::class, 'id', 'group_id');
    }

    public function shop(){
        return $this->hasOne(Shop::class, 'id', 'shop_id');
    }

}
