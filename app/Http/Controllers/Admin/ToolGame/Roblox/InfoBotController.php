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
use App\Models\ActivityLog;
use App\Models\Bot;
use App\Models\Roblox_Bot;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class InfoBotController extends Controller
{
    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;
    public function __construct()
    {



        $this->module='roblox-info-bot';

        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');
        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);


        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.roblox-info-bot.index'),
                'title' => __('Bán roblox - Bot')
            ];
        }
    }



    public function index(Request $request)
    {

        if ($request->ajax()) {

            $model = Roblox_Bot::with('shop')
                ->orderByRaw('CASE WHEN status = 1 THEN 0 ELSE 1 END') // Ưu tiên status = 1
                ->where('type_order',1);
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
                ->blacklist(['pass'])
                ->editColumn('updated_at', function ($row) {
                    return date('d/m/Y H:i:s', strtotime($row->updated_at));
                })
                ->addColumn('action', function ($row) {
                    $temp = "<a href=\"" . route('admin.roblox-info-bot.edit', $row->id) . "\"  rel=\"$row->id\" class=\"m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    if ($row->status == 1){
                        $temp .= "<a href=\"" . route('admin.roblox-info-bot.show', $row->id) . "\"  rel=\"$row->id\" class=\"m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill \" title=\"Show\"><i class=\"la la-eye\"></i></a>";
                    }
                    $temp .= "<a  rel=\"$row->id\" class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
//                ->editColumn('defragment', function ($row) {
//                    $html_defragment = "";
//                    if (isset($row->params)){
//                        $params = json_decode($row->params);
//                        if (isset($params->defragment) && count($params->defragment)){
//                            $defragments = $params->defragment;
//// Định dạng theo kiểu `Y-m-d H:i:s`:
//                            foreach ($defragments??[] as $index => $defragment){
//                                $c_index = $index + 1;
//                                $html_defragment .= '<b>Lần thứ: '.$c_index.'</b>';
//                                $html_defragment .= '<br>';
//                                $html_defragment .= '  Thời gian: '.date('d/m/Y H:i:s', strtotime($defragment->time));
//                                $html_defragment .= '<br>';
//                                $html_defragment .= '  Robux: '.$defragment->coin;
//                                $html_defragment .= '<br>';
//                            }
//                        }
//                    }
//                    return $html_defragment;
//                })
                ->editColumn('defragment', function ($row) {
                    $last_coin = 0;
                    if (isset($row->params)){
                        $params = json_decode($row->params);

                        if (isset($params->defragment) && count($params->defragment)){
                            $defragments = $params->defragment;
// Định dạng theo kiểu `Y-m-d H:i:s`:
                            foreach ($defragments??[] as $index => $defragment){
                                if ($last_coin == 0){
                                    $last_coin = (int)$defragment->last_coin;
                                }else{
                                    if ($last_coin < (int)$defragment->last_coin){
                                        $last_coin = (int)$defragment->last_coin;
                                    }
                                }
                            }
                        }
                    }
                    return $last_coin;
                })
                ->rawColumns(['action','defragment'])
                ->toJson();

        }

        return view('admin.toolgame.roblox.infobot.index')
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


        return view('admin.toolgame.roblox.infobot.create_edit')
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
            'acc' => 'required|unique:roblox_bot,acc',
        ], [
            'acc.required' => __('Vui lòng nhập tài khoản'),
            'acc.unique' => __('Tài khoản này đã tồn tại'),
        ]);

        $input=$request->all();

        if ($request->get('account_type') && $request->get('account_type') == 1){
            $input['shop_id'] = null;
            $input['status'] = '6';
        }else{
            $input['shop_id'] = session('shop_id');
        }

//        $check_bot = Roblox_Bot::query()->where('acc',)->first();


        //check cookie và số dư của bot
        if($request->get('cookies')!="" ){
            $result=RobloxGate::checkLiveAndBalanceBot($request->get('cookies'));
            if($result && $result->status){
                if($result->status==1){
                    $input['coin']=$result->balance;
                    $result_userid = RobloxGate::detectUserIdRoblox($request->get('acc'),null,$request->get('cookies'));
                    if($result_userid && $result_userid->status) {
                        if ($result_userid->status == 1) {
                            $input['uid']=$result_userid->user_id;
                        }
                    }
                }
                else{
                    return redirect()->back()->withErrors(__('Bot cookies không đúng hoặc chưa đăng nhập'))->withInput();
                }
            }
            else{
                return redirect()->back()->withErrors(__('Không thể kết nối với server roblox để check bot live'))->withInput();
            }
        }
        else{
            return redirect()->back()->withErrors(__('Vui lòng nhập cookies của bot'))->withInput();

        }


        $input['type_order'] = 1;
        $item=Roblox_Bot::create($input);


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


        $data = Roblox_Bot::findOrFail($id);
        return view('admin.toolgame.roblox.infobot.create_edit', compact('data'))
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

        $item = Roblox_Bot::findOrFail($id);

        $this->validate($request, [
            'acc' => [
                'required',
                Rule::unique('roblox_bot', 'acc')->ignore($item->id), // $robloxBot là đối tượng bản ghi hiện tại
            ],
        ], [
            'acc.required' => __('Vui lòng nhập tài khoản'),
            'acc.unique' => __('Tài khoản này đã tồn tại'),
        ]);

        if($request->pass=="" || $request->pass==null){
            $input = $request->except('pass');
        }else{
            $input = $request->all();
        }

        if ($request->get('account_type') && $request->get('account_type') == 1){
            $input['status'] = '6';
        }

        //check cookie và số dư của bot
        if($request->get('cookies')!="" ){

            $result=RobloxGate::checkLiveAndBalanceBot($request->get('cookies'));
            if($result && $result->status){
                if($result->status==1){
                    $input['coin']=$result->balance;
                    $result_userid = RobloxGate::detectUserIdRoblox($request->get('acc'),null,$request->get('cookies'));
                    if($result_userid && $result_userid->status) {
                        if ($result_userid->status == 1) {
                            $input['uid']=$result_userid->user_id;
                        }
                    }
                }
                else{
                    return redirect()->back()->withErrors(__('Bot cookies không đúng hoặc chưa đăng nhập'))->withInput();
                }
            }
            else{
                return redirect()->back()->withErrors(__('Không thể kết nối với server roblox để check bot live'))->withInput();;
            }
        }
        else{
            return redirect()->back()->withErrors(__('Vui lòng nhập cookies của bot'))->withInput();
        }
        $input['type_order'] = 1;

        unset($input["id_pengiriman"]);
        unset($input["acc"]);

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
            $roblox_bot = Roblox_Bot::query()->where('id',$idOrderNeedRecharge)->first();
            if (isset($roblox_bot)){
                $roblox_bot->status = 0;
                $roblox_bot->save();
            }
        }

        ActivityLog::add($request, 'Xóa thành công '.$this->module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Xóa thành công !'));
    }


}
