<?php

namespace App\Models;


use DateTimeInterface;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

class Order extends BaseModel
{

    //demo lưu order với bảng biến động số dư
    //Order::create([
    //    //thông tin cần lưu....
    //'module'=>'booking', //ví dụ đây là booking
    //
    //])->txns()->create([
    //'user_id'=>$userTransaction->id,
    //'trade_type'=>'booking', //ví dụ đây là booking
    //'is_add'=>'0',//Trừ tiền
    //'amount'=>$amount,
    //'last_balance'=>$userTransaction->balance,
    //'description'=>$request->description,
    //'ip'=>$request->getClientIp(),
    //'status'=>1
    //]);


    protected $table = 'order';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    //Cấm ông nào thay đổi hoặc xóa
    //Chỉ cần insert array vào là nó tự parse json
    //Lúc lấy ra thì nó là object dùng assignment như bt ( Ví dụ: $data->title)
    protected $casts = [
        'params' => 'object',
    ];

    protected $fillable = [
        'idkey',
        'shop_id',
        'module',
        'locale',
        'payment_type',
        'title',
        'description',
        'ended_at',
        'content',
        'author_id',
        'price_base',
        'price',
        'price_ctv',
        'ratio_ctv',
        'real_received_price_ctv',
        'ratio',
        'ratio_exchange_rate',
        'additional_amount',
        'gate_id',
        'bank_id',
        'real_received_price',
        'params',
        'ref_id',
        'order',
        'sticky',
        'processor_id',
        'acc_id',
        'expired_lock',
        'position',
        'response_code',
        'response_mess',
        'status',
        'value_gif_bonus',
        'request_id_customer',
        'request_id_provider',
        'process_at',
        'recheck_at',
        'price_input',
        'finished_at',
        'position1',
        'url',
        'app_client',
        'type_refund'
    ];

    //one to one
    public function author()
    {
        return $this->belongsTo(User::class,'author_id','id')->select(['id','username','email','type_information']);;
    }

    //one to one
    public function processor()
    {
        return $this->belongsTo(User::class,'processor_id','id')->select(['id','username','email','account_type']);
    }

    public function txns()
    {
        return $this->morphOne(Txns::class, 'txnsable');
    }

    public function txns_order(){
        return $this->hasOne(Txns::class,'order_id');
    }

    public function workflow_reject(){
        return $this->hasMany(OrderDetail::class,'order_id')->where('status',3);
    }

    public function user_ref()
    {
        return $this->belongsTo(User::class,'ref_id','id')->select(['id','username','email']);
    }

    public function item_ref()
    {
        return $this->belongsTo(Item::class,'ref_id','id')->with('parrent');
    }

    public function itemconfig_ref()
    {
        return $this->belongsTo(Item::class,'ref_id','id');
    }

    public function itemconfig_minigame()
    {
        return $this->belongsTo(ItemConfig::class,'sticky','id')->select(['id','idkey','gate_id','slug','title','params','parent_id','item_id']);
    }

    public function item_acc()
    {
        return $this->belongsTo(Item::class,'acc_id','id')->select(['id','title','position']);
    }

    public function group()
    {
        return $this->belongsTo(Group::class,'gate_id','id')->select(['id','title','params']);
    }

    public function order_detail(){
        return $this->hasMany(OrderDetail::class,'order_id');
    }

    public function order_detail_orderby(){
        return $this->hasOne(OrderDetail::class,'order_id')->orderBy('updated_at', 'desc');
    }

    public function order_refund(){
        return $this->hasOne(OrderDetail::class,'order_id')
            ->where('module','service-refund');
    }

    public function order_service_workflow(){
        return $this->hasMany(OrderDetail::class,'order_id')
            ->where('module','service-workflow');
    }

    public function order_service_workname(){
        return $this->hasMany(OrderDetail::class,'order_id')
            ->where('module','service-workname');
    }

    public function order_nick_refund(){
        return $this->hasOne(OrderDetail::class,'order_id')
            ->where('module','nick-refund');
    }

    public function order_pengiriman(){
        return $this->hasOne(OrderDetail::class,'order_id')
            ->where('module','pengiriman');
    }

    public function order_rbx(){
        return $this->hasOne(OrderDetail::class,'order_id')
            ->where('module','rbx_api');
    }

    public function workflow_excel(){
        return $this->hasMany(OrderDetail::class,'order_id')->where('module','service-workname');
    }

    public function workflow_excelv2(){
        return $this->hasOne(OrderDetail::class,'order_id')->where('module','service-workname');
    }

    public function workflow_reception(){
        return $this->hasOne(OrderDetail::class,'order_id')
            ->where('module','service-workflow')
            ->where('status',2);
    }

    public function workflow(){
        return $this->hasMany(OrderDetail::class,'order_id');
    }
    public function workname(){
        return $this->hasMany(OrderDetail::class,'order_id');
    }

    public function reject_detail(){
        return $this->hasOne(OrderDetail::class,'order_id')
            ->where('module',config('module.service-workflow.key'))
            ->where('status',3);
    }

    public function pengiriman_detail(){
        return $this->hasOne(OrderDetail::class,'order_id')
            ->where('module', 'pengiriman');
    }

    public function khachhang(){
        return $this->hasOne(KhachHang::class,'order_id');
    }

    public function nrogem_giaodich(){
        return $this->hasOne(Nrogem_GiaoDich::class,'order_id');
    }

    public function ninjaxu_khachhang(){
        return $this->hasOne(NinjaXu_KhachHang::class,'order_id');
    }

    public function bank()
    {
        return $this->belongsTo(Item::class,'bank_id','id')->select(['id','title','params']);
    }

    //bắt buộc thêm select('id','domain') để bảo mật ẩn key nạp thẻ và mua thẻ
     public function shop(){
         return $this->belongsTo(Shop::class)->select('id','domain');
     }

    public function item_rels(){
        return $this->hasMany(Nrogem_GiaoDich::class,'order_id');
    }

    public function nick()
    {
        return $this->belongsTo(Item::class,'ref_id','id');
     }

    public function roblox_order(){
        return $this->hasOne(Roblox_Order::class,'order_id');
    }

    public function card()
    {
        return $this->hasMany('App\Models\StoreCard',"order_id","id")->select('id','order_id','serial','pin','amount');
    }
    public static function boot()
    {
        parent::boot();

        //set default auto add  scope to query
        static::addGlobalScope('global_scope', function (Builder $model){
            //if(session('shop_id')){
            //    $model->where('order.shop_id', session('shop_id'));
            //}
        });
        // static::saving(function ($model) {
        //     $model->shop_id = session('shop_id');
        // });
        //end set default auto add  scope to query

        static::deleting(function($model) {

        });
    }





}
