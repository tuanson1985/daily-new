<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Charge;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;

use Carbon\Carbon;

class ChargeExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    function __construct(array $data,$template) {
        $this->data = $data;
        $this->template = $template;
    }
    // public function collection()
    // {
    //     // $data = $this->data;
    //     // $data_excel = [];
    //     // foreach($data as $item){
    //     //     $data_excel[] = [
    //     //         "id" => $item->id??null,
    //     //         'shop' => $item->shop->domain??null,
    //     //         'user' => $item->user->username??null,
    //     //         'telecom_key' => $item->telecom_key??null,
    //     //         'serial' => $item->serial.' '??null,
    //     //         'declare_amount' => $item->declare_amount??null,
    //     //         'amount' => $item->amount??null,
    //     //         'ratio' => $item->ratio??null,
    //     //         'real_received_amount' => $item->real_received_amount??null,
    //     //         'fee_ncc' => null,
    //     //         'money_received' => $item->money_received??null,
    //     //         'profit' => $item->money_received - $item->real_received_amount??null,
    //     //         'gate_id' => config('module.telecom.gate_id.'.$item->gate_id)??null,
    //     //         'tranid' => $item->tranid,
    //     //         'created_at' => isset($item->created_at)?Carbon::parse($item->created_at)->format('d-m-Y H:i:s'):null,
    //     //         'process_at' =>  isset($item->process_at)?Carbon::parse($item->process_at)->format('d-m-Y H:i:s'):null,
    //     //         'status' => config('module.charge.status.'.$item->status)
    //     //     ]; 
    //     // }
    //     // if(!empty($data_excel)){
    //     //     return collect($data_excel);
    //     // }
    //     // return collect(null);
    //     return $this->data;
    // }
    // public function headings() :array {
    // 	return [
    //             "ID",
    //             "Shop",
    //             'Tài khoản',
    //             'Loại thẻ',
    //             'Serial',
    //             'Mệnh giá yêu cầu',
    //             'Mệnh giá thực',
    //             'Phí nạp thẻ',
    //             'Thực nhận',
    //             'Phí cổng nạp',
    //             'Thực nhận từ cổng nạp',
    //             'Lợi nhuận',
    //             'Cổng nạp',
    //             'Mã nhà cung cấp',
    //             'Thời gian tạo',
    //             'Thời gian cập nhật',
    //             'Trạng thái'
    //         ];
    // }
    public function view(): View
    {
        return View($this->template)->with(array('data' => $this->data));
    }

}
