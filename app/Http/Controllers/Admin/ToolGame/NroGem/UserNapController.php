<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 20/12/2018
 * Time: 14:43 CH
 */


namespace App\Http\Controllers\Admin\ToolGame\NroGem;
use App\Http\Controllers\Controller;
use App\Library\HelperPermisionShopMinigame;
use App\Models\ActivityLog;
use App\Models\Nrogem_AccNap;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserNapController extends Controller
{


    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;

    public function __construct()
    {
        $this->module='nrogem-usernap';
        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');
        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);

        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.nrogem-usernap.index'),
                'title' => __('Bán ngọc NRO - Thông tin bot nạp')
            ];
        }
    }



    public function index(Request $request)
    {

        if ($request->ajax()) {

            $model = Nrogem_AccNap::query();
            if ($request->filled('id')) {
                $model->where('id', 'LIKE', '%' . $request->get('id') . '%');
                $model->orWhere('idkey', 'LIKE', '%' . $request->get('id') . '%');
            }
            if ($request->filled('acc')) {
                $model->where('acc', 'LIKE', '%' . $request->get('acc') . '%');
            }

            if ($request->filled('status')) {
                $model->where('status', 'LIKE', '%' . $request->get('status') . '%');
            }
            if ($request->filled('started_at')) {

                $model->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $model->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }

            return $datatable = \datatables()->eloquent($model)
                ->blacklist(['pass'])
                ->editColumn('pass', function ($row) {
                    return "";
                })
                ->addColumn('action', function ($row) {
                    $temp = "<a href=\"" . route('admin.nrogem-usernap.edit', $row->id) . "\"  rel=\"$row->id\" class=\"m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp .= "<a  rel=\"$row->id\" class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })->toJson();

        }

        return view('admin.toolgame.nrogem.usernap.index')
        ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }


    /**
     * Show the form for creating a new newscategory
     *
     * @return Response
     */
    public function create()
    {


        return view('admin.toolgame.nrogem.usernap.create_edit')
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
        $input=$request->all();
        $item=Nrogem_AccNap::create($input);

        if($request->filled('submit-new')){
            $response=redirect()->route('admin.nrogem-usernap.create');
        }
        else {
            $response=redirect()->route('admin.nrogem-usernap.index');
        }
        return $response->with('success',trans('admin/message.add_success'));



    }

    /**
     * Display the specified newscategory.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //$datatable = Nrogem_AccNap::findOrFail($id);
        //return view('admin.toolgame.nrogem.usernap.show', compact('datatable'));
    }

    /**
     * Show the form for editing the specified newscategory.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {

        $data = Nrogem_AccNap::findOrFail($id);
        return view('admin.toolgame.nrogem.usernap.create_edit', compact('data'))
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);

    }

    /**
     * Update the specified newscategory in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(Request $request,$id)
    {

        $item = Nrogem_AccNap::findOrFail($id);
        $input = $request->all();
        $item->update($input);

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

        $input=explode(',',$request->id);
        Nrogem_AccNap::whereIn('id',$input)->delete();
        ActivityLog::add($request, 'Xóa thành công '.$this->module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Xóa thành công !'));
    }



}
