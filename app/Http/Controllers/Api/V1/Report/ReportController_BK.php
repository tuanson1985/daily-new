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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    const SIGN = '9xvcwstiquvhyxxumj0s';

    private $shops = [];
    private $shop_id_array = [];
    private $shop_title_array = [];

    public function __construct(Request $request)
    {
        $this->shops = DB::table('shop')->select('id', 'domain', 'group_id');


        if ($request->get('times') && $request->get('times') == 1) {
            $this->shops = $this->shops->whereBetween('id',[1,400]);
        }elseif ($request->get('times') && $request->get('times') == 2){
            $this->shops = $this->shops->whereBetween('id',[401,800]);
        }

        $this->shops = $this->shops->get();

        foreach ($this->shops->toArray() as $shop) {
            $this->shop_id_array[] = $shop->id;
            $this->shop_title_array[$shop->id] = $shop->domain;
        }
    }

    public function chargeReport(Request $request)
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

            $charge_query = DB::table('charge')
                ->selectRaw('shop_id')
                ->selectRaw('COALESCE(SUM(IF(status = 1 OR status = 3,1,0)),0) as total_record')
                ->selectRaw('COALESCE(SUM(IF(status = 1 OR status = 3,amount,0)),0) as total_amount')
                ->selectRaw('COALESCE(SUM(IF(status = 1 OR status = 3,real_received_amount,0)),0) as real_received_amount')
                ->selectRaw('COALESCE(SUM(IF(status = 1 OR status = 3,money_received,0)),0) as money_received');
            if ($is_month) {
                $data_charge = $charge_query->whereYear('created_at', $year)->whereMonth('created_at', $month);
            } else {
                $data_charge = $charge_query->whereYear('created_at', $year)->whereMonth('created_at', $month)
                    ->whereDay('created_at', $day);
            }
            $data_query = $data_charge->groupBy('shop_id')->get()->toArray();

            foreach ($data_query as $key => $data_shop) {
                $data['total_record'] = $data_shop->total_record;
                $data['real_received_amount'] = $data_shop->real_received_amount;
                $data['total_amount'] = $data_shop->total_amount;
                $data['money_received'] = $data_shop->money_received;

                $data['time'] = $time_display;

                $data_array['shop'] = $this->shop_title_array[$data_shop->shop_id] ?? $data_shop->shop_id;
                $data_array['data'] = $data;

                $data_api[] = $data_array;
            }
            return response()->json($data_api);
        } else {
            return response()->json('Không được phép truy cập !');
        }
    }

    public function transferReport(Request $request)
    {
        if ($request->filled('sign') && $request->get('sign') == self::SIGN) {
            $data_api = [];
            $is_month = false;

            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $day = Carbon::now()->subDay()->day; //Hôm qua
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

            $transfer_query = DB::table('order')->where('module', config('module.transfer.key'))
                ->selectRaw('shop_id') // Tổng số tiền nạp
                ->selectRaw('COALESCE(SUM(IF(status = 1,price,0)),0) as total_money') // Tổng số tiền nạp
                ->selectRaw('COALESCE(SUM(IF(status = 1,real_received_price,0)),0) as total_real_received_price'); //Tổng số tiền KH thực nhận trong hệ thống
            if ($is_month) {
                $data_transfer = $transfer_query
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month);
            } else {
                $data_transfer = $transfer_query
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->whereDay('created_at', $day);
            }

            $data_query = $data_transfer->groupBy('shop_id')->get()->toArray();

            foreach ($data_query as $key => $data_shop) {
                $data['total_money'] = $data_shop->total_money;
                $data['total_real_received_price'] = $data_shop->total_real_received_price;

                $data['time'] = $time_display;

                $data_array['shop'] = $this->shop_title_array[$data_shop->shop_id] ?? $data_shop->shop_id;
                $data_array['data'] = $data;

                $data_api[] = $data_array;
            }

            return response()->json($data_api);
        } else {
            return response()->json('Không được phép truy cập !');
        }
    }

    public function accountReportOld(Request $request)
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
            $shop_id_array = [];
            $shop_title_array = [];
            foreach ($this->shops->toArray() as $shop) {
                $shop_id_array[] = $shop->id;
                $shop_title_array[$shop->id] = $shop->domain;
            }
            $txns_query = Txns::query()->whereHas('item', function ($q) use ($shop_id_array, $is_month, $day, $month, $year) {
                $q->where('module', 'acc')
                    ->where('status', 0)
                    ->whereNotNull('sticky')
                    ->whereIn('shop_id', $shop_id_array);
                if ($is_month) {
                    $q->whereYear('published_at', $year)
                        ->whereMonth('published_at', $month);
                } else {
                    $q->whereYear('published_at', $year)
                        ->whereMonth('published_at', $month)
                        ->whereDay('published_at', $day);
                }
            })->where('txnsable_type', 'App\Models\Item')
                ->where('trade_type', 'buy_acc')
                ->where('is_add', 1)
                ->where('is_refund', 0)
                ->selectRaw('shop_id,COALESCE(SUM(amount),0) as price_ctv')
                ->selectRaw('COUNT(*) as total_record')
                ->whereIn('shop_id', $shop_id_array)
                ->groupBy('shop_id')
                ->get()
                ->toArray();

            $order_query = Order::query()->where('module', 'buy_acc')
                ->whereHas('nick', function ($q) use ($shop_id_array, $is_month, $day, $month, $year) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereIn('shop_id', $shop_id_array);
                    if ($is_month) {
                        $q->whereYear('published_at', $year)
                            ->whereMonth('published_at', $month);
                    } else {
                        $q->whereYear('published_at', $year)
                            ->whereMonth('published_at', $month)
                            ->whereDay('published_at', $day);
                    }
                })
                ->selectRaw('shop_id,COALESCE(SUM(price),0) as price_sell')
                ->groupBy('shop_id')
                ->get()->toArray();

            $account_query = Item::query()
                ->where('module', 'acc')
                ->where('status', 0)
                ->whereNotNull('sticky')
                ->whereIn('shop_id', $shop_id_array)
                ->selectRaw('shop_id,COALESCE(SUM(price),0) as price')
                ->selectRaw('COUNT(*) as total_record');

            if ($is_month) {
                $account_query
                    ->whereYear('published_at', $year)
                    ->whereMonth('published_at', $month);
            } else {
                $account_query
                    ->whereYear('published_at', $year)
                    ->whereMonth('published_at', $month)
                    ->whereDay('published_at', $day);
            }
            $account_data = $account_query->groupBy('shop_id')->get()->toArray();

            $result = array();

            foreach ($account_data as $a_item) {
                foreach ($txns_query as $b_item) {
                    if ($a_item['shop_id'] == $b_item['shop_id']) {
                        foreach ($order_query as $c_item) {
                            if ($b_item['shop_id'] == $c_item['shop_id']) {
                                $result[] = array_merge($a_item, $b_item, $c_item);
                            }
                        }
                    }
                }
            }
            foreach ($result as $key => $data_shop) {
                $data['total_record'] = $data_shop['total_record'];
                $data['price'] = $data_shop['price'];
                $data['price_sell'] = $data_shop['price_sell'] ?? "0";
                $data['price_ctv'] = $data_shop['price_ctv'] ?? "0";
                $data['time'] = $time_display;

                $data_array['shop'] = $shop_title_array[$data_shop['shop_id']];
                $data_array['data'] = $data;

                $data_api[] = $data_array;
            }
            return response()->json($data_api);
        } else {
            return response()->json('Không được phép truy cập !');
        }
    }

    public function accountReport(Request $request)
    {
        if ($request->filled('sign') && $request->get('sign') == self::SIGN) {
            if (!$request->filled('shop')) {
                return response([
                    'status' => 0,
                    'message' => 'Chưa truyền ID shop',
                ]);
            }

            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $day = Carbon::now()->subDay()->day;//Hôm qua

            $date_from = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
            $date_to = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');

            if ($request->filled('time')) {
                $time = explode('/', $request->get('time'));
                if (count($time) == 2) {
                    $month = $time[0];
                    $year = $time[1];

                    $date_from = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d H:i:s');
                    $date_to = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d H:i:s');
                }
                if (count($time) == 3) {
                    $day = $time[0];
                    $month = $time[1];
                    $year = $time[2];

                    $date_from = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
                    $date_to = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
                }
            }

            $result_query = NickHelper::timeline_dayly_one([
                'shops' => $request->get('shop'),
                'date_from' => $date_from,
                'date_to' => $date_to,
            ]);

            $data_api = [
                "price" => 0,
                "amount" => 0,
                "amount_ctv" => 0,
                "count_total" => 0,
                "count_customer" => 0,
                "count_failed" => 0,
                "count_deleted" => 0,
            ];

            $data_api['price'] = $result_query['price'];
            $data_api['amount'] = $result_query['amount'];
            $data_api['amount_ctv'] = $result_query['amount_ctv'];
            $data_api['count_total'] = $result_query['count_total'];
            $data_api['count_customer'] = $result_query['count_customer'];
            $data_api['count_failed'] = $result_query['count_failed'];
            $data_api['count_deleted'] = $result_query['count_deleted'];

            return response()->json($data_api);
        } else {
            return response()->json('Không được phép truy cập !');
        }
    }

    public function serviceReport(Request $request)
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

            $service_query = Order::query()->where('module', 'service-purchase')
                ->where('gate_id', 0)
                ->where('status', 4)
                ->selectRaw('shop_id')
                ->selectRaw('COUNT(*) as total_record')
                ->selectRaw('COALESCE(SUM(price_ctv),0) as price_ctv')
                ->selectRaw('COALESCE(SUM(price),0) as price')
                ->selectRaw('COALESCE(SUM(price_input),0) as price_input')
                ->selectRaw('COALESCE(SUM(real_received_price_ctv),0) as real_received_price_ctv');

            if ($is_month) {
                $data_service = $service_query
                    ->whereYear('updated_at', $year)->whereMonth('updated_at', $month);
            } else {
                $data_service = $service_query
                    ->whereYear('updated_at', $year)
                    ->whereMonth('updated_at', $month)
                    ->whereDay('updated_at', $day);
            }

            $data_query = $data_service->groupBy('shop_id')->get()->toArray();

            foreach ($data_query as $key => $data_shop) {
                $data['total_record'] = $data_shop['total_record'];
                $data['price_ctv'] = $data_shop['price_ctv'];
                $data['price'] = $data_shop['price'];
                $data['real_received_price_ctv'] = (int)$data_shop['real_received_price_ctv'] + (int)$data_shop['price_input'];

                $data['time'] = $time_display;

                $data_array['shop'] = $this->shop_title_array[$data_shop['shop_id']] ?? $data_shop['shop_id'];
                $data_array['data'] = $data;

                $data_api[] = $data_array;
            }
            return response()->json($data_api);
        } else {
            return response()->json('Không được phép truy cập !');
        }
    }

    public function serviceAutoReport(Request $request)
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

            $categories = ItemConfig::query()->where('module', 'service')
                ->where('gate_id', 1)->select(['id', 'shop_id', 'module', 'idkey', 'title'])->get()->toArray();

            $service_orders = DB::table('order')
                ->where('status', 4)
                ->where('module', 'service-purchase')
                ->where('gate_id', 1)
                ->select(['id', 'module', 'idkey', 'ref_id', 'title', 'price_base', 'price', 'price_input', 'params', 'created_at', 'updated_at']);

            if ($request->filled('idkey')){
                $service_orders->where('idkey',$request->get('idkey'));
            }

            if ($is_month) {
                $service_orders
                    ->whereYear('updated_at', $year)
                    ->whereMonth('updated_at', $month);
            } else {
                $service_orders
                    ->whereYear('updated_at', $year)
                    ->whereMonth('updated_at', $month)
                    ->whereDay('updated_at', $day);
            }
            $service_orders = $service_orders->get()->toArray();
            $order_by_category = $this->groupByField($service_orders, 'ref_id');
            $category_group_by_shop = $this->groupByField($categories, 'shop_id');


            foreach ($category_group_by_shop as $shop_id => $categories_group) {
                $data_array = [];

                $data_array['shop'] = $this->shop_title_array[$shop_id] ?? $shop_id;

                foreach ($categories_group as $category) {

                    if (isset($order_by_category[$category['id']])) {
                        $data_array['data'][$category['id'] . '-' . $category['idkey']]['title'] = $category['title'];
                        $data_array['data'][$category['id'] . '-' . $category['idkey']]['data'] = $order_by_category[$category['id']];
                    }
                }
                if (isset($data_array['data'])) {
                    $data_api[] = $data_array;
                }
            }
            return response()->json($data_api);
        } else {
            return response()->json('Không được phép truy cập !');
        }
    }

    public function storeCardReport(Request $request)
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

            $store_card_query = Order::query()
                ->where('module', 'store-card')
                ->where('status', 1)
                ->selectRaw('shop_id')
                ->selectRaw('COUNT(*) as total_success')
                ->selectRaw('COALESCE(SUM(real_received_price),0) as total_value_order')
                ->selectRaw('COALESCE(SUM(price_input),0) as total_value_ncc');

            if ($is_month) {
                $data_store_card = $store_card_query
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month);
            } else {
                $data_store_card = $store_card_query
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->whereDay('created_at', $day);
            }
            $data_query = $data_store_card->groupBy('shop_id')->get()->toArray();

            foreach ($data_query as $key => $data_shop) {
                $data['total_success'] = $data_shop['total_success'];
                $data['total_value_order'] = $data_shop['total_value_order'];
                $data['total_value_ncc'] = $data_shop['total_value_ncc'];
                $data['time'] = $time_display;

                $data_array['shop'] = $this->shop_title_array[$data_shop['shop_id']] ?? $data_shop['shop_id'];
                $data_array['data'] = $data;

                $data_api[] = $data_array;
            }

            return response()->json($data_api);
        } else {
            return response()->json('Không được phép truy cập !');
        }
    }

    public function withdrawItemReport(Request $request)
    {
        ini_set('max_execution_time', 1200); //20 minutes
        if ($request->filled('sign') && $request->get('sign') == self::SIGN) {
            $data_api = [];
            $is_month = false;

            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $day = Carbon::now()->subDay()->day;//Hôm qua

            if ($request->filled('month') && $request->get('month') == "true") {
                $is_month = true;
                $month = Carbon::now()->subMonth()->month;
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
            }

            foreach ($this->shops as $shop) {
                $withdraw_item_query = Order::query()
                    ->where('module', 'withdraw-item')
                    ->whereNull('acc_id')
                    ->where('status', 1)
                    ->where('shop_id', $shop->id)
                    ->selectRaw('COALESCE(SUM(price),0) as total_item')
                    ->selectRaw('COALESCE(SUM(price_base),0) as total_price_base')
                    ->selectRaw('COUNT(*) as total_record');

                if ($is_month) {
                    $data_withdraw_item = $withdraw_item_query->selectRaw('ref_id')
                        ->whereYear('updated_at', $year)->whereMonth('updated_at', $month)
                        ->selectRaw('CONCAT(MONTH(updated_at),"/",YEAR(updated_at)) as time')
                        ->groupBy('time')->groupBy('ref_id')->get();
                } else {
                    $data_withdraw_item = $withdraw_item_query->selectRaw('ref_id')
                        ->whereYear('updated_at', $year)
                        ->whereMonth('updated_at', $month)->whereDay('updated_at', $day)
                        ->selectRaw('CONCAT(DAY(updated_at),"/",MONTH(updated_at),"/",YEAR(updated_at)) as time')
                        ->groupBy('time')->groupBy('ref_id')->get();
                }
                foreach ($data_withdraw_item as $key => $data_withdraw) {
                    $item_ref = Item::query()->where('id', $data_withdraw->ref_id)->select(['id', 'idkey', 'gate_id', 'slug', 'title', 'params', 'parent_id'])
                        ->with(['parrent' => function ($q) {
                            return $q->select(['id', 'parent_id', 'image',]);
                        }])->first();
                    $data_withdraw_item[$key]['item_ref'] = $item_ref;
                }

                $data_return = [];
                foreach ($data_withdraw_item as $element) {
                    $category_name = $element->itemconfig_ref->parrent->image ?? 'null';
                    $category_pack = $element->itemconfig_ref->title ?? 'null';
                    unset($element['itemconfig_ref'], $element['ref_id']);
                    $data_return[$category_name][$category_pack][] = $element;
                }

                $data_api[] = [
                    'shop' => $shop->domain,
                    'data' => $data_return
                ];
            }
            return response()->json($data_api);
        } else {
            return response()->json('Không được phép truy cập !');
        }
    }

    public function withdrawServiceItemReport(Request $request)
    {
        ini_set('max_execution_time', 1200); //10 minutes
        if ($request->filled('sign') && $request->get('sign') == self::SIGN) {
            $data_api = [];
            $is_month = false;

            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $day = Carbon::now()->subDay()->day;//Hôm qua

            if ($request->filled('month') && $request->get('month') == "true") {
                $is_month = true;
                $month = Carbon::now()->subMonth()->month;
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
            }

            foreach ($this->shops as $shop) {
                $withdraw_item_query = Order::query()
                    ->where('module', 'withdraw-service-item')
                    ->whereNull('acc_id')->where('status', 4)
                    ->where('shop_id', $shop->id)
                    ->where('gate_id', 1)
                    ->selectRaw('COALESCE(SUM(price),0) as total_item')
                    ->selectRaw('COUNT(*) as total_record');

                if ($is_month) {
                    $data_withdraw_item = $withdraw_item_query->selectRaw('ref_id')
                        ->whereYear('updated_at', $year)->whereMonth('updated_at', $month)
                        ->selectRaw('CONCAT(MONTH(updated_at),"/",YEAR(updated_at)) as time')
                        ->groupBy('time')->groupBy('ref_id')->get();
                } else {
                    $data_withdraw_item = $withdraw_item_query->selectRaw('ref_id')
                        ->whereYear('updated_at', $year)
                        ->whereMonth('updated_at', $month)->whereDay('updated_at', $day)
                        ->selectRaw('CONCAT(DAY(updated_at),"/",MONTH(updated_at),"/",YEAR(updated_at)) as time')
                        ->groupBy('time')->groupBy('ref_id')->get();
                }
                foreach ($data_withdraw_item as $key => $data_withdraw) {
                    $item_ref = ItemConfig::query()->where('id', $data_withdraw->ref_id)
                        ->select(['id', 'idkey', 'gate_id', 'slug', 'title', 'params', 'parent_id'])
                        ->with(['parrent' => function ($q) {
                            return $q->select(['id', 'parent_id', 'image',]);
                        }])->first();
                    $data_withdraw_item[$key]['item_ref'] = $item_ref;
                }

                $data_return = [];
                foreach ($data_withdraw_item as $element) {
                    $category_name = $element->item_ref->parrent->image ?? 'null';
                    $category_pack = $element->item_ref->title ?? 'null';
                    unset($element['item_ref'], $element['ref_id']);
                    $data_return[$category_name][$category_pack][] = $element;
                }

                $data_api[] = [
                    'shop' => $shop->domain,
                    'shop_id' => $shop->id,
                    'data' => $data_return
                ];
            }
            return response()->json($data_api);
        } else {
            return response()->json('Không được phép truy cập !');
        }
    }

    public function withdrawServiceItemReportTC(Request $request)
    {
        ini_set('max_execution_time', 1200); //10 minutes
        if ($request->filled('sign') && $request->get('sign') == self::SIGN) {
            $data_api = [];
            $is_month = false;

            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $day = Carbon::now()->subDay()->day;//Hôm qua

            if ($request->filled('month') && $request->get('month') == "true") {
                $is_month = true;
                $month = Carbon::now()->subMonth()->month;
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
            }

            foreach ($this->shops as $shop) {
                $withdraw_item_query = Order::query()
                    ->where('module', 'withdraw-service-item')
                    ->whereNull('acc_id')->where('status', 4)
                    ->where('shop_id', $shop->id)
                    ->where('gate_id', 0)
                    ->selectRaw('COALESCE(SUM(price),0) as total_item')
                    ->selectRaw('COALESCE(SUM(price_input),0) as total_price_sms')
                    ->selectRaw('COUNT(*) as total_record');

                if ($is_month) {
                    $data_withdraw_item = $withdraw_item_query
                        ->whereYear('updated_at', $year)->whereMonth('updated_at', $month)
                        ->selectRaw('CONCAT(MONTH(updated_at),"/",YEAR(updated_at)) as time')
                        ->groupBy('time')->get();
                } else {
                    $data_withdraw_item = $withdraw_item_query
                        ->whereYear('updated_at', $year)
                        ->whereMonth('updated_at', $month)->whereDay('updated_at', $day)
                        ->selectRaw('CONCAT(DAY(updated_at),"/",MONTH(updated_at),"/",YEAR(updated_at)) as time')
                        ->groupBy('time')->get();
                }

//                foreach ($data_withdraw_item as $key => $data_withdraw) {
//                    $item_ref = ItemConfig::query()->where('id', $data_withdraw->ref_id)
//                        ->select(['id', 'idkey', 'gate_id', 'slug', 'title', 'params', 'parent_id'])
//                        ->with(['parrent' => function ($q) {
//                            return $q->select(['id', 'parent_id', 'image',]);
//                        }])->first();
//                    $data_withdraw_item[$key]['item_ref'] = $item_ref;
//                }

//                $data_return = [];
//                foreach ($data_withdraw_item as $element) {
//                    $category_name = $element->item_ref->parrent->image ?? 'null';
//                    $category_pack = $element->item_ref->title ?? 'null';
//                    unset($element['item_ref'], $element['ref_id']);
//                    $data_return[$category_name][$category_pack][] = $element;
//                }

                $data_api[] = [
                    'shop' => $shop->domain,
                    'shop_id' => $shop->id,
                    'data' => $data_withdraw_item
                ];
            }
            return response()->json($data_api);
        } else {
            return response()->json('Không được phép truy cập !');
        }
    }

    private function groupByField($array, $field)
    {
        return array_reduce($array, function ($groupedArray, $item) use ($field) {
            $key = $item->{$field} ?? $item[$field];
            $groupedArray[$key][] = $item;
            return $groupedArray;
        }, []);
    }

    public function shops(Request $request)
    {
        if ($request->filled('sign') && $request->get('sign') == self::SIGN) {
            return $this->shops;
        } else {
            return response()->json('Không được phép truy cập !');
        }
    }
    // function truong viet
    public function getReportBalance(Request $request)
    {
        if ($request->get('sign') != self::SIGN) {
            return response()->json('Không được phép truy cập !');
        }
        $data = UserBalance::query();
        if ($request->filled('started_at')) {
            $data = $data->whereDate('created_at', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
        } else {
            $data = $data->whereDate('created_at', Carbon::today());
        }
        if ($request->filled('type')) {
            $data = $data->where('type', $request->get('type'));
        }
        $data = $data->select('id', 'title', 'balance', 'type', 'created_at')->get();
        return response()->json($data);
    }

}
