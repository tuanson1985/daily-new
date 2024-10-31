<?php

namespace App\Http\Controllers\Admin\Donate;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Telecom;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SettingController extends Controller
{


    public function __construct(Request $request)
    {

        //set permission to function
        $this->middleware('permission:donate-setting');
        //$this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate']]);
        //$this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        //$this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);


        $this->page_breadcrumbs[] = [
            'page' => route('admin.donate-setting.index'),
            'title' => __('Cấu hình donate')
        ];
    }


    public function index(Request $request)
    {
        ActivityLog::add($request, 'Vào form edit donate-setting');
        $ratio = null;
        $setting = Setting::where('name','ratio_donate')->first();
        if($setting){
            $ratio = $setting->val;
        }
        return view('admin.donate.setting.index')
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
        $setting = Setting::where('name','ratio_donate')->first();
        if(!$setting){
            $data = Setting::create([
                'name' => 'ratio_donate',
                'val' => $ratio
            ]);
        }
        else{
            $setting->val = $ratio;
            $setting->save();
        }
        ActivityLog::add($request, 'Cập nhật thành công setting donate');
        return response()->json([
            'success' => true,
            'message' => __('Cập nhật thành công !'),
            'redirect' => ''
        ]);

    }

    public function getKeyWord(Request $request){
        ActivityLog::add($request, 'Vào form edit từ khóa nội dung donate');
        $key = null;
        $string = Setting::where('name','prohibit_key_donate')->first();
        if($string){
            $key = explode('|', $string->val);
        }
        return view('admin.donate.setting.key-word')
        ->with('page_breadcrumbs', $this->page_breadcrumbs)
        ->with('key', $key);
    }

    public function postKeyword(Request $request){
        $key = $request->key_word;
        // if(in_array(null, $key)){
        //     return redirect()->back()->withErrors(__('Không có dữ liệu cập nhật'));
        // }
        $count = count($key);
        $string = "";
        for($i = 0; $i < $count; $i++){
            if($key[$i] != "" || $key[$i] != null){
                if($i == 0){
                    $string .= $key[$i];
                }
                else{
                    $string .= '|'.$key[$i];
                }
            }
        }
        $setting = Setting::where('name','prohibit_key_donate')->first();
        if(!$setting){
            $data = Setting::create([
                'name' => 'prohibit_key_donate',
                'val' => $string
            ]);
        }
        else{
            $setting->val = $string;
            $setting->save();
        }
        ActivityLog::add($request, 'Cập nhật thành công từ khóa chặn donate');
        
        return redirect()->back()->with('success',__('Cập nhật thành công '.$count.' từ khóa'));
        
    }




}
