<?php

namespace App\Http\Controllers\Admin\PlusMoney;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PlusMoney;
use App\Models\Txns;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;


class ReportQTVController extends Controller
{
    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;
    public function __construct(Request $request)
    {
        //set permission to function
        $this->middleware('permission:plusmoney-report-qtv-list');
        $this->page_breadcrumbs[] = [
            'page' => route('admin.plusmoney-report-qtv.index'),
            'title' => __('Lịch sử Cộng trừ/tiền QTV(CTV)')
        ];
    }
    public function index(Request $request)
    {

        ActivityLog::add($request, 'Truy cập lịch sử cộng tiền QTV (CTV)');
        if ($request->ajax) {
            $datatable = PlusMoney::with('user','processor');
            $datatable->whereHas('user', function ($query) use ($request) {
                $query->where(function ($queryChild)use ($request){
//                    $queryChild->orWhere('account_type', 1);
                    $queryChild->orWhere('account_type', 2);
                });
            });
            // kiểm tra điều kiện truy cập
            // trường hợp qtv duyệt tiền là admin

            if ($request->filled('id')) {
                $datatable->where('id', $request->get('id'));
            }

            if ($request->filled('username')) {

                $datatable->whereHas('user', function ($query) use ($request) {
                    $query->orWhere('username', $request->get('username'));
                    $query->orWhere('email', $request->get('username'));
                });
            }


            if ($request->filled('is_add')) {
                $datatable->where('is_add', $request->get('is_add'));
            }

            if ($request->filled('status')) {
                $datatable->where('status', $request->get('status'));
            }
            if ($request->filled('source_type')) {
                $datatable->where('source_type', $request->get('source_type'));
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
            //$subDatatable= $datatable;
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
        return view('admin.plusmoney.report-qtv.index')
            ->with('module', null)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }


    public function show(Request $request,$id)
    {
        $datatable=Txns::with('user')->findOrFail($id);

        ActivityLog::add($request, 'Xem chi tiết biến động số dư plusmoney-report #'.$id);

        return view('admin.plusmoney.report-qtv.show', compact('datatable'));
        //$data = Group::findOrFail($id);
        //ActivityLog::add($request, 'Show '.$this->module.' #'.$data->id);
        //return view('admin.module.item.show', compact('datatable'));
    }

}
