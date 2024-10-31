<?php

namespace App\Http\Controllers\Api\V1\AgencyService;

use App\Http\Controllers\Controller;
use App\Library\Helpers;

use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;

use Log;


class OdpController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function OdpVerify(Request $request)
    {


        DB::beginTransaction();

        try {

            if ($request->get('secret')!="5c807d9d9f51d") {
                return "Ma xac thuc khong chinh xac !";
            }


            //SET {ID USER}
            $body = trim($request->get('body'));
             $body = strtoupper($body);

            if (strpos($body,'SET ') > -1) {

                $user_id = str_replace("SET ", "", $body);
                $userTransaction = User::where('id', $user_id)
                    ->where('id', $user_id)
                    ->where(function($q){
                        $q->orWhere('is_verify','!=',"1");
                        $q->orWhereNull('is_verify');
                    })
                    ->lockForUpdate()
                    ->first();

                if (!$userTransaction) {
                    return "Khong tim thay ma ID phu hop.Xin vui long thu lai";
                }

                //tạo verify_code
                $verify_code = strtoupper(Helpers::rand_string(6));
                //set verify_code và thời gian hết hạn vào user
                $userTransaction->verify_code = $verify_code;
                $userTransaction->verify_code_expired_at = Carbon::now()->addMinute(1);
                $userTransaction->save();
                DB::commit();
                return "Ma kich hoat cua quy khach la ".$verify_code;

            }
            elseif ($body == "ODP") {


                $phoneCustomer=$request->get('phone');
                $phoneCustomer=$user = ltrim($phoneCustomer, '084');
                $phoneCustomer=$user = ltrim($phoneCustomer, '0');
                $phoneCustomer=$user = '0' . ltrim($phoneCustomer, '84');

                $userTransaction = User::where('phone', $phoneCustomer)
                    ->where('is_verify',1)
                    ->where('odp_active',1)
                    ->lockForUpdate()
                    ->first();

                if (!$userTransaction) {
                    return "Khong tim thay ma ID phu hop.Xin vui long thu lai";
                }
                $odp_code = strtoupper(Helpers::rand_num_string(6));
                $userTransaction->odp_code=$odp_code;
                $userTransaction->odp_expired_at=Carbon::now()->endOfDay();
                $userTransaction->odp_fail=0;
                $userTransaction->save();
                DB::commit();


                 return APP_CLIENT.": Ma ODP cua quy khach la ".$odp_code .". Han su dung den ".Carbon::now()->endOfDay()->format('d/m/Y H:i:s');

            }
            else {
                DB::commit();
                return "Sai cu phap soan tin.Xin vui long thu lai";
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return 'Xin loi. He thong dang bao tri, vui long lien he admin';
        }

        // Commit the queries!




    }




}
