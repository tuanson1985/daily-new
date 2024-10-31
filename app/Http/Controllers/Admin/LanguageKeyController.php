<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\LanguageKey;
use Carbon\Carbon;
use Illuminate\Http\Request;



class LanguageKeyController extends Controller
{

    protected $page_breadcrumbs;

    public function __construct()
    {
        //set permission to function
        $this->middleware("permission:language-key-list");



        $this->page_breadcrumbs[] = [
            'page' => route('admin.language-key.index'),
            'title' => __("Từ khóa ngôn ngữ")
        ];
    }

    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập danh sách language-key');

        if($request->ajax) {
            $datatable= LanguageKey::query();

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
                    $temp= "<a href=\"".route('admin.language-key.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->toJson();
        }

        return view('admin.language-key.index')->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    public function create(Request $request)
    {

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];
        ActivityLog::add($request, 'Vào form create language-key');
        return view('admin.language-key.create_edit')->with('page_breadcrumbs', $this->page_breadcrumbs);
    }


    public function store(Request $request)
    {
        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhật tiêu đề'),
        ]);
        $input=$request->all();

        $data=LanguageKey::create($input);


        ActivityLog::add($request, 'Tạo mới thành công language-key #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.language-key.index')->with('success',__('Thêm mới thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }
    }


    public function show($id)
    {
        //$data = LanguageKey::findOrFail($id);
        //ActivityLog::add($request, 'Show language-key #'.$data->id);
        //return view('admin.language-key.show', compact('datatable'));
    }

    public function edit(Request $request,$id)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];
        $data = LanguageKey::findOrFail($id);

        ActivityLog::add($request, 'Vào form edit language-key #'.$data->id);
        return view('admin.language-key.create_edit')
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

        ActivityLog::add($request, 'Cập nhật thành công language-key #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.language-key.index')->with('success',__('Cập nhật thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
        }
    }

    public function destroy(Request $request)
    {
        $input=explode(',',$request->id);
        LanguageKey::whereIn('id',$input)->delete();

        ActivityLog::add($request, 'Xóa thành công language-key #'.json_encode($input));
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

        ActivityLog::add($request, 'Cập nhật field thành công language-key '.json_encode($whitelist).' #'.json_encode($input));

        return response()->json([
            'success'=>true,
            'message'=>__('Cập nhật thành công !'),
            'redirect'=>''
        ]);

    }

}
