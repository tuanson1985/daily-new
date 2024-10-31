<?php

namespace App\Http\Controllers\Admin\Cloudflare;

use App\Exports\ExportData;
use App\Http\Controllers\Controller;
use Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\ActivityLog;
use App\Models\Item;
use Illuminate\Pagination\LengthAwarePaginator;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;
    public function __construct(Request $request)
    {
        $this->module='cloudflare';
        $this->moduleCategory=null;
        $this->middleware('permission:'. $this->module.'-list');
        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate']]);
        $this->middleware('permission:'. $this->module.'-show', ['only' => ['show']]);
        $this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);
        $this->page_breadcrumbs[] = [
            'page' => route('admin.'.$this->module.'.index'),
            'title' => __(config('module.'.$this->module.'.title'))
        ];
    }
    public function index(Request $request)
    {
        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);
        if($request->ajax) {
            $datatable= Item::where('module',$this->module);
            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', 'LIKE', '%' . $request->get('id') . '%');
                });
            }
            if ($request->filled('title'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
                });
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
                    'title',
                    'params',
                    'action'
                ])
                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->editColumn('params', function($data) {
                    $temp = '';
                    if($data->params){
                        if(isset($data->params->authorization)){
                            $authorization = $data->params->authorization;
                            $authorization = substr($authorization, 0, -20);
                            $mh = "********************";
                            $authorization = $authorization.$mh;
                            $temp .= "- authorization: ".$authorization;
                            $temp .= '<br/>';
                        }
                        else{
                            $temp .= "- authorization: Chưa thêm";
                            $temp .= '<br/>';
                        }
                        if(isset($data->params->auth_key)){
                            $auth_key = $data->params->authorization;
                            $auth_key = substr($auth_key, 0, -20);
                            $mh = "********************";
                            $auth_key = $auth_key.$mh;
                            $temp .= "auth_key: ".$auth_key;
                            $temp .= '<br/>';
                        }
                        else{
                            $temp .= "- auth_key: Chưa thêm";
                            $temp .= '<br/>';
                        }
                        if(isset($data->params->auth_email)){
                            $temp .= "- auth_email: ".$data->params->auth_email;
                            $temp .= '<br/>';
                        }
                        else{
                            $temp .= "- auth_email: Chưa thêm";
                            $temp .= '<br/>';
                        }
                    }
                    else{
                        $temp .= 'Trống';
                    }
                    return $temp;
                })
                ->addColumn('action', function($row) {
                    $temp= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.= "<a href=\"".route('admin.'.$this->module.'.show',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Xem danh sách domain\"><i class=\"flaticon-eye\"></i></a>";
                    // $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })
                ->toJson();
        }
        return view('admin.'.$this->module.'.item.index')
        ->with('module', $this->module)
        ->with('page_breadcrumbs', $this->page_breadcrumbs);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];
        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.'.$this->module.'.item.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'title'=>'required',
            'authorization'=>'required',
            'auth_key'=>'required',
            'auth_email'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
            'authorization.required' => __('Vui lòng nhập authorization'),
            'auth_key.required' => __('Vui lòng nhập auth-key'),
            'auth_email.required' => __('Vui lòng nhập auth-email'),
        ]);
        $input = $request->except('authorization', 'auth_email', 'auth_email','created_at');
        $input['module'] = $this->module;
        $params = array();
        if($request->authorization){
            $params['authorization'] = $request->authorization;
        }
        if($request->auth_key){
            $params['auth_key'] = $request->auth_key;
        }
        if($request->auth_email){
            $params['auth_email'] = $request->auth_email;
        }
        $input['params'] = $params;
        $data=Item::create($input);
        ActivityLog::add($request, 'Tạo mới thành công '.$this->module.' #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        ini_set('max_execution_time', 2400); //20 minutes

        $data = Item::findOrFail($id);

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __($data->title)
        ];
        $authorization = $data->params->authorization;//Global API Key
        $auth_email = $data->params->auth_email;//Global Email

        $page = 1;
        $per_page = 500;
        if ($request->get('page')){
            $page = $request->get('page');
        }

        $url = 'https://api.cloudflare.com/client/v4/zones/?page='.$page.'&per_page='.$per_page.'&match=all';

        if ($request->get('type')){
            $url = 'https://api.cloudflare.com/client/v4/zones?page='.$page.'&per_page='.$per_page.'&match=all&type='.$request->get('type');
        }

        if ($request->get('per_page')){
            $per_page = $request->get('per_page');
            $url = 'https://api.cloudflare.com/client/v4/zones?page='.$page.'&per_page='.$per_page.'&match=all&type='.$request->get('type');
        }

        $headers = array();
        $headers[] = "X-Auth-Key: $authorization";
        $headers[] = "X-Auth-Email: $auth_email";
        $headers[] = 'Content-Type: application/json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec ($ch);
        $result = json_decode($result);

        $status = 0;
        $count = 0;
        $items = null;

        if (isset($result) && $result->success == true){

            foreach ($result->result as $sult){

                $url_detail = 'https://api.cloudflare.com/client/v4/zones/'.$sult->id.'/settings/ssl';
                $headers = array();
                $headers[] = "X-Auth-Key: $authorization";
                $headers[] = "X-Auth-Email: $auth_email";
                $headers[] = 'Content-Type: application/json';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$url_detail);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $result_detail = curl_exec ($ch);
                $result_detail = json_decode($result_detail);

                $result_detail = $result_detail->result;
                $sult->type = $result_detail->value;

                $url_recove = 'https://api.cloudflare.com/client/v4/zones/'.$sult->id.'/dns_records';
                $headers = array();
                $headers[] = "X-Auth-Key: $authorization";
                $headers[] = "X-Auth-Email: $auth_email";
                $headers[] = 'Content-Type: application/json';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$url_recove);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $result_recove = curl_exec ($ch);
                $result_recove = json_decode($result_recove);

                $result_recove = $result_recove->result;

                $sult->permissions = $result_recove;

            }

            $count = $result->result_info->total_count;
            $items = new LengthAwarePaginator($result->result,$count,$result->result_info->per_page,$result->result_info->page,$result->result);
            $items->setPath($request->url());
            $status = 1;
        }

        $url = 'http://'.\Request::server("HTTP_HOST").'/admin/cloudflare/'.$id.'?page='.$page.'&per_page='.$per_page.'&match=all';

        return view('admin.'.$this->module.'.item.show')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('items', $items)
            ->with('page', $page)
            ->with('url', $url)
            ->with('status', $status)
            ->with('count', $count)
            ->with('data', $data);
    }
    function getIpCF($headers,$id){
        $url = "https://api.cloudflare.com/client/v4/zones/".$id."/dns_records";
        $data = array();
        if(is_array($data)){
            $dataPost = http_build_query($data);
        }else{
            $dataPost = $data;
        }
        $url = $url.'?'.$dataPost;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
        $resultRaw = curl_exec($ch);
        $result= json_decode($resultRaw);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $resultChange = new \stdClass();
        if(isset($result) && isset($result->success) && $result->success == true && $httpcode === 200){
            $resultChange->status = 1;
            $resultChange->data = $result->result;
            $resultChange->count = $result->result_info->total_count;
            $resultChange->message = "OK";
        }
        else{
            $resultChange->status = 0;
            $resultChange->message = "Messages: Gọi API lấy IP thất bại. HTTP: ".$httpcode;
        }
        return $resultChange;
    }
    function getInfoCloudflare($headers){
        $url = "https://api.cloudflare.com/client/v4/zones";
        $data = array();
        $data['page'] = 1;
        $data['per_page'] = 1000;
        $data['order'] = "status";
        $data['direction'] = "desc";
        $data['match'] = "all";
        if(is_array($data)){
            $dataPost = http_build_query($data);
        }else{
            $dataPost = $data;
        }
        $url = $url.'?'.$dataPost;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
        $resultRaw = curl_exec($ch);
        $result= json_decode($resultRaw);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $resultChange = new \stdClass();
        if(isset($result) && isset($result->success) && $result->success == true && $httpcode === 200){
            $resultChange->status = 1;
            $resultChange->data = $result->result;
            $resultChange->count = $result->result_info->total_count;
            $resultChange->message = "OK";
        }
        else{
            $resultChange->status = 0;
            $resultChange->message = "Messages: Gọi API lấy thông tin thất bại. HTTP: ".$httpcode;
        }
        return $resultChange;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];
        $data = Item::findOrFail($id);
        return view('admin.'.$this->module.'.item.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'title'=>'required',
            'authorization'=>'required',
            'auth_key'=>'required',
            'auth_email'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
            'authorization.required' => __('Vui lòng nhập authorization'),
            'auth_key.required' => __('Vui lòng nhập auth-key'),
            'auth_email.required' => __('Vui lòng nhập auth-email'),
        ]);
        $data =  Item::findOrFail($id);
        $input = $request->except('authorization', 'auth_email', 'auth_email','created_at');
        $input['module'] = $this->module;
        $params = array();
        if($request->authorization){
            $params['authorization'] = $request->authorization;
        }
        if($request->auth_key){
            $params['auth_key'] = $request->auth_key;
        }
        if($request->auth_email){
            $params['auth_email'] = $request->auth_email;
        }
        $input['params'] = $params;
        $data->update($input);
        ActivityLog::add($request, 'Cập nhật thành công '.$this->module.' #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function exPostExcel(Request $request){

        ini_set('max_execution_time', 2400); //20 minutes

        $id = $request->get('id');

        $data = Item::findOrFail($id);

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __($data->title)
        ];
        $authorization = $data->params->authorization;//Global API Key
        $auth_email = $data->params->auth_email;//Global Email

        $page = 1;
        $per_page = 500;
        if ($request->get('page')){
            $page = $request->get('page');
        }

        $url = 'https://api.cloudflare.com/client/v4/zones/?page='.$page.'&per_page='.$per_page.'&match=all';

        if ($request->get('type')){
            $url = 'https://api.cloudflare.com/client/v4/zones?page='.$page.'&per_page='.$per_page.'&match=all&type='.$request->get('type');
        }

        if ($request->get('per_page')){
            $per_page = $request->get('per_page');
            $url = 'https://api.cloudflare.com/client/v4/zones?page='.$page.'&per_page='.$per_page.'&match=all&type='.$request->get('type');
        }

        $headers = array();
        $headers[] = "X-Auth-Key: $authorization";
        $headers[] = "X-Auth-Email: $auth_email";
        $headers[] = 'Content-Type: application/json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec ($ch);
        $result = json_decode($result);

        $status = 0;
        $count = 0;
        $items = null;

        if (isset($result) && $result->success == true){

            foreach ($result->result as $sult){

                $url_detail = 'https://api.cloudflare.com/client/v4/zones/'.$sult->id.'/settings/ssl';
                $headers = array();
                $headers[] = "X-Auth-Key: $authorization";
                $headers[] = "X-Auth-Email: $auth_email";
                $headers[] = 'Content-Type: application/json';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$url_detail);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $result_detail = curl_exec ($ch);
                $result_detail = json_decode($result_detail);

                $result_detail = $result_detail->result;
                $sult->type = $result_detail->value;

                $url_recove = 'https://api.cloudflare.com/client/v4/zones/'.$sult->id.'/dns_records';
                $headers = array();
                $headers[] = "X-Auth-Key: $authorization";
                $headers[] = "X-Auth-Email: $auth_email";
                $headers[] = 'Content-Type: application/json';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$url_recove);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $result_recove = curl_exec ($ch);
                $result_recove = json_decode($result_recove);

                $result_recove = $result_recove->result;

                $sult->permissions = $result_recove;

            }

            $count = $result->result_info->total_count;
            $items = new LengthAwarePaginator($result->result,$count,$result->result_info->per_page,$result->result_info->page,$result->result);
            $items->setPath($request->url());
            $status = 1;
        }

        $data = [
            'data' => $items,
        ];

        return Excel::download(new ExportData($data,view('admin.cloudflare.item.export_excel')), 'Clofe ' . time() . '.xlsx');

    }
}
