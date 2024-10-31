<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\LanguageNation;
use Carbon\Carbon;
use Illuminate\Http\Request;



class LanguageNationController extends Controller
{

    protected $page_breadcrumbs;

    public function __construct()
    {
        //set permission to function
        $this->middleware("permission:language-nation-list");



        $this->page_breadcrumbs[] = [
            'page' => route('admin.language-nation.index'),
            'title' => __("Ngôn ngữ hệ thống")
        ];
    }

    public function index(Request $request)
    {

        ActivityLog::add($request, 'Truy cập danh sách language-nation');
        if($request->ajax) {
            $datatable= LanguageNation::query();

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

            if ($request->filled('started_at')) {
                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }

            return \datatables()->eloquent($datatable)

                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->addColumn('action', function($row) {
                    $temp= "<a href=\"".route('admin.language-nation.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->toJson();
        }

        return view('admin.language-nation.index')->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    public function create(Request $request)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];
        ActivityLog::add($request, 'Vào form create language-nation');
        return view('admin.language-nation.create_edit')->with('page_breadcrumbs', $this->page_breadcrumbs);
    }


    public function store(Request $request)
    {
        $this->validate($request,[
            'title'=>'required',
            'locale'=>'required|max:10|unique:language_nation'
        ],[
            'title.required' => __('Vui lòng nhật tiêu đề'),
            'locale.required' =>__('Từ khóa đã tồn tại'),
            'locale.max' =>__('Từ khóa chỉ được tối đa 10 ký tự'),
            'locale.unique' =>__('Từ khóa đã tồn tại')
        ]);
        $input=$request->all();

        if($request->is_default==1){
            LanguageNation::where('is_default',1)->update([
                'is_default'=>0
            ]);
        }

        $data=LanguageNation::create($input);

        ActivityLog::add($request, 'Tạo mới thành công language-nation #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.language-nation.index')->with('success',__('Thêm mới thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }
    }


    public function show($id)
    {
        //$data = LanguageNation::findOrFail($id);
        //ActivityLog::add($request, 'Show language-nation #'.$data->id);
        //return view('admin.language-nation.show', compact('datatable'));
    }

    public function edit(Request $request,$id)
    {

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];
        $data = LanguageNation::findOrFail($id);

        ActivityLog::add($request, 'Vào form edit language-nation #'.$data->id);
        return view('admin.language-nation.create_edit')
            ->with('data', $data)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    public function update(Request $request,$id)
    {
        $data = LanguageNation::findOrFail($id);


        $this->validate($request,[
            'title'=>'required',
            'locale'=>'required|max:10|unique:language_nation,locale,'.$id
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
            'locale.required' =>__('Từ khóa đã tồn tại'),
            'locale.max' =>__('Từ khóa chỉ được tối đa 10 ký tự'),
            'locale.unique' =>__('Từ khóa đã tồn tại')
        ]);

        $input = $request->all();
        if($request->is_default==1){
            LanguageNation::where('is_default',1)->update([
                'is_default'=>0
            ]);
        }
        $data->update($input);
        ActivityLog::add($request, 'Cập nhật thành công language-nation #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.language-nation.index')->with('success',__('Cập nhật thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
        }
    }

    public function destroy(Request $request)
    {
        $input=explode(',',$request->id);
        LanguageNation::whereIn('id',$input)->delete();

        ActivityLog::add($request, 'Xóa thành công language-nation #'.json_encode($input));
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


        $data=LanguageNation::whereIn('id',$input)->update([
            $field=>$value
        ]);

        ActivityLog::add($request, 'Cập nhật field thành công language-nation '.json_encode($whitelist).' #'.json_encode($input));

        return response()->json([
            'success'=>true,
            'message'=>__('Cập nhật thành công !'),
            'redirect'=>''
        ]);

    }

}
