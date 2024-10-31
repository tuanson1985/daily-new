<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Library\MediaHelpers;
use App\Models\Group;
use App\Models\GameAutoProperty;
use App\Models\Item;
use App\Models\Nick;
use App\Models\NickComplete;
use App\Models\OrderDetail;
use App\Models\Shop;
use App\Models\Txns;
use App\Models\Order;
use App\Models\User;
use App\Models\GameAccess;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Library\NickHelper;
use Log;
use Validator;

class AccController extends Controller
{
    function analytic(Request $request){
        $input = $request->input();
        switch ($input['action']??null) {
            case 'timeline':
                $result = NickHelper::timeline($input);
            break;

            default:
                $result = [];
            break;
        }
        return response()->json($result);
    }
    function index(Request $request){
        $input = $request->input();
        $result = [];
        try {
            if (config('etc.acc.off_servie')) {
                return response()->json(['message' => 'Bảo trì hệ thống, vui lòng quay lại sau', 'data' => null, 'status' => 0]);
            }
            /*params: data*/
            switch ($input['data']??null) {
                case 'config':
                    $result = \Arr::only(config('etc'), ['acc', 'acc_property']);
                break;
                case 'category_list':
                    /* params: module*/
                    $result = $this->category_list($input);
                break;
                case 'category_list_random':
                    /* params: module*/
                    $result = $this->category_list_random($input);
                break;
                case 'category_detail':
                    /* params: id*/
                    $result = $this->category_detail($input);
                break;
                case 'list_acc':
                    /*
                        params: |id|group_ids|title|status|cat_slug|auto_category ;
                            - auto_category: danh sách ids phân cách dấu phẩy
                    */
                    $result = $this->get_list_acc($input);
                break;
                case 'acc_detail':
                    /* params: id */
                    $result = $this->get_acc_detail($input);
                break;
                case 'buy_acc':
                    /* params: id, shop_id, user_id, amount */
                    $result = $this->buy_acc($input);
                break;
                case 'show_password':
                    /* params: id, user_id, ip */
                    $result = $this->show_password($input);
                break;
                case 'property_lienminh_auto':
                    /* params: thêm param id nếu lấy 1 thuộc tính */
                    $result = $this->property_lienminh_auto($input);
                break;
                case 'property_auto':
                    /* params: provider, thêm param id nếu lấy 1 thuộc tính */
                    $result = $this->property_auto($input);
                break;
                case 'nick_refund':
                    /* params: provider, thêm param id nếu lấy 1 thuộc tính */
                    $result = $this->refund_nick($input);
                    break;
                case 'delete_nick_refund':
                    /* params: provider, thêm param id nếu lấy 1 thuộc tính */
                    $result = $this->delete_nick_refund($input);
                    break;
                default:
                    return response('abort', 404);
                break;
            }
        } catch (\Exception $e) {
            $result = ['message' => $e->getMessage(), 'desc' => "Error line: ".$e->getLine(), 'status' => 0];
            \Log::error("Api AccController line ".$e->getLine()." ".$e->getMessage());
        }
        return response()->json(['message' => 'Thành công', 'data' => $result, 'status' => 1]);
    }

    function property_lienminh_auto($input){
        if (!empty($input['id'])) {
            $prop = cache("property_lienminh_auto_{$input['id']}");
            if (empty($prop)) {
                $prop = GameAutoProperty::where(['id' => $input['id']])->with(['childs' => function($query){
                    $query->select('id', 'key', 'parent_id', 'parent_table', 'name')->with(['childs' => function($query){
                        $query->select('id', 'key', 'parent_id', 'parent_table', 'name');
                    }]);
                }])->first();
                cache(["property_lienminh_auto_{$input['id']}" => $prop], 600);
            }
            $items = $prop->nicks()->with(['category'])->where('module', 'acc')->whereHas('access_category', function($query){
                $query->where('active', 1);
            })->where(function($query) use($input){
                $query->whereHas('access_shops', function($query) use($input){
                    $query->where('shop.id', $input['shop_id']);
                })->orWhereHas('author', function($query) use($input){
                    $query->where('shop_access', 'all');
                })->orWhereHas('access_shop_groups', function($query) use($input){
                    $query->where('shop_group.id', $input['shop_group_id']);
                });
            });
            if (isset($input['status'])) {
                $items->where('status', $input['status']);
            }
            if (!empty($input['started_at'])) {
                $items->where('published_at', '>=', $input['started_at']);
            }
            if (!empty($input['ended_at'])) {
                $items->where('published_at', '<=', $input['ended_at']);
            }
            if (!empty($input['price'])) {
                $price = explode('-', $input['price']);
                $items->where('price', '>=', $price[0]);
                if (!empty($price[1])) {
                    $items->where('price', '<=', $price[1]);
                }
            }
            if (!empty($input['sort'])) {
                if ($input['sort'] == 'random') {
                    $orders = ['asc', 'desc'];
                    $result->orderBy('order', $orders[array_rand($orders)]);
                }elseif(in_array($input['sort'], ['asc', 'desc'])){
                    $items->orderBy($input['sort_by']??'id', $input['sort']);
                }
            }else{
                $items->orderBy('order', 'asc');
            }
            $items->select('id', 'title', 'slug', 'idkey', 'shop_id', 'parent_id', 'author_id', 'image', 'price_old', 'price', 'sticky', 'status', 'params', 'created_at', 'published_at', 'order');
            $items = $items->paginate($input['limit']??10)->appends($input);
            $items->map(function($item) {
                $item->randId = \App\Library\Helpers::encodeItemID($item->id);
                if ($item->category->display_type == 2 && !empty($item->category->params->price)) {
                    $item->price_atm = \App\Library\HelpMoneyPercent::shop_price_atm($item->category->params->price);
                    $item->price_old_atm = \App\Library\HelpMoneyPercent::shop_price_atm($item->category->params->price_old);
                    $item->price = \App\Library\HelpMoneyPercent::shop_price($item->category->params->price);
                    $item->price_old = \App\Library\HelpMoneyPercent::shop_price($item->category->params->price_old);
                }else{
                    $item->price_atm = \App\Library\HelpMoneyPercent::shop_price_atm($item->price);
                    $item->price_old_atm = \App\Library\HelpMoneyPercent::shop_price_atm($item->price_old);
                    $item->price = \App\Library\HelpMoneyPercent::shop_price($item->price);
                    $item->price_old = \App\Library\HelpMoneyPercent::shop_price($item->price_old);
                }
                return $item;
            });
            $result = compact('prop', 'items');
        }else{
            $result = Group::where(['position' => 'lienminh', 'status' => 1])->with(['auto_properties' => function($query){
                $query->select('id', 'provider', 'key', 'parent_id', 'parent_table', 'name')->with(['childs' => function($query){
                    $query->select('id', 'key', 'parent_id', 'parent_table', 'name');
                }]);
            }])->with(['childs', 'custom' => function($query) use($input){
                $query->where('shop_id', $input['shop_id']);
            }])->first();
        }
        return $result;
    }

