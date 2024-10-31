<?php
namespace App\Exports;

use App\Models\Order;
use App\Models\Txns;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromCollection, WithHeadings
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

        $datatable= User::with(['roles'=>function($query){
            $query->select(['id','title','name']);
        }])->with('shop')
            ->where("account_type",2)
            ->orderByRaw('FIELD(`status`,1,2,3,4,0,-1,99)');

        if ($request->filled('id'))  {
            $datatable->where(function($q) use($request){
                $q->orWhere('id', 'LIKE', '%' . $request->get('id') . '%');
            });
        }
        if ($request->filled('incorrect_txns')) {
            if($request->incorrect_txns==1){
                $datatable->whereRaw('(balance_in - balance_out + balance_in_refund - balance) != 0');
            }
            else{
                $datatable->havingRaw('(balance_in - balance_out + balance_in_refund - balance) = 0');
            }

        }
        if ($request->filled('fullname_display')) {
            $datatable->where('fullname_display', 'LIKE', '%' . $request->get('fullname_display') . '%');
        }
        if ($request->filled('username')) {
            $datatable->where('username', 'LIKE', '%' . $request->get('username') . '%');
        }

        if ($request->filled('email')) {
            $datatable->where('email', 'LIKE', '%' . $request->get('email') . '%');
        }
        if ($request->filled('fullname')) {
            $datatable->where('fullname', 'LIKE', '%' . $request->get('fullname') . '%');
        }
        if ($request->filled('status')) {
            $datatable->where('status',$request->get('status') );
        }

        if ($request->filled('started_at')) {
            $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
        }
        if ($request->filled('ended_at')) {
            $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
        }

        $datatable = $datatable->orderBy('id','desc')->get();

        $datatable = $datatable->map(function($item) use ($request) {

            $balance = 0;

            if ($request->filled('balance_time')){
                $txns = Txns::query()
                    ->where('user_id',$item->id);
                if ($request->filled('balance_time')){
                    $txns = $txns->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('balance_time')));
                }

                $txns = $txns->orderBy('created_at','desc')->first();
                if (isset($txns)){
                    $balance = $txns->last_balance;
                }
            }else{
                $balance = $item->balance;
            }

            $item->last_balance = $balance;

            $balance_in = intval($item->balance_in);
            $balance_out = intval($item->balance_out);
            $balance_in_refund = intval($item->balance_in_refund);
            $resuft_in_out = $balance_out - $balance_in_refund;
            $balance = $item->balance;
            $not_equal = $balance_in - $balance_out + $balance_in_refund - $balance;
            $lech = '';
            if($not_equal != 0) {
                $lech = 'Lệch: '.$not_equal;
            }else{
                $lech = 'Chuẩn +';
            }

            $status = config('module.user.status.'.$item->status);

            $create_at = \App\Library\Helpers::FormatDateTime('H:i:s d/m/Y',$item->created_at);
            return [
                'id' =>  $item->id??null,
                'username' => $item->username??'',
                'balance' => $item->balance??'',
                'last_balance' => $item->last_balance,
                'balance_in' => $balance_in,
                'resuft_in_out' => $resuft_in_out,
                'not_equal' => $lech,
                'status' => $status,
                'create_at' => $create_at,
            ];
        })->toArray();

        return collect($datatable);
    }

    public function headings(): array
    {
        return ["ID", "Tên tài khoản", "Số dư", "Số dư theo thời gian", "Biến động số dư",'balance_in','resuft_in_out', "Trạng thái", "Thời gian","Thời gian lọc"];
    }
}
