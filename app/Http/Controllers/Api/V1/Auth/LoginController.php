<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;
use Log;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\SocialAccount;
use App\Models\PlusMoney;
use App\Models\TxnsVp;
use Cache;
use JWTAuth;
use App\Library\Helpers;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class LoginController extends Controller
{
    public function veryShop(Request $request){
        try {

            $validator = Validator::make($request->all(), [
                'domain' => 'required',
            ],[
                'domain.required' => __("Yêu cầu tham số domain."),
            ]);
            if($validator->fails()){
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'status' => 0
                ],422);
            }
            $secret_key = null;
            $shop = Shop::where('domain',$request->domain)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => 'Domain chưa được đăng kí',
                    'status' => 0,
                ], 200);
            }
            if($request->filled('secret_key_default')){
                $config_key_default = config('module.shop.secret_client_backup');
                $secret_key_default = Helpers::Decrypt($request->get('secret_key_default'),config('module.shop.secret_key_client'));
                $secret_key_default = explode(',',$secret_key_default);
                if(empty($secret_key_default[0])){
                    return response()->json([
                        'message' => 'Dữ liệu không hợp lệ.',
                        'status' => 0,
                    ], 200);
                }
                if($secret_key_default[0] != $config_key_default){
                    return response()->json([
                        'message' => 'Dữ liệu không hợp lệ.',
                        'status' => 0,
                    ], 200);
                }
                $secret_key = $shop->secret_key;
            }
            else{
                if($shop->secret_key != $request->get('secret_key')){
                    return response()->json([
                        'message' => 'Domain chưa được đăng kí',
                        'status' => 0,
                    ], 200);
                }
            }
            return response()->json([
                'message' => 'Domain hợp lệ',
                'status' => 1,
                'secret_key' => $secret_key,
            ], 200);
        }
        catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => 'Lỗi hệ thống.',
                'status' => -1
            ], 500);
        }
    }
    public function login(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
                'secret_key' => 'required',
                'domain' => 'required',
            ],[
                'username.required' => __("username không được để trống."),
                'password.required' => __("Bạn chưa mật khẩu."),
                'secret_key.required' => __("Yêu cầu tham số secret_key và tên domain."),
                'domain.required' => __("Yêu cầu tham số secret_key và domain."),
            ]);
            if($validator->fails()){
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'status' => 0
                ],422);
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
            // trường hợp shop này có yêu cầu lấy dữ liệu từ điểm bán cũ
            if($shop->is_get_data === 1){
                $user = User::where('username',$username)
                    ->where('status', 1)
                    ->where('shop_id', $request->shop_id)
                    ->where('account_type', 2)
                    ->select('id','username','email','fullname','balance','password')
                    ->first();
                if(!$user){
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
                    if($data_shop_user->status == 0){
                        return response()->json([
                            'message' => __('Tài khoản hoặc mật khẩu không đúng.'),
                            'status' => 0
                        ], 401);
                    }
                    if($data_shop_user->status == 1){
                        $data_user = $data_shop_user->data_user;
                        $user = User::create([
                            'username' => $username,
                            'password' => Hash::make($request->password),
                            'account_type' => 2,
                            'shop_id' => $shop->id,
                            'balance' => $data_user->balance,
                            'balance_in' => $data_user->balance,
                            'ruby_num1' => $data_user->ruby_num1,
                            'ruby_num2' => $data_user->ruby_num2,
                            'ruby_num3' => $data_user->ruby_num3,
                            'ruby_num4' => $data_user->ruby_num4,
                            'ruby_num5' => $data_user->ruby_num5,
                            'ruby_num6' => $data_user->ruby_num6,
                            'ruby_num7' => $data_user->ruby_num7,
                            'ruby_num8' => $data_user->ruby_num8,
                            'ruby_num9' => $data_user->ruby_num9,
                            'ruby_num10' => $data_user->ruby_num10,
                            'gem_num' => $data_user->gem_num,
                            'coin_num' => $data_user->coin_num,
                            'xu_num' => $data_user->xu_num,
                            'status' => 1,
                        ]);
                        if($data_user->balance > 0){
                            PlusMoney::create([
                                'user_id'=>$user->id,
                                'shop_id' => $shop->id,
                                'is_add'=>'1',//Cộng tiền
                                'amount'=>$data_user->balance,
                                'source_type'=>'',
                                'source_bank'=>'',
                                'processor_id'=>1,
                                'description'=>"He thong cong tien chuyen doi du lieu nguoi dung.",
                                'status'=>1,

                            ])->txns()->create([
                                'user_id'=>$user->id,
                                'shop_id' => $shop->id,
                                'trade_type'=>'plus_money', //cộng tiền
                                'is_add'=>'1',//Cộng tiền
                                'amount'=>$data_user->balance,
                                'last_balance'=>$user->balance,
                                'description'=>'He thong cong tien chuyen doi du lieu nguoi dung '.' [ +'.currency_format($data_user->balance).' ]',
                                'ip'=>$request->getClientIp(),
                                'status'=>1
                            ]);
                        }
                        $this->logTxnsVp($user);
                        ActivityLog::add($request,"Đăng nhập frontend thành công");
                        Cache::put('last_login', Carbon::now(), 1440);
                        $token = JWTAuth::fromUser($user);
                        return response()->json([
                            'message' => 'Đăng nhập thành công.',
                            'status' => 1,
                            'token' => $token,
                            'token_type' => 'bearer',
                            'refresh_token' => null,
                            'exp_token' => config('jwt.ttl') * 60,
                            'user' => $user,
                        ], 200);
                    }
                }
                if ($user && \Hash::check($request->password, $user->password)) {
                    $refresh_token = null;
                    Cache::put('last_login', Carbon::now(), 1440);
                    ActivityLog::add($request,"Đăng nhập frontend thành công");
                    $token = JWTAuth::fromUser($user);
                    // trường hợp có yêu cầu nhớ mật khẩu
                    if($request->filled('remember_token')){
                        $refresh_token = Helpers::Encrypt($token.time(),config('jwt.secret'));
                        $exp_token_refresh = Carbon::now()->addMinutes(config('jwt.refresh_ttl'));
                        $user->refresh_token = $refresh_token;
                        $user->exp_token_refresh = $exp_token_refresh;
                        $user->save();
                    }
                    return response()->json([
                        'message' => 'Đăng nhập thành công.',
                        'status' => 1,
                        'token' => $token,
                        'token_type' => 'bearer',
                        'refresh_token' => $refresh_token,
                        'exp_token' => config('jwt.ttl') * 60,
                        'user' => $user,
                    ], 200);
                }
                else{
                    return response()->json([
                        'message' => __('Tài khoản hoặc mật khẩu không đúng.'),
                        'status' => 0
                    ], 401);
                }
            }
            else{
                $user = User::where('username',$username)
                    ->where('status', 1)
                    ->where('shop_id', $request->shop_id)
                    ->where('account_type', 2)
                    ->select('id','username','email','fullname','balance','password')
                    ->first();
                if(!$user){
                    return response()->json([
                        'message' => __('Tài khoản hoặc mật khẩu không đúng.'),
                        'status' => 0
                    ], 401);
                }
                if ($user && \Hash::check($request->password, $user->password)) {
                    $refresh_token = null;
                    Cache::put('last_login', Carbon::now(), 1440);
                    ActivityLog::add($request,"Đăng nhập frontend thành công");
                    $token = JWTAuth::fromUser($user);
                    // trường hợp có yêu cầu nhớ mật khẩu
                    if($request->filled('remember_token')){
                        $refresh_token = Helpers::Encrypt($token.time(),config('jwt.secret'));
                        $exp_token_refresh = Carbon::now()->addMinutes(config('jwt.refresh_ttl'));
                        $user->refresh_token = $refresh_token;
                        $user->exp_token_refresh = $exp_token_refresh;
                        $user->save();
                    }
                    return response()->json([
                        'message' => 'Đăng nhập thành công.',
                        'status' => 1,
                        'token' => $token,
                        'token_type' => 'bearer',
                        'refresh_token' => $refresh_token,
                        'exp_token' => config('jwt.ttl') * 60,
                        'user' => $user,
                    ], 200);
                }
                else{
                    return response()->json([
                        'message' => __('Tài khoản hoặc mật khẩu không đúng.'),
                        'status' => 0
                    ], 401);
                }
            }
        }
        catch (JWTException $e) {
            return response()->json([
                'message' => 'Lỗi hệ thống.',
                'status' => -1
            ], 500);
        }
    }

    public function logout(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'secret_key' => 'required',
                'domain' => 'required',
            ],[
                'token.required' => __("token không được để trống."),
                'secret_key.required' => __("Yêu cầu tham số secret_key và tên domain."),
                'domain.required' => __("Yêu cầu tham số secret_key và domain."),
            ]);
            if($validator->fails()){
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'status' => 0
                ],422);
            }
            $shop = Shop::where('secret_key',$request->secret_key)->where('domain',$request->domain)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => 'Domain chưa được đăng kí',
                    'status' => 0,
                ], 200);
            }
            $user = Auth::guard('api')->user();
            if(!$user){
                return response()->json([
                    'message' => __('Tài khoản hoặc mật khẩu không đúng.'),
                    'status' => 0
                ], 401);
            }
            $user = User::where('id',$user->id)->first();
            $user->refresh_token = null;
            $user->exp_token_refresh = null;
            $user->save();
            JWTAuth::invalidate($request->token);
            ActivityLog::add($request,"Đăng xuất frontend thành công");
            return response()->json([
                'message' => 'Đăng xuất thành công.',
                'status' => 1
            ], 200);
        }
        catch (JWTException $e) {
            return response()->json([
                'message' => 'Lỗi hệ thống.',
                'status' => -1
            ], 500);
        }
    }

    public function refreshTokenRemember(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'refresh_token' => 'required',
                'secret_key' => 'required',
                'domain' => 'required',
            ],[
                'refresh_token.required' => __("refresh_token không được để trống."),
                'secret_key.required' => __("Yêu cầu tham số secret_key và tên domain."),
                'domain.required' => __("Yêu cầu tham số secret_key và domain."),
            ]);
            if($validator->fails()){
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'status' => 0
                ],422);
            }
            $refresh_token = $request->refresh_token;
            $time_now = Carbon::now();
            $user = User::whereNotNull('refresh_token')

                ->where('refresh_token',$refresh_token)
                ->where('status', 1)
                ->where('shop_id', $request->shop_id)
                ->where('account_type', 2)
                ->select('id','username','email','fullname','balance','exp_token_refresh')
                ->first();


            if(!$user){
                return response()->json([
                    'message' => __('Token không tồn tại trên hệ thống.'),
                    'status' => 0
                ], 200);
            }
            if(strtotime($time_now) > strtotime($user->exp_token_refresh)){
                $user->refresh_token = null;
                $user->exp_token_refresh = null;
                $user->save();
                return response()->json([
                    'message' => __('Token đã hết hiệu lực.'),
                    'status' => 0
                ], 200);
            }
            $token = JWTAuth::fromUser($user);
            return response()->json([
                'message' => 'Refresh thành công.',
                'status' => 1,
                'token' => $token,
                'exp_token' => config('jwt.ttl') * 60,
                'user' => $user,
            ], 200);
        }
        catch(TokenInvalidException $e){
            return response()->json([
                'message' => "Lỗi hệ thống.",
                'status' => -1
            ]);
        }
    }

    public function refresh_token(Request $request){
        $token = $request->token;
        try{
            $token = JWTAuth::refresh($token);
            return response()->json([
                'message' => 'Refresh thành công.',
                'status' => 1,
                'token' => $token,
                'exp_token' => config('jwt.ttl') * 60,
            ], 200);
        }
        catch(TokenInvalidException $e){
            return response()->json([
                'message' => "Lỗi hệ thống.",
                'status' => -1
            ]);
        }
    }

    public function loginfacebook(Request $request){
        try{
            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }
            $info = base64_decode(base64_decode($request->accessToken));
            $user = explode("||", $info);
            $dataLogin = $this->FindOrCreateUser($user,$shop);
            if($dataLogin === 999){
                return response()->json([
                    'message' => 'Quá trình xử lý dữ liệu người dùng bị lỗi.',
                    'status' => 0,
                ], 200);
            }
            $dataUser = User::where('username',$dataLogin->username)
            ->where('status', 1)
            ->where('shop_id', $request->shop_id)
            ->where('account_type', 2)
            ->select('id','username','email','fullname','balance')
            ->first();
            $token = JWTAuth::fromUser($dataLogin);
            ActivityLog::add($request,"Đăng nhập frontend thành công");
            return response()->json([
                'message' => 'Đăng nhập thành công.',
                'status' => 1,
                'token' => $token,
                 'user' => $dataUser,
                'exp_token' => config('jwt.ttl') * 60,
            ], 200);
        }catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }

    function FindOrCreateUser($facebookUser,$shop){
        $provider = 'facebook';
        try{
            if($shop->is_get_data === 1){
                $username = $facebookUser[0].'@facebook.com';
                $password = "geHEDed46112e2fyfQS";
                $account = SocialAccount::where('provider',$provider)->where('provider_user_id',$facebookUser[0])->where('shop_id',$shop->id)->first();
                if($account){
                    $user = User::where('id',$account->user_id)->where('shop_id',$shop->id)->where('account_type',2)->where('status',1)->first();
                    if(!$user){
                        $data_shop_user = $this->getDataUser($username,$password,$shop);
                        if(empty($data_shop_user)){
                            return 999;
                        }
                        if($data_shop_user->status == 999){
                            return 999;
                        }
                        if($data_shop_user->status == 1){
                            $data_user = $data_shop_user->data_user;
                            $user = User::create([
                                'shop_id' => $shop->id,
                                'username' => $facebookUser[0].'@facebook.com',
                                'fullname' => $facebookUser[1],
                                'provider_id' => $facebookUser[0],
                                'account_type' => 2,
                                'balance' => $data_user->balance,
                                'ruby_num1' => $data_user->ruby_num1,
                                'ruby_num2' => $data_user->ruby_num2,
                                'ruby_num3' => $data_user->ruby_num3,
                                'ruby_num4' => $data_user->ruby_num4,
                                'ruby_num5' => $data_user->ruby_num5,
                                'ruby_num6' => $data_user->ruby_num6,
                                'ruby_num7' => $data_user->ruby_num7,
                                'ruby_num8' => $data_user->ruby_num8,
                                'ruby_num9' => $data_user->ruby_num9,
                                'ruby_num10' => $data_user->ruby_num10,
                                'gem_num' => $data_user->gem_num,
                                'coin_num' => $data_user->coin_num,
                                'xu_num' => $data_user->xu_num,
                                'status' => 1,
                            ]);
                            if($data_user->balance > 0){
                                PlusMoney::create([
                                    'user_id'=>$user->id,
                                    'shop_id' => $shop->id,
                                    'is_add'=>'1',//Cộng tiền
                                    'amount'=>$data_user->balance,
                                    'source_type'=>'',
                                    'source_bank'=>'',
                                    'processor_id'=>1,
                                    'description'=>"He thong cong tien chuyen doi du lieu nguoi dung.",
                                    'status'=>1,
                                ])->txns()->create([
                                    'user_id'=>$user->id,
                                    'shop_id' => $shop->id,
                                    'trade_type'=>'plus_money', //cộng tiền
                                    'is_add'=>'1',//Cộng tiền
                                    'amount'=>$data_user->balance,
                                    'last_balance'=>$user->balance,
                                    'description'=>'He thong cong tien chuyen doi du lieu nguoi dung '.' [ +'.currency_format($data_user->balance).' ]',
                                    'ip'=> '',
                                    'status'=>1
                                ]);
                            }
                            $this->logTxnsVp($user);
                            return $user;
                        }
                        elseif($data_shop_user->status == 0){
                            $user = User::create([
                                'shop_id' => $shop->id,
                                'username' => $facebookUser[0].'@facebook.com',
                                'fullname' => $facebookUser[1],
                                'provider_id' => $facebookUser[0],
                                'account_type' => 2,
                                'status' => 1,
                            ]);
                            return $user;
                        }
                        else{
                            return 999;
                        }
                    }
                    return $user;
                }
                $data_shop_user = $this->getDataUser($username,$password,$shop);
                if(empty($data_shop_user)){
                    return 999;
                }
                if($data_shop_user->status == 999){
                    return 999;
                }
                if($data_shop_user->status == 1){
                    $data_user = $data_shop_user->data_user;
                    $user = User::create([
                        'shop_id' => $shop->id,
                        'username' => $facebookUser[0].'@facebook.com',
                        'fullname' => $facebookUser[1],
                        'provider_id' => $facebookUser[0],
                        'account_type' => 2,
                        'balance' => $data_user->balance,
                        'balance_in' => $data_user->balance,
                        'ruby_num1' => $data_user->ruby_num1,
                        'ruby_num2' => $data_user->ruby_num2,
                        'ruby_num3' => $data_user->ruby_num3,
                        'ruby_num4' => $data_user->ruby_num4,
                        'ruby_num5' => $data_user->ruby_num5,
                        'ruby_num6' => $data_user->ruby_num6,
                        'ruby_num7' => $data_user->ruby_num7,
                        'ruby_num8' => $data_user->ruby_num8,
                        'ruby_num9' => $data_user->ruby_num9,
                        'ruby_num10' => $data_user->ruby_num10,
                        'gem_num' => $data_user->gem_num,
                        'coin_num' => $data_user->coin_num,
                        'xu_num' => $data_user->xu_num,
                        'status' => 1,
                    ]);
                    if($data_user->balance > 0){
                        PlusMoney::create([
                            'user_id'=>$user->id,
                            'shop_id' => $shop->id,
                            'is_add'=>'1',//Cộng tiền
                            'amount'=>$data_user->balance,
                            'source_type'=>'',
                            'source_bank'=>'',
                            'processor_id'=>1,
                            'description'=>"He thong cong tien chuyen doi du lieu nguoi dung.",
                            'status'=>1,
                        ])->txns()->create([
                            'user_id'=>$user->id,
                            'shop_id' => $shop->id,
                            'trade_type'=>'plus_money', //cộng tiền
                            'is_add'=>'1',//Cộng tiền
                            'amount'=>$data_user->balance,
                            'last_balance'=>$user->balance,
                            'description'=>'He thong cong tien chuyen doi du lieu nguoi dung '.' [ +'.currency_format($data_user->balance).' ]',
                            'ip'=> '',
                            'status'=>1
                        ]);
                    }
                    $this->logTxnsVp($user);
                }
                elseif($data_shop_user->status == 0){
                    $user = User::create([
                        'shop_id' => $shop->id,
                        'username' => $facebookUser[0].'@facebook.com',
                        'fullname' => $facebookUser[1],
                        'provider_id' => $facebookUser[0],
                        'account_type' => 2,
                        'status' => 1,
                    ]);
                }
                else{
                    return 999;
                }
                $socialAccount= SocialAccount::create([
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                    'provider_user_id' => $facebookUser[0],
                    'provider' => $provider
                ]);
                return $user;
            }
            else{
                $account = SocialAccount::where('provider',$provider)->where('provider_user_id',$facebookUser[0])->where('shop_id',$shop->id)->first();
                if($account){
                    $user = User::where('id',$account->user_id)->where('shop_id',$shop->id)->where('account_type',2)->where('status',1)->first();
                    if(!$user){
                        $user = User::create([
                            'shop_id' => $shop->id,
                            'username' => $facebookUser[0].'@facebook.com',
                            'fullname' => $facebookUser[1],
                            'provider_id' => $facebookUser[0],
                            'account_type' => 2,
                            'status' => 1,
                        ]);
                    }
                    return $user;
                }
                $user = User::create([
                    'shop_id' => $shop->id,
                    'username' => $facebookUser[0].'@facebook.com',
                    'fullname' => $facebookUser[1],
                    'provider_id' => $facebookUser[0],
                    'account_type' => 2,
                    'status' => 1,
                ]);
                $socialAccount= SocialAccount::create([
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                    'provider_user_id' => $facebookUser[0],
                    'provider' => $provider
                ]);
                return $user;
            }
        }
        catch (\Exception $e) {
            Log::error($e);
            return 999;
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
                    $resultChange->status = 999;
                    $resultChange->httpcode = $httpcode;
                    $resultChange->message = "Dữ liệu không thể được xử lý.Vui lòng báo QTV để kịp thời xử lý";
                    return $resultChange;
                }
            }
            else{
                if($httpcode === 200){
                    $result = json_decode($resultRaw);
                    if(empty($result)){
                        $resultChange->status = 999;
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
                    $resultChange->status = 999;
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
    function logTxnsVp($user){
        if($user->gem_num > 0){
            TxnsVp::create([
                'trade_type' => 'plus_vp',
                'is_add' => '0',
                'user_id' =>  $user->id,
                'amount' => $user->gem_num,
                'last_balance' => $user->gem_num,
                'description' =>  'He thong cong vat pham gem_num chuyen doi du lieu nguoi dung '.' [ +'.currency_format($user->gem_num).' ]',
                'txnsable_type' =>  null,
                'ip' => '',
                'status' => 1,
                'shop_id' =>  $user->shop_id,
                'item_type' => 'gem_num',
            ]);
        }
        if($user->coin_num > 0){
            TxnsVp::create([
                'trade_type' => 'plus_vp',
                'is_add' => '0',
                'user_id' =>  $user->id,
                'amount' => $user->coin_num,
                'last_balance' => $user->coin_num,
                'description' =>  'He thong cong vat pham coin_num chuyen doi du lieu nguoi dung '.' [ +'.currency_format($user->coin_num).' ]',
                'txnsable_type' =>  null,
                'ip' => '',
                'status' => 1,
                'shop_id' =>  $user->shop_id,
                'item_type' => 'coin_num',
            ]);
        }
        if($user->xu_num > 0){
            TxnsVp::create([
                'trade_type' => 'plus_vp',
                'is_add' => '0',
                'user_id' =>  $user->id,
                'amount' => $user->xu_num,
                'last_balance' => $user->xu_num,
                'description' =>  'He thong cong vat pham xu_num chuyen doi du lieu nguoi dung '.' [ +'.currency_format($user->xu_num).' ]',
                'txnsable_type' =>  null,
                'ip' => '',
                'status' => 1,
                'shop_id' =>  $user->shop_id,
                'item_type' => 'xu_num',
            ]);
        }
        for($i = 1; $i <= 10; $i++){
            if($user['ruby_num'.$i] > 0){
                TxnsVp::create([
                    'trade_type' => 'plus_vp',
                    'is_add' => '0',
                    'user_id' =>  $user->id,
                    'amount' => $user['ruby_num'.$i],
                    'last_balance' => $user['ruby_num'.$i],
                    'description' =>  'He thong cong vat pham ruby_num'.$i.' chuyen doi du lieu nguoi dung '.' [ +'.currency_format($user['ruby_num'.$i]).' ]',
                    'txnsable_type' =>  null,
                    'ip' => '',
                    'status' => 1,
                    'shop_id' =>  $user->shop_id,
                    'item_type' => 'ruby_num'.$i,
                ]);
            }
        }
    }
}
