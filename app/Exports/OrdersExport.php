<?php
namespace App\Exports;

use App\Models\Order;
use Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
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

        $datatable = Order::query()->with(['item_ref','author','processor','workflow_reception','order_pengiriman'])
            ->where('order.module', config('module.service-purchase'))
            ->where('gate_id',0);

        if ($request->filled('id')) {

            $datatable->where(function($q) use ($request) {
                $q->orWhere('id',$request->get('id'));
                $q->orWhere('request_id_customer',$request->get('id'));
            });

        }

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

        if ($request->filled('type')) {
            $type_status = $request->get('type');
            if ($type_status == 1) {
                $datatable->whereIn('status', [1]);
            } elseif ($type_status == 2) {
                $datatable->whereIn('status', [2]);
                if (!Auth::user()->can('service-reception-all')){
                    $datatable->where('author_id',Auth::user()->id);
                }
            } elseif ($type_status == 3) {
                $datatable->whereIn('status', [0,5,3]);
                if (!Auth::user()->can('service-reception-all')){
                    $datatable->where('author_id',Auth::user()->id);
                }
            } elseif ($type_status == 4) {
                $datatable->whereIn('status', [10,4]);
                if (!Auth::user()->can('service-reception-all')){
                    $datatable->where('author_id',Auth::user()->id);
                }
            } elseif ($type_status == 5) {
                $datatable->whereIn('status', [11,12]);
                if (!Auth::user()->can('service-reception-all')){
                    $datatable->where('author_id',Auth::user()->id);
                }
            }
        }

        if ($request->filled('request_id')) {

            $request_id = explode(',',$request->get('request_id'));
            $datatable->whereIn('request_id_customer',$request_id);

        }

        if ($request->filled('title')) {

            $datatable->where('title', 'LIKE', '%' . $request->get('title') . '%');

        }

        if ($request->filled('work_name')) {

            $datatable->where('description', 'LIKE', '%' . $request->get('work_name') . '%');

        }

        if ($request->filled('author')) {
            $datatable->whereHas('author', function ($query) use ($request) {
                $query->Where('username', 'LIKE', '%' . $request->get('author') . '%');
            });
        }

        if ($request->filled('mistake_error_by')) {

            $datatable->where('content', 'LIKE', '%' . $request->get('mistake_error_by') . '%');

        }

        if ($request->filled('type_information')) {

            $datatable->whereHas('author', function ($query) use ($request) {
                $query->where('type_information',$request->get('type_information'));
            });

        }

        if ($request->filled('type_information_ctv')) {
            $datatable->whereHas('processor', function ($query) use ($request) {
                $query->where('type_information_ctv',$request->get('type_information_ctv'));
            });
        }

        if ($request->filled('account_type')) {

            if($request->get('account_type') == 1){
                $datatable->whereHas('processor', function ($query) use ($request) {
                    $query->where('username', 'REGEXP', '^qtv');
                });
            }else if($request->get('account_type') == 3){
                $datatable->whereHas('processor', function ($query) use ($request) {
                    $query->where('username', 'REGEXP', '^ctv');
                });
            }
        }

        if ($request->filled('processor')) {

            $datatable->whereHas('processor', function ($query) use ($request) {
                $query->where('username',$request->get('processor'));
            });

        }

        if ($request->filled('status')) {

            $datatable->whereIn('status',$request->get('status'));

        }

        if ($request->filled('started_at')) {

            $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
        }

        if ($request->filled('ended_at')) {
            $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
        }

        if ($request->filled('arrange')){
            if ($request->get('arrange') == 0){
                $datatable = $datatable->orderBy('created_at', 'desc');
            }elseif ($request->get('arrange') == 1){
                $datatable = $datatable->orderBy('created_at', 'asc');
            }elseif ($request->get('arrange') == 2){
                $datatable = $datatable->orderBy('updated_at', 'desc');
            }elseif ($request->get('arrange') == 3){
                $datatable = $datatable->orderBy('updated_at', 'asc');
            }elseif ($request->get('arrange') == 4){
                $datatable = $datatable->orderBy('price', 'desc');
            }elseif ($request->get('arrange') == 5){
                $datatable = $datatable->orderBy('price', 'asc');
            }
        }else{
            $datatable = $datatable->orderBy('created_at', 'asc');
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

                $datatable = $datatable->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_started_at')));
            }
            if ($request->filled('finished_ended_at')) {
                $datatable = $datatable->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('finished_ended_at')));
            }
        }

        if ($request->filled('id_pengiriman')) {

            $datatable->whereHas('order_pengiriman', function ($query) use ($request) {
                $query->where('title', 'LIKE', '%' . $request->get('id_pengiriman') . '%');
            });
        }

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
            $type_processor = 'N/A';
            if (isset($item->processor) && isset($item->processor->username)){
                if (substr($item->processor->username, 0, 3) === "ctv") {
                    $type_processor = 'Cộng tác viên shop khách';
                } elseif (substr($item->processor->username, 0, 3) === "qtv"){
                    $type_processor = 'Cộng tác viên shop nhà';
                }
            }

            $server = $item->params->server??"";

            $total = 0;
            if ($item->status==4){
                $total = intval($item->price)-intval($item->real_received_price_ctv);
            }

            $status = config('module.service-purchase.status.'.$item->status);

            $updated_at = '';

            if ($item->status == 4){
                $updated_at = date('d/m/Y H:i:s', strtotime($item->process_at));
            }elseif ($item->status == 10 || $item->status == 11){
                $updated_at =  date('d/m/Y H:i:s', strtotime($item->process_at));
            }
            else{
                $updated_at = date('d/m/Y H:i:s', strtotime($item->updated_at));
            }

            if (isset($item->author) && isset($item->author->type_information)){
                if ($item->author->type_information == 1){
                    $author = 'Global';
                }elseif ($item->author->type_information == 2){
                    $author = 'Sàn';
                }
                else{
                    $author = 'Việt Nam';
                }
            }else{
                $author = 'Việt Nam';
            }

            //Thời gian nhận đơn

            $reception_at = '';
            if (isset($item->workflow_reception)){
                $workflow_reception = $item->workflow_reception;

                $reception_at = date('d/m/Y H:i:s', strtotime($workflow_reception->updated_at));
            }

            $errors = "";

            if ($status == 3 || $status == 5 || $status == 0){
                $errors = $item->content??'';
            }

            $id_pengiriman = '';
            $account_pengiriman = '';

            if (isset($item->order_pengiriman)){
                $id_pengiriman = $item->order_pengiriman->title??'';
                $account_pengiriman = $item->order_pengiriman->description??'';
            }

            return [
                'id' => $item->id??null,
                'requet_id' => '"'.$item->request_id_customer??'',
                'title' => $item->title??'',
                'author' => $author,
                'server' => $server,
                'description' => $item->description??'',
                'price' => $item->price??'',
                'real_received_price_ctv' => $item->real_received_price_ctv??'',
                'total' => $total,
                'errors' => $errors,
                'id_pengiriman' => $id_pengiriman,
                'account_pengiriman' => $account_pengiriman,
                'status' => $status,
                'created_at' => $item->created_at->toDateTimeString(), // Adjust the date format as needed
                'reception_at' => $reception_at, // Adjust the date format as needed
                'updated_at' => $updated_at,
                'username' => $item->author->username??"",
                'processor' => $item->processor->username??"",
                'type_processor' => $type_processor??"",
            ];
        })->toArray();

        return collect($datatable);
    }

    public function headings(): array
    {
        return [
            "ID",
            "Requet id",
            "Dịch vụ",
            "Loại tài khoản",
            "Server",
            "Tên công việc",
            "Trị giá",
            "Số tiền CTV Nhận",
            "Lợi nhuận",
            "Lý do từ chối",
            "Id lô hàng",
            "Account lô hàng",
            "Trạng thái",
            "Ngày tạo",
            "Ngày nhận đơn",
            "Ngày hoàn tất",
            "Người order",
            "Người nhận",
            "Loại cộng tác viên"
        ];
    }
}
