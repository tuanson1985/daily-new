<?php

namespace App\Http\Controllers\Admin\ThemeClient;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\ItemConfig;
use App\Models\Setting;
use App\Models\ThemeAttribute;
use App\Models\ThemeAttributeValue;
use App\Models\Shop;
use App\Models\ThemeClient;
use Html;
use Illuminate\Http\Request;
use App\Models\Theme;
use Carbon\Carbon;
use DB;
use App\Models\ActivityLog;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\Auth;
use Validator;
use function GuzzleHttp\Promise\all;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $page_breadcrumbs;
    protected $module;
    protected $moduletheme;
    protected $moduleCategory;

    public function __construct(Request $request)
    {

        $this->module='theme-client';
        $this->moduleCategory=null;
        //set permission to function
//        $this->middleware('permission:'. $this->module.'-list');
//        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate']]);
//        $this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);



        $this->page_breadcrumbs[] = [
            'page' => route('admin.theme.index'),
            'title' => "Danh sách theme"
        ];
    }

    public function index(Request $request)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cấu hình theme cho client")
        ];

        $theme = Theme::where('status',1)->get();
        $shop = Shop::where('id',session('shop_id'))->first();
        $themeclient = null;

        $key_theme = null;
        if (session('shop_id')){
            $themeclient = ThemeClient::where('client_id',$shop->id)->where('client_name',$shop->domain)->first();
            $c_theme = null;

            if (isset($themeclient)){
                $c_theme = Theme::where('id',$themeclient->theme_id)->where('status',1)->first();
                $key_theme = $c_theme->key;
            }
//            return $themeclient;
        }

        $module = config('module.theme-page.key');

        $pagesbuild = config('module.theme.pages');

        $key = 'sys_theme_ver_page_build';

        $setting_build = null;
        $datatable = null;

        $datatable = Group::where('module','=',$module)->where('idkey',$key_theme)->orderBy('status','desc')->orderBy('order');

        if(session('shop_id')){
            $datatable->where('shop_id',session('shop_id'));
        }

        $datatable=$datatable->get();

//        return $datatable;
        $data=$this->getHTMLCategory($datatable);

        $client = null;

        if(Auth::user()->account_type == 1){
            $client = Shop::orderBy('id','desc');
            $shop_access_user = Auth::user()->shop_access;
            if(isset($shop_access_user) && $shop_access_user !== "all"){
                $shop_access_user = json_decode($shop_access_user);
                $client = $client->whereIn('id',$shop_access_user);
            }
            $client = $client->select('id','domain','title')->get();
        }


//        Danh sách dịch vụ.
        $services = ItemConfig::with(array('items' => function ($query) {

            $query->with(array('groups' => function ($q) {
                $q->select('groups.id', 'title', 'slug');
            }));

        }))->where('module', config('module.service.key'))

            ->where('status', '=', 1);
        if(session('shop_id')){
            $services = $services->where('shop_id', session('shop_id'));
        }

        $services = $services->orderByRaw('ISNULL(`order`), `order` ASC')->orderBy('id','desc')->get();

