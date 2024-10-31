<?php

namespace App\Exports;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Request;

class DepositBankExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    function __construct($year,$month) {
        $this->year = $year;
        $this->month = $month;
    }
    public function collection()
    {
        $data = Order::with('author')
                    ->where('module','=','charge_bank')
                    ->where('payment_type',1)
                    ->whereMonth('created_at', '=', $this->month)
                    ->whereYear('created_at', '=',  $this->year)
                    ->select('id','author_id','price','params','status','created_at')
                    ->get();
        foreach($data as $item){
            $account = '';
            if($item->author->fullname_display){
                $account .= $item->author->fullname_display;
            }
            if($item->author->email){
                $account .= ' - '.$item->author->email;
            }
            $fee = null;
            if(isset($item->params)){
                $fee = number_format(json_decode($item->params)->discount_amount). " VNĐ";
            }
            $status = null;
            if($item->status == 0){
                $status = "Thất bại";
            }
            elseif($item->status == 1){
                $status = "Thành công";
            }
            elseif($item->status == 2){
                $status = "Đang chờ";
            }
            else if($item->status == 3){
                $status = "Đã hủy";
            }
            $data_excel[] = [
                'id' => $item->id,
                'created_at' => $item->created_at->format('H:i d-m-Y'),
                'account' => $account,
                'price' => number_format($item->pirce). ' VNĐ',
                'free' => $fee,
                'status' => $status,
            ];
        }
        if(!empty($data_excel)){
            return collect($data_excel);
        }
        return collect(null);
    } 
    public function headings() :array {
    	return ["ID",'Thời gian',"Tài khoản","Số tiền","Phí","Trạng thái"];
    }
}
