<?php

namespace App\Http\Controllers\Admin\DepositBank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\ActivityLog;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(Request $request)
    {

        //set permission to function
        $this->middleware('permission:deposit-bank-setting');
        //$this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate']]);
        //$this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        //$this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);


        $this->page_breadcrumbs[] = [
            'page' => route('admin.deposit-bank-setting.index'),
            'title' => __('Cấu hình chiết khấu cổng thanh toán')
        ];
    }
    public function index(Request $request)
    {
        ActivityLog::add($request, 'Vào form edit deposit-bank-setting');
        $percent = null;
        $percent = Setting::where('name','ratio_percent_alepay')->first();
        if($percent){
            $percent = $percent->val;
        }
        $amount = null;
        $amount = Setting::where('name','ratio_amount_alepay')->first();
        if($amount){
            $amount = $amount->val;
        }
        return view('admin.deposit-bank.setting.index')
            ->with('percent',$percent)
            ->with('amount',$amount)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
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
        $percent = (int)$request->percent;
        $amount = (int)str_replace(array(' ','.',','), '', $request->amount);
        if($percent < 0){
            return response()->json([
                'success' => false,
                'message' => __('% chiết khấu nhỏ hơn 0%, không hợp lệ, Cấu hình không được cập nhật, Vui lòng tải lại trang để xem chiết khấu ban đầu !'),
                'redirect' => ''
            ]);
        }
        if($percent > 100){
            return response()->json([
                'success' => false,
                'message' => __('% chiết khấu lớn hơn 100%, không hợp lệ, Cấu hình không được cập nhật, Vui lòng tải lại trang để xem chiết khấu ban đầu !'),
                'redirect' => ''
            ]);
        }
        if($amount < 0){
            return response()->json([
                'success' => false,
                'message' => __('Số tiền chiết khấu nhỏ hơn 0%, không hợp lệ, Cấu hình không được cập nhật, Vui lòng tải lại trang để xem chiết khấu ban đầu !'),
                'redirect' => ''
            ]);
        }

        $setting_percent = Setting::where('name','ratio_percent_alepay')->first();
        if(!$setting_percent){
            $data_percent = Setting::create([
                'name' => 'ratio_percent_alepay',
                'val' => $percent
            ]);
        }
        else{
            $setting_percent->val = $percent;
            $setting_percent->save();
        }
        $setting_amount = Setting::where('name','ratio_amount_alepay')->first();
        if(!$setting_amount){
            $data_amount = Setting::create([
                'name' => 'ratio_amount_alepay',
                'val' => $amount
            ]);
        }
        else{
            $setting_amount->val = $amount;
            $setting_amount->save();
        }
        ActivityLog::add($request, 'Cập nhật thành công setting chiết khẩu cổng thanh toán');
        return response()->json([
            'success' => true,
            'message' => __('Cập nhật thành công !'),
            'redirect' => ''
        ]);

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
        //
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
}
