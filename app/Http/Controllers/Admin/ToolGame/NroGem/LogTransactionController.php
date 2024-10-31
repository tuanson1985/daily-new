<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 20/12/2018
 * Time: 14:43 CH
 */


namespace App\Http\Controllers\Admin\ToolGame\NroGem;

use App\Exports\ExportData;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Bot;
use App\Models\Bot_UserNap;
use App\Models\Item;
use App\Models\KhachHang;
use App\Models\Nrogem_AccBan;
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


        $this->module='nrogem-logtransaction';


        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');
        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.nrogem-logtransaction.index'),
                'title' => __('Bán ngọc NRO - Thống kê giao dịch')
            ];
        }

    }



    public function index(Request $request)
    {


        if ($request->ajax()) {



            $datatable=Nrogem_GiaoDich::query()
                ->with(['order'=>function($query) use ($request){
                    $query->with(['author'=>function($query) use ($request){

                    }]);
                }])
                ->orderBy('id','desc');

            if ($request->filled('status_nrogem')) {
                $datatable->where('status',  $request->get('status_nrogem'));
            }

            if ($request->filled('status')) {
                $datatable->whereHas('order', function ($query) use ($request) {
                    $query->where('status', $request->get('status'));
                });
            }

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

            if ($request->filled('started_at')) {

                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }

            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }

            if(

                $request->filled('id') ||
                $request->filled('username') ||
                $request->filled('status_nrogem') ||
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


//            if($request->filled('started_at') || $request->filled('ended_at')) {
//
//                if ($request->filled('started_at')) {
//                    $datatable->where('updated_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
//                }
//                if ($request->filled('ended_at')) {
//                    $datatable->where('updated_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
//                }
//
//            }
//            else{
//                $datatable->where('updated_at', '>=', Carbon::now()->startOfDay());
//                $datatable->where('updated_at', '<=', Carbon::now()->endOfDay());
//            }


            $datatableTotal=$datatable->clone();

            return $datatable = \datatables()->eloquent($datatable)

                ->with('totalSumary', function() use ($datatableTotal) {
                    return $datatableTotal=$datatableTotal->first([
                        DB::raw('COUNT(nrogem_giaodich.id) as total_record'),
                        DB::raw('SUM(IF( (INSTR(nrogem_giaodich.process, \'dachuyenitem\') or (INSTR(nrogem_giaodich.process, \'danhanngoc\'))) , nrogem_giaodich.gem, 0)) as total_gem'),
                        DB::raw('SUM(IF(nrogem_giaodich.status = \'danap\', nrogem_giaodich.gem, 0)) as total_gem_nap'),
//                        DB::raw('SUM(order.price) as total_price')
                    ]);

                })
//                ->editColumn('order.status', function ($row) {
//                    return $row->order->status??"";
//                })

                ->editColumn('info_item', function ($row) {
                    return str_replace("\n","</br>",base64_decode($row->info_item));
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



        ////begin total
        //$total=KhachHang::leftJoin('items','nrogem_giaodich.item_id','order.id')
        //->where(function ($q){
        //    $q->orWhere('nrogem_giaodich.status', "danhan");
        //    $q->orWhere('nrogem_giaodich.status', "danap");
        //});
        //$total=$total->select(
        //    DB::raw('COUNT(nrogem_giaodich.id) as total_record'),
        //    DB::raw('SUM(IF(nrogem_giaodich.status = \'danhan\', nrogem_giaodich.money, 0)) as total_coin'),
        //    DB::raw('SUM(IF(nrogem_giaodich.status = \'danap\', nrogem_giaodich.money, 0)) as total_coin_nap'),
        //	DB::raw('SUM(IF(nrogem_giaodich.thoivangsau < nrogem_giaodich.thoivangtruoc, nrogem_giaodich.thoivangtruoc - nrogem_giaodich.thoivangsau, 0)) as total_gold'),
        //    DB::raw('SUM(IF(nrogem_giaodich.thoivangsau > nrogem_giaodich.thoivangtruoc, nrogem_giaodich.thoivangsau - nrogem_giaodich.thoivangtruoc, 0)) as total_gold_nap'),
        //    DB::raw('SUM(order.price) as total_price')
        //)->get();
        //
        ////end total
        //
        //$datatable =$this->MakeQuery($request)->paginate(200);

        return view('admin.toolgame.nrogem.logtransaction.index')
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }



    public function MakeQuery(Request $request)
    {
        $datatable=KhachHang::with('item')->orderBy('updated_at','DESC')->where(function ($q){
            $q->orWhere('status', "danhan");
            $q->orWhere('status', "danap");
        });



        if ($request->filled('id')) {

            $datatable->whereHas('item', function ($query) use ($request) {

                $query->where('id', $request->get('id'));

            });
        }

        if ($request->filled('username')) {

            $datatable->whereHas('item', function ($query) use ($request) {

                $query->where('author', 'LIKE', '%' . $request->get('username') . '%');

            });
        }

        if ($request->filled('ver')) {
            $datatable->where('ver', $request->get('ver'));

        }
        if ($request->filled('status')) {
            $datatable->where('status',  $request->get('status'));
        }
        if ($request->filled('server')) {
            $datatable->where('server',  $request->get('server'));
        }

        //if ($request->filled('started_at')) {
        //    $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
        //}
        //if ($request->filled('ended_at')) {
        //    $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
        //}

        if ($request->filled('started_at')) {
            $datatable->where('nrogem_giaodich.updated_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
        }
        if ($request->filled('ended_at')) {
            $datatable->where('nrogem_giaodich.updated_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
        }


        return $datatable;
    }


    public function exportExcel(Request $request){

        $datatable=Nrogem_GiaoDich::query()
            ->with(['order'=>function($query) use ($request){
                $query->with(['author'=>function($query) use ($request){

                }]);
            }])
            ->orderBy('id','desc');

        if ($request->filled('status_nrogem')) {
            $datatable->where('status',  $request->get('status_nrogem'));
        }

        if ($request->filled('status')) {
            $datatable->whereHas('order', function ($query) use ($request) {
                $query->where('status', $request->get('status'));
            });
        }

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
            $datatable->where('status',  $request->get('status'));
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
            $request->filled('status_nrogem') ||
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
        return Excel::download(new ExportData($data,view('admin.toolgame.nrogem.logtransaction.excel')), 'Thống kê giao dịch tool nrogem_ ' . time() . '.xlsx');
    }

}
