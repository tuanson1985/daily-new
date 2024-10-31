<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Library\HelperPermisionShopMinigame;
use App\Library\HelperShopClient;
use App\Models\Item;
use App\Models\LogEdit;
use App\Models\Server;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\ThemeClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ActivityLog;
use App\Models\Shop;
use App\Models\Group;
use App\Models\GroupShop;
use App\Models\Shop_Group;
use App\Library\Helpers;
use Html;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Session;
use Validator;
use App\Library\ChargeGateway\NAPTHENHANH;
use App\Library\ChargeGateway\CANCAUCOM;
use App\Library\ChargeGateway\PAYPAYPAY;
use App\Library\HelpItemAdd;
use App\Library\HelperItemDaily;
use App\Library\HelperReplicationModule;
use Carbon\Carbon;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $page_breadcrumbs;
    protected $module;
    public function __construct(Request $request)
    {

        $this->module=$request->segments()[1]??"";

        //set permission to function

        $this->middleware('permission:client-list');
        $this->middleware('permission:client-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:client-edit', ['only' => ['edit', 'update','UpdateStatus','secret_key']]);
        $this->middleware('permission:client-delete', ['only' => ['destroy']]);
        $this->middleware('permission:client-get-partner', ['only' => ['getPartNer']]);
        $this->middleware('permission:client-access',['only' => ['access','access_custom']]);

        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.shop.index'),
                'title' => __(config('module.shop.title'))
            ];
        }
    }



    public function index(Request $request)
    {

        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);

        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        if($request->ajax) {

            $datatable= Shop::with('group')->whereIn('id',$arr_permission);
            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', $request->get('id'));
                });
            }

            if ($request->filled('kitio'))  {

                if ($request->get('kitio') == 1){
                    $getAllSetting = HelperShopClient::getSettingKitioShop(1);
                }else{
                    $getAllSetting = HelperShopClient::getSettingKitioShop(0);
                }

                $datatable = $datatable->whereIn('id',$getAllSetting);
            }

            if ($request->filled('domain'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhereIn('domain',$request->domain);
                });
            }

            if ($request->filled('group'))  {
                $datatable = $datatable->with('group', function ($querysub) use ($request){
                    $querysub->whereIn('id', $request->get('group'));
                })->whereHas('group', function ($querysub) use ($request){
                    $querysub->whereIn('id', $request->get('group'));
                });
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
                    'domain',
                    'title',
                    'group',
                    'server',
                    'status',
                    'type_information',
                    'created_at',
                    'action',
                    'param_tracking',
                    'kitio',
                    'expired_time'
                ])

                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->editColumn('expired_time', function($data) {
                    if(isset($data->expired_time)){
                        return date('d/m/Y', strtotime($data->expired_time));
                    }
                    return "Chưa cấu hình";
                })
                ->editColumn('server', function($data) {
                    $server = Server::where('id',$data->server_id)->first();
                    $temp = '';
                    if($server){
                        $temp .= '<a href="javascript:updateServer('.$data->id.')" class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-info btn-refund ml-2" title="Cập nhật IP"><i class="la la-refresh"></i></a><a id="dataserver_'.$data->id.'" href="/admin/server/'.$server->id.'/edit" target="_blank" title="Chỉnh sửa server">'.$server->ipaddress.'</a>';
                    }
                    else{
                        $temp .= '<a href="javascript:updateServer('.$data->id.')" class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-info btn-refund ml-2" title="Cập nhật IP"><i class="la la-refresh"></i></a><a id="dataserver_'.$data->id.'" href="javascript://" title="Đang cập nhật">Đang cập nhật...</a>';
                    }
                    return $temp;

                })
                ->editColumn('status', function($data) {
                    $temp = '';
                    $temp .= '<span class="switch switch-outline switch-icon switch-success btn-update-stt" data-id="'.$data->id.'">';
                    $temp .= '<label>';
                    if($data->status == 1){
                        $temp .= '<span class="badge badge-success">Hoạt động</span>';
                    }
                    else{
                        $temp .= '<span class="badge badge-secondary">Ngừng hoạt động</span>';
                    }
                    $temp .= '<span></span>';
                    $temp .= '</label>';
                    $temp .= '</span>';
                    return $temp;
                })
                ->addColumn('group', function($row) {
                    if(isset($row->group)){
                        return $row->group->title??'';
                    }
                    else{
                        return "";
                    }
                })
                ->addColumn('param_tracking', function($row) {
                    if (isset($row->param_tracking)){
                        $track = null;
                        $params = json_decode($row->param_tracking);
                        if (isset($params->tracking)){
                            $track = json_decode($params->tracking);
                        }

                        $html = '';
                        $html .= '<span class="badge badge-success">';
                        $html .= $track->group;
                        $html .= '</span>';
                        if ($track->status == 1){
                            $html .= '<span class="badge badge-primary" style="margin-left: 8px">';
                            $html .= "Theo dõi";
                            $html .= '</span>';
                        }else{
                            $html .= '<span class="badge badge-secondary" style="margin-left: 8px">';
                            $html .= "Ngừng theo dõi";
                            $html .= '</span>';
                        }

                        return $html;

                    }else{
                        return "<span class='badge badge-danger'>Chưa cấu hình</span>";
                    }
                })
                ->addColumn('kitio', function($row) {

                    $key = Setting::getSettingShop('sys_footer_kitio',null,$row->id);
                    $html = '';
                    if ($key != '' && $key == 1){
                        $html .= '<span class="badge badge-primary" style="margin-left: 8px">';
                            $html .= "Hiển thị";
                            $html .= '</span>';
                    }else{
                            $html .= '<span class="badge badge-secondary" style="margin-left: 8px">';
                            $html .= "Không hiển thị";
                            $html .= '</span>';
                    }
                    return $html;
                })
                ->addColumn('action', function($row) {
                    $temp= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    if(Auth::user()->can('client-access')) {
                        $temp .= "<a href=\"" . route('admin.shop.access', $row->id) . "\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger \" title=\"Phân quyền\"><i class=\"la la-sitemap\"></i></a>";
                    }
                    return $temp;
                })
                ->rawColumns(['action', 'status','server','param_tracking','kitio'])
                ->toJson();
        }

        if(Auth::user()->account_type == 1){
            $client = Shop::orderBy('id','desc')->whereIn('id',$arr_permission);
            $shop_access_user = Auth::user()->shop_access;
            if(isset($shop_access_user) && $shop_access_user !== "all"){
                $shop_access_user = json_decode($shop_access_user);
                $client = $client->whereIn('id',$shop_access_user);
            }
            $client = $client->select('id','domain','title')->get();
        }

        $group_shops = Shop_Group::where('status',1)->get();

        return view('admin.shop.item.index')
            ->with('module', $this->module)
            ->with('client', $client)
            ->with('group_shops', $group_shops)
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

        $dataCategory = Shop_Group::orderBy('id','desc')->get();

        $theme = Theme::where('status',1)->get();

        $roles=Role::orderBy('order','asc')->get();
        $shop = Shop::orderBy('id','asc')->get();
        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.shop.item.create_edit')
            ->with('module', $this->module)
            ->with('roles', $roles)
            ->with('shop', $shop)
            ->with('theme', $theme)
            ->with('dataCategory', $dataCategory)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'domain'=>'required|unique:shop,domain',
            'group_id'=>'required',
            'ratio_atm'=>'required',
            'key_transfer' => 'unique:shop,key_transfer'
        ],[
            'domain.required' => __('Vui lòng nhập tên shop'),
            'domain.unique' => __('Domain đã tồn tại'),
            'group_id.required' => __('Bạn chưa chọn nhóm shop'),
            'ratio_atm.required' => __('Chiết khấu ATM không được bỏ trống'),
            'key_transfer.unique' => __('Key nạp ví ATM bị trùng'),
        ]);

        $input=$request->except(['is_clone','shop_clone','clone_module']);
        $ratio_atm = (float)$request->ratio_atm;
        if($ratio_atm > 250 || $ratio_atm < 60){
            return redirect()->back()->withErrors('Chiết khấu ATM không hợp lệ, vui lòng kiểm tra lại');
        }
        $input['ratio_atm'] = $ratio_atm;
        $data=Shop::create($input);
        $string = time().rand(100000,999999);
        $secret_key = Helpers::Encrypt($string,md5($data->id));
        $data->secret_key = $secret_key;
        $data->status = 0;
        $data->save();
        if(isset($request->expired_time)){
            $input['expired_time'] = Carbon::createFromFormat('d/m/Y H:i:s', $request->expired_time);
        }
        if(isset($input['role_ids']) && $input['role_ids'] != ""){
            $roles = Role::whereIn('id',$input['role_ids'])->get();
            foreach($roles as $item){
                $shop_id_roles = $item->shop_access;
                if(empty($shop_id_roles)){
                    continue;
                }
                elseif(isset($shop_id_roles) && $shop_id_roles === "all"){
                    continue;
                }
                else{
                    $shop_id_roles = json_decode($shop_id_roles);
                    if(is_object($shop_id_roles)){
                        $shop_id_roles = (array)$shop_id_roles;
                    }
                    array_push($shop_id_roles, $data->id.'');
                    $shop_id_roles = json_encode($shop_id_roles,JSON_UNESCAPED_UNICODE);
                    $item->shop_access = $shop_id_roles;
                    $item->save();
                }
            }
        }
        // trường hợp yêu cầu cần clone shop
        if($request->is_clone == 1){
            $shop_id_clone = $request->shop_id_clone;
            $shop_clone = Shop::where('id',$shop_id_clone)->first();
            if(!$shop_clone){
                return redirect()->back()->withErrors('ID shop nhân bản không hợp lệ.');
            }
            $clone_module = $request->clone_module;
            if(count($clone_module) < 1){
                return redirect()->back()->withErrors('Module yêu cầu nhân bản không hợp lệ hoặc không tồn tại.');
            }
            foreach($clone_module as $item){
                if($item === "charge"){
                    HelperReplicationModule::__moduleCharge($data->id,$shop_clone->id,null);
                }
                if($item === "store_card"){
                    HelperReplicationModule::__moduleStoreCard($data->id,$shop_clone->id,null);
                }

                if($item === "service"){
                    HelperReplicationModule::__moduleService($data->id,$shop_clone->id);
                }

                if($item === "menu-profile"){
                    HelperReplicationModule::__moduleMenuCateogy($data->id,$shop_clone->id);
                }

                if($item === "menu-category"){
                    HelperReplicationModule::__moduleMenuProfile($data->id,$shop_clone->id);
                }

                if($item === "menu-transaction"){
                    HelperReplicationModule::__moduleMenuTransaction($data->id,$shop_clone->id);
                }

                if($item === "article"){
                    HelperReplicationModule::__moduleArticle($data->id,$shop_clone->id);
                }
            }
        }


