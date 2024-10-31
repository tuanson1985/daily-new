<?php

namespace App\Http\Controllers\Admin\Bank;
use App\Http\Controllers\Controller;
use App\Library\Helpers;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Group;
use App\Models\Activity;
use Auth;



use Illuminate\Http\Request;
use Session;
use Yajra\DataTables\EloquentDataTable;


class AccountController extends Controller
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

        $this->middleware('permission:bank-account');
//		$this->middleware('permission:game-item-create', ['only' => ['create', 'store']]);
//		$this->middleware('permission:game-item-edit', ['only' => ['edit', 'update']]);
//		$this->middleware('permission:game-item-delete', ['only' => ['destroy']]);


        $this->module="bank-account";
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

			$datatable = BankAccount::with('bank')
                ->where('user_id',Auth::user()->id)
                ->orderBy('created_at','desc');
            if ($request->filled('find'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('account_number', 'LIKE', '%' . $request->get('find') . '%');
                    $q->orWhere('holder_name', 'LIKE', '%' . $request->get('find') . '%');
                });
            }
            return $datatable =\datatables()->eloquent($datatable)
                ->addColumn('action', function($row) {

//                    $temp= "<a href=\"".route('admin.bank-account.edit',$row->id)."\"  rel=\"$row->id\" class=\"m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill edit_toggle \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp= "<a  rel=\"$row->id\" class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
            })->setTotalRecords($datatable->count())->toJson();
		}

		$bank_type_0=Bank::where('bank_type',0)->where('status',1)->pluck('title','id')->toArray();
		$bank_type_1=Bank::where('bank_type',1)->where('status',1)->pluck('title','id')->toArray();
		return view('admin.bank.account.index')
            ->with('bank_type_0',$bank_type_0)
            ->with('bank_type_1',$bank_type_1)
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

        if ($request->bank_type == 0) {

            $this->validate($request, [
                'bank_id' => 'required',
                'holder_name' => 'required',
                'account_number' => 'required',
            ], [
                'bank_id.required' => "Vui lòng chọn loại ngân hàng hoặc ví",
                'holder_name.required' => "Vui lòng nhập tên chủ tài khoản",
                'account_number.required' => "Vui lòng số tài khoản",
            ]);

            $input = $request->except('account_vi');
            $input['user_id'] = Auth::user()->id;
            BankAccount::create($input);


        }
        if ($request->bank_type == 1) {

            $this->validate($request, [
                'bank_id' => 'required',
                'account_vi' => 'required',
                'account_vi_confirmation' => 'required|same:account_vi',

            ], [
                'bank_id.required' => "Vui lòng chọn loại ngân hàng hoặc ví",
                'account_vi.required' => "Vui lòng nhập tên tài khoản ví",
                'account_vi_confirmation.required' => "Vui lòng điền xác nhận tên tài khoản ví",
                'account_vi_confirmation.same' => "Nhập lại tài khoản ví không trùng khớp",
            ]);

            $input = $request->except('account_number');
            $input['user_id'] = Auth::user()->id;
            BankAccount::create($input);
        }

        if ($request->has('submit-new')) {
            $response = redirect()->route('admin.bank-account.create');
        } else {
            $response = redirect()->route('admin.bank-account.index');
        }
        return $response->with('success', __('Thêm mới thành công'));


    }

	/**
	 * Display the specified newscategory.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function show($id)
	{
//		$datatable = Item::with('groups');
//		$datatable = $datatable->where('module','=',config('constants.module.game.key_app'))->findOrFail($id);
//		return view('admin.bank.account.show', compact('datatable'));
	}

	/**
	 * Show the form for editing the specified newscategory.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function edit($id)
	{


		$data = BankAccount::with('bank')->where('username',Auth::user()->username)->findOrFail($id);

        $bank_type_0=Bank::where('bank_type',0)->pluck('title','id')->toArray();
        $bank_type_1=Bank::where('bank_type',1)->pluck('title','id')->toArray();

        return view('admin.bank.account.create_edit')
        ->with('data',$data)
        ->with('bank_type_0',$bank_type_0)
        ->with('bank_type_1',$bank_type_1);


	}

	/**
	 * Update the specified newscategory in storage.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function update(Request $request,$id)
	{

//        $data = BankAccount::where('username',Auth::user()->username)->findOrFail($id);
//
//        if ($request->bank_type == 0) {
//
//            $this->validate($request, [
//                'bank_id' => 'required',
//                'holder_name' => 'required',
//                'account_number' => 'required',
//            ], [
//                'bank_id.required' => "Vui lòng chọn loại ngân hàng hoặc ví",
//                'holder_name.required' => "Vui lòng nhập tên chủ tài khoản",
//                'account_number.required' => "Vui lòng số tài khoản",
//            ]);
//
//            $input = $request->except('account_vi');
//            $input['username'] = Auth::user()->username;
//            $data->update($input);
//
//            //active log active
//            Activity::create([
//                'user_id' => Auth::user()->id,
//                'action' => 'CREATE',
//                'content' => 'Username: ' . Auth::user()->username . ' thêm mới tài khoản ngân hàng',
//                'module' => config('constants.module.game-setting.key_app'),
//                'data' => json_encode($input),
//                'ip_address' => $request->getClientIp(),
//                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent'
//            ]);
//        }
//        if ($request->bank_type == 1) {
//
//            $this->validate($request, [
//                'bank_id' => 'required',
//                'account_vi' => 'required',
//                'account_vi_confirmation' => 'required|same:account_vi',
//
//            ], [
//                'bank_id.required' => "Vui lòng chọn loại ngân hàng hoặc ví",
//                'account_vi.required' => "Vui lòng nhập tên tài khoản ví",
//                'account_vi_confirmation.required' => "Vui lòng điền xác nhận tên tài khoản ví",
//                'account_vi_confirmation.same' => "Nhập lại tài khoản ví không trùng khớp",
//            ]);
//
//            $input = $request->except('account_number');
//            $input['username'] = Auth::user()->username;
//            $data->update($input);
//
//            //active log active
//            Activity::create([
//                'user_id' => Auth::user()->id,
//                'action' => 'CREATE',
//                'content' => 'Username: ' . Auth::user()->username . ' thêm mới tài khoản ngân hàng',
//                'module' => config('constants.module.game-setting.key_app'),
//                'data' => json_encode($input),
//                'ip_address' => $request->getClientIp(),
//                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent'
//            ]);
//        }
//
//
//        return redirect()->route('admin.bank-account.index')->with('success',trans('admin/message.update_success'));

    }

	/**
	 * Remove the specified newscategory from storage.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function destroy(Request $request)
	{

        $input=explode(',',$request->id);
        BankAccount::where('user_id',Auth::user()->id)
            ->whereIn('id',$input)->delete();

        return redirect()->route('admin.bank-account.index')->with('success',__('Xóa thành công'));
	}


}
