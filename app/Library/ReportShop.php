<?php


namespace App\Library;


use App\Models\Charge;
use App\Models\Item;
use App\Models\Order;
use App\Models\PlusMoney;
use App\Models\Shop;
use App\Models\SocialAccount;
use App\Models\Txns;
use App\Models\TxnsVp;
use App\Models\User;
use Carbon\Carbon;

class ReportShop
{
    public function __construct($time, $shop_id, $order_group = '')
    {
        try {
            $shop = Shop::query()->find($shop_id);

            if ($shop) {
                $time = $time->format('Y-m-d');
                if ($shop->status == 1) {

                    // NGƯỜI DÙNG

                    // hình thức đăng kí
                    $user_query = User::query()->where('shop_id', $shop_id)->whereDate('created_at', $time);

                    $social_user_query = SocialAccount::query()->where('shop_id', $shop_id)->whereDate('created_at', $time);

                    $social_user_data = $social_user_query->selectRaw('COALESCE(SUM(IF(provider ="facebook",1,0)),0) as user_facebook')->selectRaw('COALESCE(SUM(IF(provider ="google",1,0)),0) as user_google')->first();

                    $user_data = $user_query
                        ->selectRaw('COUNT(*) as total_new_user') //Số người dùng đăng kí mới
                        ->selectRaw('COALESCE(SUM(IF(account_type = 2,balance,0)),0) as total_balance_user') //Tổng số dư thành viên
                        ->selectRaw((int)$social_user_data->user_facebook . ' as total_register_facebook') //Số người dùng đăng kí mới qua facebook
                        ->selectRaw((int)$social_user_data->user_google . ' as total_register_google') //Số người dùng đăng kí mới qua google
                        ->selectRaw('COUNT(*) - ' . ((int)$social_user_data->user_google + (int)$social_user_data->user_facebook) . ' as total_register_live') //Số người dùng đăng kí trực tiếp
                        ->first();

                    //Tỷ lệ giao dịch
                    $txns_query = Txns::query()->whereNotNull('txns.user_id')->where('txns.shop_id', $shop_id);

                    //Giao dịch phát sinh lớn nhất.
                    $txns_biggest = (clone $txns_query)->with('user')->where('shop_id', $shop_id)->whereDate('created_at', $time)
                        ->orderBy('amount', 'DESC')->first();

                    if ($txns_biggest) {
                        $txns_biggest_text = $txns_biggest->user->username . ' - ' . number_format($txns_biggest->amount) . ' đ';
                    } else {
                        $txns_biggest_text = 0;
                    }
                    // Người dùng có giao dịch trong ngày;
                    $txns_query_clone_data = (clone $txns_query)->whereDate('txns.created_at', '<=', $time)
                        ->join('users', 'users.id', 'txns.user_id')->whereDate('users.created_at', $time)
                        ->selectRaw('COUNT(DISTINCT user_id) as new_user_has_txns')
                        ->first();

                    //Tài khoản có số dư lớn nhất
                    $user_biggest_query = User::query()->where('shop_id', $shop_id)->where('account_type', 2);
                    $user_biggest = (clone $user_biggest_query)->orderBy('balance', 'DESC')->first();
                    $total_balance_user = (clone $user_biggest_query)->selectRaw('COALESCE(SUM(balance),0) as total')->first();
                    if ($user_biggest) {
                        $user_biggest_text = $user_biggest->username . ' - ' . number_format($user_biggest->balance) .' đ';
                    } else {
                        $user_biggest_text = 0;
                    }
                    $total_balance_user = number_format($total_balance_user->total) . ' đ';
                    $txns_user_data = (clone $txns_query)->whereDate('created_at', $time)
                        ->selectRaw($txns_query_clone_data->new_user_has_txns . ' as new_user_has_txns') //Tổng số người dùng đăng kí mới có giao dịch trong ngày
                        ->selectRaw('COUNT(DISTINCT user_id) as user_has_txns') //Tổng số người dùng có giao dịch trong ngày
                        ->selectRaw("'$user_biggest_text' as balance_user_biggest") //Thành viên có số dư lớn nhất
                        ->selectRaw("'$total_balance_user' as total_balance_user") //Tổng số dư thành viên
                        ->selectRaw("'$txns_biggest_text' as txns_biggest") //Giao dịch phát sinh lớn nhất trong ngày
                        ->first();

                    //nạp tiền qua thẻ cào;
                    $charge_query = Charge::query()->where('shop_id', $shop_id)->whereDate('created_at', $time);
                    if ($shop->ratio_atm > 100) {
                        $charge_query->selectRaw('coalesce(SUM(IF(status = 1,amount,0)) - SUM(money_received),0) as cost_charge');//Chi phí nạp tiền bằng thẻ cào
                    } else {
                        $charge_query->selectRaw('0 as cost_charge');//Chi phí nạp tiền bằng thẻ cào
                    }
                    $data_charge = $charge_query
                        ->selectRaw('count(*) as total_txns') // Số lượng giao dịch
                        ->selectRaw('COUNT(DISTINCT user_id) as total_user') //Số người giao dịch
                        ->selectRaw('coalesce(SUM(IF(status = 1,1,0)),0) as total_success') //Số giao dịch thành công
                        ->selectRaw('coalesce(SUM(IF(status = 2,1,0)),0) as total_pending') //Số giao dịch đang chờ hệ thống xử lý
                        ->selectRaw('coalesce(SUM(IF(status = 0,1,0)),0) as total_error') //Số giao dịch thất bại

                        ->selectRaw('coalesce(SUM(IF(status = 1,real_received_amount,0)) / SUM(IF(status = 1,1,0)),0) as total_success_avg') // Giá trị giao dịch trung bình (chỉ tính giao dịch thành công)
                        ->selectRaw('coalesce(SUM(IF(status = 1,amount,0)),0) as total_amount_value') //Tổng số tiền nạp theo mệnh giá
                        ->selectRaw('coalesce(SUM(IF(status = 1,real_received_amount,0)),0) as total_real_received_price') //Tổng số tiền nạp thành công thực nhận
                        ->selectRaw('CONCAT(coalesce(SUM(IF(status = 1,real_received_amount,0)) / SUM(IF(status = 1,amount,0)) * 100,0),"%") as density_success') //Tỷ trọng / Tổng số tiền nạp
                        ->first();
                    // nạp tiền qua ATM
                    $recharge_atm_query = Order::query()->where('module', config('module.transfer.key'))->where('shop_id', $shop_id)
                        ->whereDate('created_at', $time);

                    $recharge_atm_data = $recharge_atm_query
                        ->selectRaw('COUNT(*) as total_txns') // Số lượng giao dịch
                        ->selectRaw('COUNT(DISTINCT author_id) as total_user') // Số người giao dịch
                        ->selectRaw('COALESCE(SUM(IF(status = 1,price,0)),0) as total_money') // Tổng số tiền nạp
                        ->selectRaw('COALESCE(SUM(IF(status = 1,real_received_price,0)),0) as total_real_received_price') //Tổng số tiền KH thực nhận trong hệ thống
                        ->selectRaw('ROUND(COALESCE(SUM(IF(status = 1,1,0)) / COUNT(*) * 100,0),2) as ratio_success') //Tỷ lê giao dịch thành công
                        ->selectRaw('ROUND(COALESCE(SUM(IF(status = 1,price,0)) / SUM(IF(status = 1,1,0)) ,0),0) as total_success_avg') //Giá trị giao dịch trung bình (chỉ tính giao dịch thành công)
                        ->selectRaw('COALESCE(SUM(IF(status = 1,real_received_price,0)),0) - COALESCE(SUM(IF(status = 1,price,0)),0) as recharge_fee_money') //Phí nạp ATM
                        ->first();

                    //Cộng trừ tiền thủ công trong hệ thống quản trị
                    $plus_money_query = PlusMoney::query()->join('users', 'users.id', 'plus_money.user_id')->where('plus_money.status', 1)
                        ->where('users.shop_id', $shop_id)->whereDate('plus_money.created_at', $time);

                    $plus_money_data = $plus_money_query
                        ->selectRaw('COALESCE(SUM(IF(plus_money.is_add = 1,1,0)),0) as total_cmd_add') //Tổng số lệnh cộng tiền
                        ->selectRaw('COALESCE(SUM(IF(users.account_type = 2 AND plus_money.is_add = 1,1,0)),0) as total_user_was_add') //Tổng số tài khoản người dùng được cộng tiền
                        ->selectRaw('COALESCE(SUM(IF(users.account_type = 2 AND plus_money.is_add = 1,amount,0)),0) as total_money_user_was_add') //Tổng số tiền được cộng cho người dùng

                        ->selectRaw('COALESCE(SUM(IF(plus_money.is_add = 0,1,0)),0) as total_cmd_minus') //Tổng số lệnh trừ tiền
                        ->selectRaw('COALESCE(SUM(IF(users.account_type = 2 AND plus_money.is_add = 0,1,0)),0) as total_user_was_minus') //Tổng số người dùng bị trừ tiền
                        ->selectRaw('COALESCE(SUM(IF(users.account_type = 2 AND plus_money.is_add = 0,amount,0)),0) as total_money_user_was_minus') //Tổng số tiền bị trừ cho người dùng
                        ->first();

                    //Bán Account
                    $account_query = Item::query()->where('items.module', 'acc')->whereNotNull('items.sticky')
                        ->where('items.shop_id', $shop_id)->whereDate('items.published_at', $time);

                    $turnover_account = (clone $account_query)->where('items.status', 0)
                            ->join('order', 'order.ref_id', 'items.id')
                            ->selectRaw('SUM(order.price) as turnover_account')
                            ->first()->turnover_account ?? 0;

                    $capital_expend = (clone $account_query)->where('items.status', 0)
                        ->with(['acc_txns' => function ($q) {
                            return $q->where('is_add', 1)->where('is_refund', 0)->latest();
                        }])->get()->sum(function ($item) {
                            return $item->acc_txns[0]->amount ?? 0;
                        });

                    $account_data = $account_query
                        ->selectRaw('COALESCE(SUM(IF(status != 1 , 1 ,0)),0) as total_txsns') //Tổng số giao dịch
                        ->selectRaw('COALESCE(SUM(IF(status = 0,1,0)),0) as total_success') // Tổng số đơn hàng thành công (%)
                        ->selectRaw('COALESCE(SUM(IF(status = 3,1,0)),0) as total_check_info') //Tổng số đơn hàng đang check thông tin
                        ->selectRaw('COALESCE(SUM(IF(status = 4,1,0))) as total_wrong_password') //Tổng số đơn hàng sai mật khẩu
                        ->selectRaw($turnover_account . ' as total_turnover') //Doanh thu account
                        ->selectRaw($capital_expend . ' as capital_expend') //Giá vốn account
                        ->selectRaw($turnover_account - $capital_expend . ' as total_profit') //Lợi nhuận account
                        ->first();

                    // Bán thẻ
                    $store_card_query = Order::query()->where('module', 'store-card')->where('shop_id', $shop_id)->whereDate('created_at', $time);
                    $store_card_data = $store_card_query
                        ->selectRaw('COUNT(*) as total_txns') //Tổng số giao dịch
                        ->selectRaw('COALESCE(SUM(IF(status = 1,1,0)),0) as total_success') //Tổng số đơn hàng thành công
                        ->selectRaw('COALESCE(SUM(IF(status = 2,1,0)),0) as total_pending') //Tổng số đơn hàng đang chờ
                        ->selectRaw('COALESCE(SUM(IF(status = 0,1,0)),0) as total_error') //Tổng số đơn hàng thất bại
                        ->selectRaw('COALESCE(SUM(IF(status = 1,real_received_price,0)),0) as total_turnover') //Doanh thu mua thẻ
                        ->selectRaw('0 as capital_expend') //Giá vốn thẻ
                        ->selectRaw('0 as total_profit') //Lợi nhuận mua thẻ
                        ->first();

                    // Minigame & Rút vật phẩm
                    $minigame_query = Order::query()->where('module', 'minigame-log')->whereNull('acc_id')->where('shop_id', $shop_id)
                        ->whereDate('created_at', $time);

                    $minigame_data = $minigame_query
                        ->selectRaw('COUNT(DISTINCT author_id) as total_user') //Số người giao dịch trong ngày
                        ->selectRaw('COUNT(*) as total_txns') //Số lượng giao dịch trong ngày
                        ->selectRaw('COALESCE(SUM(price),0) as total_turnover') //Doanh thu trong ngày
                        ->first();

                    $withdraw_item_query = Order::query()->where('module', 'withdraw-item')->whereNull('acc_id')->where('shop_id', $shop_id)
                        ->whereDate('created_at', $time);
                    $withdraw_item_data = $withdraw_item_query
                        ->selectRaw('COALESCE(SUM(IF(status = 1,1,0)),0) as total_cmd_withdraw_success') //Tổng số lệnh rút vật phẩm thành công trong ngày
                        ->selectRaw('COALESCE(SUM(IF(status = 0,1,0)),0) as total_cmd_withdraw_pending') //Tổng số lệnh rút vật phẩm đang chờ trong ngày
                        ->selectRaw('COALESCE(SUM(IF(status = 2,1,0)),0) as total_cmd_withdraw_error') //Tổng số lệnh rút vật phẩm thanh toán lỗi trong ngày
                        ->selectRaw('COALESCE(SUM(IF(status = 3,1,0)),0) as total_cmd_withdraw_txns_error') //Tổng số lệnh rút vật phẩm giao dịch lỗi trong ngày
                        ->selectRaw('COALESCE(SUM(IF(status = 1 , price , 0)),0) as total_withdraw_item_success') //Tổng số vật phẩm rút thành công trong ngày
                        ->selectRaw('0 as cost_withdraw') //Chi phí rút vật phẩm
                        ->selectRaw('0 as turnover_temp') //Lợi nhuận tạm tính
                        ->first();

                    $minigame_withdraw_item_data = (object)array_merge($minigame_data->toArray(), $withdraw_item_data->toArray());
                    // Dịch vụ thủ công
                    $service_query = Order::query()->where('module', 'service-purchase')->where('gate_id', 0)->where('shop_id', $shop_id)
                        ->whereDate('created_at', $time);

                    $total_service_pending = (clone $service_query)->selectRaw('COALESCE(SUM(IF(status = 1 , 1, 0)),0) as total')->first();

                    $service_data = $service_query
                        ->selectRaw('COUNT(*) as total_txns') //Số giao dịch phát sinh trong ngày
                        ->selectRaw('COALESCE(SUM(IF(status != 0 AND status != 3 , 1, 0)),0) as total_success') //Số giao dịch KH thanh toán thành công
                        ->selectRaw($total_service_pending->total . ' as total_pending') //Tổng số giao dịch đang chờ trên website
                        ->selectRaw('COALESCE(SUM(IF(status = 4 , 1, 0)),0) as total_ctv_success') //Số giao dịch CTV hoàn tất
                        ->selectRaw('COALESCE(SUM(IF(status = 0 , 1, 0)),0) as total_ctv_cancel') //Số giao dịch CTV hủy
                        ->selectRaw('COALESCE(SUM(IF(status != 0 AND status != 3 , price, 0)),0) as turnover_success') //Doanh thu thành công
                        ->selectRaw('COALESCE(SUM(IF(status = 4 , price, 0)),0) as turnover_ctv_success') //Doanh thu dịch vụ thủ công CTV hoàn tất
                        ->selectRaw('ROUND(COALESCE(SUM(IF(status = 4 , real_received_price_ctv, 0)),0),0) as cost_price_success') //Giá vốn đơn hoàn tất
                        ->selectRaw('ROUND(COALESCE(SUM(IF(status = 4, price, 0)) - COALESCE(SUM(IF(status = 4,real_received_price_ctv, 0)),0),0),0) as turnover_txns_success') //Lợi nhuận đơn hoàn tất
                        ->first();
                    // Dịch vụ tự động
                    $service_auto_query = Order::query()->where('module', 'service-purchase')->where('gate_id', 1)
                        ->where('shop_id', $shop_id)->whereDate('created_at', $time);
                    $plus_item_query = TxnsVp::query()->where('shop_id', $shop_id)->whereDate('created_at', $time);
                    // cộng/trừ vật phẩm thủ công
                    $plus_item_data = $plus_item_query
                        ->selectRaw('COALESCE(SUM(IF(is_add = 1,amount,0)),0) as item_add')
                        ->selectRaw('COALESCE(SUM(IF(is_add = 0,amount,0)),0) as item_minus')
                        ->first();

                    $service_auto_data = $service_auto_query
                        ->selectRaw('COUNT(*) as total_txns') //Tổng số giao dịch trong ngày
                        ->selectRaw('COALESCE(SUM(IF(status = 4 ,1,0)),0) as total_success') //Số giao dịch hoàn tất trong ngày
                        ->selectRaw('COALESCE(SUM(IF(status = 5 ,1,0)),0) as total_error') //Số giao dịch thất bại trong ngày
                        ->selectRaw('COALESCE(SUM(IF(status = 6 ,1,0)),0) as total_lost_item') //Tổng số giao dịch mất item
                        ->selectRaw('COALESCE(SUM(IF(status = 1 ,1,0)),0) as total_pending') //Tổng số giao dịch đang chờ
                        ->selectRaw('COALESCE(SUM(price),0) as turnover_payment_success') //Doanh thu đơn hàng thanh toán thành công
                        ->selectRaw('COALESCE(SUM(price_ctv),0) as turnover_ctv_success') //Doanh thu đơn hàng CTV hoàn tất
                        ->first();

                    $groups = json_decode($shop->telegram_config);
                    if ($groups) {
                        foreach ($groups as $group) {
                            if ($group->status) {
                                $chanel_id = $group->group_id;
                                $message = '';
                                $message .= 'BÁO CÁO NGÀY ' . Carbon::createFromFormat('Y-m-d', $time)
                                        ->format('d-m-Y') . ' tại ' . $shop->domain;
                                $message .= "\n";
                                $message .= "\n";

                                $config_group = json_decode($group->config);

                                $index_group = 1;
                                foreach ($config_group as $report) {

                                    $report_key = '';
                                    if ($report->report == 'total-quantity-config') {
                                        $report_key = 'total_output';
                                        $message .= '<b>'.$this->numberToRomanRepresentation($index_group).'.' . config('module.telegram.report.total_output.title') . '</b>';
                                        $message .= "\n";
                                    }
                                    if ($report->report == 'user-config') {
                                        $report_key = 'user';
                                        $message .= "\n";
                                        $message .= '<b>'.$this->numberToRomanRepresentation($index_group). '.' . config('module.telegram.report.user.title') . '</b>';
                                        $message .= "\n";
                                    }
                                    $index_module = 1;
                                    foreach ($report->modules as $module_key => $module) {
                                        $count_tick = 0;
                                        foreach ($module->index as $key_arr_index => $index) {
                                            $index_key = key((array)$index);
                                            $index_status = $index->{$index_key};
                                            if ($index_status) {
                                                ++$count_tick;
                                            }
                                        }
                                        if ($count_tick) {
                                            $message .= "\n";
                                            $message .= '<b>'.$index_module .'. ' . config('module.telegram.report.' . $report_key . '.module')[$module_key]['title'] . '</b>';
                                            $message .= "\n";
                                            $message .= "\n";
                                        }

                                        foreach ($module->index as $key_arr_index => $index) {
                                            $index_key = key((array)$index);
                                            $index_status = $index->{$index_key};
                                            if ($index_status) {
                                                $message .= '- ' . config('module.telegram.report.' . $report_key . '.module')[$module_key]['indexs'][$key_arr_index]['title'];

                                                switch ($module->module) {
                                                    case 'charge':
                                                        if ($index_key === 'density_success') {
                                                            $message .= ': ' . $data_charge->{$index_key};
                                                        } else {
                                                            $message .= ': ' . number_format($data_charge->{$index_key});
                                                        }
                                                        break;
                                                    case 'transfer':
                                                        if ($index_key === 'ratio_success') {
                                                            $message .= ': ' . $recharge_atm_data->{$index_key};
                                                        } else {
                                                            $message .= ': ' . number_format($recharge_atm_data->{$index_key});
                                                        }
                                                        break;
                                                    case 'plus_money':
                                                        $message .= ': ' . number_format($plus_money_data->{$index_key});
                                                        break;
                                                    case 'account':
                                                        if ($index_key === 'total_success'){
                                                            $message .= ': ' . number_format($account_data->total_success);
                                                            if ($account_data->total_txsns) {
                                                                $message .= '('.($account_data->total_success / $account_data->total_txsns * 100).'%)';
                                                            }
                                                        } else {
                                                            $message .= ': ' . number_format($account_data->{$index_key});
                                                        }
                                                        break;
                                                    case 'store_card':
                                                        $message .= ': ' . number_format($store_card_data->{$index_key});
                                                        break;
                                                    case 'minigame_withdraw_item':
                                                        $message .= ': ' . number_format($minigame_withdraw_item_data->{$index_key});
                                                        break;
                                                    case 'service':
                                                        $message .= ': ' . number_format($service_data->{$index_key});
                                                        break;
                                                    case 'service_auto':
                                                        $message .= ': ' . number_format($service_auto_data->{$index_key});
                                                        break;
                                                    case 'type_register':
                                                        $message .= ': ' . number_format($user_data->{$index_key});
                                                        break;
                                                    case 'ratio_txns':
                                                        if ($index_key === 'balance_user_biggest'|| $index_key === 'total_balance_user') {
                                                            $message .= ': ' . $txns_user_data->{$index_key};
                                                        } else {
                                                            $message .= ': ' . number_format($txns_user_data->{$index_key});
                                                        }
                                                        break;
                                                    default:
                                                        break;
                                                }

                                                $message .= "\n";
                                            }
                                        }
                                        ++$index_module;
                                    }
                                    ++$index_group;
                                }
                                if ($order_group) {
                                    if ($group->status) {
                                        if ($order_group == $group->order) {
                                            Helpers::TelegramNotify($message, $chanel_id);
                                        }
                                    }
                                } else {
                                    if ($group->status) {
                                        Helpers::TelegramNotify($message, $chanel_id);
                                    }
                                }
                            }
                        }
                    }

                }
            }
        } catch (\Exception $e) {
            $message = "Đã xảy ra lỗi trong quá trình thống kê: " . $e->getMessage();
            Helpers::TelegramNotify($message);
        }
    }

    private function numberToRomanRepresentation($number)
    {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }
}
