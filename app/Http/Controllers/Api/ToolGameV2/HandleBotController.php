<?php

namespace App\Http\Controllers\Api\ToolGameV2;
use App\Http\Controllers\Controller;
use App\Models\Toolgame_Account;
use App\Models\Toolgame_Config;
use App\Models\Toolgame_Order;
use Illuminate\Http\Request;



class HandleBotController extends Controller
{

    private $secretkey = "234jhjfj33333%@sss";
    private $ip_array = ['45.118.145.145', '103.237.144.44'];

    private $module=null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {



        if (!in_array($request->getClientIp(), $this->ip_array)) {
            //return "IP not allowed";
        }

        if ($request->secretkey != $this->secretkey) {
            return "không được truy cập!";
        }




    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    //"Lấy danh sách tất cả tài khoản trên web
    //chỉ lấy những tài khoản đang bật và không bị lỗi (sai mật khẩu, không có nhân vật,...)"
    public function getAccount(Request $request,$module)
    {


        $dataAccount = Toolgame_Account::with('shop')
            ->with(['config' => function ($q) {
                $q->with('autoChat');
            }])
            ->where('module', $module)
            ->get();
        $dataAccount=$dataAccount->each(function ($item){

            return $item->password=$this->encrypted($item->password);
        });

        return $dataAccount;

    }

    //"Cập nhật thông tin tài khoản, thông tin sẽ là danh sách tài khoản kèm thông tin được cập nhật
    //Chú ý: danh sách bot update lên có thể k đủ tất cả các bot của web, nhưng danh sách bot trả về phải là tất cả bot (bot được cập nhật + bot không được cập nhật)"
    public function postAccount(Request $request,$module)
    {


        $input = $request->all();
        foreach ($input ?? [] as $item) {

            $id = $item['id'] ?? "";
            $info = $item['info'] ?? "";


            $account = Toolgame_Account::where('id', $id)
                ->where('module', $module)
                ->first();

            if ($account) {

                $account->info=$info;
                $account->save();


            }
        }
        $dataAccount = Toolgame_Account::with('shop')
            ->with(['config' => function ($q) {
                $q->with('autoChat');
            }])->get();
        $dataAccount=$dataAccount->each(function ($item){

            return $item->password=$this->encrypted($item->password);
        });
        return $dataAccount;
    }


    //Cập nhật thông tin 1 tài khoản, cập nhật này chỉ để cập nhật trạng thái
    public function putAccount(Request $request,$module)
    {

        $module = "nrocoin";
        $input = $request->all();
        $id = $input['id']??"";
        $status = $input['status']??"";

        $account=Toolgame_Account::where('id', $id)
            ->where('module', $request->module)
            ->first();
        if(!$account){
            return response()->json([
                'success'=>false,
                'message'=>"Không tìm thấy tài khoản"
            ]);
        }
        //update trạng thái bot
        $account->update([
            'status'=>$status
        ]);
        return response()->json([
            'success'=>true,
            'message'=>"Thành công"
        ]);
    }


    //"Kiểm tra và lấy thông tin của đơn chưa giao dịch
    //Lưu ý: Mỗi lần kiểm tra này sẽ không cho sửa thông tin đơn trên web trong vòng 5p"
    public function getOrder(Request $request,$module){

        $server=$request->get('server');
        $charname=$request->get('charname');

        $order=Toolgame_Order::with('web_order')->where('server',$server)
            ->where('charname',$charname)
            ->where('module', $module)
            ->first();

        if($order){
            return response()->json([
                'success'=>true,
                'message'=>"Thành công",
                'order'=>[
                    'id'=>$order->id,
                    'coin'=>$order->coin,
                    'coin'=>$order->coin,
                    'name'=>$order->charname,
                ]

            ]);
        }
        else{
            return response()->json([
                'success'=>false,
                'message'=>"Không tìm thấy đơn hàng",
                'order'=>null
            ]);
        }

    }

    //Cập nhật trạng thái thành công cho đơn
    public function putOrder(Request $request,$module){

        $input=$request->all();
        $id=$input['id']??"";
        $data=$input['data']??"";
        $bot_id=$input['bot_id']??"";

        $order=Toolgame_Order::where('id',$id)
            ->where('module', $module)
            ->first();

        if($order){

            $order->bot_id=$bot_id;
            $order->trans_type=-1;//Giao dịch đơn
            $order->data=$data;
            $order->save();
            return response()->json([
                'success'=>true,
                'message'=>"Thành công",
            ]);
        }
        else{
            return response()->json([
                'success'=>false,
                'message'=>"Không tìm thấy đơn hàng",
                'order'=>null
            ]);
        }

    }


    //Gửi lên thông tin về giao dịch của mỗi lần giao dịch, để thống kế hoặc đối chiếu dữ liệu
    public function postTransaction(Request $request,$module){

        $input=$request->all();
        $bot_id=$input['bot_id']??"";
        $transType=$input['transType']??"";
        Toolgame_Order::create([
            'bot_id'=>$bot_id,
            'data'=>$input,
            'trans_type'=>$transType,
            'module'=> $module

        ]);
        return response()->json([
            'success'=>true,
            'message'=>"Thành công",
        ]);



    }





    function encrypted($plaintext){

        $key = 'z86nDPtWC3jQLTWa14FvVQWjvZBj80Ti';
        $method = 'aes-256-cbc';

        $key = hash('sha256', $key, true);

        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);

        return $encrypted = base64_encode(openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv));
    }

    function decrypted($plaintext){

        $key = 'z86nDPtWC3jQLTWa14FvVQWjvZBj80Ti';
        $method = 'aes-256-cbc';
        $key = hash('sha256', $key, true);

        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);

        $encrypted = base64_encode(openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv));

        return $decrypted = openssl_decrypt(base64_decode($encrypted), $method, $key, OPENSSL_RAW_DATA, $iv);
    }



}
