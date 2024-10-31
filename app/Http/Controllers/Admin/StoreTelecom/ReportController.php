<?php

namespace App\Http\Controllers\Admin\StoreTelecom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ActivityLog;
use App\Models\StoreTelecom;
use App\Models\StoreTelecomValue;
use App\Models\StoreCard;
use App\Models\Txns;
use App\Models\Order;
use Carbon\Carbon;
use App\Library\Helpers;
use App\Models\User;
use App\Models\Shop;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportData;
use App\Library\StoreCardGateway\StoreCardNapTheNhanh;
// use DateTime;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;
    public function __construct(Request $request)
    {

        //set permission to function
        $this->middleware('permission:store-card-report-list');
        $this->middleware('permission:store-card-report-show', ['only' => ['show','updateOrder']]);
        $this->middleware('permission:store-card-export',['only' => ['exportExcel']]);
        $this->middleware('permission:store-card-recheck',['only' => ['reCheckOrder']]);


        $this->page_breadcrumbs[] = [
            'page' => route('admin.store-card-report.index'),
            'title' => __('Thống kê mua thẻ')
        ];
    }
    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập thống kê mua thẻ store-card-report');
        if ($request->ajax) {
            $datatable = Order::with('author')
                        ->withCount('card')
                        ->where('module','store-card');


            if ($request->filled('id')) {
                $datatable->where('id', $request->get('id'));
            }
            if ($request->filled('request_id')) {
                $datatable->where('request_id', $request->get('request_id'));
            }


            if ($request->filled('username')) {
                $datatable->whereHas('author', function ($query) use ($request) {
                    $query->where(function ($qChild) use ($request){
                        $qChild->orWhere('username', $request->get('username'));
                        $qChild->orWhere('email', $request->get('username'));
                        $qChild->orWhere('fullname_display', 'LIKE', '%' . $request->get('username') . '%');
                    });

                });
            }

            if ($request->filled('find')) {
                $datatable->where(function ($query) use ($request) {
                    $query->orWhere('pin', Helpers::Encrypt($request->get('find'),config('module.charge.key_encrypt')));
                    $query->orWhere('serial', $request->get('find'));
                });
            }
            if ($request->filled('gate_id')) {
                $datatable->where('gate_id', $request->get('gate_id'));
            }
            if ($request->filled('amount')) {
                $datatable->where('amount', $request->get('amount'));
            }

            if ($request->filled('status')) {
                $datatable->where('status', $request->get('status'));
            }
            if ($request->filled('shop_id')) {
                $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                if(empty($shop_id_shop_access) || $shop_id_shop_access == 'all'){
                    $datatable->whereIn('shop_id', $request->get('shop_id'));
                }
                else{
                    $shop_id_shop_access_search = array_intersect($shop_id_shop_access,$request->get('shop_id'));
                    $datatable->whereIn('shop_id', $shop_id_shop_access_search);
                }
            }
            else{
                if(session('shop_id')){
                    $datatable->where('shop_id',session('shop_id'));
                }
                else{
                    if(isset(Auth::user()->shop_access) &&Auth::user()->shop_access !== "all"){
                        $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                        $datatable->whereIn('shop_id',$shop_id_shop_access);
                    }
                }
            }
            if ($request->filled('process_started_at')) {
                $datatable->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('process_started_at')));
            }
            if ($request->filled('process_ended_at')) {
                $datatable->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('process_ended_at')));
            }
            if($request->filled('started_at') || $request->filled('ended_at')){
                if ($request->filled('started_at')) {

                    $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
                }
                if ($request->filled('ended_at')) {
                    $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
                }
            }else{
                if($request->filled('find')){

                }
                else{
                    $datatable->whereDate('created_at', Carbon::today())->get();
                }
            }
            //$subDatatable= $datatable;
            return \datatables()->eloquent($datatable)

                ->only([
                    'id',
                    'user',
                    'telecom',
                    'params',
                    'amount',
                    'username',
                    'quantity',
                    'shop_id',
                    'request_id',
                    'ratio',
                    'description',
                    'real_received_price',
                    'gate_id',
                    'tranid',
                    'status',
                    'card_count',
                    'action',
                    'created_at',
                ])
                ->addColumn('username', function($row) {
                    $username = $row->author->username;
                    $temp = '';
                    if(auth()->user()->hasRole('admin') || auth()->user()->can('view-profile')){
                        $temp .= "<a href=\"#\"  class=\"load-modal\" rel=\"".route('admin.view-profile',["username" => "$username","shop_id" => "$row->shop_id"])."\">".$username."</a>";
                    }
                    else{
                        $temp .= $row->username;
                    }
                    return $temp;
                })
                ->editColumn('shop_id', function($data) {
                    $temp= '';
                    if(isset($data->shop_id)){
                        $temp .=  $data->shop->domain;
                    }
                    return $temp;
                })
                ->editColumn('ratio', function ($data) {
                    return percent_format($data->ratio) . "%";
                })
                ->editColumn('real_received_price', function ($data) {
                    return number_format($data->real_received_price) . " VNĐ";
                })
                ->editColumn('created_at', function ($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->addColumn('telecom', function($row) {
                   return json_decode($row->params)->telecom;
                })
                ->addColumn('amount', function($row) {
                   return json_decode($row->params)->amount;
                })
                ->addColumn('quantity', function($row) {
                   return json_decode($row->params)->quantity;
                })
                ->addColumn('action', function($row) {
                    $temp= "<a href=\"".route('admin.store-card-report.show',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Xem chi tiết\"><i class=\"flaticon-medical\"></i></a>";
                    return $temp;
                })
                ->with('total_price',$datatable->sum('price'))
                ->with('total_real_received_price',$datatable->sum('real_received_price'))
                ->setTotalRecords($datatable->count())
                ->toJson();
        }

        $telecom = StoreTelecom::pluck('title','key')->toArray();
        $shop_access_user = Auth::user()->shop_access;
        $shop = Shop::orderBy('id','desc');
        if(isset($shop_access_user) && $shop_access_user !== "all"){
            $shop_access_user = json_decode($shop_access_user);
            $shop = $shop->whereIn('id',$shop_access_user);
        }
        $shop = $shop->get();
        return view('admin.store-telecom.report.index')
            ->with('module', null)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('shop', $shop)
            ->with('telecom', $telecom);
    }


    public function exportExcel(Request $request){
        ActivityLog::add($request, 'Xuất excel thống kê mua thẻ store-card-export');
        $datatable = Order::with('author')->with('shop')
        ->withCount('card')
        ->where('module','store-card');
        if ($request->filled('id')) {
            $datatable->where('id', $request->get('id'));
        }
        if ($request->filled('request_id')) {
            $datatable->where('request_id', $request->get('request_id'));
        }
        if ($request->filled('username')) {
            $datatable->whereHas('author', function ($query) use ($request) {
                $query->where(function ($qChild) use ($request){
                    $qChild->orWhere('username', $request->get('username'));
                    $qChild->orWhere('email', $request->get('username'));
                });
            });
        }
        if ($request->filled('find')) {
            $datatable->where(function ($query) use ($request) {
                $query->orWhere('pin', Helpers::Encrypt($request->get('find'),config('module.charge.key_encrypt')));
                $query->orWhere('serial', $request->get('find'));
            });
        }
        if ($request->filled('gate_id')) {
            $datatable->where('gate_id', $request->get('gate_id'));
        }
        if ($request->filled('amount')) {
            $datatable->where('amount', $request->get('amount'));
        }

        if ($request->filled('status')) {
            $datatable->where('status', $request->get('status'));
        }
        if ($request->filled('shop_id')) {
            $shop_id_shop_access = json_decode(Auth::user()->shop_access);
            if(empty($shop_id_shop_access) || $shop_id_shop_access == 'all'){
                $datatable->whereIn('shop_id', $request->get('shop_id'));
            }
            else{
                $shop_id_shop_access_search = array_intersect($shop_id_shop_access,$request->get('shop_id'));
                $datatable->whereIn('shop_id', $shop_id_shop_access_search);
            }
        }
        else{
            if(session('shop_id')){
                $datatable->where('shop_id',session('shop_id'));
            }
            else{
                if(isset(Auth::user()->shop_access) &&Auth::user()->shop_access !== "all"){
                    $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                    $datatable->whereIn('shop_id',$shop_id_shop_access);
                }
            }
        }
        if($request->filled('started_at') || $request->filled('ended_at')){
            if ($request->filled('started_at')) {
                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }
        }else{
            $datatable->whereDate('created_at', Carbon::today())->get();
        }
        $datatable = $datatable->get();
        $data = [
            'data' => $datatable,
        ];
        return Excel::download(new ExportData($data,view('admin.store-telecom.report.export_excel')), 'Thống kê mua thẻ ' . time() . '.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Chi tiết đơn hàng")
        ];
        $data = Order::with('author')->where('id',$id)->firstOrFail();
        ActivityLog::add($request, 'Truy cập thống kê nạp thẻ charge-report #'.$data->id);
        return view('admin.store-telecom.report.show')
        ->with('data',$data)
        ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function reCheckOrder(Request $request, $id){
        try {
            DB::beginTransaction();
            // tìm đơn hàng
            $data = Order::with('author')->where('id',$id)->lockForUpdate()->first();
            if(!$data){
                return response()->json([
                    'message' => 'Không tìm thấy đơn hàng cần xử lý.',
                    'status' => 0,
                ], 200);
            }
            $old_status = $data->status;
            if($data->status != 2 && $data->status != 4){
                return response()->json([
                    'message' => 'Thao tác chỉ xử lý những đơn hàng đang chờ và lỗi gọi nhà cung cấp.',
                    'status' => 0,
                ], 200);
            }
            $user = User::where('id', $data->author_id)->where('account_type',2)->where('status',1)->first();
            if(!$user){
                return response()->json([
                    'message' => 'Người dùng không hợp lệ.',
                    'status' => 0,
                ], 200);
            }
            $request_id = $data->request_id;
            // tìm shop xử lý đơn hàng
            $shop_id = $data->shop_id;
            $shop = Shop::where('id',$shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => 'Điểm bán không tồn tại hoặc đã bị khóa.',
                    'status' => 0,
                ], 200);
            }
            $partner_id = $shop->ntn_partner_id;
            $partner_key = $shop->ntn_partner_key_card;
            $domain = $shop->domain;
            if($data->gate_id == 1){
                $result = StoreCardNapTheNhanh::detailOrder($partner_id,$partner_key,$request_id,$domain);
            }
            else{
                $result = "WRONG_GATEWAY";
            }
            if($result === "WRONG_GATEWAY"){
                return response()->json([
                    'message' => 'Nhà cung cấp không hợp lệ, Vui lòng kiểm tra lại',
                    'status' => 0,
                ], 200);
            }
            // tìm người mua
            $userTransaction = User::where('id', $user->id)->where('account_type',2)->where('status',1)->lockForUpdate()->first();
            if(!$userTransaction){
                return response()->json([
                    'message' => 'Người dùng không hợp lệ.',
                    'status' => 0,
                ], 200);
            }
            // kiểm tra tính xác thực đơn hàng
            if($result  && $result->status == 1){
                $data->status = 1;
                $data->price_input = $result->total_price??null;
                $data->process_at = Carbon::now();
                $data->description = 'CODE '.$result->response_code.' - '.$result->message . '. Đơn xử lý khi kiểm tra lại với nhà cung cấp';
                $data->save();
                // cập nhật thông tin thẻ bán
                $data_card = $result->data_card;
                foreach ($data_card as $card) {
                    StoreCard::create([
                        'shop_id' => $shop->id,
                        'key' => $card->telecom_key,
                        'pin' => Helpers::Encrypt($card->pin,config('module.charge.key_encrypt')),
                        'serial' => Helpers::Encrypt($card->serial,config('module.charge.key_encrypt')),
                        'amount' => $card->amount,
                        'status' => 1,
                        'user_id' => $userTransaction->id,
                        'buy_at' => Carbon::now(),
                        'ratio' => $data->ratio,
                        'order_id' => $data->id
                    ]);
                }
                ActivityLog::add($request, 'Kiểm tra đơn hàng mua thẻ với nhà cung cấp #'.$data->id. 'Trạng thái ban đầu: '.$old_status.'. Trạng thái sau khi kiểm tra: '.$data->status);
                DB::commit();
                return response()->json([
                    'message' => 'Đơn hàng thành công, thẻ đã được lấy vào chi tiết đơn hàng người dùng !',
                    'status' => 1,
                ], 200);
            }
            else if(isset($result) && $result->status == 2){
                // lưu trạng thái đang chờ, giữ lại tiền khách hàng, chờ check dữ liệu với nhà cung cấp
                $data->description = 'CODE '.$result->response_code.' - '.$result->message. ". Đơn đã được gọi lại.";
                $data->save();
                ActivityLog::add($request, 'Kiểm tra đơn hàng mua thẻ với nhà cung cấp #'.$data->id. 'Trạng thái ban đầu: '.$old_status.'. Trạng thái sau khi kiểm tra: '.$data->status);
                DB::commit();
                return response()->json([
                    'message' => 'Đơn hàng đang được chờ xử lý. '.$data->description,
                    'status' => 1,
                ],200);
            }
            else if(isset($result) && $result->status == 0){
                $data->status = 0;
                $data->process_at = Carbon::now();
                $data->description = 'CODE '.$result->response_code.' - '.$result->message . '. Đơn xử lý khi kiểm tra lại với nhà cung cấp';
                $data->save();
                $userTransaction->balance = $userTransaction->balance + $data->real_received_price;
                // cộng số tiền vào cho user
                $userTransaction->balance_in = $userTransaction->balance_in + $data->real_received_price;
                $userTransaction->save();
                $data->status = 0;
                $data->save();
                $txns = $data->txns()->create([
                    'shop_id' => $data->shop_id,
                    'trade_type' => 'plus_money', // mua thẻ
                    'is_add'=>'1', // cộng tiền
                    'user_id'=>$userTransaction->id,
                    'amount'=>$data->real_received_price,
                    'profit'=>null,
                    'last_balance'=>$userTransaction->balance,
                    'description'=> "Hoàn tiền giao dịch thẻ lỗi #".$data->id,
                    'ip'=>$request->getClientIp(),
                    'status'=>1
                ]);
                ActivityLog::add($request, 'Kiểm tra đơn hàng mua thẻ với nhà cung cấp #'.$data->id. 'Trạng thái ban đầu: '.$old_status.'. Trạng thái sau khi kiểm tra: '.$data->status);
                DB::commit();
                return response()->json([
                    'message' => 'Đơn hàng ở trạng thái thất bại. '.$data->description,
                    'status' => 1,
                ],200);
            }
            else{
                DB::rollback();
                ActivityLog::add($request, 'Kiểm tra đơn hàng mua thẻ với nhà cung cấp #'.$data->id. 'Xử lý giao dịch bị lỗi');
                return response()->json([
                    'message' => 'Lỗi giao dịch, vui lòng liên hệ QTV để xác thực giao dịch',
                    'status' => 0
                ],200);
            }
        }catch(\Exception $e){
            DB::rollback();
            Log::error($e);
            return response()->json([
                'message' => 'Có lỗi phát sinh, vui lòng kiểm tra lại',
                'status' => 0
            ],200);
        }
    }


    // public function updateOrder(Request $request, $id){
    //     // tìm đơn hàng

    //     DB::beginTransaction();
    //     try {

    //         $data = Order::with('author')->where('id',$id)->lockForUpdate()->first();
    //         $status_old = $data->status;
    //         if(!$data){
    //             DB::rollBack();
    //             return redirect()->back()->withErrors(__('Đơn hàng không hợp lệ !'));
    //         }
    //         if($data->status == 0){
    //             DB::rollBack();
    //             return redirect()->back()->withErrors(__('Đơn hàng đang ở trạng thái thất bại !'));
    //         }
    //         if($data->status == 1){
    //             DB::rollBack();
    //             return redirect()->back()->withErrors(__('Đơn hàng đang ở trạng thái thành công !'));
    //         }
    //         if($data->status == $request->status){
    //             DB::rollBack();
    //             return redirect()->back()->withErrors(__('Đơn hàng đang ở trạng thái này !'));
    //         }
    //         $status = $request->status;
    //         if($status != 1 && $status != 0){
    //             DB::rollBack();
    //             return redirect()->back()->withErrors(__('Chỉ cập nhật đơn hàng về trạng thái thành công hoặc thất bại !'));
    //         }

    //         // trường hợp cập nhật đơn hàng về thất bại
    //         if($status == 0){
    //             // tìm người mua
    //             $userTransaction = User::where('id', $data->author_id)->lockForUpdate()->first();
    //             if(!$userTransaction){
    //                 DB::rollBack();
    //                 return redirect()->back()->withErrors(__('Không tìm thấy chủ đơn hàng !'));
    //             }
    //             // hoàn lại tiền cho user
    //             $userTransaction->balance = $userTransaction->balance + $data->real_received_price;
    //             // cộng số tiền vào cho user
    //             $userTransaction->balance_in = $userTransaction->balance_in + $data->real_received_price;
    //             $userTransaction->save();
    //             $data->status = 0;
    //             $data->save();
    //             $txns = $data->txns()->create([
    //                 'shop_id' => $data->shop_id,
    //                 'trade_type' => 'plus_money', // mua thẻ
    //                 'is_add'=>'1',//tru tien
    //                 'user_id'=>$userTransaction->id,
    //                 'amount'=>$data->real_received_price,
    //                 'profit'=>null,
    //                 'last_balance'=>$userTransaction->balance,
    //                 'description'=> "Hoàn tiền giao dịch thẻ lỗi #".$data->id,
    //                 'ip'=>$request->getClientIp(),
    //                 'status'=>1
    //             ]);
    //         }
    //         // cập nhật thành công
    //         else if($status == 1){
    //             $data->status = 1;
    //             $data->save();
    //         }
    //         else{
    //             DB::rollback();
    //             return redirect()->back()->withErrors(__('Chỉ cập nhật đơn hàng về trạng thái thành công hoặc thất bại !'));
    //         }
    //     }catch(\Exception $e){
    //         DB::rollback();
    //         Log::error($e);
    //         return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');
    //     }
    //     ActivityLog::add($request, 'Cập nhật trạng thái đơn hàng #'.$data->id. ' từ trạng thái '.$status_old. ' sang trạng thái '.$status);
    //     DB::commit();
    //     return redirect()->back()->with('success',__('Cập nhật trạng thái đơn hàng thành công !'));

    // }
}
