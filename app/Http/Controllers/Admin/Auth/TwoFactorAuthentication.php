<?php

namespace App\Http\Controllers\Admin\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class TwoFactorAuthentication extends Controller
{

    public function show2faForm(Request $request){


        $user = auth()->user();


        $google2fa_url = "";
        if($user->google2fa_secret){

            $google2fa = app('pragmarx.google2fa');
            $google2fa_url = $google2fa->getQRCodeInline(
                $request->getHttpHost(),
                $user->email,
                $user->google2fa_secret
            );
        }
        $data = array(
            'user' => $user,
            'google2fa_url' => $google2fa_url
        );

        return view('admin.auth.2fa')->with('data', $data);
    }


    public function generate2faSecret(Request $request){

        //$user= User::findOrFail(auth()->user()->id);
        $user = auth()->user();
        if($user->google2fa_enable==1){
           return redirect()->back()->with('success',"2FA is Enable");
        }
        // Initialise the 2FA class
        $google2fa = app('pragmarx.google2fa');

        // Add the secret key to the registration data
        $user->google2fa_secret=$google2fa->generateSecretKey();
        $user->save();
        return redirect('/admin-1102/2fa')->with('success',"Secret Key is generated, Please verify Code to Enable 2FA");
    }


    public function enable2fa(Request $request){

        $user = auth()->user();
        $google2fa = app('pragmarx.google2fa');
        $secret = $request->get('verify-code');
        $valid = $google2fa->verifyKey($user->google2fa_secret, $secret);
        if($valid){
            $user->google2fa_enable=1;
            $user->save();
            return redirect('/admin-1102/2fa')->with('success',"2FA is Enabled Successfully.");
        }else{
            return redirect('/admin-1102/2fa')->with('error',"Invalid Verification Code, Please try again.");
        }
    }
    public function disable2fa(Request $request){

        if (!(\Hash::check($request->get('current-password'), auth()->user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error","Your  password does not matches with your account password. Please try again.");
        }

        $validatedData = $request->validate([
            'current-password' => 'required',
        ]);
        $user = auth()->user();
        $user->google2fa_secret = null;
        $user->google2fa_enable = 0;
        $user->save();
        return redirect('/admin-1102/2fa')->with('success',"2FA is now Disabled.");
    }




}
