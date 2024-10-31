<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Group;
use Html;
use Illuminate\Http\Request;


class ArticleCategoryController extends Controller
{

    protected $page_breadcrumbs;

    public function __construct()
    {



        //set permission to function
        $this->middleware('permission:article-category-list');
        $this->middleware('permission:article-category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:article-category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:article-category-delete', ['only' => ['destroy']]);



        $this->page_breadcrumbs[] = [
            'page' => route('admin.article-category.index'),
            'title' => __("Danh mục bài viết")
        ];
    }

    public function index(Request $request)
    {

        ActivityLog::add($request, 'Truy cập danh sách article-category');

        $data= Group::where('module','=','article-category')->orderBy('order')->get();
        $data=$this->getHTMLCategory($data);
        $dataCategory = Group::where('module', '=', 'article-category')->orderBy('order','asc')->get();

        return view('admin.article-category.index')
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

        $dataCategory = Group::where('module', '=', 'article-category')->orderBy('order','asc')->get();

        ActivityLog::add($request, 'Vào form create article-category');
        return view('admin.article-category.create_edit')
            ->with('dataCategory', $dataCategory)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }


    public function store(Request $request)
    {
        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhật tiêu đề'),
        ]);
        $input=$request->all();
        $input['module']="article-category";
        $data=Group::create($input);

        ActivityLog::add($request, 'Tạo mới thành công article-category #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.article-category.index')->with('success',__('Thêm mới thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }
    }


    public function show($id)
    {
        //$data = LanguageNation::findOrFail($id);
        //ActivityLog::add($request, 'Show article-category #'.$data->id);
        //return view('admin.article-category.show', compact('datatable'));
    }

    public function edit(Request $request,$id)
    {

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];
        $data = Group::where('module', '=', 'article-category')->findOrFail($id);
        $dataCategory = Group::where('module', '=', 'article-category')->where('id','!=',$id)->orderBy('order','asc')->get();

        ActivityLog::add($request, 'Vào form edit article-category #'.$data->id);
        return view('admin.article-category.create_edit')
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('dataCategory', $dataCategory);

    }

    public function update(Request $request,$id)
    {
        $data =  Group::where('module', '=', 'article-category')->findOrFail($id);


        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
        ]);

        $input=$request->all();
        $input['module']="article-category";
        $data->update($input);

        ActivityLog::add($request, 'Cập nhật thành công article-category #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.article-category.index')->with('success',__('Cập nhật thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
        }
    }

    public function destroy(Request $request)
    {
        $input=explode(',',$request->id);

        Group::where('module','=',"article-category")->whereIn('id',$input)->delete();
        ActivityLog::add($request, 'Xóa thành công article-category #'.json_encode($input));
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


        $data=Group::where('module','=',"article-category")::whereIn('id',$input)->update([
            $field=>$value
        ]);

        ActivityLog::add($request, 'Cập nhật field thành công article-category '.json_encode($whitelist).' #'.json_encode($input));

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

        $item = Group::where('module', '=', 'article-category')->find($source);
        $item->parent_id = isset($destination)?$destination:0;
        $item->save();

        $ordering = json_decode($request->get('order'));

        $rootOrdering = json_decode($request->get('rootOrder'));

        if ($ordering) {
            foreach ($ordering as $order => $item_id) {
                if ($itemToOrder = Group::where('module', '=', 'article-category')->find($item_id)) {
                    $itemToOrder->order = $order;
                    $itemToOrder->save();
                }
            }
        } else {
            foreach ($rootOrdering as $order => $item_id) {
                if ($itemToOrder = Group::where('module', '=', 'article-category')->find($item_id)) {
                    $itemToOrder->order = $order;
                    $itemToOrder->save();
                }
            }
        }
        ActivityLog::add($request, 'Thay đổi STT thành công permission #'.$item->id);
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

                $result .= "<a href='" . route("admin.article-category.edit",$item->id) . "' class='btn btn-sm btn-primary'>Sửa</a>
                    <a href=\"#\" class=\"btn btn-sm btn-danger  delete_toggle \" rel=\"{$item->id}\">
                                        Xóa
                    </a>
                </div>
              </div>" . $this->buildMenu($menu, $item->id) . "</li>";
            }
        return $result ? "\n<ol class=\"dd-list\">\n$result</ol>\n" : null;
    }

}
