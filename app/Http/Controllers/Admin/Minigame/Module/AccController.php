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


class AccController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;
    protected $moduleItem;
    public function __construct(Request $request)
    {


        $this->module=$request->segments()[1]??"";
        $this->moduleCategory=explode("-", $this->module)[0].'-category';
        $this->moduleItem= explode("-", $this->module)[0];

        //set permission to function
        $this->middleware('permission:'. $this->module);
        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'.index'),
                'title' => __(config('module.minigame.'.$this->module.'.title'))
            ];
        }
    }


    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);
        if($request->ajax) {
            $datatable= Item::with('parrent')->where('module', $this->module);

            // if (session('shop_id')) {
            //     $datatable->where('shop_id',session('shop_id'));
            // }
            if ($request->filled('idkey')) {
                $datatable->where('idkey',$request->get('idkey'));
            }

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
            if ($request->filled('idkey')) {
                $datatable->where('idkey',$request->get('idkey') );
            }

            return \datatables()->eloquent($datatable)

                ->only([
                    'id',
                    'title',
                    'order',
                    'status',
                    'action',
                    'created_at',
                    'idkey',
                    'position',
                    'parent_id',
                    'locale',
                    'parrent',
                    'author_id'
                ])
                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->addColumn('action', function($row) {
                    $temp= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->with('total',$datatable->count())
                ->with('total_active',$datatable->sum('status'))
                ->toJson();
        }
        $dataItem = Item::with(array('groups' => function ($query) {
                $query->where('module', $this->moduleCategory);
                $query->select('groups.id','title');
            }))->where('module', '=',  $this->moduleItem)->where('params','like','%gift_type":"1%')->orderBy('order','asc')->get();
        return view('admin.minigame.module.acc.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('dataItem', $dataItem);
    }


    public function create(Request $request)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];

        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.minigame.module.acc.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);

    }

    public function store(Request $request)
    {
        // if(!session('shop_id')){
        //     return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        // }
        $this->validate($request,[
            'idkey'=>'required',
            'title'=>'required',
        ],[
            'idkey.required' => __('Vui lòng chọn loại game'),
            'title.required' => __('Vui lòng nhập tiêu đề'),
        ]);
        $input=$request->all();
        $input['module']=$this->module;
        $input['author_id']=auth()->user()->id;
        // $input['shop_id'] = session('shop_id');

        //xử lý params
        if($request->filled('params')){
            //check value param ở đây nếu cần //Example:  $params['demo']='Value demo edited'
            $params=$request->params;
            //foreach ($params as $aPram){
            //
            //    return $aPram;
            //
            //}
            $input['params'] =$params;
        }
        $data=Item::create($input);

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
        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        return view('admin.minigame.module.acc.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data);

    }

    public function update(Request $request,$id)
    {
        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }
        $data =  Item::where('module', '=', $this->module)->findOrFail($id);

        $this->validate($request,[
            'idkey'=>'required',
            'title'=>'required',
        ],[
            'idkey.required' => __('Vui lòng chọn loại game'),
            'title.required' => __('Vui lòng nhập tiêu đề'),
        ]);

        $input=$request->all();
        $input['module']=$this->module;
        // $input['shop_id'] = session('shop_id');

        //xử lý params
        if($request->filled('params')){
            //check value param ở đây nếu cần //Example:  $params['demo']='Value demo edited'

            $params=$request->params;
            $input['params'] =$params;
        }

        $data->update($input);
        //set category

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


        $data=Item::where('module','=',$this->module)::whereIn('id',$input)->update([
            $field=>$value
        ]);

        ActivityLog::add($request, 'Cập nhật field thành công '.$this->module.' '.json_encode($whitelist).' #'.json_encode($input));

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
