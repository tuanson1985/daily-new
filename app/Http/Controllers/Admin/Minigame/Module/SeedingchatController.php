<?php

namespace App\Http\Controllers\Admin\Minigame\Module;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Item;
use App\Models\Shop;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class SeedingchatController extends Controller
{
    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;
    protected $moduleItem;

    public function __construct(Request $request)
    {


        $this->module=$request->segments()[1]??"";

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

            $datatable= Item::where('module', $this->module)->with('shop');

             if (session('shop_id')) {
                 $datatable->where('shop_id',session('shop_id'));
             }

            if ($request->filled('id')) {
                $datatable->where('id',$request->get('id'));
            }

            if ($request->filled('title'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
                });
            }

            return \datatables()->eloquent($datatable)

                ->only([
                    'id',
                    'title',
                    'price',
                    'status',
                    'shop',
                    'action',
                    'price_old',
                    'created_at',
                    'locale',
                    'total_item'
                ])
                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->addColumn('action', function($row) {
                    $temp= "<a data-id=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary chat-show \" title=\"Demo chat\"><i class=\"la la-eye\"></i></a>";
                    $temp .= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->toJson();
        }

        $client = null;

        if(Auth::user()->account_type == 1){
            $client = Shop::orderBy('id','desc');
            $shop_access_user = Auth::user()->shop_access;
            if(isset($shop_access_user) && $shop_access_user !== "all"){
                $shop_access_user = json_decode($shop_access_user);
                $client = $client->whereIn('id',$shop_access_user);
            }
            $client = $client->select('id','domain','title')->get();
        }

        return view('admin.minigame.module.package-chat.index')
            ->with('module', $this->module)
            ->with('client', $client)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    public function create(Request $request)
    {

         if(!session('shop_id')){
             return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
         }

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];

        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.minigame.module.package-chat.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    public function store(Request $request)
    {
         if(!session('shop_id')){
             return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
         }

        $this->validate($request,[
            'title'=>'required',
            'total_item'=>'required',
            'content_chat'=>'required',
            'content_chat_defailt'=>'required',
            'price_old'=>'required',
            'price'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tên gói'),
            'total_item.required' => __('Vui lòng nhập số lượng chat ban đầu'),
            'content_chat.required' => __('Vui lòng nhập nội dung chat readtime'),
            'content_chat_defailt.required' => __('Vui lòng nhập nội dung chat ban đầu'),
            'price_old.required' => __('Vui lòng nhập thời gian xuất hiện nội dung chat nhỏ nhất'),
            'price.required' => __('Vui lòng nhập thời gian xuất hiện nội dung chat lớn nhất'),
        ]);

        $input=$request->all();

        $input['module']=$this->module;
        $input['author_id']=auth()->user()->id;
        $input['shop_id'] = session('shop_id');
        $content_chat = $request->content_chat;
        $content_chat_defailt = $request->content_chat_defailt;

        $input['params'] = json_encode($content_chat);
        $input['params_plus'] = json_encode($content_chat_defailt);

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

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $this->page_breadcrumbs[] = [
            'page' => '#',
            'title' => __("Cập nhật")
        ];

        $data = Item::where('module', '=', $this->module)->findOrFail($id);

        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);

        return view('admin.minigame.module.package-chat.create_edit')
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
            'title'=>'required',
            'total_item'=>'required',
            'content_chat'=>'required',
            'content_chat_defailt'=>'required',
            'price_old'=>'required',
            'price'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tên gói'),
            'total_item.required' => __('Vui lòng nhập số lượng chat ban đầu'),
            'content_chat.required' => __('Vui lòng nhập nội dung chat readtime'),
            'content_chat_defailt.required' => __('Vui lòng nhập nội dung chat ban đầu'),
            'price_old.required' => __('Vui lòng nhập thời gian xuất hiện nội dung chat nhỏ nhất'),
            'price.required' => __('Vui lòng nhập thời gian xuất hiện nội dung chat lớn nhất'),
        ]);

        $input=$request->all();
        $input['module']=$this->module;
        $input['shop_id'] = session('shop_id');

        $content_chat = $request->content_chat;
        $content_chat_defailt = $request->content_chat_defailt;

        $input['params'] = json_encode($content_chat);
        $input['params_plus'] = json_encode($content_chat_defailt);

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

    public function cloneItem(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'shop_access' => 'required',
        ],[
            'shop_access.required' => "Vui lòng chọn shop cần clone",
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop cần clone'));
        }

        $inputId =explode(',',$request->id);

        $data = Item::where('module', '=', $this->module)->whereIn('id',$inputId)->get();

        $shops = Shop::orderBy('id','desc')->whereIn('id',$request->shop_access)->get();

        foreach ($shops as $shop){
            foreach ($data as $item){
                $item_new = $item->replicate()->fill(
                    [
                        'shop_id' => $shop->id,
                        'author_id' => auth()->user()->id,
                        'created_at' => Carbon::now(),
                    ]
                );

                $item_new->save();
            }
        }

        ActivityLog::add($request, 'Nhân bản '.$this->module ."thành #");
        return redirect()->back()->with('success',__('Nhân bản thành công'));
    }
}
