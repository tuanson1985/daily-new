<?php

namespace App\Http\Controllers\Admin\Transfer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\Order;
use App\Models\Txns;
use App\Models\User;
use Carbon\Carbon;
use Log;
use App\Models\Shop;
use DB;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportData;

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

        $this->module='transfer';
        $this->moduleCategory=null;
        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');
        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate']]);
        $this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);
        $this->middleware('permission:'. $this->module.'-update-order', ['only' => ['updateOrder']]);
        $this->middleware('permission:'. $this->module.'-report', ['only' => ['Report']]);
        $this->middleware('permission:'. $this->module.'-export', ['only' => ['exportExcel']]);



        $this->page_breadcrumbs[] = [
            'page' => route('admin.'.$this->module.'.index'),
            'title' => __(config('module.'.$this->module.'.title'))
        ];
    }

    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);
        $partner_key = config('module.transfer.partner_key');
        $partner_id = config('module.transfer.partner_id');
        if($request->ajax()){
            $datatable = Order::where('module','=',config('module.transfer.key'));
            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', 'LIKE', '%' . $request->get('id') . '%');
                });
            }
            if ($request->filled('status')) {
                $datatable->where('status',$request->get('status') );
            }

            if($request->filled('started_at') || $request->filled('ended_at')){
                if ($request->filled('started_at')) {

                    $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
                }
                if ($request->filled('ended_at')) {
                    $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
                }
            }else{
                $datatable->whereDate('created_at', Carbon::today())->get();
            }
            if ($request->filled('shop_id')) {
                $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                if(empty($shop_id_shop_access) || $shop_id_shop_access == 'all'){
                    $datatable->whereIn('shop_id', $request->get('shop_id'));
                }
                else{
                    $shop_id_shop_access_search = array_intersect($shop_id_shop_access,$request->get('shop_id'));
                    $datatable->whereIn('shop_id', $shop_id_shop_access_search);
                }
            }
            else{
                if(session('shop_id')){
                    $datatable->where('shop_id',session('shop_id'));
                }
                else{
                    if(isset(Auth::user()->shop_access) &&Auth::user()->shop_access !== "all"){
                        $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                        $datatable->whereIn('shop_id',$shop_id_shop_access);
                    }
                }
            }
            return \datatables()->eloquent($datatable)

            ->only([
                'shop_id',
                'bank_id',
                'id',
                'author_id',
                'request_id',
                'username',
                'price',
                'real_received_price',
                'ratio',
                'tranid',
                'status',
                'created_at',
                'action'
            ])


            ->editColumn('created_at', function($data) {
                return date('d/m/Y H:i:s', strtotime($data->created_at));
            })
            ->editColumn('shop_id', function($data) {
                $temp= '';
                if(isset($data->shop_id)){
                    if(isset($data->shop->domain)){
                        $temp .=  $data->shop->domain;
                    }
                }
                return $temp;
            })
            ->addColumn('username', function($row) {
                $username = $row->author->username;
                $temp = '';
                if(auth()->user()->hasRole('admin') || auth()->user()->can('view-profile')){
                    $temp .= "<a href=\"#\"  class=\"load-modal\" rel=\"".route('admin.view-profile',["username" => "$username","shop_id" => "$row->shop_id"])."\">".$username."</a>";
                }
                else{
                    $temp .= $row->username;
                }
                return $temp;
            })
            ->editColumn('price', function($data) {
                return number_format($data->price);
            })
            ->editColumn('real_received_price', function($data) {
                return number_format($data->real_received_price);
            })
            ->with('total_price',$datatable->sum('price'))
            ->with('total_real_received_price',$datatable->sum('real_received_price'))
            ->setTotalRecords($datatable->count())
            ->toJson();
        }
        $shop_access_user = Auth::user()->shop_access;
        $shop = Shop::orderBy('id','desc');
        if(isset($shop_access_user) && $shop_access_user !== "all"){
            $shop_access_user = json_decode($shop_access_user);
            $shop = $shop->whereIn('id',$shop_access_user);
        }
        // $shop = $shop->pluck('title','id')->toArray();
        $shop = $shop->get();
        return view('admin.transfer.item.index')
        ->with('module', $this->module)
        ->with('shop', $shop)
        ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }
    public function exportExcel(Request $request){
        ActivityLog::add($request, 'Xuất excel nạp tiền qua ATM tự động');
        $datatable = Order::where('module','=',config('module.transfer.key'))->with('author')->with('shop');
        if ($request->filled('id'))  {
            $datatable->where(function($q) use($request){
                $q->orWhere('id', 'LIKE', '%' . $request->get('id') . '%');
            });
        }
        if ($request->filled('status')) {
            $datatable->where('status',$request->get('status') );
        }
        if($request->filled('started_at') || $request->filled('ended_at')){
            if ($request->filled('started_at')) {
                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }
        }else{
            $datatable->whereDate('created_at', Carbon::today())->get();
        }
        if ($request->filled('shop_id')) {
            $shop_id_shop_access = json_decode(Auth::user()->shop_access);
            if(empty($shop_id_shop_access) || $shop_id_shop_access == 'all'){
                $datatable->whereIn('shop_id', $request->get('shop_id'));
            }
            else{
                $shop_id_shop_access_search = array_intersect($shop_id_shop_access,$request->get('shop_id'));
                $datatable->whereIn('shop_id', $shop_id_shop_access_search);
            }
        }
        else{
            if(session('shop_id')){
                $datatable->where('shop_id',session('shop_id'));
            }
            else{
                if(isset(Auth::user()->shop_access) &&Auth::user()->shop_access !== "all"){
                    $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                    $datatable->whereIn('shop_id',$shop_id_shop_access);
                }
            }
        }
        $datatable = $datatable->get();
        $data = [
            'data' => $datatable,
        ];
        return Excel::download(new ExportData($data,view('admin.transfer.report.export_excel')), 'Thống kê nạp ATM tự động ' . time() . '.xlsx');
          
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data =  Order::where('module','=',config('module.transfer.key'))->findOrFail($id);
        return view('admin.transfer.item.show')
        ->with('data',$data)
        ->with('module', $this->module)
        ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

    }
    
    public function updateOrder(Request $request, $id){
        $this->validate($request,[
            'status'=>'required',
        ],[
            'status.required' => __('Trạng thái chưa được chọn'),
        ]);
        return redirect()->back()->withErrors(__('Chức năng đang bảo trì'));
        try{
            DB::beginTransaction();
            $data =  Order::where('module','=',config('module.transfer.key'))->findOrFail($id);
            // check status gửi lên trùng status của đơn hàng
            if($data->status == $request->status){
                return redirect()->back()->withErrors(__('Đơn hàng đã ở trạng thái được chọn'));
            }

            // kiểm tra đơn hàng đã được xử lý hay chưa
            if($data->status != 2){
                return redirect()->back()->withErrors(__('Chỉ xử lý đơn hàng ở trạng thái đang chờ'));
            }

            // kiểm tra số tiền của đơn hàng
            if($data->pirce < 0){
                return redirect()->back()->withErrors(__('Số tiền không hợp lệ'));
            }

            // tìm tài khoản người dùng
            $userTransaction = User::where('account_type',2)->where('status',1)->where('id',$data->author_id)->lockForUpdate()->first();
            if(!$userTransaction){
                return redirect()->back()->withErrors(__('Không tìm thấy người dùng'));
            }

            // kiểm tra tính hợp lệ của tài khoản người dùng
            if($userTransaction->checkBalanceValid() == false){
                return redirect()->back()->withErrors(__('Tài khoản người dùng đang có nghi vấn'));
            }
            $status = $request->status;
            // cập nhật người xử lý đơn hàng
            $data->processor_id = Auth::user()->id;

            // đơn hàng được tính là thành công và đúng tiền nạp
            if($status == 1){
                 // cập nhật trạng thái
                $data->status = 1;
                // số tiền được cộng bằng với sô tiền của đơn hàng
                $real_received_price = $data->price;
                $data->real_received_price = $real_received_price; // cập nhật số tiền thực nhận

                // cộng tiền user
                $userTransaction->balance = $userTransaction->balance + $real_received_price;

                // cộng số balance_in của user
                $userTransaction->balance_in = $userTransaction->balance_in + $real_received_price;
                $userTransaction->save();
                $txns=Txns::create([
                    'trade_type' => '1',//thanh toan ngan hang
                    'is_add'=>'1',//tru tien
                    'user_id'=>$userTransaction->id,
                    'amount'=>$real_received_price,
                    'real_received_amount'=>$real_received_price,
                    'last_balance'=>$userTransaction->balance,
                    'description'=>"Cộng tiền nạp tiền theo hình thức chuyển khoản thủ công",
                    'ip'=>$request->getClientIp(),
                    'ref_id'=>$data->id,
                    'status'=>1
                ]);
            }
            // đơn hàng được tính là thành công nhưng số tiền nạp sai
            else if($status == 3){
                // số tiền thực nhận mà qtv gửi lên
                $real_received_price = (int)str_replace(array(' ','.',','), '', $request->price);

                // kiểm tra số tiền thực nhận
                if($real_received_price < 0){
                    return redirect()->back()->withErrors(__('Số tiền thực nhận không hợp lệ'));
                }
                // cập nhật trạng thái đơn hàng thành công sai mệnh giá
                $data->status = 3;

                $data->real_received_price = $real_received_price; // cập nhật số tiền thực nhận

                // cộng tiền user
                $userTransaction->balance = $userTransaction->balance + $real_received_price;

                // cộng số balance_in của user
                $userTransaction->balance_in = $userTransaction->balance_in + $real_received_price;
                $userTransaction->save();
                $txns=Txns::create([
                    'trade_type' => '1',//thanh toan ngan hang
                    'is_add'=>'1',//tru tien
                    'user_id'=>$userTransaction->id,
                    'amount'=>$real_received_price,
                    'real_received_amount'=>$real_received_price,
                    'last_balance'=>$userTransaction->balance,
                    'description'=>"Cộng tiền (sai mệnh giá) nạp tiền theo hình thức chuyển khoản thủ công",
                    'ip'=>$request->getClientIp(),
                    'ref_id'=>$data->id,
                    'status'=>1
                ]);
            }
            // đơn hàng thất bại
            else if($status == 0){
                // cập nhật trạng thái đơn hàng
                $data->status = 0;
                $data->real_received_price = 0;
            }

            $data->save();
            DB::commit();
            return redirect()->back()->with('success',__('Xử lý đơn hàng thành công !'));
        }
        catch (\Exception $e) {
            Log::error($e);
            throw $e;
            return redirect()->back()->withErrors('Có lỗi phát sinh vui lòng thử lại');
        }
        

        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function Report(Request $request){
        ActivityLog::add($request, 'Truy cập danh sách thống kê nạp tiền qua ngân hàng');
        $partner_key = config('module.transfer.partner_key');
        $partner_id = config('module.transfer.partner_id');
        if($request->ajax()){
            $datatable = Order::where('module','=',config('module.transfer.key'))
            ->where(function ($query){
                $query->where('status', 1);
                $query->orWhere('status',3);
            });
            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', 'LIKE', '%' . $request->get('id') . '%');
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
            if ($request->filled('shop_id')) {
                $datatable->where('shop_id', $request->get('shop_id'));
            }
            else{
                if(session('shop_id')){
                    $datatable->where('shop_id',session('shop_id'));
                }
                else{
                    if(isset(Auth::user()->shop_access) &&Auth::user()->shop_access !== "all"){
                        $shop_id_shop_access = json_decode(Auth::user()->shop_access);
                        $datatable->whereIn('shop_id',$shop_id_shop_access);
                    }
                }
            }
            return \datatables()->eloquent($datatable)
            ->only([
                'shop_id',
                'bank_id',
                'id',
                'author_id',
                'request_id',
                'data_content',
                'price',
                'bank_name',
                'number_account',
                'status',
                'created_at',
                'action'
            ])
            ->editColumn('created_at', function($data) {
                return date('d/m/Y H:i:s', strtotime($data->created_at));
            })
            ->editColumn('shop_id', function($data) {
                $temp= '';
                if(isset($data->shop_id)){
                    $temp .=  $data->shop->domain;
                }
                return $temp;
            })
            ->editColumn('author_id', function($data) {
                return $data->author->username;
            })
            ->editColumn('price', function($data) {
                return number_format($data->price);
            })
            ->addColumn('data_content', function($data) use($partner_key) {
               return  "NAP ".$partner_key.' '.$data->request_id;
            })
            ->addColumn('action', function($row) {
                $temp= "<a href=\"".route('admin.'.$this->module.'.show',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Xem\"><i class=\"la la-eye\"></i></a>";
                return $temp;
            })
            ->toJson();
        }
        return view('admin.transfer.report.index')
        ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }


}
