<?php

namespace App\Http\Controllers\Frontend\Service;

use App;
use App\Exports\ExportData;
use App\Http\Controllers\Controller;
use App\Library\Files;
use App\Library\HelperPermisionShopMinigame;
use App\Library\Helpers;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Conversation;
use App\Models\Inbox;
use App\Models\ItemConfig;
use App\Models\KhachHang;
use App\Models\LangLaCoin_KhachHang;
use App\Models\Nrogem_GiaoDich;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Roblox_Order;
use App\Models\ServiceAccess;
use App\Models\Group;
use App\Models\Group_Item;
use App\Models\Item;
use App\Models\Shop;
use App\Models\SubItem;
use App\Models\Txns;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use Validator;


class PurchaseController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;

    public function __construct()
    {

//        $this->middleware('permission:service-purchase-list', ['only' => ['index']]);


        $this->module="service-purchase";
        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('frontend.'.$this->module.'.index'),
                'title' => __('Danh sách yêu cầu dịch vụ thủ công')
            ];
        }

    }

    public function index(Request $request)
    {

        if ($request->ajax()|| $request->export_excel==1) {

            $datatable = Order::with('item_ref','author','processor')
            ->where('order.module', config('module.service-purchase'))
            ->where('author_id',auth('frontend')->user()->id)
            ->whereNull('type_version')
              //lấy điều kiện đơn bt
            ->where(DB::raw('COALESCE(order.gate_id,0)'), '<>', 1 );

            //if ($request->filled('parrent_id')) {
            //    $datatable->where('parrent_id', $request->get('parrent_id'));
            //}
            if ($request->filled('id')) {
                $datatable->where(function($q) use ($request) {
                    $q->orWhere('id',$request->get('id'));
                    $q->orWhere('request_id',$request->get('id'));
                });
            }
            if ($request->filled('title')) {
                $datatable->where('title', 'LIKE', '%' . $request->get('title') . '%');
            }

            if ($request->filled('author')) {

                $datatable->whereHas('author', function ($query) use ($request) {
                    $query->where('username',$request->get('author'));
                });
            }

            if ($request->filled('processor')) {

                $datatable->whereHas('processor', function ($query) use ($request) {
                    $query->where('username',$request->get('processor'));
                });
            }

            if ($request->filled('status')) {
                $datatable->where('status',$request->get('status'));
            }


            if ($request->filled('started_at')) {


                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }


            if(

                $request->filled('id') ||
                $request->filled('group_id') ||
                $request->filled('title') ||
                $request->filled('status') ||
                $request->filled('author') ||
                $request->filled('processor')||
                $request->filled('started_at') ||
                $request->filled('ended_at')||
                $request->filled('finished_started_at')||
                $request->filled('finished_ended_at')
            ){


                if ($request->filled('finished_started_at')) {

                    $datatable->where('updated_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_started_at')));
                }
                if ($request->filled('finished_ended_at')) {
                    $datatable->where('updated_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_ended_at')));
                }
            }else{

                $datatable->whereMonth('updated_at', Carbon::now()->month);

            }

            //nếu user ko full quyền nhận các dịch vụ thì lấy các id dịch vụ được cấp quyền

            $datatableTotal=$datatable->clone();

            //xuất excel
            if($request->export_excel==1){
                $datatable= $datatable->orderBy('id','desc')->select('order.*')->get();
                $data = [
                    'data' => $datatable,
                ];
                return Excel::download(new ExportData($data,view('frontend.service.purchase.excel')), 'Thống kê dịch vụ thủ công_ ' . time() . '.xlsx');
            }
            else{
                $datatable= $datatable->select('order.*');
            }

            return \datatables()->eloquent($datatable)


                ->orderColumn('server', function ($query, $order) {
                    $query->orderByRaw('CAST(JSON_EXTRACT(params, "$.server") AS NCHAR) '.$order);
                })

                ->editColumn('server', function ($row) {
                    $server=$row->params->server??"";
                    return $server;
                })
                ->editColumn('params', function ($row) {
                    return "";
                })
                ->editColumn('author', function($row) {
                    $temp = '';
                    $temp .= $row->author->username??"";
                    return $temp;
                })
                ->editColumn('price', function ($row) {
                    return  number_format($row->price)??"";

                })

                ->editColumn('processor', function ($row) {

                    $tempProcessor="";


                    $tempProcessor=$row->processor->username??"";

                    $temp = '';
                    if( auth()->user()->can('view-profile') && $tempProcessor!="" ){

                        $temp .= "<a href=\"#\"  class=\"load-modal\" rel=\"".route('frontend.view-profile',["username" => ($tempProcessor)])."\">".($tempProcessor)."</a>";
                    }
                    else{
                        $temp .= $tempProcessor;
                    }
                    return $temp;


                })

                ->editColumn('price', function ($row) {
                    $price=$row->price;
                    return $price;
                })
                ->editColumn('price_ctv', function ($row) {
                    $price_ctv=$row->price_ctv;
                    return $price_ctv;
                })


                ->editColumn('profit', function ($row) {
                    $price=$row->price;
                    $real_received_price_ctv=$row->real_received_price_ctv;
                    return intval($price)-intval($real_received_price_ctv);
                })
                ->editColumn('created_at', function ($row) {
                    return date('d/m/Y H:i:s', strtotime($row->created_at));
                })
                ->editColumn('updated_at', function ($row) {
                    if ($row->status == 4){
                        return date('d/m/Y H:i:s', strtotime($row->updated_at));
                    }elseif ($row->status == 10 || $row->status == 11){
                        return date('d/m/Y H:i:s', strtotime($row->process_at));
                    }
                    else{
                        return date('d/m/Y H:i:s', strtotime($row->updated_at));
                    }

                })
                ->editColumn('ratio', function ($row) {

                    //nếu là đơn hoàn thành rồi
                    if($row->status!=4){
                        $ratio = 80;
                        $author_id=$row->processor_id??auth('frontend')->user()->id;
                        $service_access1 = ServiceAccess::where('user_id', $author_id)->first();
                        $param1 = json_decode(isset($service_access1->params) ? $service_access1->params : "");
                        $ratio = isset($param1->{'ratio_' . ($row->item_ref->id??null)}) ? $param1->{'ratio_' . ($row->item_ref->id??null)??null} : $ratio;
                    }
                    else{
                        $ratio = $row->ratio_ctv;
                    }

                    $ratio=floor(floatval($ratio) *10)/10;

                    return $ratio;

                })

                ->addColumn('action', function ($row) {
                    if ($row->type_version == 2){
                        return '';
                    }
                    $temp = '';

                    $temp .= "<a href=\"" . route('frontend.service-purchase.show', $row->id) . "\"  rel=\"$row->id\" class=\"m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill \" title=\"Xem\"><i class=\"la la-eye\"></i></a>";
                    return $temp;
                })

                ->with('totalSumary', function() use ($datatableTotal) {
                    return $datatableTotal=$datatableTotal->first([
                        DB::raw('COUNT(order.id) as total_record'),
                        DB::raw('SUM(order.price) as total_price'),
                        DB::raw('SUM(order.real_received_price_ctv ) as total_real_received_price_ctv'),
                        DB::raw('SUM(order.price - order.real_received_price_ctv ) as total_profit'),
                    ]);
                })
                ->toJson();

        }


        $dataCategory = Item::where('module', '=', config('module.service.key'))->where('status','1')
            ->orderBy('title', 'asc');

        $dataCategory=$dataCategory->get();

        return view('frontend.service.purchase.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('dataCategory', $dataCategory);

    }


    /**
     * Show the form for creating a new newscategory
     *
     * @return Response
     */
    public function create(Request $request)
    {


    }

    /**
     * Store a newly created newscategory in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {


    }

    /**
     * Display the specified newscategory.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {

        $datatable = Order::with('author','processor','order_refund')
            ->where('author_id',auth('frontend')->user()->id)
            ->with(['item_ref'=>function($q){
                $q->with('groups');
            }])
            ->where('module', config('module.service-purchase'))
            ->whereNull('type_version')
            //lấy điều kiện đơn bt
            ->where(DB::raw('COALESCE(gate_id,0)'), '<>', 1 )->findOrFail($id);

        return view('frontend.service.purchase.show')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('datatable',$datatable);

    }

    /**
     * Show the form for editing the specified newscategory.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {


    }

    /**
     * Remove the specified newscategory from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {

    }


    public function getCount(Request $request)
    {

        $group_ids = [];

        if (!Auth::user()->can('service-reception-all')) {
            //lấy các quyền được xem yêu cầu dịch vụ
            $service_access = ServiceAccess::where('user_id', auth('frontend')->user()->id)
                ->first();

            $param = json_decode(isset($service_access->params) ? $service_access->params : "");
            $group_ids = isset($param->view_role) ? $param->view_role : [];
        }

        $datatable = Order::where('order.module', config('module.service-purchase.key'))
            ->where('status', 1) //đang chờ
            //lấy điều kiện đơn bt
            ->where(DB::raw('COALESCE(order.gate_id,0)'), '<>', 1 );

        if(session('shop_id')){
            $datatable = $datatable->where('shop_id',session('shop_id'));
        }


        //nếu user ko full quyền nhận các dịch vụ thì lấy các id dịch vụ được cấp quyền
        if (!Auth::user()->can('service-reception-all')) {

            //nếu có lọc dịch vụ thì chỉ chấp nhận lọc các dịch vụ cho phép
            if ($request->filled('group_id')) {

                $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
                    $groupAllowView = array_intersect($group_ids,(array)$request->get('group_id'));
                    $query->whereIn('id', $groupAllowView);
                });
            }
            //else lọc dịch vụ all các dịch vụ cho phép
            else{
                $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
                    $groupAllowView = array_intersect($group_ids,(array)$request->get('group_id'));
                    $query->whereIn('id', $group_ids);
                });
            }
        }
        //nếu user có full quyền nhận các dịch vụ thì lấy luôn id dịch vụ đó
        else{
            if ($request->filled('group_id')) {
                $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
                    $query->where('id',$request->get('group_id'));
                });
            }
        }
        $datatable=$datatable->count();
        return response()->json([
            'status'=>1,
            'data'=>$datatable
        ]);
    }

}
