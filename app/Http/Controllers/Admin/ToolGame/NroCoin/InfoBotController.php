<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 20/12/2018
 * Time: 14:43 CH
 */


namespace App\Http\Controllers\Admin\ToolGame\NroCoin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\Bot;
use App\Models\Bot_UserNap;
use App\Models\Item;
use App\Models\KhachHang;
use App\Models\Shop;
use App\Models\SubItem;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Log;
use Session;
use App\Library\Helpers;


class InfoBotController extends Controller
{
    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;

    public function __construct()
    {

        //set permission to function
        $this->module = 'nrocoin-info-bot';

        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');
        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);


        if ($this->module != "") {
            $this->page_breadcrumbs[] = [
                'page' => route('admin.nrocoin-info-bot.index'),
                'title' => __('Bán vàng NRO - Bot')
            ];
        }
    }


    public function index(Request $request)
    {
        // dd(KEYDECRYPT);
        if ($request->ajax()) {

            $model = Bot::orderBy('ver', 'asc')->orderBy('server', 'asc');
            if ($request->filled('id')) {
                $model->where('id', 'LIKE', '%' . $request->get('id') . '%');
                $model->orWhere('idkey', 'LIKE', '%' . $request->get('id') . '%');
            }
            if ($request->filled('title')) {
                $model->where('title', 'LIKE', '%' . $request->get('title') . '%');
            }

            if ($request->filled('status')) {
                $model->where('active', 'LIKE', '%' . $request->get('status') . '%');
            }
            if ($request->filled('started_at')) {

                $model->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $model->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }

            return $datatable = \datatables()->eloquent($model)
                //->editColumn('coin', function ($row) {
                //    return number_format($row->coin);
                //})
                ->editColumn('updated_at', function ($row) {
                    return date('d/m/Y H:i:s', strtotime($row->updated_at));
                })
                ->editColumn('status', function ($row) {

                    if( (time()-strtotime($row->updated_at)) >30 ){
                        return "0";
                    }
                    else{
                        return "1";
                    }
                })
                ->editColumn('pass', function ($row) use ($request) {
                    if($request->filled('pass') != config('module.toolgame.nro.keydecrypt') || $request->filled('pass')==''){
                        return Helpers::Encrypt($row->pass,config('module.toolgame.nro.keydecrypt'));
                    }
                    else{
                        return $row->pass;
                    }

                })
                ->addColumn('action', function ($row) {
                    $temp = "<a href=\"" . route('admin.nrocoin-info-bot.edit', $row->id) . "\"  rel=\"$row->id\" class=\"m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp .= "<a  rel=\"$row->id\" class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })->toJson();

        }
        //SET BACK URL
        return view('admin.toolgame.nrocoin.infobot.index')
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }



    public function getTotalSt(Request $request){
        $total = Bot::orderBy('ver', 'asc')->orderBy('server', 'asc');
        if ($request->filled('id')) {
            //Total
            $total->where('id', 'LIKE', '%' . $request->get('id') . '%');
            $total->orWhere('idkey', 'LIKE', '%' . $request->get('id') . '%');
        }
        if ($request->filled('title')) {
            //Total
            $total->where('title', 'LIKE', '%' . $request->get('title') . '%');
        }

        if ($request->filled('status')) {
            //Total
            $total->where('active', 'LIKE', '%' . $request->get('status') . '%');
        }
        if ($request->filled('server')) {
            //Total
            $total->where('server', 'LIKE', '%' . $request->get('server') . '%');
        }
        if ($request->filled('started_at')) {
            //Total
            $total->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
        }
        if ($request->filled('ended_at')) {
            //Total
            $total->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
        }
        $total = $total->select(
            DB::raw('COUNT(id) as totalNick'),
            DB::raw('SUM(thoivang) as totalTicketGold'),
            DB::raw('SUM(coin) as totalMoneyIngame')
        )->get();

        return response()->json(['data'=>$total,'status'=>1]);
    }


    /**
     * Show the form for creating a new newscategory
     *
     * @return Response
     */
    public function create()
    {
        $this->page_breadcrumbs[] = [
            'page' => '#',
            'title' => __("Thêm mới")
        ];

        return view('admin.toolgame.nrocoin.infobot.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    /**
     * Store a newly created newscategory in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {

        $input=$request->all();
        $input['app_client']=rtrim($request->app_client,',').',';
        $item=Bot::create($input);

        //active log active
        ActivityLog::add($request, 'Thêm mới thành công#'.$item->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Thêm mới thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }
    }

    /**
     * Display the specified newscategory.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $datatable = Bot::findOrFail($id);
        return view('admin.toolgame.nrocoin.infobot.show', compact('datatable'));
    }

    /**
     * Show the form for editing the specified newscategory.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];

        $data = Bot::findOrFail($id);
        return view('admin.toolgame.nrocoin.infobot.create_edit', compact('data'))
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);

    }

    /**
     * Update the specified newscategory in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(Request $request,$id)
    {

        $item = Bot::findOrFail($id);
        $item->app_client=rtrim($request->app_client,',').',';
        if($request->pass=="" || $request->pass==null){
            $input = $request->except('pass');
        }else{
            $input = $request->all();
        }

        $item->update($input);

        //active log active
        ActivityLog::add($request, 'Cập nhật bot thành công#'.$item->id);

        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Cập nhật thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
        }
    }

    /**
     * Remove the specified newscategory from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(Request $request)
    {

        $input=explode(',',$request->id);

        Bot::whereIn('id',$input)->delete();

        ActivityLog::add($request, 'Xóa bot thành công#'.$request->id);
        return redirect()->back()->with('success',__('Xóa bot thành công !'));
    }


}
