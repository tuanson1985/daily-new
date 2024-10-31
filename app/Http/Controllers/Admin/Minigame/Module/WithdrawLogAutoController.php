<?php

namespace App\Http\Controllers\Admin\Minigame\Module;

use App\Exports\ExportData;
use App\Http\Controllers\Controller;
use App\Library\HelperPermisionShopMinigame;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Item;
use App\Models\Order;
use App\Models\TxnsVp;
use App\Models\User;
use Carbon\Carbon;
use Excel;
use Html;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class WithdrawLogAutoController extends Controller
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
        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);

        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        if($request->ajax) {

            $datatable= Order::with('author')->with('shop')
                ->with('itemconfig_minigame')->whereIn('shop_id',$arr_permission)
                ->where(function($q) use($request){
                    $q->orWhere('order.module', 'withdraw-service-item');
                });

            if (session('shop_id')) {
                $datatable->where('shop_id',session('shop_id'));
            }

            if ($request->filled('payment_type')) {
                $datatable->where('payment_type',$request->get('payment_type'));
            }

            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', $request->get('id'));
                    $q->orWhere('tranid', $request->get('id'));
                });
            }

            if ($request->filled('title'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('idkey', 'LIKE', '%' . $request->get('title') . '%');
                    $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
                });
            }

            if ($request->filled('status')) {
                $datatable->where('status',$request->get('status') );
            }

            if ($request->filled('started_at')) {
                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }

            if ($request->filled('started_updated_at')) {
                $datatable->where('updated_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_updated_at')));
            }
            if ($request->filled('ended_updated_at')) {
                $datatable->where('updated_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_updated_at')));
            }

            $datatableTotal=$datatable->clone();

            $user_item = User::query();
            if ($request->filled('payment_type'))
            {
                $payment_type = $request->get('payment_type');
                if ($payment_type == 11)
                {
                    $user_item = $user_item->selectRaw('sum(COALESCE(xu_num,0)) total_item');
                }
                elseif ($payment_type == 12)
                {
                    $user_item = $user_item->selectRaw('sum(COALESCE(gem_num,0)) total_item');
                }
                elseif ($payment_type == 13)
                {
                    $user_item = $user_item->selectRaw('sum(COALESCE(robux_num,0)) total_item');
                }
                elseif ($payment_type == 14)
                {
                    $user_item = $user_item->selectRaw('sum(COALESCE(coin_num,0)) total_item');
                }
                else{
                    $user_item = $user_item->selectRaw('sum(COALESCE(ruby_num'.$payment_type.',0)) total_item');
                }
            }
            else{
                $user_item = $user_item->selectRaw('sum(COALESCE(ruby_num1,0) + COALESCE(ruby_num2,0) + COALESCE(ruby_num3,0) + COALESCE(ruby_num4,0) +
            COALESCE(ruby_num5,0) + COALESCE(ruby_num6,0) + COALESCE(ruby_num7,0) + COALESCE(ruby_num8,0) + COALESCE(ruby_num9,0)
             + COALESCE(ruby_num10,0) + COALESCE(robux_num,0) + COALESCE(xu_num,0) + COALESCE(gem_num,0) + COALESCE(coin_num,0)) total_item');
            }

            if(session('shop_id')) {
                $user_item = $user_item->where('shop_id', session('shop_id'));
            }

            $user_item  = $user_item->where('status', 1)->where('account_type', 2)->first();

            return \datatables()->eloquent($datatable)

                ->only([
                    'id',
                    'idkey',
                    'title',
                    'locale',
                    'params',
                    'module',
                    'author_id',
                    'price',
                    'price_base',
                    'price_input',
                    'author',
                    'item_ref',
                    'status',
                    'payment_type',
                    'request_id',
                    'shop',
                    'created_at',
                    'updated_at',
                    'action'
                ])
                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->addColumn('action', function($row) {
                    $temp = "<button type='button'  rel=\"".route('admin.withdraw-item-auto.shows',$row->id)."\" class=\" btn btn-outline-secondary load-modal \" title=\"Show\">Chi tiết</button>";
                    return $temp;
                })
                ->with('totalSumary', function() use ($datatableTotal,$user_item) {
                    return $datatableTotal=$datatableTotal->first([
                        DB::raw(''.$user_item->total_item.' as total_item'),
                        DB::raw('COUNT(order.id) as total_record'),
                        DB::raw('COUNT(CASE WHEN order.status = 4 THEN order.id ELSE NULL END) as total_record_complete'),
                        DB::raw('COUNT(CASE WHEN order.status = 1 THEN order.id ELSE NULL END) as total_record_wanning'),
                        DB::raw('COUNT(CASE WHEN order.status = 0 OR status = 3 OR status = 5 THEN order.id ELSE NULL END) as total_record_delete'),
                        DB::raw('COUNT(CASE WHEN order.status = 2 THEN order.id ELSE NULL END) as total_record_pendding2'),
                        DB::raw('COUNT(CASE WHEN order.status = 6 THEN order.id ELSE NULL END) as total_record_pendding6'),
                        DB::raw('COUNT(CASE WHEN order.status = 7 THEN order.id ELSE NULL END) as total_record_pendding7'),
                        DB::raw('COUNT(CASE WHEN order.status = 9 THEN order.id ELSE NULL END) as total_record_pendding9'),
                        DB::raw('COUNT(CASE WHEN order.status = 77 THEN order.id ELSE NULL END) as total_record_pendding77'),
                        DB::raw('COUNT(CASE WHEN order.status = 88 THEN order.id ELSE NULL END) as total_record_pendding88'),
                        DB::raw('SUM(order.price) as total_withdraw_item'),
                        DB::raw('SUM(CASE WHEN order.status = 1 THEN order.price ELSE 0 END) as total_withdraw_item_complete'),
                        DB::raw('SUM(CASE WHEN order.status = 2 OR status = 3 THEN order.price ELSE 0 END) as total_withdraw_item_delete'),
                        DB::raw('SUM(CASE WHEN order.status = 0 OR status = 7 OR status = 9 THEN order.price ELSE 0 END) as total_withdraw_item_pendding'),
                    ]);
                })
                ->toJson();
        }

        $listgametype = Item::where('module', config('module.minigame.module.gametype'))
            ->where('status', 1)->whereIn('parent_id',[11,12,13,14])->pluck('title','parent_id')->toArray();
        return view('admin.minigame.module.withdrawlog-auto.index')
            ->with('module', $this->module)
            ->with('listgametype', $listgametype)
            ->with('arr_permission', $arr_permission)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    public function changeStatus(Request $request)
    {

        $msg = "";
        $status = 'ERROR';
        $rubystatus = 1;
        $id=$request->id;

        $order = Order::where('id',$id)->first();

        if (!isset($order)){
            return response()->json([
                'success'=>false,
                'message'=>__('Đơn hàng không tồn tại.!'),
                'status'=>0
            ]);
        }

        if ($order->payment_type == 13 || $order->payment_type == 11 || $order->payment_type == 12 || $order->payment_type == 14){
            return response()->json([
                'success'=>false,
                'message'=>__('Dịch vụ không tồn tại.!'),
                'status'=>0
            ]);
        }

        DB::beginTransaction();
        try {

            $rubystatus = $request->status;

            DB::table('order')
                ->where('id', $id)
                ->update(['status' => $rubystatus, 'processor_id' => Auth::user()->id]);

            //nếu là hủy cộng lại ruby cho user
            if($rubystatus=="2"){

                if (!$request->filled('w_content')) {
                    return response()->json([
                        'success'=>false,
                        'message'=>__('Vui lòng nhập nội dung hủy%!'),
                        'status'=>0
                    ]);
                }

                $itemTran = Order::where('id',$id)->firstOrFail();

                $itemTran->content = $request->w_content;

                $itemTran->save();

                //lấy loại vật phẩm lưu khi tạo giao dịch
                $type = $itemTran->payment_type;
                $userid = $request->userid;
                $userTransaction = User::where('id',$userid)->lockForUpdate()->firstOrFail();
                $balance_item_txns = $userTransaction['ruby_num'.$type];
                $amount =  $itemTran->price;
                $userTransaction['ruby_num'.$type] = $userTransaction['ruby_num'.$type] + $itemTran->price;
                $userTransaction->save();

                $txns = TxnsVp::create([
                    'trade_type' => config('module.txnsvp.trade_type.refund'),
                    'is_add' => '1',
                    'user_id' => $userTransaction->id,
                    'amount' => $amount,
                    'last_balance' => $balance_item_txns + $amount,
                    'description' => "Hoàn ".$amount." rút vật phẩm thất bại gói rút" . $itemTran->ref_id ,
                    'ref_id' => $itemTran->id,
                    'status' => 1,
                    'shop_id' => $userTransaction->shop_id,
                    'order_id' => $itemTran->id,
                    'item_type' => $type
                ]);

            }
            $msg = $id;
            $status = 'OK';


        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            $status = 'ERROR';
            $msg = "Có lỗi phát sinh.Xin vui lòng thử lại !";
        }
        DB::commit();
        return response()->json(array('msg'=> $msg, 'status' => $status,'rubystatus' => $rubystatus), 200);
    }

    public function exportExcel(Request $request){

        ini_set('max_execution_time', 2400); //20 minutes

        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        ActivityLog::add($request, 'Xuất excel thống kê trúng thưởng minigame txnsvp-export');

        $datatable= Order::with('author')->with('shop')->with('item_ref')
            ->whereIn('shop_id',$arr_permission)
            ->where(function($q) use($request){
                $q->orWhere('order.module', 'withdraw-service-item');
            });

        if (session('shop_id')) {
            $datatable->where('shop_id',session('shop_id'));
        }

        if ($request->filled('payment_type')) {
            $datatable->where('payment_type',$request->get('payment_type'));
        }

//        if ($request->filled('module')) {
//            if ($request->get('module') == 1){
//                $datatable->where('module', 'withdraw-item');
//            }elseif ($request->get('module') == 2){
////                $datatable->where('module', 'withdraw-service-item');
//            }elseif ($request->get('module') == 3){
//                $datatable->where('module', 'withdraw-itemrefund');
//            }
//        }

        if ($request->filled('title'))  {
            $datatable->where(function($q) use($request){
                $q->orWhere('idkey', 'LIKE', '%' . $request->get('title') . '%');
                $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
            });
        }

        if ($request->filled('status')) {
            $datatable->where('status',$request->get('status') );
        }

        if ($request->filled('started_at')) {
            $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
        }
        if ($request->filled('ended_at')) {
            $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
        }

        $datatable = $datatable->get();
        $data = [
            'data' => $datatable,
        ];

        return Excel::download(new ExportData($data,view('admin.minigame.module.withdrawlog-auto.export_excel')), 'Thống kê biến động số dư rút vật phẩm ' . time() . '.xlsx');
    }

    public function showReportWithdrawItem(Request $request,$id){

        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        $datatable = Order::with('author')->with('shop')->with('item_ref')
            ->whereIn('shop_id',$arr_permission)
            ->where(function($q) use($request){
                $q->orWhere('order.module', 'withdraw-service-item');
            })->where('id',$id)->first();

        $gametype = Item::where('module', 'gametype')->where('parent_id', $datatable->payment_type)->first();

        return view('admin.minigame.module.withdrawlog-auto.show')->with('datatable',$datatable)->with('gametype',$gametype);
    }
}

