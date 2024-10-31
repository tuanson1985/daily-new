<?php

namespace App\Http\Controllers\Admin\Service;

use App;
use App\Exports\ExportData;
use App\Exports\OrdersExport;
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
use App\Models\Roblox_Bot;
use App\Models\Roblox_Order;
use App\Models\ServiceAccess;
use App\Models\Group;
use App\Models\Group_Item;
use App\Models\Item;
use App\Models\Shop;
use App\Models\SubItem;
use App\Models\Txns;
use App\Models\User;
use App\Models\UserAccess;
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

        $this->middleware('permission:service-purchase-list', ['only' => ['index']]);
        $this->middleware('throttle:10,1')->only('index');

        $this->module="service-purchase";
        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'.index'),
                'title' => __('Danh sách yêu cầu dịch vụ thủ công')
            ];
        }

    }

    public function index(Request $request)
    {
        $group_ids = [];
        $all_names = [];
        $service_access = [];

        $dataCategory = Item::where('module', '=', config('module.service.key'))
            ->where('status','1')
            ->where('gate_id',0)
            ->orderBy('title', 'asc');


        if (!Auth::user()->can('service-reception-all')) {
            //lấy các quyền được xem yêu cầu dịch vụ
            $service_access = ServiceAccess::query()->where('module','user')->with('user')->where('user_id', Auth::user()->id)
                ->first();
            $param = json_decode(isset($service_access->params) ? $service_access->params : "");
            $group_ids = isset($param->view_role) ? $param->view_role : [];

            $dataCategory->whereIn('id', $group_ids);

            $services = $dataCategory->get();
            $allow_names = [];

            foreach ($services??[] as $service){

                if (isset($service->params)){
                    $service_params = json_decode($service->params);
                    if (!empty($service_params->name)){
                        $names = $service_params->name;
                        $user_access = UserAccess::query()
                            ->with('service_access',function ($q) use ($service){
                                $q->where('user_id',$service->id)->where('module','service');
                            })->whereHas('service_access',function ($q) use ($service){
                                $q->where('user_id',$service->id)->where('module','service');
                            })
                            ->where('user_id',Auth::user()->id)
                            ->first();

                        $service_allow = ServiceAccess::query()
                            ->with('service_user_access', function ($q){
                                $q->where('user_id',Auth::user()->id);
                            })
                            ->where('module','service')
                            ->where('user_id',$service->id)
                            ->first();

                        $allow_attributes = [];
                        $is_allow_attributes = false;
                        $type_information_ctv_access = 0;
                        if (isset($service_allow)){
                            $user_access_service_access_params = json_decode($service_allow->params);

                            if (!empty($user_access_service_access_params->allow_attribute)){
                                $allow_attributes = $user_access_service_access_params->allow_attribute;
                            }

                            if (count($service_allow->service_user_access)){
                                $is_allow_attributes = true;
                            }

                            if (!empty($user_access_service_access_params->type_information_ctv_access)){
                                $type_information_ctv_access = $user_access_service_access_params->type_information_ctv_access;
                            }
                        }

                        foreach ($names??[] as $name){
                            if (count($allow_attributes) > 0){
                                if ($is_allow_attributes == true || ($type_information_ctv_access != 0 && Auth::user()->type_information_ctv == $type_information_ctv_access)){
                                    if ($is_allow_attributes == true && in_array($name,$allow_attributes)){
                                        array_push($allow_names,$name);
                                    }
                                    if ($type_information_ctv_access != 0 && Auth::user()->type_information_ctv == $type_information_ctv_access && in_array($name,$allow_attributes)){
                                        array_push($allow_names,$name);
                                    }
                                }else{
                                    array_push($allow_names,$name);
                                }
                            }
                            else{
                                array_push($allow_names,$name);
                            }
                        }
                    }
                }
            }
        }

        $dataCategory = $dataCategory->get();

        if ($request->ajax()|| $request->export_excel==1) {

            $datatable = Order::query()
                ->with(['item_ref','workflow_reception','author', 'processor' => function($query){
                    $query->with('service_access');
                }])
                ->where('order.module', config('module.service-purchase'))
                ->whereNull('type_version')
                ->where('gate_id',0);

            if ($request->filled('id')) {

                $datatable->where(function($q) use ($request) {
                    $q->orWhere('id',$request->get('id'));
                    $q->orWhere('request_id_customer',$request->get('id'));
                });

            }

            if ($request->filled('group_id')) {

                $datatable->whereHas('item_ref', function ($query) use ($request) {
                    $query->whereIn('id',$request->get('group_id'));
                });
            }

            if ($request->filled('type_information_ctv')) {
                $datatable->whereHas('processor', function ($query) use ($request) {
                    $query->where('type_information_ctv',$request->get('type_information_ctv'));
                });
            }

            if ($request->filled('group_id2')) {

                $datatable->whereHas('item_ref', function ($query) use ($request) {
                    $query->where('id',$request->get('group_id2'));
                });
            }

            if ($request->filled('id_pengiriman')) {

                $datatable->whereHas('order_pengiriman', function ($query) use ($request) {
                    $query->where('title', 'LIKE', '%' . $request->get('id_pengiriman') . '%');
                });
            }

            if ($request->filled('request_id')) {

                $request_id = explode(',',$request->get('request_id'));
                $datatable->whereIn('request_id_customer',$request_id);

            }

            if ($request->filled('title')) {

                $datatable->where('title', 'LIKE', '%' . $request->get('title') . '%');

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

            $status_datas = config('module.service-purchase.status');
            $data_html = view('admin.service.purchase.widget.__status')
                ->with('status_datas',$status_datas)
                ->render();
            if ($request->filled('type')) {
                $type_status = $request->get('type');
                if ($type_status == 1) {
                    $datatable->whereIn('status', [1]);

                    $status_datas = new \stdClass();
                    $status_datas->{1} = "Đang chờ";
                    $data_html = view('admin.service.purchase.widget.__status')
                        ->with('status_datas',$status_datas)
                        ->render();

                }
                elseif ($type_status == 2) {
                    $datatable->whereIn('status', [2]);
                    if (!Auth::user()->can('service-reception-all')){
                        $datatable->where('processor_id',Auth::user()->id);
                    }

                    $status_datas = new \stdClass();
                    $status_datas->{2} = "Đang thực hiện";
                    $data_html = view('admin.service.purchase.widget.__status')
                        ->with('status_datas',$status_datas)
                        ->render();
                }
                elseif ($type_status == 3) {
                    $datatable->whereIn('status', [0,5,3]);
                    if (!Auth::user()->can('service-reception-all')){
                        $datatable->where('processor_id',Auth::user()->id);
                    }

                    $status_datas = new \stdClass();
                    $status_datas->{0} = "Đã hủy";
                    $status_datas->{3} = "Từ chối";
                    $status_datas->{5} = "Thất bại";
                    $data_html = view('admin.service.purchase.widget.__status')
                        ->with('status_datas',$status_datas)
                        ->render();

                }
                elseif ($type_status == 4) {
                    $datatable->whereIn('status', [10,4]);
                    if (!Auth::user()->can('service-reception-all')){
                        $datatable->where('processor_id',Auth::user()->id);
                    }

                    $status_datas = new \stdClass();
                    $status_datas->{4} = "Hoàn tất";
                    $status_datas->{10} = "Hoàn tất đợi xác nhận";
                    $data_html = view('admin.service.purchase.widget.__status')
                        ->with('status_datas',$status_datas)
                        ->render();

                }
                elseif ($type_status == 5) {
                    $datatable->whereIn('status', [11,12]);
                    $status_datas = new \stdClass();
                    $status_datas->{11} = "Yêu cầu hoàn tiền";
                    $status_datas->{12} = "Đã hoàn tiền";
                    $data_html = view('admin.service.purchase.widget.__status')
                        ->with('status_datas',$status_datas)
                        ->render();
                }
            }

            if ($request->filled('mistake_error_by')) {

                $datatable->where('content', 'LIKE', '%' . $request->get('mistake_error_by') . '%');

            }

            if ($request->filled('type_information')) {

                $datatable->whereHas('author', function ($query) use ($request) {
                    $query->where('type_information',$request->get('type_information'));
                });

            }

            if ($request->filled('account_type')) {

                if($request->get('account_type') == 1){
                    $datatable->whereHas('processor', function ($query) use ($request) {
                        $query->where('username', 'REGEXP', '^qtv');
                    });
                }else if($request->get('account_type') == 3){
                    $datatable->whereHas('processor', function ($query) use ($request) {
                        $query->where('username', 'REGEXP', '^ctv');
                    });
                }
            }

            if ($request->filled('processor')) {

                $datatable->whereHas('processor', function ($query) use ($request) {
                    $query->where('username',$request->get('processor'));
                });

            }

            if ($request->filled('status')) {

                $datatable->whereIn('status',$request->get('status'));

            }

            if ($request->filled('started_at')) {

                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }

            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }

            if ($request->filled('arrange')){
                if ($request->get('arrange') == 0){
                    $datatable = $datatable->orderBy('created_at', 'desc');
                }elseif ($request->get('arrange') == 1){
                    $datatable = $datatable->orderBy('created_at', 'asc');
                }elseif ($request->get('arrange') == 2){
                    $datatable = $datatable->orderBy('process_at', 'desc');
                }elseif ($request->get('arrange') == 3){
                    $datatable = $datatable->orderBy('process_at', 'asc');
                }elseif ($request->get('arrange') == 4){
                    $datatable = $datatable->orderBy('price', 'desc');
                }elseif ($request->get('arrange') == 5){
                    $datatable = $datatable->orderBy('price', 'asc');
                }
            }
            else{
                $datatable = $datatable->orderBy('created_at', 'asc');
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

                    $datatable = $datatable->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_started_at')));
                }
                if ($request->filled('finished_ended_at')) {
                    $datatable = $datatable->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_ended_at')));
                }
            }

            //nếu user ko full quyền nhận các dịch vụ thì lấy các id dịch vụ được cấp quyền
            if (!Auth::user()->can('service-reception-all')) {

                //nếu có lọc dịch vụ thì chỉ chấp nhận lọc các dịch vụ cho phép
                if ($request->filled('group_id')) {

                    $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
                        $query->whereIn('id', $group_ids)->whereIn('id', (array)$request->get('group_id'));
                    });
                }
                elseif ($request->get('group_id2')){

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

                if ($type_status == 1){

//                    ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(JSON_UNQUOTE(params), '$.filter_type')) = 9")
                    if (Auth::user()->id != 198773){
                        $datatable = $datatable->whereIn('description', $allow_names);
                    }
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

            //xuất excel
            return \datatables()->eloquent($datatable)
                ->orderColumn('server', function ($query, $order) {
                    $query->orderByRaw('CAST(JSON_EXTRACT(params, "$.server") AS NCHAR) '.$order);
                })
                ->editColumn('work_name', function ($row) {
                    $html = '';

                    if (isset($row->description)){
                        $html = $row->description;
                    }

                    return $html;
                })
                ->editColumn('params', function ($row) {
                    return "";
                })
                ->editColumn('server', function($row) {

                    return $row->content;
                })
//                ->editColumn('information_ctv', function ($row) {
//                    if ($row->processor){
//                        if ($row->processor->type_information_ctv){
//                            if ($row->processor->type_information_ctv == 1){
//                                return  "Cộng tác viên nhà";
//                            }elseif ($row->processor->type_information_ctv == 2){
//                                return  "Cộng tác viên khách";
//                            }
//                        }else{
//                            return  "";
//                        }
//                    }
//                    return  "";
//                })
                ->editColumn('information', function ($row) {
                    if (!$row->author || !$row->author->type_information || $row->author->type_information == 0){
                        return  "Việt Nam";
                    }
                    if ($row->author->type_information == 1){
                        return  "Global";
                    }
                    return  "Sàn";
                })
                ->editColumn('author', function($row) {
                    $temp = '';
                    if( auth()->user()->can('view-profile')){
                        $temp .= "<a href=\"#\"  class=\"load-modal\" rel=\"".route('admin.view-profile',["username" => ($row->author->username??""),"shop_id" => "$row->shop_id"])."\">".($row->author->username??"")."</a>";
                    }
                    else{
                        $temp .= $row->username;
                    }
                    return $temp;
                })
                ->editColumn('price', function ($row) {
                    if(Auth::user()->can('service-purchase-view-price')){
                        return  number_format($row->price) ?? "";
                    }
                    else{
                        return null;
                    }

                })
                ->editColumn('reception_at', function ($row) {

                    if (isset($row->workflow_reception)){
                        $workflow_reception = $row->workflow_reception;

                        return date('d/m/Y H:i:s', strtotime($workflow_reception->updated_at));
                    }

                    return "";

                })
                ->editColumn('processor', function ($row) {

                    $tempProcessor="";


                    if(Auth::user()->id==($row->processor->id??"")){
                        $tempProcessor=$row->processor->username??"";
                    }
                    else{
                        if( auth()->user()->can( 'service-purchase-view-processor')){
                            $tempProcessor=$row->processor->username??"";
                        }
                    }

                    $temp = '';
                    if( auth()->user()->can('view-profile') && $tempProcessor!="" ){

                        $temp .= "<a href=\"#\"  class=\"load-modal\" rel=\"".route('admin.view-profile',["username" => ($tempProcessor)])."\">".($tempProcessor)."</a>";
                    }
                    else{
                        $temp .= $tempProcessor;
                    }
                    return $temp;
                })
                ->editColumn('price', function ($row) {
                    $price=$row->price;
                    if(Auth::user()->can('service-purchase-view-price'))
                    {
                        return $price;
                    }
                    else{
                        return "";
                    }
                })
                ->editColumn('price_ctv', function ($row) {
                    $price_ctv=$row->price_ctv;
                    if(Auth::user()->can('service-purchase-view-price-ctv'))
                    {
                        return $price_ctv;
                    }
                    else{
                        return "";
                    }
                })
                ->editColumn('profit', function ($row) {
                    $price=$row->price;
                    $real_received_price_ctv=$row->real_received_price_ctv;
                    if(Auth::user()->can('service-purchase-view-profit'))
                    {
                        if($row->status==4){
                            return intval($price)-intval($real_received_price_ctv);
                        }
                        else{
                            return 0;
                        }

                    }
                    else{
                        return 0;
                    }
                })
                ->editColumn('created_at', function ($row) {
                    return date('d/m/Y H:i:s', strtotime($row->created_at));
                })
                ->editColumn('process_at', function ($row) {
                    if ($row->status == 4){
                        return date('d/m/Y H:i:s', strtotime($row->process_at));
                    }elseif ($row->status == 10 || $row->status == 11){
                        return date('d/m/Y H:i:s', strtotime($row->process_at));
                    }
                    else{
                        return date('d/m/Y H:i:s', strtotime($row->updated_at));
                    }

                })
                ->editColumn('ratio', function ($row) use ($service_access){

                    //nếu là đơn hoàn thành rồi
                    if($row->status!=4){
                        $ratio = 80;

                        if (isset($row->processor)){
                            if (isset($row->processor->service_access)){
                                $service_access1 = $row->processor->service_access;
                                $param1 = json_decode(isset($service_access1->params) ? $service_access1->params : "");
                                $ratio = isset($param1->{'ratio_' . ($row->item_ref->id??null)}) ? $param1->{'ratio_' . ($row->item_ref->id??null)??null} : $ratio;
                            }
                        }else{
                            if (!Auth::user()->can('service-reception-all')) {
                                //lấy các quyền được xem yêu cầu dịch vụ

                                $service_access1 = $service_access;
                                $param1 = json_decode(isset($service_access1->params) ? $service_access1->params : "");
                                $ratio = isset($param1->{'ratio_' . ($row->item_ref->id??null)}) ? $param1->{'ratio_' . ($row->item_ref->id??null)??null} : $ratio;
                            }
                        }
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
                    if (Auth::user()->can('permission-manual-refund-request') && $row->status === 4){
                        $temp .= '<a href=\'javascript:void(0)\' data-toggle="modal" data-target="#refundModal" rel="'.$row->id.'" class=\'btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-danger btn-refund mr-2\' title="Hoàn tiền"><i class="la la-refresh"></i></a>';

                    }

                    if ((Auth::user()->id == 5551 || Auth::user()->id == 198544) && ($row->status === 3 || $row->status === 5 || $row->status === 0)){
                        $temp .= '<a href=\'javascript:void(0)\' data-toggle="modal" data-target="#refundDeleteModal" rel="'.$row->id.'" class=\'btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-danger btn-refund-delete mr-2\' title="Chuyển trạng thái"><i class="la la-refresh"></i></a>';
                    }
//                    $isSticky = false;
//                    if (isset($row->item_ref->sticky) && $row->item_ref->sticky == 1){
//
//                    }
                    $temp .= "<a href=\"" . route('admin.service-purchase.show', $row->id) . "\"  rel=\"$row->id\" class=\"m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill \" title=\"Xem\"><i class=\"la la-eye\"></i></a>";
                    return $temp;
                })
                ->with('totalSumary', function() use ($data_html) {
                    return $data_html;
                })
                ->toJson();

        }

        $params_error = [];

        $group = Group::query()->where('module','service-category')->where('id',5)->first();

        if (isset($group)){
            if (isset($group->params_error) && !empty($group->params_error)){
                $params_error = json_decode($group->params_error);
            }
        }

        $attributes = [];

        return view('admin.service.purchase.index')
            ->with('module', $this->module)
            ->with('params_error',$params_error)
            ->with('attributes',$attributes)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('dataCategory', $dataCategory);

    }

    public function loadTopAttribute(Request $request){

        if (!Auth::user()->can('service-purchase-top-attribute')){
            return redirect()->back()->withErrors(__('Bạn không có quyền từ chối yêu cầu hoàn tiền'));
        }

        if ($request->ajax()) {

            $attributes = Order::query()
                ->select('description', DB::raw('count(*) as total'))
                ->whereNotNull('description')
                ->groupBy('description')
                ->orderByDesc('total');

            if ($request->filled('group_id')) {

                $attributes->whereHas('item_ref', function ($query) use ($request) {
                    $query->whereIn('id',$request->get('group_id'));
                });
            }

            $limit = 10;

            if ($request->filled('top_limit')){
                $limit = $request->get('top_limit');
            }

            if ($request->filled('type_information_ctv')) {
                $attributes->whereHas('processor', function ($query) use ($request) {
                    $query->where('type_information_ctv',$request->get('type_information_ctv'));
                });
            }

            if ($request->filled('id_pengiriman')) {

                $datatable->whereHas('order_pengiriman', function ($query) use ($request) {
                    $query->where('title', 'LIKE', '%' . $request->get('id_pengiriman') . '%');
                });
            }

            if ($request->filled('group_id2')) {

                $attributes->whereHas('item_ref', function ($query) use ($request) {
                    $query->where('id',$request->get('group_id2'));
                });
            }

            if ($request->filled('title')) {

                $attributes->where('title', 'LIKE', '%' . $request->get('title') . '%');
            }

            if ($request->filled('author')) {
                $attributes->whereHas('author', function ($query) use ($request) {
                    $query->Where('username', 'LIKE', '%' . $request->get('author') . '%');
                });
            }

            if ($request->filled('mistake_error_by')) {

                $attributes->where('content', 'LIKE', '%' . $request->get('mistake_error_by') . '%');

            }

            if ($request->filled('type')) {
                $type_status = $request->get('type');
                if ($type_status == 1) {
                    $attributes->whereIn('status', [1]);
                } elseif ($type_status == 2) {
                    $attributes->whereIn('status', [2]);
                    if (!Auth::user()->can('service-reception-all')){
                        $attributes->where('processor_id',Auth::user()->id);
                    }
                } elseif ($type_status == 3) {
                    $attributes->whereIn('status', [0,5,3]);
                    if (!Auth::user()->can('service-reception-all')){
                        $attributes->where('processor_id',Auth::user()->id);
                    }
                } elseif ($type_status == 4) {
                    $attributes->whereIn('status', [10,4]);
                    if (!Auth::user()->can('service-reception-all')){
                        $attributes->where('processor_id',Auth::user()->id);
                    }
                } elseif ($type_status == 5) {
                    $attributes->whereIn('status', [11,12]);
                    if (!Auth::user()->can('service-reception-all')){
                        $attributes->where('processor_id',Auth::user()->id);
                    }
                }
            }

            if ($request->filled('type_information')) {

                $attributes->whereHas('author', function ($query) use ($request) {
                    $query->where('type_information',$request->get('type_information'));
                });

            }

            if ($request->filled('account_type')) {

                if($request->get('account_type') == 1){
                    $attributes->whereHas('processor', function ($query) use ($request) {
                        $query->where('username', 'REGEXP', '^qtv');
                    });
                }else if($request->get('account_type') == 3){
                    $attributes->whereHas('processor', function ($query) use ($request) {
                        $query->where('username', 'REGEXP', '^ctv');
                    });
                }
            }

            if ($request->filled('processor')) {

                $attributes->whereHas('processor', function ($query) use ($request) {
                    $query->where('username',$request->get('processor'));
                });

            }

            if ($request->filled('status')) {
                $attributes->whereIn('status',$request->get('status'));
            }

            if ($request->filled('started_at')) {

                $attributes->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }

            if ($request->filled('ended_at')) {
                $attributes->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
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

                    $attributes = $attributes->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_started_at')));
                }
                if ($request->filled('finished_ended_at')) {
                    $attributes = $attributes->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_ended_at')));
                }
            }

            $attributes = $attributes->limit($limit)->get();

            $html = view('admin.service.purchase.widget.__attribute')
                ->with('attributes',$attributes)
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

    public function loadAttributeTk(Request $request){

        $group_ids = [];

        $service_access = ServiceAccess::query()->where('module','user')->where('module','user')->with('user')->where('user_id', Auth::user()->id)
            ->first();

        if (!Auth::user()->can('service-reception-all')) {
            //lấy các quyền được xem yêu cầu dịch vụ

            $param = json_decode(isset($service_access->params) ? $service_access->params : "");
            $group_ids = isset($param->view_role) ? $param->view_role : [];
        }

        if ($request->ajax()) {

            $datatable = Order::query()
                ->with(['item_ref','author', 'processor' => function($query){
                    $query->with('service_access');
                }])
                ->where('order.module', config('module.service-purchase'))
                ->whereNull('type_version')
                //lấy điều kiện đơn bt
                ->where(DB::raw('COALESCE(order.gate_id,0)'), '<>', 1 );

            if ($request->filled('id')) {

                $datatable->where(function($q) use ($request) {
                    $q->orWhere('id',$request->get('id'));
                    $q->orWhere('request_id_customer',$request->get('id'));
                });

            }

            if ($request->filled('id_pengiriman')) {

                $datatable->whereHas('order_pengiriman', function ($query) use ($request) {
                    $query->where('title', 'LIKE', '%' . $request->get('id_pengiriman') . '%');
                });
            }

            if ($request->filled('group_id')) {

                $datatable->whereHas('item_ref', function ($query) use ($request) {
                    $query->whereIn('id',$request->get('group_id'));
                });
            }

            if ($request->filled('type_information_ctv')) {
                $datatable->whereHas('processor', function ($query) use ($request) {
                    $query->where('type_information_ctv',$request->get('type_information_ctv'));
                });
            }

            if ($request->filled('group_id2')) {

                $datatable->whereHas('item_ref', function ($query) use ($request) {
                    $query->where('id',$request->get('group_id2'));
                });
            }

            if ($request->filled('request_id')) {

                $request_id = explode(',',$request->get('request_id'));
                $datatable->whereIn('request_id_customer',$request_id);

            }

            if ($request->filled('type')) {
                $type_status = $request->get('type');
                if ($type_status == 1) {
                    $datatable->whereIn('status', [1]);
                } elseif ($type_status == 2) {
                    $datatable->whereIn('status', [2]);
                    if (!Auth::user()->can('service-reception-all')){
                        $datatable->where('processor_id',Auth::user()->id);
                    }
                } elseif ($type_status == 3) {
                    $datatable->whereIn('status', [0,5,3]);
                    if (!Auth::user()->can('service-reception-all')){
                        $datatable->where('processor_id',Auth::user()->id);
                    }
                } elseif ($type_status == 4) {
                    $datatable->whereIn('status', [10,4]);
                    if (!Auth::user()->can('service-reception-all')){
                        $datatable->where('processor_id',Auth::user()->id);
                    }
                } elseif ($type_status == 5) {
                    $datatable->whereIn('status', [11,12]);
                    if (!Auth::user()->can('service-reception-all')){
                        $datatable->where('processor_id',Auth::user()->id);
                    }
                }
            }

            if ($request->filled('title')) {

                $datatable->where('title', 'LIKE', '%' . $request->get('title') . '%');

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

            if ($request->filled('mistake_error_by')) {

                $datatable->where('content', 'LIKE', '%' . $request->get('mistake_error_by') . '%');

            }

            if ($request->filled('type_information')) {

                $datatable->whereHas('author', function ($query) use ($request) {
                    $query->where('type_information',$request->get('type_information'));
                });

            }

            if ($request->filled('account_type')) {

                if($request->get('account_type') == 1){
                    $datatable->whereHas('processor', function ($query) use ($request) {
                        $query->where('username', 'REGEXP', '^qtv');
                    });
                }else if($request->get('account_type') == 3){
                    $datatable->whereHas('processor', function ($query) use ($request) {
                        $query->where('username', 'REGEXP', '^ctv');
                    });
                }
            }

            if ($request->filled('processor')) {

                $datatable->whereHas('processor', function ($query) use ($request) {
                    $query->where('username',$request->get('processor'));
                });

            }

            if ($request->filled('status')) {

                $datatable->whereIn('status',$request->get('status'));

            }

            if ($request->filled('started_at')) {

                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }

            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }

            if ($request->filled('arrange')){
                if ($request->get('arrange') == 0){
                    $datatable = $datatable->orderBy('created_at', 'desc');
                }elseif ($request->get('arrange') == 1){
                    $datatable = $datatable->orderBy('created_at', 'asc');
                }elseif ($request->get('arrange') == 2){
                    $datatable = $datatable->orderBy('process_at', 'desc');
                }elseif ($request->get('arrange') == 3){
                    $datatable = $datatable->orderBy('process_at', 'asc');
                }elseif ($request->get('arrange') == 4){
                    $datatable = $datatable->orderBy('price', 'desc');
                }elseif ($request->get('arrange') == 5){
                    $datatable = $datatable->orderBy('price', 'asc');
                }
            }
            else{
                $datatable = $datatable->orderBy('created_at', 'asc');
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

                    $datatable = $datatable->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_started_at')));
                }
                if ($request->filled('finished_ended_at')) {
                    $datatable = $datatable->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_ended_at')));
                }
            }

            //nếu user ko full quyền nhận các dịch vụ thì lấy các id dịch vụ được cấp quyền
            if (!Auth::user()->can('service-reception-all')) {

                //nếu có lọc dịch vụ thì chỉ chấp nhận lọc các dịch vụ cho phép
                if ($request->filled('group_id')) {

                    $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
                        $query->whereIn('id', $group_ids)->whereIn('id', (array)$request->get('group_id'));
                    });
                }
                elseif ($request->get('group_id2')){
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
                DB::raw('SUM(CASE WHEN order.module = "service-purchase" THEN order.price ELSE 0 END) as total_price'),
                DB::raw('SUM(order.real_received_price_ctv) as total_real_received_price_ctv'),
                DB::raw('SUM(CASE WHEN order.idkey IS NOT NULL THEN order.price_input ELSE 0 END) as total_price_input')
            )->first();

            $html = view('admin.service.purchase.widget.__attribute_tk')
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

        $export = new OrdersExport($request);
        return \Excel::download($export, 'Thống kê dịch vụ thủ công_ ' . time() . '.xlsx');

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

        $datatable = Order::query()
            ->with(['item_ref','author','order_refund','order_service_workflow','order_service_workname','pengiriman_detail'])
            ->where('module', config('module.service-purchase'))
            ->where('gate_id',0)->findOrFail($id);

        if (!isset($datatable->item_ref)){
            return redirect()->back()->withErrors(__("Không tìm thấy dịch vụ"));
        }

        $itemconfig_ref = $datatable->item_ref;

        if (!isset($datatable->author)){
            return redirect()->back()->withErrors(__("Không tìm thấy người tạo đơn"));
        }



        $author = $datatable->author;

        $order_refund = $datatable->order_refund;
        $allow_attribute = [];
        $service_access = null;
        $user_access = null;
        //lấy các quyền được xem yêu cầu dịch vụ
        //lấy các quyền được xem yêu cầu dịch vụ
        if (!Auth::user()->can('service-reception-all')) {

            //lấy các quyền được xem yêu cầu dịch vụ
            $service_access = ServiceAccess::query()
                ->where('module','user')
                ->with('user')
                ->where('user_id', Auth::guard()->user()->id)
                ->first();

            $param = json_decode(isset($service_access->params) ? $service_access->params : "");
            $service_member_access = ServiceAccess::query()->where('module','user')
                ->with('user')
                ->where('user_id', $datatable->author_id)
                ->first();
            $param_member = json_decode(isset($service_member_access->params) ? $service_member_access->params : "");

            if (isset($param->view_role) && in_array($itemconfig_ref->id??null, (array)$param->view_role)) {
                if (isset($itemconfig_ref->sticky) && $itemconfig_ref->sticky == 1){
                    if (isset($param->service_accept_ctv) && in_array($itemconfig_ref->id??null, (array)$param->service_accept_ctv)) {
                        if (isset($param_member->service_accept_member) && in_array($itemconfig_ref->id??null, (array)$param_member->service_accept_member)) {
                            return redirect()->back()->withErrors(__("Bạn không có quyền truy cập yêu cầu dịch vụ này 1"));
                        }
                    }
                }

                if (isset($itemconfig_ref->position)){
                    if ($itemconfig_ref->position == 1 || $itemconfig_ref->position == 2){
                        if (isset(Auth::user()->type_information_ctv) && ((int)$itemconfig_ref->position == Auth::user()->type_information_ctv)){
                            if (isset($param_member->service_accept_member) && in_array($itemconfig_ref->id??null, (array)$param_member->service_accept_member)) {
                                return redirect()->back()->withErrors(__("Bạn không có quyền truy cập yêu cầu dịch vụ này 2"));
                            }
                        }
                    }
                }
            }
            else {
                return redirect()->back()->withErrors(__("Bạn không có quyền truy cập yêu cầu dịch vụ này 3"));
            }

            $user_access = ServiceAccess::query()
                ->where('module','service')
                ->where('user_id',$itemconfig_ref->id)
                ->with('service_user_access', function ($q){
                    $q->where('user_id',Auth::guard()->user()->id);
                })
                ->first();



            $isAlowAttribute = false;

            //Kiểm tra thuộc tính.
            if (isset($user_access->params)){
                $user_access_params = json_decode($user_access->params);
                if (!empty($user_access_params->allow_attribute)){
                    $allow_attribute = $user_access_params->allow_attribute;
                }

                if (count($user_access->service_user_access)){
                    $isAlowAttribute = true;
                }

                if (!empty($user_access->type_information_ctv_access) && $user_access->type_information_ctv_access == Auth::user()->type_information_ctv){
                    $isAlowAttribute = true;
                }

                if ($isAlowAttribute === true && count($allow_attribute) && !in_array($datatable->description,$allow_attribute)){
                    return redirect()->back()->withErrors(__("Bạn không có quyền truy cập yêu cầu dịch vụ này 4"));
                }

                if (count($user_access->service_user_access) == 0 && $isAlowAttribute === false){
                    $allow_attribute = [];
                }
            }
        }

        $bots = [];
        $roblox_order = null;

        if ($datatable->idkey == 'anime_defenders_auto'){
            $bots = Roblox_Bot::query()
                ->where('type_order',2)
                ->where('type_bot',2)
                ->orderBy('ver', 'asc')
                ->where('status',1)
                ->orderBy('server', 'asc')->get();

            $roblox_order = Roblox_Order::query()->where('order_id',$datatable->id)->first();
        }

        return view('admin.service.purchase.show')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('datatable',$datatable)
            ->with('itemconfig_ref',$itemconfig_ref)
            ->with('author',$author)
            ->with('allow_attribute',$allow_attribute)
            ->with('bots',$bots)
            ->with('roblox_order',$roblox_order)
            ->with('order_refund',$order_refund)
            ->with('service_access',$service_access);

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

    public function postReception(Request $request, $id)
    {

        DB::beginTransaction();
        try {

            $data = Order::with('item_ref')
                ->where('module', '=', config('module.service-purchase'))
                ->where('status', "1")
                ->lockForUpdate()
                ->findOrFail($id);

            //check nếu là dịch vu auto thì không thể tiếp nhận

            if ($data->gate_id == "1") {
                DB::rollback();
                return redirect()->back()->withErrors(__('Không thể tiếp nhận dịch vụ có hệ thống tự động'));
            }

            if ($data->idkey == 'anime_defenders_auto'){
                if (!$request->filled('bot_id')){
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Vui lòng chọn bot xử lý'));
                }
            }

            //Kiểm tra giới hạn số đơn hàng.
            $isLimit = false;

            if (isset($data->item_ref)){
                $service = $data->item_ref;
                if (isset($service->is_display) && ($service->is_display == 1 || $service->is_display == 2)){
                    if ($service->is_display == 1){
                        $isLimit = true;
                    }
                    else{
                        $service_access = ServiceAccess::query()->where('module','user')->where('user_id',Auth::guard()->user()->id)->first();

                        if (!empty($service_access->params)){
                            $params = json_decode($service_access->params);
                            if (!empty($params->service_limit)){
                                if (in_array($service->id,$params->service_limit)){
                                    $isLimit = true;
                                }
                            }
                        }
                    }
                }

                if ($isLimit){
                    if (isset($service->display_type)){
                        $count_order = Order::query()
                            ->where('processor_id',Auth::guard()->user()->id)
                            ->where('module', '=', config('module.service-purchase'))
                            ->where('status',2)
                            ->where('ref_id',$data->ref_id)
                            ->count();
                        if ($count_order >= $service->display_type){
                            DB::rollback();
                            return redirect()->back()->withErrors(__('Không thể tiếp nhận đơn thêm đơn'));
                        }
                    }
                }
            }

            //lấy các quyền được tiếp nhận yêu cầu dịch vụ
            if (!Auth::user()->can('service-reception-all')) {

                //lấy các quyền được xem yêu cầu dịch vụ
                $service_access = ServiceAccess::query()->where('module','user')->with('user')->where('user_id', Auth::guard()->user()->id)
                    ->first();

                $param = json_decode(isset($service_access->params) ? $service_access->params : "");
                $service_member_access = ServiceAccess::query()->where('module','user')->with('user')->where('user_id', $data->author_id)->first();
                $param_member = json_decode(isset($service_member_access->params) ? $service_member_access->params : "");

                if((isset($param->{'accept_role'}) && in_array($data->item_ref->id??null,(array)$param->{'accept_role'})) ){
                    if(\App\Library\Helpers::DecodeJson('server_mode',$data->item_ref->params)==1){
                        if(isset($param->{'allow_server_'.($data->item_ref->id??null)}) && in_array($data->position,(array)$param->{'allow_server_'.($data->item_ref->id??null)})){}else{
                            DB::rollback();
                            return redirect()->back()->withErrors(__('Không thể tiếp nhận đơn 1'));
                        }
                    }
                    else{
                        $user_access = ServiceAccess::query()
                            ->where('module','service')
                            ->where('user_id',$data->item_ref->id)
                            ->with('service_user_access', function ($q){
                                $q->where('user_id',Auth::guard()->user()->id);
                            })
                            ->whereHas('service_user_access',function ($q){
                                $q->where('user_id',Auth::guard()->user()->id);
                            })
                            ->first();

                        $isAlowAttribute = false;

                        //Kiểm tra thuộc tính.
                        if (isset($user_access->params)){
                            $user_access_params = json_decode($user_access->params);

                            $allow_attribute = [];
                            if (!empty($user_access_params->allow_attribute)){
                                $allow_attribute = $user_access_params->allow_attribute;
                            }

                            if (count($user_access->service_user_access)){
                                $isAlowAttribute = true;
                            }

                            if (!empty($user_access->type_information_ctv_access) && $user_access->type_information_ctv_access == Auth::user()->type_information_ctv){
                                $isAlowAttribute = true;
                            }

                            if ($isAlowAttribute === true && count($allow_attribute) && !in_array($data->description,$allow_attribute)){
                                return redirect()->back()->withErrors(__("Bạn không có quyền truy cập yêu cầu dịch vụ này 4"));
                            }
//                            if (!empty($user_access_params->allow_attribute)){
//                                $allow_attribute = $user_access_params->allow_attribute;
//                                if (!in_array($data->description,$allow_attribute)){
//                                    return redirect()->back()->withErrors(__("Không thể tiếp nhận đơn 2"));
//                                }
//                            }
                        }
                    }

                    if (isset($data->item_ref->sticky) && $data->item_ref->sticky == 1){
                        if (isset($param->service_accept_ctv) && in_array($data->item_ref->id??null, (array)$param->service_accept_ctv)) {
                            if (isset($param_member->service_accept_member) && in_array($data->item_ref->id??null, (array)$param_member->service_accept_member)) {
                                return redirect()->back()->withErrors(__("Không thể tiếp nhận đơn 3"));
                            }
                        }
                    }

                    if (isset($data->item_ref->position)){
                        if ($data->item_ref->position == 1 || $data->item_ref->position == 2){
                            if (isset(Auth::user()->type_information_ctv) && ((int)$data->item_ref->position == Auth::user()->type_information_ctv)){
                                if (isset($param_member->service_accept_member) && in_array($data->item_ref->id??null, (array)$param_member->service_accept_member)) {
                                    return redirect()->back()->withErrors(__("Không thể tiếp nhận đơn 4"));
                                }
                            }
                        }
                    }
                }
                else {
                    DB::rollBack();
                    return redirect()->back()->withErrors(__("Không thể tiếp nhận đơn 5"));
                }
            }

            $data->processor_id = Auth::guard()->user()->id;
            $data->status = 2;
            $data->save();

            //set tiến độ tiếp nhận
            OrderDetail::create([
                'order_id'=>$data->id,
                'module' => config('module.service-workflow.key'),
                'author_id' =>  Auth::guard()->user()->id,
                'status' => "2",
            ]);

            if ($data->idkey == 'anime_defenders_auto'){
                $bot_id = $request->get('bot_id');
                $bot = Roblox_Bot::query()
                    ->where('type_order',2)
                    ->where('type_bot',2)
                    ->orderBy('ver', 'asc')
                    ->where('status',1)
                    ->where('id',$bot_id)
                    ->orderBy('server', 'asc')->first();

                if (!isset($bot)){
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Không tìm thấy bot cần xử lý'));
                }

                $roblox_order = Roblox_Order::query()
                    ->where('order_id',$data->id)
                    ->whereNull('bot_handle')
                    ->where('status',"chuanhan")
                    ->where('type_order',10)
                    ->first();

                if (!isset($roblox_order)){
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Không tìm thấy đơn hàng roblox cần xử lý'));
                }

                $roblox_order->bot_handle = $bot->id;
                $roblox_order->status = 'gandonchobot';
                $roblox_order->save();

                //set tiến độ tiếp nhận
                OrderDetail::create([
                    'order_id'=>$data->id,
                    'module' => config('module.service-workflow.key'),
                    'author_id' =>  Auth::guard()->user()->id,
                    'title'=> 'Bot: '.$bot->acc.' sẽ xử lý đơn hàng',
                    'description'=> $bot->id,
                    'status' => "2",
                ]);
            }

            $this->callbackToShop($data,__('Tiếp nhận thành công yêu cầu dịch vụ'),null,null);

            ActivityLog::add($request,__("Tiếp nhận thành công yêu cầu dịch vụ ").config('module.service-purchase.key').' #'.$id );

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return redirect()->back()->withErrors(__('Có lỗi phát sinh.Xin vui lòng thử lại !'));
        }

        // Commit the queries!
        DB::commit();
        return redirect()->back()->with('success', __("Tiếp nhận thành công yêu cầu dịch vụ  #") . $data->id);

    }

    public function postRejectRefund(Request $request, $id){

        DB::beginTransaction();
        try {

            if (!Auth::user()->can('service-purchase-delete-order-refund')){
                return redirect()->back()->withErrors(__('Bạn không có quyền từ chối yêu cầu hoàn tiền'));
            }

            if (!$request->note_refund) {
                return redirect()->back()->withErrors(__('Vui lòng nhập nội dung từ chối'));
            }

            $data = Order::with('item_ref','author','processor','order_refund')
                ->where('module', '=', config('module.service-purchase'))
                ->where('status', "11")
                ->lockForUpdate()
                ->findOrFail($id);

            $note = $request->note_refund;

            //check nếu là dịch vu auto thì không thể tiếp nhận

            if ($data->gate_id == "1") {
                return redirect()->back()->withErrors(__('Không thể hoàn tiền dịch vụ có hệ thống tự động'));
            }

            //Yêu cầu hoàn tiền.

            $order_refund = OrderDetail::query()
                ->where('module','service-refund')
                ->where('order_id',$data->id)
                ->where('status',2)
                ->first();

            if (!$order_refund) {
                return redirect()->back()->withErrors(__('Không tìm thấy yêu cầu hoàn tiền'));
            }
            //Cập nhật trạng thái yêu cầu hoàn tiền.
            $order_refund->status = 3;
            $order_refund->save();

            //Cập nhật trạng thái đơn hàng về chờ đối soát

            $data->status = 10;
//            $data->process_at = Carbon::now();//Thời gian xác nhận đơn hàng
            $data->save();

            //set tiến độ tiếp nhận
            OrderDetail::create([
                'order_id'=>$data->id,
                'module' => config('module.service-workflow.key'),
                'author_id' =>  Auth::guard()->user()->id,
                'content' => __('Từ chối hoàn tiền vì lý do: ').$note,
                'status' => 10,
            ]);

            $this->callbackToShop($data,$note,'refund',null);

//            $this->dispatch(new App\Jobs\CallbackOrderService($data,$note,'refund',null));

            ActivityLog::add($request,__("Từ chối yêu cầu ").config('module.service-purchase.key').' #'.$id );

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return redirect()->back()->withErrors(__('Có lỗi phát sinh.Xin vui lòng thử lại !'));
        }

        // Commit the queries!
        DB::commit();
        return redirect()->back()->with('success', __("Từ chối yêu cầu hoàn tiền thành công  #") . $data->id);
    }

    public function postCompletedRefund(Request $request,$id){
        DB::beginTransaction();
        try {

            if (!Auth::user()->can('service-purchase-complete-order-refund')){
                return redirect()->back()->withErrors(__('Bạn không có quyền từ chối yêu cầu hoàn tiền'));
            }

            $data = Order::with('item_ref','author','processor','order_refund')
                ->where('module', '=', config('module.service-purchase'))
                ->where('status', "11")
                ->lockForUpdate()
                ->findOrFail($id);

            //check nếu là dịch vu auto thì không thể tiếp nhận

            if ($data->gate_id == "1") {
                return redirect()->back()->withErrors(__('Không thể hoàn tiền dịch vụ có hệ thống tự động'));
            }

            //Yêu cầu hoàn tiền.

            $order_refund = OrderDetail::query()
                ->where('module','service-refund')
                ->where('order_id',$data->id)
                ->where('status',2)
                ->first();

            if (!$order_refund) {
                return redirect()->back()->withErrors(__('Không tìm thấy yêu cầu hoàn tiền'));
            }

            //Cập nhật trạng thái yêu cầu hoàn tiền.
            $order_refund->status = 1;
            $order_refund->save();

            //Cập nhật trạng thái đơn hàng về chờ đối soát

            $data->status = 12;
            $data->save();

            //set tiến độ tiếp nhận
            OrderDetail::create([
                'order_id'=>$data->id,
                'module' => config('module.service-workflow.key'),
                'author_id' =>  Auth::guard()->user()->id,
                'status' => 12,
                'content' => __('Chấp nhận hoàn tiền'),
            ]);

            if ($data->idkey == 'anime_defenders_auto'){

                $roblox_order = Roblox_Order::query()
                    ->where('order_id',$data->id)
                    ->where('type_order',10)
                    ->first();

                if (!isset($roblox_order)){
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Không tìm thấy đơn hàng roblox cần xử lý'));
                }

                $roblox_order->status = 'dahuybo';
                $roblox_order->save();
            }

            //hoàn tiền cho khách hàng

            $userTransaction = User::where('id', $data->author_id)->lockForUpdate()->firstOrFail();

            if($userTransaction->checkBalanceValid() == false){
                DB::rollback();
                return redirect()->back()->withErrors(__('Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý'));
            }

            $userTransaction->balance = $userTransaction->balance + $data->price;
            $userTransaction->balance_in = $userTransaction->balance_in + $data->price;
            $userTransaction->save();

            Txns::create([
                'trade_type'=>'service_destroy', //Hủy dịch vụ
                'user_id'=>$userTransaction->id,
                'is_add' => '1',//Cộng tiền
                'amount' => $data->price,
                'real_received_amount' => $data->price,
                'last_balance' => $userTransaction->balance,
                'description' => __("Hoàn tiền dịch vụ thủ công #") . $data->id,
                'ip' => $request->getClientIp(),
                'order_id' => $data->id,
                'status' => 1,
                'shop_id'=>$data->shop_id
            ]);

            $this->callbackToShop($data,__('Chấp nhận yêu cầu hoàn tiền đơn hàng'),null,null);

//            $this->dispatch(new App\Jobs\CallbackOrderService($data,__('Chấp nhận yêu cầu hoàn tiền đơn hàng'),null,null));

            ActivityLog::add($request,__("Chấp nhận yêu cầu hoàn tiền đơn hàng ").config('module.service-purchase.key').' #'.$id );

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return redirect()->back()->withErrors(__('Có lỗi phát sinh.Xin vui lòng thử lại !'));
        }

        // Commit the queries!
        DB::commit();
        return redirect()->back()->with('success', __("Chấp nhận yêu cầu hoàn tiền đơn hàng thành công  #") . $data->id);
    }

    public function postCompleted(Request $request, $id)
    {

        DB::beginTransaction();
        try {

            $data = Order::with('item_ref','author')
                ->where('module', '=', config('module.service-purchase'))
                ->where('status', "2")
                ->lockForUpdate()
                ->findOrFail($id);

            if(isset(Auth::user()->type_information_ctv) && Auth::user()->type_information_ctv == 1 && $data->idkey == "gamepass_roblox"){
                if (!$request->filled('id_pengiriman') || !$request->filled('account_pengiriman')) {
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Vui lòng nhập đầy đủ thông tin id lô hàng và account lô hàng'));
                }
            }

            $type_refund = $data->type_refund??0;

            if($data->processor_id != Auth::guard()->user()->id){
                DB::rollback();
                return redirect()->back()->withErrors(__('Bạn không phải là người tiếp nhận xử lý đơn này. Vui lòng thử lại'));
            }

            //check nếu là dịch vu auto thì không thể tiếp nhận
            if ($data->gate_id == "1") {
                DB::rollback();
                return redirect()->back()->withErrors(__('Không thể hoàn tất dịch vụ có hệ thống tự động'));
            }

            if (isset($data->type_refund) && $data->type_refund == 1){
                //set tiến độ hoàn tất
                OrderDetail::create([
                    'order_id'=>$data->id,
                    'module' => config('module.service-workflow.key'),
                    'author_id' =>  Auth::guard()->user()->id,
                    'status' => 10,
                ]);

                if ($request->filled('id_pengiriman') && $request->filled('account_pengiriman')){
                    $id_pengiriman = $request->get('id_pengiriman');
                    $account_pengiriman = $request->get('account_pengiriman');
                    OrderDetail::create([
                        'order_id' => $data->id,
                        'module' => 'pengiriman',
                        'title' => $id_pengiriman??'',
                        'description' => $account_pengiriman??'',
                        'content' => "Nhập hàng",
                        'status' => 10,
                    ]);

                }

                $data->status = 10;
            }
            else{

                $userTransaction = User::where('id', $data->processor_id)->lockForUpdate()->first();

                if($userTransaction->checkBalanceValid() == false){
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý'));
                }

                //tính chiết khấu cho người bán
                $ratio = 80;
                //lấy các quyền được tiếp nhận yêu cầu dịch vụ
                $service_access = ServiceAccess::query()->where('module','user')->with('user')->where('user_id', $userTransaction->id)
                    ->first();

                if (isset($service_access)){
                    if (isset($service_access->params)){
                        $param = json_decode(isset($service_access->params) ? $service_access->params : "");

                        if(isset($param->{'ratio_' . ($data->item_ref->id??null)})){
                            $ratio= $param->{'ratio_' . ($data->item_ref->id??null)};
                        }
                        else{
                            $ratio=$ratio;
                        }
                    }
                }

                //cộng tiền user
                $real_received_amount = ($ratio * $data->price_ctv) / 100;

                //Cập nhật trạng thái đơn hàng
                $data->status = 4;
                $data->ratio_ctv = $ratio;
                $data->real_received_price_ctv = $real_received_amount;
                $data->save();

                //Cộng tiền cho CYV
                $userTransaction->balance = $userTransaction->balance + $real_received_amount;
                $userTransaction->balance_in = $userTransaction->balance_in + $real_received_amount;
                $userTransaction->save();

                //set tiến độ hoàn tất
                OrderDetail::create([
                    'order_id'=>$data->id,
                    'module' => config('module.service-workflow.key'),
                    'title' =>  'Thành công',
                    'status' => 4,
                ]);

                if ($request->filled('id_pengiriman') && $request->filled('account_pengiriman')){
                    $id_pengiriman = $request->get('id_pengiriman');
                    $account_pengiriman = $request->get('account_pengiriman');
                    OrderDetail::create([
                        'order_id' => $data->id,
                        'module' => 'pengiriman',
                        'title' => $id_pengiriman??'',
                        'description' => $account_pengiriman??'',
                        'content' => "Nhập hàng",
                        'status' => 4,
                    ]);

                }

                //Lưu biến động số dư
                Txns::create([
                    'trade_type'=>'service_completed', //Thanh toán dịch vụ
                    'user_id'=>$userTransaction->id,
                    'is_add' => '1',//Cộng tiền
                    'amount' => $real_received_amount,
                    'real_received_amount' => $real_received_amount,
                    'last_balance' => $userTransaction->balance,
                    'description' => __("Thanh toán hoàn thành xử lý dịch vụ thủ công #") . $data->id,
                    'order_id' => $data->id,
                    'status' => 1,
                ]);

            }

            $data->process_at = Carbon::now();//thời gian xác nhận đơn hàng
            $data->save();

            if ($data->idkey == 'anime_defenders_auto'){

                $roblox_order = Roblox_Order::query()
                    ->where('order_id',$data->id)
                    ->where('type_order',10)
                    ->first();

                if (!isset($roblox_order)){
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Không tìm thấy đơn hàng roblox cần xử lý'));
                }

                $roblox_order->status = 'danhan';
                $roblox_order->save();
            }

            $this->callbackToShop($data,__('Hoàn tất thành công yêu cầu dịch vụ'),null,null);

            ActivityLog::add($request,__("Hoàn tất thành công yêu cầu dịch vụ ").config('module.service-purchase.key').' #'.$id );

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return redirect()->back()->withErrors(__('Có lỗi phát sinh.Xin vui lòng thử lại !'));
        }

        // Commit the queries!
        DB::commit();
        return redirect()->back()->with('success', __("Hoàn tất thành công yêu cầu dịch vụ #") . $data->id);

    }

    public function postPengiriman(Request $request, $id)
    {
        if (!Auth::user()->can('service-purchase-edit-pengiriman')){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        DB::beginTransaction();
        try {

            $data = Order::query()
                ->with(['item_ref','author'])
                ->where('idkey',"gamepass_roblox")
                ->where('module', '=', config('module.service-purchase'))
                ->whereIn('status', [4,10])
                ->lockForUpdate()
                ->findOrFail($id);

            if(isset(Auth::user()->type_information_ctv) && Auth::user()->type_information_ctv == 1 && $data->idkey == "gamepass_roblox"){
                if (!$request->filled('edit_id_pengiriman') || !$request->filled('edit_account_pengiriman')) {
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Vui lòng nhập đầy đủ thông tin id lô hàng và account lô hàng'));
                }
            }

            $edit_id_pengiriman = $request->get('edit_id_pengiriman');
            $edit_account_pengiriman = $request->get('edit_account_pengiriman');

//            if($data->processor_id != Auth::guard()->user()->id){
//                DB::rollback();
//                return redirect()->back()->withErrors(__('Bạn không phải là người tiếp nhận xử lý đơn này. Vui lòng thử lại'));
//            }

            $order_detail = OrderDetail::query()
                ->where('module','pengiriman')
                ->where('order_id',$data->id)
                ->first();

            if (!isset($order_detail)){
                OrderDetail::create([
                    'order_id' => $data->id,
                    'module' => 'pengiriman',
                    'title' => $edit_id_pengiriman??'',
                    'description' => $edit_account_pengiriman??'',
                    'content' => "Nhập hàng",
                    'status' => 4,
                ]);
            }else{
                $order_detail->title = $edit_id_pengiriman;
                $order_detail->description = $edit_account_pengiriman;
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

    public function postPengirimanAll(Request $request)
    {
        if (!Auth::user()->can('service-purchase-edit-pengiriman')){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        $input=explode(',',$request->id_pengiriman);

        if (!$request->filled('edit_id_pengiriman') || !$request->filled('edit_account_pengiriman')) {
            return redirect()->back()->withErrors(__('Vui lòng nhập đầy đủ thông tin id lô hàng và account lô hàng'));
        }

        $edit_id_pengiriman = $request->get('edit_id_pengiriman');
        $edit_account_pengiriman = $request->get('edit_account_pengiriman');

        foreach ($input??[] as $idOrderNeedRecharge){
            DB::beginTransaction();
            try {

                $data = Order::query()
                    ->where('id',$idOrderNeedRecharge)
                    ->with(['item_ref','author'])
                    ->where('idkey',"gamepass_roblox")
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
                    OrderDetail::create([
                        'order_id' => $data->id,
                        'module' => 'pengiriman',
                        'title' => $edit_id_pengiriman??'',
                        'description' => $edit_account_pengiriman??'',
                        'content' => "Nhập hàng",
                        'status' => 4,
                    ]);
                }else{
                    $order_detail->title = $edit_id_pengiriman;
                    $order_detail->description = $edit_account_pengiriman;
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

    public function postRefundDelete(Request $request, $id)
    {

        DB::beginTransaction();
        try {

            $data = Order::with('item_ref','author')
                ->where('module', '=', config('module.service-purchase'))
                ->whereIn('status', [0,3,5])
                ->where('id',$request->get('id'))
                ->lockForUpdate()->first();

            if(!isset($data)){
                DB::rollback();
                return redirect()->back()->withErrors(__('Không tìm thấy đơn hàng'));
            }

            //check nếu là dịch vu auto thì không thể tiếp nhận

            if ($data->gate_id == "1") {
                DB::rollback();
                return redirect()->back()->withErrors(__('Không thể hoàn tất dịch vụ có hệ thống tự động'));
            }

            if (isset($data->type_refund) && $data->type_refund == 1){
                //set tiến độ hoàn tất
                OrderDetail::create([
                    'order_id'=>$data->id,
                    'module' => config('module.service-workflow.key'),
                    'author_id' =>  Auth::guard()->user()->id,
                    'status' => 10,
                ]);

                $data->status = 10;
            }
            else{

                $userTransaction = User::where('id', $data->processor_id)->lockForUpdate()->first();

                if(!isset($userTransaction)){
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Không tìm thấy ctv'));
                }

                if($userTransaction->checkBalanceValid() == false){
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý'));
                }

                //tính chiết khấu cho người bán

                $ratio = 80;

                //lấy các quyền được tiếp nhận yêu cầu dịch vụ

                $service_access = ServiceAccess::query()->where('module','user')->with('user')->where('user_id', $userTransaction->id)
                    ->first();

                if (isset($service_access)){

                    if (isset($service_access->params)){
                        $param = json_decode(isset($service_access->params) ? $service_access->params : "");
                        if(isset($param->{'ratio_' . ($data->item_ref->id??null)})){
                            $ratio= $param->{'ratio_' . ($data->item_ref->id??null)};
                        }
                        else{
                            $ratio=$ratio;
                        }
                    }
                }
                //cộng tiền user

                $real_received_amount = ($ratio * $data->price_ctv) / 100;

                //Cập nhật trạng thái đơn hàng

                $data->status = 4;
                $data->ratio_ctv = $ratio;
                $data->real_received_price_ctv = $real_received_amount;
                $data->save();

                //Cộng tiền cho CYV

                $userTransaction->balance = $userTransaction->balance + $real_received_amount;
                $userTransaction->balance_in = $userTransaction->balance_in + $real_received_amount;
                $userTransaction->save();

                if ($data->idkey == 'anime_defenders_auto'){

                    $roblox_order = Roblox_Order::query()
                        ->where('order_id',$data->id)
                        ->where('type_order',10)
                        ->first();

                    if (!isset($roblox_order)){
                        DB::rollback();
                        return redirect()->back()->withErrors(__('Không tìm thấy đơn hàng roblox cần xử lý'));
                    }

                    $roblox_order->status = 'danhan';
                    $roblox_order->save();
                }

                //set tiến độ hoàn tất

                OrderDetail::create([
                    'order_id'=>$data->id,
                    'module' => config('module.service-workflow.key'),
                    'title' =>  'Thành công',
                    'author_id' =>  Auth::guard()->user()->id,
                    'status' => 4,
                ]);

                //Lưu biến động số dư

                Txns::create([
                    'trade_type'=>'service_completed', //Thanh toán dịch vụ
                    'user_id'=>$userTransaction->id,
                    'is_add' => '1',//Cộng tiền
                    'amount' => $real_received_amount,
                    'real_received_amount' => $real_received_amount,
                    'last_balance' => $userTransaction->balance,
                    'description' => __("Thanh toán hoàn thành xử lý dịch vụ thủ công #") . $data->id,
                    'order_id' => $data->id,
                    'status' => 1,
                ]);
            }

//            $data->process_at = Carbon::now();//thời gian xác nhận đơn hàng
            $data->save();

            $userKtvTransaction = User::where('id', $data->author_id)->lockForUpdate()->first();

            if(!isset($userKtvTransaction)){
                DB::rollback();
                return redirect()->back()->withErrors(__('Không tìm thấy ktv'));
            }

            if($userKtvTransaction->checkBalanceValid() == false){
                DB::rollback();
                return redirect()->back()->withErrors(__('Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý'));
            }

            if ($userKtvTransaction->balance < $data->price) {
                DB::rollBack();
                return redirect()->back()->withErrors(__('Kho thành viên không đủ tiền để thanh toán.Vui lòng nạp thêm tiền vào tài khoản'));
            }

            //trừ tiền user
            $userKtvTransaction->balance = $userKtvTransaction->balance - $data->price;
            $userKtvTransaction->balance_out = $userKtvTransaction->balance_out + $data->price;
            $userKtvTransaction->save();

            $data->txns()->create([
                'trade_type' => 'service_purchase', //Thanh toán dịch vụ
                'user_id' => $userKtvTransaction->id,
                'is_add' => '0',//Trừ tiền
                'amount' => $data->price,
                'real_received_amount' => $data->price,
                'last_balance' => $userKtvTransaction->balance,
                'description' =>  "Chuyển trạng thái đơn dịch vụ do CTV đã hoàn tất đơn hàng# ".$data->id,
                'ip' => $request->getClientIp(),
                'order_id' => $data->id,
                'status' => 1
            ]);

            //set tiến độ
            OrderDetail::create([
                'order_id' => $data->id,
                'module' => config('module.service-workflow.key'),
                'author_id' => Auth::guard()->user()->id,
                'title' =>  'Chuyển trạng thái đơn dịch vụ do CTV đã hoàn tất đơn hàng trong game',
                'status' => 10,
            ]);

//            $this->dispatch(new App\Jobs\CallbackOrderService($data,__('Hoàn tất thành công yêu cầu dịch vụ'),null,null));

            $this->callbackToShop($data,__('Hoàn tất thành công yêu cầu dịch vụ'),null,null);
            ActivityLog::add($request,__("Hoàn tất thành công yêu cầu dịch vụ ").config('module.service-purchase.key').' #'.$id );

        } catch (\Exception $e) {

            DB::rollback();
            Log::error($e);
            return redirect()->back()->withErrors(__('Có lỗi phát sinh.Xin vui lòng thử lại !'));

        }

        // Commit the queries!

        DB::commit();

        return redirect()->back()->with('success', __("Hoàn tất thành công yêu cầu dịch vụ #") . $data->id);
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
            'mistake_by.required' => __("Vui lòng chọn lỗi thuộc về"),
            'note.required' => __("Vui lòng nhập nội dung lỗi"),
            'note.min' => __("Nội dung phải ít nhất 10 ký tự"),
            'note.max' => __("Nội dung phải không quá 500 ký tự"),
        ]);
        // Start transaction!
        DB::beginTransaction();
        try {

            $data = Order::with('item_ref','author','processor')
                ->where('module', '=', config('module.service-purchase'))
                ->where(function ($query) {
                    $query->orWhere('status', "1");
                    $query->orWhere('status', "2");
                })
                ->lockForUpdate()
                ->findOrFail($id);

            if ($data->expired_lock != null && $data->expired_lock > Carbon::now()) {
                return redirect()->back()->withErrors(__("Dịch vụ đã được thực hiện. Vui lòng thử lại trong vòng 5 phút"));
            }
            if($data->status==2){
                if(!Auth::user()->can('service-reception-all')){
                    if($data->processor_id!=Auth::user()->id ){
                        return redirect()->back()->withErrors(__("Bạn không phải người nhận yêu cầu nên không thể hủy"));
                    }
                }
            }
            $mistake_by = config('module.service-purchase.mistake_by.' . $request->mistake_by);
            $note = $request->note;
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

            if ($data->idkey == 'anime_defenders_auto'){

                $roblox_order = Roblox_Order::query()
                    ->where('order_id',$data->id)
                    ->where('type_order',10)
                    ->first();

                if (!isset($roblox_order)){
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Không tìm thấy đơn hàng roblox cần xử lý'));
                }

                $roblox_order->status = 'danhan';
                $roblox_order->save();
            }

            //hoàn tiền cho khách hàng

            $userTransaction = User::where('id', $data->author_id)->lockForUpdate()->firstOrFail();

            if($userTransaction->checkBalanceValid() == false){
                DB::rollback();
                return redirect()->back()->withErrors(__('Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý'));
            }

            $userTransaction->balance = $userTransaction->balance + $data->price;
            $userTransaction->balance_in = $userTransaction->balance_in + $data->price;
            $userTransaction->save();

            Txns::create([
                'trade_type'=>'service_destroy', //Hủy dịch vụ
                'user_id'=>$userTransaction->id,
                'is_add' => '1',//Cộng tiền
                'amount' => $data->price,
                'real_received_amount' => $data->price,
                'last_balance' => $userTransaction->balance,
                'description' => __("Hủy dịch vụ thủ công #") . $data->id,
                'ip' => $request->getClientIp(),
                'order_id' => $data->id,
                'status' => 1,
                'shop_id'=>$data->shop_id
            ]);

            $this->callbackToShop($data,$note,null,$mistake_by);
//            $this->dispatch(new App\Jobs\CallbackOrderService($data,$note,null,$mistake_by));
            //active log active

            ActivityLog::add($request,__("Đã từ chối thành công yêu cầu dịch vụ ").config('module.service-purchase.key').' #'.$id  );



        } catch (\Exception $e) {
            DB::rollback();
            Log::error( $e);
            return redirect()->back()->withErrors(__('Có lỗi phát sinh.Xin vui lòng thử lại !'));
        }
        // Commit the queries!
        DB::commit();
        return redirect()->back()->with('success', __("Đã từ chối thành công yêu cầu dịch vụ #") . $data->id);
    }

    public function postDeleteAll(Request $request)
    {

        if(!Auth::user()->id == 301 && !Auth::user()->id == 5551){
            return redirect()->back()->withErrors(__("Khong co quyen truy cap"));
        }

        $desc = 'The Rift Sorcerer (Awakened)';
        $orders = Order::query()
            ->where('idkey','item_anime_defenders')
            ->where('status',1)
            ->where('price',400000)
            ->where('description', 'LIKE', '%' . $desc . '%')
            ->get();

        foreach ($orders as $order){
            // Start transaction!
            DB::beginTransaction();
            try {

                $data = Order::with('item_ref','author','processor')
                    ->where('module', '=', config('module.service-purchase'))
                    ->where(function ($query) {
                        $query->orWhere('status', "1");
                    })
                    ->where('id',$order->id)
                    ->lockForUpdate()
                    ->first();

                if (!isset($data)) {
                    DB::rollback();
                    continue;
                }

                if ($data->expired_lock != null && $data->expired_lock > Carbon::now()) {
                    DB::rollback();
                    continue;
                }

                $mistake_by = 0;
                $note = 'This product is out of stock';
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

                //hoàn tiền cho khách hàng

                $userTransaction = User::where('id', $data->author_id)->lockForUpdate()->firstOrFail();

                if($userTransaction->checkBalanceValid() == false){
                    DB::rollback();
                    continue;
                }

                $userTransaction->balance = $userTransaction->balance + $data->price;
                $userTransaction->balance_in = $userTransaction->balance_in + $data->price;
                $userTransaction->save();

                Txns::create([
                    'trade_type'=>'service_destroy', //Hủy dịch vụ
                    'user_id'=>$userTransaction->id,
                    'is_add' => '1',//Cộng tiền
                    'amount' => $data->price,
                    'real_received_amount' => $data->price,
                    'last_balance' => $userTransaction->balance,
                    'description' => __("Hủy dịch vụ thủ công #") . $data->id,
                    'ip' => $request->getClientIp(),
                    'order_id' => $data->id,
                    'status' => 1,
                    'shop_id'=>$data->shop_id
                ]);

                $this->callbackToShop($data,$note,null,$mistake_by);

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


    public function postInbox(Request $request, $id)
    {


        $this->validate($request, [
            //'captcha' => 'required|captcha'
        ], [
            'captcha.required' => __("Vui lòng nhập mã bảo vệ"),
            'captcha.captcha' => __("Mã bảo vệ không đúng"),
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
        ActivityLog::add($request, 'Gửi tin nhắn thành công '.config('constants.module.service.key_purchase').'_manual'.' #'.$id  );
        return redirect()->back()->with('success', 'Gửi tin nhắn thành công');
    }

    public function getCount(Request $request)
    {

        $group_ids = [];

//        if (!Auth::user()->can('service-reception-all')) {
//            //lấy các quyền được xem yêu cầu dịch vụ
//            $service_access = ServiceAccess::query()->where('module','user')->with('user')->where('user_id', Auth::user()->id)
//                ->first();
//
//            $param = json_decode(isset($service_access->params) ? $service_access->params : "");
//            $group_ids = isset($param->view_role) ? $param->view_role : [];
//        }
//
////        if(Auth::user()->account_type == 1){
////            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
////        }
////
////        if (!isset($arr_permission)){
////            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
////        }
//
//        $datatable = Order::where('order.module', config('module.service-purchase.key'))
//            ->where('status', 1) //đang chờ
//            //lấy điều kiện đơn bt
//            ->where(DB::raw('COALESCE(order.gate_id,0)'), '<>', 1 );
//
//        if(session('shop_id')){
//            $datatable = $datatable->where('shop_id',session('shop_id'));
//        }
//
//
//        //nếu user ko full quyền nhận các dịch vụ thì lấy các id dịch vụ được cấp quyền
//        if (!Auth::user()->can('service-reception-all')) {
//
//            //nếu có lọc dịch vụ thì chỉ chấp nhận lọc các dịch vụ cho phép
//            if ($request->filled('group_id')) {
//
//                $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
//                    $groupAllowView = array_intersect($group_ids,(array)$request->get('group_id'));
//                    $query->whereIn('id', $groupAllowView);
//                });
//            }
//            //else lọc dịch vụ all các dịch vụ cho phép
//            else{
//                $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
//                    $groupAllowView = array_intersect($group_ids,(array)$request->get('group_id'));
//                    $query->whereIn('id', $group_ids);
//                });
//            }
//        }
//        //nếu user có full quyền nhận các dịch vụ thì lấy luôn id dịch vụ đó
//        else{
//            if ($request->filled('group_id')) {
//                $datatable->whereHas('item_ref', function ($query) use ($request,$group_ids) {
//                    $query->where('id',$request->get('group_id'));
//                });
//            }
//        }
//        $datatable=$datatable->count();
        $datatable = 99;
        return response()->json([
            'status'=>1,
            'data'=>$datatable
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
                'note.required' => __("Vui lòng nhập nội dung lỗi"),
                'note.min' => __("Nội dung phải ít nhất 10 ký tự"),
                'note.max' => __("Nội dung phải không quá 500 ký tự"),
                'btn_submit_refund.required' => __("Vui lòng chọn loại hoàn tiền"),
            ]);
            if($validator->fails()){
                DB::rollback();
                return redirect()->back()->withErrors(__($validator->errors()->first()));

            }

            $data = Order::query()
                ->where('module', config('module.service-purchase.key'))
                ->where('status', 4)
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
                $note = __("Có hoàn tiền");
            }else{
                $note = __("Không hoàn tiền");
            }
            //set tiến độ hoan tien
            OrderDetail::create([
                'order_id' => $data->id,
                'module' => config('module.service-workflow.key'),
                'author_id' => Auth::user()->id,
                'status' => 5,
                'content' => $request->note.' ('.$note.')',
            ]);

            if ($data->idkey == 'anime_defenders_auto'){

                $roblox_order = Roblox_Order::query()
                    ->where('order_id',$data->id)
                    ->where('type_order',10)
                    ->first();

                if (!isset($roblox_order)){
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Không tìm thấy đơn hàng roblox cần xử lý'));
                }

                $roblox_order->status = 'dahuybo';
                $roblox_order->save();
            }

            if ($request->get('btn_submit_refund') == 'refund'){
                //Cộng tiền cho cộng tác viên.

                $userCTVTransaction = User::where('id', $data->processor_id)->lockForUpdate()->firstOrFail();

                if ($userCTVTransaction->checkBalanceValid() == false) {

                    DB::rollback();
                    return redirect()->back()->withErrors(__('Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý'));

                }

                if ($data->real_received_price_ctv <= 0){
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Số tiền thanh toán trước đó không hợp lệ'));

                }

                $real_received_amount = $data->real_received_price_ctv;

                if ($userCTVTransaction->balance < $real_received_amount){
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Số tiền trong tài khoản ctv không đủ'));
                }
                $userCTVTransaction->balance = $userCTVTransaction->balance - $real_received_amount;
                $userCTVTransaction->balance_out=$userCTVTransaction->balance_out+$real_received_amount;
                $userCTVTransaction->save();

                //tạo tnxs
                $txns = Txns::create([
                    'trade_type' => 'minus_money',//Trừ tiền
                    'is_add' => '0',//Trừ tiền
                    'user_id' => $userCTVTransaction->id,
                    'amount' => $real_received_amount,
                    'real_received_amount' => $real_received_amount,
                    'last_balance' => $userCTVTransaction->balance,
                    'description' => __('Trừ tiền thủ công khi chuyển trạng thái hoàn tất (4) sang thất bại(5) #') . $data->id,
                    'order_id' => $data->id,
                    'ip' => $request->getClientIp(),
                    'status' => 1
                ]);

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
                    'description' => __('Hoàn tiền thủ công khi chuyển trạng thái hoàn tất (4) sang thất bại(5) #') . $data->id,
                    'order_id' => $data->id,
                    'ip' => $request->getClientIp(),
                    'status' => 1
                ]);

                $data->real_received_price_ctv = 0;
                $data->save();

            }

            $this->callbackToShop($data,$request->note,$request->get('btn_submit_refund'),null);

//                $this->dispatch(new App\Jobs\CallbackOrderService($data,$request->note,$request->get('btn_submit_refund'),null));

            ActivityLog::add($request, __('Chuyển đổi trạng thái thành công đơn hàng #'). $data->id);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error( $e);
            DB::rollback();
            return redirect()->back()->withErrors(__('Có lỗi phát sinh.Xin vui lòng thử lại !'));

        }
        // Commit the queries!
        DB::commit();
        //active log active

        return redirect()->back()->with('success', __('Chuyển đổi trạng thái thành công'));

    }

    public function postRecallback(Request $request){

        $input=explode(',',$request->id);
        $data = Order::where('module', '=', config('module.service-purchase.key'))
            ->where(function($q){
                $q->orWhere('status','!=',  1);
//                $q->orWhere('status','!=',  2);
            })
            ->whereIn('id',$input)->get();

        foreach ($data?$data:[] as $item){

            if($item->url!=""){

                $message = config('module.service-purchase.status.'.$item->status);

//                $this->dispatch(new App\Jobs\CallbackOrderService($item,$message,null,null));
                $this->callbackToShop($item,$message,null,null);
            }

        }

        //active log active
        ActivityLog::add($request, "Đã recallback dịch vụ tự động #".json_encode($data,JSON_UNESCAPED_UNICODE));

        return redirect()->back()->with('success', "Đã callback thành công");

    }

    public function postRechang(Request $request,$id){

        $datatable = Order::query()
            ->with(['item_ref','author','order_refund','order_service_workflow','order_service_workname'])
            ->where('module', config('module.service-purchase'))
            ->where('idkey','anime_defenders_auto')
            ->where('status',2)
            ->where('processor_id',Auth::user()->id)
            ->where('gate_id',0)->findOrFail($id);

        $roblox_order = Roblox_Order::query()
            ->where('order_id',$datatable->id)
            ->where('status','recharge')
            ->first();

        if (!isset($roblox_order)){
            return redirect()->back()->withErrors(__("Không tìm thấy đơn hàng roblox"));
        }

        if (!$request->filled('bot_id')){
            DB::rollback();
            return redirect()->back()->withErrors(__('Vui lòng chọn bot xử lý'));
        }

        $bot_id = $request->get('bot_id');
        $bot = Roblox_Bot::query()
            ->where('type_order',2)
            ->where('type_bot',2)
            ->orderBy('ver', 'asc')
            ->where('status',1)
            ->where('id',$bot_id)
            ->orderBy('server', 'asc')->first();

        if (!isset($bot)){
            DB::rollback();
            return redirect()->back()->withErrors(__('Không tìm thấy bot cần xử lý'));
        }

        $roblox_order->bot_handle = $bot->id;
        $roblox_order->status = 'gandonchobot';
        $roblox_order->save();

        //set tiến độ tiếp nhận
        OrderDetail::create([
            'order_id'=>$datatable->id,
            'module' => config('module.service-workflow.key'),
            'author_id' =>  Auth::guard()->user()->id,
            'title'=> 'Bot: '.$bot->acc.' sẽ xử lý lại đơn hàng',
            'description'=> $bot->id,
            'status' => "2",
        ]);

        //active log active
        ActivityLog::add($request, "Đã Rechang dịch vụ thủ công #".json_encode($datatable,JSON_UNESCAPED_UNICODE));

        return redirect()->back()->with('success', "Đã gọi lại đơn hàng thành công");

    }

    public function callbackToShop(Order $order,$message,$refund = null,$mistake_by = null)
    {

        $url = $order->url;

        $data = array();

        $data['status'] = $order->status;

        if (isset($refund)){
            $data['refund'] = $refund;
        }

        $data['message'] = $message;

        if (isset($mistake_by)){
            $data['mistake_by'] = $mistake_by;
        }

        $data['input_auto'] = 0;

        if ($order->status == 4){
            $data['price'] = $order->real_received_price_ctv;
        }

        if ($order->status == 4 || $order->status == 10){
            $data['process_at'] = strtotime($order->process_at);
        }

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
                $path = storage_path() ."/logs/curl_callback-service-to-shop-".Carbon::now()->format('Y-m-d');
                if(!\File::exists($path)){
                    \File::makeDirectory($path, $mode = "0755", true, true);
                }
                $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
                \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");
//
//                $myfile = fopen(storage_path() . "/logs/curl_callback-service-to-shop-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
//                $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
//                fwrite($myfile, $txt);
//                fclose($myfile);

                if($httpcode==200){

                    if(strpos($resultRaw, __("Có lỗi phát sinh.Xin vui lòng thử lại")) > -1){
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

}
