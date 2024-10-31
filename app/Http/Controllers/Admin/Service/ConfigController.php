<?php

namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Controller;
use App\Library\Helpers;
use App\Library\RatioCommon\ServiceRatio;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Item;
use App\Models\ItemConfig;
use App\Models\LogEdit;
use App\Models\Order;
use App\Models\Shop;
use Carbon\Carbon;
use Html;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;


class ConfigController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;

    public function __construct(Request $request)
    {


        $this->module = $request->segments()[1] ?? "";
        $this->moduleCategory = $this->module . '-config';
        $this->moduleNeedConfig ='service';

        //set permission to function
        $this->middleware('permission:service-config-list');
        $this->middleware('permission:service-config-create', ['only' => ['create', 'store']]);
        //$this->middleware('permission:service-config-list|service-config-create|service-config-update', ['only' => [ 'duplicate']]);
        $this->middleware('permission:service-config-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:service-config-delete', ['only' => ['destroy']]);
        $this->middleware('permission:service-config-update-config', ['only' => ['update_config']]);
        $this->middleware('permission:service-config-update-config-base', ['only' => ['update_config_base']]);


        if ($this->module != "") {
            $this->page_breadcrumbs[] = [
                'page' => route('admin.' . $this->module . '.index'),
                'title' => __('Cấu hình dịch vụ cho từng shop')
            ];
        }
    }


    public function index(Request $request)
    {


        ActivityLog::add($request, 'Truy cập danh sách ' . $this->module);
        if ($request->ajax) {

            $datatable = ItemConfig::with(['items'=>function($query) use ($request){
                $query->with(['groups'=>function($query) use ($request){
                    $query->select(['groups.id','groups.title']);
                    return $query;
                }]);
                if ($request->filled('group_id')) {

                    $query->whereHas('groups', function ($query) use ($request) {
                        $query->where('group_id', $request->get('group_id'));
                    });

                }

                return $query;
            }])
                ->with('shop')
                ->where('module', 'service')
                ->where(function($q){
                    $q->orWhere('status', '=',1);
                    $q->orWhere('status', '=',2);
                });

            if ($request->filled('id')) {
                $datatable->where(function ($q) use ($request) {
                    $q->orWhere('id', $request->get('id'));
                    $q->orWhere('idkey', $request->get('id'));
                });
            }


            if ($request->filled('title')) {
                $datatable->where(function ($q) use ($request) {
                    $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
                });
            }
            if ($request->filled('position')) {
                $datatable->where('position', $request->get('position'));
            }

            if ($request->filled('status')) {
                $datatable->where('status', $request->get('status'));
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

            return \datatables()->eloquent($datatable)
                ->only([
                    'id',
                    'item_id',
                    'title_base',
                    'title',
                    'shop',
                    'module',
                    'slug',
                    'image',
                    'locale',
                    'items.id',
                    'items.title',
                    'items.groups',
                    'title_group',
                    'order',
                    'position',
                    'status',
                    'action',
                    'created_at',
                ])
                ->editColumn('items.title', function ($row) {
                    if(Auth::user()->can('service-config-view-base-parent-title')){
                        return $row->items->title ?? "";
                    }
                    else{
                        return "";
                    }

                })
                ->editColumn('image', function($data) {
                    $image = '';

                    if (isset($data->image)){
                        $image = "<img class='image-item' src='".\App\Library\MediaHelpers::media($data->image)."' style ='max-width: 90px;max-height: 90px'>";
                    }else{
                        $image = "<img class=\"image-item\" src=\"/assets/backend/themes/images/empty-photo.jpg\" style=\"max-width: 90px;max-height: 90px\">";
                    }

                    return $image;
                })
                //->addColumn('title_group', function ($row) {
                //    $temp="";
                //    foreach($row->items->groups??[] as $aGroup){
                //        $temp .= "<span class=\"label label-pill label-inline label-center mr-2  label-primary \">" . $aGroup->title . "</span><br />";
                //    }
                //    return $temp;
                //})

                ->editColumn('created_at', function ($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->addColumn('action', function ($row) {
                    $temp = "<a href=\"" . route('admin.' . $this->module . '.edit', $row->id) . "\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    //if(Auth::guard()->user()->can('service-list|service-create|service-update')){
                    //    $temp .= "<a href=\"" . route('admin.' . $this->module . '.duplicate', $row->id) . "\"  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-primary' title=\"Nhân bản\"><i class=\"la la-copy\"></i></a>";
                    //}
                    if(Auth::user()->can('service-config-delete')) {
                        $temp .= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    }
                    return $temp;
                })
                ->rawColumns(['action', 'image'])
                ->toJson();
        }

        $shop_access_user = Auth::user()->shop_access;
        $shop = Shop::orderBy('id', 'desc');
        if (isset($shop_access_user) && $shop_access_user !== "all") {
            $shop_access_user = json_decode($shop_access_user);
            $shop = $shop->whereIn('id', $shop_access_user);
        }
        $shop = $shop->pluck('title', 'id')->toArray();


        $dataItem = Item::where('module', 'service')->get();
        $dataItemConfigSelected= ItemConfig::where('module', 'service')
            ->where('shop_id', session('shop_id'))
            ->where('status', 1)
            ->get();

        $dataCategory = Group::where('module', '=', 'service-category')->orderBy('order','asc')->get();

        return view('admin.service.config.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('shop', $shop)
            ->with('dataCategory', $dataCategory)
            ->with('dataItem', $dataItem)
            ->with('dataItemConfigSelected', $dataItemConfigSelected);
    }


    public function create(Request $request)
    {
        //$this->page_breadcrumbs[] = [
        //    'page' => '#',
        //    'title' => __("Thêm mới")
        //];
        //
        //$dataCategory = Group::where('module', '=', $this->moduleCategory)->orderBy('order', 'asc')->get();
        //
        //ActivityLog::add($request, 'Vào form create ' . $this->module);
        //return view('admin.' . $this->module . '.item.create_edit')
        //    ->with('module', $this->module)
        //    ->with('page_breadcrumbs', $this->page_breadcrumbs)
        //    ->with('dataCategory', $dataCategory);

    }


    public function store(Request $request)
    {


        //$this->validate($request,[
        //    'title'=>'required',
        //],[
        //    'title.required' => __('Vui lòng nhập tiêu đề'),
        //]);

        $ids_active = $request->ids_active;

        if(count($ids_active ?? []) > 0){

        }
        else{

            //nếu mà ko select dịch vụ nào thì chuyển hết các config về status 0
            ItemConfig::where(['shop_id' => session('shop_id')])
                ->where(function($q){
                    $q->orWhere('status', '=',1);
                    $q->orWhere('module', '=',2);
                })
                ->where('module', $this->moduleNeedConfig)
                ->update(['status' => 0]);

            if ($request->filled('submit-close')) {
                return redirect()->route('admin.' . $this->module . '.index')->with('success', __('Thêm mới thành công !'));
            } else {
                return redirect()->back()->with('success', __('Thêm mới thành công !'));
            }


        }

        ItemConfig::where(['shop_id' => session('shop_id')])
            ->whereNotIn('item_id', $ids_active)
            ->where(function($q){
                $q->orWhere('status', '=',1);
                $q->orWhere('module', '=',2);
            })
            ->where('module', $this->moduleNeedConfig)
            ->update(['status' => 0]);

        ItemConfig::where(['shop_id' => session('shop_id')])
            ->whereIn('item_id', $ids_active)
            ->where('module', $this->moduleNeedConfig)
            ->update(['status' => 1]);



        $dataFromItem = Item::where('module',$this->moduleNeedConfig)->whereIn('id', $ids_active)->get()->toArray();


        foreach ($dataFromItem as $item) {
            $checkEsxit = ItemConfig::where('module',$this->moduleNeedConfig)
                ->where('shop_id' , session('shop_id'))
                ->where('item_id', $item['id'])
                ->first();

            if (!$checkEsxit) {
                $itemConfig = ItemConfig::create($item);
                $itemConfig->shop_id = session('shop_id');
                $itemConfig->item_id = $item['id'];
                $itemConfig->save();
            }
        }
        if ($request->filled('submit-close')) {
            return redirect()->route('admin.' . $this->module . '.index')->with('success', __('Thêm mới thành công !'));
        } else {
            return redirect()->back()->with('success', __('Thêm mới thành công !'));
        }

        //$input['module']=$this->module;
        //$input['author_id']=auth()->user()->id;
        //$input['price_old'] = (float)str_replace(array(' ', '.'), '', $request->price_old);
        //$input['price'] = (float)str_replace(array(' ', '.'), '', $request->price);
        //$input['percent_sale'] = (float)str_replace(array(' ', '.'), '', $request->percent_sale);
        //$input['shop_id'] = session('shop_id');
        //
        //
        //
        //$params = $request->except([
        //    '_method',
        //    '_token',
        //    'submit-close',
        //    'is_slug_override',
        //    'description',
        //    'content',
        //    'target',
        //    'url',
        //    'image',
        //    'image_extension',
        //    'image_banner',
        //    'image_icon',
        //    'image_logo',
        //    'status',
        //    'ended_at',
        //    'order',
        //    'gate_id',
        //    'idkey',
        //    'seo_title',
        //    'seo_description',
        //]);
        //
        //
        //$input['params'] = json_encode($params, JSON_UNESCAPED_UNICODE);
        //
        //$data=Item::create($input);
        //
        ////set category
        //if( isset($input['group_id'] ) &&  $input['group_id']!=0){
        //    $data->groups()->attach($input['group_id']);
        //}
        //ActivityLog::add($request, 'Tạo mới thành công '.$this->module.' #'.$data->id);
        //
        //if($request->filled('submit-close')){
        //    return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Thêm mới thành công !'));
        //}
        //else {
        //    return redirect()->back()->with('success',__('Thêm mới thành công !'));
        //}
    }


    public function show(Request $request, $id)
    {
        //$data = Group::findOrFail($id);
        //ActivityLog::add($request, 'Show '.$this->module.' #'.$data->id);
        //return view('admin.'.$this->module.'.item.show', compact('datatable'));
    }

    public function edit(Request $request, $id)
    {
        $this->page_breadcrumbs[] = [
            'page' => '#',
            'title' => __("Cập nhật")
        ];

        $shop = Shop::where('id',session('shop_id'))->first();

        $data = ItemConfig::where('module',$this->moduleNeedConfig)->where(['shop_id' => session('shop_id')])->findOrFail($id);
        $ratioOfShop=ServiceRatio::get(session('shop_id'));
        if($ratioOfShop==false){
            return redirect()->back()->withErrors(__('Shop chưa được cấu hình tỉ giá'));
        }

        $table_name = $data->getTable();

        $log_edit = LogEdit::where('table_name',$table_name)->with(array('author' => function ($query) {
            $query->select('id','username');
        }))->where('table_id',$data->id)->get();

        $secret_key = config('module.service.secret_key');
        $name_shop = Helpers::Encrypt(\Str::slug($shop->title),$secret_key);

        $folder_image = "service-config-".$name_shop;

        //$dataCategory = Group::where('module', '=', $this->moduleCategory)->orderBy('order', 'asc')->get();

        ActivityLog::add($request, 'Vào form edit ' . $this->module . ' #' . $data->id);
        return view('admin.service.config.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('folder_image', $folder_image)
            ->with('log_edit', $log_edit)
            ->with('ratioOfShop', $ratioOfShop);
           // ->with('dataCategory', $dataCategory);

    }

    public function update(Request $request, $id)
    {

        //return $request->all();
        $data = ItemConfig::where('module',$this->moduleNeedConfig)->findOrFail($id);

        $this->validate($request, [
            'title' => 'required',
        ], [
            'title.required' => __('Vui lòng nhập tiêu đề'),
        ]);

        $input = $request->all();

        //nếu ko có quyền chuyển cổng nạp sms tự động
        if(!auth()->user()->can('service-config-change-gate-sms')){
            unset($input['idkey']);
        }

        $params = $request->except([
            '_method',
            '_token',
            'submit-close',
            'is_slug_override',
            'description',
            'content',
            'target',
            'url',
            'image',
            'image_extension',
            'image_banner',
            'image_icon',
            'image_logo',
            'status',
            'ended_at',
            'order',
            'gate_id',
            'idkey',
            'seo_title',
            'seo_description',
            'params_plus',
            'note',
        ]);

        $input['params'] = json_encode($params, JSON_UNESCAPED_UNICODE);

//        Lưu log edit
        if (session('shop_id')){
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
            $c_input['shop_id'] = session('shop_id');

            if ($c_input['title_before'] == $c_input['title_after'] && $c_input['description_before'] == $c_input['description_after'] && $c_input['content_before'] == $c_input['content_after']){

            }else{
                LogEdit::create($c_input);
            }
        }

        $data->update($input);
        //set category

        ActivityLog::add($request, 'Cập nhật thành công '.$this->module.' #'.$data->id);
        if ($request->filled('submit-close')) {
            return redirect()->route('admin.' . $this->module . '.index')->with('success', __('Cập nhật thành công !'));
        } else {
            return redirect()->back()->with('success', __('Cập nhật thành công !'));
        }
    }

    public function revision(Request $request,$id,$slug)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Revision"),
        ];

        $data = ItemConfig::where('module', '=', 'service')->findOrFail($id);

        $log = LogEdit::where('id',$slug)->with(array('author' => function ($query) {
            $query->select('id','username');
        }))->first();


        ActivityLog::add($request, 'Vào form revision service #'.$data->id);
        return view('admin.service.config.revision')
            ->with('module', $this->moduleNeedConfig)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('log', $log)
            ->with('slug', $slug);

    }

    public function postRevision(Request $request,$id,$slug){

        $data =  ItemConfig::where('module', '=', 'service')->findOrFail($id);

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

        return redirect()->route('admin.service-config.index')->with('success',__('Phục hồi thành công !'));
    }

    public function destroy(Request $request)
    {

        $input = explode(',', $request->id);

        $orders = Order::query()->whereIn('ref_id',$input)->whereIn('status',[1,2,6,7,9,77,88,999])->pluck('id')->toArray();

        if (!empty($orders)){
            return redirect()->back()->with('success', __('Không thể gỡ dịch vụ config vì đang có giao dịch hoạt động !'));
        }

        ItemConfig::where('module', '=', $this->moduleNeedConfig)->whereIn('id', $input)->update([
            'status'=>0
        ]);
        ActivityLog::add($request, 'Xóa thành công ' . $this->module . ' #' . json_encode($input));
        return redirect()->back()->with('success', __('Xóa thành công !'));
    }


    //public function duplicate(Request $request, $id)
    //{
    //
    //
    //    $data = ItemConfig::where('module', '=', $this->module)->find($id);
    //    if (!$data) {
    //        return redirect()->back()->withErrors(__('Không tìm thấy dữ liệu để nhân bản'));
    //    }
    //    $dataGroup = $data->groups()->get()->pluck(['id']);
    //
    //    $dataNew = $data->replicate();
    //    $dataNew->title = $dataNew->title . " (" . ((int)$data->duplicate + 1) . ")";
    //    $dataNew->slug = $dataNew->slug . "-" . ((int)$data->duplicate + 1);
    //    $dataNew->duplicate = 0;
    //    $dataNew->is_slug_override = 0;
    //    $dataNew->save();
    //    //set group cho dataNew
    //    $dataNew->groups()->sync($dataGroup);
    //
    //    //update data old plus 1 count version
    //    $data->duplicate = (int)$data->duplicate + 1;
    //    $data->save();
    //
    //    ActivityLog::add($request, 'Nhân bản ' . $this->module . ' #' . $data->id . "thành #" . $dataNew->id);
    //    return redirect()->back()->with('success', __('Nhân bản thành công'));
    //
    //
    //}


    public function update_field(Request $request)
    {


        $input = explode(',', $request->id);
        $field = $request->field;
        $value = $request->value;
        $whitelist = ['status'];

        if (!in_array($field, $whitelist)) {
            return response()->json([
                'success' => false,
                'message' => __('Trường cập nhật không được chấp thuận'),
                'redirect' => ''
            ]);
        }


        $data = ItemConfig::where('module', '=', $this->moduleNeedConfig)::whereIn('id', $input)->update([
            $field => $value
        ]);

        ActivityLog::add($request, 'Cập nhật field thành công ' . $this->module . ' ' . json_encode($whitelist) . ' #' . json_encode($input));
        return redirect()->back()->with('success', __('Cập nhật thành công !'));

    }



    public function update_config(Request $request)
    {

        $input = explode(',', $request->id);
        $data = ItemConfig::with('items')->where('module', '=', $this->moduleNeedConfig)->whereIn('id', $input)->get();
        foreach ($data??[] as $item){

            $item->update([
                'title'=>$item->items->title,
                'seo_title'=>$item->items->title,
                'image'=>$item->items->image,
                'image_banner'=>$item->items->image_banner,
                'content'=>$item->items->content,
                'description'=>$item->items->description,
                'seo_description'=>$item->items->description,
                'image_icon'=>$item->items->image_icon,

            ]);
        }

        ActivityLog::add($request, 'Cập nhật cấu hình thành công ' . $this->module . ' #' . json_encode($input));

        return redirect()->back()->with('success', __('Cập nhật thành công !'));

    }

    public function update_config_base(Request $request)
    {

        $input = explode(',', $request->id);
        $data = ItemConfig::with('items')->where('module', '=', $this->moduleNeedConfig)->whereIn('id', $input)->get();
        foreach ($data??[] as $item){
            $item->update([
                'params'=>$item->items->params,
            ]);
        }
        ActivityLog::add($request, 'Cập nhật cấu hình gốc thành công ' . $this->module . ' #' . json_encode($input));

        return redirect()->back()->with('success', __('Cập nhật thành công !'));

    }



    // AJAX Reordering function
    public function order(Request $request)
    {


        $source = e($request->get('source'));
        $destination = $request->get('destination');

        $item = Group::where('module', '=', $this->module)->find($source);
        $item->parent_id = isset($destination) ? $destination : 0;
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
        ActivityLog::add($request, 'Thay đổi STT thành công ' . $this->module . ' #' . $item->id);
        return 'ok ';
    }
}
