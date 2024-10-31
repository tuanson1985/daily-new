<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 20/12/2018
 * Time: 14:43 CH
 */


namespace App\Http\Controllers\Admin\ToolGame\Roblox;
use App\Http\Controllers\Controller;
use App\Library\ChargeGameGateway\RobloxGate;
use App\Library\ChargeGameGateway\RobloxGateV2;
use App\Library\DirectAPI;
use App\Library\Helpers;
use App\Models\ActivityLog;
use App\Models\Bot;
use App\Models\Roblox_Bot;
use App\Models\Roblox_Bot_San;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class InfoBotSanController extends Controller
{
    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;
    public function __construct()
    {


        $this->module='roblox-info-bot-san';

        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');
        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);


        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.roblox-info-bot-san.index'),
                'title' => __('Bán roblox - BOT SAN')
            ];
        }
    }



    public function index(Request $request)
    {

        if ($request->ajax()) {

            $model = Roblox_Bot_San::query()
                ->orderByRaw('CASE WHEN status = 1 THEN 0 ELSE 1 END'); // Ưu tiên status = 1;
            if ($request->filled('id')) {
                $model->where('id', 'LIKE', '%' . $request->get('id') . '%');
                $model->orWhere('idkey', 'LIKE', '%' . $request->get('id') . '%');
            }
            if ($request->filled('acc')) {
                $model->where('acc', 'LIKE', '%' . $request->get('acc') . '%');
            }

            if ($request->filled('status')) {
                $model->where('status', $request->get('status'));
            }
            if ($request->filled('started_at')) {
                $model->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $model->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }

            return $datatable = \datatables()->eloquent($model)
                ->blacklist(['password'])
                ->editColumn('updated_at', function ($row) {
                    return date('d/m/Y H:i:s', strtotime($row->updated_at));
                })
                ->editColumn('price', function ($row) {
                    return number_format($row->price);
                })
                ->editColumn('coin', function ($row) {
                    return number_format($row->coin);
                })
                ->editColumn('get_nick_detail', function ($row) {
                    $html_get_nick_detail = "";
                    if (isset($row->params)){
                        $params = json_decode($row->params);
                        if (isset($params->get_nick_detail) && count($params->get_nick_detail)){
                            $get_nick_details = $params->get_nick_detail;
// Định dạng theo kiểu `Y-m-d H:i:s`:

                            foreach ($get_nick_details??[] as $index => $get_nick_detail){
                                $c_index = $index + 1;
                                $html_get_nick_detail .= '<b>Lần thứ: '.$c_index.'</b>';
                                $html_get_nick_detail .= '<br>';
                                $html_get_nick_detail .= '  Thời gian: '.date('d/m/Y H:i:s', strtotime($get_nick_detail->time))." - Robux: ".$get_nick_detail->coin;
                                $html_get_nick_detail .= '<br>';
                                $html_get_nick_detail .= '  Robux: '.$get_nick_detail->coin;
                                $html_get_nick_detail .= '<br>';
                            }
                        }
                    }
                    return $html_get_nick_detail;
                })
                ->editColumn('defragment_type', function ($row) {
                    $html_defragment_type = "";
                    if (isset($row->params)){
                        $params = json_decode($row->params);
                        if (isset($params->defragment_type) && count($params->defragment_type)){
                            $defragment_types = $params->defragment_type;
// Định dạng theo kiểu `Y-m-d H:i:s`:
                            foreach ($defragment_types??[] as $index => $defragment_type){
                                $c_index = $index + 1;
                                $html_defragment_type .= '<b>Lần thứ: '.$c_index.'</b>';
                                $html_defragment_type .= '<br>';
                                $html_defragment_type .= '  Thời gian: '.date('d/m/Y H:i:s', strtotime($defragment_type->time));
                                $html_defragment_type .= '<br>';
                                $html_defragment_type .= '  Robux: '.$defragment_type->coin;
                                $html_defragment_type .= '<br>';
                                $type = "Chuyển về 120H";
                                if (isset($defragment_type->defragment_type) && $defragment_type->defragment_type == 2){
                                    $type = "Dồn nick";
                                }
                                $html_defragment_type .= '  Defragment: '.$type;
                                $html_defragment_type .= '<br>';
                            }
                        }
                    }
                    return $html_defragment_type;
                })
                ->editColumn('defragment', function ($row) {
                    $html_defragment = "";
                    if (isset($row->params)){
                        $params = json_decode($row->params);
                        if (isset($params->defragment) && count($params->defragment)){
                            $defragments = $params->defragment;
// Định dạng theo kiểu `Y-m-d H:i:s`:
                            foreach ($defragments??[] as $index => $defragment){
                                $c_index = $index + 1;
                                $html_defragment .= '<b>Lần thứ: '.$c_index.'</b>';
                                $html_defragment .= '<br>';
                                $html_defragment .= '  Thời gian: '.date('d/m/Y H:i:s', strtotime($defragment->time));
                                $html_defragment .= '<br>';
                                $html_defragment .= '  Robux: '.$defragment->coin;
                                $html_defragment .= '<br>';
                            }
                        }
                    }
                    return $html_defragment;
                })
                ->addColumn('action', function ($row) {
                    $temp = "<a href=\"" . route('admin.roblox-info-bot-san.edit', $row->id) . "\"  rel=\"$row->id\" class=\"m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp .= "<a  rel=\"$row->id\" class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->rawColumns(['action','get_nick_detail','defragment_type','defragment'])
                ->toJson();

        }

        return view('admin.toolgame.roblox.infobotsan.index')
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


        return view('admin.toolgame.roblox.infobotsan.create_edit')
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

        $this->validate($request, [
            'acc' => 'required|unique:roblox_bot_san,acc',
            'rate' => 'required|numeric|min:0.0001',
        ], [
            'acc.required' => __('Vui lòng nhập tài khoản'),
            'acc.unique' => __('Tài khoản này đã tồn tại'),
            'rate.required' => __('Vui lòng nhập tỷ giá'),
            'rate.min' => __('Tỷ giá lớn hơn 0'),
        ]);

        $input = $request->all();
        //check cookie và số dư của bot
        if (!$request->filled('cookies')){
            return redirect()->back()->withErrors(__('Vui lòng nhập cookies của bot'))->withInput();
        }

        if (!$request->filled('password')){
            return redirect()->back()->withErrors(__('Vui lòng nhập mật khẩu của bot'))->withInput();
        }

//        $input['password'] = Helpers::Encrypt($request->get('password'), config('roblox_bot_san.encrypt_bot'));

        if (!$request->filled('rate')){
            return redirect()->back()->withErrors(__('Vui lòng nhập tỷ giá của bot'))->withInput();
        }

        if (!$request->filled('id_pengiriman')){
            return redirect()->back()->withErrors(__('Vui lòng nhập số tiền bot'))->withInput();
        }

        $data = Roblox_Bot_San::query()->where('id_pengiriman',$request->get('id_pengiriman'))->first();

        if (isset($data)){
            return redirect()->back()->withErrors(__('Đã tồn tại ID đơn hàng'))->withInput();
        }

        $url = '/check-balance';
        $method = "POST";
        $dataSend = array();
        $secretkey = config('proxy.sign');
        $dataSend['cookies'] = $request->get('cookies'); // cookies
        $sign = Helpers::encryptProxy($dataSend,$secretkey);
        $dataSend['sign'] = $sign; // refund_id
        $result = DirectAPI::_checkBalanceRoblox($url,$dataSend,$method);
        if (!isset($result)){
            return redirect()->back()->withErrors(__('Không thể kết nối với server roblox để check bot live'))->withInput();
        }

        if (!isset($result->status)){
            return redirect()->back()->withErrors(__('Không thể kết nối với server roblox để check bot live'))->withInput();
        }

        if ($result->status != 1){
            return redirect()->back()->withErrors(__('Bot cookies không đúng hoặc chưa đăng nhập'))->withInput();
        }



        $input['coin'] = $result->balance??0;

        $item = Roblox_Bot_San::create($input);
        $message='Có 1 tài khoản robux mới đơn giá <b>'.$item->rate.' - '.number_format($item->coin).'</b> Robux';
        Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_bot_add_roblox'));

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

        $bot = \App\Models\Roblox_Bot::query()->where('type_order',1)->where('id',$id)->where('status',1)->first();
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

        $data = Roblox_Bot_San::findOrFail($id);
        return view('admin.toolgame.roblox.infobotsan.create_edit', compact('data'))
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

        $item = Roblox_Bot_San::findOrFail($id);

        $this->validate($request, [
            'acc' => [
                'required',
                Rule::unique('roblox_bot_san', 'acc')->ignore($item->id), // $robloxBot là đối tượng bản ghi hiện tại
            ],
            'id_pengiriman' => [
                'required',
                Rule::unique('roblox_bot_san', 'id_pengiriman')->ignore($item->id), // $robloxBot là đối tượng bản ghi hiện tại
            ],
            'rate' => 'required|numeric|min:0.0001',
        ], [
            'acc.required' => __('Vui lòng nhập tài khoản'),
            'acc.unique' => __('Tài khoản này đã tồn tại'),
            'id_pengiriman.required' => __('Vui lòng nhập tài khoản'),
            'id_pengiriman.unique' => __('Tài khoản này đã tồn tại'),
            'rate.required' => __('Vui lòng nhập tỷ giá'),
            'rate.min' => __('Tỷ giá lớn hơn 0'),
        ]);

        $input = $request->all();

        //check cookie và số dư của bot
        if (!$request->filled('cookies')){
            return redirect()->back()->withErrors(__('Vui lòng nhập cookies của bot'))->withInput();
        }

        if (!$request->filled('password')){
            return redirect()->back()->withErrors(__('Vui lòng nhập mật khẩu của bot'))->withInput();
        }

//        $input['password'] = Helpers::Encrypt($request->get('password'), config('roblox_bot_san.encrypt_bot'));

        if (!$request->filled('rate')){
            return redirect()->back()->withErrors(__('Vui lòng nhập tỷ giá của bot'))->withInput();
        }

        if (!$request->filled('id_pengiriman')){
            return redirect()->back()->withErrors(__('Vui lòng nhập số tiền bot'))->withInput();
        }

        $data = Roblox_Bot_San::query()->where('id','!=',$item->id)->where('id_pengiriman',$request->get('id_pengiriman'))->first();

        if (isset($data)){
            return redirect()->back()->withErrors(__('Đã tồn tại ID đơn hàng'))->withInput();
        }

        $url = '/check-balance';
        $method = "POST";
        $dataSend = array();
        $dataSend['cookies'] = $request->get('cookies'); // cookies
        $secretkey = config('proxy.sign');
        $sign = Helpers::encryptProxy($dataSend,$secretkey);
        $dataSend['sign'] = $sign; // refund_id
        $result = DirectAPI::_checkBalanceRoblox($url,$dataSend,$method);
        if (!isset($result)){
            return redirect()->back()->withErrors(__('Không thể kết nối với server roblox để check bot live'))->withInput();
        }

        if (!isset($result->status)){
            return redirect()->back()->withErrors(__('Không thể kết nối với server roblox để check bot live'))->withInput();
        }

        if ($result->status != 1){
            return redirect()->back()->withErrors(__('Bot cookies không đúng hoặc chưa đăng nhập'))->withInput();
        }

        if (isset($result->balance)){
            $input['coin'] = $result->balance??0;

        }

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

        foreach ($input??[] as $idOrderNeedRecharge){
            $roblox_bot = Roblox_Bot_San::query()->where('id',$idOrderNeedRecharge)->first();
            if (isset($roblox_bot)){
                $roblox_bot->status = 0;
                $roblox_bot->save();
            }
        }

        ActivityLog::add($request, 'Xóa thành công '.$this->module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Xóa thành công !'));
    }


}
