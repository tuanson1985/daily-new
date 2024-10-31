<?php

namespace App\Http\Controllers\Api\ToolGame;

use App\Http\Controllers\Controller;

use App\Library\Helpers;
use App\Models\Item;
use App\Models\Nrogem_AccBan;
use App\Models\Nrogem_AccNap;
use App\Models\Nrogem_GiaoDich;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\SubItem;

use App\Models\Txns;
use App\Models\TxnsVp;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Log;
use App\Library\HelpChangeGate;
use Cache;


class SellGemController extends Controller
{

    private $secretkey = "234jhjfj33333%@sss";
    private $ip_array = ['45.118.145.145', '103.237.144.44'];

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

    public function getMain(Request $request)
    {

        if(!in_array($request->getClientIp(),$this->ip_array) ){
            //return "IP not allowed";
        }

        if($request->secretkey != $this->secretkey){
            return "không được truy cập!";
        }

        $data = Nrogem_AccBan::where('server', $request->get('server'))->where('ver', $request->get('ver'))->get();
        $result = "";
        if (!empty($data) && count($data) > 0) {
            foreach ($data as $item) {
                $result .= "[acc]" . $item->acc . "[/acc]\n";
                $result .= "[pass]" . $item->pass . "[/pass]\n";
                $result .= "[status]" . $item->status . "[/status]\n";

            }
        }

        return $result;
    }

    public function CheckGiaoDich(Request $request)
    {

        if(!in_array($request->getClientIp(),$this->ip_array) ){
            //return "IP not allowed";
        }

        if($request->secretkey != $this->secretkey){
            return "không được truy cập!";
        }

        $data = Nrogem_GiaoDich::where('server', $request->get('server'))
            ->where('ver', $request->get('ver'))
            ->where('status', 'dalogin')
            ->whereHas('order', function ($query) use ($request) {
                $query->whereIn('status', [1, 2]);
            })
            ->orderBy('id', 'asc')
            ->first();

        $result = "";
        if (!empty($data)) {
            $result .= "[id]" . $data->id . "[/id]\n";
            $result .= "[uname]" . $data->uname . "[/uname]\n";
            $result .= "[gem]" . $data->gem . "[/gem]\n";
        } else {
            $result = "[wait]";
        }

        return $result;
    }

    public function CheckStatus(Request $request)
    {

        if(!in_array($request->getClientIp(),$this->ip_array) ){
            //return "IP not allowed";
        }

        if($request->secretkey != $this->secretkey){
            return "không được truy cập!";
        }

        $data = Nrogem_GiaoDich::where('id', $request->get('id'))->first();

        $result = "";
        if (!empty($data)) {
            $result .= "[status]" . $data->status . "[/status]\n";
            $result .= "[item]" . $data->item . "[/item]\n";
            $result .= "[info]" . $data->info_item . "[/info]\n";
            $result .= "[process]" . $data->process . "[/process]\n";
        }
        return $result;
    }

    public function getDelete(Request $request)
    {

        if(!in_array($request->getClientIp(),$this->ip_array) ){
            //return "IP not allowed";
        }

        if($request->secretkey != $this->secretkey){
            return "không được truy cập!";
        }

        $data = Nrogem_GiaoDich::where('id', $request->get('id'))->delete();
        return $data;
    }

    public function getSave(Request $request)
    {

        if(!in_array($request->getClientIp(),$this->ip_array) ){
            //return "IP not allowed";
        }

        if($request->secretkey != $this->secretkey){
            return "không được truy cập!";
        }

        //debug thì mở cái này
        $myfile = fopen(storage_path() . "/logs/callback-services-auto-nrogem".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
        $txt = Carbon::now() . ":" . $request->getUri() . '?' . http_build_query($request->all());
        fwrite($myfile, $txt . "\n");
        fclose($myfile);

        // Start transaction!
        DB::beginTransaction();
        try {

            $data = Nrogem_GiaoDich::where('id', $request->get('id'))
                ->whereHas('order', function ($query) use ($request) {
                    $query->whereIn('status', [1, 2]);
                })->lockForUpdate()->firstOrFail();

            $order = Order::where('module', '=', config('module.service-purchase.key'))
                ->with('item_ref')
                ->where('status',1)
                ->where('id',$data->order_id)
                ->lockForUpdate()
                ->firstOrFail();


            $refund=false;
            if($request->get('status')=="danhanngoc" || $request->get('status')=="loichuyenngoc"){
                //cập nhật trạng thái của purchase

                if (isset($data)){
                    if (isset($data->ver) && isset($data->server)){
                        $bot = Nrogem_AccBan::query()
                            ->where('ver', $data->ver)->where('server', $data->server)->first();

                        if (isset($bot) && $bot->uname){
                            $data->bot_handle = $bot->uname;
                        }
                    }
                }


                $order->status = 4;
                $order->process_at = Carbon::now();
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'status' => 4,
                ]);
                $refund=false;
            }
            elseif($request->get('status')=="muanhamitem"){
                //cập nhật trạng thái của purchase

                if (isset($data)){
                    if (isset($data->ver) && isset($data->server)){
                        $bot = Nrogem_AccBan::query()
                            ->where('ver', $data->ver)->where('server', $data->server)->first();

                        if (isset($bot) && $bot->uname){
                            $data->bot_handle = $bot->uname;
                        }
                    }
                }

                $order->status = 9;
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'content' => 'Mua nhầm item',
                    'status' => 9,
                ]);