    function property_auto($input){
        if (!empty($input['id'])) {
            $prop = GameAutoProperty::where(['id' => $input['id']])->with(['childs' => function($query){
                $query->select('id', 'key', 'parent_id', 'parent_table', 'name')->with(['childs' => function($query){
                    $query->select('id', 'key', 'parent_id', 'parent_table', 'name');
                }]);
            }])->first();
            $items = $prop->nicks()->with(['category'])->where('module', 'acc')->whereHas('access_category', function($query){
                $query->where('active', 1);
            })->where(function($query) use($input){
                $query->whereHas('access_shops', function($query) use($input){
                    $query->where('shop.id', $input['shop_id']);
                })->orWhereHas('author', function($query) use($input){
                    $query->where('shop_access', 'all');
                })->orWhereHas('access_shop_groups', function($query) use($input){
                    $query->where('shop_group.id', $input['shop_group_id']);
                });
            });
            if (isset($input['status'])) {
                $items->where('status', $input['status']);
            }
            if (!empty($input['started_at'])) {
                $items->where('published_at', '>=', $input['started_at']);
            }
            if (!empty($input['ended_at'])) {
                $items->where('published_at', '<=', $input['ended_at']);
            }
            if (!empty($input['price'])) {
                $price = explode('-', $input['price']);
                $items->where('price', '>=', $price[0]);
                if (!empty($price[1])) {
                    $items->where('price', '<=', $price[1]);
                }
            }
            if (!empty($input['sort'])) {
                if ($input['sort'] == 'random') {
                    $orders = ['asc', 'desc'];
                    $result->orderBy('order', $orders[array_rand($orders)]);
                }elseif(in_array($input['sort'], ['asc', 'desc'])){
                    $items->orderBy($input['sort_by']??'id', $input['sort']);
                }
            }else{
                $items->orderBy('order', 'asc');
            }
            $items->select('id', 'title', 'slug', 'idkey', 'shop_id', 'parent_id', 'author_id', 'image', 'price_old', 'price', 'sticky', 'status', 'params', 'created_at', 'published_at', 'order');
            $items = $items->paginate($input['limit']??10)->appends($input);
            $items->map(function($item) {
                $item->randId = \App\Library\Helpers::encodeItemID($item->id);
                if ($item->category->display_type == 2 && !empty($item->category->params->price)) {
                    $item->price_atm = \App\Library\HelpMoneyPercent::shop_price_atm($item->category->params->price);
                    $item->price_old_atm = \App\Library\HelpMoneyPercent::shop_price_atm($item->category->params->price_old);
                    $item->price = \App\Library\HelpMoneyPercent::shop_price($item->category->params->price);
                    $item->price_old = \App\Library\HelpMoneyPercent::shop_price($item->category->params->price_old);
                }else{
                    $item->price_atm = \App\Library\HelpMoneyPercent::shop_price_atm($item->price);
                    $item->price_old_atm = \App\Library\HelpMoneyPercent::shop_price_atm($item->price_old);
                    $item->price = \App\Library\HelpMoneyPercent::shop_price($item->price);
                    $item->price_old = \App\Library\HelpMoneyPercent::shop_price($item->price_old);
                }
                return $item;
            });
            $result = compact('prop', 'items');
        }else{
            $result = Group::where(['position' => $input['provider'], 'status' => 1])->with(['auto_properties' => function($query) use($input){
                $query->select('id', 'provider', 'key', 'parent_id', 'parent_table', 'name')->with(['childs' => function($query){
                    $query->select('id', 'key', 'parent_id', 'parent_table', 'name')->with(['childs' => function($query){
                        $query->select('id', 'key', 'parent_id', 'parent_table', 'name');
                    }]);
                }]);
            }])->with(['childs', 'custom' => function($query) use($input){
                $query->where('shop_id', $input['shop_id']);
            }])->first();
        }
        return $result;
    }

