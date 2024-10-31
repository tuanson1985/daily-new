<?php

namespace App\Http\Controllers\Admin\Minigame\Module;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Html;
use Illuminate\Http\Request;
use DB;
use App\Library\HelperPermisionShopMinigame;


class MinigameStatiticsController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;
    public function __construct(Request $request)
    {


        $this->module=$request->segments()[1]??"";
        $this->moduleCategory=explode("-", $this->module)[0].'-category';

        //set permission to function
        $this->middleware('permission:'. $this->module);

        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'.index'),
                'title' => __(config('module.minigame.'.$this->module.'.title'))
            ];
        }
    }

    public function index(Request $request)
    {
//        $current_query = request()->query();
//        if (!isset($current_query['started_at']) || !isset($current_query['ended_at'])) {
//            $url = url()->current().'?started_at='.\Carbon\Carbon::now()->startOfDay()->format('d/m/Y H:i:s').'&ended_at='.\Carbon\Carbon::now()->endOfDay()->format('d/m/Y H:i:s');
//            return redirect($url);
//        }

        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        ini_set('max_execution_time', 1200); //10 minutes

        ActivityLog::add($request, 'Truy cập thống kê '.$this->module);
        $all = [];

        $datatable_minigame = Group::query()
            ->select('id','title','position','status','shop_id','module')
            ->with(array('customs' => function ($query) {
                $query->select('id','title','shop_id','group_id');
                if(session('shop_id')) {
                    $query->where('shop_id', session('shop_id'));
                }
            }))
            ->WhereHas('customs', function ($querysub) use ($arr_permission){
                $querysub->select('id','title','shop_id','group_id');
                $querysub->whereIn('shop_id', $arr_permission);
                if(session('shop_id')){
                    $querysub->where('shop_id', session('shop_id'));
                }
            });

        $datatable_minigame = $datatable_minigame->where('position', 'rubywheel');

//        $datatable_minigame = $datatable_minigame->with(array('order_gate' => function ($query) {
//            $query->select('id','created_at','gate_id','real_received_price','value_gif_bonus','price');
//            $query->select('groups.*', DB::raw('SUM(order.price) as total_price'));
//            $query->leftJoin('order', 'groups.id', '=', 'order.gate_id');
//            $query->groupBy('gate_id');
//
//        }));
        $datatable_minigame = $datatable_minigame->withSum(['order_gate as total_price' => function ($query) {
            $query->whereDate('created_at', '=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->format('d/m/Y H:i:s')));
            if(session('shop_id')) {
                $query->where('shop_id', session('shop_id'));
            }
        }], 'price');

        $datatable_minigame = $datatable_minigame->withSum(['order_gate as total_value_gif_bonus' => function ($query) {
            $query->whereDate('created_at', '=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->format('d/m/Y H:i:s')));
            if(session('shop_id')) {
                $query->where('shop_id', session('shop_id'));
            }
        }], 'value_gif_bonus');

        $datatable_minigame = $datatable_minigame->withSum(['order_gate as total_real_received_price' => function ($query) {
            $query->whereDate('created_at', '=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->format('d/m/Y H:i:s')));
            if(session('shop_id')) {
                $query->where('shop_id', session('shop_id'));
            }
        }], 'real_received_price');

//        $datatable_minigame = $datatable_minigame->with(array('items' => function ($query) {
//            $query->with(array('minigameorder' => function ($querysub) {
//                $querysub->select('id','created_at','module');
//                $querysub->whereDate('created_at', '=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->format('d/m/Y H:i:s')));
//            }));
////            $query->WhereHas('minigameorder', function ($querysub){
////                $querysub->whereDate('created_at', '=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->format('d/m/Y H:i:s')));
////            });
//        }));
//        $totalPrice = $datatable_rubywheel->pluck('order_gate.total_price');
        $datatable_minigame = $datatable_minigame->where('status', 1)
            ->get();

        return $datatable_minigame;

        $datatable_rubywheel = [
            'id' => 1,
            'title' => config('module.txns.trade_type.rubywheel'),
            'data' => $datatable_rubywheel
        ];

        if($datatable_rubywheel['data']->count()>0) array_push($all, $datatable_rubywheel);
//        $datatable_flip = Group::where('position', 'flip')
//            ->with(array('customs' => function ($query) {
//                if(session('shop_id')) {
//                    $query->where('shop_id', session('shop_id'));
//                }
//            }))
//            ->WhereHas('customs', function ($querysub) use ($arr_permission){
//                $querysub->whereIn('shop_id', $arr_permission);
//                if(session('shop_id')){
//                    $querysub->where('shop_id', session('shop_id'));
//                }
//            })->where('status', 1)->get();
//        $datatable_flip = [
//            'id' => 2,
//            'title' => config('module.txns.trade_type.flip'),
//            'data' => $datatable_flip
//        ];
//        if($datatable_flip['data']->count()>0) array_push($all, $datatable_flip);
//        $datatable_squarewheel = Group::where('position', 'squarewheel')
//            ->with(array('customs' => function ($query) {
//                if(session('shop_id')) {
//                    $query->where('shop_id', session('shop_id'));
//                }
//            }))
//            ->WhereHas('customs', function ($querysub) use ($arr_permission){
//                $querysub->whereIn('shop_id', $arr_permission);
//                if(session('shop_id')){
//                    $querysub->where('shop_id', session('shop_id'));
//                }
//            })->where('status', 1)->get();
//        $datatable_squarewheel = [
//            'id' => 3,
//            'title' => config('module.txns.trade_type.squarewheel'),
//            'data' => $datatable_squarewheel
//        ];
//        if($datatable_squarewheel['data']->count()>0) array_push($all, $datatable_squarewheel);
//        $datatable_slotmachine = Group::where('position', 'slotmachine')
//            ->with(array('customs' => function ($query) {
//                if(session('shop_id')) {
//                    $query->where('shop_id', session('shop_id'));
//                }
//            }))
//            ->WhereHas('customs', function ($querysub) use ($arr_permission){
//                $querysub->whereIn('shop_id', $arr_permission);
//                if(session('shop_id')){
//                    $querysub->where('shop_id', session('shop_id'));
//                }
//            })->where('status', 1)->get();
//        $datatable_slotmachine = [
//            'id' => 4,
//            'title' => config('module.txns.trade_type.slotmachine'),
//            'data' => $datatable_slotmachine
//        ];
//        if($datatable_slotmachine['data']->count()>0) array_push($all, $datatable_slotmachine);
//        $datatable_slotmachine5 = Group::where('position', 'slotmachine5')
//            ->with(array('customs' => function ($query) {
//                if(session('shop_id')) {
//                    $query->where('shop_id', session('shop_id'));
//                }
//            }))
//            ->WhereHas('customs', function ($querysub) use ($arr_permission){
//                $querysub->whereIn('shop_id', $arr_permission);
//                if(session('shop_id')){
//                    $querysub->where('shop_id', session('shop_id'));
//                }
//            })->where('status', 1)->get();
//        $datatable_slotmachine5 = [
//            'id' => 5,
//            'title' => config('module.txns.trade_type.slotmachine5'),
//            'data' => $datatable_slotmachine5
//        ];
//        if($datatable_slotmachine5['data']->count()>0) array_push($all, $datatable_slotmachine5);
//        $datatable_smashwheel = Group::where('position', 'smashwheel')
//            ->with(array('customs' => function ($query) {
//                if(session('shop_id')) {
//                    $query->where('shop_id', session('shop_id'));
//                }
//            }))
//            ->WhereHas('customs', function ($querysub) use ($arr_permission){
//                $querysub->whereIn('shop_id', $arr_permission);
//                if(session('shop_id')){
//                    $querysub->where('shop_id', session('shop_id'));
//                }
//            })->where('status', 1)->get();
//        $datatable_smashwheel = [
//            'id' => 6,
//            'title' => config('module.txns.trade_type.smashwheel'),
//            'data' => $datatable_smashwheel
//        ];
//        if($datatable_smashwheel['data']->count()>0) array_push($all, $datatable_smashwheel);
//        $datatable_rungcay = Group::where('position', 'rungcay')
//            ->with(array('customs' => function ($query) {
//                if(session('shop_id')) {
//                    $query->where('shop_id', session('shop_id'));
//                }
//            }))
//            ->WhereHas('customs', function ($querysub) use ($arr_permission){
//                $querysub->whereIn('shop_id', $arr_permission);
//                if(session('shop_id')){
//                    $querysub->where('shop_id', session('shop_id'));
//                }
//            })->where('status', 1)->get();
//        $datatable_rungcay = [
//            'id' => 7,
//            'title' => config('module.txns.trade_type.rungcay'),
//            'data' => $datatable_rungcay
//        ];
//        if($datatable_rungcay['data']->count()>0) array_push($all, $datatable_rungcay);
//        $datatable_gieoque = Group::where('position', 'gieoque')
//            ->with(array('customs' => function ($query) {
//                if(session('shop_id')) {
//                    $query->where('shop_id', session('shop_id'));
//                }
//            }))
//            ->WhereHas('customs', function ($querysub) use ($arr_permission){
//                $querysub->whereIn('shop_id', $arr_permission);
//                if(session('shop_id')){
//                    $querysub->where('shop_id', session('shop_id'));
//                }
//            })->where('status', 1)->get();
//        $datatable_gieoque = [
//            'id' => 8,
//            'title' => config('module.txns.trade_type.gieoque'),
//            'data' => $datatable_gieoque
//        ];
//        if($datatable_gieoque['data']->count()>0) array_push($all, $datatable_gieoque);


//Rút vật phẩm đơn hoàn thành
//        $datatable_withdraw = Order::selectRaw('items.parent_id,items.title, count(*) as total_withdraw, sum(order.price) as price_withdraw')
//            ->leftJoin('items', 'items.parent_id','order.payment_type')
//            ->where('order.module', 'withdraw-item')
//            ->where('order.status', 1)
//            ->where('items.module','gametype')->where('items.status', 1);
//
//        if(session('shop_id')) {
//            $datatable_withdraw = $datatable_withdraw->where('order.shop_id', session('shop_id'));
//        }
//
//        $datatable_withdraw = $datatable_withdraw->groupBy('items.parent_id', 'items.title')->get();
//Rút vật phẩm vàng ngọc xu robox
/*        $datatable_withdraw_service = Order::selectRaw('items.parent_id,items.title, count(*) as total_withdraw, sum(order.price) as price_withdraw')
            ->leftJoin('items', 'items.parent_id','order.payment_type')
            ->where('order.module', 'withdraw-service-item')
            ->where('order.status', 4)
            ->where('items.module','gametype')->where('items.status', 1);
        if(session('shop_id')) {
            $datatable_withdraw_service = $datatable_withdraw_service->where('order.shop_id', session('shop_id'));
        }
        $datatable_withdraw_service = $datatable_withdraw_service->groupBy('items.parent_id', 'items.title')->get();*/
//đơn chờ rút vật phẩm
//        $datatable_pending_withdraw = Order::selectRaw('items.parent_id,items.title, count(*) as total_withdraw, sum(order.price) as price_withdraw')
//            ->leftJoin('items', 'items.parent_id','order.payment_type')
//            ->where('order.module', 'withdraw-item')
//            ->where('order.status', 0)
//            ->where('items.module','gametype')->where('items.status', 1);
//        if(session('shop_id')) {
//            $datatable_pending_withdraw = $datatable_pending_withdraw->where('order.shop_id', session('shop_id'));
//        }
//        $datatable_pending_withdraw  =  $datatable_pending_withdraw->groupBy('items.parent_id', 'items.title')->get();
//đơn chờ vàng ngọc xu
//        $datatable_pending_withdraw_service = Order::selectRaw('items.parent_id,items.title, count(*) as total_withdraw, sum(order.price) as price_withdraw')
//            ->leftJoin('items', 'items.parent_id','order.payment_type')
//            ->where('order.module', 'withdraw-service-item')
//            ->whereIn('order.status', [1,2,9])
//            ->where('items.module','gametype')->where('items.status', 1);
//        if(session('shop_id')) {
//            $datatable_pending_withdraw_service = $datatable_pending_withdraw_service->where('order.shop_id', session('shop_id'));
//        }
//        $datatable_pending_withdraw_service = $datatable_pending_withdraw_service->groupBy('items.parent_id', 'items.title')->get();


        $user_item = User::selectRaw('sum(ruby_num1) ruby_num1,sum(ruby_num2) ruby_num2,sum(ruby_num3) ruby_num3,sum(ruby_num4) ruby_num4,sum(ruby_num5) ruby_num5,sum(ruby_num6) ruby_num6,sum(ruby_num7) ruby_num7,sum(ruby_num8) ruby_num8,sum(ruby_num9) ruby_num9,sum(ruby_num10) ruby_num10,sum(robux_num) robux_num,sum(xu_num) xu_num,sum(gem_num) gem_num,sum(coin_num) coin_num')
            ->where('status', 1)->where('account_type', 2);
        if(session('shop_id')) {
            $user_item = $user_item->where('shop_id', session('shop_id'));
        }
        $user_item  = $user_item->first();
        $totalnickon = Item::where('status', 1)->where('module', 'minigame-acc')->count();

        return view('admin.minigame.module.minigamestatitics.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('all', $all)
//            ->with('datatable_withdraw', $datatable_withdraw)
//            ->with('datatable_withdraw_service', $datatable_withdraw_service)
//            ->with('datatable_pending_withdraw', $datatable_pending_withdraw)
//            ->with('datatable_pending_withdraw_service', $datatable_pending_withdraw_service)
            ->with('user_item', $user_item)
            ->with('totalnickon', $totalnickon);
    }
}
