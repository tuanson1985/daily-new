<?php

namespace App\Http\Controllers\Admin\Transfer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Item;
use Carbon\Carbon;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
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

        $this->module='transfer-bank';
        $this->moduleCategory=null;
        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');
        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate']]);
        $this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);



        $this->page_breadcrumbs[] = [
            'page' => route('admin.'.$this->module.'.index'),
            'title' => __(config('module.'.$this->module.'.title'))
        ];
    }

    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);
        if($request->ajax) {
            $datatable= Item::with(array('groups' => function ($query) {
                $query->where('module', $this->moduleCategory);

                $query->select('groups.id','title');
            }))->where('module', $this->module);

            if ($request->filled('group_id')) {
                $datatable->whereHas('groups', function ($query) use ($request) {
                    $query->where('group_id',$request->get('group_id'));
                });
            }

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
            if ($request->filled('position')) {
                $datatable->where('position',$request->get('position') );
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
            if ($request->filled('shop_id')) {
                $datatable->where('shop_id', $request->get('shop_id'));
            }
            if ($request->filled('shop_id')) {
                $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                if(empty($shop_id_shop_access) || $shop_id_shop_access == 'all'){
                    $datatable->whereIn('shop_id', $request->get('shop_id'));
                }
                else{
                    $shop_id_shop_access_search = array_intersect($shop_id_shop_access,$request->get('shop_id'));
                    $datatable->whereIn('shop_id', $shop_id_shop_access_search);
                }
            }
            else{
                if(session('shop_id')){
                    $datatable->where('shop_id',session('shop_id'));
                }
                else{
                    if(isset(Auth::user()->shop_access) &&Auth::user()->shop_access !== "all"){
                        $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                        $datatable->whereIn('shop_id',$shop_id_shop_access);
                    }
                }
            }
            return \datatables()->eloquent($datatable)
                ->only([
                    'id',
                    'shop_id',
                    'title',
                    'image',
                    'key',
                    'ratio',
                    'order',
                    'gate_id',
                    'note',
                    'status',
                    'created_at',
                    'action'
                ])
                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->editColumn('ratio', function($data) {
                    return number_format($data->ratio);
                })
                ->editColumn('shop_id', function($data) {
                    $temp= '';
                    if(isset($data->shop_id)){
                        $temp .=  $data->shop->domain;
                    }
                    return $temp;
                })
                ->addColumn('action', function($row) {
                    $temp= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->toJson();
        }
        $shop_access_user = Auth::user()->shop_access;
        $shop = Shop::orderBy('id','desc');
        if(isset($shop_access_user) && $shop_access_user !== "all"){
            $shop_access_user = json_decode($shop_access_user);
            $shop = $shop->whereIn('id',$shop_access_user);
        }
        $shop = $shop->get();
        return view('admin.transfer.bank.index')
        ->with('module', $this->module)
        ->with('shop', $shop)
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
        $dataCategory = null;
        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.transfer.bank.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
            // ->with('dataCategory', $dataCategory);
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
            'title'=>'required',
            'account_name'=>'required',
            'number_account'=>'required',
            'ratio'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tên ngân hàng'),
            'account_name.required' => __('Vui lòng nhập chủ tài khoản'),
            'number_account.required' => __('Vui lòng nhập số tài khoản'),
            'ratio.required' => __('Vui lòng nhập chiết khấu'),
        ]);
        if(empty(session('shop_id')) || session('shop_id') == null){
            return redirect()->back()->withErrors('Bạn chưa chọn shop cấu hình');
        }
        $ratio = (float)$request->ratio;
		if(!is_float($ratio)){
			return redirect()->back()->withErrors('% chiết khấu không hợp lệ');
		}
		if($ratio < 60 || $ratio > 200){
			return redirect()->back()->withErrors('% chiết khấu phải trong phạm vi lớn hơn 60 và nhỏ hơn 200');
		}
        $title = $request->title;
        $account_name = $request->account_name;
        $number_account = $request->number_account;
        $params = [
            'account_name' => $account_name,
            'number_account' => $number_account,
        ];
        $params = json_decode(json_encode($params), FALSE);
        $input['module']=$this->module;
        $input['title']=$title;
        $input['params']=$params;
        $input['image']=$request->image;
        $input['status']=$request->status;
        $input['order']=$request->order;
        $input['ratio']=$ratio;
        $input['author_id']=auth()->user()->id;
        $data=Item::create($input);
        ActivityLog::add($request, 'Tạo mới thành công '.$this->module.' #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Thêm mới thành công !'));
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
        $data = Item::findOrFail($id);
        if($data->shop_id){
            $shop = Shop::findOrFail($data->shop_id);
            session()->put('shop_id', $shop->id);
            session()->put('shop_name', $shop->domain);
        }
        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        return view('admin.transfer.bank.create_edit')
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
            'account_name'=>'required',
            'number_account'=>'required',
            'ratio'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tên ngân hàng'),
            'account_name.required' => __('Vui lòng nhập chủ tài khoản'),
            'number_account.required' => __('Vui lòng nhập số tài khoản'),
            'ratio.required' => __('Vui lòng nhập chiết khấu'),
        ]);
        if(empty(session('shop_id')) || session('shop_id') == null){
            return redirect()->back()->withErrors('Bạn chưa chọn shop cấu hình');
        }
        $data =  Item::where('module', '=', $this->module)->findOrFail($id);
        $ratio = (float)$request->ratio;
		if(!is_float($ratio)){
			return redirect()->back()->withErrors('% chiết khấu không hợp lệ');
		}
		if($ratio < 60 || $ratio > 200){
			return redirect()->back()->withErrors('% chiết khấu phải trong phạm vi lớn hơn 60 và nhỏ hơn 200');
		}
        $title = $request->title;
        $account_name = $request->account_name;
        $number_account = $request->number_account;
        $params = [
            'account_name' => $account_name,
            'number_account' => $number_account,
        ];
        $params = json_decode(json_encode($params), FALSE);
        $input['module']=$this->module;
        $input['title']=$title;
        $input['params']=$params;
        $input['image']=$request->image;
        $input['status']=$request->status;
        $input['order']=$request->order;
        $input['ratio']=$ratio;
        $input['author_id']=auth()->user()->id;
        $data->update($input);
        ActivityLog::add($request, 'Chỉnh sửa thành công'.$this->module.' #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Cập nhật thành công !'));
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
        $data =  Item::where('module', '=', $this->module)->findOrFail($request->id);
        $data->status = 0;
        $data->save();
        return redirect()->back()->with('success',__('Xóa thành công !'));
    }
}
