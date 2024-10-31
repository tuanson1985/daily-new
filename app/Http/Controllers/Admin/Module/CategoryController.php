<?php

namespace App\Http\Controllers\Admin\Module;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Html;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;


class CategoryController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;
    public function __construct(Request $request)
    {

        $this->module=$request->segments()[1]??"";

        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');
        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);


        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'.index'),
                'title' => __(config('module.'.$this->module.'.title'))
            ];
        }
    }

    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);

        $datatable= Group::where('module','=',$this->module)->orderBy('order');

        if(session('shop_id')){
            $datatable->where('shop_id',session('shop_id'));
        }
        else{
            if ($this->module == 'menu-category' || $this->module == 'menu-profile' || $this->module == 'menu-transaction' || $this->module == 'article-category'){
                $datatable->where('shop_id',null)->where('is_slug_override',1);
            }else{
                if(isset(Auth::user()->shop_access) &&Auth::user()->shop_access !== "all"){
                    $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                    $datatable->whereIn('shop_id',$shop_id_shop_access);
                }
            }
        }

        $datatable=$datatable->get();

//        return $datatable;
        $data=$this->getHTMLCategory($datatable);


        if(session('shop_id')){
            $dataCategory = Group::where('module', '=', $this->module)->where('shop_id',session('shop_id'))->orderBy('order','asc')->get();
        }else{
            if ($this->module == 'menu-category' || $this->module == 'menu-profile' || $this->module == 'menu-transaction' || $this->module == 'article-category'){
                $dataCategory = Group::where('module', '=', $this->module)->where('shop_id',null)->where('is_slug_override',1)->orderBy('order','asc')->get();
            }else{
                $dataCategory = Group::where('module', '=', $this->module)->orderBy('order','asc')->get();
            }
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

        $url = Setting::get('sys_zip_shop');

        return view('admin.module.category.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('url', $url)
            ->with('client', $client)
            ->with('dataCategory', $dataCategory);

    }

    public function create(Request $request)
    {
        if ($this->module == 'menu-category' || $this->module == 'menu-profile' || $this->module == 'menu-transaction'){
            if(session('shop_id')){
                $this->page_breadcrumbs[] =[
                    'page' => '#',
                    'title' => __("Thêm mới")
                ];
            }else{
                $this->page_breadcrumbs[] =[
                    'page' => '#',
                    'title' => __("Thêm mới mặc định")
                ];
            }
        }else{
            $this->page_breadcrumbs[] =[
                'page' => '#',
                'title' => __("Thêm mới")
            ];
        }

        if ($this->module == 'menu-category' || $this->module == 'menu-profile' || $this->module == 'menu-transaction' || $this->module == 'article-category'){
            if(session('shop_id')){
                $dataCategory = Group::where('module', '=', $this->module)->where('shop_id',session('shop_id'))->orderBy('order','asc')->get();
            }else{
                $dataCategory = Group::where('module', '=', $this->module)->orderBy('order','asc')->where('is_slug_override',1)->get();
            }
        }elseif ($this->module == 'article-category'){
            if(session('shop_id')){
                $dataCategory = Group::where('module', '=', $this->module)->where('shop_id',session('shop_id'))->orderBy('order','asc')->get();
            }else{
                $dataCategory = Group::where('module', '=', $this->module)->orderBy('order','asc')->where('is_slug_override',1)->get();
            }
        } else{
            $dataCategory = Group::where('module', '=', $this->module)->orderBy('order','asc')->get();
        }

        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.module.category.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('dataCategory', $dataCategory);

    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
        ]);

        $input= $request->all();

        $slug = $request->slug;

        for ($i = 0; $i < 100; $i++){
            if ($i == 0){
                $checkslug = Group::where('module','=',$this->module)->orderBy('created_at','desc');

                if ($request->filled('shop_id')) {
                    $checkslug->where('shop_id', $request->get('shop_id'));
                }
                else{
                    if(session('shop_id')){
                        $checkslug->where('shop_id',session('shop_id'));
                    }
                    else{
                        if(isset(Auth::user()->shop_access) &&Auth::user()->shop_access !== "all"){
                            $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                            $checkslug->whereIn('shop_id',$shop_id_shop_access);
                        }
                    }
                }

                $checkslug = $checkslug->where('slug',$request->slug)->first();

                if (isset($checkslug)){
                    $slug = $slug.'-'.'1';
                }else{
                    break;
                }
            }else{
                $index = $i + 1;
                $checkslug = Group::where('module','=',$this->module)->orderBy('created_at','desc');

                if ($request->filled('shop_id')) {
                    $checkslug->where('shop_id', $request->get('shop_id'));
                }
                else{
                    if(session('shop_id')){
                        $checkslug->where('shop_id',session('shop_id'));
                    }
                    else{
                        if(isset(Auth::user()->shop_access) &&Auth::user()->shop_access !== "all"){
                            $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                            $checkslug->whereIn('shop_id',$shop_id_shop_access);
                        }
                    }
                }

                $checkslug = $checkslug->where('slug',$slug)->first();

                if (isset($checkslug)){
                    $slug = $request->slug.'-'.$index;
                }else{
                    break;
                }
            }
        }

        $input['slug'] = $slug;
        if (session('shop_id')){
            $input['shop_id'] = session('shop_id');
        }else{
            $input['duplicate'] = 0;
            $input['is_slug_override'] = 1;
        }

        $input['module'] = $this->module;
        if($request->filled('params')){
            $params=$request->params;
            $input['params'] =$params;
        }
