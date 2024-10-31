<?php

namespace App\Models;
use App\Traits\Metable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends BaseModel
{
    use SoftDeletes;
    use Metable;

    protected $table = 'groups';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];




    //Cấm ông nào thay đổi hoặc xóa
    //Chỉ cần insert array vào là nó tự parse json
    //Lúc lấy ra thì nó là object dùng assignment như bt ( Ví dụ: $data->title)

    protected $casts = [
        'params' => 'object',
        'params_plus' => 'object',
        'params_error' => 'object',
    ];


    protected $fillable = [

        'idkey',
        'shop_id',
        'module',
        'module',
        'locale',
        'parent_id',
        'title',
        'slug',
        'is_slug_override',
        'duplicate',
        'description',
        'content',
        'image',
        'image_extension',
        'image_banner',
        'image_icon',
        'url',
        'type_url',
        'author_id',
        'target',
        'price',
        'params',
        'params_plus',
        'params_error',
        'total_item',
        'total_view',
        'total_order',
        'order',
        'position',
        'display_type',
        'sticky',
        'is_display',
        'seo_title',
        'seo_description',
        'seo_robots',
        'status',
        'started_at',
        'ended_at',
        'published_at',
        'created_at',
        'account_fake',
    ];

    protected $attributes = [
        'locale' => 'vi',
    ];



    public function items(){
        return $this->belongsToMany(Item::class,'groups_items')->with('parrent')->withPivot('order');
    }
    public function nicks(){
        return $this->belongsToMany(Nick::class,'groups_nicks');
    }

    public function order_gate(){
        return $this->hasMany(Order::class,'gate_id');
    }

    public function author(){
        return $this->belongsTo(User::class,'id','author_id');
    }

    //nhớ thêm select('id','domain') để bảo mật key nạp thẻ và mua thẻ
    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }

    function childs(){
        return $this->hasMany(Group::class, 'parent_id', 'id')->orderBy('order')->with('childs')
        ->select('id', 'parent_id', 'module', 'title', 'slug', 'image', 'seo_title', 'seo_description', 'seo_robots', 'status', 'display_type', 'position', 'is_slug_override', 'is_display', 'deleted_at', 'params');
    }

    function parent(){
        return $this->hasOne(Group::class, 'id', 'parent_id')
        ->select('id', 'parent_id', 'module', 'title', 'slug', 'image', 'seo_title', 'seo_description', 'seo_robots', 'status', 'display_type', 'position', 'is_slug_override', 'is_display', 'deleted_at', 'params');
    }

    public function custom(){
        return $this->hasOne(GroupShop::class, 'group_id', 'id');
    }

    public function customs(){
        return $this->hasMany(MinigameDistribute::class, 'group_id', 'id');
    }

    public function auto_properties(){
        return $this->hasMany(GameAutoProperty::class, 'parent_id', 'id')->where('parent_table', 'groups');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function($group) {
            $group->items()->sync([]);

        });
    }


}
