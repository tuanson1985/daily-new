<?php

namespace App\Http\Controllers\Admin\Module;

use App\Http\Controllers\Controller;
use App\Library\GoogleIndexing;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Item;
use App\Models\LogEdit;
use App\Models\Setting;
use App\Models\Shop;
use Carbon\Carbon;
use Html;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Exception;
use Validator;

class AdsController extends Controller
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
        if($request->ajax) {

            $datatable= Item::where('module', 'advertise-ads')->with('shop')->with('author');

            if ($request->filled('idkey')) {

                $datatable->where('idkey',$request->get('idkey'));
            }

            if ($request->filled('author_id')) {

                $datatable->whereHas('author', function ($query) use ($request) {
                    $query->Where('username', 'LIKE', '%' . $request->get('author_id') . '%');
                });
            }


            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', $request->get('id'));
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

            return \datatables()->eloquent($datatable)

                ->only([
                    'id',
                    'shop',
                    'title',
                    'idkey',
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
                    $temp= "<a href=\"".route('admin.advertise-ads.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    //$temp.= "<a href=\"".route('admin.'.$this->module.'.duplicate',$row->id)."\"  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-primary' title=\"Nhân bản\"><i class=\"la la-copy\"></i></a>";
                    $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->rawColumns(['action', 'title','created_at'])
                ->toJson();

        }

        if(Auth::user()->account_type == 1){
            $client = Shop::orderBy('id','desc');
            $shop_access_user = Auth::user()->shop_access;
            if(isset($shop_access_user) && $shop_access_user !== "all"){
                $shop_access_user = json_decode($shop_access_user);
                $client = $client->whereIn('id',$shop_access_user);
            }
            $client = $client->select('id','domain','title')->get();
        }

        return view('admin.module.advertise-ads.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('client', $client);
    }

    public function create(Request $request)
    {
        if (!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop cấu hình'));
        }

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];


        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.module.advertise-ads.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    public function store(Request $request)
    {
        if (!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop cấu hình'));
        }
        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
        ]);
        $input= $request->all();
        $input['module']=$this->module;
        $input['author_id']=auth()->user()->id;
        $slug = $request->slug;
        $input['slug'] = $slug;
        $input['shop_id'] = session('shop_id');
        //xử lý params
        if ($request->filled('params')){
            $params = $request->get('params');
            $input['params']=  json_encode($params,JSON_UNESCAPED_UNICODE);
        }

        $data = Item::create($input);

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

        $data = Item::where('module', '=', $this->module)->findOrFail($id);

        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        return view('admin.module.advertise-ads.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data);

    }

    public function update(Request $request,$id)
    {
        if (!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop cấu hình'));
        }

        $data =  Item::where('module', '=', $this->module)->findOrFail($id);

        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
        ]);

        $input=$request->all();
        $input['shop_id'] = session('shop_id');
        $input['module']=$this->module;
        $input['author_id']=auth()->user()->id;
        //xử lý params
        if ($request->filled('params')){
            $params = $request->get('params');
            $input['params']=  json_encode($params,JSON_UNESCAPED_UNICODE);
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

    public function cloneItem(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'shop_access' => 'required',
        ],[
            'shop_access.required' => "Vui lòng chọn shop cần clone",
        ]);

        $module = $request->module;

        $inputId =explode(',',$request->id);

        if($validator->fails()){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop cần clone'));
        }

        if(!session('shop_id')){
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

}

