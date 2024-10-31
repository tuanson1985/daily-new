<?php

namespace App\Http\Controllers\Admin\Booking;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Telecom;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ReportController extends Controller
{


    public function __construct(Request $request)
    {

        //set permission to function
        $this->middleware('permission:charge-report-list');
        //$this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate']]);
        //$this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        //$this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);


        $this->page_breadcrumbs[] = [
            'page' => route('admin.booking-report.index'),
            'title' => __('Thống kê booking')
        ];
    }


    public function index(Request $request)
    {


        ActivityLog::add($request, 'Truy cập thống kê donate-report');
        if ($request->ajax) {
            $datatable = Order::with('author')
                ->with('user_ref')
                ->where('module','booking');


            if ($request->filled('id')) {
                $datatable->where('id', $request->get('id'));
            }


            if ($request->filled('username')) {
                $datatable->whereHas('author', function ($query) use ($request) {
                    $query->where(function ($qChild) use ($request){
                        $qChild->where('username', $request->get('username'));
                    });

                });
            }
            if ($request->filled('email')) {
                $datatable->whereHas('author', function ($query) use ($request) {
                    $query->where(function ($qChild) use ($request){
                        $qChild->where('email', $request->get('email'));
                    });

                });
            }


            if ($request->filled('user_ref')) {
                $datatable->whereHas('user_ref', function ($query) use ($request) {
                    $query->where(function ($qChild) use ($request){
                        $qChild->orWhere('username', $request->get('user_ref'));
                        $qChild->orWhere('email', $request->get('user_ref'));
                    });

                });
            }


            if ($request->filled('amount')) {
                $datatable->where('amount', $request->get('amount'));
            }

            if ($request->filled('status')) {
                $datatable->where('status', $request->get('status'));
            }

            if($request->filled('started_at') || $request->filled('ended_at')) {

                if ($request->filled('started_at')) {
                    $datatable->where('order.created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
                }
                if ($request->filled('ended_at')) {
                    $datatable->where('order.created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
                }

            }
            else{
                $datatable->where('order.created_at', '>=', Carbon::now()->startOfDay());
                $datatable->where('order.created_at', '<=', Carbon::now()->endOfDay());
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
                ->addColumn('action', function ($row) {
                    $temp = "";
                    return $temp;
                })

                ->with('totalSumary', function() use ($datatable) {
                   return $datatable->first([
                       DB::raw('SUM(price) as total_price'),
                       DB::raw('SUM(IF(status = 4, price, 0)) as total_success'),
                       DB::raw('SUM(IF(status != 4, price, 0)) as total_wrong_amount'),
                       DB::raw('SUM(IF(status = 1, real_received_price, 0)) as total_real_received_price'),
                   ]);

                })
                ->toJson();
        }

        $telecom = Telecom::where('type_charge', 0)->pluck('title','key')->toArray();

        return view('admin.booking.report.index')
            ->with('module', null)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('telecom', $telecom);

    }





}
