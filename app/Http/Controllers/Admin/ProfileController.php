<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Hash;
use Auth;
use Illuminate\Http\Request;
use Log;

class ProfileController extends Controller
{



	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//$this->middleware('permission:admin-index');



        $this->page_breadcrumbs[] = [
            'page' => route('admin.profile'),
            'title' => __("Thông tin cá nhân")
        ];

	}


	public function profile(){

        $price_control_total = 0;
        try {

            $price_control = Order::query()
                ->where('gate_id',0)
                ->where(function($q){
                    $q->orWhere('status', '=',10);
                    $q->orWhere('status', '=',11);
                })
                ->where('module', '=', config('module.service-purchase'))
                ->where('processor_id',Auth::user()->id)
                ->whereNull('type_version')
                ->with(['item_ref','author', 'processor' => function($query){
                    $query->with('service_access');
                }])
                ->get()->map(function ($item){
                    $ratio = 80;
                    if (isset($item->processor)){
                        if (isset($item->processor->service_access)){
                            $service_access = $item->processor->service_access;
                            $param = json_decode(isset($service_access->params) ? $service_access->params : "");
                            if(isset($param->{'ratio_' . ($item->item_ref->id??null)})){
                                $ratio= $param->{'ratio_' . ($item->item_ref->id??null)};
                            }
                            else{
                                $ratio=$ratio;
                            }
                        }
                    }

                    //cộng tiền user
                    $real_received_amount = ($ratio * $item->price_ctv) / 100;
                    return (int)$real_received_amount;
                });
            $price_control_total = $price_control->toArray();
            $price_control_total = array_sum($price_control_total);

        }
        catch(\Exception $e){
            Log::error($e);
            $price_control_total = 0;
        }

        return view('admin.profile.index')->with('page_breadcrumbs',$this->page_breadcrumbs)->with('price_control_total',$price_control_total);
    }



    public function getChangeCurrentPassword()
    {

        return view('admin.profile.index')->with('tab',1);
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
        $user = User::findOrFail(Auth::user()->id);
        if(Hash::check($request->old_password,$user->password)){
            $user->password=Hash::make($request->password);
            $user->save();
            Auth::login($user);



            return redirect()->back()->with('tab',1)->with('success', trans( 'Đổi mật khẩu thành công'));
        }
        else{

            return redirect()->back()->withErrors(trans('Mật khẩu cũ không đúng'));
        }
    }

    public function postChangeCurrentPassword2(Request $request){
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
        $user = User::findOrFail(Auth::user()->id);
        if(Hash::check($request->old_password,$user->password2)){
            $user->password2=Hash::make($request->password);
            $user->save();
            Auth::login($user);
            return redirect()->back()->with('tab',1)->with('success', trans( 'Đổi mật khẩu cấp 2 thành công'));
        }
        else{

            return redirect()->back()->withErrors(trans('Mật khẩu cấp 2 cũ không đúng'));
        }
    }
}
