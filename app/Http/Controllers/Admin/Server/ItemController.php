<?php

namespace App\Http\Controllers\Admin\Server;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerLog;
use App\Models\Shop;
use App\Models\Server_Category;
use App\Models\Shop_Group;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;

class ItemController extends Controller
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

        $this->module='server';
        $this->moduleCategory=null;
        //set permission
        $this->middleware('permission:'. $this->module.'-list');
        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate']]);
        $this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);



        $this->page_breadcrumbs[] = [
            'page' => route('admin.'.$this->module.'.index'),
            'title' => "Tất cả server"
        ];
    }

    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);

        if($request->ajax) {

            $datatable=Server::select('server.*')->where('server.status','<>','999')->where('type',1);

            if ($request->filled('parrent_id'))  {
                 $datatable->where('parrent_id',$request->get('parrent_id') );
            }

            if ($request->filled('server_category_id'))  {
                //Check xem danh mục này có danh mục con hay không, Nếu có, lấy thông tin cả danh mục con
//                $parentData = Server_Category::where('parent_id',$request->get('server_category_id'))->pluck('id')->toArray();
//                if(count($parentData) > 0){
//                    array_push($parentData,$request->get('server_category_id'));
//                    $datatable->whereIn('server_category_id',$parentData);
//                }
//                else{
//                    $datatable->where('server_category_id',$request->get('server_category_id') );
//                }
                $datatable->where('server_category_id',$request->get('server_category_id') );
            }
            if ($request->filled('type_category_id'))  {
                $datatable->where('type_category_id', $request->get('type_category_id'));
            }
            if ($request->filled('title'))  {
                $datatable->where('title', 'LIKE', '%' . $request->get('title') . '%');
            }
            if ($request->filled('shop_name'))  {
                $shopSearch_id = Shop::where('domain','LIKE', '%' . $request->get('shop_name') . '%')->pluck('server_id');
                //dd($shopSearch_id);
                //$datatable->whereRaw(" (shop_name LIKE '%". $request->get('shop_name')."%' or server.id in())");
                $datatable->where(function($q) use($request,$shopSearch_id){
                    $q->orWhere('shop_name', 'LIKE', '%' . $request->get('shop_name') . '%');
                    $q->orWhere(function($t) use($shopSearch_id){
                       $t->whereIn('server.id',$shopSearch_id);
                    });
                });
                ///$datatable->whereRaw('(shop_name LIKE %' . $request->get('shop_name') . '% or server_id in ('.$shopSearch_id.'))');
            }

            if ($request->filled('ipaddress'))  {
                $arrIP = explode(',', $request->get('ipaddress'));
                $datatable->whereIn('ipaddress', $arrIP);
                //dd($datatable->toSql());
            }

            if ($request->filled('status')) {
                $datatable->where('server.status',$request->get('status') );
            }

            return \datatables()->eloquent($datatable)
                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->editColumn('ended_at', function($data) {
                    if(strlen($data->ended_at) > 0) {
                        return date('d/m/Y', strtotime($data->ended_at));
                    }
                    else{
                        return "Không có hạn";
                    }
                })
                ->editColumn('check_express', function($data) {
                    if(strlen($data->ended_at) > 0){
                        if(Carbon::parse($data->ended_at)->gte(Carbon::now()->addDays(7))){
                            return 11;
                        }
                        else{
                            if(Carbon::parse($data->ended_at)->lte(Carbon::now()))
                            {
                                return 0;
                            }
                            else {
                                return 1;
                            }
                        }
                    }
                    else{
                        return 10;
                    }
                })
                ->editColumn('cateName', function($data) {
                    $serverCate = Server::where('status','1')->where('type',0)->where('id',$data->parrent_id)->first();
                    if($serverCate){
                        return $serverCate->title;
                    }
                    else{
                       return "";
                    }

                })
                ->editColumn('lst_shop_of_shop', function($data) {
                    $lstShop = Shop::where('server_id',$data->id)->get();
                    if(isset($lstShop) && count($lstShop)>0){
                        return $lstShop;
                    }
                    else{
                        return "";
                    }

                })
                ->editColumn('catalogName', function($data) {
                    $serverCatalog = Server_Category::where('id',$data->server_category_id)->first();
                    if($serverCatalog){
                        //Check xem danh mục có cha hay không?
                        $parentCatalog = Server_Category::where('id',$serverCatalog->parent_id)->first();
                        if($parentCatalog){
                            return $serverCatalog->title." - thuộc ".$parentCatalog->title;
                        }
                        else {
                            return $serverCatalog->title;
                        }
                    }
                    else{
                        return "";
                    }

                })
                ->editColumn('typeName', function($data) {
                    $typeCatalog = Server_Category::where('id',$data->type_category_id)->where('module','catalog')->first();
                    if($typeCatalog){
                        return $typeCatalog->title;
                    }
                    else{
                        return "";
                    }

                })

                ->addColumn('action', function($row) {
                    $temp = "<a href='javascript://' data-id=\"$row->id\" data-content=\"$row->content\" data-toggle=\"modal\" data-target=\"#infoModal\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary btn_info\" title=\"Thông tin\"><i class=\"la la-info-circle\"></i></a>";
                    $temp.= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    $temp.= "<a href='javascript://' data-id=\"$row->id\" style=\"display:none\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary btn_update btn_update_$row->id\" title=\"Cập nhật\"><i class=\"la la-save\"></i></a>";

                    return $temp;
                })
                ->toJson();
        }

