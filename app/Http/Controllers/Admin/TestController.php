<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\ChargeGameGateway\RobloxGate;
use App\Models\OrderDetail;
use App\Models\Roblox_Bot;
use App\Models\Roblox_Order;
use App\Models\ServiceAccess;
use Auth;
use File;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\SocialAccount;
use App\Models\Txns;
use App\Models\Charge;
use App\Models\Order;
use App\Models\PlusMoney;
use App\Models\Shop;
use App\Models\StoreCard;
use App\Models\Item;
use Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Library\Report;
use App\Library\Helpers;
use DB;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        if(Auth::user()->id == 301 || Auth::user()->id == 5551){
            $cookies = "GuestData=UserID=-262276524; RBXcb=RBXViralAcquisition=true&RBXSource=true&GoogleAnalytics=true; RBXSource=rbx_acquisition_time=10/28/2024 01:18:14&rbx_acquisition_referrer=https://www.roblox.com/Login&rbx_medium=Social&rbx_source=www.roblox.com&rbx_campaign=&rbx_adgroup=&rbx_keyword=&rbx_matchtype=&rbx_send_info=0; __utmc=200924205; __utmz=200924205.1730078445.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); rbx-ip2=1; __utma=200924205.723117565.1730078445.1730087160.1730105952.4; __utmb=200924205.0.10.1730105952; .ROBLOSECURITY=_|WARNING:-DO-NOT-SHARE-THIS.--Sharing-this-will-allow-someone-to-log-in-as-you-and-to-steal-your-ROBUX-and-items.|_FB0F51A15A5250FDF43ADD0B3D51E505B30E7227FA85705BF7CC92500C9533DEE6435FB84D1C8BAED1866968C1D369A051A514A77C614ADB2DA7B83E459E6894247E6AD3F5F2E04947E37B300DCDFF02E0EEA7B939FC5848F5ABCE2A327C872CEBEB2214894B9BD81D8C90B57F26FCD3F4A613A1D93F54DD5B01CFF44D0F34BA6B10443BEEE490C171F7B29A07B7B55D02063EF7FEFD80FA7ABA6B67FB73B77111BA2A84C6A32AB3B30D392A63FB469AA3893160C822127958B31ABCC795D7D8B51719410510B6172B932D6248A23A742CC04F79C493383EFD6D5212231DB9BF75684731EE7D6B5C9FFB33AF7283F5EB891F4D1AECEA01487AE0BDD70574E161C67D94E0D0516B851125CE3DFB0EFD494CFEFC79D0492E2E1FC61C9C53D1B3C122DF987ED591BDF458406C8499EB6EEC755846AFAA54B5F5FDD4CF72684475104B3DB3D7D002CABEAE97D26C547A2B6D1FC297AC29B9628AA926D1FA03276A69F40CF4C0C814F920ED4A2879B0EA9E5D12C93195BAAAAFF10919AD1C8D3722557134C95CD4589AA73F35D3BF70BA5632001513F39E21A4BBE886ACEB3FE615E648F25DCD066ED60E7DC34D5C180675D511FBC7B92DBBA29A101B096847E5C5EA15558E6F52A81EA63024159D7B1C252DD79812A596333120E3D6BE14AD0CFB4D87C67F4F390F72E90C7A1B22A6299D58106147957BB3EF588687562E7AF205A2577E07E4B50957226E9D6971C905FCDF96DB48EB3360F8EB19FB7682F3D344A6D69043D2D714E1B0951A38EE2FC59A1F73BC10CA63D3ABAFF1E57B327C0FF754CC8ED9CD24B02907308ACDBF7B70139E83DFE997E2838E62871D79427380ABE1FC4D77BBB7BCC3A217CF0BE6FE43C5C670A08D4E4CE48BF45E9202A236AC88106AD08E75C03C0B7199C4C606DA0AD1994CCA18C9D2A13F9468731811F3145DBE61922D1F74D9E12691197B8A46C613B851F9753E148A47B26426B2A80901A7D0B9FFD1B2DF727CF0654ECF79751218301DC65A5A2A119EB409A4830C34C175FBF292E2580490D92DE9DCE029D45BEB10F2A8393A26D3FF16F5F1893FB209B0FA07F40586CED02035F9D6A7834DA7B3F8FCA4C4A2278F360A5B7C11E4E91D35C57B304D8B; rbxas=631dee6dbd41dbe669d5e7e45ee98c4f5b31514b2c4245223c8cf5a47d86687d; RBXEventTrackerV2=CreateDate=10/28/2024 03:59:26&rbxid=7513915776&browserid=1730078286510008; RBXSessionTracker=sessionid=0e58e357-e103-457b-92bb-aa1ae155c44a";
            $username = "DcmLJN9195";
//            $placeId = 16373825184;
            $product_id = 2366727674;
            $sell_id = 5569619618;
            $result = RobloxGate::ProcessBuyGamePassNew($username,6,$cookies,1542451254121541,null);
            return $result;
            $content = "https://daily.tichhop.pro/admin/service-purchase-auto/";
            $new_content = ""; // Chuỗi trống

            $order_details = OrderDetail::query()
                ->Where('content', 'LIKE', '%' . $content . '%')
                ->where('module',config('module.service-workflow.key'))
                ->whereHas('order',function ($q){
                    $q->where('idkey',"robux_premium_auto")->where('status',5);
                })
                ->where('status',5)
                ->get();

            foreach ($order_details as $order_detail){
                $od_content = $order_detail->content;
                $order_detail->content = str_replace($content, $new_content, $od_content);
                $order_detail->save(); // Lưu thay đổi
            }

            return $order_details;
        }

        return 111111;

    }

    public function getUname(Request $request,$uname)
    {
        if(Auth::user()->id == 301 || Auth::user()->id == 5551){

            $total = 100;

//check xem có đúng link mua server ko
            $aBot = Roblox_Bot::where('status',6)
                ->where('account_type',1)
                ->orderBy('ver','asc')
                ->first();

            $result = RobloxGate::detectUserId($uname,$total,null,$aBot->cookies??'');

            return $result;
        }

        return 111111;

    }

    public function getPlaceId(Request $request,$user_id)
    {

        if(Auth::user()->id == 301 || Auth::user()->id == 5551){

//            $uname = 'KSTtiekn159pro';
            $total = 100;

//check xem có đúng link mua server ko
            $aBot = Roblox_Bot::where('status',6)
                ->where('account_type',1)
                ->orderBy('ver','asc')
                ->first();
            $result = RobloxGate::detectPlaceId($user_id,$total,null,$aBot->cookies??'');

            return $result;
        }

        return 111111111;
    }

    public function getGamepass(Request $request,$place_id)
    {

        if(Auth::user()->id == 301 || Auth::user()->id == 5551){

//            $uname = 'KSTtiekn159pro';
            $total = 100;

//check xem có đúng link mua server ko
            $aBot = Roblox_Bot::where('status',6)
                ->where('account_type',1)
                ->orderBy('ver','asc')
                ->first();
            $result = RobloxGate::detectGamepass($place_id,$total,null,$aBot->cookies??'');

            return $result;
        }

        return 111111111;
    }

    public function callbackToShop(Order $order,$message,$refund = null,$mistake_by = null)
    {

        $url = $order->url;

        $data = array();

        $data['status'] = $order->status;

        if (isset($refund)){
            $data['refund'] = $refund;
        }

        $data['message'] = $message;

        if (isset($mistake_by)){
            $data['mistake_by'] = $mistake_by;
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
                $path = storage_path() ."/logs/curl_callback-service-to-shop-".Carbon::now()->format('Y-m-d');
                if(!\File::exists($path)){
                    \File::makeDirectory($path, $mode = "0755", true, true);
                }
                $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
                \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");

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

    public function callbackToShopRoblox(Order $order,$statusBot,$message = '',$image = false)
    {

        $url = $order->url;

        $data = array();

        $data['status'] = $order->status;

        $data['message'] = $statusBot;

        $data['message_daily'] = $message;

        $data['price'] = $order->price;

        $data['price_base'] = $order->price_base;

        $data['image'] = $image;

        $data['input_auto'] = 1;

        if ($order->status ==4){
            $data['process_at'] = strtotime($order->process_at);
        }

        try{

            for ($i=0;$i<3;$i++){
                if(is_array($data)){
                    $dataPost = http_build_query($data);
                }else{
                    $dataPost = $data;
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
                $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                curl_setopt($ch, CURLOPT_REFERER, $actual_link);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                $resultRaw = curl_exec($ch);
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
        catch (\Exception $e){
            \Log::error($e);
        }

    }

}
