<?php

namespace App\Http\Controllers\Admin\Minigame\Module;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Shop;
use Html;
use Illuminate\Http\Request;


class ItemTypeController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;
    public function __construct(Request $request)
    {

        $this->module=$request->segments()[1]??"";

        //set permission to function
        $this->middleware('permission:'. $this->module);


        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'.index'),
                'title' => __(config('module.minigame.itemtype.title'))
            ];
        }
    }

    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);
        
        if($request->ajax) {
            $datatable= Group::where('module','=',$this->module);
            // if (session('shop_id')) {
            //     $datatable->where('shop_id',session('shop_id'));
            // }
            return \datatables()->eloquent($datatable)
                ->only([
                    'id',
                    'title',
                    'locale',
                    'groups',
                    'order',
                    'position',
                    'status',
                    'action',
                    'created_at',
                    'slug'
                ])


                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->addColumn('action', function($row) {
                    $temp= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."?position=".$row->position."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->toJson();
        }

        return view('admin.minigame.module.itemtype.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);

    }

    public function create(Request $request)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];

        $dataCategory = Group::where('module', '=', $this->module)->orderBy('order','asc')->get();

        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.minigame.module.itemtype.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('dataCategory', $dataCategory);

    }


    public function store(Request $request)
    {
        // if(!session('shop_id')){
        //     return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        // }
        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
        ]);
        $input=$request->all();
        $input['module']=$this->module;
        // $input['shop_id'] = session('shop_id');

        $data=Group::create($input);

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
        $data = Group::where('module', '=', $this->module)->findOrFail($id);
        // if($data->shop_id){
        //     $shop = Shop::findOrFail($data->shop_id);
        //     session()->put('shop_id', $shop->id);
        //     session()->put('shop_name', $shop->domain);
        // }
        $dataCategory = Group::where('module', '=', $this->module)->where('id','!=',$id)->orderBy('order','asc')->get();

        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        return view('admin.minigame.module.itemtype.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('dataCategory', $dataCategory);

    }

    public function update(Request $request,$id)
    {
        // if(!session('shop_id')){
        //     return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        // }
        $data =  Group::where('module', '=', $this->module)->findOrFail($id);
        
        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
        ]);

        $input=$request->all();
        $input['module']=$this->module;
        // $input['shop_id'] = session('shop_id');
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

        Group::where('module','=',$this->module)->whereIn('id',$input)->delete();
        ActivityLog::add($request, 'Xóa thành công '.$this->module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Xóa thành công1 !'));
    }
}
