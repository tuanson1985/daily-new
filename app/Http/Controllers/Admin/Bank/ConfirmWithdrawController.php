<?php

namespace App\Http\Controllers\Admin\Bank;
use App\Exports\ConfirmWithdrawExport;
use App\Http\Controllers\Controller;
use App\Library\Helpers;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Group;
use App\Models\Activity;
use App\Models\Txns;
use App\Models\User;
use App\Models\Withdraw;
use Auth;


use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Log;
use Session;
use Yajra\DataTables\EloquentDataTable;


class ConfirmWithdrawController extends Controller
{



	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */

    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;
	public function __construct()
	{
        $this->middleware('permission:confirm-withdraw');
        $this->module='confirm-withdraw';
        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'.index'),
                'title' => __('Phê duyệt lệnh rút tiền')
            ];
        }

	}
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $model = Withdraw::query()
                ->with(['txns', 'user']);

            if ($request->filled('id')) {
                $model->where('id',$request->id);
            }
            if ($request->filled('request_id')) {
                $model->where('request_id',$request->request_id);
            }
            if ($request->filled('username')) {
                $model->whereHas('user', function ($query) use ($request) {
                    $query->where(function ($qChild) use ($request){
                        $qChild->where('username', $request->get('username'));
                    });
                });
            }
            if ($request->filled('type_information_ctv')) {
                $model->whereHas('user', function ($query) use ($request) {
                    $query->where(function ($qChild) use ($request){
                        $qChild->where('type_information_ctv', $request->get('type_information_ctv'));
                    });
                });
            }
            if ($request->filled('bank_type')) {
                $model->where('bank_type',$request->bank_type);
            }
            if ($request->filled('bank_title')) {
                $model->where('bank_title',$request->bank_title);
            }
            if ($request->filled('account_number')) {
                $model->where(function($q) use ($request) {
                    $q->orWhere('account_number',$request->account_number);
                    $q->orWhere('account_vi',$request->account_number);
                });
            }
            if ($request->filled('source_money')) {
                $model->where('source_money',$request->source_money);
            }
            if ($request->filled('source_bank')) {

                $model->where('source_bank',$request->source_bank);
            }
            if ($request->filled('status')) {
                if($request->status==2){
                    $model->whereIn('status',[-1,2]);
                }
                else{
                    $model->where('status', $request->status);
                }
            }
            if ($request->filled('role_id')) {
                $model->where('role_id', 'LIKE', '%' . $request->role_id . '%');
            }
            if ($request->filled('started_at')) {
                $model->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $model->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }
            if ($request->filled('started_process_at')) {
                $model->where('process_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_process_at')));
            }
            if ($request->filled('ended_process_at')) {
                $model->where('process_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_process_at')));
            }

            $datatableTotal=$model->clone();
            return $datatable =\datatables()->eloquent($model)
                ->editColumn('amount', function($row) {
                    return number_format($row->amount, 0,',','.');
                })
                ->editColumn('user.username', function($row) {
                    return $row->user->username??"";
                })
                ->editColumn('processor.username', function($row) {
                    return $row->processor->username??"";
                })
                ->editColumn('bank_title', function($row) {
                    return Str::upper($row->bank_title);
                })
                ->editColumn('bank_type', function($row) {
                    return config('module.bank.bank_type.'.$row->bank_type);
                })
                ->editColumn('source_money', function($row) {
                    return config('module.bank.bank_type.'.$row->source_money);
                })
                ->editColumn('account_number', function($row) {
                    return $row->account_number. $row->account_vi;
                })
                ->editColumn('process_at', function($row) {
                    if($row->process_at==null){
                        return "";
                    }
                    return date('d/m/Y H:i:s', strtotime($row->process_at));
                })
                ->addColumn('action', function($row) {
                    $temp= "<a  href='#' rel=\"".route('admin.confirm-withdraw.show',$row->id)."\"  class=\"m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill load-modal \" title=\"Chi tiết\"><i  class=\"la la-eye\"></i></a>";

                   if($row->status==2 ||$row->status==-1){
                       $temp.= "<a href='#'  rel=\"$row->id\" rel-bank-type=\"$row->bank_type\"  class='m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill confirm_toggle' data-toggle=\"modal\" data-target=\"#confirmModal\"  title=\"Xác nhận\"><i class=\"fa fa-check\"></i></a>";
                       $temp.= "<a href='#' rel=\"$row->id\" class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\"  title=\"Hủy bỏ\"><i class=\"la la-close\"></i></a>";
                   }
                    return $temp;
                })
                ->with('total_amount',$model->sum('amount'))
                ->with('totalSumary', function() use ($datatableTotal) {
                    return $datatableTotal=$datatableTotal->first([
                        DB::raw('COUNT(withdraw.id) as total_record'),
                        DB::raw('SUM(withdraw.amount) as total_amount'),
                    ]);
                })
                ->setTotalRecords($model->count()) ->toJson();
        }


        //SET BACK URL
        $count_confirm=Withdraw::query()->where(function ($q){
            $q->orWhere('status',2);
            $q->orWhere('status',-1);
        });

        $count_confirm = $count_confirm->count();
        $bank_type_0=Bank::where('bank_type',0)->where('status',1)->pluck('title','title')->toArray();
        $bank_type_1=Bank::where('bank_type',1)->where('status',1)->pluck('title','title')->toArray();

        return view('admin.bank.confirm-withdraw.index')
        ->with('module', $this->module)
        ->with('page_breadcrumbs', $this->page_breadcrumbs)
        ->with('count_confirm', $count_confirm)
        ->with('bank_type_0', $bank_type_0)
        ->with('bank_type_1', $bank_type_1);

    }

    public function exportExcel(Request $request){

        $export = new ConfirmWithdrawExport($request);
        return \Excel::download($export, 'Lịch sử phê duyệt rút tiền_ ' . time() . '.xlsx');

    }

    public function show($id){
        $datatable=Withdraw::with('user','processor');
//        if(Auth::user()->hasRole('admin')){
//
//        }
//        // trường hợp qtv duyệt tiền là shop nhà
//        elseif(Auth::user()->type_information == 0){
//            $datatable->whereHas('user', function ($query) {
//                $query->where('type_information',0);
//            });
//        }
//        // trường hợp qtv duyệt tiền là shop khách
//        elseif(Auth::user()->type_information == 1){
//            $shop_access = Auth::user()->shop_access;
//            $datatable->whereHas('user', function ($query) use($shop_access) {
//                $query->where('type_information',1);
//                $shop_access = json_decode($shop_access);
//                if(isset($shop_access) && count($shop_access) > 0){
//                    $query->where(function($q) use($shop_access){
//                        foreach($shop_access as $item){
//                            $q->orwhereJsonContains('shop_access',[$item]);
//                        }
//                    });
//                }
//            });
//        }
//        else{
//            abort(403);
//        }
        $datatable = $datatable->findOrFail($id);
        return view('admin.bank.confirm-withdraw.show', compact('datatable'));
    }

    public function postConfirm(Request $request){
        if($request->source_money=="" ){
            return redirect()->back()->withErrors("Vui lòng chọn nguồn chuyển tiền" )->withInput();
        }
        if($request->source_money==0 && $request->source_bank==""){
            return redirect()->back()->withErrors("Vui lòng chọn ngân hàng/ví chuyển" )->withInput();
        }
        if($request->source_money==1 && $request->source_bank==""){
            return redirect()->back()->withErrors("Vui lòng chọn ngân hàng/ví chuyển" )->withInput();
        }
        // Start transaction!
        DB::beginTransaction();
        try {
            $withdraw=Withdraw::where(function ($q){
                $q->orWhere('status',2);
                $q->orWhere('status',-1);
            });
//            if(Auth::user()->hasRole('admin')){
//
//            }
//            // trường hợp qtv duyệt tiền là shop nhà
//            elseif(Auth::user()->type_information == 0){
//                $withdraw->whereHas('user', function ($query) {
//                    $query->where('type_information',0);
//                });
//            }
//            // trường hợp qtv duyệt tiền là shop khách
//            elseif(Auth::user()->type_information == 1){
//                $shop_access = Auth::user()->shop_access;
//                $withdraw->whereHas('user', function ($query) use($shop_access) {
//                    $query->where('type_information',1);
//                    $shop_access = json_decode($shop_access);
//                    if(isset($shop_access) && count($shop_access) > 0){
//                        $query->where(function($q) use($shop_access){
//                            foreach($shop_access as $item){
//                                $q->orwhereJsonContains('shop_access',[$item]);
//                            }
//                        });
//                    }
//                });
//            }
//            else{
//                return redirect()->back()->withErrors("Bạn chưa được phân loại thông tin" )->withInput();
//            }

            $withdraw = $withdraw->lockForUpdate()->findOrFail($request->id);

            if($withdraw->bank_type==1  && $withdraw->status ==2 && $withdraw->bank_titte=="TICHHOP.NET"){
                DB::rollback();
                return redirect()->back()->withErrors('Giao dịch ngân hàng tự động không thể can thiệp');
            }
            //cập nhật trạng thái
            $withdraw->status=1;
            $withdraw->source_money=$request->source_money;
            $withdraw->source_bank=$request->source_bank;
            $withdraw->admin_note=$request->admin_note;
            $withdraw->processor_id=Auth::user()->id;
            $withdraw->process_at=Carbon::now();
            $withdraw->save();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');
        }
        // Commit the queries!
        DB::commit();
        return redirect()->route('admin.confirm-withdraw.index')->with('success', 'Xác nhận lệnh rút tiền thành công !');
    }

    public function postDeny(Request $request){
        // Start transaction!
        DB::beginTransaction();
        try {
            $withdraw=Withdraw::where('status',2);

//            if(Auth::user()->hasRole('admin')){
//
//            }
//            // trường hợp qtv duyệt tiền là shop nhà
//            elseif(Auth::user()->type_information == 0){
//                $withdraw->whereHas('user', function ($query) {
//                    $query->where('type_information',0);
//                });
//            }
//            // trường hợp qtv duyệt tiền là shop khách
//            elseif(Auth::user()->type_information == 1){
//                $shop_access = Auth::user()->shop_access;
//                $withdraw->whereHas('user', function ($query) use($shop_access) {
//                    $query->where('type_information',1);
//                    $shop_access = json_decode($shop_access);
//                    if(isset($shop_access) && count($shop_access) > 0){
//                        $query->where(function($q) use($shop_access){
//                            foreach($shop_access as $item){
//                                $q->orwhereJsonContains('shop_access',[$item]);
//                            }
//                        });
//                    }
//                });
//            }
//            else{
//                return redirect()->back()->withErrors("Bạn chưa được phân loại thông tin" )->withInput();
//            }

            $withdraw = $withdraw->lockForUpdate()->findOrFail($request->id);
            //cập nhật trạng thái
            $withdraw->status=0;
            $withdraw->description="Lý do hủy: ".$request->description;
            $withdraw->processor_id=Auth::user()->id;
            $withdraw->process_at=Carbon::now();
            $withdraw->save();
            //hoàn tiền cho user
            $userTransaction=User::where('id',$withdraw->user_id)->lockForUpdate()->firstOrFail();
            if($userTransaction->checkBalanceValid() == false){
                DB::rollBack();
                return redirect()->back()->withErrors('Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý');
            }

            $userTransaction->balance= $userTransaction->balance+$withdraw->amount;
            $userTransaction->balance_in=$userTransaction->balance_in+$withdraw->amount;
            $userTransaction->save();

            //tạo txns hoàn tiền
            $txns=Txns::create([
                'trade_type'=>'refund',//Hoàn tiền
                'is_add'=>'1',//Công tiền
                'user_id'=>$userTransaction->id,
                'amount'=>$withdraw->amount,
                'real_received_amount'=>$withdraw->amount,
                'last_balance' => $userTransaction->balance,
                'description' => 'Hoàn tiền hủy lệnh rút tiền #' . $withdraw->id,
                'ref_id' => $withdraw->id,
                'ip'=>$request->getClientIp(),
                'status'=>1,
                'shop_id' => $userTransaction->shop_id
            ]);
        } catch (\Exception $e) {

            DB::rollback();
            Log::error($e);
            return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');
        }
        // Commit the queries!
        DB::commit();
        return redirect()->route('admin.confirm-withdraw.index')->with('success', 'Hủy bỏ lệnh rút tiền thành công !');
    }


    public function getCount(Request $request){
        //SET BACK URL
        $count_confirm=Withdraw::where(function ($q){
            $q->orWhere('status',2);
            $q->orWhere('status',-1);
        });
//        if(Auth::user()->hasRole('admin')){
//
//        }
//        // trường hợp qtv duyệt tiền là shop nhà
//        elseif(Auth::user()->type_information == 0){
//            $count_confirm->whereHas('user', function ($query) {
//                $query->where('type_information',0);
//            });
//        }
//        // trường hợp qtv duyệt tiền là shop khách
//        elseif(Auth::user()->type_information == 1){
//            $shop_access = Auth::user()->shop_access;
//            $count_confirm->whereHas('user', function ($query) use($shop_access) {
//                $query->where('type_information',1);
//                $shop_access = json_decode($shop_access);
//                if(isset($shop_access) && count($shop_access) > 0){
//                    $query->where(function($q) use($shop_access){
//                        foreach($shop_access as $item){
//                            $q->orwhereJsonContains('shop_access',[$item]);
//                        }
//                    });
//                }
//            });
//        }
//        else{
//            $count_confirm->whereNull('id');
//        }
        $count_confirm = $count_confirm->count();
        return response()->json([
            'status'=>1,
            'data'=>$count_confirm
        ]);
    }
}
