<?php

namespace App\Http\Controllers\Admin\User;

use App\Exports\ExportData;
use App\Http\Controllers\Controller;
use App\Library\HelperPermisionShop;
use App\Library\HelperPermisionShopMinigame;
use App\Models\ActivityLog;
use App\Models\PlusMoney;
use App\Models\SessionTracker;
use App\Models\User;
use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use App\Models\ServiceAccess;
use App\Models\Item;
use App\Models\GameAccess;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Shop;
use App\Models\Shop_Group;
use App\Models\Txns;
use App\Models\TxnsVp;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use App\Library\Helpers;


class UserQTVController extends Controller
{
    protected $page_breadcrumbs;
    //Quản trị viên (Nội bộ)
    protected $account_type=1;
    public function __construct()
    {
        //set permission to function
        $this->middleware('permission:user-qtv-list', ['except' => ['getMoney','getMoneyQTV','postMoney','postMoneyQTV','getUserToMoney','getUserToMoneyQTV','view_profile','AccessUser']]);
        $this->middleware('permission:user-qtv-create', ['only' => ['create','store']]);
        $this->middleware('permission:user-qtv-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user-qtv-delete', ['only' => ['destroy']]);
        $this->middleware('permission:plus-minus-money', ['only' => ['getMoney','postMoney','getUserToMoney']]);
        $this->middleware('permission:plus-minus-money-qtv', ['only' => ['getMoneyQTV','postMoneyQTV','getUserToMoneyQTV']]);
        $this->middleware('permission:view-profile', ['only' => ['view_profile']]);
        $this->middleware('permission:access-user', ['only' => ['AccessUser']]);
        $this->page_breadcrumbs[] = [
            'page' => route('admin.user-qtv.index'),
            'title' => __("Thành viên quản trị")
        ];
    }

