<?php

namespace App\Http\Controllers\Admin\Minigame\Module;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Item;
use App\Models\ItemConfig;
use App\Models\Shop;
use Auth;
use Carbon\Carbon;
use Html;
use Illuminate\Http\Request;


class PackageConfigController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;
    protected $moduleItem;
    public function __construct(Request $request)
    {


        $this->module=$request->segments()[1]??"";

        //set permission to function
        $this->middleware('permission:'. $this->module);
        $this->middleware('permission:withdraw-package-config-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:withdraw-package-config-create', ['only' => ['store']]);
        $this->middleware('permission:withdraw-package-config-delete', ['only' => ['destroy']]);
        $this->middleware('permission:withdraw-package-config-base', ['only' => ['update_config_base']]);

        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'.index'),
                'title' => __(config('module.minigame.'.$this->module.'.title'))
            ];
        }
    }

    public function index(Request $request)
    {

         if (!session('shop_id')) {
             return redirect()->back()->withErrors(__('Không shop truy cập'));
         }

        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);

        if($request->ajax) {

            $datatable = ItemConfig::with(array('items' => function ($query) {
                $query->with('parrent');
            }))
                ->where('shop_id',session('shop_id'))
                ->where('module', 'package')->where('status', 1);

            if ($request->filled('parent_id')) {

                $datatable->with(array('items' => function ($query) use($request){
                    $query->whereHas('parrent', function ($query) use ($request){
                        $query->where('id',$request->get('parent_id'));
                    });
                    $query->with(array('parrent' => function ($query) use($request){
                        $query->where('id',$request->get('parent_id'));
                    }));
                }))->whereHas('items', function ($query) use ($request){
                    $query->with(array('parrent' => function ($query) use($request){
                        $query->where('id',$request->get('parent_id'));
                    }));
                    $query->whereHas('parrent', function ($query) use ($request){
                        $query->where('id',$request->get('parent_id'));
                    });
                });
            }

            if ($request->filled('title'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
                });
            }

            return \datatables()->eloquent($datatable)

                ->only([
                    'id',
                    'title',
                    'price',
                    'price_old',
                    'status',
                    'action',
                    'items',
                    'created_at',
                    'locale',
                    'parent_id',
                    'sticky',
                ])
                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->addColumn('action', function($row) {
                    $temp = '';
                    if( Auth::user()->can('withdraw-package-config-edit')) {
                        $temp.= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    }
                    if( Auth::user()->can('withdraw-package-config-delete')){
                        $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";

                    }

                    return $temp;
                })
                ->toJson();
        }

        $gametypes = Item::where('module', 'gametype')->with('children')->where('status',1)->get();

        $dataItemConfigSelected= ItemConfig::where('module', 'package')
            ->where('shop_id', session('shop_id'))
            ->where('status', 1)
            ->get();

        $listgametype = Item::where('module', config('module.minigame.module.gametype'))
            ->where('status', 1)->pluck('title','id')->toArray();

        return view('admin.minigame.module.package-config.index')
            ->with('module', $this->module)
            ->with('listgametype', $listgametype)
            ->with('gametypes', $gametypes)
            ->with('dataItemConfigSelected', $dataItemConfigSelected)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }


    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {

        $ids_active = $request->ids_active;

        if(count($ids_active ?? []) > 0){

        }
        else{

            //nếu mà ko select dịch vụ nào thì chuyển hết các config về status 0
            ItemConfig::where(['shop_id' => session('shop_id')])
                ->where('status', 1)
                ->where('module', 'package')
                ->update(['status' => 0]);

            if ($request->filled('submit-close')) {
                return redirect()->route('admin.' . $this->module . '.index')->with('success', __('Thêm mới thành công !'));
            } else {
                return redirect()->back()->with('success', __('Thêm mới thành công !'));
            }
        }

        ItemConfig::where(['shop_id' => session('shop_id')])
            ->whereNotIn('item_id', $ids_active)
            ->where('status', 1)
            ->where('module', 'package')
            ->update(['status' => 0]);

        ItemConfig::where(['shop_id' => session('shop_id')])
            ->whereIn('item_id', $ids_active)
            ->where('module', 'package')
            ->update(['status' => 1]);



        $dataFromItem = Item::where('module','package')->whereIn('id', $ids_active)->get()->toArray();


        foreach ($dataFromItem as $item) {
            $checkEsxit = ItemConfig::where('module','package')
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

    }

    public function edit(Request $request,$id)
    {

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];

        $data = ItemConfig::where('module', '=', 'package')
            ->with(['items'=>function($query) use ($request){
                $query->with('parrent');
                $query->where('module', '=', 'package');
            }])
            ->findOrFail($id);

        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        $listgametype = Item::where('module', config('module.minigame.module.gametype'))
            // ->where('shop_id', session('shop_id'))
            ->where('status', 1)->pluck('title','id')->toArray();
        return view('admin.minigame.module.package-config.create_edit')
            ->with('module', $this->module)
            ->with('listgametype', $listgametype)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data);

    }

    public function update(Request $request,$id)
    {

        $data =  ItemConfig::where('module', '=', 'package')->findOrFail($id);

        $this->validate($request,[
            'title'=>'required',
            'price_old'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tên gói'),
            'price.required' => __('Vui lòng nhập giá trị gói'),
            'price_old.required' => __('Vui lòng nhập đơn giá'),
        ]);

        $input=$request->all();
        $input['module']= 'package';
        $input['shop_id'] = session('shop_id');

        $data->update($input);
        //set category

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
        $input = explode(',', $request->id);

        ItemConfig::where('module', '=', 'package')->whereIn('id', $input)->update([
            'status'=>0
        ]);

        ActivityLog::add($request, 'Xóa thành công ' . $this->module . ' #' . json_encode($input));
        return redirect()->back()->with('success', __('Xóa thành công !'));
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

    public function update_config_base(Request $request)
    {

        $input = explode(',', $request->id);

        $data = ItemConfig::with('items')->where('module', '=', 'package')->whereIn('id', $input)->get();

        foreach ($data??[] as $item){
            $item->update([
                'title'=>$item->items->title,
                'price_old'=>$item->items->price_old,
                'price'=>$item->items->price,
                'sticky'=>$item->items->sticky,
            ]);
        }
        ActivityLog::add($request, 'Cập nhật cấu hình gốc thành công ' . $this->module . ' #' . json_encode($input));

        return redirect()->back()->with('success', __('Cập nhật thành công !'));

    }

}
