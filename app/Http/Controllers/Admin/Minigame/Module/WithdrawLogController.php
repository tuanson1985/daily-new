<?php

namespace App\Http\Controllers\Admin\Minigame\Module;

use App\Exports\ExportData;
use App\Http\Controllers\Controller;
use App\Library\HelperPermisionShopMinigame;
use App\Library\Helpers;
use App\Library\HelpItemAdd;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shop;
use App\Models\Shop_Group;
use App\Models\TxnsVp;
use App\Models\User;
use Carbon\Carbon;
use Excel;
use Html;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class WithdrawLogController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;
    public function __construct(Request $request)
    {


        $this->module=$request->segments()[1]??"";
        $this->moduleCategory=explode("-", $this->module)[0].'-category';
        //set permission to function
        $this->middleware('permission:'. $this->module);
        $this->middleware('permission:withdraw-item-recharge', ['only' => ['recharge']]);

        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'.index'),
                'title' => __(config('module.minigame.'.$this->module.'.title'))
            ];
        }
    }

    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);

        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        if($request->ajax) {

            $datatable= Order::with('author')->with('shop')
                ->with('item_ref')->whereIn('shop_id',$arr_permission)
                ->where(function($q) use($request){
                    $q->orWhere('order.module', 'withdraw-item');
                    $q->orWhere('order.module', 'withdraw-itemrefund');
                });

            if (session('shop_id')) {
                $datatable->where('shop_id',session('shop_id'));
            }

            if ($request->filled('payment_type')) {

                $datatable->where('payment_type',$request->get('payment_type'));
            }

            if ($request->filled('module')) {
                if ($request->get('module') == 1){
                    $datatable->where('module', 'withdraw-item');
                }elseif ($request->get('module') == 3){
                    $datatable->where('module', 'withdraw-itemrefund');
                }
            }

            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', $request->get('id'));
                    $q->orWhere('request_id', $request->get('id'));
                });
            }

            if ($request->filled('title'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('idkey', 'LIKE', '%' . $request->get('title') . '%');
                    $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
                });
            }

            if ($request->filled('status')) {
                $datatable->where('status',$request->get('status') );
            }

            if ($request->filled('started_at')) {
                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }

            if ($request->filled('started_updated_at')) {
                $datatable->where('updated_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_updated_at')));
            }
            if ($request->filled('ended_updated_at')) {
                $datatable->where('updated_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_updated_at')));
            }

            $datatableTotal=$datatable->clone();

            $user_item = User::query();
            if ($request->filled('payment_type'))
            {
                $payment_type = $request->get('payment_type');
                if ($payment_type == 11)
                {
                    $user_item = $user_item->selectRaw('sum(COALESCE(xu_num,0)) total_item');
                }
                elseif ($payment_type == 12)
                {
                    $user_item = $user_item->selectRaw('sum(COALESCE(gem_num,0)) total_item');
                }
                elseif ($payment_type == 13)
                {
                    $user_item = $user_item->selectRaw('sum(COALESCE(robux_num,0)) total_item');
                }
                elseif ($payment_type == 14)
                {
                    $user_item = $user_item->selectRaw('sum(COALESCE(coin_num,0)) total_item');
                }
                else{
                    $user_item = $user_item->selectRaw('sum(COALESCE(ruby_num'.$payment_type.',0)) total_item');
                }
            }
            else{
                $user_item = $user_item->selectRaw('sum(COALESCE(ruby_num1,0) + COALESCE(ruby_num2,0) + COALESCE(ruby_num3,0) + COALESCE(ruby_num4,0) +
            COALESCE(ruby_num5,0) + COALESCE(ruby_num6,0) + COALESCE(ruby_num7,0) + COALESCE(ruby_num8,0) + COALESCE(ruby_num9,0)
             + COALESCE(ruby_num10,0) + COALESCE(robux_num,0) + COALESCE(xu_num,0) + COALESCE(gem_num,0) + COALESCE(coin_num,0)) total_item');
            }

            if(session('shop_id')) {
                $user_item = $user_item->where('shop_id', session('shop_id'));
            }

            $user_item  = $user_item->where('status', 1)->where('account_type', 2)->first();

            return \datatables()->eloquent($datatable)

                ->only([
                    'id',
                    'idkey',
                    'title',
                    'sticky',
                    'locale',
                    'params',
                    'module',
                    'author_id',
                    'price',
                    'price_base',
                    'price_input',
                    'author',
                    'item_ref',
                    'status',
                    'payment_type',
                    'request_id',
                    'shop',
                    'created_at',
                    'updated_at',
                    'trainid',
                    'action'
                ])
                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->addColumn('action', function($row) {
                    $temp = "<button type='button'  rel=\"".route('admin.withdraw-item.shows',$row->id)."\" class=\" btn btn-outline-secondary load-modal \" title=\"Show\">Chi tiết</button>";
                    return $temp;
                })
                ->with('totalSumary', function() use ($datatableTotal,$user_item) {
                    return $datatableTotal=$datatableTotal->first([
                        DB::raw(''.$user_item->total_item.' as total_item'),
                        DB::raw('COUNT(order.id) as total_record'),
                        DB::raw('COUNT(CASE WHEN order.status = 1 THEN order.id ELSE NULL END) as total_record_complete'),
                        DB::raw('COUNT(CASE WHEN order.status = 2 OR status = 3 THEN order.id ELSE NULL END) as total_record_delete'),
                        DB::raw('COUNT(CASE WHEN order.status = 0 OR status = 7 OR status = 9 THEN order.id ELSE NULL END) as total_record_pendding'),
                        DB::raw('SUM(order.price) as total_withdraw_item'),
                        DB::raw('SUM(CASE WHEN order.status = 1 THEN order.price ELSE 0 END) as total_withdraw_item_complete'),
                        DB::raw('SUM(CASE WHEN order.status = 2 OR status = 3 THEN order.price ELSE 0 END) as total_withdraw_item_delete'),
                        DB::raw('SUM(CASE WHEN order.status = 0 OR status = 7 OR status = 9 THEN order.price ELSE 0 END) as total_withdraw_item_pendding'),
                    ]);
                })
                ->toJson();
        }

        $listgametype = Item::where('module', config('module.minigame.module.gametype'))
            ->whereIn('parent_id', range(1, 10))
            ->where('status', 1)->pluck('title','parent_id')->toArray();


        return view('admin.minigame.module.withdrawlog.index')
            ->with('module', $this->module)
            ->with('listgametype', $listgametype)
            ->with('arr_permission', $arr_permission)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    public function changeStatus(Request $request)
    {

        $msg = "";
        $status = 'ERROR';
        $rubystatus = 1;
        $id=$request->id;

        $order = Order::where('id',$id)->first();

        if (!isset($order)){
            return response()->json([
                'success'=>false,
                'message'=>__('Đơn hàng không tồn tại.!'),
                'status'=>0
            ]);
        }

        if ($order->payment_type == 13 || $order->payment_type == 11 || $order->payment_type == 12 || $order->payment_type == 14){
            return response()->json([
                'success'=>false,
                'message'=>__('Dịch vụ không tồn tại.!'),
                'status'=>0
            ]);
        }

        DB::beginTransaction();
        try {

            $rubystatus = $request->status;

            DB::table('order')
                ->where('id', $id)
                ->update(['status' => $rubystatus, 'processor_id' => Auth::user()->id]);

            //nếu là hủy cộng lại ruby cho user
            if($rubystatus=="2"){

                if (!$request->filled('w_content')) {
                    return response()->json([
                        'success'=>false,
                        'message'=>__('Vui lòng nhập nội dung hủy%!'),
                        'status'=>0
                    ]);
                }

                $itemTran = Order::where('id',$id)->firstOrFail();

                $itemTran->content = $request->w_content;

                $itemTran->save();

                //lấy loại vật phẩm lưu khi tạo giao dịch
                $type = $itemTran->payment_type;
                $userid = $request->userid;
                $userTransaction = User::where('id',$userid)->lockForUpdate()->firstOrFail();
                $balance_item_txns = $userTransaction['ruby_num'.$type];
                $amount =  $itemTran->price;
                $userTransaction['ruby_num'.$type] = $userTransaction['ruby_num'.$type] + $itemTran->price;
                $userTransaction->save();

                $txns = TxnsVp::create([
                    'trade_type' => config('module.txnsvp.trade_type.refund'),
                    'is_add' => '1',
                    'user_id' => $userTransaction->id,
                    'amount' => $amount,
                    'last_balance' => $balance_item_txns + $amount,
                    'description' => "Hoàn ".$amount." rút vật phẩm thất bại gói rút" . $itemTran->ref_id ,
                    'ref_id' => $itemTran->id,
                    'status' => 1,
                    'shop_id' => $userTransaction->shop_id,
                    'order_id' => $itemTran->id,
                    'item_type' => $type
                ]);

            }
            $msg = $id;
            $status = 'OK';


        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            $status = 'ERROR';
            $msg = "Có lỗi phát sinh.Xin vui lòng thử lại !";
        }
        DB::commit();
        return response()->json(array('msg'=> $msg, 'status' => $status,'rubystatus' => $rubystatus), 200);
    }

    public function exportExcel(Request $request){

        ini_set('max_execution_time', 2400); //20 minutes

        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        ActivityLog::add($request, 'Xuất excel thống kê trúng thưởng minigame txnsvp-export');

        $datatable= Order::with('author')->with('shop')->with('item_ref')->whereIn('shop_id',$arr_permission)
            ->where(function($q) use($request){
                $q->orWhere('order.module', 'withdraw-item');
                $q->orWhere('order.module', 'withdraw-itemrefund');
            });

        if (session('shop_id')) {
            $datatable->where('shop_id',session('shop_id'));
        }

        if ($request->filled('payment_type')) {
            $datatable->where('payment_type',$request->get('payment_type'));
        }

        if ($request->filled('module')) {
            if ($request->get('module') == 1){
                $datatable->where('module', 'withdraw-item');
            }elseif ($request->get('module') == 2){

            }elseif ($request->get('module') == 3){
                $datatable->where('module', 'withdraw-itemrefund');
            }
        }

        if ($request->filled('title'))  {
            $datatable->where(function($q) use($request){
                $q->orWhere('idkey', 'LIKE', '%' . $request->get('title') . '%');
                $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
            });
        }

        if ($request->filled('status')) {
            $datatable->where('status',$request->get('status') );
        }

        if ($request->filled('started_at')) {
            $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
        }
        if ($request->filled('ended_at')) {
            $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
        }

        $datatable = $datatable->get();
        $data = [
            'data' => $datatable,
        ];

//        return view('admin.minigame.module.withdrawlog.export_excel')->with('data',$data);
        return Excel::download(new ExportData($data,view('admin.minigame.module.withdrawlog.export_excel')), 'Thống kê biến động số dư rút vật phẩm ' . time() . '.xlsx');
    }

    public function showReportWithdrawItem(Request $request,$id){

        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        $datatable = Order::with('author')->with('shop')
            ->with('item_ref')
            ->whereIn('shop_id',$arr_permission)
            ->with('order_detail')
            ->where(function($q) use($request){
                $q->orWhere('order.module', 'withdraw-item');
                $q->orWhere('order.module', 'withdraw-itemrefund');
            })->where('id',$id)->first();

        $gametype = Item::where('module', 'gametype')->where('parent_id', $datatable->payment_type)->first();

        return view('admin.minigame.module.withdrawlog.show')->with('datatable',$datatable)->with('gametype',$gametype);
    }

    public function recharge(Request $request){

        $input = explode(',',$request->id);

        $result="";

        foreach ($input??[] as $idOrderNeedRecharge){

            // Start transaction!
            DB::beginTransaction();
            try {

                $order = Order::lockForUpdate()->findOrFail($idOrderNeedRecharge);

                if($order->status!= 7){
                    DB::rollback();
                    continue;
                }

                //convert module dịch vụ sang rút minigame

                $shop = Shop::findorFail($order->shop_id);

                $type = $order->payment_type;

                $provider = config('module.minigame.game_type_map.'.$type);

                $package_sticky = 1;

                if (isset($order->sticky)){
                    $package_sticky = $order->sticky;
                }

                $payment_gateways = config('module.minigame.payment_gateway.'.$package_sticky);

                if (!isset($payment_gateways)){
                    $payment_gateways = 'SMS';
                }

                if (!isset($provider)){
                    DB::rollback();
                    continue;
                }

                if ($provider == 'lienquan' || $provider == 'freefire' || $provider == 'lienminh' || $provider == 'bns' ||
                    $provider == 'ads' || $provider == 'fo4m' || $provider == 'fo4' || $provider == 'pubgm' || $provider == 'codm'){
                    if ($provider == "freefire" || $provider == "pubgm") {

                        $id = $order->idkey;
                        $username = "";
                        $password = "";
                        if(empty($id)){
                            DB::rollback();
                            continue;
                        }
                    }else {

                        $id = "";
                        $username = $order->idkey;
                        $password = $order->title;
                        if(empty($username) || empty($password)){
                            DB::rollback();
                            continue;
                        }
                    }

                    $amount = $order->price;

                    $item = 0;
                    if($provider == "lienminh"){
                        if ($payment_gateways == 'SMS'){
                            if($amount == "16"){
                                $item = 10;
                            }else if($amount == "32"){
                                $item = 20;
                            }else if($amount == "84"){
                                $item = 50;
                            }else if($amount == "168"){
                                $item = 100;
                            }else if($amount == "340"){
                                $item = 200;
                            }else if($amount == "856"){
                                $item = 500;
                            }else{
                                DB::rollback();
                                continue;
                            }
                        }else{
                            DB::rollback();
                            continue;
                        }

                    }
                    else if($provider == "lienquan"){
                        if ($payment_gateways == 'SMS'){
                            if($amount == "16"){
                                $item = 10;
                            }else if($amount == "32"){
                                $item = 20;
                            }else if($amount == "80"){
                                $item = 50;
                            }else if($amount == "160"){
                                $item = 100;
                            }else if($amount == "320"){
                                $item = 200;
                            }else if($amount == "800"){
                                $item = 500;
                            }else{
                                DB::rollback();
                                continue;
                            }
                        }
                        elseif ($payment_gateways == 'GARENA'){
                            if($amount == "40"){
                                $item = 20;
                            }else if($amount == "100"){
                                $item = 50;
                            }else if($amount == "200"){
                                $item = 100;
                            }else if($amount == "400"){
                                $item = 200;
                            }else if($amount == "1000"){
                                $item = 500;
                            }else{
                                DB::rollback();
                                continue;
                            }
                        }
                        else{
                            DB::rollback();
                            continue;
                        }

                    }
                    else if($provider == "freefire"){
                        if ($payment_gateways == 'SMS'){
                            if($amount == "40"){
                                $item = 10;
                            }else if($amount == "88"){
                                $item = 20;
                            }else if($amount == "220"){
                                $item = 50;
                            }else if($amount == "440"){
                                $item = 100;
                            }else if($amount == "880"){
                                $item = 200;
                            }else if($amount == "2200"){
                                $item = 500;
                            }
                            else{
                                DB::rollback();
                                continue;
                            }
                        }
                        elseif ($payment_gateways == 'GARENA'){
                            if($amount == "110"){
                                $item = 20;
                            }else if($amount == "275"){
                                $item = 50;
                            }else if($amount == "550"){
                                $item = 100;
                            }else if($amount == "1100"){
                                $item = 200;
                            }else if($amount == "2750"){
                                $item = 500;
                            }
                            else{
                                DB::rollback();
                                continue;
                            }
                        }
                        else{
                            DB::rollback();
                            continue;
                        }

                    }
                    else if($provider == "pubgm"){
                        if ($payment_gateways == 'SMS'){
                            if($amount == "48"){
                                $item = 20;
                            }else if($amount == "119"){
                                $item = 50;
                            }else if($amount == "246"){
                                $item = 100;
                            }else if($amount == "252"){
                                $item = 200;
                            }else{
                                DB::rollback();
                                continue;
                            }
                        }else{
                            DB::rollback();
                            continue;
                        }

                    }
                    else if($provider == "bns"){
                        if ($payment_gateways == 'SMS'){
                            if($amount == "800"){
                                $item = 10;
                            }else if($amount == "1600"){
                                $item = 20;
                            }else if($amount == "4000"){
                                $item = 50;
                            }else if($amount == "8000"){
                                $item = 100;
                            }else if($amount == "16000"){
                                $item = 200;
                            }else{
                                DB::rollback();
                                continue;
                            }
                        }else{
                            DB::rollback();
                            continue;
                        }

                    }
                    else if($provider == "ads"){
                        if ($payment_gateways == 'SMS'){
                            if($amount == "800"){
                                $item = 10;
                            }else if($amount == "1600"){
                                $item = 20;
                            }else if($amount == "4000"){
                                $item = 50;
                            }else if($amount == "8000"){
                                $item = 100;
                            }else if($amount == "16000"){
                                $item = 200;
                            }
                            else{
                                DB::rollback();
                                continue;
                            }
                        }
                        else{
                            DB::rollback();
                            continue;
                        }
                    }
                    else if($provider == "fo4m"){
                        if ($payment_gateways == 'SMS'){
                            if($amount == "16"){
                                $item = 10;
                            }else if($amount == "32"){
                                $item = 20;
                            }else if($amount == "80"){
                                $item = 50;
                            }else if($amount == "168"){
                                $item = 100;
                            }else if($amount == "340"){
                                $item = 200;
                            }else{
                                DB::rollback();
                                continue;
                            }
                        }
                        else{
                            DB::rollback();
                            continue;
                        }
                    }
                    else if($provider == "fo4"){
                        if ($payment_gateways == 'SMS'){
                            if($amount == "16"){
                                $item = 10;
                            }else if($amount == "32"){
                                $item = 20;
                            }else if($amount == "80"){
                                $item = 50;
                            }else if($amount == "168"){
                                $item = 100;
                            }else if($amount == "340"){
                                $item = 200;
                            }
                            else{
                                DB::rollback();
                                continue;
                            }
                        }
                        else{
                            DB::rollback();
                            continue;
                        }

                    }
                    else{
                        $provider ='';
                    }

                    $order->update([
                        'status'=>0
                    ]);

                    //set tiến độ
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'module' => config('module.minigame.module.withdraw-item'),
                        'status' => 0,
                        'content' => "Đơn đã được thực hiện lại qua NCC",
                    ]);

                    DB::commit();

                    $result = HelpItemAdd::ITEMADD_CALLBACK($provider, $username, $password, $id, $item, "", $order->request_id, $shop->id,$payment_gateways);

                    if ($result &&  isset($result->status)) {
                        if($result->status==0){
                            // Update lại dữ liệu
                            $order->content = $result->message;
                            $order->save();

                            if (isset($result->user_balance)){
                                if($result->user_balance<1000000){
                                    $message="[" . Carbon::now() . "] "."[" . $request->root() . "] " . $shop->domain . " đã mua bắn kim cương và tài khoản tichhop.net còn dưới 1 triệu (Số dư hiện tại: ".number_format($result->user_balance).")" ;
                                    Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_notify_balance_tichhop_net'));
                                }
                            }

                            //set tiến độ
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.minigame.module.withdraw-item'),
                                'status' => 0,
                                'content' => "NCC tích hợp đã nhận lại đơn",
                            ]);

                            // Commit the queries!
                            DB::commit();
                            continue;
                        }
                        elseif ($result->status == 3){

                            if($result->status == -1){
                                $message="[" . Carbon::now() . "] "."[" . $request->root() . "] " . $shop->domain . " đã bắn kim cương và tài khoản tichhop.net còn dưới 1 triệu (Số dư hiện tại: ".number_format($result->user_balance).")" ;
                                Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_notify_balance_tichhop_net'));
                                $message_response="Tài khoản đại lý không đủ số dư";
                            }
                            else{
                                $message_response=$result->message??__('Kết nối với nhà cung cấp thất bại');
                                $message="[" . Carbon::now() . "] "."[" . $request->root() . "] " . $shop->domain . " đã bắn kim cương trên tichhop.net kết nối thất bại:".$message_response." ";
                                Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_notify_balance_tichhop_net'));
                            }

                            // Start transaction!
                            DB::beginTransaction();
                            try {

                                $order = Order::lockForUpdate()->findOrFail($order->id);

                                $order->update([
                                    'status' => 7,
                                ]);//trạng thái hủy

                                //set tiến độ hủy
                                OrderDetail::create([
                                    'order_id' => $order->id,
                                    'module' => config('module.minigame.module.withdraw-item'),
                                    'content' => $message_response,
                                    'status' => 7, //Đã hủy
                                ]);

                            } catch (\Exception $e) {
                                DB::rollback();
                                Log::error( $e);
                                continue;
                            }

                            DB::commit();
                            continue;
                        }
                        else{

                            $order->update([
                                'status' => 7,
                            ]);//trạng thái hủy

                            //set tiến độ hủy
                            OrderDetail::create([
                                'order_id' => $order->id,
                                'module' => config('module.minigame.module.withdraw-item'),
                                'content' => "Kết nối lại NCC thất bại (7) - ".$result->message??'',
                                'status' => 7, //Đã hủy
                            ]);

                            DB::commit();
                            continue;
                        }
                    }
                    else{

                        $order->update([
                            'status' => 9,
                        ]);//trạng thái hủy

                        //set tiến độ hủy
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.minigame.module.withdraw-item'),
                            'content' => "Kết nối lại NCC thất bại (9)",
                            'status' => 9, //Đã hủy
                        ]);

                        DB::commit();
                        continue;
                    }

                }else{
                    DB::rollback();
                    continue;
                }

            } catch (\Exception $e) {
                DB::rollback();
                \Log::error( $e);
                continue;

            }
        }

        return redirect()->back()->with('success', 'Các đơn đã được nạp lại thàng công');

        return response()->json([
            'status'=>1,
            'message'=>'Các đơn đã được nạp lại thàng công',

        ]);

    }

    public function deleteRecharge(Request $request){

        DB::beginTransaction();
        try {

            $id = $request->id;

            $order = Order::where('id',$id)->where('status',7)->lockForUpdate()->first();

            if (!isset($order)){
                DB::rollback();
                return redirect()->back()->withErrors('Đơn hàng không tồn tại!');
            }

            $shop= Shop::findorFail($order->shop_id);

            $type = $order->payment_type;

            $provider = config('module.minigame.game_type_map.'.$type);

            if (!isset($provider)){
                DB::rollback();
                return redirect()->back()->withErrors('Không tìm thấy loại game!');
            }

            if ($provider == 'lienquan' || $provider == 'freefire' || $provider == 'lienminh' || $provider == 'bns' ||
                $provider == 'ads' || $provider == 'fo4m' || $provider == 'fo4' || $provider == 'pubgm' || $provider == 'codm'){


                $userid = $order->author_id;
                $userTransaction = User::where('id',$userid)->lockForUpdate()->firstOrFail();

                if (!isset($userTransaction)){
                    DB::rollback();
                    return redirect()->back()->withErrors('Không tìm thấy khách hàng!');
                }

                $order->update([
                    'status' => 3,
                ]);//trạng thái hủy

                //set tiến độ hủy
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.minigame.module.withdraw-item'),
                    'content' => "Giao dịch lỗi (xem tiến độ)",
                    'status' => 3, //Đã hủy
                ]);

                $balance_item_txns = $userTransaction['ruby_num'.$type];
                $amount =  $order->price;
                $userTransaction['ruby_num'.$type] = $userTransaction['ruby_num'.$type] + $order->price;
                $userTransaction->save();

                $txns = TxnsVp::create([
                    'trade_type' => config('module.txnsvp.trade_type.refund'),
                    'is_add' => '1',
                    'user_id' => $userTransaction->id,
                    'amount' => $amount,
                    'last_balance' => $balance_item_txns + $amount,
                    'description' => "Hoàn ".$amount." rút vật phẩm thất bại gói rút" . $order->ref_id ,
                    'ref_id' => $order->id,
                    'status' => 1,
                    'shop_id' => $userTransaction->shop_id,
                    'order_id' => $order->id,
                    'item_type' => $type
                ]);

                $mesage = 'Hoàn vật phẩm cho khách thành công';

                //set tiến độ hủy
                OrderDetail::create([
                    'order_id' => $order->id,
                    'module' => config('module.minigame.module.withdraw-item'),
                    'content' => $mesage,
                    'status' => 3, //Đã hủy
                ]);

            }else{
                DB::rollback();
                return redirect()->back()->withErrors('Cấu hình loại game không hợp lệ!');
            }


        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            $status = 'ERROR';
            $msg = "Có lỗi phát sinh.Xin vui lòng thử lại !";
        }
        DB::commit();
        return redirect()->back()->with('success', 'Hủy đơn rút thành công');
    }

    public function rechargeTimeOut(Request $request){
        $id = $request->id;

        // Start transaction!
        DB::beginTransaction();
        try {

            $order = Order::where('id',$id)->where('status',9)->lockForUpdate()->first();

            if (!$order){
                DB::rollback();
                return redirect()->back()->withErrors('Không tìm thấy đơn hàng!');
            }

            //convert module dịch vụ sang rút minigame

            $shop = Shop::where('id',$order->shop_id)->first();

            if (!isset($shop)){
                DB::rollback();
                return redirect()->back()->withErrors('Không tìm thấy điểm bán!');
            }

            if (!$order->payment_type){
                DB::rollback();
                return redirect()->back()->withErrors('Không tìm thấy payment_type!');
            }

            $type = $order->payment_type;

            $provider = config('module.minigame.game_type_map.'.$type);

            if (!isset($provider)){
                DB::rollback();
                return redirect()->back()->withErrors('Không tìm thấy provider!');
            }

            if ($provider == 'lienquan' || $provider == 'freefire' || $provider == 'lienminh' || $provider == 'bns' ||
                $provider == 'ads' || $provider == 'fo4m' || $provider == 'fo4' || $provider == 'pubgm' || $provider == 'codm'){

                if (!isset($order->author_id)){
                    DB::rollback();
                    return redirect()->back()->withErrors('Không tìm thấy author_id!');
                }

                $userid = $order->author_id;

                $userTransaction = User::where('id',$userid)->lockForUpdate()->first();

                if (!isset($userTransaction)){
                    DB::rollback();
                    return redirect()->back()->withErrors('Không tìm thấy khách hàng!');
                }

                $url = config('app.app_url_api_tichhop_minigame'); //url API auto add item

                if (!isset($order->request_id)){
                    DB::rollback();
                    return redirect()->back()->withErrors('Không tìm thấy request_id!');
                }

                $tranid = $order->request_id;

                if (!isset($shop->tichhop_key)){
                    DB::rollback();
                    return redirect()->back()->withErrors('Không tìm thấy tichhop_key!');
                }

                $tichhop_key = $shop->tichhop_key;

                $data = array();
                $data['secret'] = $tichhop_key;
                $data['check_tranid'] = $tranid;

                if(is_array($data)){
                    $dataPost = http_build_query($data);
                }else{
                    $dataPost = $data;
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url."?".$dataPost);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                $resultRaw = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                $result = json_decode($resultRaw);

                if ($result && isset($result->status)) {

                    if ($result->status==1) {
                        //Yêu cầu được chấp nhận (có đơn trên hệ thống)
                        if (isset($result->data)){
                            $data = $result->data;
                            if (isset($data->status)){
                                $status = $data->status;
                                if ($status == 0){
                                    DB::rollback();
                                    return redirect()->back()->withErrors(__('Đơn hàng đang được sử lý vui lòng KT lại.'));
                                }
                                elseif ($status == 1){
                                    //Giao dịch đã thành công => Cập nhật trạng thái thành công cho đơn hàng này.
                                    $order->update([
                                        'status' => 1,
                                    ]);//trạng thái hủy

                                    $mesage = 'Giao dịch thành công sau khi kiểm tra đơn hàng đã thành công bên ncc tích hợp';

                                    //set tiến độ hủy
                                    OrderDetail::create([
                                        'order_id' => $order->id,
                                        'module' => config('module.minigame.module.withdraw-item'),
                                        'content' => $mesage,
                                        'status' => 1, //Đã hủy
                                    ]);

                                    DB::commit();
                                    return redirect()->back()->with('success', 'Giao dịch đã thành công => Cập nhật trạng thái thành công cho đơn hàng này.');

                                }
                                elseif ($status == 3){
                                    //Giao dịch đã thất bại => hủy đơn và hoàn tiền cho khách.

                                    $order->update([
                                        'status' => 3,
                                    ]);//trạng thái hủy

                                    //set tiến độ hủy
                                    OrderDetail::create([
                                        'order_id' => $order->id,
                                        'module' => config('module.minigame.module.withdraw-item'),
                                        'content' => "Giao dịch lỗi (xem tiến độ)",
                                        'status' => 3, //Đã hủy
                                    ]);

                                    $balance_item_txns = $userTransaction['ruby_num'.$type];
                                    $amount =  $order->price;
                                    $userTransaction['ruby_num'.$type] = $userTransaction['ruby_num'.$type] + $order->price;
                                    $userTransaction->save();

                                    $txns = TxnsVp::create([
                                        'trade_type' => config('module.txnsvp.trade_type.refund'),
                                        'is_add' => '1',
                                        'user_id' => $userTransaction->id,
                                        'amount' => $amount,
                                        'last_balance' => $balance_item_txns + $amount,
                                        'description' => "Hoàn ".$amount." rút vật phẩm thất bại gói rút" . $order->ref_id ,
                                        'ref_id' => $order->id,
                                        'status' => 1,
                                        'shop_id' => $userTransaction->shop_id,
                                        'order_id' => $order->id,
                                        'item_type' => $type
                                    ]);

                                    $mesage = 'Hoàn vật phẩm cho khách thành công sau khi kiểm tra đơn hàng đã thất bại bên ncc tích hợp';

                                    //set tiến độ hủy
                                    OrderDetail::create([
                                        'order_id' => $order->id,
                                        'module' => config('module.minigame.module.withdraw-item'),
                                        'content' => $mesage,
                                        'status' => 3, //Đã hủy
                                    ]);
                                    DB::commit();
                                    return redirect()->back()->with('success', 'Giao dịch đã thất bại => hủy đơn và hoàn tiền cho khách');
                                }
                                else{
                                    DB::rollback();
                                    return redirect()->back()->withErrors(__('Bên ncc trả về data status không đúng'));
                                }
                            }
                            else{
                                DB::rollback();
                                return redirect()->back()->withErrors(__('Bên ncc trả về data status không đúng'));
                            }
                        }
                        else{
                            DB::rollback();
                            return redirect()->back()->withErrors(__('Lỗi data trả về bên NCC'));
                        }
                    }
                    elseif ($result->status==2){
                        //Dev kiểm tra lại key tich hop

                        DB::rollback();
                        return redirect()->back()->withErrors(__('Secret không đúng hoặc user đã bị khoá'));
                    }
                    elseif ($result->status==3){
                        //Giao dịch không tồn tại => hủy đơn và hoàn tiền cho khách

                        $order->update([
                            'status' => 3,
                        ]);//trạng thái hủy

                        //set tiến độ hủy
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.minigame.module.withdraw-item'),
                            'content' => "Giao dịch lỗi (xem tiến độ)",
                            'status' => 3, //Đã hủy
                        ]);

                        $balance_item_txns = $userTransaction['ruby_num'.$type];
                        $amount =  $order->price;
                        $userTransaction['ruby_num'.$type] = $userTransaction['ruby_num'.$type] + $order->price;
                        $userTransaction->save();

                        $txns = TxnsVp::create([
                            'trade_type' => config('module.txnsvp.trade_type.refund'),
                            'is_add' => '1',
                            'user_id' => $userTransaction->id,
                            'amount' => $amount,
                            'last_balance' => $balance_item_txns + $amount,
                            'description' => "Hoàn ".$amount." rút vật phẩm thất bại gói rút" . $order->ref_id ,
                            'ref_id' => $order->id,
                            'status' => 1,
                            'shop_id' => $userTransaction->shop_id,
                            'order_id' => $order->id,
                            'item_type' => $type
                        ]);

                        $mesage = 'Hoàn vật phẩm cho khách thành công sau khi kiểm tra đơn hàng không tồn tại bên ncc tích hợp';

                        //set tiến độ hủy
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'module' => config('module.minigame.module.withdraw-item'),
                            'content' => $mesage,
                            'status' => 3, //Đã hủy
                        ]);

                        DB::commit();
                        return redirect()->back()->with('success', 'Giao dịch không tồn tại => hủy đơn và hoàn tiền cho khách');
                    }
                    else{
                        DB::rollback();
                        return redirect()->back()->withErrors(__('Bên ncc trả về status không đúng'));
                    }

                }
                else {
                    DB::rollback();
                    return redirect()->back()->withErrors(__('Kết nối NCC thất bại'));
                }

            }else{
                DB::rollback();
                return redirect()->back()->withErrors(__('Không tìm thấy loại game vui lòng thử lại'));
            }

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error( $e);
            $txt = Carbon::now()."Lỗi hệ thống: ".$e->getLine()." - ".$e->getMessage();
            return redirect()->back()->withErrors($txt);

        }

    }
}
