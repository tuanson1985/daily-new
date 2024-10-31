<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 20/12/2018
 * Time: 14:43 CH
 */


namespace App\Http\Controllers\Admin\ToolGame\RobloxGem;
use App\Http\Controllers\Controller;
use App\Library\ChargeGameGateway\RobloxGate;
use App\Library\ChargeGameGateway\RobloxGateV2;
use App\Models\ActivityLog;
use App\Models\Bot;
use App\Models\Item;
use App\Models\Roblox_Bot;
use App\Models\Roblox_Bot_Item;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class InfoBotController extends Controller
{
    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;
    public function __construct()
    {



        $this->module='roblox-gem-info-bot';

        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');
        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);


        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.roblox-gem-info-bot.index'),
                'title' => __('Bán roblox gem - Bot')
            ];
        }
    }



    public function index(Request $request)
    {

        if ($request->ajax()) {

            $model = Roblox_Bot::query()
                ->where('type_order',2)
                ->with('roblox_bot_item')
                ->orderBy('ver', 'asc')
                ->orderBy('server', 'asc');

            if ($request->filled('id')) {
                $model->where('id', 'LIKE', '%' . $request->get('id') . '%');
                $model->orWhere('idkey', 'LIKE', '%' . $request->get('id') . '%');
            }

            if ($request->filled('acc')) {
                $model->where('acc', 'LIKE', '%' . $request->get('acc') . '%');
            }

            if ($request->filled('status')) {
                $model->where('active', 'LIKE', '%' . $request->get('status') . '%');
            }

            if ($request->filled('started_at')) {

                $model->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }

            if ($request->filled('ended_at')) {
                $model->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }

            return $datatable = \datatables()->eloquent($model)
                ->blacklist(['pass'])
                ->editColumn('params', function ($row) {
                    if (!empty($row->roblox_bot_item)){
                        $units = $row->roblox_bot_item;
                        $html = '';
                        foreach ($units??[] as $unit){
                            $html.= '<b>'.$unit->title.'</b> - '.$unit->quantity;
                            $html.= '<br>';
                        }

                        return $html;
                    }
                    return '';
                })
                ->editColumn('updated_at', function ($row) {
                    return date('d/m/Y H:i:s', strtotime($row->updated_at));
                })
                ->addColumn('action', function ($row) {
                    $temp = "<a href=\"" . route('admin.roblox-gem-info-bot.edit', $row->id) . "\"  rel=\"$row->id\" class=\"m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp .= "<a  rel=\"$row->id\" class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->toJson();
        }

        return view('admin.toolgame.roblox-gem.infobot.index')
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    /**
     * Show the form for creating a new newscategory
     *
     * @return Response
     */
    public function create()
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];

        return view('admin.toolgame.roblox-gem.infobot.create_edit')
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
        //return $request->all();
        $this->validate($request, [
            'ver' => 'required',
            'acc' => 'required',
        ], [
            'acc.required' => __('Vui lòng nhập tiêu đề'),
            'ver.required' => __('Vui lòng nhập máy chú'),
        ]);

        $input=$request->all();

        $units = $request->get('units');

        $input['params'] = json_encode($units, JSON_UNESCAPED_UNICODE);
        $input['type_order'] = 2;

        $item = Roblox_Bot::create($input);

        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Thêm mới thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
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

        $bot = \App\Models\Roblox_Bot::query()->where('type_order',2)->where('id',$id)->where('status',1)->first();
        if (isset($bot)){
            $cookies = $bot->cookies;
            $user_id = $bot->uid;
            $username = $bot->acc;

            $refulft = RobloxGateV2::GetTransactions($user_id,$cookies);

            return $refulft;

            return view('admin.toolgame.roblox.infobot.show', compact('datatable'));
        }

        //$datatable = Roblox_Bot::findOrFail($id);
        //return view('admin.toolgame.roblox.infobot.show', compact('datatable'));
    }

    /**
     * Show the form for editing the specified newscategory.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];


        $data = Roblox_Bot::query()->with('roblox_bot_item')->findOrFail($id);

        return view('admin.toolgame.roblox-gem.infobot.create_edit', compact('data'))
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
        $this->validate($request, [
            'ver' => 'required',
            'acc' => 'required',
        ], [
            'acc.required' => __('Vui lòng nhập tiêu đề'),
            'ver.required' => __('Vui lòng nhập máy chú'),
        ]);

        $input=$request->all();

        $units['units'] = $request->get('units');
        $input['params'] = json_encode($units, JSON_UNESCAPED_UNICODE);
        $input['type_order'] = 2;

        $item = Roblox_Bot::findOrFail($id);

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
        Roblox_Bot::query()->whereIn('id',$input)->delete();
        ActivityLog::add($request, 'Xóa thành công '.$this->module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Xóa thành công !'));
    }


    public function addUnits(Request $request,$id){

        $data = Roblox_Bot::query()
            ->where('id',$id)
            ->first();

        if (!isset($data)){
            return redirect()->back()->withErrors('Không tìm thấy thông tin bot');
        }

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới units")
        ];

        return view('admin.toolgame.roblox-gem.infobot.add-units', compact('data'))
            ->with('page_breadcrumbs', $this->page_breadcrumbs);

    }

    public function storeUnits(Request $request){

        $validator = Validator::make($request->all(), [
            'bot_id' => 'required',
            'title' => 'required',
            'type_item' => 'required',
            'quantity' => 'required',
        ],[
            'bot_id.required' => __('Chưa truyền id bot'),
            'title.required' => __('Vui lòng nhập tên vật phẩm'),
            'type_item.required' => __('Vui lòng chọn loại vật phẩm'),
            'quantity.required' => __('Vui lòng nhập số lượng'),
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 0
            ],422);
        }

        $bot_id = $request->get('bot_id');
        $title = $request->get('title');
        $type_item = $request->get('type_item');
        $quantity = $request->get('quantity');

        if ($quantity < 0){
            return redirect()->back()->withErrors('Số lượng lớn hơn hoặc bằng 0');
        }

        if ($type_item == 2){
            $check_roblox_bot_item = Roblox_Bot_Item::query()
                ->where('bot_id',$bot_id)
                ->where('type_item',2)
                ->first();
            if (isset($check_roblox_bot_item)){
                return redirect()->back()->withErrors('Units gem không được cấu hình quá 1');
            }
        }

        $input = $request->all();

        $data = Roblox_Bot_Item::create($input);

        ActivityLog::add($request, 'Tạo mới thành công units #' . $data->id);

        return redirect()->back()->with('success', __('Thêm mới thành công !'));
    }

    public function editUnits(Request $request,$id,$id_bot){

        $data = Roblox_Bot::query()
            ->where('id',$id)
            ->first();

        if (!isset($data)){
            return redirect()->back()->withErrors('Không tìm thấy thông tin bot');
        }

        $item = Roblox_Bot_Item::query()->where('id',$id_bot)->first();

        if (!isset($item)){
            return redirect()->back()->withErrors('Không tìm thấy thông tin unit');
        }

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Edit units")
        ];

        return view('admin.toolgame.roblox-gem.infobot.add-units', compact('data'))
            ->with('item', $item)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);

    }

    public function updateUnits(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'type_item' => 'required',
        ],[
            'title.required' => __('Vui lòng nhập tên vật phẩm'),
            'type_item.required' => __('Vui lòng chọn loại vật phẩm'),
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 0
            ],422);
        }

        $item = Roblox_Bot_Item::query()->where('id',$id)->first();

        if (!isset($item)){
            return redirect()->back()->withErrors('Không tìm thấy units');
        }

        $type_item = $request->get('type_item');
        if ($type_item == 2){
            $check_roblox_bot_item = Roblox_Bot_Item::query()
                ->where('bot_id',$item->bot_id)
                ->where('type_item',2)
                ->first();
            if (isset($check_roblox_bot_item)){
                return redirect()->back()->withErrors('Units gem không được cấu hình quá 1');
            }
        }

        $input = $request->all();

        $item->update($input);

        ActivityLog::add($request, 'Cập nhật thành công units #' . $item->id);

        return redirect()->back()->with('success', __('Thêm mới thành công !'));
    }

    public function updatePushQuantity(Request $request){

        $validator = Validator::make($request->all(), [
            'quantity_modal' => 'required|numeric',
            'id' => 'required',
        ],[
            'quantity_modal.required' => __('Vui lòng nhập số lượng vp'),
            'quantity_modal.numeric' => __('Số lượng phải là số'),
            'id.required' => __('Vui lòng chọn loại vật phẩm'),
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 0
            ],422);
        }

        $id = $request->get('id');

        $data = Roblox_Bot_Item::query()->where('id',$id)->first();

        $quantity_modal = $request->get('quantity_modal');

        if ($quantity_modal < 0){
            return redirect()->back()->withErrors('Vui lòng nhập số lượng lớn hơn 0');
        }

        $quantity_modal = $request->get('quantity_modal');

        $quantity = 0;
        if (isset($data->quantity)){
            $quantity = $data->quantity;
        }

        $data->quantity = $quantity + $quantity_modal;
        $data->save();

        ActivityLog::add($request, 'Cộng số lượng vật phẩm thành công units #' . $data->id);

        return redirect()->back()->with('success', __('Cập nhật số lượng thành công !'));
    }

    public function updateMinusQuantity(Request $request){

        $validator = Validator::make($request->all(), [
            'quantity_modal' => 'required|numeric',
            'id' => 'required',
        ],[
            'quantity_modal.required' => __('Vui lòng nhập số lượng vp'),
            'quantity_modal.numeric' => __('Số lượng phải là số'),
            'id.required' => __('Vui lòng chọn loại vật phẩm'),
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 0
            ],422);
        }

        $id = $request->get('id');

        $data = Roblox_Bot_Item::query()->where('id',$id)->first();

        $quantity_modal = $request->get('quantity_modal');

        if ($quantity_modal < 0){
            return redirect()->back()->withErrors('Vui lòng nhập số lượng lớn hơn 0');
        }

        $quantity_modal = $request->get('quantity_modal');

        $quantity = 0;
        if (isset($data->quantity)){
            $quantity = $data->quantity;
        }

        $total = $quantity - $quantity_modal;
        if ($total < 0){
            return redirect()->back()->withErrors('Số lượng trừ vật phẩm lớn hơn số lượng vật phẩm hiện có');
        }

        $data->quantity = $total;
        $data->save();

        ActivityLog::add($request, 'Trừ số lượng vật phẩm thành công units #' . $data->id);

        return redirect()->back()->with('success', __('Cập nhật số lượng thành công !'));
    }

    public function updateStatusQuantity(Request $request){

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ],[
            'id.required' => __('Vui lòng chọn loại vật phẩm'),
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 0
            ],422);
        }

        $id = $request->get('id');

        $data = Roblox_Bot_Item::query()->where('id',$id)->first();

        if ($data->status == 1) {
            $data->status = 0;
        } elseif ($data->status == 0) {
            $data->status = 1;
        }
        $data->save();

        ActivityLog::add($request, 'Chuyển trạng thái units #' . $data->id);
        return response()->json([
            'message' => __("Chuyển trạng thái thành công"),
            'status' => 1
        ]);
    }
}
