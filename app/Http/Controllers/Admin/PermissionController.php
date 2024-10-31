<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Spatie\Permission\Models\Permission;
use Html;
use Illuminate\Http\Request;



class PermissionController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */

    protected $user;
    protected $page_breadcrumbs;

	public function __construct()
	{

        $this->middleware(function ($request, $next) {

//            if (strtolower(Auth::guard()->user()->username) != "admin") {
//                abort('403');
//            }

            return $next($request);
        });
		//set permission to function
        $this->middleware('role:admin');
        //		$this->middleware('permission:permission-list');
		//		$this->middleware('permission:permission-create', ['only' => ['create','store']]);
		//		$this->middleware('permission:permission-edit', ['only' => ['edit','update']]);
		//		$this->middleware('permission:permission-delete', ['only' => ['destroy']]);

        $this->page_breadcrumbs = [
            [   'page' => route('admin.permission.index'),
                'title' => "Phân quyền truy cập",
            ],

        ];
	}

	public function index(Request $request)
	{
        ActivityLog::add($request, 'Truy cập danh sách permission');
		$data=Permission::orderBy('order','asc')->get();
		$datatable=$this->getHTMLCategory($data);

		return view('admin.permission.index')
        ->with('page_breadcrumbs',$this->page_breadcrumbs)
        ->with('datatable',$datatable);

	}


	/**
	 * Show the form for creating a new newscategory
	 *
	 * @return Response
	 */
    public function create(Request $request)
    {
        $dataCategory= Permission::orderBy('order','asc')->get();
        ActivityLog::add($request, 'Vào form create permission');
        $id = null;

        if (count($request->all())){
            $all = $request->all();
            $id = $all['id'];
        }

        return view('admin.permission.create_edit', compact('dataCategory','id'));
    }

	/**
	 * Store a newly created newscategory in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{

        $this->validate($request,[
            'title'=>'required',
            'name'=>'required|unique:permissions'
        ],[
            'title.required' => __('Vui lòng nhật tiêu đề'),
            'name.required' =>__('Vui lòng nhâp từ khóa name'),
            'name.unique' =>__('Keyword đã tồn tại')
        ]);
		$input=$request->all();
        $data=Permission::create($input);

        ActivityLog::add($request, 'Tạo mới thành công permission #'.$data->id);
		return redirect()->route('admin.permission.index')
            ->with('success',__('Thêm mới thành công !'));

	}

	/**
	 * Display the specified newscategory.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function show($id)
	{
        //$data = Permission::findOrFail($id);
        //ActivityLog::add($request, 'Show permission #'.$data->id);
		//return view('admin.permission.show', compact('item'));
	}

	/**
	 * Show the form for editing the specified newscategory.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function edit(Request $request,$id)
	{
		$data = Permission::findOrFail($id);
		$dataCategory= Permission::where('id','!=',$id)->orderBy('order','asc')->get();
        ActivityLog::add($request, 'Vào form edit user-qtv #'.$data->id);
		return view('admin.permission.create_edit', compact('data','dataCategory'));

	}

	/**
	 * Update the specified newscategory in storage.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function update(Request $request,$id)
	{
		$data = Permission::findOrFail($id);

        $this->validate($request,[
            'title'=>'required',
            'name'=>'required|unique:permissions,name,'.$id
        ],[
            'title.required' => __('Vui lòng nhật tiêu đề'),
            'title.required' => __('Vui lòng nhâp từ khóa name'),
            'name.required' =>__('Keyword đã tồn tại'),
            'name.unique' =>__('Keyword đã tồn tại')
        ]);

		$input = $request->all();
        $data->update($input);
        ActivityLog::add($request, 'Cập nhật thành công permission #'.$data->id);
		return redirect()->route('admin.permission.index')->with('success',__('Cập nhật thành công !'));

	}

	/**
	 * Remove the specified newscategory from storage.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function destroy(Request $request)
	{

		$input=explode(',',$request->id);
		Permission::destroy($input);
        ActivityLog::add($request, 'Xóa thành công permission #'.json_encode($input));
		return redirect()->route('admin.permission.index')->with('success',__('Xóa thành công !'));
	}


	// AJAX Reordering function
	public function order(Request $request)
	{


		$source = e($request->get('source'));
		$destination = $request->get('destination');

		$item = Permission::find($source);
		$item->parent_id = isset($destination)?$destination:0;
		$item->save();

		$ordering = json_decode($request->get('order'));

		$rootOrdering = json_decode($request->get('rootOrder'));

		if ($ordering) {
			foreach ($ordering as $order => $item_id) {
				if ($itemToOrder = Permission::find($item_id)) {
					$itemToOrder->order = $order;
					$itemToOrder->save();
				}
			}
		} else {
			foreach ($rootOrdering as $order => $item_id) {
				if ($itemToOrder = Permission::find($item_id)) {
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
		foreach ($menu as $key => $item)
			if ($item->parent_id == $parent_id) {
				$result .= "<li class='dd-item nested-list-item' data-order='{$item->order}' data-id='{$item->id}'>
              <div class='dd-handle nested-list-handle'>
                <span class='la la-arrows-alt'></span>
              </div>
              <div class='nested-list-content'>";
				$url = $item->url ?? "#";
				if($parent_id!=0){
                    $result.="<div class=\"m-checkbox\">
                                    <label class=\"checkbox checkbox-outline\">
                                    <input  type=\"checkbox\" rel=\"{$item->id}\" class=\"children_of_{$item->parent_id}\"  >
                                      <span></span><a href=\"$url\" style='color:#333' target='_blank'>".HTML::entities($item->title).' - ['.HTML::entities($item->name)."]</a>
                                    </label>
                                </div>";


				}
				else{

                    $result.="<div class=\"m-checkbox\">
                                    <label class=\"checkbox checkbox-outline\">
                                    <input  type=\"checkbox\" rel=\"{$item->id}\" class=\"children_of_{$item->parent_id}\"  >
                                    <span></span> <a href=\"$url\" style='color:#333' target='_blank'>".HTML::entities($item->title).' - ['.HTML::entities($item->name)."]</a>
                                    </label>
                                </div>";
				}
				$result .= "<div class='btnControll'>";

				$result .= "
				<a href='#' class='btn btn-sm btn-success edit_toggle' data-url='" . route("admin.permission.create",['id'=>$item->id]) . "' rel='{$item->id}' >Thêm mới</a>
				<a href='#' class='btn btn-sm btn-primary edit_toggle' data-url='" . route("admin.permission.edit",$item->id) . "' rel='{$item->id}' >Sửa</a>
                    <a href=\"#\" class=\"btn btn-sm btn-danger  delete_toggle \" rel=\"{$item->id}\">
                                        Xóa
                    </a>
                </div>
              </div>" . $this->buildMenu($menu, $item->id) . "</li>";
			}
		return $result ? "\n<ol class=\"dd-list\">\n$result</ol>\n" : null;
	}










}
