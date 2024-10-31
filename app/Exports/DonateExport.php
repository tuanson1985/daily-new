<?php

namespace App\Exports;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DonateExport implements FromCollection, WithHeadings
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
        $data =  Order::with('author')
                ->with('user_ref')
                ->with('order_detail')
                ->where('module','donate')
                ->whereMonth('created_at', '=', $this->month)
                ->whereYear('created_at', '=',  $this->year)
                ->get();
        foreach($data as $item){
            $account = '';
            if($item->author->fullname_display){
                $account .= $item->author->fullname_display;
            }
            if($item->author->email){
                $account .= ' - '.$item->author->email;
            }

            $account_idol = '';
            if($item->user_ref->fullname_display){
                $account_idol .= $item->user_ref->fullname_display;
            }
            if($item->user_ref->email){
                $account_idol .= ' - '.$item->user_ref->email;
            }
            $status = null;
            if($item->status == 0){
                $status = "Thất bại";
            }
            elseif($item->status == 1){
                $status = "Thành công";
            }
            elseif($item->status == 2){
                $status = "Đang chờ thanh toán";
            }
            elseif($item->status == 3){
                $status = "Đã hủy";
            }
            $data_excel[] = [
                'id' => $item->id,
                'created_at' => $item->created_at->format('H:i d-m-Y'),
                'account' => $account,
                'payment_type' => config('module.donate.payment_type.'.$item->payment_type),
                'account_idol' => $account_idol,
                'price' => number_format($item->price). ' VNĐ',
                'real_received_price' => number_format($item->real_received_price). ' VNĐ',
                'status' => $status,
            ];
        }
        if(!empty($data_excel)){
            return collect($data_excel);
        }
        return collect(null);
    }
    public function headings() :array {
    	return ["ID",'Thời gian',"Người donate","Phương thức thanh toán","Người nhận","Số tiền thanh toán","Số tiền idol nhận","Trạng thái"];
    }
}
