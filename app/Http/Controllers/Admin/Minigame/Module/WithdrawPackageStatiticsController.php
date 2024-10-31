<?php

namespace App\Http\Controllers\Admin\Minigame\Module;

use App\Http\Controllers\Controller;
use App\Library\HelperPermisionShopMinigame;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Item;
use App\Models\Order;
use Auth;
use Carbon\Carbon;
use Html;
use Illuminate\Http\Request;
use DB;


class WithdrawPackageStatiticsController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;
    public function __construct(Request $request)
    {


        $this->module=$request->segments()[1]??"";
        $this->moduleCategory=explode("-", $this->module)[0].'-category';

        //set permission to function
        $this->middleware('permission:'. $this->module);

        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'.index'),
                'title' => __(config('module.minigame.'.$this->module.'.title'))
            ];
        }
    }

    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập thống kê '.$this->module);

        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        $datatable = Item::where('module', 'gametype')
            ->with(array('packageorder' => function ($query) use ($arr_permission) {
                $query->where(function($q){
                    $q->orWhere('module','withdraw-item');
                    $q->orWhere('module','withdraw-service-item');
                });
                $query->whereIn('shop_id',$arr_permission);
                if (session('shop_id')) {
                    $query->where('shop_id',session('shop_id'));
                }
            }))
            ->with(array('gametypeorder' => function ($query) use ($arr_permission) {
                $query->where(function($q){
                    $q->orWhere('module','withdraw-item');
                    $q->orWhere('module','withdraw-service-item');
                });
                $query->whereIn('shop_id',$arr_permission);
                if (session('shop_id')) {
                    $query->where('shop_id',session('shop_id'));
                }
            }))
            ->where('status', 1);

        $datatable = $datatable->get();
        return view('admin.minigame.module.withdrawpackagestatitics.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('datatable', $datatable);
    }
}
