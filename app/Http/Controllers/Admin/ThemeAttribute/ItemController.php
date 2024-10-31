<?php

namespace App\Http\Controllers\Admin\ThemeAttribute;

use App\Http\Controllers\Controller;
use App\Models\ThemeAttribute;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\ActivityLog;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;

    public function __construct(Request $request)
    {

        $this->module='theme-attribute';
        $this->moduleCategory=null;
        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');
        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate']]);
        $this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);



        $this->page_breadcrumbs[] = [
            'page' => route('admin.'.$this->module.'.index'),
            'title' => "Tất cả thuộc tính theme"
        ];
    }

    public function index(Request $request)
    {

        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);

        if($request->ajax) {

            $datatable=ThemeAttribute::where('status','<>','999');

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

            return \datatables()->eloquent($datatable)
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



        return view('admin.'.$this->module.'.index')
            ->with('module', $this->module)
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

        if( $this->moduleCategory==null){
            $dataCategory=null;
        }
        else{
            //$dataCategory = Group::where('module', '=',  $this->moduleCategory)->orderBy('order','asc')->get();
        }

        $dataCategory = null;
        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.'.$this->module.'.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('dataCategory', $dataCategory);
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
        $params = $request->except([
            '_method',
            '_token',
            'key',
            'title',
            'status',
            'submit-close',
            'link'
        ]);
        $input = [
            'key' => $request->key,
            'link' => $request->link,
            'title' => $request->title,
            'param_attribute' => json_encode($params, JSON_UNESCAPED_UNICODE),
            'status' => $request->status,
            'created_at' => Carbon::now()
        ];

        $data=ThemeAttribute::create($input);

        ActivityLog::add($request, 'Tạo mới thành công '.$this->module.' #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }

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
    public function edit(Request $request, $id)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];
        $data = ThemeAttribute::findOrFail($id);
        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        return view('admin.'.$this->module.'.create_edit')
            ->with('module', $this->module)
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
        $this->validate($request,[
            'title'=>'required',
            'status' => 'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
            'status.required' => __('Vui lòng chọn trạng thái')
        ]);
        $data =  ThemeAttribute::findOrFail($id);
        $params = $request->except([
            '_method',
            '_token',
            'key',
            'title',
            'status',
            'submit-close',
            'link',
            'is_image'
        ]);
        $input = [
            'link' => $request->link,
            'is_image' => $request->is_image,
            'key' => $request->key,
            'param_attribute' => json_encode($params, JSON_UNESCAPED_UNICODE),
            'title' => $request->title,
            'status' => $request->status
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
        $data =  ThemeAttribute::where("id",$input)->first();
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function attribute(Request $request)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];

        if( $this->moduleCategory==null){
            $dataCategory=null;
        }
        else{
            //$dataCategory = Group::where('module', '=',  $this->moduleCategory)->orderBy('order','asc')->get();
        }

        $dataCategory = null;
        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.'.$this->module.'.item.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('dataCategory', $dataCategory);
    }


}
