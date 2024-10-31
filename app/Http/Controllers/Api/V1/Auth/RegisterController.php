<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Shop;
use Validator;
use Carbon\Carbon;
use Cache;
use JWTAuth;
use App\Library\Helpers;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\ActivityLog;
use App\Models\PlusMoney;
use App\Models\TxnsVp;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Database\QueryException;
use Log;

class RegisterController extends Controller
{
    public function register(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|min:4|max:30|regex:/^([A-Za-z0-9])+$/i',
                'password' => 'required|min:6|max:32|string|min:6|confirmed',
                'secret_key' => 'required',
                'domain' => 'required',
            ],[
                'username.required' => __("Tài khoản không được để trống."),
                'username.min' => __("Tên tài khoản ít nhất 4 ký tự."),
                'username.max' => __("Tên tài khoản không quá 30 ký tự."),
                'username.regex'	=> __('Tên tài khoản không ký tự đặc biệt.'),
                'password.required' => __('Vui lòng nhập mật khẩu'),
                'password.min'		=> __('Mật khẩu phải ít nhất 6 ký tự.'),
                'password.max'		=> __('Mật khẩu không vượt quá 32 ký tự.'),
                'password.confirmed' => __('Mật khẩu xác nhận không đúng'),
                'secret_key.required' => __("Yêu cầu tham số secret_key và têndomain."),
                'domain.required' => __("Yêu cầu tham số secret_key và domain."),
            ]);
            if($validator->fails()){
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'status' => 0
                ],400);
            }
            $shop = Shop::where('secret_key',$request->secret_key)->where('domain',$request->domain)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => 'Domain chưa được đăng kí',
                    'status' => 0,
                ], 200);
            }
            $username = $request->username;
            $password = $request->password;
            // kiểm tra username
            $checkUsername = User::where('shop_id',$shop->id)->where('username',$username)->first();
            if($checkUsername){
                return response()->json([
                    'message' => 'Tên tài khoản đã được đăng kí',
                    'status' => 0,
                ], 200);
            }
            if($shop->is_get_data == 1){
                // trường hợp tài khoản chưa có trên hệ thống
                // gọi API lên để lấy thông tin
                $data_shop_user = $this->getDataUser($username,$password,$shop);
                if(empty($data_shop_user)){
                    return response()->json([
                        'message' => __('Quá trình xử lý dữ liệu người dùng bị lỗi.'),
                        'status' => 0
                    ], 401); 
                }
                if($data_shop_user->status == 999){
                    return response()->json([
                        'message' => __('Quá trình xử lý dữ liệu người dùng bị lỗi.'),
                        'status' => 0
                    ], 401); 
                }
                if($data_shop_user->status == 1){
                    return response()->json([
                        'message' => __('Tên tài khoản đã được đăng kí.'),
                        'status' => 0
                    ], 401); 
                }
                elseif($data_shop_user->status == 2){
                    return response()->json([
                        'message' => __('Tên tài khoản đã được đăng kí.'),
                        'status' => 0
                    ], 401); 
                }
            }
            $user = User::create([
                'username' => $username,
                'password' => Hash::make($request->password),
                'account_type' => 2,
                'shop_id' => $shop->id,
                'utm_source' => $request->utm_source??'',
                'utm_campain' => $request->utm_campain??'',
                'status' => 1,
            ]);
            ActivityLog::add($request,"Đăng nhập frontend thành công");
            $token = JWTAuth::fromUser($user);
            $data = User::where('id',$user->id)->where('status',1)->select('id','username','email','fullname','balance')->first();
            return response()->json([
                'message' => 'Đăng kí tài khoản thành công.',
                'status' => 1,
                'token' => $token,
                'user' => $data,
                'exp_token' => config('jwt.ttl') * 60,
            ], 200);
        }
        catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                return response()->json([
                    'message' => 'Tên tài khoản đã được đăng kí',
                    'status' => 0
                ], 200);
            }
            return response()->json([
                'message' => 'Lỗi hệ thống.',
                'status' => -1
            ], 500);
        }
    }
    function getDataUser($username,$password,$shop,$loop=0){
        try{
            $resultChange = new \stdClass();
            $data = array();
            $url = $shop->url_get_data;
            $data ['secretkey'] = 'seHEDe2fyfQSed46112';
            $data ['username'] = $shop->domain.'_'.$username;
            $data ['password'] = $password;
            if(is_array($data)){
                $dataPost = http_build_query($data);
            }else{
                $dataPost = $data;
            }
            $url = $url.'?'.$dataPost;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, []);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            $resultRaw = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $path = storage_path() ."/logs/get-user/".$shop->domain."/";
            if(!\File::exists($path)){
                \File::makeDirectory($path, $mode = "0755", true, true);
            }
            $txt = Carbon::now()." : [" .$httpcode."] :  [username: ".$username."] : ".$resultRaw;
            \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");
            if($httpcode != 200){
                Log::error($resultRaw);
                $loop ++;
                if($loop < 3){
                    return $this->getDataUser($username,$password,$shop,$loop);
                }
                else{
                    $resultChange->status == 999;
                    $resultChange->httpcode = $httpcode;
                    $resultChange->message = "Dữ liệu không thể được xử lý.Vui lòng báo QTV để kịp thời xử lý";
                    return $resultChange;
                }
            }
            else{
                if($httpcode === 200){
                    $result = json_decode($resultRaw);
                    if(empty($result)){
                        $resultChange->status == 999;
                        $resultChange->httpcode = $httpcode;
                        $resultChange->message = "Không xử lý được dữ liệu trả về. Vùi lòng báo QTV để kịp thời xử lý";
                        return $resultChange;
                    }
                    else{
                        if($result->status == 1){
                            $resultChange->status = 1;
                            $resultChange->httpcode = $httpcode;
                            $resultChange->data_user = $result->data;
                            $resultChange->message = "Thành công";
                            return $resultChange;
                        }
                        elseif($result->status == 2){
                            $resultChange->status = 2;
                            $resultChange->httpcode = $httpcode;
                            $resultChange->data_user = $result->data;
                            $resultChange->message = "Tài khoản đã được đăng kí";
                            return $resultChange;
                        }
                        else{
                            $resultChange->status = 0;
                            $resultChange->httpcode = $httpcode;
                            $resultChange->data_user = $result->data;
                            $resultChange->message = "Không tìm thấy thông tin người dùng";
                            return $resultChange;
                        }
                    }
                }
                else{
                    $resultChange->status == 999;
                    $resultChange->httpcode = $httpcode;
                    $resultChange->message = "Dữ liệu không thể được xử lý.Vui lòng báo QTV để kịp thời xử lý";
                    return $resultChange;
                }
            }
            return $resultChange;
        }catch(\Exception $e){
            Log::error($e);
            return null;
        }
    }
}
