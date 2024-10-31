<?php

namespace App\Http\Controllers\Admin\ShopGroup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Shop_Group;
use App\Models\Shop;
use App\Models\Shop_Group_Shop;
use Html;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Validator;


class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $page_breadcrumbs;
    protected $module;

    public function __construct(Request $request)
    {
        $this->module=$request->segments()[1]??"";

        //set permission to function
        $this->middleware('permission:shop-group-list');
        $this->middleware('permission:shop-group-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:shop-group-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:shop-group-delete', ['only' => ['destroy']]);
        $this->middleware('permission:shop-group-item', ['only' => ['getShopInGroup','getSearchShop','updateShopInGroup','deleteShopInGroup']]);
        // $this->middleware('role:admin', ['only' => ['getPartNer']]);
        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.shop-group.index'),
                'title' => __(config('module.shop-group.title'))
            ];
        }
    }
    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);
        if($request->ajax) {
            $datatable= Shop_Group::with('shop');
            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', $request->get('id'));
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

            return \datatables()->eloquent($datatable)

                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->editColumn('status', function($data) {
                    $temp = '';
                    $temp .= '<span class="switch switch-outline switch-icon switch-success btn-update-stt" data-id="'.$data->id.'">';
                    $temp .= '<label>';
                    if($data->status == 1){
                        $temp .= '<input type="checkbox" checked="checked" name="select">';
                    }
                    else{
                        $temp .= '<input type="checkbox" name="select">';
                    }
                    $temp .= '<span></span>';
                    $temp .= '</label>';
                    $temp .= '</span>';
                    return $temp;
                })
                ->addColumn('count',function($row){
                    return $row->shop->count();
                })
                ->addColumn('action', function($row) {
                    $temp= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.= "<a href=\"\"  rel=\"$row->id\" data-id=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-primary btn-show-item' class=\"listitem_toggle\" title=\"Danh sách trong nhóm\"><i class=\"la la-list-ol\"></i></a>";
                    $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->toJson();
        }
        return view('admin.shop-group.item.index')
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
        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.shop-group.item.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
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
        ],[
            'title.required' => __('Vui lòng nhập tên nhóm'),
        ]);
        $input = $request->all();
        $data_params = $this->validate_data_decimal($input['params'],true);
        if($data_params === false){
            return redirect()->back()->withErrors('Dữ liệu tỷ giá truyền vào không hợp lệ, vui lòng kiểm tra lại.');
        }
        $input['params'] = $data_params;
        $data=Shop_Group::create($input);
        ActivityLog::add($request, 'Tạo mới thành công '.$this->module.' #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Thêm mới thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }
    }
    function validate_data_decimal(array $data, bool $check){
        foreach($data as $key => $item){
            if(is_array($item)){
                $item_reb = $this->validate_data_decimal($item,$check);
                if($item_reb === false){
                    return false;
                    break;
                }
                $data[$key] = $item_reb;
            }
            else{
                // trường hợp có giá trị truyền vào;
               if($item != null){
                   // kiểm tra xem dữ liệu là định dạng nào bằng cách search key nếu như là amount thì định dạng int còn nếu như là percent thì là định dạng float vì có dữ liệu % thập phân
                   if(strstr($key,"additional_amount")){
                        $item = str_replace('.','',$item);
                        if(!is_numeric($item)){
                           $check = false;
                           break;
                        }
                        if((int)$item < 0){
                            $check = false;
                           break;
                        }
                        $data[$key] = (int)$item;
                   }
                   elseif(strstr($key,"ratio_percent")){
                        if(!is_numeric($item)){
                           $check = false;
                           break;
                        }
                        if((int)$item < 60 || (int)$item > 150){
                            $check = false;
                           break;
                        }
                        $data[$key] = (float)$item;
                   }
               }
               // trường hợp không có giá trị truyền vào, không validate dữ liệu và tiếp tục vòng lặp
               continue;
            }
        }
        if($check === false){
            return false;
        }
        return $data;
    }
    public function UpdateStatus(Request $request){
        $id = $request->id;
        $data = Shop_Group::find($id);
        $old_status = $data->status;
        if($data->status == 1){
            $data->status = 0;
        }
        elseif($data->status == 0){
            $data->status = 1;
        }
        $data->save();
        $content = $data->title.' đã chuyển về trạng thái '.config('module.shop.status.'.$data->status);
        ActivityLog::add($request, 'Cập nhật thành công '.$this->module.' #'.$data->id.' từ trạng thái '.config('module.shop.status.'.$old_status).' sang trạng thái '.config('module.shop.status.'.$data->status));
        return response()->json([
           'message'=>__($content),
           'status'=> 1
       ]);
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
    public function edit(Request $request,$id)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];
        $data = Shop_Group::findOrFail($id);
        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        return view('admin.shop-group.item.create_edit')
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
        $data =  Shop_Group::findOrFail($id);
        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tên nhóm'),
        ]);
        $input=$request->all();
        $data_params = $this->validate_data_decimal($input['params'],true);
        if($data_params === false){
            return redirect()->back()->withErrors('Dữ liệu tỷ giá truyền vào không hợp lệ, vui lòng kiểm tra lại.');
        }
        $input['params'] = $data_params;
        $data->update($input);
        ActivityLog::add($request, 'Cập nhật thành công '.$this->module.' #'.$data->id);
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
        $input=explode(',',$request->id);
        $shop = Shop::whereIn('group_id',$input)->get();
        if(isset($shop) && count($shop) > 0){
            return redirect()->back()->withErrors('Không thể xóa vì nhóm vẫn đang chứa shop hoạt động');
        }
        Shop_Group::whereIn('id',$input)->delete();
        ActivityLog::add($request, 'Ngừng hoạt động thành công '.$this->module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Xóa thành công !'));
    }
    public function getShopInGroup(Request $request){
        if($request->ajax) {
            $id = $request->get('shop_group');
            $datatable= Shop::with('group')->where('group_id',$id);
            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', $request->get('id'));
                });
            }
            if ($request->filled('title'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
                });
            }
            $datatable = $datatable->orderBy('id','desc');
            ActivityLog::add($request, 'Xem thông tin các shop trong nhóm #'.$id);
            return \datatables()->eloquent($datatable)
                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->addColumn('action', function($row) use ($id){
                    $temp = "<a data-shop=\"$row->id\" data-group=\"$id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger btn-delete-shop' title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->only([
                    'id',
                    'shop_id',
                    'shop_group_id',
                    'order',
                    'title',
                    'domain',
                    'status',
                    'shop',
                    'action'
                ])
                ->toJson();
        }
    }
    public function getSearchShop(Request $request){
        $datatable = Shop::with('group')->orderBy('id','desc');
        if ($request->filled('find'))  {
            $datatable->where(function($q) use($request){
                $q->orWhere('title', 'LIKE', '%' . $request->get('find') . '%');
                $q->orWhere('domain', 'LIKE', '%' . $request->get('find') . '%');
                $q->orWhere('id', 'LIKE', '%' . $request->get('find') . '%');
            });
        }
       $datatable = $datatable->paginate(10);
        return view('admin.shop-group.item.search')
            ->with('datatable',$datatable)->render();
    }
    public function updateShopInGroup(Request $request){
        $id = $request->id;
        $group_shop = $request->group_shop;
        // kiểm tra shop
        $shop = Shop::with('group')->where('id',$id)->first();
        if(!$shop){
            return response()->json([
                'status' => 0,
                'message' => "Shop không hợp lệ",
            ]);
        }
        // kiểm tra xem shop đã được ở trong nhóm chưa
        if(isset($shop->group)){
            return response()->json([
                'status' => 0,
                'message' => "Shop này đã được ở trong nhóm: ".$shop->group->title
            ]);
        }

        // kiểm tra shop shop
        $group_shop = Shop_Group::where('id',$group_shop)->first();
        if(!$group_shop){
            return response()->json([
                'status' => 0,
                'message' => "Nhóm shop không hợp lệ",
            ]);
        }
        $shop->group_id = $group_shop->id;
        $shop->save();
        ActivityLog::add($request, 'Thêm shop #'.$shop->id.' vào nhóm #'.$group_shop->id);
        return response()->json([
            'status' => 1,
            'title' => $group_shop->title,
            'message' => 'Thành công'
        ]);
    }
    public function deleteShopInGroup(Request $request){
        $shop_id = $request->shop_id;
        $group_shop = $request->group_shop;


        // kiểm tra shop
        $shop = Shop::with('group')->where('id',$shop_id)->where('group_id',$group_shop)->first();
        if(!$shop){
            return response()->json([
                'status' => 0,
                'message' => "Dữ liệu không hợp lệ, vui lòng kiểm tra lại",
            ]);
        }
        $shop->group_id = null;
        $shop->save();
        ActivityLog::add($request, 'Xóa thành công shop #'.$shop_id.' khỏi nhóm #'.$group_shop);
        return response()->json([
            'status' => 1,
            'message' => 'Xóa thành công',
        ]);
    }


}
