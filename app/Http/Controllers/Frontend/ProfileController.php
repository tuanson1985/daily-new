<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Library\Helpers;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Otp;
use Hash;
use Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ProfileController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
    protected $page_breadcrumbs;
    protected $module;
	public function __construct()
	{
		//$this->middleware('permission:admin-index');
        $this->page_breadcrumbs[] = [
            'page' => route('frontend.profile'),
            'title' => __("Thông tin cá nhân")
        ];
	}
	public function profile(){
        return view('frontend.profile.index')->with('page_breadcrumbs',$this->page_breadcrumbs);
    }
    public function getChangeCurrentPassword()
    {
        return view('frontend.profile.index')->with('tab',1);
    }

    public function postChangeCurrentPassword(Request $request){
        $this->validate($request,[
            'old_password'=>'required',
            'password'=>'required',
            'password_confirmation' => 'required|same:password',
        ],[
            'old_password.required' => __('Vui lòng nhập mật khẩu cũ'),
            'password.required' => __('Vui lòng nhập mật khẩu mới'),
            'password_confirmation.required' => __('Vui lòng nhập mật khẩu xác nhận'),
            'password_confirmation.same' => __('Mật khẩu xác nhận không đúng.'),
        ]);

        $user = User::findOrFail(Auth::guard('frontend')->user()->id);
        if(Hash::check($request->old_password,$user->password)){
            $user->password=Hash::make($request->password);
            $user->save();

            $ip = $request->getClientIp();
            $user_agent = $request->userAgent();
            $message = "Thời gian: <b>" . Carbon::now()->format('d-m-Y H:i:s') . "</b>";
            $message .= "\n";
            $message .= "Thành viên <b>".Auth::guard('frontend')->user()->username."</b> đã đổi mật khẩu";
            $message .= "\n";
            $message .= "IP: <b>" . $ip . "</b>";
            $message .= "\n";
            $message .= "User_agent: <b>" . $user_agent . "</b>";
            Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_bot_change_current_password'));

            Auth::login($user);
            return redirect()->back()->with('tab',1)->with('success',  __('Đổi mật khẩu thành công'));
        }
        else{

            return redirect()->back()->withErrors(__('Mật khẩu cũ không đúng'));
        }
    }
}
