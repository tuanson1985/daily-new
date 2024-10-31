<?php

namespace App\Http\Controllers\Admin\Point;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Voucher;
use App\Models\VoucherItem;
use App\Models\VoucherUser;
use Carbon\Carbon;
use App\Library\Helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(Request $request)
    {

        //set permission to function
        $this->middleware('permission:point-report-list');
        $this->middleware('permission:point-report-show', ['only' => ['show']]);


        $this->page_breadcrumbs[] = [
            'page' => route('admin.point-report.index'),
            'title' => __('Thống kê nhận thưởng giftcode')
        ];
    }
    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập thống kê nhận gift code');
        if ($request->ajax) {
            $datatable = VoucherUser::with('author')->with('voucher')->orderBy('id','desc');
            if ($request->filled('id')) {
                $datatable->where('id', $request->get('id'));
            }
            if ($request->filled('username')) {
                $datatable->whereHas('author', function ($query) use ($request) {
                    $query->where(function ($qChild) use ($request){
                        $qChild->orWhere('username', $request->get('username'));
                        $qChild->orWhere('email', $request->get('username'));
                        $qChild->orWhere('fullname_display', 'LIKE', '%' . $request->get('username') . '%');
                    });

                });
            }
            if ($request->filled('giftcode')) {
                $datatable->whereHas('voucher', function ($query) use ($request) {
                    $query->where(function ($qChild) use ($request){
                        $qChild->orWhere('id', $request->get('giftcode'));
                        $qChild->orWhere('code', $request->get('giftcode'));
                    });

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
            //$subDatatable= $datatable;
            return \datatables()->eloquent($datatable)

                ->only([
                    'id',
                    'user',
                    'voucher',
                    'title',
                    'discount',
                    'created_at'
                ])
                ->editColumn('voucher.type', function ($data) {
                    return config('module.point.type.'.$data->voucher->type);
                })
                ->editColumn('discount', function ($data) {
                    if($data->voucher->type == 1){
                        return number_format($data->discount).' VNĐ';
                    }elseif ($data->voucher->type == 2){
                        return number_format($data->discount).' VNĐ';
                    }
                })
                ->editColumn('created_at', function ($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->addColumn('user', function($row) {
                    $result = "";
                    if($row->author->fullname_display != ""){
                        $result .= $row->author->fullname_display;
                    }
                    if($row->author->email != ""){
                        $result .= ' - '.$row->author->email;
                    }
                    return $result;
                })
                ->addColumn('action', function($row) {
                    $temp= "<a href=\"".route('admin.store-card-report.show',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Xem chi tiết\"><i class=\"flaticon-medical\"></i></a>";
                    return $temp;
                })
                ->toJson();
        }
        return view('admin.point.report.index')
            ->with('module', null)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
