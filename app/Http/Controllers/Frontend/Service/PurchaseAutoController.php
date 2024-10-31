<?php

namespace App\Http\Controllers\Frontend\Service;

use App;
use App\Exports\ExportData;
use App\Http\Controllers\Controller;
use App\Jobs\CallbackOrderRobloxBuyGemPet;
use App\Jobs\ServiceAuto\RobloxJob;
use App\Jobs\ServiceAuto\RobloxUserIdJob;
use App\Library\ChargeGameGateway\GarenaGate_Phap;
use App\Library\Files;
use App\Library\HelperItemDaily;
use App\Library\Helpers;
use App\Library\MediaHelpers;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Conversation;
use App\Models\Inbox;
use App\Models\ItemConfig;
use App\Models\KhachHang;
use App\Models\LangLaCoin_KhachHang;
use App\Models\NinjaXu_KhachHang;
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
use App\Models\TxnsVp;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use Validator;


class PurchaseAutoController extends Controller
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


//        $this->middleware('permission:service-purchase-auto-list', ['only' => ['index']]);
//        $this->middleware('permission:service-purchase-auto-show', ['only' => ['show']]);
//        $this->middleware('permission:service-purchase-auto-delete', ['only' => ['destroy']]);
//        $this->middleware('permission:service-purchase-auto-recharge', ['only' => ['recharge']]);


        $this->module="service-purchase-auto";
        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('frontend.'.$this->module.'.index'),
                'title' => __('Danh sách yêu cầu dịch vụ tự động')
            ];
        }

    }

    public function index(Request $request)
    {

        $group_ids = [];

        if ($request->ajax() || $request->export_excel==1) {

            $datatable =  Order::with('shop','item_ref','author','processor')
                ->where('module',config('module.service-purchase.key'))
                ->where('author_id',auth('frontend')->user()->id)
                //lấy điều kiện đơn bt
                ->where(DB::raw('COALESCE(order.gate_id,0)'),  1 );

            if ($request->filled('group_id')) {

                $datatable->whereHas('item_ref', function ($query) use ($request) {
                    $query->where('id',$request->get('group_id'));
                });
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

            if ($request->filled('id')) {
                $datatable->where(function($q) use ($request) {
                    $q->orWhere('id',$request->get('id'));
                    $q->orWhere('request_id_customer',$request->get('id'));
                });

            }

            if ($request->filled('title')) {
                $datatable->where('title', 'LIKE', '%' . $request->get('title') . '%');
            }

            if ($request->filled('status_nrogem')) {

                $datatable->where('idkey', '=', 'nrogem');

                $datatable->whereHas('item_rels', function ($query) use ($request) {
                    $query->where('status', 'LIKE', '%' . $request->get('status_nrogem') . '%');
                });
            }

            if ($request->filled('status')) {
                $datatable->where('status', $request->get('status'));
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

            $datatableTotal=$datatable->clone();

            //xuất excel
            if($request->export_excel==1){
                $datatable= $datatable->orderBy('id','desc')->select('order.*')->get();
                $data = [
                    'data' => $datatable,
                ];
                return Excel::download(new ExportData($data,view('admin.service.purchase-auto.excel')), 'Thống kê dịch vụ tự động_ ' . time() . '.xlsx');
            }
            else{
                $datatable= $datatable->select('order.*');
            }


            return \datatables()->eloquent($datatable)

                ->orderColumn('server', function ($query, $order) {
                    $query->orderByRaw('CAST(JSON_EXTRACT(params, "$.server") AS NCHAR) '.$order);
                })

                ->editColumn('server', function ($row) {
                    $html = '';

                    if ($row->params){
                        $params = json_decode($row->params);
                        $html= $params->server??"";
                    }

                    return $html;
                })
                ->editColumn('params', function ($row) {
                    return "";
                })

                ->editColumn('author', function($row) {
                    $temp = '';
                    $temp .= $row->author->username??"";
                    return $temp;
                })
                ->editColumn('processor', function ($row) {
                    return  $row->processor->username??"";
                })
                ->editColumn('price', function ($row) {
                    return  $row->price;
                })

                ->editColumn('price_input', function ($row) {
                    return  $row->price_input;
                })
                ->editColumn('price_base', function ($row) {
                    $price = number_format($row->price_base);
                    if ($row->idkey == 'huge_psx_auto'){
                        $order_roblox = Roblox_Order::where('order_id',$row->id)->first();
                        if ($order_roblox){
                            $price = $order_roblox->phone??'';
                        }
                    }
                    return  $price;
                })

                ->editColumn('profit', function ($row) {
                    return  (int)$row->price - (int)$row->price_input;
                })
                ->editColumn('created_at', function ($row) {
                    return date('d/m/Y H:i:s', strtotime($row->created_at));
                })
                ->editColumn('updated_at', function ($row) {
                    return date('d/m/Y H:i:s', strtotime($row->updated_at));
                })
                ->addColumn('action', function ($row) {
                    $temp = "<a href=\"" . route('frontend.service-purchase-auto.show', $row->id) . "\"  rel=\"$row->id\" class=\"m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill \" title=\"Xem\"><i class=\"la la-eye\"></i></a>";
                    return $temp;
                })

                ->with('totalSumary', function() use ($datatableTotal) {
                    return $datatableTotal=$datatableTotal->first([
                        DB::raw('COUNT(order.id) as total_record'),
                        DB::raw('SUM(order.price) as total_price'),
                        DB::raw('SUM(order.price_base) as total_price_base'),
                        DB::raw('SUM(order.price_input) as price_input'),
                        DB::raw('SUM(COALESCE(order.price,0)) - SUM(COALESCE(order.price_input,0)) as total_profit'),
                    ]);
                })
                ->toJson();
        }


        $dataCategory = Item::where('module', '=', config('module.service.key'))->where('status','1')
            ->orderBy('title', 'asc');

         $dataCategory=$dataCategory->get();

        return view('frontend.service.purchase-auto.index')
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
    public function show(Request $request,$id)
    {
        $datatable = Order::with('author')
            ->where('author_id',auth('frontend')->user()->id)
            ->with(['item_ref'=>function($q){

            }])
            ->where('module',config('module.service-purchase.key'))
            ->where(DB::raw('COALESCE(gate_id,0)'),  1 );

        $datatable = $datatable->findOrFail($id);

        return view('frontend.service.purchase-auto.show')
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
     * Update the specified newscategory in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(Request $request, $id)
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

}
