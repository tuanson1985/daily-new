<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Shop;
use App\Library\Helpers;

class ClientSwitchController extends Controller
{
    public function ClientSwitch(Request $request){
        if(auth()->user()->account_type != 1){
            return response()->json([
                'message'=>__('Bạn không được phép sử dụng thao tác này'),
                'status'=> 0,
                'redirect' => false,
            ]);
        }
        if(isset($request->id)){
            $id = $request->id;
            $data =  Shop::where('id',$id)->first();
            if(!$data){
                return response()->json([
                    'message'=>__('Không tìm thấy shop'),
                    'status'=> 0,
                    'redirect' => false,
                ]);
            }
            // Session::put('shop_id',$data->id);
            session()->put('shop_id', $data->id);
            session()->put('shop_name', $data->domain);
            ActivityLog::add($request, 'Thay đổi truy cập shop #'.$data->id);
        }
        else{
            session()->forget('shop_id');
            session()->forget('shop_name');
        }
        return response()->json([
            'message'=>__('Thành công, đang chuyển hướng'),
            'status'=> 1,
            'redirect' =>  route('admin.index'),
        ]);
    }
    public function DeleteCache(Request $request){
        if(auth()->user()->account_type != 1){
            return redirect()->back()->withErrors(__('Bạn không có quyền thực hiện thao tác này'));
        }
        $shop_id = session()->get('shop_id');
        $shop =  Shop::where('id',$shop_id)->first();
        if(!$shop){
            return redirect()->back()->withErrors(__('Shop yêu cầu không hợp lệ'));
        }
        $url = 'https://'.$shop->domain.'/api/clear-cache';
        $data = array();
        $data['secret_key'] = $shop->secret_key;
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
        $result = json_decode($resultRaw);
        if($result && $result->status == 1){
            return redirect()->back()->with('success',__('Thành công !'));
        }
        return redirect()->back()->withErrors(__('Yêu cầu xóa cache không thực hiện được'));
    }
}
