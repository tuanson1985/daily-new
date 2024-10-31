<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\ServiceAuto\RobloxJob;
use App\Library\ChargeGameGateway\RobloxGate;
use App\Library\HelperItemDaily;
use App\Library\Helpers;
use App\Models\Group;
use App\Models\ItemConfig;
use App\Models\KhachHang;
use App\Models\MinigameDistribute;
use App\Models\NinjaXu_KhachHang;
use App\Models\Nrogem_AccBan;
use App\Models\Nrogem_GiaoDich;
use App\Models\Order;
use App\Models\Item;
use App\Models\OrderDetail;
use App\Models\Roblox_Order;
use App\Models\Shop;
use App\Models\Voucher;
use App\Models\Txns;
use App\Models\TxnsVp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Setting;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Library\HelpItemAdd;


class MinigameController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth:api');
    }

    public function getListMiniGame(Request $request){
        try {

            $module = $request->module;
            $shop_id = $request->shop_id;

            $group = Group::select('id','title','price','params','module','slug','image','image_icon','seo_title','position','description','seo_description')
                ->where('module', 'minigame-category')
                ->where('status', 1)
                ->with('customs', function ($query) use ($shop_id) {
                    $query->where('shop_id', $shop_id)->orderBy('order');
                })
                ->whereHas('customs', function ($query) use ($shop_id) {
                    $query->where('shop_id', $shop_id)->where('status', 1)->orderBy('order');
                })->orderBy('order')
                ->get();

            foreach ($group as $key) {
                $key->title = $key->customs[0]->title;
                $key->slug = $key->customs[0]->slug;
                $key->params = $key->customs[0]->params;
                $key->image = $key->customs[0]->image;
                $key->order = $key->customs[0]->order;
                $key->description = $key->customs[0]->description;
                $key->seo_description = $key->customs[0]->seo_description;
                $key->seo_title = $key->customs[0]->seo_title;
                $key->order_gate_count = $key->customs[0]->total_order??0;
            }

            return response()->json([
                'msg' => __('Thành công'),
                'data' => $group,
                'status' => 1
            ], 200);
        }catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'msg' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }

    public function getMiniGameInfo(Request $request){
        try {

            $module = $request->module;
            $limit = $request->limit;
            $id = $request->id;
            $module = $request->module.'-log';
            $moduleCate = 'minigame-category';
            $shop_id = $request->shop_id;
            $freewheel = 0;
            $pointuser = 0;
            $num_roll = 0;

            $group = Group::select('id','title','price','params','price','image_icon','content','position','description')
                ->with(['items'=>function($query) use ($request){
                    $query->whereHas('parrent', function ($querysub) use ($request) {
                        $querysub->where('status', 1);
                    });
                    $query->with('children',function($querysub) use ($request){
                        $querysub->where('shop_id', $request->shop_id);
                    });
                    $query->whereHas('children',function($querysub) use ($request){
                        $querysub->where('shop_id', $request->shop_id);
                    });
                    $query->select('item_id','title','image','params','parent_id','items.id')->orderByRaw('items.order');
                }])
                ->whereHas('items',function($query) use ($request){
                    $query->whereHas('parrent', function ($querysub)  use ($request){
                        $querysub->where('status', 1);
                    });
                    $query->with('children',function($querysub) use ($request){
                        $querysub->where('shop_id', $request->shop_id);
                    });
                    $query->whereHas('children',function($querysub) use ($request){
                        $querysub->where('shop_id', $request->shop_id);
                    });
                    $query->select('item_id','items.order','title','image','params','parent_id','items.id')->orderBy('order');
                })
                ->with('customs', function ($query) use ($shop_id) {
                    $query->where('shop_id', $shop_id);
                })
                ->whereHas('customs', function ($query) use ($shop_id) {
                    $query->where('shop_id', $shop_id)->where('status', 1);
                })
                ->where('module', $moduleCate)
                ->where('id', $id)
                ->where('status', 1)->first();

            $group->title = $group->customs[0]->title;
            $group->seo_title = $group->customs[0]->seo_title;
            $group->slug = $group->customs[0]->slug;
            $group->params = $group->customs[0]->params;
            $group->image = $group->customs[0]->image;
            $group->image_icon = $group->customs[0]->image_icon;
            $group->content = $group->customs[0]->content;
            $group->description = $group->customs[0]->description;
            $group->seo_description = $group->customs[0]->seo_description;
            $group->image = $group->customs[0]->image;
            $group->order_gate_count = $group->customs[0]->total_order??0;
            $group->number_item = 0;

            $numberitem = Item::where('module', config('module.minigame.module.gametype'))
                ->where('parent_id', $group->customs[0]->params->game_type)
                ->where('status', 1)->orderBy('order')->first();

            $group->title_item = $numberitem->image??'';

            $game_type = $group->params->game_type;
            $number_item = null;
            $name_item = null;

            if (Auth::guard('api')->check()) {
                $user = Auth::guard('api')->user();

                $userTransaction = User::where('id', $user->id)->firstOrFail();
                $number_item = $userTransaction['ruby_num'.$game_type];
                $name_item = Item::where('module', config('module.minigame.module.gametype'))
                    ->where('parent_id', $game_type)->first();

                $userbalance = $group->params->type_charge == 1?$userTransaction->balance_lock:$userTransaction->balance;
                $fee = $group->price;
                $num_roll = $fee==0?0:FLOOR($userbalance/$fee);
                if($user->free_wheel_type == $id && $user->free_wheel > 0)
                {
                    $freewheel = $user->free_wheel;
                }
                $pointuser =  $user->point;

                $group->number_item = $userTransaction['ruby_num'.$group->customs[0]->params->game_type];
            }
            else
            {
                $pointuser = 50;
            }

            $log = Order::select('id',
                'gate_id',
                'author_id',
                'ref_id',
                'price',
                'real_received_price',
                'value_gif_bonus',
                'created_at')
                ->with('author')
                ->with('group',function($querysub) use ($request){
                    $querysub->select(['id','title','params']);
                })->with('item_ref', function($query) use ($request){
                    $query->with('children',function($querysub) use ($request){
                        $querysub->where('shop_id', $request->shop_id);
                    });
                })
                ->where('module', $module)
                ->where('gate_id',$id)
                ->where('shop_id', $shop_id)
                ->orderBy('created_at','desc')
                ->limit($limit)->get();

            $checkPoint = isset($group->params->point)?$group->params->point:0;
            $checkVoucher = isset($group->params->voucher)?$group->params->voucher:0;

            $seddingchat = Item::where('module','minigame-seedingchat')
                ->where('shop_id',$shop_id)->where('status',1)
                ->select('module','title','price_old','price','params','params_plus','total_item','status')->first();

            $data=[
                'log' => $log,
                'seddingchat' => $seddingchat,
                'group' =>$group,
                'freewheel' =>$freewheel,
                'num_roll' =>$num_roll,
                'pointuser' =>$pointuser,
                'name_item' =>$name_item,
                'number_item' =>$number_item,
                'checkPoint' => $checkPoint,
                'checkVoucher' => $checkVoucher
            ];
            return response()->json([
                'msg' => __('Thành công'),
                'data' => $data,
                'status' => 1
            ], 200);
        }catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'msg' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }

    public function getLog(Request $request){
        try {
            //            $id = $request->id;
            $module = $request->module.'-log';
            $shop_id = $request->shop_id;
            $page = $request->page;
            $paginate = 10;

            $data= Order::select(
                'id',
                'gate_id',
                'author_id',
                'real_received_price',
                'value_gif_bonus',
                'ref_id',
                'price',
                'created_at')
                ->with('author')
                ->with('group',function($querysub) use ($request){
                    $querysub->select(['id','title','params']);
                })
                ->with('item_ref', function($query) use ($request){
                    $query->with('children',function($querysub) use ($request){
                        $querysub->where('shop_id', $request->shop_id);
                    });
                })
                ->whereNull('acc_id')
                ->where('module', $module)
                ->where('shop_id', $shop_id)
                //                ->where('gate_id', $id)
                ->where('author_id', Auth::guard('api')->user()->id);

            if ($request->filled('id')) {
                $data->where('gate_id', $request->get('id'));
            }

            if ($request->filled('gift_name'))  {
                $data->whereHas('item_ref', function ($query) use ($request) {
                    $query->whereHas('children', function ($querysub) use ($request) {
                        $querysub->where('title','like','%'.$request->get('gift_name').'%')->where('shop_id', $request->shop_id);
                    });
                });
            }

//            if ($request->filled('started_at')) {
//                $data->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i', $request->get('started_at')));
//            }
//
//            if ($request->filled('ended_at')) {
//                $data->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i', $request->get('ended_at')));
//            }

            $dataFirst = $data->orderBy('created_at','desc')->get();

            $data = collect($dataFirst)->slice($paginate * ($page - 1), $paginate);

            $data = new LengthAwarePaginator($data, count($dataFirst), $paginate, $page, [
                'path'  => $request->url(),
            ]);
            return response()->json($data, 200);
        }catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'msg' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }

    public function getLogAcc(Request $request){
        try {

            $module = $request->module.'-log';
            $shop_id = $request->shop_id;
            $page = $request->page;
            $paginate = 10;

            $data= Order::select(
                'id',
                'gate_id',
                'author_id',
                'ref_id',
                'acc_id',
                'price',
                'real_received_price',
                'value_gif_bonus',
                'created_at')
                ->with('author')
                ->with('group',function($querysub) use ($request){
                    $querysub->select(['id','title','params']);
                })
                ->with('item_ref', function($query) use ($request){
                    $query->with('children',function($querysub) use ($request){
                        $querysub->where('shop_id', $request->shop_id);
                    });
                })->with('item_acc')
                ->whereNotNull('acc_id')
                ->where('shop_id', $shop_id)
                ->where('module', $module)
                ->where('author_id', Auth::guard('api')->user()->id);

            if ($request->filled('id')) {
                $data->where('gate_id', $request->get('id'));
            }

            if ($request->filled('gift_name'))  {
                $data->whereHas('item_ref', function ($query) use ($request) {
                    $query->whereHas('children', function ($querysub) use ($request) {
                        $querysub->where('title','like','%'.$request->get('gift_name').'%')->where('shop_id', $request->shop_id);
                    });
                })->orWhereHas('item_acc', function ($query) use ($request) {
                    $query->where('title','like','%'.$request->get('gift_name').'%');
                });
            }

            if ($request->filled('started_at')) {
                $data->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $data->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }
            $dataFirst = $data->orderBy('created_at','desc')->get();
            $data = collect($dataFirst)->slice($paginate * ($page - 1), $paginate);
            $data = new LengthAwarePaginator($data, count($dataFirst), $paginate, $page, [
                'path'  => $request->url(),
            ]);
            return response()->json($data, 200);
        }catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'msg' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }

    public function postMiniGame(Request $request){
        $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
        if(!$shop){
            return response()->json([
                'msg' => __('Domain chưa được đăng ký!'),
                'status' => 0
            ], 200);
        }

        if(!Auth::guard('api')->check()){
            return response()->json([
                'msg' => __('Vui lòng đăng nhập!'),
                'status' => 4
            ], 200);
        }


        $listgift = array();
        $wheel_continute  = 0;
        $free_wheel = 0;
        $value_gif_bonus = array();
        $msg_random_bonus = array();
        $msg = "";
        $numrollbyorder =  $request->numrollbyorder;
        //Start transaction!
        $id = $request->id;
        $typeRoll = $request->typeRoll;
        //lấy vòng quay
        $numrolllop=$request->numrolllop;

        $group = Group::select('id','title','price','params','module')
            ->with(['items'=>function($query) use ($request){
                $query->whereHas('parrent', function ($querysub)  use ($request){
                    $querysub->where('status', 1);
                });
                $query->with('children',function($querysub) use ($request){
                    $querysub->where('shop_id', $request->shop_id);
                });
                $query->whereHas('children',function($querysub) use ($request){
                    $querysub->where('shop_id', $request->shop_id);
                });
                $query->select('item_id','items.order','title','image','params','parent_id','items.id')->orderBy('order');
            }])
            ->whereHas('items',function($query) use ($request){
                $query->whereHas('parrent', function ($querysub)  use ($request){
                    $querysub->where('status', 1);
                });
                $query->with('children',function($querysub) use ($request){
                    $querysub->where('shop_id', $request->shop_id);
                });
                $query->whereHas('children',function($querysub) use ($request){
                    $querysub->where('shop_id', $request->shop_id);
                });
                $query->select('item_id','items.order','title','image','params','parent_id','items.id')->orderBy('order');
            })
            ->with('customs', function ($query) use ($request) {
                $query->where('shop_id', $request->shop_id);
            })
            ->whereHas('customs', function ($query) use ($request) {
                $query->where('shop_id', $request->shop_id)->where('status', 1);
            })
            ->where('id', $id)
            ->where('status', 1)->firstOrFail();


        $moduleLog = explode('-', $group->module)[0].'-log';
        $module = explode('-', $group->module)[0];

        $group_name = $group->title;
        if($numrolllop == 1)
        {
            $fee =  $group->price==''?20000:$group->price;
        }
        if($numrolllop == 3)
        {
            $fee = $group->params->price_sticky_3==''?60000:$group->params->price_sticky_3;
        }
        if($numrolllop == 5)
        {
            $fee = $group->params->price_sticky_5==''?100000:$group->params->price_sticky_5;
        }
        if($numrolllop == 7)
        {
            $fee = $group->params->price_sticky_7==''?140000:$group->params->price_sticky_7;
        }
        if($numrolllop == 10)
        {
            $fee = $group->params->price_sticky_10==''?200000:$group->params->price_sticky_10;
        }
        $arr_gift = array();
        $gift_detail = array();
        $xgt = array();
        $xValue = array();
        $point = 1;
        $userpoint = 50;
        $vp_txns = 0;
        $balance_item_txns = 0;
        if($typeRoll == "real")
        {
            DB::beginTransaction();
            try {
                //check tiền user
                $user = Auth::guard('api')->user();
                $userTransaction = User::where('id', $user->id)->lockForUpdate()->firstOrFail();
                if ($userTransaction->checkBalanceValid() == false) {
                    DB::rollback();
                    return response()->json([
                        'status' => 0,
                        'msg' => 'Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý'
                    ]);

                }


                //Check Free
                if($group->params->type_charge == 1)//Thanh toán = tiền khóa
                {
                    if($fee <= 0 || ($fee> 0 && $userTransaction->balance_lock/$fee < 1))
                    {
                        if($userTransaction->free_wheel_type == $id && $userTransaction->free_wheel > 0)
                        {
                            $wheel_continute  = 1;
                        }
                        else
                        {
                            $wheel_continute  = 0;
                        }
                    }
                    else
                    {
                        $wheel_continute  = 1;
                    }
                }
                else
                {
                    if($fee <= 0 || ($fee> 0 && $userTransaction->balance/$fee < 1))
                    {
                        if($userTransaction->free_wheel_type == $id && $userTransaction->free_wheel > 0)
                        {
                            $wheel_continute  = 1;
                        }
                        else
                        {
                            $wheel_continute  = 0;
                        }
                    }
                    else
                    {
                        $wheel_continute  = 1;
                    }
                }

                if ($wheel_continute  == 0) {
                    if($group->params->type_charge == 1)
                    {
                        DB::rollback();
                        return response()->json([
                            'msg' => __('Minigame sử dụng tiền khóa để chơi. Số dư tiền khóa không đủ. Nạp thêm để chơi tiếp!'),
                            'status' => 3
                        ], 200);
                    }
                    else
                    {
                        DB::rollback();
                        return response()->json([
                            'msg' => __('Bạn đã hết lượt chơi. Nạp thêm để chơi tiếp!'),
                            'status' => 3
                        ], 200);
                    }
                }
                else{
                    //Check Free
                    if($userTransaction->free_wheel_type == $id && $userTransaction->free_wheel > 0)
                    {
                        $fee = 0;
                    }
                    $gift = '';
                    //Lấy danh sách quà
                    $gifts = $group->items;

                    //Lấy list quà để random theo xác suất đã config
                    if(count($gifts) <= 0){
                        DB::rollback();
                        return response()->json([
                            'msg' => __('Đang cập nhật quà!...'),
                            'status' => 0
                        ], 200);
                    }
                    $pos = array();
                    $random_gift = array();
                    $gift = array();
                    $random_x = array();
                    $count_total_percent = 0;

                    //                    $arrOder = explode(',',$group->params->user_wheel_order);
                    //                    $arrUSer =  explode(',',$group->params->user_wheel);

                    $arrOder = explode(',',isset($group->params->user_wheel_order_idol)?$group->params->user_wheel_order_idol:"");
                    $arrUSer =  explode(',',isset($group->params->user_wheel_idol)?$group->params->user_wheel_idol:"");

                    foreach ($gifts as $key) {
                        $random_gift[$key->order]=$key->params->percent;
                        $count_total_percent += $key->params->percent;
                    }
                    if($count_total_percent == 0){
                        DB::rollback();
                        return response()->json([
                            'msg' => __('Đang cập nhật quà!.'),
                            'status' => 0
                        ], 200);
                    }
                    $nogift = array();
                    $gift_types = array();
                    if($numrolllop > 0)
                    {
                        for($i=0;$i<$numrolllop;$i++)
                        {
                            if(in_array($user->username,$arrUSer) || in_array($user->id,$arrUSer))
                            {
                                if($numrollbyorder < count($arrOder))
                                {
                                    $numrollbyorder = $numrollbyorder + 1;
                                }
                                if($numrollbyorder > count($arrOder)-1)
                                {
                                    $numrollbyorder = 0;
                                }
                                foreach ($gifts as $key) {
                                    //Reset phan thuong
                                    $random_gift[$key->order]=0;
                                    $count_total_percent = 0;
                                }
                                //Set phan thuong lon nhat
                                $index_ = $arrOder[$numrollbyorder];
                                $random_gift[$index_]=100;
                                $count_total_percent = 100;

                            }

                            $pos[$i] = $this->getRandomWeightedElement($random_gift);
                            $gift[$i] = '';
                            $xgt[$i] = '';
                            $xValue = '';
                        }
                    }

                    //Lấy vị trí trúng quà theo trọng số xác suất
                    $i=0;
                    foreach ($gifts as $key) {
                        foreach ($pos as $keychild=>$value) {
                            if($key->order == $value){
                                $gift[$keychild] = $key;
                            }
                        }
                    }

                    $countGift = count($gift);
                    foreach ($gifts as $key) {
                        if($key->item_id != $gift[$countGift-1]->item_id){
                            array_push($listgift, $key);
                        }
                    }

                    for($j=0;$j<$numrolllop;$j++)
                    {
                        $gift_types[$j] = $gift[$j]->parrent->params->gift_type;
                    }

                    $list_id_order = "";
                    $list_id_gift = "";
                    if($group->params->game_type == 14){//Vàng ngọc rồng nrocoin
                        $balance_item_txns = $userTransaction->coin_num;
                    }else if($group->params->game_type == 12){//Ngọc ngọc rồng nrogem
                        $balance_item_txns = $userTransaction->gem_num;
                    }else if($group->params->game_type == 11){//Xu ninja school ninjaxu
                        $balance_item_txns = $userTransaction->xum_num;
                    }else if($group->params->game_type == 13){//roblox roblox_buyserver
                        $balance_item_txns = $userTransaction->robux_num;
                    }
                    else{
                        $balance_item_txns = $userTransaction['ruby_num'.$group->params->game_type];
                    }
                    foreach ($gift_types as $key=>$gift_type) {
                        if($gift[$key]->params->limit!="" && $gift[$key]->params->limit==0){
                            DB::rollback();
                            return response()->json([
                                'msg' => __('Đang cập nhật quà!'),
                                'status' => 0
                            ], 200);
                        }
                        if($group->params->random_point_from > 0 && $group->params->random_point_to > 0 && $group->params->random_point_from < $group->params->random_point_to)
                        {
                            $point = rand($group->params->random_point_from, $group->params->random_point_to);
                        }

                        $random_x =  array();
                        $random_x[0] = 100;//$gift[$key]->position1;//X1 giá trị
                        $random_x[1] = 0;//$gift[$key]->position2;//X2 giá trị
                        $random_x[2] = 0;//$gift[$key]->position3;//X3 giá trị
                        $xgt[$key] = $this->getRandomX($random_x);
                        if($xgt[$key] == 0)
                        {
                            $xValue[$key] = 1;
                        }
                        elseif($xgt[$key] == 1)
                        {
                            $xValue[$key] = 4;
                        }
                        elseif($xgt[$key] == 2)
                        {
                            $xValue[$key] = 5;
                        }
                        else
                        {
                            $xValue[$key]=1;
                        }

                        //check random bonus kim cương
                        $value_gif_bonus[$key] = 0;
                        if($gift[$key]->params->bonus_from > 0 && $gift[$key]->params->bonus_to <= 0)
                        {
                            $value_gif_bonus[$key] = $gift[$key]->params->bonus_from;
                        }
                        elseif($gift[$key]->params->bonus_from <= 0 && $gift[$key]->params->bonus_to > 0)
                        {
                            $value_gif_bonus[$key] = $gift[$key]->params->bonus_to;
                        }
                        elseif($gift[$key]->params->bonus_from > 0 && $gift[$key]->params->bonus_to > 0 && $gift[$key]->params->bonus_from > $gift[$key]->params->bonus_to)
                        {
                            $value_gif_bonus[$key] = mt_rand($gift[$key]->params->bonus_to, $gift[$key]->params->bonus_from);
                        }
                        elseif($gift[$key]->params->bonus_from > 0 && $gift[$key]->params->bonus_to > 0 && $gift[$key]->params->bonus_from < $gift[$key]->params->bonus_to)
                        {
                            $value_gif_bonus[$key] = mt_rand($gift[$key]->params->bonus_from, $gift[$key]->params->bonus_to);
                        }
                        elseif($gift[$key]->params->bonus_from > 0 && $gift[$key]->params->bonus_to > 0 && $gift[$key]->params->bonus_from == $gift[$key]->params->bonus_to)
                        {
                            $value_gif_bonus[$key] = $gift[$key]->params->bonus_from;
                        }
                        else
                        {
                            $value_gif_bonus[$key] = 0;
                        }

                        $userbalance = $group->params->type_charge == 1?$userTransaction->balance_lock:$userTransaction->balance;
                        if($gift_type==0){
                            if($group->params->game_type == 14){//Vàng ngọc rồng nrocoin
                                if($userTransaction->coin_num > 1500000000){
                                    $status = 0;
                                    $msg = __("Rút bớt vàng trước khi chơi tiếp!");
                                    return response()->json(array('msg'=> $msg, 'status' => $status), 200);
                                }
                            }else if($group->params->game_type == 12){//Ngọc ngọc rồng nrogem
                                if($userTransaction->gem_num > 1500000000){
                                    $status = 0;
                                    $msg = __("Rút bớt ngọc trước khi chơi tiếp!");
                                    return response()->json(array('msg'=> $msg, 'status' => $status), 200);
                                }
                            }else if($group->params->game_type == 11){//Xu ninja school ninjaxu
                                if($userTransaction->xu_num > 1500000000){
                                    $status = 0;
                                    $msg = __("Rút bớt xu trước khi chơi tiếp!");
                                    return response()->json(array('msg'=> $msg, 'status' => $status), 200);
                                }
                            }else if($group->params->game_type == 13){// roblox_buyserver
                                if($userTransaction->robux_num > 1500000000){
                                    $status = 0;
                                    $msg = __("Rút bớt robux trước khi chơi tiếp!");
                                    return response()->json(array('msg'=> $msg, 'status' => $status), 200);
                                }
                            }else{
                                if($userTransaction['ruby_num'.$group->params->game_type] > 1500000000){
                                    $status = 0;
                                    $msg = __("Rút bớt vật phẩm trước khi chơi tiếp!");
                                    return response()->json(array('msg'=> $msg, 'status' => $status), 200);
                                }
                            }

                            $order = Order::create([
                                'module' => $moduleLog,
                                'shop_id' => $request->shop_id,
                                'description' => "Chơi ".$module." trúng phần thưởng #".$gift[$key]->parrent->id,
                                'gate_id' => $group->id,
                                'author_id' => $user->id,
                                'price' => $fee/$numrolllop,
                                'value_gif_bonus' => $value_gif_bonus[$key],
                                'real_received_price' => $gift[$key]->parrent->params->value * $xValue[$key],
                                'ref_id' => $gift[$key]->item_id
                            ]);

                            $list_id_order = $list_id_order.($list_id_order==""?"":",").$order->id;
                            $list_id_gift = $list_id_gift.($list_id_gift==""?"":",").$gift[$key]->item_id;

                            //Cộng vật phẩm
                            if($group->params->game_type == 14){//Vàng ngọc rồng nrocoin
                                $userTransaction->coin_num = $userTransaction->coin_num + ($gift[$key]->parrent->params->value* $xValue[$key]) + $value_gif_bonus[$key];
                                if($value_gif_bonus[$key] > 0)
                                {
                                    $msg_random_bonus[$key] = " .Bạn nhận được thêm ".$value_gif_bonus[$key]." vàng bonus";
                                }
                                else
                                {
                                    $msg_random_bonus[$key] = "";
                                }
                            }else if($group->params->game_type == 12){//Ngọc ngọc rồng nrogem
                                $userTransaction->gem_num = $userTransaction->gem_num + ($gift[$key]->parrent->params->value* $xValue[$key]) + $value_gif_bonus[$key];
                                if($value_gif_bonus[$key] > 0)
                                {
                                    $msg_random_bonus[$key] = " .Bạn nhận được thêm ".$value_gif_bonus[$key]." ngọc bonus";
                                }
                                else
                                {
                                    $msg_random_bonus[$key] = "";
                                }
                            }else if($group->params->game_type == 11){//Xu ninja school ninjaxu
                                $userTransaction->xu_num = $userTransaction->xu_num + ($gift[$key]->parrent->params->value* $xValue[$key]) + $value_gif_bonus[$key];
                                if($value_gif_bonus[$key] > 0)
                                {
                                    $msg_random_bonus[$key] = " .Bạn nhận được thêm ".$value_gif_bonus[$key]." xu bonus";
                                }
                                else
                                {
                                    $msg_random_bonus[$key] = "";
                                }
                            }else if($group->params->game_type == 13){//Xu ninja school ninjaxu
                                $userTransaction->robux_num = $userTransaction->robux_num + ($gift[$key]->parrent->params->value* $xValue[$key]) + $value_gif_bonus[$key];
                                if($value_gif_bonus[$key] > 0)
                                {
                                    $msg_random_bonus[$key] = " .Bạn nhận được thêm ".$value_gif_bonus[$key]." robux bonus";
                                }
                                else
                                {
                                    $msg_random_bonus[$key] = "";
                                }
                            }else{

                                if ($group->params->game_type == 1) {
                                    $userTransaction['ruby_num'.$group->params->game_type] = $userTransaction['ruby_num'.$group->params->game_type] + ($gift[$key]->parrent->params->value* $xValue[$key]) + $value_gif_bonus[$key];
                                    if($value_gif_bonus[$key] > 0)
                                    {
                                        $msg_random_bonus[$key] = " .Bạn nhận được thêm ".$value_gif_bonus[$key]." quân huy bonus";

                                    }
                                    else
                                    {
                                        $msg_random_bonus[$key] = "";
                                    }
                                }else{
                                    $userTransaction['ruby_num'.$group->params->game_type] = $userTransaction['ruby_num'.$group->params->game_type] + ($gift[$key]->parrent->params->value* $xValue[$key]) + $value_gif_bonus[$key];
                                    if($value_gif_bonus[$key] > 0)
                                    {
                                        $msg_random_bonus[$key] = " .Bạn nhận được thêm ".$value_gif_bonus[$key]." kim cương bonus";

                                    }
                                    else
                                    {
                                        $msg_random_bonus[$key] = "";
                                    }
                                }
                            }
                            $vp_txns += ($gift[$key]->parrent->params->value* $xValue[$key]) + $value_gif_bonus[$key];

                            if($gift[$key]->params->limit!="" && $gift[$key]->params->limit>0){
                                $itemUpdate = Item::where('id',$gift[$key]->item_id)->first();
                                if($itemUpdate){
                                    $params=$itemUpdate->params;
                                    foreach ($params as $aPram=>$keyitem){
                                        if(str_contains($aPram, 'limit')){
                                            if($params->$aPram>0){
                                                $params->$aPram = $params->$aPram - 1;
                                            }
                                        }
                                    }
                                    $itemUpdate->params = $params;
                                    $itemUpdate->save();
                                }
                            }

                            $datagift = array(
                                'id'                => $gift[$key]->item_id,
                                'image'             => $gift[$key]->children[0]->image,
                                'name'              => $gift[$key]->children[0]->title,
                                'order'             => $gift[$key]->order,
                                'winbox'            => $gift[$key]->parrent->params->winbox,
                                'num_roll_remain'   => $fee==0?0:FLOOR($userbalance/$fee),
                                'gift_type'         => $gift_type,
                                'game_type'           => $group->params->game_type
                            );
                            $status = 'OK';
                            $gift_detail = $datagift;
                        }else{
                            $items = Item::where('status','1')->where('module','minigame-acc')
                                ->where('idkey', $group->params->game_type)->limit(20)->get();
                            if(count($items) <= 0){
                                return response()->json([
                                    'msg' => __('Đang cập nhật quà!'),
                                    'status' => 0
                                ], 200);
                            }
                            $index = rand(0, count($items)-1);

                            $order = Order::create([
                                'module' => $moduleLog,
                                'shop_id' => $request->shop_id,
                                'description' => "Chơi ".$module." trúng phần thưởng #".$gift[$key]->parrent->item_id,
                                'gate_id' => $group->id,
                                'acc_id' => $items[$index]["id"],
                                'author_id' => $user->id,
                                'price' => $fee/$numrolllop,
                                'ref_id' => $gift[$key]->item_id
                            ]);

                            $list_id_order = $list_id_order.($list_id_order==""?"":",").$order->id;
                            $list_id_gift = $list_id_gift.($list_id_gift==""?"":",").$gift[$key]->item_id;

                            if($gift[$key]->params->limit!="" && $gift[$key]->params->limit>0){
                                $itemUpdate = Item::where('id',$gift[$key]->item_id)->first();
                                if($itemUpdate){
                                    $params=$itemUpdate->params;
                                    foreach ($params as $aPram=>$keyitem){
                                        if(str_contains($aPram, 'limit')){
                                            if($params->$aPram>0){
                                                $params->$aPram = $params->$aPram - 1;
                                            }
                                        }
                                    }
                                    $itemUpdate->params = $params;
                                    $itemUpdate->save();
                                }
                            }

                            $datagift = array(
                                'id'                => $gift[$key]->item_id,
                                'image'             => $gift[$key]->children[0]->image,
                                'name'              => $gift[$key]->children[0]->title,
                                'order'             => $gift[$key]->order,
                                'winbox'            => $gift[$key]->parrent->params->winbox,
                                'num_roll_remain'   => $fee==0?0:FLOOR($userbalance/$fee),
                                'gift_type'         => $gift_type, //'input_auto'
                                'game_type'         => '' //is_ruby
                            );

                            Item::where('id', $items[$index]["id"])->update(['status' => 0]);

                            $status = 'OK';
                            $gift_detail = $datagift;
                        }
                        $arr_gift[$key]  = $gift[$key];
                    }
                    //tạo tnxs vp
                    $txns = TxnsVp::create([
                        'trade_type' => $module,
                        'is_add' => '1',
                        'user_id' => $user->id,
                        'amount' => $vp_txns,
                        'last_balance' => $balance_item_txns + $vp_txns,
                        'description' => "Chơi ".$module." ".($numrolllop>1?("gói ".$numrolllop." lần liên tiếp"):"")." trúng phần thưởng #".$list_id_gift,
                        'txnsable_type' => $list_id_order,
                        'ip' => $request->getClientIp(),
                        'status' => 1,
                        'shop_id' => $request->shop_id,
                        'item_type' => $group->params->game_type
                    ]);

                    //tạo tnxs tiền
                    $txns = Txns::create([
                        'trade_type' => $module,
                        'is_add' => '0',
                        'user_id' => $user->id,
                        'amount' => $fee,
                        'last_balance' => $userbalance - $fee,
                        'description' => "Chơi ".$module." ".($numrolllop>1?("gói ".$numrolllop." lần liên tiếp"):"")." trúng phần thưởng #".$list_id_gift,
                        'txnsable_type' => $list_id_order,
                        'ip' => $request->getClientIp(),
                        'status' => 1,
                        'shop_id' => $request->shop_id,
                        'type' => ($group->params->type_charge == 1?1:NULL) //1: tiền khóa
                    ]);

                    //Minigame custom.

                    if (!isset($group->customs)){
                        DB::rollback();
                        return response()->json([
                            'status' => 0,
                            'msg' => 'MInigame không tồn tại'
                        ]);
                    }

                    $minigameDistribute_id = $group->customs[0]->id;

                    $minigameDistribute = MinigameDistribute::with('shop')->where('id',$minigameDistribute_id)->first();

                    if (!isset($minigameDistribute)){
                        DB::rollback();
                        return response()->json([
                            'status' => 0,
                            'msg' => 'MInigame không tồn tại'
                        ]);
                    }

                    $minigameDistribute->total_order = $minigameDistribute->total_order+$numrolllop;

                    $minigameDistribute->save();

                    $checkPoint = isset($group->params->point)?$group->params->point:0;
                    //Trừ tiền
                    if($userTransaction->point < 100 && $checkPoint == 1)
                    {
                        $userTransaction->point = $userTransaction->point + $point;
                        $userTransaction->save();
                    }
                    if($userTransaction->free_wheel_type == $id && $userTransaction->free_wheel > 0)
                    {
                        $userTransaction->free_wheel = $userTransaction->free_wheel - 1;
                        $free_wheel = $userTransaction->free_wheel;
                    }
                    else
                    {
                        //update txns --source_money == 0 - trừ tiền khóa, `1 - trừ tiền chính
                        if($group->params->type_charge == 1)// Thanh toán bằng tiền khóa
                        {
                            $txns->source_money = 1;
                            $userTransaction->balance_lock =  $userTransaction->balance_lock - $fee;
                        }
                        else
                        {
                            $txns->source_money = 0;
                            $userTransaction->balance =  $userTransaction->balance - $fee;
                            $userTransaction->balance_out = $userTransaction->balance_out + $fee;
                        }
                    }
                    $userTransaction->save();
                    $userpoint = $userTransaction->point;
                }


            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);
                return response()->json([
                    'msg' => __('Có lỗi phát sinh.Xin vui lòng thử lại!'),
                    'status' => 0
                ], 200);
            }
            DB::commit();
        }
        else
        {
            try {
                $user = Auth::guard('api')->user();
                $userTransaction = User::where('id', $user->id)->lockForUpdate()->firstOrFail();
                $gift = '';
                //Lấy danh sách quà
                $gifts = $group->items;

                //Lấy list quà để random theo xác suất đã config
                if(count($gifts) <= 0){
                    DB::rollback();
                    return response()->json([
                        'msg' => __('Đang cập nhật quà!'),
                        'status' => 0
                    ], 200);
                }
                $pos = array();
                $random_gift = array();
                $gift = array();
                $random_x = array();
                $count_total_percent = 0;
                $arrOder = explode(',',isset($group->params->user_wheel_order_idol)?$group->params->user_wheel_order_idol:"");
                $arrUSer =  explode(',',isset($group->params->user_wheel_idol)?$group->params->user_wheel_idol:"");
                foreach ($gifts as $key) {
                    $random_gift[$key->order]=$key->params->try_percent!=""?$key->params->try_percent:$key->params->percent;
                    $count_total_percent += $key->params->try_percent!=""?$key->params->try_percent:$key->params->percent;
                }

                if($count_total_percent == 0){
                    DB::rollback();
                    return response()->json([
                        'msg' => __('Đang cập nhật quà!'),
                        'status' => 0
                    ], 200);
                }
                $gift_types = array();
                if($numrolllop > 0)
                {
                    for($i=0;$i<$numrolllop;$i++)
                    {
                        if(in_array($user->username,$arrUSer) || in_array($user->id,$arrUSer))
                        {
                            if($numrollbyorder < count($arrOder))
                            {
                                $numrollbyorder = $numrollbyorder + 1;
                            }
                            if($numrollbyorder > count($arrOder)-1)
                            {
                                $numrollbyorder = 0;
                            }
                            foreach ($gifts as $key) {
                                //Reset phan thuong
                                $random_gift[$key->order]=0;
                                $count_total_percent = 0;
                            }
                            //Set phan thuong lon nhat
                            $index_ = $arrOder[$numrollbyorder];
                            $random_gift[$index_]=100;
                            $count_total_percent = 100;

                        }

                        $pos[$i] = $this->getRandomWeightedElement($random_gift);
                        $gift[$i] = '';
                        $xgt[$i] = '';
                        $xValue = '';
                    }
                }

                //Lấy vị trí trúng quà theo trọng số xác suất
                $i=0;

                foreach ($gifts as $key) {
                    foreach ($pos as $keychild=>$value) {
                        if($key->order == $value){
                            $gift[$keychild] = $key;
                        }
                    }
                }

                $countGift = count($gift);
                foreach ($gifts as $key) {
                    if($key->item_id != $gift[$countGift-1]->item_id){
                        array_push($listgift, $key);
                    }
                }

                for($j=0;$j<$numrolllop;$j++)
                {
                    $gift_types[$j] = $gift[$j]->parrent->params->gift_type;
                }

                foreach ($gift_types as $key=>$gift_type) {
                    if($group->params->random_point_from > 0 && $group->params->random_point_to > 0 && $group->params->random_point_from < $group->params->random_point_to)
                    {
                        $point = rand($group->params->random_point_from, $group->params->random_point_to);
                    }

                    $random_x =  array();
                    $random_x[0] = 100;//$gift[$key]->position1;//X1 giá trị
                    $random_x[1] = 0;//$gift[$key]->position2;//X2 giá trị
                    $random_x[2] = 0;//$gift[$key]->position3;//X3 giá trị
                    $xgt[$key] = $this->getRandomX($random_x);
                    if($xgt[$key] == 0)
                    {
                        $xValue[$key] = 1;
                    }
                    elseif($xgt[$key] == 1)
                    {
                        $xValue[$key] = 4;
                    }
                    elseif($xgt[$key] == 2)
                    {
                        $xValue[$key] = 5;
                    }
                    else
                    {
                        $xValue[$key]=1;
                    }

                    //check random bonus kim cương
                    $value_gif_bonus[$key] = 0;
                    if($gift[$key]->params->bonus_from > 0 && $gift[$key]->params->bonus_to <= 0)
                    {
                        $value_gif_bonus[$key] = $gift[$key]->params->bonus_from;
                    }
                    elseif($gift[$key]->params->bonus_from <= 0 && $gift[$key]->params->bonus_to > 0)
                    {
                        $value_gif_bonus[$key] = $gift[$key]->params->bonus_to;
                    }
                    elseif($gift[$key]->params->bonus_from > 0 && $gift[$key]->params->bonus_to > 0 && $gift[$key]->params->bonus_from > $gift[$key]->params->bonus_to)
                    {
                        $value_gif_bonus[$key] = mt_rand($gift[$key]->params->bonus_to, $gift[$key]->params->bonus_from);
                    }
                    elseif($gift[$key]->params->bonus_from > 0 && $gift[$key]->params->bonus_to > 0 && $gift[$key]->params->bonus_from < $gift[$key]->params->bonus_to)
                    {
                        $value_gif_bonus[$key] = mt_rand($gift[$key]->params->bonus_from, $gift[$key]->params->bonus_to);
                    }
                    elseif($gift[$key]->params->bonus_from > 0 && $gift[$key]->params->bonus_to > 0 && $gift[$key]->params->bonus_from == $gift[$key]->params->bonus_to)
                    {
                        $value_gif_bonus[$key] = $gift[$key]->params->bonus_from;
                    }
                    else
                    {
                        $value_gif_bonus[$key] = 0;
                    }
                    if($gift_type==0){
                        //Cộng vật phẩm
                        if($gift_type == 14){//Vàng ngọc rồng nrocoin
                            if($value_gif_bonus[$key] > 0)
                            {
                                $msg_random_bonus[$key] = " .Bạn nhận được thêm ".$value_gif_bonus[$key]." vàng bonus";
                            }
                            else
                            {
                                $msg_random_bonus[$key] = "";
                            }
                        }else if($gift_type == 12){//Ngọc ngọc rồng nrogem
                            if($value_gif_bonus[$key] > 0)
                            {
                                $msg_random_bonus[$key] = " .Bạn nhận được thêm ".$value_gif_bonus[$key]." ngọc bonus";
                            }
                            else
                            {
                                $msg_random_bonus[$key] = "";
                            }
                        }else if($gift_type == 11){//Xu ninja ninjaxu
                            if($value_gif_bonus[$key] > 0)
                            {
                                $msg_random_bonus[$key] = " .Bạn nhận được thêm ".$value_gif_bonus[$key]." xu bonus";
                            }
                            else
                            {
                                $msg_random_bonus[$key] = "";
                            }
                        }else if($gift_type == 13){//Roblox
                            if($value_gif_bonus[$key] > 0)
                            {
                                $msg_random_bonus[$key] = " .Bạn nhận được thêm ".$value_gif_bonus[$key]." robux bonus";
                            }
                            else
                            {
                                $msg_random_bonus[$key] = "";
                            }
                        }
                        else{
                            if($value_gif_bonus[$key] > 0)
                            {
                                $msg_random_bonus[$key] = " .Bạn nhận được thêm ".$value_gif_bonus[$key]." kim cương bonus";

                            }
                            else
                            {
                                $msg_random_bonus[$key] = "";
                            }
                        }
                        $datagift = array(
                            'id'                => $gift[$key]->item_id,
                            'image'             => $gift[$key]->children[0]->image,
                            'name'              => $gift[$key]->children[0]->title,
                            'order'             => $gift[$key]->order,
                            'winbox'            => $gift[$key]->parrent->params->winbox,
                            'num_roll_remain'   => $fee==0?0:FLOOR($userTransaction->balance/$fee),
                            'gift_type'         => $gift_type, //'input_auto'
                            'game_type'           => $group->params->game_type //is_ruby
                        );
                        $gift_detail = $datagift;

                    }else{
                        $items = Item::where('status','1')->where('module','minigame-acc')->where('idkey',$group->params->game_type)->limit(20)->get();
                        if(count($items) <= 0){
                            return response()->json([
                                'msg' => __('Đang cập nhật quà!'),
                                'status' => 0
                            ], 200);
                        }
                        $index = rand(0, count($items)-1);

                        $datagift = array(
                            'id'                => $gift[$key]->item_id,
                            'image'             => $gift[$key]->children[0]->image,
                            'name'              => $gift[$key]->children[0]->title,
                            'order'             => $gift[$key]->order,
                            'winbox'            => $gift[$key]->parrent->params->winbox,
                            'num_roll_remain'   => $fee==0?0:FLOOR($userTransaction->balance/$fee),
                            'gift_type'         => $gift_type, //'input_auto'
                            'game_type'         => '' //is_ruby
                        );

                        $status = 'OK';
                        $gift_detail = $datagift;
                    }
                    $arr_gift[$key]  = $gift[$key];
                }
            } catch (\Exception $e) {
                Log::error($e);
                return response()->json([
                    'msg' => __('Có lỗi phát sinh.Xin vui lòng thử lại!'),
                    'status' => 0
                ], 200);
            }
        }

        //lay goi rut nho nhat cua vp
        $showwithdrawbtn = true;
        if($gift_detail['game_type'] != ''){
            $gametype = Item::where('module', config('module.minigame.module.gametype'))
                ->where('status', 1)->where('parent_id',$gift_detail['game_type'])->first();
            $package_min = null;
            if($gametype){
//                $package_min = Item::select('price')
//                    ->where('module', config('module.minigame.module.package'))
//                    ->where('status', 1)
//                    ->where('parent_id',$gametype->id)->min('price');

                $package_min = ItemConfig::with(array('items' => function ($query) use($gametype){
                    $query->where('module','package');
                    $query->whereHas('parrent', function ($query) use ($gametype){
                        $query->where('id',$gametype->id);
                    });
                    $query->with(array('parrent' => function ($query) use($gametype){
                        $query->where('id',$gametype->id);
                        $query->where('module','package');
                    }));
                }))->where('status',1)->where('module','package')
                    ->where('shop_id',$shop->id)->min('price');
            }
            if(($balance_item_txns + $vp_txns) < $package_min) $showwithdrawbtn = false;
        }

        return response()->json([
            'free_wheel'=> $free_wheel,
            'arr_gift' => $arr_gift,
            'gift_detail' => $gift_detail,
            'xgt' => $xgt,
            'xValue' => $xValue,
            'numrollbyorder' => $numrollbyorder,
            'value_gif_bonus' => $value_gif_bonus,
            'msg_random_bonus' => $msg_random_bonus,
            'userpoint' => $userpoint,
            'listgift' => $listgift,
            'status' => 1,
            'msg'=> $msg,
            'game_type' => $group->params->game_type,
            'showwithdrawbtn' => $showwithdrawbtn
        ], 200);
    }

    public function postMiniGameBonus(Request $request){
        if(!Auth::guard('api')->check()){
            return response()->json([
                'msg' => __('Vui lòng đăng nhập!'),
                'status' => 4
            ], 200);
        }
        $listgift = array();
        $wheel_continute  = 0;
        $free_wheel = 0;
        $value_gif_bonus = array();
        $msg_random_bonus = array();
        $msg = "";
        $numrollbyorder =  $request->numrollbyorder;
        $id = $request->id;
        $numrolllop=1;

        $group = Group::select('id','title','price','params','module')
            ->with(['items'=>function($query) use ($request){
                $query->whereHas('parrent', function ($querysub)  use ($request){
                    $querysub->where('status',1);
                });
                $query->with('children',function($querysub) use ($request){
                    $querysub->where('shop_id', $request->shop_id);
                });
                $query->whereHas('children',function($querysub) use ($request){
                    $querysub->where('shop_id', $request->shop_id);
                });
                $query->select('item_id','items.order','title','image','params','parent_id','items.id')->orderBy('order');
            }])
            ->whereHas('items',function($query) use ($request){
                $query->whereHas('parrent', function ($querysub)  use ($request){
                    $querysub->where('status', 1);
                });
                $query->with('children',function($querysub) use ($request){
                    $querysub->where('shop_id', $request->shop_id);
                });
                $query->whereHas('children',function($querysub) use ($request){
                    $querysub->where('shop_id', $request->shop_id);
                });
                $query->select('item_id','items.order','title','image','params','parent_id','items.id')->orderBy('order');
            })
            ->where('id', $id)
            ->where('status', 1)->firstOrFail();
        $moduleLog = explode('-', $group->module)[0].'-log';
        $module = explode('-', $group->module)[0];

        $group_name = $group->title;
        $fee =  $group->price==''?20000:$group->price;
        $arr_gift = array();
        $gift_detail = array();
        $xgt = array();
        $xValue = array();
        $point = 1;
        $userpoint = 50;
        DB::beginTransaction();
        try {
            //check tiền user
            $user = Auth::guard('api')->user();
            $userTransaction = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

            if($userTransaction->point <= 99)
            {
                DB::rollback();
                return response()->json([
                    'msg' => __('Số điểm của bạn chưa đủ để nhận quà. Hãy tiếp tục chơi để tích điểm may mắn!'),
                    'status' => 0
                ], 200);
            }
            $gift = '';
            //Lấy danh sách quà
            $gifts = $group->items;

            //Lấy list quà để random theo xác suất đã config
            if(count($gifts) <= 0){
                DB::rollback();
                return response()->json([
                    'msg' => __('Đang cập nhật quà!'),
                    'status' => 0
                ], 200);
            }
            $pos = array();
            $random_gift = array();
            $gift = array();
            $random_x = array();
            $count_total_percent = 0;
            $arrOder = explode(',',isset($group->params->user_wheel_order_idol)?$group->params->user_wheel_order_idol:"");
            $arrUSer =  explode(',',isset($group->params->user_wheel_idol)?$group->params->user_wheel_idol:"");
            foreach ($gifts as $key) {
                $random_gift[$key->order]=$key->params->percent;
                $count_total_percent += $key->params->percent;
            }
            if($count_total_percent == 0){
                DB::rollback();
                return response()->json([
                    'msg' => __('Đang cập nhật quà!'),
                    'status' => 0
                ], 200);
            }
            $gift_types = array();
            if($numrolllop > 0)
            {
                for($i=0;$i<$numrolllop;$i++)
                {
                    if(in_array($user->username,$arrUSer) || in_array($user->id,$arrUSer))
                    {
                        if($numrollbyorder < count($arrOder))
                        {
                            $numrollbyorder = $numrollbyorder + 1;
                        }
                        if($numrollbyorder > count($arrOder)-1)
                        {
                            $numrollbyorder = 0;
                        }
                        foreach ($gifts as $key) {
                            //Reset phan thuong
                            $random_gift[$key->order]=0;
                            $count_total_percent = 0;
                        }
                        //Set phan thuong lon nhat
                        $index_ = $arrOder[$numrollbyorder];
                        $random_gift[$index_]=100;
                        $count_total_percent = 100;

                    }

                    $pos[$i] = $this->getRandomWeightedElement($random_gift);
                    $gift[$i] = '';
                    $xgt[$i] = '';
                    $xValue = '';
                }
            }

            //Lấy vị trí trúng quà theo trọng số xác suất
            $i=0;
            foreach ($gifts as $key) {
                foreach ($pos as $keychild=>$value) {
                    if($key->order == $value){
                        $gift[$keychild] = $key;
                    }
                }
            }

            $countGift = count($gift);
            foreach ($gifts as $key) {
                if($key->item_id != $gift[$countGift-1]->item_id){
                    array_push($listgift, $key);
                }
            }

            for($j=0;$j<$numrolllop;$j++)
            {
                $gift_types[$j] = $gift[$j]->parrent->params->gift_type;
            }

            $vp_txns = 0;
            $balance_item_txns = 0;
            if($group->params->game_type == 14){//Vàng ngọc rồng nrocoin
                $balance_item_txns = $userTransaction->coin_num;
            }else if($group->params->game_type == 12){//Ngọc ngọc rồng nrogem
                $balance_item_txns = $userTransaction->gem_num;
            }else if($group->params->game_type == 11){ //Xu ninja ninjaxu
                $balance_item_txns = $userTransaction->xum_num;
            }else if($group->params->game_type == 13){ //Xu ninja ninjaxu
                $balance_item_txns = $userTransaction->robux_num;
            }
            else{
                $balance_item_txns = $userTransaction['ruby_num'.$group->params->game_type];
            }
            foreach ($gift_types as $key=>$gift_type) {
                if($group->params->random_point_from > 0 && $group->params->random_point_to > 0 && $group->params->random_point_from < $group->params->random_point_to)
                {
                    $point = rand($group->params->random_point_from, $group->params->random_point_to);
                }

                $random_x =  array();
                $random_x[0] = 100;//$gift[$key]->position1;//X1 giá trị
                $random_x[1] = 0;//$gift[$key]->position2;//X2 giá trị
                $random_x[2] = 0;//$gift[$key]->position3;//X3 giá trị
                $xgt[$key] = $this->getRandomX($random_x);
                if($xgt[$key] == 0)
                {
                    $xValue[$key] = 1;
                }
                elseif($xgt[$key] == 1)
                {
                    $xValue[$key] = 4;
                }
                elseif($xgt[$key] == 2)
                {
                    $xValue[$key] = 5;
                }
                else
                {
                    $xValue[$key]=1;
                }

                //check random bonus kim cương
                $value_gif_bonus[$key] = 0;
                if($gift[$key]->params->bonus_from > 0 && $gift[$key]->params->bonus_to <= 0)
                {
                    $value_gif_bonus[$key] = $gift[$key]->params->bonus_from;
                }
                elseif($gift[$key]->params->bonus_from <= 0 && $gift[$key]->params->bonus_to > 0)
                {
                    $value_gif_bonus[$key] = $gift[$key]->params->bonus_to;
                }
                elseif($gift[$key]->params->bonus_from > 0 && $gift[$key]->params->bonus_to > 0 && $gift[$key]->params->bonus_from > $gift[$key]->params->bonus_to)
                {
                    $value_gif_bonus[$key] = mt_rand($gift[$key]->params->bonus_to, $gift[$key]->params->bonus_from);
                }
                elseif($gift[$key]->params->bonus_from > 0 && $gift[$key]->params->bonus_to > 0 && $gift[$key]->params->bonus_from < $gift[$key]->params->bonus_to)
                {
                    $value_gif_bonus[$key] = mt_rand($gift[$key]->params->bonus_from, $gift[$key]->params->bonus_to);
                }
                elseif($gift[$key]->params->bonus_from > 0 && $gift[$key]->params->bonus_to > 0 && $gift[$key]->params->bonus_from == $gift[$key]->params->bonus_to)
                {
                    $value_gif_bonus[$key] = $gift[$key]->params->bonus_from;
                }
                else
                {
                    $value_gif_bonus[$key] = 0;
                }

                $userbalance = $group->params->type_charge == 1?$userTransaction->balance_lock:$userTransaction->balance;
                if($gift_type==0){
                    if($group->params->game_type == 14){//Vàng ngọc rồng nrocoin
                        if($userTransaction->coin_num > 1500000000){
                            $status = 0;
                            $msg = __("Rút bớt vàng trước khi chơi tiếp!");
                            return response()->json(array('msg'=> $msg, 'status' => $status), 200);
                        }
                    }else if($group->params->game_type == 12){//Ngọc ngọc rồng nrogem
                        if($userTransaction->gem_num > 1500000000){
                            $status = 0;
                            $msg = __("Rút bớt ngọc trước khi chơi tiếp!");
                            return response()->json(array('msg'=> $msg, 'status' => $status), 200);
                        }
                    }else if($group->params->game_type == 11){//Xu ninja ninjaxu
                        if($userTransaction->xu_num > 1500000000){
                            $status = 0;
                            $msg = __("Rút bớt xu trước khi chơi tiếp!");
                            return response()->json(array('msg'=> $msg, 'status' => $status), 200);
                        }
                    }else if($group->params->game_type == 13){//Xu ninja ninjaxu
                        if($userTransaction->robux_num > 1500000000){
                            $status = 0;
                            $msg = __("Rút bớt robux trước khi chơi tiếp!");
                            return response()->json(array('msg'=> $msg, 'status' => $status), 200);
                        }
                    }else{
                        if($userTransaction['ruby_num'.$group->params->game_type] > 1500000000){
                            $status = 0;
                            $msg = __("Rút bớt vật phẩm trước khi chơi tiếp!");
                            return response()->json(array('msg'=> $msg, 'status' => $status), 200);
                        }
                    }

                    $order = Order::create([
                        'module' => $moduleLog,
                        'shop_id' => $request->shop_id,
                        'description' => "Nổ hũ ".$module." trúng phần thưởng #".$gift[$key]->item_id,
                        'gate_id' => $group->id,
                        'author_id' => $user->id,
                        'price' => $fee,
                        'value_gif_bonus' => $value_gif_bonus[$key],
                        'real_received_price' => $gift[$key]->parrent->params->value * $xValue[$key],
                        'ref_id' => $gift[$key]->item_id
                    ]);

                    //Cộng vật phẩm
                    if($group->params->game_type == 14){ //Vàng ngọc rồng nrocoin
                        $userTransaction->coin_num = $userTransaction->coin_num + ($gift[$key]->parrent->params->value* $xValue[$key]) + $value_gif_bonus[$key];
                        if($value_gif_bonus[$key] > 0)
                        {
                            $msg_random_bonus[$key] = " .Bạn nhận được thêm ".$value_gif_bonus[$key]." vàng bonus";
                        }
                        else
                        {
                            $msg_random_bonus[$key] = "";
                        }
                    }else if($group->params->game_type == 12){//Ngọc ngọc rồng nrogem
                        $userTransaction->gem_num = $userTransaction->gem_num + ($gift[$key]->parrent->params->value* $xValue[$key]) + $value_gif_bonus[$key];
                        if($value_gif_bonus[$key] > 0)
                        {
                            $msg_random_bonus[$key] = " .Bạn nhận được thêm ".$value_gif_bonus[$key]." ngọc bonus";
                        }
                        else
                        {
                            $msg_random_bonus[$key] = "";
                        }
                    }else if($group->params->game_type == 11){//Xu ninja ninjaxu
                        $userTransaction->xu_num = $userTransaction->xu_num + ($gift[$key]->parrent->params->value* $xValue[$key]) + $value_gif_bonus[$key];
                        if($value_gif_bonus[$key] > 0)
                        {
                            $msg_random_bonus[$key] = " .Bạn nhận được thêm ".$value_gif_bonus[$key]." xu bonus";
                        }
                        else
                        {
                            $msg_random_bonus[$key] = "";
                        }
                    }else if($group->params->game_type == 13){//Xu ninja ninjaxu
                        $userTransaction->robux_num = $userTransaction->robux_num + ($gift[$key]->parrent->params->value* $xValue[$key]) + $value_gif_bonus[$key];
                        if($value_gif_bonus[$key] > 0)
                        {
                            $msg_random_bonus[$key] = " .Bạn nhận được thêm ".$value_gif_bonus[$key]." robux bonus";
                        }
                        else
                        {
                            $msg_random_bonus[$key] = "";
                        }
                    }else{

                        if ($group->params->game_type == 1) {
                            $userTransaction['ruby_num'.$group->params->game_type] = $userTransaction['ruby_num'.$group->params->game_type] + ($gift[$key]->parrent->params->value* $xValue[$key]) + $value_gif_bonus[$key];
                            if($value_gif_bonus[$key] > 0)
                            {
                                $msg_random_bonus[$key] = " .Bạn nhận được thêm ".$value_gif_bonus[$key]." quân huy bonus";

                            }
                            else
                            {
                                $msg_random_bonus[$key] = "";
                            }
                        }else{
                            $userTransaction['ruby_num'.$group->params->game_type] = $userTransaction['ruby_num'.$group->params->game_type] + ($gift[$key]->parrent->params->value* $xValue[$key]) + $value_gif_bonus[$key];
                            if($value_gif_bonus[$key] > 0)
                            {
                                $msg_random_bonus[$key] = " .Bạn nhận được thêm ".$value_gif_bonus[$key]." kim cương bonus";

                            }
                            else
                            {
                                $msg_random_bonus[$key] = "";
                            }
                        }

                    }
                    $vp_txns += ($gift[$key]->parrent->params->value* $xValue[$key]) + $value_gif_bonus[$key];
                    $datagift = array(
                        'id'                => $gift[$key]->item_id,
                        'image'             => $gift[$key]->children[0]->image,
                        'name'              => $gift[$key]->children[0]->title,
                        'order'             => $gift[$key]->order,  //'pos'
                        'winbox'            => $gift[$key]->parrent->params->winbox, //'locale'
                        'num_roll_remain'   => $fee==0?0:FLOOR($userbalance/$fee),
                        'gift_type'         => $gift_type, //'input_auto'
                        'game_type'           => $group->params->game_type //is_ruby
                    );
                    $status = 'OK';
                    $gift_detail = $datagift;
                }else{
                    $items = Item::where('status','1')->where('module','minigame-acc')->where('idkey',$group->params->game_type)->limit(20)->get();
                    if(count($items) <= 0){
                        return response()->json([
                            'msg' => __('Đang cập nhật quà!'),
                            'status' => 0
                        ], 200);
                    }
                    $index = rand(0, count($items)-1);

                    $order = Order::create([
                        'module' => $moduleLog,
                        'shop_id' => $request->shop_id,
                        'description' => "Nổ hũ ".$module." trúng phần thưởng #".$gift[$key]->item_id,
                        'gate_id' => $group->id,
                        'acc_id' => $items[$index]["id"],
                        'author_id' => $user->id,
                        'price' => 0,
                        'ref_id' => $gift[$key]->item_id
                    ]);

                    $datagift = array(
                        'id'                => $gift[$key]->item_id,
                        'image'             => $gift[$key]->children[0]->image,
                        'name'              => $gift[$key]->children[0]->title,
                        'order'             => $gift[$key]->order,  //'pos'
                        'winbox'            => $gift[$key]->parrent->params->winbox, //'locale'
                        'num_roll_remain'   => $fee==0?0:FLOOR($userbalance/$fee),
                        'gift_type'         => $gift_type, //'input_auto'
                        'game_type'         => '' //is_ruby
                    );

                    Item::where('id', $items[$index]["id"])->update(['status' => 0]);

                    $status = 'OK';
                    $gift_detail = $datagift;
                }
                $arr_gift[$key]  = $gift[$key];
            }
            //tạo tnxs vp
            $txns = TxnsVp::create([
                'trade_type' => $module,
                'is_add' => '0',
                'user_id' => $user->id,
                'amount' => $vp_txns,
                'last_balance' => $balance_item_txns + $vp_txns,
                'description' => "Nổ hũ ".$module." trúng phần thưởng #".$gift[$key]->item_id,
                'ref_id' => $order->id,
                'ip' => $request->getClientIp(),
                'status' => 1,
                'shop_id' => $request->shop_id,
                'order_id' => $order->id,
                'item_type' => $group->params->game_type
            ]);

            //tạo tnxs
            $txns = Txns::create([
                'trade_type' => $module,
                'is_add' => '0',
                'user_id' => $user->id,
                'amount' => 0,
                'last_balance' => $userbalance,
                'description' => "Nổ hũ ".$module." trúng phần thưởng #".$gift[$key]->item_id,
                'ref_id' => $order->id,
                'ip' => $request->getClientIp(),
                'status' => 1,
                'shop_id' => $request->shop_id,
                'order_id' => $order->id
            ]);

            $msg = "Nổ hủ may mắn!";
            $userpoint = $userTransaction->point - 100 > 0 ? $userTransaction->point - 100 : 0;
            $userTransaction->point = $userpoint;
            $userTransaction->save();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json([
                'msg' => __('Có lỗi phát sinh.Xin vui lòng thử lại!'),
                'status' => 0
            ], 200);
        }
        DB::commit();
        return response()->json([
            'free_wheel'=> $free_wheel,
            'arr_gift' => $arr_gift,
            'gift_detail' => $gift_detail,
            'xgt' => $xgt,
            'xValue' => $xValue,
            'value_gif_bonus' => $value_gif_bonus,
            'msg_random_bonus' => $msg_random_bonus,
            'userpoint' => $userpoint,
            'status' => 1,
            'listgift' => $listgift,
            'game_type' => $group->params->game_type,
            'msg'=> $msg
        ], 200);
    }

    function getRandomWeightedElement(array $weightedValues){
        $array = array();
        foreach ($weightedValues as $key => $weight) {
            if($weight == 100)
            {
                return $key;
            }
            $array = array_merge(array_fill(0, $weight, $key), $array);
        }
        return $array[array_rand($array)];
    }

    function getRandomX(array $weightedValues){
        $array = array();

        foreach ($weightedValues as $key => $weight) {
            $array = array_merge(array_fill(0, $weight, $key), $array);
        }

        return $array[array_rand($array)];
    }

    public function getWithdrawItem(Request $request)
    {
        $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
        if(!$shop){
            return response()->json([
                'msg' => __('Domain chưa được đăng ký!'),
                'status' => 0
            ], 200);
        }

        $shop_id = $shop->id;

        if(!Auth::guard('api')->check()){
            return response()->json([
                'msg' => __('Vui lòng đăng nhập!'),
                'status' => 4
            ], 200);
        }
        $page = $request->page;
        $paginate = 10;

        if ($request->filled('limit')) {
            $paginate = $request->limit;
        }

        $number_item = 0;
        $user = Auth::guard('api')->user();
        $userTransaction = User::where('id', $user->id)->firstOrFail();

        $game_type = $request->game_type;
        $service = null;
        $idkey = null;

        if ($game_type == 11){//Xu ninja school ninjaxu
            $idkey = 'ninjaxu';
            $number_item = $userTransaction->xu_num;
        }elseif ($game_type == 12){//Ngọc ngọc rồng nrogem
            $idkey = 'nrogem';
            $number_item = $userTransaction->gem_num;
        }elseif ($game_type == 14){//Vàng ngọc rồng nrocoin
            $idkey = 'nrocoin';
            $number_item = $userTransaction->coin_num;
        }elseif ($game_type == 13){//Bán Roblox (Dạng mua server) roblox_buyserver
            $idkey = 'roblox_buyserver';
            $number_item = $userTransaction->robux_num;
        }
        else{
            $number_item = $userTransaction['ruby_num'.$game_type];
        }

        $groups = Group::select('id','title','price','params','module','slug','image','image_icon','seo_title','position','description','seo_description')
            ->where('module', 'minigame-category')
            ->with('order_gate',function ($query) use ($shop_id) {
                $query->where('shop_id', $shop_id);
            })
            ->withCount('order_gate')
            ->where('status', 1)
            ->with('customs', function ($query) use ($shop_id) {
                $query->where('shop_id', $shop_id)->orderBy('order');
            })
            ->whereHas('customs', function ($query) use ($shop_id) {
                $query->where('shop_id', $shop_id)->where('status', 1)->orderBy('order');
            })->orderBy('order')
            ->get();

        if (isset($groups) && count($groups)){}else{
            return response()->json([
                'msg' => __('Điểm bán chưa cấu hình có minigame nào'),
                'status' =>0
            ], 200);
        }

        $list_game_types = array();

        foreach ($groups as $group){
            if (!in_array($group->params->game_type,$list_game_types)){
                array_push($list_game_types,$group->params->game_type);
            }
        }

        $listgametype = Item::where('module', config('module.minigame.module.gametype'))
            ->where('status', 1)->orderBy('order')->whereIn('parent_id',$list_game_types)->get();

        foreach ($listgametype as $item){

            if ($item->parent_id == 11){
                $n_item = $userTransaction->xu_num;
            }elseif ($item->parent_id == 12){
                $n_item = $userTransaction->gem_num;
            }elseif ($item->parent_id == 14){
                $n_item = $userTransaction->coin_num;
            }elseif ($item->parent_id == 13){
                $n_item = $userTransaction->robux_num;
            }else{
                $n_item = $userTransaction['ruby_num'.$item->parent_id];
            }

            if (isset($n_item)){
                $item->set_number_item = $n_item;
            }else{
                $item->set_number_item = 0;
            }
        }

        if (isset($idkey)){
            if ($idkey == 'nrocoin'){
                $aridkey = ['nrocoin_internal','nrocoin'];
            }else if($idkey == "nrogem"){
                $aridkey = ['nrogem_internal','nrogem'];
            }else if ($idkey == "ninjaxu"){
                $aridkey = ['ninjaxu_internal','ninjaxu'];
            }else if ($idkey == "roblox_buyserver"){
                $aridkey = ['roblox_buyserver_internal','roblox_buyserver','roblox_buygamepass','roblox_buygamepass_internal','roblox_internal'];
            }

            $service = ItemConfig::with(array('items' => function ($query) {
                $query->with(array('groups' => function ($q) {
                    $q->select('groups.id', 'title', 'slug');
                }));
            }))->where('module', config('module.service.key'))
                ->where('status', '=', 1)
                ->where('gate_id', '=', 1)
                ->whereIn('idkey',  $aridkey)
                ->where('shop_id', $request->shop_id)
                ->select('id','params','idkey')
                ->first();

        }

        $gametype = Item::where('module', config('module.minigame.module.gametype'))
            ->where('status', 1)->where('parent_id',$game_type)->first();

        if (!isset($gametype)){
            return response()->json([
                'msg' => __('Điểm bán chưa cấu hình thông tin người rút'),
                'status' =>0
            ], 200);
        }

        $package = null;
        if($gametype){
//            $package = Item::select('id','title','price')
//                ->where('module', config('module.minigame.module.package'))->where('status', 1)->where('parent_id',$gametype->id)
//                ->orderBy('price')->get();
            $package = ItemConfig::with(array('items' => function ($query) use($gametype){
                $query->where('module','package');
                $query->where('parent_id',$gametype->id);
                $query->whereHas('parrent', function ($query) use ($gametype){
                    $query->where('id',$gametype->id);
                });
                $query->with(array('parrent' => function ($query) use($gametype){
                    $query->where('id',$gametype->id);
                    $query->where('module','package');
                }));
            }))
                ->whereHas('items', function ($query) use ($gametype){
                    $query->where('module','package');
                    $query->where('parent_id',$gametype->id);
                    $query->whereHas('parrent', function ($query) use ($gametype){
                        $query->where('id',$gametype->id);
                    });
                    $query->with(array('parrent' => function ($query) use($gametype){
                        $query->where('id',$gametype->id);
                        $query->where('module','package');
                    }));
                })
                ->where('status',1)->where('module','package')->where('shop_id',$shop_id)->get();
        }

        $data = Order::with('author')
            ->where(function($q){
                $q->orWhere('module', config('module.minigame.module.withdraw-item'));
                $q->orWhere('module', 'withdraw-service-item');
            })
            ->where('shop_id', $request->shop_id)->where('author_id', $userTransaction->id)->where('payment_type',$game_type);

        $data_first = $data->orderBy('created_at', 'desc')->get();

        $data = collect($data_first)->slice($paginate * ($page - 1), $paginate);
        $data = new LengthAwarePaginator($data, count($data_first), $paginate, $page, [
            'path'  => $request->url(),
        ]);

        return response()->json([
            'msg' => __('Thành công'),
            'withdraw_history' => $data,
            'package' => $package,
            'service' => $service,
            'gametype' => $gametype,
            'listgametype' => $listgametype,
            'number_item' => $number_item,
            'status' => 1
        ], 200);
    }

    public function postWithdrawItem(Request $request)
    {
        if(!Auth::guard('api')->check()){
            return response()->json([
                'msg' => __('Vui lòng đăng nhập!'),
                'status' => 4
            ], 200);
        }

        $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
        if(!$shop){

            return response()->json([
                'msg' => __('Điểm bán chưa được đăng ký!'),
                'status' => 0
            ], 200);
        }

        //loại vật phẩm cộng dồn
        $type = $request->type;
        DB::beginTransaction();
        try {

            $userTransaction = User::where('id', Auth::guard('api')->user()->id)->lockForUpdate()->firstOrFail();
            $old_item = 0;
            if ($type == 11){//Xu ninja school ninjaxu
                $old_item = $userTransaction->xu_num;
            }elseif ($type == 12){//ngọc ngọc rồng nrogem
                $old_item = $userTransaction->gem_num;
            }elseif ($type == 14){//Vàng ngọc rồng nrocoin
                $old_item = $userTransaction->coin_num;
            }elseif ($type == 13){//Roblox roblox_buyserver
                $old_item = $userTransaction->robux_num;
            }else{
                $old_item = $userTransaction['ruby_num'.$type];
            }

            $idgame = $request->idgame;
            $info_plus = $request->phone;
            $server = $request->get('server');
            $service_id = $request->get('service_id');
            $params = $request->get('params');

            $package = ItemConfig::with(array('items' => function ($query) use($request){
                $query->where('module','package');
                $query->whereHas('parrent', function ($query) use ($request){
                    $query->where('module','package');
                });
                $query->with(array('parrent' => function ($query) use($request){
                    $query->where('module','package');
                }));
            }))->where('status',1)->where('module','package')->where('id', $request->package)
                ->where('shop_id',$shop->id)->first();

            if(!$package){
                DB::rollback();
                return response()->json([
                    'msg' => __('Không tìm thấy gói rút!'),
                    'status' => 0
                ], 200);
            }
            //Lấy gói rút
//            $package = Item::where('module', config('module.minigame.module.package'))->where('id', $request->package)->where('status',1)->firstOrFail();
            $amount = $package->price;

            $payment_gateways = null;

            $package_sticky = 1;

            if (isset($package->sticky)){
                $package_sticky = $package->sticky;
            }
            $payment_gateways = config('module.minigame.payment_gateway.'.$package_sticky);

            if (!isset($payment_gateways)){
                $payment_gateways = 'SMS';
            }

            $provider = "";
            $id = "";
            $username = "";
            $password = "";
            $link_server = "";

            if($old_item < $amount){
                DB::rollback();
                return response()->json([
                    'msg' => __('Bạn không đủ số vật phẩm để rút gói này!'),
                    'status' => 0
                ], 200);
            }

            $balance_item_txns = 0;

            if ($old_item >= $amount) {
                //trừ vật phẩm của user
                if ($type == 11){//Xu ninja school ninjaxu

                    $balance_item_txns = $userTransaction->xu_num;
                    $userTransaction->xu_num = $userTransaction->xu_num - $amount;
                }elseif ($type == 12){//ngọc ngọc rồng nrogem

                    $balance_item_txns = $userTransaction->gem_num;
                    $userTransaction->gem_num = $userTransaction->gem_num - $amount;
                }elseif ($type == 14){//Vàng ngọc rồng nrocoin

                    $balance_item_txns = $userTransaction->coin_num;
                    $userTransaction->coin_num = $userTransaction->coin_num - $amount;
                }elseif ($type == 13){//Vàng ngọc rồng nrocoin

                    $balance_item_txns = $userTransaction->robux_num;
                    $userTransaction->robux_num = $userTransaction->robux_num - $amount;
                }else{
                    $balance_item_txns = $userTransaction['ruby_num'.$type];
                    $userTransaction['ruby_num'.$type] = $userTransaction['ruby_num'.$type] - $amount;
                }

                $userTransaction->save();
            }

            $gametype= Item::where('module', config('module.minigame.module.gametype'))->where('status',1)
                ->where('parent_id', $type)
                ->first();

            if($gametype){
                //Lấy loại game
                $provider = config('module.minigame.game_type_map.'.$type);

                if (!isset($provider)){
                    DB::rollback();
                    return response()->json([
                        'msg' => __('Không tìm thấy loại game!'),
                        'status' => 0
                    ], 200);
                }

                if ($provider == "freefire" || $provider == "pubgm") {
                    $id = $idgame;
                    $username = "";
                    $password = "";
                }
                else {
                    $id = "";
                    $username = $idgame;
                    $password = $info_plus;
                }

                //lấy giá của gói rút
                $item = 0;
                if($provider == "lienminh"){
                    if ($payment_gateways == 'SMS'){
                        if($amount == "16"){
                            $item = 10;
                        }else if($amount == "32"){
                            $item = 20;
                        }else if($amount == "84"){
                            $item = 50;
                        }else if($amount == "168"){
                            $item = 100;
                        }else if($amount == "340"){
                            $item = 200;
                        }else if($amount == "856"){
                            $item = 500;
                        }else{
                            DB::rollback();
                            return response()->json([
                                'msg' => __('Vui lòng chọn đúng 1 trong các gói sau: 16, 32, 84, 168, 340!'),
                                'status' => 0
                            ], 200);
                        }
                    }
                    else{
                        DB::rollback();
                        return response()->json([
                            'msg' => __('lienminh không chọn cổng GARENA ĐƯỢC!'),
                            'status' => 0
                        ], 200);
                    }

                }
                else if($provider == "roblox_ads"){
                    if($amount == "20000"){
                        $item = 20000;
                    }
                    else if($amount == "25000"){
                        $item = 25000;
                    }
                    else if($amount == "50000"){
                        $item = 50000;
                    }
                    else if($amount == "100000"){
                        $item = 100000;
                    }
                    else{
                        DB::rollback();
                        return response()->json([
                            'msg' => __('Vui lòng chọn đúng 1 trong các gói sau: 20.000,25.000,50.000,100.000!'),
                            'status' => 0
                        ], 200);
                    }
                }
                else if($provider == "freefire_ads"){
                    if($amount == "10000"){
                        $item = 10000;
                    }
                    else if($amount == "20000"){
                        $item = 20000;
                    }
                    else if($amount == "30000"){
                        $item = 30000;
                    }
                    else if($amount == "40000"){
                        $item = 40000;
                    }
                    else if($amount == "50000"){
                        $item = 50000;
                    }
                    else if($amount == "60000"){
                        $item = 60000;
                    }
                    else if($amount == "100000"){
                        $item = 100000;
                    }
                    else if($amount == "250000"){
                        $item = 250000;
                    }
                    else{
                        DB::rollback();
                        return response()->json([
                            'msg' => __('Vui lòng chọn đúng 1 trong các gói sau: 10.000,20.000,30.000,40.000,50.000,60.000,100.000,250.000!'),
                            'status' => 0
                        ], 200);
                    }
                }
                else if($provider == "lienquan"){
                    if ($payment_gateways == 'SMS'){
                        if($amount == "16"){
                            $item = 10;
                        }else if($amount == "32"){
                            $item = 20;
                        }else if($amount == "80"){
                            $item = 50;
                        }else if($amount == "160"){
                            $item = 100;
                        }else if($amount == "320"){
                            $item = 200;
                        }else if($amount == "800"){
                            $item = 500;
                        }else{
                            DB::rollback();
                            return response()->json([
                                'msg' => __('Vui lòng chọn đúng 1 trong các gói sau: 16, 32, 80, 168, 340!'),
                                'status' => 0
                            ], 200);
                        }
                    }
                    elseif ($payment_gateways == 'GARENA'){
                        if($amount == "40"){
                            $item = 20;
                        }else if($amount == "100"){
                            $item = 50;
                        }else if($amount == "200"){
                            $item = 100;
                        }else if($amount == "400"){
                            $item = 200;
                        }else if($amount == "1000"){
                            $item = 500;
                        }else{
                            DB::rollback();
                            return response()->json([
                                'msg' => __('Vui lòng chọn đúng 1 trong các gói sau: 16, 32, 80, 168, 340!'),
                                'status' => 0
                            ], 200);
                        }
                    }
                    else{
                        DB::rollback();
                        return response()->json([
                            'msg' => __('Vui lòng chọn Cổng thanh toán!'),
                            'status' => 0
                        ], 200);
                    }
                }
                else if($provider == "freefire"){
                    if ($payment_gateways == 'SMS'){
                        if($amount == "40"){
                            $item = 10;
                        }else if($amount == "88"){
                            $item = 20;
                        }else if($amount == "220"){
                            $item = 50;
                        }else if($amount == "440"){
                            $item = 100;
                        }else if($amount == "880"){
                            $item = 200;
                        }else if($amount == "2200"){
                            $item = 500;
                        }
                        else{
                            DB::rollback();
                            return response()->json([
                                'msg' => __('Vui lòng chọn đúng 1 trong các gói sau: 40, 88, 220, 440, 880, 2200!'),
                                'status' => 0
                            ], 200);
                        }
                    }
                    elseif ($payment_gateways == 'GARENA'){
                        if($amount == "110"){
                            $item = 20;
                        }else if($amount == "275"){
                            $item = 50;
                        }else if($amount == "550"){
                            $item = 100;
                        }else if($amount == "1100"){
                            $item = 200;
                        }else if($amount == "2750"){
                            $item = 500;
                        }
                        else{
                            DB::rollback();
                            return response()->json([
                                'msg' => __('Vui lòng chọn đúng 1 trong các gói sau: 110, 275, 550, 1100, 2750!'),
                                'status' => 0
                            ], 200);
                        }
                    }
                    else{
                        DB::rollback();
                        return response()->json([
                            'msg' => __('Vui lòng chọn Cổng thanh toán!'),
                            'status' => 0
                        ], 200);
                    }

                }
                else if($provider == "knightageonline"){
                    if($amount == "3000000"){
                        $item = 3000000;
                    }else if($amount == "12000000"){
                        $item = 12000000;
                    }else if($amount == "20000000"){
                        $item = 20000000;
                    }else if($amount == "40000000"){
                        $item = 40000000;
                    }else if($amount == "100000000"){
                        $item = 100000000;
                    }else{
                        DB::rollback();
                        return response()->json([
                            'msg' => __('Vui lòng chọn đúng 1 trong các gói sau: 3tr, 12tr, 20tr, 40tr, 100tr!'),
                            'status' => 0
                        ], 200);
                    }
                }
                else if($provider == "pubgm"){
                    if ($payment_gateways == 'SMS'){
                        if($amount == "48"){
                            $item = 20;
                        }else if($amount == "119"){
                            $item = 50;
                        }else if($amount == "246"){
                            $item = 100;
                        }else if($amount == "252"){
                            $item = 200;
                        }else{
                            DB::rollback();
                            return response()->json([
                                'msg' => __('Vui lòng chọn đúng 1 trong các gói sau: 48, 119, 246, 252!'),
                                'status' => 0
                            ], 200);
                        }
                    }
                    else{
                        DB::rollback();
                        return response()->json([
                            'msg' => __('pubgm không chọn cổng GARENA ĐƯỢC!'),
                            'status' => 0
                        ], 200);
                    }
                }
                else if($provider == "ruby"){

                    if($amount == "10"){
                        $item = 10;
                    }else if($amount == "50"){
                        $item = 50;
                    }else if($amount == "100"){
                        $item = 100;
                    }else if($amount == "200"){
                        $item = 200;
                    }else{
                        DB::rollback();
                        return response()->json([
                            'msg' => __('Vui lòng chọn đúng 1 trong các gói sau: 10, 50, 100, 200!'),
                            'status' => 0
                        ], 200);
                    }
                }
                else if($provider == "genesis_crystal"){
                    if($amount == "60"){
                        $item = 60;
                    }else if($amount == "300"){
                        $item = 300;
                    }else if($amount == "980"){
                        $item = 980;
                    }else if($amount == "1980"){
                        $item = 1980;
                    }else{
                        DB::rollback();
                        return response()->json([
                            'msg' => __('Vui lòng chọn đúng 1 trong các gói sau: 60, 300, 980, 1980!'),
                            'status' => 0
                        ], 200);
                    }
                }
                else if($provider == "roblox_buyserver"){
                    if($amount == "100"){
                        $item = 100;
                    }else if($amount == "200"){
                        $item = 200;
                    }else if($amount == "300"){
                        $item = 300;
                    }else if($amount == "500"){
                        $item = 500;
                    }else if($amount == "1000"){
                        $item = 1000;
                    }
                    else{
                        DB::rollback();
                        return response()->json([
                            'msg' => __('Vui lòng chọn đúng 1 trong các gói sau: 100, 200, 300!'),
                            'status' => 0
                        ], 200);
                    }
                }
                else if($provider == "ninjaxu"){
                    if($amount == "1500000"){
                        $item = 1500000;
                    }else if($amount == "2500000"){
                        $item = 2500000;
                    }else if($amount == "4500000"){
                        $item = 4500000;
                    }else if($amount == "7000000"){
                        $item = 7000000;
                    }else if($amount == "10000000"){
                        $item = 10000000;
                    }else if($amount == "20000000"){
                        $item = 20000000;
                    }else{
                        DB::rollback();
                        return response()->json([
                            'msg' => __('Vui lòng chọn đúng 1 trong các gói sau: 1 tr, 2 tr, 3 tr, 5 tr Xu!'),
                            'status' => 0
                        ], 200);
                    }
                }
                else if($provider == "nrogem"){
                    if($amount == "150"){
                        $item = 150;
                    }else if($amount == "200"){
                        $item = 200;
                    }else if($amount == "250"){
                        $item = 250;
                    }else if($amount == "301"){
                        $item = 301;
                    }else if($amount == "401"){
                        $item = 401;
                    }else if($amount == "550"){
                        $item = 550;
                    }else if($amount == "600"){
                        $item = 600;
                    }else if($amount == "700"){
                        $item = 700;
                    }else if($amount == "800"){
                        $item = 800;
                    }else if($amount == "900"){
                        $item = 900;
                    }else if($amount == "1000"){
                        $item = 1000;
                    }else if($amount == "1500"){
                        $item = 1500;
                    }
                    else{
                        DB::rollback();
                        return response()->json([
                            'msg' => __('Vui lòng chọn đúng 1 trong các gói sau: 150, 200, 250, 301, 401, 550, 600, 700, 800, 900, 1.000, 1.500 Hồng Ngọc!'),
                            'status' => 0
                        ], 200);
                    }
                }
                else if($provider == "nrocoin"){
                    if($amount == "50000000"){
                        $item = 50000000;
                    }else if($amount == "100000000"){
                        $item = 100000000;
                    }else if($amount == "200000000"){
                        $item = 200000000;
                    }else if($amount == "300000000"){
                        $item = 300000000;
                    }else if($amount == "400000000"){
                        $item = 400000000;
                    }
                    else if($amount == "500000000"){
                        $item = 500000000;
                    }else{
                        DB::rollback();
                        return response()->json([
                            'msg' => __('Vui lòng chọn đúng 1 trong các gói sau: 100 tr, 200 tr, 300 tr, 500 tr Vàng!'),
                            'status' => 0
                        ], 200);
                    }
                }
                else{
                    $provider ='';
                }
            }
            else{
                DB::rollback();
                return response()->json([
                    'msg' => __('Không tìm thấy thông tin gói rút!'),
                    'status' => 0
                ], 200);

            }

            if ($provider == 'roblox_buyserver' || $provider == 'nrocoin' || $provider == 'nrogem' || $provider == 'ninjaxu'){// Xử lý rút roblox
                if($gametype->target == 1){

                    $idkey = null; //lấy idkey

                    if (!$request->get('service_id')){
                        DB::rollBack();

                        return response()->json([
                            'msg' => __('Dịch vụ không tồn tại.Vui lòng thử lại'),
                            'status' => 0
                        ], 200);
                    }

                    $service = ItemConfig::with('items')
                        ->where('status', '=', 1)
                        ->where('module', '=', config('module.service.key'))
                        ->where('shop_id', $request->shop_id)
                        ->where('id',$service_id)
                        ->first();

                    if (!isset($service)){
                        DB::rollBack();

                        return response()->json([
                            'msg' => __('Dịch vụ không tồn tại.Vui lòng thử lại'),
                            'status' => 0
                        ], 200);
                    }

                    $idkey = $service->idkey;

                    $input_auto = $service->gate_id;

                    if ($input_auto != 1){
                        DB::rollBack();

                        return response()->json([
                            'msg' => __('Dịch vụ hiện tại đang thủ công.Vui lòng thử lại'),
                            'status' => 0
                        ], 200);
                    }

                    //Kiểm tra thời gian giao dịch đối với ngọc ngọc rồng.
                    if ($service->idkey == "nrogem" || $service->idkey == "nrogem_internal") {

                        $check5Min = Order::where('author_id', auth()->user()->id)
                            ->where('module', config('module.minigame.module.withdraw-service-item'))
                            ->where('sticky', $service->id)
                            ->where('shop_id', $request->shop_id)
                            ->orderBy('created_at', 'desc')->first();

                        if ($check5Min) {

                            if (strtotime($check5Min->created_at) < strtotime("-5 minutes")) {

                            } else {
                                DB::rollback();
                                return response()->json([
                                    'msg' => __('Vui lòng chờ khoảng 5 phút để tạo thêm order mua ngọc!'),
                                    'status' => 0
                                ], 200);
                            }
                        }

                        $checkOrderFinish = Order::where('author_id', auth()->user()->id)
                            ->where('module', config('module.minigame.module.withdraw-service-item'))
                            ->where('sticky', $service->id)
                            ->where('status', 1)
                            ->where('shop_id', $request->shop_id)
                            ->orderBy('created_at', 'desc')->first();

                        if ($checkOrderFinish) {
                            DB::rollback();
                            return response()->json([
                                'msg' => __('Hiện tại hệ thống đang xử lý yêu cầu trước của bạn. Vui lòng thử lại sau'),
                                'status' => 0
                            ], 200);

                        }
                    }
                    //Kiểm tra server.

                    if (Helpers::DecodeJson("server_mode", $service->params) == 1) {
                        $server_data = Helpers::DecodeJson("server_data", $service->params);
                        if(!($server_data[$server]??null)){

                            DB::rollback();
                            return response()->json([
                                'msg' => __('Vui lòng chọn máy chủ của dịch vụ thanh toán!'),
                                'status' => 0
                            ], 200);

                        }

                        if ((strpos($server_data[$server], '[DELETE]') === true)) {

                            DB::rollback();
                            return response()->json([
                                'msg' => __('Máy chủ bạn đã đóng hoặc không hợp lệ.Xin vui lòng chọn lại máy chủ!'),
                                'status' => 0
                            ], 200);
                        }
                    }

                    if ($service->idkey == 'roblox_buyserver'){
                        $link_server = $idgame;
                        //Kiểm tra có đúng link hay không

                        $server_id= RobloxGate::detectLink($link_server);

                        if($server_id == ""){

                            DB::rollBack();
                            return response()->json([
                                'msg' => __('Link server roblox không hợp lệ.Vui lòng thử lại!'),
                                'status' => 0
                            ], 200);

                        }

                        $customer_info['server'] = $server_id;
                        //Tự động
                        $tranid = $userTransaction->id.time() . rand(10000, 99999);  /// Cái này có thể mà mã order của bạn, nó là duy nhất (enique) để phân biệt giao dịch.
                        //tao lệnh rút tiền
                        $order = Order::create([
                            'request_id'=> $tranid,
                            'ref_id' => $package->id,
                            'sticky' => $service->id,
                            'idkey'=>$service->idkey,
                            'gate_id'=>1,
                            'shop_id' => $request->shop_id,
                            'description'=> "Rút ".$amount." vật phẩm ".config('module.minigame.game_type.'.$type)." vào link server: #".$link_server,
                            'price'=>$amount,
                            'price_base' => $amount,//khách rút
                            'payment_type' => $type,
                            'title'=> $info_plus,
                            'author_id'=>$userTransaction->id,
                            'real_received_price'=> $old_item,
                            'params' => $customer_info,
                            'position' => $server_id,
                            'status'=> 1, // 'Đang chờ xử lý đối với roblox'
                            'module'=>config('module.minigame.module.withdraw-service-item')
                        ]);

                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.withdraw-service-workflow.key'),
                            'author_id' => $userTransaction->id,
                            'status' => 1,

                        ]);
                        //set tên công việc
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.withdraw-service-workname.key'),
                            'title' => number_format($amount) . " " . Helpers::DecodeJson("filter_name", $service->params),
                            'unit_price' => $amount,
                        ]);

                        $roblox_order = Roblox_Order::create([
                            'order_id'=>$order->id,
                            'server'=>$server_id,
                            'uname'=>$link_server,
                            'money'=>$amount,
                            'phone'=>"",
                            'type_order'=>3,
                            'status'=>"chuanhan",
                            'shop_id' => $request->shop_id,
                        ]);

                        //tạo tnxs vp
                        $txns = TxnsVp::create([
                            'trade_type' => config('module.txnsvp.trade_type.withdraw_item'),
                            'is_add' => '0',
                            'user_id' => $userTransaction->id,
                            'amount' => $amount,
                            'last_balance' => $balance_item_txns - $amount,
                            'description' => "Rút vật phẩm mã lệnh rút #".$order->id,
                            'ref_id' => $order->id,
                            'ip' => $request->getClientIp(),
                            'status' => 1,
                            'shop_id' => $request->shop_id,
                            'order_id' => $order->id,
                            'item_type' => $type
                        ]);

                        DB::commit();

                        //Gọi api bắn qua daily

                        $result = HelperItemDaily::fire($shop->daily_partner_id,$shop->daily_partner_key_service,$order->id,$server+1, $idgame, $info_plus,$amount,$service->idkey,1,$order->request_id);

                        if($result && isset($result->status)){
                            if($result->status == 2){

                                //set tiến độ
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'module' => config('module.withdraw-service-workflow.key'),
                                    'status' => 1,
                                    'content' => "Đại lý đã tiếp nhận (Code:2)",
                                ]);

                                return response()->json([
                                    'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                    'status' => 1
                                ], 200);
                            }

                            if($result->status == 0){
                                $order->status=7;
                                $order->save();

                                //set tiến độ
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'module' => config('module.withdraw-service-workflow.key'),
                                    'status' => 1,
                                    'content' => $result->message??"",
                                ]);

                                return response()->json([
                                    'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                    'status' => 1
                                ], 200);
                            }
                        }
                        else{
                            $order->status=9;
                            $order->save();

                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.withdraw-service-workflow.key'),
                                'status' => 1,
                                'content' => "Kết nối NCC thất bại,chuyển về thao tác thủ công",
                            ]);

                            return response()->json([
                                'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                'status' => 1
                            ], 200);
                        }
                    }
                    elseif ($service->idkey == 'roblox_buyserver_internal'){
                        $link_server = $idgame;
                        //Kiểm tra có đúng link hay không

                        $server_id= RobloxGate::detectLink($link_server);

                        if($server_id == ""){

                            DB::rollBack();
                            return response()->json([
                                'msg' => __('Link server roblox không hợp lệ.Vui lòng thử lại!'),
                                'status' => 0
                            ], 200);

                        }

                        $customer_info['server'] = $server_id;
                        //Tự động
                        $tranid = $userTransaction->id.time() . rand(10000, 99999);  /// Cái này có thể mà mã order của bạn, nó là duy nhất (enique) để phân biệt giao dịch.
                        //tao lệnh rút tiền
                        $order = Order::create([
                            'request_id'=> $tranid,
                            'ref_id' => $package->id,
                            'sticky' => $service->id,
                            'idkey'=>$service->idkey,
                            'gate_id'=>1,
                            'shop_id' => $request->shop_id,
                            'description'=> "Rút ".$amount." vật phẩm ".config('module.minigame.game_type.'.$type)." vào link server: #".$link_server,
                            'price'=>$amount,
                            'price_base' => $amount,//khách rút
                            'payment_type' => $type,
                            'title'=> $info_plus,
                            'author_id'=>$userTransaction->id,
                            'real_received_price'=> $old_item,
                            'params' => $customer_info,
                            'position' => $server_id,
                            'status'=> 1, // 'Đang chờ xử lý đối với roblox'
                            'module'=>config('module.minigame.module.withdraw-service-item')
                        ]);

                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.withdraw-service-workflow.key'),
                            'author_id' => $userTransaction->id,
                            'status' => 1,

                        ]);
                        //set tên công việc
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.withdraw-service-workname.key'),
                            'title' => number_format($amount) . " " . Helpers::DecodeJson("filter_name", $service->params),
                            'unit_price' => $amount,
                        ]);

                        $roblox_order = Roblox_Order::create([
                            'order_id'=>$order->id,
                            'server'=>$server_id,
                            'uname'=>$link_server,
                            'money'=>$amount,
                            'phone'=>"",
                            'type_order'=>3,
                            'status'=>"chuanhan",
                            'shop_id' => $request->shop_id,
                        ]);

                        //tạo tnxs vp
                        $txns = TxnsVp::create([
                            'trade_type' => config('module.txnsvp.trade_type.withdraw_item'),
                            'is_add' => '0',
                            'user_id' => $userTransaction->id,
                            'amount' => $amount,
                            'last_balance' => $balance_item_txns - $amount,
                            'description' => "Rút vật phẩm mã lệnh rút #".$order->id,
                            'ref_id' => $order->id,
                            'ip' => $request->getClientIp(),
                            'status' => 1,
                            'shop_id' => $request->shop_id,
                            'order_id' => $order->id,
                            'item_type' => $type
                        ]);

                        DB::commit();

                        $this->dispatch(new RobloxJob($order->id));

                        return response()->json([
                            'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                            'status' => 1
                        ], 200);
                    }
                    elseif ($service->idkey == 'roblox_internal'){

                        $customer_info['customer_data0'] = $idgame;
                        //Tự động
                        $tranid = $userTransaction->id.time() . rand(10000, 99999);  /// Cái này có thể mà mã order của bạn, nó là duy nhất (enique) để phân biệt giao dịch.
                        //tao lệnh rút tiền
                        $order = Order::create([
                            'request_id'=> $tranid,
                            'ref_id' => $package->id,
                            'sticky' => $service->id,
                            'idkey'=>$service->idkey,
                            'gate_id'=>1,
                            'shop_id' => $request->shop_id,
                            'description'=> "Rút ".$amount." vật phẩm ".config('module.minigame.game_type.'.$type)." vào tài khoản game: #".$idgame,
                            'price'=>$amount,
                            'price_base' => $amount,//khách rút
                            'payment_type' => $type,
                            'title'=> $info_plus,
                            'author_id'=>$userTransaction->id,
                            'real_received_price'=> $old_item,
                            'params' => $customer_info,
                            'position' => $idgame,
                            'status'=> 1, // 'Đang chờ xử lý đối với roblox'
                            'module'=>config('module.minigame.module.withdraw-service-item')
                        ]);

                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.withdraw-service-workflow.key'),
                            'author_id' => $userTransaction->id,
                            'status' => 1,
                        ]);

                        //set tên công việc
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.withdraw-service-workname.key'),
                            'title' => number_format($amount) . " " . Helpers::DecodeJson("filter_name", $service->params),
                            'unit_price' => $amount,
                        ]);

                        $roblox_order = Roblox_Order::create([
                            'order_id'=>$order->id,
                            'uname'=>$idgame,
                            'money'=>$amount,
                            'phone'=>"",
                            'type_order'=>4,
                            'status'=>"chuanhan",
                            'shop_id' => $request->shop_id,
                        ]);

                        //tạo tnxs vp
                        $txns = TxnsVp::create([
                            'trade_type' => config('module.txnsvp.trade_type.withdraw_item'),
                            'is_add' => '0',
                            'user_id' => $userTransaction->id,
                            'amount' => $amount,
                            'last_balance' => $balance_item_txns - $amount,
                            'description' => "Rút vật phẩm mã lệnh rút #".$order->id,
                            'ref_id' => $order->id,
                            'ip' => $request->getClientIp(),
                            'status' => 1,
                            'shop_id' => $request->shop_id,
                            'order_id' => $order->id,
                            'item_type' => $type
                        ]);

                        DB::commit();

                        return response()->json([
                            'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                            'status' => 1
                        ], 200);
                    }
                    elseif ($service->idkey == 'roblox_buygamepass'){
                        $link_server = $idgame;
                        //Kiểm tra có đúng link hay không

                        $customer_info['server'] = $link_server;
                        //Tự động
                        $tranid = $userTransaction->id.time() . rand(10000, 99999);  /// Cái này có thể mà mã order của bạn, nó là duy nhất (enique) để phân biệt giao dịch.
                        //tao lệnh rút tiền
                        $order = Order::create([
                            'request_id'=> $tranid,
                            'ref_id' => $package->id,
                            'sticky' => $service->id,
                            'idkey'=>$service->idkey,
                            'gate_id'=>1,
                            'shop_id' => $request->shop_id,
                            'description'=> "Rút ".$amount." vật phẩm ".config('module.minigame.game_type.'.$type)." vào link server: #".$link_server,
                            'price'=>$amount,
                            'price_base' => $amount,//khách rút
                            'payment_type' => $type,
                            'title'=> $info_plus,
                            'author_id'=>$userTransaction->id,
                            'real_received_price'=> $old_item,
                            'params' => $customer_info,
                            'position' => $link_server,
                            'status'=> 1, // 'Đang chờ xử lý đối với roblox'
                            'module'=>config('module.minigame.module.withdraw-service-item')
                        ]);

                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.withdraw-service-workflow.key'),
                            'author_id' => $userTransaction->id,
                            'status' => 1,

                        ]);
                        //set tên công việc
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.withdraw-service-workname.key'),
                            'title' => number_format($amount) . " " . Helpers::DecodeJson("filter_name", $service->params),
                            'unit_price' => $amount,
                        ]);

                        //tạo tnxs vp
                        $txns = TxnsVp::create([
                            'trade_type' => config('module.txnsvp.trade_type.withdraw_item'),
                            'is_add' => '0',
                            'user_id' => $userTransaction->id,
                            'amount' => $amount,
                            'last_balance' => $balance_item_txns - $amount,
                            'description' => "Rút vật phẩm mã lệnh rút #".$order->id,
                            'ref_id' => $order->id,
                            'ip' => $request->getClientIp(),
                            'status' => 1,
                            'shop_id' => $request->shop_id,
                            'order_id' => $order->id,
                            'item_type' => $type
                        ]);

                        //check xem có đúng link mua server ko
                        $result=RobloxGate::detectUsernameRoblox($link_server);

                        if($result &&  $result->status==1){
                            $order->idkey=$service->idkey;
                            $order->save();

                            $roblox_order = Roblox_Order::create([
                                'order_id'=>$order->id,
                                'server'=>$result->user_id,
                                'uname'=>$link_server,
                                'money'=>$amount,
                                'phone'=>"",
                                'type_order'=>3,
                                'status'=>"chuanhan",
                                'shop_id' => $request->shop_id,
                            ]);
                        }
                        else{
                            DB::rollBack();
                            return response()->json([
                                'status' => 0,
                                'msg' => 'Link server roblox không hợp lệ.Vui lòng thử lại',
                            ]);

                        }

                        DB::commit();

                        //Gọi api bắn qua daily

                        $result = HelperItemDaily::fire($shop->daily_partner_id,$shop->daily_partner_key_service,$order->id,$server+1, $idgame, $info_plus,$amount,$service->idkey,1,$order->request_id);

                        if($result && isset($result->status)){
                            if($result->status == 2){

                                //set tiến độ
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'module' => config('module.withdraw-service-workname.key'),
                                    'status' => 1,
                                    'content' => "Đại lý đã tiếp nhận (Code:2)",
                                ]);

                                return response()->json([
                                    'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                    'status' => 1
                                ], 200);
                            }
                            if($result->status == 0){

                                $order->status=7;
                                $order->save();

                                //set tiến độ
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'module' => config('module.withdraw-service-workname.key'),
                                    'status' => 1,
                                    'content' => $result->message??"",
                                ]);


                                return response()->json([
                                    'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                    'status' => 1
                                ], 200);
                            }
                        }
                        else{

                            $order->status=9;
                            $order->save();
                            return response()->json([
                                'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                'status' => 1
                            ], 200);
                        }

                    }
                    elseif ($service->idkey == 'roblox_buygamepass_internal'){
                        $link_server = $idgame;
                        //Kiểm tra có đúng link hay không

                        $customer_info['server'] = $link_server;
                        //Tự động
                        $tranid = $userTransaction->id.time() . rand(10000, 99999);  /// Cái này có thể mà mã order của bạn, nó là duy nhất (enique) để phân biệt giao dịch.
                        //tao lệnh rút tiền
                        $order = Order::create([
                            'request_id'=> $tranid,
                            'ref_id' => $package->id,
                            'sticky' => $service->id,
                            'idkey'=>$service->idkey,
                            'gate_id'=>1,
                            'shop_id' => $request->shop_id,
                            'description'=> "Rút ".$amount." vật phẩm ".config('module.minigame.game_type.'.$type)." vào link server: #".$link_server,
                            'price'=>$amount,
                            'price_base' => $amount,//khách rút
                            'payment_type' => $type,
                            'title'=> $info_plus,
                            'author_id'=>$userTransaction->id,
                            'real_received_price'=> $old_item,
                            'params' => $customer_info,
                            'position' => $link_server,
                            'status'=> 1, // 'Đang chờ xử lý đối với roblox'
                            'module'=>config('module.minigame.module.withdraw-service-item')
                        ]);

                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.withdraw-service-workflow.key'),
                            'author_id' => $userTransaction->id,
                            'status' => 1,

                        ]);
                        //set tên công việc
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.withdraw-service-workname.key'),
                            'title' => number_format($amount) . " " . Helpers::DecodeJson("filter_name", $service->params),
                            'unit_price' => $amount,
                        ]);

                        //tạo tnxs vp
                        $txns = TxnsVp::create([
                            'trade_type' => config('module.txnsvp.trade_type.withdraw_item'),
                            'is_add' => '0',
                            'user_id' => $userTransaction->id,
                            'amount' => $amount,
                            'last_balance' => $balance_item_txns - $amount,
                            'description' => "Rút vật phẩm mã lệnh rút #".$order->id,
                            'ref_id' => $order->id,
                            'ip' => $request->getClientIp(),
                            'status' => 1,
                            'shop_id' => $request->shop_id,
                            'order_id' => $order->id,
                            'item_type' => $type
                        ]);

                        //check xem có đúng link mua server ko
                        $result=RobloxGate::detectUsernameRoblox($link_server);

                        if($result &&  $result->status==1){
                            $order->idkey=$service->idkey;
                            $order->save();
                            $roblox_order = Roblox_Order::create([
                                'order_id'=>$order->id,
                                'server'=>$result->user_id,
                                'uname'=>$link_server,
                                'money'=>$amount,
                                'phone'=>"",
                                'type_order'=>3,
                                'status'=>"chuanhan",
                                'shop_id' => $request->shop_id,
                            ]);

                            DB::commit();
                            $this->dispatch(new RobloxJob($order->id));
                            return response()->json([
                                'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                'status' => 1
                            ], 200);

                        }
                        else{
                            DB::rollBack();
                            return response()->json([
                                'status' => 0,
                                'message' => 'Link server roblox không hợp lệ.Vui lòng thử lại'
                            ]);

                        }
                    }
                    else{

                        $customer_info['server'] = $server;

                        //Tự động
                        $tranid = $userTransaction->id.time() . rand(10000, 99999);  /// Cái này có thể mà mã order của bạn, nó là duy nhất (enique) để phân biệt giao dịch.


                        //tao lệnh rút tiền
                        $order = Order::create([
                            'request_id'=> $tranid,
                            'ref_id' => $package->id,
                            'sticky' => $service->id,
                            'idkey'=>$service->idkey,
                            'gate_id'=>1,
                            'shop_id' => $request->shop_id,
                            'description'=> "Rút ".$amount." vật phẩm ".config('module.minigame.game_type.'.$type)." vào link server: #".$idgame,
                            'price'=>$amount,
                            'price_base' => $amount,//khách rút
                            'payment_type' => $type,
                            'title'=> $info_plus,
                            'author_id'=>$userTransaction->id,
                            'real_received_price'=> $old_item,
                            'params' => $idgame,
                            'position' => $server,
                            'status'=> 1, // 'Đang chờ xử lý đối với roblox'
                            'module'=>config('module.minigame.module.withdraw-service-item')
                        ]);

                        //tạo tnxs vp
                        $txns = TxnsVp::create([
                            'trade_type' => config('module.txnsvp.trade_type.withdraw_item'),
                            'is_add' => '0',
                            'user_id' => $userTransaction->id,
                            'amount' => $amount,
                            'last_balance' => $balance_item_txns - $amount,
                            'description' => "Rút vật phẩm mã lệnh rút #".$order->id,
                            'ref_id' => $order->id,
                            'ip' => $request->getClientIp(),
                            'status' => 1,
                            'shop_id' => $request->shop_id,
                            'order_id' => $order->id,
                            'item_type' => $type
                        ]);

                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.withdraw-service-workflow.key'),
                            'author_id' => $userTransaction->id,
                            'status' => 1,

                        ]);
                        //set tên công việc
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.withdraw-service-workname.key'),
                            'title' => number_format($amount) . " " . Helpers::DecodeJson("filter_name", $service->params),
                            'unit_price' => $amount,
                        ]);

                        if ($service->idkey == 'nrocoin') {// Vàng ngọc rồng

                            $order->idkey=$service->idkey;
                            $order->save();

                            $khachhang = KhachHang::create([
                                'server' => $server + 1,
                                'order_id' => $order->id,
                                'uname' => $idgame,
                                'money' => $amount,
                                'status' => "chuanhan",
                            ]);

                            DB::commit();
                            //Gọi api bắn qua daily

                            $result = HelperItemDaily::fire($shop->daily_partner_id,$shop->daily_partner_key_service,$order->id,$server+1, $idgame, $info_plus,$amount,$service->idkey,1,$order->request_id);

                            if($result && isset($result->status)){
                                if($result->status == 2){

                                    //set tiến độ
                                    OrderDetail::create([
                                        'order_id' => $order->id,
                                        'module' => config('module.withdraw-service-workflow.key'),
                                        'status' => 1,
                                        'content' => "Đại lý đã tiếp nhận (Code:2)",
                                    ]);

                                    return response()->json([
                                        'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                        'status' => 1
                                    ], 200);
                                }

                                if($result->status == 0){

                                    $order->status=7;
                                    $order->save();

                                    //set tiến độ
                                    OrderDetail::create([
                                        'order_id' => $order->id,
                                        'module' => config('module.withdraw-service-workflow.key'),
                                        'status' => 1,
                                        'content' => $result->message??"",
                                    ]);


                                    return response()->json([
                                        'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                        'status' => 1
                                    ], 200);
                                }
                            }
                            else{
                                $order->status=9;
                                $order->save();

                                //set tiến độ
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'module' => config('module.withdraw-service-workflow.key'),
                                    'status' => 1,
                                    'content' => "Kết nối NCC thất bại,chuyển về thao tác thủ công",
                                ]);

                                return response()->json([
                                    'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                    'status' => 1
                                ], 200);
                            }

                        }
                        elseif ($service->idkey == 'nrocoin_internal'){//ngọc ngọc rồng
                            $order->idkey=$service->idkey;
                            $order->save();

                            $khachhang = KhachHang::create([
                                'server' => $server + 1,
                                'order_id' => $order->id,
                                'uname' => $idgame,
                                'money' => $amount,
                                'status' => "chuanhan",
                                'shop_id' => $request->shop_id,
                            ]);
// Commit the queries!
                            DB::commit();
                            return response()->json([
                                'status' => 1,
                                'msg' => __('Thực hiện thanh toán thành công'),
                            ]);

                        }
                        elseif ($service->idkey == 'nrogem'){//ngọc ngọc rồng

                            $random_gem = 0;
                            $random_gem = rand(1, 3);
                            $amount = $amount + $random_gem;

                            $total = $amount;
                            $total_old = $total;

                            //lưu thông tin bot ver để hiển thị cho dễ
                            $order->idkey=$service->idkey;
                            $order->save();

                            $nrogem_GiaoDich = Nrogem_GiaoDich::create([
                                'order_id' => $order->id,
                                'shop_id' => $request->shop_id,
                                'acc' => $idgame,
                                'pass' => $info_plus,
                                'server' =>  ($server + 1),
                                'gem' => $total,
                                'gem_base' => $total_old,
                                'gem_rand' => $random_gem,
                                'status' => "chualogin",
                            ]);

                            DB::commit();
                            //Gọi api bắn qua daily

                            $result = HelperItemDaily::fire($shop->daily_partner_id,$shop->daily_partner_key_service,$order->id,$server+1, $idgame, $info_plus,$total,$service->idkey,1,$order->request_id);

                            if($result && isset($result->status)){
                                if($result->status == 2){

                                    //set tiến độ
                                    OrderDetail::create([
                                        'order_id' => $order->id,
                                        'module' => config('module.withdraw-service-workflow.key'),
                                        'status' => 1,
                                        'content' => "Đại lý đã tiếp nhận (Code:2)",
                                    ]);

                                    return response()->json([
                                        'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                        'status' => 1
                                    ], 200);
                                }

                                if($result->status == 0){

                                    $order->status=7;
                                    $order->save();

                                    //set tiến độ
                                    OrderDetail::create([
                                        'order_id' => $order->id,
                                        'module' => config('module.withdraw-service-workflow.key'),
                                        'status' => 1,
                                        'content' => $result->message??"",
                                    ]);


                                    return response()->json([
                                        'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                        'status' => 1
                                    ], 200);
                                }
                            }
                            else{
                                $order->status=9;
                                $order->save();

                                //set tiến độ
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'module' => config('module.withdraw-service-workflow.key'),
                                    'status' => 1,
                                    'content' => "Kết nối NCC thất bại,chuyển về thao tác thủ công",
                                ]);

                                return response()->json([
                                    'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                    'status' => 1
                                ], 200);
                            }
                        }
                        elseif ($service->idkey == 'nrogem_internal'){//ngọc ngọc rồng

                            //lẩy random bot xử lý

                            $dataBot= Nrogem_AccBan::where('server', ($server + 1))
                                ->where(function($q){
                                    $q->orWhere('ver','!=','');
                                    $q->orWhereNotNull('ver');
                                })
                                ->where('status','on')
                                ->inRandomOrder()
                                ->first();

                            if(!$dataBot){
                                return response()->json([
                                    'msg' => __('Không có bot bán Ngọc hoạt động.Vui lòng thử lại'),
                                    'status' => 0
                                ], 200);

                            }

                            $random_gem = 0;
                            $random_gem = rand(1, 3);
                            $amount = $amount + $random_gem;

                            $total = $amount;
                            $total_old = $total;

                            //lưu thông tin bot ver để hiển thị cho dễ
                            $order->idkey=$service->idkey;
                            $order->acc_id=$dataBot->ver;
                            $order->save();

                            $nrogem_GiaoDich = Nrogem_GiaoDich::create([
                                'order_id' => $order->id,
                                'shop_id' => $request->shop_id,
                                'acc' => $idgame,
                                'pass' => $info_plus,
                                'server' =>  ($server + 1),
                                'gem' => $total,
                                'gem_base' => $total_old,
                                'gem_rand' => $random_gem,
                                'status' => "chualogin",
                                'ver'=>$dataBot->ver,
                            ]);

                            // Commit the queries!
                            DB::commit();
                            return response()->json([
                                'status' => 1,
                                'msg' => __('Thực hiện thanh toán thành công'),
                            ]);
                        }
                        elseif ($service->idkey == 'ninjaxu'){
                            $order->idkey=$service->idkey;
                            $order->save();

                            $ninjaxu_khachhang = NinjaXu_KhachHang::create([
                                'server' => $server + 1,
                                'order_id' => $order->id,
                                'uname' => $idgame,
                                'coin' => $amount,
                                'status' => "chuanhan",
                            ]);

                            DB::commit();
                            //Gọi api bắn qua daily

                            $result = HelperItemDaily::fire($shop->daily_partner_id,$shop->daily_partner_key_service,$order->id,$server+1, $idgame, $info_plus,$amount,$service->idkey,1,$order->request_id);

                            if($result && isset($result->status)){
                                if($result->status == 2){

                                    //set tiến độ
                                    OrderDetail::create([
                                        'order_id' => $order->id,
                                        'module' => config('module.withdraw-service-workflow.key'),
                                        'status' => 1,
                                        'content' => "Đại lý đã nhận đơn",
                                    ]);

                                    return response()->json([
                                        'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                        'status' => 1
                                    ], 200);

                                }

                                if($result->status == 0){

                                    $order->status=7;
                                    $order->save();

                                    //set tiến độ
                                    OrderDetail::create([
                                        'order_id' => $order->id,
                                        'module' => config('module.withdraw-service-workflow.key'),
                                        'status' => 1,
                                        'content' => $result->message??"",
                                    ]);


                                    return response()->json([
                                        'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                        'status' => 1
                                    ], 200);
                                }
                            }
                            else{
                                $order->status=9;
                                $order->save();

                                //set tiến độ
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'module' => config('module.withdraw-service-workflow.key'),
                                    'status' => 1,
                                    'content' => "Kết nối NCC thất bại,chuyển về thao tác thủ công",
                                ]);

                                return response()->json([
                                    'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                    'status' => 1
                                ], 200);

                            }
                        }
                        elseif ($service->idkey == 'ninjaxu_internal'){//ngọc ngọc rồng

                            $order->idkey=$service->idkey;
                            $order->save();

                            $ninjaxu_khachhang = NinjaXu_KhachHang::create([
                                'server' => $server + 1,
                                'order_id' => $order->id,
                                'uname' => $idgame,
                                'coin' => $amount,
                                'status' => "chuanhan",
                                'shop_id' => $request->shop_id,
                            ]);

                            // Commit the queries!
                            DB::commit();
                            return response()->json([
                                'status' => 1,
                                'msg' => __('Thực hiện thanh toán thành công'),
                            ]);

                        }
                        else{
                            DB::rollback();
                            return response()->json([
                                'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thất bại, dịch vụ không tồn tại!'),
                                'status' => 0
                            ], 200);
                        }
                    }

                }else{

                    DB::rollback();
                    return response()->json([
                        'msg' => __('Có lỗi phát sinh.Xin vui lòng thử lại !'),
                        'status' => 0
                    ], 200);

                }
            }
            else{

                if($gametype->target == 1){

                    if ($provider == 'lienquan' || $provider == 'freefire' || $provider == 'lienminh' || $provider == 'bns' ||
                        $provider == 'ads' || $provider == 'fo4m' || $provider == 'fo4' || $provider == 'pubgm' || $provider == 'codm'){

//Tự động
                        $tranid = $userTransaction->id.time() . rand(10000, 99999);  /// Cái này có thể mà mã order của bạn, nó là duy nhất (enique) để phân biệt giao dịch.
                        //tao lệnh rút tiền
                        $order = Order::create([
                            'request_id'=> $tranid,
                            'ref_id' => $package->id,
                            'sticky' => $package_sticky,
                            'idkey'=>$idgame,
                            'shop_id' => $request->shop_id,
                            'description'=> "Rút ".$amount." vật phẩm ".config('module.minigame.game_type.'.$type)." vào ID: #".$idgame,
                            'price'=>$amount,
                            'price_base' => $package->price_old,
                            'payment_type' => $type,
                            'title'=> $info_plus,
                            'author_id'=>$userTransaction->id,
                            'real_received_price'=> $old_item,
                            'status'=> 0, // 'Đang chờ xử lý' config(module.minigame.withdraw-status)
                            'module'=>config('module.minigame.module.withdraw-item')
                        ]);

                        //tạo tnxs vp
                        $txns = TxnsVp::create([
                            'trade_type' => config('module.txnsvp.trade_type.withdraw_item'),
                            'is_add' => '0',
                            'user_id' => $userTransaction->id,
                            'amount' => $amount,
                            'last_balance' => $balance_item_txns - $amount,
                            'description' => "Rút vật phẩm mã lệnh rút #".$order->id,
                            'ref_id' => $order->id,
                            'ip' => $request->getClientIp(),
                            'status' => 1,
                            'shop_id' => $request->shop_id,
                            'order_id' => $order->id,
                            'item_type' => $type
                        ]);

                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.minigame.module.withdraw-item'),
                            'author_id' => $userTransaction->id,
                            'content' => "Đơn hàng chờ xác nhận",
                            'status' => 0,
                        ]);

                        DB::commit();

                        $result = HelpItemAdd::ITEMADD_CALLBACK($provider, $username, $password, $id, $item, "", $tranid, $request->shop_id,$payment_gateways);

                        if ($result &&  isset($result->status)) {
                            if($result->status==0){
                                // Update lại dữ liệu
                                $order->content = $result->message;
                                $order->paided_at = Carbon::now();
                                $order->save();

                                if (isset($result->user_balance)){
                                    if($result->user_balance<1000000){
                                        $message="[" . Carbon::now() . "] "."[" . $request->root() . "] " . $shop->domain . " đã mua bắn kim cương và tài khoản tichhop.net còn dưới 1 triệu (Số dư hiện tại: ".number_format($result->user_balance).")" ;
                                        Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_notify_balance_tichhop_net'));
                                    }
                                }

                                //set tiến độ
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'module' => config('module.minigame.module.withdraw-item'),
                                    'status' => 0,
                                    'content' => "NCC tích hợp đã nhận đơn",
                                ]);

                                // Commit the queries!
                                DB::commit();
                                return response()->json([
                                    'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                    'status' => 1
                                ], 200);
                            }
                            elseif ($result->status == 3){

                                if($result->status == -1){
                                    $message="[" . Carbon::now() . "] "."[" . $request->root() . "] " . $shop->domain . " đã bắn kim cương và tài khoản tichhop.net còn dưới 1 triệu (Số dư hiện tại: ".number_format($result->user_balance).")" ;
                                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_notify_balance_tichhop_net'));
                                    $message_response="Tài khoản đại lý không đủ số dư";
                                }
                                else{
                                    $message_response=$result->message??__('Kết nối với nhà cung cấp thất bại');
                                    $message="[" . Carbon::now() . "] "."[" . $request->root() . "] " . $shop->domain . " đã bắn kim cương trên tichhop.net kết nối thất bại:".$message_response." ";
                                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_notify_balance_tichhop_net'));
                                }

                                // Start transaction!
                                DB::beginTransaction();
                                try {

                                    $order = Order::lockForUpdate()->findOrFail($order->id);
                                    $order->status=7;
                                    $order->paided_at=Carbon::now();
                                    $order->save();

                                    //set tiến độ hủy
                                    OrderDetail::create([
                                        'order_id' => $order->id,
                                        'module' => config('module.minigame.module.withdraw-item'),
                                        'content' => $message_response,
                                        'status' => 7, //Đã hủy
                                    ]);

                                } catch (\Exception $e) {
                                    DB::rollback();
                                    Log::error( $e);
                                    return response()->json([
                                        'msg' => __('Có lỗi phát sinh.Xin vui lòng liên hệ admin để xử lý !'),
                                        'status' => 0
                                    ], 500);
                                }

                                DB::commit();
                                return response()->json([
                                    'msg' => $message_response,
                                    'status' => 0
                                ], 200);
                            }
                            else{

                                $order->status=7;
                                $order->paided_at=Carbon::now();
                                $order->save();

                                //set tiến độ hủy
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'module' => config('module.minigame.module.withdraw-item'),
                                    'content' => "Kết nối NCC thất bại (7)",
                                    'status' => 7, //Đã hủy
                                ]);

                                DB::commit();
                                return response()->json([
                                    'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                    'status' => 1
                                ], 200);
                            }
                        }
                        else{

                            $order->status=9;
                            $order->paided_at=Carbon::now();
                            $order->save();

                            //set tiến độ hủy
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.minigame.module.withdraw-item'),
                                'content' => "Kết nối NCC thất bại (9)",
                                'status' => 9, //Đã hủy
                            ]);

                            DB::commit();
                            return response()->json([
                                'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type'.$type).' thành công. hệ thống sẽ kiểm tra và phản hồi khi có kết quả!'),
                                'status' => 1
                            ], 200);
                        }

                        DB::commit();
                        return response()->json([
                            'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type.'.$type).' thành công!'),
                            'status' => 1
                        ], 200);
                    }else{
                        DB::rollback();
                        return response()->json([
                            'msg' => __('Kiểm tra lại cấu hình thông tin gói rút!'),
                            'status' => 0
                        ], 200);
                    }

                }
                 else{
                    //Thủ công
                    $order = Order::create([
                        'idkey'=>$idgame,
                        'ref_id' => $package->id,
                        'shop_id' => $request->shop_id,
                        'description'=> "Rút ".$amount." vật phẩm ".config('module.minigame.game_type.'.$type)." vào ID: #".$idgame,
                        'price'=>$amount,
                        'params'=>$params,
                        'price_base' => $package->price_old,
                        'payment_type' => $type,
                        'title'=> $info_plus,
                        'author_id'=>$userTransaction->id,
                        'real_received_price'=> $old_item,
                        'status'=>0, // 'Đang chờ xử lý' confit(module.minigame.withdraw-status)
                        'module'=>config('module.minigame.module.withdraw-item')
                    ]);

                     //tạo tnxs vp
                     $txns = TxnsVp::create([
                         'trade_type' => config('module.txnsvp.trade_type.withdraw_item'),
                         'is_add' => '0',
                         'user_id' => $userTransaction->id,
                         'amount' => $amount,
                         'last_balance' => $balance_item_txns - $amount,
                         'description' => "Rút vật phẩm mã lệnh rút #".$order->id,
                         'ref_id' => $order->id,
                         'ip' => $request->getClientIp(),
                         'status' => 1,
                         'shop_id' => $request->shop_id,
                         'order_id' => $order->id,
                         'item_type' => $type
                     ]);

                    DB::commit();
                    return response()->json([
                        'msg' => __('Tạo lệnh rút vật phẩm game '.config('module.minigame.game_type.'.$type).' thành công!'),
                        'status' => 1
                    ], 200);
                }

                //tạo tnxs vp
                $txns = TxnsVp::create([
                    'trade_type' => config('module.txnsvp.trade_type.withdraw_item'),
                    'is_add' => '0',
                    'user_id' => $userTransaction->id,
                    'amount' => $amount,
                    'last_balance' => $balance_item_txns - $amount,
                    'description' => "Rút vật phẩm mã lệnh rút #".$order->id,
                    'ref_id' => $order->id,
                    'ip' => $request->getClientIp(),
                    'status' => 1,
                    'shop_id' => $request->shop_id,
                    'order_id' => $order->id,
                    'item_type' => $type
                ]);
            }


        } catch (\Exception $e) {
            DB::rollback();
            logger($e);
            return response()->json([
                'msg' => __('Có lỗi phát sinh.Xin vui lòng thử lại !'),
                'status' => 0
            ], 200);
        }
    }

    public function getCallback(Request $request){
        DB::beginTransaction();
        try {
            //debug thì mở cái này
            $myfile = fopen(storage_path() ."/logs/log_itemadd-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().":".$request->fullUrl().json_encode($request->all());
            fwrite($myfile, $txt ."\n");
            fclose($myfile);

            $result_final = false;

            $status = $request->get('status');
            $message = $request->message;
            $request_id=$request->tranid;

            //check lệnh đã thực hiện
            $ordercheck = Order::where('request_id',$request->tranid)
                ->where('module', config('module.minigame.module.withdraw-item'))
                ->where(function($q){
                    $q->orWhere('status',1);
                    $q->orWhere('status',2);
                    $q->orWhere('status',3);
                    $q->orWhere('status',7);
                    $q->orWhere('status',9);
                })->first();

            if($ordercheck){
                return "Đã xử lý trước đó";
            }
            //tìm lệnh rút
            $order = Order::where('request_id',$request->tranid)
                ->where('module', config('module.minigame.module.withdraw-item'))
                ->where('status','!=',1)
                ->where('status','!=',2)
                ->where('status','!=',3)
                ->where('status','!=',7)
                ->where('status','!=',9)
                ->lockForUpdate()
                ->firstOrFail();

            $amount = $order->price;

            //tìm user nạp
            $userTransaction=User::where('id',$order->author_id)->lockForUpdate()->firstOrFail();

            if($request->get('status')==1){
                // Update lại trạng thái
                $order->status = 1;
                $order->content = "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!";
                $order->updated_at=Carbon::now();

//                Lưu giá trị gói.
                $order->price_input = $request->amount;

                $order->save();

                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.minigame.module.withdraw-item'),
                    'status' => 1,
                    'content' => "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!",

                ]);

                $result_final = true;
            }
            elseif ($request->get('status')==2){
                //trả lại vật phẩm cho user
                $userTransaction['ruby_num'.$order->payment_type] = $userTransaction['ruby_num'.$order->payment_type] + $order->price;
                $userTransaction->save();
                // Update lại trạng thái
                $order->status = 2;
                $order->content = "Giao dịch thất bại. Hệ thống đã hoàn tiền vào tài khoản, Bạn vui lòng mua lại!";
                $order->updated_at=Carbon::now();
                $order->price_input = $request->amount;

                $order->save();

                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.minigame.module.withdraw-item'),
                    'status' => 2,
                    'content' => "Giao dịch thất bại. ".$message
                ]);

                //tạo tnxs vp
                $txns = TxnsVp::create([
                    'trade_type' => config('module.txnsvp.trade_type.refund'),
                    'is_add' => '1',
                    'is_refund' => '1',
                    'user_id' => $userTransaction->id,
                    'amount' => $amount,
                    'last_balance' => $userTransaction['ruby_num'.$order->payment_type] + $amount,
                    'description' => "Trả lại vật phẩm rút không thành công mã lệnh #".$order->id,
                    'ref_id' => $order->id,
                    'ip' => $request->getClientIp(),
                    'status' => 1,
                    'shop_id' => $order->shop_id,
                    'order_id' => $order->id,
                    'item_type' => $order->payment_type
                ]);

                //set tiến độ hủy
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.minigame.module.withdraw-item'),
                    'content' => "Hoàn vật phẩm cho khách thành công",
                    'status' => 2, //Đã hủy
                ]);

                $result_final = true;
            }
            elseif ($request->get('status')==3){
                //trả lại vật phẩm cho user
                $userTransaction['ruby_num'.$order->payment_type] = $userTransaction['ruby_num'.$order->payment_type] + $order->price;
                $userTransaction->save();
                // Update lại trạng thái
                $order->status = 3;
                $order->content = "Giao dịch thất bại. Hệ thống đã hoàn tiền vào tài khoản, Bạn vui lòng mua lại!";
                $order->updated_at=Carbon::now();
                //                Lưu giá trị gói.
                $order->price_input = $request->amount;
                $order->save();

                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.minigame.module.withdraw-item'),
                    'status' => 3,
                    'content' => "Giao dịch thất bại. ".$message
                ]);

                //tạo tnxs vp
                $txns = TxnsVp::create([
                    'trade_type' => config('module.txnsvp.trade_type.withdraw_item'),
                    'is_add' => '1',
                    'is_refund' => '1',
                    'user_id' => $userTransaction->id,
                    'amount' => $amount,
                    'last_balance' => $userTransaction['ruby_num'.$order->payment_type] + $amount,
                    'description' => "Trả lại vật phẩm rút không thành công mã lệnh #".$order->id,
                    'ref_id' => $order->id,
                    'ip' => $request->getClientIp(),
                    'status' => 1,
                    'shop_id' => $order->shop_id,
                    'order_id' => $order->id,
                    'item_type' => $order->payment_type
                ]);

                //set tiến độ hủy
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.minigame.module.withdraw-item'),
                    'content' => "Hoàn vật phẩm cho khách thành công",
                    'status' => 3, //Đã hủy
                ]);

                $result_final = true;
            }

            if ($result_final === true) {
                DB::commit();
                return 'Xử lý giao dịch thành công #' . $request->tranid;
            } else {
                return '[Lỗi] Xử lý thất bại#' . $request->tranid;
            }

        }catch(\Exception $e)
        {
            DB::rollback();
            Log::error($e);
            return 'Có lỗi phát sinh.Xin vui lòng thử lại !';
        }
        // Commit the queries!
