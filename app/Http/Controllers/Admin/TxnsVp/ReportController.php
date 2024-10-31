<?php

namespace App\Http\Controllers\Admin\TxnsVp;

use App\Exports\ExportData;
use App\Http\Controllers\Controller;
use App\Library\HelperPermisionShopMinigame;
use App\Library\Helpers;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\TxnsVp;
use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ReportController extends Controller
{


    public function __construct(Request $request)
    {

        //set permission to function
        $this->middleware('permission:txnsvp-report-list|txnsvp-person-report-list');
        //$this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate']]);
        //$this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        //$this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);


        $this->page_breadcrumbs[] = [
            'page' => route('admin.txnsvp-report.index'),
            'title' => __('Biến động số dư vật phẩm')
        ];
    }


    public function index(Request $request)
    {
        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        ActivityLog::add($request, 'Truy cập biến động số dư txnsvp-report');
        if ($request->ajax) {

            $datatable = TxnsVp::with('user','shop')->whereIn('shop_id',$arr_permission);

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
            if ($request->filled('trade_type')) {
                $datatable->where('trade_type', $request->get('trade_type'));
            }
            if ($request->filled('is_add')) {
                $datatable->where('is_add', $request->get('is_add'));
            }


            if ($request->filled('status')) {
                $datatable->where('status', $request->get('status'));
            }

            if(Auth::guard()->user()->can('txnsvp-report-list')){

                if ($request->filled('shop_id')) {
                    $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                    if(empty($shop_id_shop_access) || $shop_id_shop_access == 'all'){
                        $datatable->whereIn('shop_id', $request->get('shop_id'));
                    }
                    else{
                        $shop_id_shop_access_search = array_intersect($shop_id_shop_access,$request->get('shop_id'));
                        $datatable->whereIn('shop_id', $shop_id_shop_access_search);
                    }
                }
                else{
                    if(session('shop_id')){
                        $datatable->where('shop_id',session('shop_id'));
                    }
                    else{
                        if(isset(Auth::user()->shop_access) &&Auth::user()->shop_access !== "all"){
                            $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                            $datatable->whereIn('shop_id',$shop_id_shop_access);
                        }
                    }
                }
            }
            else{

                $datatable->where('user_id', Auth::guard()->user()->id);
            }


            if ($request->filled('started_at')) {
                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }
            //$subDatatable= $datatable;
            return \datatables()->eloquent($datatable)

                //->only([
                //    'id',
                //    'pin',
                //    'serial',
                //
                //])

                ->editColumn('created_at', function ($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->addColumn('shop', function ($row) {
                    if(Auth::guard()->user()->can('txnsvp-report-list')){
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
                    $temp = "<button type=\"button\" class=\"btn btn-outline-secondary load-modal\" rel=\"".route('admin.txnsvp-report.show',$row->id)."\">".__('Chi tiết')."</button>";
                    return $temp;


                })
                ->toJson();
        }


        return view('admin.txnsvp.report.index')
            ->with('module', null)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }


    public function show(Request $request,$id)
    {
        $datatable=TxnsVp::with('user')->findOrFail($id);

        ActivityLog::add($request, 'Xem chi tiết biến động số dư txnsvp-report #'.$id);

        return view('admin.txnsvp.report.show', compact('datatable'));
        //$data = Group::findOrFail($id);
        //ActivityLog::add($request, 'Show '.$this->module.' #'.$data->id);
        //return view('admin.module.item.show', compact('datatable'));
    }

    public function exportExcel(Request $request){

        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        ActivityLog::add($request, 'Xuất excel thống kê rút vật phẩm txnsvp-export');

        $datatable = TxnsVp::with('user','shop')->whereIn('shop_id',$arr_permission);

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
        if ($request->filled('trade_type')) {
            $datatable->where('trade_type', $request->get('trade_type'));
        }
        if ($request->filled('is_add')) {
            $datatable->where('is_add', $request->get('is_add'));
        }


        if ($request->filled('status')) {
            $datatable->where('status', $request->get('status'));
        }

        if ($request->filled('shop_id')) {
            $shop_id_shop_access = json_decode(Auth::user()->shop_access);
            if(empty($shop_id_shop_access) || $shop_id_shop_access == 'all'){
                $datatable->whereIn('shop_id', $request->get('shop_id'));
            }
            else{
                $shop_id_shop_access_search = array_intersect($shop_id_shop_access,$request->get('shop_id'));
                $datatable->whereIn('shop_id', $shop_id_shop_access_search);
            }
        }
        else{
            if(session('shop_id')){
                $datatable->where('shop_id',session('shop_id'));
            }
            else{
                if(isset(Auth::user()->shop_access) &&Auth::user()->shop_access !== "all"){
                    $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                    $datatable->whereIn('shop_id',$shop_id_shop_access);
                }
            }
        }
        if($request->filled('started_at') || $request->filled('ended_at')){
            if ($request->filled('started_at')) {
                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }
        }else{
            $datatable->whereDate('created_at', Carbon::today())->get();
        }
        $datatable = $datatable->get();
        $data = [
            'data' => $datatable,
        ];

//        return view('admin.txnsvp.report.export_excel')->with('data',$data);
        return Excel::download(new ExportData($data,view('admin.txnsvp.report.export_excel')), 'Thống kê biến động số dư rút vật phẩm ' . time() . '.xlsx');
    }

}
