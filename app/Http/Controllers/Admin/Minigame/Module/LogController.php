<?php

namespace App\Http\Controllers\Admin\Minigame\Module;

use App\Exports\ExportData;
use App\Http\Controllers\Controller;
use App\Library\HelperPermisionShopMinigame;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Item;
use App\Models\Order;
use Auth;
use Carbon\Carbon;
use DB;
use Excel;
use Html;
use Illuminate\Http\Request;


class LogController extends Controller
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

        ini_set('max_execution_time', 2400); //20 minutes

        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);

        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

//        $datatable = Order::with('author')->whereIn('shop_id',$arr_permission)
//            ->with('group')
//            ->with('shop')
//            ->with('item_ref')
//            ->whereNull('acc_id')->where('module', $this->module)->limit(50)->get();
//
//        return $datatable;

        if($request->ajax) {

            $datatable= Order::with('author')
                ->whereIn('shop_id',$arr_permission)
                ->with('group')
                ->with('shop')
                ->with('item_ref')
            ->whereNull('acc_id')->where('module', $this->module);

            if (session('shop_id')) {
                $datatable->where('shop_id',session('shop_id'));
            }
//            dd($request->get('group_id'));
            if ($request->filled('group_id')) {

                $datatable->where('gate_id',$request->get('group_id'));
            }

            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', $request->get('id'));
                    $q->orWhere('author_id',$request->get('id') );
                });
            }


            if ($request->filled('title'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
                });
            }

            if ($request->filled('payment_type'))  {
//                dd($request->get('payment_type'));
//                $datatable->WhereHas('group', function ($querysub) use($request){
//                    $querysub->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') = ".$request->get('payment_type'));
//                });
                $datatable->with(array('group' => function ($queryuse) use($request){
                    $queryuse->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') = ".$request->get('payment_type'));
                }))->WhereHas('group', function ($querysub) use($request){
                    $querysub->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') = ".$request->get('payment_type'));
                });
            }


            if(
                $request->filled('started_at') ||
                $request->filled('ended_at')
            ){
                if ($request->filled('started_at')) {
                    $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
                }
                if ($request->filled('ended_at')) {
                    $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
                }

            }else{

                $datatable->whereMonth('created_at', Carbon::now()->month);

            }
            $datatableTotal=$datatable->clone();

            return \datatables()->eloquent($datatable)

                ->only([
                    'id',
                    'gate_id',
                    'locale',
                    'author_id',
                    'order',
                    'ref_id',
                    'price',
                    'shop',
                    'shop_id',
                    'author',
                    'group',
                    'item_ref',
                    'real_received_price',
                    'value_gif_bonus',
                    'created_at'
                ])
                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->with('totalSumary', function() use ($datatableTotal) {
                    return $datatableTotal=$datatableTotal->first([
                        DB::raw('COUNT(order.id) as total_record'),
                        DB::raw('SUM(order.price) as total_price'),
                        DB::raw('SUM(order.value_gif_bonus) as total_value_gif_bonus'),
                        DB::raw('SUM(order.real_received_price) as total_real_received_price'),
                    ]);
                })
                ->toJson();
        }

        $dataCategory = Group::select('id','title','price','params','module','slug','image','image_icon','seo_title','position','description','seo_description')
            ->where('module', 'minigame-category')
            ->where('status', 1)
            ->with('customs', function ($query) use ($arr_permission) {
                $query->whereIn('shop_id', $arr_permission)->orderBy('order');
            })
            ->whereHas('customs', function ($query) use ($arr_permission) {
                $query->whereIn('shop_id', $arr_permission)
                    ->where('status', 1)
                    ->orderBy('order');
            })->orderBy('order')
            ->get();

        $payment_type = Item::where('module', 'gametype')->orderBy('order')->get();

        return view('admin.minigame.module.log.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('payment_type', $payment_type)
            ->with('dataCategory', $dataCategory);
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

        $datatable= Order::with('author')
            ->whereIn('shop_id',$arr_permission)->with('group')
            ->with('item_ref')
            ->with('shop')
            ->whereNull('acc_id')->where('module', 'minigame-log');

        if (session('shop_id')) {
            $datatable->where('shop_id',session('shop_id'));
        }

        if ($request->filled('group_id')) {
            $datatable->where('gate_id',$request->get('group_id'));
        }

        if ($request->filled('id'))  {
            $datatable->where(function($q) use($request){
                $q->orWhere('id', $request->get('id'));
                $q->orWhere('author_id',$request->get('id') );
            });
        }

        if ($request->filled('title'))  {
            $datatable->where(function($q) use($request){
                $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
            });
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

//        return view('admin.minigame.module.log.export_excel')->with('data',$data);
        return Excel::download(new ExportData($data,view('admin.minigame.module.log.export_excel')), 'Thống kê biến động số dư trùng vật phẩm ' . time() . '.xlsx');
    }

}
