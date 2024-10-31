<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;



class ActivityLogController extends Controller
{

    protected $page_breadcrumbs;

    public function __construct()
    {
        //set permission to function
        $this->middleware("permission:activity-log-list");


        //        demo them action
        $this->page_breadcrumbs[] = [
            'page' => '#',
            'title' => __("Log hệ thống")
        ];
    }

    public function index(Request $request)
    {

        ActivityLog::add($request, 'Truy cập danh sách activity-log');


        if($request->ajax) {

            $datatable= ActivityLog::with('user')->select('activity_log.*');

            if ($request->filled('url')) {
                $url = $request->get('url');
                $datatable->where('url', 'LIKE', '%' .$url. '%');

            }

            if ($request->filled('description')) {

                $description = $request->get('description');
                $datatable->where('description', 'LIKE', '%' .$description. '%');

            }

            if ($request->filled('started_at')) {
                $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
            }
            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
            }

             return \datatables()->eloquent($datatable)
                 ->only([
                     'id',
                     'activity_log.shop_id',
                     'user_id',
                     'user.username',
                     'user.email',
                     'description',
                     'method',
                     'url',
                     'ip',
                     'user_agent',
                     'created_at',
                 ])

                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->toJson();
        }


        return view('admin.activity-log.index')->with('page_breadcrumbs', $this->page_breadcrumbs);

    }


}
