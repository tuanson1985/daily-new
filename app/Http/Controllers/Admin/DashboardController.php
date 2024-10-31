<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Library\NickHelper;
use App\Models\ActivityLog;
use App\Models\Charge;
use App\Models\Item;
use App\Models\Nick;
use App\Models\Order;
use App\Models\PlusMoney;
use App\Models\SocialAccount;
use App\Models\StoreCard;
use App\Models\Txns;
use App\Models\TxnsVp;
use App\Models\User;
use App\Models\Shop;
use App\Models\Shop_Group;
use App\Models\GameAccess;
use App\Models\Withdraw;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewer;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DepositBankExport;
use App\Exports\ChargeExport;
use App\Exports\StoreCardExport;
use App\Exports\DonateExport;
use App\Exports\UserBDayExport;
use App\Exports\IdolBookingTimeExport;
use Symfony\Component\HttpKernel\HttpCache\Store;
use function GuzzleHttp\Psr7\str;

class DashboardController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('permission:dashboard-revenue-overview', ['only' => ['ReportGeneralTurnover']]);
        $this->middleware('permission:dashboard-revenue-ratio', ['only' => ['ReportGeneralDensityTurnover']]);
        $this->middleware('permission:dashboard-transfer-atm', ['only' => ['ReportTransfer2']]);
        $this->middleware('permission:dashboard-store-card', ['only' => ['ReportStoreCard2']]);
        $this->middleware('permission:dashboard-recharge', ['only' => ['ReportCharge2']]);
        $this->middleware('permission:dashboard-account', ['only' => ['ReportAccount']]);
        $this->middleware('permission:dashboard-minigame-withdraw', ['only' => ['ReportMinigame', 'ReportWithdrawItem']]);
        $this->middleware('permission:dashboard-service', ['only' => ['ReportService']]);
        $this->middleware('permission:dashboard-service-auto', ['only' => ['ReportServiceAuto']]);
        $this->middleware('permission:dashboard-plus-money', ['only' => ['ReportMoney']]);
        $this->middleware('permission:dashboard-new-user', ['only' => ['ReportUser']]);
        $this->middleware('permission:dashboard-transaction-user', ['only' => ['ReportTransactionUser']]);
        $this->middleware('permission:dashboard-biggest-deal', ['only' => ['ReportTxnsBiggest']]);
        $this->middleware('permission:dashboard-top-balance', ['only' => ['ReportTopMoney']]);
        $this->middleware('permission:dashboard-total-balance', ['only' => ['ReportSurplusUser']]);
    }

    public function index(Request $request)
    {
        $page_title = 'Dashboard';
        $page_breadcrumbs = [
            ['page' => '1',
                'title' => 'Home',
            ],
        ];
        $category_access = GameAccess::where(['user_id' => auth()->user()->id, 'active' => 1])->with(['acc_category'])->whereHas('acc_category')->get();
        ActivityLog::add($request, 'Truy cập dashboard index');
        return view('admin.dashboard.index-v2', compact('page_title', 'page_breadcrumbs', 'category_access'));
    }

    public function GrowthUser(Request $request)
    {
        $response_cate_data = cache("growth_user");

        return dd($response_cate_data);
        // if(isset($response_cate_data)){
        //     cache(["game_props_list_{$slug}" => $response_cate_data], 600);
        // }

        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        if ($request->filled('year')) {
            $year = $request->get('year');
        }
        if ($year < Carbon::now()->year) {
            $month = 12;
        }
        $data = User::select(DB::raw('count(*) as user, month(created_at) as m'))
            ->where('account_type', 2)
            ->whereYear('created_at', '=', $year)
            ->groupBy('m')
            ->get();
        $growth_user = [];
        for ($i = 1; $i <= $month; $i++) {
            $growth_user[$i] = 0;
            $growth_month[$i] = "Tháng " . $i;
        }
        foreach ($data as $item) {
            $growth_user[$item->m] = $item->user;
        }
        // dd($growth);
        $growth = [
            'growth_user' => $growth_user,
            'growth_month' => $growth_month,
        ];
        return response()->json([
            "success" => true,
            "data" => $growth,
        ], 200);
    }

    public function GrowthCTV(Request $request)
    {
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;
        if ($request->filled('year')) {
            $year = $request->get('year');
        }
        if ($year < Carbon::now()->year) {
            $month = 12;
        }
        $data = User::select(DB::raw('count(*) as user, month(created_at) as m'))
            ->where('account_type', 3)
            ->whereYear('created_at', '=', $year)
            ->groupBy('m')
            ->get();
        $growth_ctv = [];
        for ($i = 1; $i <= $month; $i++) {
            $growth_ctv[$i] = 0;
            $growth_month[$i] = "Tháng " . $i;
        }
        foreach ($data as $item) {
            $growth_ctv[$item->m] = $item->user;
        }
        $growth = [
            'growth_ctv' => $growth_ctv,
            'growth_month' => $growth_month,
        ];
        return response()->json([
            "success" => true,
            "data" => $growth,
        ], 200);
    }

    public function ClassifyUser(Request $request)
    {
        $idol = User::where('account_type', 2)->where('is_idol', 1)->where('status', 1)->count();
        $pedding_idol = User::where('account_type', 2)->where('is_idol', 2)->where('status', 1)->count();
        $user = User::where('account_type', 2)
            ->where(function ($query) use ($request) {
                $query->where('is_idol', null);
                $query->orWhere('is_idol', 0);
            })
            ->where('status', 1)->count();
        $user_block = User::where('account_type', 2)->where('status', 0)->count();
        $user_qtv = User::where('account_type', 1)->where('status', 1)->count();
        $data = [
            'idol' => $idol,
            'pedding_idol' => $pedding_idol,
            'user' => $user,
            'user_block' => $user_block,
            'user_qtv' => $user_qtv,
        ];
        return response()->json([
            "success" => true,
            "data" => $data,
        ], 200);
    }

    public function GrowthTopupCard(Request $request)
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        $day = Carbon::now()->day;
        if ($request->filled('year')) {
            $year = $request->get('year');
            $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }
        if ($request->filled('month')) {
            $month = $request->get('month');
            $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }
        $sum_day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        for ($i = 1; $i <= $day; $i++) {
            $growth_card_fail[$i] = 0;
            $growth_card_succes[$i] = 0;
            $growth_card_pendding[$i] = 0;
            $growth_day[$i] = "Ngày " . $i;
        }
        $data_card_fail = Charge::select(DB::raw('count(*) as charge, day(created_at) as d'))->whereMonth('created_at', '=', $month)->whereYear('created_at', '=', $year)->where('status', 0)->groupBy('d')->get();
        foreach ($data_card_fail as $item) {
            $growth_card_fail[$item->d] = $item->charge;
        }
        $data_card_succes = Charge::select(DB::raw('count(*) as charge, day(created_at) as d'))->whereMonth('created_at', '=', $month)->whereYear('created_at', '=', $year)->where('status', 1)->groupBy('d')->get();
        foreach ($data_card_succes as $item) {
            $growth_card_succes[$item->d] = $item->charge;
        }
        $data_card_pendding = Charge::select(DB::raw('count(*) as charge, day(created_at) as d'))->whereMonth('created_at', '=', $month)->whereYear('created_at', '=', $year)->where('status', 2)->groupBy('d')->get();
        foreach ($data_card_pendding as $item) {
            $growth_card_pendding[$item->d] = $item->charge;
        }
        $data = [
            'growth_card_fail' => $growth_card_fail,
            'growth_card_susscess' => $growth_card_succes,
            'growth_card_pendding' => $growth_card_pendding,
            'growth_day' => $growth_day,
        ];
        return response()->json([
            "success" => true,
            "data" => $data,
        ], 200);
    }

    public function GrowthStoreCard(Request $request)
    {
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;
        $day = Carbon::now()->day;
        if ($request->filled('year')) {
            $year = $request->get('year');
            $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }
        if ($request->filled('month')) {
            $month = $request->get('month');
            $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }
        for ($i = 1; $i <= $day; $i++) {
            $growth_fail[$i] = 0;
            $growth_susscess[$i] = 0;
            $growth_pendding[$i] = 0;
            $growth_day[$i] = "Ngày " . $i;
        }
        $data_fail = Order::select(DB::raw('count(*) as item, day(created_at) as d'))->where('module', config('module.store-card.key'))->whereMonth('created_at', '=', $month)->whereYear('created_at', '=', $year)->where('status', 0)->groupBy('d')->get();
        foreach ($data_fail as $item) {
            $growth_fail[$item->d] = $item->item;
        }
        $data_susscess = Order::select(DB::raw('count(*) as item, day(created_at) as d'))->where('module', config('module.store-card.key'))->whereMonth('created_at', '=', $month)->whereYear('created_at', '=', $year)->where('status', 1)->groupBy('d')->get();
        foreach ($data_susscess as $item) {
            $growth_susscess[$item->d] = $item->item;
        }
        $data_pendding = Order::select(DB::raw('count(*) as item, day(created_at) as d'))->where('module', config('module.store-card.key'))->whereMonth('created_at', '=', $month)->whereYear('created_at', '=', $year)
            ->where(function ($query) use ($request) {
                $query->where('status', 2);
                $query->orWhere('status', 4);
                $query->orWhere('status', 5);
            })
            ->groupBy('d')->get();
        foreach ($data_pendding as $item) {
            $growth_pendding[$item->d] = $item->item;
        }
        $data = [
            'growth_fail' => $growth_fail,
            'growth_susscess' => $growth_susscess,
            'growth_pendding' => $growth_pendding,
            'growth_day' => $growth_day,
        ];
        return response()->json([
            "success" => true,
            "data" => $data,
        ], 200);
    }

    public function GrowthTopupBank(Request $request)
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        $day = Carbon::now()->day;
        if ($request->filled('year')) {
            $year = $request->get('year');
            $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }
        if ($request->filled('month')) {
            $month = $request->get('month');
            $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }
        for ($i = 1; $i <= $day; $i++) {
            $growth_fail[$i] = 0;
            $growth_susscess[$i] = 0;
            $growth_pendding[$i] = 0;
            $growth_cancelled[$i] = 0;
            $growth_day[$i] = "Ngày " . $i;
        }
        $data_fail = Order::select(DB::raw('count(*) as item, day(created_at) as d'))
            ->where('module', '=', 'charge_bank')
            ->where('payment_type', 1)
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year)
            ->where('status', 0)
            ->groupBy('d')
            ->get();
        foreach ($data_fail as $item) {
            $growth_fail[$item->d] = $item->item;
        }
        $data_susscess = Order::select(DB::raw('count(*) as item, day(created_at) as d'))
            ->where('module', '=', 'charge_bank')
            ->where('payment_type', 1)
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year)
            ->where('status', 1)
            ->groupBy('d')
            ->get();
        foreach ($data_susscess as $item) {
            $growth_susscess[$item->d] = $item->item;
        }
        $data_pendding = Order::select(DB::raw('count(*) as item, day(created_at) as d'))
            ->where('module', '=', 'charge_bank')
            ->where('payment_type', 1)
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year)
            ->where('status', 2)
            ->groupBy('d')
            ->get();
        foreach ($data_pendding as $item) {
            $growth_pendding[$item->d] = $item->item;
        }
        $data_cancelled = Order::select(DB::raw('count(*) as item, day(created_at) as d'))
            ->where('module', '=', 'charge_bank')
            ->where('payment_type', 1)
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year)
            ->where('status', 3)
            ->groupBy('d')
            ->get();
        foreach ($data_cancelled as $item) {
            $growth_cancelled[$item->d] = $item->item;
        }
        $data = [
            'growth_fail' => $growth_fail,
            'growth_susscess' => $growth_susscess,
            'growth_pendding' => $growth_pendding,
            'growth_cancelled' => $growth_cancelled,
            'growth_day' => $growth_day,
        ];
        return response()->json([
            "success" => true,
            "data" => $data,
        ], 200);
    }

    public function GrowthDonate(Request $request)
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        $day = Carbon::now()->day;
        if ($request->filled('year')) {
            $year = $request->get('year');
            $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }
        if ($request->filled('month')) {
            $month = $request->get('month');
            $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }
        for ($i = 1; $i <= $day; $i++) {
            $growth_fail[$i] = 0;
            $growth_susscess[$i] = 0;
            $growth_pendding[$i] = 0;
            $growth_cancelled[$i] = 0;
            $growth_day[$i] = "Ngày " . $i;
        }
        $data_fail = Order::select(DB::raw('count(*) as item, day(created_at) as d'))
            ->where('module', 'donate')
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year)
            ->where('status', 0)
            ->groupBy('d')
            ->get();
        foreach ($data_fail as $item) {
            $growth_fail[$item->d] = $item->item;
        }
        $data_susscess = Order::select(DB::raw('count(*) as item, day(created_at) as d'))
            ->where('module', 'donate')
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year)
            ->where('status', 1)
            ->groupBy('d')
            ->get();
        foreach ($data_susscess as $item) {
            $growth_susscess[$item->d] = $item->item;
        }
        $data_pendding = Order::select(DB::raw('count(*) as item, day(created_at) as d'))
            ->where('module', 'donate')
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year)
            ->where('status', 2)
            ->groupBy('d')
            ->get();
        foreach ($data_pendding as $item) {
            $growth_pendding[$item->d] = $item->item;
        }
        $data_pendding = Order::select(DB::raw('count(*) as item, day(created_at) as d'))
            ->where('module', 'donate')
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year)
            ->where('status', 2)
            ->groupBy('d')
            ->get();
        foreach ($data_pendding as $item) {
            $growth_pendding[$item->d] = $item->item;
        }
        $data_cancelled = Order::select(DB::raw('count(*) as item, day(created_at) as d'))
            ->where('module', 'donate')
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year)
            ->where('status', 3)
            ->groupBy('d')
            ->get();
        foreach ($data_cancelled as $item) {
            $growth_cancelled[$item->d] = $item->item;
        }
        $data = [
            'growth_fail' => $growth_fail,
            'growth_susscess' => $growth_susscess,
            'growth_pendding' => $growth_pendding,
            'growth_cancelled' => $growth_cancelled,
            'growth_day' => $growth_day,
        ];
        return response()->json([
            "success" => true,
            "data" => $data,
        ], 200);
    }

    public function ExportDepositBank(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        return Excel::download(new DepositBankExport($year, $month), 'Thống kê nạp tiền qua ngân hàng tự động ' . $month . '-' . $year . '.xlsx');
    }

    public function ExportCharge(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        return Excel::download(new ChargeExport($year, $month), 'Thống kê nạp thẻ ' . $month . '-' . $year . '.xlsx');
    }

    public function ExportStoreCard(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        return Excel::download(new StoreCardExport($year, $month), 'Thống kê mua thẻ ' . $month . '-' . $year . '.xlsx');
    }

    public function ExportDonate(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        return Excel::download(new DonateExport($year, $month), 'Thống kê donate ' . $month . '-' . $year . '.xlsx');
    }

    public function ExportUserBirthday()
    {
        return Excel::download(new UserBDayExport(), 'Thống kê ngày tháng năm sinh user.xlsx');
    }

    public function ExportIdolBookingTime()
    {
        return Excel::download(new IdolBookingTimeExport(), 'Thống kê thời gian nhận booking idol.xlsx');
    }

    // thông tin nhóm điểm bán
    public function classifyShopGroup(Request $request)
    {
        $shop_group = Shop_Group::orderBy('id', 'asc')->withCount('shop')->get();
        $data = array();
        $title = array();
        $shop_count = array();
        foreach ($shop_group as $item) {
            $title[] = $item->title;
            $shop_count[] = (int)$item->shop_count;
        }
        $data = [
            'title' => $title,
            'shop_count' => $shop_count
        ];
        return response()->json([
            "success" => true,
            "data" => $data,
        ], 200);
    }

    // tăng trưởng shop
    public function GrowthShop(Request $request)
    {
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;
        if ($request->filled('year')) {
            $year = $request->get('year');
        }
        if ($year < Carbon::now()->year) {
            $month = 12;
        }
        $data = Shop::select(DB::raw('count(*) as shop, month(created_at) as m'))
            ->whereYear('created_at', '=', $year)
            ->groupBy('m')
            ->get();
        $growth_shop = [];
        for ($i = 1; $i <= $month; $i++) {
            $growth_shop[$i] = 0;
            $growth_month[$i] = "Tháng " . $i;
        }
        foreach ($data as $item) {
            $growth_shop[$item->m] = $item->shop;
        }
        $growth = [
            'growth_shop' => $growth_shop,
            'growth_month' => $growth_month,
        ];
        return response()->json([
            "success" => true,
            "data" => $growth,
        ], 200);
    }

    public function ReportCharge(Request $request)
    {
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;
        if ($request->filled('year')) {
            $year = $request->get('year');
        }
        if ($request->filled('month')) {
            $month = $request->get('month');
        }
        $data = Charge::with(array('shop' => function ($query) {
            $query->select('id', 'title', 'domain');
        }))
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year)
            ->groupBy('shop_id')
            ->selectRaw('id')
            ->selectRaw('shop_id')
            ->selectRaw('count(*) as total_record')
            ->selectRaw('count(IF(status = 1,1,NULL)) as total_record_success')
            ->selectRaw('count(IF(status = 2,1,NULL)) as total_record_pendding')
            ->selectRaw('count(IF(status = 0,1,NULL)) as total_record_error')
            ->selectRaw('SUM(IF(status = 1, amount, 0)) as total_amount');
        return \datatables()->eloquent($data)
            ->editColumn('total_amount', function ($row) {
                return number_format($row->total_amount);
            })
            ->addColumn('shop_title', function ($row) {
                $result = '';
                if (isset($row->shop->domain)) {
                    $result .= $row->shop->domain;
                } else {
                    $result .= "null";
                }
                return $result;
            })
            ->toJson();
    }

    public function ReportStoreCard(Request $request)
    {
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;
        if ($request->filled('year')) {
            $year = $request->get('year');
        }
        if ($request->filled('month')) {
            $month = $request->get('month');
        }
        $data = Order::with(array('shop' => function ($query) {
            $query->select('id', 'title', 'domain');
        }))
            ->where('module', config('module.store-card.key'))
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year)
            ->groupBy('shop_id')
            ->selectRaw('id')
            ->selectRaw('shop_id')
            ->selectRaw('count(*) as total_record')
            ->selectRaw('count(IF(status = 1,1,NULL)) as total_record_success')
            ->selectRaw('count(IF(status = 2,1,NULL)) as total_record_pendding')
            ->selectRaw('count(IF(status = 0,1,NULL)) as total_record_error')
            ->selectRaw('SUM(IF(status = 1, price, 0)) as total_amount');

        return \datatables()->eloquent($data)
            ->editColumn('total_amount', function ($row) {
                return number_format($row->total_amount);
            })
            ->addColumn('shop_title', function ($row) {
                $result = '';
                if (isset($row->shop->domain)) {
                    $result .= $row->shop->domain;
                } else {
                    $result .= "null";
                }
                return $result;
            })
            ->toJson();
    }

    public function ReportCharge2(Request $request)
    {
        $charge_query = Charge::query();

        if (session('shop_id')) {
            $charge_query->where('shop_id', session('shop_id'));
        } else {
            if (isset(auth()->user()->shop_access) && auth()->user()->shop_access !== "all") {
                $shop_id_shop_access = json_decode(auth()->user()->shop_access);
                $charge_query->whereIn('shop_id', $shop_id_shop_access);
            }
        }
        switch ($request->get('type')) {
            case 'today':
            case 'day':
                $time_query = $request->get('type') === 'day' ? Carbon::createFromFormat('d/m/Y', $request->get('time')) : Carbon::today();
                $charge_query->whereDate('created_at', $time_query);
                break;
            case 'week':
            case '7-day':
                $time_query = $request->get('type') === 'week' ? explode('-', preg_replace('/\s+/', '', $request->get('time'))) : '';
                $start = $request->get('type') === '7-day' ? Carbon::today()->subDays(7) : Carbon::createFromFormat('d/m/Y', $time_query[0]);
                $end = $request->get('type') === '7-day' ? Carbon::today() : Carbon::createFromFormat('d/m/Y', $time_query[1]);
                $charge_query->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end);
                break;
            case 'month':
            case 'this-month':
                $time_query = $request->get('type') === 'month' ? explode('/', $request->get('time')) : '';
                $month = $request->get('type') === 'month' ? $time_query[0] : Carbon::now()->month;
                $year = $request->get('type') === 'month' ? $time_query[1] : Carbon::now()->year;

                $charge_query->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year);
                break;
            case 'this-year':
            case 'year':
                $year = $request->get('type') === 'year' ? $request->get('time') : Carbon::now()->year;
                $charge_query->whereYear('created_at', $year);
                break;
        }

        $data = $charge_query
            ->selectRaw('count(*) as total_record')
            ->selectRaw('SUM(IF(status = 1,1,0)) as total_record_success')
            ->selectRaw('SUM(IF(status = 0,1,0)) as total_record_error')
            ->selectRaw('SUM(IF(status = 2,1,0)) as total_record_pending')
            ->selectRaw('SUM(IF(status = 1,real_received_amount,0)) / SUM(IF(status = 1,1,0)) as avg_value')
            ->selectRaw('COUNT(DISTINCT user_id) as total_user')
            ->selectRaw('SUM(IF(status = 1,amount,0)) as total_charge_value')
            ->selectRaw('SUM(IF(status = 1,money_received,0)) as total_money_received')
            ->selectRaw('SUM(IF(status = 1,real_received_amount,0)) as total_charge_real_value')
            ->first();
        return response()->json($data);
    }

    public function ReportStoreCard2(Request $request)
    {
        $order_query = Order::query();
        $user_query = User::query();

        $order_query->where('module', 'store-card');

        if (session('shop_id')) {
            $order_query->where('shop_id', session('shop_id'));
            $user_query->where('shop_id', session('shop_id'));
        } else {
            if (isset(auth()->user()->shop_access) && auth()->user()->shop_access !== "all") {
                $shop_id_shop_access = json_decode(auth()->user()->shop_access);
                $order_query->whereIn('shop_id', $shop_id_shop_access);
                $user_query->whereIn('shop_id', $shop_id_shop_access);
            }
        }

        switch ($request->get('type')) {
            case 'today':
            case 'day':
                $time_query = $request->get('type') === 'day' ? Carbon::createFromFormat('d/m/Y', $request->get('time')) : Carbon::today();
                $order_query->whereDate('created_at', $time_query);
                break;
            case 'week':
            case '7-day':
                $time_query = $request->get('type') === 'week' ? explode('-', preg_replace('/\s+/', '', $request->get('time'))) : '';
                $start = $request->get('type') === '7-day' ? Carbon::today()->subDays(7) : Carbon::createFromFormat('d/m/Y', $time_query[0]);
                $end = $request->get('type') === '7-day' ? Carbon::today() : Carbon::createFromFormat('d/m/Y', $time_query[1]);
                $order_query->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end);
                break;
            case 'month':
            case 'this-month':
                $time_query = $request->get('type') === 'month' ? explode('/', $request->get('time')) : '';
                $month = $request->get('type') === 'month' ? $time_query[0] : Carbon::now()->month;
                $year = $request->get('type') === 'month' ? $time_query[1] : Carbon::now()->year;

                $order_query->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year);
                break;
            case 'year':
            case 'this-year':
                $year = $request->get('type') === 'year' ? $request->get('time') : Carbon::now()->year;
                $order_query->whereYear('created_at', $year);
                break;
        }

        $total_user = $user_query->selectRaw('COUNT(*) as total_user')->first()->total_user;
        $count_user = clone $order_query;
        $count_user = $count_user->where('status', 1)
            ->selectRaw('CONCAT(COUNT(DISTINCT author_id),"( ", ROUND(COUNT(DISTINCT author_id) / ' . $total_user . ' * 100,2), "% )") as total_user')
            ->first();
        $charge_query = $order_query->selectRaw('SUM(IF(status = 1,1,0)) as total_record_success')
            ->selectRaw('SUM(IF(status = 0,1,0)) as total_record_error')
            ->selectRaw('SUM(IF(status = 2,1,0)) as total_record_pending')
            ->selectRaw('SUM(IF(status = 1,real_received_price,0)) as total_income')
            ->selectRaw("'$count_user->total_user' as total_user")
            ->selectRaw('AVG(IF(status = 1 ,real_received_price,0)) as avg_income')
            ->first();
        return response()->json($charge_query);
    }

    public function ReportMoney(Request $request)
    {
        $plus_money_query = PlusMoney::query();
        if (session('shop_id')) {
            $plus_money_query->where('plus_money.shop_id', session('shop_id'));
        } else {
            if (isset(auth()->user()->shop_access) && auth()->user()->shop_access !== "all") {
                $shop_id_shop_access = json_decode(auth()->user()->shop_access);
                $plus_money_query->whereIn('plus_money.shop_id', $shop_id_shop_access);
            }
        }
        switch ($request->get('type')) {
            case 'today':
            case 'day':
                $time_query = $request->get('type') === 'day' ? Carbon::createFromFormat('d/m/Y', $request->get('time')) : Carbon::today();
                $plus_money_query->whereDate('plus_money.created_at', $time_query);
                break;
            case '7-day':
            case 'week':
                $time_query = $request->get('type') === 'week' ? explode('-', preg_replace('/\s+/', '', $request->get('time'))) : '';
                $start = $request->get('type') === '7-day' ? Carbon::today()->subDays(7) : Carbon::createFromFormat('d/m/Y', $time_query[0]);
                $end = $request->get('type') === '7-day' ? Carbon::today() : Carbon::createFromFormat('d/m/Y', $time_query[1]);

                $plus_money_query->whereDate('plus_money.created_at', '>=', $start)->whereDate('plus_money.created_at', '<=', $end);
                break;
            case  'month':
            case 'this-month':
                $time_query = $request->get('type') === 'month' ? explode('/', $request->get('time')) : '';
                $month = $request->get('type') === 'month' ? $time_query[0] : Carbon::now()->month;
                $year = $request->get('type') === 'month' ? $time_query[1] : Carbon::now()->year;
                $plus_money_query->whereMonth('plus_money.created_at', $month)
                    ->whereYear('plus_money.created_at', $year);
                break;
            case 'year':
            case 'this-year':
                $year = $request->get('type') === 'year' ? $request->get('time') : Carbon::now()->year;
                $plus_money_query->whereYear('plus_money.created_at', $year);
                break;
        }

        $data_user = $plus_money_query->join('users', 'users.id', 'plus_money.user_id');
        $count_user_add = clone $data_user;
        $count_user_minus = clone $data_user;

        $count_user_add = $count_user_add->where('plus_money.is_add', 1)->distinct()->count('user_id');
        $count_user_minus = $count_user_minus->where('plus_money.is_add', 0)->distinct()->count('user_id');
        if (!session('shop_id')) {
            $data_user->selectRaw('SUM(IF(account_type = 1 AND is_add = 1, amount, 0)) as add_money_qtv')
                ->selectRaw('SUM(IF(account_type = 1 AND is_add = 0, amount, 0)) as minus_money_qtv');
        }
        $result = $data_user
            ->selectRaw('SUM(IF(account_type = 2 AND is_add = 1, amount, 0)) as add_money_user')
            ->selectRaw('SUM(IF(account_type = 2 AND is_add = 0, amount, 0)) as minus_money_user')
            ->selectRaw('SUM(IF(is_add = 1,1,0)) as count_command_add')
            ->selectRaw('SUM(IF(is_add = 0,1,0)) as count_command_minus')
            ->first();
        $result['count_user_add'] = $count_user_add;
        $result['count_user_minus'] = $count_user_minus;
        return response()->json($result);
    }

    public function ReportWithdraw(Request $request)
    {
        $withdraw_query = Withdraw::query();
        if (!$request->filled('started_at') && !$request->filled('ended_at')) {
            $withdraw_query->whereDate('withdraw.created_at', Carbon::today());
        }
        if ($request->filled('started_at')) {
            $time = Carbon::createFromFormat('d/m/Y', $request->get('started_at'));
            $time->hour(0);
            $time->minute(0);
            $time->second(0);
            $withdraw_query->where('withdraw.created_at', '>=', $time);
        }
        if ($request->filled('ended_at')) {
            $time = Carbon::createFromFormat('d/m/Y', $request->get('ended_at'));
            $time->hour(23);
            $time->minute(59);
            $time->second(59);
            $withdraw_query->where('withdraw.created_at', '<=', $time);
        }

        $withdraw_query = $withdraw_query->join('users', 'users.id', 'withdraw.user_id')
            ->where('withdraw.status', 1)
            ->selectRaw('SUM(IF(account_type = 1 , amount_passed, 0)) as total_withdraw_qtv')
            ->selectRaw('SUM(IF(account_type = 2 , amount_passed, 0)) as total_withdraw_user')
            ->first();

        return response()->json($withdraw_query);
    }

    public function ReportService(Request $request)
    {
        $response_cate_data = cache("report_service");

        if (empty($response_cate_data)){
            $order_query = Order::query()->where('gate_id', 0);

            if (session('shop_id')) {
                $order_query->where('shop_id', session('shop_id'));
            } else {
                if (isset(auth()->user()->shop_access) && auth()->user()->shop_access !== "all") {
                    $shop_id_shop_access = json_decode(auth()->user()->shop_access);
                    $order_query->whereIn('shop_id', $shop_id_shop_access);
                }
            }

            $order_query_date = clone $order_query;
            switch ($request->get('type')) {
                case '7-day':
                    $order_query->whereDate('updated_at', '>=', Carbon::today()->subDays(7));
                    break;
                case 'this-month':
                    $order_query->whereMonth('updated_at', Carbon::today()->month)
                        ->whereYear('updated_at', Carbon::today()->year);
                    break;
                case 'this-year':
                    $order_query->whereYear('updated_at', Carbon::today()->year);
                    break;
                case 'day':
                    $time = Carbon::createFromFormat('d/m/Y', $request->get('time'));
                    $order_query->whereDate('updated_at', $time);
                    break;
                case 'week':
                    $time = explode('-', preg_replace('/\s+/', '', $request->get('time')));
                    $start = Carbon::createFromFormat('d/m/Y', $time[0]);
                    $end = Carbon::createFromFormat('d/m/Y', $time[1]);
                    $order_query->whereDate('updated_at', '>=', $start)
                        ->whereDate('updated_at', '<=', $end);
                    break;
                case 'month':
                    $time = explode('/', $request->get('time'));
                    $month = $time[0];
                    $year = $time[1];
                    $order_query->whereMonth('updated_at', $month)
                        ->whereYear('updated_at', $year);
                    break;
                case 'year':
                    $order_query->whereYear('updated_at', $request->get('time'));
                    break;
                default:
                    // to day
                    $order_query->whereDate('updated_at', Carbon::today());
                    break;
            }
            $count_pending = $order_query_date->selectRaw('SUM(IF(status = 1 , 1, 0)) as total_record_pending')->first()->total_record_pending ?? 0;
            $order_query = $order_query
                ->selectRaw('COUNT(*) as total_record')
                ->selectRaw('SUM(IF(status = 4 , 1, 0)) as total_record_paid')
                ->selectRaw($count_pending . ' as total_record_pending')
                ->selectRaw('SUM(IF(status = 4 , 1, 0)) as total_record_success')
                ->selectRaw('SUM(IF(status = 0 , 1, 0)) as total_record_canceled')
                ->selectRaw('SUM(IF(status != 0 AND status != 3 , price, 0)) as total_turnover_success')

//            ->selectRaw('SUM(IF(status = 4 , price_ctv, 0)) as total_price_ctv')

                ->selectRaw('ROUND(COALESCE(SUM(IF(status = 4 , real_received_price_ctv, 0)),0),0) as total_complete_price_service')
                ->selectRaw('ROUND(COALESCE(SUM(IF(status = 4, price, 0)) - COALESCE(SUM(IF(status = 4,real_received_price_ctv, 0)),0),0),0) as total_price_profit')
                ->first();

            cache(["report_service" => $order_query], 600);

            return response()->json($order_query);
        }
        return response()->json($response_cate_data);
    }

    public function ReportServiceAuto(Request $request)
    {
        $response_cate_data = cache("report_service_auto");

        if (empty($response_cate_data)){
            $order_query = Order::query()->where('module', 'service-purchase')->where('gate_id', 1);
            $plus_item_query = TxnsVp::query();

            if (session('shop_id')) {
                $order_query->where('shop_id', session('shop_id'));
                $plus_item_query->where('shop_id', session('shop_id'));
            } else {
                if (isset(auth()->user()->shop_access) && auth()->user()->shop_access !== "all") {
                    $shop_id_shop_access = json_decode(auth()->user()->shop_access);
                    $order_query->whereIn('shop_id', $shop_id_shop_access);
                    $plus_item_query->whereIn('shop_id', $shop_id_shop_access);
                }
            }

            switch ($request->get('type')) {
                case '7-day':
                    $order_query->whereDate('updated_at', '>=', Carbon::today()->subDays(7));
                    $plus_item_query->whereDate('updated_at', '>=', Carbon::today()->subDays(7));
                    break;
                case 'this-month':
                    $order_query->whereMonth('updated_at', Carbon::today()->month)
                        ->whereYear('created_at', Carbon::today()->year);
                    $plus_item_query->whereMonth('updated_at', Carbon::today()->month)
                        ->whereYear('updated_at', Carbon::today()->year);
                    break;
                case 'this-year':
                    $order_query->whereYear('updated_at', Carbon::today()->year);
                    $plus_item_query->whereYear('updated_at', Carbon::today()->year);
                    break;
                case 'day':
                    $time = Carbon::createFromFormat('d/m/Y', $request->get('time'));
                    $order_query->whereDate('updated_at', $time);
                    break;
                case 'week':
                    $time = explode('-', preg_replace('/\s+/', '', $request->get('time')));
                    $start = Carbon::createFromFormat('d/m/Y', $time[0]);
                    $end = Carbon::createFromFormat('d/m/Y', $time[1]);
                    $order_query->whereDate('updated_at', '>=', $start)->whereDate('updated_at', '<=', $end);
                    break;
                case 'month':
                    $time = explode('/', $request->get('time'));
                    $month = $time[0];
                    $year = $time[1];
                    $order_query->whereMonth('updated_at', $month)->whereYear('updated_at', $year);
                    break;
                case 'year':
                    $order_query->whereYear('updated_at', $request->get('time'));
                    break;
                default:
                    // to day
                    $order_query->whereDate('created_at', Carbon::today());
                    $plus_item_query->whereDate('created_at', Carbon::today());
                    break;
            }
            $plus_item = $plus_item_query->selectRaw('SUM(IF(is_add = 1,amount,0)) as item_add')
                ->selectRaw('SUM(IF(is_add = 0,amount,0)) as item_minus')
                ->first();
            $order_query = $order_query
                ->selectRaw('count(*) as total_record')
                ->selectRaw('SUM(IF(status = 4 ,1,0)) as total_record_success')
                ->selectRaw('SUM(IF(status = 1 ,1,0)) as total_record_pending')
                ->selectRaw('SUM(IF(status = 5 ,1,0)) as total_record_error')
                ->selectRaw('SUM(IF(status = 6 ,1,0)) as total_record_lost_item')
                ->selectRaw('SUM(IF(status = 4 ,price,0)) as total_price')
                ->selectRaw('SUM(price_ctv) as total_price_ctv')
                ->selectRaw("COUNT(DISTINCT CASE WHEN status = '4' THEN author_id END) as total_user")
                ->selectRaw("(SUM(price) / COUNT(DISTINCT CASE WHEN status = '4' THEN author_id END)) as avg_revenue")
                ->first();

            $order_query['items_add'] = $plus_item->item_add;
            $order_query['items_minus'] = $plus_item->item_minus;

            cache(["report_service_auto" => $order_query], 600);
            return response()->json($order_query);
        }

        return response()->json($response_cate_data);
    }

    public function ReportAccountOld(Request $request)
    {
        $account_query = Item::query()
            ->where('items.module', 'acc')
            ->whereNotNull('items.sticky');

        $txns_query = Txns::query();
        $order_query = Order::query()->where('module', 'buy_acc');

        if (session('shop_id')) {
            $account_query->where('items.shop_id', session('shop_id'));

            $txns_query->whereHas('item', function ($q) {
                $q->where('module', 'acc')
                    ->where('status', 0)
                    ->whereNotNull('sticky')
                    ->where('shop_id', session('shop_id'));
            });

            $order_query->whereHas('nick', function ($q) {
                $q->where('module', 'acc')
                    ->where('status', 0)
                    ->whereNotNull('sticky')
                    ->where('shop_id', session('shop_id'));
            });
        } else {
            if (isset(auth()->user()->shop_access) && auth()->user()->shop_access !== "all") {
                $shop_id_shop_access = json_decode(auth()->user()->shop_access);
                $account_query->whereIn('items.shop_id', $shop_id_shop_access);

                $txns_query->whereHas('item', function ($q) use ($shop_id_shop_access) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereIn('items.shop_id', $shop_id_shop_access);
                });

                $order_query->whereHas('nick', function ($q) use ($shop_id_shop_access) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereIn('items.shop_id', $shop_id_shop_access);
                });

            }
        }

        switch ($request->get('type')) {
            case '7-day':
                $account_query->whereDate('items.published_at', '>=', Carbon::today()->subDays(7));

                $txns_query->whereHas('item', function ($q) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereDate('published_at', '>=', Carbon::today()->subDays(7));
                });

                $order_query->whereHas('nick', function ($q) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereDate('published_at', '>=', Carbon::today()->subDays(7));
                });
                break;
            case 'this-month':
                $account_query->whereMonth('items.published_at', Carbon::today()->month)
                    ->whereYear('items.published_at', Carbon::today()->year);

                $txns_query->whereHas('item', function ($q) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereMonth('published_at', Carbon::today()->month)
                        ->whereYear('published_at', Carbon::today()->year);

                });

                $order_query->whereHas('nick', function ($q) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereMonth('published_at', Carbon::today()->month)
                        ->whereYear('published_at', Carbon::today()->year);

                });
                break;
            case 'this-year':
                $account_query->whereYear('items.published_at', Carbon::today()->year);
                $txns_query->whereHas('item', function ($q) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereYear('published_at', Carbon::today()->year);

                });

                $order_query->whereHas('nick', function ($q) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereYear('published_at', Carbon::today()->year);

                });
                break;
            case 'day':
                $time = Carbon::createFromFormat('d/m/Y', $request->get('time'));
                $account_query->whereDate('items.published_at', $time);

                $txns_query->whereHas('item', function ($q) use ($time) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereDate('published_at', $time);
                });
                $order_query->whereHas('nick', function ($q) use ($time) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereDate('published_at', $time);
                });
                break;
            case 'week':
                $time = explode('-', preg_replace('/\s+/', '', $request->get('time')));
                $start = Carbon::createFromFormat('d/m/Y', $time[0]);
                $end = Carbon::createFromFormat('d/m/Y', $time[1]);
                $account_query->whereDate('items.published_at', '>=', $start)->whereDate('items.published_at', '<=', $end);

                $txns_query->whereHas('item', function ($q) use ($start, $end) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereDate('published_at', '>=', $start)
                        ->whereDate('published_at', '<=', $end);
                });

                $order_query->whereHas('nick', function ($q) use ($start, $end) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereDate('published_at', '>=', $start)
                        ->whereDate('published_at', '<=', $end);
                });
                break;
            case 'month':
                $time = explode('/', $request->get('time'));
                $month = $time[0];
                $year = $time[1];
                $account_query->whereMonth('items.published_at', $month)->whereYear('items.published_at', $year);

                $txns_query->whereHas('item', function ($q) use ($month, $year) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereMonth('published_at', $month)
                        ->whereYear('published_at', $year);
                });

                $order_query->whereHas('nick', function ($q) use ($month, $year) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereMonth('published_at', $month)
                        ->whereYear('published_at', $year);
                });
                break;
            case 'year':
                $account_query->whereYear('items.published_at', $request->get('time'));

                $txns_query->whereHas('item', function ($q) use ($request) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereYear('published_at', $request->get('time'));
                });

                $order_query->whereHas('nick', function ($q) use ($request) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereYear('published_at', $request->get('time'));
                });
                break;
            default:
                // to day
                $account_query->whereDate('items.published_at', Carbon::today());

                $txns_query->whereHas('item', function ($q) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereDate('published_at', Carbon::today());
                });
                $order_query->whereHas('nick', function ($q) {
                    $q->where('module', 'acc')
                        ->where('status', 0)
                        ->whereNotNull('sticky')
                        ->whereDate('published_at', Carbon::today());
                });
                break;
        }
        $txns_query = $txns_query->where('txnsable_type', 'App\Models\Item')
            ->where('trade_type', 'buy_acc')
            ->where('is_add', 1)
            ->where('is_refund', 0)
            ->selectRaw('COALESCE(SUM(amount),0) as price_ctv')
            ->first();

        $order_query = $order_query
            ->selectRaw('COUNT(DISTINCT author_id) as total_user')
            ->selectRaw('COALESCE(SUM(price),0) as price_sell')
            ->first();

        $total_capital_expend = $txns_query->price_ctv ?? '0';
        $total_user = $order_query->total_user ?? '0';
        $total_turnover_account = $order_query->price_sell ?? '0';
        $report_data = $account_query
            ->selectRaw('SUM(IF(items.status != 1 , 1 ,0)) as total_txns')
            ->selectRaw('SUM(IF(items.status = 0,1,0)) as total_success')
            ->selectRaw('SUM(IF(items.status = 3,1,0)) as total_check_info')
            ->selectRaw('SUM(IF(items.status = 4,1,0)) as total_wrong_password')
            ->selectRaw('SUM(IF(items.status = 2,1,0)) as total_pending')
            ->selectRaw('SUM(IF(items.status = 5,1,0)) as total_removed')
            ->selectRaw('SUM(IF(items.status = 6,1,0)) as total_check_error')
            ->selectRaw('SUM(IF(items.status = 7,1,0)) as total_pending_auto')
            ->selectRaw('SUM(IF(items.status = 8,1,0)) as total_get_info')
            ->selectRaw('SUM(IF(items.status = 9,1,0)) as total_filling_out_info')
            ->selectRaw($total_capital_expend . ' as total_capital_expend')
            ->selectRaw($total_user . ' as total_user')
            ->selectRaw($total_turnover_account . ' as total_turnover_account')
            ->first();

        return response()->json($report_data);
    }

    public function ReportAccount(Request $request)
    {
        $date_from = Carbon::now()->format('Y-m-d');
        $date_to = Carbon::now()->format('Y-m-d');
        $type_filter = $request->get('type');
        switch ($type_filter) {
            case '7-day':
                $date_from = Carbon::today()->subDays(7)->format('Y-m-d 00:00:00');
                $date_to = Carbon::now()->format('Y-m-d 23:59:59');
                break;
            case 'this-month':
                $date_from = Carbon::now()->startOfMonth()->format('Y-m-d 00:00:00');
                $date_to = Carbon::now()->format('Y-m-d 23:59:59');
                break;
            case 'this-year':
                $date_from = Carbon::now()->startOfYear()->format('Y-m-d 00:00:00');
                $date_to = Carbon::now()->format('Y-m-d 23:59:59');
                break;
            case 'week':
                $time = explode('-', preg_replace('/\s+/', '', $request->get('time')));
                $date_from = Carbon::createFromFormat('d/m/Y', $time[0])->format('Y-m-d 00:00:00');
                $date_to = Carbon::createFromFormat('d/m/Y', $time[1])->format('Y-m-d 23:59:59');
                break;
            case 'month':
                $time = explode('/', $request->get('time'));
                $month = $time[0];
                $year = $time[1];
                $date_from = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d 00:00:00');
                $date_to = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d 23:59:59');
                break;
            case 'year':
                $year = $request->get('time');
                $date_from = Carbon::createFromDate($year, 1, 1)->startOfYear()->format('Y-m-d 00:00:00');
                $date_to = Carbon::createFromDate($year, 1, 1)->endOfYear()->format('Y-m-d 23:59:59');
                break;
            default:
                // to day

                break;
        }

        $shops = null;
        if (session('shop_id')) {
            $shops = session('shop_id');
        } else {
            if (isset(auth()->user()->shop_access) && auth()->user()->shop_access !== "all") {
                $shops = json_decode(auth()->user()->shop_access);
            } else if (isset(auth()->user()->shop_access) && auth()->user()->shop_access === "all") {
                $shops = Shop::query()->pluck('id');
            }
        }

        if($type_filter == 'today' || $type_filter == 'day') {

            $date = $type_filter == 'today'
                ? Carbon::now()->format('Y-m-d')
                : Carbon::createFromFormat('d/m/Y', $request->get('time'))->format('Y-m-d');
            $data = NickHelper::timeline_hourly(
                [
                    'date'=>$date,
                    'shops' => $shops,
                ]
            );

            $result = [
                "price" => 0,
                "amount" => 0,//số tiền khách trả
                "amount_ctv" => 0,//số tiền ctv hưởng
                "count_total" => 0,
                "count_customer" => 0,
                "total_success" => 0,
                "total_wrong_password" => 0,
            ];
            foreach ($data as $day) {
                $result["price"] += $day["price"];
                $result["amount"] += $day["amount"];
                $result["amount_ctv"] += $day["amount_ctv"];
                $result["count_total"] += $day["count_total"];
                $result["count_customer"] += $day["count_customer"];
                $result["total_success"] += $day["count_total"];
                $result["total_wrong_password"] += $day["count_failed"];
            }
            return response()->json($result);
        } else {

            $data = NickHelper::timeline_dayly(
                [
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'shops' => $shops,
                ]
            );

            return response()->json($data);
        }

    }

    public function ReportTransfer2(Request $request)
    {
        $order_query = Order::query()->where('module', config('module.transfer.key'));

        if (session('shop_id')) {
            $order_query->where('shop_id', session('shop_id'));
        } else {
            if (isset(auth()->user()->shop_access) && auth()->user()->shop_access !== "all") {
                $shop_id_shop_access = json_decode(auth()->user()->shop_access);
                $order_query->whereIn('shop_id', $shop_id_shop_access);
            }
        }
        switch ($request->get('type')) {
            case '7-day':
                $order_query->whereDate('created_at', '>=', Carbon::today()->subDays(7));
                break;
            case 'this-month':
                $order_query->whereMonth('created_at', Carbon::today()->month)
                    ->whereYear('created_at', Carbon::today()->year);
                break;
            case 'this-year':
                $order_query->whereYear('created_at', Carbon::today()->year);
                break;
            case 'day':
                $time = Carbon::createFromFormat('d/m/Y', $request->get('time'));
                $order_query->whereDate('created_at', $time);
                break;
            case 'week':
                $time = explode('-', preg_replace('/\s+/', '', $request->get('time')));
                $start = Carbon::createFromFormat('d/m/Y', $time[0]);
                $end = Carbon::createFromFormat('d/m/Y', $time[1]);
                $order_query->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end);
                break;
            case 'month':
                $time = explode('/', $request->get('time'));
                $month = $time[0];
                $year = $time[1];
                $order_query->whereMonth('created_at', $month)->whereYear('created_at', $year);
                break;
            case 'year':
                $order_query->whereYear('created_at', $request->get('time'));
                break;
            default:
                // to day
                $order_query->whereDate('created_at', Carbon::today());
                break;
        }

        $order_query = $order_query->selectRaw('SUM(IF(status = 1,1,0)) as total_record_success')
            ->selectRaw('SUM(IF(status = 0,1,0)) as total_record_error')
            ->selectRaw('SUM(IF(status = 2,1,0)) as total_record_pending')
            ->selectRaw('SUM(IF(status = 1,price,0)) as total_price')
            ->selectRaw('SUM(IF(status = 1,real_received_price,0)) as total_real_received_price')
