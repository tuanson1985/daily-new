<?php
namespace App\Exports;

use App\Models\Order;
use App\Models\ServiceAccess;
use Auth;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ServiceExport implements FromCollection, WithHeadings, WithColumnFormatting
{
    protected $started_at;
    protected $ended_at;
    protected $status;
    public function __construct($started_at,$ended_at,$status)
    {
        $this->started_at = $started_at;
        $this->ended_at = $ended_at;
        $this->status = $status;
    }

    public function collection()
    {
        ini_set('max_execution_time', 1200); //2 minutes
        ini_set('memory_limit', '-1');

        $finished_started_at = $this->started_at;
        $finished_ended_at = $this->ended_at;
        $status = $this->status;

        $service_query = Order::query()
            ->with(['workflow_reception','author'=> function ($q){
                $q->select('id','username','type_information');
            },'processor'=>function ($q){
                $q->select('id','username');
            }])
            ->select('id','title','request_id_customer','author_id','processor_id','params','description','price','real_received_price_ctv','status','created_at','updated_at','process_at')
            ->where('module', 'service-purchase')
            ->where('gate_id', 0);

        $service_query = $service_query->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_started_at));
        $service_query = $service_query->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $finished_ended_at));

        $data_query = $service_query
            ->orderBy('created_at','asc')->get();

        $data_query = $data_query->map(function ($item){
            //Username
            $service_title = $item->title??'';

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

            $status = config('module.service-purchase.status.'.$item->status);

            //Thời gian nhận đơn

            $reception_at = '';
            if (isset($item->workflow_reception)){
                $workflow_reception = $item->workflow_reception;
                $reception_at = date('d/m/Y H:i:s', strtotime($workflow_reception->updated_at));
            }

            $created_at = date('d/m/Y H:i:s', strtotime($item->created_at));

            $process_at = date('d/m/Y H:i:s', strtotime($item->process_at));

            return [
                'service_title' => $service_title,
                'author' => $author,
                'description' => $item->description??'',
                'id' => $item->id??null,
                'price' => $item->price??'',
                'status' => $status??'',
                'created_at' => \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($created_at),
                'reception_at' => \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($reception_at),
                'process_at' => \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($process_at),
                'processor' => $item->processor->username??"",
            ];

        })->toArray();

        return collect($data_query);
    }

    public function headings(): array
    {
        return [
            "Dịch vụ",
            "Loại tài khoản",
            "Tên công việc",
            'id',
            'Trị giá',
            "Trạng thái",
            "Ngày tạo",
            "Ngày nhận đơn",
            "Ngày hoàn thất",
            "Người nhận đơn",
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];

    }
}