//        Tạo theme cho shop.
        if ($request->filled('theme_id')){

            $param_attribute = [
                'sys_store_card_vers' => 'sys_store_card_vers_1',
                'sys_store_card_vers_value' => 'Hiển thị mua thẻ ver1',
                'sys_theme_ver' => 'sys_theme_ver3.0',
                'sys_theme_ver_value'=> 'Shop Brand chung',
            ];

            $input_theme = [
                'client_name' => $data->domain,
                'param_attribute' => json_encode($param_attribute, JSON_UNESCAPED_UNICODE),
                'client_id' => $data->id,
                'theme_id' => $request->theme_id,
                'order' => 1,
                'status'=> 1,
                'created_at' => Carbon::now()
            ];

            ThemeClient::create($input_theme);

            $key = 'sys_theme_ver_page_build';

            $module = config('module.theme-page.key');

            $themeclient = ThemeClient::where('client_id',$data->id)->first();

            $c_theme = Theme::where('id',$themeclient->theme_id)->where('status',1)->first();
            $key_theme = $c_theme->key;

            $page_build = config('pages_build.'.$key_theme);

            if(isset($page_build)){

                foreach ($page_build as $key => $item){

                    Group::create([
                        'module' => $module,
                        'shop_id' => $data->id,
                        'title' => $item,
                        'slug' => $key,
                        'idkey' => $key_theme,
                        'author_id' => auth()->user()->id,
                        'status' => 0,
                    ]);

                }

            }

        }

        ActivityLog::add($request, 'Tạo mới thành công '.$this->module.' #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Thêm mới thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }
    }

    public function zipItem(Request $request){

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $shop_id = session()->get('shop_id');
        $shop =  Shop::where('id',$shop_id)->first();
        $media_url = config('module.media.url').'/storage';

        $host = 'https://'.$shop->domain;
        $path_url = "/api/get-getArticle";
        $url = $host.$path_url;

        $data = array();
        $data['sign'] = md5('hqplay');
        if(is_array($data)){
            $dataPost = http_build_query($data);
        }else{
            $dataPost = $data;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        curl_setopt($ch, CURLOPT_REFERER, $actual_link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $resultRaw = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = json_decode($resultRaw);

        if ($httpcode == 200 && $result->status == 1 && isset($result->data) && count($result->data)){

            $data_g = $result->data;

            foreach ($data_g as $key => $value_g){

                $input_g['title'] = $value_g->title;
                $input_g['slug'] = $value_g->slug;
                $input_g['shop_id'] = $shop_id;
                $input_g['module'] = 'article-category';
                $input_g['status'] = $value_g->status;
                $input_g['target'] = $value_g->target;
                $input_g['url'] = $value_g->url;
                $input_g['order'] = $value_g->order;
                $input_g['author_id'] = Auth::user()->id;
                $input_g['parent_id'] = $value_g->parrent_id;
                $input_g['image'] = '/storage'.$value_g->image;
                $input_g['description'] = $value_g->description;
                $input_g['content'] = $value_g->content;

                $group = Group::create($input_g);
                // xử lý ảnh.
                if(isset($value_g->image)){
                    $image = $value_g->image;
                    $cdn_image = $host.$image;
                    $this->__makeImage($image,$cdn_image);
                }


                if (isset($value_g->items) && count($value_g->items)){

                    $items = $value_g->items;

                    foreach ($items as $key_i => $value_i){
                        $input_i['shop_id'] = $shop_id;
                        $input_i['slug'] = $value_i->slug;
                        $input_i['title'] = $value_i->title;
                        $input_i['seo_title'] = $value_i->title;
                        $input_i['description'] = $value_i->description;
                        $input_i['seo_description'] = $value_i->description;
                        $input_i['content'] = $value_i->content;
                        $input_i['module'] = 'article';
                        $input_i['status'] = $value_i->status;
                        $input_i['order'] = $value_i->order;
                        $input_i['url'] = $value_i->url;
                        $input_i['author_id'] = Auth::user()->id;
                        $input_i['image'] = '/storage'.$value_i->image;
                        $input_i['published_at'] = $value_i->created_at;
                        $input_i['display_type'] = 1;

                        $item = Item::create($input_i);

                        $item->groups()->attach($group->id);

                        // xử lý ảnh đại diện
                        if(isset($value_i->image)){
                            $image_i = $value_i->image;
                            $cdn_image_i = $host.$image_i;
                            $this->__makeImage($image_i,$cdn_image_i);
                        }



                        // xử lý ảnh chi tiết bài viết
                        $content = $value_i->content;
                        $content_replace_img = $content;
                        $image_article = $this->__parseImages($content);
                        if(isset($image_article) && count($image_article)){
                            if(isset($image_article[1])){
                                $image_article_url = $image_article[1];
                                foreach($image_article_url as $key_url => $item_image_article_url){
                                    // trường hợp ảnh không có https
                                    if(!str_contains($item_image_article_url, 'https://')){

                                        $url_save = $media_url.$item_image_article_url;
                                        $image_article_url = $item_image_article_url;
                                        $cdn_image_article = $host.$image_article_url;
                                        $this->__makeImage($image_article_url,$cdn_image_article);
                                    }
                                    // trường hợp ảnh có https
                                    else{
                                        $parse_article_url = parse_url($item_image_article_url);
                                        $url_save = $media_url.$parse_article_url['path'];
                                        $image_article_url = $parse_article_url['path']??null;
                                        $cdn_image_article = $item_image_article_url;
                                        $this->__makeImage($image_article_url,$cdn_image_article);
                                    }
                                    $content_replace_img = str_replace($item_image_article_url,$url_save,$content_replace_img);
                                }

                                $item->content = $content_replace_img;
                                $item->save();
                            }
                        }

                    }

                }
            }

            $key = "sys_zip_shop";
            $val = "/blog";

            Setting::add($key, $val, Setting::getDataType($key));

            return redirect()->back()->with('success',__('Zip thành công !'));
        }else{
            return redirect()->back()->with('error',__('Zip shop thất bại !'));
        }



    }

    function __makeImage($image,$cdn_image){
        $path = explode("/",$image);
        $image_name = $path[count($path) - 1];
        $dir = "";
        for($i = 1;$i<count($path)-1;$i++){
            $dir .= '/'.$path[$i];
        }
        if (!is_dir(storage_path('app/public'.$dir))) {
            mkdir(storage_path('app/public'.$dir), 0755, true);
        }
        try{
            if(!file_exists(storage_path('app/public'.$dir.'/'.$image_name))){
                file_put_contents(storage_path('app/public'.$dir.'/'.$image_name), file_get_contents($cdn_image));
            }
        }
        catch (\Exception $e){
            return null;
        }

        return storage_path('app/public'.$dir.'/'.$image_name);
    }

    function __parseImages($text)
    {
        preg_match_all(
            '#<img.+?src="([^"]*)#s',
            $text,
            $images
        );
        return $images;
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
    public function edit(Request $request,$id)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];
        $data = Shop::findOrFail($id);
        if($data->shop_id){
            $shop = Shop::findOrFail($id);
            session()->put('shop_id', $shop->id);
            session()->put('shop_name', $shop->domain);
        }
        $roles=Role::orderBy('order','asc')->get();
        $dataCategory = Shop_Group::orderBy('id','desc')->get();
        // $shop_access = Role::where('shop_access', 'LIKE', '%"' . $data->id . '"%')->pluck('id')->toArray();
        $shop_access = Role::where(function($query) use ($data){
            $query->where('shop_access', 'LIKE', '%"' . $data->id . '"%');
            $query->orWhere('shop_access','=', 'all');
            $query->orWhereNull('shop_access');
        })->pluck('id')->toArray();


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,fr-FR;q=0.6,fr;q=0.5',
            'Cache-Control: max-age=0',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36'

        ));

        $url = "http://tracking.tichhop.pro/api/tracking?action=config";

        $t_data = array();
        if(is_array($t_data)){
            $dataPost = http_build_query($t_data);
        }else{
            $dataPost = $t_data;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        curl_setopt($ch, CURLOPT_REFERER, $actual_link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $resultRaw = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = json_decode($resultRaw);

        $trackings = null;

        if (isset($result)){
            $trackings = $result->groups??null;
        }

        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        return view('admin.shop.item.create_edit')
            ->with('module', $this->module)
            ->with('roles', $roles)
            ->with('shop_access', $shop_access)
            ->with('trackings', $trackings)
            ->with('dataCategory', $dataCategory)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data);
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
        $data =  Shop::findOrFail($id);

        $this->validate($request,[
            'domain'=>'required|unique:shop,domain,'.$data->id,
            'group_id'=>'required',
            'ratio_atm'=>'required',
            'key_transfer' => 'unique:shop,key_transfer,'.$data->id
        ],[
            'domain.required' => __('Vui lòng nhập tên domain'),
            'domain.unique' => __('Domain đã tồn tại'),
            'group_id.required' => __('Bạn chưa chọn nhóm shop'),
            'ratio_atm.required' => __('Chiết khấu ATM không được bỏ trống'),
            'key_transfer.unique' => __('Key nạp ví ATM bị trùng'),
        ]);

        if ($data->status != 1 && $request->status == 1){
            if (!isset($request->group_tracking) || !isset($request->status_tracking)){
                return redirect()->back()->with('error',"Vui lòng chọn group và trạng thái tracking");
            }
        }

        $mesageerror = '';
        $slage = 1;

        if (empty($data->ntn_partner_key)){

            $slage = 0;
            $mesageerror= 'Chưa có thông tin key nạp thẻ NTN!';

        }

        if (empty($data->ccc_partner_key)){

            if ($slage == 1){
                $mesageerror = 'Chưa có thông tin key nạp thẻ CCC!';
            }else{
                $mesageerror = $mesageerror.', Chưa có thông tin key nạp thẻ CCC!';
            }

            $slage = 0;

        }

        if (empty($data->ntn_partner_key_card)){

            if ($slage == 1){
                $mesageerror = 'Chưa có thông tin key mua thẻ!';
            }else{
                $mesageerror = $mesageerror.', Chưa có thông tin key mua thẻ!';
            }

            $slage = 0;

        }

        if (empty($data->tichhop_key)){

            if ($slage == 1){
                $mesageerror = 'Chưa có thông tin key tích hợp!';
            }else{
                $mesageerror = $mesageerror.', Chưa có thông tin key tích hợp!';
            }

            $slage = 0;
        }

        if ($slage == 0){

            return redirect()->back()->with('error',__($mesageerror));
        }

        $input=$request->all();
        $ratio_atm = (float)$request->ratio_atm;
        if($ratio_atm > 250 || $ratio_atm < 60){
            return redirect()->back()->withErrors('Chiết khấu ATM không hợp lệ, vui lòng kiểm tra lại');
        }
        $input['ratio_atm'] = $ratio_atm;
        unset($input['secret_key']);
        if(isset($request->expired_time)){
            $input['expired_time'] = Carbon::createFromFormat('d/m/Y H:i:s', $request->expired_time);
        }
        else{
            $input['expired_time'] = null;
        }
//        $input['params'] = $params;

        $data->update($input);

        if(isset($input['role_ids']) && $input['role_ids'] != ""){
            $roles_del=Role::orderBy('order','asc')->whereNotIn('id',$input['role_ids'])->get();
            $shop_id = Shop::pluck('id')->toArray();

            foreach($roles_del as $key => $item){
                $shop_id_roles = null;
                if(empty($item->shop_access) || isset($item->shop_access) && $item->shop_access == "all"){
                    $shop_id_roles = $this->delArrValues($shop_id, [$data->id]);
                    $shop_id_roles = json_encode($shop_id_roles,JSON_UNESCAPED_UNICODE);
                    $item->shop_access = $shop_id_roles;
                    $item->save();
                    continue;
                }
                else{
                    $shop_id_roles = json_decode($item->shop_access);

                    if(is_object($shop_id_roles)){
                        $shop_id_roles = (array)$shop_id_roles;
                    }

                    $shop_id_roles = $this->delArrValues($shop_id_roles, [$data->id]);
                    $shop_id_roles = json_encode($shop_id_roles,JSON_UNESCAPED_UNICODE);
                    $item->shop_access = $shop_id_roles;
                    $item->save();
                    continue;
                }
            }
            $roles_add=Role::orderBy('order','asc')->whereIn('id',$input['role_ids'])->get();
            foreach($roles_add as $item){
                $shop_id_roles = null;
                $shop_id_roles = $item->shop_access;
                if(empty($shop_id_roles)){
                    continue;
                }
                elseif(isset($shop_id_roles) && $shop_id_roles === "all"){
                    continue;
                }
                else{
                    $shop_id_roles = json_decode($shop_id_roles);

                    if(is_object($shop_id_roles)){
                        $shop_id_roles = (array)$shop_id_roles;
                    }

                    if(in_array($data->id, $shop_id_roles)){
                        continue;
                    }
                    else{
                        array_push($shop_id_roles, $data->id.'');
                        $shop_id_roles = json_encode($shop_id_roles,JSON_UNESCAPED_UNICODE);
                        $item->shop_access = $shop_id_roles;
                        $item->save();
                        continue;
                    }

                }
            }
        }

        if ($data->status == 1){

            $checkparams = 1;

            if (isset($data->param_tracking)){
                $track = null;
                $params = json_decode($data->param_tracking);
                if (isset($params->tracking)){
                    $track = json_decode($params->tracking);
                }

                if ($track->status != $request->status_tracking || $track->group != $request->group_tracking){
                    $checkparams = 0;
                }
            }else{
                $checkparams = 0;
            }

            if ($checkparams == 0){

                $group_tracking = $request->group_tracking;

                $params_tracking["group"] = $request->group_tracking;
                $params_tracking["status"] = $request->status_tracking;
                $params_input["tracking"] = json_encode($params_tracking);
                $params = json_encode($params_input);

                $url = 'http://tracking.tichhop.pro/api/tracking';
                $host = 'https://'.$data->domain;
                $t_data = array();
                $t_data['action'] = 'submit';
                $t_data['group'] = $group_tracking;
                $t_data['url'] = $host;
                $t_data['name'] = $data->title;
                $t_data['status'] = $request->status_tracking;

                if(is_array($t_data)){
                    $dataPost = http_build_query($t_data);
                }else{
                    $dataPost = $t_data;
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
                $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                curl_setopt($ch, CURLOPT_REFERER, $actual_link);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                $resultRaw = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                $result = json_decode($resultRaw);

                if (isset($result)){
                    $data->param_tracking = $params;
                    $data->save();
                }
            }
        }

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
        Shop::whereIn('id',$input)->update(['status' => 0]);
        ActivityLog::add($request, 'Xóa thành công '.$this->module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Xóa thành công !'));
    }

    function access(Request $request, $id){

        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        if (!in_array($id,$arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        if(!Auth::user()->can('client-access')) {
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        if ($request->method() == 'POST') {
            $input = $request->all();
            $ids = explode(',', $input['permission_ids']??'');
            GroupShop::where(['shop_id' => $id])->whereNotIn('group_id', $ids)->where('status', 1)->update(['status' => 0]);
            GroupShop::where(['shop_id' => $id])->whereIn('group_id', $ids)->update(['status' => 1]);
            ActivityLog::add($request, "Cập nhật phân quyền danh mục cho shop #{$id}");
            return redirect()->back()->with('success',__('cập nhật thành công !'));
        }
        $shop = Shop::findOrFail($id);
        $providers = Group::where('module', 'acc_provider')->with(['childs' => function($query) use($id){
            $query->with(['custom' => function($query) use($id){
                $query->where('groups_shops.shop_id', $id);
            }])->orderBy('order');
        }])->orderBy('order')->get();

        $array = [];
        $cat_selected = [];

        if (Auth::user()->can('client-access-list')){
            foreach ($providers as $key => $provider) {
                if($provider->childs->count()){
                    $array[]=[
                        "id"=> $provider->id."",
                        "parent" => "#",
                        "text"=> htmlentities($provider->title),
                        "state"=>[
                            'opened' => true,
                        ]
                    ];
                    foreach ($provider->childs as $cat) {
                        $array[]=[
                            "id"=> $cat->id."",
                            "parent" => $provider->id."",
                            "text"=> $cat->title.'<a class="ml-2 jstree-link" href="'.route('admin.shop.access.custom', [$id, $cat->id]).'"><i class="fa fa-edit"></i> custom</a>',
                            "state"=>[
                                'opened' => false,
                                'selected' => ($cat->custom->status??0) != 0
                            ],
                        ];
                        if (($cat->custom->status??0) != 0) {
                            $cat_selected[] = $cat->id;
                        }elseif (empty($cat->custom)) {
                            GroupShop::firstOrCreate(['shop_id' => $id, 'group_id' => $cat->id]);
                        }
                    }
                }
            }
        }else{
            foreach ($providers as $key => $provider) {
                if($provider->childs->count()){
                    $array[]=[
                        "id"=> $provider->id."",
                        "parent" => "#",
                        "text"=> htmlentities($provider->title),
                        "state"=>[
                            'opened' => true,
                        ]
                    ];
                    foreach ($provider->childs as $cat) {
                        if (isset($cat->custom->status)){
                            if ($cat->custom->status != 0){
                                $array[]=[
                                    "id"=> $cat->id."",
                                    "parent" => $provider->id."",
                                    "text"=> $cat->title.'<a class="ml-2 jstree-link" href="'.route('admin.shop.access.custom', [$id, $cat->id]).'"><i class="fa fa-edit"></i> custom</a>',
                                    "state"=>[
                                        'opened' => false,
                                        'selected' => ($cat->custom->status??0) != 0
                                    ],
                                ];
                                if (($cat->custom->status??0) != 0) {
                                    $cat_selected[] = $cat->id;
                                }elseif (empty($cat->custom)) {
                                    GroupShop::firstOrCreate(['shop_id' => $id, 'group_id' => $cat->id]);
                                }
                            }
                        }


                    }
                }
            }
        }

        $categoryJson = json_encode($array);
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => "Phân quyền shop {$shop->title}"
        ];
        $page_breadcrumbs = $this->page_breadcrumbs;

        return view('admin.shop.access', compact('page_breadcrumbs', 'categoryJson', 'id', 'shop', 'cat_selected'));
    }

    function access_custom(Request $request, $id, $cat_id){
        $shop = Shop::findOrFail($id);
        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        if (!in_array($shop->id,$arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        if(!Auth::user()->can('client-access')) {
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        $cattegory = Group::findOrFail($cat_id);
        $data = GroupShop::firstOrCreate(['shop_id' => $id, 'group_id' => $cat_id]);
//      Check quyền chỉnh sửa custom.
        if (!Auth::user()->can('client-access-list') && ($data->status??0) == 0){
            return redirect()->route('admin.shop.access', $id)->withErrors(__('Không có quyền truy cập'));
        }

        if ($request->method() == 'POST') {
            $input = $request->all();
            if (!empty($input['image'])) {
                $input['image'] = explode('?', $input['image'])[0];
            }
            if(!empty($input['meta']['price'])) $input['meta']['price'] = intval(preg_replace("/[^0-9]/", "", $input['meta']['price']));
            if(!empty($input['meta']['price_old']))$input['meta']['price_old'] = intval(preg_replace("/[^0-9]/", "", $input['meta']['price_old']));

//            Log edit
            $c_input['price_before'] = $data->meta['price']??null;
            $c_input['price_after'] = $input['meta']['price'];
            $c_input['price_old_before'] = $data->meta['price_old']??null;
            $c_input['price_old_after'] = $input['meta']['price_old'];
            $c_input['title_before'] = $data->title??$cattegory->title;
            $c_input['title_after'] = $input['title'];
            $c_input['description_before'] = $data->description??$cattegory->description;
            $c_input['description_after'] = $input['description'];
            $c_input['seo_title_before'] = $data->seo_title??$cattegory->seo_title;
            $c_input['seo_title_after'] = $input['seo_title'];
            $c_input['seo_description_before'] = $data->seo_description??$cattegory->seo_description;
            $c_input['seo_description_after'] = $input['seo_description'];
            $c_input['content_before'] = $data->content??$cattegory->content;
            $c_input['content_after'] = $input['content'];
            $c_input['author_id'] = auth()->user()->id;
            $c_input['type'] = 0;
            $c_input['table_name'] = $data->getTable();
            $c_input['table_id'] = $data->id;
            $c_input['shop_id'] = $shop->id;

            if ($c_input['title_before'] == $c_input['title_after'] && $c_input['description_before'] == $c_input['description_after'] && $c_input['content_before'] == $c_input['content_after']){

            }else{
                LogEdit::create($c_input);
            }

            $data->fill($input)->save();
            if($request->filled('submit-close')){
                return redirect()->route('admin.shop.access.custom', [$id, $cat_id])->with('success',__('Cập nhật thành công !'));
            }else {
                return redirect()->route('admin.shop.access', $id)->with('success',__('Cập nhật thành công !'));
            }
        }
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => "Custom danh mục {$cattegory->title} shop {$shop->title}"
        ];
        $page_breadcrumbs = $this->page_breadcrumbs;
        $folder_image = "acc-category-".\Str::slug($shop->title);
//        return $cattegory;

        $table_name = $data->getTable();

//        Du lieu log edit

        $log_edit = LogEdit::where('table_name',$table_name)->with(array('author' => function ($query) {
            $query->select('id','username');
        }))->where('table_id',$data->id)->get();

        return view('admin.shop.access-custom', compact('page_breadcrumbs', 'data', 'id', 'cat_id', 'cattegory', 'shop', 'folder_image', 'log_edit'));
    }

    public function revision(Request $request,$id,$slug)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Revision"),
        ];

        $data = GroupShop::with(array('group' => function ($query) {
            $query->select('id');
        }))->findOrFail($id);


        $log = LogEdit::where('id',$slug)->with(array('author' => function ($query) {
            $query->select('id','username');
        }))->first();


        ActivityLog::add($request, 'Vào form revision nick #'.$data->id);
        return view('admin.shop.revision')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('log', $log)
            ->with('slug', $slug);

    }

    public function postRevision(Request $request,$id,$slug){

        $data = GroupShop::with(array('group' => function ($query) {
            $query->select('id');
        }))->findOrFail($id);

        $log = LogEdit::where('id',$slug)->with(array('author' => function ($query) {
            $query->select('id','username');
        }))->first();

//        update
        $input['title'] = $log->title_before;
        $input['seo_title'] = $log->seo_title_before;
        $input['description'] = $log->description_before;
        $input['seo_description'] = $log->seo_description_before;
        $input['content'] = $log->content_before;

//   Lưu log
        $c_input['title_before'] = $data->title;
        $c_input['title_after'] = $log->title_before;
        $c_input['description_before'] = $data->description;
        $c_input['description_after'] = $log->description_before;
        $c_input['seo_title_before'] = $data->seo_title;
        $c_input['seo_title_after'] = $log->seo_title_before;
        $c_input['seo_description_before'] = $data->seo_description;
        $c_input['seo_description_after'] = $log->seo_description_before;
        $c_input['content_before'] = $data->content;
        $c_input['content_after'] = $log->content_before;
        $c_input['author_id'] = auth()->user()->id;
        $c_input['type'] = 1;
        $c_input['table_name'] = $data->getTable();
        $c_input['table_id'] = $data->id;
        $c_input['shop_id'] = session('shop_id');

        LogEdit::create($c_input);

        $data->update($input);
        ActivityLog::add($request, 'Phục hồi bài viết thành công service #'.$data->id);

        return redirect()->route('admin.shop.access.custom', [$data->shop_id, $data->group_id])->with('success',__('Cập nhật thành công !'));
    }

    // Getter for the HTML menu builder

    function getHTMLCategory($menu)
    {
        return $this->buildMenu($menu);
    }

    function buildMenu($menu, $parent_id = 0)
    {
        $result = null;
        foreach ($menu as $item)
            if ($item->parent_id == $parent_id) {
                $result .= "<li class='dd-item nested-list-item' data-order='{$item->order}' data-id='{$item->id}'>
               <div class='dd-handle nested-list-handle'>
                 <span class='la la-arrows-alt'></span>
               </div>
               <div class='nested-list-content'>";
                if($parent_id!=0){
                    $result.="<div class=\"m-checkbox\">
                                     <label class=\"checkbox checkbox-outline\">
                                     <input  type=\"checkbox\" rel=\"{$item->id}\" class=\"children_of_{$item->parent_id}\">
                                       <span></span> <a href='https://".HTML::entities($item->domain)."' target='_blank'>".HTML::entities($item->domain)."</a>
                                     </label>
                                 </div>";


                }
                else{

                    $result.="<div class=\"m-checkbox\">
                                     <label class=\"checkbox checkbox-outline\">
                                     <input  type=\"checkbox\" rel=\"{$item->id}\" class=\"children_of_{$item->parent_id}\"  >
                                     <span></span> <a href='https://".HTML::entities($item->domain)."' target='_blank'>".HTML::entities($item->domain)."</a>
                                     </label>
                                 </div>";
                }
                $result .= "<div class='btnControll'>";
                if ($item->status == 1) {
                    $result .= "<a href='#' class=''  title='Active'><img src='" . asset('/assets/backend/images/check.png') . "' alt='Active' /></a>&nbsp;";
                } else {
                    $result .= "<a href='#' class='' title='Unactive'><img src='" . asset('/assets/backend/images/uncheck.png') . "' alt='Unactive' /></a>&nbsp;";
                }

                $result .= "<a href='" . route("admin.".$this->module.".edit",$item->id) . "' class='btn btn-sm btn-primary'>Sửa</a>
                     <a href=\"#\" class=\"btn btn-sm btn-danger  delete_toggle \" rel=\"{$item->id}\">
                                         Xóa
                     </a>
                 </div>
               </div>" . $this->buildMenu($menu, $item->id) . "</li>";
            }
        return $result ? "\n<ol class=\"dd-list\">\n$result</ol>\n" : null;
    }


    public function UpdateStatus(Request $request){
        $id = $request->id;
        $data = Shop::find($id);

        if ($data->status == 0){

            $mesageerror = '';
            $slage = 1;

            if (empty($data->ntn_partner_key)){

                $slage = 0;
                $mesageerror= 'Chưa có thông tin key nạp thẻ NTN!';

            }

            if (empty($data->ccc_partner_key)){

                if ($slage == 1){
                    $mesageerror = 'Chưa có thông tin key nạp thẻ CCC!';
                }else{
                    $mesageerror = $mesageerror.', Chưa có thông tin key nạp thẻ CCC!';
                }

                $slage = 0;

            }

            if (empty($data->ntn_partner_key_card)){

                if ($slage == 1){
                    $mesageerror = 'Chưa có thông tin key mua thẻ!';
                }else{
                    $mesageerror = $mesageerror.', Chưa có thông tin key mua thẻ!';
                }

                $slage = 0;

            }

            if (empty($data->tichhop_key)){

                if ($slage == 1){
                    $mesageerror = 'Chưa có thông tin key tích hợp!';
                }else{
                    $mesageerror = $mesageerror.', Chưa có thông tin key tích hợp!';
                }

                $slage = 0;
            }

            if ($slage == 0){
                return response()->json([
                    'message'=>__($mesageerror),
                    'status'=> 0
                ]);
            }
        }

        $old_status = $data->status;
        if($data->status == 1){
            $data->status = 0;
        }
        elseif($data->status == 0){
            $data->status = 1;
        }
        $data->save();
        $content = $data->domain.' đã chuyển về trạng thái '.config('module.shop.status.'.$data->status);
        ActivityLog::add($request, 'Cập nhật thành công '.$this->module.' #'.$data->id.' từ trạng thái '.config('module.sop.status.'.$old_status).' sang trạng thái '.config('module.shop.status.'.$data->status));
        return response()->json([
            'message'=>__($content),
            'status'=> 1
        ]);
    }

    public function UpdateServer(Request $request){
        $id = $request->id;
        $shop_item = Shop::find($id);
        $status =0;
        $message="";
        $newIP="";
        $idServer="";

        try{
            //$fileCookie = storage_path(\App\Library\Helpers::rand_string(20) . '.txt');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,fr-FR;q=0.6,fr;q=0.5',
                'Cache-Control: max-age=0',
                'Connection: keep-alive',
                'Upgrade-Insecure-Requests: 1',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36'

            ));
            $domain = "https://".$shop_item->domain."/api/ip";
            curl_setopt($ch, CURLOPT_URL, $domain);
            curl_setopt($ch, CURLOPT_COOKIEFILE, "");
            curl_setopt($ch, CURLOPT_COOKIEJAR, "");

            $ketqua = curl_exec($ch);
            $ketqua = json_decode($ketqua);
            try{
                if(isset($ketqua->ip) && $ketqua->ip != ""){
                    $ipweb = $ketqua->ip;
                    self::updateServerName($shop_item->id,$shop_item->domain,$ipweb);
                    $status = 1;
                    $message= "Success";
                }
                else{
                    $domain = "http://".$shop_item->domain."/api/ip";
                    curl_setopt($ch, CURLOPT_URL, $domain);
                    curl_setopt($ch, CURLOPT_COOKIEFILE, "");
                    curl_setopt($ch, CURLOPT_COOKIEJAR, "");
                    $ketqua = curl_exec($ch);
                    $ketqua = json_decode($ketqua);
                    if(isset($ketqua->ip) && $ketqua->ip != ""){
                        $ipweb = $ketqua->ip;
                        self::updateServerName($shop_item->id,$shop_item->domain,$ipweb);
                        $status = 1;
                        $message= "Success";
                    }else{
                        $ipweb = "0.0.0.0";
                        $myfile = fopen(storage_path() ."/logs/log-ThuCongServer.txt", "a") or die("Unable to open file!");
                        $txt = Carbon::now()."__Check  thu cong: Lỗi không lấy được IP shop: ".$shop_item->domain.":".$ipweb;
                        fwrite($myfile, $txt ."\n");
                        fclose($myfile);
                    }
                }
            }
            catch(\Exception $e){
                $ipweb = "0.0.0.0";
                $myfile = fopen(storage_path() ."/logs/log-ThuCongServer.txt", "a") or die("Unable to open file!");
                $txt = Carbon::now()."__Check  thu cong: Lỗi không lấy được IP shop: ".$shop_item->domain.":".$ipweb;
                fwrite($myfile, $txt ."\n");
                fclose($myfile);
            }
            $server = Server::where("ipaddress",$ipweb)->first();
            if($server){
                $idServer = $server->id;
            }
            curl_close($ch);
        }catch (\Exception $e) {
            Log::error($e);
            $myfile = fopen(storage_path() ."/logs/log-ThuCongServer.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().":Check thu cong: : Check that bai";
            fwrite($myfile, $txt ."\n");
            fclose($myfile);
        }


        return response()->json([
            'message'=>__("Success"),
            'new_ip'=>$ipweb,
            'idServer'=>$idServer,
            'status'=> 1
        ]);
    }


    public function updateServerName($id_shop,$web,$ip){
        $shop = Shop::where("domain",$web)->first();
        if($shop){
            $shop_id = $id_shop;
            $current_shop_server_id = 0;
            if( $shop->server_id != null && $shop->server_id > 0) {
                $current_shop_server_id = $shop->server_id;
            }
            //Lấy thông tin server
            $server = Server::where("ipaddress",$ip)->first();
            if($server){
                if($current_shop_server_id > 0) {
                    if ($current_shop_server_id != $server->id) {//Trường hợp 2 ID server khác nhau-Ghi log thay đổi server
                        //Cập nhật ID server vào Shop
                        $shop->server_id = $server->id;
                        $shop->save();
                        //Ghi log thay đổi
                        ActivityLog::create([
                            "shop_id" => $shop_id,
                            "user_id" => 1,
                            "prefix" => "admin",
                            "method" => "cronjob",
                            "description" => "Thay đổi ip server từ id #" . $current_shop_server_id . " sang id #" . $server->id,
                            "ip" => $ip,
                            "url" => $shop->domain
                        ]);
                    }
                }
                else{
                    $shop->server_id = $server->id;
                    $shop->save();
                    ActivityLog::create([
                        "shop_id"=>$shop_id,
                        "user_id"=>1,
                        "prefix" =>"admin",
                        "method" => "cronjob",
                        "description" =>"Cập nhật ip server id #".$server->id." cho shop",
                        "ip"=> $ip,
                        "url" => $shop->domain
                    ]);
                }
            }
            else{//Không có server, tạo mới server
                //Tạo mới server
                $new_server = Server::create([
                    'ipaddress' => $ip,
                    'type' => 1,
                    'status' => 1
                ]);
                //Cập nhật Server mới cho shop
                $shop->server_id = $new_server->id;
                $shop->save();
                //Ghi log thay đổi
                if($current_shop_server_id >0) {
                    ActivityLog::create([
                        "shop_id"=>$shop_id,
                        "user_id"=>1,
                        "prefix" =>"admin",
                        "method" => "cronjob",
                        "description" =>"Thay đổi ip server mới từ id #".$current_shop_server_id." sang id #".$new_server->id,
                        "ip"=> $ip,
                        "url" => $shop->domain
                    ]);
                }
                else{
                    ActivityLog::create([
                        "shop_id"=>$shop_id,
                        "user_id"=>1,
                        "prefix" =>"admin",
                        "method" => "cronjob",
                        "description" =>"Cập nhật ip server id #".$new_server->id." cho shop",
                        "ip"=> $ip,
                        "url" => $shop->domain
                    ]);
                }
            }
        }
    }

    public function RenderSecretKey(Request $request){
        $id = $request->id;
        $data =  Shop::findOrFail($id);
        $string = time().rand(100000,999999);
        $secret_key = Helpers::Encrypt($string,md5($data->id));
        $data->secret_key = $secret_key;
        $data->save();
        ActivityLog::add($request, 'Cập nhật secret_key thành công #'.$data->id);
        return response()->json([
            'message'=>__('Thành công'),
            'status'=> 1,
            'secret_key' => $data->secret_key
        ]);
    }

    public function delArrValues(array $arr, array $remove) {
        return array_filter($arr, fn($e) => !in_array($e, $remove));
    }

    public function getPartNer(Request $request, $id){
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Lấy thông tin nhà cung cấp")
        ];
        $data =  Shop::findOrFail($id);
        ActivityLog::add($request, 'Truy cập trang thông tin nhà cung cấp shop #'.$data->id);
        return view('admin.shop.item.partner')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data);
    }

    public function postPartNer(Request $request, $partner){
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required',
        ],[
            'shop_id.required' => __('ID shop bị thiếu'),
        ]);
        $shop = Shop::where('id',$request->shop_id)->first();
        if(!$shop){
            return response()->json([
                'message' => __('Shop không tồn tại.'),
                'status' => 0,
            ]);
        }
        if(strtolower($partner) == strtolower("ntn")){
            if(!$request->ntn_username){
                return response()->json([
                    'message' => __('Tên tài khoản trên NTN bị thiếu'),
                    'status' => 0,
                ]);
            }
            if(!$request->ntn_password){
                return response()->json([
                    'message' => __('Mật khẩu trên NTN bị thiếu'),
                    'status' => 0,
                ]);
            }
            $ntn_username = $request->ntn_username;
            $ntn_password = $request->ntn_password;
            $result = NAPTHENHANH::createUser($ntn_username,$ntn_password);
            if(empty($result)){
                return response()->json([
                    'message' => __('Gọi API NTN thất bại, vui lòng thử lại !'),
                    'status' => 0,
                ]);
            }
            if($result === "ERROR"){
                return response()->json([
                    'message' => __('Gọi API NTN thất bại, vui lòng thử lại !'),
                    'status' => 0,
                ]);
            }
            if(isset($result->status) && $result->status == 0){
                return response()->json([
                    'message' => $result->message,
                    'status' => 0,
                ]);
            }
            if(isset($result->status) && $result->status == 1){
                $shop->ntn_username = $result->username;
                $shop->ntn_partner_id = $result->id;
                $shop->ntn_partner_key = $result->partner_key;
                $shop->ntn_partner_key_card = $result->partner_key_card;
                $shop->save();
                ActivityLog::add($request, 'Gọi thành công API tạo tài khoản NTN #'.$shop->id);
                $shop->ntn_password = $ntn_password;
                return response()->json([
                    'message' => $result->message,
                    'status' => 1,
                ]);
            }
        }
        elseif(strtolower($partner) == strtolower("ccc")){
            if(!$request->ccc_username){
                return response()->json([
                    'message' => __('Tên tài khoản trên CCC bị thiếu'),
                    'status' => 0,
                ]);
            }
            if(!$request->ccc_password){
                return response()->json([
                    'message' => __('Mật khẩu trên CCC bị thiếu'),
                    'status' => 0,
                ]);
            }
            $ccc_username = $request->ccc_username;
            $ccc_password = $request->ccc_password;
            $result = CANCAUCOM::createUser($ccc_username,$ccc_password);
            if(empty($result)){
                return response()->json([
                    'message' => __('Gọi API CCC thất bại, vui lòng thử lại !'),
                    'status' => 0,
                ]);
            }
            if($result === "ERROR"){
                return response()->json([
                    'message' => __('Gọi API CCC thất bại, vui lòng thử lại !'),
                    'status' => 0,
                ]);
            }
            if(isset($result->status) && $result->status == 0){
                return response()->json([
                    'message' => $result->message,
                    'status' => 0,
                ]);
            }
            if(isset($result->status) && $result->status == 1){
                $shop->ccc_username = $result->username;
                $shop->ccc_partner_id = $result->id;
                $shop->ccc_partner_key = $result->partner_key;
                $shop->ntn_password = $ccc_password;
                $shop->save();
                ActivityLog::add($request, 'Gọi thành công API tạo tài khoản CCC #'.$shop->id);
                return response()->json([
                    'message' => $result->message,
                    'status' => 1,
                ]);
            }
        }
        else if(strtolower($partner) == strtolower("tichhop")){
            if(!$request->tichhop_username){
                return response()->json([
                    'message' => __('Tên tài khoản trên Tích hợp bị thiếu'),
                    'status' => 0,
                ]);
            }
            if(!$request->tichhop_password){
                return response()->json([
                    'message' => __('Mật khẩu trên Tích hợp bị thiếu'),
                    'status' => 0,
                ]);
            }
            $tichhop_username = $request->tichhop_username;
            $tichhop_password = $request->tichhop_password;
            $result = HelpItemAdd::createUser($tichhop_username,$tichhop_password);
            if(empty($result)){
                return response()->json([
                    'message' => __('Gọi API Tích hợp thất bại, vui lòng thử lại !'),
                    'status' => 0,
                ]);
            }
            if($result === "ERROR"){
                return response()->json([
                    'message' => __('Gọi API Tích hợp thất bại, vui lòng thử lại !'),
                    'status' => 0,
                ]);
            }
            if(isset($result->status) && $result->status == 0){
                return response()->json([
                    'message' => $result->message,
                    'status' => 0,
                ]);
            }
            if(isset($result->status) && $result->status == 1){
                $shop->tichhop_key = $result->tichhop_key;
                $shop->tichhop_username = $result->username;
                $shop->tichhop_password = $tichhop_password;
                $shop->save();
                ActivityLog::add($request, 'Gọi thành công API tạo tài khoản Tích hợp #'.$shop->id);
                return response()->json([
                    'message' => $result->message,
                    'status' => 1,
                ]);
            }
        }
        else if(strtolower($partner) == strtolower("daily")){
            if(!$request->daily_username){
                return response()->json([
                    'message' => __('Tên tài khoản trên Daily bị thiếu'),
                    'status' => 0,
                ]);
            }
            if(!$request->daily_password){
                return response()->json([
                    'message' => __('Mật khẩu trên Daily bị thiếu'),
                    'status' => 0,
                ]);
            }
            $daily_username = $request->daily_username;
            $daily_password = $request->daily_password;
            $result = HelperItemDaily::createUser($daily_username,$daily_password);
            if(empty($result)){
                return response()->json([
                    'message' => __('Gọi API Daily thất bại, vui lòng thử lại !'),
                    'status' => 0,
                ]);
            }
            if($result === "ERROR"){
                return response()->json([
                    'message' => __('Gọi API Daily thất bại, vui lòng thử lại !'),
                    'status' => 0,
                ]);
            }
            if(isset($result->status) && $result->status == 0){
                return response()->json([
                    'message' => $result->message,
                    'status' => 0,
                ]);
            }
            if(isset($result->status) && $result->status == 1){
                $shop->daily_username = $result->username;
                $shop->daily_partner_id = $result->id;
                $shop->daily_partner_key_service = $result->partner_key_service;
                $shop->save();
                ActivityLog::add($request, 'Gọi thành công API tạo tài khoản Daily #'.$shop->id);
                return response()->json([
                    'message' => $result->message,
                    'status' => 1,
                ]);
            }
        }
        elseif(strtolower($partner) == strtolower("paypaypay")){
            if(!$request->ppp_username){
                return response()->json([
                    'message' => __('Tên tài khoản trên PPP bị thiếu'),
                    'status' => 0,
                ]);
            }
            if(!$request->ppp_password){
                return response()->json([
                    'message' => __('Mật khẩu trên PPP bị thiếu'),
                    'status' => 0,
                ]);
            }
            $ppp_username = $request->ppp_username;
            $ppp_password = $request->ppp_password;
            $result = PAYPAYPAY::createUser($ppp_username,$ppp_password);
            if(empty($result)){
                return response()->json([
                    'message' => __('Gọi API PPP thất bại, vui lòng thử lại !'),
                    'status' => 0,
                ]);
            }
            if($result === "ERROR"){
                return response()->json([
                    'message' => __('Gọi API PPP thất bại, vui lòng thử lại !'),
                    'status' => 0,
                ]);
            }
            if(isset($result->status) && $result->status == 0){
                return response()->json([
                    'message' => $result->message,
                    'status' => 0,
                ]);
            }
            if(isset($result->status) && $result->status == 1){
                $shop->ppp_username = $result->username;
                $shop->ppp_partner_id = $result->id;
                $shop->ppp_partner_key = $result->partner_key;
                $shop->save();
                ActivityLog::add($request, 'Gọi thành công API tạo tài khoản PPP #'.$shop->id);
                return response()->json([
                    'message' => $result->message,
                    'status' => 1,
                ]);
            }
        }
        else{
            return response()->json([
                'message' => __('Dữ liệu không hợp lệ, vui lòng thử lại !'),
                'status' => 0,
            ]);
        }
    }

    public function getCheckPartNer(Request $request,$id){

        if($request->ajax()) {

            $shop = Shop::where('id',$id)->first();

            if (!$shop){
                return response()->json([
                    'status'=>0,
                    'message'=>__('Shop không tồn tại!'),
                ]);
            }

            if (empty($shop->ntn_partner_key)){
                return response()->json([
                    'status'=>0,
                    'message'=>__('Chưa có thông tin key nạp thẻ NTN!'),
                ]);
            }

            if (empty($shop->ccc_partner_key)){
                return response()->json([
                    'status'=>0,
                    'message'=>__('Chưa có thông tin key nạp thẻ CCC!'),
                ]);
            }

            if (empty($shop->ntn_partner_key_card)){
                return response()->json([
                    'status'=>0,
                    'message'=>__('Chưa có thông tin key mua thẻ!'),
                ]);
            }

            if (empty($shop->tichhop_key)){
                return response()->json([
                    'status'=>0,
                    'message'=>__('Chưa có thông tin key tích hợp!'),
                ]);
            }

            return response()->json([
                'status'=>1,
                'message'=>__('Đã đủ thông tin!'),
            ]);
        }
    }

    public function autosaveContent(Request $request){

        if (!$request->filled('shop_id')){
            return response()->json([
                'status'=>0,
                'message'=>__('Không tìm thấy bài viết !'),
            ]);
        }

        $shop_id = $request->get('shop_id');

        if (!$request->filled('nick_id')){
            return response()->json([
                'status'=>0,
                'message'=>__('Không tìm thấy danh mục custom !'),
            ]);
        }

        $nick_id = $request->get('nick_id');

        $shop = Shop::findOrFail($shop_id);

        if (!$request->filled('content')){
            return response()->json([
                'status'=>0,
                'message'=>__('Không tìm thấy nội dung bài viết !'),
            ]);
        }

        $content = $request->get('content');

        $cattegory = Group::findOrFail($nick_id);
        $data = GroupShop::where('shop_id',$shop->id)->where('group_id',$cattegory->id)->first();

        if (!$data){
            return response()->json([
                'status'=>0,
                'message'=>__('Không tìm thấy danh mục custom !'),
            ]);
        }

        $input['content'] = $content;

        $data->update($input);

        return response()->json([
            'status'=>1,
            'message'=>__('Lưu nội dung thành công !'),
        ]);

    }
}
