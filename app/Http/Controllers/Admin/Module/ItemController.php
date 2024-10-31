<?php

namespace App\Http\Controllers\Admin\Module;

use App\Http\Controllers\Admin\GoogleIndexing\GoogleIndexingController;
use App\Http\Controllers\Controller;
use App\Library\GoogleIndexing;
use App\Library\Helpers;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Item;
use App\Models\LogEdit;
use App\Models\Setting;
use App\Models\Shop;
use Carbon\Carbon;
use Google_Client;
use Google_Service_Indexing;
use Html;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Exception;
use Validator;

class ItemController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;

    public function __construct(Request $request)
    {

        $this->module=$request->segments()[1]??"";
        $this->moduleCategory=$this->module.'-category';

        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');
        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate']]);
        $this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);


        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'.index'),
                'title' => __(config('module.'.$this->module.'.title'))
            ];
        }

    }

    public function index(Request $request)
    {

        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);

        $setting_zip = null;

        if(session('shop_id')){
            $key = "sys_zip_shop";
            $setting_zip = Setting::getAllSettingsShopId(session('shop_id'));
        }

        $setting_zipv2 = null;

        if(session('shop_id')){
            $keyv2 = "sys_zip_setting_shop";
            $setting_zipv2 = Setting::getAllSettingsShopId(session('shop_id'))->where('name', $keyv2)->first();
        }


        if($request->ajax) {

            $datatable= Item::with(array('groups' => function ($query) {
                $query->where('module', $this->moduleCategory);

                $query->select('groups.id','title');
            }))->where('module', $this->module);

            if ($this->module == 'article'){
                $datatable =  $datatable->with('shop')->with('author');
            }

            if ($request->filled('author_id')) {

                $datatable->whereHas('author', function ($query) use ($request) {
                    $query->Where('username', 'LIKE', '%' . $request->get('author_id') . '%');
                });
            }

            if ($request->filled('group_id')) {

                $datatable->whereHas('groups', function ($query) use ($request) {
                    $query->where('group_id',$request->get('group_id'));
                });
            }

            if ($request->filled('author_id')) {

                $datatable->whereHas('author', function ($query) use ($request) {
                    $query->Where('username', 'LIKE', '%' . $request->get('author_id') . '%');
                });
            }


            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', $request->get('id'));
                    $q->orWhere('idkey',$request->get('id') );
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

            if ($request->filled('started_at')) {
                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }

            if (isset($setting_zip) && $this->module == 'article'){
                $datatable = $datatable->orderBy('published_at','desc');
            }

            if (isset($setting_zip)){

                if ($this->module == 'article'){
                    return \datatables()->eloquent($datatable)

                        ->only([
                            'id',

                            'title',
                            'module',
                            'slug',
                            'image',
                            'locale',
                            'groups',
                            'author',
                            'order',
                            'position',
                            'status',
                            'action',
                            'created_at',
                            'published_at',
                            'display_type',
                            'shop',
                        ])
                        ->editColumn('published_at', function($data) {
                            return date('d/m/Y H:i:s', strtotime($data->published_at));
                        })
                        ->editColumn('title', function($row) {
                            $http_url = \Request::server ("HTTP_HOST");
                            $temp = '';
                            if (isset($row->shop)){
                                $shop = $row->shop;

                                $key = "sys_zip_shop";
                                $c_setting_zip = '';
                                $setting_zips = Setting::getAllSettingsShopId($shop->id);

                                foreach ($setting_zips as $value){
                                    if ($value->name == $key && $shop->id == $value->shop_id){
                                        $c_setting_zip = $value->val;
                                    }
                                }

                                if (isset($c_setting_zip) && $c_setting_zip != ''){
                                    if ($c_setting_zip == '/blog'){
                                        $temp .="<a href=\"https://".$shop->domain.'/blog/'.$row->slug."\" title=\"".$row->title."\"  target='_blank'    >" .$row->title."</a>";
                                    }else{
                                        $temp .="<a href=\"https://".$shop->domain.'/tin-tuc/'.$row->slug."\" title=\"".$row->title."\"  target='_blank'    >" .$row->title."</a>";
                                    }
                                }else{
                                    $temp .="<a href=\"https://".$shop->domain.'/tin-tuc/'.$row->slug."\" title=\"".$row->title."\"  target='_blank'    >" .$row->title."</a>";
                                }
                            }else{
                                $temp .="<a href=\"https://".$http_url.'/'.$row->slug."\" title=\"".$row->title."\"  target='_blank'    >" .$row->title."</a>";
                            }

                            return $temp;
                        })
                        ->addColumn('action', function($row) {
                            $temp= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                            $temp.= "<a href=\"".route('admin.'.$this->module.'.duplicate',$row->id)."\"  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-primary' title=\"Nhân bản\"><i class=\"la la-copy\"></i></a>";
                            $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                            return $temp;
                        })
                        ->rawColumns(['action', 'title','published_at'])
                        ->toJson();
                }else{
                    if ($this->module == 'article'){
                        return \datatables()->eloquent($datatable)

                            ->only([
                                'id',
                                'shop',
                                'title',
                                'module',
                                'slug',
                                'image',
                                'locale',
                                'groups',
                                'author',
                                'order',
                                'position',
                                'status',
                                'action',
                                'created_at',
                                'published_at',
                                'display_type'

                            ])
                            ->editColumn('published_at', function($data) {
                                return date('d/m/Y H:i:s', strtotime($data->published_at));
                            })
                            ->editColumn('title', function($row) {

                                $http_url = \Request::server ("HTTP_HOST");
                                $temp = '';

                                if (isset($row->shop)){

                                    $shop = $row->shop;

                                    $key = "sys_zip_shop";

                                    $c_setting_zip = Setting::getAllSettingsShopId($shop->id)->where('name', $key)->first();

                                    if (isset($c_setting_zip) && $c_setting_zip != ''){
                                        if ($c_setting_zip == '/blog'){
                                            $temp .="<a href=\"https://".$shop->domain.'/blog/'.$row->slug."\" title=\"".$row->title."\"  target='_blank'    >" .$row->title."</a>";
                                        }else{
                                            $temp .="<a href=\"https://".$shop->domain.'/tin-tuc/'.$row->slug."\" title=\"".$row->title."\"  target='_blank'    >" .$row->title."</a>";
                                        }
                                    }else{
                                        $temp .="<a href=\"https://".$shop->domain.'/tin-tuc/'.$row->slug."\" title=\"".$row->title."\"  target='_blank'    >" .$row->title."</a>";
                                    }
                                }else{
                                    $temp .="<a href=\"https://".$http_url.'/'.$row->slug."\" title=\"".$row->title."\"  target='_blank'    >" .$row->title."</a>";
                                }

                                return $temp;
                            })
                            ->addColumn('action', function($row) {
                                $temp= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                                $temp.= "<a href=\"".route('admin.'.$this->module.'.duplicate',$row->id)."\"  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-primary' title=\"Nhân bản\"><i class=\"la la-copy\"></i></a>";
                                $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                                return $temp;
                            })
                            ->rawColumns(['action', 'title','published_at'])
                            ->toJson();
                    }else{

                        return \datatables()->eloquent($datatable)
                            ->only([
                                'id',
                                'shop',
                                'title',
                                'module',
                                'slug',
                                'image',
                                'locale',
                                'groups',
                                'author',
                                'order',
                                'position',
                                'status',
                                'action',
                                'created_at',
                                'published_at',
                                'display_type'

                            ])
                            ->editColumn('published_at', function($data) {
                                return date('d/m/Y H:i:s', strtotime($data->published_at));
                            })
                            ->editColumn('title', function($row) {
                                $temp = '';
                                $http_url = \Request::server ("HTTP_HOST");
                                $temp .="<a href=\"https://".$http_url.'/'.$row->slug."\" title=\"".$row->title."\"  target='_blank'    >" .$row->title."</a>";
                                return $temp;
                            })
                            ->addColumn('action', function($row) {
                                $temp= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                                $temp.= "<a href=\"".route('admin.'.$this->module.'.duplicate',$row->id)."\"  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-primary' title=\"Nhân bản\"><i class=\"la la-copy\"></i></a>";
                                $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                                return $temp;
                            })
                            ->rawColumns(['action', 'title','published_at'])
                            ->toJson();
                    }

                }


            }else{

                if ($this->module == 'article'){
                    return \datatables()->eloquent($datatable)

                        ->only([
                            'id',
                            'title',
                            'module',
                            'slug',
                            'image',
                            'locale',
                            'groups',
                            'author',
                            'order',
                            'position',
                            'status',
                            'action',
                            'created_at',
                            'published_at',
                            'display_type',
                            'shop',
                        ])
                        ->editColumn('published_at', function($data) {
                            return date('d/m/Y H:i:s', strtotime($data->published_at));
                        })
                        ->editColumn('title', function($row) {
                            $http_url = \Request::server ("HTTP_HOST");
                            $temp = '';

                            if (isset($row->shop)){
                                $shop = $row->shop;

                                $key = "sys_zip_shop";
                                $c_setting_zip = '';
                                $setting_zips = Setting::getAllSettingsShopId($shop->id);

                                foreach ($setting_zips as $value){

                                    if ($value->name == $key && $shop->id == $value->shop_id){
                                        $c_setting_zip = $value->val;
                                    }
                                }

                                if (isset($c_setting_zip) && $c_setting_zip != ''){
                                    if ($c_setting_zip == '/blog'){
                                        $temp .="<a href=\"https://".$shop->domain.'/blog/'.$row->slug."\" title=\"".$row->title."\"  target='_blank'    >" .$row->title."</a>";
                                    }else{
                                        $temp .="<a href=\"https://".$shop->domain.'/tin-tuc/'.$row->slug."\" title=\"".$row->title."\"  target='_blank'    >" .$row->title."</a>";
                                    }
                                }else{
                                    $temp .="<a href=\"https://".$shop->domain.'/tin-tuc/'.$row->slug."\" title=\"".$row->title."\"  target='_blank'    >" .$row->title."</a>";
                                }
                            }else{
                                $temp .="<a href=\"https://".$http_url.'/'.$row->slug."\" title=\"".$row->title."\"  target='_blank'    >" .$row->title."</a>";
                            }

                            return $temp;
                        })
                        ->addColumn('action', function($row) {
                            $temp= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                            $temp.= "<a href=\"".route('admin.'.$this->module.'.duplicate',$row->id)."\"  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-primary' title=\"Nhân bản\"><i class=\"la la-copy\"></i></a>";
                            $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                            return $temp;
                        })
                        ->rawColumns(['action', 'title','published_at'])
                        ->toJson();
                }else{
                    return \datatables()->eloquent($datatable)

                        ->only([
                            'id',
                            'shop',
                            'title',
                            'module',
                            'slug',
                            'image',
                            'locale',
                            'groups',
                            'author',
                            'order',
                            'position',
                            'status',
                            'action',
                            'created_at',
                            'published_at',
                            'display_type'

                        ])

                        ->editColumn('created_at', function($data) {
                            return date('d/m/Y H:i:s', strtotime($data->created_at));
                        })
                        ->editColumn('title', function($data) {
                            $temp = '';
                            $http_url = \Request::server ("HTTP_HOST");
                            $temp .="<a href=\"https://".$http_url.'/'.$data->slug."\" title=\"".$data->title."\"  target='_blank'    >" .$data->title."</a>";
                            return $temp;
                        })
                        ->addColumn('action', function($row) {
                            $temp= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                            $temp.= "<a href=\"".route('admin.'.$this->module.'.duplicate',$row->id)."\"  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-primary' title=\"Nhân bản\"><i class=\"la la-copy\"></i></a>";
                            $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                            return $temp;
                        })
                        ->rawColumns(['action', 'title','created_at'])
                        ->toJson();
                }

            }

        }

        $dataCategory = Group::where('module', '=',  $this->moduleCategory);

        if(session('shop_id')){
            $dataCategory->where('shop_id',session('shop_id'));
        }

        $dataCategory = $dataCategory->orderBy('order','asc')->get();

        $client = null;

        if(Auth::user()->account_type == 1){
            $client = Shop::orderBy('id','desc');
            $shop_access_user = Auth::user()->shop_access;
            if(isset($shop_access_user) && $shop_access_user !== "all"){
                $shop_access_user = json_decode($shop_access_user);
                $client = $client->whereIn('id',$shop_access_user);
            }
            $client = $client->select('id','domain','title')->get();
        }

        return view('admin.module.item.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('client', $client)
            ->with('setting_zip', $setting_zip)
            ->with('setting_zipv2', $setting_zipv2)
            ->with('dataCategory', $dataCategory);
    }

    public function create(Request $request)
    {

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $dataCategory = Group::where('module', '=',  $this->moduleCategory);

        if(session('shop_id')){
            $dataCategory->where('shop_id',session('shop_id'));
        }

        $dataCategory = $dataCategory->orderBy('order','asc')->get();

        $setting_zip = null;

        if(session('shop_id')){
            $key = "sys_zip_shop";
            $setting_zip = Setting::getAllSettingsShopId(session('shop_id'))->where('name', $key)->first();
        }

        $shop = Shop::where('id',session('shop_id'))->first();

        $secret_key = config('module.service.secret_key');
        $name_shop = Helpers::Encrypt(\Str::slug($shop->title),$secret_key);

        if($this->module == "advertise"){
            $folder_image = "advertise-config-".$name_shop;
        }else{
            $folder_image = "article-config-".$name_shop;
        }

        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.module.item.create_edit')
            ->with('module', $this->module)
            ->with('setting_zip', $setting_zip)
            ->with('folder_image', $folder_image)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('dataCategory', $dataCategory);

    }

    public function store(Request $request)
    {

        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
        ]);
        $input=$request->all();
        $input['module']=$this->module;
        $input['author_id']=auth()->user()->id;
        $input['price_old'] = (float)str_replace(array(' ', '.'), '', $request->price_old);
        $input['price'] = (float)str_replace(array(' ', '.'), '', $request->price);
        $input['percent_sale'] = (float)str_replace(array(' ', '.'), '', $request->percent_sale);

        if ($this->module == 'article' || $this->module == 'advertise'){
            $input['shop_id'] = session('shop_id');
        }


        $slug = $request->slug;

        for ($i = 0; $i < 100; $i++){
            if ($i == 0){

                $checkslug= Item::with(array('groups' => function ($query) {
                    $query->where('module', $this->moduleCategory);

                    $query->select('groups.id','title');
                }))->where('module', $this->module);

                if ($request->filled('shop_id')) {
                    $checkslug->where('shop_id', $request->get('shop_id'));
                }
                else{
                    if(session('shop_id')){
                        $checkslug->where('shop_id',session('shop_id'));
                    }
                    else{
                        if(isset(Auth::user()->shop_access) &&Auth::user()->shop_access !== "all"){
                            $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                            $checkslug->whereIn('shop_id',$shop_id_shop_access);
                        }
                    }
                }

                $checkslug = $checkslug->where('slug',$request->slug)->first();

                if (isset($checkslug)){
                    $slug = $slug.'-'.'1';
                }else{
                    break;
                }
            }else{
                $index = $i + 1;
                $checkslug= Item::with(array('groups' => function ($query) {
                    $query->where('module', $this->moduleCategory);

                    $query->select('groups.id','title');
                }))->where('module', $this->module);

                if ($request->filled('shop_id')) {
                    $checkslug->where('shop_id', $request->get('shop_id'));
                }
                else{
                    if(session('shop_id')){
                        $checkslug->where('shop_id',session('shop_id'));
                    }
                    else{
                        if(isset(Auth::user()->shop_access) &&Auth::user()->shop_access !== "all"){
                            $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                            $checkslug->whereIn('shop_id',$shop_id_shop_access);
                        }
                    }
                }

                $checkslug = $checkslug->where('slug',$slug)->first();

                if (isset($checkslug)){
                    $slug = $request->slug.'-'.$index;
                }else{
                    break;
                }
            }
        }

        $input['slug'] = $slug;
        //xử lý params
        if($request->filled('params')){
            //check value param ở đây nếu cần //Example:  $params['demo']='Value demo edited'
            $params = $request->params;

            $input['params'] = $params;
        }

        if ($this->module == 'article'){
            $params_plus = null;
            if($request->filled('first_question') && $request->filled('first_answer')){
                $params_first["first_question"] = $request->first_question;
                $params_first["first_answer"] = $request->first_answer;
                $params_input["first"] = json_encode($params_first);
            }

            if($request->filled('second_question') && $request->filled('second_answer')){
                $params_second["second_question"] = $request->second_question;
                $params_second["second_answer"] = $request->second_answer;
                $params_input["second"] = json_encode($params_second);
            }

            if($request->filled('three_question') && $request->filled('three_answer')){
                $params_three["three_question"] = $request->three_question;
                $params_three["three_answer"] = $request->three_answer;
                $params_input["three"] = json_encode($params_three);
            }

            if($request->filled('foor_question') && $request->filled('foor_answer')){
                $params_foor["foor_question"] = $request->foor_question;
                $params_foor["foor_answer"] = $request->foor_answer;
                $params_input["foor"] = json_encode($params_foor);
                $params_plus = json_encode($params_input);
            }

            $input['params_plus'] = $params_plus;
        }


        $data = Item::create($input);

        if($this->module == 'article'){
            $data->published_at = $data->created_at;
            $data->save();
        }


        if(session('shop_id') && $this->module == 'article'){
            $auth_config = Setting::get('sys_google_analytics');

            if (isset($auth_config) && $auth_config != ''){

                $domain = session('shop_name');

                $setting_zip = null;

                if(session('shop_id')){
                    $key = "sys_zip_shop";
                    $setting_zip = Setting::getAllSettingsShopId(session('shop_id'))->where('name', $key)->first();
                }

                if (isset($setting_zip)){
                    $url = 'https://'.$domain.'/blog/'.$data->slug.'';

                }else{
                    $url = 'https://'.$domain.'/tin-tuc/'.$data->slug.'';
                }


                $resuft = GoogleIndexing::create()->update($url);

                ActivityLog::add($request, 'Index google '.json_encode($resuft));

            }
        }

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

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $data = Item::where('module', '=', $this->module)->findOrFail($id);

        $dataCategory = Group::where('module', '=',  $this->moduleCategory);

        if(session('shop_id')){
            $dataCategory->where('shop_id',session('shop_id'));
        }

        $shop = Shop::where('id',session('shop_id'))->first();

        $table_name = $data->getTable();

        $log_edit = LogEdit::where('table_name',$table_name)->with(array('author' => function ($query) {
            $query->select('id','username');
        }))->where('table_id',$data->id)->get();

        $dataCategory = $dataCategory->orderBy('order','asc')->get();

        $setting_zip = null;

        if(session('shop_id')){
            $key = "sys_zip_shop";
            $setting_zip = Setting::getAllSettingsShopId(session('shop_id'))->where('name', $key)->first();
        }

        $secret_key = config('module.service.secret_key');
        $name_shop = Helpers::Encrypt(\Str::slug($shop->title),$secret_key);
        if($this->module == "advertise"){
            $folder_image = "advertise-config-".$name_shop;
        }else{
            $folder_image = "article-config-".$name_shop;
        }


        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        return view('admin.module.item.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('setting_zip', $setting_zip)
            ->with('log_edit', $log_edit)
            ->with('folder_image', $folder_image)
            ->with('dataCategory', $dataCategory);

    }

    public function update(Request $request,$id)
    {

        $data =  Item::where('module', '=', $this->module)->findOrFail($id);

        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
        ]);

        $input=$request->all();

        $input['module']=$this->module;
        if ($this->module == 'article'){

            $c_input['title_before'] = $data->title;
            $c_input['title_after'] = $input['title'];
            $c_input['description_before'] = $data->description;
            $c_input['description_after'] = $input['description'];
            $c_input['seo_title_before'] = $data->seo_title;
            $c_input['seo_title_after'] = $input['seo_title'];
            $c_input['seo_description_before'] = $data->seo_description;
            $c_input['seo_description_after'] = $input['seo_description'];
            $c_input['content_before'] = $data->content;
            $c_input['content_after'] = $input['content'];
            $c_input['author_id'] = auth()->user()->id;
            $c_input['type'] = 0;
            $c_input['table_name'] = $data->getTable();
            $c_input['table_id'] = $data->id;
            $shop_id = null;

            if(session('shop_id')){
                $shop_id = session('shop_id');
            }elseif (isset($data->shop_id)){
                $shop_id = $data->shop_id;
            }
            $c_input['shop_id'] = $shop_id;

            if ($c_input['title_before'] == $c_input['title_after'] && $c_input['description_before'] == $c_input['description_after'] && $c_input['content_before'] == $c_input['content_after']){

            }else{
                LogEdit::create($c_input);
            }

        }
        //xử lý params
        if($request->filled('params')){
            //check value param ở đây nếu cần //Example:  $params['demo']='Value demo edited'

            $params=$request->params;
            $input['params'] =$params;
        }
        if ($request->price){
            $input['price'] = preg_replace('/\./', '', $request->price);
        }
        if ($request->price_old){
            $input['price_old'] = preg_replace('/\./', '', $request->price_old);
        }
        if ($this->module == 'article'){
            $params_plus = null;
            if($request->filled('first_question') && $request->filled('first_answer')){

            }

            if($request->filled('second_question') && $request->filled('second_answer')){

            }

            if($request->filled('three_question') && $request->filled('three_answer')){

            }

            if($request->filled('foor_question') && $request->filled('foor_answer')){

            }
            $params_first["first_question"] = $request->first_question;
            $params_first["first_answer"] = $request->first_answer;
            $params_input["first"] = json_encode($params_first);
            $params_second["second_question"] = $request->second_question;
            $params_second["second_answer"] = $request->second_answer;
            $params_input["second"] = json_encode($params_second);
            $params_three["three_question"] = $request->three_question;
            $params_three["three_answer"] = $request->three_answer;
            $params_input["three"] = json_encode($params_three);
            $params_foor["foor_question"] = $request->foor_question;
            $params_foor["foor_answer"] = $request->foor_answer;
            $params_input["foor"] = json_encode($params_foor);
            $params_plus = json_encode($params_input);
            $input['params_plus'] = $params_plus;
        }

        $data->update($input);
        //set category

        if($this->module == 'article'){
            $data->published_at = $data->created_at;
            $data->save();
        }

        if( isset($input['group_id'] ) &&  $input['group_id']!=0){
            $data->groups()->sync($input['group_id']);
        }
        else{
            $data->groups()->sync([]);
        }

        if(session('shop_id') && $this->module == 'article'){

            $auth_config = Setting::get('sys_google_analytics');

            if (isset($auth_config) && $auth_config != ''){

                $domain = session('shop_name');

                $setting_zip = null;

                if(session('shop_id')){
                    $key = "sys_zip_shop";
                    $setting_zip = Setting::getAllSettingsShopId(session('shop_id'))->where('name', $key)->first();
                }

                if (isset($setting_zip)){
                    $url = 'https://'.$domain.'/blog/'.$data->slug.'';
                }else{
                    $url = 'https://'.$domain.'/tin-tuc/'.$data->slug.'';
                }

                $resuft = GoogleIndexing::create()->update($url);

                ActivityLog::add($request, 'Index google '.json_encode($resuft));

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

    public function revision(Request $request,$id,$slug)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Revision"),
        ];

        $data = Item::where('module', '=', $this->module)->findOrFail($id);

        $dataCategory = Group::where('module', '=',  $this->moduleCategory);

        if(session('shop_id')){
            $dataCategory->where('shop_id',session('shop_id'));
        }

        $log = LogEdit::where('id',$slug)->with(array('author' => function ($query) {
            $query->select('id','username');
        }))->first();

        $dataCategory = $dataCategory->orderBy('order','asc')->get();

        ActivityLog::add($request, 'Vào form revision '.$this->module.' #'.$data->id);
        return view('admin.module.item.revision')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('log', $log)
            ->with('slug', $slug)
            ->with('dataCategory', $dataCategory);

    }

    public function postRevision(Request $request,$id,$slug){

        $data =  Item::where('module', '=', $this->module)->findOrFail($id);

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
        $shop_id = null;

        if(session('shop_id')){
            $shop_id = session('shop_id');
        }elseif (isset($data->shop_id)){
            $shop_id = $data->shop_id;
        }

        $c_input['shop_id'] = $shop_id;

        LogEdit::create($c_input);

        $data->update($input);

        ActivityLog::add($request, 'Phục hồi bài viết thành công '.$this->module.' #'.$data->id);

        return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Phục hồi thành công !'));
    }

    public function destroy(Request $request)
    {
        $input=explode(',',$request->id);

        Item::where('module','=',$this->module)->whereIn('id',$input)
            ->where('shop_id',session('shop_id'))
            ->delete();
        ActivityLog::add($request, 'Xóa thành công '.$this->module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Xóa thành công !'));
    }

    public function duplicate(Request $request,$id)
    {

        $data= Item::where('module', '=', $this->module)->find($id);
        if(!$data){
            return redirect()->back()->withErrors(__('Không tìm thấy dữ liệu để nhân bản'));
        }
        $dataGroup= $data->groups()->get()->pluck(['id']);

        $dataNew = $data->replicate();
        $dataNew->title=$dataNew->title." (".((int)$data->duplicate+1) .")";
        $dataNew->slug=$dataNew->slug."-".((int)$data->duplicate+1);
        $dataNew->duplicate=0;
        $dataNew->author_id= auth()->user()->id;
        $dataNew->is_slug_override=0;

        $dataNew->save();
        //set group cho dataNew
        $dataNew->groups()->sync($dataGroup);

        //update data old plus 1 count version
        $data->duplicate=(int)$data->duplicate+1;
        $data->save();

        if(session('shop_id') && $this->module == 'article'){
            $auth_config = Setting::get('sys_google_analytics');

            if (isset($auth_config) && $auth_config != ''){
                $domain = session('shop_name');
                $url = 'https://'.$domain.'/tin-tuc/'.$data->slug.'';

                $resuft = GoogleIndexing::create()->update($url);

                ActivityLog::add($request, 'Index google '.json_encode($resuft));

            }
        }

        ActivityLog::add($request, 'Nhân bản '.$this->module.' #'.$data->id ."thành #".$dataNew->id);
        return redirect()->back()->with('success',__('Nhân bản thành công'));


    }

    public function zipItem(Request $request){

        ini_set('max_execution_time', 120); //2 minutes

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        if(!isset($request->route_article)){
            return redirect()->back()->withErrors(__('Vui lòng chọn link dẫn !'));
        }

        $r_router = $request->route_article;

        if ($r_router == 0){
            $val = "/tin-tuc";
        }else{
            $val = "/blog";
        }

        $shop_id = session()->get('shop_id');
        $shop =  Shop::where('id',$shop_id)->first();
        $media_url = config('module.media.url').'/storage/upload/images/leech';

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

                    $headers = get_headers($host.$image, 1);
                    if ($headers[0] == 'HTTP/1.1 200 OK') {
                        $cdn_image = $host.$image;
                    }else{
                        $cdn_image = $host.'/storage'.$image;
                    }
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
                        $input_i['image'] = '/storage/upload/images/leech'.$value_i->image;
                        $input_i['published_at'] = $value_i->created_at;
                        $input_i['display_type'] = 1;

                        $item = Item::create($input_i);

                        $item->groups()->attach($group->id);

                        // xử lý ảnh đại diện
                        if(isset($value_i->image)){

                            $image_i = $value_i->image;
                            $headers = get_headers($host.$image_i, 1);
                            if ($headers[0] == 'HTTP/1.1 200 OK') {
                                $cdn_image_i = $host.$image_i;
                            }else{
                                $cdn_image_i = $host.'/storage'.$image_i;
                            }

                            $this->__makeImage($image_i,$cdn_image_i);
//                            $url = 'https://v1.napgamegiare.net/storage/images/XX6haYFFhv_1634958641.jpg';
//                            dd(get_headers($url, 1));
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


            $sys_zip_shop = Setting::updateOrCreate(['name' => 'sys_zip_shop','shop_id' => $shop->id], [
                'val' => $val,
                'type' => Setting::getDataType('sys_zip_shop')
            ]);

            Setting::add($key, $val, Setting::getDataType($key));

            return redirect()->back()->with('success',__('Zip thành công !'));
        }else{
            return redirect()->back()->with('error',__('Zip shop thất bại !'));
        }

    }

    public function zipSetting(Request $request){

        ini_set('max_execution_time', 240); //2 minutes

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $shop_id = session()->get('shop_id');
        $shop =  Shop::where('id',$shop_id)->first();
        $media_url = config('module.media.url').'/storage/upload/images/leech';

        $host = 'https://'.$shop->domain;
//        $host = "https://banthegarena.net";
        $path_url = "/api/get-getSetting";
        $url = $host.$path_url;

//        $url = 'https://banthegarena.net/api/get-getArticle';

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

        if (isset($result)){
            if ($httpcode == 200 && $result->status == 1 && isset($result->data)){

                if (isset($result->data->value)){
                    $data_g = $result->data->value;
                    $data_g = json_decode($data_g);

                    $sys_title = Setting::updateOrCreate(['name' => 'sys_title','shop_id' => $shop->id], [
                        'val' => $data_g->SETTING_SYSTEM_TITLE,
                        'type' => Setting::getDataType('sys_title')
                    ]);

                    $sys_description = Setting::updateOrCreate(['name' => 'sys_description','shop_id' => $shop->id], [
                        'val' => $data_g->SETTING_SYSTEM_DESCRIPTION,
                        'type' => Setting::getDataType('sys_description')
                    ]);

                    $sys_keyword = Setting::updateOrCreate(['name' => 'sys_keyword','shop_id' => $shop->id], [
                        'val' => $data_g->SETTING_SYSTEM_KEYWORD,
                        'type' => Setting::getDataType('sys_keyword')
                    ]);

                    $sys_fanpage = Setting::updateOrCreate(['name' => 'sys_fanpage','shop_id' => $shop->id], [
                        'val' => $data_g->SETTING_SYSTEM_FANPAGE_FACEBOOK,
                        'type' => Setting::getDataType('sys_fanpage')
                    ]);

                    $str_head = $data_g->SETTING_GOOGLE_TASK_MANAGER_HEAD;
                    $strv2_head = str_replace(' ', '', $str_head);

                    $whatIWant_head = strpos($strv2_head,'GTM');
                    $whatIWant_head = substr($strv2_head,$whatIWant_head,11);

                    $sys_google_tag_manager_head = Setting::updateOrCreate(['name' => 'sys_google_tag_manager_head','shop_id' => $shop->id], [
                        'val' => $whatIWant_head,
                        'type' => Setting::getDataType('sys_google_tag_manager_head')
                    ]);

                    $str_body = $data_g->SETTING_GOOGLE_TASK_MANAGER_BODY;
                    $strv2_body = str_replace(' ', '', $str_body);

                    $whatIWant_body = strpos($strv2_body,'GTM');
                    $whatIWant_body = substr($strv2_body,$whatIWant_body,11);

                    $sys_google_tag_manager_body = Setting::updateOrCreate(['name' => 'sys_google_tag_manager_body','shop_id' => $shop->id], [
                        'val' => $whatIWant_body,
                        'type' => Setting::getDataType('sys_google_tag_manager_body')
                    ]);

                    $str = $data_g->SETTING_SYSTEM_MESSAGER;
                    $strv2 = str_replace(' ', '', $str);

                    $whatIWant = strpos($strv2,'page_id') + 10;
                    $whatIWant = substr($strv2,$whatIWant,15);

                    $sys_id_chat_message = Setting::updateOrCreate(['name' => 'sys_id_chat_message','shop_id' => $shop->id], [
                        'val' => $whatIWant,
                        'type' => Setting::getDataType('sys_id_chat_message')
                    ]);

                    $sys_favicon = Setting::updateOrCreate(['name' => 'sys_favicon','shop_id' => $shop->id], [
                        'val' => '/storage/upload/images/leech'.$data_g->SETTING_SYSTEM_FAVICON,
                        'type' => Setting::getDataType('sys_favicon')
                    ]);

                    if(isset($data_g->SETTING_SYSTEM_FAVICON)){
                        $image_sys_favicon = $data_g->SETTING_SYSTEM_FAVICON;

                        $headers = get_headers($host.$image_sys_favicon, 1);
                        if ($headers[0] == 'HTTP/1.1 200 OK') {
                            $cdn_image_sys_favicon = $host.$image_sys_favicon;
                        }else{
                            $cdn_image_sys_favicon = $host.'/storage'.$image_sys_favicon;
                        }

//                $cdn_image_sys_favicon = $host.$image_sys_favicon;
                        $this->__makeImage($image_sys_favicon,$cdn_image_sys_favicon);
                    }

                    $sys_logo = Setting::updateOrCreate(['name' => 'sys_logo','shop_id' => $shop->id], [
                        'val' => '/storage/upload/images/leech'.$data_g->SETTING_SYSTEM_LOGO,
                        'type' => Setting::getDataType('sys_logo')
                    ]);

                    if(isset($data_g->SETTING_SYSTEM_LOGO)){
                        $image_sys_logo = $data_g->SETTING_SYSTEM_LOGO;

                        $headers_lg = get_headers($host.$image_sys_logo, 1);
                        if ($headers_lg[0] == 'HTTP/1.1 200 OK') {
                            $cdn_image_sys_logo = $host.$image_sys_logo;
                        }else{
                            $cdn_image_sys_logo = $host.'/storage'.$image_sys_logo;
                        }

                        $this->__makeImage($image_sys_logo,$cdn_image_sys_logo);
                    }

                    // xử lý intro text
                    $content = $data_g->SETTING_SYSTEM_INTRO_TEXT;
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

                            $sys_intro_text = Setting::updateOrCreate(['name' => 'sys_intro_text','shop_id' => $shop->id], [
                                'val' => $content_replace_img,
                                'type' => Setting::getDataType('sys_intro_text')
                            ]);

                        }
                    }

//            Xử lý footer

                    $content_f = $data_g->SETTING_SYSTEM_FOOTER_CONTENT1;
                    $content_replace_img_f = $content_f;
                    $image_article_f = $this->__parseImages($content_f);
                    if(isset($image_article_f) && count($image_article_f)){
                        if(isset($image_article_f[1])){
                            $image_article_url_f = $image_article_f[1];
                            foreach($image_article_url_f as $key_url_f => $item_image_article_url_f){
                                // trường hợp ảnh không có https
                                if(!str_contains($item_image_article_url_f, 'https://')){

                                    $url_save_f = $media_url.$item_image_article_url_f;
                                    $image_article_url_f = $item_image_article_url_f;
                                    $cdn_image_article_f = $host.$image_article_url_f;
                                    $this->__makeImage($image_article_url_f,$cdn_image_article_f);
                                }
                                // trường hợp ảnh có https
                                else{
                                    $parse_article_url_f = parse_url($item_image_article_url_f);
                                    $url_save_f = $media_url.$parse_article_url_f['path'];
                                    $image_article_url_f = $parse_article_url_f['path']??null;
                                    $cdn_image_article_f = $item_image_article_url_f;
                                    $this->__makeImage($image_article_url_f,$cdn_image_article_f);
                                }
                                $content_replace_img_f = str_replace($item_image_article_url_f,$url_save_f,$content_replace_img_f);
                            }

                            $sys_intro_text = Setting::updateOrCreate(['name' => 'sys_footer','shop_id' => $shop->id], [
                                'val' => $content_replace_img_f,
                                'type' => Setting::getDataType('sys_footer')
                            ]);

                        }
                    }

                    $key = "sys_zip_setting_shop";
                    $val = "/blogsetting";

                    $sys_zip_shop = Setting::updateOrCreate(['name' => 'sys_zip_setting_shop','shop_id' => $shop->id], [
                        'val' => $val,
                        'type' => Setting::getDataType('sys_zip_setting_shop')
                    ]);
                }else{

                    foreach ($result->data as $key => $data_g) {

                        if ($data_g->key == "SETTING_SYSTEM_TITLE"){
                            $sys_title = Setting::updateOrCreate(['name' => 'sys_title', 'shop_id' => $shop->id], [
                                'val' => $data_g->value,
                                'type' => Setting::getDataType('sys_title')
                            ]);
                        }


                        if ($data_g->key == "SETTING_SYSTEM_DESCRIPTION"){
                            $sys_description = Setting::updateOrCreate(['name' => 'sys_description', 'shop_id' => $shop->id], [
                                'val' => $data_g->value,
                                'type' => Setting::getDataType('sys_description')
                            ]);
                        }

                        if ($data_g->key == "SETTING_SYSTEM_KEYWORD"){
                            $sys_keyword = Setting::updateOrCreate(['name' => 'sys_keyword', 'shop_id' => $shop->id], [
                                'val' => $data_g->value,
                                'type' => Setting::getDataType('sys_keyword')
                            ]);
                        }

                        if ($data_g->key == "SETTING_SYSTEM_FANPAGE_FACEBOOK"){
                            $sys_fanpage = Setting::updateOrCreate(['name' => 'sys_fanpage', 'shop_id' => $shop->id], [
                                'val' => $data_g->value,
                                'type' => Setting::getDataType('sys_fanpage')
                            ]);
                        }

                        if ($data_g->key == "SETTING_GOOGLE_TASK_MANAGER_HEAD"){
                            $str_head = $data_g->value;
                            $strv2_head = str_replace(' ', '', $str_head);

                            $whatIWant_head = strpos($strv2_head, 'GTM');
                            $whatIWant_head = substr($strv2_head, $whatIWant_head, 11);

                            $sys_google_tag_manager_head = Setting::updateOrCreate(['name' => 'sys_google_tag_manager_head', 'shop_id' => $shop->id], [
                                'val' => $whatIWant_head,
                                'type' => Setting::getDataType('sys_google_tag_manager_head')
                            ]);
                        }

                        if ($data_g->key == "SETTING_GOOGLE_TASK_MANAGER_BODY"){
                            $str_body = $data_g->value;
                            $strv2_body = str_replace(' ', '', $str_body);

                            $whatIWant_body = strpos($strv2_body, 'GTM');
                            $whatIWant_body = substr($strv2_body, $whatIWant_body, 11);

                            $sys_google_tag_manager_body = Setting::updateOrCreate(['name' => 'sys_google_tag_manager_body', 'shop_id' => $shop->id], [
                                'val' => $whatIWant_body,
                                'type' => Setting::getDataType('sys_google_tag_manager_body')
                            ]);
                        }

                        if ($data_g->key == "SETTING_SYSTEM_MESSAGER"){
                            $str = $data_g->value;
                            $strv2 = str_replace(' ', '', $str);

                            $whatIWant = strpos($strv2, 'page_id') + 9;
                            $whatIWant = substr($strv2, $whatIWant, 15);

                            $sys_id_chat_message = Setting::updateOrCreate(['name' => 'sys_id_chat_message', 'shop_id' => $shop->id], [
                                'val' => $whatIWant,
                                'type' => Setting::getDataType('sys_id_chat_message')
                            ]);
                        }



                        if ($data_g->key == "SETTING_SYSTEM_FAVICON"){

                            $sys_favicon = Setting::updateOrCreate(['name' => 'sys_favicon', 'shop_id' => $shop->id], [
                                'val' => '/storage/upload/images/leech' . $data_g->value,
                                'type' => Setting::getDataType('sys_favicon')
                            ]);

                            if (isset($data_g->value)) {
                                $image_sys_favicon = $data_g->value;

                                $headers = get_headers($host . $image_sys_favicon, 1);
                                if ($headers[0] == 'HTTP/1.1 200 OK') {
                                    $cdn_image_sys_favicon = $host . $image_sys_favicon;
                                } else {
                                    $cdn_image_sys_favicon = $host . '/storage' . $image_sys_favicon;
                                }

//                $cdn_image_sys_favicon = $host.$image_sys_favicon;
                                $this->__makeImage($image_sys_favicon, $cdn_image_sys_favicon);
                            }
                        }

                        if ($data_g->key == "SETTING_SYSTEM_LOGO"){
                            $sys_logo = Setting::updateOrCreate(['name' => 'sys_logo', 'shop_id' => $shop->id], [
                                'val' => '/storage/upload/images/leech' . $data_g->value,
                                'type' => Setting::getDataType('sys_logo')
                            ]);

                            if (isset($data_g->value)) {
                                $image_sys_logo = $data_g->value;

                                $headers_lg = get_headers($host . $image_sys_logo, 1);
                                if ($headers_lg[0] == 'HTTP/1.1 200 OK') {
                                    $cdn_image_sys_logo = $host . $image_sys_logo;
                                } else {
                                    $cdn_image_sys_logo = $host . '/storage' . $image_sys_logo;
                                }

                                $this->__makeImage($image_sys_logo, $cdn_image_sys_logo);
                            }
                        }

                        if ($data_g->key == "SETTING_SYSTEM_INTRO_TEXT"){
                            // xử lý intro text
                            $content = $data_g->value;
                            $content_replace_img = $content;
                            $image_article = $this->__parseImages($content);
                            if (isset($image_article) && count($image_article)) {
                                if (isset($image_article[1])) {
                                    $image_article_url = $image_article[1];
                                    foreach ($image_article_url as $key_url => $item_image_article_url) {
                                        // trường hợp ảnh không có https
                                        if (!str_contains($item_image_article_url, 'https://')) {

                                            $url_save = $media_url . $item_image_article_url;
                                            $image_article_url = $item_image_article_url;
                                            $cdn_image_article = $host . $image_article_url;
                                            $this->__makeImage($image_article_url, $cdn_image_article);
                                        } // trường hợp ảnh có https
                                        else {
                                            $parse_article_url = parse_url($item_image_article_url);
                                            $url_save = $media_url . $parse_article_url['path'];
                                            $image_article_url = $parse_article_url['path'] ?? null;
                                            $cdn_image_article = $item_image_article_url;
                                            $this->__makeImage($image_article_url, $cdn_image_article);
                                        }
                                        $content_replace_img = str_replace($item_image_article_url, $url_save, $content_replace_img);
                                    }

                                    $sys_intro_text = Setting::updateOrCreate(['name' => 'sys_intro_text', 'shop_id' => $shop->id], [
                                        'val' => $content_replace_img,
                                        'type' => Setting::getDataType('sys_intro_text')
                                    ]);

                                }
                            }
                        }


//            Xử lý footer

                        if ($data_g->key == "SETTING_SYSTEM_FOOTER_CONTENT1"){
                            $content_f = $data_g->value;
                            $content_replace_img_f = $content_f;
                            $image_article_f = $this->__parseImages($content_f);
                            if (isset($image_article_f) && count($image_article_f)) {
                                if (isset($image_article_f[1])) {
                                    $image_article_url_f = $image_article_f[1];
                                    foreach ($image_article_url_f as $key_url_f => $item_image_article_url_f) {
                                        // trường hợp ảnh không có https
                                        if (!str_contains($item_image_article_url_f, 'https://')) {

                                            $url_save_f = $media_url . $item_image_article_url_f;
                                            $image_article_url_f = $item_image_article_url_f;
                                            $cdn_image_article_f = $host . $image_article_url_f;
                                            $this->__makeImage($image_article_url_f, $cdn_image_article_f);
                                        } // trường hợp ảnh có https
                                        else {
                                            $parse_article_url_f = parse_url($item_image_article_url_f);
                                            $url_save_f = $media_url . $parse_article_url_f['path'];
                                            $image_article_url_f = $parse_article_url_f['path'] ?? null;
                                            $cdn_image_article_f = $item_image_article_url_f;
                                            $this->__makeImage($image_article_url_f, $cdn_image_article_f);
                                        }
                                        $content_replace_img_f = str_replace($item_image_article_url_f, $url_save_f, $content_replace_img_f);
                                    }

                                    $sys_intro_text = Setting::updateOrCreate(['name' => 'sys_footer', 'shop_id' => $shop->id], [
                                        'val' => $content_replace_img_f,
                                        'type' => Setting::getDataType('sys_footer')
                                    ]);

                                }
                            }
                        }

                    }

                    $key = "sys_zip_setting_shop";
                    $val = "/blogsetting";

                    $sys_zip_shop = Setting::updateOrCreate(['name' => 'sys_zip_setting_shop','shop_id' => $shop->id], [
                        'val' => $val,
                        'type' => Setting::getDataType('sys_zip_setting_shop')
                    ]);

                }

                return redirect()->back()->with('success',__('Zip thành công !'));
            }else{
                return redirect()->back()->with('error',__('Zip shop thất bại !'));
            }
        }else{
            return redirect()->back()->with('error',__('Zip shop thất bại !'));
        }

    }

    public function zipItemV1(Request $request){

        ini_set('max_execution_time', 120); //2 minutes

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        if(!isset($request->route_article)){
            return redirect()->back()->withErrors(__('Vui lòng chọn link dẫn !'));
        }

        $r_router = $request->route_article;

        if ($r_router == 0){
            $val = "/tin-tuc";
        }else{
            $val = "/blog";
        }

        $shop_id = session()->get('shop_id');
        $shop =  Shop::where('id',$shop_id)->first();
        $media_url = config('module.media.url').'/storage/upload/images/leech';

        $host = 'https://v1.'.$shop->domain;
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

                    $headers = get_headers($host.$image, 1);
                    if ($headers[0] == 'HTTP/1.1 200 OK') {
                        $cdn_image = $host.$image;
                    }else{
                        $cdn_image = $host.'/storage'.$image;
                    }
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
                        $input_i['image'] = '/storage/upload/images/leech'.$value_i->image;
                        $input_i['published_at'] = $value_i->created_at;
                        $input_i['display_type'] = 1;

                        $item = Item::create($input_i);

                        $item->groups()->attach($group->id);

                        // xử lý ảnh đại diện
                        if(isset($value_i->image)){

                            $image_i = $value_i->image;
                            $headers = get_headers($host.$image_i, 1);
                            if ($headers[0] == 'HTTP/1.1 200 OK') {
                                $cdn_image_i = $host.$image_i;
                            }else{
                                $cdn_image_i = $host.'/storage'.$image_i;
                            }

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


            $sys_zip_shop = Setting::updateOrCreate(['name' => 'sys_zip_shop','shop_id' => $shop->id], [
                'val' => $val,
                'type' => Setting::getDataType('sys_zip_shop')
            ]);

            Setting::add($key, $val, Setting::getDataType($key));

            return redirect()->back()->with('success',__('Zip thành công !'));
        }else{
            return redirect()->back()->with('error',__('Zip shop thất bại !'));
        }

    }

    public function zipSettingV1(Request $request){

        ini_set('max_execution_time', 240); //2 minutes

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $shop_id = session()->get('shop_id');
        $shop =  Shop::where('id',$shop_id)->first();
        $media_url = config('module.media.url').'/storage/upload/images/leech';

        $host = 'https://v1.'.$shop->domain;
//        $host = "https://banthegarena.net";
        $path_url = "/api/get-getSetting";
        $url = $host.$path_url;

//        $url = 'https://banthegarena.net/api/get-getArticle';

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

        if (isset($result)){
            if ($httpcode == 200 && $result->status == 1 && isset($result->data)){

                if (isset($result->data->value)){
                    $data_g = $result->data->value;
                    $data_g = json_decode($data_g);

                    $sys_title = Setting::updateOrCreate(['name' => 'sys_title','shop_id' => $shop->id], [
                        'val' => $data_g->SETTING_SYSTEM_TITLE,
                        'type' => Setting::getDataType('sys_title')
                    ]);

                    $sys_description = Setting::updateOrCreate(['name' => 'sys_description','shop_id' => $shop->id], [
                        'val' => $data_g->SETTING_SYSTEM_DESCRIPTION,
                        'type' => Setting::getDataType('sys_description')
                    ]);

                    $sys_keyword = Setting::updateOrCreate(['name' => 'sys_keyword','shop_id' => $shop->id], [
                        'val' => $data_g->SETTING_SYSTEM_KEYWORD,
                        'type' => Setting::getDataType('sys_keyword')
                    ]);

                    $sys_fanpage = Setting::updateOrCreate(['name' => 'sys_fanpage','shop_id' => $shop->id], [
                        'val' => $data_g->SETTING_SYSTEM_FANPAGE_FACEBOOK,
                        'type' => Setting::getDataType('sys_fanpage')
                    ]);

                    $str_head = $data_g->SETTING_GOOGLE_TASK_MANAGER_HEAD;
                    $strv2_head = str_replace(' ', '', $str_head);

                    $whatIWant_head = strpos($strv2_head,'GTM');
                    $whatIWant_head = substr($strv2_head,$whatIWant_head,11);

                    $sys_google_tag_manager_head = Setting::updateOrCreate(['name' => 'sys_google_tag_manager_head','shop_id' => $shop->id], [
                        'val' => $whatIWant_head,
                        'type' => Setting::getDataType('sys_google_tag_manager_head')
                    ]);

                    $str_body = $data_g->SETTING_GOOGLE_TASK_MANAGER_BODY;
                    $strv2_body = str_replace(' ', '', $str_body);

                    $whatIWant_body = strpos($strv2_body,'GTM');
                    $whatIWant_body = substr($strv2_body,$whatIWant_body,11);

                    $sys_google_tag_manager_body = Setting::updateOrCreate(['name' => 'sys_google_tag_manager_body','shop_id' => $shop->id], [
                        'val' => $whatIWant_body,
                        'type' => Setting::getDataType('sys_google_tag_manager_body')
                    ]);

                    $str = $data_g->SETTING_SYSTEM_MESSAGER;
                    $strv2 = str_replace(' ', '', $str);

                    $whatIWant = strpos($strv2,'page_id') + 10;
                    $whatIWant = substr($strv2,$whatIWant,15);

                    $sys_id_chat_message = Setting::updateOrCreate(['name' => 'sys_id_chat_message','shop_id' => $shop->id], [
                        'val' => $whatIWant,
                        'type' => Setting::getDataType('sys_id_chat_message')
                    ]);

                    $sys_favicon = Setting::updateOrCreate(['name' => 'sys_favicon','shop_id' => $shop->id], [
                        'val' => '/storage/upload/images/leech'.$data_g->SETTING_SYSTEM_FAVICON,
                        'type' => Setting::getDataType('sys_favicon')
                    ]);

                    if(isset($data_g->SETTING_SYSTEM_FAVICON)){
                        $image_sys_favicon = $data_g->SETTING_SYSTEM_FAVICON;

                        $headers = get_headers($host.$image_sys_favicon, 1);
                        if ($headers[0] == 'HTTP/1.1 200 OK') {
                            $cdn_image_sys_favicon = $host.$image_sys_favicon;
                        }else{
                            $cdn_image_sys_favicon = $host.'/storage'.$image_sys_favicon;
                        }

//                $cdn_image_sys_favicon = $host.$image_sys_favicon;
                        $this->__makeImage($image_sys_favicon,$cdn_image_sys_favicon);
                    }

                    $sys_logo = Setting::updateOrCreate(['name' => 'sys_logo','shop_id' => $shop->id], [
                        'val' => '/storage/upload/images/leech'.$data_g->SETTING_SYSTEM_LOGO,
                        'type' => Setting::getDataType('sys_logo')
                    ]);

                    if(isset($data_g->SETTING_SYSTEM_LOGO)){
                        $image_sys_logo = $data_g->SETTING_SYSTEM_LOGO;

                        $headers_lg = get_headers($host.$image_sys_logo, 1);
                        if ($headers_lg[0] == 'HTTP/1.1 200 OK') {
                            $cdn_image_sys_logo = $host.$image_sys_logo;
                        }else{
                            $cdn_image_sys_logo = $host.'/storage'.$image_sys_logo;
                        }

                        $this->__makeImage($image_sys_logo,$cdn_image_sys_logo);
                    }

                    // xử lý intro text
                    $content = $data_g->SETTING_SYSTEM_INTRO_TEXT;
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

                            $sys_intro_text = Setting::updateOrCreate(['name' => 'sys_intro_text','shop_id' => $shop->id], [
                                'val' => $content_replace_img,
                                'type' => Setting::getDataType('sys_intro_text')
                            ]);

                        }
                    }

//            Xử lý footer

                    $content_f = $data_g->SETTING_SYSTEM_FOOTER_CONTENT1;
                    $content_replace_img_f = $content_f;
                    $image_article_f = $this->__parseImages($content_f);
                    if(isset($image_article_f) && count($image_article_f)){
                        if(isset($image_article_f[1])){
                            $image_article_url_f = $image_article_f[1];
                            foreach($image_article_url_f as $key_url_f => $item_image_article_url_f){
                                // trường hợp ảnh không có https
                                if(!str_contains($item_image_article_url_f, 'https://')){

                                    $url_save_f = $media_url.$item_image_article_url_f;
                                    $image_article_url_f = $item_image_article_url_f;
                                    $cdn_image_article_f = $host.$image_article_url_f;
                                    $this->__makeImage($image_article_url_f,$cdn_image_article_f);
                                }
                                // trường hợp ảnh có https
                                else{
                                    $parse_article_url_f = parse_url($item_image_article_url_f);
                                    $url_save_f = $media_url.$parse_article_url_f['path'];
                                    $image_article_url_f = $parse_article_url_f['path']??null;
                                    $cdn_image_article_f = $item_image_article_url_f;
                                    $this->__makeImage($image_article_url_f,$cdn_image_article_f);
                                }
                                $content_replace_img_f = str_replace($item_image_article_url_f,$url_save_f,$content_replace_img_f);
                            }

                            $sys_intro_text = Setting::updateOrCreate(['name' => 'sys_footer','shop_id' => $shop->id], [
                                'val' => $content_replace_img_f,
                                'type' => Setting::getDataType('sys_footer')
                            ]);

                        }
                    }

                    $key = "sys_zip_setting_shop";
                    $val = "/blogsetting";

                    $sys_zip_shop = Setting::updateOrCreate(['name' => 'sys_zip_setting_shop','shop_id' => $shop->id], [
                        'val' => $val,
                        'type' => Setting::getDataType('sys_zip_setting_shop')
                    ]);
                }else{

                    foreach ($result->data as $key => $data_g) {

                        if ($data_g->key == "SETTING_SYSTEM_TITLE"){
                            $sys_title = Setting::updateOrCreate(['name' => 'sys_title', 'shop_id' => $shop->id], [
                                'val' => $data_g->value,
                                'type' => Setting::getDataType('sys_title')
                            ]);
                        }


                        if ($data_g->key == "SETTING_SYSTEM_DESCRIPTION"){
                            $sys_description = Setting::updateOrCreate(['name' => 'sys_description', 'shop_id' => $shop->id], [
                                'val' => $data_g->value,
                                'type' => Setting::getDataType('sys_description')
                            ]);
                        }

                        if ($data_g->key == "SETTING_SYSTEM_KEYWORD"){
                            $sys_keyword = Setting::updateOrCreate(['name' => 'sys_keyword', 'shop_id' => $shop->id], [
                                'val' => $data_g->value,
                                'type' => Setting::getDataType('sys_keyword')
                            ]);
                        }

                        if ($data_g->key == "SETTING_SYSTEM_FANPAGE_FACEBOOK"){
                            $sys_fanpage = Setting::updateOrCreate(['name' => 'sys_fanpage', 'shop_id' => $shop->id], [
                                'val' => $data_g->value,
                                'type' => Setting::getDataType('sys_fanpage')
                            ]);
                        }

                        if ($data_g->key == "SETTING_GOOGLE_TASK_MANAGER_HEAD"){
                            $str_head = $data_g->value;
                            $strv2_head = str_replace(' ', '', $str_head);

                            $whatIWant_head = strpos($strv2_head, 'GTM');
                            $whatIWant_head = substr($strv2_head, $whatIWant_head, 11);

                            $sys_google_tag_manager_head = Setting::updateOrCreate(['name' => 'sys_google_tag_manager_head', 'shop_id' => $shop->id], [
                                'val' => $whatIWant_head,
                                'type' => Setting::getDataType('sys_google_tag_manager_head')
                            ]);
                        }

                        if ($data_g->key == "SETTING_GOOGLE_TASK_MANAGER_BODY"){
                            $str_body = $data_g->value;
                            $strv2_body = str_replace(' ', '', $str_body);

                            $whatIWant_body = strpos($strv2_body, 'GTM');
                            $whatIWant_body = substr($strv2_body, $whatIWant_body, 11);

                            $sys_google_tag_manager_body = Setting::updateOrCreate(['name' => 'sys_google_tag_manager_body', 'shop_id' => $shop->id], [
                                'val' => $whatIWant_body,
                                'type' => Setting::getDataType('sys_google_tag_manager_body')
                            ]);
                        }

                        if ($data_g->key == "SETTING_SYSTEM_MESSAGER"){
                            $str = $data_g->value;
                            $strv2 = str_replace(' ', '', $str);

                            $whatIWant = strpos($strv2, 'page_id') + 9;
                            $whatIWant = substr($strv2, $whatIWant, 15);

                            $sys_id_chat_message = Setting::updateOrCreate(['name' => 'sys_id_chat_message', 'shop_id' => $shop->id], [
                                'val' => $whatIWant,
                                'type' => Setting::getDataType('sys_id_chat_message')
                            ]);
                        }



                        if ($data_g->key == "SETTING_SYSTEM_FAVICON"){
                            $sys_favicon = Setting::updateOrCreate(['name' => 'sys_favicon', 'shop_id' => $shop->id], [
                                'val' => '/storage/upload/images/leech' . $data_g->value,
                                'type' => Setting::getDataType('sys_favicon')
                            ]);

                            if (isset($data_g->value)) {
                                $image_sys_favicon = $data_g->value;

                                $headers = get_headers($host . $image_sys_favicon, 1);
                                if ($headers[0] == 'HTTP/1.1 200 OK') {
                                    $cdn_image_sys_favicon = $host . $image_sys_favicon;
                                } else {
                                    $cdn_image_sys_favicon = $host . '/storage' . $image_sys_favicon;
                                }

//                $cdn_image_sys_favicon = $host.$image_sys_favicon;
                                $this->__makeImage($image_sys_favicon, $cdn_image_sys_favicon);
                            }
                        }

                        if ($data_g->key == "SETTING_SYSTEM_LOGO"){
                            $sys_logo = Setting::updateOrCreate(['name' => 'sys_logo', 'shop_id' => $shop->id], [
                                'val' => '/storage/upload/images/leech' . $data_g->value,
                                'type' => Setting::getDataType('sys_logo')
                            ]);

                            if (isset($data_g->value)) {
                                $image_sys_logo = $data_g->value;

                                $headers_lg = get_headers($host . $image_sys_logo, 1);
                                if ($headers_lg[0] == 'HTTP/1.1 200 OK') {
                                    $cdn_image_sys_logo = $host . $image_sys_logo;
                                } else {
                                    $cdn_image_sys_logo = $host . '/storage' . $image_sys_logo;
                                }

                                $this->__makeImage($image_sys_logo, $cdn_image_sys_logo);
                            }
                        }

                        if ($data_g->key == "SETTING_SYSTEM_INTRO_TEXT"){
                            // xử lý intro text
                            $content = $data_g->value;
                            $content_replace_img = $content;
                            $image_article = $this->__parseImages($content);
                            if (isset($image_article) && count($image_article)) {
                                if (isset($image_article[1])) {
                                    $image_article_url = $image_article[1];
                                    foreach ($image_article_url as $key_url => $item_image_article_url) {
                                        // trường hợp ảnh không có https
                                        if (!str_contains($item_image_article_url, 'https://')) {

                                            $url_save = $media_url . $item_image_article_url;
                                            $image_article_url = $item_image_article_url;
                                            $cdn_image_article = $host . $image_article_url;
                                            $this->__makeImage($image_article_url, $cdn_image_article);
                                        } // trường hợp ảnh có https
                                        else {
                                            $parse_article_url = parse_url($item_image_article_url);
                                            $url_save = $media_url . $parse_article_url['path'];
                                            $image_article_url = $parse_article_url['path'] ?? null;
                                            $cdn_image_article = $item_image_article_url;
                                            $this->__makeImage($image_article_url, $cdn_image_article);
                                        }
                                        $content_replace_img = str_replace($item_image_article_url, $url_save, $content_replace_img);
                                    }

                                    $sys_intro_text = Setting::updateOrCreate(['name' => 'sys_intro_text', 'shop_id' => $shop->id], [
                                        'val' => $content_replace_img,
                                        'type' => Setting::getDataType('sys_intro_text')
                                    ]);

                                }
                            }
                        }


//            Xử lý footer

                        if ($data_g->key == "SETTING_SYSTEM_FOOTER_CONTENT1"){
                            $content_f = $data_g->value;
                            $content_replace_img_f = $content_f;
                            $image_article_f = $this->__parseImages($content_f);
                            if (isset($image_article_f) && count($image_article_f)) {
                                if (isset($image_article_f[1])) {
                                    $image_article_url_f = $image_article_f[1];
                                    foreach ($image_article_url_f as $key_url_f => $item_image_article_url_f) {
                                        // trường hợp ảnh không có https
                                        if (!str_contains($item_image_article_url_f, 'https://')) {

                                            $url_save_f = $media_url . $item_image_article_url_f;
                                            $image_article_url_f = $item_image_article_url_f;
                                            $cdn_image_article_f = $host . $image_article_url_f;
                                            $this->__makeImage($image_article_url_f, $cdn_image_article_f);
                                        } // trường hợp ảnh có https
                                        else {
                                            $parse_article_url_f = parse_url($item_image_article_url_f);
                                            $url_save_f = $media_url . $parse_article_url_f['path'];
                                            $image_article_url_f = $parse_article_url_f['path'] ?? null;
                                            $cdn_image_article_f = $item_image_article_url_f;
                                            $this->__makeImage($image_article_url_f, $cdn_image_article_f);
                                        }
                                        $content_replace_img_f = str_replace($item_image_article_url_f, $url_save_f, $content_replace_img_f);
                                    }

                                    $sys_intro_text = Setting::updateOrCreate(['name' => 'sys_footer', 'shop_id' => $shop->id], [
                                        'val' => $content_replace_img_f,
                                        'type' => Setting::getDataType('sys_footer')
                                    ]);

                                }
                            }
                        }

                    }

                    $key = "sys_zip_setting_shop";
                    $val = "/blogsetting";

                    $sys_zip_shop = Setting::updateOrCreate(['name' => 'sys_zip_setting_shop','shop_id' => $shop->id], [
                        'val' => $val,
                        'type' => Setting::getDataType('sys_zip_setting_shop')
                    ]);
                }

                return redirect()->back()->with('success',__('Zip thành công !'));
            }else{
                return redirect()->back()->with('error',__('Zip shop thất bại !'));
            }
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
        if (!is_dir(storage_path('app/public/upload/images/leech'.$dir))) {
            mkdir(storage_path('app/public/upload/images/leech'.$dir), 0755, true);
        }
        try{
            if(!file_exists(storage_path('app/public/upload/images/leech'.$dir.'/'.$image_name))){
                file_put_contents(storage_path('app/public/upload/images/leech'.$dir.'/'.$image_name), file_get_contents($cdn_image));
            }
        }
        catch (\Exception $e){
            return null;
        }

        return storage_path('app/public/upload/images/leech'.$dir.'/'.$image_name);
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

    public function cloneItem(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'shop_access' => 'required',
        ],[
            'shop_access.required' => "Vui lòng chọn shop cần clone",
        ]);

        $module = $request->module;


        if ($module == 'article'){

            $inputId =explode(',',$request->id);

            if($validator->fails()){
                return redirect()->back()->withErrors(__('Vui lòng chọn shop cần clone'));
            }

            if(session('shop_id')){}else{
                return redirect()->back()->withErrors(__('Vui lòng chọn shop để clone'));
            }


            $data = Item::with('groups')->where('module', $module)->whereIn('id',$inputId)->get();

            $module_category = $module.'-category';

            $shops = $request->shop_access;

            $datacategory = Group::where('module', $module_category)
                ->where('shop_id','=', session('shop_id'))
                ->where('status', '=', 1)
                ->whereHas('items', function ($query) use ($inputId) {
                    $query->whereIn('.items.id',  $inputId);
                })->get();

            foreach ($shops as $shop_id){

                //        Kiểm tra danh mục shop cần clone.

                foreach ($datacategory as $cate){

                    $group = Group::where('module','=','article-category')->where('id',$cate->id)->first();

                    $check_groups = Group::where('module','=','article-category')->where('shop_id',$shop_id)->where('title',$group->title)->first();

                    if (!isset($check_groups)){

                        $group_new = $group->replicate()->fill(
                            [
                                'shop_id' => $shop_id,
                                'author_id' => auth()->user()->id,
                                'created_at' => Carbon::now(),
                            ]
                        );
                        $group_new->save();

                    }
                }

//        clone bài viết:

                foreach ($data as $itemi){

                    $vali = Item::with(array('groups' => function ($query) {
                        $query->where('groups.module','article-category');
                    }))->where('module', $module)->where('id',$itemi->id)->first();

                    $item_newi = $vali->replicate()->fill(
                        [
                            'shop_id' => $shop_id,
                            'author_id' => auth()->user()->id,
                            'created_at' => Carbon::now(),
                        ]
                    );

                    if (isset($vali->groups[0])){

                        $checkgroup = Group::where('module','=','article-category')->where('slug',$vali->groups[0]->slug??'')->where('shop_id',$shop_id)->first();

                        if (isset($checkgroup)){
                            $item_newi->save();

                            $item_newi->groups()->attach($checkgroup->id);

                        }
                    }

                }
            }

            ActivityLog::add($request, 'Nhân bản '.$module ."thành #");
            return redirect()->back()->with('success',__('Nhân bản thành công'));
        }elseif ($module == 'advertise'){
            $inputId =explode(',',$request->id);

            if($validator->fails()){
                return redirect()->back()->withErrors(__('Vui lòng chọn shop cần clone'));
            }

            if(session('shop_id')){}else{
                return redirect()->back()->withErrors(__('Vui lòng chọn shop để clone'));
            }


            $data = Item::where('module', $module)->whereIn('id',$inputId)->get();

            $shops = $request->shop_access;

            foreach ($shops as $shop_id){

//        clone bài viết:
                foreach ($data as $itemi){
                    $vali = Item::where('module', $module)->where('id',$itemi->id)->first();

                    $item_newi = $vali->replicate()->fill(
                        [
                            'shop_id' => $shop_id,
                            'author_id' => auth()->user()->id,
                            'created_at' => Carbon::now(),
                        ]
                    );

                    $item_newi->save();

                }
            }

            ActivityLog::add($request, 'Nhân bản '.$module ."thành #");
            return redirect()->back()->with('success',__('Nhân bản thành công'));
        }


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


        $data=Item::where('module','=',$this->module)->whereIn('id',$input)->update([
            $field=>$value
        ]);

        ActivityLog::add($request, 'Cập nhật field thành công '.$this->module.' '.json_encode($whitelist).' #'.json_encode($input));

        return response()->json([
            'success'=>true,
            'message'=>__('Cập nhật thành công !'),
            'redirect'=>''
        ]);

    }

    // AJAX Reordering function
    public function order(Request $request)
    {


        $source = e($request->get('source'));
        $destination = $request->get('destination');

        $item = Group::where('module', '=', $this->module)->find($source);
        $item->parent_id = isset($destination)?$destination:0;
        $item->save();

        $ordering = json_decode($request->get('order'));

        $rootOrdering = json_decode($request->get('rootOrder'));

        if ($ordering) {
            foreach ($ordering as $order => $item_id) {
                if ($itemToOrder = Group::where('module', '=', $this->module)->find($item_id)) {
                    $itemToOrder->order = $order;
                    $itemToOrder->save();
                }
            }
        } else {
            foreach ($rootOrdering as $order => $item_id) {
                if ($itemToOrder = Group::where('module', '=', $this->module)->find($item_id)) {
                    $itemToOrder->order = $order;
                    $itemToOrder->save();
                }
            }
        }
        ActivityLog::add($request, 'Thay đổi STT thành công '.$this->module.' #'.$item->id);
        return 'ok ';
    }

    public function switchImage(Request $request){

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $shop =  Shop::where('id',session('shop_id'))->first();

        if (!$request->filled('youtube_link')){
            return redirect()->back()->withErrors(__('Vui lòng điền link youtube !'));
        }

        $youtube_link = $request->get('youtube_link');

        $articles = Item::where('module','article')->where('status',1)->where('shop_id',$shop->id)->get();
        $new_youtube = 'src="'.$youtube_link.'"';

        foreach ($articles as $article){
            $article = Item::where('module','article')
                ->where('id',$article->id)
                ->where('status',1)->where('shop_id',$shop->id)->first();
            $content = $article->content;
            $links = null;
            preg_match_all('@src="([^"]+)"@', $content , $links);
            $youtube = null;
            if (isset($links) && count($links)){
                foreach ($links as $key => $link){
                    if (isset($link) && count($link)){
                        foreach ($link as $val){
                            if (strpos($val, 'https://www.youtube.com') > -1){

                                $youtube =  $val;
                            }

                        }
                    }

                }
            }

            if (isset($youtube)){
                $new_content = str_replace($youtube, $youtube_link, $content);
                $article->content = $new_content;
                $article->save();
            }
        }

//        $media_url = config('module.media.url').'/storage';
//        $media_url_new = config('module.media.url').'/storage/upload/images/leech';
////        $media_url_new_one = config('module.media.url').'/storage/upload/images/leech';
//        $host = 'https://'.$shop->domain;
//
//
////        Setting.
//
//        $settingall = Setting::getAllSettingsShopId(session('shop_id'));
//
//        foreach ($settingall as $setting){
//
//            if (isset($setting->name) && $setting->name == 'sys_favicon'){
//
//                $image_stt = $setting->val;
//
//                if (str_contains($image_stt, '/storage/images/') && !str_contains($image_stt, 'https://') && !str_contains($image_stt,'/upload/images/leech/')){
//
//                    $image_body_stt = strpos($image_stt,'/images/');
//                    $image_body_stt = substr($image_stt, $image_body_stt, 1100);
//
//                    $image_stt = '/storage/upload/images/leech'.$image_body_stt;
//
//
//                }elseif (str_contains($image_stt, '/storage/upload-usr/images/') && !str_contains($image_stt, 'https://')  && !str_contains($image_stt,'/upload/images/leech/')){
//                    $image_body_stt = strpos($image_stt,'/upload-usr/images/');
//                    $image_body_stt = substr($image_stt, $image_body_stt, 1100);
//
//                    $image_stt = '/storage/upload/images/leech'.$image_body_stt;
//                }
//
//                Setting::updateOrCreate(['name' => 'sys_favicon','shop_id' => session('shop_id')], [
//                    'val' => $image_stt,
//                    'type' => Setting::getDataType('sys_favicon')
//                ]);
//
//            }elseif (isset($setting->name) && $setting->name == 'sys_logo'){
//                $image_stt = $setting->val;
//
//                if (str_contains($image_stt, '/storage/images/') && !str_contains($image_stt, 'https://')  && !str_contains($image_stt,'/upload/images/leech/')){
//
//                    $image_body_stt = strpos($image_stt,'/images/');
//                    $image_body_stt = substr($image_stt, $image_body_stt, 1100);
//
//                    $image_stt = '/storage/upload/images/leech'.$image_body_stt;
//
//
//                }elseif (str_contains($image_stt, '/storage/upload-usr/images/') && !str_contains($image_stt, 'https://')  && !str_contains($image_stt,'/upload/images/leech/')){
//                    $image_body_stt = strpos($image_stt,'/upload-usr/images/');
//                    $image_body_stt = substr($image_stt, $image_body_stt, 1100);
//
//                    $image_stt = '/storage/upload/images/leech'.$image_body_stt;
//                }
//
//                Setting::updateOrCreate(['name' => 'sys_logo','shop_id' => session('shop_id')], [
//                    'val' => $image_stt,
//                    'type' => Setting::getDataType('sys_logo')
//                ]);
//            }elseif (isset($setting->name) && $setting->name == 'sys_intro_text'){
//                $content_stt  = $setting->val;
//
//                $content_replace_img_stt = $content_stt;
//                $image_article_stt = $this->__parseImages($content_stt);
//
//                if(isset($image_article_stt) && count($image_article_stt)){
//                    if(isset($image_article_stt[1])){
//                        $image_article_url_stt = $image_article_stt[1];
//
//                        foreach($image_article_url_stt as $key_url_stt => $item_image_article_url_stt){
//                            // trường hợp ảnh không có https
//                            if(str_contains($item_image_article_url_stt, '/storage/upload/userfiles/images/')  && !str_contains($item_image_article_url_stt,'/upload/images/leech/')){
//
//                                $whatIWant_body_stt = strpos($item_image_article_url_stt,'/upload/userfiles/');
//                                $whatIWant_body_stt = substr($item_image_article_url_stt, $whatIWant_body_stt, 1100);
//
//                                $url_save_stt = $media_url_new.$whatIWant_body_stt;
//
//                                $content_replace_img_stt = str_replace($item_image_article_url_stt,$url_save_stt,$content_replace_img_stt);
//                            }
//
//                        }
////                    return $content_replace_img;
//                        $content_stt = $content_replace_img_stt;
//
//                        Setting::updateOrCreate(['name' => 'sys_intro_text', 'shop_id' => session('shop_id')], [
//                            'val' => $content_stt,
//                            'type' => Setting::getDataType('sys_intro_text')
//                        ]);
//                    }
//                }
//            }elseif (isset($setting->name) && $setting->name == 'sys_footer'){
//                $content_stt  = $setting->val;
//
//                $content_replace_img_stt = $content_stt;
//                $image_article_stt = $this->__parseImages($content_stt);
//
//                if(isset($image_article_stt) && count($image_article_stt)){
//                    if(isset($image_article_stt[1])){
//                        $image_article_url_stt = $image_article_stt[1];
//
//                        foreach($image_article_url_stt as $key_url_stt => $item_image_article_url_stt){
//                            // trường hợp ảnh không có https
//                            if(str_contains($item_image_article_url_stt, '/storage/upload/userfiles/images/') && !str_contains($item_image_article_url_stt,'/upload/images/leech/')){
//
//                                $whatIWant_body_stt = strpos($item_image_article_url_stt,'/upload/userfiles/');
//                                $whatIWant_body_stt = substr($item_image_article_url_stt, $whatIWant_body_stt, 1100);
//
//                                $url_save_stt = $media_url_new.$whatIWant_body_stt;
//
//                                $content_replace_img_stt = str_replace($item_image_article_url_stt,$url_save_stt,$content_replace_img_stt);
//                            }
//
//                        }
////                    return $content_replace_img;
//                        $content_stt = $content_replace_img_stt;
//
//                        Setting::updateOrCreate(['name' => 'sys_footer', 'shop_id' => session('shop_id')], [
//                            'val' => $content_stt,
//                            'type' => Setting::getDataType('sys_footer')
//                        ]);
//                    }
//                }
//            }
//
//        }
//
////        Bài viết.
//
//        $data = Item::where('module', 'article')->where('shop_id',session('shop_id'))->where('status',1)->get();
//
//        foreach ($data as $item){
//
//            $image = $item->image;
//
//            if (str_contains($image, '/storage/images/') && !str_contains($image, 'https://') && !str_contains($image,'/upload/images/leech/')){
//
//                $image_body = strpos($image,'/images/');
//                $image_body = substr($image, $image_body, 1100);
//
//                $image = '/storage/upload/images/leech'.$image_body;
//
//
//            }elseif (str_contains($image, '/storage/upload-usr/images/') && !str_contains($image, 'https://') && !str_contains($image,'/upload/images/leech/')){
//                $image_body = strpos($image,'/upload-usr/images/');
//                $image_body = substr($image, $image_body, 1100);
//
//                $image = '/storage/upload/images/leech'.$image_body;
//            }
//
//            $item->image = $image;
//
//            $content  = $item->content;
//
//            $content_replace_img = $content;
//            $image_article = $this->__parseImages($content);
//
//            if(isset($image_article) && count($image_article)){
//                if(isset($image_article[1])){
//                    $image_article_url = $image_article[1];
//
//                    foreach($image_article_url as $key_url => $item_image_article_url){
//                        // trường hợp ảnh không có https
//                        if(str_contains($item_image_article_url, '/storage/upload/userfiles/images/') && !str_contains($item_image_article_url,'/upload/images/leech/')){
//
//                            $whatIWant_body = strpos($item_image_article_url,'/upload/userfiles/');
//                            $whatIWant_body = substr($item_image_article_url, $whatIWant_body, 1100);
//
//                            $url_save = $media_url_new.$whatIWant_body;
//                            $content_replace_img = str_replace($item_image_article_url,$url_save,$content_replace_img);
//                        }
//
//                    }
////
//                    $item->content = $content_replace_img;
//
//                    $item->save();
//                }
//            }
//
//        }

        return redirect()->back()->with('success',__('Đổi thành công !'));

    }

    function __makeSwichImage($image,$cdn_image){
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

    public function autosaveContent (Request $request){

        if (!$request->filled('id')){
            return response()->json([
                'status'=>0,
                'message'=>__('Không tìm thấy bài viết !'),
            ]);
        }

        $id = $request->get('id');

        if (!$request->filled('content')){
            return response()->json([
                'status'=>0,
                'message'=>__('Không tìm thấy nội dung bài viết !'),
            ]);
        }

        $content = $request->get('content');

        $data = Item::where('module', '=', 'article')->findOrFail($id);
        $data->content = $content;
        $data->save();

        return response()->json([
            'status'=>1,
            'message'=>__('Lưu nội dung thành công !'),
        ]);

    }
}