//        return $input;
        $data= Group::create($input);

        ActivityLog::add($request, 'Tạo mới thành công '.$this->module.' #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Thêm mới thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }
    }

    public function show(Request $request,$id)
    {
        //$data = Group::findOrFail($id);
        //ActivityLog::add($request, 'Show '.$this->module.' #'.$data->id);
        //return view('admin.module.category.show', compact('datatable'));
    }

    public function edit(Request $request,$id)
    {

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];
        $data = Group::where('module', '=', $this->module)->findOrFail($id);
        if ($this->module == 'menu-category' || $this->module == 'menu-profile' || $this->module == 'menu-transaction' || $this->module == 'article-category'){
            if(session('shop_id')){
                $dataCategory = Group::where('module', '=', $this->module)->where('shop_id',session('shop_id'))->where('id','!=',$id)->orderBy('order','asc')->get();
            }else{
                $dataCategory = Group::where('module', '=', $this->module)->where('id','!=',$id)->where('is_slug_override',1)->orderBy('order','asc')->get();
            }
        }else{
            $dataCategory = Group::where('module', '=', $this->module)->where('id','!=',$id)->orderBy('order','asc')->get();
        }


        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        return view('admin.module.category.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('dataCategory', $dataCategory);

    }

    public function update(Request $request,$id)
    {
        $data =  Group::where('module', '=', $this->module)->findOrFail($id);

        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
        ]);

        $input=$request->all();
        $input['module']=$this->module;
        if($request->filled('params')){
            $params=$request->params;
            $input['params'] =$params;
        }
        $data->update($input);

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

        Group::where('module','=',$this->module)->whereIn('id',$input)->delete();
        ActivityLog::add($request, 'Xóa thành công '.$this->module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Xóa thành công !'));
    }

    public function duplicate(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'shop_access' => 'required',
        ],[
            'shop_access.required' => "Vui lòng chọn shop cần clone",
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first(),'status' => 0]);
        }

        $shop = $request->shop_access;

        $input=explode(',',$request->id);

        $data = Group::where('module','=',$this->module)->whereIn('id',$input)->orderBy('order')->get();


//        $data = $this->getHTMLCategory($data);

        foreach ($data as $val){
            foreach ($shop as $shop_id){
                $item = Group::where('module','=',$this->module)->where('id',$val->id)->whereIn('id',$input)->first();
                if ($item->parent_id == 0){
                    $item_new = $item->replicate()->fill(
                        [
                            'shop_id' => $shop_id,
                            'created_at' => Carbon::now(),
                            'author_id' => auth()->user()->id,
                        ]
                    );

                    $item_new->save();
                }

            }
        }

        foreach ($data as $val){
            foreach ($shop as $shop_id){

                $item = Group::where('module','=',$this->module)->where('id',$val->id)->whereIn('id',$input)->first();

                if ($item->parent_id != 0){
                    $item_new = $item->replicate()->fill(
                        [
                            'shop_id' => $shop_id,
                            'created_at' => Carbon::now(),
                            'author_id' => auth()->user()->id,
                        ]
                    );

                    $groupcheck = Group::where('module','=',$this->module)->where('id',$item->parent_id)->first();

                    $itemcheck = Group::where('module','=',$this->module)->where('shop_id',$shop_id)->where('slug',$groupcheck->slug)->first();

                    if (isset($itemcheck)){
                        $item_new->parent_id=$itemcheck->id;
                    }else{

//                        Tạo mới thư mục cha
                        $item_new_p = $groupcheck->replicate()->fill(
                            [
                                'shop_id' => $shop_id,
                                'created_at' => Carbon::now(),
                                'author_id' => auth()->user()->id,
                            ]
                        );

                        $item_new_p->save();

//                        Tạo mới thư mục con
                        $item_new->parent_id=$item_new_p->id;

                    }

                    $item_new->save();
                }
            }
        }