                $refund=false;
            }
            elseif(
                $request->get('status')=="taikhoansai" ||
                $request->get('status')=="koosieuthi" ||
                $request->get('status')=="kconhanvat" ||
                $request->get('status')=="thieungoc" ||
                $request->get('status')=="caimk2" ||
                $request->get('status')=="hanhtrangday" ||
                $request->get('status')=="khongcoitemkigui" ||
                $request->get('status')=="kodusucmanh" ||
                $request->get('status')=="tamhetngoc"
            )
            {
                if (isset($data)){
                    if (isset($data->ver) && isset($data->server)){
                        $bot = Nrogem_AccBan::query()
                            ->where('ver', $data->ver)->where('server', $data->server)->first();

                        if (isset($bot) && $bot->uname){
                            $data->bot_handle = $bot->uname;
                        }
                    }
                }

                if((strpos($data->process, 'matitem') !== false)){ //nếu đã có trạng thái matitem thi ko hoàn tiền
                    //cập nhật trạng thái của purchase
                    $order->status = 4;
                    $order->updated_at = Carbon::now();
                    $order->process_at = Carbon::now();
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 4,
                        'content' => $request->get('status'),
                    ]);
                    $refund=false;
                }
                else{
                    //cập nhật trạng thái của purchase

                    $order->status = 5;
                    $order->updated_at = Carbon::now();
                    $order->save();
                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.service-workflow.key'),
                        'status' => 5,
                        'content' => $request->get('status'),
                    ]);
                    $refund=true;
                }
            }
            if($request->get('status')=="matitem"){

                if (isset($data)){
                    if (isset($data->ver) && isset($data->server)){
                        $bot = Nrogem_AccBan::query()
                            ->where('ver', $data->ver)->where('server', $data->server)->first();

                        if (isset($bot) && $bot->uname){
                            $data->bot_handle = $bot->uname;
                        }
                    }
                }

                //cập nhật trạng thái của purchase
                $order->status = 6;
                $order->updated_at = Carbon::now();
                $order->process_at = Carbon::now();
                $order->save();
                //set tiến độ
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.service-workflow.key'),
                    'status' => 6,
                ]);

                $message="[".Carbon::now()."] ".": Mất item ngọc - ID: ".$order->id . " - Link xử lý đơn: "."https://daily.tichhop.pro/admin/service-purchase-auto/{$order->id}";
                Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_id_matitem_nrogem'));
                $refund = false;
            }
            //hoàn tiền cho khách nếu đủ điều kiện refund
            if($refund==true){

                $userTransaction = User::where('id',$order->author_id)->lockForUpdate()->firstOrFail();



                if($order->price == 0 || $order->price == ""){
                    if($order->idkey == 'nrogem'){
                        $userTransaction->gem_num = $userTransaction->gem_num + $order->price_base;
                    }
                    if($order->idkey == 'nrocoin'){
                        $userTransaction->coin_num = $userTransaction->coin_num + $order->price_base;
                    }
                    if($order->idkey == 'ninjaxu')
                    {
                        $userTransaction->xu_num = $userTransaction->xu_num + $order->price_base;
                    }
                }
                else
                {
                    $userTransaction->balance = $userTransaction->balance + $order->price;
                    $userTransaction->balance_in_refund = $userTransaction->balance_in_refund + $order->price;
                }
                $userTransaction->save();

                //tạo tnxs
                $txns = Txns::create([
                    'trade_type' => 'refund',//Hoàn tiền
                    'is_add' => '1',//Công tiền
                    'user_id' => $userTransaction->id,
                    'amount' => $order->price,
                    'real_received_amount' => $order->price,
                    'last_balance' => $userTransaction->balance,
                    'description' => 'Hoàn tiền giao dịch lỗi dich vụ #' . $order->id .'('.$order->title.')',
                    'ref_id' => $order->id,
                    'ip' => $request->getClientIp(),
                    'status' => 1
                ]);

            }

            //fix checklogin của toàn
            if(strtolower($request->get('status'))=="dalogin"){

                if((strpos($data->process, 'dalogin') !== false)){

                }
                else{
                    $data->status = $request->get('status');
                }
            }
            else{
                $data->status = $request->get('status');
            }

            //lưu thông tin c_truoc và c_sau
            if(strtolower($request->get('status'))=="dachuyenngoc" || strtolower($request->get('status'))=="loichuyenngoc" ){

                $data->c_truoc = $request->get('c_truoc');
                $data->c_sau = $request->get('c_sau');
            }
            //lưu thông tin item_info
            if(strtolower($request->get('status'))=="dachuyenitem"){

                $data->info_item = $request->get('info_item');

            }

            $data->process =  $data->process.$request->get('status')."|";
            if ($request->filled('uname')) {
                $data->uname = $request->get('uname');
            }
            if ($request->filled('item')) {
                $data->item = $request->get('item');
            }
            $data->save();
            DB::commit();

            //callback trả shop
            if($order->status==4 || $order->status==5 || $order->status==6 ){

                if($order->url!=""){
                    $this->callbackToShop($order,$request->get('status'));
                }
            }


            return "[ok]";
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return "Lỗi bán ngọc:".$e->getMessage();
        }
    }

    public function Update(Request $request)
    {

        if(!in_array($request->getClientIp(),$this->ip_array) ){
            //return "IP not allowed";
        }

        if($request->secretkey != $this->secretkey){
            return "không được truy cập!";
        }
        //HelpChangeGate::SLACK_CALL("test".Cache::get('cothebao'));
        if(Cache::get('cothebao') != "true" && $request->get('gem')<2000){
            Cache::put('cothebao', 'true', 15);
            Helpers::TelegramNotify("[" . Carbon::now() . "] " . $request->getHost() . ": Server" . $request->get('server') . " Số ngọc của bot < 2000", config('telegram.bots.mybot.channel_ban_ngoc'));
        }
        $data = Nrogem_AccBan::where('server', $request->get('server'))->where('ver',$request->ver)->first();
        $result = "";
        if (!empty($data)) {
            $data->uname = $request->get('uname');
            $data->gem = $request->get('gem');
            $data->item = $request->get('items');
            $data->updated_at = Carbon::now();
            $data->save();
            $result = "[status]" . $data->status . "[/status]\n";
            $result .= "[action_status]" . $data->action_status . "[/action_status]\n";
        }
        return $result;
    }

    //giao dịch mua

    public function getGiaoDich(Request $request)
    {

        if(!in_array($request->getClientIp(),$this->ip_array) ){
            //return "IP not allowed";
        }

        if($request->secretkey != $this->secretkey){
            return "không được truy cập!";
        }


        // Start transaction!
        DB::beginTransaction();
        try {
            $data = Nrogem_GiaoDich::where('server', $request->get('server'))
                ->where(function ($query) {
                    $query->orWhere('status', 'chualogin');
                    $query->orWhere('status', 'dalogin');
                })
                ->where('ver', $request->ver)
                ->whereHas('order', function ($query) use ($request) {
                    $query->whereIn('status', [1, 2]);
                })
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->first();

            $dataBot= Nrogem_AccBan::where('server', $request->get('server'))->where('ver', $request->ver)->first();

            if ($data && $dataBot) {

                //update khóa edit
                $order = Order::where('module', '=', config('module.service-purchase.key'))
                    ->where('id', $data->order_id)->lockForUpdate()->firstOrFail();
                $order->expired_lock = Carbon::now()->addMinutes(5);
                $order->save();

                DB::commit();

                $result = "[id]" . $data->id . "[/id]\n";
                $result .= "[acc]" . $data->acc . "[/acc]\n";
                $result .= "[pass]" . $data->pass . "[/pass]\n";
                $result .= "[gem]" . $data->gem . "[/gem]\n";
                $result .= "[status]" . $data->status . "[/status]\n";
                $result .= "[uname]" . $data->uname . "[/uname]\n";
                $result .= "[uname_bot]" . $dataBot->uname . "[/uname_bot]\n";

                return $result;
            } else {
                return "[wait]";
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return "Lỗi bán ngọc:".$e->getMessage();
        }


    }

    public function GetUname(Request $request)
    {

        if(!in_array($request->getClientIp(),$this->ip_array) ){
            //return "IP not allowed";
        }

        if($request->secretkey != $this->secretkey){
            return "không được truy cập!";
        }

        $data = Nrogem_AccBan::where('server', $request->get('server'))->get();

        $result = "";
        if (!empty($data) && count($data) > 0) {
            foreach ($data as $item) {
                $result .= "[uname]" . $item->uname . "[/uname]\n";
            }
        }
        return $result;
    }

    public function getCheck(Request $request)
    {

        if(!in_array($request->getClientIp(),$this->ip_array) ){
            //return "IP not allowed";
        }

        if($request->secretkey != $this->secretkey){
            return "không được truy cập!";
        }

        $accnap = Nrogem_AccNap::where('server', $request->get('server'))
            ->where('uname', $request->get('uname'))->first();
        if ($accnap) {
            return "[true]";
        } else {
            return "[false]";
        }
    }

    public function getNap(Request $request)
    {

        if (!in_array($request->getClientIp(), $this->ip_array)) {
            //return "IP not allowed";
        }

        if ($request->secretkey != $this->secretkey) {
            return "không được truy cập!";
        }

        $khachhang = Nrogem_GiaoDich::create([
            'ver' => $request->get('ver'),
            'server' => $request->get('server'),
            'uname' => $request->get('from') . " nạp cho " . $request->get('to'),
            'gem' => $request->get('send'),
            'item' => $request->get('items'),
            'c_truoc' => $request->get('c_truoc'),
            'c_sau' => $request->get('c_sau'),
            'status' => 'danap',
        ]);
        return "[ok]";
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
