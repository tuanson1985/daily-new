<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\Files;
use App\Library\Helpers;
use App\Models\ActivityLog;
use App\Models\LogEdit;
use App\Models\Setting;
use App\Models\Shop;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;


class SettingController extends Controller
{


    protected $page_breadcrumbs;

    public function __construct()
    {
        //set permission to function
        $this->middleware("permission:setting-list");
        $this->middleware("permission:setting-create", ['only' => ['create', 'store']]);
        $this->middleware("permission:setting-edit", ['only' => ['edit', 'update']]);
        $this->middleware("permission:setting-delete", ['only' => ['destroy']]);

        $this->page_breadcrumbs = [
            ['page' => route('admin.setting.index'),
                'title' => "Cấu hình hệ thống"
            ],
        ];
    }

    public function index(Request $request)
    {

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $shop = Shop::where('id',session('shop_id'))->first();

        $secret_key = config('module.service.secret_key');
        $name_shop = Helpers::Encrypt(\Str::slug($shop->title),$secret_key);

        $folder_image = "setting-config-".$name_shop;

        ActivityLog::add($request, 'Vào form edit setting');
        return view('admin.setting.index')
            ->with('folder_image', $folder_image)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);

    }


    public function store(Request $request)
    {

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $shop = Shop::where('id',session('shop_id'))->first();
        $params_before = null;
        $params_after = null;
        // lấy thông tin IP và user_angent người dùng
        // lấy thông tin IP và user_angent người dùng
        $ip = $request->getClientIp();
        $user_agent = $request->userAgent();
        $message = "Thời gian: <b>" . Carbon::now()->format('d-m-Y H:i:s') . "</b>";
        $message .= "\n";
        $message .= Auth::user()->username." Thay đổi setting điểm bán: ".$shop->domain;

        $old_sys_address = Setting::getSettingShop('sys_address',null,$shop->id);

        $params_before['sys_address'] = $old_sys_address??'';
        $params_after['sys_address'] = $request->get('sys_address')??'';

        if ($request->filled('sys_address')){

            if ($request->get('sys_address') != $old_sys_address){
                $message .= "\n";
                $message .= "Thay đổi sys_address - Địa chỉ";
            }
        }



        $old_sys_atm_percent = Setting::getSettingShop('sys_atm_percent',null,$shop->id);
        $params_before['sys_atm_percent'] = $old_sys_atm_percent??'';
        $params_after['sys_atm_percent'] = $request->get('sys_atm_percent')??'';
        if ($request->filled('sys_atm_percent')){
            if ($request->get('sys_atm_percent') != $old_sys_atm_percent){
                $message .= "\n";
                $message .= "Thay đổi sys_atm_percent - % ATM";
            }
        }

        $old_sys_avatar = Setting::getSettingShop('sys_avatar',null,$shop->id);
        $params_before['sys_avatar'] = $old_sys_avatar??'';
        $params_after['sys_avatar'] = $request->get('sys_avatar')??'';
        if ($request->filled('sys_avatar')){
            if ($request->get('sys_avatar') != $old_sys_avatar){
                $message .= "\n";
                $message .= "Thay đổi sys_avatar - ảnh Avatar";
            }
        }

        $old_sys_charge_content = Setting::getSettingShop('sys_charge_content',null,$shop->id);
        $params_before['sys_charge_content'] = $old_sys_charge_content??'';
        $params_after['sys_charge_content'] = $request->get('sys_charge_content')??'';
        if ($request->filled('sys_charge_content')){
            if ($request->get('sys_charge_content') != $old_sys_charge_content){
                $message .= "\n";
                $message .= "Thay đổi sys_charge_content - Nội dung nạp thẻ";
            }
        }

        $old_sys_description = Setting::getSettingShop('sys_description',null,$shop->id);
        $params_before['sys_description'] = $old_sys_description??'';
        $params_after['sys_description'] = $request->get('sys_description')??'';
        if ($request->filled('sys_description')){
            if ($request->get('sys_description') != $old_sys_description){
                $message .= "\n";
                $message .= "Thay đổi sys_description - Mô tả";
            }
        }

        $old_sys_fanpage = Setting::getSettingShop('sys_fanpage',null,$shop->id);
        $params_before['sys_fanpage'] = $old_sys_fanpage??'';
        $params_after['sys_fanpage'] = $request->get('sys_fanpage')??'';
        if ($request->filled('sys_fanpage')){
            if ($request->get('sys_fanpage') != $old_sys_fanpage){
                $message .= "\n";
                $message .= "Thay đổi sys_fanpage - link fanpage";
            }
        }

        $old_sys_favicon = Setting::getSettingShop('sys_favicon',null,$shop->id);
        $params_before['sys_favicon'] = $old_sys_favicon??'';
        $params_after['sys_favicon'] = $request->get('sys_favicon')??'';
        if ($request->filled('sys_favicon')){
            if ($request->get('sys_favicon') != $old_sys_favicon){
                $message .= "\n";
                $message .= "Thay đổi sys_favicon - Ảnh favicon";
            }
        }

        $old_sys_footer = Setting::getSettingShop('sys_footer',null,$shop->id);
        $params_before['sys_footer'] = $old_sys_footer??'';
        $params_after['sys_footer'] = $request->get('sys_footer')??'';
        if ($request->filled('sys_footer')){
            if ($request->get('sys_footer') != $old_sys_footer){
                $message .= "\n";
                $message .= "Thay đổi sys_footer - Footer điểm bán";
            }
        }

        $old_sys_google_analytics = Setting::getSettingShop('sys_google_analytics',null,$shop->id);
        $params_before['sys_google_analytics'] = $old_sys_google_analytics??'';
        $params_after['sys_google_analytics'] = $request->get('sys_google_analytics')??'';
        if ($request->filled('sys_google_analytics')){
            if ($request->get('sys_google_analytics') != $old_sys_google_analytics){
                $message .= "\n";
                $message .= "Thay đổi sys_google_analytics - Google analytics";
            }
        }

        $old_sys_google_tag_manager_body = Setting::getSettingShop('sys_google_tag_manager_body',null,$shop->id);
        $params_before['sys_google_tag_manager_body'] = $old_sys_google_tag_manager_body??'';
        $params_after['sys_google_tag_manager_body'] = $request->get('sys_google_tag_manager_body')??'';
        if ($request->filled('sys_google_tag_manager_body')){
            if ($request->get('sys_google_tag_manager_body') != $old_sys_google_tag_manager_body){
                $message .= "\n";
                $message .= "Thay đổi sys_google_tag_manager_body - Google tag manager body";
            }
        }

        $old_sys_google_tag_manager_head = Setting::getSettingShop('sys_google_tag_manager_head',null,$shop->id);
        $params_before['sys_google_tag_manager_head'] = $old_sys_google_tag_manager_head??'';
        $params_after['sys_google_tag_manager_head'] = $request->get('sys_google_tag_manager_head')??'';
        if ($request->filled('sys_google_tag_manager_head')){
            if ($request->get('sys_google_tag_manager_head') != $old_sys_google_tag_manager_head){
                $message .= "\n";
                $message .= "Thay đổi sys_google_tag_manager_head - Google tag manager head";
            }
        }

        $old_sys_id_chat_message = Setting::getSettingShop('sys_id_chat_message',null,$shop->id);
        $params_before['sys_id_chat_message'] = $old_sys_id_chat_message??'';
        $params_after['sys_id_chat_message'] = $request->get('sys_id_chat_message')??'';
        if ($request->filled('sys_id_chat_message')){
            if ($request->get('sys_id_chat_message') != $old_sys_id_chat_message){
                $message .= "\n";
                $message .= "Thay đổi sys_id_chat_message - ID chat message";
            }
        }

        $old_sys_intro_text = Setting::getSettingShop('sys_intro_text',null,$shop->id);
        $params_before['sys_intro_text'] = $old_sys_intro_text??'';
        $params_after['sys_intro_text'] = $request->get('sys_intro_text')??'';
        if ($request->filled('sys_intro_text')){
            if ($request->get('sys_intro_text') != $old_sys_intro_text){
                $message .= "\n";
                $message .= "Thay đổi sys_intro_text - Intro text";
            }
        }

        $old_sys_keyword = Setting::getSettingShop('sys_keyword',null,$shop->id);
        $params_before['sys_keyword'] = $old_sys_keyword??'';
        $params_after['sys_keyword'] = $request->get('sys_keyword')??'';
        if ($request->filled('sys_keyword')){
            if ($request->get('sys_keyword') != $old_sys_keyword){
                $message .= "\n";
                $message .= "Thay đổi sys_keyword - Keyword điểm bán";
            }
        }

        $old_sys_logo = Setting::getSettingShop('sys_logo',null,$shop->id);
        $params_before['sys_logo'] = $old_sys_logo??'';
        $params_after['sys_logo'] = $request->get('sys_logo')??'';
        if ($request->filled('sys_logo')){
            if ($request->get('sys_logo') != $old_sys_logo){
                $message .= "\n";
                $message .= "Thay đổi sys_logo - Ảnh logo";
            }
        }

        $old_sys_mail = Setting::getSettingShop('sys_mail',null,$shop->id);
        $params_before['sys_mail'] = $old_sys_mail??'';
        $params_after['sys_mail'] = $request->get('sys_mail')??'';
        if ($request->filled('sys_mail')){
            if ($request->get('sys_mail') != $old_sys_mail){
                $message .= "\n";
                $message .= "Thay đổi sys_mail - Mail liên hệ";
            }
        }

        $old_sys_logo_mobile = Setting::getSettingShop('sys_logo_mobile',null,$shop->id);
        $params_before['sys_logo_mobile'] = $old_sys_logo_mobile??'';
        $params_after['sys_logo_mobile'] = $request->get('sys_logo_mobile')??'';
        if ($request->filled('sys_logo_mobile')){
            if ($request->get('sys_logo_mobile') != $old_sys_logo_mobile){
                $message .= "\n";
                $message .= "Thay đổi sys_logo_mobile - Logo mobile";
            }
        }

        $old_sys_marquee = Setting::getSettingShop('sys_marquee',null,$shop->id);
        $params_before['sys_marquee'] = $old_sys_marquee??'';
        $params_after['sys_marquee'] = $request->get('sys_marquee')??'';
        if ($request->filled('sys_marquee')){
            if ($request->get('sys_marquee') != $old_sys_marquee){
                $message .= "\n";
                $message .= "Thay đổi sys_marquee - Nội dung chạy chữ";
            }
        }

        $old_sys_noti_popup = Setting::getSettingShop('sys_noti_popup',null,$shop->id);
        $params_before['sys_noti_popup'] = $old_sys_noti_popup??'';
        $params_after['sys_noti_popup'] = $request->get('sys_noti_popup')??'';
        if ($request->filled('sys_noti_popup')){
            if ($request->get('sys_noti_popup') != $old_sys_noti_popup){
                $message .= "\n";
                $message .= "Thay đổi sys_noti_popup - Nội dung thông báo popup";
            }
        }

        $old_sys_og_image = Setting::getSettingShop('sys_og_image',null,$shop->id);
        $params_before['sys_og_image'] = $old_sys_og_image??'';
        $params_after['sys_og_image'] = $request->get('sys_og_image')??'';
        if ($request->filled('sys_og_image')){
            if ($request->get('sys_og_image') != $old_sys_og_image){
                $message .= "\n";
                $message .= "Thay đổi sys_og_image - Ảnh og";
            }
        }

        $old_sys_phone = Setting::getSettingShop('sys_phone',null,$shop->id);
        $params_before['sys_phone'] = $old_sys_phone??'';
        $params_after['sys_phone'] = $request->get('sys_phone')??'';
        if ($request->filled('sys_phone')){
            if ($request->get('sys_phone') != $old_sys_phone){
                $message .= "\n";
                $message .= "Thay đổi sys_phone - Số điện thoại liên hệ";
            }
        }

        $old_sys_card_setting = Setting::getSettingShop('sys_card_setting',null,$shop->id);
        $params_before['sys_card_setting'] = $old_sys_card_setting??'';
        $params_after['sys_card_setting'] = $request->get('sys_card_setting')??'';
        if ($request->filled('sys_card_setting')){
            if ($request->get('sys_card_setting') != $old_sys_card_setting){
                $message .= "\n";
                $message .= "Thay đổi sys_card_setting - Hiển thị giá card";
            }
        }

        $old_sys_default_change_image = Setting::getSettingShop('sys_default_change_image',null,$shop->id);
        $params_before['sys_default_change_image'] = $old_sys_default_change_image??'';
        $params_after['sys_default_change_image'] = $request->get('sys_default_change_image')??'';
        if ($request->filled('sys_default_change_image')){
            if ($request->get('sys_default_change_image') != $old_sys_default_change_image){
                $message .= "\n";
                $message .= "Thay đổi sys_default_change_image - Ảnh mặc định nạp thẻ";
            }
        }

        $old_sys_error_image = Setting::getSettingShop('sys_error_image',null,$shop->id);
        $params_before['sys_error_image'] = $old_sys_error_image??'';
        $params_after['sys_error_image'] = $request->get('sys_error_image')??'';
        if ($request->filled('sys_error_image')){
            if ($request->get('sys_error_image') != $old_sys_error_image){
                $message .= "\n";
                $message .= "Thay đổi sys_error_image - Ảnh lỗi mặc định";
            }
        }

        $old_sys_google_plus = Setting::getSettingShop('sys_google_plus',null,$shop->id);
        $params_before['sys_google_plus'] = $old_sys_google_plus??'';
        $params_after['sys_google_plus'] = $request->get('sys_google_plus')??'';
        if ($request->filled('sys_google_plus')){
            if ($request->get('sys_google_plus') != $old_sys_google_plus){
                $message .= "\n";
                $message .= "Thay đổi sys_google_plus - Google plus";
            }
        }

        $old_sys_google_search_console = Setting::getSettingShop('sys_google_search_console',null,$shop->id);
        $params_before['sys_google_search_console'] = $old_sys_google_search_console??'';
        $params_after['sys_google_search_console'] = $request->get('sys_google_search_console')??'';
        if ($request->filled('sys_google_search_console')){
            if ($request->get('sys_google_search_console') != $old_sys_google_search_console){
                $message .= "\n";
                $message .= "Thay đổi sys_google_search_console - Google search console";
            }
        }

        $old_sys_schema = Setting::getSettingShop('sys_schema',null,$shop->id);
        $params_before['sys_schema'] = $old_sys_schema??'';
        $params_after['sys_schema'] = $request->get('sys_schema')??'';
        if ($request->filled('sys_schema')){
            if ($request->get('sys_schema') != $old_sys_schema){
                $message .= "\n";
                $message .= "Thay đổi sys_schema - Schema";
            }
        }

        $old_sys_store_card_content = Setting::getSettingShop('sys_store_card_content',null,$shop->id);
        $params_before['sys_store_card_content'] = $old_sys_store_card_content??'';
        $params_after['sys_store_card_content'] = $request->get('sys_store_card_content')??'';
        if ($request->filled('sys_store_card_content')){
            if ($request->get('sys_store_card_content') != $old_sys_store_card_content){
                $message .= "\n";
                $message .= "Thay đổi sys_store_card_content - Nội dung mua thẻ";
            }
        }

        $old_sys_store_card_seo = Setting::getSettingShop('sys_store_card_seo',null,$shop->id);
        $params_before['sys_store_card_seo'] = $old_sys_store_card_seo??'';
        $params_after['sys_store_card_seo'] = $request->get('sys_store_card_seo')??'';
        if ($request->filled('sys_store_card_seo')){
            if ($request->get('sys_store_card_seo') != $old_sys_store_card_seo){
                $message .= "\n";
                $message .= "Thay đổi sys_store_card_seo - Seo mua thẻ";
            }
        }

        $old_sys_store_card_title = Setting::getSettingShop('sys_store_card_title',null,$shop->id);
        $params_before['sys_store_card_title'] = $old_sys_store_card_title??'';
        $params_after['sys_store_card_title'] = $request->get('sys_store_card_title')??'';
        if ($request->filled('sys_store_card_title')){
            if ($request->get('sys_store_card_title') != $old_sys_store_card_title){
                $message .= "\n";
                $message .= "Thay đổi sys_store_card_title - Tiêu đề mua thẻ";
            }
        }

        $old_sys_title = Setting::getSettingShop('sys_title',null,$shop->id);
        $params_before['sys_title'] = $old_sys_title??'';
        $params_after['sys_title'] = $request->get('sys_title')??'';
        if ($request->filled('sys_title')){
            if ($request->get('sys_title') != $old_sys_title){
                $message .= "\n";
                $message .= "Thay đổi sys_title - Tiêu đề điểm bán";
            }
        }

        $old_sys_top_charge = Setting::getSettingShop('sys_top_charge',null,$shop->id);
        $params_before['sys_top_charge'] = $old_sys_top_charge??'';
        $params_after['sys_top_charge'] = $request->get('sys_top_charge')??'';
        if ($request->filled('sys_top_charge')){
            if ($request->get('sys_top_charge') != $old_sys_top_charge){
                $message .= "\n";
                $message .= "Thay đổi sys_top_charge - Top nạp thẻ";
            }
        }
        $old_sys_tranfer_content = Setting::getSettingShop('sys_tranfer_content',null,$shop->id);
        $params_before['sys_tranfer_content'] = $old_sys_tranfer_content??'';
        $params_after['sys_tranfer_content'] = $request->get('sys_tranfer_content')??'';
        if ($request->filled('sys_tranfer_content')){
            if ($request->get('sys_tranfer_content') != $old_sys_tranfer_content){
                $message .= "\n";
                $message .= "Thay đổi sys_tranfer_content - Nội dung chuyển tiền";
            }
        }
        $old_sys_twitter = Setting::getSettingShop('sys_twitter',null,$shop->id);
        $params_before['sys_twitter'] = $old_sys_twitter??'';
        $params_after['sys_twitter'] = $request->get('sys_twitter')??'';
        if ($request->filled('sys_twitter')){
            if ($request->get('sys_twitter') != $old_sys_twitter){
                $message .= "\n";
                $message .= "Thay đổi sys_twitter - Link twitter";
            }
        }
        $old_sys_youtube = Setting::getSettingShop('sys_youtube',null,$shop->id);
        $params_before['sys_youtube'] = $old_sys_youtube??'';
        $params_after['sys_youtube'] = $request->get('sys_youtube')??'';
        if ($request->filled('sys_youtube')){
            if ($request->get('sys_youtube') != $old_sys_youtube){
                $message .= "\n";
                $message .= "Thay đổi sys_youtube - Link youtube";
            }
        }

        $message .= "\n";
        $message .= "IP: <b>" . $ip . "</b>";
        $message .= "\n";
        $message .= "User_agent: <b>" . $user_agent . "</b>";
        Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_noty_setting'));

        $rules = Setting::getValidationRules();
        $this->validate($request, $rules);
        $data=$request->all();
        $validSettings = array_keys($rules);

        foreach ($data as $key => $val) {
            if (in_array($key, $validSettings)) {
                $InputTypeOfField = Setting::getInputType($key);
                if($InputTypeOfField == 'list_top_charge'){
                    $list_top_charge = null;
                    if(isset($val['user']) && isset($val['amount']) && count($val['user']) > 0 && count($val['amount']) > 0){
                        for($i = 0; $i < count($val['amount']); $i++){
                           if(isset($val['user'][$i]) && $val['user'][$i] != "" && isset($val['amount'][$i]) && $val['amount'][$i] != ""){
                               $list_top_charge[] = [
                                'user' => $val['user'][$i],
                                'amount' => $val['amount'][$i],
                               ];
                           }
                        }
                    }
                    if(isset($list_top_charge)){
                        $list_top_charge = json_encode($list_top_charge);
                    }
                    $val = $list_top_charge;
                }
                Setting::add($key, $val, Setting::getDataType($key));
            }
        }

        $log_data['description_before'] = json_encode($params_before);
        $log_data['description_after'] = json_encode($params_after);
        $log_data['author_id'] = auth()->user()->id;
        $log_data['type'] = 0;
        $log_data['table_name'] = 'settings';
        $log_data['table_id'] = $shop->id;
        $log_data['shop_id'] = $shop->id;

        LogEdit::create($log_data);

        $description = 'Cập nhật thành công setting: shop-id: '.$shop->id.' domain: '.$shop->domain.'QTV: '.Auth::user()->username;
        ActivityLog::add($request, $description);

        return response()->json([
            'success' => true,
            'message' => __('Cập nhật thành công !'),
            'redirect' => ''
        ]);

    }


}
