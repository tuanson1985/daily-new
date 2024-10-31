<?php

namespace App\Http\Controllers\Api\V1\AgencyService;

use App\Http\Controllers\Controller;
use App\Library\Helpers;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('throttle:150,2', ['except' => '']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request)
    {

       if($request->sign=="9xvcwstiq34623423demo"){


           $user = User::where('username', '=', "tt_demo")
               ->select([
                   'id',
                   'username',
                   'password',
                   'email',
                   'phone',
                   'account_type',
                   'partner_key',
               ])
               ->where('status', 1)
               ->where('account_type', 2)
               ->first();


           if ($user) {

               return response()->json([
                   'status' => 1,
                   'data' => $user
               ]);
           } else {

               return response()->json([
                   'status' => 1,
                   'data' => 'Tài khoản hoặc mật khẩu không đúng'
               ], 404);
           }

       }




        if ($request->sign != "12f9xvcwstiSH1231quvhyxxumj022222slive") {


            return response()->json([
                'status' => 0,
                'message' => "Không được phép truy cập",
            ]);
        }

        $user = User::where('username', '=', $request->username)
            ->select([
                'id',
                'username',
                'email',
                'phone',
                'account_type',
                'partner_key_service',


            ])
            ->where('status', 1)
            ->where('account_type', 2)
            ->first();

        if ($user) {

            if (\Hash::check($request->password, $user->password)) {

                //active log active
//                Activity::create([
//                    'action' => 'CREATE',
//                    'content' => "Xem tài khoản shop qua api",
//                    'module' => config('constants.module.game.key_attribute'),
//                    'ip_address' => $request->getClientIp(),
//                    'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent'
//                ]);
                ActivityLog::add($request, 'Xem tài khoản shop qua api');

                return response()->json([
                    'status' => 1,
                    'data' => $user->makeHidden('password')
                ]);


            } else {

                return response()->json([
                    'status' => 0,
                    'data' => 'Tài khoản hoặc mật khẩu không đúng'
                ]);
            }
        } else {

            return response()->json([
                'status' => 1,
                'data' => 'Tài khoản hoặc mật khẩu không đúng'
            ], 404);
        }


    }


    public function store(Request $request)
    {

        if($request->sign=="9xvcwstiq34623423demo"){


            $user = User::where('username', '=', "tt_demo")
                ->select([
                    'id',
                    'username',
                    'email',
                    'phone',
                    'account_type',
                    'partner_key_service',
                ])
                ->where('status', 1)
                ->where('account_type', 2)
                ->first();


            if ($user) {
                return response()->json([
                    'status' => 1,
                    'data' => $user->makeHidden('password'),
                    'message' => "Tạo tài khoản thành công",
                ]);
            } else {

                return response()->json([
                    'status' => 0,
                    'message' => 'Tài khoản hoặc mật khẩu không đúng'
                ], 404);
            }

        }

       //////////////LIVE///////////




        if ($request->sign != "12f9xvcwstiSH1231quvhyxxumj022222slive") {
            return response()->json([
                'status' => 0,
                'message' => "Không được phép truy cập",
            ]);
        }


        $input = $request->all();

        $rules = [
            'username' => 'required|min:3|max:30|regex:/^([A-Za-z0-9_\-\.])+$/i',
            'password' => 'required|min:6|max:32',
            //					'password_confirmation' => 'required|same:password',
        ];
        $message = [
            'username.min' => 'Tên tài khoản ít nhất 3 ký tự.',
            'password.min' => 'Mật khẩu phải ít nhất 6 ký tự.',
            'username.max' => 'Tên tài khoản không quá 16 ký tự.',
            'password.max' => 'Mật khẩu không vượt quá 32 ký tự.',
            'email.required' => 'Vui lòng nhập trường này',
            'username.required' => 'Vui lòng nhập tên tài khoản',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password_confirmation.required' => 'Vui lòng nhập mật khẩu xác nhận',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'username.regex' => 'Tên tài khoản không ký tự đặc biệt',
            'email.unique' => 'Địa chỉ email đã được sử dụng.',
            'phone.unique' => 'Số điện thoại đã được sử dụng.',
            'username.unique' => 'Tên tài khoản đã được sử dụng.',
            'password_confirmation.same' => 'Mật khẩu xác nhận không đúng.',
            //			'created_at.required' => 'Vui lòng nhập ngày tạo',
            //			'created_at.date_format' => 'Vui lòng nhập đúng định dạng ngày tháng (dd/mm/YYYY H:i:s)',

        ];


        $validator = \Validator::make($input, $rules, $message);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::where('username', '=', $request->username)
            ->select([
                'id',
                'username',
                'password',
                'email',
                'phone',
                'account_type',
                'partner_key_service',

            ])
            ->where('status', 1)
            ->where('account_type', 2)
            ->first();
        if ($user) {

            if (\Hash::check($request->password, $user->password)) {

                ActivityLog::add($request, 'Tạo tài khoản shop qua api');

                return response()->json([
                    'status' => 1,
                    'data' => $user->makeHidden('password'),
                    'message' => "Tạo tài khoản thành công",
                ]);


            } else {
                return response()->json([
                    'status' => 0,
                    'message' => "Tài khoản không tồn tại hoặc sai mật khẩu",
                ]);
            }
        }
        else {

            $input['password'] = \Hash::make($request->password);
            $input['account_type'] = 2;
            $input['active_api_buy_nrogem'] =1;
            $input['is_agency_buygem'] =0;
            $input['active_api_buy_ninjaxu'] =1;
            $input['is_agency_ninjaxu'] =0;
            $input['nrocoin_discount'] =1;
            $input['active_api_buy_nrocoin'] =1;
            $input['is_agency_nrocoin'] =0;
            $input['active_api_card'] =1;
            $input['partner_key_service'] = md5($input['username'] . time() . Helpers::rand_num_string(6));
            $input['status'] = 1;
            $input['type_information'] =0;
            $user = User::create($input);

            //active log active
            ActivityLog::add($request, 'Tạo tài khoản user qua api');

            return response()->json([
                'status' => 1,
                'message' => "Tạo tài khoản thành công",
                'data' => $user
            ]);
        }

    }


}
