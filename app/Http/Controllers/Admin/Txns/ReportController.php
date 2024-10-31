<?php

namespace App\Http\Controllers\Admin\Txns;

use App\Exports\TxnsExport;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Txns;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ReportController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;
    public function __construct(Request $request)
    {
        $this->middleware('permission:txns-report-list|txns-person-report-list|txns-report-in-shop-list');
        $this->page_breadcrumbs[] = [
            'page' => route('admin.txns-report.index'),
            'title' => __('Biến động số dư')
        ];
    }
    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập biến động số dư txns-report');
        if ($request->ajax) {

            $datatable = Txns::with('user','shop')->orderBy('created_at','desc');
            if ($request->filled('id')) {
                $datatable->where('id', $request->get('id'));
            }
            if ($request->filled('username')) {
                $datatable->whereHas('user', function ($query) use ($request) {
                    $query->where('username',$request->get('username'));
                });
            }
            if ($request->filled('email')) {
                $datatable->whereHas('user', function ($query) use ($request) {
                    $query->where('email', $request->get('email'));
                });
            }
            if ($request->filled('account_type')) {
                $datatable->whereHas('user', function ($query) use ($request) {
                    $query->where('account_type', $request->get('account_type'));
                });
                $datatable->with('user', function ($query) use ($request) {
                    $query->where('account_type', $request->get('account_type'));
                });
            }
            if ($request->filled('trade_type')) {
                $datatable->where('trade_type', $request->get('trade_type'));
            }
            if ($request->filled('is_add')) {
                $datatable->where('is_add', $request->get('is_add'));
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
            // trường hợp là admin hoặc có quyền xem tất cả danh sách của all shop
            if(Auth::user()->hasAllRoles('admin') || Auth::user()->can('txns-report-list')){

            }
            // trường hợp có quyền xem các điểm bán được gắn tag
            elseif(Auth::user()->can('txns-report-in-shop-list')){
                // trường hợp đang search
                if($request->filled('shop_id')){
                    // lấy id danh sách các shop được truy cập
                    $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                    // nếu không có hoặc là all thì ko trả kết quả vì conflict với quyền
                    if(empty($shop_id_shop_access) || $shop_id_shop_access == 'all'){
                        $datatable->whereNull('id');
                    }
                    // loại bỏ những shop không đc phép truy cập và search dữ liệu
                    else{
                        $shop_id_shop_access_search = array_intersect($shop_id_shop_access,$request->get('shop_id'));
                        $datatable->whereIn('shop_id', $shop_id_shop_access_search);
                    }
                }
                else{
                    // trường hợp có lựa chọn shop trên thanh select
                    if(session('shop_id')){
                        $datatable->where('shop_id',session('shop_id'));
                    }
                    // trường hợp không lựa chọn shop trên thanh select
                    else{
                        $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                           // nếu không có hoặc là all thì ko trả kết quả vì conflict với quyền
                        if(empty($shop_id_shop_access) || $shop_id_shop_access == 'all'){
                            $datatable->whereNull('id');
                        }
                        // lấy danh sách các shop được phép truy cập và trả thông tin
                        else{
                            $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                            $datatable->whereIn('shop_id',$shop_id_shop_access);

                        }
                    }
                }
            }
            // trường hợp có quyền xem biến động số dư của riêng mình
            elseif(Auth::user()->can('txns-person-report-list')){
                $datatable->where('user_id', Auth::guard()->user()->id);
            }
            // nếu không có thì không được xem gì cả
            else{
                $datatable->whereNull('id');
            }


            return \datatables()->eloquent($datatable)
                ->editColumn('created_at', function ($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->editColumn('updated_at', function ($data) {
                    return date('d/m/Y H:i:s', strtotime($data->updated_at));
                })
                ->addColumn('shop', function ($row) {
                    if(Auth::guard()->user()->can('txns-report-list')){
                        return $row->shop->domain??"";
                    }
                    else{
                        return "";
                    }
                })
                ->addColumn('username', function ($row) {
                    $temp = '';
                    if(isset($row->user->username)){
                        $temp .= $row->user->username;
                    }
                    return $temp;
                })
                ->addColumn('account_type', function ($row) {
                    return $row->user->account_type??"";
                })
                ->addColumn('action', function ($row) {
                    $temp = "<button type=\"button\" class=\"btn btn-outline-secondary load-modal\" rel=\"".route('admin.txns-report.show',$row->id)."\">".__('Chi tiết')."</button>";
                    return $temp;
                })
                ->toJson();
        }
        return view('admin.txns.report.index')
            ->with('module', null)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }
    public function show(Request $request,$id)
    {
        $datatable=Txns::with('user')->findOrFail($id);
        ActivityLog::add($request, 'Xem chi tiết biến động số dư txns-report #'.$id);
        return view('admin.txns.report.show', compact('datatable'));
    }
    public function exportExcel(Request $request){
        $export = new TxnsExport($request);
        return \Excel::download($export, 'Thống kê dịch vụ thủ công_ ' . time() . '.xlsx');
    }
}
