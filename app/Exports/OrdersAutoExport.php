<?php
namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersAutoExport implements FromCollection, WithHeadings
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

        $datatable =  Order::with(['shop','item_ref','author','processor','roblox_order' => function($q){
            $q->with('bot');
        },'order_pengiriman'])
            ->where('module',config('module.service-purchase.key'))
            ->where('gate_id',1);

        if ($request->filled('group_id')) {

            $datatable->whereHas('item_ref', function ($query) use ($request) {
                $query->whereIn('id',$request->get('group_id'));
            });
        }

        if ($request->filled('payment_type')) {
            $payment_type = $request->get('payment_type');
            if ($payment_type == 1){
                $datatable->where(function($q){
                    $q->orWhereNull('payment_type');
                    $q->orWhere('payment_type', 1);
                });
            }elseif ($payment_type == 2){
                $datatable->whereIn('payment_type',config('module.service-purchase-auto.rbx_api'));
            }
        }

        if ($request->filled('group_id2')) {

            $datatable->whereHas('item_ref', function ($query) use ($request) {
                $query->where('id',$request->get('group_id2'));
            });
        }

        if ($request->filled('type_information')) {
            $datatable->whereHas('author', function ($query) use ($request) {
                $query->where('type_information',$request->get('type_information'));
            });
        }

        if ($request->filled('roblox_acc')) {

            $datatable->whereHas('roblox_order', function ($query) use ($request) {
                $query->whereHas('bot', function ($query) use ($request) {
                    $query->Where('acc', 'LIKE', '%' . $request->get('roblox_acc') . '%');
                });
            });
        }


        if ($request->filled('author')) {

            $datatable->whereHas('author', function ($query) use ($request) {
                $query->where('username',$request->get('author'));
            });
        }

        if ($request->filled('processor')) {

            $datatable->whereHas('processor', function ($query) use ($request) {
                $query->where('username',$request->get('processor'));
            });
        }

        if ($request->filled('id')) {
            $datatable->where(function($q) use ($request) {
                $q->orWhere('id',$request->get('id'));
                $q->orWhere('request_id_customer',$request->get('id'));
            });

        }

        if ($request->filled('request_id')) {
            $request_id = explode(',',$request->get('request_id'));
            $datatable->whereIn('request_id_customer',$request_id);
        }

        if ($request->filled('check_status')) {

            $datatable->with('order_detail',function ($query){
                $query->whereIn('status',[3,4]);
            });
            $datatable->whereHas('order_detail',function ($query){
                $query->where('status',3);
            });
            $datatable->whereHas('khachhang',function ($q){
                $q->where('status','danhan');
            });
        }

        if ($request->filled('check_status_ninjaxu')) {

            $datatable->with('order_detail',function ($query){
                $query->whereIn('status',[3,4]);
            });
            $datatable->whereHas('order_detail',function ($query){
                $query->where('status',3);
            });
            $datatable->whereHas('ninjaxu_khachhang',function ($q){
                $q->where('status','danhan');
            });
        }

        if ($request->filled('check_status_nrogem')) {

            $datatable->with('order_detail',function ($query){
                $query->whereIn('status',[3,4]);
            });
            $datatable->whereHas('order_detail',function ($query){
                $query->where('status',3);
            });
            $datatable->whereHas('item_rels',function ($q){
                $q->where('status','danhanngoc');
            });
        }

        if ($request->filled('check_status_roblox')) {

            $datatable->with('order_detail',function ($query){
                $query->whereIn('status',[3,4]);
            });
            $datatable->whereHas('order_detail',function ($query){
                $query->where('status',3);
            });
            $datatable->whereHas('roblox_order',function ($q){
                $q->where('status','danhan');
            });
        }

        if ($request->filled('title')) {
            $datatable->where('title', 'LIKE', '%' . $request->get('title') . '%');
        }

        if ($request->filled('status_nrogem')) {

            $datatable->where('idkey', '=', 'nrogem');

            $datatable->whereHas('item_rels', function ($query) use ($request) {
                $query->where('status', 'LIKE', '%' . $request->get('status_nrogem') . '%');
            });
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

        if(

            $request->filled('id') ||
            $request->filled('group_id') ||
            $request->filled('title') ||
            $request->filled('status') ||
            $request->filled('author') ||
            $request->filled('processor')||
            $request->filled('started_at') ||
            $request->filled('ended_at')||
            $request->filled('finished_started_at')||
            $request->filled('finished_ended_at')
        ){


            if ($request->filled('finished_started_at')) {

                $datatable->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_started_at')));
            }
            if ($request->filled('finished_ended_at')) {
                $datatable->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_ended_at')));
            }
        }else{

            $datatable->whereMonth('updated_at', Carbon::now()->month);

        }

        //nếu user ko full quyền nhận các dịch vụ thì lấy các id dịch vụ được cấp quyền
        if ($request->filled('group_id')) {
            $datatable->whereHas('item_ref', function ($query) use ($request) {
                $query->whereIn('id',$request->get('group_id'));
            });
        }
        if ($request->filled('group_id2')) {
            $datatable->whereHas('item_ref', function ($query) use ($request) {
                $query->where('id',$request->get('group_id2'));
            });
        }

        $datatable = $datatable->orderBy('id','desc')->get();

        $datatable = $datatable->map(function ($item) {
            $author = '';
            if (isset($item->author) && isset($item->author->type_information)){
                if ($item->author->type_information == 1){
                    $author = 'Global';
                }elseif ($item->author->type_information == 2){
                    $author = 'Sàn';
                }else{
                    $author = 'Việt Nam';
                }
            }else{
                $author = 'Việt Nam';
            }

            $server = $item->params->server??"";

            if ($item->idkey == 'roblox_buyserver' || $item->idkey == 'roblox_buygamepass' || $item->idkey == 'huge_99_auto' || $item->idkey == 'gem_unist_auto'
                || $item->idkey == 'unist_auto' || $item->idkey == 'huge_psx_auto' || $item->idkey == 'pet_99_auto' || $item->idkey == 'robux_premium_auto'){
                $order_roblox = $item->roblox_order;
                $server = $order_roblox->uname??'';
            }

            $uname = '';
            if (isset($item->roblox_order) && $item->roblox_order->phone){
                if ($item->idkey == 'roblox_gem_pet'){
                    $valueWithB = $item->roblox_order->phone;
                    // Loại bỏ ký tự "B" và chuyển đổi thành số
                    $valueInBillion = (float) str_replace('B', '', $valueWithB);
                    $uname = '"'.($valueInBillion * 1000000000);
                }else{
                    $uname = $item->roblox_order->phone??'';
                }
            }

            $bot = '';
            $id_pengiriman = '';

            if (isset($item->order_pengiriman)){
                $id_pengiriman = $item->order_pengiriman->title??"";
                if (isset($item->roblox_order)){
                    $roblox_order = $item->roblox_order;
                    $bot = $roblox_order->bot_handle??'';
                    if (isset($roblox_order->bot)){
                        $bot = $roblox_order->bot->acc??'';
                    }
                }
            }else{
                if (isset($item->roblox_order)){
                    $roblox_order = $item->roblox_order;
                    $bot = $roblox_order->bot_handle??'';
                    if (isset($roblox_order->bot)){
                        $bot = $roblox_order->bot->acc??'';
                        $id_pengiriman = $roblox_order->bot->id_pengiriman??'';
                    }
                }
            }

            $total = 0;
            if ($item->status==4){
                $total = intval($item->price)-intval($item->real_received_price_ctv);
            }
            $process_at = '';
            if (isset($item->process_at)){
                $process_at = date('d/m/Y H:i:s', strtotime($item->process_at));
            }
//            date('d/m/Y H:i:s', strtotime($item->created_at));
            $status = config('module.service-purchase-auto.status.'.$item->status);

            $payment_type = "ĐẠI LÝ";
            if (isset($item->payment_type) && $item->payment_type == 2){
                $payment_type = "RBX API";
            }

            return [
                'id' => $item->id??null,
                'idkey' => $item->idkey,
                'title' => $item->title??'',
                'payment_type' => $payment_type,
                'author' => $author,
                'price_base' => '"'.$item->price_base,
                'server' => $server,
                'uname' => $uname,
                'bot' => $bot,
                'id_pengiriman' => $id_pengiriman,
                'price' => $item->price,
                'price_input' => $item->price_input??'',
                'total' => ($item->price - $item->price_input),
                'status' => $status,
                'created_at' => $item->created_at->toDateTimeString(), // Adjust the date format as needed
                'process_at' => $process_at,
                'username' => $item->author->username??"",
                'processor' => $item->processor->username??"",
                'request_id_customer' => '"'.$item->request_id_customer??'',
            ];
        })->toArray();

        return collect($datatable);
    }

    public function headings(): array
    {
        return [
            "ID",
            "Cổng Auto",
            "Dịch vụ",
            "Phương thức thanh toán",
            "Loại tài khoản",
            "Số lượng",
            "Server",
            "Tên công việc",
            "Bot thực hiện",
            "ID lô hàng",
            "Trị giá",
            "Phải trả NCC",
            "Lợi nhuận",
            "Trạng thái",
            "Ngày tạo",
            "Ngày hoàn tất",
            "Người order",
            "Người nhận",
            "Requet id"
        ];
    }
}
