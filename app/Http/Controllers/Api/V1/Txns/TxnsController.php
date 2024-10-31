<?php

namespace App\Http\Controllers\Api\V1\Txns;

use App\Http\Controllers\Controller;
use App\Library\Helpers;
use App\Library\HelpItemAdd;
use App\Library\HelpServiceAuto;
use App\Library\RatioCommon\ServiceRatio;
use App\Models\Bot;
use App\Models\Group;
use App\Models\Item;
use App\Models\ItemConfig;
use App\Models\KhachHang;
use App\Models\LangLaCoin_KhachHang;
use App\Models\LangLaCoin_User;
use App\Models\MoneySpent;
use App\Models\NinjaXu_KhachHang;
use App\Models\NinjaXu_User;
use App\Models\Nrogem_AccBan;
use App\Models\Nrogem_GiaoDich;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shop;
use App\Models\Shop_Group;
use App\Models\SubItem;
use App\Models\Txns;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use JWTAuth;


class TxnsController extends Controller
{

    public function index(Request $request){

        $shop = Shop::where('secret_key',$request->secret_key)->where('domain',$request->domain)->where('status',1)->first();

        if(!$shop){
            return response()->json([
                'message' => __('Domain chưa được đăng kí'),
                'status' => 0,
            ], 200);
        }

        $shopid = $shop->id;

        $datatable = Txns::with('user');

        $datatable->whereHas('user', function ($query)  {
            $query->where('id',  Auth::guard('api')->user()->id);
        })->where('shop_id',$shopid)->select('id','trade_type','user_id','is_refund','is_add','description','txnsable_id','order_id','amount','last_balance','shop_id','status','created_at');

        if ($request->filled('id')) {
            $datatable->where('id', $request->id);
        }

        if ($request->filled('trade_type')) {
            $datatable->where('trade_type', $request->trade_type);
        }

        if ($request->filled('status')) {
            $datatable->where('status', $request->status);
        }

        if ($request->filled('started_at')) {
            $datatable->where('created_at', '>=', $request->started_at);

        }
        if ($request->filled('ended_at')) {
            $datatable->where('created_at', '<=', $request->ended_at);
        }

        if ($request->filled('sort')) {
            if ($request->sort == 'random') {
                $datatable->inRandomOrder();
            }elseif(in_array($request->sort, ['asc', 'desc'])){
                $datatable->orderBy($request->sort_by??'id', $request->sort);
            }
        }else{
            $datatable->orderBy('created_at','desc');
        }

        $datatable = $datatable->paginate($request->limit?? 10);

        return response()->json([
            'data' => $datatable,
            'status' => 1,
            'message' => "Lấy dữ liệu thành công",
        ]);

    }

    public function show(Request $request,$id){
        $shop = Shop::where('secret_key',$request->secret_key)->where('domain',$request->domain)->where('status',1)->first();

        if(!$shop){
            return response()->json([
                'message' => __('Domain chưa được đăng kí'),
                'status' => 0,
            ], 200);
        }

        $shopid = $shop->id;

        $data = Txns::with('user')->where('id',$id);

        $data->whereHas('user', function ($query)  {
            $query->where('id',  Auth::guard('api')->user()->id);
        })->where('shop_id',$shopid)->select('id','trade_type','user_id','is_refund','is_add','description','txnsable_id','order_id','amount','last_balance','shop_id','status','created_at')->first();

        return response()->json([
            'data' => $data,
            'status' => 1,
            'message' => "Lấy dữ liệu thành công",
        ]);
    }

}
