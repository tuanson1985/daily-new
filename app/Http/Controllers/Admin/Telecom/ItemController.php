<?php

namespace App\Http\Controllers\Admin\Telecom;

use App\Http\Controllers\Controller;
use App\Library\Helpers;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\LogEdit;
use App\Models\Setting;
use App\Models\Telecom;
use App\Models\TelecomValue;
use Carbon\Carbon;
use Html;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Shop;
use App\Models\Shop_Group;
use App\Library\HelperReplicationModule;
use Illuminate\Support\Facades\Auth;


class ItemController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;
    public function __construct(Request $request)
    {

        $this->module='telecom';
        $this->moduleCategory=null;
        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');
        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate','postReplication']]);
        $this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);
        $this->middleware('permission:'. $this->module.'-replication', ['only' => ['postReplication']]);


        $this->page_breadcrumbs[] = [
            'page' => route('admin.'.$this->module.'.index'),
            'title' => __(config('module.'.$this->module.'.title'))
        ];
    }


    public function index(Request $request)
    {

        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);
        if($request->ajax) {
            $datatable= Telecom::query();
            if ($request->filled('group_id')) {
                $datatable->whereHas('groups', function ($query) use ($request) {
                    $query->where('group_id',$request->get('group_id'));
                });
            }

            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', 'LIKE', '%' . $request->get('id') . '%');
                });
            }

            if ($request->filled('title'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
                });
            }
            if ($request->filled('position')) {
                $datatable->where('position',$request->get('position') );
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
                    'title',
                    'image',
                    'key',
                    'ratio',
                    'type_charge',
                    'seri',
                    'order',
                    'gate_id',
                    'note',
                    'status',
                    'created_at',
                    'action'
                ])
                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->editColumn('shop_id', function($data) {
                    $temp= '';
                    if(isset($data->shop_id)){
                        $temp .=  $data->shop->domain;
                    }
                    return $temp;
                })
                ->addColumn('action', function($row) {
                    $temp= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.="<a href=\"".route('admin.'.$this->module.'.set-value',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary  setvalue_toggle\" title=\"Mệnh giá\"><i class=\"la la-th-list\"></i></a>";
                    $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->toJson();
        }
        if( $this->moduleCategory==null){
            $dataCategory=null;
        }
        else{

        }
        $shop_access_user = Auth::user()->shop_access;
        $shop = Shop::orderBy('id','desc');
        if(isset($shop_access_user) && $shop_access_user !== "all"){
            $shop_access_user = json_decode($shop_access_user);
            $shop = $shop->whereIn('id',$shop_access_user);
        }
        $shop = $shop->get();
        $shop_id_shop_access = json_decode(Auth::user()->shop_access);
        $shop_groups = Shop_Group::with(['shop' => function($query) use ($shop_id_shop_access){
            if(!empty($shop_id_shop_access)){
                $query->whereIn('id',$shop_id_shop_access);
            }
            if(session()->has('shop_id')){
                $query->where('id','!=',session()->get('shop_id'));
            }
        }])
        ->orderBy('id','asc')
        ->get();
        foreach($shop_groups as $item){
            $children = null;
            if(isset($item->shop) && count($item->shop)){
                foreach($item->shop as $item_shop)
                $children[] = [
                    "id"=>$item_shop->id."",
                    "parent"=> $item_shop->group_id."",
                    "text"=>htmlentities($item_shop->domain)."",
                    "state"=>[
                        'opened'=>true
                    ],
                ];
            }
            $shop_replication [] = [
                "id"=> "group_id_".$item->id."",
                "text"=>htmlentities($item->title)."",
                "state"=>[
                    'opened'=>true
                ],
                "children"=>$children
            ];
        }
        $shop_replication=json_encode($shop_replication);
        return view('admin.'.$this->module.'.item.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('shop', $shop)
            ->with('shop_replication', $shop_replication)
            ->with('dataCategory', $dataCategory);
    }


    public function create(Request $request)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];

        if( $this->moduleCategory==null){
            $dataCategory=null;
        }
        else{
            //$dataCategory = Group::where('module', '=',  $this->moduleCategory)->orderBy('order','asc')->get();
        }

        $dataCategory = null;
        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.'.$this->module.'.item.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('dataCategory', $dataCategory);

    }


    public function store(Request $request)
    {
        $params=$request->params;

        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
        ]);
        if(empty(session('shop_id')) || session('shop_id') == null){
            return redirect()->back()->withErrors('Bạn chưa chọn shop cấu hình');
        }
        $input=$request->all();
        $input['module']=$this->module;
        $input['type_charge']=0;
        $input['shop_id'] = session('shop_id');


        //xử lý params
        if($request->filled('params')){
            //check value param ở đây nếu cần //Example:  $params['demo']='Value demo edited'
            $params=$request->params;
            $input['params'] =$params;
        }
        $data=Telecom::create($input);

        //set category
        if( isset($input['group_id'] ) &&  $input['group_id']!=0){
            $data->groups()->attach($input['group_id']);
        }
        ActivityLog::add($request, 'Tạo mới thành công '.$this->module.' #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Thêm mới thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }
    }


    public function show(Request $request,$id)
    {
        //$data = Group::findOrFail($id);
        //ActivityLog::add($request, 'Show '.$this->module.' #'.$data->id);
        //return view('admin.module.item.show', compact('datatable'));
    }

    public function edit(Request $request,$id)
    {


        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];
        $data = Telecom::findOrFail($id);
        if( $this->moduleCategory==null){
            $dataCategory=null;
        }
        else{
            //$dataCategory = Group::where('module', '=',  $this->moduleCategory)->orderBy('order','asc')->get();
        }
        if($data->shop_id){
            $shop = Shop::findOrFail($data->shop_id);
            session()->put('shop_id', $shop->id);
            session()->put('shop_name', $shop->domain);
        }

        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        return view('admin.'.$this->module.'.item.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('dataCategory', $dataCategory);

    }

    public function update(Request $request,$id)
    {
        if(empty(session('shop_id')) || session('shop_id') == null){
            return redirect()->back()->withErrors('Bạn chưa chọn shop cấu hình');
        }
        $data =  Telecom::findOrFail($id);
        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
        ]);

        $input=$request->all();
        $input['module']=$this->module;
        $input['type_charge']=0;
        $input['shop_id'] = session('shop_id');
        //xử lý params
        if($request->filled('params')){
            //check value param ở đây nếu cần //Example:  $params['demo']='Value demo edited'

            $params=$request->params;
            $input['params'] =$params;
        }

        $data->update($input);
        ActivityLog::add($request, 'Cập nhật thành công '.$this->module.' #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Cập nhật thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
        }
    }

    public function destroy(Request $request)
    {

        $input=explode(',',$request->id);

        $telecoms = Telecom::whereIn('id',$input)->with('shop')->get();

        if (isset($telecoms) && count($telecoms)){
            // lấy thông tin IP và user_angent người dùng
            $ip = $request->getClientIp();
            $user_agent = $request->userAgent();
            $message = "Thời gian: <b>" . Carbon::now()->format('d-m-Y H:i:s') . "</b>";
            $message .= "\n";
            foreach ($telecoms as $telecom){
                $message .= "Tài khoản qtv <b>" . Auth::user()->username . "</b> xóa thẻ " . $telecom->id . " - <b>" . $telecom->title . "</b> trên điểm bán <b>" . $telecom->shop->domain . "</b>";
            }

            $message .= "\n";
            $message .= "IP: <b>" . $ip . "</b>";
            $message .= "\n";
            $message .= "User_agent: <b>" . $user_agent . "</b>";
            Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_noty_telecom'));
        }


        Telecom::whereIn('id',$input)->delete();
        ActivityLog::add($request, 'Xóa thành công '.$this->module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Xóa thành công !'));
    }



    public function getSetValue(Request $request,$id)
    {
        $data=Telecom::findOrFail($id);
        $shop = Shop::findOrFail($data->shop_id);
        $data_telecom_value=TelecomValue::where('telecom_id',$data->id)->get();
        ActivityLog::add($request, 'Vào form cập nhật mệnh giá nhà mạng '.$this->module.' #'.$data->id);
        return view('admin.'.$this->module.'.item.set-value', compact('data','data_telecom_value','shop'));
    }



    public function postSetValue(Request $request,$id)
    {
        //check password2
        if(!\Hash::check($request->password2,\Auth::user()->password2)){
            session()->put('fail_password2',  session()->get('fail_password2')+1);
            DB::rollBack();
            return redirect()->back()->withErrors(__('Mật khẩu cấp 2 không đúng'))->withInput();
        }
        else{
            session()->put('fail_password2', 0);
        }


        $data=Telecom::findOrFail($id);
        TelecomValue::where('telecom_id',$data->id)->delete();
        for ($i=0;$i<count($request->amount);$i++){
            if($request->amount[$i]!='' && $request->ratio_true_amount[$i]!='' && $request->ratio_false_amount[$i]!='' && $request->agency_ratio_true_amount[$i]!=''&& $request->agency_ratio_false_amount[$i]!=''&& $request->status[$i]!='' )
            {
                $input=[
                    'shop_id'=>$data->shop_id,
                    'telecom_id'=>$data->id,
                    'telecom_key'=>$data->key,
                    'amount'=>$request->amount[$i],
                    'ratio_true_amount'=>$request->ratio_true_amount[$i],
                    'ratio_false_amount'=>$request->ratio_false_amount[$i],
                    'agency_ratio_true_amount'=>$request->agency_ratio_true_amount[$i],
                    'agency_ratio_false_amount'=>$request->agency_ratio_false_amount[$i],
                    'type_charge'=>0,
                    'status'=>$request->status[$i],
                ];
                TelecomValue::create($input);
            }

        }

        ActivityLog::add($request, 'Cập nhật thành công nhật mệnh giá nhà mạng '.$this->module.' #'.$data->id);

        return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Câp nhật thành công !'));

    }

    public function postReplication(Request $request){
        $this->validate($request,[
            'shop_id'=>'required',
        ],[
            'shop_id.required' => __('Bạn chưa chọn danh sách shop cần nhân bản.'),
        ]);
        if(!session()->has('shop_id')){
            return redirect()->back()->withErrors(__('Không tìm thấy shop lấy dữ liệu yêu cầu.'))->withInput();
        };
        $shop_id_replication = session()->get('shop_id');
        $shop_id = explode(',',$request->shop_id);

        // loại bỏ các id của nhóm shop
        $shop_id = $this->unset_key_in_array($shop_id,'group_id_');

        // lấy thông tin các shop được gửi lên

        $shop_id_shop_access = json_decode(Auth::user()->shop_access);
        $shop = Shop::orderBy('id','asc');
        if(isset($shop_id_shop_access) && $shop_id_shop_access != "all"){
            $shop_id_shop_access_search = array_values(array_intersect($shop_id_shop_access,$shop_id));
            $shop = $shop->whereIn('id',$shop_id_shop_access_search);
        }
        else{
            $shop = $shop->whereIn('id',$shop_id);
        }
        $shop = $shop->get();


        // lấy thông tin nhà mạng cần update sang các shop khác
        $telecom_replication = Telecom::where('shop_id',$shop_id_replication)->get();

        if(empty($telecom_replication)){
            return redirect()->back()->withErrors(__('Không tìm thấy dữ liệu yêu cầu của shop này.'))->withInput();
        }

        foreach($shop as $key => $shop_item){
            // xóa tât cả các nhà mạng đã được cấu hình trước đó
            Telecom::where('shop_id',$shop_item->id)->delete();
            // xóa tất cả các mệnh giá đã được cấu hình trước đó
            TelecomValue::where('shop_id',$shop_item->id)->delete();

            // bắt đầu quá trình convert
            HelperReplicationModule::__moduleCharge($shop_item->id,$shop_id_replication,$telecom_replication);

        }

        ActivityLog::add($request, 'Nhân bản thành công cấu hình nạp thẻ tự động từ shop '.$shop_id_replication.' sang các shop #'.json_encode($shop_id));
        return redirect()->back()->with('success',__('Câp nhật thành công !'));
    }

    public function unset_key_in_array(array $array, string $string){
        foreach($array as $key => $item){
            if( strpos($item, $string) !== false){
                unset($array[$key]);
            }
        }
        return array_values($array);
    }

}
