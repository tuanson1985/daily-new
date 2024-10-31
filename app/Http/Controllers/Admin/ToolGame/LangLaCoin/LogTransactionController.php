<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 20/12/2018
 * Time: 14:43 CH
 */


namespace App\Http\Controllers\Admin\ToolGame\LangLaCoin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Bot;
use App\Models\Bot_UserNap;
use App\Models\Item;
use App\Models\LangLaCoin_KhachHang;
use App\Models\Nrogem_GiaoDich;
use App\Models\Shop;
use App\Models\SubItem;
use Auth;
use Carbon\Carbon;
use DB;
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

        $this->module='langlacoin-logtransaction';

        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');

        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.langlacoin-logtransaction.index'),
                'title' => __('Bán bạc làng lá - Thống kê giao dịch')
            ];
        }

    }

    public function index(Request $request)
    {


        if ($request->ajax()) {



            $datatable=LangLaCoin_KhachHang::leftJoin('order','order.id','langlacoin_khachhang.order_id')
                //->whereNotNull('order.id')
                ->leftJoin('shop','order.shop_id','shop.id')
                ->leftJoin('users','order.author_id','users.id')
            //->where(function ($q){
            //    $q->orWhere('langlacoin_khachhang.status', "danhan");
            //    $q->orWhere('langlacoin_khachhang.status', "danap");
            //})

            ->orderBy('langlacoin_khachhang.id','desc');

            if ($request->filled('id')) {
                $datatable->where('langlacoin_khachhang.id',  $request->get('id'));

            }
            if ($request->filled('username')) {
                $datatable->where('users.username', $request->get('username'));

            }


            if ($request->filled('ver')) {
                $datatable->where('langlacoin_khachhang.ver', $request->get('ver'));

            }
            if ($request->filled('server')) {
                $datatable->where('langlacoin_khachhang.server',  $request->get('server'));
            }

            if ($request->filled('status')) {
                $datatable->where('langlacoin_khachhang.status',  $request->get('status'));
            }


            if($request->filled('started_at') || $request->filled('ended_at')) {

                if ($request->filled('started_at')) {
                    $datatable->where('langlacoin_khachhang.updated_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
                }
                if ($request->filled('ended_at')) {
                    $datatable->where('langlacoin_khachhang.updated_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
                }

            }
            else{
                $datatable->where('langlacoin_khachhang.updated_at', '>=', Carbon::now()->startOfDay());
                $datatable->where('langlacoin_khachhang.updated_at', '<=', Carbon::now()->endOfDay());
            }

            $datatableTotal=$datatable->clone();

            $datatable->select('langlacoin_khachhang.*','users.username','order.price','shop.domain');
            return $datatable = \datatables()->eloquent($datatable)
                ->with('totalSumary', function() use ($datatableTotal) {
                    return $datatableTotal=$datatableTotal->first([
                        DB::raw('COUNT(langlacoin_khachhang.id) as total_record'),
                        DB::raw('SUM(IF(langlacoin_khachhang.status = \'danhan\', langlacoin_khachhang.coin, 0)) as total_coin'),
                        DB::raw('SUM(IF(langlacoin_khachhang.status = \'danap\', langlacoin_khachhang.coin, 0)) as total_coin_nap'),
                        DB::raw('SUM(order.price) as total_price')
                    ]);

                })

                ->editColumn('created_at', function ($row) {
                    return date('d/m/Y H:i:s', strtotime($row->created_at));
                })

                ->editColumn('username', function ($row) {
                    return $row->username;
                })


                ->toJson();
        }

        $shop_access_user = Auth::user()->shop_access;
        $shop = Shop::orderBy('id', 'desc');
        if (isset($shop_access_user) && $shop_access_user !== "all") {
            $shop_access_user = json_decode($shop_access_user);
            $shop = $shop->whereIn('id', $shop_access_user);
        }
        $shop = $shop->pluck('title', 'id')->toArray();

        return view('admin.toolgame.langlacoin.logtransaction.index')
            ->with('module',null)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }








}