//        $item_new = $this->getHTMLCategory($item_new);

//        Group::where('module','=',$this->module)->whereIn('id',$input)->delete();
        ActivityLog::add($request, 'Duplicate thành công '.$this->module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Clone thành công !'));
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


        $data= Group::where('module','=',$this->module)->whereIn('id',$input)->update([
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
        //dd($item);
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

    // Getter for the HTML menu builder

    function getHTMLCategory($menu)
    {
        return $this->buildMenu($menu);
    }

    function buildMenu($menu, $parent_id = 0)
    {
        $result = null;
        foreach ($menu as $item)
            if ($item->parent_id == $parent_id) {
                $result .= "<li class='dd-item nested-list-item' data-order='{$item->order}' data-id='{$item->id}'>
              <div class='dd-handle nested-list-handle'>
                <span class='la la-arrows-alt'></span>
              </div>
              <div class='nested-list-content'>";
                if($parent_id!=0){
                    $result.="<div class=\"m-checkbox\">
                                    <label class=\"checkbox checkbox-outline\">
                                    <input  type=\"checkbox\" rel=\"{$item->id}\" class=\"children_of_{$item->parent_id}\">
                                      <span></span> ".HTML::entities($item->title)." - {$item->id}
                                    </label>
                                </div>";


                }
                else{

                    $result.="<div class=\"m-checkbox\">
                                    <label class=\"checkbox checkbox-outline\">
                                    <input  type=\"checkbox\" rel=\"{$item->id}\" class=\"children_of_{$item->parent_id}\"  >
                                    <span></span> ".HTML::entities($item->title)." - {$item->id}
                                    </label>
                                </div>";
                }
                $result .= "<div class='btnControll'>";

                $result .= "<a href='" . route("admin.".$this->module.".edit",$item->id) . "' class='btn btn-sm btn-primary'>Sửa</a>
                    <a href=\"#\" class=\"btn btn-sm btn-danger  delete_toggle \" rel=\"{$item->id}\">
                                        Xóa
                    </a>
                </div>
              </div>" . $this->buildMenu($menu, $item->id) . "</li>";
            }
        return $result ? "\n<ol class=\"dd-list\">\n$result</ol>\n" : null;
    }

    public function switchUrl(Request $request){
        $data = Group::where('module', '=', $this->module)->orderBy('order','asc')->get();

        foreach ($data as $item){
            $url = $item->url;
            if (str_contains($url, '/minigame-log')){

                $url = '/minigame-log';
                $item->url = $url;
                $item->save();
            }
        }

        return redirect()->back()->with('success',__('Swich thành công !'));
    }

    public function switchUrlNick(Request $request){
        $data = Group::where('module', '=', $this->module)->orderBy('order','asc')->get();

        foreach ($data as $item){
            $url = $item->url;
            if (str_contains($url, '/lich-su-mua-nick')){

                $url = '/lich-su-mua-account';
                $item->url = $url;
                $item->save();
            }
        }

        return redirect()->back()->with('success',__('Swich thành công !'));
    }

    public function switchRouter(Request $request){
        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop'));
        }

        $url = Setting::get('sys_zip_shop');

        if ($url == ''){
            Setting::updateOrCreate(['name' => 'sys_zip_shop', 'shop_id' => session('shop_id')], [
                'val' => '/blog',
                'type' => Setting::getDataType('sys_zip_shop')
            ]);
        }else{
            if ($url == '/blog'){
                Setting::updateOrCreate(['name' => 'sys_zip_shop', 'shop_id' => session('shop_id')], [
                    'val' => '/tin-tuc',
                    'type' => Setting::getDataType('sys_zip_shop')
                ]);
            }else{
                Setting::updateOrCreate(['name' => 'sys_zip_shop', 'shop_id' => session('shop_id')], [
                    'val' => '/blog',
                    'type' => Setting::getDataType('sys_zip_shop')
                ]);
            }
        }

        ActivityLog::add($request, 'Chuyển route bài viết thành công '.$this->module.' #'.session('shop_id'));
        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Thêm mới thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }
    }

}
