<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 20/12/2018
 * Time: 14:43 CH
 */


namespace App\Http\Controllers\Admin\ToolGame\Rbxapi;

use App\Exports\ExportData;
use App\Http\Controllers\Controller;

use App\Library\Helpers;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Roblox_Order;

use Carbon\Carbon;
use DB;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;
use Session;


class LogTransactionController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;

    public function __construct()
    {


        $this->module='roblox-logtransaction';
        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');


        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.roblox-logtransaction.index'),
                'title' => __('Bán roblox - Thống kê giao dịch')
            ];
        }
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $datatable = Roblox_Order::query()
                ->where('type_order',3)
                ->with(['order'=>function($query){
                    $query->with('author');
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

            if ($request->filled('acc')) {
                $datatable->Where('bot_handle', 'LIKE', '%' . $request->get('acc') . '%');
            }

            if ($request->filled('ver')) {
                $datatable->where('ver', $request->get('ver'));
            }

            if ($request->filled('server')) {
                $datatable->where('server',  $request->get('server'));
            }

            if ($request->filled('status')) {
                $datatable->Where('status', 'LIKE', '%' . $request->get('status') . '%');
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
                $request->filled('status_roblox') ||
                $request->filled('ver') ||
                $request->filled('server') ||
                $request->filled('status') ||
                $request->filled('started_at') ||
                $request->filled('ended_at')||
                $request->filled('finished_started_at')||
                $request->filled('finished_ended_at')
            ){
                if ($request->filled('finished_started_at')) {
                    $datatable->whereHas('order', function ($query) use ($request) {
                        $query->where('updated_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_started_at')));
                    });
                }

                if ($request->filled('finished_ended_at')) {
                    $datatable->whereHas('order', function ($query) use ($request) {
                        $query->where('updated_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_ended_at')));
                    });
                }
            }else{


                $datatable->whereHas('order', function ($query) {
                    $query->where('updated_at', '>=', Carbon::now()->startOfMonth());
                });

                $datatable->whereHas('order', function ($query) {
                    $query->where('updated_at', '<=', Carbon::now()->endOfMonth());
                });

            }

            $datatableTotal=$datatable->clone();

            return $datatable = \datatables()->eloquent($datatable)
                ->editColumn('created_at', function ($row) {
                    return date('d/m/Y H:i:s', strtotime($row->created_at));
                })
                ->editColumn('updated_at', function ($row) {
                    $time = $row->updated_at;
                    if ($row->order && $row->order->updated_at){
                        $time = $row->order->updated_at;
                    }
                    return date('d/m/Y H:i:s', strtotime($time));
                })
                ->with('totalSumary', function() use ($datatableTotal) {
                    return $datatableTotal->first([
                        DB::raw('COUNT(roblox_order.id) as total_record'),
                        DB::raw('SUM(IF(roblox_order.status = \'danhan\', roblox_order.money, 0)) as total_coin'),
                        DB::raw('SUM(IF(roblox_order.status = \'danap\', roblox_order.money, 0)) as total_coin_nap'),
                    ]);
                })
                ->toJson();
        }

        return view('admin.toolgame.roblox-gem.logtransaction.index')
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }



    public function MakeQuery(Request $request)
    {
        $datatable= Roblox_Order::with('order')
            ->orderBy('updated_at','DESC')
            ->where(function ($q){
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
            $datatable->where('roblox_order.updated_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
        }
        if ($request->filled('ended_at')) {
            $datatable->where('roblox_order.updated_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
        }


        return $datatable;
    }

    public function robloxAnimeDefender(Request $request){

        $input=explode(',',$request->id);

        $checkOrders = [];

        foreach ($input??[] as $idOrderNeedRecharge){
            // Start transaction!
            DB::beginTransaction();
            try {

                $roblox_order = Roblox_Order::query()
                    ->where('type_order',10)
                    ->where('status','dangxuly')
                    ->where('id',$idOrderNeedRecharge)
                    ->lockForUpdate()->first();

                if(!$roblox_order){
                    DB::rollback();
                    continue;
                }

                $order = Order::where('module', '=', config('module.service-purchase.key'))
                    ->where('idkey','anime_defenders_auto')
                    ->where('status',2)
                    ->where('id',$roblox_order->order_id)
                    ->first();

                if(!$order){
                    DB::rollback();
                    continue;
                }

                $roblox_order->status = 'gandonchobot';
                $roblox_order->save();

                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'status' => 1,
                    'author_id' => Auth::user()->id,
                    'content' => "Chuyển trạng thái đơn hàng thành công",
                ]);

                DB::commit();

            } catch (\Exception $e) {
                DB::rollback();
                \Log::error( $e);
                continue;
            }
        }
        //active log active

        return redirect()->back()->with('success', 'Các đơn đã được nạp lại thàng công');

    }

    public function exportExcel(Request $request){
        $datatable = Roblox_Order::query()
            ->with(['order'=>function($query){
                $query->with('author');
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

        if ($request->filled('status_roblox')) {
            $datatable->where('type_order', $request->get('status_roblox'));

        }

        if ($request->filled('status')) {
            $datatable->whereHas('order', function ($query) use ($request) {
                $query->where('status', $request->get('status'));
            });
        }

        if ($request->filled('acc')) {
            $datatable->Where('bot_handle', 'LIKE', '%' . $request->get('acc') . '%');
        }

        if ($request->filled('ver')) {
            $datatable->where('ver', $request->get('ver'));

        }
        if ($request->filled('server')) {
            $datatable->where('server',  $request->get('server'));
        }

        if ($request->filled('status_tool')) {
            $datatable->where('status',  $request->get('status_tool'));
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
            $request->filled('status_roblox') ||
            $request->filled('ver') ||
            $request->filled('server') ||
            $request->filled('status') ||
            $request->filled('started_at') ||
            $request->filled('ended_at')||
            $request->filled('finished_started_at')||
            $request->filled('finished_ended_at')
        ){

            if ($request->filled('finished_started_at')) {
                $datatable->whereHas('order', function ($query) use ($request) {
                    $query->where('updated_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_started_at')));
                });
            }

            if ($request->filled('finished_ended_at')) {
                $datatable->whereHas('order', function ($query) use ($request) {
                    $query->where('updated_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_ended_at')));
                });
            }

        }else{

            $datatable->whereHas('order', function ($query) use ($request) {
                $query->where('updated_at', '>=', Carbon::now()->startOfDay());
            });

            $datatable->whereHas('order', function ($query) use ($request) {
                $query->where('updated_at', '<=', Carbon::now()->endOfDay());
            });

        }

        $datatable= $datatable->get();
        $data = [
            'data' => $datatable,
        ];
        return Excel::download(new ExportData($data,view('admin.toolgame.roblox.logtransaction.excel')), 'Thống kê giao dịch tool roblox_ ' . time() . '.xlsx');
    }

}
