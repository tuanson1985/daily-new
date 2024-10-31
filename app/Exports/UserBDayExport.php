<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\User;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserBDayExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = User::whereNull('is_idol')
                    ->orWhere('is_idol',0)
                    ->where('account_type', 2)
                    ->select('id', 'fullname_display', 'email', 'birtday', 'gender')
                    ->get();

        foreach ($data as $item) {

            $gender = "Không có dữ liệu";
            if ( $item->gender == 2) {
                $gender = "Nữ";
            } else if ( $item->gender == 1) {
                $gender = "Nam";
            }

            $fullname_display = "Không có dữ liệu";
            if( $item->fullname_display ) {
                $fullname_display = $item->fullname_display;
            }

            $email = "Không có dữ liệu";
            if( $item->email ) {
                $email = $item->email;
            }

            $birthday = "Không có dữ liệu";
            if( $item->birtday ) {
                $birthday = $item->birtday;
            }



            $data_excel[] = [
                'id' => $item->id,
                'fullname_display' => $fullname_display,
                'email' => $email,
                'birtday' => $birthday,
                'gender' => $gender,
            ];
        }

        if( !empty($data_excel) ) return collect($data_excel);

        return collect(null);
    }

    public function headings() :array {
        return ["ID", "Tên người dùng", "Email", "Ngày tháng năm sinh", "Giới tính"];
    }
}
