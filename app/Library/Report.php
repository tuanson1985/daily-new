<?php
namespace App\Library;
use Html;
use App\Library\Helpers;
use Carbon\Carbon;
use App\Models\User;
use App\Models\SocialAccount;
use App\Models\Txns;
use App\Models\Charge;
use App\Models\Order;
use App\Models\PlusMoney;
use App\Models\Shop;
use App\Models\StoreCard;
use App\Models\TxnsVp;
use DB;
class Report
{
    public function __construct($time,$channel_id = ""){
        try{
           // tìm các user đăng kí trong ngày
            $user = User::where('account_type',2)->where('status',1)->whereDate('created_at','=', $time)->pluck('id')->toArray();
            // tính tổng user đăng kí trong ngày
            $total_user = count($user);
            // tổng user đăng kí mới có phát sinh giao dịch trong ngày
            $user_transaction = Txns::groupBy('user_id')->whereIn('user_id',$user)->whereDate('created_at', $time)->select('user_id',DB::raw('count(*) as total'))->get()->count();
            // tổng user đăng kí mới không phát sinh giao dịch trong ngày
            $user_not_transaction = $total_user - $user_transaction;
            // tổng user đăng kí mới bằng facebook
            $user_social_facebook = SocialAccount::whereDate('created_at', $time)->where('provider','facebook')->count();
            // tổng user đăng kí mới bằng google
            $user_social_google = SocialAccount::whereDate('created_at', $time)->where('provider','google')->count();
            // tổng user đăng kí mới qua tài khoản
            $user_account = $total_user - $user_social_facebook - $user_social_google;
            // nạp tiền qua thẻ cào
            // tổng số giao dịch
            $count_charge = Charge::whereDate('updated_at', $time)->count();
            // số lượng người giao dịch
            $count_user_charge = Charge::whereDate('updated_at', $time)->groupBy('user_id')->select('user_id',DB::raw('count(*) as total'))->get()->count();
            // số lượng giao dịch thành công
            $count_charge_succsess = Charge::whereDate('updated_at', $time)->where(function ($query){
                $query->orWhere('status',1);
                $query->orWhere('status',3);
            })->count();
            // số lượng giao dịch đang chờ
            $count_charge_pendding = Charge::whereDate('updated_at', $time)->where('status',2)->count();
            // số lượng giao dịch thất bại
            $count_charge_errors = Charge::whereDate('updated_at', $time)->where('status',0)->count();
            // tổng số tiền nạp
            $sum_charge = Charge::whereDate('updated_at', $time)->sum('declare_amount');
            // tổng số tiền nạp thành công
            $sum_succsess_charge = Charge::whereDate('updated_at', $time)->where(function ($query){
                $query->orWhere('status',1);
                $query->orWhere('status',3);
            })->sum('declare_amount');
            // sản lượng nạp ATM
            // tổng số giao dịch
            $count_atm = Order::where('module',config('module.transfer.key'))->whereDate('updated_at', $time)->count();
            // tổng số người giao dịch
            $count_user_atm =  Order::where('module',config('module.transfer.key'))->whereDate('updated_at', $time)->groupBy('author_id')->select('author_id',DB::raw('count(*) as total'))->get()->count();
            // tổng số tiền nạp
            $sum_atm = Order::where('module',config('module.transfer.key'))->whereDate('updated_at', $time)->sum('real_received_price');
            // cộng tiền trong hệ thống
            // tổng số lệnh cộng tiền
            $count_plus_money = PlusMoney::whereDate('updated_at', $time)->where('is_add',1)->count();
            // số lượng người được cộng tiền
            $count_user_plus_money =  PlusMoney::whereDate('updated_at', $time)->where('is_add',1)->groupBy('user_id')->select('user_id',DB::raw('count(*) as total'))->get()->count();
            // tổng số tiền được cộng cho thành viên
            $sum_plus_money = PlusMoney::whereDate('updated_at', $time)->where('is_add',1)->sum('amount');
            // trừ tiền trong hệ thống
            // tổng số lệnh trừ tiền
            $count_minus_money = PlusMoney::whereDate('updated_at', $time)->where('is_add',0)->count();
            // số lượng người được trừ tiền
            $count_user_minus_money =  PlusMoney::whereDate('updated_at', $time)->where('is_add',0)->groupBy('user_id')->select('user_id',DB::raw('count(*) as total'))->get()->count();
            // tổng số tiền được trừ cho thành viên
            $sum_minus_money = PlusMoney::whereDate('updated_at', $time)->where('is_add',0)->sum('amount');

            // Tổng số shop đang hoạt động
            $shop = Shop::where('status',1)->count();
            // Tổng số shop ngừng hoạt động
            $shop_ngung_hoat_dong = Shop::where('status',0)->count();
            // Tổng số shop đang nhập liệu
            $shop_dang_nhap_lieu = Shop::where('status',2)->count();
            // Tổng số shop đang ngừng kinh doanh
            $shop_ngung_kinh_doanh = Shop::where('status',3)->count();
            // Tổng số shop có doanh thu theo tháng
            $shop_turnover_month = Txns::groupBy('shop_id')->where('created_at', '>=', Carbon::now()->startOfMonth())->where('created_at', '<=', Carbon::now()->endOfMonth())->select('shop_id',DB::raw('count(*) as total'))->get()->count();
            // Tổng số shop có doanh thu theo ngày
            $shop_turnover_day = Txns::groupBy('shop_id')->whereDate('created_at', $time)->select('shop_id',DB::raw('count(*) as total'))->get()->count();
            
            // Tổng số dư tài khoản thành viên:
            $sum_balance_user = User::where('account_type',2)->sum('balance');
            // Tổng số dư tài khoản qtv:
            $sum_balance_qtv = User::where('account_type',1)->sum('balance');
            // Tổng số dư tài khoản ctv:
            $sum_balance_ctv = User::where('account_type',3)->sum('balance');
            // Số dư tài khoản lớn nhất của thành viên
            $user_max_balance = User::with('shop')->where('account_type',2)->orderBy('balance','desc')->first();
            // Số dư tài khoản lớn nhất của qtv
            $user_max_balance_qtv = User::with('shop')->where('account_type',1)->orderBy('balance','desc')->first();
            // Số dư tài khoản lớn nhất của ctv
            $user_max_balance_ctv = User::with('shop')->where('account_type',3)->orderBy('balance','desc')->first();
            // giao dịch phát sinh lớn nhất trong ngày
            $transaction_max_money = Txns::with('shop')->with('user')->whereDate('created_at', $time)->orderBy('amount','desc')->first();

            // số người giao dịch bán thẻ chỉ tính giao dịch thành công
            $count_user_store_card = Order::groupBy('author_id')->where('module',config('module.store-card.key'))->whereDate('created_at', $time)->where('status',1)->select('author_id',DB::raw('count(*) as total'))->get()->count();
            // số lượng giao dịch thành công
            $count_order_store_card = Order::whereDate('created_at', $time)->where('module',config('module.store-card.key'))->where('status',1)->get()->count();
            // số lượng giao dịch đang chờ
            $count_order_store_card_pendding = Order::whereDate('created_at', $time)->where('module',config('module.store-card.key'))->where('status',2)->get()->count();
            // số lượng giao dịch thất bại
            $count_order_store_card_errors = Order::whereDate('created_at', $time)->where('module',config('module.store-card.key'))->where('status',0)->get()->count();
            // doanh thu mua thẻ
            $turnover_store_card = Order::whereDate('created_at', $time)->where('module',config('module.store-card.key'))->where('status',1)->sum('real_received_price');
            // doanh thu mua tổng mua thẻ
            $turnover_store_card_total = Order::whereDate('created_at', $time)->where('module',config('module.store-card.key'))->sum('real_received_price');
            // tổng số lệnh rút theo ngày thành công
            $total_order_items_day = Order::with('author')->where('module', 'withdraw-item')->whereDate('created_at', $time)->where('status',1)->count();
            // tổng số lệnh rút theo ngày đang chờ
            $total_order_items_day_pendding = Order::with('author')->where('module', 'withdraw-item')->whereDate('created_at', $time)->where('status',0)->count();
            // tổng số lệnh rút theo ngày thanh toán lỗi
            $total_order_items_day_errors = Order::with('author')->where('module', 'withdraw-item')->whereDate('created_at', $time)->where('status',2)->count();
            // tổng số lệnh rút theo ngày giao dịch lỗi
            $total_order_items_day_giao_dich_loi = Order::with('author')->where('module', 'withdraw-item')->whereDate('created_at', $time)->where('status',3)->count();
            // tổng số lệnh rút theo tháng
            $total_order_items_month = Order::with('author')->where('module', 'withdraw-item')->where('created_at', '>=', Carbon::now()->startOfMonth())->where('created_at', '<=', Carbon::now()->endOfMonth())->count();
            // tổng số vật phẩm rút
            $total_items_day = Order::with('author')->where('module', 'withdraw-item')->whereDate('created_at', $time)->where('status',1)->sum('price');
            // tổng số vật phẩm rút theo tháng
            $total_items_month = Order::with('author')->where('module', 'withdraw-item')->where('created_at', '>=', Carbon::now()->startOfMonth())->where('created_at', '<=', Carbon::now()->endOfMonth())->where('status',1)->sum('price');
            // số giao dịch vòng quay
            $count_rotation_in_day = Order::with('author')->with('group')->whereDate('created_at', $time)->with('item_ref')->with('item_acc')->where('module', 'minigame-log')->count();
            //Số nguoi giao dich
            $count_user_rotation_in_day = Order::with('author')->whereDate('created_at', $time)->with('group')->whereNull('acc_id')->distinct('author_id')->where('module', 'minigame-log')->count();
            //tong so tien
            $sum_price_rotation_in_day = Order::with('author')->with('group')->whereDate('created_at', $time)->whereNull('acc_id')->where('module', 'minigame-log')->sum('price');
            
            
            
            // Số lượng đơn dịch vụ thủ công
            $total_service_purchase = Order::select('order.*')->whereDate('created_at', $time)->where('order.module', config('module.service-purchase'))->where('gate_id',0)->get()->count();
            // số lượng đơn dịch vụ thủ công thành công
            $total_service_purchase_succsess = Order::select('order.*')->whereDate('created_at', $time)->where('order.module', config('module.service-purchase'))->where('gate_id',0)->where('status',4)->get()->count();
            // số lượng đơn dịch vụ thủ công đang chờ
            $total_service_purchase_pendding = Order::select('order.*')->whereDate('created_at', $time)->where('order.module', config('module.service-purchase'))->where('gate_id',0)->where('status',1)->get()->count();
            // số lượng đơn dịch vụ thủ công thất bại
            $total_service_purchase_errors = Order::select('order.*')->whereDate('created_at', $time)->where('order.module', config('module.service-purchase'))->where('gate_id',0)->where('status',5)->get()->count();
            // doanh thu dịch vụ thủ công
            $total_money_service_purchase = Order::whereDate('created_at', $time)->where('order.module', config('module.service-purchase'))->where('status',1)->where('gate_id',0)->sum('price');
            // số tiền ctv nhận được
            $total_money_service_purchase_ctv = Order::whereDate('created_at', $time)->where('order.module', config('module.service-purchase'))->where('status',1)->where('gate_id',0)->sum('price_ctv');
        
        
        
            // Số lượng đơn dịch vụ tự động
            $total_service_purchase_auto = Order::select('order.*')->whereDate('created_at', $time)->where('order.module', config('module.service-purchase'))->where('gate_id',1)->get()->count();
            // số lượng đơn dịch vụ thủ tự động
            $total_service_purchase_succsess_auto = Order::select('order.*')->whereDate('created_at', $time)->where('order.module', config('module.service-purchase'))->where('gate_id',1)->where('status',4)->get()->count();
            // số lượng đơn dịch vụ tự động đang chờ
            $total_service_purchase_pendding_auto = Order::select('order.*')->whereDate('created_at', $time)->where('order.module', config('module.service-purchase'))->where('gate_id',1)->where('status',1)->get()->count();
            // số lượng đơn dịch vụ tự động thất bại
            $total_service_purchase_errors_auto = Order::select('order.*')->whereDate('created_at', $time)->where('order.module', config('module.service-purchase'))->where('gate_id',1)->where('status',5)->get()->count();
            // doanh thu dịch vụ tự động
            $total_money_service_purchase_auto = Order::whereDate('created_at', $time)->where('order.module', config('module.service-purchase'))->where('status',1)->where('gate_id',1)->sum('price');
            // tổng số vật phẩm cộng
            $sum_plus_item = TxnsVp::with('itemtype')->whereDate('updated_at', Carbon::today())->where('is_add',1)->groupBy('item_type')->select('item_type',DB::raw('sum(amount) as amount'))->get();

            // tổng số vật phẩm trừ
            $sum_minus_item = TxnsVp::with('itemtype')->whereDate('updated_at', Carbon::today())->where('is_add',0)->groupBy('item_type')->select('item_type',DB::raw('sum(amount) as amount'))->get();

            $message = '';
            $message .= "BÁO CÁO THỐNG KÊ NGÀY ".$time->format('d-m-Y');
            $message .= "\n";
            $message .= "\n";
            $message .= '<b>1.Điểm bán: </b>';
            $message .= "\n";
            $message .= "\n";
            $message .= '- Tổng số điểm bán đang hoạt động: '.$shop;
            $message .= "\n";
            $message .= '- Tổng số điểm bán ngừng hoạt động: '.$shop_ngung_hoat_dong;
            $message .= "\n";
            $message .= '- Tổng số điểm bán ở trạng thái nhập liệu: '.$shop_dang_nhap_lieu;
            $message .= "\n";
            $message .= '- Tổng số điểm bán ngừng kinh doanh: '.$shop_ngung_kinh_doanh;
            $message .= "\n";
            $message .= '- Tổng số điểm bán có doanh thu theo tháng: '.$shop_turnover_month;
            $message .= "\n";
            $message .= '- Tổng số điểm bán có doanh thu theo ngày: '.$shop_turnover_day;
            $message .= "\n";
            $message .= "\n";
            $message .= '<b>2.Người dùng: </b>';
            $message .= "\n";
            $message .= "\n";
            $message .= '<b>* Hình thức đăng kí: </b>';
            $message .= "\n";
            $message .= '- Số người dùng đăng kí mới: '.$total_user;
            $message .= "\n";
            $message .= '- Số người dùng đăng kí mới qua tài khoản: '.$user_account. " (".round(($user_account / ($total_user == 0 ? 1 : $total_user)) * 100,2) ." %)";
            $message .= "\n";
            $message .= '- Số người dùng đăng kí mới qua facebook: '.$user_social_facebook. " (".round(($user_social_facebook / ($total_user == 0 ? 1 : $total_user)) * 100,2) ." %)";
            $message .= "\n";
            $message .= '- Số người dùng đăng kí mới qua google: '.$user_social_google. " (".round(($user_social_google / ($total_user == 0 ? 1 : $total_user)) * 100,2) ." %)";
            $message .= "\n";
            $message .= '<b>* Tỷ lệ giao dịch: </b>';
            $message .= "\n";
            $message .= '- Tổng số người dùng đăng kí mới có giao dịch trong ngày: '.$user_transaction. " (".round(($user_transaction / ($total_user == 0 ? 1 : $total_user)) * 100,2) ." %)";
            $message .= "\n";
            $message .= '- Tổng số người dùng đăng kí mới không có giao dịch trong ngày: '.$user_not_transaction. " (".round(($user_not_transaction / ($total_user == 0 ? 1 : $total_user)) * 100,2) ." %)";
            $message .= "\n";
            $message .= '- Tổng số dư thành viên: '.number_format($sum_balance_user)." VNĐ";
            $message .= "\n";
            $message .= '- Tổng số dư quản trị viên: '.number_format($sum_balance_qtv)." VNĐ";
            $message .= "\n";
            $message .= '- Tổng số dư cộng tác viên: '.number_format($sum_balance_ctv)." VNĐ";
            $message .= "\n";
            $message .= '- Thành viên có số dư lớn nhất: '.$user_max_balance->username.' - '.$user_max_balance->shop->domain.' - '.number_format($user_max_balance->balance).' VNĐ';
            $message .= "\n";
            $message .= '- QTV có số dư lớn nhất: '.$user_max_balance_qtv->username.' - '.number_format($user_max_balance_qtv->balance).' VNĐ';
            $message .= "\n";
            $message .= '- CTV có số dư lớn nhất: '.$user_max_balance_ctv->username.' - '.number_format($user_max_balance_ctv->balance).' VNĐ';
            $message .= "\n";
            $message .= '- Giao dịch phát sinh lớn nhất trong ngày: '.$transaction_max_money->user->username.' - Số tiền: '.number_format($transaction_max_money->amount).' Loại: '.$transaction_max_money->withdraw_money.' Nội dung: '.$transaction_max_money->description;
            $message .= "\n";
            $message .= "\n";
            $message .= '<b>3. Tổng sản lượng: </b>';
            $message .= "\n";
            $message .= "\n";
            $message .= '<b>* Nạp tiền qua thẻ cào: </b>';
            $message .= "\n";
            $message .= '- Số lượng giao dịch: '.$count_charge;
            $message .= "\n";
            $message .= '- Số người giao dịch: '.$count_user_charge;
            $message .= "\n";
            $message .= '- Số giao dịch thành công: '.$count_charge_succsess. " (".round(($count_charge_succsess / ($count_charge == 0 ? 1 : $count_charge)) * 100,2) ." %)";
            $message .= "\n";
            $message .= '- Số giao dịch đang chờ: '.$count_charge_pendding. " (".round(($count_charge_pendding / ($count_charge == 0 ? 1 : $count_charge)) * 100,2) ." %)";
            $message .= "\n";
            $message .= '- Số giao dịch thất bại: '.$count_charge_errors. " (".round(($count_charge_errors / ($count_charge == 0 ? 1 : $count_charge)) * 100,2) ." %)";
            $message .= "\n";
            $message .= '- Giá trị giao dịch trung bình (chỉ tính giao dịch thành công): '.number_format(round($sum_succsess_charge / ($count_charge_succsess == 0 ? 1 : $count_charge_succsess),2)). " VNĐ";
            $message .= "\n";
            $message .= '- Tổng số tiền nạp theo mệnh giá: '.number_format($sum_charge). " VNĐ";
            $message .= "\n";
            $message .= '- Tổng số tiền nạp thành công: '.number_format($sum_succsess_charge). " VNĐ";
            $message .= "\n";
            $message .= '- Tỷ trọng / Tổng số tiền nạp: '.round(($sum_succsess_charge / ($sum_charge == 0 ? 1 : $sum_charge)) * 100,2). " %";
            $message .= "\n";
            $message .= '<b>* Nạp tiền qua ATM: </b>';
            $message .= "\n";
            $message .= '- Số lượng giao dịch: '.$count_atm;
            $message .= "\n";
            $message .= '- Số người giao dịch: '.$count_user_atm;
            $message .= "\n";
            $message .= '- Tổng số tiền nạp: '.number_format($sum_atm). " VNĐ";
            $message .= "\n";
            $message .= '- Giao dịch thành công: 100%';
            $message .= "\n";
            $message .= '- Giá trị giao dịch trung bình (chỉ tính giao dịch thành công): '.number_format($sum_atm / ($count_atm == 0 ? 1 : $count_atm)). " VNĐ";
            $message .= "\n";
            $message .= '<b>* Cộng trừ tiền thủ công trong hệ thống quản trị: </b>';
            $message .= "\n";
            $message .= '- Cộng tiền:';
            $message .= "\n";
            $message .= '+ Tổng số lệnh cộng tiền: '.$count_plus_money;
            $message .= "\n";
            $message .= '+ Tổng số người dùng được cộng tiền: '.$count_user_plus_money;
            $message .= "\n";
            $message .= '+ Tổng số tiền được cộng: '.number_format($sum_plus_money);
            $message .= "\n";
            $message .= '- Trừ tiền:';
            $message .= "\n";
            $message .= '+ Tổng số lệnh trừ tiền: '.$count_minus_money;
            $message .= "\n";
            $message .= '+ Tổng số người dùng được trừ tiền: '.$count_user_minus_money;
            $message .= "\n";
            $message .= '+ Tổng số tiền được trừ: '.number_format($sum_minus_money);
            $message .= "\n";
            $message .= '<b>* Doanh thu bán thẻ: </b>';
            $message .= "\n";
            $message .= '- Số thành viên giao dịch (Chỉ tính giao dịch thành công): '.number_format($count_user_store_card);
            $message .= "\n";
            $message .= '- Tổng số đơn hàng thành công: '.number_format($count_order_store_card);
            $message .= "\n";
            $message .= '- Tổng số đơn hàng đang chờ: '.number_format($count_order_store_card_pendding);
            $message .= "\n";
            $message .= '- Tổng số đơn hàng thất bại: '.number_format($count_order_store_card_errors);
            $message .= "\n";
            $message .= '- Doanh thu mua thẻ: '.number_format($turnover_store_card). " VNĐ";
            $message .= "\n";
            $message .= '- Tỷ trọng/tổng doanh thu: '.round(($turnover_store_card / ($turnover_store_card_total == 0 ? 1 : $turnover_store_card_total)) * 100,2). " %";
            $message .= "\n";
            $message .= '<b>* Vật phẩm rút: </b>';
            $message .= "\n";
            $message .= '- Tổng số lệnh rút vật phẩm thành công trong ngày: '.number_format($total_order_items_day);
            $message .= "\n";
            $message .= '- Tổng số lệnh rút vật phẩm đang chờ trong ngày: '.number_format($total_order_items_day_pendding);
            $message .= "\n";
            $message .= '- Tổng số lệnh rút vật phẩm thanh toán lỗi trong ngày: '.number_format($total_order_items_day_errors);
            $message .= "\n";
            $message .= '- Tổng số lệnh rút vật phẩm giao dịch lỗi trong ngày: '.number_format($total_order_items_day_giao_dich_loi);
            $message .= "\n";
            $message .= '- Tổng số vật phẩm rút thành công trong ngày: '.number_format($total_items_day);
            $message .= "\n";
            $message .= '- Tổng số lệnh rút thành công trong tháng: '.number_format($total_order_items_month);
            $message .= "\n";
            $message .= '- Tổng số vật phẩm rút thành công trong tháng: '.number_format($total_items_month);
            $message .= "\n";
            $message .= '<b>* Mini game: </b>';
            $message .= "\n";
            $message .= '- Số người giao dịch trong ngày: '.number_format($count_user_rotation_in_day);
            $message .= "\n";
            $message .= '- Số lượng giao dịch trong ngày: '.number_format($count_rotation_in_day);
            $message .= "\n";
            $message .= '- Doanh thu trong ngày: '.number_format($sum_price_rotation_in_day);
            $message .= "\n";
            $message .= '<b>* Dịch vụ thủ công: </b>';
            $message .= "\n";
            $message .= '- Tổng số giao dịch: '.number_format($total_service_purchase);
            $message .= "\n";
            $message .= '- Tổng số giao dịch thành công: '.number_format($total_service_purchase_succsess);
            $message .= "\n";
            $message .= '- Tổng số giao dịch đang chờ: '.number_format($total_service_purchase_pendding);
            $message .= "\n";
            $message .= '- Tổng số giao dịch thất bại: '.number_format($total_service_purchase_errors);
            $message .= "\n";
            $message .= '- Doanh thu dịch vụ thủ công: '.number_format($total_money_service_purchase). " VNĐ";
            $message .= "\n";
            $message .= '- Số tiền CTV được nhận: '.number_format($total_money_service_purchase_ctv). " VNĐ";
            $message .= "\n";
            $message .= '- Lợi nhuận: '.number_format($total_money_service_purchase - $total_money_service_purchase_ctv). " VNĐ";
            $message .= "\n"; 
            $message .= '<b>* Dịch vụ tự động: </b>';
            $message .= "\n";
            $message .= '- Tổng số giao dịch: '.number_format($total_service_purchase_auto);
            $message .= "\n";
            $message .= '- Tổng số giao dịch thành công: '.number_format($total_service_purchase_succsess_auto);
            $message .= "\n";
            $message .= '- Tổng số giao dịch đang chờ: '.number_format($total_service_purchase_pendding_auto);
            $message .= "\n";
            $message .= '- Tổng số giao dịch thất bại: '.number_format($total_service_purchase_errors_auto);
            $message .= "\n";
            $message .= '- Doanh thu dịch vụ tự động: '.number_format($total_money_service_purchase_auto). " VNĐ";
            $message .= "\n";	    
            $message .= '-Tổng số vật phẩm cộng:';            
            foreach ($sum_plus_item as $key) {
                  $message .= "\n";
                  $message .= config('module.minigame.game_type.'.$key->itemtype->parent_id).': '.$key->amount;
            }
            $message .= '-Tổng số vật phẩm trừ:';            
            foreach ($sum_minus_item as $key) {
                  $message .= "\n";
                  $message .= config('module.minigame.game_type.'.$key->itemtype->parent_id).': '.$key->amount;
            }
            $message .= "\n";
            Helpers::TelegramNotify($message,$channel_id);
         }
         catch (\Exception $e) {
            $message = "Đã xảy ra lỗi trong quá trình thống kê. ERROR ".$e->getMessage();
            Helpers::TelegramNotify($message);
         }
    }
}
