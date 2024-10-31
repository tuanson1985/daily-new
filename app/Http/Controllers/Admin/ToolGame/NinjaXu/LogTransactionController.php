<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 20/12/2018
 * Time: 14:43 CH
 */


namespace App\Http\Controllers\Admin\ToolGame\NinjaXu;

use App\Exports\ExportData;
use App\Http\Controllers\Controller;
use App\Models\Bot;
use App\Models\Bot_UserNap;
use App\Models\Item;
use App\Models\KhachHang;
use App\Models\NinjaXu_KhachHang;
use App\Models\Nrogem_GiaoDich;
use App\Models\Shop;
use App\Models\SubItem;
use Auth;
use Carbon\Carbon;
use DB;
use Excel;
use Illuminate\Http\Request;
use Log;
use Session;


class LogTransactionController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;

    public function __construct()
    {


        $this->module='ninjaxu-logtransaction';

        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');

        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.ninjaxu-logtransaction.index'),
                'title' => __('Bán xu ninja - Thống kê giao dịch')
            ];
        }

    }



    public function index(Request $request)
    {


        if ($request->ajax()) {



            $datatable=NinjaXu_KhachHang::query()
                ->with(['order'=>function($query) use ($request){
                    $query->with(['author'=>function($query) use ($request){

                    }]);
                }]);

            if ($request->filled('id')) {

                $datatable->where(function($q) use ($request) {
                    $q->orWhere('id',$request->get('id'));
                    $q->orWhere('order_id',$request->get('id'));
                    $q->orWhereHas('order', function ($query) use ($request) {
                        $query->where('request_id_customer', $request->get('id'));
                    });
                });

            }

            if ($request->filled('username')) {
                $datatable->whereHas('order', function ($query) use ($request) {
                    $query->whereHas('author', function ($query) use ($request) {
                        $query->where('username', $request->get('username'));
                    });
                });
            }


            if ($request->filled('ver')) {
                $datatable->where('ver', $request->get('ver'));

            }
            if ($request->filled('server')) {
                $datatable->where('server',  $request->get('server'));
            }

            if ($request->filled('status')) {
                $datatable->where('status', 'LIKE', '%' . $request->get('status') . '%');
            }

            if ($request->filled('status_order')) {
                $datatable->whereHas('order', function ($query) use ($request) {
                    $query->where('status', $request->get('status_order'));
                });
            }

            if ($request->filled('started_at')) {

                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }

            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }

            if(

                $request->filled('id') ||
                $request->filled('username') ||
                $request->filled('status_order') ||
                $request->filled('ver') ||
                $request->filled('server') ||
                $request->filled('status') ||
                $request->filled('started_at') ||
                $request->filled('ended_at')||
                $request->filled('finished_started_at')||
                $request->filled('finished_ended_at')
            ){
                if ($request->filled('finished_started_at')) {
                    $datatable->where('updated_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_started_at')));
                }

                if ($request->filled('finished_ended_at')) {
                    $datatable->where('updated_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_ended_at')));
                }
            }else{

                $datatable->where('updated_at', '>=', Carbon::now()->startOfDay());
                $datatable->where('updated_at', '<=', Carbon::now()->endOfDay());

            }

            $datatableTotal=$datatable->clone();

            return $datatable = \datatables()->eloquent($datatable)

                ->with('totalSumary', function() use ($datatableTotal) {
                    return $datatableTotal=$datatableTotal->first([
                        DB::raw('COUNT(ninjaxu_khachhang.id) as total_record'),
                        DB::raw('SUM(IF(ninjaxu_khachhang.status = \'danhan\', ninjaxu_khachhang.coin, 0)) as total_coin'),
                        DB::raw('SUM(IF(ninjaxu_khachhang.status = \'danap\', ninjaxu_khachhang.coin, 0)) as total_coin_nap'),
//                        DB::raw('SUM(order.price) as total_price')
                    ]);

                })
                ->editColumn('created_at', function ($row) {
                    return date('d/m/Y H:i:s', strtotime($row->created_at));
                })
                ->editColumn('updated_at', function ($row) {
                    return date('d/m/Y H:i:s', strtotime($row->updated_at));
                })
                ->editColumn('username', function ($row) {
                    $html = '';
                    if ($row->order && $row->order->author){
                        $html = $row->order->author->username;
                    }
                    return $html;
                })
                ->editColumn('bot_handle', function ($row) {

                    $html = '';
                    if ($row->bot_handle){
                        $html = $row->bot_handle;
                    }
                    return $html;
                })

                ->toJson();
        }

        return view('admin.toolgame.ninjaxu.logtransaction.index')
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    public function exportExcel(Request $request){

        $datatable=NinjaXu_KhachHang::query()
            ->with(['order'=>function($query) use ($request){
                $query->with(['author'=>function($query) use ($request){

                }]);
            }]);

        if ($request->filled('id')) {

            $datatable->where(function($q) use ($request) {
                $q->orWhere('id',$request->get('id'));
                $q->orWhere('order_id',$request->get('id'));
                $q->orWhereHas('order', function ($query) use ($request) {
                    $query->where('request_id_customer', $request->get('id'));
                });
            });

        }

        if ($request->filled('username')) {
            $datatable->whereHas('order', function ($query) use ($request) {
                $query->whereHas('author', function ($query) use ($request) {
                    $query->where('username', $request->get('username'));
                });
            });
        }


        if ($request->filled('ver')) {
            $datatable->where('ver', $request->get('ver'));

        }
        if ($request->filled('server')) {
            $datatable->where('server',  $request->get('server'));
        }

        if ($request->filled('status')) {
            $datatable->where('status', 'LIKE', '%' . $request->get('status') . '%');
        }

        if ($request->filled('status_order')) {
            $datatable->whereHas('order', function ($query) use ($request) {
                $query->where('status', $request->get('status_order'));
            });
        }

        if ($request->filled('started_at')) {

            $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
        }

        if ($request->filled('ended_at')) {
            $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
        }

        if(

            $request->filled('id') ||
            $request->filled('username') ||
            $request->filled('status_order') ||
            $request->filled('ver') ||
            $request->filled('server') ||
            $request->filled('status') ||
            $request->filled('started_at') ||
            $request->filled('ended_at')||
            $request->filled('finished_started_at')||
            $request->filled('finished_ended_at')
        ){
            if ($request->filled('finished_started_at')) {
                $datatable->where('updated_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_started_at')));
            }

            if ($request->filled('finished_ended_at')) {
                $datatable->where('updated_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_ended_at')));
            }
        }else{

            $datatable->where('updated_at', '>=', Carbon::now()->startOfDay());
            $datatable->where('updated_at', '<=', Carbon::now()->endOfDay());

        }

        $datatable= $datatable->get();
        $data = [
            'data' => $datatable,
        ];
        return Excel::download(new ExportData($data,view('admin.toolgame.ninjaxu.logtransaction.excel')), 'Thống kê giao dịch tool ninjaxu_ ' . time() . '.xlsx');
    }
}
