<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AutoLink;
use App\Models\Group;
use App\Models\Item;
use App\Models\Setting;
use App\Models\Shop;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
        if(!$shop){
            return response()->json([
                'message' => __('Domain chưa được đăng kí'),
                'status' => 0,
            ], 200);
        }

        $modulecate = "article-category";

        $datatable= Item::with(array('groups' => function ($query) {
            $query->where('groups.module','article-category');
            $query->select('groups.id','title','slug','image_icon');
        }))->with(array('author' => function ($query) {
            $query->select('id','username');
        }))->where('module', config('module.article'))
            ->where('status', '=', 1)
            ->select('id','author_id','title','seo_title','slug','description','seo_description','content','image','published_at','created_at','url_redirect_301')
            ->where('shop_id','=', $shop->id);

            $setting_zip = null;

            $key = "sys_zip_shop";
            $setting_zip = Setting::getAllSettingsShopId($shop->id)->where('name', $key)->first();

            if (isset($setting_zip)){
                $datatable = $datatable->orderBy('published_at', 'desc');
            }else{
                $datatable = $datatable->orderBy('created_at', 'desc');
            }

        if ($request->filled('group_slug')) {

            $datatable->whereHas('groups', function ($query) use ($request) {
                $query->where('slug',$request->get('group_slug'));
            });
        }

        if ($request->filled('id')) {
            $datatable = $datatable->where('id','!=', $request->get('id'));
        }

        if ($request->filled('querry')){
            $querry = $request->querry;
            $datatable = $datatable->where(function ($query) use ($querry){
                $query->orWhere('title','LIKE', '%' . $querry . '%');
                $query->orWhere('description','LIKE', '%' . $querry . '%');
            });
        }

        $datatable= $datatable->paginate( $request->get('limit')??10);

        $adss = Item::where('module','advertise-ads')->where('idkey',0)
            ->select('id','author_id','module','title','params')
            ->first();

        return response()->json([
            'data' => $datatable,
            'adss' => $adss,
            'message' => 'Lấy dữ liệu thành công',
            'status' => 1,
        ]);
    }

    public function show(Request $request,$slug)
    {
        $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
        if(!$shop){
            return response()->json([
                'message' => __('Domain chưa được đăng kí'),
                'status' => 0,
            ], 200);
        }


        $categoryarticle = Group::where('module', config('module.article-category'))
            ->where('status', '=', 1)->where('slug', '=', $slug)
            ->where('shop_id','=', $shop->id)
            ->select('id','title','slug','description','content','image','image_icon')
            ->first();


        if (!isset($categoryarticle)){

            $datatable= Item::with(array('groups' => function ($query) {
                $query->where('groups.module','article-category');
                $query->select('groups.id','title','slug','image_icon');
            }))->with(array('author' => function ($query) {
                $query->select('id','username');
            }))->where('module', config('module.article'))
                ->whereIn('status', [1,2])
                ->where('slug', '=', $slug)
                ->where('shop_id','=', $shop->id)
                ->select('id','author_id','title','seo_title','slug','description','seo_description','published_at','content','image','created_at','params','params_plus','url_redirect_301')
                ->orderBy('created_at', 'desc')->first();

            $item = 1;

            $dataautolink = AutoLink::orderBy('id')->where('shop_id',$shop->id)->get();

            $flag_changed = 0;
            $check_total = 0;

            if (isset($datatable->content) && $datatable->content != ''){
                if (isset($dataautolink) && count($dataautolink)){

                    foreach ($dataautolink as $itemat){
                        $total_link = $itemat->percent_dofollow??null;

                        $datatablecontent = \App\Library\AutoLink::replace($itemat->title,$itemat->url,$datatable->content,$itemat->target,$itemat->dofollow);

                        if ($itemat->link_type == 1 && $datatablecontent['changed']) {
                            if (isset($total_link)){
                                if ($check_total <= $total_link){
                                    $check_total = $check_total + 1;
                                    $datatable->content = $datatablecontent['content'];
                                }
                            }else{
                                $datatable->content = $datatablecontent['content'];
                            }
                        }

                    }

                }
            }

            return response()->json([
                'data' => $datatable,
                'item' => $item,
                'status' => 1,
                'message' => 'Lấy dữ liệu thành công',
            ]);
        }else{

            $datatable= Item::with(array('groups' => function ($query) {
                $query->where('groups.module','article-category');
                $query->select('groups.id','title','slug','image_icon');
            }))->with(array('author' => function ($query) {
                $query->select('id','username');
            }))->where('module', config('module.article'))
                ->where('status', '=', 1)
                ->where('shop_id','=', $shop->id)
                ->select('id','author_id','title','seo_title','slug','description','seo_description','published_at','content','image','created_at','params','params_plus','url_redirect_301');

            $setting_zip = null;

            $key = "sys_zip_shop";
            $setting_zip = Setting::getAllSettingsShopId($shop->id)->where('name', $key)->first();

            if (isset($setting_zip)){
                $datatable = $datatable->orderBy('published_at', 'desc');
            }else{
                $datatable = $datatable->orderBy('created_at', 'desc');
            }

            if ($request->filled('querry')){
                $querry = $request->querry;
                $datatable = $datatable->where('title','LIKE', '%' . $querry . '%');
            }

            if ($request->filled('slug')){
                $slug = $request->slug;
            }

            $datatable = $datatable->whereHas('groups', function ($query) use ($slug) {
                $query->where('slug', '=', $slug);
            });

            $datatable= $datatable->paginate( $request->get('limit')??10);

            $item = 0;
            return response()->json([
                'data' => $datatable,
                'item' => $item,
                'status' => 1,
                'categoryarticle' => $categoryarticle,
                'is_over'=>false,
                'message' => 'Lấy dữ liệu thành công',
            ]);

        }
    }

    public function getCategory(Request $request){

        $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
        if(!$shop){
            return response()->json([
                'message' => __('Domain chưa được đăng kí'),
                'status' => 0,
            ], 200);
        }

        $datacategory = Group::where('module', config('module.article-category'))
            ->where('status', '=', 1)
            ->select('id','title','slug');

        if ($request->filled('shop_id')){
            $shopid = $shop->id;
            $datacategory = $datacategory->where('shop_id','=', $shopid)
                ->withCount(
                    [
                        'items as count_item' => function ($query1) use ($shopid) {
                            $query1->where('items.shop_id','=', $shopid)->where('status',1);
                        },
                    ]);
        }

        $datacategory =  $datacategory->get();

        return response()->json([
            'datacategory' => $datacategory,
            'status' => 1,
            'message' => 'Lấy dữ liệu thành công',
        ]);
    }

    public function getShowCategory(Request $request){

        $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
        if(!$shop){
            return response()->json([
                'message' => __('Domain chưa được đăng kí'),
                'status' => 0,
            ], 200);
        }

        $datacategory = Group::where('module', config('module.article-category'))
            ->with(['items' => function($query){
                $query->select('items.id','items.title','items.slug','items.description','items.content','items.image','items.created_at','items.url_redirect_301');
            }])
            ->where('shop_id','=', $shop->id)
            ->where('status', '=', 1)
            ->select('id','title','slug')->get();

        return response()->json([
            'datacategory' => $datacategory,
            'status' => 1,
            'message' => 'Lấy dữ liệu thành công',
        ]);

    }
}
