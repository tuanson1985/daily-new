<?php

namespace App\Http\Controllers\Admin\GifCode;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use Carbon\Carbon;
use App\Models\ActivityLog;

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

        $this->module='gift-code';
        $this->moduleCategory=null;
        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');
        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate']]);
        $this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);



        $this->page_breadcrumbs[] = [
            'page' => route('admin.'.$this->module.'.index'),
            'title' => __(config('module.'.$this->module.'.title'))
        ];
    }

    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);
        if($request->ajax) {
            $datatable= Voucher::withCount('voucher_user');
            if ($request->filled('group_id')) {
                $datatable->whereHas('groups', function ($query) use ($request) {
                    $query->where('group_id',$request->get('group_id'));
                });
            }

            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', 'LIKE', '%' . $request->get('id') . '%');
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
            if ($request->filled('code')) {
                $datatable->where('code',$request->get('code') );
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
                    'code',
                    'status',
                    'created_at',
                    'started_at',
                    'type',
                    'ended_at',
                    'effect',
                    'status_effect',
                    'voucher_user_count',
                    'rest_user',
                    'max_uses',
                    'action'
                ])


                ->editColumn('type', function($data) {
                    return config('module.gift-code.type.'.$data->type);
                })
                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->editColumn('started_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->started_at));
                })
                ->editColumn('ended_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->ended_at));
                })
                ->addColumn('effect', function($row) {
                    $temp = "";
                    $time = Carbon::now();
                    if(strtotime($time) < strtotime($row->started_at)){
                        $temp = "Chưa có hiệu lực";
                    }
                    else if(strtotime($time) > strtotime($row->started_at) && strtotime($time) < strtotime($row->ended_at)){
                        $temp = "Đang có hiệu lực";
                    }
                    else if(strtotime($time) > strtotime($row->ended_at)){
                        $temp = "Đã hết hiệu lực";
                    }
                    return $temp;
                })
                ->addColumn('rest_user', function($row) {
                    $temp = "";
                    $temp = $row->max_uses - $row->voucher_user_count;
                    return $temp;
                })
                ->addColumn('status_effect', function($row) {
                    $temp = "";
                    $time = Carbon::now();
                    if(strtotime($time) < strtotime($row->started_at)){
                        $temp = 2;
                    }
                    else if(strtotime($time) > strtotime($row->started_at) && strtotime($time) < strtotime($row->ended_at)){
                        $temp = 1;
                    }
                    else if(strtotime($time) > strtotime($row->ended_at)){
                        $temp = 0;
                    }
                    return $temp;
                })
                ->addColumn('action', function($row) {
                    $temp= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->toJson();
        }


        return view('admin.'.$this->module.'.item.index')
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

        if( $this->moduleCategory==null){
            $dataCategory=null;
        }
        else{
            //$dataCategory = Group::where('module', '=',  $this->moduleCategory)->orderBy('order','asc')->get();
        }

        $dataCategory = null;
        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.'.$this->module.'.item.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('dataCategory', $dataCategory);
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
            // 'code' => 'required|unique:voucher,code',
            'code' => 'required',
            'max_uses' => 'required',
            'max_uses_user' => 'required',
            'type' => 'required',
            'started_at' => 'required',
            'ended_at' => 'required',
            'status' => 'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
            'code.required' => __('Vui lòng nhập mã code'),
            // 'code.unique' => __('Mã code đã tồn tại'),
            'max_uses.required' => __('Vui lòng nhập tổng số người sử dụng'),
            'max_uses_user.required' => __('Vui lòng nhập số lần người dùng sử dụng trong 1 giftcode'),
            'type.required' => __('Vui lòng nhập loại giftcode'),
            'started_at.required' => __('Vui lòng nhập ngày bắt đầu'),
            'ended_at.required' => __('Vui lòng nhập ngày kết thúc'),
            'status.required' => __('Vui lòng nhập trạng thái'),
        ]);
        // mã code được nhập
        $code = $request->code;
        // tìm kiếm xem mã code đã được sử dụng hay chưa và còn đang hoạt động hay không
        $code_item = Voucher::where('code',$code)->where('status',1)->where('ended_at','>', Carbon::now())->first();
        if($code_item){
            return redirect()->back()->withErrors(__('Mã code này đang trong thời gian hoạt động có hiệu lực'));
        }

        $input = [
            'title' => $request->title,
            'code' => $request->code,
            'max_uses' => $request->max_uses,
            'max_uses_user' => $request->max_uses_user,
            'type' => $request->type,
            'started_at' => Carbon::createFromFormat('d/m/Y H:i:s', $request->started_at),
            'ended_at' => Carbon::createFromFormat('d/m/Y H:i:s', $request->ended_at),
            'status' => $request->status,
        ];
        $params = null;

        if($request->type == 1){
            $user_created_at = null;
            $params_percent = $request->params_percent;
            $params_amount = $request->params_amount;
            for($i = 0; $i < count($params_percent); $i++){
                if($params_percent[$i] == "" || (int)$params_percent[$i] < 0 || (int)$params_percent[$i] > 100 || $params_amount == "" || (int)$params_amount[$i] < 0 ){
                    return redirect()->back()->withErrors(__('Thông số cầu hình nhận thưởng không hợp lệ, vui lòng kiểm tra lại'));
                }
                $gift [] = [
                    'percent' => $params_percent[$i],
                    'amount' => $params_amount[$i],
                ];
            }
            $params['gift'] = $gift;
            if($request->user_created_at){
                $user_created_at = Carbon::createFromFormat('d/m/Y H:i:s', $request->user_created_at)->toDateTimeString();
            }
            $params['user_created_at'] = $user_created_at;
            $params = json_decode(json_encode($params), FALSE);
        }
        else if($request->type == 2){
            if($request->ratio_booking == "" || $request->ratio_booking == null || (int)$request->ratio_booking < 0 || (int)$request->ratio_booking > 100){
                return redirect()->back()->withErrors(__('Thông số cầu hình nhận thưởng cho % giảm giá không hợp lệ, vui lòng kiểm tra lại'));
            }
            if($request->amount_reduction_max == "" || $request->amount_reduction_max == null || (int)$request->amount_reduction_max < 0){
                return redirect()->back()->withErrors(__('Thông số cầu hình nhận thưởng cho số tiền giảm tối đa không hợp lệ, vui lòng kiểm tra lại'));
            }
            $params = [
                'ratio_booking' => (int)$request->ratio_booking,
                'amount_reduction_max' => (int)$request->amount_reduction_max,
            ];
            $params = json_decode(json_encode($params), FALSE);
        }
        $params = json_encode($params,JSON_UNESCAPED_UNICODE);
        $input['params'] = $params;

        $data=Voucher::create($input);

        ActivityLog::add($request, 'Tạo mới thành công '.$this->module.' #'.$data->id);
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
        $data = Voucher::findOrFail($id);
        if($data->status == 0){
            return redirect()->back()->withErrors(__('Mã code đã ở trạng thái ngừng hoạt động, không thể sửa'));
        }
        $params = $data->params;
        $params = json_decode($params);
        $gift = null;
        $user_created_at = null;
        if($data->type == 1){
            $type = 1;
            $gift = $params->gift;
            $user_created_at = $params->user_created_at;
        }
        else if($data->type == 2){
           $type = 2;
           $gift = $params;
        }
        if( $this->moduleCategory==null){
            $dataCategory=null;
        }
        else{
            //$dataCategory = Group::where('module', '=',  $this->moduleCategory)->orderBy('order','asc')->get();
        }
        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        return view('admin.'.$this->module.'.item.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('dataCategory', $dataCategory)
            ->with('params', $params)
            ->with('gift', $gift)
            ->with('type', $type)
            ->with('user_created_at', $user_created_at);
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
            'title'=>'required',
            'max_uses' => 'required',
            'max_uses_user' => 'required',
            'type' => 'required',
            'started_at' => 'required',
            'ended_at' => 'required',
            'status' => 'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
            'max_uses.required' => __('Vui lòng nhập tổng số người sử dụng'),
            'max_uses_user.required' => __('Vui lòng nhập số lần người dùng sử dụng trong 1 giftcode'),
            'type.required' => __('Vui lòng nhập loại giftcode'),
            'started_at.required' => __('Vui lòng nhập ngày bắt đầu'),
            'ended_at.required' => __('Vui lòng nhập ngày kết thúc'),
            'status.required' => __('Vui lòng nhập trạng thái'),
        ]);
        $data =  Voucher::findOrFail($id);
        $input = [
            'title' => $request->title,
            'max_uses' => $request->max_uses,
            'max_uses_user' => $request->max_uses_user,
            'type' => $request->type,
            'started_at' => Carbon::createFromFormat('d/m/Y H:i:s', $request->started_at),
            'ended_at' => Carbon::createFromFormat('d/m/Y H:i:s', $request->ended_at),
            'status' => $request->status,
        ];
        $now = Carbon::now();
        if(strtotime($now) > strtotime($data->started_at) && strtotime($now) < strtotime($data->ended_at)){
            return redirect()->back()->withErrors(__('Gift code đang trong quá trình có hiệu lực, không thể sửa !'));
        }
        else if(strtotime($now) > strtotime($data->ended_at)){
            return redirect()->back()->withErrors(__('Gift code đã hết hiệu lực, không thể sửa !'));
        }
        $params = null;
        if($request->type == 1){
            $user_created_at = null;
            $params_percent = $request->params_percent;
            $params_amount = $request->params_amount;
            for($i = 0; $i < count($params_percent); $i++){
                if($params_percent[$i] == "" || (int)$params_percent[$i] < 0 || (int)$params_percent[$i] > 100 || $params_amount == "" || (int)$params_amount[$i] < 0 ){
                    return redirect()->back()->withErrors(__('Thông số cầu hình nhận thưởng không hợp lệ, vui lòng kiểm tra lại'));
                }
                $gift [] = [
                    'percent' => $params_percent[$i],
                    'amount' => $params_amount[$i],
                ];
            }
            $params['gift'] = $gift;
            if($request->user_created_at){
                $user_created_at = Carbon::createFromFormat('d/m/Y H:i:s', $request->user_created_at)->toDateTimeString();
            }
            $params['user_created_at'] = $user_created_at;
            $params = json_decode(json_encode($params), FALSE);
        }
        else if($request->type == 2){
            if($request->ratio_booking == "" || $request->ratio_booking == null || (int)$request->ratio_booking < 0 || (int)$request->ratio_booking > 100){
                return redirect()->back()->withErrors(__('Thông số cầu hình nhận thưởng cho % giảm giá không hợp lệ, vui lòng kiểm tra lại'));
            }
            if($request->amount_reduction_max == "" || $request->amount_reduction_max == null || (int)$request->amount_reduction_max < 0){
                return redirect()->back()->withErrors(__('Thông số cầu hình nhận thưởng cho số tiền giảm tối đa không hợp lệ, vui lòng kiểm tra lại'));
            }
            $params = [
                'ratio_booking' => (int)$request->ratio_booking,
                'amount_reduction_max' => (int)$request->amount_reduction_max,
            ];
            $params = json_decode(json_encode($params), FALSE);
        }
        $params = json_encode($params,JSON_UNESCAPED_UNICODE);
        $input['params'] = $params;
        $data->update($input);
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
        $data =  Voucher::where("id",$input)->first();
        $data->status = 0;
        $data->save();
        ActivityLog::add($request, 'Xóa thành công '.$this->module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Xóa thành công !'));
    }
}
