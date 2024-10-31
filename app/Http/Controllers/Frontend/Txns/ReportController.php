<?php

namespace App\Http\Controllers\Frontend\Txns;

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
        $this->page_breadcrumbs[] = [
            'page' => route('frontend.txns-report.index'),
            'title' => __('Biến động số dư')
        ];
    }
    public function index(Request $request)
    {

        ActivityLog::add($request, 'Truy cập biến động số dư txns-report');
        if ($request->ajax) {
            $datatable = Txns::query()
                ->where('user_id',auth('frontend')->user()->id)
                ->with('user','shop')
                ->whereHas('user', function ($query) use ($request) {
                    $query->where('account_type', 2);
                });
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
                ->addColumn('username', function ($row) {
                    $temp = '';
                    if(isset($row->user->username)){
                        $temp .= str_replace('tt_', '', $row->user->username);
                    }
                    return $temp;
                })
                ->addColumn('account_type', function ($row) {
                    return $row->user->account_type??"";
                })
                ->addColumn('action', function ($row) {
                    $temp = "<button type=\"button\" class=\"btn btn-outline-secondary load-modal\" rel=\"".route('frontend.txns-report.show',$row->id)."\">".__('Chi tiết')."</button>";
                    return $temp;
                })
                ->toJson();
        }
        return view('frontend.txns.report.index')
            ->with('module', null)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }
    public function show(Request $request,$id)
    {
        $datatable=Txns::with('user')->findOrFail($id);
        ActivityLog::add($request, 'Xem chi tiết biến động số dư txns-report #'.$id);
        return view('frontend.txns.report.show', compact('datatable'));
    }

}
