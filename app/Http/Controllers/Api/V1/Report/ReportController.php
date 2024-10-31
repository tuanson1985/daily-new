<?php

namespace App\Http\Controllers\Api\V1\Report;

use App\Http\Controllers\Controller;
use App\Library\NickHelper;
use App\Models\Charge;
use App\Models\Item;
use App\Models\ItemConfig;
use App\Models\Order;
use App\Models\Shop;
use App\Models\Txns;
use App\Models\UserBalance;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    const SIGN = '9xvcwstiquvhyxxumj0s';

    public function __construct()
    {

    }

    public function serviceReport(Request $request)
    {
        if ($request->filled('sign') && $request->get('sign') == self::SIGN) {

            $data_query = [];

            if ($request->filled('finished_started_at') && $request->filled('finished_ended_at')) {
                $service_query = Order::query()
                    ->with('author',function ($q){
                        $q->select('id','username','type_information');
                    })
                    ->with('processor',function ($q){
                        $q->select('id','username');
                    })
                    ->select('id','title','request_id_customer','author_id','processor_id','params','description','price','real_received_price_ctv','status','created_at','updated_at','process_at')
                    ->where('module', 'service-purchase')
                    ->where('gate_id', 0);

                $service_query = $service_query->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_started_at')));
                $service_query = $service_query->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_ended_at')));
                $data_query = $service_query
                    ->orderBy('process_at','asc')->get()->toArray();
            }

            if ($request->filled('time')) {
                $finished_started_at = $request->get('time').' 00:00:00';
                $currentDate = Carbon::now()->format('d/m/Y');
                $finished_ended_at = $currentDate.' 23:59:59';
                $service_query = Order::query()
                    ->with('author',function ($q){
                        $q->select('id','username','type_information');
                    })
                    ->with('processor',function ($q){
                        $q->select('id','username');
                    })
                    ->select('id','title','request_id_customer','author_id','processor_id','params','description','price','real_received_price_ctv','status','created_at','updated_at','process_at')
                    ->where('module', 'service-purchase')
                    ->where('gate_id', 0);

                $service_query = $service_query->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_started_at));
                $service_query = $service_query->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_ended_at));
                $data_query = $service_query
                    ->orderBy('process_at','asc')->get()->toArray();
            }

            if ($request->filled('month')) {
                if ($request->get('month') == 1){
                    $finished_started_at = '01/01/2024 00:00:00';
                    $finished_ended_at = '31/01/2024 23:59:59';
                }elseif ($request->get('month') == 2){
                    $finished_started_at = '01/02/2024 00:00:00';
                    $finished_ended_at = '29/02/2024 23:59:59';
                }elseif ($request->get('month') == 2){
                    $finished_started_at = '01/02/2024 00:00:00';
                    $finished_ended_at = '29/02/2024 23:59:59';
                }elseif ($request->get('month') == 3){
                    $finished_started_at = '01/03/2024 00:00:00';
                    $finished_ended_at = '31/03/2024 23:59:59';
                }elseif ($request->get('month') == 4){
                    $finished_started_at = '01/04/2024 00:00:00';
                    $finished_ended_at = '30/04/2024 23:59:59';
                }elseif ($request->get('month') == 51){
                    $finished_started_at = '01/05/2024 00:00:00';
                    $finished_ended_at = '15/05/2024 23:59:59';
                }elseif ($request->get('month') == 52){
                    $finished_started_at = '16/05/2024 00:00:00';
                    $finished_ended_at = '31/05/2024 23:59:59';
                }
                elseif ($request->get('month') == 6){
                    $finished_started_at = '01/06/2024 00:00:00';
                    $finished_ended_at = '30/06/2024 23:59:59';
                }elseif ($request->get('month') == 7){
                    $finished_started_at = '01/07/2024 00:00:00';
                    $finished_ended_at = '31/07/2024 23:59:59';
                }elseif ($request->get('month') == 8){
                    $finished_started_at = '01/08/2024 00:00:00';
                    $finished_ended_at = '31/08/2024 23:59:59';
                }elseif ($request->get('month') == 9){
                    $finished_started_at = '01/09/2024 00:00:00';
                    $finished_ended_at = '30/09/2024 23:59:59';
                }elseif ($request->get('month') == 10){
                    $finished_started_at = '01/10/2024 00:00:00';
                    $finished_ended_at = '31/10/2024 23:59:59';
                }elseif ($request->get('month') == 11){
                    $finished_started_at = '01/11/2024 00:00:00';
                    $finished_ended_at = '31/11/2024 23:59:59';
                }elseif ($request->get('month') == 12){
                    $finished_started_at = '01/12/2024 00:00:00';
                    $finished_ended_at = '31/12/2024 23:59:59';
                }

                $service_query = Order::query()
                    ->with('author',function ($q){
                        $q->select('id','username','type_information');
                    })
                    ->with('processor',function ($q){
                        $q->select('id','username');
                    })
                    ->select('id','title','request_id_customer','author_id','processor_id','params','description','price','real_received_price_ctv','status','created_at','updated_at','process_at')
                    ->where('module', 'service-purchase')
                    ->where('gate_id', 0);

                $service_query = $service_query->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_started_at));
                $service_query = $service_query->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_ended_at));
                $data_query = $service_query
                    ->orderBy('process_at','asc')->get()->toArray();
            }


            if ($request->filled('month_pengiriman')) {
                if ($request->get('month_pengiriman') == 1){
                    $finished_started_at = '01/01/2025 00:00:00';
                    $finished_ended_at = '31/01/2025 23:59:59';
                }elseif ($request->get('month_pengiriman') == 2){
                    $finished_started_at = '01/02/2025 00:00:00';
                    $finished_ended_at = '29/02/2025 23:59:59';
                }elseif ($request->get('month_pengiriman') == 3){
                    $finished_started_at = '01/03/2025 00:00:00';
                    $finished_ended_at = '31/03/2025 23:59:59';
                }elseif ($request->get('month_pengiriman') == 4){
                    $finished_started_at = '01/04/2025 00:00:00';
                    $finished_ended_at = '30/04/2025 23:59:59';
                }elseif ($request->get('month_pengiriman') == 5){
                    $finished_started_at = '01/05/2025 00:00:00';
                    $finished_ended_at = '15/05/2025 23:59:59';
                }
                elseif ($request->get('month_pengiriman') == 6){
                    $finished_started_at = '01/06/2025 00:00:00';
                    $finished_ended_at = '30/06/2025 23:59:59';
                }elseif ($request->get('month_pengiriman') == 7){
                    $finished_started_at = '01/07/2025 00:00:00';
                    $finished_ended_at = '31/07/2025 23:59:59';
                }elseif ($request->get('month_pengiriman') == 8){
                    $finished_started_at = '01/08/2025 00:00:00';
                    $finished_ended_at = '31/08/2025 23:59:59';
                }elseif ($request->get('month_pengiriman') == 9){
                    $finished_started_at = '01/09/2025 00:00:00';
                    $finished_ended_at = '30/09/2025 23:59:59';
                }elseif ($request->get('month_pengiriman') == 10){
                    $finished_started_at = '01/10/2024 00:00:00';
                    $finished_ended_at = '31/10/2024 23:59:59';
                }elseif ($request->get('month_pengiriman') == 11){
                    $finished_started_at = '01/11/2024 00:00:00';
                    $finished_ended_at = '31/11/2024 23:59:59';
                }elseif ($request->get('month_pengiriman') == 12){
                    $finished_started_at = '01/12/2024 00:00:00';
                    $finished_ended_at = '31/12/2024 23:59:59';
                }

                $service_query = Order::query()
                    ->with(['author' => function ($q){
                        $q->select('id','username','type_information');
                    },'processor' => function ($q){
                        $q->select('id','username','type_information_ctv');
                    },'order_pengiriman'])
                    ->whereHas('processor',function ($q){
                        $q->where('type_information_ctv', 1);
                    })
                    ->where( 'idkey','gamepass_roblox')
                    ->whereIn('status',[4,10])
                    ->select('id','title','request_id_customer','author_id','processor_id','params','description','price','real_received_price_ctv','status','created_at','updated_at','process_at')
                    ->where('module', 'service-purchase')
                    ->where('gate_id', 0);

                $service_query = $service_query->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_started_at));
                $service_query = $service_query->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_ended_at));
                $data_query = $service_query
                    ->orderBy('process_at','asc')->get()->toArray();
            }

            if ($request->filled('time_created')) {
                $finished_started_at = $request->get('time_created').' 00:00:00';
                $currentDate = Carbon::now()->format('d/m/Y');
                $finished_ended_at = $currentDate.' 23:59:59';
                $service_query = Order::query()
                    ->with('author',function ($q){
                        $q->select('id','username','type_information');
                    })
                    ->with('processor',function ($q){
                        $q->select('id','username');
                    })
                    ->select('id','title','request_id_customer','author_id','processor_id','params','description','price','real_received_price_ctv','status','created_at','updated_at','process_at')
                    ->where('module', 'service-purchase')
                    ->where('gate_id', 0);

                $service_query = $service_query->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_started_at));
                $service_query = $service_query->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_ended_at));
                $data_query = $service_query
                    ->orderBy('created_at','asc')->get()->toArray();
            }

            if ($request->filled('month_created')) {
                if ($request->get('month_created') == 1){
                    $finished_started_at = '01/01/2024 00:00:00';
                    $finished_ended_at = '31/01/2024 23:59:59';
                }elseif ($request->get('month_created') == 2){
                    $finished_started_at = '01/02/2024 00:00:00';
                    $finished_ended_at = '29/02/2024 23:59:59';
                }elseif ($request->get('month_created') == 2){
                    $finished_started_at = '01/02/2024 00:00:00';
                    $finished_ended_at = '29/02/2024 23:59:59';
                }elseif ($request->get('month_created') == 3){
                    $finished_started_at = '01/03/2024 00:00:00';
                    $finished_ended_at = '31/03/2024 23:59:59';
                }elseif ($request->get('month_created') == 4){
                    $finished_started_at = '01/04/2024 00:00:00';
                    $finished_ended_at = '30/04/2024 23:59:59';
                }elseif ($request->get('month_created') == 51){
                    $finished_started_at = '01/05/2024 00:00:00';
                    $finished_ended_at = '15/05/2024 23:59:59';
                }elseif ($request->get('month_created') == 52){
                    $finished_started_at = '16/05/2024 00:00:00';
                    $finished_ended_at = '31/05/2024 23:59:59';
                }
                elseif ($request->get('month_created') == 6){
                    $finished_started_at = '01/06/2024 00:00:00';
                    $finished_ended_at = '30/06/2024 23:59:59';
                }elseif ($request->get('month_created') == 7){
                    $finished_started_at = '01/07/2024 00:00:00';
                    $finished_ended_at = '31/07/2024 23:59:59';
                }

                $service_query = Order::query()
                    ->with('author',function ($q){
                        $q->select('id','username','type_information');
                    })
                    ->with('processor',function ($q){
                        $q->select('id','username');
                    })
                    ->select('id','title','request_id_customer','author_id','processor_id','params','description','price','real_received_price_ctv','status','created_at','updated_at','process_at')
                    ->where('module', 'service-purchase')
                    ->where('gate_id', 0);

                $service_query = $service_query->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_started_at));
                $service_query = $service_query->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_ended_at));
                $data_query = $service_query
                    ->orderBy('created_at','asc')->get()->toArray();
            }

            return response()->json($data_query);

        } else {
            return response()->json('Không được phép truy cập !');
        }
    }

    public function serviceDayReport(Request $request)
    {
        if ($request->filled('sign') && $request->get('sign') == self::SIGN) {

            $data_query = [];

            if ($request->filled('finished_started_at') && $request->filled('finished_ended_at')) {
                $service_query = Order::query()
                    ->with('author',function ($q){
                        $q->select('id','username','type_information');
                    })
                    ->with('processor',function ($q){
                        $q->select('id','username');
                    })
                    ->select('id','title','request_id_customer','author_id','processor_id','params','description','price','real_received_price_ctv','status','created_at','updated_at','process_at')
                    ->where('module', 'service-purchase')
                    ->where('gate_id', 0)->whereIn('status',[4,10]);

                $service_query = $service_query->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_started_at')));
                $service_query = $service_query->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_ended_at')));
                $data_query = $service_query
                    ->orderBy('process_at','asc')->get()->toArray();
            }

            return response()->json($data_query);

        } else {
            return response()->json('Không được phép truy cập !');
        }
    }

    public function serviceDayNewReport(Request $request)
    {
        if ($request->filled('sign') && $request->get('sign') == self::SIGN) {

            $data_query = [];

            if ($request->filled('day')) {
                $service_query = Order::query()
                    ->with('author',function ($q){
                        $q->select('id','username','type_information');
                    })
                    ->with('processor',function ($q){
                        $q->select('id','username');
                    })
                    ->select('id','title','request_id_customer','author_id','processor_id','params','description','price','real_received_price_ctv','status','created_at','updated_at','process_at')
                    ->where('module', 'service-purchase')
                    ->where('gate_id', 0)->where('status',4);

                $service_query = $service_query->whereDate('process_at',  $request->get('day'));
                $data_query = $service_query
                    ->orderBy('process_at','asc')->get()->toArray();
            }

            return response()->json($data_query);

        } else {
            return response()->json('Không được phép truy cập !');
        }
    }

    public function serviceReportDay(Request $request)
    {
        if ($request->filled('sign') && $request->get('sign') == self::SIGN) {
            $data_api = [];
            $is_month = false;

            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $day = Carbon::now()->subDay()->day;//Hôm qua
            $time_display = $day . '/' . $month . '/' . $year;

            if ($request->filled('month') && $request->get('month') == "true") {
                $is_month = true;
                $month = Carbon::now()->subMonth()->month;
                $time_display = $month . '/' . $year;
            }

            if ($request->filled('time')) {
                $time = explode('/', $request->get('time'));
                if (count($time) == 2) {
                    $month = $time[0];
                    $year = $time[1];
                    $is_month = true;
                }
                if (count($time) == 3) {
                    $day = $time[0];
                    $month = $time[1];
                    $year = $time[2];
                }
                $time_display = $request->get('time');
            }

            $service_query = Order::query()
                ->with('author',function ($q){
                    $q->select('id','username','type_information');
                })
                ->with('processor',function ($q){
                    $q->select('id','username');
                })
                ->select('id','title','request_id_customer','author_id','processor_id','params','description','price','real_received_price_ctv','status','created_at','updated_at')
                ->where('module', 'service-purchase')
                ->where('gate_id', 0);

            if ($is_month) {
                $data_service = $service_query
                    ->whereYear('created_at', $year)->whereMonth('created_at', $month);
            } else {
                $data_service = $service_query
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->whereDay('created_at', $day);
            }

            $data_query = $data_service
                ->orderBy('created_at','asc')->get()->toArray();

            return response()->json($data_query);

        } else {
            return response()->json('Không được phép truy cập !');
        }
    }

    public function serviceAutoReport(Request $request)
    {
        if ($request->filled('sign') && $request->get('sign') == self::SIGN) {

            if ($request->filled('month_pengiriman')) {
                if ($request->get('month_pengiriman') == 1){
                    $finished_started_at = '01/01/2025 00:00:00';
                    $finished_ended_at = '31/01/2025 23:59:59';
                }elseif ($request->get('month_pengiriman') == 2){
                    $finished_started_at = '01/02/2025 00:00:00';
                    $finished_ended_at = '29/02/2025 23:59:59';
                }elseif ($request->get('month_pengiriman') == 3){
                    $finished_started_at = '01/03/2025 00:00:00';
                    $finished_ended_at = '31/03/2025 23:59:59';
                }elseif ($request->get('month_pengiriman') == 4){
                    $finished_started_at = '01/04/2025 00:00:00';
                    $finished_ended_at = '30/04/2025 23:59:59';
                }elseif ($request->get('month_pengiriman') == 5){
                    $finished_started_at = '01/05/2025 00:00:00';
                    $finished_ended_at = '15/05/2025 23:59:59';
                }
                elseif ($request->get('month_pengiriman') == 6){
                    $finished_started_at = '01/06/2025 00:00:00';
                    $finished_ended_at = '30/06/2025 23:59:59';
                }elseif ($request->get('month_pengiriman') == 7){
                    $finished_started_at = '01/07/2025 00:00:00';
                    $finished_ended_at = '31/07/2025 23:59:59';
                }elseif ($request->get('month_pengiriman') == 8){
                    $finished_started_at = '01/08/2025 00:00:00';
                    $finished_ended_at = '31/08/2025 23:59:59';
                }elseif ($request->get('month_pengiriman') == 9){
                    $finished_started_at = '01/09/2025 00:00:00';
                    $finished_ended_at = '30/09/2025 23:59:59';
                }elseif ($request->get('month_pengiriman') == 10){
                    $finished_started_at = '01/10/2024 00:00:00';
                    $finished_ended_at = '31/10/2024 23:59:59';
                }elseif ($request->get('month_pengiriman') == 11){
                    $finished_started_at = '01/11/2024 00:00:00';
                    $finished_ended_at = '31/11/2024 23:59:59';
                }elseif ($request->get('month_pengiriman') == 12){
                    $finished_started_at = '01/12/2024 00:00:00';
                    $finished_ended_at = '31/12/2024 23:59:59';
                }

                $service_query = Order::query()
                    ->with(['author' => function ($q){
                        $q->select('id','username','type_information');
                    },'roblox_order'=> function ($q){
                        $q->with('bot');
                    },'order_pengiriman'])
                    ->whereIn('idkey',["roblox_buygamepass","roblox_buyserver"])
                    ->select('id','title','request_id_customer','author_id','idkey','params','description','price','price_base','price_input','status','created_at','updated_at','process_at')
                    ->where('module', 'service-purchase')
                    ->where('status',4)
                    ->where('gate_id', 1);

                $service_query = $service_query->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_started_at));
                $service_query = $service_query->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_ended_at));
                $data_query = $service_query
                    ->orderBy('process_at','asc')->get()->toArray();

                return response()->json($data_query);
            }else{
                $data_query = [];

                $data_api = [];
                $is_month = false;

                $year = Carbon::now()->year;
                $month = Carbon::now()->month;
                $day = Carbon::now()->subDay()->day;//Hôm qua
                $time_display = $day . '/' . $month . '/' . $year;

                if ($request->filled('month') && $request->get('month') == "true") {
                    $is_month = true;
                    $month = Carbon::now()->subMonth()->month;
                    $time_display = $month . '/' . $year;
                }

                if ($request->filled('time')) {
                    $time = explode('/', $request->get('time'));
                    if (count($time) == 2) {
                        $month = $time[0];
                        $year = $time[1];
                        $is_month = true;
                    }
                    if (count($time) == 3) {
                        $day = $time[0];
                        $month = $time[1];
                        $year = $time[2];
                    }
                    $time_display = $request->get('time');
                }

                $service_query = Order::query()
                    ->with(['author' => function ($q){
                        $q->select('id','username','type_information');
                    },'roblox_order' => function ($q){
                        $q->select('order_id','money','status','phone','type_order');
                    }])
                    ->select('id','title','request_id_customer','author_id','idkey','params','description','price','price_base','price_input','status','created_at','updated_at','process_at')
                    ->where('module', 'service-purchase')
                    ->where('status',4)
                    ->where('gate_id', 1);

                if ($is_month) {
                    $data_service = $service_query->whereYear('process_at', $year)->whereMonth('process_at', $month);
                }
                else {
                    $data_service = $service_query->whereYear('process_at', $year)
                        ->whereMonth('process_at', $month)
                        ->whereDay('process_at', $day);
                }

                $data_query = $data_service
                    ->orderBy('process_at','asc')->get()->toArray();

                return response()->json($data_query);
            }

        }
        else {
            return response()->json('Không được phép truy cập !');
        }
    }

    public function serviceAutoReportV2(Request $request)
    {
        if ($request->filled('sign') && $request->get('sign') == self::SIGN) {

            $data_query = [];

            if ($request->filled('time')) {
                $finished_started_at = $request->get('time').' 00:00:00';
                $currentDate = Carbon::now()->format('d/m/Y');
                $finished_ended_at = $currentDate.' 23:59:59';
                $service_query = Order::query()
                    ->with('author',function ($q){
                        $q->select('id','username','type_information');
                    })
                    ->with('roblox_order',function ($q){
                        $q->select('order_id','money','status','phone','type_order','ver');
                    })
                    ->select('id','title','request_id_customer','author_id','idkey','params','description','price','price_base','price_input','status','created_at','updated_at','process_at')
                    ->where('module', 'service-purchase')
                    ->where('status',4)
                    ->where('gate_id', 1);

                $service_query = $service_query->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_started_at));
                $service_query = $service_query->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_ended_at));
                $data_query = $service_query
                    ->orderBy('process_at','asc')->get()->toArray();
            }

            if ($request->filled('finished_started_at') && $request->filled('finished_ended_at')) {
                $finished_started_at = $request->get('finished_started_at').' 00:00:00';
                $finished_ended_at = $request->get('finished_ended_at').' 23:59:59';
                $service_query = Order::query()
                    ->with('author',function ($q){
                        $q->select('id','username','type_information');
                    })
                    ->with('roblox_order',function ($q){
                        $q->select('order_id','money','status','phone','type_order','ver');
                    })
                    ->select('id','title','request_id_customer','author_id','idkey','params','description','price','price_base','price_input','status','created_at','updated_at','process_at')
                    ->where('module', 'service-purchase')
                    ->where('status',4)
                    ->where('gate_id', 1);

                $service_query = $service_query->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_started_at));
                $service_query = $service_query->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_ended_at));
                $data_query = $service_query
                    ->orderBy('process_at','asc')->get()->toArray();
            }
            return response()->json($data_query);

        } else {
            return response()->json('Không được phép truy cập !');
        }
    }

    public function serviceAutoPedding(Request $request)
    {
        if ($request->filled('sign') && $request->get('sign') == self::SIGN) {

            $data_query = [];

            $service_query = Order::query()
                ->with(['item_ref','author','processor','workflow_reception','order_pengiriman'])
                ->where('order.module', config('module.service-purchase'))
                ->where('gate_id',0);

            if ($request->filled('status')) {
                $service_query = $service_query->where('status',$request->get('status'));
            }

            $data_query = $service_query
                ->orderBy('created_at','asc')->get()->toArray();

            return response()->json($data_query);

        } else {
            return response()->json('Không được phép truy cập !');
        }
    }

    public function confirmWithdraw(Request $request)
    {
        if ($request->filled('sign') && $request->get('sign') == self::SIGN) {
            $data_api = [];
            $is_month = false;

            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $day = Carbon::now()->subDay()->day;//Hôm qua
            $time_display = $day . '/' . $month . '/' . $year;

            if ($request->filled('month') && $request->get('month') == "true") {
                $is_month = true;
                $month = Carbon::now()->subMonth()->month;
                $time_display = $month . '/' . $year;
            }

            if ($request->filled('time')) {
                $time = explode('/', $request->get('time'));
                if (count($time) == 2) {
                    $month = $time[0];
                    $year = $time[1];
                    $is_month = true;
                }
                if (count($time) == 3) {
                    $day = $time[0];
                    $month = $time[1];
                    $year = $time[2];
                }
                $time_display = $request->get('time');
            }

            $data_withdraw = Withdraw::query()
                ->with('txns', 'user')->where('status',1);

            if ($is_month) {
                $data_withdraw = $data_withdraw
                    ->whereYear('process_at', $year)->whereMonth('process_at', $month);
            } else {
                $data_withdraw = $data_withdraw
                    ->whereYear('process_at', $year)
                    ->whereMonth('process_at', $month)
                    ->whereDay('process_at', $day);
            }

            $data_query = $data_withdraw
                ->orderBy('process_at','asc')->get()->toArray();

            return response()->json($data_query);

        } else {
            return response()->json('Không được phép truy cập !');
        }
    }

    public function getServiceAttribute(Request $request){

        if ($request->filled('sign') && $request->get('sign') == self::SIGN) {
            $services = \App\Models\Item::query()
                ->whereNotNull('idkey')
                ->where('module','service')
                ->where('gate_id',0)
                ->select('id','title','params','updated_at','idkey')
                ->get();

            $data = [];
            $date = \Carbon\Carbon::now()->format('d/m/Y');

            foreach ($services??[] as $service){
                if (!empty($service->params)){
                    $params = json_decode($service->params);
                    if (!empty($params->name) && !empty($params->keyword)){
                        $names = $params->name;
                        $keywords = $params->keyword;
                        $prices = $params->price;
                        foreach ($names??[] as $key => $name){

                            $resultChange = new \stdClass();
                            $resultChange->name = $name;
                            if (!empty($keywords[$key])){
                                $resultChange->keyword = $keywords[$key];
                            }
                            if (!empty($prices[$key])){
                                $resultChange->price = $prices[$key];
                            }
                            if (isset($service->title)){
                                $resultChange->service = $service->title;
                            }
                            if (isset($service->idkey)){
                                $resultChange->idkey = config('module.service.idkey.'.$service->idkey);
                            }
                            $resultChange->date = $date;
                            array_push($data,$resultChange);
                        }
                    }

                }
            }

            return response()->json($data);

        } else {
            return response()->json('Không được phép truy cập !');
        }

    }


}
