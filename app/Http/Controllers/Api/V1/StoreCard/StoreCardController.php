<?php

namespace App\Http\Controllers\Api\V1\StoreCard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreTelecomValue;
use App\Models\StoreTelecom;
use App\Models\StoreCard;
use App\Models\User;
use App\Models\Order;
use App\Models\Txns;
use App\Models\ActivityLog;
use App\Models\Shop;
use App\Models\Shop_Group;
use App\Models\Shop_Group_Shop;
use App\Library\Helpers;
use App\Library\HelpMoneyPercent;
use App\Library\StoreCardGateway\StoreCardNapTheNhanh;
use App\Library\StoreCardGateway\StoreCardHqpay;
use App\Models\Group;
use Auth;
use DB;
use Log;
use Validator;
use Carbon\Carbon;

class StoreCardController extends Controller
{
    public function getHistory(Request $request){
        try {
            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }
            $user = Auth::guard('api')->user();
            $data = Order::where('shop_id',$shop->id)
            ->where('module','store-card')
            ->where('author_id',$user->id);
            if ($request->filled('serial') || $request->filled('pin') || $request->filled('telecom'))  {
                $data->whereHas('card', function ($query) use ($request) {
                    $query->where(function ($qChild) use ($request){
                        if($request->filled('serial')){
                            $qChild->where('serial',Helpers::Encrypt($request->get('serial'),config('module.charge.key_encrypt')));
                        }
                        if($request->filled('pin')){
                            $qChild->where('pin', Helpers::Encrypt($request->get('pin'),config('module.charge.key_encrypt')));
                        }
                        if($request->filled('telecom')){
                            $qChild->where('key', $request->get('telecom'));
                        }
                    });
                });
            }
            if ($request->filled('id')) {
                $data->where('id', $request->get('id'));
            }
            if ($request->filled('status')) {
                $data->where('status', $request->get('status'));
            }
            if ($request->filled('started_at')) {
                $data->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $data->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }
            $data->with(array('card' => function ($query) use ($shop) {
                $query->where('shop_id', $shop->id)->select('id','order_id','key','serial','pin','amount');
            }));
            $data = $data->orderBy('id','desc')
            ->select('id','params','price','real_received_price','status','created_at','content','ratio')
            ->paginate(20);
            return response()->json([
                'message' => __('Thành công'),
                'status' => 1,
                'data' => $data
            ], 200);
        }
        catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => "Có lỗi phát sinh.Xin vui lòng thử lại !",
                'status' => 0
            ],500);
        }
    }
    public function getDetails(Request $request, $id){
        try {
            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }
            $user = Auth::guard('api')->user();
            $data = Order::with(array('card' => function ($query) use ($shop) {
                $query->where('shop_id', $shop->id)->select('id','order_id','serial','pin','amount');
            }))
            ->where('shop_id',$shop->id)
            ->where('id',$id)
            ->where('module','store-card')
            ->where('author_id',$user->id)
            ->orderBy('id','desc')
            ->select('id','params','price','real_received_price','status','created_at','content','ratio')
            ->first();
            if(!$data){
                return response()->json([
                    'message' => __('Không tìm thấy đơn hàng yêu cầu.'),
                    'status' => 0,
                ], 200);
            }
            return response()->json([
                'message' => __('Thành công'),
                'status' => 1,
                'data' => $data
            ], 200);
        }
        catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => "Có lỗi phát sinh.Xin vui lòng thử lại !",
                'status' => 0
            ],500);
        }
    }
    public function getTelecomStoreCard(Request $request){
        try {
            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }
            $data = StoreTelecom::where('shop_id',$shop->id)
            ->where('status', 1)
            ->orderby('order','asc')
            ->select('id','title','key','image','params')
            ->get();
            return response()->json([
                'message' => __('Thành công'),
                'data' => $data,
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
    public function getAmount(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'telecom' => 'required',
            ],[
                'telecom.required' => __('Nhà mạng bị thiếu'),
            ]);
            if($validator->fails()){
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'status' => 0
                ],422);
            }
            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }
            $telecom = StoreTelecom::where('shop_id',$shop->id)
            ->where('status', 1)
            ->where('key', $request->telecom)
            ->first();
            if(!$telecom){
                return response()->json([
                    'message' => __('Nhà mạng không tồn tại hoặc bị khóa bởi Admin'),
                    'status' => 0,
                ], 200);
            }
            $store_telecom_value = StoreTelecomValue::where('shop_id',$shop->id)
            ->where('telecom_id', $telecom->id)
            ->where('status', 1)
            ->select('id','amount','telecom_key','ratio_default')
            ->get();

            return response()->json([
                'message' => __('Thành công'),
                'status' => 1,
                'data' => $store_telecom_value
            ], 200);
        }catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0,
            ], 500);
        }

    }
    public function postStoreCard(Request $request){
        $validator = Validator::make($request->all(), [
            'telecom_key' => 'required',
            'amount' => 'required',
            'quantity' => 'required|integer|min:1|max:20',
        ], [
            'telecom_key.required' => "Vui lòng chọn loại thẻ",
            'amount.required' => "Vui lòng chọn mệnh giá",
            'quantity.required' => "Vui lòng chọn số lượng thẻ muốn mua",
            'quantity.min' => "Số lượng thẻ từ 1 - 20 thẻ",
            'quantity.max' => "Số lượng thẻ từ 1 - 20 thẻ",
            'quantity.integer' => "Số lượng thẻ không đúng định dạng",
        ]);
        if($validator->fails()){
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 0
            ],422);
        }
        try{
            DB::beginTransaction();
            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }
            $telecom = $request->telecom_key;
            $amount = (int)$request->amount;
            $quantity = (int)$request->quantity;

            // check nhà mạng
            $store_telecom = StoreTelecom::where('shop_id',$shop->id)
            ->where('key', $telecom)
            ->where('status', 1)
            ->first();
            if(!$store_telecom){
                return response()->json([
                    'message' => __('Không tìm thấy nhà mạng phù hợp'),
                    'status' => 0,
                ], 200);
            }

              // kiểm tra mệnh giá được gửi lên
              $store_telecom_value = StoreTelecomValue::where('shop_id',$shop->id)
              ->where('telecom_id', $store_telecom->id)
              ->where('amount',$amount)
              ->first();
              if(!$store_telecom_value){
                  return response()->json([
                    'message' => __('Không tìm thấy mệnh giá phù hợp'),
                    'status' => 0,
                ], 200);
              }



                // lấy cổng thẻ được gọi theo nhà mạng
                $gate_id = $store_telecom->gate_id;
                // lấy chiết khấu theo từng mệnh giá
                $ratio = $store_telecom_value->ratio_default;

                // tổng tiền
                $total_amount = $amount * $quantity;

                // số tiền chiết khấu
                $sale = $total_amount - ($total_amount * $ratio / 100);
                // số tiền phải thanh toán
                $real_received_amount = $total_amount - $sale;


                // *************** tính số tiền sau tỷ giá shop ***************



                // *************** kết thúc tính số tiền sau tỷ giá shop ***************

                if ($real_received_amount <= 0) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Số tiền thanh toán không hợp lệ',
                    ],200);
                }

                // tìm người sử dụng dịch vụ
                $userTransaction = User::where('id',Auth::guard('api')->user()->id)->lockForUpdate()->first();

                // kiểm tra số dư còn khả dụng để sử dụng dịch vụ
                if ($userTransaction->balance < $real_received_amount) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Bạn không đủ tiền để thanh toán.Vui lòng nạp thêm tiền vào tài khoản để sử dụng dịch vụ.',
                    ],200);
                }

                // kiểm tra tính xác thực số dư người dùng
                if($userTransaction->checkBalanceValid() == false){
                    return response()->json([
                        'status' => 0,
                        'message' => 'Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý !',
                    ],200);
                }
                //trừ tiền người dùng
                $userTransaction->balance = $userTransaction->balance - $real_received_amount;
                // cộng tổng số tiền chi tiêu
                $userTransaction->balance_out = $userTransaction->balance_out + $real_received_amount;
                $userTransaction->save();
                // request_id gửi lên nhà cc
                $request_id = time().$userTransaction->id.rand(10,99).'';
                 // lưu thông tin order
                 $params_order = new \stdClass();
                 $params_order->telecom = $telecom; // nhà mạng
                 $params_order->amount = $amount; // mệnh giá thẻ
                 $params_order->quantity = $quantity; // mệnh giá thẻ
                 $params_order = json_encode($params_order,JSON_UNESCAPED_UNICODE);
                 $order = Order::create([
                     'shop_id' => $shop->id,
                     'module' => config('module.store-card.key'),
                     'content' => "Mua ".$quantity.' thẻ '.$telecom.' mệnh giá '.$amount,
                     'request_id' => $request_id,
                     'ratio' => $ratio,
                     'payment_type' => 0,
                     'gate_id' => $gate_id,
                     'author_id' => $userTransaction->id,
                     'params' => $params_order,
                     'real_received_price' => $real_received_amount, // số tiền cần thanh toán, đã qua chiết khấu
                     'price' => $total_amount, // tổng tiền chưa qua chiết khấu
                     'status' => 2 // đang chờ xử lý
                 ]);
                 $txns = $order->txns()->create([
                    'shop_id' => $order->shop_id,
                    'trade_type' => 'store_card', // mua thẻ
                    'is_add'=>'0',//tru tien
                    'user_id'=>$userTransaction->id,
                    'amount'=>$real_received_amount,
                    'ratio'=>$ratio,
                    'profit'=>null,
                    'last_balance'=>$userTransaction->balance,
                    'description'=> "Mua ".$quantity.' thẻ '.$telecom.' mệnh giá '.$amount,
                    'ip'=>$request->getClientIp(),
                    'status'=>1
                ]);
                 // đóng Transaction lưu dữ liệu trước khi gửi sang ncc
                 DB::commit();

                 // sử dụng cổng NTN
                if($order->gate_id == 1){
                    $result = StoreCardNapTheNhanh::API($shop->ntn_partner_id,$shop->ntn_partner_key_card,$telecom,$amount,$quantity,$order->request_id,$shop->domain);
                }
                // elseif($order->gate_id == 2){
                //     $result = StoreCardHqpay::BuyCard($telecom,$amount,$quantity,$order->request_id);
                // }
                else{
                    $result="WRONG_GATEWAY";
                }
                // mở Transaction
                DB::beginTransaction();
                try{
                    $userTransaction = User::where('id', $order->author_id)->lockForUpdate()->first();
                    if($result==="WRONG_GATEWAY"){
                        // trường hợp không tìm thấy cổng thẻ nhà cung cấp
                        $order->status = 0;
                        $order->content = "Cổng thẻ không hợp lệ, vui lòng liên hệ Admin để xử lý.";
                        $order->save();
                          // hoàn tiền khách hàng
                        $userTransaction->balance = $userTransaction->balance + $order->real_received_price;
                        // cộng số tiền vào
                        $userTransaction->balance_in = $userTransaction->balance_in + $real_received_amount;
                        $userTransaction->save();
                        DB::commit();
                        return response()->json([
                            'message' => 'Mua thẻ thất bại ! Cổng thẻ không hợp lệ, vui lòng liên hệ Admin để xử lý.',
                            'status' => 0,
                        ],200);
                    }
                    // trường hợp gọi cổng thanh toán nhà cung cấp thành công
                    if($result  && $result->status == 1){
                        $order->status = 1;
                        $order->price_input = $result->total_price??null;
                        $order->process_at = Carbon::now();
                        $order->description = 'CODE '.$result->status.' - '.$result->message;
                        $order->save();
                        // cập nhật thông tin thẻ bán
                        $data_card = $result->data_card;
                        foreach ($data_card as $card) {
                            StoreCard::create([
                                'shop_id' => $shop->id,
                                'key' => $telecom,
                                'pin' => Helpers::Encrypt($card->pin,config('module.charge.key_encrypt')),
                                'serial' => Helpers::Encrypt($card->serial,config('module.charge.key_encrypt')),
                                'amount' => $amount,
                                'status' => 1,
                                'user_id' => $userTransaction->id,
                                'buy_at' => Carbon::now(),
                                'ratio' => $order->ratio,
                                // 'expiryDate' => $card->expiryDate,
                                'order_id' => $order->id
                            ]);
                        }
                        ActivityLog::add($request, 'Mua thẻ thành công #'.$order->id);
                        DB::commit();
                        // đoạn này sẽ trả thẻ cho  khách
                        return response()->json([
                            'message' => 'Thực hiện mua thẻ thành công !',
                            'id' => $order->id,
                            'status' => 1,
                            'data_card' => $data_card,
                            'amount' => str_replace(',','.',number_format($real_received_amount)),
                            'ratio' => $ratio,
                            'created_at' => $order->created_at->format('d/m/Y H:i'),
                            'description' => $txns->description
                        ],200);
                    }
                    // trường hợp gọi cổng dịch vụ có trạng thái đang chờ
                    else if(isset($result) && $result->status == 2){
                        // lưu trạng thái đang chờ, giữ lại tiền khách hàng, chờ check dữ liệu với nhà cung cấp
                        $order->status = 2;
                        $order->description = 'CODE '.$result->status.' - '.$result->message;
                        $order->save();
                        DB::commit();
                        $message = "Giao dịch đang chờ xử lý, vui lòng liên hệ QTV để xác thực giao dịch";
                        return response()->json([
                            'message' => $message,
                            'status' => 2,
                        ],200);
                    }
                    // trường hợp gọi cổng thanh toán thất bại
                    else if(isset($result) && $result->status == 0){
                        $order->status = 0;
                        $order->process_at = Carbon::now();
                        $order->description = 'CODE '.$result->status.' - '.$result->message;
                        $order->save();
                         // hoàn tiền khách hàng
                         $userTransaction->balance = $userTransaction->balance + $order->real_received_price;
                         // cộng số tiền vào
                        $userTransaction->balance_in = $userTransaction->balance_in + $real_received_amount;
                        $userTransaction->save();

                        $txns = $order->txns()->create([
                            'shop_id' => $order->shop_id,
                            'trade_type' => 'store_card', // mua thẻ
                            'is_add'=>'1', // cộng tiền
                            'user_id'=>$userTransaction->id,
                            'amount'=>$real_received_amount,
                            'ratio'=>$ratio,
                            'profit'=>null,
                            'last_balance'=>$userTransaction->balance,
                            'description'=> "Hoàn tiền dịch vụ mua thẻ lỗi: Mua ".$quantity.' thẻ '.$telecom.' mệnh giá '.$amount,
                            'ip'=>$request->getClientIp(),
                            'ref_id'=>$order->id,
                            'status'=>1
                        ]);
                        DB::commit();
                        return response()->json([
                            'message' => 'Mua thẻ thất bại. '.$result->message,
                            'status' => 0
                        ],200);
                    }
                    // trường hợp gặp các lỗi khác hoặc nhà cung cấp không phản hồi
                    else{
                        $order->status = 4;
                        $order->save();
                        DB::commit();
                        return response()->json([
                            'message' => 'Lỗi giao dịch, vui lòng liên hệ QTV để xác thực giao dịch',
                            'status' => 0
                        ],200);
                    }

                }
                // trường hợp này có thể lỗi hệ thống khi nhận dữ liệu từ nhà cung cấp, yêu cầu xác thực giao dịch
                catch (\Exception $e) {
                    DB::rollBack();
                    $order->status = 5;
                    $order->save();
                    Log::error($e);
                    return response()->json([
                        'message' => 'Lỗi hệ thống, vui lòng liên hệ QTV để xác thực giao dịch',
                        'status' => 0,
                    ],200);
                }


        }catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0,
            ], 500);
        }

    }
}
