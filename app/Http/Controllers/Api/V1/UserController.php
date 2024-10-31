<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shop;
use Validator;
use Carbon\Carbon;
use Cache;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;

class UserController extends Controller
{

    public function postCheckToken(Request $request){
        try {
            // $data = auth('api')->user();
            // $data = Auth::guard('api')->user();
            $data = JWTAuth::parseToken()->authenticate();
            $user = JWTAuth::setToken($request->token)->toUser();

            // $user =
            if($data->status != 1){
                JWTAuth::invalidate($request->token);
                return response()->json([
                    'message' => __('Tài khoản của bạn đã bị vô hiệu hóa.'),
                    'status' => 0
                ], 401);
            }
            return response()->json([
                'message' => __('Dữ liệu được chấp thuận'),
                'data' => $user,
                'status' => 1
            ],200);
        }
        catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json([
                'message' => __('Trường token không hợp lệ hoặc đã hết hạn.'),
                'status' => 0
            ], 401);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json([
                'message' => __('Trường token không hợp lệ hoặc đã hết hạn.'),
                'status' => 0
            ], 401);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json([
                'message' => __('Trường token bị thiếu hoặc không hợp lệ.'),
                'status' => 0
            ], 401);
        }
    }

    public function getProfile(Request $request){
        try{
            $user = User::where('id',Auth::guard('api')->user()->id)->where('status',1)->select('id','username','email','fullname','balance')->first();
            if(!$user){
                return response()->json([
                    'message' => __('Người dùng không tồn tại.'),
                    'user' => null,
                    'status' => 0
                ], 200);
            }
            return response()->json([
                'message' => __('Thành công.'),
                'user' => $user,
                'status' => 1
            ], 200);
        }catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }

//    public function postChangeCurrentPassword(Request $request){
//        try{
//            $validator = Validator::make($request->all(), [
//                'password' => 'required|min:6|max:32',
//                'old_password' => 'required',
//                'password_confirmation' => 'required|same:password',
//            ],[
//                'password.min' => __('Mật khẩu mới phải ít nhất 6 ký tự.'),
//                'password.max' => __('Mật khẩu mới không vượt quá 32 ký tự.'),
//                'password.required' => __('Vui lòng nhập mật khẩu mới'),
//                'password.old_password' => __('Vui lòng nhập mật khẩu cũ'),
//                'password_confirmation.required' => __('Vui lòng nhập mật khẩu xác nhận'),
//                'password_confirmation.same' => __('Mật khẩu xác nhận không đúng.'),
//            ]);
//            if($validator->fails()){
//                return response()->json([
//                    'message' => $validator->errors()->first(),
//                    'status' => 0
//                ],422);
//            }
//            $user = User::where('account_type',2)->where('status',1)->where('id',Auth::guard('api')->user()->id)->first();
//            if ($request->password == $user->username) {
//                return response()->json([
//                    'message' => __('Mật khẩu không được trùng với tài khoản.'),
//                    'status' => 0,
//                ], 200);
//            }
//            if (Hash::check($request->old_password, $user->password)) {
//                $user->password = Hash::make($request->password);
//                $user->save();
//                ActivityLog::add($request, 'Thay đổi mật khẩu thành công');
//                return response()->json([
//                    'message' => __('Thay đổi mật khẩu thành công.'),
//                    'status' => 1,
//                ], 200);
//            }
//            else{
//                return response()->json([
//                    'message' => __('Mật khẩu cũ không đúng.'),
//                    'status' => 0,
//                ], 200);
//            }
//        }catch(\Exception $e){
//            Log::error($e);
//            return response()->json([
//                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
//                'status' => 0,
//            ], 500);
//        }
//    }
}
