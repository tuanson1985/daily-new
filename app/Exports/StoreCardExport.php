<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StoreCardExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    function __construct($data) {
        $this->data = $data;
    }
    public function collection()
    {
        $data_excel = [];
        if(!empty($data_excel)){
            return collect($data_excel);
        }
        return collect(null);
    }
    public function headings() :array {
    	return [
            "ID",
            'Thời gian',
            "Tài khoản",
            "Nhà mạng",
            "Mệnh giá",
            "Số lượng",
            "Chiết khấu",
            "Tổng tiền",
            "Nhà cung cấp",
            "Trạng thái"
        ];
    }
}
