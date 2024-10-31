<?php

namespace App\Http\Controllers\Api\V1\AgencyService;

use App\Http\Controllers\Controller;
use App\Library\Helpers;

use App\Models\KhachHang;
use App\Models\Nrogem_GiaoDich;
use App\Models\PlusMoney;
use App\Models\User;
use App\Models\Client;
use Auth;
use Carbon\Carbon;
use DB;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;
use App\Models\Charge;
use App\Models\Txns;
use Log;
use Setting;


class ReportController extends Controller
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

    public function money(Request $request)
    {
		  if($request->sign!="9xvcwstiquvhyxxumj0s"){
			return "Không được phép truy cập";
		}

		//$client=Client::get(['app_client']);

        $datatable = PlusMoney::with('processor','shop')->with('user',function($query){
            $query->with('shop');
        });
        $datatable->whereHas('user', function ($query) use ($request) {
            $query->where(function ($queryChild)use ($request){
                $queryChild->orWhere('account_type', 3);
            });
        });

        if ($request->filled('started_at')) {

            $datatable->whereDate('created_at', $request->get('started_at'));
        }
        else{
            $datatable->whereDate('created_at', Carbon::today()->subDay(1));
        }

        $datatable = $datatable->get();

		$arrAll=[];
            foreach ($datatable as $item) {
			   $arrAll[]=[
					'Thoigian' => $item->created_at,
					'Magiaodich' => $item->id,
					'Loaigiaodich' => $item->is_add,
					'Nguoicong' => $item->processor->username,
					'Nguoinhan' => $item->user->username,
					'Sotien' => $item->amount,
					'Nguontien' => config('module.txns.source_type.'.$item->source_type),
					'Nganhang' => config('module.txns.source_bank.'.$item->source_bank),
					'Ghichu' => $item->description,
				];
            }
        return $arrAll;
    }

    public function bomvang(Request $request)
    {
        if($request->sign!="9xvcwstiquvhyxxumj0s"){
            return "Không được phép truy cập";
        }
        // dd($request->get('started_at'));
        //$client=Client::get(['app_client']);

        $datatable= KhachHang::whereRaw('c_truoc is not null');
        if ($request->filled('started_at')) {
            $datatable->whereDate('updated_at', $request->get('started_at'));
        }
        else{
            $datatable->whereDate('updated_at', Carbon::today()->subDay(1));
        }
        $datatable->whereRaw('c_truoc < c_sau');

        $datatable=$datatable
            ->selectRaw('updated_at')
            ->selectRaw('server')
            ->selectRaw('uname')
            ->selectRaw('thoivangtruoc')
            ->selectRaw('thoivangsau')
            ->selectRaw('c_truoc')
            ->selectRaw('c_sau')
            ->get();
        $arrAll=[];

        foreach ($datatable as $item_dt) {
            array_push($arrAll,[
                'thoigianbom'=>$item_dt->updated_at,
                'server'=>$item_dt->server,
                'tennhanvat'=> $item_dt->uname,
                'sovangtruoc'=> $item_dt->c_truoc,
                'sovangsau'=> $item_dt->c_sau,
                'sovang'=>$item_dt->c_sau-$item_dt->c_truoc
            ]);
        }
        return $arrAll;
    }

    public function rutvang(Request $request)
    {
        if($request->sign!="9xvcwstiquvhyxxumj0s"){
            return "Không được phép truy cập";
        }
        // dd($request->get('started_at'));
        //$client=Client::get(['app_client']);


        $datatable= KhachHang::whereRaw('c_truoc is not null');
        if ($request->filled('started_at')) {
            $datatable->whereDate('updated_at', $request->get('started_at'));
        }
        else{
            $datatable->whereDate('updated_at', Carbon::today()->subDay(1));
        }
        $datatable->whereRaw('c_sau < c_truoc')->whereHas('order', function ($query) use ($request) {

            $query->whereNull('price');

        });

        $datatable=$datatable
            ->selectRaw('updated_at')
            ->selectRaw('server')
            ->selectRaw('uname')
            ->selectRaw('thoivangtruoc')
            ->selectRaw('thoivangsau')
            ->selectRaw('c_truoc')
            ->selectRaw('c_sau')
            ->get();
        $arrAll=[];
        foreach ($datatable as $item_dt) {
            array_push($arrAll,[
                'thoigianrut'=>$item_dt->updated_at,
                'server'=>$item_dt->server,
                'tennhanvat'=> $item_dt->uname,
                'sovangtruoc'=> $item_dt->c_truoc,
                'sovangsau'=> $item_dt->c_sau,
                'sovang'=>$item_dt->c_truoc-$item_dt->c_sau
            ]);
        }
        return $arrAll;
    }

    public function muavang(Request $request)
    {
        if($request->sign!="9xvcwstiquvhyxxumj0s"){
            return "Không được phép truy cập";
        }
        // dd($request->get('started_at'));
        //$client=Client::get(['app_client']);


        $datatable= KhachHang::query()->where('status','danhan')
            ->whereHas('order', function ($query) use ($request) {
                $query->where('status',4);
            })
            ->whereRaw('money is not null');

        if ($request->filled('started_at')) {
            $datatable->whereDate('updated_at', $request->get('started_at'));
        }
        else{
            $datatable->whereDate('updated_at', Carbon::today()->subDay(1));
        }

        $datatable=$datatable
            ->selectRaw('updated_at')
            ->selectRaw('server')
            ->selectRaw('uname')
            ->selectRaw('money')
            ->selectRaw('c_truoc')
            ->selectRaw('c_sau')
            ->get();

        $arrAll=[];
        foreach ($datatable as $item_dt) {
            array_push($arrAll,[
                'thoigianbom'=>$item_dt->updated_at,
                'server'=>$item_dt->server,
                'tennhanvat'=> $item_dt->uname,
                'sovangtruoc'=> $item_dt->c_truoc,
                'sovangsau'=> $item_dt->c_sau,
                'sovang'=>$item_dt->money
            ]);
        }
        return $arrAll;
    }


}
