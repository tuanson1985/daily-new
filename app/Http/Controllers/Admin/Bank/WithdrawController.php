<?php

namespace App\Http\Controllers\Admin\Bank;

use App\Http\Controllers\Controller;
use App\Library\Helpers;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Txns;
use App\Models\User;
use App\Models\Withdraw;
use Auth;
use DB;
use Illuminate\Http\Request;
use Log;


class WithdrawController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $page_breadcrumbs;
    protected $module;

    public function __construct()
    {

        $this->middleware('permission:withdraw-money');
        //$this->middleware('permission:game-item-create', ['only' => ['create', 'store']]);
        //$this->middleware('permission:game-item-edit', ['only' => ['edit', 'update']]);
        //$this->middleware('permission:game-item-delete', ['only' => ['destroy']]);

        $this->module = "withdraw";
        if ($this->module != "") {
            $this->page_breadcrumbs[] = [
                'page' => route('admin.' . $this->module . '.index'),
                'title' => " Rút tiền ra ngần hàng/Ví điện tử"
            ];
        }
    }

    public function index(Request $request)
    {


        $data = BankAccount::with('bank')->where('user_id', Auth::user()->id)->get();
        return view('admin.bank.withdraw.index')
            ->with('data', $data)
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);


    }


    /**
     * Show the form for creating a new newscategory
     *
     * @return Response
     */
    public function create(Request $request)
    {

        return view('admin.bank.account.create_edit');
    }

    /**
     * Store a newly created newscategory in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {



        $this->validate($request, [
            'bank_account_id' => 'required',
            'amount' => 'required',
        ], [
            'bank_account_id.required' => "Vui lòng chọn tài khoản ngân hàng/Ví để rút tiền",
            'amount.required' => "Vui lòng nhập số tiền muốn rút",

        ]);


        // Start transaction!
        DB::beginTransaction();

        try {
            //tìm user
            $userTransaction = User::where('id', Auth::guard()->user()->id)->lockForUpdate()->firstOrFail();



            //check password2
            if(!\Hash::check($request->password2,\Auth::user()->password2)){
                session()->put('fail_password2',  session()->get('fail_password2')+1);

                DB::rollBack();
                return redirect()->back()->withErrors(__('Mật khẩu cấp 2 không đúng'))->withInput();
            }
            else{
                session()->put('fail_password2', 0);
            }

            if($userTransaction->checkBalanceValid() == false){
                DB::rollBack();
                return redirect()->back()->withErrors('Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý');
            }

            $amount = (int)str_replace(array(' ', '.'), '', $request->amount);

            //check điều kiên rút tối đa và tối thiểu
            if ($amount < 100000) {

                DB::rollBack();
                return redirect()->back()->withErrors('Số tiền rút tối thiểu phải lớn hơn 100,000 VNĐ');
            } elseif ($amount > 10000000) {

                DB::rollBack();
                return redirect()->back()->withErrors('Số tiền rút chỉ được tối đa 10,000,000 VNĐ');
            }
            //tìm bank_account
            $bank_account = BankAccount::where('user_id', Auth::user()->id)->findOrFail($request->bank_account_id);
            //tìm bank id
            $bank = Bank::findOrFail($bank_account->bank_id);
            //tính fee rút theo ngân hàng
            if ($bank->fee_type == 0) {
                $fee = $bank->fee;
            } else {
                $fee = $amount * $bank->fee / 100;
            }

            //check xem user có đủ số dư hay không
            $amount_passed = $amount + $fee;
            if ($userTransaction->balance < $amount_passed) {

                DB::rollBack();
                return redirect()->back()->withErrors('Bạn không đủ số dư để thực hiện lệnh rút tiền');
            }
            //trừ tiền user
            $userTransaction->balance = $userTransaction->balance - $amount_passed;
            $userTransaction->balance_out=$userTransaction->balance_out+$amount_passed;
            $userTransaction->save();

            //tao lệnh rút tiền
            $withdraw = Withdraw::create([
                'user_id' => $userTransaction->id,
                'bank_title' => $bank->title,
                'bank_type' => $bank->bank_type,
                'holder_name' => $bank_account->holder_name,
                'account_number' => $bank_account->account_number,
                'account_vi' => $bank_account->account_vi,
                'bank_id' => $bank_account->bank_id,
                'brand' => $bank_account->brand,
                'amount' => $amount,
                'fee' => $fee,
                'amount_passed' => $amount_passed,
                'description' => Helpers::convert_vi_to_en($request->description),
                'request_id' => time() . Helpers::rand_num_string(6),
                'status' => 2 //Chờ xử lý

            ]);

            if($userTransaction->account_type==1 || $userTransaction->account_type==3){
                $shop_id=null;
            }
            else{
                $shop_id=session('shop_id');
            }
            //tạo tnxs
            $txns = $withdraw->txns()->create([
                'trade_type' => 'withdraw_money',//Rút tiền
                'is_add' => '0',//Trừ tiền tiền
                'user_id' => $userTransaction->id,
                'amount' => $amount_passed,
                'real_received_amount' => $amount_passed,
                'last_balance' => $userTransaction->balance,
                'description' => '',
                'ref_id' => $withdraw->id,
                'ip' => $request->getClientIp(),
                'status' => 1, //Thành công
                'shop_id' =>$shop_id
            ]);
            //lưu txns cho lệnh rút tiền
            $withdraw->txns_id = $txns->id;
            $withdraw->save();

            //gửi thông báo cho người duyệt

            $userHasPermission = User::whereHas('permissions', function ($query) {
                $query->where('name', 'confirm-withdraw');
            })->get();


            if ($userHasPermission) {
                foreach ($userHasPermission as $userPerm) {

                    \App\Models\Notification::create([
                        'notifiable_id' => $userPerm->id,
                        'notifiable_type' => 'App\Models\User',
                        'type' => "2", //rút tiền
                        'data' => $withdraw,

                    ]);
                }
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');
        }
        // Commit the queries!
        DB::commit();
        return redirect()->route('admin.withdraw-history.index')->with('success', 'Tạo lệnh rút tiền thành công !');


    }


    public function getLoadInfo(Request $request)
    {


        $data = BankAccount::with('bank')
            ->where('user_id', Auth::user()->id)
            ->findOrFail($request->id);

        return view('admin.bank.withdraw.load-info')->with('data', $data);

    }


}
