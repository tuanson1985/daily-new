<?php

namespace App\Http\Controllers\Admin\ServerType;

use App\Http\Controllers\Controller;
use App\Models\Server_Category;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Html;
use App\Models\ActivityLog;

class ItemController extends Controller
{
    protected $page_breadcrumbs;
    protected $module;
    public function __construct(Request $request)
    {

        $this->module='server-type';

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

        $datatable=Server_Category::where('module','type')->where('status','<>','0');

//        if ($request->filled('shop_id')) {
//            $datatable->where('shop_id', $request->get('shop_id'));
//        }
//        else{
//            if(session('shop_id')){
//                $datatable->where('shop_id',session('shop_id'));
//            }
//            else{
//                if(isset(Auth::user()->shop_access) &&Auth::user()->shop_access !== "all"){
//                    $shop_id_shop_access = json_decode(Auth::user()->shop_access);
//                    $datatable->whereIn('shop_id',$shop_id_shop_access);
//                }
//            }
//        }
        $datatable=$datatable->get();
        $data=$this->getHTMLCategory($datatable);
        $dataCategory = Server_Category::where('module','type')->where('status','<>','0')->get();

        return view('admin.'.$this->module.'.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)->with('dataCategory',$dataCategory);

    }

    public function create(Request $request)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];

        $dataCategory = Server_Category::where('module','type')->where('status','<>','0')->get();
        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.'.$this->module.'.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)->with('dataCategory',$dataCategory);

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
            'title'=>'required'
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề')
        ]);

        $input = [
            'module' => 'type',
            'parent_id' => $request->parent_id,
            'title' => $request->title,
            'description' => $request->description,
            'image' => $request->image,
            'status' => $request->status,
            'created_at' => Carbon::now(),
            'content' => $request->get('content')
        ];

        $data=Server_Category::create($input);

        ActivityLog::add($request, 'Tạo mới thành công '.$this->module.' #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }

    }


    public function show(Request $request,$id)
    {
        //$data = Group::findOrFail($id);
        //ActivityLog::add($request, 'Show '.$this->module.' #'.$data->id);
        //return view('admin.module.category.show', compact('datatable'));
    }

    public function edit(Request $request, $id)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];
        $data = Server_Category::findOrFail($id);
        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        $dataCategory = Server_Category::where('module','type')->where('status','<>','0')->where('id','<>',$id)->get();
        return view('admin.'.$this->module.'.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)->with('dataCategory',$dataCategory);
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
        $this->validate($request,[
            'title'=>'required',
            'status' => 'required'
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
            'status.required' => __('Vui lòng chọn trạng thái')
        ]);
        $data =  Server_Category::findOrFail($id);
        $input = [
            'module' => 'type',
            'parent_id' => $request->parent_id,
            'title' => $request->title,
            'status' => $request->status,
            'description' => $request->description,
            'image' => $request->image,
            'content' => $request->get('content')
        ];
        $data->update($input);
        ActivityLog::add($request, 'Cập nhật thành công '.$this->module.' #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
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
        $data =  Server_Category::where("id",$input)->first();
        if($data) {
            $data->status = 0;
            $data->save();
            ActivityLog::add($request, 'Xóa thành công ' . $this->module . ' #' . json_encode($input));
            return redirect()->back()->with('success', __('Xóa thành công !'));
        }
        else{
            return redirect()->back()->with('success', __('Xóa thất bại, không tồn tại ID xóa !'));
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


        $data=Server_Category::whereIn('id',$input)->update([
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
        //$destination = $request->get('destination');

        $item = Server_Category::find($source);
        //dd($item);
        //$item->parent_id = isset($destination)?$destination:0;
        $item->save();

        $ordering = json_decode($request->get('order'));

        $rootOrdering = json_decode($request->get('rootOrder'));

        if ($ordering) {
            foreach ($ordering as $order => $item_id) {
                if ($itemToOrder = Server_Category::find($item_id)) {
                    $itemToOrder->order = $order;
                    $itemToOrder->save();
                }
            }
        } else {
            foreach ($rootOrdering as $order => $item_id) {
                if ($itemToOrder = Server_Category::find($item_id)) {
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
                if ($item->status == 1) {
                    $result .= "<a href='#' class=''  title='Đang hoạt động'><img src='" . asset('/assets/backend/images/check.png') . "' alt='Đang hoạt động' /></a>&nbsp;";
                } else {
                    $result .= "<a href='#' class='' title='Ngưng hoạt động'><img src='" . asset('/assets/backend/images/uncheck.png') . "' alt='Ngưng hoạt động' /></a>&nbsp;";
                }
                $result .= "<a href='" . route("admin.".$this->module.".edit",$item->id) . "' class='btn btn-sm btn-primary'>Sửa</a>
                    <a href=\"#\" class=\"btn btn-sm btn-danger  delete_toggle \" rel=\"{$item->id}\">
                                        Xóa
                    </a>
                    <a href=\"#\" class=\"btn btn-sm btn-info  info_toggle \" rel=\"{$item->id}\" data-content=\"{$item->content}\">
                                        Thông tin
                    </a>
                </div>
              </div>" . $this->buildMenu($menu, $item->id) . "</li>";
            }
        return $result ? "\n<ol class=\"dd-list\">\n$result</ol>\n" : null;
    }
}
