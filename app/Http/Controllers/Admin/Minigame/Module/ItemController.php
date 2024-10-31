<?php

namespace App\Http\Controllers\Admin\Minigame\Module;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Item;
use App\Models\Shop;
use Carbon\Carbon;
use Html;
use Illuminate\Http\Request;


class ItemController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;
    protected $moduleType;
    protected $moduleCategory;
    public function __construct(Request $request)
    {


        $this->module=$request->segments()[1]??"";
        $this->moduleType=$this->module.'-type';
        $this->moduleCategory=$this->module.'-category';

        //set permission to function
        $this->middleware('permission:'. $this->module);

        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'.index'),
                'title' => __(config('module.minigame.item.title'))
            ];
        }
    }

    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);
        if($request->ajax) {
            $datatable= Item::with(array('groups' => function ($query) {
                $query->where('module', $this->moduleType);

                $query->select('groups.id','title');
            }))->where('module', $this->module);

            if ($request->filled('gametype')) {

                $datatable->whereHas('groups', function ($query) use ($request) {
                    $query->where('group_id',$request->get('gametype'));
                });
            }

            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', $request->get('id'));
                    $q->orWhere('idkey',$request->get('id') );
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
            if ($request->filled('position')) {
                $datatable->where('position',$request->get('position') );
            }
            if ($request->filled('valuefrom') && !$request->filled('valueto')) {
                $datatable = $datatable->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') >= ".$request->get('valuefrom'));
            }

            if ($request->filled('valueto') && !$request->filled('valuefrom')) {
                $datatable = $datatable->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') <= ".$request->get('valueto'));
            }

            if ($request->filled('valueto') && $request->filled('valuefrom')) {
                $datatable = $datatable->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') >= ".$request->get('valuefrom'));
                $datatable = $datatable->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') <= ".$request->get('valueto'));
            }

            if($request->get('order')[0]['column'] == '6' and $request->get('order')[0]['dir'] == 'asc'){
                $datatable = $datatable->orderByRaw("CAST(replace(JSON_EXTRACT(params, '$.value'),'\"','') AS int) ");
            }
            if($request->get('order')[0]['column'] == '6' and $request->get('order')[0]['dir'] == 'desc'){
                $datatable = $datatable->orderByRaw("CAST(replace(JSON_EXTRACT(params, '$.value'),'\"','') AS int) desc");
            }

            return \datatables()->eloquent($datatable)

                ->only([
                    'id',
                    'title',
                    'slug',
                    'image',
                    'locale',
                    'groups',
                    'status',
                    'action',
                    'position',
                    'created_at',
                    'params'
                ])


                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->addColumn('action', function($row) {
                    $temp= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.= "<a href=\"".route('admin.'.$this->module.'.duplicate',$row->id)."\"  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-primary' title=\"Nhân bản\"><i class=\"la la-copy\"></i></a>";
                    $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->toJson();
        }
        $dataCategory = Group::where('module', '=',  'minigame-type')->where('status',1);
        // if (session('shop_id')) {
        //     $dataCategory->where('shop_id',session('shop_id'));
        // }
        if ($request->filled('position')) {
            $dataCategory->where('position',$request->get('position') );
        }
        $dataCategory = $dataCategory->orderBy('order','asc')->get();
        return view('admin.minigame.module.item.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('dataCategory', $dataCategory);
    }


    public function create(Request $request)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới"),
        ];

        $dataCategory = Group::where('module', '=',  'minigame-type')->where('status',1);
        // if (session('shop_id')) {
        //     $dataCategory->where('shop_id',session('shop_id'));
        // }
        if ($request->filled('position')) {
            $dataCategory->where('position',$request->get('position') );
        }
        $dataCategory = $dataCategory->orderBy('id','desc')->get();

        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.minigame.module.item.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('dataCategory', $dataCategory);

    }

    public function store(Request $request)
    {

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }
        $this->validate($request,[
            'position'=>'required',
            'title'=>'required',
//            'image'=>'required',
            'group_id'=>'required',
        ],[
            'position.required' => __('Vui lòng chọn loại game'),
            'title.required' => __('Vui lòng nhập tiêu đề'),
//            'image.required' => __('Vui lòng nhập ảnh'),
            'group_id.required' => __('vui lòng chọn loại phần thưởng'),
        ]);

        $data=Item::where('module', $this->module)->where('position', $request->position)
            ->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') = ".$request->params['value'])->first();
        if($data){
            return redirect()->back()->withErrors(__('Giải thưởng này đã tồn tại trên hệ thống !'));
        }

        $input=$request->all();
        $input['module']=$this->module;

        if (!isset($input['image'])){
            $input['image'] = $input['image_default'];
        }

        $input['author_id']=auth()->user()->id;
        $input['price_old'] = (float)str_replace(array(' ', '.'), '', $request->price_old);
        $input['price'] = (float)str_replace(array(' ', '.'), '', $request->price);
        $input['percent_sale'] = (float)str_replace(array(' ', '.'), '', $request->percent_sale);
        // $input['shop_id'] = session('shop_id');

        //xử lý params
        if($request->filled('params')){
            //check value param ở đây nếu cần //Example:  $params['demo']='Value demo edited'
            $params=$request->params;
            $input['params'] =$params;

        }
        $data=Item::create($input);

        //set category
        if( isset($input['group_id'] ) &&  $input['group_id']!=0){
            $data->groups()->attach($input['group_id']);
        }
        ActivityLog::add($request, 'Tạo mới thành công '.$this->module.' #'.$data->id);

        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Thêm mới thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }
    }

    public function edit(Request $request,$id)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];
        $data = Item::where('module', '=', $this->module)->findOrFail($id);
        // if($data->shop_id){
        //     $shop = Shop::findOrFail($data->shop_id);
        //     session()->put('shop_id', $shop->id);
        //     session()->put('shop_name', $shop->domain);
        // }
        $dataCategory = Group::where('module', '=',  'minigame-type')->where('status',1);
        // if (session('shop_id')) {
        //     $dataCategory->where('shop_id',session('shop_id'));
        // }
        if ($request->filled('position')) {
            $dataCategory->where('position',$request->get('position') );
        }
        $dataCategory = $dataCategory->orderBy('order','asc')->get();

        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        return view('admin.minigame.module.item.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('dataCategory', $dataCategory);

    }

    public function update(Request $request,$id)
    {
        // if(!session('shop_id')){
        //     return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        // }

        $data =  Item::where('module', '=', $this->module)->findOrFail($id);

        $this->validate($request,[
            'position'=>'required',
            'title'=>'required',
            'group_id'=>'required',
        ],[
            'position.required' => __('Vui lòng chọn loại game'),
            'title.required' => __('Vui lòng nhập tiêu đề'),
            'group_id.required' => __('vui lòng chọn loại phần thưởng'),
        ]);

        $dataCheck=Item::where('module', $this->module)->where('id','!=',$data->id)->where('position', $request->position)->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') = ".$request->params['value'])->first();

//        if($dataCheck){
//            return redirect()->back()->withErrors(__('Giải thưởng này đã tồn tại trên hệ thống !'));
//        }

        $input=$request->all();
        $input['module']=$this->module;
        // $input['shop_id'] = session('shop_id');

        if (!isset($input['image'])){
            $input['image'] = $input['image_default'];
        }

        //xử lý params
        if($request->filled('params')){
            //check value param ở đây nếu cần //Example:  $params['demo']='Value demo edited'

            $params=$request->params;
            $input['params'] =$params;
        }

        $data->update($input);
        //set category

        if( isset($input['group_id'] ) &&  $input['group_id']!=0){
            $data->groups()->sync($input['group_id']);
        }
        else{
            $data->groups()->sync([]);
        }
        ActivityLog::add($request, 'Cập nhật thành công '.$this->module.' #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Cập nhật thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
        }
    }

    public function destroy(Request $request)
    {
        $input=explode(',',$request->id);

        Item::where('module','=',$this->module)->whereIn('id',$input)->delete();
        ActivityLog::add($request, 'Xóa thành công '.$this->module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Xóa thành công !'));
    }


    public function duplicate(Request $request,$id)
    {

        $data= Item::where('module', '=', $this->module)->find($id);
        if(!$data){
            return redirect()->back()->withErrors(__('Không tìm thấy dữ liệu để nhân bản'));
        }
        $dataGroup= $data->groups()->get()->pluck(['id']);

        $dataNew = $data->replicate();
        $dataNew->title=$dataNew->title." (".((int)$data->duplicate+1) .")";
        $dataNew->slug=$dataNew->slug."-".((int)$data->duplicate+1);
        $dataNew->duplicate=0;
        $dataNew->is_slug_override=0;
        $dataNew->save();
        //set group cho dataNew
        $dataNew->groups()->sync($dataGroup);

        //update data old plus 1 count version
        $data->duplicate=(int)$data->duplicate+1;
        $data->save();

        ActivityLog::add($request, 'Nhân bản '.$this->module.' #'.$data->id ."thành #".$dataNew->id);
        return redirect()->back()->with('success',__('Nhân bản thành công'));


    }

    public function update_field(Request $request)
    {
        $id=$request->id;
        $field=$request->field;
        $value=$request->value;
        $required=$request->required;
        $whitelist=['value','bonus_from','bonus_to','order','percent','try_percent','nohu_percent'];

        if(!in_array($field,$whitelist)){
            return response()->json([
                'success'=>false,
                'message'=>__('Trường cập nhật không được chấp thuận'),
                'redirect'=>''
            ]);
        }
        if($required==1 && $value==""){
            return response()->json([
                'success'=>false,
                'message'=>__('Trường này không được bỏ trống!'),
                'redirect'=>''
            ]);
        }
        $data =  Item::where('module', '=', $this->module)->findOrFail($id);
        $old_value = "";
        if($field!='order'){
            $params=$data->params;
            foreach ($params as $aPram=>$key){
                if(str_contains($aPram, $field)){
                    $old_value = $params->$aPram;
                    $params->$aPram = $value;
                }
            }
            $data->params = $params;
        }else{
            $data->order = $value;
        }
        $data->save();
        $name = '';
        if($field=='value'){
            $name = __('Giá trị');
        }elseif($field=='bonus_from'){
            $name = __('Giá trị bonus từ');
        }elseif($field=='bonus_to'){
            $name = __('Giá trị bonus đến');
        }elseif($field=='order'){
            $name = __('Vị trí');
        }elseif($field=='percent'){
            $name = __('Phần trăm');
        }elseif($field=='try_percent'){
            $name = __('Phần trăm chơi thử');
        }elseif($field=='nohu_percent'){
            $name = __('Phần trăm nổ hũ');
        }
        ActivityLog::add($request, config('module.txns.trade_type.'.$this->module).': Cập nhật phần thưởng #'.$id.' mục '.$name.' từ ['.$old_value.'] -> ['.$value.']');

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

        $item = Group::where('module', '=', $this->module)->find($source);
        $item->parent_id = isset($destination)?$destination:0;
        $item->save();

        $ordering = json_decode($request->get('order'));

        $rootOrdering = json_decode($request->get('rootOrder'));

        if ($ordering) {
            foreach ($ordering as $order => $item_id) {
                if ($itemToOrder = Group::where('module', '=', $this->module)->find($item_id)) {
                    $itemToOrder->order = $order;
                    $itemToOrder->save();
                }
            }
        } else {
            foreach ($rootOrdering as $order => $item_id) {
                if ($itemToOrder = Group::where('module', '=', $this->module)->find($item_id)) {
                    $itemToOrder->order = $order;
                    $itemToOrder->save();
                }
            }
        }
        ActivityLog::add($request, 'Thay đổi STT thành công '.$this->module.' #'.$item->id);
        return 'ok ';
    }
}
