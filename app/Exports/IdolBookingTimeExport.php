<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\User;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IdolBookingTimeExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = User::where('is_idol', 1)
                    ->where('account_type',2)
                    ->with(['meta' => function($query) {
                        $query->where('key', 'khunggio');
                    }])
                    ->select('id', 'fullname_display', 'email', 'gender')
                    ->get();

        foreach ($data as $item) {

            // dd($item->meta[0]->value);
            $booking_time = "Không có dữ liệu";
            if ( $item->meta != null || $item->meta != "" ) {
                if( $item->meta->first() != null || $item->meta->first() != "" ) {
                    $meta_value = $item->meta->first()->value;
                    // dd($meta_value);
                    if ( $meta_value != null || $meta_value != "") {
                        $booking_time = $meta_value;
                        $booking_time = str_replace( array( '"', '[', ']' ), '', $booking_time);
                        $booking_time = str_replace( ',', ', ', $booking_time);
                    }

                }
            }


            $gender = "Không có dữ liệu";
            if ( $item->gender == 2) {
                $gender = "Nữ";
            } else if ( $item->gender == 1) {
                $gender = "Nam";
            }

            $email = "Không có dữ liệu";
            if( $item->email ) {
                $email = $item->email;
            }

            $fullname_display = "Không có dữ liệu";
            if( $item->fullname_display ) {
                $fullname_display = $item->fullname_display;
            }


            $data_excel[] = [
                'id' => $item->id,
                'fullname_display' => $fullname_display,
                'email' => $email,
                'booking_time' => $booking_time,
                'gender' => $gender,
            ];
        }

        if( !empty($data_excel) ) return collect($data_excel);

        return collect(null);
    }

    public function headings(): array
    {
        return ["ID", "Tên Idol", "Email", "Thời gian nhận booking", "Giới tính"];
    }
}
