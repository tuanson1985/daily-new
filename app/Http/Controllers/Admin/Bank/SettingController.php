<?php

namespace App\Http\Controllers\Admin\Bank;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Bank\SettingRequest;
use App\Models\Activity;
use App\Models\Bank;
use App\Models\Group;
use App\Models\Setting;
use Auth;
use Illuminate\Http\Request;
use Session;


class SettingController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $page_breadcrumbs;
    protected $module;


    public function __construct()
    {

                $this->middleware('permission:bank-setting');
        //		$this->middleware('permission:game-item-create', ['only' => ['create', 'store']]);
        //		$this->middleware('permission:game-item-edit', ['only' => ['edit', 'update']]);
        //		$this->middleware('permission:game-item-delete', ['only' => ['destroy']]);

        $this->module="bank-setting";
        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'.index'),
                'title' => "Cấu hình rút tiền ATM"
            ];
        }

    }

    public function index(Request $request)
    {

        if ($request->ajax()) {

            $datatable = Bank::orderBy('created_at', 'desc');

            if ($request->filled('id')) {
                $datatable->where('id', 'LIKE', '%' . $request->get('id') . '%');
                $datatable->orWhere('idkey', 'LIKE', '%' . $request->get('id') . '%');
            }

            if ($request->filled('title'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
                });
            }


            if ($request->filled('bank_type')) {
                $datatable->where('bank_type', '=', $request->get('bank_type'));
            }
            if ($request->filled('status')) {
                $datatable->where('status',  $request->get('status'));
            }

            return $datatable =\datatables()->eloquent($datatable)
                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->addColumn('action', function ($row) {

                    $temp = "<a href=\"" . route('admin.bank-setting.edit', $row->id) . "\"  rel=\"$row->id\" class=\"m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill edit_toggle \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp .= "<a  rel=\"$row->id\" class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })->setTotalRecords($datatable->count())->toJson();
        }

        //$setting = Setting::pluck('value','key');
        return view('admin.bank.setting.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
            //->with('setting',$setting);

    }


    /**
     * Show the form for creating a new newscategory
     *
     * @return Response
     */
    public function create(Request $request)
    {
        return view('admin.bank.setting.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    /**
     * Store a newly created newscategory in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {

        $input = $request->all();
        $input['fee'] = str_replace(array(' ', ','), '', $request->fee);
        Bank::create($input);
        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Thêm mới thành công'));
        }
        else {
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
        }





    }

    /**
     * Display the specified newscategory.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified newscategory.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {

        $data = Bank::findOrFail($id);
        return view('admin.bank.setting.create_edit', compact('data'))
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);


    }

    /**
     * Update the specified newscategory in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {

        $data = Bank::findOrFail($id);
        $input = $request->all();
        $input['fee'] = (float)str_replace(array(' ', '.'), '', $request->fee);
        $data->update($input);

        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Cập nhật thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
        }
    }

    /**
     * Remove the specified newscategory from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(Request $request)
    {


        $input = explode(',', $request->id);

        Bank::whereIn('id', $input)->update([
            'status' => 0
        ]);

        return redirect()->route('admin.bank-setting.index')->with('success', trans('admin/message.delete_success'));
    }


    public function LoadAttribute(Request $request)
    {
        $category_id = $request->category_id;
        $group = Group::where('module', '=', config('constants.module.game-setting.key_app'))->findOrFail($category_id);
        //tìm data attribute
        $params = json_decode($group->params);
        if ($params) {
            $dataAttribute = Group::where('module', '=', config('constants.module.game-setting.key_attribute'))->whereIn('id', $params)->get();
        } else {
            $dataAttribute = null;
        }
        return view('admin.bank.setting.load-attribute', compact('dataAttribute'));
    }

    public function UpdatePrice(Request $request)
    {

        $item = Bank::findOrFail($request->id);

        $item->fee = str_replace(array(' ', ','), '', $request->price);
        $item->save();
        return response()->json(['success' => true]);
    }

}
