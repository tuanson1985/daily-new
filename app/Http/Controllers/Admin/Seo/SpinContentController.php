<?php

namespace App\Http\Controllers\Admin\Seo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Shop_Group;
use App\Models\Shop;
use App\Models\Shop_Group_Shop;
use Html;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Validator;


class SpinContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function __construct(Request $request)
    {

    }
    public function index(Request $request)
    {
        $oldText=$request->text;

        if(strlen($oldText)<32){
            return json_encode([
                'status'=>1,
                'text'=>"Dữ liệu không được nhỏ hơn 32 ký tự"
            ]);
        }
        $result=\App\Library\HelpSpinContent::spinPaid($oldText);
        if($result){
            if($result->status==1){
                return response()->json([
                    'oldText'=>$oldText,
                    'status'=>1,
                    'text'=>$result->message


                ]);
            }
            else{
                return response()->json([
                    'oldText'=>$oldText,
                    'status'=>0,
                    'text'=>$result->message??""

                ]);
            }
        }
        else{
            return response()->json([
                'status'=>0,
                'text'=>"Không thể kết nối với máy chủ"

            ]);
        }
    }



}