    public function index(Request $request)
    {

        ActivityLog::add($request, 'Truy cập danh sách user-qtv');
        if($request->ajax) {
            $user = Auth::user();
            $datatable= User::with(['roles'=>function($query){
                $query->select(['id','title','name']);
            }])
                ->where("account_type",1)
                ->orderByRaw('FIELD(`status`,1,2,3,4,0,-1,99)');
            if($user->hasRole('admin') || $user->can('user-qtv-list-all')){

            }
            elseif($user->can('user-qtv-list-type-information-0')){
                $datatable->where('type_information',0);
            }
            elseif($user->can('user-qtv-list-type-information-1')){
                $datatable->where('type_information',1);
            }
            else{
                $datatable->whereNull('id');
            }
            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', 'LIKE', '%' . $request->get('id') . '%');
                });
            }
            if ($request->filled('incorrect_txns')) {
                if($request->incorrect_txns==1){
                    $datatable->whereRaw('(balance_in - balance_out + balance_in_refund - balance) != 0');
                }
                else{
                    $datatable->havingRaw('(balance_in - balance_out + balance_in_refund - balance) = 0');
                }
            }
            if ($request->filled('type_information_ctv')) {
                $datatable->where('type_information_ctv',$request->get('type_information_ctv') );
            }
            if ($request->filled('username')) {
                $datatable->where('username', 'LIKE', '%' . $request->get('username') . '%');
            }
            if ($request->filled('email')) {
                $datatable->where('email', 'LIKE', '%' . $request->get('email') . '%');
            }
            if ($request->filled('roles_id')) {
                if(is_array($request->roles_id) &&  in_array('-1',$request->roles_id) && count($request->roles_id)==1){
                    $datatable->doesntHave('roles');
                }
                elseif(is_array($request->roles_id) &&  in_array('-1',$request->roles_id)){
                    $datatable->doesntHave('roles');
                    $datatable->orWhereHas('roles', function ($query) use ($request) {
                        $query->whereIn('id', $request->get('roles_id'));
                    });
                }
                else{
                    $datatable->WhereHas('roles', function ($query) use ($request) {
                        $query->whereIn('id', $request->get('roles_id'));
                    });
                }
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
            return \datatables()->eloquent($datatable)->whitelist(['id'])
                ->only([
                    'id',
                    'username',
                    'account_type',
                    'email',
                    'roles',
                    'balance',
                    'balance_in',
                    'balance_out',
                    'balance_in_refund',
                    'phone',
                    'image',
                    'status',
                    'created_at',
                    'lastlogin_at',
                    'type_information',
                    'google2fa_enable',
                    'type_information_ctv',
                    'action',
                    'shop',
                    'balance_time',
                ])
                ->orderColumn('balance', function ($query, $order) {
                    $query->orderBy('balance', $order);
                })
                ->editColumn('balance', function($row) {
                    return intval($row->balance);
                })
                ->editColumn('google2fa_enable', function($row) {
                    if($row->google2fa_enable == 1){
                        return "Bật";
                    }
                    return "Tắt";
                })
                ->editColumn('balance_time', function($row)  use ($request){

                    if ($request->filled('balance_time')){
                        $txns = Txns::query()
                            ->where('user_id',$row->id);
                        $txns = $txns->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('balance_time')));

                        $txns = $txns->orderBy('created_at','desc')->first();
                        $balance = 0;

                        if (isset($txns)){
                            $balance = number_format($txns->last_balance);
                        }

                        return $balance;
                    }

                    return intval($row->balance_in);
                })
                ->editColumn('balance_in', function($row) {
                    return intval($row->balance_in);
                })
                ->editColumn('balance_out', function($row) {
                    return intval($row->balance_out);
                })
                ->editColumn('balance_in_refund', function($row) {
                    return intval($row->balance_in_refund);
                })
                ->editColumn('created_at', function($row) {
                    return date('d/m/Y H:i:s', strtotime($row->created_at));
                })
                ->editColumn('lastlogin_at', function($row) {
                    return date('d/m/Y H:i:s', strtotime($row->lastlogin_at));
                })
                ->editColumn('username', function($row) {
                    $temp = '';
                    if(auth()->user()->hasRole('admin')){
                        $temp .= "<a href=\"#\"  class=\"load-modal\" rel=\"".route('admin.view-profile',["username" => "$row->username"])."\">".$row->username."</a>";
                    }
                    else{
                        $temp .= $row->username;
                    }
                    return $temp;
                })
                ->addColumn('action', function($row) {
                    $temp = '';
                    if(auth()->user()->hasRole('admin') || auth()->user()->can('plus-minus-money-qtv')){
                        $temp .= "<a href=\"" .route('admin.get_money_qtv',['mode'=>1,'field'=>'username','id'=>$row->id,'value'=>$row->username])."\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary\"  title=\"Cộng tiền\"><i class=\"la la-plus\"></i></a>";
                        $temp .= "<a href=\"" .route('admin.get_money_qtv',['mode'=>0,'field'=>'username','id'=>$row->id,'value'=>$row->username])."\"  class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary'  title=\"Trừ tiền\"><i class=\"la la-minus\"></i></a>";
                    }
                    if(auth()->user()->hasRole('admin') || auth()->user()->can('user-qtv-edit')){
                        $temp.= "<a href=\"".route('admin.user-qtv.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    };
                    if ($row->status == 0) {
                        if(auth()->user()->can('user-qtv-unlock')){
                            $temp .= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger unlock_toggle' data-toggle=\"modal\" data-target=\"#unlockModal\" class=\"unlock_toggle\" title=\"Mở khóa\"><i class=\"la la-unlock-alt\"></i></a>";
                        };
                    } else {
                        if(auth()->user()->can('user-qtv-unlock')){
                            $temp .= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger lock_toggle' data-toggle=\"modal\" data-target=\"#lockModal\" class=\"lock_toggle\" title=\"Khóa\"><i class=\"la la-lock\"></i></a>";
                        };
                    }
                    if(config('module.user-qtv.need_set_permission')){
                        if(auth()->user()->can('attach-permission-qtv')){
                            $temp.= "<a href=\"".route('admin.user-qtv.set_permission',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger \" title=\"Phân quyền\"><i class=\"la la-sitemap\"></i></a>";
                        }
                    }

                    return $temp;
                })
                ->rawColumns(['action', 'shop'])
                ->toJson();
        }
        $roles=Role::orderBy('order','asc')->get();
        return view('admin.user-qtv.index')->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('roles',$roles);
    }

    public function create(Request $request)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];

        $shop = null;
        $shop_groups = null;
        $roles = null;
        $user = Auth::user();
        if($user->hasRole('admin') || $user->type_information == 0){
            $roles=Role::orderBy('order','asc')->get();
        }
        elseif($user->type_information == 1){
            $roles=Role::orderBy('order','asc')->where('type_information',1)->get();
        }
        ActivityLog::add($request, 'Vào form create user-qtv');
        return view('admin.user-qtv.create_edit')
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('user', $user)
            ->with('roles', $roles);
    }

    public function store(Request $request)
    {
        $roleAdmin = Role::where('name','admin')->first();
        if(!$roleAdmin){
            return redirect()->back()->withErrors(__('Hệ thống chưa khởi tạo vai trò Admin.Liên hệ admin để xử lý'))->withInput();
        }
        if(!auth()->user()->hasRole('admin') && in_array($roleAdmin->id,$request->role_ids??[])){
            return redirect()->back()->withErrors(__('Bạn không có quyền tạo hoặc chỉnh sửa tài khoản Admin'))->withInput();
        }

        $this->validate($request, [
            'username' => 'required|unique:users,username',
            'email' => 'required|unique:users,email',
//            'account_type' => 'required',
            'role_ids' => 'required',
            'username' => 'required|min:3|max:16|unique:users|regex:/^([A-Za-z0-9])+$/i',
            'password' => 'required|min:6|max:32',
            'password2' => 'required|min:6|max:32',
        ], [
            'username.required' => 'Vui lòng nhập tên tài khoản',
            'username.min' => 'Tên tài khoản ít nhất 3 ký tự.',
            'username.unique' => 'Tên tài khoản đã được sử dụng.',
            'username.regex' => 'Tên tài khoản không ký tự đặc biệt',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'username.max' => 'Tên tài khoản không quá 16 ký tự.',
            'password.min' => 'Mật khẩu phải ít nhất 6 ký tự.',
            'password.max' => 'Mật khẩu không vượt quá 32 ký tự.',
            'password_confirmation.same' => 'Mật khẩu xác nhận không đúng.',
            'password2.required' => 'Vui lòng nhập mật khẩu',
            'password2.min' => 'Mật khẩu 2 phải ít nhất 6 ký tự.',
            'password2.max' => 'Mật khẩu 2 không vượt quá 32 ký tự.',
            'email.required' => 'Vui lòng nhập trường này',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.unique' => 'Địa chỉ email đã được sử dụng.',
//            'account_type.required' => 'Vui lòng chọn loại tài khoản',
            'role_ids.required' => 'Vui lòng chọn vai trò cho tài khoản',
        ]);

        $input = $request->all();
        $input = $request->except('balance');
//        if($input['account_type'] != 1 && $input['account_type'] != 3){
//            return redirect()->back()->withErrors(__('Loại tài khoản không hợp lệ'))->withInput();
//        }
        if ($request->filled('password')) {
            $input['password'] = \Hash::make($request->password);

        }
        if ($request->filled('password2')) {
            $input['password2'] = \Hash::make($request->password2);

        }
        $user = Auth::user();

        //return $shop_access;
        $input['created_by'] = auth()->user()->id;
        if(Auth::user()->can('user-qtv-classify')){
            $input['type_information'] = $request->type_information;
        }
        else{
            $input['type_information'] = $user->type_information;
        }
        $required_login_gmail = 0;
        if(Auth::user()->can('user-qtv-required-login-gmail')){
            $required_login_gmail = $request->required_login_gmail;
        }
        $input['required_login_gmail'] = $required_login_gmail;
//        $input['account_type'] = 2;
        $data = User::create($input);

        $ratioDefault = 80;
        for ($i = 0; $i < count((array)$request->group_id); $i++) {
            if ($request->group_id[$i] != null) {
                if ($request->ratio[$i] != '') {
                    $ratio = $request->ratio[$i];
                } else {
                    $ratio = $ratioDefault;
                }
                $input = [
                    'group_id' => $request->group_id[$i],
                    'user_id' => $data->id,
                    'ratio' => $ratio
                ];
                GameAccess::create($input);
            }
        }
        if($user->hasRole('admin') || $user->type_information == 0){
            //update roles of user
            $data->syncRoles(isset($request->role_ids) ? $request->role_ids : []);
        }
        else{
            $roles_ids = Role::where('type_information',1)->whereIn('id',$request->role_ids)->pluck('id')->toArray();
            $data->syncRoles(isset($request->role_ids) ? $request->role_ids : []);
        }
        if($request->filled('role_ids')){
            $roles = Role::whereIn('id',$request->get('role_ids'))->get();
            $message = '';
            $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
            $message .= "\n";
            $message .= "<b>".auth()->user()->username."</b> đã cập nhật nhóm vai trò cho thành viên <b>".$data->username."</b> :";
            $message .= "\n";
            foreach($roles as $key => $item){
                $message .= '- '.$item->title;
                $message .= "\n";
            }
            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_notify_roles'));
        }


        ActivityLog::add($request, 'Tạo mới thành công user-qtv #'.$data->id);

        if ($request->filled('submit-close')) {
            return redirect()->route('admin.user-qtv.index')->with('success', __('Thêm mới thành công !'));
        } else {
            return redirect()->back()->with('success', __('Thêm mới thành công !'));
        }
    }

    public function show(Request $request,$id)
    {

    }

    public function edit(Request $request,$id)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];

        $user = Auth::user();

        $data = User::with('roles', 'access_categories')->whereIn("account_type",[1,3]);
        if($user->hasRole('admin') || $user->can('user-qtv-list-all')){

        }
        elseif($user->can('user-qtv-list-type-information-0')){
            $data->where('type_information',0);
        }
        elseif($user->can('user-qtv-list-type-information-1')){
            $data->where('type_information',1);
        }

        $data = $data->findOrFail($id);

        $roles = null;
        if($user->hasRole('admin') || $user->type_information == 0){
            $roles=Role::orderBy('order','asc')->get();
        }
        else{
            $roles=Role::orderBy('order','asc')->where('type_information',1)->get();
        }

        ActivityLog::add($request, 'Vào form edit user-qtv #'.$data->id);
        return view('admin.user-qtv.create_edit')
            ->with('data', $data)
            ->with('user', $user)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('roles', $roles);
    }

    public function update(Request $request,$id)
    {
        $data = User::whereIn("account_type",[1,3]);
        $user = Auth::user();
        if($user->hasRole('admin') || $user->can('user-qtv-list-all')){

        }
        elseif($user->can('user-qtv-list-type-information-0')){
            $data->where('type_information',0);
        }
        elseif($user->can('user-qtv-list-type-information-1')){
            $data->where('type_information',1);
        }
        else{
            $data->whereNull('id');
        }

        $data = $data->findOrFail($id);

        $this->validate($request, [
            'role_ids' => 'required',
        ], [
            'role_ids.required' => 'Vui lòng chọn vai trò cho tài khoản',
        ]);

        $roleAdmin = Role::where('name','admin')->first();

        //kiểm tra có  quyền chỉnh sửa tài khoản admin
        if(!auth()->user()->hasRole('admin') && $data->hasRole('admin')){
            return redirect()->back()->withErrors(__('Bạn không có quyền tạo hoặc chỉnh sửa tài khoản Admin'))->withInput();
        }

        if(!auth()->user()->hasRole('admin') && in_array($roleAdmin->id,$request->role_ids??[])){
            return redirect()->back()->withErrors(__('Bạn không có quyền tạo hoặc chỉnh sửa tài khoản Admin'))->withInput();
        }

        $this->validate($request,[
            'email'=>'required|unique:users,email,'.$id
        ],[
            'email.required' => 'Vui lòng nhập trường này',
            'email.email'	=> 'Địa chỉ email không đúng định dạng.',
            'email.unique'	=> 'Địa chỉ email đã được sử dụng.',
        ]);

        $input = $request->except('username', 'password','password2', 'balance');
//        if($input['account_type'] != 1 && $input['account_type'] != 3){
//            return redirect()->back()->withErrors(__('Loại tài khoản không hợp lệ'))->withInput();
//        }
        if($request->filled('password'))
        {
            $input['password'] = \Hash::make($request->password);

        }
        if($request->filled('password2'))
        {
            $input['password2'] = \Hash::make($request->password2);
        }
        $shop = null;
        $shop_groups = null;
        $roles = null;

        $data = User::findOrFail($id);
        if(Auth::user()->can('user-qtv-classify')){
            $input['type_information'] = $request->type_information;
        }
        else{
            $input['type_information'] = $user->type_information;
        }
        $required_login_gmail = 0;
        if(Auth::user()->can('user-qtv-required-login-gmail')){
            $required_login_gmail = $request->required_login_gmail;
        }
        $input['required_login_gmail'] = $required_login_gmail;
//        $input['account_type'] = 2;

        $data->update($input);
        if($user->hasRole('admin') || $user->type_information == 0){
            //update roles of user
            $data->syncRoles(isset($request->role_ids) ? $request->role_ids : []);
        }
        else{
            $roles_ids = Role::where('type_information',1)->whereIn('id',$request->role_ids)->pluck('id')->toArray();
            $data->syncRoles(isset($request->role_ids) ? $request->role_ids : []);
        }
        if($request->filled('role_ids')){
            $roles = Role::whereIn('id',$request->get('role_ids'))->get();
            $message = '';
            $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
            $message .= "\n";
            $message .= "<b>".auth()->user()->username."</b> đã cập nhật nhóm vai trò cho thành viên <b>".$data->username."</b> :";
            $message .= "\n";
            foreach($roles as $key => $item){
                $message .= '- '.$item->title;
                $message .= "\n";
            }
            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_notify_roles'));
        }


        ActivityLog::add($request, 'Cập nhật thành công user-qtv #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.user-qtv.index')->with('success',__('Cập nhật thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
        }
    }

    public function destroy(Request $request)
    {



        // $input=explode(',',$request->id);

        // $currentUserRole=auth()->user()->hasRole('admin');

        //  $data=User::with(['roles'=>function($query){
        //  }])->whereIn('id',$input)
        //      ->where("account_type",$this->account_type)
        //     ->get();
        //  foreach ($data as $aUser){
        //      $isAdmin=false;
        //      foreach ($aUser->roles??[] as $role){
        //         if($role->name=='admin'){
        //             $isAdmin=true;
        //             break;
        //         }
        //      }
        //      //nếu không phải admin thì không cho cập nhật,xóa user có quyền admin
        //      if(!$currentUserRole  &&  $isAdmin==true){
        //      }
        //      else{
        //          $aUser->update([
        //              'status'=>0
        //          ]);
        //          //nếu cho xóa user vĩnh viễn
        //          //$aUser->delete();
        //      }
        //  }
        // ActivityLog::add($request, 'Xóa thành công user-qtv #'.json_encode($input));

        // return redirect()->back()->with('success',__('Xóa thành công !'));
    }

    public function update_field(Request $request)
    {
        $input=explode(',',$request->id);
        $field=$request->field;
        $value=$request->value;
        $whitelist=['status'];
        if(!in_array($field,$whitelist)){
            return response()->json([
                'success'=>false,
                'message'=>__('Trường cập nhật không được chấp thuận'),
                'redirect'=> ''
            ]);
        }
        $data=User::whereIn('id',$input)->where("account_type",$this->account_type)->update([
            $field=>$value
        ]);
        ActivityLog::add($request, 'Cập nhật field thành công user-qtv '.json_encode($whitelist).' #'.json_encode($input));
        return response()->json([
            'success'=>true,
            'message'=>__('Cập nhật thành công !'),
            'redirect'=>''
        ]);
    }

    public function lock(Request $request)
    {
        $input = $request->id;
        $user = Auth::user();
        $data = User::whereIn('account_type',[1,3]);
        if($user->hasRole('admin') || $user->can('user-qtv-list-all')){

        }
        elseif($user->can('user-qtv-list-type-information-0')){
            $data->where('type_information',0);
        }
        elseif($user->can('user-qtv-list-type-information-1')){
            $data->where('type_information',1);
        }
        elseif($user->can('user-qtv-list-type-information-1-in-shop-tag')){
            $shop_access_index = $user->shop_access;
            if(empty($shop_access_index) || $shop_access_index == "all"){
                $data->whereNull('id');
            }
            else{
                $shop_access_index = json_decode($shop_access_index);
                if(isset($shop_access_index) && count($shop_access_index) > 0){
                    $data->where(function($q) use($shop_access_index){
                        foreach($shop_access_index as $item){
                            $q->orwhereJsonContains('shop_access',[$item]);
                        }
                    });
                }
            }
        }
        else{
            $data->whereNull('id');
        }
        $data = $data->findOrFail($input);
        $data->update([
            'status' => 0,
            'locker_by' => auth()->user()->username
        ]);
        SessionTracker::endSessionByUser($data->id);
        ActivityLog::add($request, 'Khóa thành công user-qtv #'.$data->id);
        return redirect()->back()->with('success', trans('Khóa tài khoản thành công'));
    }

    public function unlock(Request $request)
    {
        $user = Auth::user();
        $input = $request->id;
        $data = User::whereIn('account_type',[1,3]);
        if($user->hasRole('admin') || $user->can('user-qtv-list-all')){

        }
        elseif($user->can('user-qtv-list-type-information-0')){
            $data->where('type_information',0);
        }
        elseif($user->can('user-qtv-list-type-information-1')){
            $data->where('type_information',1);
        }
        elseif($user->can('user-qtv-list-type-information-1-in-shop-tag')){
            $shop_access_index = $user->shop_access;
            if(empty($shop_access_index) || $shop_access_index == "all"){
                $data->whereNull('id');
            }
            else{
                $shop_access_index = json_decode($shop_access_index);
                if(isset($shop_access_index) && count($shop_access_index) > 0){
                    $data->where(function($q) use($shop_access_index){
                        foreach($shop_access_index as $item){
                            $q->orwhereJsonContains('shop_access',[$item]);
                        }
                    });
                }
            }
        }
        else{
            $data->whereNull('id');
        }
        $data = $data->findOrFail($input);
        $data->update([
            'status' => 1,
        ]);
        ActivityLog::add($request, 'Mở khóa thành công user-qtv #'.$data->id);
        return redirect()->back()->with('success', 'Mở khóa tài khoản thành công');
    }

    public function view_profile(Request $request){

        $username=$request->username;
        $data=User::where('username',$username)->where('status',1)
//            ->where(function ($query){
//                $query->where("account_type",1);
//                $query->orWhere("account_type",3);
//            })
            ->firstOrFail();
        ActivityLog::add($request, 'Xem profile user #'.$data->id);
        return view('admin.user-qtv.view_profile')->with('user',$data);
    }

    public function getMoney(Request $request){
        $this->page_breadcrumbs=[[
            'page' => '#',
            'title' => __("Cộng trừ tiền cho thành viên")
        ]];
        $shop_access_user = Auth::user()->shop_access;
        $shop = Shop::orderBy('id','desc');
        if(isset($shop_access_user) && $shop_access_user !== "all"){
            $shop_access_user = json_decode($shop_access_user);
            $shop = $shop->whereIn('id',$shop_access_user);
        }
        $shop = $shop->pluck('title','id')->toArray();
        // $shop = $shop->get();

        ActivityLog::add($request, 'Vào form cộng tiền thành viên');
        return view('admin.user-qtv.money')
            ->with('shop', $shop)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    public function getMoneyQTV(Request $request){

        $this->page_breadcrumbs=[[
            'page' => '#',
            'title' => __("Cộng trừ tiền cho QTV(CTV)")
        ]];
        $shop_access_user = Auth::user()->shop_access;
        $shop = Shop::orderBy('id','desc');
        if(isset($shop_access_user) && $shop_access_user !== "all"){
            $shop_access_user = json_decode($shop_access_user);
            $shop = $shop->whereIn('id',$shop_access_user);
        }
        $shop = $shop->pluck('title','id')->toArray();
        // $shop = $shop->get();

        ActivityLog::add($request, 'Vào form cộng tiền QTV');
        return view('admin.user-qtv.money-qtv')
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    // Cộng tiền cho thành viên của shop
    public function postMoney(Request $request){

        $this->validate($request, [
            'amount' => 'required',
            'password2' => 'required',
        ], [
            'amount.required' => "Vui lòng nhập số tiền",
            'password2.required' => "Vui lòng nhập mật khẩu cấp 2",

        ]);
        //check password2
        if(!\Hash::check($request->password2,\Auth::user()->password2)){
            session()->put('fail_password2',  session()->get('fail_password2')+1);
            DB::rollBack();
            return redirect()->back()->withErrors(__('Mật khẩu cấp 2 không đúng'))->withInput();
        }
        else{
            session()->put('fail_password2', 0);
        }

        $shop_access_user = Auth::user()->shop_access;
        if(isset($shop_access_user) && $shop_access_user != 'all'){
            $shop_access_user = json_decode($shop_access_user);
            if(!in_array($request->shop_id,$shop_access_user)){
                return redirect()->back()->withErrors(__('Bạn không có quyền cộng tiền tài khoản thành viên cho shop này.'));
            }
        }
        if($request->mode==1){
            $this->validate($request, [
                'source_type' => 'required|numeric',
                'field' => 'required|in:id,username,email',
            ], [
                'source_type.required' => "Vui lòng chọn nguồn tiền cộng",
                'source_type.numeric' => "Vui lòng chọn nguồn tiền cộng",
                'field.required' => 'Trường thông tin tìm kiếm không phù hợp',
                'field.in' => 'Trường thông tin tìm kiếm không phù hợp',
            ]);
        }

        if($request->source_type==1 && $request->source_bank==""){
            return redirect()->back()->withErrors("Vui lòng chọn ngân hàng/ví" )->withInput();
        }
        if($request->source_type==2 && $request->source_bank==""){
            return redirect()->back()->withErrors("Vui lòng chọn ngân hàng/ví" )->withInput();
        }
        if($request->source_type==4 && $request->source_bank==""){
            return redirect()->back()->withErrors("Vui lòng chọn ngân hàng/ví" )->withInput();
        }
        // Start transaction!
        DB::beginTransaction();
        try {

            $delayTime=30;
            //tìm user cộng trừ tiền
            $userTransaction = User::where($request->field, $request->username)
                ->where('account_type',2)
                ->where('id', $request->id)
                ->lockForUpdate()->first();
            if (!$userTransaction) {
                DB::rollBack();
                return redirect()->back()->withErrors('Không tìm thấy người dùng');
            }

            if ($userTransaction->checkBalanceValid() == false) {
                DB::rollback();
                return redirect()->back()->withErrors('Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý');

            }


            if(abs(strtotime(Carbon::now()) - strtotime($userTransaction->last_add_balance))<$delayTime){
                DB::rollBack();
                return redirect()->back()
                    ->withErrors('Vui lòng thực hiện thao tác tiếp theo sau '.($delayTime-abs(strtotime(Carbon::now()) -strtotime($userTransaction->last_add_balance)).'s' ))
                    ->withInput();
            }
            $amount=(int)str_replace(array(' ', ',','.'), '', $request->amount);

            if($amount<=0){
                DB::rollBack();
                return redirect()->back()->withErrors('Số tiền thực hiện phải lớn hơn 0')->withInput();
            }
            //nếu cộng tiền

            if($request->mode==1)
            {
                $userTransaction->balance=$userTransaction->balance+$amount;
                $userTransaction->balance_in=$userTransaction->balance_in+$amount;
                $userTransaction->last_add_balance=Carbon::now();
                $userTransaction->save();

                PlusMoney::create([
                    'user_id'=>$userTransaction->id,
                    'shop_id' => $userTransaction->shop_id,
                    'is_add'=>'1',//Cộng tiền
                    'amount'=>$amount,
                    'source_type'=>$request->source_type,
                    'source_bank'=>$request->source_bank,
                    'processor_id'=>auth()->user()->id,
                    'description'=>$request->description,
                    'status'=>1,

                ])->txns()->create([
                    'user_id'=>$userTransaction->id,
                    'shop_id' => $userTransaction->shop_id,
                    'trade_type'=>'plus_money', //cộng tiền
                    'is_add'=>'1',//Cộng tiền
                    'amount'=>$amount,
                    'last_balance'=>$userTransaction->balance,
                    'description'=>'Cộng tiền tài khoản '.' [ +'.currency_format($amount).' ]',
                    'ip'=>$request->getClientIp(),
                    'status'=>1
                ]);
            }
            //nếu trừ tiền
            elseif($request->mode==0)
            {
                //nếu số tiền nhỏ hơn balance sẽ không cho trừ
                if($userTransaction->balance<$amount){
                    DB::rollBack();
                    return redirect()->back()->withErrors('Số dư của tài khoản không đủ để trừ.Vui lòng thử lại')->withInput();
                }
                if(abs(strtotime(Carbon::now()) - strtotime($userTransaction->last_minus_balance))<$delayTime){
                    DB::rollBack();
                    return redirect()->back()
                        ->withErrors('Vui lòng thực hiện thao tác tiếp theo sau '.($delayTime-abs(strtotime(Carbon::now()) -strtotime($userTransaction->last_minus_balance)).'s' ))
                        ->withInput();
                }
                $userTransaction->balance=$userTransaction->balance-$amount;
                $userTransaction->balance_out=$userTransaction->balance_out+$amount;
                $userTransaction->last_minus_balance=Carbon::now();
                $userTransaction->save();
                PlusMoney::create([
                    'user_id'=>$userTransaction->id,
                    'is_add'=>'0',//Trừ tiền
                    'shop_id' => $userTransaction->shop_id,
                    'amount'=>$amount,
                    'source_type'=>$request->source_type,
                    'source_bank'=>$request->source_bank,
                    'processor_id'=>auth()->user()->id,
                    'description'=>$request->description,
                    'status'=>1,
                ])->txns()->create([
                    'user_id'=>$userTransaction->id,
                    'shop_id' => $userTransaction->shop_id,
                    'trade_type'=>'minus_money', //trừ tiền
                    'is_add'=>'0',//Trừ tiền
                    'amount'=>$amount,
                    'last_balance'=>$userTransaction->balance,
                    'description'=>'Trừ tiền tài khoản '.' [ -'.currency_format($amount).' ]',
                    'ip'=>$request->getClientIp(),
                    'status'=>1
                ]);
            }
            else{
                DB::rollBack();
                return redirect()->back()->withErrors('Không có chức năng yêu cầu.Vui lòng thử lại')->withInput();
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error($e);
            return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');
        }
        // Commit the queries!
        DB::commit();
        $type_money = $this->converSourceTypeMoney($request->source_type);
        $type_bank = $request->source_bank;
        if($request->mode==1){
            ActivityLog::add($request, 'Cộng tiền tài khoản '.$userTransaction->username.' [ +'.currency_format($amount).' ]'.' thành công');
            $message = '<b>'.auth()->user()->username.'</b> cộng tiền tài khoản thành viên <b>'.$userTransaction->username.'</b> - [ +'.currency_format($amount).' ]'.' thành công. - Nội dung: '.$request->description.". - Nguồn tiền: ".$type_money.". - Ngân hàng: ".$type_bank;
            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_noty_congtien'));
            return redirect()->route('admin.get_money')
                ->with('success','Cộng tiền tài khoản '.$userTransaction->username.' - [ +'.currency_format($amount).' ]'.' thành công')
                ->withInput($request->only('field','username'));
        }
        else{
            ActivityLog::add($request, 'Trừ tiền tài khoản '.$userTransaction->username.' [ -'.currency_format($amount).' ]'.' thành công');
            $message = '<b>'.auth()->user()->username.'</b> trừ tiền tài khoản thành viên <b>'.$userTransaction->username.'</b> - [ -'.currency_format($amount).' ]'.' thành công. - Nội dung: '.$request->description.". - Nguồn tiền: ".$type_money.". - Ngân hàng: ".$type_bank;
            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_noty_congtien'));
            return redirect()->route('admin.get_money')
                ->with('success','Trừ tiền tài khoản '.$userTransaction->username.' [ -'.currency_format($amount).' ]'.' thành công')
                ->withInput($request->only('field','username'));
        }
    }

    function converSourceTypeMoney($type){
        if($type == ""){
            return "";
        }
        $source_type = [
            1 => "ATM",
            2 => "Ví điện tử",
            3 => "Khác",
            4 => "Mo Mo",
            5 => "Tiền PR",
            6 => "Tiền test",
            7 => "Tiền thẻ lỗi",
        ];
        return $source_type[$type];
    }

    // Cộng tiền cho QTV(CTV)
    public function postMoneyQTV(Request $request){

        $this->validate($request, [
            'amount' => 'required',
            'password2' => 'required',
        ], [
            'amount.required' => "Vui lòng nhập số tiền",
            'password2.required' => "Vui lòng nhập mật khẩu cấp 2",
        ]);
        //check password2
        if(!\Hash::check($request->password2,\Auth::user()->password2)){
            session()->put('fail_password2',  session()->get('fail_password2')+1);
            DB::rollBack();
            return redirect()->back()->withErrors(__('Mật khẩu cấp 2 không đúng'))->withInput();
        }
        else{
            session()->put('fail_password2', 0);
        }
        if($request->mode==1){
            $this->validate($request, [
                'source_type' => 'required|numeric',
                'field' => 'required|in:id,username,email',
            ], [
                'source_type.required' => "Vui lòng chọn nguồn tiền cộng",
                'source_type.numeric' => "Vui lòng chọn nguồn tiền cộng",
                'field.required' => 'Trường thông tin tìm kiếm không phù hợp',
                'field.in' => 'Trường thông tin tìm kiếm không phù hợp',
            ]);
        }
        if(Auth::user()->account_type != 1){
            return redirect()->back()->withErrors("Tính năng chỉ được sử dụng bởi Quản trị viên" );
        }
        if($request->source_type==1 && $request->source_bank==""){
            return redirect()->back()->withErrors("Vui lòng chọn ngân hàng/ví" )->withInput();
        }
        if($request->source_type==2 && $request->source_bank==""){
            return redirect()->back()->withErrors("Vui lòng chọn ngân hàng/ví" )->withInput();
        }
        if($request->source_type==4 && $request->source_bank==""){
            return redirect()->back()->withErrors("Vui lòng chọn ngân hàng/ví" )->withInput();
        }
        // Start transaction!
        DB::beginTransaction();
        try {

            $delayTime=30;
            //tìm user cộng trừ tiền
            $userTransaction = User::where($request->field, $request->username);
            // trường hợp qtv duyệt tiền là admin
            if(Auth::user()->hasRole('admin')){

            }
            // trường hợp qtv duyệt tiền là shop nhà
//            elseif(Auth::user()->type_information == 0){
//                $userTransaction->where('type_information',0);
//            }
//            // trường hợp qtv duyệt tiền là shop khách
//            elseif(Auth::user()->type_information == 1){
//                $userTransaction->where('type_information',1);
//            }
            else{
                $userTransaction->whereNull('id');
            }
            $userTransaction = $userTransaction->whereIn('account_type',[1,3])->lockForUpdate()->first();

            if (!$userTransaction) {
                DB::rollBack();
                return redirect()->back()->withErrors('Không tìm thấy người dùng');
            }
            if(abs(strtotime(Carbon::now()) - strtotime($userTransaction->last_add_balance))<$delayTime){
                DB::rollBack();
                return redirect()->back()
                    ->withErrors('Vui lòng thực hiện thao tác tiếp theo sau '.($delayTime-abs(strtotime(Carbon::now()) -strtotime($userTransaction->last_add_balance)).'s' ))
                    ->withInput();
            }
            $amount=(int)str_replace(array(' ', ',','.'), '', $request->amount);

            if($amount<=0){
                DB::rollBack();
                return redirect()->back()->withErrors('Số tiền thực hiện phải lớn hơn 0')->withInput();
            }
            //nếu cộng tiền
            if($request->mode==1)
            {
                $userTransaction->balance=$userTransaction->balance+$amount;
                $userTransaction->balance_in=$userTransaction->balance_in+$amount;
                $userTransaction->last_add_balance=Carbon::now();
                $userTransaction->save();
                PlusMoney::create([
                    'user_id'=>$userTransaction->id,
                    'is_add'=>'1',//Cộng tiền
                    'amount'=>$amount,
                    'source_type'=>$request->source_type,
                    'source_bank'=>$request->source_bank,
                    'processor_id'=>auth()->user()->id,
                    'description'=>$request->description,
                    'status'=>1,
                ])->txns()->create([
                    'user_id'=>$userTransaction->id,
                    'trade_type'=>'plus_money', //cộng tiền
                    'is_add'=>'1',//Cộng tiền
                    'amount'=>$amount,
                    'last_balance'=>$userTransaction->balance,
                    'description'=>'Cộng tiền tài khoản '.' [ +'.currency_format($amount).' ]',
                    'ip'=>$request->getClientIp(),
                    'status'=>1
                ]);
            }
            //nếu trừ tiền
            elseif($request->mode==0)
            {
                //nếu số tiền nhỏ hơn balance sẽ không cho trừ
                if($userTransaction->balance<$amount){
                    DB::rollBack();
                    return redirect()->back()->withErrors('Số dư của tài khoản không đủ để trừ.Vui lòng thử lại')->withInput();
                }
                if(abs(strtotime(Carbon::now()) - strtotime($userTransaction->last_minus_balance))<$delayTime){
                    DB::rollBack();
                    return redirect()->back()
                        ->withErrors('Vui lòng thực hiện thao tác tiếp theo sau '.($delayTime-abs(strtotime(Carbon::now()) -strtotime($userTransaction->last_minus_balance)).'s' ))
                        ->withInput();
                }
                $userTransaction->balance=$userTransaction->balance-$amount;
                $userTransaction->balance_out=$userTransaction->balance_out+$amount;
                $userTransaction->last_minus_balance=Carbon::now();
                $userTransaction->save();
                PlusMoney::create([
                    'user_id'=>$userTransaction->id,
                    'is_add'=>'0',//Trừ tiền
                    'amount'=>$amount,
                    'source_type'=>$request->source_type,
                    'source_bank'=>$request->source_bank,
                    'processor_id'=>auth()->user()->id,
                    'description'=>$request->description,
                    'status'=>1,
                ])->txns()->create([
                    'user_id'=>$userTransaction->id,
                    'trade_type'=>'minus_money', //trừ tiền
                    'is_add'=>'0',//Trừ tiền
                    'amount'=>$amount,
                    'last_balance'=>$userTransaction->balance,
                    'description'=>'Trừ tiền tài khoản '.' [ -'.currency_format($amount).' ]',
                    'ip'=>$request->getClientIp(),
                    'status'=>1
                ]);
            }
            else{
                DB::rollBack();
                return redirect()->back()->withErrors('Không có chức năng yêu cầu.Vui lòng thử lại')->withInput();
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error($e);
            return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');
        }
        // Commit the queries!
        DB::commit();
        $type_money = $this->converSourceTypeMoney($request->source_type);
        $type_bank = $request->source_bank;
        if($request->mode==1){
            ActivityLog::add($request, 'Cộng tiền tài khoản '.$userTransaction->username.' [ +'.currency_format($amount).' ]'.' thành công');
            $message = '<b>'.auth()->user()->username.'</b> cộng tiền tài khoản CTV <b>'.$userTransaction->username.'</b> [ +'.currency_format($amount).' ]'.' thành công. - Nội dung: '.$request->description.". - Nguồn tiền: ".$type_money.". - Ngân hàng: ".$type_bank;
            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_noty_congtien'));
            return redirect()->route('admin.get_money_qtv')
                ->with('success','Cộng tiền tài khoản '.$userTransaction->username.' [ +'.currency_format($amount).' ]'.' thành công')
                ->withInput($request->only('field','username'));
        }
        else{
            ActivityLog::add($request, 'Trừ tiền tài khoản '.$userTransaction->username.' [ -'.currency_format($amount).' ]'.' thành công');
            $message = '<b>'.auth()->user()->username.'</b> trừ tiền tài khoản CTV <b>'.$userTransaction->username.'</b> [ -'.currency_format($amount).' ]'.' thành công. - Nội dung: '.$request->description.". - Nguồn tiền: ".$type_money.". - Ngân hàng: ".$type_bank;
            Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_noty_congtien'));
            return redirect()->route('admin.get_money_qtv')
                ->with('success','Trừ tiền tài khoản '.$userTransaction->username.' [ -'.currency_format($amount).' ]'.' thành công')
                ->withInput($request->only('field','username'));
        }
    }

    public function getVP(Request $request){
        $this->page_breadcrumbs=[[
            'page' => '#',
            'title' => __("Cộng trừ vật phẩm cho thành viên")
        ]];

        $typeVp = Item::where('module','gametype')->get();

        ActivityLog::add($request, 'Vào form cộng trừ vật phẩm user-qtv');
        return view('admin.user-qtv.vp')
            ->with('typeVp', $typeVp)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    public function postVP(Request $request){
        $this->validate($request, [
            'amount' => 'required',
            'password2' => 'required',
            'description' => 'required',
        ], [
            'amount.required' => "Vui lòng nhập số tiền",
            'password2.required' => "Vui lòng nhập mật khẩu cấp 2",
            'description.required' => "Nội dung bị thiếu",

        ]);
        //check password2
        if(!\Hash::check($request->password2,\Auth::user()->password2)){
            session()->put('fail_password2',  session()->get('fail_password2')+1);
            DB::rollBack();
            return redirect()->back()->withErrors(__('Mật khẩu cấp 2 không đúng'))->withInput();
        }
        else{
            session()->put('fail_password2', 0);
        }
        $type_vp = $request->type_vp;
        $username=$request->username;
        $source = $request->source_vp;

        DB::beginTransaction();
        try {
            $delayTime=30;
            //tìm user cộng trừ vp
            $userTransaction = User::where($request->field, $request->username)->where('shop_id',$request->shop_id)->lockForUpdate()->first();
            $amount=(int)str_replace(array('.', ','), '', $request->amount);

            if($amount<=0){
                DB::rollback();
                return redirect()->back()->withErrors('Số vật phẩm thực hiện phải lớn hơn 0')->withInput();
            }
            //nếu cộng vật phẩm
            if($request->mode==1){
                // kiểm tra loại vật phẩm cộng
                // trường hợp cộng ngọc
                if($type_vp === "gem_num"){
                    $userTransaction->gem_num = $userTransaction->gem_num + $amount;
                    $last_balance_vp = $userTransaction->gem_num;
                    $type_vp = 12;
                }
                // trường hợp cộng coin
                elseif($type_vp === "coin_num"){
                    $userTransaction->coin_num = $userTransaction->coin_num + $amount;
                    $last_balance_vp = $userTransaction->coin_num;
                    $type_vp = 14;
                }
                // trường hợp cộng xu
                elseif($type_vp === "xu_num"){
                    $userTransaction->xu_num = $userTransaction->xu_num + $amount;
                    $last_balance_vp = $userTransaction->xu_num;
                    $type_vp = 11;
                }
                // trường hợp cộng xu
                elseif($type_vp === "robux_num"){
                    $userTransaction->robux_num = $userTransaction->robux_num + $amount;
                    $last_balance_vp = $userTransaction->robux_num;
                    $type_vp = 13;
                }
                // trường hợp cộng xu
                elseif($type_vp === "robux_pet_num"){
                    $userTransaction->robux_pet_num = $userTransaction->robux_pet_num + $amount;
                    $last_balance_vp = $userTransaction->robux_pet_num;
                    $type_vp = 15;
                }
                // trường hợp vật phẩm game
                else{
                    $parent_id_vp = Item::where('module','gametype')->where('parent_id',$type_vp)->first();
                    if(!$parent_id_vp){
                        DB::rollback();
                        return redirect()->back()->withErrors('Loại vật phẩm không hợp lệ.')->withInput();
                    }
                    $userTransaction['ruby_num'.$type_vp] = $userTransaction['ruby_num'.$type_vp]+$amount;
                    $last_balance_vp = $userTransaction['ruby_num'.$type_vp];
                }
                $userTransaction->save();
                //tạo tnxs vp
                $txns = TxnsVp::create([
                    'trade_type' => 'plus_vp',
                    'is_add' => '1',
                    'user_id' =>  $userTransaction->id,
                    'amount' => $amount,
                    'last_balance' => $last_balance_vp,
                    'description' =>  $request->description,
                    'txnsable_type' =>  null,
                    'ip' => $request->getClientIp(),
                    'status' => 1,
                    'shop_id' =>  $userTransaction->shop_id,
                    'item_type' =>  $type_vp
                ]);
                if($type_vp!='gem' && $type_vp!='coin' && $type_vp!='xu'){
                    $name_item = Item::where('module', config('module.minigame.module.gametype'))
                        ->where('parent_id', $type_vp)->first();
                    $type_vp = $name_item->title;
                }

                $ip = $request->getClientIp();
                $user_agent = $request->userAgent();
                $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                $message .= "\n";
                $message .= 'Tài khoản qtv <b>'.auth()->user()->username.'</b> cộng vật phẩm tài khoản thành viên <b>'.$userTransaction->username.'</b> - '.$userTransaction->shop->domain.' [ +'.currency_format($amount).' ]'.' thành công. - Nội dung: '.$request->description.". - Loại vật phẩm: ".$type_vp;
                $message .= "\n";
                $message .= "IP: <b>".$ip."</b>";
                $message .= "\n";
                $message .= "User_agent: <b>".$user_agent."</b>";
                Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_noty_congvatpham'));


                ActivityLog::add($request, 'Cộng vật phẩm, tài khoản '.$userTransaction->username.' [ +'.currency_format($amount).' ]'.' thành công');
                DB::commit();
                return redirect()->back()->with('success','Cộng vật phẩm tài khoản '.$userTransaction->username.' [ +'.number_format($amount).' ]'.' thành công');
            }
            elseif($request->mode==0){
                // kiểm tra loại vật phẩm trừ
                // trường hợp trừ ngọc
                if($type_vp === "gem_num"){
                    if($userTransaction->gem_num < $amount){
                        DB::rollback();
                        return redirect()->back()->withErrors('Số gem của tài khoản không đủ để trừ, vui lòng kiểm tra lại')->withInput();
                    }
                    $userTransaction->gem_num = $userTransaction->gem_num - $amount;
                    $last_balance_vp = $userTransaction->gem_num;
                    $type_vp = 12;
                }
                // trường hợp trừ coin
                elseif($type_vp === "coin_num"){
                    if($userTransaction->coin_num < $amount){
                        DB::rollback();
                        return redirect()->back()->withErrors('Số coin của tài khoản không đủ để trừ, vui lòng kiểm tra lại')->withInput();
                    }
                    $userTransaction->coin_num = $userTransaction->coin_num - $amount;
                    $last_balance_vp = $userTransaction->coin_num;
                    $type_vp = 14;
                }
                // trường hợp trừ xu
                elseif($type_vp === "xu_num"){
                    if($userTransaction->xu_num < $amount){
                        DB::rollback();
                        return redirect()->back()->withErrors('Số xu của tài khoản không đủ để trừ, vui lòng kiểm tra lại')->withInput();
                    }
                    $userTransaction->xu_num = $userTransaction->xu_num - $amount;
                    $last_balance_vp = $userTransaction->xu_num;
                    $type_vp = 11;
                }// trường hợp trừ roblox
                elseif($type_vp === "robux_num"){
                    if($userTransaction->robux_num < $amount){
                        DB::rollback();
                        return redirect()->back()->withErrors('Số robux của tài khoản không đủ để trừ, vui lòng kiểm tra lại')->withInput();
                    }
                    $userTransaction->robux_num = $userTransaction->robux_num - $amount;
                    $last_balance_vp = $userTransaction->robux_num;
                    $type_vp = 13;
                }
                elseif($type_vp === "robux_pet_num"){
                    if($userTransaction->robux_pet_num < $amount){
                        DB::rollback();
                        return redirect()->back()->withErrors('Số robux của tài khoản không đủ để trừ, vui lòng kiểm tra lại')->withInput();
                    }
                    $userTransaction->robux_pet_num = $userTransaction->robux_pet_num - $amount;
                    $last_balance_vp = $userTransaction->robux_pet_num;
                    $type_vp = 15;
                }
                // trường hợp trừ vật phẩm game
                else{
                    $parent_id_vp = Item::where('module','gametype')->where('parent_id',$type_vp)->first();
                    if(!$parent_id_vp){
                        DB::rollback();
                        return redirect()->back()->withErrors('Loại vật phẩm không hợp lệ.')->withInput();
                    }
                    if($userTransaction['ruby_num'.$type_vp]<$amount){
                        DB::rollback();
                        return redirect()->back()->withErrors(__('Số vật phẩm của tài khoản không đủ để trừ.Vui lòng thử lại'))->withInput();
                    }
                    $userTransaction['ruby_num'.$type_vp] = $userTransaction['ruby_num'.$type_vp]-$amount;
                    $last_balance_vp = $userTransaction['ruby_num'.$type_vp];
                }
                $userTransaction->save();
                //tạo tnxs vp
                $txns = TxnsVp::create([
                    'trade_type' => 'plus_vp',
                    'is_add' => '0',
                    'user_id' =>  $userTransaction->id,
                    'amount' => $amount,
                    'last_balance' => $last_balance_vp,
                    'description' =>  $request->description,
                    'txnsable_type' =>  null,
                    'ip' => $request->getClientIp(),
                    'status' => 1,
                    'shop_id' =>  $userTransaction->shop_id,
                    'item_type' =>  $type_vp
                ]);
                if($type_vp!='gem' && $type_vp!='coin' && $type_vp!='xu'){
                    $name_item = Item::where('module', config('module.minigame.module.gametype'))
                        ->where('parent_id', $type_vp)->first();
                    $type_vp = $name_item->title;
                }

                $ip = $request->getClientIp();
                $user_agent = $request->userAgent();
                $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                $message .= "\n";
                $message .= 'Tài khoản qtv <b>'.auth()->user()->username.'</b> trừ vật phẩm tài khoản thành viên <b>'.$userTransaction->username.'</b> - '.$userTransaction->shop->domain.' [ - '.currency_format($amount).' ]'.' thành công. - Nội dung: '.$request->description.". - Loại vật phẩm: ".$type_vp;
                $message .= "\n";
                $message .= "IP: <b>".$ip."</b>";
                $message .= "\n";
                $message .= "User_agent: <b>".$user_agent."</b>";
                Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_noty_congvatpham'));

                ActivityLog::add($request, 'Trừ vật phẩm, tài khoản '.$userTransaction->username.' [ -'.currency_format($amount).' ]'.' thành công');
                DB::commit();
                return redirect()->back()->with('success','Trừ vật phẩm tài khoản '.$userTransaction->username.' [ -'.number_format($amount).' ]'.' thành công');
            }

        }
        catch(\Exception $e)
        {
            DB::rollback();
            // return $e->getMessage();
            Log::error('[ErrorByCode]'.$e->getMessage() );
            return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');
        }

    }

    public function getUserToVP(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'field' => 'required|in:id,username,email',
        ], [
            'username.required' => 'Vui lòng nhập tên tài khoản',
            'field.required' => 'Trường thông tin tìm kiếm không phù hợp',
            'field.in' => 'Trường thông tin tìm kiếm không phù hợp',
        ]);

        $user = User::where($request->field, $request->username)->firstOrFail();

        $data= PlusMoney::with('txns','processor','user')
            ->orderBy('created_at', 'DESC')
            ->where('user_id', $user->id)
            ->limit(10)->get();

        $typeVp = Item::where('module','gametype')->get();

        ActivityLog::add($request, 'Lấy lịch sử cộng tiền user-qtv'.$user->id);
        return view('admin.user-qtv.show-txns-vp')
            ->with('data', $data)
            ->with('typeVp', $typeVp)
            ->with('user', $user);

    }

    public function getUserToMoney(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'field' => 'required|in:id,username,email',
        ], [
            'username.required' => 'Vui lòng nhập tên tài khoản',
            'field.required' => 'Trường thông tin tìm kiếm không phù hợp',
            'field.in' => 'Trường thông tin tìm kiếm không phù hợp',

        ]);

        $user = User::where($request->field, $request->username)->where('status',1)->firstOrFail();

        $data=PlusMoney::with('txns','processor','user')
            ->orderBy('created_at', 'DESC')
            ->where('user_id', $user->id)
            ->limit(10)->get();

        ActivityLog::add($request, 'Lấy lịch sử cộng tiền user-qtv'.$user->id);
        return view('admin.user-qtv.show-txns')
            ->with('data', $data)
            ->with('user', $user);

    }

    public function getUserToMoneyQTV(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'field' => 'required|in:id,username,email',
        ], [
            'username.required' => 'Vui lòng nhập tên tài khoản',
            'field.required' => 'Trường thông tin tìm kiếm không phù hợp',
            'field.in' => 'Trường thông tin tìm kiếm không phù hợp',

        ]);
        $user = User::where($request->field, $request->username);
//        if(Auth::user()->hasRole('admin')){
//
//        }
//        // trường hợp qtv duyệt tiền là shop nhà
//        elseif(Auth::user()->type_information == 0){
//            $user->where('type_information',0);
//        }
//        elseif(Auth::user()->type_information == 1){
//            $user->where('type_information',1);
//            $shop_access = Auth::user()->shop_access;
//            $shop_access = json_decode($shop_access);
//            if(isset($shop_access) && count($shop_access) > 0){
//                $user->where(function($q) use($shop_access){
//                    foreach($shop_access as $item){
//                        $q->orwhereJsonContains('shop_access',[$item]);
//                    };
//                });
//            }
//        }
//        else{
//            $user->whereNull('id');
//        }

        $user = $user->whereIn('account_type',[1,3])->where('status',1)->firstOrFail();
        $data=PlusMoney::with('txns','processor','user')
            ->orderBy('created_at', 'DESC')
            ->where('user_id', $user->id)
            ->limit(10)->get();

        ActivityLog::add($request, 'Lấy lịch sử cộng tiền user-qtv'.$user->id);
        return view('admin.user-qtv.show-txns-qtv')
            ->with('data', $data)
            ->with('user', $user);

    }

    public function set_permission(Request $request,$id){

        if(!config('module.user-qtv.need_set_permission')){
            return redirect()->back()->withErrors(__('Chức năng chưa được kích hoạt'));
        }

        $this->page_breadcrumbs[] = [
            'page' => '#',
            'title' => __("Phân quyền truy cập")
        ];
        $data = User::whereIn('account_type',[1,3])->with('access_categories', 'access_shops', 'access_shop_groups')->findOrFail($id);
        $user = Auth::user();
        $datatableService = null;
        $service_access = null;
        $providers = null;
        $shop_groups = null;
        $shops = null;
        $service_access = ServiceAccess::where('user_id', $data->id)->where('module','user')->first();
        if($user->hasRole('admin') || $user->type_information == 0){
            // lấy thông tin danh mục acc
            $providers = Group::where('module', 'acc_provider')->with('childs')->orderBy('order')->get();
            //lấy all dịch vụ
            $datatableService = Item::query()
                ->where('module', '=', config('module.service.key'))
                ->where('status',1)
                ->orderBy('order')
                ->get();
            //lấy các quyền được up tài khoản game

            $shops = Shop::orderBy('id','desc')->get();
            $shop_groups = Shop_Group::orderBy('id','desc')->get();
        }
        else{
            $shop_access_qtv = $user->shop_access;
            $shop_access_ctv = $data->shop_access;
            if(empty($shop_access_ctv) || $shop_access_ctv == "all"){
                return "Dữ liệu điểm bán bất đồng bộ";
            }
            else{
                $service_access_auth = ServiceAccess::where('user_id', Auth::user()->id)
                    ->where('module','user')
                    ->first();

                if (isset($service_access_auth) && $service_access_auth->params){
                    $param = json_decode(isset($service_access_auth->params) ? $service_access_auth->params : "");
                    if (isset($param) && $param->accept_role){
                        $accept_role = $param->accept_role;

                        $datatableService = Item::query()
                            ->where('module', '=', config('module.service.key'))
                            ->where('status',1)
                            ->whereIn('id',$accept_role)
                            ->orderBy('order')
                            ->get();
                    }
                }

                $shop_access_qtv = json_decode($shop_access_qtv);
                $shop_access_ctv = json_decode($shop_access_ctv);
                $shop_ids = array_intersect($shop_access_qtv,$shop_access_ctv);

                // lấy thông tin danh mục acc
                $providers = Group::where('module', 'acc_provider')->with('childs',function($q) use ($shop_ids){
                    $q->where(function($query) use($shop_ids){
                        foreach ($shop_ids as $id) {
                            $query->whereHas('custom', function($query) use($id){
                                $query->where(['shop_id' => $id, 'status' => 1]);
                            });
                        }
                    });
                })->orderBy('order')->get();
                $shops = Shop::orderBy('id','desc')->whereIn('id',$shop_ids)->get();


            }
        }
        ActivityLog::add($request, 'Vào form cập nhật permission user-qtv #'.$data->id);
        return view('admin.user-qtv.set_permission')
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('user', $user)
            ->with('shops', $shops)
            ->with('shop_groups', $shop_groups)
            ->with('providers', $providers)
            ->with('datatableService', $datatableService)
            ->with('service_access', $service_access);
    }

    public function post_set_permission(Request $request,$id){

        if(!config('module.user-qtv.need_set_permission')){
            return redirect()->back()->withErrors(__('Chức năng chưa được kích hoạt'));
        }
        $user = Auth::user();
        $data = User::whereIn('account_type',[1,3])->findOrFail($id);
        //tannm
        //tắt tính năng phân quyền lẻ cho user
        //$data->permissions()->sync(isset($request->permission_ids) ? explode(",",$request->permission_ids) : []);
        /*phaptq*/
        $discount = [];
        $all = $request->all();

//        return $all;
        if($user->hasRole('admin') || $user->type_information == 0){

            //author: tannm
            //phân quyền cho dịch vụ
            $permission_service['display_info_role'] = $request->display_info_role;
            $permission_service['view_role'] = $request->view_role;
            $permission_service['accept_role'] = $request->accept_role;
            $permission_service['accept_attribute_role'] = $request->accept_attribute_role;
            if (!empty($permission_service['accept_role']) && count($permission_service['accept_role']) > 0) {
                foreach ($permission_service['accept_role'] as $item) {

                    $permission_service['allow_server_' . $item] = $request->get('allow_server_' . $item);
                    $permission_service['allow_name_' . $item] = $request->get('allow_name_' . $item);
                    $permission_service['limit_' . $item] = $request->get('limit_' . $item);
                    $permission_service['ratio_' . $item] = $request->get('ratio_' . $item);
                }
            }

            $service_access=ServiceAccess::where('user_id',$data->id)->where('module','user')->first();
            if($service_access){
                if (!empty($service_access->params)){
                    $params = json_decode($service_access->params);
                    if (!empty($params->service_limit)){
                        $service_limit_list = $params->service_limit;
                        $permission_service['service_limit'] = $service_limit_list;
                    }
                }
                $service_access->params=json_encode($permission_service,JSON_UNESCAPED_UNICODE);
                $service_access->save();

            }
            else{
                ServiceAccess::create([
                    'module' => 'user',
                    'user_id' => $data->id,
                    'params' => json_encode($permission_service,JSON_UNESCAPED_UNICODE)
                ]);
            }
            //end phân quyền cho dịch vụ
        }
        else{
            //phân quyền cho dịch vụ
            $permission_service['display_info_role'] = $request->display_info_role;
            $permission_service['view_role'] = $request->view_role;
            $permission_service['accept_role'] = $request->accept_role;
            if (!empty($permission_service['accept_role']) && count($permission_service['accept_role']) > 0) {
                foreach ($permission_service['accept_role'] as $item) {

                    $permission_service['allow_server_' . $item] = $request->get('allow_server_' . $item);
                    $permission_service['allow_name_' . $item] = $request->get('allow_name_' . $item);
                    $permission_service['limit_' . $item] = $request->get('limit_' . $item);
                    $permission_service['ratio_' . $item] = $request->get('ratio_' . $item);
                }
            }

            $service_access= ServiceAccess::where('user_id',$data->id)->where('module','user')->first();

            if($service_access){
                if (!empty($service_access->params)){
                    $params = json_decode($service_access->params);
                    if (!empty($params->service_limit)){
                        $service_limit_list = $params->service_limit;
                        $permission_service['service_limit'] = $service_limit_list;
                    }
                }
                $service_access->params=json_encode($permission_service,JSON_UNESCAPED_UNICODE);
                $service_access->save();
            }
            else{
                ServiceAccess::create([
                    'module' => 'user',
                    'user_id' => $data->id,
                    'params' => json_encode($permission_service,JSON_UNESCAPED_UNICODE)
                ]);
            }
        }
        ActivityLog::add($request, 'Cập nhật permission thành công user-qtv #'.$data->id);
        return redirect()->back()->with('success',__('Phân quyền truy cập thành công'));
    }

    public function AccessUser(Request $request){
        $this->validate($request, [
            'description' => 'required',
        ], [
            'description.required' => 'Vui lòng nhập lí do truy cập tài khoản',
        ]);
        $id = $request->id;
        $description = $request->description;
        $user = User::where('account_type',2)->where('id',$id)->firstOrFail();

        $time = strtotime(Carbon::now()->addMinute(2));
        $token = JWTAuth::fromUser($user);
        $encrypt = config('module.user.encryt');
        $sign_access = Helpers::Encrypt($token.','.$time.','.Auth::user()->username,$encrypt);
        $url = config('app.url');
        Auth::guard('frontend')->login($user);

        ActivityLog::add($request, 'Truy cập tài khoản thành viên #'.$user->id. ". Lý do: ".$description);

        return response()->json([
            'message' => __('Đăng nhập thành công'),
            'status' => 1,
            'url' => $url,
        ]);
//        return redirect()->to($url);
    }

    public function showShop(Request $request,$id){

        $data = null;

        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShop::VeryShop();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        $user = User::whereIn('account_type',[1,3])->where('id',$id)->firstOrFail();

        $data = Shop::orderBy('id','desc');
        $shop_access_user = $user->shop_access;
        if(isset($shop_access_user) && $shop_access_user !== "all"){
            $shop_access_user = json_decode($shop_access_user);
            $data = $data->whereIn('id',$shop_access_user);
        }
        $data = $data->select('id','domain','title')->whereIn('id',$arr_permission)->get();

        return view('admin.user-qtv.show')->with('data',$data)->with('user',$user);

    }

    public function exportExcel(Request $request){

        ini_set('max_execution_time', 2400); //20 minutes

        $datatable= User::with(['roles'=>function($query){
            $query->select(['id','title','name']);
        }])
            ->where("account_type",1)
            ->orderByRaw('FIELD(`status`,1,2,3,4,0,-1,99)');

        if ($request->filled('id'))  {
            $datatable->where(function($q) use($request){
                $q->orWhere('id', 'LIKE', '%' . $request->get('id') . '%');
            });
        }
        if ($request->filled('incorrect_txns')) {
            if($request->incorrect_txns==1){
                $datatable->whereRaw('(balance_in - balance_out + balance_in_refund - balance) != 0');
            }
            else{
                $datatable->havingRaw('(balance_in - balance_out + balance_in_refund - balance) = 0');
            }

        }
        if ($request->filled('fullname_display')) {
            $datatable->where('fullname_display', 'LIKE', '%' . $request->get('fullname_display') . '%');
        }
        if ($request->filled('username')) {
            $datatable->where('username', 'LIKE', '%' . $request->get('username') . '%');
        }

        if ($request->filled('email')) {
            $datatable->where('email', 'LIKE', '%' . $request->get('email') . '%');
        }
        if ($request->filled('fullname')) {
            $datatable->where('fullname', 'LIKE', '%' . $request->get('fullname') . '%');
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

        $datatable = $datatable->get()->map(function($item) use ($request) {

            $txns = Txns::query()
                ->where('user_id',$item->id);
            if ($request->filled('balance_time')){
                $txns = $txns->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('balance_time')));
            }

            $txns = $txns->orderBy('created_at','desc')->first();
            $balance = 0;

            if (isset($txns)){
                $balance = $txns->last_balance;
            }

            $item->last_balance = $balance;

            return $item;
        });

        $data = [
            'data' => $datatable,
        ];

//        return view('admin.minigame.module.withdrawlog.export_excel')->with('data',$data);
        return Excel::download(new ExportData($data,view('admin.user-qtv.excel')), 'Thống kê thành viên đại lý_ ' . time() . '.xlsx');
    }

}