    function category_list($input){
        if (empty($input['module'])) {
            $input['module'] = 'acc_provider';
        }
        $result = Group::where('module', $input['module']??'acc_provider')->orderBy('order')->with(['childs' => function($query) use($input){
            if (($input['module']??'acc_provider') == 'acc_provider') {
                $query->withCount(['nicks' => function($query) use($input){
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

        if (!empty($input['id_option'])) {
            $result->whereIn('id', $input['id_option']);
        }

        if (!empty($input['id_not_option'])) {
            $id_not_option = explode('|',$input['id_not_option']);
            $result->whereNotIn('id', $id_not_option);
        }

        if (($input['module']??'acc_provider') == 'acc_category') {
            $result->withCount(['nicks' => function($query) use($input){
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
        $result = $result->get();
        if ($input['module'] == 'acc_provider') {
            $result = $result->map(function($value) {
                $value->childs->map(function($item) {
                    $item->lm_auto = 0;
                    $item->items_count = $item->nicks_count;
                    if ( $item->position == 'lienminh' ) {
                        $item->lm_auto = 1;
                    }
                    if (!empty($item->params->price) || !empty($item->custom->meta['price'])) {
                        $item->price = \App\Library\HelpMoneyPercent::shop_price($item->custom->meta['price']??$item->params->price);
                    }
                    if (!empty($item->params->price_old) || !empty($item->custom->meta['price_old'])) {
                        $item->price_old = \App\Library\HelpMoneyPercent::shop_price($item->custom->meta['price_old']??$item->params->price_old);
                    }
                });
                $value->childss = array_values($value->childs->sortBy('custom.order')->toArray());
                return $value;
            })->toArray();
            foreach ($result as $key => $value) {
                $result[$key]['childs'] = $value['childss'];
                unset($result[$key]['childss']);
            }
        }elseif ($input['module'] == 'acc_category') {
            $result->map(function($value) use($input) {
                $value->lm_auto = 0;
                $value->items_count = $value->nicks_count;
                if ( $value->position == 'lienminh' ) {
                    $value->lm_auto = 1;
                }
                if (!empty($value->params->price) || !empty($value->custom->meta['price'])) {
                    $value->price = \App\Library\HelpMoneyPercent::shop_price($value->custom->meta['price']??$value->params->price);
                }
                if (!empty($value->params->price_old) || !empty($value->custom->meta['price_old'])) {
                    $value->price_old = \App\Library\HelpMoneyPercent::shop_price($value->custom->meta['price_old']??$value->params->price_old);
                }
            });
            $result = array_values($result->sortBy('custom.order')->toArray());
        }
        return $result;
    }

    function category_list_random($input){
        $result = Group::where('module', $input['module']??'acc_provider')->with(['childs' => function($query) use($input){
            if (($input['module']??'acc_provider') == 'acc_provider') {
                $query->withCount(['nicks' => function($query) use($input){
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
                }])->where(['display_type' => 2, 'status' => 1]);
            }
        }])->where(['status' => 1]);
        if (($input['module']??'acc_provider') == 'acc_category') {
            $result->withCount(['nicks' => function($query) use($input){
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
            }])->where('display_type', 2);
        }
        $result = $result->where('status',1)->get()->sortBy(function($query){
            return $query->custom->order??null;
        })->take($input['limit']??4);

        $result->map(function($value) use($input){
            if (($input['module']??'acc_provider') == 'acc_category') {
                if (!empty($value->params->price) || !empty($value->custom->meta['price'])) {
                    $value->price = \App\Library\HelpMoneyPercent::shop_price($value->custom->meta['price']??$value->params->price);
                }
                if (!empty($value->params->price_old) || !empty($value->custom->meta['price_old'])) {
                    $value->price_old = \App\Library\HelpMoneyPercent::shop_price($value->custom->meta['price_old']??$value->params->price_old);
                }
                $value->items_count = $value->nicks_count;
            }else{
                $value->childs->map(function ($item) use($input){
                    if (!empty($item->params->price) || !empty($item->custom->meta['price'])) {
                        $item->price = \App\Library\HelpMoneyPercent::shop_price($item->custom->meta['price']??$item->params->price);
                    }
                    if (!empty($item->params->price_old) || !empty($item->custom->meta['price_old'])) {
                        $item->price_old = \App\Library\HelpMoneyPercent::shop_price($item->custom->meta['price_old']??$item->params->price_old);
                    }
                    $item->items_count = $item->nicks_count;
                    return $item;
                });
            }
            return $value;
        });
        if (($input['module']??'acc_provider') == 'acc_category') {
            $result = array_values($result->sortBy('custom.order')->toArray());
        }
        return $result;
    }

    function category_detail($input){
        $result = Group::with(['childs', 'custom' => function($query) use($input){
            $query->where(['shop_id' => $input['shop_id'], 'status' => 1]);
        }])->whereHas('custom', function($query) use($input){
            $query->where(['groups_shops.shop_id' => $input['shop_id'], 'status' => 1]);
        })->where(['status' => 1])->withCount(['nicks' => function($query) use($input){
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
        }]);
        if (!empty($input['id'])) {
            $result->where('id', $input['id']);
        }
        if (!empty($input['slug'])) {
            $result->where(function($query) use($input){
                $query->where('slug', $input['slug'])->orWhereHas('custom', function($query) use($input){
                    $query->where(['slug' => $input['slug'], 'groups_shops.shop_id' => $input['shop_id'], 'status' => 1]);
                });
            })->whereHas('parent');
        }
        $result = $result->first();
        if (empty($result)) {
            return $result;
        }
        $result->lm_auto = 0;
        if ($result->position == 'lienminh') {
            $result->lm_auto = 1;
        }
        if (!empty($result->params->price) || !empty($result->custom->meta['price'])) {
            $result->price = \App\Library\HelpMoneyPercent::shop_price($result->custom->meta['price']??$result->params->price);
        }
        if (!empty($result->params->price_old) || !empty($result->custom->meta['price_old'])) {
            $result->price_old = \App\Library\HelpMoneyPercent::shop_price($result->custom->meta['price_old']??$result->params->price_old);
        }
        $result->items_count = $result->nicks_count;
        return $result;
    }

    function get_list_acc($input){
        if (!empty($input['user_id'])) {
            $result = (new Nick(['table' => 'nicks_completed']))->where('sticky', $input['user_id']);
        }else{
            $result = (new Nick(['table' => 'nicks']))->where(function($query) use($input){
                $query->whereHas('access_shops', function($query) use($input){
                    $query->where('shop.id', $input['shop_id']);
                })->orWhereHas('author', function($query) use($input){
                    $query->where('shop_access', 'all');
                })->orWhereHas('access_shop_groups', function($query) use($input){
                    $query->where('shop_group.id', $input['shop_group_id']);
                });
            })->whereHas('access_category', function($query){
                $query->where('active', 1);
            })->whereHas('category_custom', function($query) use($input){
                $query->where(['status' => 1, 'shop_id' => $input['shop_id']]);
            });
        }

        $result->with(['groups' => function ($query) {
            $query->select('groups.id','groups.title', 'groups.module', 'groups.display_type', 'groups.parent_id', 'groups.is_slug_override')->with(['parent' => function ($query) {
                $query->select('groups.id','groups.title', 'groups.module', 'groups.display_type', 'groups.parent_id', 'groups.is_slug_override');
            }]);
        }, 'category' => function ($query) {
            $query->select('groups.id','groups.title', 'groups.module', 'groups.display_type', 'groups.parent_id', 'groups.is_slug_override', 'groups.params');
        }, 'category_custom' => function ($query) use($input) {
            $query->where(['shop_id' => $input['shop_id']])->select('id', 'shop_id', 'meta', 'group_id');
        }])->where('module', 'acc');


        if (!empty($input['group_ids'])) {
            foreach ($input['group_ids'] as $key => $value) {
                $result->whereHas('groups', function ($query) use ($value) {
                    $query->where('group_id', $value);
                });
            }
        }
        if (!empty($input['cat_slug'])) {
            $result->where(function($query) use($input){
                $query->whereHas('category', function($query) use($input){
                    $query->where('slug', $input['cat_slug']);
                })->orWhereHas('category_custom', function($query) use($input){
                    $query->where(['status' => 1, 'shop_id' => $input['shop_id'], 'slug' => $input['cat_slug']]);
                });
            });
        }

        if (!empty($input['arr_id']))  {
            $arr_id = explode(',',$input['arr_id']);
            $result->whereIn('id', $arr_id);
        }

        if (!empty($input['randId']))  {
            $id = \App\Library\Helpers::decodeItemID($input['randId']);
            if ($id != $input['randId'] && \App\Library\Helpers::encodeItemID($id) != $input['randId']) {
                return [];
            }
            $result->where('id', $id);
        }

        if (!empty($input['author_id']))  {
            $result->where('author_id', $input['author_id']);
        }

        if (!empty($input['title']))  {
            $result->where('title', 'like', $input['title']);
        }

        if (isset($input['status'])) {
            $result->where('status', $input['status']);
        }
        if (!empty($input['started_at'])) {
            $result->where('published_at', '>=', $input['started_at']);
        }
        if (!empty($input['ended_at'])) {
            $result->where('published_at', '<=', $input['ended_at']);
        }
        if (!empty($input['price'])) {
            $price = explode('-', $input['price']);
            $price[0] = \App\Library\HelpMoneyPercent::shop_de_price($price[0]);
            $result->where('price', '>=', $price[0]);
            if (!empty($price[1])) {
                $price[1] = \App\Library\HelpMoneyPercent::shop_de_price($price[1]);
                $result->where('price', '<=', $price[1]);
            }
        }
        if (!empty($input['auto_category'])) {
            $ids = !is_array($input['auto_category'])? explode(',', $input['auto_category']): $input['auto_category'];
            foreach ($ids as $prop_id) {
                if (!empty($prop_id)) {
                    $result->whereHas('game_auto_props', function($query) use($prop_id){
                        if (strpos($prop_id, '-') > -1) {
                            $prop = explode('-', $prop_id);
                            $query->where('item_game_auto_properties.property_id', $prop[0]);
                            if (count($prop) == 2) {
                                $query->where('item_game_auto_properties.point', $prop[1]);
                            }elseif (count($prop) == 3) {
                                $query->where('item_game_auto_properties.point', '>=' , $prop[1])->where('item_game_auto_properties.point', '<=' , $prop[2]);
                            }
                        }else{
                            $query->where('item_game_auto_properties.property_id', $prop_id);
                        }
                    });
                }
            }
        }
        if (!empty($input['sort'])) {
            if ($input['sort'] == 'random') {
                $orders = ['asc', 'desc'];
                $result->orderBy('order', $orders[array_rand($orders)]);
            }elseif(in_array($input['sort'], ['asc', 'desc'])){
                $result->orderBy($input['sort_by']??'id', $input['sort']);
            }
        }else{
            $result->orderBy('order', 'asc');
        }
        $result->select('id', 'title', 'slug', 'idkey', 'shop_id', 'parent_id', 'author_id', 'image', 'price_old', 'price', 'sticky', 'status', 'params', 'created_at', 'published_at', 'order', 'description');
        $result = $result->paginate($input['limit']??10)->appends($input);
        $result->map(function($item) {
            $item->randId = \App\Library\Helpers::encodeItemID($item->id);
            if ($item->category->display_type == 2 && (!empty($item->category->params->price) || !empty($item->category_custom->meta['price']))) {
                $item->price_atm = \App\Library\HelpMoneyPercent::shop_price_atm($item->category_custom->meta['price']??$item->category->params->price);
                $item->price_old_atm = \App\Library\HelpMoneyPercent::shop_price_atm($item->category_custom->meta['price_old']??$item->category->params->price_old);
                $item->price = \App\Library\HelpMoneyPercent::shop_price($item->category_custom->meta['price']??$item->category->params->price);
                $item->price_old = \App\Library\HelpMoneyPercent::shop_price($item->category_custom->meta['price_old']??$item->category->params->price_old);
            }else{
                $item->price_atm = \App\Library\HelpMoneyPercent::shop_price_atm($item->price);
                $item->price_old_atm = \App\Library\HelpMoneyPercent::shop_price_atm($item->price_old);
                $item->price = \App\Library\HelpMoneyPercent::shop_price($item->price);
                $item->price_old = \App\Library\HelpMoneyPercent::shop_price($item->price_old);
            }
            return $item;
        });
        return $result;
    }

    function get_acc_detail($input){
        $id = \App\Library\Helpers::decodeItemID($input['id']);
        if ($id != $input['id'] && \App\Library\Helpers::encodeItemID($id) != $input['id']) {
            return null;
        }
        $input['id'] = $id;
        if (!empty($input['user_id'])) {
            $result = (new Nick(['table' => 'nicks_completed']))->where('sticky', $input['user_id']);
        }else{
            $result = (new Nick(['table' => 'nicks']))->where(function($query) use($input){
                $query->whereHas('access_shops', function($query) use($input){
                    $query->where('shop.id', $input['shop_id']);
                })->orWhereHas('author', function($query) use($input){
                    $query->where('shop_access', 'all');
                })->orWhereHas('access_shop_groups', function($query) use($input){
                    $query->where('shop_group.id', $input['shop_group_id']);
                });
            })->whereHas('access_category', function($query){
                $query->where('active', 1);
            })->whereHas('access_category', function($query){
                $query->where('active', 1);
            });
        }
        $result->with(['groups' => function ($query) {
            $query->select('groups.id','groups.title', 'groups.module', 'groups.display_type', 'groups.parent_id', 'groups.is_slug_override', 'groups.position')->with(['parent' => function ($query) {
                $query->select('groups.id','groups.title', 'groups.module', 'groups.display_type', 'groups.parent_id', 'groups.is_slug_override', 'groups.position');
            }]);
        }, 'category' => function($query) use($input){
            $query->with(['childs' => function ($query) {
                $query->select('groups.id','groups.title', 'groups.module', 'groups.display_type', 'groups.parent_id', 'groups.is_slug_override', 'groups.position', 'groups.params');
            }, 'custom' => function($query) use($input){
                $query->where('shop_id', $input['shop_id'])->select('id', 'title', 'shop_id', 'meta', 'group_id');
            }]);
        }, 'game_auto_props' => function ($query) {
            $query->withPivot('level', 'point', 'grade');
        }])->where(['module' => 'acc', 'id' => $input['id']]);

        if (!empty($input['user_id']) && !empty($input['order_refund'])){//Lấy y/c hoàn tiền
            $result->with(['txns_order' => function($query){
                $query->select('id','ref_id','price');
                $query->with(['order_nick_refund' => function($query){
                    $query->select('id','order_id','module','title','description','content','status','created_at');
                }]);
            }]);
        }

        $result->select('id', 'title', 'slug', 'idkey', 'shop_id', 'parent_id', 'author_id', 'params', 'image', 'image_extension',
            'price_old', 'price', 'sticky', 'status', 'created_at', 'published_at', 'description', 'content'
        );
        $result = $result->first();
        if (!empty($result)) {
            $result->randId = \App\Library\Helpers::encodeItemID($result->id);
            if ($result->category->display_type == 2 && (!empty($result->category->params->price) || !empty($result->category->custom->meta['price']))) {
                $result->price_atm = \App\Library\HelpMoneyPercent::shop_price_atm($result->category->custom->meta['price']??$result->category->params->price);
                $result->price_old_atm = \App\Library\HelpMoneyPercent::shop_price_atm($result->category->custom->meta['price_old']??$result->category->params->price_old);
                $result->price = \App\Library\HelpMoneyPercent::shop_price($result->category->custom->meta['price']??$result->category->params->price);
                $result->price_old = \App\Library\HelpMoneyPercent::shop_price($result->category->custom->meta['price_old']??$result->category->params->price_old);
            }else{
                $result->price_atm = \App\Library\HelpMoneyPercent::shop_price_atm($result->price);
                $result->price_old_atm = \App\Library\HelpMoneyPercent::shop_price_atm($result->price_old);
                $result->price = \App\Library\HelpMoneyPercent::shop_price($result->price);
                $result->price_old = \App\Library\HelpMoneyPercent::shop_price($result->price_old);
            }
        }
        return $result;
    }

    function buy_acc($input){
        if (!empty($input['rand_cat_id'])) {
            $selects = Nick::where(['module' => 'acc', 'parent_id' => $input['rand_cat_id'], 'status' => 1, 'shop_id' => null, 'sticky' => null])
            ->whereHas('category', function($query){
                $query->where('display_type', 2);
            })->whereHas('access_category', function($query){
                $query->where('active', 1);
            })->where(function($query) use($input){
                $query->whereHas('access_shops', function($query) use($input){
                    $query->where('shop.id', $input['shop_id']);
                })->orWhereHas('author', function($query) use($input){
                    $query->where('shop_access', 'all');
                })->orWhereHas('access_shop_groups', function($query) use($input){
                    $query->where('shop_group.id', $input['shop_group_id']);
                });
            })->select('id','module','parent_id','status','shop_id','sticky', 'order')->orderBy('order')->take(200)->get();
            if ($selects->count()) {
                $random = $selects->random();
                $input['id'] = $random->id;
                $acc = Nick::where(['id' => $random->id, 'status' => 1, 'shop_id' => null, 'sticky' => null])
                ->with(['author', 'category', 'category_custom'=> function ($query) use($input) {
                    $query->where(['shop_id' => $input['shop_id']])->select('id', 'shop_id', 'meta', 'group_id');
                }])->first();
            }
        }else{
            $input['id'] = \App\Library\Helpers::decodeItemID($input['id']);
            $id = \App\Library\Helpers::decodeItemID($input['id']);
            if ($id != $input['id'] && \App\Library\Helpers::encodeItemID($id) != $input['id']) {
                return ['success' => 0, 'message' => 'Mã không hợp tồn tại'];
            }
            $input['id'] = $id;
            $acc = Nick::where(['id' => $input['id'], 'status' => 1, 'shop_id' => null, 'sticky' => null])->whereHas('access_category', function($query){
                $query->where('active', 1);
            })->where(function($query) use($input){
                $query->whereHas('access_shops', function($query) use($input){
                    $query->where('shop.id', $input['shop_id']);
                })->orWhereHas('author', function($query) use($input){
                    $query->where('shop_access', 'all');
                })->orWhereHas('access_shop_groups', function($query) use($input){
                    $query->where('shop_group.id', $input['shop_group_id']);
                });
            })->with(['author', 'category', 'category_custom'=> function ($query) use($input) {
                $query->where(['shop_id' => $input['shop_id']])->select('id', 'shop_id', 'meta', 'group_id');
            }])->first();
        }
        if (empty($acc)) {
            return ['success' => 0, 'message' => 'acc đã có sở hữu hoặc không còn hiệu lực'];
        }
        $author_price = $acc->price;
        if ($acc->category->display_type == 2 && (!empty($acc->category->params->price) || !empty($acc->category_custom->meta['price']))) {
            $input['amount'] = \App\Library\HelpMoneyPercent::shop_price($acc->category_custom->meta['price']??$acc->category->params->price);
            if (!empty($input['amount'])) {
                $author_price = $acc->category_custom->meta['price']??$acc->category->params->price;
            }
        }else{
            $input['amount'] = \App\Library\HelpMoneyPercent::shop_price($acc->price);
        }
        if ($input['amount'] <= 0) {
            return ['success' => 0, 'message' => 'Giá không hợp lệ'];
        }
        $user = User::find($input['user_id']);
        if (!$user->checkBalanceValid() || $user->balance < $input['amount']) {
            return ['success' => 0, 'message' => 'Số dư không đủ hoặc biến động số dư không hợp lệ'];
        }
        $access = GameAccess::where(['group_id' => $acc->parent_id, 'user_id' => $acc->author_id])->first();
        if (($access->active??null) != 1) {
            return ['success' => 0, 'message' => 'Acc không còn hiệu lực bán'];
        }
        $tran = $this->lock_trans("buy_acc_{$input['id']}", function () use($input, $author_price) {
            $result = \DB::transaction(function () use($input, $author_price) {
                $user = User::lockForUpdate()->find($input['user_id']);
                if ($user->balance >= $input['amount']) {
                    $acc = Nick::where(['id' => $input['id'], 'status' => 1, 'shop_id' => null, 'sticky' => null])->first();
                    if ($acc) {
                        User::lockForUpdate()->where('id', $input['user_id'])->update([
                            'balance' => \DB::raw("balance - ".$input['amount']),
                            'balance_out' => \DB::raw("balance_out + ".$input['amount'])
                        ]);
                        $user = User::find($input['user_id']);
                        $acc->fill(['status' => 2, 'shop_id' => $input['shop_id'], 'sticky' => $input['user_id'], 'published_at' => date('Y-m-d H:i:s'), 'price' => $author_price, 'amount' => $input['amount']])->save();
                        $order = Order::create([
                            'module' => 'buy_acc', 'ref_id' => $acc->id, 'author_id' => $user->id, 'shop_id' => $acc->shop_id, 'status' => 2,
                            'price' => $input['amount'], 'description' => "Mua acc #{$acc->id}",
                        ]);
                        $txns = Txns::create([
                            'shop_id' => $acc->shop_id, 'trade_type' => 'buy_acc', 'user_id' => $user->id, 'order_id' => $order->id, 'amount' => $order->price,
                            'last_balance' => $user->balance, 'is_add' => 0, 'is_refund' => 0, 'status' => 1, 'txnsable_type' => 'App\Models\NickComplete', 'txnsable_id' => $acc->id,
                            'description' => "Trừ tiền mua acc #{$acc->id}"
                        ]);
                        try {
                            $complete_data = $acc->toArray();
                            $complete_data['params'] = json_encode($complete_data['params']);
                            $complete_data['meta'] = json_encode($complete_data['meta']);
                            \DB::table('nicks_completed')->insert([$complete_data]);
                        } catch (\Exception $e) {
                            \Log::error("buy_acc create complete error #{$acc->id}: ".$e->getMessage());
                        }

                        $complete = (new Nick(['table' => 'nicks_completed']))->where('id', $acc->id)->first();
                        if ($complete) {
                            \DB::table('nicks')->where('id', $acc->id)->delete();
                            $acc = $complete;
                        }
                        $message = 'Thành công';
                        return compact('user', 'order', 'txns', 'acc', 'message');
                    }else{
                        return ['message' => 'Acc đã có sở hữu'];
                    }
                }
                return ['message' => 'Số dư không đủ'];
            });
            if (!$result) {
                return ['message' => 'Acc đã có sở hữu'];
            }
            return $result;
        });
        $result = ['success' => empty($tran['acc'])? 0: 1, 'message' => $tran['message']];
        if (!empty($tran['order'])) {
            $category = Group::find($tran['acc']->parent_id);
            if (in_array($category->is_display, array_keys(config('etc.acc_property.check_login')))) {
                $tran['acc']->fill(['status' => 3])->save();
                $job = new \App\Jobs\AccCheckLogin($tran['order']);
                dispatch($job);
            }else{
                $tran['acc']->fill(['status' => 12])->save();
//                $author = $this->author_trans($tran['acc'], $tran['order']);
            }
        }
        if (!empty($tran['acc'])) {
            $result['acc'] = ['id' => $tran['acc']->id, 'randId' => \App\Library\Helpers::encodeItemID($tran['acc']->id, $input['shop_id'])];
        }
        return $result;
    }

    function show_password($input){
        $input['id'] = \App\Library\Helpers::decodeItemID($input['id']);
        $acc = (new Nick(['table' => 'nicks_completed']))
            ->where(function($q){
                $q->orWhere('status', '=',12);
                $q->orWhere('status', '=',13);
                $q->orWhere('status', '=',0);
            })->where(['sticky' => $input['user_id'], 'id' => $input['id']])->first();
        if (empty($acc)) {
            return ['success' => 0, 'message' => 'Bạn không có quyền này'];
        }
        if (!empty($acc->params['show_password'])) {
            return ['success' => 0, 'message' => "Đã xem lúc {$acc->params['show_password']['time']}"];
        }
        $params = json_decode(json_encode($acc->params), true)??[];
        $params['show_password'] = ['time' => date('Y-m-d H:i:s'), 'ip' => $input['ip']??null];
        $acc->fill(['params' => $params])->save();
        return ['success' => 1, 'message' => "Thành công"];
    }

    function lock($name, $callback){
        $lock = Cache::lock($name);
        $times = 10*10; /*10 seconds*/
        while (!$lock->get() && $times) {
            usleep(100000);
            $times--;
        }
        try {
            return $callback();
        } finally {
            $lock->release();
        }
    }

    function callback_login(Request $request){
        $input = $request->input();
        try {
            $map_status = [0 => 4, 1 => 12, 2 => 6];
            $order = Order::find($input['tranid']);
            $item = $this->lock_trans("callback_login_{$order->id}", function () use($order, $input, $map_status) {
                if ((new Nick(['table' => 'nicks_completed']))->where(['id' => $order->ref_id, 'status' => 3])->update(['status' => $map_status[$input['status']]])) {
                    return (new Nick(['table' => 'nicks_completed']))->where(['id' => $order->ref_id, 'status' => $map_status[$input['status']]])->first();
                }else{
                    return null;
                }
            });
            if (empty($item)) {
                return response('item này không được phép callback');
            }
            if (in_array($item->status, [4])) {
                $txns = Txns::where(['order_id' => $order->id, 'is_add' => 0, 'is_refund' => 0, 'status' => 1])->first();
                $user = $this->lock_trans("user_trans_{$txns->user_id}", function () use($txns) {
                    return \DB::transaction(function () use($txns) {
                        User::lockForUpdate()->where('id', $txns->user_id)->update([
                            'balance' => \DB::raw("balance + ".$txns->amount),
                            'balance_in_refund' => \DB::raw("balance_in_refund + ".$txns->amount)
                        ]);
                        return User::find($txns->user_id);
                    });
                });
                $refund = Txns::create([
                    'shop_id' => $txns->shop_id, 'trade_type' => $txns->trade_type, 'user_id' => $user->id, 'order_id' => $txns->order_id, 'amount' => $txns->amount,
                    'last_balance' => $user->balance, 'is_add' => 1, 'is_refund' => 1, 'status' => 1, 'txnsable_type' => 'App\Models\NickComplete', 'txnsable_id' => $txns->txnsable_id,
                    'description' => "Hoàn tiền mua acc #{$item->id}",
                ]);
                $resp = 'hoàn tiền';
                $check = $user->checkBalanceValid();
            }elseif ($item->status == 6) {
                $item->fill(['status' => 3])->save();
                if (empty($order->order) || $order->order < 3) {
                    $order->fill(['order' => $order->order+1])->save();
                    $resp = 'check lại';
                    $job = (new \App\Jobs\AccCheckLogin($order))->delay(Carbon::now()->addMinutes(1));
                    dispatch($job);
                }else{
                    $resp = 'Đã check quá 3 lần';
                }
            }else{
                $resp = 'thành công';
//                $author = $this->author_trans($item, $order);
            }
        } catch (\Exception $e) {
            $resp = $e->getMessage();
            \Log::error("Api AccController line ".$e->getLine()." ".$e->getMessage());
        }
        return response($resp);
    }

    function author_trans($item, $order){
        $add_price = $item->price;
        /*Tính lại chiết khấu*/
        $discount = GameAccess::where(['user_id' => $item->author_id, 'group_id' => $item->parent_id])->first();
        if (!empty($discount)) {
            $step = 0;
            $ratio = 0;
            foreach ($discount->ratio as $key => $value) {
                if ($key > 0) {
                    $step = $key;
                    if ($add_price <= $key) {
                        $ratio = $value;
                        break;
                    }
                }elseif ($key == 'over' && $add_price > $step) {
                    $ratio = $value;
                }
            }
            if ((empty($ratio) || $ratio == 0) && !empty($discount['default'])) {
                $ratio = $discount['default'];
            }
            if ($ratio > 0) {
                $add_price = $add_price - $add_price*$ratio/100;
            }
        }
        $author = $this->lock_trans("user_trans_{$item->author_id}", function () use($item, $add_price, $order) {
            if (Txns::where(['trade_type' => 'buy_acc','order_id' => $order->id,'is_add' => 1,'is_refund' => 0,'status' => 1])->exists()) {
                return null;
            }else{
                return \DB::transaction(function () use($item, $add_price, $order) {
                    User::lockForUpdate()->where('id', $item->author_id)->update([
                        'balance' => \DB::raw("balance + ".$add_price),
                        'balance_in' => \DB::raw("balance_in + ".$add_price)
                    ]);
                    $author = User::lockForUpdate()->find($item->author_id);
                    $author_txns = Txns::create([
                        'shop_id' => $item->shop_id, 'trade_type' => 'buy_acc', 'user_id' => $author->id, 'order_id' => $order->id, 'amount' => $add_price,
                        'last_balance' => $author->balance, 'is_add' => 1, 'is_refund' => 0, 'status' => 1, 'txnsable_type' => 'App\Models\NickComplete', 'txnsable_id' => $item->id,
                        'description' => "Cộng tiền bán acc #{$item->id}",
                    ]);
                    return $author;
                });
            }
        });
        if ($author) {
            $real_received_price = $order->price - $add_price;
            $order->fill(['real_received_price' => $real_received_price, 'ratio' => $real_received_price*100/$order->price])->save();
            \DB::table('nicks_completed')->where('id', $item->id)->update(['amount_ctv' => $add_price]);
            $check = $author->checkBalanceValid();
        }else{
            \Log::error("author_trans exists for nick #{$item->id}");
        }
        return $author;
    }

    function lock_trans($name, $callback){
        $lock = Cache::lock($name);
        $times = 5*10; /*5 seconds*/
        while (!$lock->get() && $times) {
            usleep(100000);
            $times--;
        }
        try {
            return $callback();
        } finally {
            $lock->release();
        }
    }

    function refund_nick($input){
        DB::beginTransaction();
        try {

            $input['id'] = \App\Library\Helpers::decodeItemID($input['id']);
            $id = \App\Library\Helpers::decodeItemID($input['id']);
            if ($id != $input['id'] && \App\Library\Helpers::encodeItemID($id) != $input['id']) {
                return ['success' => 0, 'message' => 'Mã không hợp tồn tại'];
            }
            $input['id'] = $id;

            $acc = (new Nick(['table' => 'nicks_completed']))->where(['status' => 12,'sticky' => $input['user_id'], 'id' => $input['id']])->lockForUpdate()->first();

            if (empty($acc) && empty($acc->txns_order)) {
                DB::rollback();
                return ['success' => 0, 'message' => 'Không tìm thấy nick'];
            }

            $order = $acc->txns_order;
            $content = $input['content']??'';

            if (!empty($order->order_nick_refund)) {
                DB::rollback();
                return ['success' => 0, 'message' => 'Bạn đã gửi yêu cầu hoàn tiền'];
            }

            $txns = Txns::where(['order_id' => $order->id, 'is_add' => 0, 'is_refund' => 0, 'status' => 1])->first();

            if(empty($txns)){
                DB::rollback();
                return ['success' => 0, 'message' => 'Không tìm thấy giao dịch trước đó'];
            }

            $userTransaction = User::where('id', $txns->user_id)->lockForUpdate()->firstOrFail();

            if (!isset($userTransaction)) {
                DB::rollback();
                return ['success' => 0, 'message' => 'Không tìm thấy khách hàng'];
            }
            //update trạng thái cho nick

            $acc->status = 13;
            $acc->save();

            //tạo ticksy

            $images = json_decode($input['images'],JSON_UNESCAPED_UNICODE);
            $array_images = [];
            if (count($images)){
                foreach ($images as $image){

                    $info = MediaHelpers::imageBase64($image,'/storage/upload/images/nick/nick-');

                    array_push($array_images,$info);
                }
            }

            $params['image_customer'] = $array_images;

            $params['account'] = $input['account']??'';
            $params['password'] = $input['password']??'';

            OrderDetail::create([
                'order_id' => $order->id,
                'description' => $content,
                'module' => 'nick-refund',
                'author_id' => $userTransaction->id,
                'content' => json_encode($params,JSON_UNESCAPED_UNICODE),
                'status' => 2,//chờ xử lý
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error( $e);
            return ['success' => 0, 'message' => 'Có lỗi phát sinh.Xin vui lòng thử lại !'];
        }
        // Commit the queries!
        DB::commit();

        return ['success' => 1, 'message' => 'Yêu cầu hoàn tiền thành công'];
    }

    function delete_nick_refund($input){
        DB::beginTransaction();
        try {
            $input['id'] = \App\Library\Helpers::decodeItemID($input['id']);
            $id = \App\Library\Helpers::decodeItemID($input['id']);
            if ($id != $input['id'] && \App\Library\Helpers::encodeItemID($id) != $input['id']) {
                return ['success' => 0, 'message' => 'Mã không hợp tồn tại'];
            }
            $input['id'] = $id;
            $acc = (new Nick(['table' => 'nicks_completed']))->where(['status' => 13,'sticky' => $input['user_id'], 'id' => $input['id']])->lockForUpdate()->first();

            if (empty($acc) && empty($acc->txns_order)) {
                DB::rollback();
                return ['success' => 0, 'message' => 'Không tìm thấy nick'];
            }

            $order = $acc->txns_order;

            if (empty($order->order_nick_refund)) {
                DB::rollback();
                return ['success' => 0, 'message' => 'Không tìm thấy yêu cầu hoàn tiền.'];
            }

            $order_refund = $order->order_nick_refund;

            //Cập nhật trạng thái yêu cầu hoàn tiền.
            $order_refund->status = 0;
            $order_refund->save();

            //update trạng thái cho nick

            $acc->status = 12;
            $acc->published_at = Carbon::now();//Thời gian xác nhận đơn hàng
            $acc->save();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error( $e);
            return ['success' => 0, 'message' => 'Có lỗi phát sinh.Xin vui lòng thử lại !'];
        }
        // Commit the queries!
        DB::commit();

        return ['success' => 1, 'message' => 'Yêu cầu hoàn tiền thành công'];
    }
}
