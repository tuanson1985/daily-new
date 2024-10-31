<?php

namespace App\Http\Controllers\Admin\Provider;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Provider;
use Html;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProviderController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;
    public function __construct(Request $request)
    {

        $this->module="provider";

        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');
        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store']]);
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

        $datatable= Provider::query();

        $datatable=$datatable->get();
        $data=$this->getHTMLCategory($datatable);
        $dataCategory = Provider::query()->get();

        return view('admin.provider.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('dataCategory', $dataCategory);

    }

    public function create(Request $request)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];

        $dataCategory = Provider::query()->get();

        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.provider.create_edit')
            ->with('module', $this->module)
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

        $data= Provider::create($input);

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
        //return view('admin.service.category.show', compact('datatable'));
    }

    public function edit(Request $request,$id)
    {

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];
        $data = Provider::query()->findOrFail($id);
        $dataCategory = Provider::query()
            ->where('id','!=',$id)->get();

        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        return view('admin.provider.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('dataCategory', $dataCategory);

    }

    public function update(Request $request,$id)
    {
        $data =  Provider::query()->findOrFail($id);

        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
        ]);

        $input=$request->all();

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

        Provider::query()->whereIn('id',$input)->delete();
        ActivityLog::add($request, 'Xóa thành công '.$this->module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Xóa thành công !'));
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


        $data=Group::where('module','=',$this->module)::whereIn('id',$input)->update([
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
        //dd($item);
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
                                      <span></span> ".HTML::entities($item->title)."
                                    </label>
                                </div>";


                }
                else{

                    $result.="<div class=\"m-checkbox\">
                                    <label class=\"checkbox checkbox-outline\">
                                    <input  type=\"checkbox\" rel=\"{$item->id}\" class=\"children_of_{$item->parent_id}\"  >
                                    <span></span> ".HTML::entities($item->title)."
                                    </label>
                                </div>";
                }
                $result .= "<div class='btnControll'>";

                $result .= "<a href='" . route("admin.".$this->module.".edit",$item->id) . "' class='btn btn-sm btn-primary'>Sửa</a>
                   
                </div>
              </div>" . $this->buildMenu($menu, $item->id) . "</li>";
            }
        return $result ? "\n<ol class=\"dd-list\">\n$result</ol>\n" : null;
    }

}
