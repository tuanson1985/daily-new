<?php

namespace App\Models;


use App\Traits\Metable;
use Illuminate\Database\Eloquent\Builder;
use DB;
class Item extends BaseModel
{
    use Metable;
    use \Awobaz\Compoships\Compoships;
    protected $table = 'items';



    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'published_at'
    ];




    protected $attributes = [
        'locale' => 'vi'
    ];
    //Đừng có ông nào thay đổi hoặc xóa
    //Chỉ cần insert array vào là nó tự parse json
    //Lúc lấy ra thì nó là object dùng assignment như bt ( Ví dụ: $data->title)
    protected $casts = [
        'params' => 'object',
        'params_plus' => 'object',
    ];


    protected $fillable = [
        'idkey',
        'shop_id',
        'module',
        'locale',
        'parent_id',
        'title',
        'slug',
        'is_slug_override',
        'duplicate',
        'description',
        "providers_id",
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
        'price_ctv',
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
    ];

    public static function WithGroups()
    {
        $data=DB::table('items')
            ->join('groups_items', 'items.id', '=', 'groups_items.item_id')
            ->join('groups', 'groups.id', '=', 'groups_items.group_id');

        return $data;

    }

    public function groups(){
        return $this->belongsToMany('App\Models\Group','groups_items')->withPivot('order');
    }
    public function game_auto_props(){
        return $this->belongsToMany('App\Models\GameAutoProperty','item_game_auto_properties', 'item_id', 'property_id');
    }


    public function nick(){
        return $this->hasOne(Nick::class, 'id', 'id');
    }
    public function nick_complete(){
        return $this->hasOne(NickComplete::class, 'id', 'id');
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

    public function subitem(){
        return $this->hasMany(SubItem::class,'item_id');
    }

    public function item_config(){
        return $this->hasMany(ItemConfig::class,'item_id');
    }


    //nhớ thêm select('id','domain') để bảo mật key nạp thẻ và mua thẻ
    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }
    public function parrent() {
        return $this->belongsTo(static::class, 'parent_id');
    }

    //each category might have multiple children
    public function children() {
        return $this->hasMany(static::class, 'parent_id');
    }
    public function images() {
        return $this->hasMany(Media::class, 'table_id', 'id')->where('table', 'items');
    }

    public function minigameorder() {
        return $this->hasMany(Order::class,'ref_id')->where('module','like','%-log%');
    }

    public function gametypeorder() {
        return $this->hasMany(Order::class,'payment_type', 'parent_id')
            ->whereHas('item_ref',function($q){
                $q->where('module','package');
            })->where('status',1);
    }
    public function gametypechildren() {
        return $this->hasMany(static::class, 'parent_id', 'id')->where('module','package')->where('status',1);
    }
    public function packageorder() {
        return $this->hasMany(Order::class,'ref_id')
            ->whereHas('item_ref',function($q){
            $q->where('module','package');
        })->where('status',1);
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
            $model->locale = app()->getLocale();
        });
        //end set default auto add  scope to query

        static::deleting(function($model) {
            $model->groups()->sync([]);
            $model->subitem()->delete();
            return true;
        });
    }


}
