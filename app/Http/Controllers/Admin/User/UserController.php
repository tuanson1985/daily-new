<?php

namespace App\Http\Controllers\Admin\User;

use App\Exports\ExportData;
use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Library\Helpers;
use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\Notification;
use App\Models\Otp;
use App\Models\SessionTracker;
use App\Models\Txns;
use App\Models\User;
use App\Models\Shop;
use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use App\Library\Files;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Auth;


class UserController extends Controller
{

    protected $page_breadcrumbs;
    //Thành viên
    protected $account_type= 2;

    public function __construct()
    {
        //set permission to function
        $this->middleware('permission:user-list');
        $this->middleware('permission:user-create', ['only' => ['create','store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
        $this->middleware('permission:plus-minus-money', ['only' => ['getMoney','postMoney','getUserToMoney']]);
        $this->middleware('permission:plus-minus-vp', ['only' => ['getVP','postVP']]);


        $this->page_breadcrumbs[] = [
            'page' => route('admin.user.index'),
            'title' => __("Thành viên")
        ];
    }

    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập danh sách user');
        if($request->ajax) {

            $datatable= User::with(['roles'=>function($query){
                $query->select(['id','title','name']);
            }])->with('shop')
                ->where("account_type",$this->account_type)
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
            if ($request->filled('type_information')) {
                $datatable->where('type_information',$request->get('type_information') );
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
            if ($request->filled('is_idol')) {
                $datatable->where('is_idol',$request->get('is_idol') );
            }

            if ($request->filled('started_at')) {
                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }

            return \datatables()->eloquent($datatable)
                ->only([
                    'id',
                    'username',
                    'fullname_display',
                    'email',
                    'is_idol',
                    'shop_id',
                    'fullname',
                    'balance',
                    'balance_in',
                    'balance_out',
                    'balance_in_refund',
                    'phone',
                    'image',
                    'cover',
                    'status',
                    'type_information',
                    'created_at',
                    'action',
                    'utm_source',
                    'utm_campain',
                    'balance_time',
                ])


                ->orderColumn('balance', function ($query, $order) {
                    $query->orderBy('balance', $order);
                })

                ->editColumn('balance', function($row) {
                    return intval($row->balance);
                })
                ->editColumn('balance_in', function($row) {
                    return intval($row->balance_in);
                })
                ->editColumn('balance_time', function($row)  use ($request){

                    $balance = $row->balance;
                    if ($request->filled('balance_time')){
                        $txns = Txns::query()
                            ->with('user')
                            ->where('user_id',$row->id);
                        $txns = $txns->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('balance_time')));

                        $txns = $txns->orderBy('created_at','desc')->first();

                        if (isset($txns)){
                            $balance = number_format($txns->last_balance);
                        }
                    }

                    return $balance;
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
                ->editColumn('shop_id', function($data) {
                    $temp= '';
                    if(isset($data->shop_id) && isset($data->shop->domain)){
                        $temp .=  $data->shop->domain;
                    }
                    return $temp;
                })
                ->editColumn('username', function($row) {
                    $temp = '';
                    if( auth()->user()->can('view-profile')){
                        $temp .= "<a href=\"#\"  class=\"load-modal\" rel=\"".route('admin.view-profile',["username" => "$row->username","id" => "$row->id"])."\">".$row->username."</a>";
                    }
                    else{
                        $temp .= $row->username;
                    }
                    return $temp;
                })
                ->editColumn('image', function($row) {
                    if(gettype(json_decode($row->image)) == 'object'){
                        return Files::media(get_object_vars(json_decode($row->image))['anh_crop']);
                    }
                    else{
                        return null;
                    }
                })
                ->addColumn('cover', function($row) {
                    if(gettype(json_decode($row->cover)) == 'object' && isset(get_object_vars(json_decode($row->cover))['image_Coverx342x220'])){
                        return Files::media(get_object_vars(json_decode($row->cover))['image_Coverx342x220']);
                    }
                    else{
                        return null;
                    }
                })
                ->addColumn('action', function($row) {
                    $temp = '';
                    if(auth()->user()->hasRole('admin') || auth()->user()->can('plus-minus-vp')){
                        $temp .= "<a href=\"" .route('admin.get_vp',['mode'=>1,'field'=>'username','value'=>$row->username,'shop_id'=>$row->shop_id])."\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary\"  title=\"Cộng vật phẩm\"><i class=\"flaticon-add-circular-button\"></i></a>";
                    }
                    if(auth()->user()->hasRole('admin') || auth()->user()->can('plus-minus-money')){
                        $temp .= "<a href=\"" .route('admin.get_money',['mode'=>1,'field'=>'username','value'=>$row->username,'id'=>$row->id,'shop_id'=>$row->shop_id])."\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary\"  title=\"Cộng tiền\"><i class=\"la la-plus\"></i></a>";
                        $temp.= "<a href=\"" .route('admin.get_money',['mode'=>0,'field'=>'username','value'=>$row->username,'id'=>$row->id,'shop_id'=>$row->shop_id])."\"  class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary'  title=\"Trừ tiền\"><i class=\"la la-minus\"></i></a>";
                    }

                    if(auth()->user()->can('user-edit')){
                        $temp.= "<a href=\"".route('admin.user.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";

                    };
                    if ($row->status == 0) {
                        if(auth()->user()->can('user-unlock')){
                            $temp .= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger unlock_toggle' data-toggle=\"modal\" data-target=\"#unlockModal\" class=\"unlock_toggle\" title=\"Mở khóa\"><i class=\"la la-unlock-alt\"></i></a>";
                        };
                    } else {
                        if(auth()->user()->can('user-unlock')){
                            $temp .= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger lock_toggle' data-toggle=\"modal\" data-target=\"#lockModal\" class=\"lock_toggle\" title=\"Khóa\"><i class=\"la la-lock\"></i></a>";

                        };
                    }

                    if(auth()->user()->can('user-edit')){
                        $temp.= "<a href=\"".route('admin.user.show',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger \" title=\"Xem chi tiết\"><i class=\"la la-eye\"></i></a>";
                    }

                    if(config('module.user.need_set_permission')){
                        $temp.= "<a href=\"".route('admin.user.set_permission',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger \" title=\"Phân quyền\"><i class=\"la la-sitemap\"></i></a>";
                    }

                    if(auth()->user()->can('user-delete')){
                        $temp.= "<a  rel=\"".$row->id."\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    };

                    return $temp;
                })
                ->toJson();
        }

        $roles=Role::orderBy('order','asc')->get();
        return view('admin.user.index')->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('roles',$roles);
    }

    public function create(Request $request)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];

        //Dịch vụ ngọc
        $serviceNrogem = Item::query()
            ->select('id','module','status','params','title')
            ->where('module', config('module.service.key'))
            ->where('idkey','nrogem')
            ->where('status', '=', 1)
            ->first();
        //Dịch vụ xu
        $serviceNinjaxu = Item::query()
            ->select('id','module','status','params','title')
            ->where('module', config('module.service.key'))
            ->where('idkey', 'ninjaxu')
            ->where('status', '=', 1)
            ->first();

        //Dịch vụ vàng
        $serviceNrocoin = Item::query()
            ->select('id','module','status','params','title')
            ->where('module', config('module.service.key'))
            ->where('idkey', 'nrocoin')
            ->where('status', '=', 1)
            ->first();

        ActivityLog::add($request, 'Vào form create user');

        return view('admin.user.create_edit')
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('serviceNinjaxu', $serviceNinjaxu)
            ->with('serviceNrocoin', $serviceNrocoin)
            ->with('serviceNrogem', $serviceNrogem);
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            //'username' => 'required|unique:users,username',
            'email' => 'required|unique:users,email',
            //'phone' => 'required|unique:users',
            'username' => 'required|min:3|max:16|unique:users|regex:/^([A-Za-z0-9])+$/i',
            'password' => 'required|min:6|max:32',
            'password2' => 'required|min:6|max:32',
            //'password_confirmation' => 'required|same:password',
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
            //'password2_confirmation.same' => 'Mật khẩu xác nhận không đúng.',

            'email.required' => 'Vui lòng nhập trường này',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.unique' => 'Địa chỉ email đã được sử dụng.',
            //'phone.unique'	=> 'Số điện thoại đã được sử dụng.',
            //'created_at.required' => 'Vui lòng nhập ngày tạo',
            //'created_at.date_format' => 'Vui lòng nhập đúng định dạng ngày tháng (dd/mm/YYYY H:i:s)',
        ]);

        $input=$request->all();
        $input = $request->except('balance');

        if ($request->filled('password')) {
            $input['password'] = \Hash::make($request->password);

        }
        if ($request->filled('password2')) {
            $input['password2'] = \Hash::make($request->password2);

        }

        $payment_limit = (int)str_replace(array(' ', ','), '', $request->payment_limit);
        if ($payment_limit <= 0) {
            $payment_limit = config('module.service.payment_limit');
        }
        $input['payment_limit'] = $payment_limit;

        $input['account_type'] = 2;
        $input['created_by'] = Auth::user()->id;
        $input['balance'] = 0;

        $user = User::create($input);

        //cập nhật chiết khấu mua nrogem
        if ($request->is_agency_buygem == 1) {
            $user->buygem_discount= json_encode($request->discount,JSON_UNESCAPED_UNICODE);
            $user->save();

        } else {
            $user->buygem_discount="";
            $user->save();
        }

        if ($request->is_agency_ninjaxu == 1) {
            $user->ninjaxu_discount= json_encode($request->ninjaxu_discount,JSON_UNESCAPED_UNICODE);
            $user->save();

        } else {
            $user->ninjaxu_discount="";
            $user->save();
        }

        //cập nhật chiết khấu mua nrocoin
        if ($request->is_agency_nrocoin == 1) {
            $user->nrocoin_discount=json_encode($request->nrocoin_discount,JSON_UNESCAPED_UNICODE);
            $user->save();

        } else {
            $user->nrocoin_discount="";
            $user->save();
        }

        //update roles of user

        ActivityLog::add($request, 'Cập nhật thành công user #'.$user->id);

        if ($request->filled('submit-close')) {
            return redirect()->route('admin.user.index')->with('success', __('Thêm mới thành công !'));
        } else {
            return redirect()->back()->with('success', __('Thêm mới thành công !'));
        }
    }

    public function show(Request $request,$id)
    {
        $data = User::findOrFail($id);
        ActivityLog::add($request, 'Show user #'.$data->id);
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Xem chi tiết")
        ];
        return view('admin.user.show')
            ->with('data', $data)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }
    public function edit(Request $request,$id)
    {

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];

        $data = User::with('roles')->where("account_type",$this->account_type)->findOrFail($id);

        if(!auth()->user()->hasRole('admin') && $data->hasRole('admin')){
            return redirect()->back()->withErrors(__('Bạn không có quyền tạo hoặc chỉnh sửa tài khoản Admin'))->withInput();
        }

        $roles=Role::orderBy('order','asc')->get();

        $otp = Otp::where('email','LIKE', '%' . $data->email . '%')->first();

        //Dịch vụ ngọc
        $serviceNrogem = Item::query()
            ->select('id','module','status','params','title')
            ->where('module', config('module.service.key'))
            ->where('idkey','nrogem')
            ->where('status', '=', 1)
            ->first();
        //Dịch vụ xu
        $serviceNinjaxu = Item::query()
            ->select('id','module','status','params','title')
            ->where('module', config('module.service.key'))
            ->where('idkey', 'ninjaxu')
            ->where('status', '=', 1)
            ->first();

        //Dịch vụ vàng
        $serviceNrocoin = Item::query()
            ->select('id','module','status','params','title')
            ->where('module', config('module.service.key'))
            ->where('idkey', 'nrocoin')
            ->where('status', '=', 1)
            ->first();

        ActivityLog::add($request, 'Vào form edit user #'.$data->id);
        return view('admin.user.create_edit')
            ->with('data', $data)
            ->with('otp', $otp)
            ->with('serviceNinjaxu', $serviceNinjaxu)
            ->with('serviceNrocoin', $serviceNrocoin)
            ->with('serviceNrogem', $serviceNrogem)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('roles', $roles);
    }

    public function update(Request $request,$id)
    {

        $user = User::where("account_type",$this->account_type)->findOrFail($id);
        if(!auth()->user()->hasRole('admin') && $user->hasRole('admin')){
            return redirect()->back()->withErrors(__('Bạn không có quyền tạo hoặc chỉnh sửa tài khoản Admin'))->withInput();
        }

        $input = $request->except('username', 'password','password2', 'account_type', 'balance');

        if (Auth::user()->can(['edit-password-user'])){
            if($request->filled('password'))
            {

                $input['password'] = \Hash::make($request->password);

            }
            if($request->filled('password2'))
            {
                $input['password2'] = \Hash::make($request->password2);
            }

            if($request->filled('password') || $request->filled('password2')){
                $ip = $request->getClientIp();
                $user_agent = $request->userAgent();
                $message = "Thời gian: <b>" . Carbon::now()->format('d-m-Y H:i:s') . "</b>";
                $message .= "\n";
                $message .= "<b>".Auth::user()->username."</b> đổi mật khẩu thành viên <b>".$user->username."</b>";
                $message .= "\n";
                $message .= "IP: <b>" . $ip . "</b>";
                $message .= "\n";
                $message .= "User_agent: <b>" . $user_agent . "</b>";
                Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_change_current_password'));
            }
        }

        $user = User::findOrFail($id);

        $payment_limit = (int)str_replace(array(' ', ','), '', $request->payment_limit);
        if ($payment_limit <= 0) {
            $payment_limit = config('module.service.payment_limit');
        }
        $input['payment_limit'] = $payment_limit;

        $user->update($input);

        //cập nhật chiết khấu mua nrogem
        if ($request->is_agency_buygem == 1) {
            $user->buygem_discount= json_encode($request->discount,JSON_UNESCAPED_UNICODE);
            $user->save();

        } else {
            $user->buygem_discount="";
            $user->save();
        }

        if ($request->is_agency_ninjaxu == 1) {
            $user->ninjaxu_discount= json_encode($request->ninjaxu_discount,JSON_UNESCAPED_UNICODE);
            $user->save();

        } else {
            $user->ninjaxu_discount="";
            $user->save();
        }

        //cập nhật chiết khấu mua nrocoin
        if ($request->is_agency_nrocoin == 1) {
            $user->nrocoin_discount=json_encode($request->nrocoin_discount,JSON_UNESCAPED_UNICODE);
            $user->save();

        } else {
            $user->nrocoin_discount="";
            $user->save();
        }

//        $data->syncRoles(isset($request->role_ids) ? $request->role_ids : []);

        ActivityLog::add($request, 'Cập nhật thành công user #'.$user->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.user.index')->with('success',__('Cập nhật thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
        }
    }

    public function destroy(Request $request)
    {

        $input=explode(',',$request->id);

        $currentUserRole= Auth::user()->hasRole('admin');

        $data=User::with(['roles'=>function($query){
        }])->whereIn('id',$input)
            ->where("account_type",$this->account_type)
            ->get();

        foreach ($data as $aUser){

            //nếu không phải admin thì không cho cập nhật,xóa user có quyền admin
            if($currentUserRole  &&  !$aUser->hasRole('admin')){
                $aUser->update([
                    'status'=>0
                ]);
            }
        }
        ActivityLog::add($request, 'Xóa thành công user #'.json_encode($input));

        return redirect()->back()->with('success',__('Xóa thành công !'));
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
                'redirect'=>''
            ]);
        }


        $data= User::whereIn('id',$input)->where("account_type",$this->account_type)->update([
            $field=>$value
        ]);

        ActivityLog::add($request, 'Cập nhật field thành công user '.json_encode($whitelist).' #'.json_encode($input));

        return response()->json([
            'success'=>true,
            'message'=>__('Cập nhật thành công !'),
            'redirect'=>''
        ]);

    }
    public function lock(Request $request)
    {


        $input = $request->id;
        $data = User::where("account_type",$this->account_type)->findOrFail($input);
        $data->update([
            'status' => 0,
            'locker_by' => auth()->user()->username
        ]);
        SessionTracker::endSessionByUser($data->id);

        ActivityLog::add($request, 'Khóa thành công user #'.$data->id);
        return redirect()->back()->with('success', trans('Khóa tài khoản thành công'));
    }
    public function unlock(Request $request)
    {

        $input = $request->id;
        $data = User::where("account_type",$this->account_type)->findOrFail($input);
        $data->update([
            'status' => 1,
        ]);

        ActivityLog::add($request, 'Mở khóa thành công user #'.$data->id);
        return redirect()->back()->with('success', 'Mở khóa tài khoản thành công');
    }
    public function view_profile(Request $request){


        $username=$request->username;
        $data=User::where('username',$username)->where("account_type",$this->account_type)->where('status',1)->firstOrFail();

        //if(Auth::guard()->user()->username !='admin' && $user->username=='admin' ){
        //    return redirect()->back()->withErrors("Bạn không có quyền set tài khoản Admin")->withInput();
        //}

        ActivityLog::add($request, 'Xem profile user #'.$data->id);
        return view('admin.user.view-profile')->with('user',$data);
    }


    public function set_permission(Request $request,$id){

        if(!config('module.user.need_set_permission')){
            return redirect()->back()->withErrors(__('Chức năng chưa được kích hoạt'));
        }

        $this->page_breadcrumbs[] = [
            'page' => '#',
            'title' => __("Phân quyền truy cập")
        ];


        $data = User::where('account_type',1)->findOrFail($id);

        //permisson info
        $permissions = Permission::orderBy('order', 'asc')->get();
        $permissionsSelected = $data->permissions()->pluck('id')->toArray();
        $array = array();
        foreach ($permissions as $permission) {
            if($permission->parent_id==0 || $permission->parent_id.""==""){
                $permission->parent_id="#";
            }
            if($data->hasPermissionTo($permission)){
                $hasPermission=true;
            }
            else{
                $hasPermission=false;
            }
            $array[]=[
                "id"=>$permission->id."",
                "parent"=>$permission->parent_id."",
                "text"=>htmlentities($permission->title)."",
                "state"=>[
                    'opened'=>true
                ],
            ];

        }
        $permissionsJson=json_encode($array);


        ActivityLog::add($request, 'Vào form cập nhật permission user #'.$data->id);

        return view('admin.user.set_permission')
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('permissionsJson', $permissionsJson)
            ->with('permissionsSelected', $permissionsSelected);
    }
    public function post_set_permission(Request $request,$id){

        if(!config('module.user.need_set_permission')){
            return redirect()->back()->withErrors(__('Chức năng chưa được kích hoạt'));
        }

        $data = User::where('account_type',1)->findOrFail($id);
        $data->permissions()->sync(isset($request->permission_ids) ? explode(",",$request->permission_ids) : []);

        ActivityLog::add($request, 'Cập nhật permission thành công user #'.$data->id);
        return redirect()->back()->with('success',__('Phân quyền truy cập thành công'));

    }

    public function exportExcel(Request $request){
        $export = new UserExport($request);
        return \Excel::download($export, 'Thống kê ctv_ ' . time() . '.xlsx');
    }
}