//        $this->page_breadcrumbs[] =[
//            'page' => '#',
//            'title' => __("Danh sách server")
//        ];
        $dataCategory = Server::where('status','1')->where('type',0)->orderBy('id','asc')->get();
        $dataCatalog = Server_Category::where('module','catalog')->where('status','<>','0')->whereRaw('(parent_id = 0 or parent_id is null)')->orderBy('id','asc')->get();
        $dataType = Server_Category::where('module','type')->where('status','<>','0')->orderBy('id','asc')->get();

        return view('admin.'.$this->module.'.index')
            ->with('module', $this->module)
            ->with('dataCategory', $dataCategory)
            ->with('dataCatalog', $dataCatalog)
            ->with('dataType', $dataType)
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


        $dataCategory = Server::where('status',1)->where('type',0)->orderBy('id','asc')->get();
        $dataCatalog = Server_Category::where('module','catalog')->whereRaw('(parent_id = 0 or parent_id is null)')->where('status','<>','0')->orderBy('id','asc')->get();
        $dataType = Server_Category::where('module','type')->where('status','<>','0')->orderBy('id','asc')->get();
        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.'.$this->module.'.create_edit')
            ->with('module', $this->module)
            ->with('dataCategory', $dataCategory)
            ->with('dataCatalog', $dataCatalog)
            ->with('dataType', $dataType)
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
            //'title'=>'required',
            'ipaddress' => 'required',
            'parrent_id' => 'required',
            'server_category_id' => 'required',
            'type_category_id' => 'required',
        ],[
           // 'title.required' => __('Vui lòng nhập tiêu đề'),
            'ipaddress.required' => __('Vui lòng nhập địa chỉ ip'),
            'parrent_id.required' => __('Vui lòng chọn nhà phát hành'),
            'server_category_id.required' => __('Vui lòng chọn danh mục server'),
            'type_category_id.required' => __('Vui lòng chọn mảng server'),
        ]);

        $params = $request->except([
            '_method',
            '_token',
            'ipaddress',
            //'title',
            'description',
            'image',
            'status',
            'content',
            'parrent_id',
            'price',
            'submit-close',
            'register_date',
            'ended_at',
            'cf_status',
            'cf_account',
            'server_category_id',
            'type_category_id',
            'purchase_link',
            'select'
        ]);
        $input = [
            'type'=>1,
            'ipaddress' => $request->ipaddress,
           // 'title' => $request->title,
            'description' => $request->description,
            'image' => $request->image,
            'status' => 0,
            'created_at' => Carbon::now(),
            'content' => $request->get('content'),
            'parrent_id' => $request->parrent_id,
            'price' => str_replace(array(' ', '.'), '', $request->price),
            'shop_name' => json_encode($params, JSON_UNESCAPED_UNICODE),
            'register_date' => Carbon::createFromFormat('d/m/Y', $request->register_date),
            'ended_at' => Carbon::createFromFormat('d/m/Y', $request->ended_at),
            'server_category_id' => $request->server_category_id,
            'type_category_id' => $request->type_category_id,
            'cf_account' => $request->cf_account,
            'cf_status' => $request->cf_status == true ? 1 : 0,
            'purchase_link' => $request->purchase_link
        ];

        if(Carbon::createFromFormat('d/m/Y', $request->register_date) > Carbon::createFromFormat('d/m/Y', $request->ended_at)){
            return redirect()->back()->withErrors(__('Ngày đăng ký server không được lớn hơn ngày hết hạn. Hãy nhập lại'));
        }
        //Check trung khi them shop trong server
        //dd($params);
        $arr_shopname = $params["shop_name"];
        $arr_register_date_dm = $params["register_date_dm"];
        $arr_ended_date = $params["ended_date"];


        $arr_shopname_filter =  $arr_shopname;
        $dem = 0;

        // So sánh kí tự lập lại giữa A0 vs A1;
        if($arr_shopname != null && count($arr_shopname) > 0 && $arr_shopname[0] != "") {
            for ($i = 0; $i < count($arr_shopname_filter); $i++) {
                for ($j = 0; $j < count($arr_shopname); $j++) {
                    if ($arr_shopname_filter[$i] == $arr_shopname[$j]) {
                        $dem++;
                    }
                }
                if ($dem > 1) { // In ra nếu xuất hiện nhiều hơn 1 lần
                    return redirect()->back()->withErrors(__('Bạn đang thêm ' . $arr_shopname_filter[$i] . ' trùng nhau. Hãy kiểm tra lại'));
                }
                $dem = 0;
            }
            //Check ngày đăng ký và hết hạn shop
            for ($i = 0; $i < count($arr_shopname_filter); $i++) {
                if ($arr_register_date_dm[$i] == null || $arr_ended_date[$i] == null || $arr_register_date_dm[$i] == "" || $arr_ended_date[$i] == "") {
                    return redirect()->back()->withErrors(__('Ngày đăng ký, ngày hết hạn của shop không được để trống. Hãy nhập lại'));
                }
                //Kiểm tra ngày hết hạn  bé hơn ngày đăng ký và hiện tại
                $ngaydk =  Carbon::createFromFormat('d/m/Y', $arr_register_date_dm[$i]);
                $ngayhh = Carbon::createFromFormat('d/m/Y', $arr_ended_date[$i]);
                $ngayht = Carbon::now();
                //if($ngaydk > $ngayht){
               //     return redirect()->back()->withErrors(__('Ngày đăng ký không được lớn hơn ngày hiện tại. Hãy nhập lại'));
               // }
                //if($ngayhh < $ngayht){
               //     return redirect()->back()->withErrors(__('Ngày hết hạn không được bé hơn ngày hiện tại. Hãy nhập lại'));
               // }
                if($ngaydk > $ngayhh){
                    return redirect()->back()->withErrors(__('Ngày đăng ký của shop không được lớn hơn ngày hết hạn của shop. Hãy nhập lại'));
                }
            }
        }



        $data=Server::create($input);

        ActivityLog::add($request, 'Tạo mới thành công '.$this->module.' #'.$data->id);
        ServerLog::add($data,"Tạo mới server id #".$data->id."");


        //Kiểm tra list web mới thêm. Nếu web tồn tại ở server khác => inactive shop ở server đó và thêm mới ở server này
        for ($i = 0; $i < count($arr_shopname); $i++) {
            $checkShop = Server::whereRaw(" shop_name like '%".$arr_shopname[$i]."%' ")->where('id','<>',$data->id)->get();
            $checkmoveshop = 0;
            if(count($checkShop) > 0){
                foreach ($checkShop as $item){
                    $dataload = Server::findOrFail($item->id);
                    $str_old="";
                    $str="";

                    $old_shop_name =  \App\Library\Helpers::DecodeJson('shop_name', $item->shop_name);
                    $old_shop_status = \App\Library\Helpers::DecodeJson('shop_status', $item->shop_name);
                    $old_shop_end_date = \App\Library\Helpers::DecodeJson('ended_date', $item->shop_name);
                    if(count($old_shop_name) > 0){
                        foreach ($old_shop_name as $key=>$value){
                                if ($value == $arr_shopname[$i]) {
                                    if($old_shop_status[$key] == 1) {
                                        ServerLog::add($dataload, "Ngưng hoạt động shop  " . $value . "  - Chuyển sang server " . $data->ipaddress);
                                        ServerLog::add($data, "Chuyển shop " . $value . " từ server " . $dataload->ipaddress . " sang");
                                        $checkmoveshop = 1;
                                    }
                                    if ($key == (count($old_shop_name) - 1)) {
                                        $str_old = $str_old . '"' . $old_shop_status[$key] . '"';
                                    } else {
                                        $str_old = $str_old . '"' . $old_shop_status[$key] . '",';
                                    }
                                    $old_shop_status[$key] = "0";
                                    if ($key == (count($old_shop_name) - 1)) {
                                        $str = $str . '"' . $old_shop_status[$key] . '"';
                                    } else {
                                        $str = $str . '"' . $old_shop_status[$key] . '",';
                                    }
                                } else {
                                    if ($key == (count($old_shop_name) - 1)) {
                                        $str_old = $str_old . '"' . $old_shop_status[$key] . '"';
                                    } else {
                                        $str_old = $str_old . '"' . $old_shop_status[$key] . '",';
                                    }
                                    $old_shop_status[$key] = $old_shop_status[$key];
                                    if ($key == (count($old_shop_name) - 1)) {
                                        $str = $str . '"' . $old_shop_status[$key] . '"';
                                    } else {
                                        $str = $str . '"' . $old_shop_status[$key] . '",';
                                    }
                                }

                        }
                    }
                    $str_old = '"shop_status":[' . $str_old . ']';
                    $str = '"shop_status":[' . $str . ']';
                    $dataload->shop_name = str_replace($str_old, $str, $dataload->shop_name);
                    $dataload->save();

                }
            }
            if($checkmoveshop == 0) {
                ServerLog::add($data, 'Thêm mới shop ' . $arr_shopname[$i] . '');
            }
        }


        if($request->filled('submit-close')){
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
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
        $data = Server::findOrFail($id);
        $dataCategory = Server::where('status','1')->where('type',0)->orderBy('id','asc')->get();
        $dataCatalog = Server_Category::where('module','catalog')->whereRaw('(parent_id = 0 or parent_id is null)')->where('status','<>','0')->orderBy('id','asc')->get();
        $dataType = Server_Category::where('module','type')->where('status','<>','0')->orderBy('id','asc')->get();
        $dataShopInServer = Shop::with('group')->where('server_id',$data->id)->get();
        $dataGroupShop =  Shop_Group::orderBy('id','desc')->get();
        $dataLogServer = ServerLog::select('server_log.*','server.ipaddress')->leftJoin('server','server.id','server_log.current_id_server')->where('current_id_server',$id)->orderBy("id","desc")->get();
        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        //Kiểm tra xem shop hết hạn thì cập nhật shop thành ngưng hoạt động
        $old_shop_name =  \App\Library\Helpers::DecodeJson('shop_name',$data->shop_name);
        $old_shop_status = \App\Library\Helpers::DecodeJson('shop_status',$data->shop_name);
        $old_shop_end_date = \App\Library\Helpers::DecodeJson('ended_date',$data->shop_name);
        $ngayht = Carbon::now();
        $str_old = "";
        $str="";
        try {
            if ($old_shop_name != null && count($old_shop_name) > 0) {
                foreach ($old_shop_name as $key => $value) {
                    if ($key == (count($old_shop_name) - 1)) {
                        $str_old = $str_old . '"' . $old_shop_status[$key] . '"';
                    } else {
                        $str_old = $str_old . '"' . $old_shop_status[$key] . '",';
                    }
                    $ngayhh = Carbon::createFromFormat('d/m/Y', $old_shop_end_date[$key]);
                    if ($ngayhh->lt($ngayht)) {
                        $old_shop_status[$key] = "0";
                    }
                    if ($key == (count($old_shop_name) - 1)) {
                        $str = $str . '"' . $old_shop_status[$key] . '"';
                    } else {
                        $str = $str . '"' . $old_shop_status[$key] . '",';
                    }

                }
            }
            $str_old = '"shop_status":[' . $str_old . ']';
            $str = '"shop_status":[' . $str . ']';
            $data->shop_name = str_replace($str_old, $str, $data->shop_name);
            $data->save();
        }
        catch (\Exception $r){

    }
        //dd($data);


        return view('admin.'.$this->module.'.create_edit')
            ->with('module', $this->module)
            ->with('dataType', $dataType)
            ->with('dataCatalog', $dataCatalog)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('dataShopInServer',$dataShopInServer)
            ->with('dataGroupShop',$dataGroupShop)
            ->with('dataLogServer',$dataLogServer)
            ->with('data', $data)->with('dataCategory', $dataCategory);
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
            //'title'=>'required',
            'status' => 'required',
            'ipaddress' => 'required',
            'parrent_id' => 'required',
            'server_category_id' => 'required',
            'type_category_id' => 'required',
        ],[
            //'title.required' => __('Vui lòng nhập tiêu đề'),
            'ipaddress.required' => __('Vui lòng nhập địa chỉ IP'),
            'status.required' => __('Vui lòng chọn trạng thái'),
            'parrent_id.required' => __('Vui lòng chọn nhà phát hành'),
            'server_category_id.required' => __('Vui lòng chọn danh mục server'),
            'type_category_id.required' => __('Vui lòng chọn mảng server')
        ]);
        $data =  Server::findOrFail($id);
        $stt_old = $data->status;

        $old_shop_name =  \App\Library\Helpers::DecodeJson('shop_name',$data->shop_name);
        $old_shop_status = \App\Library\Helpers::DecodeJson('shop_status',$data->shop_name);
        $old_shop_end_date = \App\Library\Helpers::DecodeJson('ended_date',$data->shop_name);



        $price_old = $data->price;
        $params = $request->except([
            '_method',
            '_token',
            'ipaddress',
            //'title',
            'description',
            'image',
            'status',
            'content',
            'parrent_id',
            'price',
            'submit-close',
            'register_date',
            'ended_at',
            'cf_account',
            'cf_status',
            'server_category_id',
            'type_category_id',
            'purchase_link',
            'select'
        ]);
        $input = [
            'type'=>1,
            'ipaddress' => $request->ipaddress,
            //'title' => $request->title,
            'status' => $request->status,
            'description' => $request->description,
            'image' => $request->image,
            'content' => $request->get('content'),
            'parrent_id' => $request->parrent_id,
            'price' => str_replace(array(' ', '.'), '', $request->price),
            'shop_name' => json_encode($params, JSON_UNESCAPED_UNICODE),
            'register_date' => Carbon::createFromFormat('d/m/Y', $request->register_date),
            'ended_at' => Carbon::createFromFormat('d/m/Y', $request->ended_at),
            'server_category_id' => $request->server_category_id,
            'type_category_id' => $request->type_category_id,
            'cf_account' => $request->cf_account,
            'cf_status' => $request->cf_status == true ? 1 : 0,
            'purchase_link' => $request->purchase_link
        ];

        if(Carbon::createFromFormat('d/m/Y', $request->register_date) > Carbon::createFromFormat('d/m/Y', $request->ended_at)){
            return redirect()->back()->withErrors(__('Ngày đăng ký server không được lớn hơn ngày hết hạn. Hãy nhập lại'));
        }
        //Check trung khi them shop trong server
        //dd($params);
        $arr_shopname = $params["shop_name"];
        $arr_register_date_dm = $params["register_date_dm"];
        $arr_ended_date = $params["ended_date"];


        $arr_shopname_filter =  $arr_shopname;
        $dem = 0;

        // So sánh kí tự lập lại giữa A0 vs A1;
        if($arr_shopname != null && count($arr_shopname) > 0 && $arr_shopname[0] != "") {
            for ($i = 0; $i < count($arr_shopname_filter); $i++) {
                for ($j = 0; $j < count($arr_shopname); $j++) {
                    if ($arr_shopname_filter[$i] == $arr_shopname[$j]) {
                        $dem++;
                    }
                }
                if ($dem > 1) { // In ra nếu xuất hiện nhiều hơn 1 lần
                    return redirect()->back()->withErrors(__('Bạn đang thêm ' . $arr_shopname_filter[$i] . ' trùng nhau. Hãy kiểm tra lại'));
                }
                $dem = 0;
            }
            //Check ngày đăng ký và hết hạn shop
            for ($i = 0; $i < count($arr_shopname_filter); $i++) {
                if ($arr_register_date_dm[$i] == null || $arr_ended_date[$i] == null || $arr_register_date_dm[$i] == "" || $arr_ended_date[$i] == "") {
                    return redirect()->back()->withErrors(__('Ngày đăng ký, ngày hết hạn của shop không được để trống. Hãy nhập lại'));
                }
                //Kiểm tra ngày hết hạn  bé hơn ngày đăng ký và hiện tại
                $ngaydk =  Carbon::createFromFormat('d/m/Y', $arr_register_date_dm[$i]);
                $ngayhh = Carbon::createFromFormat('d/m/Y', $arr_ended_date[$i]);
                $ngayht = Carbon::now();
                //if($ngaydk > $ngayht){
                //     return redirect()->back()->withErrors(__('Ngày đăng ký không được lớn hơn ngày hiện tại. Hãy nhập lại'));
                // }
                //if($ngayhh < $ngayht){
                //     return redirect()->back()->withErrors(__('Ngày hết hạn không được bé hơn ngày hiện tại. Hãy nhập lại'));
                // }
                if($ngaydk > $ngayhh){
                    return redirect()->back()->withErrors(__('Ngày đăng ký của shop không được lớn hơn ngày hết hạn của shop. Hãy nhập lại'));
                }
            }

        }


        $data->update($input);
        $stt_new = $data->status;
        $price_new = $data->price;
        if($price_new != $price_old){
            ServerLog::add($data,"Cập nhật giá server id #".$data->id." từ ".$price_old ." sang ".$price_new);
        }
         if($stt_old != $stt_new){
            if($stt_new == 1){
                ServerLog::add($data,"Cập nhật trạng thái  hoạt động server #".$data->id);
            }
            if($stt_new == 2)
            {
                ServerLog::add($data,"Cập nhật trạng thái dừng hoạt động server #".$data->id);
            }
            if($stt_new ==0){
                ServerLog::add($data,"Xóa server #".$data->id);
            }
        }
         //Check shop ghi log
        $new_shop_name =  \App\Library\Helpers::DecodeJson('shop_name',$data->shop_name);
        $new_shop_status = \App\Library\Helpers::DecodeJson('shop_status',$data->shop_name);
        $new_shop_end_date = \App\Library\Helpers::DecodeJson('ended_date',$data->shop_name);

        if(count($old_shop_name) == count($new_shop_name)){
            for($i=0;$i<count($old_shop_name);$i++){
                if($old_shop_name[$i] != $new_shop_name[$i]){
                    ServerLog::add($data,"Cập nhật tên shop từ ".$old_shop_name[$i]." sang ".$new_shop_name[$i]."");
                }
                if($old_shop_end_date[$i] != $new_shop_end_date[$i]){
                    ServerLog::add($data,"Cập nhật ngày hết hạn shop ".$new_shop_name[$i]." từ ".$old_shop_end_date[$i]." sang ".$new_shop_end_date[$i]."");
                }
                if($old_shop_status[$i] != $new_shop_status[$i]){
                    ServerLog::add($data,"Cập nhật trạng thái shop ".$new_shop_name[$i]." từ #".$old_shop_status[$i]."(".($old_shop_status[$i] == 0 ? "Ngừng hoạt động" : "Hoạt động").") sang #".$new_shop_status[$i]."(".($new_shop_status[$i] == 0 ? "Ngừng hoạt động" : "Hoạt động").")");
                }
            }
        }
        elseif(count($old_shop_name) < count($new_shop_name)){
            $compare  =  array_diff_key($new_shop_name,$old_shop_name);
            //dd($compare);
            if(count($compare)>0)
            {
                foreach ($compare as $key=>$value){

                    //Kiểm tra list web mới thêm. Nếu web tồn tại ở server khác => inactive shop ở server đó và thêm mới ở server này
                    $checkmoveshop = 0;
                    $checkShop = Server::whereRaw(" shop_name like '%".$value."%' ")->where('id','<>',$data->id)->get();
                        if(count($checkShop) > 0){
                            foreach ($checkShop as $item){
                                $dataload = Server::findOrFail($item->id);
                                $str_old="";
                                $str="";
                                $old_shop_name =  \App\Library\Helpers::DecodeJson('shop_name', $item->shop_name);
                                $old_shop_status = \App\Library\Helpers::DecodeJson('shop_status', $item->shop_name);
                                $old_shop_end_date = \App\Library\Helpers::DecodeJson('ended_date', $item->shop_name);
                                if(count($old_shop_name) > 0){
                                    foreach ($old_shop_name as $key_c=>$value_c){
                                        if($value_c == $value)
                                        {

                                            if($old_shop_status[$key_c] == 1) {
                                                ServerLog::add($dataload, "Ngưng hoạt động shop  " . $value_c . "  - Chuyển sang server " . $data->ipaddress);
                                                ServerLog::add($data, "Chuyển shop " . $value_c . " từ server " . $dataload->ipaddress . " sang");
                                                $checkmoveshop = 1;
                                            }
                                            if ($key_c == (count($old_shop_name) - 1)) {
                                                $str_old = $str_old . '"' . $old_shop_status[$key_c] . '"';
                                            } else {
                                                $str_old = $str_old . '"' . $old_shop_status[$key_c] . '",';
                                            }
                                            $old_shop_status[$key_c] = "0";
                                            if ($key_c == (count($old_shop_name) - 1)) {
                                                $str = $str . '"' . $old_shop_status[$key_c] . '"';
                                            } else {
                                                $str = $str . '"' . $old_shop_status[$key_c] . '",';
                                            }

                                        }
                                        else{
                                            if ($key_c == (count($old_shop_name) - 1)) {
                                                $str_old = $str_old . '"' . $old_shop_status[$key_c] . '"';
                                            } else {
                                                $str_old = $str_old . '"' . $old_shop_status[$key_c] . '",';
                                            }
                                            $old_shop_status[$key_c] = $old_shop_status[$key_c];
                                            if ($key_c == (count($old_shop_name) - 1)) {
                                                $str = $str . '"' . $old_shop_status[$key_c] . '"';
                                            } else {
                                                $str = $str . '"' . $old_shop_status[$key_c] . '",';
                                            }
                                        }
                                    }
                                }
                                $str_old = '"shop_status":[' . $str_old . ']';
                                $str = '"shop_status":[' . $str . ']';
                                $dataload->shop_name = str_replace($str_old, $str, $dataload->shop_name);
                                $dataload->save();
                            }
                        }
                        if($checkmoveshop == 0) {
                            ServerLog::add($data, 'Thêm mới shop ' . $value . '');
                        }
                }
            }
        }
        else{
            $compare  =  array_diff_key($old_shop_name,$new_shop_name);
            //dd($compare);
            if(count($compare)>0)
            {
                foreach ($compare as $key=>$value){
                    ServerLog::add($data, 'Xóa shop '.$value.'');
                }
            }
        }

        ActivityLog::add($request, 'Cập nhật thành công '.$this->module.' #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
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
        $data =  Server::where("id",$input)->first();
        if($data) {
            $data->status = 0;
            $data->save();
            ActivityLog::add($request, 'Xóa thành công ' . $this->module . ' #' . json_encode($input));
            ServerLog::add($data,"Xóa server id #".$data->id."");
            return redirect()->back()->with('success', __('Xóa thành công !'));
        }
        else{
            return redirect()->back()->with('success', __('Xóa thất bại, không tồn tại ID xóa !'));
        }
    }

     public function update_field(Request $request)
    {
        $id=$request->id;
        $value=$request->value;

        $data =  Server::where('server.status', '<>', '999')->findOrFail($id);
        $old_value = $data->price;
        $data->price = $value;
        $data->save();
        //Get current List Shop
        $shop_name =  \App\Library\Helpers::DecodeJson('shop_name',$data->shop_name);
        //Select lstShopname from Shop
        $lstShop = Shop::where("server_id",$data->id)->get();
        if(isset($lstShop) && count($lstShop) > 0){
            foreach ($lstShop as $item){
                array_push($shop_name,$item->domain);
            }
        }
        if($old_value == $value){
            return response()->json([
            'success'=>true,
            'message'=>__('Giá trị cập nhật không đổi !'),
            'redirect'=>''
        ]);
        }
        ActivityLog::add($request, config($this->module).': Cập nhật giá server #'.$id.' từ ['.$old_value.'] -> ['.$value.']');

        ServerLog::add($data,"Cập nhật giá server id #".$data->id." từ ".$old_value ." sang ".$value);
        return response()->json([
            'success'=>true,
            'message'=>__('Cập nhật thành công !'),
            'redirect'=>''
        ]);

    }

    public function  load_DrdArrSvc(Request $request){
        $htmlAttribute= "";
        $server_category_id = $request->server_category_id;
        $server_selected = $request->server_selected;
        $htmlAttribute .="<div class=\"form-group row\">
                        <div class=\"col-12 col-md-6\">
                            <label>". __('Mảng server') ."</label>
                            <select name=\"type_category_id\" class=\"form-control select3 col-md-5\" id=\"kt_select2_4\" style=\"width: 100%\">
                                <option value=\"0\">-- ".__('Không chọn mảng server')." --</option>";
        if(isset($server_category_id) && $server_category_id != "" && $server_category_id != 0){
            $serverChild = Server_Category::where('status',1)->where('parent_id',$server_category_id)->where('module','catalog')->get();
            if(isset($serverChild) && count($serverChild) > 0){
                foreach ($serverChild as $item){
                    if(isset($server_selected) && $item->id == $server_selected){
                        $htmlAttribute .="<option value='".$item->id."' selected>".$item->title."</option>";
                    }
                    else{
                        $htmlAttribute .="<option value='".$item->id."'>".$item->title."</option>";
                    }
                }
            }
        }

        $htmlAttribute .="</select></div></div>";
        $htmlAttribute .="<script>
        $(document).ready(function() {
            $('.select3').select2();
        });
    </script>";
        return response()->json(array('status' => "SUCCESS","msg"=>"Đã load","htmlAttribute"=>$htmlAttribute), 200);
    }


    public function  load_DrdArrSvcIndex(Request $request){
        $htmlAttribute= "";
        $name_sv = "";
        $server_category_id = $request->server_category_id;
        $server = Server_Category::where('status',1)->where('id',$server_category_id)->where('module','catalog')->first();
        if($server){
            $name_sv = " thuộc - " . $server->title;
        }
        $htmlAttribute .="
                        <div class=\"input-group\">
                            <div class=\"input-group-prepend\">
                            <span class=\"input-group-text\"><i
                                    class=\"la la-calendar-check-o glyphicon-th\"></i></span>
                            </div>
                            <select name=\"type_category_id\" id=\"type_category_id\" class=\"form-control datatable-input\">
                                <option value=\"\">--Tất cả các mảng ".$name_sv."--</option>";
        if(isset($server_category_id) && $server_category_id != "" && $server_category_id != 0){
            $serverChild = Server_Category::where('status',1)->where('parent_id',$server_category_id)->where('module','catalog')->get();
            if(isset($serverChild) && count($serverChild) > 0){
                foreach ($serverChild as $item){
                    $htmlAttribute .="<option value='".$item->id."'>".$item->title."</option>";
                }
            }
        }
        $htmlAttribute .="</select>
                    </div>";
        return response()->json(array('status' => "SUCCESS","msg"=>"Đã load","htmlAttribute"=>$htmlAttribute), 200);
    }





    public function gettotal_price(Request $request)
    {
        $datatable=Server::where('server.status','<>','999')->where('type',1);
        $server_name = $request->get("server_name");
        $shop_name = $request->get("shop_name");
        $ipaddress = $request->get("ipaddress");
        $parrent_id = $request->get("parrent_id");
        $server_category_id = $request->get("server_category_id");
        $type_category_id = $request->get("type_category_id");
        $status = $request->get("status");
        if ($server_category_id != "")  {
//            //Check xem danh mục này có danh mục con hay không, Nếu có, lấy thông tin cả danh mục con
//            $parentData = Server_Category::where('parent_id',$server_category_id)->pluck('id')->toArray();
//            if(count($parentData) > 0){
//                array_push($parentData,$server_category_id);
//                $datatable->whereIn('server_category_id',$parentData);
//            }
//            else{
//                $datatable->where('server_category_id',$server_category_id);
//            }
            $datatable->where('server_category_id',$server_category_id);
        }
        if ($type_category_id != "")  {
            $datatable->where('type_category_id',$type_category_id);
        }
        if ($parrent_id != "")  {
            $datatable->where('parrent_id',$parrent_id);
        }
        if (strlen($server_name) > 0)  {
            $datatable->where('title', 'LIKE', '%' . $server_name . '%');
        }
        if (strlen($shop_name) > 0)  {
            $shopSearch_id1 = Shop::where('domain','LIKE', '%' . $request->get('shop_name') . '%')->pluck('server_id');
            //$datatable->where('shop_name', 'LIKE', '%' . $shop_name . '%');
            $datatable->where(function($q) use($request,$shopSearch_id1){
                $q->orWhere('shop_name', 'LIKE', '%' . $request->get('shop_name') . '%');
                $q->orWhere(function($t) use($shopSearch_id1){
                    $t->whereIn('server.id',$shopSearch_id1);
                });
            });
        }
        if (strlen($ipaddress) > 0)  {
            $arrIP = explode(',', $ipaddress);
            $datatable->whereIn('ipaddress', $arrIP);
        }
        if ($status != "") {
            $datatable->where('status',$status );
        }
        $total = $datatable->sum('price');
        return response()->json([
            'success'=>true,
            'total'=>$total,
            'message'=>__('Success !'),
            'redirect'=>''
        ]);

    }

    public function UpdateServer(Request $request){
        try{
            $shop = Shop::all();
            foreach($shop as $shop_item) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,fr-FR;q=0.6,fr;q=0.5',
                    'Cache-Control: max-age=0',
                    'Connection: keep-alive',
                    'Upgrade-Insecure-Requests: 1',
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36'

                ));
                $domain = "https://" . $shop_item->domain . "/api/ip";
                curl_setopt($ch, CURLOPT_URL, $domain);
                curl_setopt($ch, CURLOPT_COOKIEFILE, "");
                curl_setopt($ch, CURLOPT_COOKIEJAR, "");

                $ketqua = curl_exec($ch);
                $ketqua = json_decode($ketqua);
                try {
                    if (isset($ketqua->ip) && $ketqua->ip != "") {
                        $ipweb = $ketqua->ip;
                        self::updateServerName($shop_item->id, $shop_item->domain, $ipweb);
                    } else {
                        $domain = "http://" . $shop_item->domain . "/api/ip";
                        curl_setopt($ch, CURLOPT_URL, $domain);
                        curl_setopt($ch, CURLOPT_COOKIEFILE, "");
                        curl_setopt($ch, CURLOPT_COOKIEJAR, "");
                        $ketqua = curl_exec($ch);
                        $ketqua = json_decode($ketqua);
                        if (isset($ketqua->ip) && $ketqua->ip != "") {
                            $ipweb = $ketqua->ip;
                            self::updateServerName($shop_item->id, $shop_item->domain, $ipweb);

                        } else {
                            $ipweb = "0.0.0.0";
                            $myfile = fopen(storage_path() . "/logs/log-ThuCongServer.txt", "a") or die("Unable to open file!");
                            $txt = Carbon::now() . "__Check  thu cong: Lỗi không lấy được IP shop: " . $shop_item->domain . ":" . $ipweb;
                            fwrite($myfile, $txt . "\n");
                            fclose($myfile);
                        }
                    }
                } catch (\Exception $e) {
                    $ipweb = "0.0.0.0";
                    $myfile = fopen(storage_path() . "/logs/log-ThuCongServer.txt", "a") or die("Unable to open file!");
                    $txt = Carbon::now() . "__Check  thu cong: Lỗi không lấy được IP shop: " . $shop_item->domain . ":" . $ipweb;
                    fwrite($myfile, $txt . "\n");
                    fclose($myfile);
                }
                curl_close($ch);
                continue;
            }
            //Check active or inactive server
            $listServer = Server::where("type",1)->get();
            foreach($listServer as $sv_item){
                $countsub = Shop::where("server_id",$sv_item->id)->count();
                $shop_name =  \App\Library\Helpers::DecodeJson('shop_name',$sv_item->shop_name);
                if($countsub > 0 || (isset($shop_name) && $shop_name != null && count($shop_name) > 0)){
                    if($sv_item->status == 2)
                    {
                        $sv_item->status = 1;
                        $sv_item->save();
                        ServerLog::add($sv_item,"Cập nhật trạng thái hoạt động server #".$sv_item->id);
                    }
                }
                else{
                    if($sv_item->status == 1)
                    {
                        $sv_item->status = 2;
                        $sv_item->save();
                        ServerLog::add($sv_item,"Cập nhật trạng thái dừng hoạt động server #".$sv_item->id);
                    }
                }

            }
            $status = 1;
            $message = "Success";
        }catch (\Exception $e) {
            $status = 0;
            $message = "Fails";
            Log::error($e);
            $myfile = fopen(storage_path() ."/logs/log-ThuCongServer.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().":Check thu cong: : Check that bai";
            fwrite($myfile, $txt ."\n");
            fclose($myfile);
        }


        return response()->json([
            'message'=> $message,
            'status'=> $status
        ]);
    }


    public function updateServerName($id_shop,$web,$ip){
        $shop = Shop::where("domain",$web)->first();
        if($shop){
            $shop_id = $id_shop;
            $current_shop_server_id = 0;
            if( $shop->server_id != null && $shop->server_id > 0) {
                $current_shop_server_id = $shop->server_id;
            }
            //Lấy thông tin server
            $server = Server::where("ipaddress",$ip)->first();
            if($server){

                if($current_shop_server_id > 0) {
                    if ($current_shop_server_id != $server->id) {//Trường hợp 2 ID server khác nhau-Ghi log thay đổi server
                        //Cập nhật ID server vào Shop
                        $shop->server_id = $server->id;
                        $shop->save();
                        //Ghi log thay đổi
                        $cur_server = Server::where("id",$current_shop_server_id)->first();
                        if($cur_server) {
                            ServerLog::add($cur_server,"Dời Shop #" . $shop->domain . " từ server #" . $current_shop_server_id . " sang server #" . $server->id,$server);
                            ServerLog::add($server,"Dời Shop #" . $shop->domain . " từ server #" . $current_shop_server_id . " sang server #" . $server->id,$cur_server);
                        }
                    }
                }
                else{
                    $shop->server_id = $server->id;
                    $shop->save();
                    ServerLog::add($server,"Thêm mới Shop #".$shop->domain."");
                }
            }
            else{//Không có server, tạo mới server
                //Tạo mới server
                $new_server = Server::create([
                    'ipaddress' => $ip,
                    'type' => 1,
                    'status' => 1
                ]);
                ServerLog::add($new_server,"Tạo mới server id #".$new_server->id."");

                //Cập nhật Server mới cho shop
                $shop->server_id = $new_server->id;
                $shop->save();
                //Ghi log thay đổi
                if($current_shop_server_id >0) {
                    $cur_server = Server::where("id",$current_shop_server_id)->first();
                    if($cur_server) {
                        ServerLog::add($cur_server,"Dời Shop #" . $shop->domain . " từ server #" . $current_shop_server_id . " sang server #" . $server->id,$new_server);
                        ServerLog::add($new_server,"Dời Shop #" . $shop->domain . " từ server #" . $current_shop_server_id . " sang server #" . $server->id,$cur_server);
                    }
                }
                else{
                    ServerLog::add($new_server,"Thêm mới Shop #".$shop->domain."");
                }
            }
        }
    }


    public function UpdateShop(Request $request){
        try{
            $id=$request->id;
            $group_id=$request->group_id;
            $cf_status=$request->cf_status;
            $status=$request->status;
            if($id == null || $id == "" || $id <= 0)
            {
                return response()->json([
                    'message'=> "Shop không tồn tại.",
                    'status'=> 0
                ]);
            }

            $shop = Shop::where("id",$id)->first();
            if($shop)
            {
                $shop->group_id = $group_id;
                $shop->cf_status = $cf_status;
                $shop->status = $status;
                $shop->save();
                return response()->json([
                    'message'=> "Cập nhật thành công.Đang tải lại trang...",
                    'status'=> 1
                ]);
            }
            else{
                return response()->json([
                    'message'=> "Shop không tồn tại.",
                    'status'=> 0
                ]);
            }

        }catch (\Exception $e) {
            $status = 0;
            $message = "Cập nhật thất bại. Vui lòng thử lại sau!";
            Log::error($e);
            $myfile = fopen(storage_path() ."/logs/log-ThuCongServer.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().":Check thu cong: : Check that bai";
            fwrite($myfile, $txt ."\n");
            fclose($myfile);
        }


        return response()->json([
            'message'=> $message,
            'status'=> $status
        ]);
    }



}