//                                    ->selectRaw('AVG(IF(status = 1 ,price,0)) as price_avg')
            ->selectRaw('COUNT(DISTINCT author_id) as count_user')
            ->first();

        return response()->json($order_query);
    }

    public function ReportMinigame(Request $request)
    {
        $order_query = Order::query()->where('module', 'minigame-log')->whereNull('acc_id');

        if (session('shop_id')) {
            $order_query->where('shop_id', session('shop_id'));
        } else {
            if (isset(auth()->user()->shop_access) && auth()->user()->shop_access !== "all") {
                $shop_id_shop_access = json_decode(auth()->user()->shop_access);
                $order_query->whereIn('shop_id', $shop_id_shop_access);
            }
        }

        $total_user = clone $order_query;

        $time = [];
        $data_price = [];
        $data_record = [];
        switch ($request->get('type')) {
            case 'day':
            case 'today':
                $time_query = $request->get('type') === 'day' ? Carbon::createFromFormat('d/m/Y', $request->get('time')) : Carbon::today();

                $minigame = $order_query->whereDate('created_at', $time_query)
                    ->selectRaw('COUNT(*) as total_record,COALESCE(SUM(price),0) as total_price,HOUR(created_at) as h')
                    ->groupBy('h')->get();

                $total_user = $total_user->whereDate('created_at', $time_query)
                    ->selectRaw('COUNT(DISTINCT author_id) as total_user')->first();

                $last_hour = $request->get('type') === 'day' ? 23 : Carbon::now()->hour;
                for ($i = 0; $i <= $last_hour; $i++) {
                    $time[$i] = $i . 'h';
                    $data_price[$i] = 0;
                    $data_record[$i] = 0;
                }
                foreach ($minigame as $item) {
                    $data_price[$item->h] = (int)$item->total_price;
                    $data_record[$item->h] = (int)$item->total_record;
                }
                break;
            case 'week':
            case '7-day':
                $time_query = $request->get('type') === 'week' ? explode('-', preg_replace('/\s+/', '', $request->get('time'))) : '';
                $start = $request->get('type') === '7-day' ? Carbon::today()->subDays(7) : Carbon::createFromFormat('d/m/Y', $time_query[0]);
                $end = $request->get('type') === '7-day' ? Carbon::today() : Carbon::createFromFormat('d/m/Y', $time_query[1]);

                $minigame = $order_query->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)
                    ->selectRaw('COUNT(*) as total_record,COALESCE(SUM(price),0) as total_price,DAY(created_at) as d')
                    ->groupBy('d')->get();
                $total_user = $total_user->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)
                    ->selectRaw('COUNT(DISTINCT author_id) as total_user')->first();

                for ($i = $start->day; $i <= $end->day; $i++) {
                    $time[$i] = $i;
                    $data_price[$i] = 0;
                    $data_record[$i] = 0;
                }
                foreach ($minigame as $item) {
                    $data_price[$item->d] = (int)$item->total_price;
                    $data_record[$item->d] = (int)$item->total_record;
                }
                break;
            case 'month':
            case 'this-month':
                $time_query = $request->get('type') === 'month' ? explode('/', $request->get('time')) : '';
                $month = $request->get('type') === 'month' ? $time_query[0] : Carbon::now()->month;
                $year = $request->get('type') === 'month' ? $time_query[1] : Carbon::now()->year;

                $minigame = $order_query->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->selectRaw('COUNT(*) as total_record,COALESCE(SUM(price),0) as total_price,DAY(created_at) as d')
                    ->groupBy('d')->get();

                $total_user = $total_user->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->selectRaw('COUNT(DISTINCT author_id) as total_user')->first();

                $last_day = $request->get('type') === 'this-month' ? Carbon::now()->day : Carbon::createFromDate($year, $month, 1)->endOfMonth()->day;
                for ($i = 0; $i <= $last_day; $i++) {
                    $time[$i] = $i;
                    $data_price[$i] = 0;
                    $data_record[$i] = 0;
                }
                foreach ($minigame as $item) {
                    $data_price[$item->d] = (int)$item->total_price;
                    $data_record[$item->d] = (int)$item->total_record;
                }
                break;
            case 'year':
            case 'this-year':
                $year = $request->get('type') === 'year' ? $request->get('time') : Carbon::now()->year;

                $minigame = $order_query->whereYear('created_at', $year)
                    ->selectRaw('COUNT(*) as total_record,COALESCE(SUM(price),0) as total_price,MONTH(created_at) as m')
                    ->groupBy('m')->get();
                $total_user = $total_user->whereYear('created_at', $year)
                    ->selectRaw('COUNT(DISTINCT author_id) as total_user')->first();

                $last_month = $request->get('type') === 'year' ? 12 : Carbon::now()->month;
                for ($i = 1; $i <= $last_month; $i++) {
                    $time[$i] = 'T.' . $i;
                    $data_price[$i] = 0;
                    $data_record[$i] = 0;
                }
                foreach ($minigame as $item) {
                    $data_price[$item->m] = (int)$item->total_price;
                    $data_record[$item->m] = (int)$item->total_record;
                }
                break;
        }
        return response()->json([
            'time' => $time,
            'data_price' => $data_price,
            'data_record' => $data_record,
            'total_user' => $total_user->total_user ?? 0,
        ]);
    }

    public function ReportWithdrawItem(Request $request)
    {
        $order_query = Order::query()
            ->where('module', 'withdraw-item')
            ->whereNull('acc_id');

        if (session('shop_id')) {
            $order_query->where('shop_id', session('shop_id'));
        } else {
            if (isset(auth()->user()->shop_access) && auth()->user()->shop_access !== "all") {
                $shop_id_shop_access = json_decode(auth()->user()->shop_access);
                $order_query->whereIn('shop_id', $shop_id_shop_access);
            }
        }
        switch ($request->get('type')) {
            case '7-day':
                $order_query->whereDate('created_at', '>=', Carbon::today()->subDays(7));
                break;
            case 'this-month':
                $order_query->whereMonth('created_at', Carbon::today()->month)
                    ->whereYear('created_at', Carbon::today()->year);
                break;
            case 'this-year':
                $order_query->whereYear('created_at', Carbon::today()->year);
                break;
            case 'day':
                $time = Carbon::createFromFormat('d/m/Y', $request->get('time'));
                $order_query->whereDate('created_at', $time);
                break;
            case 'week':
                $time = explode('-', preg_replace('/\s+/', '', $request->get('time')));
                $start = Carbon::createFromFormat('d/m/Y', $time[0]);
                $end = Carbon::createFromFormat('d/m/Y', $time[1]);
                $order_query->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end);
                break;
            case 'month':
                $time = explode('/', $request->get('time'));
                $month = $time[0];
                $year = $time[1];
                $order_query->whereMonth('created_at', $month)->whereYear('created_at', $year);
                break;
            case 'year':
                $order_query->whereYear('created_at', $request->get('time'));
                break;
            default:
                // to day
                $order_query->whereDate('created_at', Carbon::today());
                break;
        }
        $order_query = $order_query
            ->selectRaw('
            SUM(IF(status = 1,1,0)) as total_record_success,
            SUM(IF(status = 0,1,0)) as total_record_pending,
            SUM(IF(status = 2,1,0)) as total_record_payment_error,
            SUM(IF(status = 3,1,0)) as total_record_error,
                SUM(IF(status = 1 , price , 0)) as total_item
            ')
            ->first();
        return response()->json($order_query);
    }

    public function ReportUser(Request $request)
    {
        $data_social_query = SocialAccount::query();
        $data_user_query = User::query();

        if (session('shop_id')) {
            $data_user_query->where('shop_id', session('shop_id'));
            $data_social_query->where('shop_id', session('shop_id'));
        } else {
            if (isset(auth()->user()->shop_access) && auth()->user()->shop_access !== "all") {
                $shop_id_shop_access = json_decode(auth()->user()->shop_access);
                $data_user_query->whereIn('shop_id', $shop_id_shop_access);
                $data_social_query->whereIn('shop_id', $shop_id_shop_access);
            }
        }

        $users = [];
        $users_facebook = [];
        $users_google = [];
        $time = [];

        switch ($request->get('type')) {
            case 'day':
            case 'today':
                $time_query = $request->get('type') === 'day' ? Carbon::createFromFormat('d/m/Y', $request->get('time')) : Carbon::today();
                $data_social = $data_social_query->whereDate('created_at', $time_query)
                    ->selectRaw('COALESCE(SUM(IF(provider ="facebook",1,0)),0) as user_facebook,
                    COALESCE(SUM(IF(provider ="google",1,0)),0) as user_google,HOUR(created_at) as h')
                    ->groupBy('h')->get();
                $data_user = $data_user_query->whereNull('provider_id')->whereDate('created_at', $time_query)
                    ->selectRaw('count(*) as user,HOUR(created_at) as h')->groupBy('h')->get();
                $last_hour = $request->get('type') === 'day' ? 23 : Carbon::now()->hour;
                for ($i = 0; $i <= $last_hour; $i++) {
                    $time[$i] = $i . 'h';
                    $users[$i] = 0;
                    $users_facebook[$i] = 0;
                    $users_google[$i] = 0;
                }
                foreach ($data_social as $item) {
                    $users_facebook[$item->h] = (int)$item->user_facebook;
                    $users_google[$item->h] = (int)$item->user_google;
                }
                foreach ($data_user as $item) {
                    $users[$item->h] = (int)$item->user;
                }
                break;
            case 'week':
            case '7-day':
                $time_query = $request->get('type') === 'week' ? explode('-', preg_replace('/\s+/', '', $request->get('time'))) : '';
                $start = $request->get('type') === '7-day' ? Carbon::today()->subDays(7) : Carbon::createFromFormat('d/m/Y', $time_query[0]);
                $end = $request->get('type') === '7-day' ? Carbon::today() : Carbon::createFromFormat('d/m/Y', $time_query[1]);

                $data_social = $data_social_query->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end)
                    ->selectRaw('COALESCE(SUM(IF(provider ="facebook",1,0)),0) as user_facebook,
                    COALESCE(SUM(IF(provider ="google",1,0)),0) as user_google,DAY(created_at) as d')
                    ->groupBy('d')->get();

                $data_user = $data_user_query->whereNull('provider_id')->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end)->selectRaw('count(*) as user,DAY(created_at) as d')
                    ->groupBy('d')->get();

                for ($i = $start->day; $i <= $end->day; $i++) {
                    $time[$i] = $i;
                    $users[$i] = 0;
                    $users_facebook[$i] = 0;
                    $users_google[$i] = 0;
                }
                foreach ($data_social as $item) {
                    $users_facebook[$item->d] = (int)$item->user_facebook;
                    $users_google[$item->d] = (int)$item->user_google;
                }
                foreach ($data_user as $item) {
                    $users[$item->d] = (int)$item->user;
                }
                break;
            case 'month':
            case 'this-month':
                $time_query = $request->get('type') === 'month' ? explode('/', $request->get('time')) : '';
                $month = $request->get('type') === 'month' ? $time_query[0] : Carbon::now()->month;
                $year = $request->get('type') === 'month' ? $time_query[1] : Carbon::now()->year;

                $data_social = $data_social_query->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->selectRaw('COALESCE(SUM(IF(provider ="facebook",1,0)),0) as user_facebook,
                    COALESCE(SUM(IF(provider ="google",1,0)),0) as user_google,DAY(created_at) as d')
                    ->groupBy('d')->get();

                $data_user = $data_user_query->whereNull('provider_id')->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->selectRaw('count(*) as user,DAY(created_at) as d')->groupBy('d')->get();

                for ($i = 0; $i <= Carbon::now()->day; $i++) {
                    $time[$i] = $i;
                    $users[$i] = 0;
                    $users_facebook[$i] = 0;
                    $users_google[$i] = 0;
                }
                foreach ($data_social as $item) {
                    $users_facebook[$item->d] = (int)$item->user_facebook;
                    $users_google[$item->d] = (int)$item->user_google;
                }
                foreach ($data_user as $item) {
                    $users[$item->d] = (int)$item->user;
                }
                break;
            case 'year':
            case 'this-year':
                $year = $request->get('type') === 'year' ? $request->get('time') : Carbon::now()->year;

                $data_social = $data_social_query->whereYear('created_at', $year)
                    ->selectRaw('COALESCE(SUM(IF(provider ="facebook",1,0)),0) as user_facebook,
                    COALESCE(SUM(IF(provider ="google",1,0)),0) as user_google,MONTH(created_at) as m')
                    ->groupBy('m')->get();

                $data_user = $data_user_query->whereNull('provider_id')->whereYear('created_at', $year)
                    ->selectRaw('count(*) as user,MONTH(created_at) as m')->groupBy('m')->get();


                $last_month = $request->get('type') === 'year' ? 12 : Carbon::now()->month;

                for ($i = 1; $i <= $last_month; $i++) {
                    $time[$i] = 'Tháng: ' . $i;
                    $users[$i] = 0;
                    $users_facebook[$i] = 0;
                    $users_google[$i] = 0;
                }
                foreach ($data_social as $item) {
                    $users_facebook[$item->m] = (int)$item->user_facebook;
                    $users_google[$item->m] = (int)$item->user_google;
                }
                foreach ($data_user as $item) {
                    $users[$item->m] = (int)$item->user;
                }
                break;
        }
        $growth = [
            'users' => $users,
            'user_facebook' => $users_facebook,
            'user_google' => $users_google,
            'growth_month' => $time,
        ];
        return response()->json([
            "success" => true,
            "data" => $growth,
        ], 200);
    }

    public function ReportTransactionUser(Request $request)
    {
        $txns_query = Txns::query()->whereNotNull('user_id');
        $data_user = User::query();

        if (session('shop_id')) {
            $txns_query->where('shop_id', session('shop_id'));
            $data_user->where('shop_id', session('shop_id'));
        } else {
            if (isset(auth()->user()->shop_access) && auth()->user()->shop_access !== "all") {
                $shop_id_shop_access = json_decode(auth()->user()->shop_access);
                $txns_query->whereIn('shop_id', $shop_id_shop_access);
                $data_user->whereIn('shop_id', $shop_id_shop_access);
            }
        }

        switch ($request->get('type')) {
            case 'today':
            case 'day':
                $time_query = $request->get('type') === 'day' ? Carbon::createFromFormat('d/m/Y', $request->get('time')) : Carbon::today();
                $data = $txns_query->selectRaw('COUNT(DISTINCT user_id) as users')
                    ->whereDate('created_at', $time_query)
                    ->first();

                $data_user = $data_user->selectRaw('COUNT(*) as users')
                    ->whereDate('created_at', $time_query)
                    ->first();
                break;
            case 'week':
            case '7-day':
                $time_query = $request->get('type') === 'week' ? explode('-', preg_replace('/\s+/', '', $request->get('time'))) : '';
                $start = $request->get('type') === '7-day' ? Carbon::today()->subDays(7) : Carbon::createFromFormat('d/m/Y', $time_query[0]);
                $end = $request->get('type') === '7-day' ? Carbon::today() : Carbon::createFromFormat('d/m/Y', $time_query[1]);

                $data = $txns_query->selectRaw('COUNT(DISTINCT user_id) as users')
                    ->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)
                    ->first();

                $data_user = $data_user->selectRaw('COUNT(*) as users')
                    ->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)
                    ->first();

                break;
            case 'month':
            case 'this-month':
                $time_query = $request->get('type') === 'month' ? explode('/', $request->get('time')) : '';
                $month = $request->get('type') === 'month' ? $time_query[0] : Carbon::now()->month;
                $year = $request->get('type') === 'month' ? $time_query[1] : Carbon::now()->year;

                $data = $txns_query->selectRaw('COUNT(DISTINCT user_id) as users')
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->first();

                $data_user = $data_user->selectRaw('COUNT(*) as users')
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->first();
                break;
            case 'year':
            case 'this-year':
                $year = $request->get('type') === 'year' ? $request->get('time') : Carbon::now()->year;
                $data = $txns_query->selectRaw('COUNT(DISTINCT user_id) as users')
                    ->whereYear('created_at', $year)
                    ->first();

                $data_user = $data_user->selectRaw('COUNT(*) as users')
                    ->whereYear('created_at', $year)
                    ->first();
                break;
        }

        return response()->json([
            'users_have_txns' => $data->users,
            'users_havent_txns' => $data_user->users - $data->users,
            'total_user' => $data_user->users
        ]);
    }

    public function ReportSurplusUser(Request $request)
    {
        $user_query = User::query();

        if (session('shop_id')) {
            $user_query->where('shop_id', session('shop_id'));
        } else {
            if (isset(auth()->user()->shop_access) && auth()->user()->shop_access !== "all") {
                $shop_id_shop_access = json_decode(auth()->user()->shop_access);
                $user_query->whereIn('shop_id', $shop_id_shop_access);
            }
        }

        $user_query = $user_query->selectRaw('SUM(IF(account_type = 2,balance,0)) as total_price_user')
            ->selectRaw('SUM(IF(account_type = 1,balance,0)) as total_price_qtv')
            ->selectRaw('SUM(IF(account_type = 3,balance,0)) as total_price_ctv')
            ->first();
        return response()->json($user_query);
    }

    public function ReportGeneralTurnover(Request $request)
    {
        $response_cate_data = cache("report_general_turnover");

        if (empty($response_cate_data)){
            $service_query = Order::query()->where('module', 'service-purchase')->where('gate_id', 0);
            $service_auto_query = Order::query()->where('module', 'service-purchase')->where('gate_id', 1);

            if (session('shop_id')) {

                $service_query->where('shop_id', session('shop_id'));
                $service_auto_query->where('shop_id', session('shop_id'));
                $nick_shops = session('shop_id');
            }
            else {
                if (isset(auth()->user()->shop_access) && auth()->user()->shop_access !== "all") {
                    $shop_id_shop_access = json_decode(auth()->user()->shop_access);
                    $service_query->whereIn('shop_id', $shop_id_shop_access);
                    $service_auto_query->whereIn('shop_id', $shop_id_shop_access);
                }
            }
            $time = [];

            $data_service = [];
            $data_service_auto = [];
            switch ($request->get('type')) {
                case 'today':
                case 'day':
                    $time_query = $request->get('type') === 'day' ? Carbon::createFromFormat('d/m/Y', $request->get('time')) : Carbon::today();

                    $service_turnover = $service_query->where('status', 4)->whereDate('updated_at', $time_query)
                        ->selectRaw('COALESCE(SUM(price),0) as turnover,HOUR(updated_at) as h')
                        ->groupBy('h')->get();

                    $service_auto_turnover = $service_auto_query->where('status', 4)
                        ->whereDate('updated_at', $time_query)
                        ->selectRaw('COALESCE(SUM(price),0) as turnover,HOUR(updated_at) as h')
                        ->groupBy('h')->get();

                    for ($i = 0; $i <= 23; $i++) {
                        $time[$i] = $i . 'h';
                        $data_store_card[$i] = 0;
                        $data_minigame[$i] = 0;
                        $data_service[$i] = 0;
                        $data_service_auto[$i] = 0;
                        $data_account[$i] = 0;
                    }

                    foreach ($service_turnover as $item) {
                        $data_service[$item->h] = (int)$item->turnover;
                    }
                    foreach ($service_auto_turnover as $item) {
                        $data_service_auto[$item->h] = (int)$item->turnover;
                    }
                    //nic
                    break;
                case 'week':
                case '7-day':
                    $time_query = $request->get('type') === 'week' ? explode('-', preg_replace('/\s+/', '', $request->get('time'))) : '';
                    $start = $request->get('type') === '7-day' ? Carbon::today()->subDays(7) : Carbon::createFromFormat('d/m/Y', $time_query[0]);
                    $end = $request->get('type') === '7-day' ? Carbon::today() : Carbon::createFromFormat('d/m/Y', $time_query[1]);

                    $service_turnover = $service_query->where('status', 4)->whereDate('updated_at', '>=', $start)
                        ->whereDate('updated_at', '<=', $end)->selectRaw('COALESCE(SUM(price),0) as turnover,DAY(updated_at) as d')
                        ->groupBy('d')->get();

                    $service_auto_turnover = $service_auto_query->where('status', 4)->whereDate('updated_at', '>=', $start)
                        ->whereDate('updated_at', '<=', $end)->selectRaw('COALESCE(SUM(price),0) as turnover,DAY(updated_at) as d')
                        ->groupBy('d')->get();

                    $period = CarbonPeriod::create($start,$end);
                    foreach ($period as $date) {
                        $day = 'ngày '.$date->day;
                        $time[$day] = $day;
                        $data_store_card[$day] = 0;
                        $data_minigame[$day] = 0;
                        $data_service[$day] = 0;
                        $data_service_auto[$day] = 0;
                        $data_account[$day] = 0;
                    }

                    foreach ($service_turnover as $item) {
                        $data_service['ngày '.$item->d] = (int)$item->turnover;
                    }
                    foreach ($service_auto_turnover as $item) {
                        $data_service_auto['ngày '.$item->d] = (int)$item->turnover;
                    }

                    break;
                case 'month':
                case 'this-month':
                    $time_query = $request->get('type') === 'month' ? explode('/', $request->get('time')) : '';
                    $month = $request->get('type') === 'month' ? $time_query[0] : Carbon::now()->month;
                    $year = $request->get('type') === 'month' ? $time_query[1] : Carbon::now()->year;

                    $service_turnover = $service_query->where('status', 4)->whereMonth('updated_at', $month)
                        ->whereYear('updated_at', $year)
                        ->selectRaw('COALESCE(SUM(price),0) as turnover,DAY(updated_at) as d')
                        ->groupBy('d')->get();

                    $service_auto_turnover = $service_auto_query->where('status', 4)->whereMonth('updated_at', $month)
                        ->whereYear('updated_at', $year)
                        ->selectRaw('COALESCE(SUM(price),0) as turnover,DAY(updated_at) as d')
                        ->groupBy('d')->get();

                    $last_day = $request->get('type') === 'this-month' ? Carbon::now()->day : Carbon::createFromDate($year, $month, 1)->endOfMonth()->day;
                    for ($i = 1; $i <= $last_day; $i++) {
                        $time[$i] = $i;
                        $data_store_card[$i] = 0;
                        $data_minigame[$i] = 0;
                        $data_service[$i] = 0;
                        $data_service_auto[$i] = 0;
                        $data_account[$i] = 0;
                    }

                    foreach ($service_turnover as $item) {
                        $data_service[$item->d] = (int)$item->turnover;
                    }
                    foreach ($service_auto_turnover as $item) {
                        $data_service_auto[$item->d] = (int)$item->turnover;
                    }

                    break;
                case 'this-year':
                case 'year':
                    $year = $request->get('type') === 'year' ? $request->get('time') : Carbon::now()->year;

                    $service_turnover = $service_query->where('status', 4)->whereYear('updated_at', $year)
                        ->selectRaw('COALESCE(SUM(price),0) as turnover,MONTH(updated_at) as m')
                        ->groupBy('m')->get();

                    $service_auto_turnover = $service_auto_query->where('status', 4)->whereYear('updated_at', $year)
                        ->selectRaw('COALESCE(SUM(price),0) as turnover,MONTH(updated_at) as m')
                        ->groupBy('m')->get();

                    $last_month = $request->get('type') === 'year' ? 12 : Carbon::now()->month;
                    for ($i = 1; $i <= $last_month; $i++) {
                        $time[$i] = 'Tháng ' . $i;
                        $data_store_card[$i] = 0;
                        $data_minigame[$i] = 0;
                        $data_service[$i] = 0;
                        $data_service_auto[$i] = 0;
                        $data_account[$i] = 0;
                    }

                    foreach ($service_turnover as $item) {
                        $data_service[$item->m] = (int)$item->turnover;
                    }
                    foreach ($service_auto_turnover as $item) {
                        $data_service_auto[$item->m] = (int)$item->turnover;
                    }

                    break;
            }

            $data_return = [
                'service' => $data_service,
                'service_auto' => $data_service_auto,
                'time' => $time,
            ];
            cache(["report_general_turnover" => $data_return], 600);
            return response()->json($data_return);
        }
        return response()->json($response_cate_data);
    }

    public function ReportGeneralDensityTurnover(Request $request)
    {
        $response_cate_data = cache("report_general_density_turnover");

        if (empty($response_cate_data)){
            $service_query = Order::query()->where('module', 'service-purchase')->where('gate_id', 0);
            $service_auto_query = Order::query()->where('module', 'service-purchase')->where('gate_id', 1);

            if (session('shop_id')) {

                $service_query->where('shop_id', session('shop_id'));
                $service_auto_query->where('shop_id', session('shop_id'));

            } else {
                if (isset(auth()->user()->shop_access) && auth()->user()->shop_access !== "all") {
                    $shop_id_shop_access = json_decode(auth()->user()->shop_access);
                    $service_query->whereIn('shop_id', $shop_id_shop_access);
                    $service_auto_query->whereIn('shop_id', $shop_id_shop_access);

                }
            }

            $date_from = Carbon::now()->format('Y-m-d');
            $date_to = Carbon::now()->format('Y-m-d');
            switch ($request->get('type')) {
                case '7-day':
                    $service_query->whereDate('updated_at', '>=', Carbon::today()->subDays(7));
                    $service_auto_query->whereDate('updated_at', '>=', Carbon::today()->subDays(7));

                    $date_from = Carbon::today()->subDays(7)->format('Y-m-d 00:00:00');
                    $date_to = Carbon::now()->format('Y-m-d 23:59:59');
                    break;
                case 'this-month':
                    $service_query->whereMonth('updated_at', Carbon::today()->month)
                        ->whereYear('updated_at', Carbon::today()->year);
                    $service_auto_query->whereMonth('updated_at', Carbon::today()->month)
                        ->whereYear('updated_at', Carbon::today()->year);

                    $date_from = Carbon::now()->startOfMonth()->format('Y-m-d 00:00:00');
                    $date_to = Carbon::now()->format('Y-m-d 23:59:59');
                    break;
                case 'this-year':
                    $service_query->whereYear('updated_at', Carbon::today()->year);
                    $service_auto_query->whereYear('updated_at', Carbon::today()->year);

                    $date_from = Carbon::now()->startOfYear()->format('Y-m-d 00:00:00');
                    $date_to = Carbon::now()->format('Y-m-d 23:59:59');
                    break;
                case 'day':
                    $time = Carbon::createFromFormat('d/m/Y', $request->get('time'));
                    $service_query->whereDate('updated_at', $time);
                    $service_auto_query->whereDate('updated_at', $time);

                    $date_from = $time->format('Y-m-d');
                    $date_to = $time->format('Y-m-d');
                    break;
                case 'week':
                    $time = explode('-', preg_replace('/\s+/', '', $request->get('time')));
                    $start = Carbon::createFromFormat('d/m/Y', $time[0]);
                    $end = Carbon::createFromFormat('d/m/Y', $time[1]);
                    $service_query->whereDate('updated_at', '>=', $start)->whereDate('updated_at', '<=', $end);
                    $service_auto_query->whereDate('updated_at', '>=', $start)->whereDate('updated_at', '<=', $end);

                    $date_from = Carbon::createFromFormat('d/m/Y', $time[0])->format('Y-m-d 00:00:00');
                    $date_to = Carbon::createFromFormat('d/m/Y', $time[1])->format('Y-m-d 23:59:59');
                    break;
                case 'month':
                    $time = explode('/', $request->get('time'));
                    $month = $time[0];
                    $year = $time[1];

                    $service_query->whereMonth('updated_at', $month)->whereYear('updated_at', $year);
                    $service_auto_query->whereMonth('updated_at', $month)->whereYear('updated_at', $year);

                    $date_from = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d 00:00:00');
                    $date_to = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d 23:59:59');
                    break;
                case 'year':

                    $service_query->whereYear('updated_at', $request->get('time'));
                    $service_auto_query->whereYear('updated_at', $request->get('time'));


                    $date_from = Carbon::createFromDate($request->get('time'), 1, 1)->startOfYear()->format('Y-m-d 00:00:00');
                    $date_to = Carbon::createFromDate($request->get('time'), 1, 1)->endOfYear()->format('Y-m-d 23:59:59');
                    break;
                default:
                    // to day

                    $service_query->whereDate('updated_at', Carbon::today());
                    $service_auto_query->whereDate('updated_at', Carbon::today());
                    break;
            }

            $data_service = $service_query->where('status', 4)->selectRaw('SUM(price) as turnover')->first()->turnover;
            $data_service_auto = $service_auto_query->where('status', 4)->selectRaw('SUM(price) as turnover')->first()->turnover;

            $data = [
                "data_service" =>$data_service,
                "data_service_auto" =>$data_service_auto,
            ];

            cache(["report_general_density_turnover" => $data], 600);

            return response()->json([
                'service' => $data_service,
                'service_auto' => $data_service_auto,
            ]);
        }

        return response()->json([
            'service' => $response_cate_data['data_service'],
            'service_auto' => $response_cate_data['data_service_auto'],
        ]);
    }

    public function ReportTopMoney(Request $request)
    {
        $user_query = User::query()
            ->join('shop', 'shop.id', 'users.shop_id')
            ->select('shop.id as shop_id', 'shop.domain', 'users.id', 'users.username', 'users.balance', 'users.account_type');

        if (session('shop_id')) {
            $user_query->where('shop_id', session('shop_id'));
        } else {
            if (isset(auth()->user()->shop_access) && auth()->user()->shop_access !== "all") {
                $shop_id_shop_access = json_decode(auth()->user()->shop_access);
                $user_query->whereIn('shop_id', $shop_id_shop_access);
            }
        }

        $ctv_query = clone $user_query;
        $qtv_query = clone $ctv_query;
        $limit_user = 3;

        $user_qtv = $qtv_query->where('account_type', 1)->orderBy('balance', 'DESC')->limit($limit_user)->get();
        $top_user = $user_query->where('account_type', 2)->orderBy('balance', 'DESC')->limit($limit_user)->get();
        $user_ctv = $ctv_query->where('account_type', 3)->orderBy('balance', 'DESC')->limit($limit_user)->get();

        return response()->json([
            'top_user' => $top_user ?? [],
            'top_qtv' => $user_qtv ?? [],
            'top_ctv' => $user_ctv ?? [],
        ]);
    }

    public function ReportTxnsBiggest(Request $request)
    {
        $txns_query = Txns::query()
            ->join('shop', 'shop.id', 'txns.shop_id')
            ->join('users', 'users.id', 'txns.user_id')
            ->select('users.username', 'txns.amount', 'txns.description', 'shop.title as shop_title');

        if (session('shop_id')) {
            $txns_query->where('shop_id', session('shop_id'));
        } else {
            if (isset(auth()->user()->shop_access) && auth()->user()->shop_access !== "all") {
                $shop_id_shop_access = json_decode(auth()->user()->shop_access);
                $txns_query->whereIn('shop_id', $shop_id_shop_access);
            }
        }
        switch ($request->get('type')) {
            case 'day':
            case 'today':
                $time_query = $request->get('type') === 'day' ? Carbon::createFromFormat('d/m/Y', $request->get('time')) : Carbon::today();
                $txns_query->whereDate('txns.created_at', $time_query);
                break;
            case 'week':
            case '7-day':
                $time_query = $request->get('type') === 'week' ? explode('-', preg_replace('/\s+/', '', $request->get('time'))) : '';
                $start = $request->get('type') === '7-day' ? Carbon::today()->subDays(7) : Carbon::createFromFormat('d/m/Y', $time_query[0]);
                $end = $request->get('type') === '7-day' ? Carbon::today() : Carbon::createFromFormat('d/m/Y', $time_query[1]);
                $txns_query->whereDate('txns.created_at', '>=', $start)->whereDate('txns.created_at', '<=', $end);
                break;
            case 'month':
            case 'this-month':
                $time_query = $request->get('type') === 'month' ? explode('/', $request->get('time')) : '';
                $month = $request->get('type') === 'month' ? $time_query[0] : Carbon::now()->month;
                $year = $request->get('type') === 'month' ? $time_query[1] : Carbon::now()->year;

                $txns_query->whereMonth('txns.created_at', $month)
                    ->whereYear('txns.created_at', $year);
                break;
            case 'year':
            case 'this-year':
                $year = $request->get('type') === 'year' ? $request->get('time') : Carbon::now()->year;
                $txns_query->whereYear('txns.created_at', $year);
                break;
        }

        $txns_user_add = clone $txns_query;
        $txns_user_minus = clone $txns_query;

        $txns_qtv_add = clone $txns_query;
        $txns_qtv_minus = clone $txns_query;

        $txns_ctv_add = clone $txns_query;
        $txns_ctv_minus = clone $txns_query;

        if (!session()->has('shop_id')) {
            $txns_qtv_add = $txns_qtv_add->where('is_add', 1)
                ->where('txns.amount', $txns_qtv_add->where('users.account_type', 1)->max('txns.amount'))->first();
            $txns_qtv_minus = $txns_qtv_minus->where('is_add', 0)
                ->where('txns.amount', $txns_qtv_minus->where('users.account_type', 1)->max('txns.amount'))->first();

            $txns_ctv_add = $txns_ctv_add->where('is_add', 1)
                ->where('txns.amount', $txns_ctv_add->where('users.account_type', 3)->max('txns.amount'))->first();
            $txns_ctv_minus = $txns_ctv_minus->where('is_add', 0)
                ->where('txns.amount', $txns_ctv_minus->where('users.account_type', 3)->max('txns.amount'))->first();

        }
        $txns_user_add = $txns_user_add->where('is_add', 1)->whereNotNull('txns.order_id')
            ->where('txns.amount', $txns_user_add->where('users.account_type', 2)->max('txns.amount'))->first();
        $txns_user_minus = $txns_user_minus->where('is_add', 0)->whereNotNull('txns.order_id')
            ->where('txns.amount', $txns_user_minus->where('users.account_type', 2)->max('txns.amount'))->first();

        $data_return = [
            'user' => [
                'add' => $txns_user_add,
                'minus' => $txns_user_minus,
            ],
        ];
        if (!session()->has('shop_id')) {
            $data_return['qtv'] = [
                'add' => $txns_qtv_add,
                'minus' => $txns_qtv_minus,
            ];
            $data_return['ctv'] = [
                'add' => $txns_ctv_add,
                'minus' => $txns_ctv_minus,
            ];
        }
        return response()->json($data_return);
    }

    public function ReportPointOfSale(Request $request)
    {
        $shop_query = Shop::query();
        $data_return = $shop_query->selectRaw('SUM(IF(status = 1,1,0)) as shop_work')
            ->selectRaw('SUM(IF(status = 0,1,0)) as shop_shut_down');
        return response()->json($data_return->first());
    }

    public function GrowthTranfer(Request $request)
    {
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;
        $day = Carbon::now()->day;
        if ($request->filled('year')) {
            $year = $request->get('year');
            $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }
        if ($request->filled('month')) {
            $month = $request->get('month');
            $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }
        for ($i = 1; $i <= $day; $i++) {
            $growth_fail[$i] = 0;
            $growth_susscess[$i] = 0;
            $growth_pendding[$i] = 0;
            $growth_day[$i] = "Ngày " . $i;
        }
        $data_fail = Order::select(DB::raw('count(*) as item, day(created_at) as d'))->where('module', config('module.transfer.key'))->whereMonth('created_at', '=', $month)->whereYear('created_at', '=', $year)->where('status', 0)->groupBy('d')->get();
        foreach ($data_fail as $item) {
            $growth_fail[$item->d] = $item->item;
        }
        $data_susscess = Order::select(DB::raw('count(*) as item, day(created_at) as d'))->where('module', config('module.transfer.key'))->whereMonth('created_at', '=', $month)->whereYear('created_at', '=', $year)->where('status', 1)->groupBy('d')->get();
        foreach ($data_susscess as $item) {
            $growth_susscess[$item->d] = $item->item;
        }
        $data_pendding = Order::select(DB::raw('count(*) as item, day(created_at) as d'))->where('module', config('module.transfer.key'))->whereMonth('created_at', '=', $month)->whereYear('created_at', '=', $year)
            ->where('status', 2)
            ->groupBy('d')->get();
        foreach ($data_pendding as $item) {
            $growth_pendding[$item->d] = $item->item;
        }
        $data = [
            'growth_fail' => $growth_fail,
            'growth_susscess' => $growth_susscess,
            'growth_pendding' => $growth_pendding,
            'growth_day' => $growth_day,
        ];
        return response()->json([
            "success" => true,
            "data" => $data,
        ], 200);
    }

    public function ReportTranfer(Request $request)
    {
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;
        if ($request->filled('year')) {
            $year = $request->get('year');
        }
        if ($request->filled('month')) {
            $month = $request->get('month');
        }
        $data = Order::with(array('shop' => function ($query) {
            $query->select('id', 'title', 'domain', 'ratio_atm');
        }))
            ->where('module', config('module.transfer.key'))
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year)
            ->groupBy('shop_id')
            ->selectRaw('id')
            ->selectRaw('shop_id')
            ->selectRaw('count(*) as total_record')
            ->selectRaw('SUM(IF(status = 1, price, 0)) as total_amount')
            ->selectRaw('SUM(IF(status = 1, real_received_price, 0)) as total_real_received_price');
        return \datatables()->eloquent($data)
            ->editColumn('total_amount', function ($row) {
                return number_format($row->total_amount);
            })
            ->editColumn('total_real_received_price', function ($row) {
                return number_format($row->total_real_received_price);
            })
            ->addColumn('shop_title', function ($row) {
                $result = '';
                if (isset($row->shop->domain)) {
                    $result .= $row->shop->domain;
                } else {
                    $result .= "null";
                }
                return $result;
            })
            ->addColumn('ratio_atm', function ($row) {
                $result = '';
                if (isset($row->shop->ratio_atm)) {
                    $result .= $row->shop->ratio_atm . " %";
                } else {
                    $result .= "null";
                }
                return $result;
            })
            ->toJson();
    }
}
