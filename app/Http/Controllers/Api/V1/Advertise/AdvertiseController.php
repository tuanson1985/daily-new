<?php

namespace App\Http\Controllers\Api\V1\Advertise;

use App\Http\Controllers\Controller;
use App\Library\Helpers;
use App\Library\HelpItemAdd;
use App\Library\HelpServiceAuto;
use App\Library\RatioCommon\ServiceRatio;
use App\Models\Bot;
use App\Models\Group;
use App\Models\Item;
use App\Models\ItemConfig;
use App\Models\KhachHang;
use App\Models\LangLaCoin_KhachHang;
use App\Models\LangLaCoin_User;
use App\Models\MoneySpent;
use App\Models\NinjaXu_KhachHang;
use App\Models\NinjaXu_User;
use App\Models\Nrogem_AccBan;
use App\Models\Nrogem_GiaoDich;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shop;
use App\Models\Shop_Group;
use App\Models\SubItem;
use App\Models\Txns;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use JWTAuth;


class AdvertiseController extends Controller
{

    public function postMenuCategory(Request $request){
        try{

            $shop = Shop::where('secret_key',$request->secret_key)->where('domain',$request->domain)->where('status',1)->first();

            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }

            $shopid = $shop->id;

            $category = Group::where('status', '=', 1)
                ->where('module',config('module.menu-category.key'))
                ->where('shop_id',$shopid)
                ->select('id','title','slug','description','content','url','url_type','parent_id','shop_id','target','order','image_icon','params')
                ->orderBy('order')->get();

            return response()->json([
                'message' => __('Mã giảm giá hợp lệ.'),
                'status' => 1,
                'data' => $category
            ]);

        }
        catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }

    }
    public function getMenuCategory(Request $request,$id){
        try{

            $shop = Shop::where('secret_key',$request->secret_key)->where('domain',$request->domain)->where('status',1)->first();

            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }
            $shopid = $shop->id;
            $category = Group::where('status', '=', 1)
                ->where('module',config('module.menu-category.key'))
                ->where('shop_id',$shopid)
                ->where('parent_id',$id)
                ->select('id','title','slug','description','content','url','url_type','parent_id','shop_id','target','order','image_icon')
                ->orderBy('order')->get();
            return response()->json([
                'message' => __('Thành công.'),
                'status' => 1,
                'data' => $category
            ]);

        }
        catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }

    }

    public function postMenuProfile(Request $request){
        try{

            $shop = Shop::where('secret_key',$request->secret_key)->where('domain',$request->domain)->where('status',1)->first();

            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }

            $shopid = $shop->id;

            $category = Group::where('status', '=', 1)
                ->where('module',config('module.menu-profile.key'))
                ->where('shop_id',$shopid)
                ->select('id','title','slug','description','content','url','url_type','parent_id','shop_id','target','order','image_icon')
                ->orderBy('order')->get();

            return response()->json([
                'message' => __('Mã giảm giá hợp lệ.'),
                'status' => 1,
                'data' => $category
            ]);

        }
        catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }

    }

    public function postMenuTransaction(Request $request){
        try{

            $shop = Shop::where('secret_key',$request->secret_key)->where('domain',$request->domain)->where('status',1)->first();

            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }

            $shopid = $shop->id;

            $category = Group::where('status', '=', 1)
                ->where('module',config('module.menu-transaction.key'))
                ->where('shop_id',$shopid)
                ->select('id','title','slug','description','content','url','url_type','parent_id','shop_id','target','order','image','image_icon')
                ->orderBy('order')->get();

            return response()->json([
                'message' => __('Mã giảm giá hợp lệ.'),
                'status' => 1,
                'data' => $category
            ]);

        }
        catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }

    }

    public function getBannerHome(Request $request){

        try{

            $sliderHomes = Item::where('status', '=', 1)
                ->where('module', '=', config('module.advertise.key'))
                ->where('position','=', "SLIDE")
                ->select('image','url','image_banner','target')
                ->orderBy('order','desc');
            if ($request->filled('shop_id')) {
                $shopid = $request->shop_id;
                $sliderHomes = $sliderHomes->where('shop_id','=', $shopid);
            }
            $sliderHomes = $sliderHomes->get();
            return response()->json([
                'message' => __('Thành công.'),
                'status' => 1,
                'data' => $sliderHomes
            ]);

        }
        catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }

    public function getBannerNick(Request $request){

        try{

            $sliderHomes = Item::where('status', '=', 1)
                ->where('module', '=', config('module.advertise.key'))
                ->where('position','=', "ACCOUNT_BANNER")
                ->select('image','url','image_banner','target')
                ->orderBy('id','desc');
            if ($request->filled('shop_id')) {
                $shopid = $request->shop_id;
                $sliderHomes = $sliderHomes->where('shop_id','=', $shopid);
            }
            $sliderHomes = $sliderHomes->get();
            return response()->json([
                'message' => __('Thành công.'),
                'status' => 1,
                'data' => $sliderHomes
            ]);

        }
        catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }

    public function getBannerMinigame(Request $request){

        try{

            $sliderHomes = Item::where('status', '=', 1)
                ->where('module', '=', config('module.advertise.key'))
                ->where('position','=', "MINIGAME_BANNER")
                ->select('image','url','image_banner','target')
                ->orderBy('id','desc');
            if ($request->filled('shop_id')) {
                $shopid = $request->shop_id;
                $sliderHomes = $sliderHomes->where('shop_id','=', $shopid);
            }
            $sliderHomes = $sliderHomes->get();
            return response()->json([
                'message' => __('Thành công.'),
                'status' => 1,
                'data' => $sliderHomes
            ]);

        }
        catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }

    public function getBannerArticle(Request $request){

        try{

            $sliderHomes = Item::where('status', '=', 1)
                ->where('module', '=', config('module.advertise.key'))
                ->where('position','=', "ARTICLE_BANNER")
                ->select('image','url','image_banner','target')
                ->orderBy('id','desc');
            if ($request->filled('shop_id')) {
                $shopid = $request->shop_id;
                $sliderHomes = $sliderHomes->where('shop_id','=', $shopid);
            }
            $sliderHomes = $sliderHomes->get();
            return response()->json([
                'message' => __('Thành công.'),
                'status' => 1,
                'data' => $sliderHomes
            ]);

        }
        catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }

    public function getBannerService(Request $request){

        try{

            $sliderHomes = Item::where('status', '=', 1)
                ->where('module', '=', config('module.advertise.key'))
                ->where('position','=', "SERVICE_BANNER")
                ->select('image','url','image_banner','target')
                ->orderBy('id','desc');
            if ($request->filled('shop_id')) {
                $shopid = $request->shop_id;
                $sliderHomes = $sliderHomes->where('shop_id','=', $shopid);
            }
            $sliderHomes = $sliderHomes->get();
            return response()->json([
                'message' => __('Thành công.'),
                'status' => 1,
                'data' => $sliderHomes
            ]);

        }
        catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }

    public function getBannerChange(Request $request){

        try{

            $sliderHomes = Item::where('status', '=', 1)
                ->where('module', '=', config('module.advertise.key'))
                ->where('position','=', "CHANGE_BANNER")
                ->select('image','url','image_banner','target')
                ->orderBy('id','desc');
            if ($request->filled('shop_id')) {
                $shopid = $request->shop_id;
                $sliderHomes = $sliderHomes->where('shop_id','=', $shopid);
            }
            $sliderHomes = $sliderHomes->get();
            return response()->json([
                'message' => __('Thành công.'),
                'status' => 1,
                'data' => $sliderHomes
            ]);

        }
        catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }

    public function getBannerAds(Request $request){

        try{

            $sliderHomes = Item::where('status', '=', 1)
                ->where('module', '=', config('module.advertise.key'))
                ->where('position','=', "ADS_BANNER")
                ->select('image','url','image_banner','target')
                ->orderBy('id','desc');
            if ($request->filled('shop_id')) {
                $shopid = $request->shop_id;
                $sliderHomes = $sliderHomes->where('shop_id','=', $shopid);
            }
            $sliderHomes = $sliderHomes->get();
            return response()->json([
                'message' => __('Thành công.'),
                'status' => 1,
                'data' => $sliderHomes
            ]);

        }
        catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }

    public function getDichVuNoiBat(Request $request){

        try{

            $data = Item::where('status', '=', 1)
                ->where('module', '=', config('module.advertise.key'))
                ->where('position','=', "GAME_BANNER")
                ->select('title','image','url','target','image_banner','content')
                ->orderBy('order');
            if ($request->filled('shop_id')) {
                $shopid = $request->shop_id;
                $data = $data->where('shop_id','=', $shopid);
            }
            $data = $data->get();
            return response()->json([
                'message' => __('Thành công.'),
                'status' => 1,
                'data' => $data
            ]);

        }
        catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }

    public function getRecommend(Request $request){

        try{

            $data = Item::where('status', '=', 1)
                ->where('module', '=', config('module.advertise.key'))
                ->where('position','=', "RECOMMEND")
                ->select('title','image','url','target','image_banner')
                ->orderBy('order');
            if ($request->filled('shop_id')) {
                $shopid = $request->shop_id;
                $data = $data->where('shop_id','=', $shopid);
            }
            $data = $data->get();
            return response()->json([
                'message' => __('Thành công.'),
                'status' => 1,
                'data' => $data
            ]);

        }
        catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }


    public function showServiceHistory(Request $request){

        try{
            $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
            if(!$shop){
                return response()->json([
                    'message' => __('Domain chưa được đăng kí'),
                    'status' => 0,
                ], 200);
            }

            $categoryservice = Item::with(array('groups' => function ($query) {
                $query->select('groups.id','title','slug','description');
            }))->where('module', config('module.service'))
                ->where('shop_id',$shop->id)
                ->select('id','title','slug','description','content','image')->get();

            $datatable = $datatable = Order::with('item_ref')
                ->with(array('workflow' => function ($query) {
                    $query->where('module', config('module.service-workflow.key'))
                        ->orderBy('id', 'asc');

                }))
                ->with(array('workname' => function ($query) {
                    $query->where('module', config('module.service-workname.key'))
                        ->orderBy('id', 'asc');

                }))
                ->where('module', config('module.service-purchase'))
                ->select('id','title','description','gate_id','content','params','status','created_at','price','ratio','module','payment_type','ref_id','author_id','position');

            if ($request->filled('author_id')) {
                $author_id = $request->author_id;
                $datatable->where('author_id', $author_id);
            }

            if ($request->filled('id')) {
                $id = $request->id;
                $datatable->where('id', $id);
            }

            if ($request->filled('status')) {
                $status = $request->status;
                $datatable->where('status', $status);
            }

            if ($request->filled('slug_category')) {
                $slug = $request->slug_category;
                $datatable->whereHas('item_ref', function ($query) use ($slug) {
                    $query->where('slug', '=', $slug);
                });
            }

            if ($request->filled('started_at')) {
                $datatable->where('created_at', '>=', $request->started_at);

            }
            if ($request->filled('ended_at')) {
                $datatable->where('created_at', '<=', $request->ended_at);
            }

            if ($request->filled('sort')) {
                if ($request->sort == 'random') {
                    $datatable->inRandomOrder();
                }elseif(in_array($request->sort, ['asc', 'desc'])){
                    $datatable->orderBy($request->sort_by??'id', $request->sort);
                }
            }else{
                $datatable->orderBy('created_at','desc');
            }


            $datatable = $datatable->paginate( 10);

            return response()->json([
                'message' => __('Thành công'),
                'status' => 1,
                'categoryservice' => $categoryservice,
                'datatable' => $datatable,
            ], 200);

        }
        catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }


    }

    function getRelatedAcc(Request $request){

        $id = \App\Library\Helpers::decodeItemID($request->id);

        $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
        if(!$shop){
            return response()->json([
                'message' => __('Domain chưa được đăng kí'),
                'status' => 0,
            ], 200);
        }

        $shop_id = $shop->id;

        $val = Item::with(['groups' => function ($query) {
            $query->select('groups.id','groups.title', 'groups.module', 'groups.display_type', 'groups.parent_id', 'groups.is_slug_override')->with('parent');
        }, 'category' => function($query) use($shop_id){
            $query->with(['childs', 'custom' => function($query) use($shop_id){
                $query->where('shop_id', $shop_id);
            }]);
        }])->where(['module' => 'acc', 'id' => $id])->whereHas('access_category', function($query){
            $query->where('active', 1);
        })->whereHas('access_shops', function($query) use($shop_id){
            $query->where('shop.id', $shop_id);
        });

        $val->select('id', 'title', 'slug', 'idkey', 'shop_id', 'parent_id', 'author_id', 'params', 'image', 'image_extension',
            'price_old', 'price', 'sticky', 'status', 'created_at', 'published_at', 'description', 'content'
        );

        $val = $val->first();

        $slug = $val->category->slug;

        $result= Item::with(['groups' => function ($query) {
            $query->select('groups.id','groups.title', 'groups.module', 'groups.display_type', 'groups.parent_id', 'groups.is_slug_override')->with('parent');
        }, 'category' => function($query) use($shop_id){
            $query->with(['childs', 'custom' => function($query) use($shop_id){
                $query->where('shop_id', $shop_id);
            }]);
        }])->where('module', 'acc')->whereHas('access_category', function($query){
            $query->where('active', 1);
        })->whereHas('access_shops', function($query) use($shop_id){
            $query->where('shop.id', $shop_id);
        });

        $result->whereHas('category', function($query) use($slug){
            $query->where('slug', $slug)->orWhereHas('custom', function($query) use($slug){
                $query->where('slug', $slug);
            });
        });

        if (isset($request->status)) {
            $result->where('status', $request->status);
        }

        $result->select('id', 'title', 'slug', 'idkey', 'shop_id', 'parent_id', 'author_id', 'image', 'price_old', 'price', 'sticky', 'status', 'params', 'created_at', 'published_at', 'order');

        $result = $result->paginate($request->limit?? 12);

        $result->map(function($item) {
            $item->randId = \App\Library\Helpers::encodeItemID($item->id);
            $item->price = \App\Library\HelpMoneyPercent::shop_price($item->price);
            $item->price_old = \App\Library\HelpMoneyPercent::shop_price($item->price_old);
            return $item;
        });

        return response()->json([
            'message' => __('Thành công'),
            'status' => 1,
            'data' => $result,
        ], 200);
    }

    function random_category_list(Request $request){

        $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
        if(!$shop){
            return response()->json([
                'message' => __('Domain chưa được đăng kí'),
                'status' => 0,
            ], 200);
        }

        $shop_id = $shop->id;
        $input = array();
        $input['shop_id'] = $shop_id;
        $input['module'] = 'acc_category';

        $result = Group::where('module', $input['module']??'acc_provider')->orderBy('order')->with(['childs' => function($query) use($input){
            if (($input['module']??'acc_provider') == 'acc_provider') {
                $query->withCount(['items' => function($query) use($input){
                    $query->whereHas('access_category', function($query){
                        $query->where('active', 1);
                    })->whereHas('access_shops', function($query) use($input){
                        $query->where('shop_id', $input['shop_id']);
                    })->where(['status' => 1]);
                }])->whereHas('custom', function($query) use($input){
                    $query->where(['groups_shops.shop_id' => $input['shop_id'], 'status' => 1]);
                })->with(['custom' => function($query) use($input){
                    $query->where('shop_id', $input['shop_id']);
                }]);
            }
        }])->where('status', 1);
        if (($input['module']??'acc_provider') == 'acc_category') {
            $result->with(['items' => function($query) use($input){
                $query->whereHas('access_category', function($query){
                    $query->where('active', 1);
                })->whereHas('access_shops', function($query) use($input){
                    $query->where('shop_id', $input['shop_id']);
                })->where(['status' => 1]);
            }])->withCount(['items' => function($query) use($input){
                $query->whereHas('access_category', function($query){
                    $query->where('active', 1);
                })->whereHas('access_shops', function($query) use($input){
                    $query->where('shop_id', $input['shop_id']);
                })->where(['status' => 1]);
            }])->whereHas('custom', function($query) use($input){
                $query->where(['shop_id' => $input['shop_id'], 'status' => 1]);
            })->with(['custom' => function($query) use($input){
                $query->where('shop_id', $input['shop_id']);
            }]);
        }

        $result->inRandomOrder()->limit(4)->get();

        return response()->json([
            'message' => __('Thành công'),
            'status' => 1,
            'data' => $result,
        ], 200);
    }

    public function getShowService(Request $request){

        $shop = Shop::where('secret_key',$request->secret_key)->where('id',$request->shop_id)->where('status',1)->first();
        if(!$shop){
            return response()->json([
                'message' => __('Domain chưa được đăng kí'),
                'status' => 0,
            ], 200);
        }

        if ($request->filled('slug')){

            $slug = $request->slug;
            $datatable= Item::with(array('groups' => function ($query) {
                $query->select('groups.id','title','slug','description');
            }))->where('shop_id',$shop->id)->where('module', config('module.service'))

                ->where('slug','=',$slug)->first();

            if (isset($datatable->groups[0]->slug)){
                $slug_category = $datatable->groups[0]->slug;

                $categoryservice= Item::with(array('groups' => function ($query) {
                    $query->select('groups.id','title','slug','description');
                }))->where('module', config('module.service'))
                    ->where('status', '=', 1)
                    ->where('shop_id',$shop->id)
                    ->where('slug','!=',$slug)
                    ->whereHas('groups', function ($query) use ($slug_category) {
                        $query->where('slug', '=', $slug_category);
                    })
                    ->select('id','title','slug','description','content','image')
                    ->inRandomOrder();

                $categoryservice= $categoryservice->paginate( '8');

                return response()->json([
                    'data' => $datatable,
                    'categoryservice'=>$categoryservice,
                ]);
            }else{
                $categoryservice= Item::with(array('groups' => function ($query) {
                    $query->select('groups.id','title','slug','description');
                }))->where('module', config('module.service'))
                    ->where('status', '=', 1)
                    ->where('slug','!=',$slug)
                    ->where('shop_id',$shop->id)
                    ->select('id','title','slug','description','content','image')
                    ->inRandomOrder();

                $categoryservice= $categoryservice->paginate( '8');

                return response()->json([
                    'data' => $datatable,
                    'categoryservice'=>$categoryservice,
                ]);
            }

        }elseif ($request->filled('id')){
            $id = $request->id;
            $datatable= Item::with(array('groups' => function ($query) {
                $query->select('groups.id','title','slug','description');
            }))->where('module', config('module.service'))
                ->where('shop_id',$shop->id)
                ->where('id','=',$id)->first();

            return response()->json([
                'data' => $datatable,
            ]);
        }else{

            $datatable= Item::with(array('groups' => function ($query) {
                $query->select('groups.id','title','slug','description');
            }))->where('module', config('module.service'))
                ->where('shop_id',$shop->id)
                ->select('id','title','slug','description','content','image')->orderBy('order');

            if ($request->filled('title')){
                $querry = $request->title;
                $datatable = $datatable->where('title','LIKE', '%' . $querry . '%');
            }

            $datatable = $datatable->paginate($request->limit ?? 20);

            return response()->json([
                'message' => __('Thành công'),
                'status' => 1,
                'data' => $datatable
            ], 200);
        }

    }

    public function getHomePosition(Request $request){
        try{
            $sliderHomes = Item::where('status', '=', 1)
                ->where('module', '=', config('module.advertise.key'))
                ->select('image','url','seo_description','description','content','image_banner','target','title','position','percent_sale','price','price_old','params')
                ->orderBy('id','desc');

            if ($request->filled('shop_id')) {
                $shopid = $request->shop_id;
                $sliderHomes = $sliderHomes->where('shop_id','=', $shopid);
            }
            if ($request->filled('position')) {
                $position = $request->position;
                $sliderHomes = $sliderHomes->where('position','=', $position);
            }

            $sliderHomes = $sliderHomes->get();
            return response()->json([
                'message' => __('Thành công.'),
                'status' => 1,
                'data' => $sliderHomes
            ]);
        }
        catch(\Exception $e){
            Log::error($e);
            return response()->json([
                'message' => __('Có lỗi phát sinh, vui lòng thử lại.'),
                'status' => 0
            ], 500);
        }
    }
}
