<?php
namespace App\Exports;

use App\Models\Order;
use App\Models\Withdraw;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ConfirmWithdrawExport implements FromCollection, WithHeadings
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

        $datatable = Withdraw::query()
            ->with('txns', 'user');

        if ($request->filled('id')) {
            $datatable->where('id',$request->id);
        }
        if ($request->filled('request_id')) {
            $datatable->where('request_id',$request->request_id);
        }
        if ($request->filled('username')) {
            $datatable->whereHas('user', function ($query) use ($request) {
                $query->where(function ($qChild) use ($request){
                    $qChild->where('username', $request->get('username'));
                });
            });
        }
        if ($request->filled('bank_type')) {
            $datatable->where('bank_type',$request->bank_type);
        }
        if ($request->filled('bank_title')) {
            $datatable->where('bank_title',$request->bank_title);
        }
        if ($request->filled('account_number')) {
            $datatable->where(function($q) use ($request) {
                $q->orWhere('account_number',$request->account_number);
                $q->orWhere('account_vi',$request->account_number);
            });
        }
        if ($request->filled('source_money')) {
            $datatable->where('source_money',$request->source_money);
        }
        if ($request->filled('source_bank')) {

            $datatable->where('source_bank',$request->source_bank);
        }
        if ($request->filled('status')) {
            if($request->status==2){
                $datatable->whereIn('status',[-1,2]);
            }
            else{
                $datatable->where('status', $request->status);
            }
        }
        if ($request->filled('role_id')) {
            $datatable->where('role_id', 'LIKE', '%' . $request->role_id . '%');
        }
        if ($request->filled('started_at')) {
            $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
        }
        if ($request->filled('ended_at')) {
            $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
        }
        if ($request->filled('started_process_at')) {
            $datatable->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_process_at')));
        }
        if ($request->filled('ended_process_at')) {
            $datatable->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_process_at')));
        }

        $datatable = $datatable->orderBy('id','desc')->get();

        $datatable = $datatable->map(function ($item) {
            //Loại thành viên
            $membership_type = "N/A";
            if ($item->user->account_type == 1) {
                $membership_type = "Quản trị viên";
            } else if ($item->user->account_type == 2) {
                $membership_type = "Thành viên";
            } else if ($item->user->account_type == 3) {
                $membership_type = "Cộng tác viên";
            }

            //Thời gian duyệt
            $process_at = "N/A";
            if (isset($item->process_at)){
                $process_at = date('d/m/Y H:i:s', strtotime($item->process_at));
            }

            //Trạng thái
            $status = 'N/A';
            if (isset($item->status)){
                $status = config('module.withdraw.status.'.$item->status);
            }

            //Người duyệt
            $processor = 'N/A';
            if (isset($item->processor)){
                $processor = $item->processor->username??'';
            }

            //Người rút
            $author = 'N/A';
            if (isset($item->user)){
                $author = $item->user->username??'';
            }

            //Rút về
            $bank_type = 'N/A';
            if (isset($item->bank_type)){
                $bank_type = config('module.bank.bank_type.'.$item->bank_type);
            }

            //STK/TK Ví
            $account_number = 'N/A';
            if (isset($item->account_number) && $item->account_number != ''){
                $account_number = '"'.$item->account_number;
            }else{
                $account_number = $item->account_vi??'N/A';
            }
            //Nguồn chuyển

            $source_money = 'N/A';
            if (isset($item->source_money)){
                $source_money = config('module.bank.bank_type.'.$item->source_money);
            }

            return [
                'membership_type' => $membership_type,
                'process_at' => $process_at,
                'id' => $item->id??'N/A',
                'request_id' => '"'.$item->request_id??'N/A',
                'status' => $status,
                'processor' => $processor,
                'author' => $author,
                'amount' => $item->amount??'',
                'bank_type' => $bank_type,
                'bank_title' => $item->bank_title??'N/A',
                'account_number' => $account_number,
                'holder_name' => $item->holder_name??'N/A',
                'source_money' => $source_money,
                'source_bank' => $item->source_bank??'N/A',
                'admin_note' => $item->admin_note??'N/A',
                'txns_id' => $item->txns_id??'N/A',
            ];
        })->toArray();

        return collect($datatable);
    }

    public function headings(): array
    {
        return ["Loại thành viên", "Thời gian", "ID", "Request ID", "Trạng thái", "Người duyệt", "Người rút", "Số tiền", "Rút về", "Tên ngân hàng/ví", "STK/TK Ví", "Tên chủ tài khoản", "Nguồn chuyển", "Tên ngân hàng/ví chuyển","Ghi chú/Lý do","Mã Txns"];
    }
}
