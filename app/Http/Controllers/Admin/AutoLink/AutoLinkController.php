<?php

namespace App\Http\Controllers\Admin\AutoLink;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AutoLink;
use App\Models\Item;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\Shop_Group;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AutoLinkController extends Controller
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

    public function index(Request $request){

        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);

        $module = $this->module;

        if($request->ajax) {

            $datatable= AutoLink::orderBy('id')->with(array('author' => function ($query) {
                $query->select('id','username');
            }))->with('shop');

            if(session('shop_id')){
                $datatable->where('shop_id',session('shop_id'));
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

            if ($request->filled('status')) {
                $datatable->where('status',$request->get('status') );
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
                    'idkey',
                    'shop_id',
                    'module',
                    'shop_id',
                    'module',
                    'author_id',
                    'group_id',
                    'parent_id',
                    'percent_dofollow',
                    'title',
                    'slug',
                    'duplicate',
                    'target',
                    'url',
                    'link_type',
                    'shop',
                    'author',
                    'dofollow',
                    'params',
                    'status',
                    'action',
                    'created_at',
                ])
                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->editColumn('percent_dofollow', function($data) {

                    $articles = Item::where('module', config('module.article'))
                        ->where('status', '=', 1)
                        ->select('id','author_id','title','seo_title','slug','description','seo_description','content','image','published_at','created_at','url_redirect_301')
                        ->where('shop_id','=', $data->shop_id)->get();

                    $flag_changed = 0;
                    $percent_dofollow = $data->percent_dofollow;

                    if ($data->status == 1 && isset($articles) && count($articles)){
                        if (isset($data->group_id)){
                            if ($data->group_id == 2){
                                foreach ($articles as $article){

                                    $datatablecontent = \App\Library\AutoLink::replace($data->title,$data->url,$article->content,$data->target,$data->dofollow);

                                    if ($datatablecontent['changed']) {

                                        if ($data->link_type == 1){
                                            if ($flag_changed < $percent_dofollow) {
                                                $flag_changed = $flag_changed + 1;
                                            }
                                        }
                                    }

                                }
                                if ($flag_changed < $percent_dofollow){

                                    return '<span class="badge badge-warning">'.$flag_changed.'/'.$percent_dofollow.'</span>';
                                }else{
                                    return '<span class="badge badge-success">'.$flag_changed.'/'.$percent_dofollow.'</span>';
                                }

                            }else{
                                return '<span class="badge badge-danger">0/0</span>';
                            }

                        }else{

                            foreach ($articles as $article){

                                $datatablecontent = \App\Library\AutoLink::replace($data->title,$data->url,$article->content,$data->target,$data->dofollow);

                                if ($datatablecontent['changed']) {

                                    if ($data->link_type == 1){
                                        if ($flag_changed < $percent_dofollow) {
                                            $flag_changed = $flag_changed + 1;
                                        }
                                    }
                                }

                            }

                            if ($flag_changed < $percent_dofollow){
                                return '<span class="badge badge-warning">'.$flag_changed.'/'.$percent_dofollow.'</span>';
                            }else{
                                return '<span class="badge badge-success">'.$flag_changed.'/'.$percent_dofollow.'</span>';
                            }
                        }
                    }else{
                        return '<span class="badge badge-danger">0/0</span>';
                    }


                })
                ->addColumn('action', function($row) {

                    $articles = Item::where('module', config('module.article'))
                        ->where('status', '=', 1)
                        ->select('id','author_id','title','seo_title','slug','description','seo_description','content','image','published_at','created_at','url_redirect_301')
                        ->where('shop_id','=', $row->shop_id)->get();

                    $flag_changed = 0;
                    $percent_dofollow = $row->percent_dofollow;

                    $arr_url = array();

                    if (isset($articles) && count($articles)){
                        if (isset($row->group_id)){
                            if ($row->group_id == 2){
                                foreach ($articles as $article){

                                    $datatablecontent = \App\Library\AutoLink::replace($row->title,$row->url,$article->content,$row->target,$row->dofollow);

                                    if ($datatablecontent['changed']) {

                                        if ($row->link_type == 1){
                                            if ($flag_changed < $percent_dofollow) {
                                                $flag_changed = $flag_changed + 1;
                                                array_push($arr_url,$article->slug);
                                            }
                                        }
                                    }

                                }


                            }

                        }else{

                            foreach ($articles as $article){

                                $datatablecontent = \App\Library\AutoLink::replace($row->title,$row->url,$article->content,$row->target,$row->dofollow);

                                if ($datatablecontent['changed']) {

                                    if ($row->link_type == 1){
                                        if ($flag_changed < $percent_dofollow) {
                                            $flag_changed = $flag_changed + 1;
                                            array_push($arr_url,$article->slug);
                                        }
                                    }
                                }

                            }
                        }
                    }

                    if (count($arr_url) > 0 && $row->status == 1){
                        $arr_url = json_encode($arr_url);
                        $temp= "<a  href='javascript:void(0)' data-shop=\"$row->shop_id\" data-id=\"$row->id\" rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary show_url\" title=\"Danh sách link\"><i class=\"la la-eye\"></i></a>";
                    }else{
                        $temp= "<a  href='javascript:void(0)' rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Danh sách link\"><i class=\"la la-eye\"></i></a>";

                    }
                    $temp.= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.= "<a  class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-primary' title=\"Nhân bản\"><i class=\"la la-copy\"></i></a>";
                    $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->rawColumns(['action','percent_dofollow'])
                ->toJson();
        }

        return view('admin.autolink.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    public function create(Request $request)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];

        $shops = Shop::orderBy('id','desc')->where('status', 1)->get();
        $groups = config('module.auto-link.category');

        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.autolink.create_edit')
            ->with('module', $this->module)
            ->with('groups', $groups)
            ->with('shops', $shops)
            ->with('created', 1)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);

    }

    public function store(Request $request){

        $this->validate($request,[
            'title'=>'required',
            'group_access'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
            'group_access.required' => __('Vui lòng nhập danh mục'),
        ]);

//        return $request->all();

        if ($request->filled('title')) {
            $title = $request->title;
        }

        if ($request->filled('link_type')) {
            $link_type = $request->link_type;
        }


        if ($request->filled('url')) {
            $url = $request->url;
        }else{
            $url = null;
            if ($link_type == 1){
                return redirect()->back()->withErrors(__('Vui lòng điền Internal link  !'));
            }
        }

        if ($request->filled('target')) {
            $target = $request->target;
        }else{
            $target = 0;
        }

        if ($request->filled('percent_dofollow')) {
            $percent_dofollow = $request->percent_dofollow;
        }

        if ($request->filled('group_access')) {
            $group_all = $request->group_access;
        }

        if ($request->filled('shop_all')) {
            $shop_all = $request->shop_all;
        }else{
            $shop_all = 0;
        }

        if ($request->filled('dofollow')) {
            $dofollow = 1;
        }else{
            $dofollow = 0;
        }

        if (!session("shop_id")){
            if ($shop_all == 1){
                $shops = Shop::where('status',1)->get();
            }else{
                if ($request->filled('shop_access')) {
                    $shop_access = $request->shop_access;
                    $shops = Shop::where('status',1)->whereIn('id',$shop_access)->get();
                }else{
                    return redirect()->back()->withErrors(__('Vui lòng chọn điểm bán phân phối !'));
                }

            }
        }

        if (session("shop_id")){
            $shop = Shop::where('status',1)->where('id',session("shop_id"))->first();

            $input['title'] = $title;
            $input['url'] = $url;
            $input['target'] = $target;
            $input['dofollow'] = $dofollow;
            $input['percent_dofollow'] = $percent_dofollow;
            $input['shop_id'] = session("shop_id");
            $input['status'] = 1;
            $input['link_type'] = $link_type;
            $input['author_id'] = auth()->user()->id;
            $input['created_at'] = Carbon::now();

            $arr_link_access = $request->url_external;

            $params_access = json_encode($arr_link_access);

            $input['group_id'] = $group_all;
            $input['params_access'] = $params_access;

            AutoLink::create($input);
//
//            $key = 'sys_theme_auto_link';
//
//            $autolinks = AutoLink::orderBy('id')->where('shop_id',session("shop_id"))->where('status',1)->get();
//
//            $itemselect = array();
//
//            foreach ($autolinks as $autolink){
//
//                $setting['title'] = $autolink->title;
//                $setting['url'] = $autolink->url;
//                $setting['target'] = $autolink->target;
//                $setting['dofollow'] = $autolink->dofollow;
//                $setting['percent_dofollow'] = $autolink->percent_dofollow;
//                $setting['link_type'] = $autolink->link_type;
//                $setting['params'] = $autolink->params;
//                $setting['params_access'] = $autolink->params_access;
//
//                array_push($itemselect,$setting);
//
//            }
//
//            Setting::add($key, json_encode($itemselect), Setting::getDataType($key));

        }else{

            foreach ($shops as $shop){

                $input['title'] = $title;
                $input['url'] = $url;
                $input['target'] = $target;
                $input['dofollow'] = $dofollow;
                $input['percent_dofollow'] = $percent_dofollow;
                $input['shop_id'] = $shop->id;
                $input['link_type'] = $link_type;
                $input['author_id'] = auth()->user()->id;
                $input['created_at'] = Carbon::now();
                $input['status'] = 1;

                $arr_link_access = $request->url_external;

                $params_access = json_encode($arr_link_access);

                $input['group_id'] = $group_all;
                $input['params_access'] = $params_access;

                AutoLink::create($input);

//                $key = 'sys_theme_auto_link';
//
//                $autolinks = AutoLink::orderBy('id')->where('shop_id',$shop->id)->where('status',1)->get();
//
//                $itemselect = array();
//
//                foreach ($autolinks as $autolink){
//
//                    $setting['title'] = $autolink->title;
//                    $setting['url'] = $autolink->url;
//                    $setting['target'] = $autolink->target;
//                    $setting['dofollow'] = $autolink->dofollow;
//                    $setting['percent_dofollow'] = $autolink->percent_dofollow;
//                    $setting['link_type'] = $autolink->link_type;
//                    $setting['params'] = $autolink->params;
//                    $setting['params_access'] = $autolink->params_access;
//                    array_push($itemselect,$setting);
//
//                }
//
//                Setting::add($key, json_encode($itemselect), Setting::getDataType($key));

            }

//            session()->forget('shop_id');
//            session()->forget('shop_name');
        }

        return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Thêm mới thành công !'));
    }

    public function edit(Request $request,$id){

        $data = AutoLink::orderBy('id')->with(array('author' => function ($query) {
            $query->select('id','username');
        }))->with('shop')->where('id',$id)->first();

        session()->put('shop_id', $data->shop->id);
        session()->put('shop_name', $data->shop->domain);

        $title_group = null;

        $shops = Shop::orderBy('id','desc')->where('status', 1)->get();

        $groups = config('module.auto-link.category');

        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$id);
        return view('admin.autolink.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('groups', $groups)
            ->with('params', json_decode($data->params))
            ->with('shops', $shops)
            ->with('data', $data);

    }

    public function update(Request $request,$id){

        $data = AutoLink::orderBy('id')->with(array('author' => function ($query) {
            $query->select('id','username');
        }))->with('shop')->where('id',$id)->first();

        $input = $request->all();
        if ($request->filled('dofollow')) {
            $input['dofollow'] = 1;
        }else{
            $input['dofollow'] = 0;
        }
        $input['group_id'] = $input["group_access"];
        $input['params_access'] = json_encode($input["url_external"]);

        $data->update($input);


        return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Cập nhật thành công !'));
    }

    public function destroy(Request $request)
    {
        $input=explode(',',$request->id);

        AutoLink::orderBy('id')->with(array('author' => function ($query) {
            $query->select('id','username');
        }))->with('shop')->whereIn('id',$input)->delete();


        ActivityLog::add($request, 'Xóa thành công '.$this->module.' #'.json_encode($input));
        return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Xóa thành công !'));
    }

    public function updateStatus(Request $request){
        $id = $request->id;

        $data = AutoLink::orderBy('id')->with(array('author' => function ($query) {
            $query->select('id','username');
        }))->with('shop')->where('id',$id)->first();

        if($data->status == 1){
            $data->status = 0;
        }
        elseif($data->status == 0){
            $data->status = 1;
        }

        $data->save();

        ActivityLog::add($request, 'Cập nhật trạng thái thành công.');
        return response()->json([
            'message'=>__('Cập nhật trạng thái thành công'),
            'status'=> 1
        ]);
    }

    public function showUrl(Request $request){

        $id = $request->id;
        $shop_id = $request->shop_id;
        $arr_url = array();

        $shop =  Shop::where('id',$shop_id)->first();

        $key = "sys_zip_shop";
        $c_setting_zip = '';
        $setting_zips = Setting::getAllSettingsShopId($shop->id);

        foreach ($setting_zips as $value){
            if ($value->name == $key && $shop->id == $value->shop_id){
                $c_setting_zip = $value->val;
            }
        }

        if (!isset($shop)){
            return response()->json([
                'status'=>0,
                'message'=>__('Shop truy cập không tồn tại'),
            ]);
        }

        $articles = Item::where('module', config('module.article'))
            ->where('status', '=', 1)
            ->select('id','author_id','title','seo_title','slug','description','seo_description','content','image','published_at','created_at','url_redirect_301')
            ->where('shop_id','=', $shop_id)->get();


        $data = AutoLink::where('status',1)->where('id',$id)->where('shop_id',$shop_id)->first();

        if (!isset($data)){
            return response()->json([
                'status'=>0,
                'message'=>__('Link không tồn tại'),
            ]);
        }

        $flag_changed = 0;
        $percent_dofollow = $data->percent_dofollow;
        $host = 'https://'.$shop->domain;

        if (isset($data->group_id)){
            if ($data->group_id == 2){
                foreach ($articles as $article){


                    $datatablecontent = \App\Library\AutoLink::replace($data->title,$data->url,$article->content,$data->target,$data->dofollow);

                    if ($datatablecontent['changed']) {

                        if ($data->link_type == 1){
                            if ($flag_changed < $percent_dofollow) {
                                $flag_changed = $flag_changed + 1;
                                if (isset($c_setting_zip) && $c_setting_zip != ''){
                                    if ($c_setting_zip == '/blog'){
                                        $url = $host.'/blog/'.$article->slug;

                                        array_push($arr_url,$url);
                                    }else{
                                        $url = $host.'/tin-tuc/'.$article->slug;

                                        array_push($arr_url,$url);
                                    }
                                }else{
                                    $url = $host.'/tin-tuc/'.$article->slug;

                                    array_push($arr_url,$url);
                                }

                            }
                        }
                    }

                }


            }
        }else{

            foreach ($articles as $article){

                $datatablecontent = \App\Library\AutoLink::replace($data->title,$data->url,$article->content,$data->target,$data->dofollow);

                if ($datatablecontent['changed']) {

                    if ($data->link_type == 1){
                        if ($flag_changed < $percent_dofollow) {
                            $flag_changed = $flag_changed + 1;
                            if (isset($c_setting_zip) && $c_setting_zip != '') {
                                if ($c_setting_zip == '/blog') {
                                    $url = $host.'/blog/'.$article->slug;
                                    array_push($arr_url,$url);
                                }else{
                                    $url = $host.'/tin-tuc/'.$article->slug;
                                    array_push($arr_url,$url);
                                }
                            }else{
                                $url = $host.'/tin-tuc/'.$article->slug;
                                array_push($arr_url,$url);
                            }

                        }
                    }
                }

            }
        }


        $html = view('admin.autolink.widget.__datatable')->with('data',$arr_url)->render();

        return response()->json([
            'status'=>1,
            'html'=>$html,
            'message'=>__('Lấy link thành công'),
        ]);

    }
}
