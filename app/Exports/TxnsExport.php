<?php
namespace App\Exports;

use App\Models\Order;
use App\Models\ServiceAccess;
use App\Models\Txns;
use Auth;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TxnsExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        ini_set('max_execution_time', 1200); //2 minutes
        ini_set('memory_limit', '-1');

        $request = $this->request;

        $datatable = Txns::with('user','shop');
        if ($request->filled('id')) {
            $datatable->where('id', $request->get('id'));
        }
        if ($request->filled('username')) {
            $datatable->whereHas('user', function ($query) use ($request) {
                $query->where('username',$request->get('username'));
            });
        }
        if($request->filled('shop_id')){
            $datatable->whereHas('user', function ($query) use ($request) {
                $query->where('shop_id',$request->get('shop_id'));
            });
        }
        if ($request->filled('email')) {
            $datatable->whereHas('user', function ($query) use ($request) {
                $query->where('email', $request->get('email'));
            });
        }
        if ($request->filled('account_type')) {
            $datatable->whereHas('user', function ($query) use ($request) {
                $query->where('account_type', $request->get('account_type'));
            });
            $datatable->with('user', function ($query) use ($request) {
                $query->where('account_type', $request->get('account_type'));
            });
        }
        if ($request->filled('trade_type')) {
            $datatable->where('trade_type', $request->get('trade_type'));
        }
        if ($request->filled('is_add')) {
            $datatable->where('is_add', $request->get('is_add'));
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
        // trường hợp là admin hoặc có quyền xem tất cả danh sách của all shop
        if(Auth::user()->hasAllRoles('admin') || Auth::user()->can('txns-report-list')){

        }
        // trường hợp có quyền xem các điểm bán được gắn tag
        elseif(Auth::user()->can('txns-report-in-shop-list')){
            // trường hợp đang search
            if($request->filled('shop_id')){
                // lấy id danh sách các shop được truy cập
                $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                // nếu không có hoặc là all thì ko trả kết quả vì conflict với quyền
                if(empty($shop_id_shop_access) || $shop_id_shop_access == 'all'){
                    $datatable->whereNull('id');
                }
                // loại bỏ những shop không đc phép truy cập và search dữ liệu
                else{
                    $shop_id_shop_access_search = array_intersect($shop_id_shop_access,$request->get('shop_id'));
                    $datatable->whereIn('shop_id', $shop_id_shop_access_search);
                }
            }
            else{
                // trường hợp có lựa chọn shop trên thanh select
                if(session('shop_id')){
                    $datatable->where('shop_id',session('shop_id'));
                }
                // trường hợp không lựa chọn shop trên thanh select
                else{
                    $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                    // nếu không có hoặc là all thì ko trả kết quả vì conflict với quyền
                    if(empty($shop_id_shop_access) || $shop_id_shop_access == 'all'){
                        $datatable->whereNull('id');
                    }
                    // lấy danh sách các shop được phép truy cập và trả thông tin
                    else{
                        $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                        $datatable->whereIn('shop_id',$shop_id_shop_access);

                    }
                }
            }
        }
        // trường hợp có quyền xem biến động số dư của riêng mình
        elseif(Auth::user()->can('txns-person-report-list')){
            $datatable->where('user_id', Auth::guard()->user()->id);
        }
        // nếu không có thì không được xem gì cả
        else{
            $datatable->whereNull('id');
        }

        $datatable = $datatable->orderBy('id','desc')->get();

        $datatable = $datatable->map(function ($item) {

            //Ngày tạo
            $created_at = date('d/m/Y H:i:s', strtotime($item->created_at));
            //điểm bán.
            $domain = 'N/A';
            if (isset($item->shop) && Auth::guard()->user()->can('txns-report-list')){
                $domain = $item->shop->domain??'';
            }
            //Loại tài khoản.
            $account_type = 'N/A';
            if (isset($item->user) && isset($item->user->account_type)){
                $account_type = config('module.user-qtv.account_type.'.$item->user->account_type);
            }

            $trade_type = 'N/A';
            if (isset($item->trade_type)){
                if (!empty(config('module.txns.trade_type.'.$item->trade_type))){
                    $trade_type = config('module.txns.trade_type.'.$item->trade_type);
                }
            }
            $amount = 0;
            if (isset($item->amount)){

                if ($item->is_add == 1){
                    $amount = '+ '.number_format($item->amount);
                }else{
                    $amount = '- '.number_format($item->amount);
                }

            }

            $last_balance = 0;
            if (isset($item->last_balance)){
                $last_balance = number_format($item->last_balance);
            }

            $username = 'N/A';
            if (isset($item->user) && isset($item->user->username)){
                $username = $item->user->username??'';
            }

            $status = 'N/A';
            if (isset($item->status)){
                $status = config('module.txns.status.'.$item->status);
            }

            // Đoạn chuỗi cố định cần kiểm tra
            $fixedString = "Hệ thông tự động hoàn tất đơn hàng sau '.24.' giờ #";
            $order_id = '';
            if (isset($item->description)){
                $description = $item->description;
                // Kiểm tra xem chuỗi mô tả có chứa đoạn chuỗi cố định không
                if (strpos($description, $fixedString) !== false) {
                    // Sử dụng biểu thức chính quy để tìm và lấy số sau dấu #
                    preg_match('/#(\d+)/', $description, $matches);

                    if (isset($matches[1])) {
                        $order_id = $matches[1];
                    }
                }
            }

            return [
                'id' => $item->id,
                'order_id' => $item->order_id??$order_id,
                'created_at' => $created_at,
                'domain' => $domain,
                'username' => $username,
                'account_type' => $account_type,
                'trade_type' => $trade_type,
                'description' => $item->description??'',
                'amount' => $amount,
                'last_balance' => $last_balance,
                'status' => $status,
            ];
        })->toArray();

        return collect($datatable);
    }

    public function headings(): array
    {
        return [
            "ID",
            "ID Đơn hàng",
            "Thời gian",
            "Shop",
            "Tài khoản",
            "Loại tài khoản",
            "Loại giao dịch",
            "Giao dịch",
            "Số tiền",
            "Số dư cuối",
            "Trạng thái",
        ];
    }
}
