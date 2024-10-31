<?php

namespace App\Http\Controllers\Admin\StoreTelecom;

use App\Http\Controllers\Controller;
use App\Library\Helpers;
use App\Models\LogEdit;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\StoreTelecom;
use App\Models\StoreTelecomValue;
use Carbon\Carbon;
use Html;
use Illuminate\Support\Facades\DB;
use App\Models\Shop;
use App\Models\Shop_Group;
use App\Library\HelperReplicationModule;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
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

        $this->module='store-telecom';
        $this->moduleCategory=null;
        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');
        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate']]);
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
            $datatable= StoreTelecom::query();

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
                $datatable->where('shop_id', $request->get('shop_id'));
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
        ->with('shop', $shop)
        ->with('shop_replication', $shop_replication)
        ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $params=$request->params;

        $this->validate($request,[
            'title'=>'required',
            'key' => 'required',
            // 'image' => 'required'
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
            'key.required' => __('Vui lòng nhập unique nhà mạng'),
            // 'image.required' => __('Vui lòng upload ảnh'),
        ]);
        if(empty(session('shop_id')) || session('shop_id') == null){
            return redirect()->back()->withErrors('Bạn chưa chọn shop cấu hình');
        }
        $checkTelecom = StoreTelecom::where('key',$request->key)->where('shop_id',session('shop_id'))->first();
        if($checkTelecom){
            return redirect()->back()->withErrors('Key nhà mạng đã tồn tại.');
        }
        $input=$request->all();
        $input['module']=$this->module;
        $input['shop_id'] = session('shop_id');

        //xử lý params
        if($request->filled('params')){
            $params=$request->params;
            $input['params'] =$params;
        }
        $data=StoreTelecom::create($input);

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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];
        $data = StoreTelecom::findOrFail($id);
        if($data->shop_id){
            $shop = Shop::findOrFail($data->shop_id);
            session()->put('shop_id', $shop->id);
            session()->put('shop_name', $shop->domain);
        }
        if( $this->moduleCategory==null){
            $dataCategory=null;
        }
        else{
            //$dataCategory = Group::where('module', '=',  $this->moduleCategory)->orderBy('order','asc')->get();
        }

        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        return view('admin.'.$this->module.'.item.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('dataCategory', $dataCategory);

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
        $data =  StoreTelecom::findOrFail($id);
        $this->validate($request,[
            'title'=>'required',
            'key' => 'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
            'key.required' => __('Vui lòng nhập nhà mạng'),
        ]);
        if(empty(session('shop_id')) || session('shop_id') == null){
            return redirect()->back()->withErrors('Bạn chưa chọn shop cấu hình');
        }
        $input=$request->all();
        $input['module']=$this->module;
        $input['shop_id'] = session('shop_id');
        //xử lý params
        if($request->filled('params')){
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $input=explode(',',$request->id);
        $data =  StoreTelecom::where("id",$input)->first();
        $data->status = 0;
        $data->save();
        ActivityLog::add($request, 'Xóa thành công '.$this->module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Xóa thành công !'));

    }

    public function SetValue(Request $request, $id){
        $data=StoreTelecom::findOrFail($id);
        $data_telecom_value=StoreTelecomValue::where('telecom_id',$data->id)->get();
        $shop = Shop::findOrFail($data->shop_id);
        ActivityLog::add($request, 'Vào form cập nhật mệnh giá nhà mạng '.$this->module.' #'.$data->id);
        return view('admin.'.$this->module.'.item.set-value', compact('data','data_telecom_value','shop'));
    }

    public function postSetValue(Request $request,$id){
        if(!\Hash::check($request->password2,\Auth::user()->password2)){
            session()->put('fail_password2',  session()->get('fail_password2')+1);
            DB::rollBack();
            return redirect()->back()->withErrors(__('Mật khẩu cấp 2 không đúng'))->withInput();
        }
        else{
            session()->put('fail_password2', 0);
        }
        $data=StoreTelecom::findOrFail($id);
        StoreTelecomValue::where('telecom_id',$data->id)->delete();
        for ($i=0;$i<count($request->amount);$i++){
            if($request->amount[$i]!='' && $request->ratio_agency[$i]!='' && $request->ratio_agency[$i]!='' && $request->status[$i]!='' )
            {
                $input=[
                    'shop_id'=>$data->shop_id,
                    'telecom_id'=>$data->id,
                    'telecom_key'=>$data->key,
                    'amount'=>$request->amount[$i],
                    'ratio_default'=>$request->ratio_default[$i],
                    'ratio_agency'=>$request->ratio_agency[$i],
                    'status'=>$request->status[$i],
                ];
                StoreTelecomValue::create($input);
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
        $store_telecom_replication = StoreTelecom::where('shop_id',$shop_id_replication)->get();

        if(empty($store_telecom_replication)){
            return redirect()->back()->withErrors(__('Không tìm thấy dữ liệu yêu cầu của shop này.'))->withInput();
        }

        foreach($shop as $key => $shop_item){
            // xóa tât cả các nhà mạng đã được cấu hình trước đó
            StoreTelecom::where('shop_id',$shop_item->id)->delete();
            // xóa tất cả các mệnh giá đã được cấu hình trước đó
            StoreTelecomValue::where('shop_id',$shop_item->id)->delete();
            // bắt đầu quá trình convert
            HelperReplicationModule::__moduleStoreCard($shop_item->id,$shop_id_replication,$store_telecom_replication);
        }
        ActivityLog::add($request, 'Nhân bản thành công cấu hình mua thẻ từ shop '.$shop_id_replication.' sang các shop #'.json_encode($shop_id));
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

    public function postSetting(Request $request)
    {

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $shop = Shop::where('id',session('shop_id'))->first();
        $params_before = null;
        $params_after = null;
        // lấy thông tin IP và user_angent người dùng
        // lấy thông tin IP và user_angent người dùng
        $ip = $request->getClientIp();
        $user_agent = $request->userAgent();
        $message = "Thời gian: <b>" . Carbon::now()->format('d-m-Y H:i:s') . "</b>";
        $message .= "\n";
        $message .= Auth::user()->username." Thay đổi setting điểm bán: ".$shop->domain;

        $old_sys_address = Setting::getSettingShop('sys_address',null,$shop->id);

        $params_before['sys_address'] = $old_sys_address??'';
        $params_after['sys_address'] = $request->get('sys_address')??'';

        if ($request->filled('sys_address')){

            if ($request->get('sys_address') != $old_sys_address){
                $message .= "\n";
                $message .= "Thay đổi sys_address - Địa chỉ";
            }
        }



        $old_sys_atm_percent = Setting::getSettingShop('sys_atm_percent',null,$shop->id);
        $params_before['sys_atm_percent'] = $old_sys_atm_percent??'';
        $params_after['sys_atm_percent'] = $request->get('sys_atm_percent')??'';
        if ($request->filled('sys_atm_percent')){
            if ($request->get('sys_atm_percent') != $old_sys_atm_percent){
                $message .= "\n";
                $message .= "Thay đổi sys_atm_percent - % ATM";
            }
        }

        $old_sys_avatar = Setting::getSettingShop('sys_avatar',null,$shop->id);
        $params_before['sys_avatar'] = $old_sys_avatar??'';
        $params_after['sys_avatar'] = $request->get('sys_avatar')??'';
        if ($request->filled('sys_avatar')){
            if ($request->get('sys_avatar') != $old_sys_avatar){
                $message .= "\n";
                $message .= "Thay đổi sys_avatar - ảnh Avatar";
            }
        }

        $old_sys_charge_content = Setting::getSettingShop('sys_charge_content',null,$shop->id);
        $params_before['sys_charge_content'] = $old_sys_charge_content??'';
        $params_after['sys_charge_content'] = $request->get('sys_charge_content')??'';
        if ($request->filled('sys_charge_content')){
            if ($request->get('sys_charge_content') != $old_sys_charge_content){
                $message .= "\n";
                $message .= "Thay đổi sys_charge_content - Nội dung nạp thẻ";
            }
        }

        $old_sys_description = Setting::getSettingShop('sys_description',null,$shop->id);
        $params_before['sys_description'] = $old_sys_description??'';
        $params_after['sys_description'] = $request->get('sys_description')??'';
        if ($request->filled('sys_description')){
            if ($request->get('sys_description') != $old_sys_description){
                $message .= "\n";
                $message .= "Thay đổi sys_description - Mô tả";
            }
        }

        $old_sys_fanpage = Setting::getSettingShop('sys_fanpage',null,$shop->id);
        $params_before['sys_fanpage'] = $old_sys_fanpage??'';
        $params_after['sys_fanpage'] = $request->get('sys_fanpage')??'';
        if ($request->filled('sys_fanpage')){
            if ($request->get('sys_fanpage') != $old_sys_fanpage){
                $message .= "\n";
                $message .= "Thay đổi sys_fanpage - link fanpage";
            }
        }

        $old_sys_favicon = Setting::getSettingShop('sys_favicon',null,$shop->id);
        $params_before['sys_favicon'] = $old_sys_favicon??'';
        $params_after['sys_favicon'] = $request->get('sys_favicon')??'';
        if ($request->filled('sys_favicon')){
            if ($request->get('sys_favicon') != $old_sys_favicon){
                $message .= "\n";
                $message .= "Thay đổi sys_favicon - Ảnh favicon";
            }
        }

        $old_sys_footer = Setting::getSettingShop('sys_footer',null,$shop->id);
        $params_before['sys_footer'] = $old_sys_footer??'';
        $params_after['sys_footer'] = $request->get('sys_footer')??'';
        if ($request->filled('sys_footer')){
            if ($request->get('sys_footer') != $old_sys_footer){
                $message .= "\n";
                $message .= "Thay đổi sys_footer - Footer điểm bán";
            }
        }

        $old_sys_google_analytics = Setting::getSettingShop('sys_google_analytics',null,$shop->id);
        $params_before['sys_google_analytics'] = $old_sys_google_analytics??'';
        $params_after['sys_google_analytics'] = $request->get('sys_google_analytics')??'';
        if ($request->filled('sys_google_analytics')){
            if ($request->get('sys_google_analytics') != $old_sys_google_analytics){
                $message .= "\n";
                $message .= "Thay đổi sys_google_analytics - Google analytics";
            }
        }

        $old_sys_google_tag_manager_body = Setting::getSettingShop('sys_google_tag_manager_body',null,$shop->id);
        $params_before['sys_google_tag_manager_body'] = $old_sys_google_tag_manager_body??'';
        $params_after['sys_google_tag_manager_body'] = $request->get('sys_google_tag_manager_body')??'';
        if ($request->filled('sys_google_tag_manager_body')){
            if ($request->get('sys_google_tag_manager_body') != $old_sys_google_tag_manager_body){
                $message .= "\n";
                $message .= "Thay đổi sys_google_tag_manager_body - Google tag manager body";
            }
        }

        $old_sys_google_tag_manager_head = Setting::getSettingShop('sys_google_tag_manager_head',null,$shop->id);
        $params_before['sys_google_tag_manager_head'] = $old_sys_google_tag_manager_head??'';
        $params_after['sys_google_tag_manager_head'] = $request->get('sys_google_tag_manager_head')??'';
        if ($request->filled('sys_google_tag_manager_head')){
            if ($request->get('sys_google_tag_manager_head') != $old_sys_google_tag_manager_head){
                $message .= "\n";
                $message .= "Thay đổi sys_google_tag_manager_head - Google tag manager head";
            }
        }

        $old_sys_id_chat_message = Setting::getSettingShop('sys_id_chat_message',null,$shop->id);
        $params_before['sys_id_chat_message'] = $old_sys_id_chat_message??'';
        $params_after['sys_id_chat_message'] = $request->get('sys_id_chat_message')??'';
        if ($request->filled('sys_id_chat_message')){
            if ($request->get('sys_id_chat_message') != $old_sys_id_chat_message){
                $message .= "\n";
                $message .= "Thay đổi sys_id_chat_message - ID chat message";
            }
        }

        $old_sys_intro_text = Setting::getSettingShop('sys_intro_text',null,$shop->id);
        $params_before['sys_intro_text'] = $old_sys_intro_text??'';
        $params_after['sys_intro_text'] = $request->get('sys_intro_text')??'';
        if ($request->filled('sys_intro_text')){
            if ($request->get('sys_intro_text') != $old_sys_intro_text){
                $message .= "\n";
                $message .= "Thay đổi sys_intro_text - Intro text";
            }
        }

        $old_sys_keyword = Setting::getSettingShop('sys_keyword',null,$shop->id);
        $params_before['sys_keyword'] = $old_sys_keyword??'';
        $params_after['sys_keyword'] = $request->get('sys_keyword')??'';
        if ($request->filled('sys_keyword')){
            if ($request->get('sys_keyword') != $old_sys_keyword){
                $message .= "\n";
                $message .= "Thay đổi sys_keyword - Keyword điểm bán";
            }
        }

        $old_sys_logo = Setting::getSettingShop('sys_logo',null,$shop->id);
        $params_before['sys_logo'] = $old_sys_logo??'';
        $params_after['sys_logo'] = $request->get('sys_logo')??'';
        if ($request->filled('sys_logo')){
            if ($request->get('sys_logo') != $old_sys_logo){
                $message .= "\n";
                $message .= "Thay đổi sys_logo - Ảnh logo";
            }
        }

        $old_sys_mail = Setting::getSettingShop('sys_mail',null,$shop->id);
        $params_before['sys_mail'] = $old_sys_mail??'';
        $params_after['sys_mail'] = $request->get('sys_mail')??'';
        if ($request->filled('sys_mail')){
            if ($request->get('sys_mail') != $old_sys_mail){
                $message .= "\n";
                $message .= "Thay đổi sys_mail - Mail liên hệ";
            }
        }

        $old_sys_logo_mobile = Setting::getSettingShop('sys_logo_mobile',null,$shop->id);
        $params_before['sys_logo_mobile'] = $old_sys_logo_mobile??'';
        $params_after['sys_logo_mobile'] = $request->get('sys_logo_mobile')??'';
        if ($request->filled('sys_logo_mobile')){
            if ($request->get('sys_logo_mobile') != $old_sys_logo_mobile){
                $message .= "\n";
                $message .= "Thay đổi sys_logo_mobile - Logo mobile";
            }
        }

        $old_sys_marquee = Setting::getSettingShop('sys_marquee',null,$shop->id);
        $params_before['sys_marquee'] = $old_sys_marquee??'';
        $params_after['sys_marquee'] = $request->get('sys_marquee')??'';
        if ($request->filled('sys_marquee')){
            if ($request->get('sys_marquee') != $old_sys_marquee){
                $message .= "\n";
                $message .= "Thay đổi sys_marquee - Nội dung chạy chữ";
            }
        }

        $old_sys_noti_popup = Setting::getSettingShop('sys_noti_popup',null,$shop->id);
        $params_before['sys_noti_popup'] = $old_sys_noti_popup??'';
        $params_after['sys_noti_popup'] = $request->get('sys_noti_popup')??'';
        if ($request->filled('sys_noti_popup')){
            if ($request->get('sys_noti_popup') != $old_sys_noti_popup){
                $message .= "\n";
                $message .= "Thay đổi sys_noti_popup - Nội dung thông báo popup";
            }
        }

        $old_sys_og_image = Setting::getSettingShop('sys_og_image',null,$shop->id);
        $params_before['sys_og_image'] = $old_sys_og_image??'';
        $params_after['sys_og_image'] = $request->get('sys_og_image')??'';
        if ($request->filled('sys_og_image')){
            if ($request->get('sys_og_image') != $old_sys_og_image){
                $message .= "\n";
                $message .= "Thay đổi sys_og_image - Ảnh og";
            }
        }

        $old_sys_phone = Setting::getSettingShop('sys_phone',null,$shop->id);
        $params_before['sys_phone'] = $old_sys_phone??'';
        $params_after['sys_phone'] = $request->get('sys_phone')??'';
        if ($request->filled('sys_phone')){
            if ($request->get('sys_phone') != $old_sys_phone){
                $message .= "\n";
                $message .= "Thay đổi sys_phone - Số điện thoại liên hệ";
            }
        }

        $old_sys_card_setting = Setting::getSettingShop('sys_card_setting',null,$shop->id);
        $params_before['sys_card_setting'] = $old_sys_card_setting??'';
        $params_after['sys_card_setting'] = $request->get('sys_card_setting')??'';
        if ($request->filled('sys_card_setting')){
            if ($request->get('sys_card_setting') != $old_sys_card_setting){
                $message .= "\n";
                $message .= "Thay đổi sys_card_setting - Hiển thị giá card";
            }
        }

        $old_sys_default_change_image = Setting::getSettingShop('sys_default_change_image',null,$shop->id);
        $params_before['sys_default_change_image'] = $old_sys_default_change_image??'';
        $params_after['sys_default_change_image'] = $request->get('sys_default_change_image')??'';
        if ($request->filled('sys_default_change_image')){
            if ($request->get('sys_default_change_image') != $old_sys_default_change_image){
                $message .= "\n";
                $message .= "Thay đổi sys_default_change_image - Ảnh mặc định nạp thẻ";
            }
        }

        $old_sys_error_image = Setting::getSettingShop('sys_error_image',null,$shop->id);
        $params_before['sys_error_image'] = $old_sys_error_image??'';
        $params_after['sys_error_image'] = $request->get('sys_error_image')??'';
        if ($request->filled('sys_error_image')){
            if ($request->get('sys_error_image') != $old_sys_error_image){
                $message .= "\n";
                $message .= "Thay đổi sys_error_image - Ảnh lỗi mặc định";
            }
        }

        $old_sys_google_plus = Setting::getSettingShop('sys_google_plus',null,$shop->id);
        $params_before['sys_google_plus'] = $old_sys_google_plus??'';
        $params_after['sys_google_plus'] = $request->get('sys_google_plus')??'';
        if ($request->filled('sys_google_plus')){
            if ($request->get('sys_google_plus') != $old_sys_google_plus){
                $message .= "\n";
                $message .= "Thay đổi sys_google_plus - Google plus";
            }
        }

        $old_sys_google_search_console = Setting::getSettingShop('sys_google_search_console',null,$shop->id);
        $params_before['sys_google_search_console'] = $old_sys_google_search_console??'';
        $params_after['sys_google_search_console'] = $request->get('sys_google_search_console')??'';
        if ($request->filled('sys_google_search_console')){
            if ($request->get('sys_google_search_console') != $old_sys_google_search_console){
                $message .= "\n";
                $message .= "Thay đổi sys_google_search_console - Google search console";
            }
        }

        $old_sys_schema = Setting::getSettingShop('sys_schema',null,$shop->id);
        $params_before['sys_schema'] = $old_sys_schema??'';
        $params_after['sys_schema'] = $request->get('sys_schema')??'';
        if ($request->filled('sys_schema')){
            if ($request->get('sys_schema') != $old_sys_schema){
                $message .= "\n";
                $message .= "Thay đổi sys_schema - Schema";
            }
        }

        $old_sys_store_card_content = Setting::getSettingShop('sys_store_card_content',null,$shop->id);
        $params_before['sys_store_card_content'] = $old_sys_store_card_content??'';
        $params_after['sys_store_card_content'] = $request->get('sys_store_card_content')??'';
        if ($request->filled('sys_store_card_content')){
            if ($request->get('sys_store_card_content') != $old_sys_store_card_content){
                $message .= "\n";
                $message .= "Thay đổi sys_store_card_content - Nội dung mua thẻ";
            }
        }

        $old_sys_store_card_seo = Setting::getSettingShop('sys_store_card_seo',null,$shop->id);
        $params_before['sys_store_card_seo'] = $old_sys_store_card_seo??'';
        $params_after['sys_store_card_seo'] = $request->get('sys_store_card_seo')??'';
        if ($request->filled('sys_store_card_seo')){
            if ($request->get('sys_store_card_seo') != $old_sys_store_card_seo){
                $message .= "\n";
                $message .= "Thay đổi sys_store_card_seo - Seo mua thẻ";
            }
        }

        $old_sys_store_card_title = Setting::getSettingShop('sys_store_card_title',null,$shop->id);
        $params_before['sys_store_card_title'] = $old_sys_store_card_title??'';
        $params_after['sys_store_card_title'] = $request->get('sys_store_card_title')??'';
        if ($request->filled('sys_store_card_title')){
            if ($request->get('sys_store_card_title') != $old_sys_store_card_title){
                $message .= "\n";
                $message .= "Thay đổi sys_store_card_title - Tiêu đề mua thẻ";
            }
        }

        $old_sys_title = Setting::getSettingShop('sys_title',null,$shop->id);
        $params_before['sys_title'] = $old_sys_title??'';
        $params_after['sys_title'] = $request->get('sys_title')??'';
        if ($request->filled('sys_title')){
            if ($request->get('sys_title') != $old_sys_title){
                $message .= "\n";
                $message .= "Thay đổi sys_title - Tiêu đề điểm bán";
            }
        }

        $old_sys_top_charge = Setting::getSettingShop('sys_top_charge',null,$shop->id);
        $params_before['sys_top_charge'] = $old_sys_top_charge??'';
        $params_after['sys_top_charge'] = $request->get('sys_top_charge')??'';
        if ($request->filled('sys_top_charge')){
            if ($request->get('sys_top_charge') != $old_sys_top_charge){
                $message .= "\n";
                $message .= "Thay đổi sys_top_charge - Top nạp thẻ";
            }
        }
        $old_sys_tranfer_content = Setting::getSettingShop('sys_tranfer_content',null,$shop->id);
        $params_before['sys_tranfer_content'] = $old_sys_tranfer_content??'';
        $params_after['sys_tranfer_content'] = $request->get('sys_tranfer_content')??'';
        if ($request->filled('sys_tranfer_content')){
            if ($request->get('sys_tranfer_content') != $old_sys_tranfer_content){
                $message .= "\n";
                $message .= "Thay đổi sys_tranfer_content - Nội dung chuyển tiền";
            }
        }
        $old_sys_twitter = Setting::getSettingShop('sys_twitter',null,$shop->id);
        $params_before['sys_twitter'] = $old_sys_twitter??'';
        $params_after['sys_twitter'] = $request->get('sys_twitter')??'';
        if ($request->filled('sys_twitter')){
            if ($request->get('sys_twitter') != $old_sys_twitter){
                $message .= "\n";
                $message .= "Thay đổi sys_twitter - Link twitter";
            }
        }
        $old_sys_youtube = Setting::getSettingShop('sys_youtube',null,$shop->id);
        $params_before['sys_youtube'] = $old_sys_youtube??'';
        $params_after['sys_youtube'] = $request->get('sys_youtube')??'';
        if ($request->filled('sys_youtube')){
            if ($request->get('sys_youtube') != $old_sys_youtube){
                $message .= "\n";
                $message .= "Thay đổi sys_youtube - Link youtube";
            }
        }

        $message .= "\n";
        $message .= "IP: <b>" . $ip . "</b>";
        $message .= "\n";
        $message .= "User_agent: <b>" . $user_agent . "</b>";
        Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_noty_setting'));

        $rules = Setting::getValidationRules();
        $this->validate($request, $rules);
        $data=$request->all();
        $validSettings = array_keys($rules);
        foreach ($data as $key => $val) {
            if (in_array($key, $validSettings)) {
                $InputTypeOfField = Setting::getInputType($key);
                if($InputTypeOfField == 'list_top_charge'){
                    $list_top_charge = null;
                    if(isset($val['user']) && isset($val['amount']) && count($val['user']) > 0 && count($val['amount']) > 0){
                        for($i = 0; $i < count($val['amount']); $i++){
                            if(isset($val['user'][$i]) && $val['user'][$i] != "" && isset($val['amount'][$i]) && $val['amount'][$i] != ""){
                                $list_top_charge[] = [
                                    'user' => $val['user'][$i],
                                    'amount' => $val['amount'][$i],
                                ];
                            }
                        }
                    }
                    if(isset($list_top_charge)){
                        $list_top_charge = json_encode($list_top_charge);
                    }
                    $val = $list_top_charge;
                }
                Setting::add($key, $val, Setting::getDataType($key));
            }
        }


        $log_data['description_before'] = json_encode($params_before);
        $log_data['description_after'] = json_encode($params_after);
        $log_data['author_id'] = auth()->user()->id;
        $log_data['type'] = 0;
        $log_data['table_name'] = 'settings';
        $log_data['table_id'] = $shop->id;
        $log_data['shop_id'] = $shop->id;
        LogEdit::create($log_data);
        $description = 'Cập nhật thành công setting: shop-id: '.$shop->id.' domain: '.$shop->domain.'QTV: '.Auth::user()->username;
        ActivityLog::add($request, $description);

        return redirect()->back()->with('success',__('Cập nhật thông báo mua thẻ thành công !'));

    }
}
