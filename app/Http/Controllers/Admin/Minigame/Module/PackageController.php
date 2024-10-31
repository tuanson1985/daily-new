<?php

namespace App\Http\Controllers\Admin\Minigame\Module;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Item;
use App\Models\ItemConfig;
use App\Models\Shop;
use Carbon\Carbon;
use Html;
use Illuminate\Http\Request;


class PackageController extends Controller
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
        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'.index'),
                'title' => __(config('module.minigame.'.$this->module.'.title'))
            ];
        }
    }

    public function index(Request $request)
    {

//         if (session('shop_id')) {
//             return redirect()->back()->withErrors(__('Không chọn shop truy cập'));
//         }

        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);
        if($request->ajax) {

            $datatable= Item::with('parrent')
                ->where('module', $this->module);

            if ($request->filled('parent_id')) {
                $datatable->where('parent_id',$request->get('parent_id'));
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
                    'parrent',
                    'created_at',
                    'locale',
                    'parent_id',
                    'sticky',
                ])
                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->addColumn('action', function($row) {
                    $temp= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->toJson();
        }

        $listgametype = Item::where('module', config('module.minigame.module.gametype'))
            // ->where('shop_id', session('shop_id'))
            ->where('status', 1)->pluck('title','id')->toArray();
        return view('admin.minigame.module.package.index')
            ->with('module', $this->module)
            ->with('listgametype', $listgametype)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }


    public function create(Request $request)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];

        ActivityLog::add($request, 'Vào form create '.$this->module);
        $listgametype = Item::where('module', config('module.minigame.module.gametype'))
            // ->where('shop_id', session('shop_id'))
            ->where('status', 1)->pluck('title','id')->toArray();
        return view('admin.minigame.module.package.create_edit')
            ->with('module', $this->module)
            ->with('listgametype', $listgametype)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);

    }

    public function store(Request $request)
    {
        // if(!session('shop_id')){
        //     return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        // }
        $this->validate($request,[
            'title'=>'required',
            'parent_id'=>'required',
            'price'=>'required',
            'price_old'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tên gói'),
            'parent_id.required' => __('Vui lòng chọn loại game'),
            'price.required' => __('Vui lòng nhập giá trị gói'),
            'price_old.required' => __('Vui lòng nhập đơn giá'),
        ]);

        $input=$request->all();
        $input['module']=$this->module;
        $input['author_id']=auth()->user()->id;
        $input['shop_id'] = session('shop_id');

        $data=Item::create($input);

        ActivityLog::add($request, 'Tạo mới thành công '.$this->module.' #'.$data->id);

        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Thêm mới thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }
    }

    public function edit(Request $request,$id)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];
        $data = Item::where('module', '=', $this->module)->findOrFail($id);
        // if($data->shop_id){
        //     $shop = Shop::findOrFail($data->shop_id);
        //     session()->put('shop_id', $shop->id);
        //     session()->put('shop_name', $shop->domain);
        // }

        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        $listgametype = Item::where('module', config('module.minigame.module.gametype'))
            // ->where('shop_id', session('shop_id'))
            ->where('status', 1)->pluck('title','id')->toArray();
        return view('admin.minigame.module.package.create_edit')
            ->with('module', $this->module)
            ->with('listgametype', $listgametype)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data);

    }

    public function update(Request $request,$id)
    {

        $data =  Item::where('module', '=', $this->module)->findOrFail($id);

        $this->validate($request,[
            'title'=>'required',
            'parent_id'=>'required',
            'price_old'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tên gói'),
            'parent_id.required' => __('Vui lòng chọn loại game'),
            'price.required' => __('Vui lòng nhập giá trị gói'),
            'price_old.required' => __('Vui lòng nhập đơn giá'),
        ]);

        $input=$request->all();
        $input['module']=$this->module;
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
        $input=explode(',',$request->id);

        $checkItemConffig= ItemConfig::where('module', '=', 'package')->whereIn('item_id',$input)->first();


        if(isset($checkItemConffig)){
            return redirect()->back()->withErrors(__('Không thể xóa gói rút này. Vì có shop đang dùng cấu hình gói rút này'));
        }

        Item::where('module','=',$this->module)->whereIn('id',$input)->delete();
        ActivityLog::add($request, 'Xóa thành công '.$this->module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Xóa thành công !'));
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

    public function updateSticky(Request $request){

        $data = Item::with('parrent')
            ->where('module', 'package')->where('status',1)->get();

        foreach ($data as $item){
            $item = Item::with('parrent')->where('id',$item->id)
                ->where('module', 'package')->where('status',1)->first();
            if (isset($item->parrent)){
                $parrent = $item->parrent;
                if (isset($parrent->parent_id)){
                    $parent_id = $parrent->parent_id;
                    if ($parent_id == 1 || $parent_id == 2 || $parent_id == 3 || $parent_id == 8){
                        $item->sticky = 1;
                        $item->save();
                    }
                }
            }

        }

        return redirect()->back()->with('success',__('Cập nhật thành công !'));

    }

    public function updateAsyncOne(Request $request){

        ini_set('max_execution_time', 2400); //20 minutes

        $shops = Shop::where('status',1)->whereIn('id', range(1, 200))->pluck('id')->toArray();

        //All gói rút.

        $ids_active = Item::with('parrent')
            ->where('module', 'package')->where('status',1)->pluck('id')->toArray();

        foreach ($shops as $shop){

            if(count($ids_active ?? []) > 0){

            }
            else{

                //nếu mà ko select dịch vụ nào thì chuyển hết các config về status 0
                ItemConfig::where(['shop_id' => $shop])
                    ->where('status', 1)
                    ->where('module', 'package')
                    ->update(['status' => 0]);

                if ($request->filled('submit-close')) {
                    return redirect()->route('admin.' . $this->module . '.index')->with('success', __('Thêm mới thành công !'));
                } else {
                    return redirect()->back()->with('success', __('Thêm mới thành công !'));
                }
            }

            ItemConfig::where(['shop_id' => $shop])
                ->whereNotIn('item_id', $ids_active)
                ->where('status', 1)
                ->where('module', 'package')
                ->update(['status' => 0]);

            ItemConfig::where(['shop_id' => $shop])
                ->whereIn('item_id', $ids_active)
                ->where('module', 'package')
                ->update(['status' => 1]);

            $dataFromItem = Item::where('module','package')->whereIn('id', $ids_active)->get()->toArray();

            foreach ($dataFromItem as $item) {
                $checkEsxit = ItemConfig::where('module','package')
                    ->where('shop_id' , $shop)
                    ->where('item_id', $item['id'])
                    ->first();

                if (!$checkEsxit) {
                    $itemConfig = ItemConfig::create($item);
                    $itemConfig->shop_id = $shop;
                    $itemConfig->item_id = $item['id'];
                    $itemConfig->save();
                }
            }
        }

        return redirect()->back()->with('success',__('Cập nhật thành công !'));
    }

    public function updateAsyncTwo(Request $request){

        ini_set('max_execution_time', 2400); //20 minutes

        $shops = Shop::where('status',1)->whereIn('id', range(201, 400))->pluck('id')->toArray();

        //All gói rút.

        $ids_active = Item::with('parrent')
            ->where('module', 'package')->where('status',1)->pluck('id')->toArray();

        foreach ($shops as $shop){

            if(count($ids_active ?? []) > 0){

            }
            else{

                //nếu mà ko select dịch vụ nào thì chuyển hết các config về status 0
                ItemConfig::where(['shop_id' => $shop])
                    ->where('status', 1)
                    ->where('module', 'package')
                    ->update(['status' => 0]);

                if ($request->filled('submit-close')) {
                    return redirect()->route('admin.' . $this->module . '.index')->with('success', __('Thêm mới thành công !'));
                } else {
                    return redirect()->back()->with('success', __('Thêm mới thành công !'));
                }
            }

            ItemConfig::where(['shop_id' => $shop])
                ->whereNotIn('item_id', $ids_active)
                ->where('status', 1)
                ->where('module', 'package')
                ->update(['status' => 0]);

            ItemConfig::where(['shop_id' => $shop])
                ->whereIn('item_id', $ids_active)
                ->where('module', 'package')
                ->update(['status' => 1]);

            $dataFromItem = Item::where('module','package')->whereIn('id', $ids_active)->get()->toArray();

            foreach ($dataFromItem as $item) {
                $checkEsxit = ItemConfig::where('module','package')
                    ->where('shop_id' , $shop)
                    ->where('item_id', $item['id'])
                    ->first();

                if (!$checkEsxit) {
                    $itemConfig = ItemConfig::create($item);
                    $itemConfig->shop_id = $shop;
                    $itemConfig->item_id = $item['id'];
                    $itemConfig->save();
                }
            }
        }

        return redirect()->back()->with('success',__('Cập nhật thành công !'));
    }

    public function updateAsyncThree(Request $request){

        ini_set('max_execution_time', 2400); //20 minutes

        $shops = Shop::where('status',1)->whereIn('id', range(401, 650))->pluck('id')->toArray();

        //All gói rút.

        $ids_active = Item::with('parrent')
            ->where('module', 'package')->where('status',1)->pluck('id')->toArray();

        foreach ($shops as $shop){

            if(count($ids_active ?? []) > 0){

            }
            else{

                //nếu mà ko select dịch vụ nào thì chuyển hết các config về status 0
                ItemConfig::where(['shop_id' => $shop])
                    ->where('status', 1)
                    ->where('module', 'package')
                    ->update(['status' => 0]);

                if ($request->filled('submit-close')) {
                    return redirect()->route('admin.' . $this->module . '.index')->with('success', __('Thêm mới thành công !'));
                } else {
                    return redirect()->back()->with('success', __('Thêm mới thành công !'));
                }
            }

            ItemConfig::where(['shop_id' => $shop])
                ->whereNotIn('item_id', $ids_active)
                ->where('status', 1)
                ->where('module', 'package')
                ->update(['status' => 0]);

            ItemConfig::where(['shop_id' => $shop])
                ->whereIn('item_id', $ids_active)
                ->where('module', 'package')
                ->update(['status' => 1]);

            $dataFromItem = Item::where('module','package')->whereIn('id', $ids_active)->get()->toArray();

            foreach ($dataFromItem as $item) {
                $checkEsxit = ItemConfig::where('module','package')
                    ->where('shop_id' , $shop)
                    ->where('item_id', $item['id'])
                    ->first();

                if (!$checkEsxit) {
                    $itemConfig = ItemConfig::create($item);
                    $itemConfig->shop_id = $shop;
                    $itemConfig->item_id = $item['id'];
                    $itemConfig->save();
                }
            }
        }

        return redirect()->back()->with('success',__('Cập nhật thành công !'));
    }
}
