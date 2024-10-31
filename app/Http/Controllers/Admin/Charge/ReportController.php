<?php

namespace App\Http\Controllers\Admin\Charge;

use App\Http\Controllers\Controller;
use App\Library\Helpers;
use App\Models\ActivityLog;
use App\Models\Charge;
use App\Models\Telecom;
use App\Models\TelecomValue;
use App\Models\TelecomValueAgency;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Shop;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportData;
use App\Library\ChargeGateway\NAPTHENHANH;
use App\Library\ChargeGateway\CANCAUCOM;


class ReportController extends Controller
{


    public function __construct(Request $request)
    {

        //set permission to function
        $this->middleware('permission:charge-report-list');
        $this->middleware('permission:charge-report-export',['only' => ['exportExcel']]);
        $this->middleware('permission:charge-report-recharge',['only' => ['reCharge']]);

        $this->page_breadcrumbs[] = [
            'page' => route('admin.charge-report.index'),
            'title' => __('Thống kê nạp thẻ')
        ];
    }


    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập thống kê nạp thẻ charge-report');
        if ($request->ajax) {
            $datatable = Charge::with('user')->with('processor');
            if ($request->filled('id')) {
                $datatable->where('id', $request->get('id'));
            }
            if ($request->filled('username')) {
                $datatable->whereHas('user', function ($query) use ($request) {
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
            if ($request->filled('key')) {
                $datatable->where('telecom_key', $request->get('key'));
            }
            if ($request->filled('amount')) {
                $datatable->where('amount', $request->get('amount'));
            }
            if ($request->filled('status')) {
                $datatable->where('status', $request->get('status'));
            }
            if($request->filled('started_at') || $request->filled('ended_at')){
                if ($request->filled('started_at')) {
                    $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
                }
                if ($request->filled('ended_at')) {
                    $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
                }
            }
            else{
                if ($request->filled('find')) {

                }
                else{
                    $datatable->whereDate('created_at', Carbon::today())->get();
                }
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
            return \datatables()->eloquent($datatable)
                ->only([
                   'id',
                   'shop_id',
                   'type_charge',
                   'username',
                   'user_id',
                   'gate_id',
                   'telecom_key',
                   'money_received',
                   'pin',
                   'serial',
                   'declare_amount',
                   'amount',
                   'ratio',
                   'real_received_amount',
                   'ratio_received',
                   'txns_id',
                   'user_auth',
                   'tranid',
                   'description',
                   'response_code',
                   'response_mess',
                   'processor_id',
                   'process_at',
                   'status',
                   'action',
                   'created_at'
                ])
                ->editColumn('ratio', function ($data) {
                    return number_format($data->ratio) . "%";
                })
                ->editColumn('declare_amount', function ($data) {
                    return number_format($data->declare_amount);
                })
                ->editColumn('pin', function ($data) {
                    $temp = Helpers::Decrypt($data->pin, config('module.charge.key_encrypt'));
                    if ($data->status == 1 || $data->status == 0 || $data->status == 3) {
                        return $temp;
                    } else {
                        return $temp = "****" . substr($temp, 4, strlen($temp));
                    }
                })
                ->addColumn('username', function($row) {
                    $username = $row->user->username;
                    $temp = '';

                    if(auth()->user()->hasRole('admin') || auth()->user()->can('view-profile')){
                        $temp .= "<a href=\"#\"  class=\"load-modal\" rel=\"".route('admin.view-profile',["username" => "$username","shop_id" => "$row->shop_id"])."\">".$username."</a>";
                    }
                    else{
                        $temp .= $username;
                    }
                    return $temp;
                })
                ->editColumn('created_at', function ($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->editColumn('process_at', function ($data) {
                    if($data->process_at){
                        return date('d/m/Y H:i:s', strtotime($data->process_at));
                    }
                    else{
                        return "";
                    }
                })
                ->editColumn('shop_id', function($data) {
                    $temp= '';
                    if(isset($data->shop_id)){
                        if(isset($data->shop->domain)){
                            $temp .=  $data->shop->domain;
                        }
                    }
                    return $temp;
                })
                ->editColumn('ratio_received', function ($data) {
                    if($data->amount!=0){
                        return ($data->money_received*100/$data->amount). "%"; ;
                    }
                    else{
                        return 0 . "%";;
                    }
                })
                ->addColumn('action',function($row){
                    $temp = "";
                    if($row->status  == 998){
                        if(Auth::user()->can('charge-report-recharge')){
                            $temp .= "<button type=\"button\" class=\"btn btn-danger btn-sm btn-recharge\" title=\"Nạp lại\" data-id=\"".$row->id."\"><i class=\"flaticon-refresh\"></i></button>";
                        }
                        else{
                            $temp .= "<button type=\"button\" class=\"btn btn-danger btn-sm\" title=\"Bạn không có quyền !\"  data-toggle=\"tooltip\"><i class=\"flaticon-refresh\"></i></button>"; 
                        }
                    }
                    return $temp;
                })
                ->with('totalSumary', function() use ($datatable) {
                   return $datatable->first([
                       DB::raw('SUM(declare_amount) as total_declare_amount'),
                       DB::raw('SUM(IF(status = 1, amount, 0)) as total_success'),
                       DB::raw('SUM(IF(status = 3, amount, 0)) as total_wrong_amount'),
                       DB::raw('SUM(real_received_amount) as total_received_amount'),
                       DB::raw('SUM(money_received) as total_money_received')
                   ]);
                })
                ->toJson();
        }
        $telecom = Telecom::where('type_charge', 0)->pluck('title','key')->toArray();
        $shop_access_user = Auth::user()->shop_access;
        $shop = Shop::orderBy('id','desc');
        if(isset($shop_access_user) && $shop_access_user !== "all"){
            $shop_access_user = json_decode($shop_access_user);
            $shop = $shop->whereIn('id',$shop_access_user);
        }
        $shop = $shop->get();
        return view('admin.charge.report.index')
            ->with('module', null)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('shop', $shop)
            ->with('telecom', $telecom);
    }
    public function exportExcel(Request $request){

        ini_set('max_execution_time', 2400); //20 minutes

        ActivityLog::add($request, 'Xuất excel thống kê nạp thẻ');
        $datatable = Charge::with('user')->with('shop')->with('processor');
        if ($request->filled('id')) {
            $datatable->where('id', $request->get('id'));
        }
        if ($request->filled('username')) {
            $datatable->whereHas('user', function ($query) use ($request) {
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
        if ($request->filled('key')) {
            $datatable->where('telecom_key', $request->get('key'));
        }
        if ($request->filled('amount')) {
            $datatable->where('amount', $request->get('amount'));
        }
        if ($request->filled('status')) {
            $datatable->where('status', $request->get('status'));
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
        }
        else{
            $datatable->whereDate('created_at', Carbon::today())->get();
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
        $datatable = $datatable->select(
            'id',
            'shop_id',
            'type_charge',
            'user_id',
            'gate_id',
            'telecom_key',
            'serial',
            'money_received',
            'declare_amount',
            'amount',
            'ratio',
            'real_received_amount',
            'response_code',
            'response_mess',
            'tranid',
            'description',
            'ip',
            'process_at',
            'process_log',
            'api_type',
            'request_at',
            'status',
            'status_callback',
            'created_at'
            )
            ->get();
            $data = [
                'data' => $datatable,
            ];
        return Excel::download(new ExportData($data,view('admin.charge.report.export_excel')), 'Thống kê nạp thẻ ' . time() . '.xlsx');
    }
    public function reCharge(Request $request){
        try {
            $id = $request->id;
            DB::beginTransaction();
            $data = Charge::where('id',$id)->lockForUpdate()->first();
            if(!$data){
                return response()->json([
                    'message' => 'Không tìm thấy thẻ cần xử lý.',
                    'status' => 0,
                ], 200);
            }
            if($data->status != 998){
                return response()->json([
                    'message' => 'Trạng thái thẻ không được gọi phép gọi lại.',
                    'status' => 0,
                ], 200);
            }
            ActivityLog::add($request, 'Nạp lại đơn thẻ #'.$data->id);
            $charge_check = Charge::where('serial',$data->serial)
            ->where('pin',$data->pin)
            ->where('type_charge', 0)
            ->where(function ($q) {
                $q->orWhere('status', 0);
                $q->orWhere('status', 1);
                $q->orWhere('status', 2);
                $q->orWhere('status', 3);
            })
            ->first();
            if($charge_check){
                $data->status = 0;
                $data->response_code = null;
                $data->response_mess = null;
                $data->tranid = null;
                $data->request_id = null;
                $data->save();
                DB::commit();
                return response()->json([
                    'message' => "Thẻ được cập nhật thất bại do trạng thái thẻ đã được xử lý.",
                    'status' => 1
                ],200);
            }
            // tìm shop
            $shop = Shop::where('id',$data->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => 'Shop không tồn tại.',
                    'status' => 0,
                ], 200);
            }
            $gate_id = $data->gate_id;
            $serial = $data->serial;
            $pin = Helpers::Decrypt($data->pin, config('module.charge.key_encrypt'));
            if($gate_id == 1){
                $result = NAPTHENHANH::API($shop->ntn_partner_id,$shop->ntn_partner_key,$data->telecom_key, $pin, $serial, $data->declare_amount,$data->request_id,$shop->domain);
            }
            // Trường hợp chạy cổng CCC
            elseif($gate_id == 2){
                $result = CANCAUCOM::API($shop->ccc_partner_id,$shop->ccc_partner_key,$data->telecom_key, $pin, $serial, $data->declare_amount,$data->request_id,$shop->domain);
            }
            else{
                DB::rollback();
                return response()->json([
                    'message' => 'Cổng gạch thẻ không được tìm thấy, vui lòng kiểm tra lại.',
                    'status' => 0
                ],200);
            }
            if ($result && isset($result->status)) {
                if ($result->status == 2) {
                    $data->status = 2;
                    $data->response_code = $result->response_code??null;
                    $data->response_mess = $result->message??null;
                    $data->tranid = $result->tranid??null;
                    $data->save();
                    DB::commit();
                    return response()->json([
                        'message' => 'Nạp lại thành công.Thẻ cào đang được kiểm tra. CODE: '.$result->response_code??null,
                        'status' => 1,
                    ],200);
                }
                elseif($result->status == 77) {
                    $data->status = 0;
                    $data->response_code = $result->response_code??null;
                    $data->response_mess = $result->message??null;
                    $data->tranid = $result->tranid??null;
                    $data->save();
                    DB::commit();
                    return response()->json([
                        'message' => "Nạp lại thành công. Trạng thái thẻ thất bại. CODE: ".$result->response_code??null.' - ' . $result->message,
                        'status' => 1
                    ],200);
                }
                else{
                    $data->status = 0;
                    $data->response_code = $result->response_code;
                    $data->response_mess = $result->message;
                    $data->tranid = $result->tranid??null;
                    $data->save();
                    DB::commit();
                    return response()->json([
                        'message' => "Nạp lại thành công. Trạng thái thẻ thất bại. CODE: ".$result->response_code??null.' - ' . $result->message,
                        'status' => 1
                    ],200);
                }
            }
            else{
                $data->status = 998;
                $data->response_code = -1;
                $data->response_mess = "Không tìm thấy phản hồi từ máy chủ gạch thẻ, vui lòng liên hệ Admin để xử lý.";
                $data->save();
                DB::commit();
                return response()->json([
                    'message' => "Nạp lại không thành công. Không tìm thấy phản hồi từ máy chủ gạch thẻ.",
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


    // public function postCallback(Request $request)
    // {


    //     // Start transaction!
    //     DB::beginTransaction();

    //     try {
    //         //kiểu nạp auto
    //         $type_charge = 0;

    //         //tìm id của nạp thẻ với trạng thái đang xử lý
    //         $charge = Charge::where('id', $request->id)->where('status', '!=', 1)->lockForUpdate()->first();

    //         if (!$charge) {
    //             DB::rollBack();
    //             return redirect()->back()->withErrors('Không tìm thấy hoặc thẻ cào đã được xử lý');
    //         }

    //         //tìm user người nạp
    //         $userTransaction = User::where('id', $charge->user_id)->lockForUpdate()->first();
    //         if (!$userTransaction) {
    //             DB::rollBack();
    //             return redirect()->back()->withErrors('Không tìm thấy người dùng');

    //         }

    //         //check status  là sai
    //         if ($request->amount == 0) {
    //             //set trạng thái thẻ sai
    //             $charge->response_mess = "Nạp thẻ sai mã thẻ hoặc serial";
    //             $charge->amount = 0;
    //             $charge->status = 0;
    //             $charge->processor_id = Auth::user()->id;
    //             if ($request->filled('description')) {
    //                 $charge->description = $request->description;
    //             } else {
    //                 $charge->description = "[THẺ CỘNG LẠI]";
    //             }

    //             $charge->process_at = Carbon::now();
    //             $charge->save();

    //         } //trường hợp đúng
    //         else {

    //             //lấy chiết khấu nhà mạng
    //             $telecom = Telecom::where('type_charge', 0)
    //                 ->where('gate_id', $request->gate_id)
    //                 ->where('key', $charge->telecom_key)
    //                 ->first();


    //             if (!$telecom) {
    //                 DB::rollBack();
    //                 return redirect()->back()->withErrors('Mệnh giá bạn chọn không tìm thấy hoặc bị khóa bởi Admin');
    //             }

    //             //ratio
    //             $telecom_value = TelecomValue::where('telecom_id', $telecom->id)
    //                 ->where('amount', $request->amount)->first();

    //             if (!$telecom_value) {
    //                 DB::rollBack();
    //                 return redirect()->back()->withErrors('Mệnh giá bạn chọn không tìm thấy hoặc bị khóa bởi Admin');
    //             }

    //             // check nếu mà đúng mệnh giá
    //             if ($charge->declare_amount == $request->amount) {

    //                 //set trạng thái thẻ đúng
    //                 $charge->amount = $request->amount;
    //                 $charge->response_mess = 'Nạp thành công thẻ ' . $charge->telecom_key . ' mệnh giá ' . number_format($charge->amount) . ' đ';
    //                 $charge->status = 1;
    //                 if ($userTransaction->is_agency_charge == 1) {
    //                     $telecom_value_agency = TelecomValueAgency::where('telecom_id', $telecom->id)
    //                         ->where('username', $userTransaction->username)
    //                         ->where('amount', $request->amount)->first();

    //                     if ($telecom_value_agency) {
    //                         $ratio = $telecom_value_agency->ratio;
    //                     } else {
    //                         $ratio = $telecom_value->agency_ratio_true_amount;
    //                     }

    //                 } else {
    //                     $ratio = $telecom_value->ratio_true_amount;
    //                 }

    //             } else {
    //                 //set trạng thái thẻ sai mệnh giá
    //                 $charge->status = 3;
    //                 $charge->response_mess = "Nạp thẻ sai mệnh giá";
    //                 $charge->amount = $request->amount;

    //                 if ($userTransaction->is_agency_charge == 1) {

    //                     $ratio = $telecom_value->agency_ratio_false_amount;
    //                 } else {
    //                     $ratio = $telecom_value->ratio_false_amount;
    //                 }
    //             }


    //             //tính tiền thực nhận
    //             $real_received_amount = ($ratio * $request->amount) / 100;

    //             //cộng tiền cho user
    //             if ($real_received_amount < 0) {
    //                 return redirect()->back()->withErrors('Số tiền thanh toán không hợp lệ');
    //             }
    //             $userTransaction->balance = $userTransaction->balance + $real_received_amount;
    //             $userTransaction->balance_in=$userTransaction->balance_in+$real_received_amount;
    //             $userTransaction->save();

    //             //lưu thông tin nạp thẻ
    //             $charge->ratio = $ratio;
    //             $charge->real_received_amount = $real_received_amount;
    //             $charge->processor_id = Auth::user()->id;
    //             if ($request->filled('description')) {
    //                 $charge->description = $request->description;
    //             } else {
    //                 $charge->description = "[THẺ CỘNG LẠI]";
    //             }

    //             $charge->process_at = Carbon::now();
    //             $charge->save();


    //             $charge->txns()->create([
    //                 'user_id' => $userTransaction->id,
    //                 'trade_type' => 'charge',
    //                 'is_add' => '1', //cộng tiền
    //                 'amount' => $real_received_amount,
    //                 'last_balance' => $userTransaction->balance,
    //                 'description' => "Nạp thẻ ".Helpers::Decrypt($charge->pin, config('module.charge.key_encrypt')). ' - '.$charge->serial. ' - '.$charge->ratio."%",
    //                 'ip' => $request->getClientIp(),
    //                 'status' => 1
    //             ]);
    //             //gọi callback cho các user
    //             //                if($userTransaction->url_callback!=""){
    //             //
    //             //                    $data = array();
    //             //                    $data['type'] = $charge->telecom_key;
    //             //                    $data['pin'] = $charge->pin;
    //             //                    $data['serial'] = $charge->serial;
    //             //                    $data['declare_amount'] = $charge->declare_amount;
    //             //                    $data['amount'] = $charge->amount;
    //             //                    $data['tranid'] = $charge->id;
    //             //                    $data['status'] = $charge->status;
    //             //                    $data['message'] = $charge->response_mess;
    //             //                    $data['callback_sign'] = md5($userTransaction->partner_key.$charge->id.$charge->pin.$charge->serial);
    //             //                    $dataPost = http_build_query($data);
    //             //                    $url =$userTransaction->url_callback;
    //             //
    //             //                    $ch = curl_init();
    //             //                    curl_setopt($ch, CURLOPT_URL, $url);
    //             //                    curl_setopt($ch, CURLOPT_POST, 1);
    //             //                    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
    //             //                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    //             //                    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    //             //                    curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
    //             //                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    //             //                    curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 10);
    //             //
    //             //                    curl_exec($ch);
    //             //                    curl_close($ch);
    //             //
    //             //
    //             //
    //             //                }


    //         }

    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         Log::error($e);
    //         return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');
    //     }

    //     // Commit the queries!
    //     DB::commit();
    //     ActivityLog::add($request, 'Xử lý callback thẻ thành công charge-report #' . $charge->id);
    //     return redirect()->back()->with('success', 'Xử lý giao dịch thẻ thành công #' . $charge->id);
    // }


}
