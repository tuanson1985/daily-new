<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use App;
use Illuminate\Http\Request;

class CallbackOrderService implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $order;
    public $message;
    public $refund;
    public $mistake_by;

    public function __construct(Order $order,$message,$refund = null,$mistake_by = null)
    {
        $this->order = $order;
        $this->message = $message;
        $this->refund = $refund;
        $this->mistake_by = $mistake_by;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = $this->order;
        $refund = $this->refund??'';
        $message = $this->message;
        $mistake_by = $this->mistake_by??'';

        $url = $order->url;

        $data = array();

        $data['status'] = $order->status;

        $data['message'] = $message;

        $data['mistake_by'] = $mistake_by;

        if (strpos($url, 'https://backend-th.tichhop.pro') > -1 || strpos($url, 'http://s-api.backend-th.tichhop.pro') > -1){
            $data['message'] = config('lang.'.$message)??$message;
            $data['mistake_by'] = config('lang.'.$mistake_by)??$mistake_by;
        }

        if (isset($refund)){
            $data['refund'] = $refund;
        }

        $data['input_auto'] = 0;
        if ($order->status == 4){
            $data['price'] = $order->real_received_price_ctv;
        }

        if ($order->status == 4 || $order->status == 10){
            $data['process_at'] = strtotime($order->process_at);
        }

        $dataPost = http_build_query($data);

        try{

            for ($i=0;$i<3;$i++){
                $ch = curl_init();

                //data dạng get
                if (strpos($url, '?') !== FALSE) {
                    $url = $url . "&" . $dataPost;
                } else {
                    $url = $url . "?" . $dataPost;
                }

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);

                $resultRaw=curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                //debug thì mở cái này
                $myfile = fopen(storage_path() . "/logs/curl_callback-service-to-shop-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
                $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
                fwrite($myfile, $txt);
                fclose($myfile);

                if($httpcode==200){

                    if(strpos($resultRaw, __("Có lỗi phát sinh.Xin vui lòng thử lại")) > -1){
                        continue;
                    }
                    break;
                }
            }
        }
        catch (\Exception $e){
            \Log::error($e);
        }
    }
}
