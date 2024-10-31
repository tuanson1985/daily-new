<?php

namespace App\Http\Controllers\Admin\Service;

use App;
use App\Exports\ExportData;
use App\Exports\OrdersAutoExport;
use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Jobs\CallbackOrderRobloxBuyGemPet;
use App\Jobs\ServiceAuto\RobloxJob;
use App\Jobs\ServiceAuto\RobloxUserIdJob;
use App\Library\ChargeGameGateway\GarenaGate_Phap;
use App\Library\ChargeGameGateway\RobloxGate;
use App\Library\DirectAPI;
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
use Illuminate\Database\Eloquent\Collection;
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


        $this->middleware('permission:service-purchase-auto-list', ['only' => ['index']]);
        $this->middleware('permission:service-purchase-auto-show', ['only' => ['show']]);
        $this->middleware('permission:service-purchase-auto-delete', ['only' => ['destroy']]);
        $this->middleware('permission:service-purchase-auto-recharge', ['only' => ['recharge']]);


        $this->module="service-purchase-auto";
        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'.index'),
                'title' => __('Danh sách yêu cầu dịch vụ tự động')
            ];
        }

    }

    public function index(Request $request)
    {
        $group_ids = [];

        if (!Auth::user()->can('service-reception-all')) {
            //lấy các quyền được xem yêu cầu dịch vụ
            $service_access = ServiceAccess::query()
                ->with('user')
                ->where('user_id', Auth::user()->id)
                ->first();

            $param = json_decode(isset($service_access->params) ? $service_access->params : "");
            $group_ids = isset($param->view_role) ? $param->view_role : [];
        }

        $robuxAvailable = 0;
        $maxRobuxAvailable = 0;
        $balance = 0;
        $url = '/orders/stock';
        $method = "GET";
        $dataSend = array();
        $result_Api = DirectAPI::_getStock($url,$dataSend,$method);

        if (isset($result_Api) && isset($result_Api->status) && $result_Api->status == 1){
            $robuxAvailable = $result_Api->robuxAvailable??0;
            $maxRobuxAvailable = $result_Api->maxRobuxAvailable??0;
        }

        $url_balance = '/shared/balance';
        $dataBalanceSend = array();
        $result_balance_Api = DirectAPI::_getBalance($url_balance,$dataBalanceSend,$method);
        if (isset($result_balance_Api) && isset($result_balance_Api->status) && $result_balance_Api->status == 1){
            $balance = $result_balance_Api->balance??0;
        }
        $dola = 25500;
        try {
            $dola = RobloxGate::detectDola();
        } catch (\Exception $e) {
            $dola = 25500;
        }

        $vnd = (int)($dola*$balance);

        if ($request->ajax() || $request->export_excel==1) {
            if ($request->filled('rbx_api') && $request->get('rbx_api') == 1){
                $urlDetail = '/orders/detailed-stock';
                $methodDetail = "GET";
                $dataDetailSend = array();
                $result_Detail_Api = DirectAPI::_getStockDetail($urlDetail,$dataDetailSend,$methodDetail);

                if (!isset($result_Detail_Api)){
                    $data = [];
                    $collection = new Collection($data);
                    return \datatables()->collection($collection)
                        ->only([
                            'rate',
                            'rate_vnd',
                            'ratio',
                            'accountsCount',
                            'maxInstantOrder',
                            'totalRobuxAmount',
                        ])
                        ->toJson();
                }

                if (!isset($result_Detail_Api->status)){
                    $data = [];
                    $collection = new Collection($data);
                    return \datatables()->collection($collection)
                        ->only([
                            'rate',
                            'rate_vnd',
                            'ratio',
                            'accountsCount',
                            'maxInstantOrder',
                            'totalRobuxAmount',
                        ])
                        ->toJson();
                }

                if ($result_Detail_Api->status == 0){
                    $data = [];
                    $collection = new Collection($data);
                    return \datatables()->collection($collection)
                        ->only([
                            'rate',
                            'rate_vnd',
                            'ratio',
                            'accountsCount',
                            'maxInstantOrder',
                            'totalRobuxAmount',
                        ])
                        ->toJson();
                }
                if (empty($result_Detail_Api->data)){
                    $data = [];
                    $collection = new Collection($data);
                    return \datatables()->collection($collection)
                        ->only([
                            'rate',
                            'rate_vnd',
                            'ratio',
                            'accountsCount',
                            'maxInstantOrder',
                            'totalRobuxAmount',
                        ])
                        ->toJson();
                }
                if (count($result_Detail_Api->data) <= 0){
                    $data = [];
                    $collection = new Collection($data);
                    return \datatables()->collection($collection)
                        ->only([
                            'rate',
                            'rate_vnd',
                            'ratio',
                            'accountsCount',
                            'maxInstantOrder',
                            'totalRobuxAmount',
                        ])
                        ->toJson();
                }

                $data = $result_Detail_Api->data;
                usort($data, function($a, $b) {
                    return (float) $a->rate <=> (float) $b->rate;
                });
                $collection = new Collection($data);
                $collection = $collection->where('maxInstantOrder','>=',500);
                return \datatables()->collection($collection)
                    ->only([
                        'rate',
                        'rate_vnd',
                        'ratio',
                        'accountsCount',
                        'maxInstantOrder',
                        'totalRobuxAmount',
                    ])
                    ->editColumn('rate_vnd', function ($row) use ($dola){
                        $rate_vnd = (int)($dola*$row->rate);
                        return number_format($rate_vnd);
                    })
                    ->editColumn('ratio', function ($row) use ($dola){
                        return number_format($dola);
                    })
                    ->editColumn('maxInstantOrder', function ($row) use ($dola){
                        return number_format($row->maxInstantOrder);
                    })
                    ->editColumn('totalRobuxAmount', function ($row) use ($dola){
                        return number_format($row->totalRobuxAmount);
                    })
                    ->toJson();
            }
            $datatable =  Order::with(['shop','order_rbx','item_ref','author','processor','roblox_order' => function($q) {
                $q->with('bot');
            }])
                ->where('module',config('module.service-purchase.key'))
                //lấy điều kiện đơn bt
                ->where('gate_id',1);

            if ($request->filled('group_id')) {

                $datatable->whereHas('item_ref', function ($query) use ($request) {
                    $query->whereIn('id',$request->get('group_id'));
                });
            }

            if ($request->filled('payment_type')) {
                $payment_type = $request->get('payment_type');
                if ($payment_type == 1){
                    $datatable->where(function($q){
                        $q->orWhereNull('payment_type');
                        $q->orWhere('payment_type', 1);
                    });
                }elseif ($payment_type == 2){
                    $datatable->whereIn('payment_type',config('module.service-purchase-auto.rbx_api'));
                }
            }

            if ($request->filled('group_id2')) {

                $datatable->whereHas('item_ref', function ($query) use ($request) {
                    $query->where('id',$request->get('group_id2'));
                });
            }

            if ($request->filled('type_information')) {
                $datatable->whereHas('author', function ($query) use ($request) {
                    $query->where('type_information',$request->get('type_information'));
                });
            }

            if ($request->filled('work_name')) {

                $string = $request->get('work_name');
                if (strpos($string, '[') !== false && strpos($string, ']') !== false) {
                    $newString = str_replace(['[', ']'], '', $request->get('work_name'));

                    $datatable->where('description', $newString);
                } else {
                    $datatable->where('description', 'LIKE', '%' . $request->get('work_name') . '%');
                }

            }

            if ($request->filled('author')) {
                $datatable->whereHas('author', function ($query) use ($request) {
                    $query->Where('username', 'LIKE', '%' . $request->get('author') . '%');
                });
            }

            if ($request->filled('roblox_acc')) {
                $datatable->whereHas('roblox_order', function ($query) use ($request) {
                    $query->whereHas('bot', function ($query) use ($request) {
                        $query->Where('acc', 'LIKE', '%' . $request->get('roblox_acc') . '%');
                    });
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

            if ($request->filled('request_id')) {
                $request_id = explode(',',$request->get('request_id'));
                $datatable->whereIn('request_id_customer',$request_id);
            }

            if ($request->filled('check_status')) {

                $datatable->with('order_detail',function ($query){
                    $query->whereIn('status',[3,4]);
                });
                $datatable->whereHas('order_detail',function ($query){
                    $query->where('status',3);
                });
                $datatable->whereHas('khachhang',function ($q){
                    $q->where('status','danhan');
                });
            }

            if ($request->filled('check_status_ninjaxu')) {

                $datatable->with('order_detail',function ($query){
                    $query->whereIn('status',[3,4]);
                });
                $datatable->whereHas('order_detail',function ($query){
                    $query->where('status',3);
                });
                $datatable->whereHas('ninjaxu_khachhang',function ($q){
                    $q->where('status','danhan');
                });
            }

            if ($request->filled('check_status_nrogem')) {

                $datatable->with('order_detail',function ($query){
                    $query->whereIn('status',[3,4]);
                });
                $datatable->whereHas('order_detail',function ($query){
                    $query->where('status',3);
                });
                $datatable->whereHas('item_rels',function ($q){
                    $q->where('status','danhanngoc');
                });
            }

            if ($request->filled('check_status_roblox')) {

                $datatable->with('order_detail',function ($query){
                    $query->whereIn('status',[3,4]);
                });
                $datatable->whereHas('order_detail',function ($query){
                    $query->where('status',3);
                });
                $datatable->whereHas('roblox_order',function ($q){
                    $q->where('status','danhan');
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

            $status_datas = config('module.service-purchase-auto.status');
            $data_html = view('admin.service.purchase-auto.widget.__status')
                ->with('status_datas',$status_datas)
                ->render();
            if ($request->filled('type')) {
                $type_status = $request->get('type');
                if ($type_status == 1) {
                    $datatable->whereIn('status', [1]);
                    $status_datas = new \stdClass();
                    $status_datas->{1} = "Đang chờ";
                    $data_html = view('admin.service.purchase-auto.widget.__status')
                        ->with('status_datas',$status_datas)
                        ->render();

                }
                elseif ($type_status == 2) {
                    $datatable->whereIn('status', [2]);
                    $status_datas = new \stdClass();
                    $status_datas->{2} = "Đang thực hiện";
                    $data_html = view('admin.service.purchase-auto.widget.__status')
                        ->with('status_datas',$status_datas)
                        ->render();
                }
                elseif ($type_status == 3) {
                    $datatable->whereIn('status', [0,5,3]);
                    $status_datas = new \stdClass();
                    $status_datas->{0} = "Đã hủy";
                    $status_datas->{3} = "Từ chối";
                    $status_datas->{5} = "Thất bại";
                    $data_html = view('admin.service.purchase-auto.widget.__status')
                        ->with('status_datas',$status_datas)
                        ->render();

                }
                elseif ($type_status == 4) {
                    $datatable->whereIn('status', [10,4]);
                    $status_datas = new \stdClass();
                    $status_datas->{4} = "Hoàn tất";
                    $status_datas->{10} = "Hoàn tất đợi xác nhận";
                    $data_html = view('admin.service.purchase-auto.widget.__status')
                        ->with('status_datas',$status_datas)
                        ->render();

                }
                elseif ($type_status == 5) {
                    $datatable->whereIn('status', [7]);
                    $status_datas = new \stdClass();
                    $status_datas->{7} = "Kết nối NCC thất bại";
                    $data_html = view('admin.service.purchase-auto.widget.__status')
                        ->with('status_datas',$status_datas)
                        ->render();
                }elseif ($type_status == 6) {
                    $datatable->whereIn('status', [9]);
                    $status_datas = new \stdClass();
                    $status_datas->{9} = "Xử lý thủ công";
                    $data_html = view('admin.service.purchase-auto.widget.__status')
                        ->with('status_datas',$status_datas)
                        ->render();
                }
            }

            if ($request->filled('status')) {
                $datatable->whereIn('status', $request->get('status'));
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
                    $datatable->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_started_at')));
                }
                if ($request->filled('finished_ended_at')) {
                    $datatable->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_ended_at')));
                }
            }else{

                // Tính toán ngày bắt đầu (7 ngày trước)
                $startOfDay = Carbon::now()->subDays(7)->startOfDay();

// Tính toán ngày kết thúc (thời điểm hiện tại)
                $endOfDay = Carbon::now()->endOfDay();

// Thêm điều kiện lọc mới cho 7 ngày trước
                $datatable->whereBetween('updated_at', [$startOfDay, $endOfDay]);
//                $datatable->whereMonth('updated_at', Carbon::now()->month);

            }

            //nếu user ko full quyền nhận các dịch vụ thì lấy các id dịch vụ được cấp quyền
            if (!Auth::user()->can('service-reception-all')) {

                //nếu có lọc dịch vụ thì chỉ chấp nhận lọc các dịch vụ cho phép
                if ($request->filled('group_id')) {

                    $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
                        $query->whereIn('id', $group_ids)->whereIn('id', (array)$request->get('group_id'));
                    });
                }elseif ($request->get('group_id2')){
                    $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
                        $groupAllowView = array_intersect($group_ids,(array)$request->get('group_id2'));
                        $query->whereIn('id', $groupAllowView);
                    });

                }
                //else lọc dịch vụ all các dịch vụ cho phép
                else{
                    $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
                        $query->whereIn('id', $group_ids);
                    });
                }
            }
            //nếu user có full quyền nhận các dịch vụ thì lấy luôn id dịch vụ đó
            else{
                if ($request->filled('group_id')) {
                    $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
                        $query->whereIn('id',$request->get('group_id'));
                    });
                }
                if ($request->filled('group_id2')) {
                    $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
                        $query->where('id',$request->get('group_id2'));
                    });
                }
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

                    if ($row->idkey == 'roblox_buyserver' || $row->idkey == 'roblox_buygamepass' || $row->idkey == 'huge_99_auto'
                        || $row->idkey == 'robux_premium_auto' || $row->idkey == 'pet_99_auto'
                        || $row->idkey == 'gem_unist_auto' || $row->idkey == 'unist_auto' || $row->idkey == 'huge_psx_auto' || $row->idkey == 'pet_99_auto'
                    ){
                        if (isset($row->roblox_order)){
                            $order_roblox = $row->roblox_order;
                            $html = $order_roblox->uname??'';
                        }
                    }

                    return $html;
                })
                ->editColumn('params', function ($row) {
                    return "";
                })
                ->editColumn('information', function ($row) {
                    if ($row->author){
                        if ($row->author->type_information){
                            if ($row->author->type_information == 0){
                                return  "Việt Nam";
                            }elseif ($row->author->type_information == 1){
                                return  "Global";
                            }
                            else{
                                return  "Sàn";
                            }
                        }else{
                            return  "Việt Nam";
                        }
                    }
                    return  "Việt Nam";
                })
                ->editColumn('author', function($row) {
                    $temp = '';
                    if( auth()->user()->can('view-profile')){
                        $temp .= "<a href=\"#\"  class=\"load-modal\" rel=\"".route('admin.view-profile',["username" => ($row->author->username??"")])."\">".($row->author->username??"")."</a>";
                    }
                    else{
                        $temp .= $row->username;
                    }
                    return $temp;
                })
                ->editColumn('author', function($row) {
                    $temp = '';
                    if( auth()->user()->can('view-profile')){
                        $temp .= "<a href=\"#\"  class=\"load-modal\" rel=\"".route('admin.view-profile',["username" => ($row->author->username??"")])."\">".($row->author->username??"")."</a>";
                    }
                    else{
                        $temp .= $row->author->username??"";
                    }
                    return $temp;
                })
                ->editColumn('payment_type', function($row) {
                    $temp = "DAILY";
                    if (isset($row->payment_type) && in_array($row->payment_type,config('module.service-purchase-auto.rbx_api'))){
                        $temp = "RBX";
                    }
                    return $temp;
                })
                ->editColumn('rate', function($row) {
                    $temp = "";
                    if (isset($row->payment_type) && in_array($row->payment_type,config('module.service-purchase-auto.rbx_api'))){
                        if ($row->status == 4 || $row->status == 10){
                            if (isset($row->order_rbx)){
                                $order_rbx = $row->order_rbx;
                                if (isset($order_rbx->content)){
                                    $params = json_decode($order_rbx->content);
                                    if (isset($params->rate)){
                                        $temp = $params->rate;
                                    }
                                }
                            }
                        }else{
                            $temp = config('module.service-purchase-auto.rbx_rate.'.$row->payment_type)??0;
                        }
                    }
                    return $temp;
                })
                ->editColumn('vnd', function($row) use ($dola){
                    $temp = "";
                    if (isset($row->payment_type) && in_array($row->payment_type,config('module.service-purchase-auto.rbx_api'))){
                        $temp = (float)config('module.service-purchase-auto.rbx_rate.'.$row->payment_type)??0;
                        if ($row->status == 4 || $row->status == 10){
                            if (isset($row->order_rbx)){
                                $order_rbx = $row->order_rbx;
                                if (isset($order_rbx->content)){
                                    $params = json_decode($order_rbx->content);
                                    if (isset($params->rate)){
                                        $temp = (float)$params->rate;
                                        $balnce_vnd = $dola*$temp;
                                        return number_format($balnce_vnd);
                                    }
                                }
                            }
                        }else{
                            $balnce_vnd = $dola*$temp;
                            return number_format($balnce_vnd);
                        }
                    }
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
                    if ($row->idkey == 'huge_psx_auto' || $row->idkey == 'huge_99_auto' || $row->idkey == 'gem_unist_auto'
                        || $row->idkey == 'unist_auto' || $row->idkey == 'roblox_gem_pet' || $row->idkey == 'pet_99_auto' || $row->idkey == 'item_pet_go_auto'
                        || $row->idkey == 'robux_premium_auto'){
                        if (isset($row->roblox_order)){
                            $order_roblox = $row->roblox_order;
                            if ($row->idkey == 'roblox_gem_pet' || $row->idkey == 'pet_99_auto' || $row->idkey == 'item_pet_go_auto'){
                                if (isset($row->price_base)){
                                    $price = number_format($row->price_base);
                                }
                                else{
                                    if ($order_roblox->phone){
                                        $valueWithB = $order_roblox->phone;
                                        // Loại bỏ ký tự "B" và chuyển đổi thành số
                                        $valueInBillion = (float) str_replace('B', '', $valueWithB);
                                        $convertedValue = $valueInBillion * 1000000000;
                                        $price = number_format($convertedValue);
                                    }
                                }
                            }
                            else{
                                $price = $order_roblox->phone??'';
                            }
                        }
                    }
                    return  $price;
                })
                ->editColumn('profit', function ($row) {
                    return  (int)$row->price - (int)$row->price_input;
                })

                ->editColumn('roblox_acc', function ($row) {
                    $acc = '';
                    if (isset($row->roblox_order)){
                        $roblox_order = $row->roblox_order;
                        $acc = $roblox_order->bot_handle??'';
                        if (isset($roblox_order->bot)){
                            $acc = $roblox_order->bot->acc??'';
                        }
                    }
                    return  $acc;
                })

                ->editColumn('created_at', function ($row) {
                    return date('d/m/Y H:i:s', strtotime($row->created_at));
                })
                ->editColumn('updated_at', function ($row) {
                    return date('d/m/Y H:i:s', strtotime($row->updated_at));
                })
                ->addColumn('action', function ($row) {
                    $temp = "<a href=\"" . route('admin.service-purchase-auto.show', $row->id) . "\"  rel=\"$row->id\" class=\"m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill \" title=\"Xem\"><i class=\"la la-eye\"></i></a>";
                    if ((Auth::user()->id == 55 || Auth::user()->id == 198544 || Auth::user()->id == 28) && $row->status === 4 && ($row->idkey == 'roblox_gem_pet' || $row->idkey == 'huge_99_auto' || $row->idkey == 'gem_unist_auto' || $row->idkey == 'unist_auto' || $row->idkey == 'huge_psx_auto' || $row->idkey == 'item_pet_go_auto' || $row->idkey == 'pet_99_auto')){
                        $temp .= '<a href=\'javascript:void(0)\' data-toggle="modal" data-target="#refundModal" rel="'.$row->id.'" class=\'btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-danger btn-refund mr-2\' title="Hoàn tiền"><i class="la la-refresh"></i></a>';
                    }
                    if($row->status==7 || ($row->status==89 && $row->idkey == 'roblox_internal')){
                        if (Auth::user()->can('service-purchase-auto-recharge')) {
                            $temp .= "<a  rel=\"$row->id\" class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill recharge_toggle' data-toggle=\"modal\" data-target=\"#rechargeModal\"  title=\"Nạp lại\"><i class=\"la la-sync-alt\"></i></a>";
                        }

                    }

                    if($row->status==9 && $row->idkey == 'roblox_buygamepass' && (Auth::user()->id == 301 || Auth::user()->id == 28 || Auth::user()->id == 198767)){
                        $temp .= "<a  rel=\"$row->id\" class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill recharge_gamepass_toggle' data-toggle=\"modal\" data-target=\"#rechargeBuyGamePassModal\"  title=\"Chuyển trạng thái gamepass\"><i class=\"la la-sync-alt\"></i></a>";
                    }
                    if(($row->status==1 || $row->status==22) && $row->idkey == 'roblox_buygamepass' && Auth::user()->id == 5551){
                        $temp .= "<a  rel=\"$row->id\" class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill recharge_gamepass_toggle' data-toggle=\"modal\" data-target=\"#rechargeBuyGamePassModal\"  title=\"Chuyển trạng thái gamepass\"><i class=\"la la-sync-alt\"></i></a>";
                    }
                    return $temp;
                })
                ->with('totalSumary', function() use ($data_html) {
                    return $data_html;
                })
                ->toJson();
        }


        $dataCategory = Item::where('module', '=', config('module.service.key'))
            ->where('status','1')
            ->where('gate_id',1)
            ->orderBy('title', 'asc');

        if (!Auth::user()->can('service-reception-all')) {
            $dataCategory->whereIn('id', $group_ids);
        }

         $dataCategory=$dataCategory->get();

        return view('admin.service.purchase-auto.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('balance',$balance)
            ->with('vnd',$vnd)
            ->with('robuxAvailable',$robuxAvailable)
            ->with('maxRobuxAvailable',$maxRobuxAvailable)
            ->with('dataCategory', $dataCategory);

    }

    public function loadAttributeTk(Request $request){

//        if (!Auth::user()->can('service-purchase-attribute-report')){
//            return redirect()->back()->withErrors(__('Bạn không có quyền'));
//        }

        $group_ids = [];

        $service_access = ServiceAccess::query()->with('user')->where('user_id', Auth::user()->id)
            ->first();

        if (!Auth::user()->can('service-reception-all')) {
            //lấy các quyền được xem yêu cầu dịch vụ

            $param = json_decode(isset($service_access->params) ? $service_access->params : "");
            $group_ids = isset($param->view_role) ? $param->view_role : [];
        }

        if ($request->ajax()) {

            $datatable =  Order::with(['shop','item_ref','author','processor','roblox_order'])
                ->where('module',config('module.service-purchase.key'))
                //lấy điều kiện đơn bt
                ->where(DB::raw('COALESCE(order.gate_id,0)'),  1 );

            if ($request->filled('group_id')) {

                $datatable->whereHas('item_ref', function ($query) use ($request) {
                    $query->whereIn('id',$request->get('group_id'));
                });
            }

            if ($request->filled('type')) {
                $type_status = $request->get('type');
                if ($type_status == 1) {
                    $datatable->whereIn('status', [1]);
                } elseif ($type_status == 2) {
                    $datatable->whereIn('status', [2]);
                } elseif ($type_status == 3) {
                    $datatable->whereIn('status', [0,5,3]);
                } elseif ($type_status == 4) {
                    $datatable->whereIn('status', [10,4]);
                } elseif ($type_status == 5) {
                    $datatable->whereIn('status', [7]);
                }elseif ($type_status == 6) {
                    $datatable->whereIn('status', [9]);
                }
            }

            if ($request->filled('group_id2')) {

                $datatable->whereHas('item_ref', function ($query) use ($request) {
                    $query->where('id',$request->get('group_id2'));
                });
            }

            if ($request->filled('type_information')) {
                $datatable->whereHas('author', function ($query) use ($request) {
                    $query->where('type_information',$request->get('type_information'));
                });
            }


            if ($request->filled('author')) {
                $datatable->whereHas('author', function ($query) use ($request) {
                    $query->Where('username', 'LIKE', '%' . $request->get('author') . '%');
                });
            }

            if ($request->filled('roblox_acc')) {
                $datatable->whereHas('roblox_order', function ($query) use ($request) {
                    $query->whereHas('bot', function ($query) use ($request) {
                        $query->Where('acc', 'LIKE', '%' . $request->get('roblox_acc') . '%');
                    });
                });
            }

            if ($request->filled('roblox_acc_old')) {
                $datatable->whereHas('roblox_order', function ($query) use ($request) {
                    $query->Where('bot_handle', 'LIKE', '%' . $request->get('roblox_acc_old') . '%');
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

            if ($request->filled('payment_type')) {
                $payment_type = $request->get('payment_type');
                if ($payment_type == 1){
                    $datatable->where(function($q){
                        $q->orWhereNull('payment_type');
                        $q->orWhere('payment_type', 1);
                    });
                }elseif ($payment_type == 2){
                    $datatable->whereIn('payment_type',config('module.service-purchase-auto.rbx_api'));
                }
            }

            if ($request->filled('request_id')) {
                $request_id = explode(',',$request->get('request_id'));
                $datatable->whereIn('request_id_customer',$request_id);
            }

            if ($request->filled('check_status')) {

                $datatable->with('order_detail',function ($query){
                    $query->whereIn('status',[3,4]);
                });
                $datatable->whereHas('order_detail',function ($query){
                    $query->where('status',3);
                });
                $datatable->whereHas('khachhang',function ($q){
                    $q->where('status','danhan');
                });
            }

            if ($request->filled('check_status_ninjaxu')) {

                $datatable->with('order_detail',function ($query){
                    $query->whereIn('status',[3,4]);
                });
                $datatable->whereHas('order_detail',function ($query){
                    $query->where('status',3);
                });
                $datatable->whereHas('ninjaxu_khachhang',function ($q){
                    $q->where('status','danhan');
                });
            }

            if ($request->filled('check_status_nrogem')) {

                $datatable->with('order_detail',function ($query){
                    $query->whereIn('status',[3,4]);
                });
                $datatable->whereHas('order_detail',function ($query){
                    $query->where('status',3);
                });
                $datatable->whereHas('item_rels',function ($q){
                    $q->where('status','danhanngoc');
                });
            }

            if ($request->filled('check_status_roblox')) {

                $datatable->with('order_detail',function ($query){
                    $query->whereIn('status',[3,4]);
                });
                $datatable->whereHas('order_detail',function ($query){
                    $query->where('status',3);
                });
                $datatable->whereHas('roblox_order',function ($q){
                    $q->where('status','danhan');
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
                    $datatable->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_started_at')));
                }
                if ($request->filled('finished_ended_at')) {
                    $datatable->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_ended_at')));
                }
            }else{

                // Tính toán ngày bắt đầu (7 ngày trước)
                $startOfDay = Carbon::now()->subDays(7)->startOfDay();

// Tính toán ngày kết thúc (thời điểm hiện tại)
                $endOfDay = Carbon::now()->endOfDay();

// Thêm điều kiện lọc mới cho 7 ngày trước
                $datatable->whereBetween('updated_at', [$startOfDay, $endOfDay]);
//                $datatable->whereMonth('updated_at', Carbon::now()->month);

            }

            //nếu user ko full quyền nhận các dịch vụ thì lấy các id dịch vụ được cấp quyền
            if (!Auth::user()->can('service-reception-all')) {

                //nếu có lọc dịch vụ thì chỉ chấp nhận lọc các dịch vụ cho phép
                if ($request->filled('group_id')) {

                    $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
                        $query->whereIn('id', $group_ids)->whereIn('id', (array)$request->get('group_id'));
                    });
                }elseif ($request->get('group_id2')){
                    $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
                        $groupAllowView = array_intersect($group_ids,(array)$request->get('group_id2'));
                        $query->whereIn('id', $groupAllowView);
                    });

                }
                //else lọc dịch vụ all các dịch vụ cho phép
                else{
                    $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
                        $query->whereIn('id', $group_ids);
                    });
                }
            }
            //nếu user có full quyền nhận các dịch vụ thì lấy luôn id dịch vụ đó
            else{
                if ($request->filled('group_id')) {
                    $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
                        $query->whereIn('id',$request->get('group_id'));
                    });
                }
                if ($request->filled('group_id2')) {
                    $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
                        $query->where('id',$request->get('group_id2'));
                    });
                }
            }


            $datatable= $datatable->select('order.*');

            $datatable = $datatable->select(
                DB::raw('COUNT(order.id) as total_record'),
                DB::raw('SUM(order.price) as total_price'),
                DB::raw('SUM(order.price_base) as total_price_base'),
                DB::raw('SUM(order.price_input) as price_input'),
                DB::raw('SUM(COALESCE(order.price,0)) - SUM(COALESCE(order.price_input,0)) as total_profit')
            )->first();

            $html = view('admin.service.purchase-auto.widget.__attribute_tk')
                ->with('datatable',$datatable)
                ->render();

            return response()->json([
                "message" => 'Lấy sms đại lý thành công',
                "data" => $html,
                "status" => 1,
            ], 200);
        }

        return response()->json([
            'status' => 0,
            'message' => __('Không thể tải dữ liệu sms vui lòng thử lại.')
        ]);
    }

    public function exportExcel(Request $request){

        $export = new OrdersAutoExport($request);
        return \Excel::download($export, 'Thống kê dịch vụ tự động_ ' . time() . '.xlsx');

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

        $datatable = Order::query()
            ->with(['item_ref','author','processor','roblox_order' => function($q) {
                $q->with('bot');
            },'order_pengiriman'])
            ->where('module',config('module.service-purchase.key'))
            ->where(DB::raw('COALESCE(gate_id,0)'),  1 );

        $datatable = $datatable->findOrFail($id);

        return view('admin.service.purchase-auto.show')
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


    public function postEditInfo(Request $request, $id)
    {
        DB::beginTransaction();
        try {

            $data = Order::where('module', '=', config('module.service-purchase.key'))
                ->where(function ($query) {
                    $query->orWhere('status', "1");
                    $query->orWhere('status', "2");
                })
                ->lockForUpdate()->findOrFail($id);

            if ($data->expired_lock != null && $data->expired_lock > Carbon::now()) {
                return redirect()->back()->withErrors("Dịch vụ đã được thực hiện. Vui lòng thử lại trong vòng 5 phút");
            }

            //Kiểm tra thông tin nhập lên
            $send_name = Helpers::DecodeJson("send_name", $data->parrent->params);
            $send_type = Helpers::DecodeJson("send_type", $data->parrent->params);
            $customer_info = [];
            if (!empty($send_name) && count($send_name) > 0) {
                for ($i = 0; $i < count($send_name); $i++) {

                    if ($send_type[$i] == 4 && $request->hasFile('customer_data' . $i)) { //nếu  nó là kiểu upload ảnh

                        $info = MediaHelpers::upload_image($request->get('customer_data' . $i), $dir = "upload/service/{$data->id}", uniqid(), $width = 900, $height = 600);
                        $customer_info['customer_data' . $i] = $info;

                    } else {
                        $customer_info['customer_data' . $i] = $request->get('customer_data' . $i);
                    }
                }
            }

            //update info cho purchase
            $data->params = json_encode($customer_info, JSON_UNESCAPED_UNICODE);
            $data->save();

            //check và edit thông tin giao dich tự động && custom service
            $input_auto = $data->gate_id;
            if ($input_auto == 1 && $data->idkey == 'nrocoin') {

                $khachhang = KhachHang::where('order_id', $data->id)->lockForUpdate()->firstOrFail();
                $khachhang->uname = $request->customer_data0;
                $khachhang->save();
            } elseif ($input_auto == 1 && $data->idkey == 'nrogem') {
                $nrogem_GiaoDich = Nrogem_GiaoDich::where('order_id', $data->id)->lockForUpdate()->firstOrFail();
                $nrogem_GiaoDich->acc = $request->customer_data0;
                $nrogem_GiaoDich->pass = $request->customer_data1;
                $nrogem_GiaoDich->save();

            } elseif ($input_auto == 1 && $data->idkey == 'langlacoin') {
                $langla_khachhang = LangLaCoin_KhachHang::where('order_id', $data->id)->lockForUpdate()->firstOrFail();
                $langla_khachhang->uname = $request->customer_data0;
                $langla_khachhang->save();
            }
            elseif ($input_auto == 1 && $data->idkey == 'ninjaxu') {
                $ninjaxu_khachhang = NinjaXu_KhachHang::where('order_id', $data->id)->lockForUpdate()->firstOrFail();
                $ninjaxu_khachhang->uname = $request->customer_data0;
                $ninjaxu_khachhang->save();
            }

            //active log active
            ActivityLog::add($request, "Chỉnh sửa thông tin thành công #".$data->id  );

        } catch (\Exception $e) {
            DB::rollback();
            Log::error( $e);
            return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');
        }

        // Commit the queries!
        DB::commit();

        return redirect()->back()->with('success', "Chỉnh sửa thông tin thành công");
    }

    public function postReception(Request $request, $id)
    {
//        DB::beginTransaction();
//        try {
//
//            $data = Order::where('module', '=', config('module.service-purchase.key'))
//                ->where('status', "1")->lockForUpdate()->findOrFail($id);
//            //check nếu là dịch vu auto thì không thể tiếp nhận
//
//
//            if ( $data->gate_id == "1") {
//                return redirect()->back()->withErrors('Không thể tiếp nhận dịch vụ có hệ thống tự động');
//            }
//
//            $data->processor_id = Auth::guard()->user()->id;
//            $data->status = 2;
//            $data->save();
//
//            //set tiến độ tiếp nhận
//            OrderDetail::create([
//                'order_id' => $data->id,
//                'module' => config('module.service-workflow.key'),
//                'author' => Auth::guard()->user()->id,
//                'status' => "2",
//            ]);
//
//            //active log active
//            ActivityLog::add($request, "Tiếp nhận thành công yêu cầu dịch vụ #".$data->id  );
//
//        } catch (\Exception $e) {
//            DB::rollback();
//            Log::error($e);
//            return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');
//        }
//
//        // Commit the queries!
//        DB::commit();
//
//        return redirect()->back()->with('success', "Tiếp nhận thành công yêu cầu dịch vụ  #".$data->id);
    }

    public function postCompleted(Request $request, $id)
    {

//        DB::beginTransaction();
//        try {
//
//            $data = Item::where('module', '=', config('module.service-purchase.key'))
//                ->where('status', "2")
//                ->where('processor_id',  Auth::guard()->user()->id)
//                ->lockForUpdate()
//                ->findOrFail($id);
//            //check nếu là dịch vu auto thì không thể tiếp nhận
//
//            if ( $data->gate_id == "1") {
//                return redirect()->back()->withErrors('Không thể hoàn tất dịch vụ có hệ thống tự động');
//            }
//            $data->status = 4;
//            $data->save();
//
//            //set tiến độ tiếp nhận
//            OrderDetail::create([
//                'order_id' => $data->id,
//                'module' => config('module.service-workflow.key'),
//                'author' => Auth::guard()->user()->id,
//                'status' => "4",
//
//            ]);
//
//            //tính chiết khấu cho người bán
//            $ratio = 80;
//
//            //cộng tiền user
//            $real_received_amount = ($ratio * $data->price) / 100;
//            $userTransaction = User::where('id',Auth::guard()->user()->id)->lockForUpdate()->firstOrFail();
//            $userTransaction->balance = $userTransaction->balance + $real_received_amount;
//            $userTransaction->balance_out = $userTransaction->balance_out + $real_received_amount;
//            $userTransaction->save();
//            //tạo tnxs
//            $txns = Txns::create([
//                'trade_type' => 'service_purchase',//Hoàn tất dịch vụ
//                'is_add' => '1',//Cộng tiền
//                'username' => $userTransaction->username,
//                'amount' => $data->price,
//                'real_received_amount' => $real_received_amount,
//                'last_balance' => $userTransaction->balance,
//                'description' => "Thanh toán dịch vụ #" . $data->id,
//                'ip' => $request->getClientIp(),
//                'ref_id' => $data->id,
//                'status' => 1
//            ]);
//            ActivityLog::add($request, "Hoàn tất thành công yêu cầu dịch vụ #".$data->id  );
//
//        } catch (\Exception $e) {
//            DB::rollback();
//            Log::error($e);
//            return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');
//        }
//
//        // Commit the queries!
//        DB::commit();
//
//        if($data->url!=""){
//            $messageBot= config('module.service-purchase.status.'.$data->status);
//            $this->callbackToShop($data,$messageBot);
//        }
//
//        return redirect()->back()->with('success', "Hoàn tất thành công yêu cầu dịch vụ #".$data->id);

    }

    /**
     * Remove the specified newscategory from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {

        $this->validate($request, [
            'mistake_by' => 'required',
            'note' => 'required|min:10|max:500',
        ], [
            'mistake_by.required' => "Vui lòng chọn lỗi thuộc về",
            'note.required' => "Vui lòng nhập nội dung lỗi",
            'note.min' => "Nội dung phải ít nhất 10 ký tự",
            'note.max' => "Nội dung phải không quá 500 ký tự",
        ]);
        // Start transaction!
        DB::beginTransaction();
        try {
            $module = config('module.service-workflow.key');
            $data = Order::where('module', '=', config('module.service-purchase.key'))
                ->with('item_ref')
                ->where(function ($query) {
                    $query->orWhere('status', "1");
                    $query->orWhere('status', "2");
                    $query->orWhere('status', "7");
                    $query->orWhere('status', "9");
                })
                ->lockForUpdate()->findOrFail($id);

            if ($data->expired_lock != null && $data->expired_lock > Carbon::now()) {

                return redirect()->back()->withErrors("Dịch vụ đã được thực hiện. Vui lòng thử lại trong vòng 5 phút");
            }


            $data->update([
                'status' => 3,
            ]);//trạng thái từ chối

            //set tiến độ từ chối
            OrderDetail::create([
                'order_id' => $data->id,
                'module' => config('module.service-workflow.key'),
                'author_id' => Auth::guard()->user()->id,
                'content' => "[Lỗi bởi: " . config('constants.module.service.mistake_by.' . $request->mistake_by) . "] - " . $request->note,
                'status' => "3",
            ]);

            //check và edit thông tin giao dich tự động && custom service
            $input_auto = $data->gate_id;
            if ($input_auto == 1 && $data->idkey == 'nrocoin') {

                $khachhang = KhachHang::where('order_id', $data->id)->lockForUpdate()->firstOrFail();
                $khachhang->status = "dahuybo";
                $khachhang->save();
            } elseif ($input_auto == 1 && $data->idkey == 'nrogem') {
                $nrogem_GiaoDich = Nrogem_GiaoDich::where('order_id', $data->id)->lockForUpdate()->firstOrFail();
                $nrogem_GiaoDich->status = "dahuybo";
                $nrogem_GiaoDich->save();

            }
            elseif ($input_auto == 1 && $data->idkey == 'langlacoin') {
                $langla_khachhang = LangLaCoin_KhachHang::where('order_id', $data->id)->lockForUpdate()->firstOrFail();
                $langla_khachhang->status = "dahuybo";
                $langla_khachhang->save();

            }
            elseif ($input_auto == 1 && $data->idkey == 'ninjaxu') {
                $ninjaxu_khachhang = NinjaXu_KhachHang::where('order_id', $data->id)->lockForUpdate()->firstOrFail();
                $ninjaxu_khachhang->status = "dahuybo";
                $ninjaxu_khachhang->save();
            }
            elseif ($input_auto == 1 && $data->idkey == 'roblox_gem_pet'){
                $roblox_order = Roblox_Order::query()->with('order')->where('order_id',$data->id)->lockForUpdate()->first();
                $roblox_order->status = 'dahuybo';
                $roblox_order->save();
            }
            elseif ($input_auto == 1 && ($data->idkey == 'roblox_buyserver' || $data->idkey == 'roblox_buygamepass' || $data->idkey == 'robux_premium_auto'
                    || $data->idkey == 'huge_99_auto' || $data->idkey == 'gem_unist_auto' || $data->idkey == 'unist_auto' || $data->idkey == 'item_pet_go_auto'
                    || $data->idkey == 'huge_psx_auto' || $data->idkey == 'pet_99_auto')){
                $roblox_order = Roblox_Order::query()->with('order')->where('order_id',$data->id)->lockForUpdate()->first();
                $roblox_order->status = 'dahoantien';
                $roblox_order->save();
            }

            //hoàn tiền cho khách hàng
            $userTransaction = User::where('id', $data->author_id)->lockForUpdate()->firstOrFail();

            if($data->price==0 || $data->price==""){
                if($data->idkey == 'nrogem'){
                    $userTransaction['gem_num'] = $userTransaction['gem_num'] + $data->price_base;
                }
                if($data->idkey == 'nrocoin'){
                    $userTransaction['coin_num'] = $userTransaction['coin_num'] + $data->price_base;
                }
                if($input_auto == 1 && $data->idkey == 'ninjaxu'){
                    $userTransaction['xu_num'] = $userTransaction['xu_num'] + $data->price_base;
                }
            }else{
                $userTransaction->balance = $userTransaction->balance + $data->price;
                $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $data->price;
            }

            $userTransaction->save();

            //tạo tnxs
            $txns = Txns::create([
                'trade_type' => 'refund',//Hoàn tiền
                'is_add' => '1',//Công tiền
                'user_id' => $userTransaction->id,
                'amount' => $data->price,
                'real_received_amount' => $data->price,
                'last_balance' => $userTransaction->balance,
                'description' => 'Hoàn tiền từ chối yêu cầu dich vụ #' . $data->id,
                'order_id' => $data->id,
                'ip' => $request->getClientIp(),
                'status' => 1
            ]);

            if (isset($order->payment_type) && in_array($order->payment_type,config('module.service-purchase-auto.rbx_api'))){
                $url = '/orders/cancel';
                $method = "POST";
                $dataSend = array();
                $dataSend['orderId'] = $data->request_id_customer;
                $payment_type = $data->payment_type;
                $result_Api = DirectAPI::_cancelProduct($url,$dataSend,$method,$payment_type);

                if (isset($result_Api) && isset($result_Api->status) && $result_Api->status == 1){
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $data->id,
                        'module' => $module,
                        'content' => "Đã gửi yêu cầu hủy đơn sang RBX API",
                        'status' => 2,
                    ]);
                }else{
                    DB::rollback();
                    return redirect()->back()->withErrors("Yêu cầu hủy đơn bị từ chối");

                }
            }

            //active log active
            ActivityLog::add($request, "Đã từ chối thành công yêu cầu dịch vụ #".$data->id  );

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');
        }

        // Commit the queries!
        DB::commit();

        //Callback to shop
        if($data->url!=""){
            $messageBot = config('module.service-purchase-auto.status.'.$data->status);

            if ($data->idkey == 'roblox_gem_pet' || $data->idkey == 'huge_psx_auto' || $data->idkey == 'gem_unist_auto' || $data->idkey == 'unist_auto'
                || $data->idkey == 'huge_99_auto' || $data->idkey == 'pet_99_auto' || $data->idkey == 'item_pet_go_auto'){
                $this->callbackToShopRoblox($data,$roblox_order->status,__('Đơn hàng bị hủy từ đại lý'));
            }else{
                $this->callbackToShop($data,$messageBot);
            }
        }

        return redirect()->back()->with('success', "Đã từ chối thành công yêu cầu dịch vụ #".$data->id);

    }

    public function postInbox(Request $request, $id)
    {

        $this->validate($request, [
            //'captcha' => 'required|captcha'
        ], [
            'captcha.required' => "Vui lòng nhập mã bảo vệ",
            'captcha.captcha' => "Mã bảo vệ không đúng",
        ]);

        if ($request->filled('image') && count($request->image) > 5) {
            return redirect()->back()->withErrors('Bạn có thể upload tối đa 5 hình ảnh');
        };

        $this->validate($request, [
            'image.*' => 'mimes:jpg,jpeg,png,gif|max:10000',
            'message' => 'required',
        ], [
            'image.*.mimes' => 'Ảnh đính kèm không đúng định dạng jpg,jpeg,png,gif',
            'message.required' => 'Vui lòng nhập nội dung trao đổi',

        ]);


        $order = Order::where('module', config('module.service-purchase.key'))->where('id',$request->id)->first();

        if(!$order){
            return redirect()->back()->withErrors('Không tìm thấy đơn hàng cần đối thoại');
        }

        if( Auth::user()->can('inbox-support-complain')) {

        }
        elseif( $order->module==config('module.service-purchase.key') && $order->processor_id==Auth::guard()->user()->id ) {

        }
        else{
            return redirect()->back()->withErrors('Không tìm thấy cuộc hội thoại');
        }

        $conversation = Conversation::where('ref_id', $order->id)->first();

        if ($conversation) {


            if(($order->processor_id==Auth::guard()->user()->id)){
                $conversation->processor_id = Auth::guard()->user()->id;
            }
            $conversation->save();

        }
        else {

            $conversation = Conversation::create([
                'ref_id' => $order->id,
                'author_id' => $order->author_id,
                'type' => 1
            ]);

            if ($order->processor_id == Auth::guard()->user()->id ) {
                $conversation->processor_id = Auth::guard()->user()->id;
            }
            $conversation->save();
        }


        $image = "";
        if ($request->hasFile('image')) {

            //upload image
            $input['image'] = Files::upload_image($request->image);
        }

        Inbox::create([
            'user_id' => Auth::guard()->user()->id,
            'message' => $request->message,
            'image' => $image,
            'conversation_id' => $conversation->id,
            'seen' => "\"" . Auth::guard()->user()->id . "\"|"

        ]);
        //active log active
        ActivityLog::add($request, 'Gửi tin nhắn thành công '.config('module.service-purchase.key').' #'.$id  );
        return redirect()->back()->with('success', 'Gửi tin nhắn thành công');
    }

    public function postSuccess(Request $request, $id){

        if(!Auth::user()->can('service-purchase-auto-success')){
            return redirect()->back()->withErrors('Không có quyền truy cập');
        }

        DB::beginTransaction();
        try {

            //tìm lệnh rút
            $data = Order::where('module', '=', config('module.service-purchase.key'))
                ->with('item_ref')
                ->where(function ($query) {
                    $query->orWhere('status', "1");
                    $query->orWhere('status', "2");
                    $query->orWhere('status', "7");
                    $query->orWhere('status', "9");
                })
                ->lockForUpdate()
                ->findOrFail($id);

            //check nếu là dịch vu auto thì không thể tiếp nhận

            $input_auto = $data->gate_id;
            if ( $input_auto!= "1") {
                DB::rollback();
                return redirect()->back()->withErrors('Không thể hoàn tất dịch vụ đang là thủ công');
            }

            $data->status = 4;//hoàn thành
            $data->content = "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!";
            $data->updated_at = Carbon::now();
            $data->process_at = Carbon::now();//Thời gian xác nhận đơn hàng
            $data->save();

            //set tiến độ tiếp nhận
            OrderDetail::create([
                'order_id' => $data->id,
                'module' => config('module.service-workflow.key'),
                'author_id' => Auth::guard()->user()->id,
                'content' =>  "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!",
                'status' => "4",
            ]);

            if ($data->idkey == 'roblox_gem_pet' || $data->idkey == 'gem_unist_auto' || $data->idkey == 'unist_auto' || $data->idkey == 'huge_99_auto'
                || $data->idkey == 'huge_psx_auto' || $data->idkey == 'pet_99_auto' || $data->idkey == 'item_pet_go_auto'){
                    $roblox_order = Roblox_Order::query()->with('order')->where('order_id',$data->id)->lockForUpdate()->first();

                if (!isset($roblox_order)) {
                    DB::rollback();
                    return redirect()->back()->withErrors('Không tìm thấy đơn hàng roblox');
                }

                $roblox_order->status = 'danhan';
                $roblox_order->save();
            }

            //active log active
            ActivityLog::add($request, "Đã tích thành công yêu cầu dịch vụ #".$data->id  );
            DB::commit();

            if($data->url!=""){
                $messageBot = config('module.service-purchase-auto.status.'.$data->status);

                if ($data->idkey == 'roblox_gem_pet' || $data->idkey == 'huge_99_auto' || $data->idkey == 'gem_unist_auto' || $data->idkey == 'unist_auto'
                    || $data->idkey == 'huge_psx_auto' || $data->idkey == 'pet_99_auto' || $data->idkey == 'item_pet_go_auto'){
                    $this->dispatch(new App\Jobs\CallbackOrderRobloxBuyGemPet($data,$roblox_order->status,__('Thành công')));
//                    $this->callbackToShopRoblox($data,$roblox_order->status,__('Thành công'));
                }else{
                    $this->callbackToShop($data,$messageBot);
                }

            }
            return redirect()->back()->with('success', "Đã từ chối thành công yêu cầu dịch vụ #".$data->id);

        }catch(\Exception $e)
        {
            DB::rollback();
            Log::error($e);
            return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');


        }
    }

    public function postLostItem(Request $request, $id){

        if(!Auth::user()->can('service-purchase-auto-lost-item')){
            return redirect()->back()->withErrors('Không có quyền truy cập');
        }

        DB::beginTransaction();
        try {

            //tìm lệnh rút
            $data = Order::where('module', '=', config('module.service-purchase.key'))
                ->with('item_ref')
                ->where('status',6)
                ->lockForUpdate()
                ->findOrFail($id);

            //check nếu là dịch vu auto thì không thể tiếp nhận

            if ( $data->gate_id != "1") {
                return redirect()->back()->withErrors('Không thể hoàn tất dịch vụ đang là thủ công');
            }


            if($request->is_refund==1){

                $data->status = 88;// Mất item hoàn tiền
                $data->content = "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!";
                $data->updated_at = Carbon::now();
                $data->save();

                //set tiến độ tiếp nhận
                OrderDetail::create([
                    'order_id' => $data->id,
                    'module' => config('module.service-workflow.key'),
                    'author' => Auth::guard()->user()->id,
                    'content' =>  "Mất item đã hoàn tiền",
                    'status' => 88,

                ]);
                //hoàn tiền cho khách hàng

                $userTransaction = User::where('id', $data->author_id)->lockForUpdate()->firstOrFail();

                if ($userTransaction->checkBalanceValid() == false) {
                    DB::rollback();
                    return redirect()->back()->withErrors('Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý');
                }

                $userTransaction->balance = $userTransaction->balance + $data->price;
                $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $data->price;

                $userTransaction->save();
                //tạo tnxs
                $txns = Txns::create([
                    'trade_type' => 'refund',//Hoàn tiền
                    'is_add' => '1',//Công tiền
                    'user_id' => $userTransaction->id,
                    'amount' => $data->price,
                    'real_received_amount' => $data->price,
                    'last_balance' => $userTransaction->balance,
                    'description' => 'Hoàn tiền mất item yêu cầu dich vụ #' . $data->id,
                    'order_id' => $data->id,
                    'ip' => $request->getClientIp(),
                    'status' => 1
                ]);

                //active log active
                ActivityLog::add($request, "Đã tích mất item có hoàn tiền yêu cầu dịch vụ #".$data->id  );
                DB::commit();

                if($data->url!=""){
                    $messageBot = config('module.service-purchase-auto.status.'.$data->status);
                    $this->callbackToShop($data,$messageBot);
                }
                return redirect()->back()->with('success', "Xử lý thành công yêu cầu dịch vụ #".$data->id);
            }
            if($request->is_refund==0){
                $data->status = 77;// Mất item không hoàn tiền
                $data->content = "Mất item không hoàn tiền";
                $data->updated_at = Carbon::now();
                $data->save();

                //set tiến độ tiếp nhận
                OrderDetail::create([
                    'order_id' => $data->id,
                    'module' => config('module.service-workflow.key'),
                    'author_id' => Auth::guard()->user()->id,
                    'content' =>  "Mất item đã hoàn tiền",
                    'status' => 77,

                ]);

                //active log active
                ActivityLog::add($request, "Đã tích mất item không hoàn tiền yêu cầu dịch vụ #".$data->id  );
                DB::commit();

                if($data->url!=""){
                    $messageBot = config('module.service-purchase-auto.status.'.$data->status);
                    $this->callbackToShop($data,$messageBot);
                }
                return redirect()->back()->with('success', "Xử lý thành công yêu cầu dịch vụ #".$data->id);
            }

            else{
                DB::rollBack();
                return redirect()->back()->withErrors('Vui lòng chọn hoàn tiền hoặc không hoàn tiền');
            }


        }catch(\Exception $e)
        {
            DB::rollback();
            Log::error($e);

            return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');


        }
    }

    public function postRecallback(Request $request){

        $input=explode(',',$request->id);
        $data = Order::where('module', '=', config('module.service-purchase.key'))
            ->where(function($q){
                $q->orWhere('status','!=',  1);
                $q->orWhere('status','!=',  2);
            })
            ->whereIn('id',$input)->get();

        foreach ($data?$data:[] as $item){

            if($item->url!=""){

                $messageBot = config('module.service-purchase-auto.status.'.$item->status);

                if ($item->idkey == 'roblox_gem_pet' || $item->idkey == 'huge_99_auto' || $item->idkey == 'gem_unist_auto' || $item->idkey == 'unist_auto'
                    || $item->idkey == 'huge_psx_auto' || $item->idkey == 'pet_99_auto' || $item->idkey == 'item_pet_go_auto'){

                    $roblox_order = Roblox_Order::query()->with('order')->where('order_id',$item->id)->first();

                    $message = '';
                    if ($item->status == 4){
                        $message = 'Thành công';
                    }elseif ($item->status == 5){
                        $message = 'Không thành công - Can not found player';
                    }

                    $this->dispatch(new CallbackOrderRobloxBuyGemPet($item,$roblox_order->status,$message));

                }else{
                    $this->callbackToShop($item,$messageBot);
                }
            }

        }

        //active log active
        ActivityLog::add($request, "Đã recallback dịch vụ tự động #".json_encode($data,JSON_UNESCAPED_UNICODE));

        return redirect()->back()->with('success', "Đã callback thành công");

    }

    public function recharge(Request $request){

        $input=explode(',',$request->id);

        foreach ($input??[] as $idOrderNeedRecharge){
            // Start transaction!
            DB::beginTransaction();
            try {

                $order = Order::where('module', '=', config('module.service-purchase.key'))
                    ->where('id',$idOrderNeedRecharge)
                    ->lockForUpdate()
                    ->first();

                if(!$order){
                    DB::rollback();
                    continue;
                }

                if($order->status!= 7){
                    DB::rollback();
                    continue;
                }
                if($order->idkey == 'roblox_buyserver' || $order->idkey == 'roblox_buygamepass'){
                    if (isset($order->payment_type) && in_array($order->payment_type,config('module.service-purchase-auto.rbx_api'))){
                        DB::rollback();
                        continue;
                    }
                }

                $input_auto= $order->gate_id;

                if($input_auto ==1 ){

                    if($order->idkey == 'roblox_buyserver' || $order->idkey == 'roblox_buygamepass'){
                        $order->status = 1;
                        $order->save();

                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 1,
                            'content' => "Đơn đã được thực hiện lại qua NCC",
                        ]);

                        DB::commit();
                        $this->dispatch(new RobloxJob($order->id));
                        continue;
                    }
                    elseif ($order->idkey == 'roblox_gem_pet' || $order->idkey == 'huge_99_auto' || $order->idkey == 'gem_unist_auto'
                        || $order->idkey == 'unist_auto' || $order->idkey == 'huge_psx_auto' || $order->idkey == 'pet_99_auto' || $order->idkey == 'item_pet_go_auto'){
                        $order->status = 1;
                        $order->save();
                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.service-workflow.key'),
                            'status' => 1,
                            'content' => "Đơn đã được thực hiện lại qua NCC",
                        ]);

                        $roblox_order = Roblox_Order::query()->with('order')->where('order_id',$order->id)->lockForUpdate()->first();
                        $roblox_order->status = 'chuanhan';
                        if ($order->idkey == 'unist_auto' || ($order->idkey == 'gem_unist_auto' && $roblox_order->ver == 1)){
                            $roblox_order->bot_handle = null;
                        }
                        $roblox_order->save();
                        DB::commit();
                    }


                }
            } catch (\Exception $e) {
                DB::rollback();
                \Log::error( $e);
                continue;
            }
        }
        //active log active
        ActivityLog::add($request, "Đã recallback dịch vụ tự động #".json_encode($input,JSON_UNESCAPED_UNICODE));

        return response()->json([
            'status'=>1,
            'message'=>'Các đơn đã được nạp lại thàng công',

        ]);

    }

    public function robloxPsx(Request $request){

        $txt_files = $request->txt_file; // 'data' là tên của textarea trong form

        $lines = explode("\n", $txt_files); // Tách dữ liệu thành các dòng

        $nummbers = [];
        $statusNummbers = [];
        foreach ($lines as $line) {
            $fields = explode(" : ", $line); // Tách dữ liệu trong mỗi dòng thành các trường
            if (!empty($fields)){
                array_push($nummbers,$fields[0]);
                if (count($fields) == 4){
                    $cleanedLine = str_replace("\r", "", $fields[3]);
                    $statusNummbers[$fields[0]] = $cleanedLine;
                }
            }
            // Kiểm tra xem dòng có đúng số trường mà bạn mong đợi hay không
        }

        $input=explode(',',$request->id);

        $checkOrders = [];

        foreach ($input??[] as $idOrderNeedRecharge){
            // Start transaction!
            DB::beginTransaction();
            try {

                $order = Order::where('module', '=', config('module.service-purchase.key'))
                    ->where(function($q){
                        $q->orWhere('idkey', '=','roblox_gem_pet');
                        $q->orWhere('idkey', '=','huge_psx_auto');
                        $q->orWhere('idkey', '=','huge_99_auto');
                        $q->orWhere('idkey', '=','unist_auto');
                        $q->orWhere('idkey', '=','gem_unist_auto');
                        $q->orWhere('idkey', '=','robux_premium_auto');
                        $q->orWhere('idkey', '=','pet_99_auto');
                        $q->orWhere('idkey', '=','item_pet_go_auto');
                    })
                    ->where('status',2)
                    ->where('id',$idOrderNeedRecharge)
                    ->lockForUpdate()
                    ->first();

                if(!$order){
                    DB::rollback();
                    continue;
                }

                if($order->status!= 2){
                    DB::rollback();
                    continue;
                }

                $input_auto = $order->gate_id;

                if($input_auto ==1 ){

                    if (!empty($nummbers)){
                        $request_id_customer = $order->request_id_customer;
                        if (in_array($request_id_customer,$nummbers)){
                            if (!empty($statusNummbers[$request_id_customer]) && $statusNummbers[$request_id_customer] == 'OK'){
                                //cập nhật trạng thái của purchase
                                $order->status = 4;
                                $order->process_at = Carbon::now();//thời gian xác nhận đơn hàng
                                $order->save();
                                //set tiến độ
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'module' => config('module.service-workflow.key'),
                                    'status' => 4,
                                    'content' => "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!",
                                ]);

                                $roblox_order = Roblox_Order::query()->with('order')->where('order_id',$order->id)->lockForUpdate()->first();
                                $roblox_order->status = 'danhan';
                                $roblox_order->save();

                                DB::commit();
                                $checkOrders[$request_id_customer] = 'OK';
                                continue;
                            }
                            else{
                                $order->status = 9;
                                $order->save();
                                //set tiến độ
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'module' => config('module.service-workflow.key'),
                                    'status' => 9,
                                    'content' => "Đơn hàng đã được xử lý vui lòng kiểm tra lại",
                                ]);

                                DB::commit();

                                $checkOrders[$request_id_customer] = 'Error';
                                continue;
                            }
                        }
                    }

                    $order->status = 1;
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 1,
                        'author_id' => Auth::user()->id,
                        'content' => "Chuyển trạng thái đơn hàng thành công",
                    ]);

                    $roblox_order = Roblox_Order::query()->with('order')->where('order_id',$order->id)->lockForUpdate()->first();
                    $roblox_order->status = 'chuanhan';
                    $roblox_order->save();

                    DB::commit();
                }

            } catch (\Exception $e) {
                DB::rollback();
                \Log::error( $e);
                continue;
            }
        }
        //active log active

        if (!empty($checkOrders)){
// lấy thông tin IP và user_angent người dùng

            $ip = $request->getClientIp();
            $user_agent = $request->userAgent();
            $message = "Thời gian: <b>" . Carbon::now()->format('d-m-Y H:i:s') . "</b>";
            $message .= "\n";
            $message .= '<b>'.Auth::user()->username."</b> Thay đổi trạng thái đơn hàng gem PSX phát hiện một số đơn hàng tool đã xử lý: ";
            $message .= "\n";
            $message .= "Nội dung:";
            $message .= "\n";

            foreach ($checkOrders as $key => $checkOrder){
                if ($checkOrder == 'OK'){
                    $message .= '   - Đơn hàng:  <b>'.$key.'</b> - <b>hoàn thành</b>';
                    $message .= "\n";
                }else{
                    $message .= '   - Đơn hàng:  <b>'.$key.'</b> - <b>thất bại</b>';
                    $message .= "\n";
                }
            }

            $message .= "\n";
            $message .= "IP: <b>" . $ip . "</b>";
            $message .= "\n";
            $message .= "User_agent: <b>" . $user_agent . "</b>";
            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
        }

//        unset($input['txt_file']);

//        ActivityLog::add($request, "Đã recallback dịch vụ tự động #".json_encode($input,JSON_UNESCAPED_UNICODE));

        return redirect()->back()->with('success', 'Các đơn đã được nạp lại thàng công');

    }

    public function robloxUnit(Request $request){

        $input=explode(',',$request->id);

        $checkOrders = [];

        foreach ($input??[] as $idOrderNeedRecharge){
            // Start transaction!
            DB::beginTransaction();
            try {

                $order = Order::where('module', '=', config('module.service-purchase.key'))
                    ->where(function($q){
                        $q->orWhere('idkey', '=','gem_unist_auto');
                    })
                    ->where('gate_id',1)
                    ->where('status',9)
                    ->where('id',$idOrderNeedRecharge)
                    ->lockForUpdate()
                    ->first();

                if(!$order){
                    DB::rollback();
                    continue;
                }

                if($order->status!= 9){
                    DB::rollback();
                    continue;
                }

                $order->status = 4;//hoàn thành
                $order->content = "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!";
                $order->updated_at = Carbon::now();
                $order->process_at = Carbon::now();//Thời gian xác nhận đơn hàng
                $order->save();

                //set tiến độ tiếp nhận
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'author_id' => Auth::guard()->user()->id,
                    'content' =>  "Hệ thống đã giao dịch xong ! Chúc bạn online vui vẻ!",
                    'status' => "4",
                ]);

                if ($order->idkey == 'roblox_gem_pet' || $order->idkey == 'gem_unist_auto' || $order->idkey == 'unist_auto' || $order->idkey == 'huge_99_auto'
                    || $order->idkey == 'huge_psx_auto' || $order->idkey == 'pet_99_auto' || $order->idkey == 'item_pet_go_auto'){
                    $roblox_order = Roblox_Order::query()->with('order')->where('order_id',$order->id)->lockForUpdate()->first();

                    if (!isset($roblox_order)) {
                        DB::rollback();
                        continue;
                    }

                    $roblox_order->status = 'danhan';
                    $roblox_order->save();
                }

                //active log active
                ActivityLog::add($request, "Đã tích thành công yêu cầu dịch vụ #".$order->id  );
                DB::commit();

                if($order->url!=""){
                    $messageBot = config('module.service-purchase-auto.status.'.$order->status);

                    if ($order->idkey == 'roblox_gem_pet' || $order->idkey == 'huge_99_auto' || $order->idkey == 'gem_unist_auto' || $order->idkey == 'item_pet_go_auto'
                        || $order->idkey == 'unist_auto' || $order->idkey == 'huge_psx_auto' || $order->idkey == 'pet_99_auto'){
                        $this->dispatch(new App\Jobs\CallbackOrderRobloxBuyGemPet($order,$roblox_order->status,__('Thành công')));
//                    $this->callbackToShopRoblox($data,$roblox_order->status,__('Thành công'));
                    }else{
                        $this->callbackToShop($order,$messageBot);
                    }

                }

            } catch (\Exception $e) {
                DB::rollback();
                \Log::error( $e);
                continue;
            }
        }
        //active log active

        if (!empty($checkOrders)){
// lấy thông tin IP và user_angent người dùng

            $ip = $request->getClientIp();
            $user_agent = $request->userAgent();
            $message = "Thời gian: <b>" . Carbon::now()->format('d-m-Y H:i:s') . "</b>";
            $message .= "\n";
            $message .= '<b>'.Auth::user()->username."</b> Thay đổi trạng thái đơn hàng gem PSX phát hiện một số đơn hàng tool đã xử lý: ";
            $message .= "\n";
            $message .= "Nội dung:";
            $message .= "\n";

            foreach ($checkOrders as $key => $checkOrder){
                if ($checkOrder == 'OK'){
                    $message .= '   - Đơn hàng:  <b>'.$key.'</b> - <b>hoàn thành</b>';
                    $message .= "\n";
                }else{
                    $message .= '   - Đơn hàng:  <b>'.$key.'</b> - <b>thất bại</b>';
                    $message .= "\n";
                }
            }

            $message .= "\n";
            $message .= "IP: <b>" . $ip . "</b>";
            $message .= "\n";
            $message .= "User_agent: <b>" . $user_agent . "</b>";
            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_roblox'));
        }

//        unset($input['txt_file']);

//        ActivityLog::add($request, "Đã recallback dịch vụ tự động #".json_encode($input,JSON_UNESCAPED_UNICODE));

        return redirect()->back()->with('success', 'Các đơn đã được nạp lại thàng công');

    }

    public function robloxUserid(Request $request){
        $input=explode(',',$request->id);

        foreach ($input??[] as $idOrderNeedRecharge){
            // Start transaction!

            try {

                $order = Order::where('module', '=', config('module.service-purchase.key'))
                    ->where('id',$idOrderNeedRecharge)
                    ->lockForUpdate()
                    ->first();

                if(!$order){
                    DB::rollback();
                    continue;
                }

                if($order->status!= 7){
                    DB::rollback();
                    continue;
                }

                $input_auto= $order->gate_id;

                if($input_auto ==1 && $order->idkey == 'roblox_buygamepass'){

                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 1,
                        'content' => "Lấy user id thành công",
                    ]);

                    $this->dispatch(new RobloxUserIdJob($order->id));
                    continue;


                }
            } catch (\Exception $e) {
                DB::rollback();
                \Log::error( $e);
                continue;
            }
        }
        //active log active
        ActivityLog::add($request, "Đã recallback dịch vụ tự động #".json_encode($input,JSON_UNESCAPED_UNICODE));

        return response()->json([
            'status'=>1,
            'message'=>'Các đơn đã được lấy user id thàng công',

        ]);
    }

    public function postRefund(Request $request,$id){

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'note' => 'required|min:10|max:500',
                'btn_submit_refund' => 'required',
            ],[
                'id.required' => __("Vui lòng nhập mã đơn hàng"),
                'note.required' => "Vui lòng nhập nội dung lỗi",
                'note.min' => "Nội dung phải ít nhất 10 ký tự",
                'note.max' => "Nội dung phải không quá 500 ký tự",
                'btn_submit_refund.required' => __("Vui lòng chọn loại hoàn tiền"),
            ]);

            if($validator->fails()){
                DB::rollback();
                return redirect()->back()->withErrors(__($validator->errors()->first()));

            }

            $data = Order::query()
                ->where('module', config('module.service-purchase.key'))
                ->where('status', 4)
                ->where(function($q){
                    $q->orWhere('idkey', '=','roblox_gem_pet');
                    $q->orWhere('idkey', '=','huge_99_auto');
                    $q->orWhere('idkey', '=','unist_auto');
                    $q->orWhere('idkey', '=','gem_unist_auto');
                    $q->orWhere('idkey', '=','huge_psx_auto');
                    $q->orWhere('idkey', '=','pet_99_auto');
                    $q->orWhere('idkey', '=','item_pet_go_auto');
                })
                ->where('id',$request->get('id'))
                ->lockForUpdate()->first();

            if (!isset($data)){
                DB::rollback();
                return redirect()->back()->withErrors(__('Không tìm thấy đơn giao dịch'));
            }

            //Lưu trạng thái
            $data->status = 5;
            $data->save();

            //Lưu tiến độ.
            if ($request->get('btn_submit_refund') == 'refund'){
                $note = "Có hoàn tiền";
            }else{
                $note = "Không hoàn tiền";
            }
            //set tiến độ hoan tien
            OrderDetail::create([
                'order_id' => $data->id,
                'module' => config('module.service-workflow.key'),
                'author_id' => Auth::user()->id,
                'status' => 5,
                'content' => $request->note.' ('.$note.')',
            ]);

            if ($request->get('btn_submit_refund') == 'refund'){

                //Cộng tiền cho khách hàng
                $userTransaction = User::where('id', $data->author_id)->lockForUpdate()->firstOrFail();

                if ($userTransaction->checkBalanceValid() == false) {

                    DB::rollback();
                    return redirect()->back()->withErrors(__('Tài khoản khách hàng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý'));

                }

                $userTransaction->balance = $userTransaction->balance + $data->price;
                $userTransaction->balance_in = $userTransaction->balance_in + $data->price;
                $userTransaction->save();

                //tạo tnxs
                $txns = Txns::create([
                    'trade_type' => 'refund',//Hoàn tiền
                    'is_add' => '1',//Công tiền
                    'user_id' => $userTransaction->id,
                    'amount' => $data->price,
                    'real_received_amount' => $data->price,
                    'last_balance' => $userTransaction->balance,
                    'description' => 'Hoàn tiền thủ công khi chuyển trạng thái hoàn tất (4) sang thất bại(5) #' . $data->id,
                    'order_id' => $data->id,
                    'ip' => $request->getClientIp(),
                    'status' => 1
                ]);
            }

            ActivityLog::add($request, 'Chuyển đổi trạng thái thành công đơn hàng #'.$data->id);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error( $e);
            DB::rollback();
            return redirect()->back()->withErrors(__('Có lỗi phát sinh.Xin vui lòng thử lại !'));

        }
        // Commit the queries!
        DB::commit();
        //active log active

        return redirect()->back()->with('success', 'Chuyển đổi trạng thái thành công');

    }

    public function postDeleteAllAuto(Request $request)
    {

        if(!Auth::user()->id == 301 && !Auth::user()->id == 5551 && !Auth::user()->id == 198544){
            return redirect()->back()->withErrors(__("Khong co quyen truy cap"));
        }

        $orders = Order::query()
            ->where('idkey','gem_unist_auto')
            ->where('status',9)
            ->get();

        foreach ($orders as $order){
            // Start transaction!
            DB::beginTransaction();
            try {

                $data = Order::query()
                    ->where('idkey','gem_unist_auto')
                    ->where('status',9)
                    ->where('id',$order->id)
                    ->lockForUpdate()
                    ->first();

                if (!isset($data)) {
                    DB::rollback();
                    continue;
                }

                $mistake_by = 2;
                $note = 'Game Error';
                //nếu đang thực hiện thì update người mua
                $data->update([
                    'status' => 3,
                    'content' => $note,
                ]);//trạng thái từ chối

                //set tiến độ
                OrderDetail::create([
                    'order_id'=>$data->id,
                    'module' => config('module.service-workflow.key'),
                    'author_id'=>Auth::guard()->user()->id,
                    'title' =>  __('Từ chối'),
                    'status' => 3,
                    'content' => __("[Lỗi bởi: " ). $mistake_by . "] - " . $note,
                ]);

                //check và edit thông tin giao dich tự động && custom service
                $roblox_order = Roblox_Order::query()->with('order')->where('order_id',$data->id)->lockForUpdate()->first();
                $roblox_order->status = 'dahoantien';
                $roblox_order->save();

                //hoàn tiền cho khách hàng
                $userTransaction = User::where('id', $data->author_id)->lockForUpdate()->firstOrFail();
                $userTransaction->balance = $userTransaction->balance + $data->price;
                $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $data->price;

                $userTransaction->save();

                //tạo tnxs
                $txns = Txns::create([
                    'trade_type' => 'refund',//Hoàn tiền
                    'is_add' => '1',//Công tiền
                    'user_id' => $userTransaction->id,
                    'amount' => $data->price,
                    'real_received_amount' => $data->price,
                    'last_balance' => $userTransaction->balance,
                    'description' => 'Hoàn tiền từ chối yêu cầu dich vụ #' . $data->id,
                    'order_id' => $data->id,
                    'ip' => $request->getClientIp(),
                    'status' => 1
                ]);

                //Callback to shop
                if($data->url!=""){
                    $messageBot = config('module.service-purchase-auto.status.'.$data->status);

                    if ($data->idkey == 'roblox_gem_pet' || $data->idkey == 'huge_psx_auto' || $data->idkey == 'gem_unist_auto' || $data->idkey == 'unist_auto'
                        || $data->idkey == 'huge_99_auto' || $data->idkey == 'pet_99_auto' || $data->idkey == 'item_pet_go_auto'){
                        $this->dispatch(new CallbackOrderRobloxBuyGemPet($data,$roblox_order->status,__('Đơn hàng bị hủy từ đại lý')));
                    }else{
                        $this->callbackToShop($data,$messageBot);
                    }
                }

            } catch (\Exception $e) {
                DB::rollback();
                Log::error( $e);
                continue;
            }
            // Commit the queries!
            DB::commit();
        }

        return redirect()->back()->with('success', __("Đã từ chối thành công yêu cầu dịch vụ #"));
    }

    public function postDeleteDescAuto(Request $request)
    {

        if(!Auth::user()->id == 301 && !Auth::user()->id == 5551){
            return redirect()->back()->withErrors(__("Khong co quyen truy cap"));
        }

        $desc = 'Aquatitan Speakerman';
        $orders = Order::query()
            ->where('idkey','gem_unist_auto')
            ->where('status',1)
            ->whereIn('author_id',[198777,198751,198723,198564,198449,198968])
            ->where('price',40000)
            ->where('description', 'LIKE', '%' . $desc . '%')
            ->get();

        foreach ($orders as $order){
            // Start transaction!
            DB::beginTransaction();
            try {

                $data = Order::with('item_ref','author','processor')
                    ->where('module', '=', config('module.service-purchase'))
                    ->where('status',1)
                    ->whereIn('author_id',[198777,198751,198723,198564,198449,198968])
                    ->where('id',$order->id)
                    ->lockForUpdate()
                    ->first();

                if (!isset($data)) {
                    DB::rollback();
                    continue;
                }

                $mistake_by = 0;
                $note = 'Transaction error';
                //nếu đang thực hiện thì update người mua
                $data->update([
                    'status' => 3,
                    'content' => $note,
                ]);//trạng thái từ chối

                //set tiến độ
                OrderDetail::create([
                    'order_id'=>$data->id,
                    'module' => config('module.service-workflow.key'),
                    'author_id'=>Auth::guard()->user()->id,
                    'title' =>  __('Từ chối'),
                    'status' => 3,
                    'content' => __("[Lỗi bởi: " ). $mistake_by . "] - " . $note,
                ]);

                //check và edit thông tin giao dich tự động && custom service
                $roblox_order = Roblox_Order::query()->with('order')->where('order_id',$data->id)->lockForUpdate()->first();
                $roblox_order->status = 'dahoantien';
                $roblox_order->save();

                //hoàn tiền cho khách hàng
                $userTransaction = User::where('id', $data->author_id)->lockForUpdate()->firstOrFail();
                $userTransaction->balance = $userTransaction->balance + $data->price;
                $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $data->price;

                $userTransaction->save();

                //tạo tnxs
                $txns = Txns::create([
                    'trade_type' => 'refund',//Hoàn tiền
                    'is_add' => '1',//Công tiền
                    'user_id' => $userTransaction->id,
                    'amount' => $data->price,
                    'real_received_amount' => $data->price,
                    'last_balance' => $userTransaction->balance,
                    'description' => 'Hoàn tiền từ chối yêu cầu dich vụ #' . $data->id,
                    'order_id' => $data->id,
                    'ip' => $request->getClientIp(),
                    'status' => 1
                ]);

                //Callback to shop
                if($data->url!=""){
                    $messageBot = config('module.service-purchase-auto.status.'.$data->status);

                    if ($data->idkey == 'roblox_gem_pet' || $data->idkey == 'huge_psx_auto' || $data->idkey == 'gem_unist_auto' || $data->idkey == 'unist_auto'
                        || $data->idkey == 'huge_99_auto' || $data->idkey == 'pet_99_auto' || $data->idkey == 'item_pet_go_auto'){
                        $this->dispatch(new CallbackOrderRobloxBuyGemPet($data,$roblox_order->status,__('Đơn hàng bị hủy từ đại lý')));
                    }else{
                        $this->callbackToShop($data,$messageBot);
                    }
                }

            } catch (\Exception $e) {
                DB::rollback();
                Log::error( $e);
                continue;
            }
            // Commit the queries!
            DB::commit();
        }

        return redirect()->back()->with('success', __("Đã từ chối thành công yêu cầu dịch vụ #"));
    }

    public function callbackToShop(Order $order,$messageBot)
    {

        $url = $order->url;

        $data = array();

        $data['status'] = $order->status;

        $data['message'] = $messageBot;

        if (strpos($url, 'https://backend-th.tichhop.pro') > -1 || strpos($url, 'http://s-api.backend-th.tichhop.pro') > -1){
            $data['message'] = config('lang.'.$messageBot)??$messageBot;
        }

        $data['input_auto'] = 1;

        if ($order->status ==4){
            $data['process_at'] = strtotime($order->process_at);
        }

        //debug thì mở cái này
        $myfile = fopen(storage_path() . "/logs/check_order-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
        $txt = Carbon::now() . " :" . $order;
        fwrite($myfile, $txt);
        fclose($myfile);

        $dataPost = http_build_query($data);

        try{

            for ($i=0;$i<3;$i++){
                $ch = curl_init();

                //data dạng get
                if (strpos($url, '?') !== FALSE) {
                    $url = $url . "&" . $dataPost;
                } else {
                    $url = $url . "?" . $dataPost;
                }

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);

                $resultRaw=curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                //debug thì mở cái này
                $myfile = fopen(storage_path() . "/logs/curl_callback-service-to-shop-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
                $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
                fwrite($myfile, $txt);
                fclose($myfile);

                if($httpcode==200){

                    if(strpos($resultRaw, "Có lỗi phát sinh.Xin vui lòng thử lại") > -1){
                        continue;
                    }
                    break;
                }
            }
        }
        catch (\Exception $e){
            \Log::error($e);
        }

    }

    public function callbackToShopRoblox(Order $order,$statusBot,$message = '',$image = false)
    {

        $url = $order->url;

        $data = array();

        $data['status'] = $order->status;

        $data['message'] = $statusBot;

        $data['message_daily'] = $message;

        $data['price'] = $order->price;

        $data['price_base'] = $order->price_base;

        $data['image'] = $image;

        $data['input_auto'] = 1;

        if ($order->status ==4){
            $data['process_at'] = strtotime($order->process_at);
        }

        try{

            for ($i=0;$i<3;$i++){
                if(is_array($data)){
                    $dataPost = http_build_query($data);
                }else{
                    $dataPost = $data;
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
                $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                curl_setopt($ch, CURLOPT_REFERER, $actual_link);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                $resultRaw = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                //debug thì mở cái này
                $myfile = fopen(storage_path() . "/logs/curl_callback-service-to-shop-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
                $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
                fwrite($myfile, $txt);
                fclose($myfile);

                if($httpcode==200){

                    if(strpos($resultRaw, "Có lỗi phát sinh.Xin vui lòng thử lại") > -1){
                        continue;
                    }
                    break;
                }
            }
        }
        catch (\Exception $e){
            \Log::error($e);
        }

    }

    public function rechargeGamepass(Request $request){

        $input=explode(',',$request->id);

        foreach ($input??[] as $idOrderNeedRecharge){
            // Start transaction!
            DB::beginTransaction();
            try {

                $order = Order::where('module', '=', config('module.service-purchase.key'))
                    ->where('id',$idOrderNeedRecharge)
                    ->where('idkey','roblox_buygamepass')
                    ->lockForUpdate()
                    ->first();

                if(!$order){
                    DB::rollback();
                    continue;
                }

                if($order->status!= 9){
                    if (($order->status == 1 || $order->status == 2) && (Auth::user()->id == 5551 || Auth::user()->id == 28 || Auth::user()->id == 301 || Auth::user()->id == 198988)){

                    }else{
                        DB::rollback();
                        continue;
                    }
                }

                $order->status = 7;
                $order->save();

                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'status' => 1,
                    'content' => "Đơn đã được chuyển trạng thái thành công",
                ]);

                DB::commit();

            } catch (\Exception $e) {
                DB::rollback();
                \Log::error( $e);
                continue;
            }
        }
        //active log active
        ActivityLog::add($request, "Đã chuyển trạng thái dịch vụ tự động #".json_encode($input,JSON_UNESCAPED_UNICODE));

        return response()->json([
            'status'=>1,
            'message'=>'Các đơn đã được chuyển trạng thái thàng công',

        ]);

    }

    public function postPengiriman(Request $request)
    {
        if (!Auth::user()->can('service-purchase-edit-pengiriman')){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }
        if (!$request->filled('pengiriman_id')){
            return redirect()->back()->withErrors(__('Không tìm thấy đơn hàng'));
        }
        $id = $request->get('pengiriman_id');
        DB::beginTransaction();
        try {

            $data = Order::query()
                ->with(['shop','item_ref'])
                ->whereIn('idkey',["roblox_buygamepass","roblox_buyserver"])
                ->where('module', '=', config('module.service-purchase'))
                ->whereIn('status', [4,10])
                ->lockForUpdate()
                ->findOrFail($id);

            if (!$request->filled('edit_id_pengiriman')) {
                DB::rollback();
                return redirect()->back()->withErrors(__('Vui lòng nhập đầy đủ thông tin id lô hàng và account lô hàng'));
            }

            $edit_id_pengiriman = $request->get('edit_id_pengiriman');

            $order_detail = OrderDetail::query()
                ->where('module','pengiriman')
                ->where('order_id',$data->id)
                ->first();

            if (!isset($order_detail)){

                $roblox_order = Roblox_Order::query()
                    ->with('bot')
                    ->where('order_id',$data->id)
                    ->first();
                if (isset($roblox_order) && isset($roblox_order->bot)){
                    $aBot = $roblox_order->bot;
                    OrderDetail::create([
                        'order_id' => $data->id,
                        'module' => 'pengiriman',
                        'title' => $edit_id_pengiriman??'',
                        'description' => $aBot->acc??'',
                        'content' => "Nhập hàng",
                        'status' => 4,
                    ]);
                }
            }else{
                $order_detail->title = $edit_id_pengiriman;
                $order_detail->save();
            }

            ActivityLog::add($request,__("Chỉnh sửa thông tin lô hàng thành công").config('module.service-purchase.key').' #'.$id );

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return redirect()->back()->withErrors(__('Có lỗi phát sinh.Xin vui lòng thử lại !'));
        }

        // Commit the queries!
        DB::commit();
        return redirect()->back()->with('success', __("Chỉnh sửa thông tin lô hàng thành công #") . $data->id);

    }

    public function postSwichRbxApi(Request $request){

        $input=explode(',',$request->id);

        if (!$request->filled('url_type')) {
            DB::rollback();
            return redirect()->back()->withErrors(__('Vui lòng nhập đầy đủ thông tin id lô hàng và account lô hàng'));
        }

        $url_type = $request->get('url_type');

        foreach ($input??[] as $idOrderNeedRecharge){
            DB::beginTransaction();
            try {

                $data = Order::query()
                    ->with(['shop','item_ref'])
                    ->where('id',$idOrderNeedRecharge)
                    ->whereIn('idkey',["roblox_buygamepass","roblox_buyserver"])
                    ->where('module', '=', config('module.service-purchase'))
                    ->whereIn('status', [7,9])
                    ->lockForUpdate()
                    ->first();

                if (!$data) {
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Không tìm thấy đơn hàng'));
                }

                if (!isset($data->item_ref)) {
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Không tìm thấy dịch vụ'));
                }

                $data->payment_type = $url_type;
                $data->save();

                DB::commit();
                ActivityLog::add($request,__("Chuyển rbx đơn hàng thành công").config('module.service-purchase.key').' #'.$idOrderNeedRecharge );

            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);

                return redirect()->back()->withErrors(__('Có lỗi phát sinh.Xin vui lòng thử lại !'));
            }
        }

        return redirect()->back()->with('success', __("Chỉnh sửa thông tin lô hàng thành công #") . $data->id);
    }

    public function postSwichDaily(Request $request){

        $input=explode(',',$request->id);
        $module = config('module.service-workflow.key');
        foreach ($input??[] as $idOrderNeedRecharge){
            DB::beginTransaction();
            try {

                $data = Order::query()
                    ->with(['shop','item_ref'])
                    ->where('id',$idOrderNeedRecharge)
                    ->whereIn('idkey',["roblox_buygamepass","roblox_buyserver"])
                    ->where('module', '=', config('module.service-purchase'))
                    ->whereIn('payment_type',config('module.service-purchase-auto.rbx_api'))
                    ->whereIn('status', [7,9])
                    ->lockForUpdate()
                    ->first();

                if (!$data) {
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Không tìm thấy đơn hàng'));
                }

                if (!isset($data->item_ref)) {
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Không tìm thấy dịch vụ'));
                }

                $item_ref = $data->item_ref;


                if ($item_ref->url_type == 1){
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Dịch vụ cấp hình phương thức thanh toán daily'));
                }
                if (isset($item_ref->url_type)){
                    $url = '/orders/cancel';
                    $method = "POST";
                    $dataSend = array();
                    $dataSend['orderId'] = $data->request_id_customer;
                    $payment_type = $data->payment_type;
                    $result_Api = DirectAPI::_cancelProduct($url,$dataSend,$method,$payment_type);

                    if (isset($result_Api) && isset($result_Api->status) && $result_Api->status == 1){
                        //set tiến độ
                        OrderDetail::create([
                            'order_id' => $data->id,
                            'module' => $module,
                            'content' => "Đã gửi đơn sang RBX API",
                            'status' => 2,
                        ]);
                    }
                }
                
                $data->status = 7;
                $data->payment_type = 1;
                $data->save();

                DB::commit();
                ActivityLog::add($request,__("Chuyển đơn hàng về daily thành công").config('module.service-purchase.key').' #'.$idOrderNeedRecharge );

            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);

                return redirect()->back()->withErrors(__('Có lỗi phát sinh.Xin vui lòng thử lại !'));
            }
        }

        return redirect()->back()->with('success', __("Chỉnh sửa thông tin lô hàng thành công #") . $data->id);
    }

    public function rechargeRbx(Request $request){

        $input=explode(',',$request->id);

        foreach ($input??[] as $idOrderNeedRecharge){
            // Start transaction!
            DB::beginTransaction();
            try {

                $order = Order::where('module', '=', config('module.service-purchase.key'))
                    ->whereIn('idkey',["roblox_buygamepass","roblox_buyserver"])
                    ->where('id',$idOrderNeedRecharge)
                    ->whereIn('status',[7,9])
                    ->lockForUpdate()
                    ->first();

                if(!$order){
                    DB::rollback();
                    continue;
                }

                if($order->idkey == 'roblox_buyserver' || $order->idkey == 'roblox_buygamepass'){
                    if (isset($order->payment_type) && in_array($order->payment_type,config('module.service-purchase-auto.rbx_api'))){}
                    else{
                        DB::rollback();
                        continue;
                    }
                }

                $input_auto= $order->gate_id;

                if($input_auto ==1 ){

                    $order->status = 1;
                    $order->save();

                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 1,
                        'content' => "Đơn đã được thực hiện lại qua NCC",
                    ]);

                    DB::commit();
                    $this->dispatch(new RobloxJob($order->id));
                    continue;

                }
            } catch (\Exception $e) {
                DB::rollback();
                \Log::error( $e);
                continue;
            }
        }
        //active log active
        ActivityLog::add($request, "Đã recallback dịch vụ tự động #".json_encode($input,JSON_UNESCAPED_UNICODE));

        return redirect()->back()->with('success', "Các đơn đã được nạp lại thàng công");
    }

    public function postPengirimanAll(Request $request)
    {
        if (!Auth::user()->can('service-purchase-edit-pengiriman')){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }
        if (!$request->filled('edit_id_pengiriman')){
            return redirect()->back()->withErrors(__('Không tìm thấy đơn hàng'));
        }

        if (!$request->filled('edit_id_pengiriman')) {
            DB::rollback();
            return redirect()->back()->withErrors(__('Vui lòng nhập đầy đủ thông tin id lô hàng và account lô hàng'));
        }

        $edit_id_pengiriman = $request->get('edit_id_pengiriman');

        $input=explode(',',$request->id_pengiriman);


        foreach ($input??[] as $idOrderNeedRecharge){
            DB::beginTransaction();
            try {

                $data = Order::query()
                    ->where('id',$idOrderNeedRecharge)
                    ->with(['shop','item_ref'])
                    ->whereIn('idkey',["roblox_buygamepass","roblox_buyserver"])
                    ->where('module', '=', config('module.service-purchase'))
                    ->whereIn('status', [4,10])
                    ->lockForUpdate()
                    ->first();


                if (!isset($data)){
                    DB::rollback();
                    continue;
                }

                $order_detail = OrderDetail::query()
                    ->where('module','pengiriman')
                    ->where('order_id',$data->id)
                    ->first();

                if (!isset($order_detail)){

                    $roblox_order = Roblox_Order::query()
                        ->with('bot')
                        ->where('order_id',$data->id)
                        ->first();
                    if (isset($roblox_order) && isset($roblox_order->bot)){
                        $aBot = $roblox_order->bot;
                        OrderDetail::create([
                            'order_id' => $data->id,
                            'module' => 'pengiriman',
                            'title' => $edit_id_pengiriman??'',
                            'description' => $aBot->acc??'',
                            'content' => "Nhập hàng",
                            'status' => 4,
                        ]);
                    }
                }else{
                    $order_detail->title = $edit_id_pengiriman;
                    $order_detail->save();
                }

                // Commit the queries!
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);
                continue;
            }
        }

        ActivityLog::add($request,__("Chỉnh sửa thông tin lô hàng thành công").config('module.service-purchase.key').' #'.json_encode($input) );

        return redirect()->back()->with('success', __("Chỉnh sửa thông tin lô hàng thành công #"));

    }
}
