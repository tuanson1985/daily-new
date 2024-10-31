<?php

namespace App\Http\Controllers\Admin\Booking;

use App\Http\Controllers\Controller;
use App\Library\Files;
use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\Setting;
use Illuminate\Http\Request;



class SettingController extends Controller
{


    public function __construct(Request $request)
    {

        //set permission to function
        $this->middleware('permission:booking-setting');
        //$this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate']]);
        //$this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        //$this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);

        $this->page_breadcrumbs[] = [
            'page' => route('admin.booking-setting.index'),
            'title' => __('Cấu hình booking')
        ];
    }


    public function index(Request $request)
    {
        $ratio = null;
        $setting = Setting::where('name','ratio_booking')->first();
        if($setting){
            $ratio = $setting->val;
        }
        ActivityLog::add($request, 'Vào form edit booking-setting');
        return view('admin.booking.setting.index')
        ->with('ratio',$ratio)
        ->with('page_breadcrumbs', $this->page_breadcrumbs);

    }


    public function store(Request $request)
    {
        $ratio = $request->ratio;
        $ratio = (int)$ratio;
        if($ratio > 100){
            return response()->json([
                'success' => false,
                'message' => __('Chiết khấu lớn hơn 100%, không hợp lệ, Cấu hình không được cập nhật, Vui lòng tải lại trang để xem chiết khấu ban đầu !'),
                'redirect' => ''
            ]);
        }
        if($ratio < 60){
            return response()->json([
                'success' => false,
                'message' => __('Chiết khấu nhỏ hơn 60%, không hợp lệ, Cấu hình không được cập nhật, Vui lòng tải lại trang để xem chiết khấu ban đầu !'),
                'redirect' => ''
            ]);
        }
        $setting = Setting::where('name','ratio_booking')->first();
        if(!$setting){
            $data = Setting::create([
                'name' => 'ratio_booking',
                'val' => $ratio
            ]);
        }
        else{
            $setting->val = $ratio;
            $setting->save();
        }
        ActivityLog::add($request, 'Cập nhật thành công setting booking');
        return response()->json([
            'success' => true,
            'message' => __('Cập nhật thành công !'),
            'redirect' => ''
        ]);

    }








}
