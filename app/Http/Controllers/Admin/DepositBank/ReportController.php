<?php

namespace App\Http\Controllers\Admin\DepositBank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\Order;
use App\Models\Txns;
use App\Models\User;
use Carbon\Carbon;
use Log;
use DB;
use Auth;

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
        $this->middleware('permission:deposit-bank-report-list');
        //$this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate']]);
        //$this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        //$this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);


        $this->page_breadcrumbs[] = [
            'page' => route('admin.deposit-bank-report.index'),
            'title' => __('Thống kê nạp tiền qua ngân hàng')
        ];
    }


    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập danh sách thống kê nạp tiền qua ngân hàng');
        if($request->ajax()){
            $datatable = Order::with('author')->where('module','=','charge_bank')->where('payment_type',1);
            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', 'LIKE', '%' . $request->get('id') . '%');
                });
            }
            if ($request->filled('status')) {
                $datatable->where('status',$request->get('status') );
            }
            if ($request->filled('fullname_display')) {
                $datatable->whereHas('author', function ($query) use ($request) {
                    $query->where(function ($qChild) use ($request){
                        $qChild->where('fullname_display', $request->get('fullname_display'));
                    });
                });
            }
            if ($request->filled('started_at')) {
                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }
            return \datatables()->eloquent($datatable)
            ->editColumn('created_at', function($data) {
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
            ->editColumn('price', function($data) {
                return number_format($data->price);
            })
            ->addColumn('total_amount', function($data) {
                $total_amount = "";
                if(isset($data->params) && $data->params != ""){
                   $params = json_decode($data->params);
                   $total_amount = number_format($params->total_amount);
               }
                return $total_amount;
            })
            ->addColumn('discount_amount', function($data) {
                $discount_amount = "";
                if(isset($data->params) && $data->params != ""){
                   $params = json_decode($data->params);
                   $discount_amount = number_format($params->discount_amount);
               }
                return $discount_amount;
            })
            // ->addColumn('action', function($row) {
            //     $temp= "<a href=\"".route('admin.'.$this->module.'.show',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Xem\"><i class=\"la la-eye\"></i></a>";
            //     return $temp;
            // })
            ->toJson();
        }
        return view('admin.deposit-bank.report.index')
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