//        Danh sách nick.
        $input['module'] = "acc_category";
        $input['data'] = "category_list";
        $input['shop_id'] = null;
        if(session('shop_id')){
            $input['shop_id'] = session('shop_id');
        }

        if (empty($input['module'])) {
            $input['module'] = 'acc_provider';
        }
        $input['shop_group_id'] = null;
        $nicks = Group::where('module', $input['module']??'acc_provider')->orderBy('order')->with(['childs' => function($query) use($input){
            if (($input['module']??'acc_provider') == 'acc_provider') {
                $query->withCount(['items' => function($query) use($input){
                    $query->whereHas('access_category', function($query){
                        $query->where('active', 1);
                    })->where(function($query) use($input){
                        $query->whereHas('access_shops', function($query) use($input){
                            $query->where('shop.id', $input['shop_id']);
                        })->orWhereHas('author', function($query) use($input){
                            $query->where('shop_access', 'all');
                        })->orWhereHas('access_shop_groups', function($query) use($input){
                            $query->where('shop_group.id', $input['shop_group_id']);
                        });
                    })->where(['status' => 1]);
                }])->whereHas('custom', function($query) use($input){
                    $query->where(['groups_shops.shop_id' => $input['shop_id'], 'status' => 1]);
                })->with(['custom' => function($query) use($input){
                    $query->where('shop_id', $input['shop_id']);
                }]);
            }
        }])->where('status', 1);
        if (($input['module']??'acc_provider') == 'acc_category') {
            $nicks->withCount(['items' => function($query) use($input){
                $query->whereHas('access_category', function($query){
                    $query->where('active', 1);
                })->where(function($query) use($input){
                    $query->whereHas('access_shops', function($query) use($input){
                        $query->where('shop.id', $input['shop_id']);
                    })->orWhereHas('author', function($query) use($input){
                        $query->where('shop_access', 'all');
                    })->orWhereHas('access_shop_groups', function($query) use($input){
                        $query->where('shop_group.id', $input['shop_group_id']);
                    });
                })->where(['status' => 1]);
            }])->whereHas('custom', function($query) use($input){
                $query->where(['shop_id' => $input['shop_id'], 'status' => 1]);
            })->with(['custom' => function($query) use($input){
                $query->where('shop_id', $input['shop_id']);
            }]);
        }
        $nicks = $nicks->get();
        if ($input['module'] == 'acc_provider') {
            $nicks = $nicks->map(function($value) {
                $value->childss = array_values($value->childs->sortBy('custom.order')->toArray());
                return $value;
            })->toArray();
            foreach ($nicks as $key => $value) {
                $nicks[$key]['childs'] = $value['childss'];
                unset($nicks[$key]['childss']);
            }
        }elseif ($input['module'] == 'acc_category') {

            foreach ($nicks as $key => $value) {
                $value->lm_auto = 0;
                if ( $value->position == 'lienminh' ) {
                    $value->lm_auto = 1;
                }
                if (!empty($value->params->price)) {
                    $value->price = \App\Library\HelpMoneyPercent::shop_price($value->params->price);
                }
                if (!empty($value->params->price_old)) {
                    $value->price_old = \App\Library\HelpMoneyPercent::shop_price($value->params->price_old);
                }
            }

            $nicks = array_values($nicks->sortBy('custom.order')->toArray());
        }

        return view('admin.'.$this->module.'.index')
            ->with('module', $this->module)
            ->with('theme',$theme)
            ->with('data', $data)
            ->with('key_theme', $key_theme)
            ->with('client', $client)
            ->with('services', $services)
            ->with('nicks', $nicks)
            ->with('pagesbuild',$pagesbuild)
            ->with('setting_build',$datatable)
            ->with('themeclient',$themeclient)
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
        $shop_id = \Session::has('shop_id') ? \Session::get('shop_id') : 0;
        $client = Shop::where('id',$shop_id)->first();

        if(!$client){
            return response()->json(array('status' => "ERRORS",'msg'=>"Vui lòng chọn shop cần setup theme!"), 200);
        }




        $params = $request->except([
            '_method',
            '_token',
            'theme_id',
        ]);

        $theme = ThemeClient::where('client_id',$client->id)->where('client_name',$client->domain)->first();

        if (isset($theme)){

            $g_theme = Theme::where('id',$theme->theme_id)->where('status',1)->first();
            if (isset($theme->param_attribute)){

                $theme->param_attribute = json_encode($params, JSON_UNESCAPED_UNICODE);
                $theme->theme_id = $request->theme_id;
                $theme->order = 1;
                $theme->status = 1;
                $theme->save();
            }else{

                $param_attribute = [
                    'sys_store_card_vers' => 'sys_store_card_vers_1',
                    'sys_store_card_vers_value' => 'Hiển thị mua thẻ ver1',
                    'sys_theme_ver' => 'sys_theme_ver3.0',
                    'sys_theme_ver_value'=> 'Shop Brand chung',
                ];

                $theme->param_attribute = json_encode($param_attribute, JSON_UNESCAPED_UNICODE);
                $theme->theme_id = $request->theme_id;
                $theme->order = 1;
                $theme->status = 1;
                $theme->save();
            }

        }else{

            $param_attribute = [
                'sys_store_card_vers' => 'sys_store_card_vers_1',
                'sys_store_card_vers_value' => 'Hiển thị mua thẻ ver1',
                'sys_theme_ver' => 'sys_theme_ver3.0',
                'sys_theme_ver_value'=> 'Shop Brand chung',
            ];
            $input = [
                'client_name' => $client->domain,
                'client_id' => $client->id,
                'param_attribute' => json_encode($param_attribute, JSON_UNESCAPED_UNICODE),
                'theme_id' => $request->theme_id,
                'order' => 1,
                'status'=> 1,
                'created_at' => Carbon::now()
            ];

            ThemeClient::create($input);

        }

        $key = 'sys_theme_ver_page_build';

        $module = config('module.theme-page.key');

        $themeclient = ThemeClient::where('client_id',session('shop_id'))->first();

        $c_theme = Theme::where('id',$themeclient->theme_id)->where('status',1)->first();
        $key_theme = $c_theme->key;

        $group = Group::where('module','=',$module)->where('shop_id',session('shop_id'))->where('idkey',$key_theme)->where('status',1)->orderBy('order')->get();

        if (isset($group) && count($group)){

            $slugselect = null;
            $titleselect = null;
            $itemselect = null;

            foreach ($group as $val){
                if (isset($slugselect)) {
                    $slugselect = $slugselect.'|';
                }

                $slugselect = $slugselect.$val->slug;

                if (isset($titleselect)) {
                    $titleselect = $titleselect.'|';
                }

                $titleselect = $titleselect.$val->title;
            }

            if (isset($titleselect) && isset($slugselect)){
                $itemselect = $titleselect.','.$slugselect;
            }

            if (isset($itemselect)){

                Setting::add($key, $itemselect, Setting::getDataType($key));
            }
        }else{
            Setting::remove($key);
        }

        ActivityLog::add($request, 'Cập nhật thành công theme cho Shop #'.$request->client_name);
        return response()->json(array('status' => "SUCCESS",'msg'=>"Cập nhật thành công"), 200);

    }

    public function  getAttribute(Request $request){
        $idTheme = $request->idTheme;
        $idClient = session("shop_id");
        $htmlAttribute = "";
        if($idTheme <= 0){
            return response()->json(array('status' => "ERRORS",'msg'=>"ID Theme không tồn tại. Vui lòng thử lại","htmlAttribute"=>$htmlAttribute), 200);
        }
        $theme = Theme::where('status',1)->where('id',$idTheme)->first();
        if(!isset($theme)){
            return response()->json(array('status' => "ERRORS",'msg'=>"Theme bạn chọn đã bị khóa hoặc không tồn tại, Vui lòng thử lại","htmlAttribute"=>$htmlAttribute), 200);
        }
        //Get Attribute for Theme
        $themeAttribute = ThemeAttributeValue::join('theme_attribute','theme_attribute_value.theme_attribute_id','=','theme_attribute.id')->where('theme_id',$idTheme)->get();
        if(!isset($themeAttribute) || count($themeAttribute) < 1){
            return response()->json(array('status' => "ERRORS",'msg'=>"Theme bạn chọn chưa được cấu hình thuộc tính, Vui lòng thử lại","htmlAttribute"=>$htmlAttribute), 200);
        }
        //
        $obj_option_client = new \stdClass;;
        $option_client = ThemeClient::where('theme_id',$idTheme)->where('client_id',$idClient)->first();

        if($option_client && isset($option_client->param_attribute)  && strlen($option_client->param_attribute) > 0){
            $obj_option_client = json_decode($option_client->param_attribute);
        }


        // return $obj_option_client;

        $htmlAttribute.="<div class=\"row\">";

        foreach ($themeAttribute as $value){
            $keyJson = $value->key;
            $keyJsonImage = $value->key."_image";
            if($value->is_image == 1){
                $htmlAttribute.=" <div class=\"form-group m-form__group  col-md-8\">";
            }
            else{
                $htmlAttribute.=" <div class=\"form-group m-form__group  col-md-4\">";
            }
            if($value->is_image == 1){
                $htmlAttribute.=" <div class=\"row\"><div class=\"form-group m-form__group  col-md-6\">";
            }
            $htmlAttribute.="
                            <input type=\"hidden\" id=\"".$value->key."\" name=\"".$value->key."_value\" value=\"\">
                            <script>getval('select[name=\"".$value->key."\"]','".$value->key."')</script>
                            <label for=\"".$value->key."\">".$value->title.":</label>

                            <select  onchange=\"getval(this,'".$value->key."');\" name=\"".$value->key."\" class=\"form-control\">";
            //Load option Attribute
            $arr_option = \App\Library\Helpers::DecodeJson('send_name',$value->param_attribute);
            $arr_option_key = \App\Library\Helpers::DecodeJson('send_key',$value->param_attribute);
            if(!empty($arr_option) && !empty( $arr_option_key))
            {
                for ($i = 0; $i < count($arr_option); $i++){
                    if( $arr_option[$i]!=null && $arr_option_key[$i]!=null){
                        try {
                            if (!empty($obj_option_client)) {
                                if(!empty($obj_option_client->$keyJson) && ($obj_option_client->$keyJson == $arr_option[$i] || $obj_option_client->$keyJson == $arr_option_key[$i])) {
                                    $htmlAttribute .= "<option selected value=\"" . ($arr_option_key[$i] != null  ? $arr_option_key[$i] : 0) . "\">" . $arr_option[$i] . "</option>";
                                }
                                else{
                                    $htmlAttribute .= "<option value=\"" . ($arr_option_key[$i] != null ? $arr_option_key[$i] : 0) . "\">" . $arr_option[$i] . "</option>";
                                }
                            } else {
                                $htmlAttribute .= "<option value=\"" . ($arr_option_key[$i] != null  ? $arr_option_key[$i] : 0) . "\">" . $arr_option[$i] . "</option>";
                            }
                        }
                        catch(\Exception $error){
                            $htmlAttribute .= "<option value=\"" . ($arr_option_key[$i] != null  ? $arr_option_key[$i] : 0) . "\">" . $arr_option[$i] . "</option>";
                        }
                    }
                }
            }
            $htmlAttribute.="</select>";
            if($value->is_image == 1){
                $htmlAttribute.=" </div>";
                //Load html image
                $data_image = isset($obj_option_client->$keyJsonImage) && $obj_option_client->$keyJsonImage != null ? $obj_option_client->$keyJsonImage : "";
                $htmlAttribute.="<div class=\"form-group m-form__group  col-md-6\">
                                    <div>
                                        <label for=\"locale\">".__('Hình ảnh(button,background...)').":</label>
                                        <div class=\"\">
                                            <div class=\"fileinput ck-parent\" data-provides=\"fileinput\">
                                                <div class=\"fileinput-new thumbnail\" style=\"width: 50px; height: 40px;line-height: 40px;\">";
                if(isset($data_image) && $data_image != "")
                {
                    $htmlAttribute .= "<input class='ck-input' type='hidden' name='".$value->key."_image' value='".$data_image."'>";
                    $htmlAttribute .= "<img class='ck-thumb' src='".$data_image."'/>";
                }
                else{
                    $htmlAttribute .= "<input class='ck-input' type='hidden' name='".$value->key."_image' value=''>";
                    $htmlAttribute .= "<img class='ck-thumb' src='/assets/backend/themes/images/empty-photo.jpg' style='height: 40px;line-height: 40px;'/>";
                }

                $htmlAttribute.="               </div>
                                                <div style='display: inline-block;top: -2px;position: relative;'>
                                                    <a href=\"#\" class=\"btn red fileinput-exists ck-popup \">".__("Thay đổi")."</a>
                                                    <a href=\"#\" class=\"btn red fileinput-exists ck-btn-remove\" >".__("Xóa")."</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                $htmlAttribute.="</div>";
            }
            else{
                $htmlAttribute.="</div>";
            }
        }
        $htmlAttribute.="</div>";
        $htmlAttribute.= "<script type='text/javascript'>
            $('.ck-popup').click(function (e) {
                e.preventDefault();
                const parent = $(this).closest('.ck-parent');

                const elemThumb = parent.find('.ck-thumb');
                const elemInput = parent.find('.ck-input');
                CKFinder.modal({
                    connectorPath: '".route('admin.ckfinder_connector')."',
                    resourceType: 'Images',
                    chooseFiles: true,

                    width: 900,
                    height: 600,
                    onInit: function (finder) {
                        finder.on('files:choose', function (evt) {
                            const file = evt.data.files.first();
                            const url = file.getUrl();
                            elemThumb.attr('src', MEDIA_URL+url);
                            elemInput.val(url);
                        });
                    }
                });
            });
            $('.ck-btn-remove').click(function (e) {
                e.preventDefault();
                const parent = $(this).closest('.ck-parent');
                const elemThumb = parent.find('.ck-thumb');
                const elemInput = parent.find('.ck-input');
                elemThumb.attr('src', '/assets/backend/themes/images/empty-photo.jpg');
                elemInput.val('');
            });

            $('.btn_delete_image').click(function (e) {
                const parent = $(this).closest('.ck-parent');
                const elemInput = parent.find('.image_input_text');
                $(this).closest('.image-preview-box').remove();
                const allImageChoose=parent.find('.image-preview-box img');

                let allPath = '';
                let len = allImageChoose.length;
                allImageChoose.each(function (index, obj) {
                    allPath += $(this).attr('src');
                    if (index != len - 1) {
                        allPath += '|';
                    }
                });
                elemInput.val(allPath);
            });
            </script>";
        return response()->json(array('status' => "SUCCESS","msg"=>"Đã load thuộc tính trang","htmlAttribute"=>$htmlAttribute), 200);
    }

    public function postPageBuild(Request $request){

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $themeclient = ThemeClient::where('client_id',session('shop_id'))->first();


        $theme = Theme::where('id',$themeclient->theme_id)->where('status',1)->first();


        $page_build = config('pages_build.'.$theme->key);

        if(!isset($page_build)){
            return redirect()->back()->withErrors(__('Shop chưa cấu hình !'));
        }

        $module = config('module.theme-page.key');

        Group::where('shop_id',session('shop_id'))->where('module',$module)->where('idkey',$theme->key)->delete();

        foreach ($page_build as $key => $item){

            Group::create([
                'module' => $module,
                'shop_id' => session('shop_id'),
                'title' => $item,
                'slug' => $key,
                'idkey' => $theme->key,
                'author_id' => auth()->user()->id,
                'status' => 0,
            ]);

        }


        ActivityLog::add($request, 'Cập nhật theme cho theme mới.');

        return redirect()->back()->with('success',__('Thêm mới thành công !'));

    }

    public function postEditTitle(Request $request){

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $this->validate($request,[
            'title'=>'required',
        ],[
            'title.required' => __('Vui lòng nhập tiêu đề'),
        ]);

        $module = config('module.theme-page.key');
        $themeclient = ThemeClient::where('client_id',session('shop_id'))->first();
        $c_theme = Theme::where('id',$themeclient->theme_id)->where('status',1)->first();
        $key_theme = $c_theme->key;

        $id = $request->group_id;

        $data = Group::where('module', '=', $module)->where('idkey',$key_theme)->findOrFail($id);

        $data->title = $request->title;

        $data->save();

        $group = Group::where('module','=',$module)->where('idkey',$key_theme)->where('status',1)->where('shop_id',session('shop_id'))->orderBy('order')->get();

        if (isset($group) && count($group)){
            $slugselect = null;
            $titleselect = null;
            $itemselect = null;

            foreach ($group as $val){
                if (isset($slugselect)) {
                    $slugselect = $slugselect.'|';
                }

                $slugselect = $slugselect.$val->slug;

                if (isset($titleselect)) {
                    $titleselect = $titleselect.'|';
                }

                $titleselect = $titleselect.$val->title;
            }

            if (isset($titleselect) && isset($slugselect)){
                $itemselect = $titleselect.','.$slugselect;
            }

            if (isset($itemselect)){
                $key = 'sys_theme_ver_page_build';

                Setting::add($key, $itemselect, Setting::getDataType($key));
            }
        }


        ActivityLog::add($request, 'Sửa thành công title #'.$data->id);

        return redirect()->back()->with('success',__('Thêm mới thành công !'));
    }

    public function destroyPageBuild(Request $request)
    {
        $input=explode('|',$request->id);

        $module = config('module.theme-page.key');
        $groups = Group::where('module','=',$module)->whereIn('id',$input)->get();

        foreach ($groups as $item){
            $item->status = 0;
            $item->save();
        }

        $themeclient = ThemeClient::where('client_id',session('shop_id'))->first();
        $c_theme = Theme::where('id',$themeclient->theme_id)->where('status',1)->first();
        $key_theme = $c_theme->key;

        $group = Group::where('module','=',$module)->where('idkey',$key_theme)->where('status',1)->where('shop_id',session('shop_id'))->orderBy('order')->get();

        if (isset($group) && count($group)){
            $slugselect = null;
            $titleselect = null;
            $itemselect = null;

            foreach ($group as $val){
                if (isset($slugselect)) {
                    $slugselect = $slugselect.'|';
                }

                $slugselect = $slugselect.$val->slug;

                if (isset($titleselect)) {
                    $titleselect = $titleselect.'|';
                }

                $titleselect = $titleselect.$val->title;
            }

            if (isset($titleselect) && isset($slugselect)){
                $itemselect = $titleselect.','.$slugselect;
            }

            if (isset($itemselect)){
                $key = 'sys_theme_ver_page_build';

                Setting::add($key, $itemselect, Setting::getDataType($key));
            }
        }

        ActivityLog::add($request, 'Inactive thành công '.$module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Inactive thành công !'));
    }

    public function inDestroyPageBuild(Request $request)
    {
        $input=explode('|',$request->id);

        $module = config('module.theme-page.key');
        $groups = Group::where('module','=',$module)->whereIn('id',$input)->get();

        foreach ($groups as $item){
            $item->status = 1;
            $item->save();
        }

        $themeclient = ThemeClient::where('client_id',session('shop_id'))->first();
        $c_theme = Theme::where('id',$themeclient->theme_id)->where('status',1)->first();
        $key_theme = $c_theme->key;

        $group = Group::where('module','=',$module)->where('idkey',$key_theme)->where('status',1)->where('shop_id',session('shop_id'))->orderBy('order')->get();

        if (isset($group) && count($group)){
            $slugselect = null;
            $titleselect = null;
            $itemselect = null;

            foreach ($group as $val){
                if (isset($slugselect)) {
                    $slugselect = $slugselect.'|';
                }

                $slugselect = $slugselect.$val->slug;

                if (isset($titleselect)) {
                    $titleselect = $titleselect.'|';
                }

                $titleselect = $titleselect.$val->title;
            }

            if (isset($titleselect) && isset($slugselect)){
                $itemselect = $titleselect.','.$slugselect;
            }

            if (isset($itemselect)){
                $key = 'sys_theme_ver_page_build';

                Setting::add($key, $itemselect, Setting::getDataType($key));
            }
        }

        ActivityLog::add($request, 'Inactive thành công '.$module.' #'.json_encode($input));
        return redirect()->back()->with('success',__('Inactive thành công !'));
    }

    public function duplicatePageBuild(Request $request)
    {

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $validator = Validator::make($request->all(),[
            'shop_access' => 'required',
        ],[
            'shop_access.required' => "Vui lòng chọn shop cần clone",
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors(__('Shop đã chọn chưa cấu hình!'));
        }

        $shop_id = $request->shop_access;

        $module = config('module.theme-page.key');
        $themeclient = ThemeClient::where('client_id',session('shop_id'))->first();
        $c_theme = Theme::where('id',$themeclient->theme_id)->where('status',1)->first();
        $key_theme = $c_theme->key;

        $data = Group::where('module','=',$module)->where('idkey',$key_theme)->where('shop_id',$shop_id)->orderBy('order')->get();

        if(!$data){
            return redirect()->back()->withErrors(__('Shop đã chọn chưa cấu hình!'));
        }

        $checkgroup = Group::where('module','=',$module)->where('idkey',$key_theme)->where('shop_id',session('shop_id'))->get();


        if (isset($checkgroup)){
            Group::where('module','=',$module)->where('idkey',$key_theme)->where('shop_id',session('shop_id'))->delete();
        }

        $key = 'sys_theme_ver_page_build';

        $setting_build = null;

        $setting_build = Setting::getAllSettingsShopId($shop_id)->where('name', $key)->first();

        if(!$setting_build){
            return response()->json(['message' => 'shop không có dữ liệu','status' => 0]);
        }

        foreach ($data as $val){
            $item = Group::where('module','=',$module)->where('idkey',$key_theme)->where('id',$val->id)->first();
            $item_new = $item->replicate()->fill(
                [
                    'shop_id' => session('shop_id'),
                    'idkey' => $key_theme,
                    'created_at' => Carbon::now(),
                    'author_id' => auth()->user()->id,
                ]
            );

            $item_new->save();
        }

        Setting::add($key, $setting_build->val, Setting::getDataType($key));

        ActivityLog::add($request, 'Nhân bản thành công theme-page-build từ shop #'.json_encode($shop_id));
        return redirect()->back()->with('success',__('Nhân bản thành công !'));
    }

    // AJAX Reordering function
    public function order(Request $request)
    {
        $source = e($request->get('source'));
        $destination = $request->get('destination');
        $module = config('module.theme-page.key');

        $themeclient = ThemeClient::where('client_id',session('shop_id'))->first();
        $c_theme = Theme::where('id',$themeclient->theme_id)->where('status',1)->first();
        $key_theme = $c_theme->key;

        $item = Group::where('module', '=', $module)->find($source);
        //dd($item);
        $item->parent_id = isset($destination)?$destination:0;
        $item->save();

        $ordering = json_decode($request->get('order'));

        $rootOrdering = json_decode($request->get('rootOrder'));

        if ($ordering) {
            foreach ($ordering as $order => $item_id) {
                if ($itemToOrder = Group::where('module', '=', $module)->find($item_id)) {
                    $itemToOrder->order = $order;
                    $itemToOrder->save();
                }
            }
        } else {
            foreach ($rootOrdering as $order => $item_id) {
                if ($itemToOrder = Group::where('module', '=', $module)->find($item_id)) {
                    $itemToOrder->order = $order;
                    $itemToOrder->save();
                }
            }
        }

        $slugselect = null;
        $titleselect = null;
        $itemselect = null;

        $group = Group::where('module','=',$module)->where('idkey',$key_theme)->where('shop_id',session('shop_id'))->where('status',1)->orderBy('order')->get();

        if (isset($group) && count($group)){
            foreach ($group as $val){
                if (isset($slugselect)) {
                    $slugselect = $slugselect.'|';
                }

                $slugselect = $slugselect.$val->slug;

                if (isset($titleselect)) {
                    $titleselect = $titleselect.'|';
                }

                $titleselect = $titleselect.$val->title;
            }

            if (isset($titleselect) && isset($slugselect)){
                $itemselect = $titleselect.','.$slugselect;
            }

            if (isset($itemselect)){
                $key = 'sys_theme_ver_page_build';

                Setting::add($key, $itemselect, Setting::getDataType($key));
            }

            ActivityLog::add($request, 'Thay đổi STT thành công theme-page-build #'.$item->id);
            return 'ok ';
        }

    }


    // Getter for the HTML menu builder

    function getHTMLCategory($menu)
    {
        return $this->buildMenu($menu);
    }

    function buildMenu($menu, $parent_id = 0)
    {
        $result = null;
        foreach ($menu as $item)

            if ($item->parent_id == $parent_id) {
                $result .= "<li class='dd-item nested-list-item' data-order='{$item->order}' data-id='{$item->id}'>
              <div class='dd-handle nested-list-handle'>
                <span class='la la-arrows-alt'></span>
              </div>
              <div class='nested-list-content' data-toggle=\"tooltip\" data-placement=\"top\" title='{$this->titleMenu($item)}'>";
                if($parent_id!=0){
                    $result.="<div class=\"m-checkbox\">
                                    <label class=\"checkbox v_nested-list-content checkbox-outline\">
                                    <input data-id='{$item->id}' type=\"checkbox\" rel=\"{$item->id}\" class=\"checkbox_outline children_of_{$item->parent_id}\">
                                      <span></span> ".HTML::entities($item->title)."
                                    </label>
                                </div>";
                }
                else{

                    $result.="<div class=\"m-checkbox\">
                                    <label class=\"checkbox v_nested-list-content checkbox-outline\">
                                    <input data-id='{$item->id}' type=\"checkbox\" rel=\"{$item->id}\" class=\"children_of_{$item->parent_id}\"  >
                                    <span></span> ".HTML::entities($item->title)."
                                    </label>
                                </div>";
                }
                if ($item->status == 1){
                    $result .= "<div class='btnControll' style='display: flex'>";
                    $result .= "
                            <span style='margin-right: 4px' class=\"switch switch-outline switch-icon switch-success switch-status-theme\" data-id='{$item->id}'><label><input checked type=\"checkbox\" class=\"checkbox-itemgroupshop-5\" name=\"status\"><span></span></label></span>
                            <a style='margin-right: 4px' data-title='{$item->title}' data-id='{$item->id}' href='javascript:void(0)' class='btn btn-sm btn-primary btn-edit-title'>Sửa</a>
                        </div>
                      </div>" . $this->buildMenu($menu, $item->id) . "</li>";
                }else{
                    $result .= "<div class='btnControll' style='display: flex'>";
                    $result .= "
                            <span style='margin-right: 4px' class=\"switch switch-outline switch-icon switch-success switch-status-theme\" data-id='{$item->id}'><label><input type=\"checkbox\" class=\"checkbox-itemgroupshop-5\" name=\"status\"><span></span></label></span>
                            <a style='margin-right: 4px' data-title='{$item->title}' data-id='{$item->id}' href='javascript:void(0)' class='btn btn-sm btn-primary btn-edit-title'>Sửa</a>
                        </div>
                      </div>" . $this->buildMenu($menu, $item->id) . "</li>";
                }

            }
        return $result ? "\n<ol class=\"dd-list\">\n$result</ol>\n" : null;
    }

    function titleMenu($item){
        $title = 'Tên cũ: '.config('pages_build.'.($item->idkey).'.'.($item->slug)).' - Key widget client: ' .$item->slug;
        return $title;
    }

    public function updateStatus(Request $request){

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $key = 'sys_theme_ver_page_build';
        $module = config('module.theme-page.key');

        $themeclient = ThemeClient::where('client_id',session('shop_id'))->first();
        $c_theme = Theme::where('id',$themeclient->theme_id)->where('status',1)->first();
        $key_theme = $c_theme->key;

        $data = Group::where('id',$request->id)->where('module','=',$module)->where('idkey',$key_theme)->where('shop_id',session('shop_id'))->first();

        $data->status = $request->status;
        $data->save();

        $group = Group::where('module','=',$module)->where('shop_id',session('shop_id'))->where('idkey',$key_theme)->where('status',1)->orderBy('order')->get();

        if (isset($group) && count($group)){

            $slugselect = null;
            $titleselect = null;
            $itemselect = null;

            foreach ($group as $val){
                if (isset($slugselect)) {
                    $slugselect = $slugselect.'|';
                }

                $slugselect = $slugselect.$val->slug;

                if (isset($titleselect)) {
                    $titleselect = $titleselect.'|';
                }

                $titleselect = $titleselect.$val->title;
            }

            if (isset($titleselect) && isset($slugselect)){
                $itemselect = $titleselect.','.$slugselect;
            }

            if (isset($itemselect)){

                Setting::add($key, $itemselect, Setting::getDataType($key));
            }
        }else{
            Setting::remove($key);
        }

        ActivityLog::add($request, 'Cập nhật trạng thái thành công #'.$data->id);

        return response()->json([
            'success'=>true,
            'message'=>__('Cập nhật trạng thái thành công !'),
            'redirect'=>''
        ]);
//        return redirect()->back()->with('success',__('Thêm mới thành công !'));

    }

    public function updateModule(Request $request){

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $key_minigame = "sys_theme_minigame_list";

        $key_service = "sys_theme_service_list";

        $key_nick = "sys_theme_nick_list";

        if ($request->filled('sys_theme_minigame_list'))  {
            Setting::add($key_minigame, $request->sys_theme_minigame_list, Setting::getDataType($key_minigame));
        }else{
            Setting::add($key_minigame, '', Setting::getDataType($key_minigame));
        }

        if ($request->filled('sys_theme_service_list'))  {
            Setting::add($key_service, $request->sys_theme_service_list, Setting::getDataType($key_service));
        }else{
            Setting::add($key_service, '', Setting::getDataType($key_service));
        }

        if ($request->filled('sys_theme_nick_list'))  {
            Setting::add($key_nick, $request->sys_theme_nick_list, Setting::getDataType($key_nick));
        }else{
            Setting::add($key_nick, '', Setting::getDataType($key_nick));
        }

        ActivityLog::add($request, 'Update module theme-page-build từ shop #'.json_encode($request->all()));
        return redirect()->back()->with('success',__('Cập nhật thành công !'));

    }

    public function displayPrice(Request $request){

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $key_minigame = "sys_theme_minigame_display_price";

        $key_service = "sys_theme_service_display_price";

        $key_nick = "sys_theme_nick_display_price";

        if ($request->filled('sys_theme_minigame_display_price'))  {
            Setting::add($key_minigame, $request->sys_theme_minigame_display_price, Setting::getDataType($key_minigame));
        }else{
            Setting::add($key_minigame, '', Setting::getDataType($key_minigame));
        }

        if ($request->filled('sys_theme_service_display_price'))  {
            Setting::add($key_service, $request->sys_theme_service_display_price, Setting::getDataType($key_service));
        }else{
            Setting::add($key_service, '', Setting::getDataType($key_service));
        }

        if ($request->filled('sys_theme_nick_display_price'))  {
            Setting::add($key_nick, $request->sys_theme_nick_display_price, Setting::getDataType($key_nick));
        }else{
            Setting::add($key_nick, '', Setting::getDataType($key_nick));
        }

        ActivityLog::add($request, 'Update hien thi gia theme-page-build từ shop #'.json_encode($request->all()));
        return redirect()->back()->with('success',__('Cập nhật thành công !'));

    }


    public function updateBackground(Request $request){

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $key_background_color = "sys_theme_background_color";

        if ($request->filled('sys_theme_background_color'))  {
            Setting::add($key_background_color, $request->sys_theme_background_color, Setting::getDataType($key_background_color));
        }else{
            Setting::add($key_background_color, '', Setting::getDataType($key_background_color));
        }

        $key_background_image = "sys_theme_background_image";

        if ($request->filled('sys_theme_background_image'))  {
            Setting::add($key_background_image, $request->sys_theme_background_image, Setting::getDataType($key_background_image));
        }else{
            Setting::add($key_background_image, '', Setting::getDataType($key_background_image));
        }

        $key_color_primary = "sys_theme_color_primary";

        if ($request->filled('sys_theme_color_primary'))  {
            Setting::add($key_color_primary, $request->sys_theme_color_primary, Setting::getDataType($key_color_primary));

        }else{
            Setting::add($key_color_primary, '', Setting::getDataType($key_color_primary));
        }

        $key_color_hover = "sys_theme_color_hover";

        if ($request->filled('sys_theme_color_hover'))  {
            Setting::add($key_color_hover, $request->sys_theme_color_hover, Setting::getDataType($key_color_hover));
        }else{
            Setting::add($key_color_hover, '', Setting::getDataType($key_color_hover));
        }

        $key_color_click = "sys_theme_color_click";

        if ($request->filled('sys_theme_color_click'))  {
            Setting::add($key_color_click, $request->sys_theme_color_click, Setting::getDataType($key_color_click));
        }else{
            Setting::add($key_color_click, '', Setting::getDataType($key_color_click));
        }

        $key_color_disable = "sys_theme_color_disable";

        if ($request->filled('sys_theme_color_disable'))  {
            Setting::add($key_color_disable, $request->sys_theme_color_disable, Setting::getDataType($key_color_disable));
        }else{
            Setting::add($key_color_disable, '', Setting::getDataType($key_color_disable));
        }

        $key_color_text = "sys_theme_color_text";

        if ($request->filled('sys_theme_color_text'))  {
            Setting::add($key_color_text, $request->sys_theme_color_text, Setting::getDataType($key_color_text));
        }else{
            Setting::add($key_color_text, '', Setting::getDataType($key_color_text));
        }

        $key_color_text_item_hover = "sys_theme_color_text_item_hover";

        if ($request->filled('sys_theme_color_text_item_hover'))  {
            Setting::add($key_color_text_item_hover, $request->sys_theme_color_text_item_hover, Setting::getDataType($key_color_text_item_hover));
        }else{
            Setting::add($key_color_text_item_hover, '', Setting::getDataType($key_color_text_item_hover));
        }

        $key_color_text_item = "sys_theme_color_text_item";

        if ($request->filled('sys_theme_color_text_item'))  {
            Setting::add($key_color_text_item, $request->sys_theme_color_text_item, Setting::getDataType($key_color_text_item));
        }else{
            Setting::add($key_color_text_item, '', Setting::getDataType($key_color_text_item));
        }

        $key_border_radius = "sys_theme_border_radius";

        if ($request->filled('sys_theme_border_radius'))  {
            Setting::add($key_border_radius, $request->sys_theme_border_radius, Setting::getDataType($key_border_radius));
        }else{
            Setting::add($key_border_radius, '', Setting::getDataType($key_border_radius));
        }

        $key_sys_theme_width_image = "sys_theme_width_image";

        if ($request->filled('sys_theme_width_image'))  {
            Setting::add($key_sys_theme_width_image, $request->sys_theme_width_image, Setting::getDataType($key_sys_theme_width_image));
        }else{
            Setting::add($key_sys_theme_width_image, '', Setting::getDataType($key_sys_theme_width_image));
        }

        $key_sys_theme_height_image = "sys_theme_height_image";

        if ($request->filled('sys_theme_height_image'))  {
            Setting::add($key_sys_theme_height_image, $request->sys_theme_height_image, Setting::getDataType($key_sys_theme_height_image));
        }else{
            Setting::add($key_sys_theme_height_image, '', Setting::getDataType($key_sys_theme_height_image));
        }
        // màu chủ đạo linear
        $key_sys_theme_linear_color1 = "sys_theme_color_primary_linear1";

        if ($request->filled('sys_theme_color_primary_linear1'))  {
            Setting::add($key_sys_theme_linear_color1, $request->sys_theme_color_primary_linear1, Setting::getDataType($key_sys_theme_linear_color1));
        }else{
            Setting::add($key_sys_theme_linear_color1, '', Setting::getDataType($key_sys_theme_linear_color1));
        }
        $key_sys_theme_linear_color2 = "sys_theme_color_primary_linear2";

        if ($request->filled('sys_theme_color_primary_linear1'))  {
            Setting::add($key_sys_theme_linear_color2, $request->sys_theme_color_primary_linear2, Setting::getDataType($key_sys_theme_linear_color2));
        }else{
            Setting::add($key_sys_theme_linear_color2, '', Setting::getDataType($key_sys_theme_linear_color2));
        }
        // màu nền danh mục
        $key_sys_theme_bg_card = "sys_theme_color_bg_card";

        if ($request->filled('sys_theme_color_bg_card'))  {
            Setting::add($key_sys_theme_bg_card, $request->sys_theme_color_bg_card, Setting::getDataType($key_sys_theme_bg_card));
        }else{
            Setting::add($key_sys_theme_bg_card, '', Setting::getDataType($key_sys_theme_bg_card));
        }
        // màu viền form
        $key_sys_theme_border_form = "sys_theme_color_border_form";

        if ($request->filled('sys_theme_color_border_form'))  {
            Setting::add($key_sys_theme_border_form, $request->sys_theme_color_border_form, Setting::getDataType($key_sys_theme_border_form));
        }else{
            Setting::add($key_sys_theme_border_form, '', Setting::getDataType($key_sys_theme_border_form));
        }
        // màu viền danh mục
        $key_sys_theme_border_card1 = "sys_theme_color_border_card1";

        if ($request->filled('sys_theme_color_border_card1'))  {
            Setting::add($key_sys_theme_border_card1, $request->sys_theme_color_border_card1, Setting::getDataType($key_sys_theme_border_card1));
        }else{
            Setting::add($key_sys_theme_border_card1, '', Setting::getDataType($key_sys_theme_border_card1));
        }

        $key_sys_theme_border_card2 = "sys_theme_color_border_card2";

        if ($request->filled('sys_theme_color_border_card2'))  {
            Setting::add($key_sys_theme_border_card2, $request->sys_theme_color_border_card2, Setting::getDataType($key_sys_theme_border_card2));
        }else{
            Setting::add($key_sys_theme_border_card2, '', Setting::getDataType($key_sys_theme_border_card2));
        }


        ActivityLog::add($request, 'Update background theme-page-build từ shop #'.json_encode($request->all()));
        return redirect()->back()->with('success',__('Cập nhật thành công !'));

    }

    public function serverImage(Request $request){

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $key_sys_server_image = "sys_server_image";

        if ($request->filled('server_image'))  {
            Setting::add($key_sys_server_image, $request->server_image, Setting::getDataType($key_sys_server_image));
        }else{
            Setting::add($key_sys_server_image, '', Setting::getDataType($key_sys_server_image));
        }

        ActivityLog::add($request, 'Update server image theme-page-build từ shop #'.json_encode($request->all()));
        return redirect()->back()->with('success',__('Cập nhật thành công !'));

    }

    public function serverApi(Request $request){

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }
        $key_sys_redirect_301 = "sys_redirect_301";
        if ($request->filled('old_redirect_301') && $request->filled('redirect_301')){
            $old_redirect_301 = $request->get('old_redirect_301');
            $redirect_301 = $request->get('redirect_301');

            if (count($old_redirect_301) && count($redirect_301) && count($old_redirect_301) == count($redirect_301)){
                foreach ($old_redirect_301 as $key => $item){
                    $params['redirect_301'][] = $item;
                    $params['old_redirect_301'][] = $redirect_301[$key];
                }

                Setting::add($key_sys_redirect_301, json_encode($params), Setting::getDataType($key_sys_redirect_301));
            }else{
                Setting::add($key_sys_redirect_301, '', Setting::getDataType($key_sys_redirect_301));
            }

        }else{
            Setting::add($key_sys_redirect_301, '', Setting::getDataType($key_sys_redirect_301));
        }

        ActivityLog::add($request, 'Update server api theme-page-build từ shop #'.json_encode($request->all()));
        return redirect()->back()->with('success',__('Cập nhật thành công !'));

    }

    public function categoryOption(Request $request){

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $key_sys_service_widget_one = "sys_service_widget_one";
        $st_service_widget_one = null;

        if ($request->filled('service_widget_one'))  {
            if ($request->get('service_widget_one') && count($request->get('service_widget_one'))){
                foreach ($request->get('service_widget_one') as $service_widget_one){
                    if (isset($st_service_widget_one)){
                        $st_service_widget_one = $st_service_widget_one.'|'.$service_widget_one;
                    }else{
                        $st_service_widget_one = $service_widget_one;
                    }
                }

                if (isset($st_service_widget_one)){
                    Setting::add($key_sys_service_widget_one, $st_service_widget_one, Setting::getDataType($key_sys_service_widget_one));
                }else{
                    Setting::add($key_sys_service_widget_one, '', Setting::getDataType($key_sys_service_widget_one));
                }
            }else{
                Setting::add($key_sys_service_widget_one, '', Setting::getDataType($key_sys_service_widget_one));
            }

        }else{
            Setting::add($key_sys_service_widget_one, '', Setting::getDataType($key_sys_service_widget_one));
        }

        $key_sys_service_widget_two = "sys_service_widget_two";
        $st_service_widget_two = null;

        if ($request->filled('service_widget_two'))  {
            if ($request->get('service_widget_two') && count($request->get('service_widget_two'))){
                foreach ($request->get('service_widget_two') as $service_widget_two){
                    if (isset($st_service_widget_two)){
                        $st_service_widget_two = $st_service_widget_two.'|'.$service_widget_two;
                    }else{
                        $st_service_widget_two = $service_widget_two;
                    }
                }

                if (isset($st_service_widget_two)){
                    Setting::add($key_sys_service_widget_two, $st_service_widget_two, Setting::getDataType($key_sys_service_widget_two));
                }else{
                    Setting::add($key_sys_service_widget_two, '', Setting::getDataType($key_sys_service_widget_two));
                }
            }else{
                Setting::add($key_sys_service_widget_two, '', Setting::getDataType($key_sys_service_widget_two));
            }

        }else{
            Setting::add($key_sys_service_widget_two, '', Setting::getDataType($key_sys_service_widget_two));
        }

        $key_sys_service_widget_three = "sys_service_widget_three";
        $st_service_widget_three = null;

        if ($request->filled('service_widget_three'))  {
            if ($request->get('service_widget_three') && count($request->get('service_widget_three'))){
                foreach ($request->get('service_widget_three') as $service_widget_three){
                    if (isset($st_service_widget_three)){
                        $st_service_widget_three = $st_service_widget_three.'|'.$service_widget_three;
                    }else{
                        $st_service_widget_three = $service_widget_three;
                    }
                }

                if (isset($st_service_widget_three)){
                    Setting::add($key_sys_service_widget_three, $st_service_widget_three, Setting::getDataType($key_sys_service_widget_three));
                }else{
                    Setting::add($key_sys_service_widget_three, '', Setting::getDataType($key_sys_service_widget_three));
                }
            }else{
                Setting::add($key_sys_service_widget_three, '', Setting::getDataType($key_sys_service_widget_three));
            }

        }else{
            Setting::add($key_sys_service_widget_three, '', Setting::getDataType($key_sys_service_widget_three));
        }

//        nick

        $key_sys_nick_widget_one = "sys_nick_widget_one";
        $st_nick_widget_one = null;

        if ($request->filled('nick_widget_one'))  {
            if ($request->get('nick_widget_one') && count($request->get('nick_widget_one'))){
                foreach ($request->get('nick_widget_one') as $nick_widget_one){
                    if (isset($st_nick_widget_one)){
                        $st_nick_widget_one = $st_nick_widget_one.'|'.$nick_widget_one;
                    }else{
                        $st_nick_widget_one = $nick_widget_one;
                    }
                }

                if (isset($st_nick_widget_one)){
                    Setting::add($key_sys_nick_widget_one, $st_nick_widget_one, Setting::getDataType($key_sys_nick_widget_one));
                }else{
                    Setting::add($key_sys_nick_widget_one, '', Setting::getDataType($key_sys_nick_widget_one));
                }
            }else{
                Setting::add($key_sys_nick_widget_one, '', Setting::getDataType($key_sys_nick_widget_one));
            }

        }else{
            Setting::add($key_sys_nick_widget_one, '', Setting::getDataType($key_sys_nick_widget_one));
        }

        $key_sys_nick_widget_two = "sys_nick_widget_two";
        $st_nick_widget_two = null;

        if ($request->filled('nick_widget_two'))  {
            if ($request->get('nick_widget_two') && count($request->get('nick_widget_two'))){
                foreach ($request->get('nick_widget_two') as $nick_widget_two){
                    if (isset($st_nick_widget_two)){
                        $st_nick_widget_two = $st_nick_widget_two.'|'.$nick_widget_two;
                    }else{
                        $st_nick_widget_two = $nick_widget_two;
                    }
                }

                if (isset($st_nick_widget_two)){
                    Setting::add($key_sys_nick_widget_two, $st_nick_widget_two, Setting::getDataType($key_sys_nick_widget_two));
                }else{
                    Setting::add($key_sys_nick_widget_two, '', Setting::getDataType($key_sys_nick_widget_two));
                }
            }else{
                Setting::add($key_sys_nick_widget_two, '', Setting::getDataType($key_sys_nick_widget_two));
            }

        }else{
            Setting::add($key_sys_nick_widget_two, '', Setting::getDataType($key_sys_nick_widget_two));
        }

        $key_sys_nick_widget_three = "sys_nick_widget_three";
        $st_nick_widget_three = null;

        if ($request->filled('nick_widget_three'))  {
            if ($request->get('nick_widget_three') && count($request->get('nick_widget_three'))){
                foreach ($request->get('nick_widget_three') as $nick_widget_three){
                    if (isset($st_nick_widget_three)){
                        $st_nick_widget_three = $st_nick_widget_three.'|'.$nick_widget_three;
                    }else{
                        $st_nick_widget_three = $nick_widget_three;
                    }
                }

                if (isset($st_nick_widget_three)){
                    Setting::add($key_sys_nick_widget_three, $st_nick_widget_three, Setting::getDataType($key_sys_nick_widget_three));
                }else{
                    Setting::add($key_sys_nick_widget_three, '', Setting::getDataType($key_sys_nick_widget_three));
                }
            }else{
                Setting::add($key_sys_nick_widget_three, '', Setting::getDataType($key_sys_nick_widget_three));
            }

        }else{
            Setting::add($key_sys_nick_widget_three, '', Setting::getDataType($key_sys_nick_widget_three));
        }

        ActivityLog::add($request, 'Update lựa chọn danh mục hiển thị theme-page-build từ shop #'.json_encode($request->all()));
        return redirect()->back()->with('success',__('Cập nhật thành công !'));
    }

    public function categoryCustomOption(Request $request){

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        if ($request->filled('nick_custom_widget_id')){
            if (count($request->get('nick_custom_widget_id'))){

                $params['nick_custom_widget_id'] = $request->get('nick_custom_widget_id');
                foreach ($request->get('nick_custom_widget_id') as $v_nick_custom_widget_id){
                    $input['module'] = "acc_category";
                    $input['data'] = "category_list";
                    $input['shop_id'] = null;
                    if(session('shop_id')){
                        $input['shop_id'] = session('shop_id');
                    }

                    if (empty($input['module'])) {
                        $input['module'] = 'acc_provider';
                    }
                    $input['shop_group_id'] = null;
                    $nicks = Group::where('module', $input['module']??'acc_provider')->orderBy('order')->with(['childs' => function($query) use($input){
                        if (($input['module']??'acc_provider') == 'acc_provider') {
                            $query->withCount(['items' => function($query) use($input){
                                $query->whereHas('access_category', function($query){
                                    $query->where('active', 1);
                                })->where(function($query) use($input){
                                    $query->whereHas('access_shops', function($query) use($input){
                                        $query->where('shop.id', $input['shop_id']);
                                    })->orWhereHas('author', function($query) use($input){
                                        $query->where('shop_access', 'all');
                                    })->orWhereHas('access_shop_groups', function($query) use($input){
                                        $query->where('shop_group.id', $input['shop_group_id']);
                                    });
                                })->where(['status' => 1]);
                            }])
                                ->whereHas('custom', function($query) use($input){
                                    $query->where(['groups_shops.shop_id' => $input['shop_id'], 'status' => 1]);
                                })->with(['custom' => function($query) use($input){
                                    $query->where('shop_id', $input['shop_id']);
                                }]);
                        }
                    }])->where('status', 1);
                    if (($input['module']??'acc_provider') == 'acc_category') {
                        $nicks->withCount(['items' => function($query) use($input){
                            $query->whereHas('access_category', function($query){
                                $query->where('active', 1);
                            })->where(function($query) use($input){
                                $query->whereHas('access_shops', function($query) use($input){
                                    $query->where('shop.id', $input['shop_id']);
                                })->orWhereHas('author', function($query) use($input){
                                    $query->where('shop_access', 'all');
                                })->orWhereHas('access_shop_groups', function($query) use($input){
                                    $query->where('shop_group.id', $input['shop_group_id']);
                                });
                            })->where(['status' => 1]);
                        }])->whereHas('custom', function($query) use($input){
                            $query->where(['shop_id' => $input['shop_id'], 'status' => 1]);
                        })->with(['custom' => function($query) use($input){
                            $query->where('shop_id', $input['shop_id']);
                        }]);
                    }
                    $nicks = $nicks->where('id',$v_nick_custom_widget_id)->first();

                    if (isset($nicks)){
                        if (isset($nicks->custom)){
                            $slug_title[] = $nicks->custom->slug;
                        }else{
                            $slug_title[] = $nicks->slug;
                        }

                    }else{
                        $slug_title[] = null;
                    }
                }

                $params['slug_title'] = $slug_title;

                $params['nick_custom_widget_title_one'] = $request->get('nick_custom_widget_title_one');

                $slug_custom_title_one = [];
                foreach ($request->get('nick_custom_widget_title_one') as $v_nick_custom_widget_title_one){
                    if (isset($v_nick_custom_widget_title_one)){
                        $slug = $this->to_slug($v_nick_custom_widget_title_one);
                        $check_nicks = Group::where('module', $input['module']??'acc_provider')->orderBy('order')->with(['childs' => function($query) use($input){
                            if (($input['module']??'acc_provider') == 'acc_provider') {
                                $query->withCount(['items' => function($query) use($input){
                                    $query->whereHas('access_category', function($query){
                                        $query->where('active', 1);
                                    })->where(function($query) use($input){
                                        $query->whereHas('access_shops', function($query) use($input){
                                            $query->where('shop.id', $input['shop_id']);
                                        })->orWhereHas('author', function($query) use($input){
                                            $query->where('shop_access', 'all');
                                        })->orWhereHas('access_shop_groups', function($query) use($input){
                                            $query->where('shop_group.id', $input['shop_group_id']);
                                        });
                                    })->where(['status' => 1]);
                                }])->whereHas('custom', function($query) use($input){
                                    $query->where(['groups_shops.shop_id' => $input['shop_id'], 'status' => 1]);
                                })->with(['custom' => function($query) use($input){
                                    $query->where('shop_id', $input['shop_id']);
                                }]);
                            }
                        }])->where('status', 1);
                        if (($input['module']??'acc_provider') == 'acc_category') {
                            $check_nicks->withCount(['items' => function($query) use($input){
                                $query->whereHas('access_category', function($query){
                                    $query->where('active', 1);
                                })->where(function($query) use($input){
                                    $query->whereHas('access_shops', function($query) use($input){
                                        $query->where('shop.id', $input['shop_id']);
                                    })->orWhereHas('author', function($query) use($input){
                                        $query->where('shop_access', 'all');
                                    })->orWhereHas('access_shop_groups', function($query) use($input){
                                        $query->where('shop_group.id', $input['shop_group_id']);
                                    });
                                })->where(['status' => 1]);
                            }])->whereHas('custom', function($query) use($input){
                                $query->where(['shop_id' => $input['shop_id'], 'status' => 1]);
                            })->with(['custom' => function($query) use($input){
                                $query->where('shop_id', $input['shop_id']);
                            }]);
                        }
                        $check_nicks = $check_nicks->where('slug',$slug)->first();
                        if (isset($check_nicks)){
                            $slug_custom_title_one[] = $slug.'-custom';
                        }else{
                            $slug_custom_title_one[] = $slug;
                        }

                    }else{
                        $slug_custom_title_one[] = null;
                    }

                }
                $params['slug_custom_title_one'] = $slug_custom_title_one;
                $params['nick_custom_widget_title_two'] = $request->get('nick_custom_widget_title_two');
                $slug_custom_title_two = [];
                foreach ($request->get('nick_custom_widget_title_two') as $v_nick_custom_widget_title_two){
                    if (isset($v_nick_custom_widget_title_two)){
                        $slug_two = $this->to_slug($v_nick_custom_widget_title_two);
                        $check_nicks = Group::where('module', $input['module']??'acc_provider')->orderBy('order')->with(['childs' => function($query) use($input){
                            if (($input['module']??'acc_provider') == 'acc_provider') {
                                $query->withCount(['items' => function($query) use($input){
                                    $query->whereHas('access_category', function($query){
                                        $query->where('active', 1);
                                    })->where(function($query) use($input){
                                        $query->whereHas('access_shops', function($query) use($input){
                                            $query->where('shop.id', $input['shop_id']);
                                        })->orWhereHas('author', function($query) use($input){
                                            $query->where('shop_access', 'all');
                                        })->orWhereHas('access_shop_groups', function($query) use($input){
                                            $query->where('shop_group.id', $input['shop_group_id']);
                                        });
                                    })->where(['status' => 1]);
                                }])->whereHas('custom', function($query) use($input){
                                    $query->where(['groups_shops.shop_id' => $input['shop_id'], 'status' => 1]);
                                })->with(['custom' => function($query) use($input){
                                    $query->where('shop_id', $input['shop_id']);
                                }]);
                            }
                        }])->where('status', 1);
                        if (($input['module']??'acc_provider') == 'acc_category') {
                            $check_nicks->withCount(['items' => function($query) use($input){
                                $query->whereHas('access_category', function($query){
                                    $query->where('active', 1);
                                })->where(function($query) use($input){
                                    $query->whereHas('access_shops', function($query) use($input){
                                        $query->where('shop.id', $input['shop_id']);
                                    })->orWhereHas('author', function($query) use($input){
                                        $query->where('shop_access', 'all');
                                    })->orWhereHas('access_shop_groups', function($query) use($input){
                                        $query->where('shop_group.id', $input['shop_group_id']);
                                    });
                                })->where(['status' => 1]);
                            }])->whereHas('custom', function($query) use($input){
                                $query->where(['shop_id' => $input['shop_id'], 'status' => 1]);
                            })->with(['custom' => function($query) use($input){
                                $query->where('shop_id', $input['shop_id']);
                            }]);
                        }
                        $check_nicks = $check_nicks->where('slug',$slug_two)->first();
                        if (isset($check_nicks)){
                            $slug_custom_title_two[] = $slug_two.'-custom';
                        }else{
                            $slug_custom_title_two[] = $slug_two;
                        }
                    }else{
                        $slug_custom_title_two[] = null;
                    }

                }
                $params['slug_custom_title_two'] = $slug_custom_title_two;
                /*                $params['nick_custom_widget_image_one'] = $request->get('nick_custom_widget_image_one');*/

                $params['nick_custom_widget_description_two'] = $request->get('nick_custom_widget_description_two');
                $params['nick_custom_widget_description_one'] = $request->get('nick_custom_widget_description_one');

                $params['nick_custom_widget_seo_description_two'] = $request->get('nick_custom_widget_seo_description_two');
                $params['nick_custom_widget_seo_description_one'] = $request->get('nick_custom_widget_seo_description_one');

                $params['nick_custom_widget_content_two'] = $request->get('nick_custom_widget_content_two');
                $params['nick_custom_widget_content_one'] = $request->get('nick_custom_widget_content_one');

//                $params['nick_custom_widget_image_one'] = $request->get('nick_custom_widget_image_one');
//
//                $params['nick_custom_widget_image_two'] = $request->get('nick_custom_widget_image_two');

                $params['nick_custom_widget_price_min'] = $request->get('nick_custom_widget_price_min');

                $params = json_encode($params);
                $key_sys_custom_widget_nick = "sys_custom_widget_nick";
                $st_sys_custom_widget_nick = $params;

                Setting::add($key_sys_custom_widget_nick, $st_sys_custom_widget_nick, Setting::getDataType($key_sys_custom_widget_nick));
            }
        }

        ActivityLog::add($request, 'Update lựa chọn custom danh mục nick theme-page-build từ shop #'.json_encode($request->all()));
        return redirect()->back()->with('success',__('Cập nhật thành công !'));
    }

//    public function choiceCategoryOption(Request $request){
//
//
//        return $request->all();
//    }

    function to_slug($str) {
        $str = trim(mb_strtolower($str));
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', '-', $str);
        return $str;
    }

}