//
//        return 'Xử lý giao dịch thành công #'.$withdraw->id;
    }

    public function getBonus(Request $request){
        $shop_id = $request->shop_id;
        $dacong = 0;
        $dangnhap = 0;
        if(Auth::guard('api')->check()) {
            $dacong = Auth::guard('api')->user()->bonus_gift;
            $dangnhap = 1;
        }
        return response()->json([
            'icon' => Setting::getSettingShop('bonus_icon',null,$shop_id),
            'status' => Setting::getSettingShop('bonus_status',null,$shop_id),
            'dacong' => $dacong,
            'giatritu' => Setting::getSettingShop('bonus_item_from',null,$shop_id),
            'giatriden' => Setting::getSettingShop('bonus_item_to',null,$shop_id),
            'contenttruoc' => Setting::getSettingShop('bonus_content',null,$shop_id),
            'contentsau' => Setting::getSettingShop('bonus_content_after',null,$shop_id),
            'dangnhap' => $dangnhap
        ], 200);
    }

    public function postBonus(Request $request)
    {
        if(!Auth::guard('api')->check()) {
            return response()->json([
                'msg' => __("Vui lòng đăng nhập"),
                'status' => 2
            ], 200);
        }
        $typedv = "";
        $status = 0;
        $random_money = 0;
        $shop_id = $request->shop_id;
        if(Setting::getSettingShop('bonus_item_from',null,$shop_id) > 0 && Setting::getSettingShop('bonus_item_to',null,$shop_id) > 0)
        {
            if(Setting::getSettingShop('bonus_item_from',null,$shop_id) > Setting::getSettingShop('bonus_item_to',null,$shop_id))
            {
                $random_money = mt_rand(Setting::getSettingShop('bonus_item_to',null,$shop_id),Setting::getSettingShop('bonus_item_from',null,$shop_id));
            }
            else{
                $random_money = mt_rand(Setting::getSettingShop('bonus_item_from',null,$shop_id),Setting::getSettingShop('bonus_item_to',null,$shop_id));
            }
        }
        $slug= '';
        $user = Auth::guard('api')->user();
        if($user->bonus_gift == '' || $user->bonus_gift == 0){
            if($user->bonus_gift == ''){
                //Check xem cộng tiền hay cộng vật phẩm
                if(Setting::getSettingShop('bonus_type',null,$shop_id) == 0)
                {//Cộng tiền
                    $user->balance = $user->balance + $random_money;
                    $user->balance_in = $user->balance_in + $random_money;
                    $user->bonus_gift = $random_money;
                    $user->save();
                    $typedv = " đ";
                    $last_balance = $user->balance;
                    $txns = Txns::create([
                        'trade_type' => 'plus_money',
                        'is_add' => '1',
                        'user_id' =>  $user->id,
                        'amount' => $random_money,
                        'last_balance' => $last_balance,
                        'description' =>  "Thưởng tiền đăng nhập",
                        'txnsable_type' =>  null,
                        'ip' => $request->getClientIp(),
                        'status' => 1,
                        'shop_id' =>  $user->shop_id,
                    ]);
                }else if(Setting::getSettingShop('bonus_type',null,$shop_id) == 99){
                    $user->free_wheel = 1;
                    $user->free_wheel_type = Setting::getSettingShop('bonus_game_id',null,$shop_id);
                    $user->bonus_gift = 1;
                    $user->save();
                    $typedv = 'lượt chơi';
                    $group = Group::select('slug')->where('id', Setting::getSettingShop('bonus_game_id',null,$shop_id))->where('status', 1)->first();
                    $slug = isset($group)?'/minigame-'.$group->slug:'';
                }else if(Setting::getSettingShop('bonus_type',null,$shop_id) == 11){
                    $type_vp = Setting::getSettingShop('bonus_type',null,$shop_id);
                    $user['xu_num'] = $user['xu_num'] + $random_money;
                    $user->bonus_gift = $random_money;
                    $user->save();
                    $name_item = Item::where('module', config('module.minigame.module.gametype'))->where('parent_id', $type_vp)->first();
                    $typedv = $name_item->image;
                    $slug = '/withdrawitem-'.$type_vp;
                    $last_balance_vp = $user['xu_num'];
                    $txns = TxnsVp::create([
                        'trade_type' => 'plus_vp',
                        'is_add' => '1',
                        'user_id' =>  $user->id,
                        'amount' => $random_money,
                        'last_balance' => $last_balance_vp,
                        'description' =>  "Thưởng vật phẩm đăng nhập",
                        'txnsable_type' =>  null,
                        'ip' => $request->getClientIp(),
                        'status' => 1,
                        'shop_id' =>  $user->shop_id,
                        'item_type' =>  11
                    ]);
                }else if(Setting::getSettingShop('bonus_type',null,$shop_id) == 12){
                    $type_vp = Setting::getSettingShop('bonus_type',null,$shop_id);
                    $user['gem_num'] = $user['gem_num'] + $random_money;
                    $user->bonus_gift = $random_money;
                    $user->save();
                    $name_item = Item::where('module', config('module.minigame.module.gametype'))->where('parent_id', $type_vp)->first();
                    $typedv = $name_item->image;
                    $slug = '/withdrawitem-'.$type_vp;
                    $last_balance_vp = $user['gem_num'];
                    $txns = TxnsVp::create([
                        'trade_type' => 'plus_vp',
                        'is_add' => '1',
                        'user_id' =>  $user->id,
                        'amount' => $random_money,
                        'last_balance' => $last_balance_vp,
                        'description' =>  "Thưởng vật phẩm đăng nhập",
                        'txnsable_type' =>  null,
                        'ip' => $request->getClientIp(),
                        'status' => 1,
                        'shop_id' =>  $user->shop_id,
                        'item_type' =>  12
                    ]);
                }else if(Setting::getSettingShop('bonus_type',null,$shop_id) == 13){
                    $type_vp = Setting::getSettingShop('bonus_type',null,$shop_id);
                    $user['robux_num'] = $user['robux_num'] + $random_money;
                    $user->bonus_gift = $random_money;
                    $user->save();
                    $name_item = Item::where('module', config('module.minigame.module.gametype'))->where('parent_id', $type_vp)->first();
                    $typedv = $name_item->image;
                    $slug = '/withdrawitem-'.$type_vp;
                    $last_balance_vp = $user['robux_num'];
                    $txns = TxnsVp::create([
                        'trade_type' => 'plus_vp',
                        'is_add' => '1',
                        'user_id' =>  $user->id,
                        'amount' => $random_money,
                        'last_balance' => $last_balance_vp,
                        'description' =>  "Thưởng vật phẩm đăng nhập",
                        'txnsable_type' =>  null,
                        'ip' => $request->getClientIp(),
                        'status' => 1,
                        'shop_id' =>  $user->shop_id,
                        'item_type' =>  13
                    ]);
                }else if(Setting::getSettingShop('bonus_type',null,$shop_id) == 14){
                    $type_vp = Setting::getSettingShop('bonus_type',null,$shop_id);
                    $user['coin_num'] = $user['coin_num'] + $random_money;
                    $user->bonus_gift = $random_money;
                    $user->save();
                    $name_item = Item::where('module', config('module.minigame.module.gametype'))->where('parent_id', $type_vp)->first();
                    $typedv = $name_item->image;
                    $slug = '/withdrawitem-'.$type_vp;
                    $last_balance_vp = $user['coin_num'];
                    $txns = TxnsVp::create([
                        'trade_type' => 'plus_vp',
                        'is_add' => '1',
                        'user_id' =>  $user->id,
                        'amount' => $random_money,
                        'last_balance' => $last_balance_vp,
                        'description' =>  "Thưởng vật phẩm đăng nhập",
                        'txnsable_type' =>  null,
                        'ip' => $request->getClientIp(),
                        'status' => 1,
                        'shop_id' =>  $user->shop_id,
                        'item_type' =>  14
                    ]);
                }
                else
                {//Cộng vật phẩm
                    //Check xem cộng vào vật phẩm cộng dồn nào
                    $type_vp = Setting::getSettingShop('bonus_type',null,$shop_id);
                    $name_item = Item::where('module', config('module.minigame.module.gametype'))->where('parent_id', $type_vp)->first();
                    $user['ruby_num'.$type_vp] = $user['ruby_num'.$type_vp] + $random_money;
                    $user->bonus_gift = $random_money;
                    $user->save();
                    $typedv = $name_item->image;
                    $slug = '/withdrawitem-'.$type_vp;
                    $last_balance_vp = $user['ruby_num'.$type_vp];
                    $txns = TxnsVp::create([
                        'trade_type' => 'plus_vp',
                        'is_add' => '1',
                        'user_id' =>  $user->id,
                        'amount' => $random_money,
                        'last_balance' => $last_balance_vp,
                        'description' =>  "Thưởng vật phẩm đăng nhập",
                        'txnsable_type' =>  null,
                        'ip' => $request->getClientIp(),
                        'status' => 1,
                        'shop_id' =>  $user->shop_id,
                        'item_type' =>  $type_vp
                    ]);
                }
            }
        }
        else{
            $status = 1;
        }
        return response()->json([
            'type' => Setting::getSettingShop('bonus_type',null,$shop_id),
            'msg' => (Setting::getSettingShop('bonus_type',null,$shop_id) == 99?1:number_format($random_money))." ".$typedv,
            'status' => $status,
            'slug' => $slug
        ], 200);
    }
}
