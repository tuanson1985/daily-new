<?php

namespace App\Http\Controllers\Admin\Service;

use App\Exports\ExportData;
use App\Http\Controllers\Controller;
use App\Library\Helpers;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Item;
use App\Models\ItemConfig;
use App\Models\LogEdit;
use App\Models\Order;
use App\Models\Provider;
use App\Models\Server;
use App\Models\ServiceAccess;
use App\Models\Shop;
use App\Models\Shop_Group;
use App\Models\Theme;
use App\Models\User;
use App\Models\UserAccess;
use Carbon\Carbon;
use Excel;
use Html;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ItemController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;

    public function __construct(Request $request)
    {



        $this->module = $request->segments()[1] ?? "";
        $this->moduleCategory = $this->module . '-category';
        $this->moduleNeedConfig = 'service';
        //set permission to function
        $this->middleware('permission:' . $this->module . '-list');
        $this->middleware('permission:' . $this->module . '-create', ['only' => ['create', 'store', 'duplicate']]);
        $this->middleware('permission:' . $this->module . '-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:' . $this->module . '-delete', ['only' => ['destroy']]);
        $this->middleware('permission:' . $this->module . '-sync-update-config', ['only' => ['postSyncUpdateConfig']]);
        $this->middleware('permission:' . $this->module . '-remove-sync-config', ['only' => ['postRemoveSyncConfig']]);


        if ($this->module != "") {
            $this->page_breadcrumbs[] = [
                'page' => route('admin.' . $this->module . '.index'),
                'title' => __(config('module.' . $this->module . '.title'))
            ];
        }
    }


    public function index(Request $request)
    {

        ActivityLog::add($request, 'Truy cập danh sách ' . $this->module);
        if ($request->ajax) {

            // thông tin dịch vụ

            $datatable = Item::with(array('groups' => function ($query) {
                $query->where('module', $this->moduleCategory);

                $query->select('groups.id', 'title');
            }))->where('module', $this->module);


            $datatable = $datatable->with('shop');

            if ($request->filled('group_id')) {

                $datatable->whereHas('groups', function ($query) use ($request) {
                    $query->where('group_id', $request->get('group_id'));
                });
            }

            if ($request->filled('id_group')) {

                $datatable->whereHas('groups', function ($query) use ($request) {
                    $query->where('group_id', $request->get('id_group'));
                });
            }

            if ($request->filled('id')) {
                $datatable->where(function ($q) use ($request) {
                    $q->orWhere('id', $request->get('id'));
                    $q->orWhere('idkey', $request->get('id'));
                });
            }


            if ($request->filled('title')) {
                $datatable->where(function ($q) use ($request) {
                    $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
                });
            }
            if ($request->filled('position')) {
                $datatable->where('position', $request->get('position'));
            }

            if ($request->filled('status')) {
                $datatable->where('status', $request->get('status'));
            }

            if ($request->filled('started_at')) {
                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }

            return \datatables()->eloquent($datatable)
                ->only([
                    'id',
                    'title',
                    'shop',
                    'module',
                    'slug',
                    'image',
                    'locale',
                    'groups',
                    'order',
                    'idkey',
                    'position',
                    'daily',
                    'status',
                    'action',
                    'created_at',
                ])
                ->editColumn('created_at', function ($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->editColumn('daily', function ($data) {
                    $html ='';
                    if (isset($data->idkey) && $data->idkey != ''){
                        return config('module.service.idkey.'.$data->idkey).' - '.$data->idkey;
                    }
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $temp = "<a href=\"" . route('admin.' . $this->module . '.edit', $row->id) . "\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.= "<a href=\"".route('admin.'.$this->module.'.duplicate',$row->id)."\"  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-primary' title=\"Nhân bản\"><i class=\"la la-copy\"></i></a>";
                    $temp .= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\"  title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    if(Auth::user()->can('service-set-permission')){
                        $temp .= "<a  href=\"" . route('admin.' . $this->module . '.set_permission', $row->id) . "\" rel=\"$row->id\" data-title=\"$row->title\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger'  title=\"Cập nhật cấu hình\"><i class=\"la la-sitemap\"></i></a>";
                    }

                    return $temp;
                })
                ->toJson();
        }

        $shop_access_user = Auth::user()->shop_access;
        $shop = Shop::orderBy('id', 'desc');
        if (isset($shop_access_user) && $shop_access_user !== "all") {
            $shop_access_user = json_decode($shop_access_user);
            $shop = $shop->whereIn('id', $shop_access_user);
        }
        $shop = $shop->pluck('title', 'id')->toArray();

        $shopSearch = Shop::where('status', 1)->get();

        $dataCategory = Group::where('module', '=', $this->moduleCategory)->orderBy('order', 'asc')->get();
        return view('admin.' . $this->module . '.item.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('shop', $shop)
            ->with('shopSearch', $shopSearch)
            ->with('dataCategory', $dataCategory);

    }

    public function create(Request $request)
    {
        $this->page_breadcrumbs[] = [
            'page' => '#',
            'title' => __("Thêm mới")
        ];

        $dataCategory = Group::where('module', '=', $this->moduleCategory)->orderBy('order', 'asc')->get();
        $shop = Shop::orderBy('id', 'desc')->get();


        //lấy nhóm shop
        $shopGroup = Shop_Group::with('shop')->orderBy('id', 'desc')->get();
        $providers = Provider::query()->where('status',1)->get();

        ActivityLog::add($request, 'Vào form create ' . $this->module);
        return view('admin.' . $this->module . '.item.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('dataCategory', $dataCategory)
            ->with('providers', $providers)
            ->with('shop', $shop);


    }

    public function store(Request $request)
    {

        //return $request->all();
        $this->validate($request, [
            'title' => 'required',
            'group_id' => 'required',
        ], [
            'title.required' => __('Vui lòng nhập tiêu đề'),
            'group_id.required' => __('Vui lòng chọn danh mục'),
        ]);
        $input = $request->all();

        $input['module'] = $this->module;
        $input['author_id'] = auth()->user()->id;
        $input['price_old'] = (float)str_replace(array(' ', '.'), '', $request->price_old);
        $input['price'] = (float)str_replace(array(' ', '.'), '', $request->price);
        $input['percent_sale'] = (float)str_replace(array(' ', '.'), '', $request->percent_sale);
        $input['shop_id'] = session('shop_id');


        $params = $request->except([
            '_method',
            '_token',
            'submit-close',
            'is_slug_override',
            'description',
            'content',
            'target',
            'url',
            'image',
            'image_extension',
            'image_banner',
            'image_icon',
            'image_logo',
            'status',
            'ended_at',
            'order',
            'gate_id',
            'idkey',
            'seo_title',
            'seo_description',
            'params_plus',
        ]);

        if ($request->get('server_mode') == 1 && $request->get('server_price') == 1){
            $indexs = count($request->server_data);

            if ($indexs > 0){
                for ($i = 0; $i < $indexs ; $i++){
                    if (count($params['price'.$i])){
                        $price = $params['price'.$i];
                        $price = array_map(function($i_price) {
                            return str_replace(",", "", $i_price);
                        }, $price);

                        $params["price".$i] = $price;
                    }
                }
            }
        }
        else{
            if (count($params['price'])){
                $price = $params['price'];
                $price = array_map(function($i_price) {
                    return str_replace(",", "", $i_price);
                }, $price);

                $params["price"] = $price;
            }
        }

        if (!empty($params['input_pack_min'])){
            $params["input_pack_min"] = str_replace(",", "", $params['input_pack_min']);
        }
        if (!empty($params['input_pack_max'])){
            $params["input_pack_max"] = str_replace(",", "", $params['input_pack_max']);
        }
        if (!empty($params['input_pack_rate'])){
            $params["input_pack_rate"] = str_replace(",", "", $params['input_pack_rate']);
        }

        if (!empty($params['service_idkey'])){
            $check_service_idkeys = $params['service_idkey'];
            $uniqueArray = array_unique($check_service_idkeys);

            // So sánh độ dài của mảng gốc và mảng sau khi loại bỏ giá trị trùng nhau
            if (count($check_service_idkeys) != count($uniqueArray)) {
                return redirect()->back()->withErrors('Trùng idkey rồi !');
            }
        }

        $input['params'] = json_encode($params, JSON_UNESCAPED_UNICODE);

        $data = Item::create($input);

        //set category
        if (isset($input['group_id']) && $input['group_id'] != 0) {
            $data->groups()->attach($input['group_id']);
        }


        $message = '';
        $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
        $message .= "\n";
        $message .= "<b>".Auth::user()->username."</b> thêm mới thành công dịch vụ:";
        $message .= "\n";
        $message .= '- Tiêu đề: '.$data->title;
        $message .= "\n";
        if (isset($data->idkey)){
            $message .= '- Cổng sms: '.config('module.service.idkey.'.$data->idkey);
            $message .= "\n";
        }
        if (isset($data->gate_id)){
            if ($data->gate_id == 0){
                $message .= '- Tự động: Không';
                $message .= "\n";
            }else{
                $message .= '- Tự động: Có';
                $message .= "\n";
            }
        }

        if ($data->idkey){
            $counts = Item::query()->where('module','service')->where('idkey',$data->idkey)->count();
            if ($counts > 1){
                $message .= '* Có 2 dịch vụ đang cấu hình cổng SMS này';
                $message .= "\n";
            }
        }

        $message .= '- Thông báo từ: '.config('app.url');
        $message .= "\n";

        Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_update_service'));

        ActivityLog::add($request, 'Tạo mới thành công ' . $this->module . ' #' . $data->id);

        if ($request->filled('submit-close')) {
            return redirect()->route('admin.' . $this->module . '.index')->with('success', __('Thêm mới thành công !'));
        } else {
            return redirect()->back()->with('success', __('Thêm mới thành công !'));
        }
    }

    public function show(Request $request, $id)
    {
        //$data = Group::findOrFail($id);
        //ActivityLog::add($request, 'Show '.$this->module.' #'.$data->id);
        //return view('admin.'.$this->module.'.item.show', compact('datatable'));
    }

    public function edit(Request $request, $id)
    {

        $this->page_breadcrumbs[] = [
            'page' => '#',
            'title' => __("Cập nhật")
        ];
        $data = Item::where('module', '=', $this->module)->findOrFail($id);

        $dataCategory = Group::where('module', '=', $this->moduleCategory)->orderBy('order', 'asc')->get();
        $providers = Provider::query()->where('status',1)->get();

        $shop = Shop::orderBy('id', 'desc')->get();
        ActivityLog::add($request, 'Vào form edit ' . $this->module . ' #' . $data->id);
        return view('admin.' . $this->module . '.item.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('providers', $providers)
            ->with('shop', $shop)
            ->with('dataCategory', $dataCategory);

    }

    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'title' => 'required',
            'group_id' => 'required',
        ], [
            'title.required' => __('Vui lòng nhập tiêu đề'),
            'group_id.required' => __('Vui lòng chọn danh mục'),
        ]);

        $data = Item::where('module', '=', $this->module)->findOrFail($id);

        //Kiểm tra giá
        $beforeUpdatePrice = [];
        $afterUpdatePrice = [];

        if (!empty($data->params)){
            $old_price_params = json_decode($data->params);

            if (!empty($old_price_params->service_idkey) && !empty($old_price_params->price)){
                $old_price_keywords = $old_price_params->price;

                foreach ($old_price_params->service_idkey??[] as $key_price => $service_price){
                    if (!empty($service_price) && !empty($old_price_keywords[$key_price])){
                        $beforeUpdatePrice[$service_price] = $old_price_keywords[$key_price];
                    }
                }
            }
        }


        //Kiểm tra thuộc tính

        $beforeUpdate = [];
        $afterUpdate = [];

        if ($data->gate_id == 0 || ($data->gate_id == 1 && \App\Library\Helpers::DecodeJson('filter_type',$data->params) == 4)){
            if (!empty($data->params)){
                $old_params = json_decode($data->params);
                if (!empty($old_params->service_idkey) && !empty($old_params->keyword)){
                    $old_keywords = $old_params->keyword;
                    foreach ($old_params->service_idkey??[] as $key => $service){
                        if (!empty($service) && !empty($old_keywords[$key])){
                            $beforeUpdate[$service] = $old_keywords[$key];
                        }
                    }
                }
            }
        }

        $input = $request->all();
        $input['module'] = $this->module;
        $input['shop_id'] = session('shop_id');

        $params = $request->except([
            '_method',
            '_token',
            'submit-close',
            'is_slug_override',
            'description',
            'content',
            'target',
            'url',
            'image',
            'image_extension',
            'image_banner',
            'image_icon',
            'image_logo',
            'status',
            'ended_at',
            'order',
            'gate_id',
            'idkey',
            'seo_title',
            'seo_description',
            'params_plus',
        ]);

        if ($request->get('server_mode') == 1 && $request->get('server_price') == 1){
            $indexs = count($request->server_data);
            if ($indexs > 0){
                for ($i = 0; $i < $indexs ; $i++){
                    if (count($params['price'.$i])){
                        $price = $params['price'.$i];
                        $price = array_map(function($i_price) {
                            return str_replace(",", "", $i_price);
                        }, $price);

                        $params["price".$i] = $price;
                    }
                }
            }
        }
        else{
            if (count($params['price'])){
                $price = $params['price'];
                $price = array_map(function($i_price) {
                    return str_replace(",", "", $i_price);
                }, $price);

                $params["price"] = $price;
            }

            $check_null_price = false;

            if (count($params['price']) && $request->get('filter_type') != 6){
                foreach ($params['price'] as $check_price){
                    if (empty($check_price) || !filter_var($check_price, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
                        $check_null_price = true;
                    }
                }
            }

            if ($check_null_price){
                return redirect()->back()->withErrors(__('Vui lòng nhập đủ giá tiền, hoặc số tiền là số nguyên dương lớn hơn 0'));
            }
        }

        if (!empty($params['input_pack_min'])){
            $params["input_pack_min"] = str_replace(",", "", $params['input_pack_min']);
        }
        if (!empty($params['input_pack_max'])){
            $params["input_pack_max"] = str_replace(",", "", $params['input_pack_max']);
        }
        if (!empty($params['input_pack_rate'])){
            $params["input_pack_rate"] = str_replace(",", "", $params['input_pack_rate']);
        }

        // Lưu log chỉnh sửa.

        $flag = true;
        $old_title = null;
        $new_title = null;
        $old_status = null;
        $new_status = null;
        $old_params = null;
        $new_params = null;
        $old_gate_id = null;
        $new_gate_id = null;
        $old_idkey = null;
        $new_idkey = null;
        $old_group_id = null;
        $new_group_id = null;

        if($request->filled('group_id')) {
            $old_group_id = $data->group_id;
            $new_group_id = $request->group_id;
            if (isset($old_group_id)){
                if ($new_group_id !== $old_group_id){
                    $flag = false;
                }
            }else{
                $flag = false;
            }
        }
        else{
            $old_group_id = $data->group_id;
            if (isset($old_group_id)){
                $flag = false;
            }
        }

        if($request->filled('title')) {
            $old_title = $data->title;
            $new_title = $request->title;
            if (isset($old_title)){
                if ($new_title !== $old_title){
                    $flag = false;
                }
            }else{
                $flag = false;
            }
        }else{
            $old_title = $data->title;
            if (isset($old_title)){
                $flag = false;
            }
        }

        if($request->filled('status')) {
            $old_status = $data->status;
            $new_status = $request->status;
            if (isset($old_status)){
                if ($new_status !== $old_status){
                    $flag = false;
                }
            }else{
                $flag = false;
            }
        }else{
            $old_status = $data->status;
            if (isset($old_status)){
                $flag = false;
            }
        }
        if($request->filled('params')) {
            $old_params = json_decode($data->params);
            $new_params = $request->params;
            if (isset($old_params)){
                if ($new_params !== $old_params){
                    $flag = false;
                }
            }else{
                $flag = false;
            }
        }else{
            $old_params = json_decode($data->params);
            if (isset($old_params)){
                $flag = false;
            }
        }

        if($request->filled('gate_id')) {
            $old_gate_id = $data->gate_id;
            $new_gate_id = $request->gate_id;
            if (isset($old_gate_id)){
                if ($new_gate_id !== $old_gate_id){
                    $flag = false;
                }
            }else{
                $flag = false;
            }
        }else{
            $old_gate_id = $data->gate_id;
            if (isset($old_gate_id)){
                $flag = false;
            }
        }

        if($request->filled('idkey')) {
            $old_idkey = $data->idkey;
            $new_idkey = $request->idkey;
            if (isset($old_idkey)){
                if ($new_idkey !== $old_idkey){
                    $flag = false;
                }
            }else{
                $flag = false;
            }
        }else{
            $old_idkey = $data->idkey;
            if (isset($old_idkey)){
                $flag = false;
            }
        }

        if (!empty($params['service_idkey'])){
            $check_service_idkeys = $params['service_idkey'];
            $uniqueArray = array_unique($check_service_idkeys);

            // So sánh độ dài của mảng gốc và mảng sau khi loại bỏ giá trị trùng nhau
            if (count($check_service_idkeys) != count($uniqueArray)) {
                return redirect()->back()->withErrors('Trùng idkey rồi !');
            }
        }

        $input['params'] = json_encode($params, JSON_UNESCAPED_UNICODE);

        if ($data->gate_id == 0 || ($data->gate_id == 1 && \App\Library\Helpers::DecodeJson('filter_type',$data->params) == 4)){

            $new_params = $input['params'];
            $new_params = json_decode($new_params);

            if (!empty($new_params->service_idkey) && !empty($new_params->keyword)){

                $new_keywords = $new_params->keyword;
                $new_prices = $new_params->price;
                foreach ($new_params->service_idkey??[] as $key => $new_service){
                    if (!empty($new_service) && !empty($new_keywords[$key])){
                        $afterUpdate[$new_service] = $new_keywords[$key];
                        $afterPriceUpdate[$new_service] = $new_prices[$key];
                    }
                }
            }

            $editedNewIndexes = [];
            $editedOldIndexes = [];
            $deletedIndexes = [];
            $addedIndexes = [];
            $send_params = [];
            $addedPriceIndexes = [];

            // Lọc ra những vị trí bị chỉnh sửa
            foreach ($beforeUpdate as $index => $value) {
                if (isset($afterUpdate[$index]) && $afterUpdate[$index] !== $value) {
                    $editedNewIndexes[$index] = $afterUpdate[$index];
                    $editedOldIndexes[$index] = $beforeUpdate[$index];
                }
            }

            // Lọc ra những vị trí bị xóa
            foreach ($beforeUpdate as $index => $value) {
                if (!isset($afterUpdate[$index])) {
                    $deletedIndexes[$index] = $value;
                }
            }

            // Lọc ra những vị trí thêm mới
            foreach ($afterUpdate as $index => $value) {
                if (!isset($beforeUpdate[$index])) {
                    $addedIndexes[$index] = $value;
                    $addedPriceIndexes[$index] = $afterPriceUpdate[$index];
                }
            }

            $message = '';
            $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
            $message .= "\n";
            $message .= "<b>".Auth::user()->username."</b> chỉnh sửa thành công dịch vụ:";
            $message .= "\n";
            $message .= '- Tiêu đề: <b>'.$data->title.'</b>';
            $message .= "\n";

            if (count($editedNewIndexes) > 0 && count($editedOldIndexes)){
                $edit_service = [];
                $edit_service['edit_new_service'] = $editedNewIndexes;
                $edit_service['edit_odl_service'] = $editedOldIndexes;
                $send_params['edit_service'] = $edit_service;
                $message .= '- <b>THÔNG TIN THUỘC TÍNH ĐÃ CHỈNH SỬA</b>';
                $message .= "\n";
                foreach ($editedNewIndexes as $edit_key => $editedNewIndexe){
                    $message .= '   + KEYWORD mới: <b>'.$editedNewIndexe.'</b>'.' - '.' KEYWORD cũ: <b>'.$editedOldIndexes[$edit_key].'</b>';
                    $message .= "\n";
                }
            }

            if (count($deletedIndexes) > 0){
                $send_params['delete_service'] = $deletedIndexes;
                $message .= '- <b>THÔNG TIN THUỘC TÍNH ĐÃ XÓA</b>';
                $message .= "\n";
                foreach ($deletedIndexes as $delete_key => $deletedIndexe){
                    $message .= '   + KEYWORD: '.$deletedIndexe;
                    $message .= "\n";
                }
            }

            if (count($addedIndexes) > 0){
                $send_params['add_service'] = $addedIndexes;
                $message .= '- <b>THÔNG TIN THUỘC TÍNH ĐÃ THÊM MỚI</b>';
                $message .= "\n";
                foreach ($addedIndexes as $add_key => $addedIndexe){
                    $message .= '   + KEYWORD: '.$addedIndexe.' Số - tiền: '.number_format($addedPriceIndexes[$add_key]);
                    $message .= "\n";
                }
            }

            if (count($editedNewIndexes) > 0 || count($deletedIndexes) > 0 || count($addedIndexes) > 0){

                $send_params = json_encode($send_params,JSON_UNESCAPED_UNICODE);


                $this->callbackToShop($send_params,$data->idkey,config('app.asset_idkey_th'));
                $this->callbackToShop($send_params,$data->idkey,config('app.asset_idkey_indo'));
                $this->callbackToShop($send_params,$data->idkey,config('app.asset_idkey_russia'));
                $this->callbackToShop($send_params,$data->idkey,config('app.asset_idkey_brazil'));

                $message .= '- Thông báo từ: '.config('app.url');
                $message .= "\n";

                Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_update_service'));
            }
        }

        $new_price_params = $input['params'];
        $new_price_params = json_decode($new_price_params);

        if (!empty($new_price_params->service_idkey) && !empty($new_price_params->price)){

            $new_price_keywords = $new_price_params->price;

            foreach ($new_price_params->service_idkey??[] as $key_price => $new_price_service){
                if (!empty($new_price_service) && !empty($new_price_keywords[$key_price])){
                    $afterUpdatePrice[$new_price_service] = $new_price_keywords[$key_price];
                }
            }
        }

        $editedPriceNewIndexes = [];
        $editedPriceOldIndexes = [];
        // Lọc ra những vị trí bị chỉnh sửa
        foreach ($beforeUpdatePrice as $index => $value) {
            if (isset($afterUpdatePrice[$index]) && $afterUpdatePrice[$index] !== $value) {
                $editedPriceNewIndexes[$index] = $afterUpdatePrice[$index];
                $editedPriceOldIndexes[$index] = $beforeUpdatePrice[$index];
            }
        }

        if (count($editedPriceNewIndexes) == count($editedPriceOldIndexes) && count($editedPriceNewIndexes) > 0 && count($editedPriceOldIndexes) > 0){

            $countService = count($editedPriceNewIndexes);

            if ($countService <= 30){
                $messagePrice = '';
                $messagePrice = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                $messagePrice .= "\n";
                $messagePrice .= "<b>".Auth::user()->username."</b> chỉnh sửa giá thuộc tính dịch vụ:";
                $messagePrice .= "\n";
                $messagePrice .= '- Tiêu đề: <b>'.$data->title.'</b>';
                $messagePrice .= "\n";
                $messagePrice .= '- <b>THÔNG TIN THUỘC TÍNH ĐÃ CHỈNH SỬA GIÁ</b>';
                $messagePrice .= "\n";
                foreach ($editedPriceNewIndexes as $edit_key_price => $editedPriceNewIndexe){
                    if (!empty($afterUpdate[$edit_key_price]) && is_numeric($editedPriceOldIndexes[$edit_key_price])){
                        $keywordPrice = $afterUpdate[$edit_key_price];
                        $messagePrice .= '   + KEYWORD: <b>'.$keywordPrice.'</b>';
                        $messagePrice .= "\n";
                        $messagePrice .= '      * Giá mới: <b>'.number_format($editedPriceNewIndexe).'</b>'.' - '.' Giá cũ: <b>'.number_format($editedPriceOldIndexes[$edit_key_price]).'</b>';
                        $messagePrice .= "\n";
                    }
                }

                Helpers::TelegramNotify($messagePrice,config('telegram.bots.mybot.channel_bot_update_service'));
            }else{

                // Tách mảng thành các mảng con với tối đa 20 phần tử
                $chunks = array_chunk($editedPriceNewIndexes, 30, true);
                foreach ($chunks as $index =>  $chunk) {
                    $solan = $index + 1;
                    $messagePrice = '';
                    $messagePrice = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                    $messagePrice .= "\n";
                    $messagePrice .= "<b>".Auth::user()->username."</b> chỉnh sửa giá thuộc tính dịch vụ:";
                    $messagePrice .= "\n";
                    $messagePrice .= '- Thông báo lần thứ: <b>'.$solan.'</b>';
                    $messagePrice .= "\n";
                    $messagePrice .= '- Tiêu đề: <b>'.$data->title.'</b>';
                    $messagePrice .= "\n";
                    $messagePrice .= '- <b>THÔNG TIN THUỘC TÍNH ĐÃ CHỈNH SỬA GIÁ</b>';
                    $messagePrice .= "\n";
                    foreach ($chunk as $edit_key_price => $editedPriceNewIndexe){
                        if (!empty($afterUpdate[$edit_key_price])){
                            $keywordPrice = $afterUpdate[$edit_key_price];
                            $messagePrice .= '   + KEYWORD: <b>'.$keywordPrice.'</b>';
                            $messagePrice .= "\n";
                            $messagePrice .= '      * Giá mới: <b>'.number_format($editedPriceNewIndexe).'</b>'.' - '.' Giá cũ: <b>'.number_format($editedPriceOldIndexes[$edit_key_price]).'</b>';
                            $messagePrice .= "\n";
                        }
                    }

                    $messagePrice .= '- Thông báo từ: '.config('app.url');
                    $messagePrice .= "\n";

                    Helpers::TelegramNotify($messagePrice,config('telegram.bots.mybot.channel_bot_update_service'));

                }
            }

        }

        $data->update($input);
        //set category

        if (isset($input['group_id']) && $input['group_id'] != 0) {
            $data->groups()->sync($input['group_id']);
        } else {
            $data->groups()->sync([]);
        }

        if (!$flag) {

            $params_before = null;
            $params_after = null;
            //Thông tin ban đầu
            $params_before['title'] = $old_title;
            $params_after['title'] = $new_title;
            $params_before['status'] = $old_status;
            $params_after['status'] = $new_status;
            $params_before['params'] = $old_params;
            $params_after['params'] = $new_params;
            $params_before['gate_id'] = $old_gate_id;
            $params_after['gate_id'] = $new_gate_id;
            $params_before['idkey'] = $old_idkey;
            $params_after['idkey'] = $new_idkey;
            $params_before['group_id'] = $old_group_id;
            $params_after['group_id'] = $new_group_id;

            $log_data['params_before'] = json_encode($params_before);
            $log_data['params_after'] = json_encode($params_after);
            $log_data['author_id'] = Auth::user()->id;
            $log_data['type'] = 0;
            $log_data['table_name'] = 'items';
            $log_data['table_id'] = $data->id;

            LogEdit::create($log_data);

        }

        ActivityLog::add($request, 'Cập nhật thành công ' . $this->module . ' #' . $data->id);
        if ($request->filled('submit-close')) {
            return redirect()->route('admin.' . $this->module . '.index')->with('success', __('Cập nhật thành công !'));
        } else {
            return redirect()->back()->with('success', __('Cập nhật thành công !'));
        }
    }

    public function destroy(Request $request)
    {

        $input = explode(',', $request->id);


        $checkItemConfig1 = ItemConfig::whereIn('item_id', $input)->where('status',1)->pluck('id')->toArray();

        if (!empty($checkItemConfig1)) {
            return redirect()->back()->withErrors(__('Không thể xóa dịch vụ này. Vì có shop đang dùng cấu hình dịch vụ này'));
        }

        $checkItemConfig0 = ItemConfig::whereIn('item_id', $input)->where('status',0)->pluck('id')->toArray();

        //Kiểm tra dịch vụ này đã có giao dịch chưa.
        $orders = Order::whereIn('ref_id', $checkItemConfig0)->pluck('id')->toArray();

        if (!empty($orders)) {
            return redirect()->back()->withErrors(__('Không thể xóa dịch vụ này. Dịch vụ đã tồn tại giao dịch'));
        }

        ItemConfig::whereIn('item_id', $input)->delete();

        Item::where('module', '=', $this->module)->whereIn('id', $input)->delete();

        ActivityLog::add($request, 'Xóa thành công ' . $this->module . ' #' . json_encode($input));
        return redirect()->back()->with('success', __('Xóa thành công !'));
    }

    public function duplicate(Request $request, $id)
    {

        $data = Item::where('module', '=', $this->module)->find($id);
        if (!$data) {
            return redirect()->back()->withErrors(__('Không tìm thấy dữ liệu để nhân bản'));
        }
        $dataGroup = $data->groups()->get()->pluck(['id']);

        $dataNew = $data->replicate();
        $dataNew->title = $dataNew->title . " (" . ((int)$data->duplicate + 1) . ")";
        $dataNew->slug = $dataNew->slug . "-" . ((int)$data->duplicate + 1);
        $dataNew->duplicate = 0;
        $dataNew->is_slug_override = 0;
        $dataNew->save();
        //set group cho dataNew
        $dataNew->groups()->sync($dataGroup);

        //update data old plus 1 count version
        $data->duplicate = (int)$data->duplicate + 1;
        $data->save();

        ActivityLog::add($request, 'Nhân bản ' . $this->module . ' #' . $data->id . "thành #" . $dataNew->id);
        return redirect()->back()->with('success', __('Nhân bản thành công'));


    }

    public function update_field(Request $request)
    {

        $input = explode(',', $request->id);
        $field = $request->field;
        $value = $request->value;
        $whitelist = ['status'];

        if (!in_array($field, $whitelist)) {
            return response()->json([
                'success' => false,
                'message' => __('Trường cập nhật không được chấp thuận'),
                'redirect' => ''
            ]);
        }


        $data = Item::where('module', '=', $this->module)::whereIn('id', $input)->update([
            $field => $value
        ]);

        ActivityLog::add($request, 'Cập nhật field thành công ' . $this->module . ' ' . json_encode($whitelist) . ' #' . json_encode($input));

        return response()->json([
            'success' => true,
            'message' => __('Cập nhật thành công !'),
            'redirect' => ''
        ]);

    }

    // AJAX Reordering function
    public function order(Request $request)
    {


        $source = e($request->get('source'));
        $destination = $request->get('destination');

        $item = Group::where('module', '=', $this->module)->find($source);
        $item->parent_id = isset($destination) ? $destination : 0;
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
        ActivityLog::add($request, 'Thay đổi STT thành công ' . $this->module . ' #' . $item->id);
        return 'ok ';
    }


    //Get shop to Updateconfig

    public function getShopUpdateConfig(Request $request)
    {

        if ($request->ajax) {


            if ($request->filter_type == 'shop') {

                $datatable = ItemConfig::with('shop')->where('status', 1);
                if ($request->filled('item_id')) {
                    $datatable->where('item_id', $request->get('item_id'));
                } else {
                    $datatable->where('item_id', -1);
                }
                if ($request->filled('shop_access')) {
                    $datatable->whereHas('shop', function ($query) use ($request) {
                        $query->whereIn('id', $request->get('shop_access'));
                    });
                }
                return \datatables()->eloquent($datatable)->whitelist(['id'])
                    ->only([
                        'id',
                        'shop.id',
                        'shop.domain',
                        'title',
                    ])
                    ->editColumn('created_at', function ($data) {
                        return date('d/m/Y H:i:s', strtotime($data->created_at));
                    })
                    ->editColumn('status', function ($data) {
                        $temp = '';
                        $temp .= '<span class="switch switch-outline switch-icon switch-success btn-update-stt" data-id="' . $data->id . '">';
                        $temp .= '<label>';
                        if ($data->status == 1) {
                            $temp .= '<input type="checkbox" checked="checked" name="select">';
                        } else {
                            $temp .= '<input type="checkbox" name="select">';
                        }
                        $temp .= '<span></span>';
                        $temp .= '</label>';
                        $temp .= '</span>';
                        return $temp;
                    })
                    ->addColumn('group', function ($row) {
                        if (isset($row->group)) {
                            return $row->group->title;
                        } else {
                            return "";
                        }
                    })
                    ->rawColumns(['action', 'status', 'server'])
                    ->toJson();
            }

            if ($request->filter_type == 'group_shop'){
                $itemconfig = ItemConfig::query()
                    ->select('id','status','item_id','title','shop_id')
                    ->with(array('shop'=>function($query){
                        $query->select('id','title','domain','group_id');
                    }))
                    ->where('status', 1)->where('item_id', $request->get('item_id'))
                    ->get();

                $shopIds = $itemconfig->pluck('shop.id');
                $groupIds = $itemconfig->pluck('shop.group_id');

                $datatable= Shop_Group::query()
                    ->whereIn('id',$groupIds)
                    ->with(['shop'=>function($query) use($shopIds){
                        $query->select(['id','title','domain','group_id']);
                        $query->whereIn('id',$shopIds);
                    }]);

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

                return \datatables()->eloquent($datatable)
                    ->addColumn('count',function($row){
                        return $row->shop->count();
                    })
                    ->rawColumns(['count'])
                    ->toJson();

            }
        }
    }


    public function postSyncUpdateConfig(Request $request)
    {

        DB::beginTransaction();
        try {

            if ($request->group_shop == '0'){
                $inputShopId = explode(',', $request->shop_id);
                $inputShopIdUpdateWithGate = explode(',', $request->shop_id_update_with_gate);

                //lấy thông tin Itemconfig từ item_id muốn update
                $datatable = ItemConfig::with(['items' => function ($q) {
                    $q->select('id', 'idkey', 'params');
                }])->whereHas('items', function ($query) use ($request) {
                    $query->where('id', $request->get('item_id'));
                })->where('module', '=', $this->moduleNeedConfig)
                    ->whereIn('shop_id', $inputShopId)
                    ->get();

                foreach ($datatable ?? [] as $itemConfig) {
                    $itemConfig->params = $itemConfig->items->params;
                    //check shop nào muốn cập nhật cổng SMS
                    if (in_array($itemConfig->shop_id, $inputShopIdUpdateWithGate)) {
                        $itemConfig->idkey = $itemConfig->items->idkey;
                    }
                    $itemConfig->save();
                }
                DB::commit();
                ActivityLog::add($request, 'Cập nhật cấu hình gốc thành công ' . $this->module . ' #' . json_encode($request->all()));

                if (!empty($inputShopId)){
                    foreach ($inputShopId as $shop_id){
                        $shop = Shop::query()
                            ->select('id','status','domain','secret_key')
                            ->where('status',1)
                            ->where('id',$shop_id)->first();

                        if(!isset($shop)){
                            continue;
                        }

                        $url = 'https://'.$shop->domain.'/api/clear-cache';
                        $data = array();
                        $data['secret_key'] = $shop->secret_key;
                        if(is_array($data)){
                            $dataPost = http_build_query($data);
                        }else{
                            $dataPost = $data;
                        }
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
                        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        curl_setopt($ch, CURLOPT_REFERER, $actual_link);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                        $resultRaw = curl_exec($ch);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);
                        $result = json_decode($resultRaw);
                        if($result && $result->status == 1){
                            continue;
                        }else{
                            continue;
                        }
                    }
                }

                return redirect()->back()->with('success', __('Cập nhật thành công !'));
            }else{
                $inputShopId = explode(',', $request->shop_id);
                $inputShopIdUpdateWithGate = explode(',', $request->shop_id_update_with_gate);

                //shop danh cho dich vu
                $shopIds = Shop::query()
                    ->select('id','title')
                    ->whereIn('group_id',$inputShopId)->pluck('id')->toArray();

                $shopIdsWithGate = Shop::query()
                    ->select('id','title')
                    ->whereIn('group_id',$inputShopIdUpdateWithGate)->pluck('id')->toArray();

                //lấy thông tin Itemconfig từ item_id muốn update
                $datatable = ItemConfig::with(['items' => function ($q) {
                    $q->select('id', 'idkey', 'params');
                }])->whereHas('items', function ($query) use ($request) {
                    $query->where('id', $request->get('item_id'));
                })->where('module', '=', $this->moduleNeedConfig)
                    ->whereIn('shop_id', $shopIds)
                    ->get();

                foreach ($datatable ?? [] as $itemConfig) {
                    $itemConfig->params = $itemConfig->items->params;
                    //check shop nào muốn cập nhật cổng SMS
                    if (in_array($itemConfig->shop_id, $shopIdsWithGate)) {
                        $itemConfig->idkey = $itemConfig->items->idkey;
                    }
                    $itemConfig->save();
                }
                DB::commit();
                ActivityLog::add($request, 'Cập nhật cấu hình gốc thành công ' . $this->module . ' #' . json_encode($request->all()));

                if (!empty($shopIds)){
                    foreach ($shopIds as $shop_id){
                        $shop = Shop::query()
                            ->select('id','status','domain','secret_key')
                            ->where('status',1)
                            ->where('id',$shop_id)->first();

                        if(!isset($shop)){
                            continue;
                        }

                        $url = 'https://'.$shop->domain.'/api/clear-cache';
                        $data = array();
                        $data['secret_key'] = $shop->secret_key;
                        if(is_array($data)){
                            $dataPost = http_build_query($data);
                        }else{
                            $dataPost = $data;
                        }
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
                        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        curl_setopt($ch, CURLOPT_REFERER, $actual_link);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                        $resultRaw = curl_exec($ch);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);
                        $result = json_decode($resultRaw);
                        if($result && $result->status == 1){
                            continue;
                        }else{
                            continue;
                        }
                    }
                }

                return redirect()->back()->with('success', __('Cập nhật thành công !'));
            }

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error($e);
            return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');
        }

    }

    public function postRemoveSyncConfig(Request $request){


        DB::beginTransaction();

        try {
            if ($request->group_shop == '0'){
                $inputShopId = explode(',', $request->shop_id);

                //lấy thông tin Itemconfig từ item_id muốn update
                $datatable = ItemConfig::with(['items' => function ($q) {
                    $q->select('id', 'idkey', 'params');
                }])->whereHas('items', function ($query) use ($request) {
                    $query->where('id', $request->get('item_id'));
                })->where('module', '=', $this->moduleNeedConfig)
                    ->whereIn('shop_id', $inputShopId)
                    ->get();

                foreach ($datatable ?? [] as $itemConfig) {
                    $itemConfig->status = 0;
                    $itemConfig->save();
                }
                DB::commit();
                ActivityLog::add($request, 'Gỡ bỏ phân phối thành công ' . $this->module . ' #' . json_encode($request->all()));

                if (!empty($inputShopId)){
                    foreach ($inputShopId as $shop_id){
                        $shop = Shop::query()
                            ->select('id','status','domain','secret_key')
                            ->where('status',1)
                            ->where('id',$shop_id)->first();

                        if(!isset($shop)){
                            continue;
                        }

                        $url = 'https://'.$shop->domain.'/api/clear-cache';
                        $data = array();
                        $data['secret_key'] = $shop->secret_key;
                        if(is_array($data)){
                            $dataPost = http_build_query($data);
                        }else{
                            $dataPost = $data;
                        }
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
                        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        curl_setopt($ch, CURLOPT_REFERER, $actual_link);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                        $resultRaw = curl_exec($ch);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);
                        $result = json_decode($resultRaw);
                        if($result && $result->status == 1){
                            continue;
                        }else{
                            continue;
                        }
                    }
                }

                return redirect()->back()->with('success', __('Gỡ bỏ phân phối thành công !'));
            }
            else{

                $inputShopId = explode(',', $request->shop_id);

                $shopIds = Shop::query()
                    ->select('id','title')
                    ->whereIn('group_id',$inputShopId)->pluck('id')->toArray();

                //lấy thông tin Itemconfig từ item_id muốn update
                $datatable = ItemConfig::with(['items' => function ($q) {
                    $q->select('id', 'idkey', 'params');
                }])->whereHas('items', function ($query) use ($request) {
                    $query->where('id', $request->get('item_id'));
                })->where('module', '=', $this->moduleNeedConfig)
                    ->whereIn('shop_id', $shopIds)
                    ->get();

                foreach ($datatable ?? [] as $itemConfig) {
                    $itemConfig->status = 0;
                    $itemConfig->save();
                }
                DB::commit();
                ActivityLog::add($request, 'Gỡ bỏ phân phối thành công ' . $this->module . ' #' . json_encode($request->all()));

                if (!empty($shopIds)){
                    foreach ($shopIds as $shop_id){
                        $shop = Shop::query()
                            ->select('id','status','domain','secret_key')
                            ->where('status',1)
                            ->where('id',$shop_id)->first();

                        if(!isset($shop)){
                            continue;
                        }

                        $url = 'https://'.$shop->domain.'/api/clear-cache';
                        $data = array();
                        $data['secret_key'] = $shop->secret_key;
                        if(is_array($data)){
                            $dataPost = http_build_query($data);
                        }else{
                            $dataPost = $data;
                        }
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
                        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        curl_setopt($ch, CURLOPT_REFERER, $actual_link);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                        $resultRaw = curl_exec($ch);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);
                        $result = json_decode($resultRaw);
                        if($result && $result->status == 1){
                            continue;
                        }else{
                            continue;
                        }
                    }
                }

                return redirect()->back()->with('success', __('Gỡ bỏ phân phối thành công !'));
            }

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error($e);
            return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');
        }
    }

    public function exportExcel(Request $request){

        $services = \App\Models\Item::query()->where('gate_id',0)->where('status',1)->get();

        $data = [
            'data' => $services,
        ];

        return Excel::download(new ExportData($data,view('admin.service.item.excel')), 'Thống kê dịch vụ thủ công_ ' . time() . '.xlsx');
    }

    public function callbackToShop($params,$idkey,$url)
    {

        $data = array();

        $data['send_params'] = $params;

        $data['idkey'] = $idkey;
        $data['sign'] = '456daily88888%@qlt';

        $dataPost = http_build_query($data);

        try{

            for ($i=0;$i<3;$i++){
                $ch = curl_init();

                //data dạng get
                if (strpos($url, '?') !== FALSE) {
                    $url = $url . "&" . $dataPost;
                } else {
                    $url = $url . "?" . $dataPost;
                }

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);

                $resultRaw=curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                //debug thì mở cái này
//                $myfile = fopen(storage_path() . "/logs/curl_callback-update-service-to-shop-".Carbon::now()->format('Y-m-d').".txt", "a") or die("Unable to open file!");
//                $txt = Carbon::now() . " :" . $url . " [" . $httpcode . "] - " . " : " . $resultRaw . "\r\n";
//                fwrite($myfile, $txt);
//                fclose($myfile);

                if($httpcode==200){

                    if(strpos($resultRaw, "Có lỗi phát sinh.Xin vui lòng thử lại") > -1){
                        continue;
                    }
                    break;
                }
            }
        }
        catch (\Exception $e){
            \Log::error($e);
        }

    }

    public function getLogEdit(Request $request){

        $id = $request->get('id');
        $data = Item::where('id',$id)
            ->groupBy('id')
            ->findOrFail($id);
        $table_name = $data->getTable();
        $log_edit = LogEdit::where('table_name',$table_name)->with(array('author' => function ($query) {
            $query->select('id','username');
        }))->where('table_id',$data->id)->orderBy('updated_at', 'DESC')->get();
        if ( $log_edit->count() ) {
            return response()->json([
                'message' => __('Thành công !'),
                'data' => $log_edit,
                'status' => 1,
            ]);
        } else {
            return response()->json([
                'message' => __('Không có dữ liệu chỉnh sửa !'),
                'status' => 0,
            ]);
        }
    }

    public function getLogEditDetail(Request $request){
        $id = $request->get('id');
        $log_edit = LogEdit::findOrFail($id);
        return response()->json([
            'message' => __('Thành công'),
            'data' => $log_edit,
            'status' => 1,
        ]);

    }

    public function rechange(Request $request){

        if (!$request->filled('id_edit')){
            return redirect()->back()->withErrors(__('Vui lòng chọn thời điểm khôi phục'))->withInput();
        }

        $id = $request->get('id_edit');

        $new_log_edit = LogEdit::query()->where('table_name','items')->findOrFail($id);

        $old_log_edit = LogEdit::query()->where('table_name','items')->where('table_id',$new_log_edit->table_id)->orderBy('created_at','desc')->first();
//        $old_log_edit = Item::query()->where('module','service')->where('id',$new_log_edit->id)->first();


        if(!$new_log_edit){
            return redirect()->back()->withErrors(__('Không tìm thấy điểm cần khôi phục !'))->withInput();
        }

        if(!$old_log_edit){
            return redirect()->back()->withErrors(__('Không tìm thấy điểm edit trước đó !'))->withInput();
        }
        // Lưu log chỉnh sửa.

        $flag = true;
        $old_title = null;
        $new_title = null;
        $old_status = null;
        $new_status = null;
        $old_params = null;
        $new_params = null;
        $old_gate_id = null;
        $new_gate_id = null;
        $old_idkey = null;
        $new_idkey = null;
        $old_group_id = null;
        $new_group_id = null;

        //params_before cũ  params_after mới

        $params_before = $new_log_edit->params_after??null;
        $params_after = $old_log_edit->params_before??null;

        $log_data['params_before'] = $params_before;
        $log_data['params_after'] = $params_after;
        $log_data['author_id'] = Auth::user()->id;
        $log_data['type'] = 0;
        $log_data['table_name'] = 'items';
        $log_data['table_id'] = $old_log_edit->table_id;

        LogEdit::create($log_data);

        $service = Item::query()->where('module','service')->where('id',$new_log_edit->table_id)->first();

        if (isset($new_log_edit->params_before)){
            $params = json_decode($new_log_edit->params_before);
            $service->title = $params->title;
            $service->gate_id = $params->gate_id;
            $service->status = $params->status;
            $service->idkey = $params->idkey;
            if (!empty($params->params)){
                $service->params = json_encode($params->params,JSON_UNESCAPED_UNICODE);
            }
            $service->save();
        }

        ActivityLog::add($request, 'Khôi phục dữ liệu dịch vụ '.$id);
        return redirect()->back()->with('success', 'Khôi phục dữ liệu thành công');
    }

    public function post_set_permission(Request $request,$id){

        $data = Item::where('module', '=', $this->module)->findOrFail($id);

        $user_id = [];
        if ($request->filled('user_id')){
            $user_id = $request->get('user_id');
        }

        if (!$request->filled('display_type')){
            return redirect()->back()->withErrors(__('Vui lòng nhập số lần nhận đơn hàng !'))->withInput();
        }

        $display_type = (int)str_replace(array(' ', '.'), '', $request->get('display_type'));

        if (!$request->filled('is_display')){
            return redirect()->back()->withErrors(__('Vui lòng chọn cấu hình giới hạn số lần đơn !'))->withInput();
        }

        $is_display = $request->get('is_display');

        $data->display_type = $display_type;
        $data->is_display = $is_display;
        $data->save();

        $userList = User::query()->where("account_type",3)
            ->where("status",1)
            ->whereIn('id',$user_id)
            ->pluck('id')->toArray();

        //lấy danh sách các user user đã được gán.
        $access_users = ServiceAccess::query()
            ->where('module','user')
            ->whereRaw("JSON_CONTAINS(JSON_UNQUOTE(JSON_EXTRACT(params, '$.accept_role')), '\"".$data->id."\"')")
            ->whereRaw("JSON_CONTAINS(JSON_UNQUOTE(JSON_EXTRACT(params, '$.view_role')), '\"".$data->id."\"')")
            ->pluck('user_id')->toArray();

        //Lấy thông tin những ctv được phân quyền nhận đpưn dịch vụ này:

        foreach ($userList??[] as $user_id){
            $user = User::query()->where("account_type",3)
                ->where("status",1)->where('id',$user_id)->first();
            if (!isset($user)){
                continue;
            }
            $service_accept = ServiceAccess::query()
                ->where('module','user')
                ->where('user_id',$user->id)
                ->first();

            if (!isset($service_accept)){
                continue;
            }

            if (empty($service_accept->params)){
                continue;
            }

            $params = json_decode($service_accept->params);

            if (empty($params->view_role)){
                continue;
            }

            if (!in_array($data->id,$params->view_role)){
                continue;
            }

            if (empty($params->accept_role)){
                continue;
            }

            if (!in_array($data->id,$params->accept_role)){
                continue;
            }

            if (!empty($params->service_limit)){

                $service_limit_list = $params->service_limit;
                array_push($service_limit_list,(string)$data->id);
                $params->service_limit = $service_limit_list;
            }else{
                $service_limit_list[] = (string)$data->id;
                $params->service_limit = $service_limit_list;
            }

            $service_accept->params = json_encode($params);
            $service_accept->save();
        }

        $result_array_diffs = array_diff($access_users, $userList);
        foreach ($result_array_diffs??[] as $result_array_diff){
            $un_user = User::query()->where("account_type",3)
                ->where("status",1)->where('id',$result_array_diff)->first();
            if (!isset($un_user)){
                continue;
            }
            $un_service_accept = ServiceAccess::query()
                ->where('module','user')
                ->where('user_id',$un_user->id)->first();

            if (!isset($un_service_accept)){
                continue;
            }
            if (empty($un_service_accept->params)){
                continue;
            }

            $un_params = json_decode($un_service_accept->params);

            if (!empty($un_params->service_limit)){
                $service_limit_list = $params->service_limit;
                array_push($service_limit_list,$data->id);

                $diff_service_limit = array_diff($un_params->service_limit, [$data->id]);
                $un_params->service_limit = $diff_service_limit;
                if (count($un_params->service_limit) == 0){
                    unset($un_params->service_limit);
                }
            }

            $un_service_accept->params = json_encode($un_params);
            $un_service_accept->save();
        }

        ActivityLog::add($request, 'Phân quyền giới hạn số đơn nhận dịch vụ '.$id);
        return redirect()->back()->with('success', 'Phân quyền thành công dịch vụ');

    }

    public function post_set_permission_user(Request $request,$id){

        $data = Item::where('module', '=', $this->module)->findOrFail($id);

        if (!$request->filled('sticky')){
            return redirect()->back()->withErrors(__('Vui lòng chọn cấu hình phân quyền !'))->withInput();
        }

        $message = '';
        $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
        $message .= "\n";
        $message .= "<b> Cảnh báo phân quyền dịch vụ:</b> ".$data->title;
        $message .= "\n";

        $sticky = $request->get('sticky');

        if ($sticky == 1){
            $message .= "<b> Cấu hình phân quyền nhận đơn của thành viên (theo ctv): Có</b> ";
            $message .= "\n";
        }else{
            $message .= "<b> Cấu hình phân quyền nhận đơn của thành viên (theo ctv): Không</b> ";
            $message .= "\n";
        }

        $ctv_access = [];
        if ($request->filled('ctv_access')){
            $ctv_access = $request->get('ctv_access');
        }

        if (!$request->filled('member_access')){
            return redirect()->back()->withErrors(__('Vui lòng chọn thành viên !'))->withInput();
        }

        $member_access = $request->get('member_access');
        if (count($member_access) < 0){
            return redirect()->back()->withErrors(__('Vui lòng chọn thành viên !'))->withInput();
        }

        if (!$request->filled('position')){
            return redirect()->back()->withErrors(__('Vui lòng chọn cấu hình phân quyền !'))->withInput();
        }

        $position = $request->get('position');

        if ($position == 1){
            $message .= "<b> - Loại tài khoản ctv được cấu hình sẽ không được nhận đơn: CTV nhà</b> ";
            $message .= "\n";
        }elseif ($position == 2){
            $message .= "<b> - Loại tài khoản ctv được cấu hình sẽ không được nhận đơn: CTV khách</b> ";
            $message .= "\n";
        }
        else{
            $message .= "<b> - Loại tài khoản ctv được cấu hình sẽ không được nhận đơn: </b> ";
            $message .= "\n";
        }

        $data->sticky = $sticky;
        $data->position = $position;
        $data->save();

        //Danh sach ctv da chon
        $ctvList = User::query()->where("account_type",3)
            ->where("status",1)
            ->whereIn('id',$ctv_access)
            ->pluck('id')->toArray();

        //Danh sach ctv da chon
        $memberList = User::query()->where("account_type",2)
            ->where("status",1)
            ->whereIn('id',$member_access)
            ->pluck('id')->toArray();

        //lấy danh sách các user user đã được gán.
        $access_ctvs = ServiceAccess::query()
            ->where('module','user')
            ->whereRaw("JSON_CONTAINS(JSON_UNQUOTE(JSON_EXTRACT(params, '$.service_accept_ctv')), '\"".$data->id."\"')")
            ->pluck('user_id')->toArray();

        //lấy danh sách các user user đã được gán.
        $access_members = ServiceAccess::query()
            ->where('module','user')
            ->whereRaw("JSON_CONTAINS(JSON_UNQUOTE(JSON_EXTRACT(params, '$.service_accept_member')), '\"".$data->id."\"')")
            ->pluck('user_id')->toArray();
        //Lấy thông tin những ctv được phân quyền nhận đpưn dịch vụ này:

        $result_array_member_diffs = array_diff($access_members, $memberList);
        foreach ($result_array_member_diffs??[] as $result_array_member_diff){

            $un_member = User::query()->where("account_type",2)
                ->where("status",1)->where('id',$result_array_member_diff)->first();
            if (!isset($un_member)){
                continue;
            }

            $un_service_member_accept = ServiceAccess::query()->where('module','user')->where('user_id',$un_member->id)->first();

            if (!isset($un_service_member_accept)){
                continue;
            }
            if (empty($un_service_member_accept->params)){
                continue;
            }

            $un_member_params = json_decode($un_service_member_accept->params);

            if (!empty($un_member_params->service_accept_member)){
//                $service_ctv_limit_list = $params->service_accept_ctv;
//                array_push($service_ctv_limit_list,$data->id);
//
                $diff_service_member_limit = array_diff($un_member_params->service_accept_member, [$data->id]);
                $un_member_params->service_accept_member = $diff_service_member_limit;
                if (count($un_member_params->service_accept_member) == 0){
                    unset($un_member_params->service_accept_member);
                }
            }

            $un_service_member_accept->params = json_encode($un_member_params);
            $un_service_member_accept->save();
        }

        $result_array_ctv_diffs = array_diff($access_ctvs, $ctvList);

        foreach ($result_array_ctv_diffs??[] as $result_array_ctv_diff){
            $un_ctv = User::query()->where("account_type",3)
                ->where("status",1)->where('id',$result_array_ctv_diff)->first();
            if (!isset($un_ctv)){
                continue;
            }

            $un_service_ctv_accept = ServiceAccess::query()->where('module','user')->where('user_id',$un_ctv->id)->first();

            if (!isset($un_service_ctv_accept)){
                continue;
            }
            if (empty($un_service_ctv_accept->params)){
                continue;
            }

            $un_ctv_params = json_decode($un_service_ctv_accept->params);

            if (!empty($un_ctv_params->service_accept_ctv)){
//                $service_ctv_limit_list = $params->service_accept_ctv;
//                array_push($service_ctv_limit_list,$data->id);
//
                $diff_service_ctv_limit = array_diff($un_ctv_params->service_accept_ctv, [$data->id]);
                $un_ctv_params->service_accept_ctv = $diff_service_ctv_limit;
                if (count($un_ctv_params->service_accept_ctv) == 0){
                    unset($un_ctv_params->service_accept_ctv);
                }
            }

            $un_service_ctv_accept->params = json_encode($un_ctv_params);
            $un_service_ctv_accept->save();
        }
        $arr_ctv_notis = [];
        foreach ($ctvList??[] as $user_id){
            $user = User::query()->where("account_type",3)
                ->where("status",1)->where('id',$user_id)->first();

            if (!isset($user)){
                continue;
            }

            array_push($arr_ctv_notis,$user->username);
            $ctv_accept = ServiceAccess::query()->where('module','user')->where('user_id',$user->id)->first();

            if (!isset($ctv_accept)){
                continue;
            }

            if (empty($ctv_accept->params)){
                continue;
            }

            $params = json_decode($ctv_accept->params);

            if (!empty($params->service_accept_ctv)){
                $service_accept_ctv = $params->service_accept_ctv;
                if (!in_array((string)$data->id,$service_accept_ctv)){
                    array_push($service_accept_ctv,(string)$data->id);
                    $params->service_accept_ctv = $service_accept_ctv;
                }
            }else{
                $service_accept_ctv[] = (string)$data->id;
                $params->service_accept_ctv = $service_accept_ctv;
            }

            $ctv_accept->params = json_encode($params);
            $ctv_accept->save();
        }
        $arr_member_notis = [];
        foreach ($memberList??[] as $member_id){
            $member = User::query()->where("account_type",2)
                ->where("status",1)->where('id',$member_id)->first();
            if (!isset($member)){
                continue;
            }

            array_push($arr_member_notis,$member->username);
            $member_accept = ServiceAccess::query()->where('module','user')->where('user_id',$member->id)->first();
            if (isset($member_accept)){
                $params = json_decode($member_accept->params);
                if (!empty($member_accept->params) && !empty($params->service_accept_member)){
                    $service_accept_member = $params->service_accept_member;
                    if (!in_array((string)$data->id,$service_accept_member)){
                        array_push($service_accept_member,(string)$data->id);
                        $params->service_accept_member = $service_accept_member;
                    }
                }else{
                    $service_accept_member[] = (string)$data->id;
                    $params = new \stdClass();
                    $params->service_accept_member = $service_accept_member;
                }
                $member_accept->params = json_encode($params);
                $member_accept->save();
            }else{
                $service_accept_member[] = (string)$data->id;
                $params = new \stdClass();
                $params->service_accept_member = $service_accept_member;
                $service_accept = ServiceAccess::create([
                    'module' => 'user',
                    'user_id' => $member->id,
                    'params' => json_encode($params),
                ]);
            }
        }


        $listCTVNo = '';
        foreach ($arr_ctv_notis??[] as $arr_ctv_noti){
            if ($listCTVNo == ''){
                $listCTVNo = $arr_ctv_noti;
            }else{
                $listCTVNo = $listCTVNo.','.$arr_ctv_noti;
            }
        }

        if(count($arr_ctv_notis)){
            $message .= "<b> - Danh sách ctv không thể nhận đơn:</b> ".$listCTVNo;
            $message .= "\n";
        }

        $listMemNo = '';
        foreach ($arr_member_notis??[] as $arr_member_noti){
            if ($listMemNo == ''){
                $listMemNo = $arr_member_noti;
            }else{
                $listMemNo = $listMemNo.','.$arr_member_noti;
            }
        }

        if(count($arr_member_notis)){
            $message .= "<b> - Danh sách thành viên áp dụng:</b> ".$listMemNo;
            $message .= "\n";
        }

        $message .= '- Thông báo từ: '.config('app.url');
        $message .= "\n";

        Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_telegram_pemission_update'));

        ActivityLog::add($request, 'Phân quyền giới hạn số đơn nhận dịch vụ '.$id);
        return redirect()->back()->with('success', 'Phân quyền thành công dịch vụ');

    }

    public function set_permission(Request $request,$id){

        $this->page_breadcrumbs[] = [
            'page' => '#',
            'title' => __("Phân quyền nhận đơn dịch vụ")
        ];
//        accept_role
        $data = Item::where('module', '=', $this->module)->findOrFail($id);

        //Lấy thông tin những ctv được phân quyền nhận đpưn dịch vụ này:

        $access_limit_users = User::query()
            ->where("account_type",3)
            ->where("status",1)
            ->whereHas('service_access',function ($q) use ($data){
                $q->whereRaw("JSON_CONTAINS(JSON_UNQUOTE(JSON_EXTRACT(params, '$.service_limit')), '\"".$data->id."\"')");
            })->get();

        $access_limit_ctvs = User::query()
            ->where("account_type",3)
            ->where("status",1)
            ->whereHas('service_access',function ($q) use ($data){
                $q->whereRaw("JSON_CONTAINS(JSON_UNQUOTE(JSON_EXTRACT(params, '$.service_accept_ctv')), '\"".$data->id."\"')");
            })->pluck('id')->toArray();

        $access_ctvs = User::query()
            ->where("account_type",3)
            ->where("status",1)
            ->whereHas('service_access',function ($q) use ($data){
                $q->whereRaw("JSON_CONTAINS(JSON_UNQUOTE(JSON_EXTRACT(params, '$.accept_role')), '\"".$data->id."\"')");
            })->pluck('id')->toArray();

        $access_limit_members = User::query()
            ->where("account_type",2)
            ->where("status",1)
            ->whereHas('service_access',function ($q) use ($data){
                $q->whereRaw("JSON_CONTAINS(JSON_UNQUOTE(JSON_EXTRACT(params, '$.service_accept_member')), '\"".$data->id."\"')");
            })->pluck('id')->toArray();

        if($request->ajax) {
            if ($request->filled('show_shop') && $request->show_shop == 2){

                $arr_users = [];
                //Lấy thông tin những ctv được phân quyền nhận đpưn dịch vụ này:
                $access_users = ServiceAccess::query()
                    ->where('module','user')
                    ->whereRaw("JSON_CONTAINS(JSON_UNQUOTE(JSON_EXTRACT(params, '$.accept_role')), '\"".$data->id."\"')")
                    ->whereRaw("JSON_CONTAINS(JSON_UNQUOTE(JSON_EXTRACT(params, '$.view_role')), '\"".$data->id."\"')")
                    ->pluck('user_id')->toArray();

                $datatable= User::query()
                    ->where("account_type",3)
                    ->where("status",1)
                    ->whereIn('id',$access_users)
                    ->whereNotIn('id',$arr_users);

                if (isset($arr_users) && count($arr_users)){
                    $datatable->where(function($q) use($arr_users){
                        $q->orWhereNotIn('id', $arr_users);
                    });
                }

                if ($request->filled('id'))  {
                    $datatable->where(function($q) use($request){
                        $q->orWhere('id', $request->get('id'));
                        $q->orWhere('username', 'LIKE', '%' . $request->get('id') . '%');
                    });
                }

                return \datatables()->eloquent($datatable)->whitelist(['id'])
                    ->only([
                        'id',
                        'username',
                        'account_type',
                        'email',
                        'roles',
                        'balance',
                        'balance_in',
                        'balance_out',
                        'phone',
                        'image',
                        'status',
                        'created_at',
                        'currency_user',
                        'action',
                    ])
                    ->editColumn('username', function($row) {
                        $temp = '';
                        $temp .= $row->username;
                        return $temp;
                    })
                    ->toJson();
            }

            // thông tin dịch vụ
            if ($request->filled('flash_sale_service') && $request->get('flash_sale_service') == 1){
                $service = Item::query()
                    ->with('shop')
                    ->where('module', 'service')
                    ->where('id', $data->id)
                    ->where('status', 1)
                    ->first();
                if (!isset($service->params)){
                    $data = [];
                    $collection = new Collection($data);
                    return \datatables()->collection($collection)
                        ->only([
                            'id',
                            'keyword',
                            'name',
                            'service_idkey',
                            'price',
                        ])
                        ->toJson();
                }
                $params = json_decode($service->params);

                $array_name = $params->name??'';
                $array_keyword = $params->keyword??'';
                $array_price = $params->price??'';
                $array_service_idkey = $params->service_idkey??'';
                $data = [];
                $index = 0;
                foreach ($array_name as $key => $name) {
                    $index = $key + 1;
                    $data[] = [
                        'id' => $index,
                        'keyword' => $array_keyword[$key]??'',
                        'name' => $name,
                        'service_idkey' => $array_service_idkey[$key]??'',
                        'price' => $array_price[$key]??'',
                    ];
                }

                $collection = new Collection($data);

                return \datatables()->collection($collection)
                    ->only([
                        'id',
                        'keyword',
                        'name',
                        'service_idkey',
                        'price',
                    ])
                    ->toJson();
            }
        }

        $dataCategory = Group::where('module', '=', $this->moduleCategory)->orderBy('order', 'asc')->get();

        $members = User::query()->where('account_type',2)->where('status',1)->get();
        $ctvs = User::query()->where('account_type',3)
            ->whereIn('id',$access_ctvs)
            ->where('status',1)->get();
        $service_accept_allow_users = [];
        $service_accept = ServiceAccess::query()
            ->with('service_user_access')
            ->where('module','service')
            ->where('user_id',$data->id)->first();
        if (!empty($service_accept->service_user_access)){
            $service_accept_allow_users = $service_accept->service_user_access->pluck('user_id')->toArray();
        }

        $service_accept_allow_attributes = [];
        $type_information_ctv_access = 0;
        if (isset($service_accept) && $service_accept->params){
            $service_accept_params = json_decode($service_accept->params);
            if (!empty($service_accept_params->allow_attribute)){
                $service_accept_allow_attributes = $service_accept_params->allow_attribute;
            }
            if (!empty($service_accept_params->type_information_ctv_access)){
                $type_information_ctv_access = $service_accept_params->type_information_ctv_access;
            }
        }

        $names = [];

        if (isset($data->params)){
            $params = json_decode($data->params);
            if (!empty($params->name)){
                $names = $params->name;
            }
        }


        ActivityLog::add($request, 'Vào form set permission ' . $this->module . ' #' . $data->id);
        return view('admin.' . $this->module . '.item.set_permission')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('access_limit_users',$access_limit_users)
            ->with('access_limit_ctvs',$access_limit_ctvs)
            ->with('service_accept_allow_users',$service_accept_allow_users)
            ->with('names',$names)
            ->with('type_information_ctv_access',$type_information_ctv_access)
            ->with('service_accept_allow_attributes',$service_accept_allow_attributes)
            ->with('access_limit_members',$access_limit_members)
            ->with('members',$members)
            ->with('ctvs',$ctvs)
            ->with('dataCategory', $dataCategory);
    }

    public function post_set_permission_detail_user(Request $request,$id){

        $data = Item::where('module', '=', $this->module)->findOrFail($id);

        if (!$request->filled('type_information_ctv_access')){
            return redirect()->back()->withErrors(__('Vui lòng chọn cấu hình loại ctv áp dụng!'))->withInput();
        }

        $type_information_ctv_access = $request->get('type_information_ctv_access');

//        if (!$request->filled('service_ctv_access')){
//            return redirect()->back()->withErrors(__('Vui lòng chọn ctv!'))->withInput();
//        }

        $service_ctv_access = $request->get('service_ctv_access');

        if (isset($service_ctv_access) && count($service_ctv_access) <= 0 && $type_information_ctv_access == 0){
            return redirect()->back()->withErrors(__('Vui lòng chọn ctv!'))->withInput();
        }

        if (!$request->filled('service_attribute')){
            return redirect()->back()->withErrors(__('Vui lòng chọn thuộc tính!'))->withInput();
        }

        $service_attributes = $request->get('service_attribute');

        if (count($service_attributes) <= 0){
            return redirect()->back()->withErrors(__('Vui lòng chọn thuộc tính!'))->withInput();
        }

        $service_accept = ServiceAccess::query()->where('module','service')->where('user_id',$data->id)->first();

        DB::beginTransaction();
        try {

            if (!isset($service_accept)){
                //set tên công việc
                $service_accept = ServiceAccess::create([
                    'user_id' => $data->id,
                    'module' => 'service',
                ]);
            }

            $input_params = new \stdClass();
            if (isset($service_accept->params)){
                $input_params = json_decode($service_accept->params);
                $input_params->allow_attribute = $service_attributes;
                $input_params->type_information_ctv_access = $type_information_ctv_access;
                $params = json_encode($input_params,JSON_UNESCAPED_UNICODE);
                $service_accept->params = $params;
            }else{
                $input_params->allow_attribute = $service_attributes;
                $input_params->type_information_ctv_access = $type_information_ctv_access;
                $params = json_encode($input_params,JSON_UNESCAPED_UNICODE);
                $service_accept->params = $params;

            }
            $service_accept->save();
            //Xóa hết giá trị cũ.
            UserAccess::query()->where('service_access_id',$service_accept->id)->delete();

            foreach ($service_ctv_access??[] as $ctv_id){
                $user = User::query()
                    ->where('id',$ctv_id)
                    ->where('account_type',3)
                    ->where('status',1)
                    ->first();
                if (isset($user)){
                    //set tên công việc
                    UserAccess::create([
                        'user_id' => $user->id,
                        'service_access_id' => $service_accept->id,
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Phân quyền thành công dịch vụ');
        }catch (\Exception $e) {
            DB::rollback();
            \Log::error( $e);
            return redirect()->back()->withErrors(__('Có lỗi phát sinh xin vui lòng thử lại !'))->withInput();

        }
    }
}
