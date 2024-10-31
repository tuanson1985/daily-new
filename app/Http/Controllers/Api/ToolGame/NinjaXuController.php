<?php

namespace App\Http\Controllers\Api\ToolGame;

use App\Http\Controllers\Controller;
use App\Models\Bot;

use App\Models\Item;

use App\Models\NinjaXu_AccNap;
use App\Models\NinjaXu_KhachHang;
use App\Models\NinjaXu_User;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\SubItem;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Log;


class NinjaXuController extends Controller
{
    private $secretkey = "3b0eedf~545";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function getNick(Request $request)
    {
        if($request->secretkey != $this->secretkey){
            return "không được truy cập!";
        }

        $bot = NinjaXu_User::where('server', $request->get('server'))
            ->where('ver', $request->get('ver'))
            ->get();

        if ($bot && count($bot) > 0) {

            $result = "";
            foreach ($bot as $abot) {
                $result .= "[acc]" . $abot->acc . "[/acc]\n";
                $result .= "[pass]" . $abot->pass . "[/pass]\n";
                $result .= "[igbanxu]" . $abot->igbanxu . "[/igbanxu]\n";
            }
            return $result;
        } else {
            return "[not]";
        }
    }

    public function getUpdate(Request $request)
    {
        if($request->secretkey != $this->secretkey){
            return "không được truy cập!";
        }
        $bot = NinjaXu_User::where('ver', $request->ver)->where('server', $request->get('server'))->first();
        if ($bot) {
            $bot->uname = $request->uname;
            $bot->coin = $request->coin;
            $bot->zone = $request->zone;
            $bot->updated_at = Carbon::now();
            $bot->save();
        }
        return "[ok]";

    }

    public function getKhachHang(Request $request)
    {
        if($request->secretkey != $this->secretkey){
            return "không được truy cập!";
        }
        // Start transaction!
        DB::beginTransaction();
        try {

            if ($request->filled('id')) {

                $khachhang = NinjaXu_KhachHang::where('id', $request->id)->lockForUpdate()->firstOrFail();
                $khachhang->status = "danhan";
                $khachhang->ver=$request->ver;
                $khachhang->c_sau=$request->c_sau;
                $khachhang->save();

                //cập nhật trạng thái của purchase
                $order = Order::where('id', $khachhang->order_id)->lockForUpdate()->firstOrFail();
                $order->status = 4;
                $order->process_at = Carbon::now();
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'status' => 4,
                    'content' => "danhan",
                ]);

                DB::commit();

                if (isset($khachhang->server)){
                    $bot = NinjaXu_User::where('server', $khachhang->server)
                        ->first();

                    if (isset($bot)){
                        $khachhang->bot_handle = $bot->uname??'';
                        $khachhang->save();
                    }
                }

                //callback trả shop
                if($order->status==4 || $order->status==5 ){

                    if($order->url!=""){
                        $this->callbackToShop($order,"danhan");
                    }
                }

                return "[ok]";

            }
            else {

                $khachhang = NinjaXu_KhachHang::where('server', $request->get('server'))
                    ->where('uname', $request->uname)
                    ->where('status', "chuanhan")
                    ->lockForUpdate()
                    ->first();

                if ($khachhang) {

                    $khachhang->c_truoc=$request->c_truoc;
                    $khachhang->save();

                    //update khóa edit
                    $order = Order::where('module', '=', config('module.service-purchase.key'))
                        ->where('id', $khachhang->order_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $order->expired_lock=Carbon::now()->addMinutes(5);
                    $order->save();

                    $result = "[id]" . $khachhang->id . "[/id]\n";
                    $result .= "[coin]" . $khachhang->coin . "[/coin]\n";
                    DB::commit();
                    return $result;
                } else {
                    return '[not]';
                }
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('[Lỗi bán xu]' . $e->getMessage());
            return "Lỗi bán bạc:".$e->getMessage();
        }


    }

    public function getUname(Request $request)
    {
        if($request->secretkey != $this->secretkey){
            return "không được truy cập!";
        }
        $bot = Bot::all();
        $result = "[uname]";
        if (!empty($bot) && count($bot) > 0) {

            foreach ($bot as $index => $aBot) {
                $result .= $aBot->uname;

                if ($index <= count($bot)) {
                    $result .= ';';
                }
            }
        }
        $result .= "[/uname]";
        return $result;
    }

    public function getCheck(Request $request)
    {
        if($request->secretkey != $this->secretkey){
            return "không được truy cập!";
        }
        $accnap = NinjaXu_AccNap::where('server', $request->get('server'))
            ->where('uname', $request->get('uname'))->first();
        if ($accnap) {
            return "[true]";
        } else {
            return "[false]";
        }
    }

    public function getNap(Request $request){

        if($request->secretkey != $this->secretkey){
            return "không được truy cập!";
        }
        $khachhang = NinjaXu_KhachHang::create([
            'ver'=>$request->get('ver'),
            'server'=>$request->get('server'),
            'uname'=>$request->get('from')." nạp cho " .$request->get('to'),
            'coin'=>$request->get('send'),
            'c_truoc'=>$request->get('c_truoc'),
            'c_sau'=>$request->get('c_sau'),
            'status'=>'danap',
        ]);
        return "[ok]";
    }

    public function listBot(Request $request){

        /* if($request->app_client=="" || $request->app_client==","){
            return [];
        } */
        $bot =  NinjaXu_User::get(['id','server','acc','uname','coin','zone','igbanxu','app_client','active']);

        return $bot;
    }

    public function callbackToShop(Order $order,$messageBot)
    {

        $url = $order->url;

        $data = array();
        $data['status'] = $order->status;
        $data['message'] = $messageBot;
        $data['price'] = $order->price;
        $data['price_base'] = $order->price_base;
        $data['input_auto'] = 1;
        if ($order->status == 4){
            $data['process_at'] = strtotime($order->process_at);
        }
        $data['request_id'] = $order->request_id_customer;

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
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);

                $resultRaw=curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                //debug thì mở cái này
                $myfile = fopen(storage_path() . "/logs/curl_callback-service-to-shop-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
                $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
                fwrite($myfile, $txt);
                fclose($myfile);

                if($httpcode==200){

                    if(strpos($resultRaw, "Có lỗi phát sinh.Xin vui lòng thử lại") > -1){
                        continue;
                    }
                    break;
                }
            }
        }
        catch (Exception $e){
            Log::error($e);
        }

    }

}
