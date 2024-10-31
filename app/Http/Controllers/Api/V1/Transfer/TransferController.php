<?php

namespace App\Http\Controllers\Api\V1\Transfer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shop;
use Validator;
use Carbon\Carbon;
use Cache;
use DB;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\Order;

class TransferController extends Controller
{
    public function getHistory(Request $request){
        try{
            $user = Auth::guard('api')->user();
            // tìm shop
            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }
            $data = Order::with('bank')->where('module','=',config('module.transfer.key'))
            ->where('author_id',$user->id)
            ->where('shop_id',$shop->id)
            ->orderBy('id','desc')
            ->select('id','title','description','content','author_id','price','ratio','real_received_price','params','status','created_at')
            ->paginate(20);
            return response()->json([
                'message' => __('Thành công'),
                'status' => 1,
                'data' => $data
            ], 200);
        }catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }
    public function getDetails(Request $request,$id){
        try{
            $user = Auth::guard('api')->user();
            // tìm shop
            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }
            $data = Order::with('bank')->where('module','=',config('module.transfer.key'))
            ->where('author_id',$user->id)
            ->where('shop_id',$shop->id)
            ->where('id',$id)
            ->select('id','title','description','content','author_id','price','ratio','real_received_price','params','status','created_at')
            ->first();
            if(!$data){
                return response()->json([
                    'message' => __('Không tìm thấy dữ liệu yêu cầu.'),
                    'status' => 0,
                ], 200);
            }
            return response()->json([
                'message' => __('Thành công'),
                'status' => 1,
                'data' => $data
            ], 200);
        }catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }
    public function getCode(Request $request){
        try{
            $user = Auth::guard('api')->user();
            // tìm shop
            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }
            if(!$shop->key_transfer){
                return response()->json([
                    'message' => __('Nội dung chuyển khoản chưa được định danh'),
                    'status' => 0,
                ], 200);
            }
            $code = 'NAP '.$shop->key_transfer.' '.$user->id;
            return response()->json([
                'message' => __('Thành công'),
                'status' => 1,
                'data' => $code,
            ], 200);
        }catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }
}
