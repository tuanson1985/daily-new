<?php

namespace App\Http\Controllers\Admin\Bank;
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
use Log;
use Session;
use Yajra\DataTables\EloquentDataTable;


class WithdrawHistoryController extends Controller
{


    protected $page_breadcrumbs;
    protected $module;
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{

        $this->middleware('permission:withdraw-money-history');

        $this->module="withdraw-history";
        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'.index'),
                'title' => "Tài khoản ngân hàng"
            ];
        }
	}

	public function index(Request $request)
	{

		if ($request->ajax()) {

			$model = Withdraw::where('user_id',Auth::user()->id);
            return $datatable =\datatables()->eloquent($model)
                ->addColumn('action', function($row) {

                    if($row->status==2){
                        $temp="<a  rel=\"$row->id\" class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\"  title=\"Hủy bỏ\"><i class=\"la la-close\"></i></a>";
                    }
                    else{
                        $temp="";
                    }
                    return $temp;


                })


              ->toJson();
		}


		return view('admin.bank.withdraw-history.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);


	}

    public function postDeny(Request $request){
        // Start transaction!
        DB::beginTransaction();

        try {

            //Check số lần hủy
            $countDeny=Withdraw::where('user_id',Auth::user()->id)
                ->where('status',0)
                ->whereDate('updated_at', Carbon::today())->count();

            if($countDeny>=3){
                return redirect()->back()->withErrors('Bạn đã hủy lệnh rút tiền quá số lần cho phép trong ngày.Vui lòng thử lại vào ngày hôm sau');
            }


            $withdraw=Withdraw::where('user_id',Auth::user()->id)->where('status',2)->lockForUpdate()->findOrFail($request->id);
            //cập nhật trạng thái
            $withdraw->status=0;
            $withdraw->save();

            //hoàn tiền cho user
            $userTransaction=User::where('id',$withdraw->user_id)->lockForUpdate()->firstOrFail();
            if($userTransaction->checkBalanceValid() == false){
                DB::rollback();
                return response()->json([
                    'status'=>0,
                    'message'=>'Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý'
                ]);
            }


            $userTransaction->balance= $userTransaction->balance+$withdraw->amount_passed;
            $userTransaction->balance_in=$userTransaction->balance_in+$withdraw->amount_passed;
            $userTransaction->save();

            //tạo txns hoàn tiền
            $txns=Txns::create([

                'trade_type'=>'11',//Hoàn tiền
                'is_add'=>'1',//Công tiền
                'username'=>$userTransaction->username,
                'amount'=>$withdraw->amount_passed,
                'real_received_amount'=>$withdraw->amount_passed,
                'last_balance' => $userTransaction->balance,
                'description' => 'Hoàn tiền hủy lệnh rút tiền #' . $withdraw->id,
                'ref_id' => $withdraw->id,
                'ip'=>$request->getClientIp(),
                'status'=>1
            ]);



        } catch (\Exception $e) {
            return $e->getMessage();
            DB::rollback();
            Log::error($e);
            return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');
        }
        // Commit the queries!
        DB::commit();
        return redirect()->route('admin.withdraw-history.index')->with('success', 'Hủy bỏ lệnh rút tiền thành công !');

    }




}
