<?php

namespace App\Models;

use App\Traits\Metable;
use Illuminate\Database\Eloquent\Builder;
use DB;
class Nick extends BaseModel
{
    use Metable;
    use \Awobaz\Compoships\Compoships;
    protected $table = null;

    public function __construct(array $attributes = []){
        parent::__construct($attributes);
        if (empty($attributes['table']) && !empty(config('etc.acc.table_name'))) {
            $attributes['table'] = config('etc.acc.table_name');
        }
        if (!empty($attributes['table']) && \Schema::hasTable($attributes['table'])) {
            $this->table = $attributes['table'];
        }else{
            $this->table = 'nicks';
        }
    }

    protected $dates = ['created_at','updated_at','deleted_at','published_at','started_at','ended_at'];

    protected $casts = ['params' => 'array','meta' => 'array'];


    protected $fillable = [
        'id','idkey','shop_id','module','parent_id','title','slug','is_slug_override','description','content','image','image_extension','author_id','target','amount_ctv',
        'amount','price_old','price','percent_sale','order','params','meta','position','display_type','sticky','is_display','seo_title','seo_description','status',
        'created_at','ended_at','started_at','published_at'
    ];

    public function groups(){
        return $this->belongsToMany('App\Models\Group','groups_nicks', 'nick_id', 'group_id')->withPivot('order');
    }
    public function game_auto_props(){
        return $this->belongsToMany('App\Models\GameAutoProperty','item_game_auto_properties', 'item_id', 'property_id');
    }

    public function author(){
        return $this->hasOne(User::class, 'id', 'author_id');
    }
    public function customer(){
        return $this->hasOne(User::class, 'id', 'sticky');
    }
    public function category(){
        return $this->hasOne(Group::class, 'id', 'parent_id');
    }
    public function category_custom(){
        return $this->hasOne(GroupShop::class, 'group_id', 'parent_id');
    }
    public function access_category(){
        return $this->hasOne(GameAccess::class, ['group_id', 'user_id'], ['parent_id', 'author_id']); /*Compoships*/
    }
    public function access_shops(){
        return $this->belongsToMany(Shop::class, 'shop_access', 'user_id', 'shop_id', 'author_id', 'id');
    }
    public function access_shop_groups(){
        return $this->belongsToMany(Shop_Group::class, 'user_shop_group_access', 'user_id', 'group_id', 'author_id', 'id');
    }
    public function acc_txns(){
        return $this->hasMany(Txns::class, 'txnsable_id', 'id')->where(['txnsable_type' => 'App\Models\Item', 'trade_type' => 'buy_acc']);
    }
    public function acc_txns_buy(){
        return $this->hasOne(Txns::class, 'txnsable_id', 'id')->where(['txnsable_type' => 'App\Models\Item', 'trade_type' => 'buy_acc', 'is_add' => 1, 'is_refund' => 0]);
    }
    public function txns_order(){
        return $this->hasOne(Order::class, 'ref_id', 'id')->where(['module' => 'buy_acc'])->orderBy('id', 'desc');
    }
    // public function getTxnsOrderPriceAttribute(){
    //     return $this->txns_order()->price;
    // }
    public function shop(){
        return $this->hasOne(Shop::class, 'id', 'shop_id')->select('id','domain');
    }

    public static function boot() {
        parent::boot();
        static::deleting(function($model) {
            $model->groups()->sync([]);
            if (!empty($model->image)) {
                try {
                    \App\Library\MediaHelpers::delete_image($model->image);
                } catch (\Exception $e) {}
            }
            if (!empty($model->image_extension)) {
                foreach (explode('|', $model->image_extension) as $key => $path) {
                    if (!empty($path)) {
                        try {
                            \App\Library\MediaHelpers::delete_image($path);
                        } catch (\Exception $e) {}
                    }
                }
            }
        });
    }
}
