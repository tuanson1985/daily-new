<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Library\Helpers;
use App\Models\ActivityLog;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = "";
    protected $redirectAfterLogout ="";
    protected $maxAttempts=5;
    protected $decayMinutes=3;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        $this->middleware('guest')->except('logout');

        $this->redirectTo=route('admin.index');
        $this->redirectAfterLogout=route('admin.login');

    }



    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {

        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);


        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {

        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {

        $user = User::where($this->username(), $request->email)
            ->where('status', 1)
            ->where(function ($query) use ($request){
                $query->orWhere('account_type', 1);
                $query->orWhere('account_type',3);
            })
            ->first();
        if(!$user){
            Session::flash('error_login_gmail', 'Thông tin tài khoản hoặc mật khẩu không chính xác');
            return false;
        }
        if($user->required_login_gmail == 1){
            Session::flash('error_login_gmail', 'Tài khoản của bạn đã được cấu hình đăng nhập với google. Vui lòng đăng nhập bằng tài khoản google để truy cập vào hệ thống');
            return false;
        }
        if(isset($user) && isset($user->ip_allow) && $user->ip_allow != 'all'){
            if (strpos($user->ip_allow, "all,") > -1 || strpos($user->ip_allow, $request->getClientIp() . ",") > -1) {

            }
            else {
                Session::flash('error_login_gmail', 'IP không được phép truy cập');
                return false;
            }
        }
        if ($user && \Hash::check($request->password, $user->password)) {
            return $this->guard()->attempt(
                $this->credentials($request)+[
                    'status'=>1
                ]
                , $request->filled('remember')
            );
        }
        return false;


        // return $this->guard()->attempt(
        //     $this->credentials($request)+[
        //         'status'=>1
        //     ]
        //     , $request->filled('remember')
        // );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->intended($this->redirectPath());
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $user->update([
            'lastlogin_at'=>Carbon::now()
        ]);

        $user->setMeta('ip_login',$request->ip());
        session()->put('ip_login_'.md5($user->id),$request->getClientIp());

        ActivityLog::add($request,'Login successfully admincp');
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    // override field login
    public function username()
    {
        $login = request()->input('username')??request()->input('email');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $login]);
        return $field;

    }

    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        throw ValidationException::withMessages([
            $this->username() => [Lang::get('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ])],
        ])->status(Response::HTTP_TOO_MANY_REQUESTS);

    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {


        $user=$this->guard()->user()->update([
            'lastlogout_at'=>Carbon::now()
        ]);

        ActivityLog::add($request,'Logout successfully admincp');

        $this->guard()->logout();

        // $request->session()->flush();

        // $request->session()->invalidate();

        // $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()? new JsonResponse([], 204) : redirect($this->redirectAfterLogout);


    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    public function loginGmail(Request $request){
        $url = config('services.google.return_url');
        if(empty($url)){
            return redirect()->to(route('admin.login'))->with('error_login_gmail', 'Google OAuth 2.0 chưa được cấu hình.Vui lòng liên hệ QTV để kịp thời xử lý');
        }
        $url = $url.'/'.str_replace(".","_",$request->getHost());
        return redirect()->to($url);
    }
    public function callbackLoginGmail(Request $request,$token){
        if(!$token){
            abort(403);
        }
        $key_config = 'A2X4oYbBGkECUa0Eeo5AVAzZZh4Rwz43';
        $encrypt = 'zK25hWfe94i9QeRWtcyfROQFvEl4PO5G';
        $data = Helpers::Decrypt($token,$encrypt);
        if(empty($data)){
            abort(403);
        }
        $data = explode('|',$data);
        if(empty($data)){
            abort(403);
        }
        $status = $data[0];
        $key = $data[1];
        $time = $data[2];
        $email = $data[3];
        $provider_id = $data[4];
        if(empty($key)){
            abort(403);
        }
        if($key !== $key_config){
            abort(403);
        }
        if (Carbon::now()->greaterThan(Carbon::createFromTimestamp($time))) {
            abort(404);
        }
        if($status != 1){
            return redirect()->to(route('admin.login'))->with('error_login_gmail', 'Đăng nhập google không thành công.');
        }
        if(!$email){
            return redirect()->to(route('admin.login'))->with('error_login_gmail', 'Không lấy được thông tin email khi đăng nhập với google.');
        }
        $user = User::where('email', $email)
            ->where('status', 1)
            ->where(function ($query){
                $query->orWhere('account_type', 1);
                $query->orWhere('account_type',3);
            })
            ->first();
        if(!$user){
            return redirect()->to(route('admin.login'))->with('error_login_gmail', 'Tài khoản không tồn tại hoặc đã bị khóa.');
        }
        if($user->required_login_gmail != 1){
            return redirect()->to(route('admin.login'))->with('error_login_gmail', 'Tài khoản này không được phép login bằng google.');
        }
        $user->update([
            'lastlogin_at'=>Carbon::now()
        ]);
        $user->setMeta('ip_login',$request->ip());
        session()->put('ip_login_'.md5($user->id),$request->getClientIp());
        Auth::login($user);
        return redirect()->intended($this->redirectPath());
    }


    // public function loginGmail(Request $request){
    //     return Socialite::driver('google')->redirect();
    // }
    // public function callbackLoginGmail(Request $request){
    //     $info = Socialite::driver('google')->stateless()->user();
    //     if(!$info){
    //         return redirect()->to(route('admin.login'))->with('error_login_gmail', 'Không xử lý được thông tin đăng nhập với google.');
    //     }
    //     $email = $info->email??null;
    //     if(!$email){
    //         return redirect()->to(route('admin.login'))->with('error_login_gmail', 'Không lấy được thông tin email khi đăng nhập với google.');
    //     }
    //     $user = User::where('email', $email)
    //     ->where('status', 1)
    //     ->where(function ($query){
    //         $query->orWhere('account_type', 1);
    //         $query->orWhere('account_type',3);
    //     })
    //     ->first();
    //     if(!$user){
    //         return redirect()->to(route('admin.login'))->with('error_login_gmail', 'Tài khoản không tồn tại hoặc đã bị khóa.');
    //     }
    //     if($user->required_login_gmail != 1){
    //         return redirect()->to(route('admin.login'))->with('error_login_gmail', 'Tài khoản này không được phép login bằng google.');
    //     }
    //     $user->update([
    //         'lastlogin_at'=>Carbon::now()
    //     ]);
    //     $user->setMeta('ip_login',$request->ip());
    //     session()->put('ip_login_'.md5($user->id),$request->getClientIp());
    //     Auth::login($user);
    //     return redirect()->intended($this->redirectPath());
    // }
}
