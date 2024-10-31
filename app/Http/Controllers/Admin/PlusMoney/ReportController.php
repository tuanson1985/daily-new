<?php

namespace App\Http\Controllers\Admin\PlusMoney;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PlusMoney;
use App\Models\Txns;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class ReportController extends Controller
{


    public function __construct(Request $request)
    {

        //set permission to function
        $this->middleware('permission:plusmoney-report-list');
        //$this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate']]);
        //$this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        //$this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);


        $this->page_breadcrumbs[] = [
            'page' => route('admin.plusmoney-report.index'),
            'title' => __('Lịch sử cộng tiền')
        ];
    }


    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập lịch sử cộng tiền thành viên');
        if ($request->ajax) {
            $datatable = PlusMoney::with('processor','shop')->with('user',function($query){
                $query->with('shop');
            });
            $datatable->whereHas('user', function ($query) use ($request) {
                $query->where(function ($queryChild)use ($request){
                    $queryChild->orWhere('account_type', 3);
                });
            });
            if ($request->filled('id')) {
                $datatable->where('id', $request->get('id'));
            }
            if ($request->filled('username')) {
                $datatable->whereHas('user', function ($query) use ($request) {
                    $query->orWhere('username', $request->get('username'));
                    $query->orWhere('email', $request->get('username'));
                });
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
            if ($request->filled('status')) {
                $datatable->where('status', $request->get('status'));
            }
            if ($request->filled('source_type')) {
                $datatable->where('source_type', $request->get('source_type'));
            }
            if ($request->filled('is_add')) {
                $datatable->where('is_add', $request->get('is_add'));
            }
            if ($request->filled('source_bank')) {
                $datatable->where('source_bank', $request->get('source_bank'));
            }
            if ($request->filled('description')) {
                $datatable->where('description', 'LIKE', '%' . $request->get('description') . '%');
            }
            if ($request->filled('started_at')) {
                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }
            return \datatables()->eloquent($datatable)
                ->editColumn('created_at', function ($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->editColumn('user.username', function ($data) {
                    return $data->user->username??"";
                })
                ->editColumn('processor.username', function ($data) {
                    return $data->processor->username??"";
                })
                ->editColumn('created_at', function ($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->addColumn('domain', function ($data) {
                    if(isset($data->shop)){
                        return $data->shop->domain;
                    }
                    elseif(isset($data->user->shop)){
                        return $data->user->shop->domain;
                    }
                    return null;
                })
                ->with('totalSumary', function() use ($datatable) {
                    return $datatable->first([
                        DB::raw('SUM(IF(is_add = 1, 1, 0)) as total_add '),
                        DB::raw('SUM(IF(is_add = 1, amount, 0)) as total_add_amount'),
                        DB::raw('SUM(IF(is_add = 0, 1, 0)) as total_minus'),
                        DB::raw('SUM(IF(is_add = 0, amount, 0)) as total_minus_amount')
                    ]);
                })
                ->toJson();
        }


        return view('admin.plusmoney.report.index')
            ->with('module', null)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }


    public function show(Request $request,$id)
    {
        $datatable=Txns::with('user')->findOrFail($id);

        ActivityLog::add($request, 'Xem chi tiết biến động số dư plusmoney-report #'.$id);

        return view('admin.plusmoney.report.show', compact('datatable'));
        //$data = Group::findOrFail($id);
        //ActivityLog::add($request, 'Show '.$this->module.' #'.$data->id);
        //return view('admin.module.item.show', compact('datatable'));
    }

}
