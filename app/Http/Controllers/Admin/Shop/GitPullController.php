<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Server;
use App\Models\Shop;
use App\Models\Shop_Group;
use App\Models\Theme;
use App\Models\ThemeClient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use function Doctrine\Common\Cache\Psr6\get;

class GitPullController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $page_breadcrumbs;
    protected $module;
    public function __construct(Request $request)
    {

        $this->module=$request->segments()[1]??"";

        //set permission to function
        $this->middleware('permission:update-git-client');

        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.shop.index'),
                'title' => __(config('module.shop.title'))
            ];
        }
    }

    public function index(Request $request)
    {
//
        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);


        $allshop = Shop::where('status',1)->get();
        $arrshop = array();

        foreach ($allshop as $value){
            $item = Server::where('id',$value->server_id)->first();
            if ($item){
                array_push($arrshop,$value->id);
            }
        }

        $client = Shop::where('status',1)->whereIn('id',$arrshop)->get();

        if($request->ajax) {

            if ($request->shop_client == 1){

                $datatable= Shop::with('group')->where('status',1)->whereIn('id',$arrshop);
                if ($request->filled('id'))  {
                    $datatable->where(function($q) use($request){
                        $q->orWhere('id', $request->get('id'));
                    });
                }
                if ($request->filled('domain'))  {
                    $datatable->where(function($q) use($request){
                        $q->orWhere('domain', 'LIKE', '%' . $request->get('domain') . '%');
                    });
                }

                if ($request->filled('shop_access'))  {
                    $datatable->where(function($q) use($request){
                        $q->whereIn('id',$request->get('shop_access'));
                    });
                }

                return \datatables()->eloquent($datatable)->whitelist(['id'])
                    ->only([
                        'id',
                        'domain',
                        'title',
                        'group',
                        'server',
                        'ip',
                        'status',
                        'created_at',
                        'update_git_at',
                        'action',

                    ])
                    ->editColumn('created_at', function($data) {
                        return date('d/m/Y H:i:s', strtotime($data->created_at));
                    })
                    ->editColumn('server', function($data) {
                        $server = Server::where('id',$data->server_id)->first();
                        $temp = '';
                        if($server){
                            $temp .= '<a id="dataserver_'.$data->id.'" href="javascript:void(0)">'.$server->ipaddress.'</a>';
                        }
                        return $temp;

                    })
                    ->editColumn('ip', function($data) {
                        $server = Server::where('id',$data->server_id)->first();
                        $temp = '';
                        if($server){
                            $temp .= $server->ipaddress;
                        }
                        return $temp;

                    })
                    ->editColumn('status', function($data) {
                        $temp = '';
                        $temp .= '<span class="switch switch-outline switch-icon switch-success btn-update-stt" data-id="'.$data->id.'">';
                        $temp .= '<label>';
                        if($data->status == 1){
                            $temp .= '<input type="checkbox" checked="checked" name="select">';
                        }
                        else{
                            $temp .= '<input type="checkbox" name="select">';
                        }
                        $temp .= '<span></span>';
                        $temp .= '</label>';
                        $temp .= '</span>';
                        return $temp;
                    })
                    ->addColumn('group', function($row) {
                        if(isset($row->group)){
                            return $row->group->title;
                        }
                        else{
                            return "";
                        }
                    })
                    ->rawColumns(['action', 'status','server'])
                    ->toJson();
            }

            if($request->group_shop == 1) {


                $datatable= Shop_Group::with(['shop' => function ($query) use ($arrshop) {
                    $query->whereIn('id',$arrshop);
                }])->where('status',1);
                if ($request->filled('id_group_shop'))  {
                    $datatable->where(function($q) use($request){
                        $q->orWhere('id', $request->get('id_group_shop'));
                    });
                }
                if ($request->filled('domain_group_shop'))  {
                    $datatable->where(function($q) use($request){
                        $q->orWhere('title', 'LIKE', '%' . $request->get('domain_group_shop') . '%');
                    });
                }

                return \datatables()->eloquent($datatable)

                    ->editColumn('created_at', function($data) {
                        return date('d/m/Y H:i:s', strtotime($data->created_at));
                    })
                    ->editColumn('status', function($data) {
                        if($data->status == 1){
                            $temp = '<span class="badge badge-success">Hoạt động</span>';
                        }
                        else{
                            $temp = '<span class="badge badge-danger">Khóa</span>';
                        }
                        return $temp;
                    })
                    ->editColumn('ip', function($data) {
                        $temp = array();
                        foreach ($data->shop as $key => $item){
                            $server = Server::where('id',$item->server_id)->first();

                            if($server){
                                if ($key > 0){
                                    $temp = $temp.'|'.$server->ipaddress;
                                }else{
                                    $temp = $server->ipaddress;
                                }

                            }
                        }

                        return $temp;

                    })
                    ->addColumn('count',function($row){
                        return $row->shop->count();
                    })
                    ->toJson();
            }

            if($request->theme_id == 1) {

                $datatable = Theme::with('themes')
                    ->with('themes', function($query){
                        $query->with('shop', function($query){
                            $query->where(['status' => 1])->whereNotNull('server_id');
                        });
                        $query->whereHas('shop', function($query){
                            $query->where(['status' => 1])->whereNotNull('server_id');
                        });
                    });

                return \datatables()->eloquent($datatable)

                    ->editColumn('created_at', function($data) {
                        return date('d/m/Y H:i:s', strtotime($data->created_at));
                    })
                    ->editColumn('status', function($data) {
                        if($data->status == 1){
                            $temp = '<span class="badge badge-success">Hoạt động</span>';
                        }
                        else{
                            $temp = '<span class="badge badge-danger">Khóa</span>';
                        }
                        return $temp;
                    })
                    ->editColumn('ip', function($data) {
                        $temp = array();

                        foreach ($data->themes as $key => $item){

                            if (isset($item->shop->server_id)){
                                $server = Server::where('id',$item->shop->server_id)->first();

                                if($server){
                                    if ($key > 0){
                                        $temp = $temp.'|'.$server->ipaddress;
                                    }else{
                                        $temp = $server->ipaddress;
                                    }

                                }
                            }

                        }

                        return $temp;

                    })
                    ->addColumn('count',function($row){
                        return $row->themes->count();
                    })
                    ->toJson();
            }
        }

        $themes = Theme::with('themes')
            ->with('themes', function($query){
                $query->with('shop', function($query){
                    $query->where(['status' => 1]);
                });
                $query->whereHas('shop', function($query){
                    $query->where(['status' => 1]);
                });
            })
            ->get();


        return view('admin.shop.git.index')
            ->with('module', $this->module)
            ->with('client', $client)
            ->with('themes', $themes)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }


    public function portShop(Request $request){

        ini_set('max_execution_time', 2400); //20 minutes

        $group_shop = $request->group_shop;

        if ($group_shop == 0){
            $r_ip = $request->r_ip;
            $r_id = $request->r_id;
            $r_domain = $request->r_domain;
            $arrid = explode("|",$r_id);

            $r_status = array();
            $r_message = array();
            $r_data = array();
            $r_ketqua = array();
            $arr_id_success = array();

            foreach ($arrid as $item){

                $shop = Shop::with('group')->where('status',1)->where('id',$item)->first();

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,fr-FR;q=0.6,fr;q=0.5',
                    'Cache-Control: max-age=0',
                    'Connection: keep-alive',
                    'Upgrade-Insecure-Requests: 1',
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36'

                ));

                $url = "https://" . $shop->domain . "/api/git-pull";

                $data['token'] = config('app.app_github_token_client');
                $data['brand'] = config('app.app_github_brand_client');

                if(is_array($data)){
                    $dataPost = http_build_query($data);
                }else{
                    $dataPost = $data;
                }
                $url = $url.'?'.$dataPost;
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_COOKIEFILE, "");
                curl_setopt($ch, CURLOPT_COOKIEJAR, "");

                $ketqua = curl_exec($ch);
                $ketqua = json_decode($ketqua);

                curl_close($ch);
                if (isset($ch)){
                    if (isset($ketqua->status)){
                        if ($ketqua->status == 0){
                            array_push($r_status,0);
                            array_push($r_message,'cannot open .git/FETCH_HEAD: Permission denied');
                            array_push($r_ketqua,'cannot open .git/FETCH_HEAD: Permission denied');
                        }elseif ($ketqua->status == 1){

                            array_push($r_status,1);
                            if (strlen(strstr($ketqua->data, 'Already up to date')) > 0) {
                                array_push($r_message,'FETCH_HEAD Already up to date');
                                array_push($r_data,$ketqua->data);
                                array_push($r_ketqua,$ketqua->data);

                            }else{
                                array_push($r_message,'Cập nhật thành công');
                                array_push($r_data,$ketqua->data);
                                array_push($r_ketqua,$ketqua->data);
                            }
                            array_push($arr_id_success,$shop->id);
                            $shop->update_git_at = Carbon::now();
                            $shop->save();
                        }else{
                            array_push($r_status,0);
                            array_push($r_message,'cannot open .git/FETCH_HEAD: Permission denied');
                            array_push($r_ketqua,'cannot open .git/FETCH_HEAD: Permission denied');
                        }
                    }else{
                        array_push($r_status,0);
                        array_push($r_message,'cannot open .git/FETCH_HEAD: Permission denied');
                        array_push($r_ketqua,'cannot open .git/FETCH_HEAD: Permission denied');
                    }
                }else{
                    array_push($r_status,0);
                    array_push($r_message,'cannot open .git/FETCH_HEAD: Permission denied');
                    array_push($r_ketqua,'cannot open .git/FETCH_HEAD: Permission denied');
                }

            }

            ActivityLog::add($request, 'Auto Deploy Github trên các shop'.json_encode($arr_id_success));

            return response()->json([
                'message' => __('Gửi dữ liệu thành công'),
                'status' => 1,
                'r_status' => $r_status,
                'r_domain' => $r_domain,
                'r_message' => $r_message,
                'r_ketqua' => $r_ketqua,
                'r_ip' => $r_ip,
            ], 200);

        }elseif ($group_shop == 1){
            $r_group = $request->r_group;
            $r_domain =$request->r_domain;
            $r_ip = $request->r_ip;
            $r_id = $request->r_id;
            $r_gid = $request->r_gid;
            $r_gid = explode(",",$r_gid);
            $r_status = array();
            $r_message = array();
            $r_data = array();
            $r_ketqua = array();
            $arr_id_success = array();
            foreach ($r_gid as $val){
                $shopid = explode("|",$val);
                foreach ($shopid as $item){
                    $shop = Shop::with('group')->where('status',1)->where('id',$item)->first();

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,fr-FR;q=0.6,fr;q=0.5',
                        'Cache-Control: max-age=0',
                        'Connection: keep-alive',
                        'Upgrade-Insecure-Requests: 1',
                        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36'

                    ));
                    $url = "https://" . $shop->domain . "/api/git-pull";

                    $data['token'] = config('app.app_github_token_client');
                    $data['brand'] = config('app.app_github_brand_client');

                    if(is_array($data)){
                        $dataPost = http_build_query($data);
                    }else{
                        $dataPost = $data;
                    }
                    $url = $url.'?'.$dataPost;
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_COOKIEFILE, "");
                    curl_setopt($ch, CURLOPT_COOKIEJAR, "");

                    $ketqua = curl_exec($ch);
                    $ketqua = json_decode($ketqua);

                    curl_close($ch);
                    if (isset($ch)){
                        if (isset($ketqua->status)){
                            if ($ketqua->status == 0){
                                array_push($r_status,0);
                                array_push($r_message,'cannot open .git/FETCH_HEAD: Permission denied');
                                array_push($r_ketqua,'cannot open .git/FETCH_HEAD: Permission denied');
                            }elseif ($ketqua->status == 1){

                                array_push($r_status,1);
                                if (strlen(strstr($ketqua->data, 'Already up to date')) > 0) {
                                    array_push($r_message,'FETCH_HEAD Already up to date');
                                    array_push($r_data,$ketqua->data);
                                    array_push($r_ketqua,$ketqua->data);
                                }else{
                                    array_push($r_message,'Cập nhật thành công');
                                    array_push($r_data,$ketqua->data);
                                    array_push($r_ketqua,$ketqua->data);
                                }

                                array_push($arr_id_success,$shop->id);
                                $shop->update_git_at = Carbon::now();
                                $shop->save();
                            }else{
                                array_push($r_status,0);
                                array_push($r_message,'cannot open .git/FETCH_HEAD: Permission denied');
                                array_push($r_ketqua,'cannot open .git/FETCH_HEAD: Permission denied');
                            }
                        }else{
                            array_push($r_status,0);
                            array_push($r_message,'cannot open .git/FETCH_HEAD: Permission denied');
                            array_push($r_ketqua,'cannot open .git/FETCH_HEAD: Permission denied');
                        }
                    }else{
                        array_push($r_status,0);
                        array_push($r_message,'cannot open .git/FETCH_HEAD: Permission denied');
                        array_push($r_ketqua,'cannot open .git/FETCH_HEAD: Permission denied');
                    }
                }
            }

            ActivityLog::add($request, 'Auto Deploy Github thành công trên các shop'.json_encode($arr_id_success));

            return response()->json([
                'message' => __('Gửi dữ liệu thành công'),
                'status' => 1,
                'r_group' => $r_group,
                'r_domain' => $r_domain,
                'r_status' => $r_status,
                'r_message' => $r_message,
                'r_ketqua' => $r_ketqua,
                'r_ip' => $r_ip,
            ], 200);
        }


    }
}
