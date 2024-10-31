<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LanguageKey;
use Carbon\Carbon;
use Illuminate\Http\Request;



class LanguageMappingController extends Controller
{

    protected $page_breadcrumbs;

    public function __construct()
    {
        //set permission to function
        $this->middleware("permission:language-mapping-list");



        $this->page_breadcrumbs[] = [
            'page' => route('admin.language-mapping.index'),
            'title' => __("Biên dịch")
        ];
    }

    public function index(Request $request)
    {


        if($request->ajax) {
            $datatable= LanguageKey::query();
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
                    $temp= "<a href=\"".route('admin.language-mapping.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->toJson();
        }

        return view('admin.language-mapping.index')->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    public function create()
    {

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];
        return view('admin.language-mapping.create_edit')->with('page_breadcrumbs', $this->page_breadcrumbs);
    }


    public function store(Request $request)
    {
        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhật tiêu đề'),
        ]);
        $input=$request->all();
        LanguageKey::create($input);

        if($request->filled('submit-close')){
            return redirect()->route('admin.language-mapping.index')->with('success',__('Thêm mới thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }
    }


    public function show($id)
    {
        //$datatable = LanguageKey::findOrFail($id);
        //return view('admin.language-mapping.show', compact('datatable'));
    }

    public function edit($id)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];
        $data = LanguageKey::findOrFail($id);
        return view('admin.language-mapping.create_edit')
            ->with('data', $data)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    public function update(Request $request,$id)
    {
        $data = LanguageKey::findOrFail($id);

        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),

        ]);

        $input = $request->all();
        $data->update($input);

        if($request->filled('submit-close')){
            return redirect()->route('admin.language-mapping.index')->with('success',__('Cập nhật thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
        }
    }

    public function destroy(Request $request)
    {
        $input=explode(',',$request->id);
        LanguageKey::whereIn('id',$input)
            ->delete();
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


        $data=LanguageKey::whereIn('id',$input)->update([
            $field=>$value
        ]);

        return response()->json([
            'success'=>true,
            'message'=>__('Cập nhật thành công !'),
            'redirect'=>''
        ]);

    }

}
