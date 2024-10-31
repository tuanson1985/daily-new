<?php

namespace App\Models;


use App\Traits\Metable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use DB;
class ItemConfig extends BaseModel
{
    use Metable;
    protected $table = 'items_config';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at', 'published_at'
    ];
    protected $attributes = [
        'locale' => 'vi'
    ];
    protected $casts = [
        'params' => 'object',
        'params_plus' => 'object',

    ];

    protected $fillable = [
        'idkey',
        'shop_id',
        'item_id',
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
        'image_logo',
        'url',
        'url_type',
        'author_id',
        'target',
        'price_input',
        'price_old',
        'price',
        'ratio',
        'percent_sale',
        'order',
        'gate_id',
        'params',
        'params_plus',
        'total_item',
        'total_view',
        'total_order',
        'position',
        'display_type',
        'sticky',
        'is_display',
        'seo_title',
        'seo_description',
        'seo_robots',
        'status',
        'created_at',
        'ended_at',
        'started_at',
        'url_redirect_301',
        'published_at',
        'log_edit',
        'note',
    ];


    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

//    public static function WithGroups()
//    {
//        $data=DB::table('items')
//            ->join('groups_items', 'items.id', '=', 'groups_items.item_id')
//            ->join('groups', 'groups.id', '=', 'groups_items.group_id');
//
//        return $data;
//
//    }

    public function items(){
        return $this->hasOne(Item::class,'id','item_id');
    }


    public function author(){
        return $this->hasOne(User::class, 'id', 'author_id');
    }
    public function customer(){
        return $this->hasOne(User::class, 'id', 'sticky');
    }


    //nhớ thêm select('id','domain') để bảo mật key nạp thẻ và mua thẻ
    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }
    public function parrent() {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public static function boot()
    {
        parent::boot();

        //set default auto add  scope to query
        static::addGlobalScope('global_scope', function (Builder $model){
            // $model->where('locale', session('locale'));
        });
        static::saving(function ($model) {
            // $model->shop_id = session('shop_id');

        });
        //end set default auto add  scope to query

        static::deleting(function($model) {

        });
    }


}
