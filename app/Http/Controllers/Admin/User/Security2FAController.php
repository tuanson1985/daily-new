<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Security2FAController extends Controller
{
    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;
    public function __construct(Request $request)
    {
        $this->module='security-2fa';
        $this->moduleCategory=null;
        //set permission to function
        $this->middleware('permission:security-2fa');
        $this->page_breadcrumbs[] = [
            'page' => route('admin.charge-report.index'),
            'title' => __('Bảo mật tài khoản')
        ];
    }
    public function index(Request $request){
        ActivityLog::add($request, 'Truy cập trang cài đặt bảo mật'.$this->module);
        $user = Auth::user();
        if($user->google2fa_enable == 1){
            $google2fa_enable = 1;
        }
        else{
            $google2fa_enable = 0;
        }
        return view('admin.2fa.index')
        ->with('module', $this->module)
        ->with('user', $user)
        ->with('google2fa_enable', $google2fa_enable)
        ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }
    public function setup(Request $request){
        ActivityLog::add($request, 'Truy cập trang setup bảo mật'.$this->module);
        $user = User::findOrFail(Auth::user()->id);
        if($user->google2fa_enable == 1){
            return redirect()->route('admin.security-2fa.index')->withErrors("Tài khoản đã được cấu hình GG2FA" );
        }
        $google2fa = app('pragmarx.google2fa');
           // secret_key cho từng user
        $google2fa_secret = $google2fa->generateSecretKey();
        if($google2fa_secret){
            $user->google2fa_secret=$google2fa_secret;
            $user->save();
        }
        // lấy QR ảnh để lấy thông tin và quét mã lưu tài khoản vào app GG2FA
        $google2fa_url = $google2fa->getQRCodeInline(
            $request->getHttpHost(),
            $user->username."-".$user->email,
            $user->google2fa_secret
        );
        return view('admin.2fa.setup')
        ->with('module', $this->module)
        ->with('user', $user)
        ->with('google2fa_url', $google2fa_url)
        ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }
    public function enable2fa(Request $request){
        $google2fa = app('pragmarx.google2fa');
        $code = $request->get('code');
        $user = User::findOrFail(Auth::user()->id);
        if(!$code){
            return response()->json([
                'message' => 'Vui lòng nhập mã code',
                'status' => 0,
            ], 200);
        }
        $data = $google2fa->verifyKey($user->google2fa_secret, $code);
        if($data === true){
            $two_factor_recovery_codes = rand(10000000,99999999);
            $user->google2fa_enable = 1;
            $user->two_factor_recovery_codes = md5($two_factor_recovery_codes);
            $user->save();
            session()->put('security_2fa_web_'.md5($user->id),$user->id);
            ActivityLog::add($request, 'Kích hoạt bảo mật google2fa thành công '.$this->module);
            return response()->json([
                'message' => 'Kích hoạt bảo mật thành công, đang chuyển hướng',
                'status' => 1,
                'two_factor_recovery_codes' => $two_factor_recovery_codes,
                'redirect' => route('admin.security-2fa.index')
            ], 200);
        }
        return response()->json([
            'message' => 'Mã code không đúng, vui lòng nhập lại',
            'status' => 0,
        ], 200);
    }
    public function disable2fa(Request $request){
        $status = $request->get('status');
        $code = $request->get('code');
        if($status != 0){
            return redirect()->back()->withErrors("Chức năng này chỉ để sử dụng để tắt bảo mật GG2FA");
        }
        $user = User::findOrFail(Auth::user()->id);
        $google2fa = app('pragmarx.google2fa');
        $data = $google2fa->verifyKey($user->google2fa_secret, $code);
        if($data === true){
            $user->google2fa_enable = 0;
            $user->google2fa_secret = null;
            $user->save();
            ActivityLog::add($request, 'Tắt bảo mật google2fa thành công '.$this->module);
            return redirect()->back()->with('success',__('Bảo mật GG2FA đã được tắt'));
        }
        return redirect()->back()->withErrors("Mã bảo mật GG2FA không đúng");
    }
    public function getVery(Request $request){
        ActivityLog::add($request, 'Vào trang nhập mã bảo mật google2fa khi đăng nhập');
        $user = User::findOrFail(Auth::user()->id);
        return view('admin.auth.very_security')
        ->with('module', $this->module)
        ->with('user', $user)
        ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }
    public function postVery(Request $request){
        $code = $request->get('code'); 
        $user = User::findOrFail(Auth::user()->id);
        $google2fa = app('pragmarx.google2fa');
        $data = $google2fa->verifyKey($user->google2fa_secret, $code);
        if($data === true){
            ActivityLog::add($request, 'Very bảo mật google2fa khi đăng nhập thành công');
            session()->put('security_2fa_web_'.md5($user->id),$user->id);
            return redirect()->route('admin.index');
        }
        return redirect()->back()->withErrors("Mã bảo mật GG2FA không đúng");
    }
    public function getRecoveryCode(Request $request){
        $user = User::findOrFail(Auth::user()->id);
        if(!empty($user->two_factor_recovery_codes)){
            return redirect()->route('admin.security-2fa.index')->withErrors("Tài khoản đã được cung cấp mã khôi phục" );
        }
        ActivityLog::add($request, 'Truy cập trang lấy mã truy cập'.$this->module);
        return view('admin.2fa.recovery-code')
        ->with('module', $this->module)
        ->with('user', $user)
        ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }
    public function postRecoveryCode(Request $request){
        $google2fa = app('pragmarx.google2fa');
        $code = $request->get('code');
        $user = User::findOrFail(Auth::user()->id);
        if(!$code){
            return response()->json([
                'message' => 'Vui lòng nhập mã code',
                'status' => 0,
            ], 200);
        }
        if(!empty($user->two_factor_recovery_codes)){
            return response()->json([
                'message' => 'Tài khoản đã được cung cấp mã khôi phục',
                'status' => 0,
            ], 200);
        }
        $data = $google2fa->verifyKey($user->google2fa_secret, $code);
        if($data === true){
            $two_factor_recovery_codes = rand(10000000,99999999);
            $user->two_factor_recovery_codes = md5($two_factor_recovery_codes);
            $user->save();
            ActivityLog::add($request, 'Lấy mã khôi phục google2fa thành công '.$this->module);
            return response()->json([
                'message' => 'Lấy mã khôi phục google2fa thành công, đang chuyển hướng',
                'status' => 1,
                'two_factor_recovery_codes' => $two_factor_recovery_codes,
                'redirect' => route('admin.security-2fa.index')
            ], 200);
        }
        return response()->json([
            'message' => 'Mã code không đúng, vui lòng nhập lại',
            'status' => 0,
        ], 200);
    }
}
