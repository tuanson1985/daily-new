<?php

namespace App\Http\Controllers\Admin\Minigame\Module;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Item;
use App\Models\Order;
use Carbon\Carbon;
use Html;
use Illuminate\Http\Request;
use DB;


class StatiticsController extends Controller
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
        $datatable = Group::where('module', $this->moduleCategory)->where('status', 1);

        if (session('shop_id')) {
            $datatable->where('shop_id',session('shop_id'));
        }

        $datatable = $datatable->get();
        return view('admin.minigame.module.statitics.index')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('datatable', $datatable);
    }
}
