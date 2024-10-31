<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Item;
use App\Models\Nick;
use App\Models\NickComplete;
use App\Models\Media;
use App\Models\OrderDetail;
use App\Models\Txns;
use App\Models\User;
use App\Models\Order;
use App\Models\Shop;
use App\Models\GameAutoProperty;
use App\Library\Helpers;
use App\Library\MediaHelpers;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon, DB;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Log;

class AccController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;
    protected $type;
    public function __construct(Request $request)
    {
        config(['etc.used_vue' => true]);
        $this->module=$request->segments()[1]??"";
        $this->middleware('permission:acc-list-1', ['only' => ['index_1']]);
        $this->middleware('permission:acc-list-2', ['only' => ['index_2']]);
        $this->middleware('permission:acc-edit', ['only' => ['quick']]);
        $this->middleware('permission:acc-history', ['only' => ['history', 'analytic']]);
        $this->middleware('permission:acc-property', ['only' => ['property', 'property_edit','property_order']]);
    }

    public function index_1(Request $request){
        $this->type = 1;
        return $this->index($request);
    }

    public function index_2(Request $request){
        $this->type = 2;
        return $this->index($request);
    }

    public function index(Request $request) {
        if (!empty($_GET['decode_id'])) {
            dd(Helpers::decodeItemID($_GET['decode_id']));
        }
        $type = $this->type;
        $this->module = 'acc';
        $status = \Arr::where(config('etc.acc.status'), function($value, $key){
            return in_array($key, [1, 4, 5, 6, 7, 8, 9, 11]);
        });
        if($request->ajax) {
            $datatable= Nick::where('module', $this->module)->whereNull('shop_id')->with(['category' => function($query){
                $query->select('id', 'title', 'module', 'display_type')->with(['custom' => function($query){
                    $query->where('shop_id', session('shop_id')??null);
                }]);
            }, 'author' => function($query){
                $query->select('id', 'username');
            }, 'access_shops']);
            if (!auth()->user()->hasRole('admin')) {
                $datatable->where('author_id', auth()->user()->id);
            }
            $datatable->whereHas('category', function ($query) use($type){
                $query->where('display_type', $type);
            });
            if (session()->has('shop_id')) {
                $datatable->whereHas('category_custom', function ($query){
                    $query->where(['status' => 1, 'shop_id' => session('shop_id')]);
                })->whereHas('access_shops', function($query){
                    $query->where('shop.id', session('shop_id'));
                });
            }
            if ($request->filled('group_id')) {
                $datatable->where('parent_id', $request->get('group_id'));
                // $datatable->whereHas('groups', function ($query) use ($request) {
                //     $query->where('group_id',$request->get('group_id'));
                // });
            }

            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', \App\Library\Helpers::decodeItemID($request->get('id')));
                    $q->orWhere('idkey', $request->get('id'));
                });
            }
            if ($request->filled('title'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
                });
            }
            if ($request->filled('author'))  {
                $datatable->whereHas('author', function($q) use($request){
                    $q->where('username', 'LIKE', '%' . $request->get('author') . '%');
                });
            }
            if ($request->filled('seo_title'))  {
                $datatable->where('seo_title', 'LIKE', '%' . $request->get('seo_title') . '%');
            }

            if ($request->filled('status')) {
                $datatable->where('status',$request->get('status') );
            }else{
                $datatable->whereIn('status', array_keys($status));
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
                    'slug',
                    'image',
                    'locale',
                    'order',
                    'position',
                    'status',
                    'action',
                    'price_old',
                    'price',
                    'published_at',
                    'created_at',
                    'author',
                    'access_shops',
                    'randId',
                    'category', 'price_shop', 'price_old_shop'
                ])->editColumn('created_at', function($row) {
                    return date('d/m/Y H:i:s', strtotime($row->created_at));
                })->editColumn('published_at', function($row) {
                    return empty($row->published_at)? '': date('d/m/Y H:i:s', strtotime($row->published_at));
                })->editColumn('price', function($row) {
                    if ($row->category->display_type == 2 && (!empty($row->category->params->price) || !empty($row->category->custom->meta['price']))) {
                        return number_format($row->category->custom->meta['price']??$row->category->params->price);
                    }
                    return $row->price;
                })->editColumn('price_old', function($row) {
                    if ($row->category->display_type == 2 && (!empty($row->category->params->price_old) || !empty($row->category->custom->meta['price_old']))) {
                        return number_format($row->category->custom->meta['price_old']??$row->category->params->price_old);
                    }
                    return $row->price_old;
                })->addColumn('action', function($row) {
                    $temp = '';
                    if ($row->target == 1) {
                        $temp.= "<a href=\"".route("admin.acc.auto_detail",$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Xem thông tin\"><i class=\"la la-eye\"></i></a>";
                    }
                    if ($row->status == 1 && $row->category->display_type != 2) {
                        $temp.= "<a href=\"javascript:void(0)\"  data-id=\"$row->id\" class=\"quick-save btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Lưu\"><i class=\"la la-save\"></i></a>";
                    }
                    $temp.= "<a href=\"".route("admin.acc.edit",[$row->category->display_type??1, $row->id])."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    $temp.= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    return $temp;
                })->addColumn('author', function($row) {
                    return $row->author->username;
                })->addColumn('price_shop', function($row) {
                    if ($row->category->display_type == 2 && (!empty($row->category->params->price) || !empty($row->category->custom->meta['price']))) {
                        return number_format(\App\Library\HelpMoneyPercent::shop_price($row->category->custom->meta['price']??$row->category->params->price));
                    }else{
                        return number_format(\App\Library\HelpMoneyPercent::shop_price($row->price));
                    }
                })->addColumn('price_old_shop', function($row) {
                    if ($row->category->display_type == 2 && (!empty($row->category->params->price_old) || !empty($row->category->custom->meta['price_old']))) {
                        return number_format(\App\Library\HelpMoneyPercent::shop_price($row->category->custom->meta['price_old']??$row->category->params->price_old));
                    }else{
                        return number_format(\App\Library\HelpMoneyPercent::shop_price($row->price_old));
                    }
                })->addColumn('randId', function($row) {
                    return \App\Library\Helpers::encodeItemID($row->id);
                })->toJson();
        }
        ActivityLog::add($request, 'Truy cập danh sách '.config('etc.acc_property.type')[$type]);
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => "Danh sách ".config('etc.acc_property.type')[$type]
        ];
        $access_group = auth()->user()->access_categories()->where('game_access.active', 1)->pluck('groups.id')->toArray();
        $properties = Group::where('module', 'acc_provider')->orderBy('order')->with(['childs' => function($query) use($access_group, $type){
            if (auth()->user()->account_type != 1) {
                $query->whereIn('id', $access_group)->where('display_type', $type);
            }
        }])->whereHas('childs', function($query) use($access_group, $type){
            if (auth()->user()->account_type != 1) {
                $query->whereIn('id', $access_group)->where('display_type', $type);
            }
        })->get();
        $status[''] = '-- Tất cả --';
        return view('admin.acc.index', [
            'module' => $this->module, 'page_breadcrumbs' => $this->page_breadcrumbs, 'properties' => $properties, 'type' => $type, 'status' => $status
        ]);
    }

    function history(Request $request){
        $this->module = 'acc';
        $input = $request->input();
        if (!empty($input['export'])) {
            if (auth()->user()->hasRole('admin') || auth()->user()->hasPermissionTo('acc-history-export')) {
                return \Excel::download(new \App\Exports\NickExport($input), "nick-".time().".xlsx");
            }else{
                return redirect()->back()->with('error', 'Không có quyền trích xuất file lịch sử bán acc');
            }
        }
        $model = new Nick(['table' => 'nicks_completed']);
        $items = $model->where('module', $this->module)->with(['category' => function($query){
            $query->with('parent');
        }, 'author' => function($query){
            $query->select('id', 'username');
        }, 'customer', 'access_shops', 'acc_txns', 'shop', 'txns_order']);
        if (auth()->user()->hasRole('admin')) {
        }elseif(auth()->user()->account_type == 1 && auth()->user()->hasPermissionTo('acc-history')){
            $user = User::with('access_shops', 'access_shop_groups')->find(auth()->user()->id);
            if ($user->shop_access != 'all') {
                $items->where(function($query) use($user){
                    $query->whereIn('shop_id', $user->access_shops->pluck('id')->toArray());
                    if ($user->access_shop_groups->count() > 0) {
                        $query->orWhereHas('shop', function($query) use($user){
                            $query->whereIn('group_id', $user->access_shop_groups->pluck('id')->toArray());
                        });
                    }
                });
            }
        }else{
            $items->where('author_id', auth()->user()->id);
        }
        if (!empty($input['group_id'])) {
            $items->where(function($query) use($input){
                $query->whereHas('groups', function ($query) use ($input) {
                    $query->where('group_id', $input['group_id']);
                })->orWhere('parent_id', $input['group_id']);
            });
        }
        if (($input['status']??null) > -1) {
            $items->where('status', $input['status']);
        }
        if (!empty($input['author'])) {
            $items->whereHas('author', function ($query) use ($input) {
                $query->where('username', $input['author']);
            });
        }
        if (!empty($input['customer'])) {
            $items->whereHas('customer', function ($query) use ($input) {
                $query->where('username', $input['customer']);
            });
        }
        if (!empty($input['title'])) {
            $items->where('title', $input['title']);
        }
        if (!empty($input['id'])) {
            $items->where('id', \App\Library\Helpers::decodeItemID($input['id']));
        }
        if (!empty($input['shop_id'])) {
            $items->where('shop_id', $input['shop_id']);
        }elseif (session()->has('shop_id')) {
            $items->where('shop_id', session('shop_id'));
        }
        if ($request->filled('started_at')) {
            $items->where('published_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
        }
        if ($request->filled('ended_at')) {
            $items->where('published_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
        }

        $items = $items->orderBy('published_at', 'desc')->paginate(100)->appends($input);
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => "Lịch sử bán Acc"
        ];
        $access_group = auth()->user()->access_categories()->where('game_access.active', 1)->pluck('groups.id')->toArray();
        $properties = Group::where('module', 'acc_provider')->orderBy('order')->with(['childs' => function($query) use($access_group){
            if (auth()->user()->account_type != 1) {
                $query->whereIn('id', $access_group);
            }
        }])->whereHas('childs', function($query) use($access_group){
            if (auth()->user()->account_type != 1) {
                $query->whereIn('id', $access_group);
            }
        })->get();
        return view('admin.acc.history', [
            'page_breadcrumbs' => $this->page_breadcrumbs, 'properties' => $properties, 'items' => $items, 'input' => $input
        ]);
    }

    function analytic(Request $request){
        $this->module = 'acc';
        $input = $request->input();

        if (!empty($input['live'])) {
            return response()->json(\App\Library\NickHelper::live());
        }
        if (!empty($input['timeline'])) {
            return response()->json(\App\Library\NickHelper::timeline());
        }
        if (!empty($input['timeline_hourly'])) {
            return response()->json(\App\Library\NickHelper::timeline_hourly(['date' => $input['timeline_hourly']]));
        }
        if (!empty($input['check_dup_txns'])) {
            if (!empty($input['order_id'])) {
                return response()->json(DB::table('txns')->where('order_id',$input['order_id'])->get());
            }
            $start = empty($input['started_at'])? Carbon::now()->format('Y-m-d 00:00:00'): Carbon::createFromFormat('d/m/Y H:i:s', $input['started_at'])->format('Y-m-d H:i:s');
            $end = empty($input['ended_at'])? Carbon::now()->format('Y-m-d H:i:s'): Carbon::createFromFormat('d/m/Y H:i:s', $input['ended_at'])->format('Y-m-d H:i:s');
            $result = DB::table('txns')
            ->select('id','user_id','trade_type','order_id','txnsable_id','is_add','is_refund','status','created_at', DB::raw('COUNT(*) as `count`'))
            ->where(['trade_type' => 'buy_acc', 'is_add' => 1, 'is_refund' => 0, 'status' => 1])->where('created_at', '>=', $start)->where('created_at', '<=', $end)
            ->groupBy('order_id')
            ->havingRaw('COUNT(*) > 1')
            ->paginate(10)->appends($input);
            return response()->json($result);
        }
        if (empty($input['started_at']) && empty($input['ended_at'])) {
            $input['started_at'] = Carbon::now()->startOfMonth()->format('d/m/Y H:i:s');
            $input['ended_at'] = Carbon::now()->format('d/m/Y H:i:s');
            return redirect()->to(url()->current()."?".http_build_query($input));
        }
        $items = (new Nick(['table' => 'nicks_completed']))->whereIn('status', [0,12,13])->whereNotNull('sticky');
        if (auth()->user()->hasRole('admin')) {
        }elseif(auth()->user()->account_type == 1 && auth()->user()->hasPermissionTo('acc-history')){
            $user = User::with('access_shops', 'access_shop_groups')->find(auth()->user()->id);
            if ($user->shop_access != 'all') {
                $items->where(function($query) use($user){
                    $query->whereIn('shop_id', $user->access_shops->pluck('id')->toArray());
                    if ($user->access_shop_groups->count() > 0) {
                        $query->orWhereHas('shop', function($query) use($user){
                            $query->whereIn('group_id', $user->access_shop_groups->pluck('id')->toArray());
                        });
                    }
                });
            }
        }else{
            $items->where('author_id', auth()->user()->id);
        }
        if (!empty($input['group_id'])) {
            $items->where('parent_id', $input['group_id']);
        }
        if (!empty($input['author'])) {
            $items->whereHas('author', function ($query) use ($input) {
                $query->where('username', $input['author']);
            });
        }
        if (!empty($input['customer'])) {
            $items->whereHas('customer', function ($query) use ($input) {
                $query->where('username', $input['customer']);
            });
        }
        if (!empty($input['shop_id'])) {
            $items->where('shop_id', $input['shop_id']);
        }elseif (session()->has('shop_id')) {
            $items->where('shop_id', session('shop_id'));
        }
        if (!empty($input['started_at'])) {
            $items->where('published_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $input['started_at'])->format('Y-m-d H:i:s'));
        }else{
            $items->where('published_at', '>=', Carbon::now()->startOfMonth()->format('Y-m-d 00:00:00'));
        }
        if (!empty($input['ended_at'])) {
            $items->where('published_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $input['ended_at'])->format('Y-m-d H:i:s'));
        }else{
            $items->where('published_at', '<=', Carbon::now()->format('Y-m-d H:i:s'));
        }

        $result = [
            'count' => (clone $items)->count(),
            'price' => (clone $items)->sum('price'),
            'amount_total' => (clone $items)->sum('amount'),
            'amount_ctv' => (clone $items)->sum('amount_ctv')
        ];
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => "Thống kê bán Acc"
        ];

        $access_group = auth()->user()->access_categories()->where('game_access.active', 1)->pluck('groups.id')->toArray();
        $properties = Group::where('module', 'acc_provider')->orderBy('order')->with(['childs' => function($query) use($access_group){
            if (auth()->user()->account_type != 1) {
                $query->whereIn('id', $access_group);
            }
        }])->whereHas('childs', function($query) use($access_group){
            if (auth()->user()->account_type != 1) {
                $query->whereIn('id', $access_group);
            }
        })->get();
        return view('admin.acc.analytic', [
            'page_breadcrumbs' => $this->page_breadcrumbs, 'properties' => $properties, 'result' => $result, 'input' => $input
        ]);
    }

    function quick(Request $request){
        $input = $request->input();
        if (!empty($input['refresh_old'])) {
            $query = Nick::whereIn('id', explode(',',$request->id))->where('status', 11);
            if (!auth()->user()->hasRole('admin')) {
                $query->where('author_id', auth()->user()->id);
            }
            $update = $query->update(['status' => 1, 'started_at' => date('Y-m-d H:i:s')]);
            ActivityLog::add($request, "Cập nhật lại nick cũ thông tin");
            return redirect()->back()->with('success', "Đã cập nhật ".$update);
        }
        $item = Nick::whereIn('status', [1,4,5])->where('id', $input['id'])->with('category')->first();
        if (!empty($item) && !auth()->user()->hasRole('admin') && $item->author_id != auth()->user()->id) {
            return redirect()->route("admin.acc_type_".$item->category()->display_type??1)->with('error',__('Không có quyền sửa !'));
        }
        if (!empty($item)) {
            if (isset($input['price'])) {
                if ($item->category->display_type == 2) {
                    return response()->json(['success' => false]);
                }
                $price = preg_replace("/[^0-9]/", "", $input['price']);
                $price_old = preg_replace("/[^0-9]/", "", $input['price_old']);
                if ($price_old < $price) {
                    return response()->json(['success' => false]);
                }
                $item->fill(['price' => $price, 'price_old' => $price_old, 'started_at' => date('Y-m-d H:i:s')])->save();
                ActivityLog::add($request, "Cập nhật giá acc #{$item->id} {$input['price']}");
                return response()->json(['success' => !empty($item)]);
            }
            if (isset($input['status']) && in_array($input['status'], [1,4,5,7])) {
                if ($input['status'] == 1) {
                    $item->fill(['sticky' => null, 'published_at' => null, 'shop_id' => null, 'started_at' => date('Y-m-d H:i:s')]);
                }
                $item->fill(['status' => $input['status']])->save();
                ActivityLog::add($request, "Cập nhật status acc #{$item->id} {$input['status']} lý do {$input['desc']}");
            }
        }
        return redirect()->back();
    }

    function auto_detail(Request $request, $id){
        $this->module = 'acc';
        $data = Nick::where('module', '=', $this->module)->with(['groups', 'images', 'category', 'game_auto_props' => function($query){
            $query->withPivot('level', 'point', 'grade');
        }])->find($id);
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => "Xem thông tin nick {$data->title}"
        ];
        return view('admin.acc.auto-detail', ['module' => $this->module, 'page_breadcrumbs' => $this->page_breadcrumbs, 'data' => $data]);
    }

    function edit(Request $request, $type, $id){
        $this->module = 'acc';
        if (!empty($_GET['check_login'])) {
            $data = (new Nick(['table' => 'nicks_completed']))->where('module', '=', $this->module)->with('groups', 'category')->find($id);
        }else{
            $data = Nick::where('module', '=', $this->module)->with('groups', 'category')->find($id);
        }
        if (!empty($data) && !auth()->user()->hasRole('admin') && $data->author_id != auth()->user()->id) {
            return redirect()->route("admin.acc_type_{$type}")->with('error',__('Không có quyền sửa !'));
        }
        if (!empty($data) && !empty($_GET['check_login']) && auth()->user()->hasRole('admin') && in_array($data->status, [2, 3, 6])) {
            $order = Order::where(['module' => 'buy_acc', 'ref_id' => $data->id])->orderBy('id', 'desc')->first();
            if (empty($order)) {
                $shop = Shop::where('id',$data->shop_id)->with(['group' => function($query){
                    $query->select('id','title','status', 'params');
                }])->first();
                if ($shop) {
                    $ratio = null;
                    if(!empty($shop->group->params->nick->ratio_percent)){
                       $ratio = $shop->group->params->nick;
                    }elseif (!empty($shop->group->params->all->ratio_percent)) {
                        $ratio = $shop->group->params->all;
                    }
                    config(['etc.shop_ratio' => $ratio]);
                    $amount = \App\Library\HelpMoneyPercent::shop_price($data->price);
                }else{
                    $amount = $data->price;
                }
                User::lockForUpdate()->where('id', $data->sticky)->update([
                    'balance' => \DB::raw("balance + ".$amount),
                    'balance_in' => \DB::raw("balance_in + ".$amount)
                ]);
                $data->fill(['status' => 1, 'shop_id' => null, 'sticky' => null])->save();
                $nick_data = $data->toArray();
                unset($nick_data['groups'], $nick_data['category']);
                $nick_data['params'] = json_encode($nick_data['params']??[]);
                $nick_data['meta'] = json_encode($nick_data['meta']??[]);
                \DB::table('nicks')->insert([$nick_data]);
                $nick = (new Nick(['table' => 'nicks']))->where('id', $data->id)->first();
                if ($nick) {
                    \DB::table('nicks_completed')->where('id', $data->id)->delete();
                    $data = $nick;
                }
                ActivityLog::add($request, "Đã hoàn acc #{$data->id}");
                return redirect()->back()->with('success',__("Acc #{$data->id} đã hoàn vì chưa tạo được lệnh trừ tiền của người mua"));
            }
            if ($data->status == 2) {
                $category = Group::find($data->parent_id);
                if (!in_array($category->is_display, array_keys(config('etc.acc_property.check_login')))) {
                    $this->lock_trans("user_trans_check_acc_{$data->id}", function () use($data, $order) {
                        if ($data->status == 2) {
                            \DB::transaction(function () use($data, $order) {
                                $data->fill(['status' => 0])->save();
                                $api = new \App\Http\Controllers\Api\V1\AccController;
                                $author = $api->author_trans($data, $order);
                            });
                        }
                    });
                    return redirect()->back()->with('success',__("Đã xử lý"));
                }
            }
            $job = new \App\Jobs\AccCheckLogin($order);
            dispatch($job);
            return redirect()->back()->with('success',__("Đang check lại acc #{$data->id}"));
        }
        if ($request->method() == 'DELETE' || $request->filled('delete_keyword')) {
            $params = $request->input();
            $result = null;
            if ($request->filled('delete_keyword')) {
                if (!empty($params['keyword'])) {
                    $deleted = Nick::where('seo_title',$params['keyword'])->whereIn('status', [1,4,5,7,8,9,10,11]);
                    if (!auth()->user()->hasRole('admin')) {
                        $deleted->where('author_id', auth()->user()->id);
                    }
                    $result = $deleted->delete();
                }
            }else{
                $input = explode(',',$request->id);
                if (($params['force']??null) == 1) {
                    $deleted = Nick::whereIn('id',$input)->whereIn('status', [1,4,5,7,8,9,10,11]);
                    if (!auth()->user()->hasRole('admin')) {
                        $deleted->where('author_id', auth()->user()->id);
                    }
                    $result = $deleted->delete();
                    ActivityLog::add($request, 'Xóa vĩnh viễn acc #'.$request->id);
                }else{
                    $deleted = Nick::whereIn('id',$input)->whereIn('status', [1,4,5,7,8,9,10,11]);
                    if (!auth()->user()->hasRole('admin')) {
                        $deleted->where('author_id', auth()->user()->id);
                    }
                    $result = $deleted->update(['status' => 5]);
                    ActivityLog::add($request, 'Xóa tạm thời acc #'.$request->id);
                }
            }
            return redirect()->back()->with('success', $result? 'Xoá thành công': 'Trạng thái Acc không thể xoá');
        }
        $access_group = auth()->user()->access_categories()->where('game_access.active', 1)->pluck('groups.id')->toArray();
        if ($request->method() == 'POST') {
            $input = $request->all();
            if (($input['submit']??null) == 'refund') {
                $acc = (new Nick(['table' => 'nicks_completed']))->where('module', '=', $this->module)->with('shop')->find($input['id']);
                if ($acc->status !== 0) {
                    return redirect()->back()->with('error', "Nick không thể hoàn tiền");
                }elseif (!auth()->user()->hasRole('admin') && !auth()->user()->hasPermissionTo('acc-refund')) {
                    return redirect()->back()->with('error', "Bạn không có quyền hoàn tiền");
                }
                $author_txns = Txns::where([
                    'trade_type' => 'buy_acc', 'user_id' => $acc->author_id, 'is_add' => 1, 'is_refund' => 0, 'status' => 1, 'txnsable_type' => 'App\Models\Item', 'txnsable_id' => $acc->id
                ])->orderBy('id', 'desc')->first();
                $txns = Txns::where([
                    'trade_type' => 'buy_acc', 'user_id' => $acc->sticky, 'is_add' => 0, 'is_refund' => 0, 'status' => 1, 'txnsable_type' => 'App\Models\Item', 'txnsable_id' => $acc->id
                ])->orderBy('id', 'desc')->first();
                $success = \DB::transaction(function () use($input, $acc, $author_txns, $txns) {
                    $author = User::lockForUpdate()->find($acc->author_id);
                    if ($author->balance >= $author_txns->amount) {
                        $author->fill(['balance' => $author->balance - $author_txns->amount, 'balance_out' => $author->balance_out + $author_txns->amount])->save();
                        $cancel = Txns::create([
                            'shop_id' => $author_txns->shop_id, 'trade_type' => $author_txns->trade_type, 'user_id' => $acc->author_id, 'order_id' => $author_txns->order_id, 'amount' => $author_txns->amount,
                            'last_balance' => $author->balance, 'is_add' => 0, 'is_refund' => 1, 'status' => 1, 'txnsable_type' => 'App\Models\Item',
                            'txnsable_id' => $author_txns->txnsable_id, 'description' => "Trừ để hoàn tiền thủ công acc #{$acc->id} đã bán. Lí do: {$input['desc']}"
                        ]);
                        $user = User::lockForUpdate()->find($txns->user_id);
                        $user->fill(['balance' => $user->balance + $txns->amount, 'balance_in_refund' => $user->balance_in_refund + $txns->amount])->save();
                        $refund = Txns::create([
                            'shop_id' => $txns->shop_id, 'trade_type' => $txns->trade_type, 'user_id' => $user->id, 'order_id' => $txns->order_id, 'amount' => $txns->amount,
                            'last_balance' => $user->balance, 'is_add' => 1, 'is_refund' => 1, 'status' => 1, 'txnsable_type' => 'App\Models\Item',
                            'txnsable_id' => $txns->txnsable_id, 'description' => "Hoàn tiền thủ công acc #{$acc->id} đã mua. Lí do: {$input['desc']}"
                        ]);
                        $acc->fill(['status' => $input['status']])->save();
                        return true;
                    }
                    return false;
                });
                $message = 'Hoàn tiền '.($success? 'Thành công': 'Thất bại do số dư ctv không đủ để trừ').' acc #'.$input['id'];
                ActivityLog::add($request, $message);
                if ($success) {
                    $user = User::find($txns->user_id);
                    $author = User::find($acc->author_id);
                    $tel_m = "Thời gian: <b>".date('d-m-Y H:i:s')."</b>.\nNgười thực hiện: <b>".auth()->user()->username."</b>";
                    $tel_m .= "\nNội dung: hoàn số tiền <b>".number_format($txns->amount)."đ</b> nick id <b>{$input['id']}</b>";
                    $tel_m .= "\nNgười được hoàn tiền: <b>{$user->username}</b>";
                    $tel_m .= "\nShop bị hoàn: <b>{$acc->shop->domain}</b>";
                    $tel_m .= "\nCTV bị hoàn: <b>{$author->username}</b>";
                    $tel_m .= "\nLý do hoàn: <b>{$input['desc']}</b>";
                    $this->curl(env('NICK_TELE_API', 'http://nick.tichhop.pro/api/tele-message').'?'.http_build_query([
                        'channel' => env('NICK_TELE_GROUP', '-854268881'), 'message' => $tel_m
                    ]));
                }
                return redirect()->back()->with('success', $message);
            }elseif (($input['submit']??null) == 'refund_nick_only') {
                $acc = (new Nick(['table' => 'nicks_completed']))->where('module', '=', $this->module)->with('shop')->find($input['id']);
                if ($acc->status !== 0) {
                    return redirect()->back()->with('error', "Nick không thể hoàn");
                }elseif (!auth()->user()->hasRole('admin') && !auth()->user()->hasPermissionTo('acc-refund')) {
                    return redirect()->back()->with('error', "Bạn không có quyền hoàn tiền");
                }
                $acc->fill(['status' => $input['status']])->save();
                $message = 'Đổi trạng thái acc thủ công #'.$input['id'];
                ActivityLog::add($request, $message);
                return redirect()->back()->with('success', $message);
            }
            if (!auth()->user()->hasRole('admin') && !in_array($input['parent_id'], $access_group)) {
                return redirect()->route("admin.acc_type_{$type}")->with('error',__('Không có quyền danh mục này !'));
            }
            $category = Group::find($input['parent_id']);
            if (!empty($input['upfile'])) {
                if ($request->hasFile('excel')) {
                    $fileName = "acc-".uniqid()."-".$request->file('excel')->getClientOriginalName();
                    $request->file('excel')->move('temp', $fileName);
                    $array = \Excel::toArray(new \App\Imports\ExcelArray(),  public_path("temp/{$fileName}"))[0];
                    @unlink("temp/{$fileName}");
                    $keys = $array[0];
                    $head = [];
                    $ids = [];
                    foreach ($array[1] as $key => $value) {
                        if (empty($keys[$key])) {
                            break;
                        }
                        $head[$key] = ['key' => $keys[$key]];
                        if (in_array($keys[$key], ['prop_fill', 'prop'])) {
                            $ids[] = $head[$key]['id'] = trim(explode('-', $value)[0]);
                        }
                    }
                    $resp = ['ok' => 0, 'unique' => []];
                    $groups = Group::whereIn('id', $ids)->select('id','title')->get()->keyBy('id');
                    $rows = [];
                    foreach ($array as $i => $row) {
                        if ($i > 1 && !empty($row[0])) {
                            $input = ['author_id' => auth()->user()->id, 'parent_id' => $category->id, 'module' => 'acc', 'order' => rand(1000, 10000), 'status' => 9, 'params' => ['ext_info' => []]];
                            $group_ids = [$category->parent_id, $category->id];
                            foreach ($row as $key => $value) {
                                if (empty($head[$key]['key'])) {
                                    break;
                                }
                                switch ($head[$key]['key']) {
                                    case 'account':
                                        $input['title'] = $value;
                                    break;
                                    case 'password':
                                        $input['slug'] = Helpers::Encrypt($value, config('etc.encrypt_key'));
                                    break;
                                    case 'code':
                                        $input['idkey'] = $value;
                                    break;
                                    case 'description':
                                        $input['description'] = $value;
                                    break;
                                    case 'price_old':
                                        $input['price_old'] = $value;
                                    break;
                                    case 'price':
                                        $input['price'] = $value;
                                    break;
                                    case 'keyword':
                                        $input['seo_title'] = $value;
                                    break;
                                    case 'prop':
                                        $group_ids[] = trim(explode('-', $value)[0]);
                                    break;
                                    case 'prop_fill':
                                        $input['params']['ext_info'][$head[$key]['id']] = $value;
                                    break;
                                    default:
                                        break;
                                }
                            }
                            if ($input['price_old'] >= $input['price']) {
                                $input['meta'] = json_encode(['groups' => $group_ids]);
                                $input['params'] = json_encode($input['params']);
                                $rows[] = $input;
                                $resp['ok']++;
                            }else{
                                $resp['unique'][] = $input['title'];
                            }
                        }
                    }
                    if (!empty($rows)) {
                        if (empty(DB::table('nicks_queue')->where('title', $rows[count($rows)-1]['title'])->first())) {
                            DB::table('nicks_queue')->insert($rows);
                        }else{
                            return response()->json([
                                'alert' => "Bạn đã up file này trước đó. Vui lòng đợi hệ thống xử lý",
                                'redirect' => route("admin.acc_type_{$type}")
                            ]);
                        }
                    }
                    ActivityLog::add($request, "Import excel acc");
                    return response()->json([
                        'alert' => 'Đã thêm '.$resp['ok'].' nick. Loại trừ '.count($resp['unique']).' nick giá không hợp lệ: '.implode(', ', $resp['unique']),
                        'redirect' => route("admin.acc_type_{$type}")
                    ]);
                }
            }elseif (!empty($input['excel'])) {
                $cat = Group::whereIn('id', $input['groups'])->where(['module' => 'acc_category'])->first();
                $cat_ids = Group::where('parent_id', $cat->parent_id)->pluck('id')->toArray();
                $price = $cat->params->price??null;
                $price_old = $cat->params->price_old??null;
                if ($type == 2 && empty($price) && empty($price_old)) {
                    return redirect()->back()->with('error',"Danh mục Chưa cấu hình giá, vui lòng báo quản trị viên !");
                }
                $fileName = "acc-".uniqid()."-".$request->file('excel')->getClientOriginalName();
                $request->file('excel')->move('temp', $fileName);
                $array = \Excel::toArray(new \App\Imports\ExcelArray(),  public_path("temp/{$fileName}"));
                @unlink("temp/{$fileName}");
                $resp = ['ok' => 0, 'unique' => []];
                $rows = [];
                foreach (($array[0]??[]) as $i => $row) {
                    if ($i > 0 && !empty($row[0])) {
                        if ($type == 2) { /*Random*/
                            $rows[] = [
                               'title' => $row[0], 'slug' => Helpers::Encrypt($row[1], config('etc.encrypt_key')), 'idkey' => $row[2],
                               'price_old' => $cat->params->price_old??$cat->params->price, 'price' => $cat->params->price,
                               'seo_title' => $row[4], 'params' => json_encode([
                                    'ext_info' => [
                                        ['name' => 'email', 'value' => $row[3]]
                                    ]
                                ]), 'parent_id' => $input['parent_id'], 'module' => 'acc', 'status' => 1, 'percent_sale' => (($cat->params->price_old??$cat->params->price) - $cat->params->price)*100/($cat->params->price_old??$cat->params->price),
                                'author_id' => auth()->user()->id, 'order' => rand(1000, 10000), 'meta' => json_encode(['groups' => $input['groups']??[]])
                            ];
                            $resp['ok']++;
                        }else{ /*Auto*/
                            $params = [];
                            $row[3] = preg_replace("/[^0-9]/", "", $row[3]);
                            $row[4] = preg_replace("/[^0-9]/", "", $row[4]);
                            if (!empty($row[6])) {
                                $params['server'] = preg_replace("/[^0-9]/", "", $row[6]);
                            }
                            $row[4] = preg_replace("/[^0-9]/", "", $row[4]);
                            if ($row[4] > 0 && $row[3] >= $row[4]) {
                                $rows[] = [
                                    'title' => $row[0], 'slug' => Helpers::Encrypt($row[1], config('etc.encrypt_key')), 'idkey' => $row[2],
                                    'price_old' => $row[3], 'price' => $row[4], 'seo_title' => $row[5], 'target' => 1,
                                    'params' => json_encode($params), 'parent_id' => $input['parent_id'], 'module' => 'acc', 'status' => 7, 'percent_sale' => ($row[3] - $row[4])*100/$row[3],
                                    'author_id' => auth()->user()->id, 'order' => rand(1000, 10000)
                                ];
                                $resp['ok']++;
                            }else{
                                $resp['unique'][] = $row[0];
                            }
                        }
                    }
                }
                if (!empty($rows)) {
                    if (empty(DB::table('nicks_queue')->where('title', $rows[count($rows)-1]['title'])->first())) {
                        DB::table('nicks_queue')->insert($rows);
                    }else{
                        return response()->json([
                            'alert' => "Bạn đã up file này trước đó. Vui lòng đợi hệ thống xử lý",
                            'redirect' => route("admin.acc_type_{$type}")
                        ]);
                    }
                }
                // $array = \Excel::import(new \App\Imports\AccRandom($input), public_path("temp/{$fileName}"));
                ActivityLog::add($request, "Import excel acc");
                return response()->json([
                    'alert' => 'Đã thêm '.$resp['ok'].' nick. Loại trừ '.count($resp['unique']).' nick giá không hợp lệ: '.implode(', ', $resp['unique']),
                    'redirect' => route("admin.acc_type_{$type}")
                ]);
                // return redirect()->route("admin.acc_type_{$type}")->with('success',__('Đã xử lý'));
            }elseif ($request->hasFile('excel_password')) {
                $cat = Group::whereIn('id', $input['groups'])->where(['module' => 'acc_category'])->first();
                $fileName = "acc-random-".uniqid()."-".$request->file('excel_password')->getClientOriginalName();
                $request->file('excel_password')->move('temp', $fileName);
                $array = \Excel::toArray(new \App\Imports\ExcelArray(),  public_path("temp/{$fileName}"));
                @unlink("temp/{$fileName}");
                $resp = ['ok' => 0, 'unique' => []];
                foreach (($array[0]??[]) as $i => $row) {
                    if ($i > 0 && !empty($row[0])) {
                        $item = Nick::where(['module' => 'acc', 'title' => $row[0], 'parent_id' => $input['parent_id'], 'author_id' => auth()->user()->id])
                        ->whereNotIn('status', [0,4,5])->first();
                        if (empty($item)) {
                            $item->fill(['slug' => Helpers::Encrypt($row[1], config('etc.encrypt_key')), 'status' => 1])->save();
                            $resp['ok']++;
                        }
                    }
                }
                // $array = \Excel::import(new \App\Imports\AccRandom($input), public_path("temp/{$fileName}"));
                ActivityLog::add($request, "Change pass by excel acc random");
                return response()->json([
                    'alert' => 'Đã cập nhật mk cho '.$resp['ok'].' nick.',
                    'redirect' => route("admin.acc_type_{$type}")
                ]);
                // return redirect()->route("admin.acc_type_{$type}")->with('success',__('Đã xử lý'));
            }
            $rules = ['title'=>'required', 'slug'=>'required'];
            if ($type != 2) {
                $rules['price'] = 'required';
                $rules['price_old'] = 'required';
                if (empty($data->id) && ($input['target']??0) != 1 && $category->position != 'lienquan') {
                    $rules['image_file'] = 'required';
                    $rules['image_extension_file'] = 'required';
                }
            }
            $validator = Validator::make($request->all(), $rules, [
                'title.required' => __('Vui lòng nhập tên đăng nhập'),
                'slug.required' => __('Vui lòng nhập mk đăng nhập'),
                'price.required' => __('Vui lòng nhập giá bán'),
                'price_old.required' => __('Vui lòng nhập giá hiển thị'),
                'image_file.required' => __('Vui lòng chọn ảnh đại diện'),
                'image_file.mimes' => __('Vui lòng chọn ảnh đại diện đúng định dạng: '.implode(',', MediaHelpers::allowedImage())),
                'image_extension_file.required' => __('Vui lòng chọn ảnh chi tiết'),
                'image_extension_file.mimes' => __('Vui lòng chọn ảnh chi tiết đúng định dạng: '.implode(',', MediaHelpers::allowedImage())),
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors(), 'message' => implode(', ', $validator->errors()->all())]);
            }
            /**/
            $input['module'] = $this->module;
            $input['slug'] = Helpers::Encrypt($input['slug'], config('etc.encrypt_key'));
            if ($type == 2) {
                $input['price'] = $category->params->price;
                $input['price_old'] = $category->params->price_old;
            }else{
                $input['price'] = preg_replace("/[^0-9]/", "", $input['price']);
                $input['price_old'] = preg_replace("/[^0-9]/", "", $input['price_old']);
                if ($input['price'] <= 0) {
                    return response()->json(['error' => 'price', 'message' => 'Giá gốc phải lớn hơn 0!']);
                }
                if ($input['price_old'] < $input['price']) {
                    return response()->json(['error' => 'price', 'message' => 'Giá ảo không được nhỏ hơn giá gốc !']);
                }
            }
            $input['percent_sale'] = ($input['price_old'] - $input['price'])*100/$input['price_old'];

            if (empty($data)) {
                /*Kiểm tra nick mới trùng với nick đang bán*/
                $cat = Group::where('id', $input['parent_id'])->with('parent')->first();
                $ids = Group::where('parent_id', $cat->parent_id)->pluck('id')->toArray();
                if (!empty(Nick::where(['module' => 'acc', 'title' => $input['title'], 'author_id' => auth()->user()->id])->whereIn('parent_id', $ids)->whereNotIn('status', [0,4,5])->first())) {
                    return response()->json(['error' => ['title' => ['Đã tồn tại acc!']], 'message' => 'Đã tồn tại acc !']);
                }
                if ($type == 2) {
                    $input['status'] = 1;
                }else{
                    $input['status'] = $input['target'] == 1? 7: 9;
                }
                $data = Nick::create(['title' => $input['title'], 'slug' => $input['slug'], 'author_id' => auth()->user()->id, 'order' => rand(1000, 10000)]);
            }else{
                if (isset($input['status'])) {
                    unset($input['status']);
                    // if ($data->status == 0) {
                    //     return redirect()->back()->with('error',__('Acc đã bán không thể sửa !'));
                    // }elseif (isset($input['status']) && !in_array($input['status'], [4,5])) {
                    //     unset($input['status']);
                    // }
                }
            }
            if ($request->hasFile('image_file')) {
                $image = MediaHelpers::upload_image($input['image_file'], $dir = "upload/product-acc/{$data->id}", "thumb", $width = 350, $height = 250);
                if (!empty($image)) {
                    $media = Media::updateOrCreate(['table' => 'items', 'table_id' => $data->id, 'type' => 'thumb'], [
                        'path' => $image, 'base_path' => config('module.media.url')
                    ]);
                    $input['image'] = $image;
                }
            }
            if (empty($input['image_extension'])) {
                $images = [];
            }else{
                $images = explode('|', trim($input['image_extension']));
                if (!empty($input['delete_image_extension'])) {
                    foreach (($input['delete_image_extension']??[]) as $key => $value) {
                        $deleted = MediaHelpers::delete_image($images[intval($value)]);
                        unset($images[intval($value)]);
                    }
                    $images = array_values($images);
                    $input['image_extension'] = implode('|', $images);
                    ActivityLog::add($request, 'Xóa ảnh chi tiết acc #'.$data->id);
                }
            }
            if (!empty($input['image_extension_file'])) {
                foreach ($input['image_extension_file'] as $key => $file) {
                    $image = MediaHelpers::upload_image($file, $dir = "upload/product-acc/{$data->id}", uniqid(), $width = 900, $height = 600);
                    if (!empty($image)) {
                        $media = Media::create(['table' => 'items', 'table_id' => $data->id, 'type' => 'detail','path' => $image, 'base_path' => config('module.media.url')]);
                        $images[] = $image;
                    }
                }
                $input['image_extension'] = implode('|', $images);
            }
            if ( (( !empty($input['image']) && !empty($input['image_extension']) ) || $type == 2) && ($input['status']??$data->status) == 9 ) {
                $input['status'] = 1;
            }
            $input['started_at'] = date('Y-m-d H:i:s');
            if ($data->status == 11) {
                $input['status'] = 1;
            }
            $data->fill($input)->save();
            $data->groups()->sync($input['groups']??[]);
            ActivityLog::add($request, 'Cập nhật thành công acc #'.$data->id);
            if (empty($input['image_extension']) && !empty($rules['image_extension_file'])) {
                return response()->json(['error' => ['image_extension_file' => ['Ảnh chi tiết không được để trống!']], 'message' => 'Ảnh chi tiết không được để trống!']);
            }
            session()->flash('success', 'Cập nhật thành công !');
            return response()->json([
                'message' => 'Cập nhật thành công !',
                'redirect' => ($request->filled('submit-close') || $type == 2)? route("admin.acc_type_{$type}"): route('admin.acc.edit', [$type, $data->id])
            ]);
        }
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => (empty($data)? 'Thêm ': 'Sửa ').config('etc.acc_property.type')[$type]
        ];
        ActivityLog::add($request, 'Vào form edit acc #'.$id);

        // if (($_GET['target']??null) == 1) {
        //     $access_group = array_intersect($access_group, array_map('intval', explode(',', env('UPNICK_LIEMINH_CAT_ID'))));
        // }
        $groups = Group::where('module', 'acc_provider')->orderBy('order')->with(['childs' => function($query) use($access_group, $type){
            $query->whereIn('id', $access_group);
            if(($_GET['target']??null) == 1){
                $query->whereIn('position', array_keys(config('etc.acc_property.auto')))->whereNotIn('position', config('etc.acc_property.semi_auto'));
            }
            $query->where('display_type', $type);
        }])->whereHas('childs', function($query) use($access_group, $type){
            $query->whereIn('id', $access_group);
            if(($_GET['target']??null) == 1){
                $query->whereIn('position', array_keys(config('etc.acc_property.auto')))->whereNotIn('position', config('etc.acc_property.semi_auto'));
            }
            $query->where('display_type', $type);
        })->get();

        /*Set folder ckfinder*/
        // config(['ckfinder.backends.default.root' => storage_path("app/public/upload/product-acc/{$id}")]);
        // config(['ckfinder.backends.default.baseUrl' => "/storage/upload/product-acc/{$id}/"]);
        // dd(config('ckfinder'));
        if (!empty($_GET['excel'])) {
            $category = Group::where('id', $_GET['category'])->whereIn('id', $access_group)->first();
            if (empty($category)) {
                return redirect()->back();
            }
            return view('admin.acc.up-file', [
                'module' => $this->module, 'page_breadcrumbs' => $this->page_breadcrumbs, 'data' => $data, 'category' => $category, 'type' => $type
            ]);
        }
        return view('admin.acc.edit', [
            'module' => $this->module, 'page_breadcrumbs' => $this->page_breadcrumbs, 'data' => $data, 'groups' => $groups, 'type' => $type
        ]);
    }

    public function property(Request $request)
    {
        $input = $request->input();
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => "Thuộc tính của kho Acc"
        ];
        ActivityLog::add($request, 'Truy cập danh sách thuộc tính Acc');

        $data = Group::where('module', 'acc_provider')->orderBy('order')->with(['childs' => function($query) use($input){
            if (!empty($input['trashed'])) {
                $query->withTrashed();
            }
        }]);
        if (!empty($input['trashed'])) {
            $data->withTrashed();
        }
        $data = $data->get();
        return view('admin.acc.property', [
            'module' => $this->module, 'page_breadcrumbs' => $this->page_breadcrumbs, 'data' => $data,
        ]);

    }

    public function property_edit(Request $request, $module, $parent_id, $id)
    {
        $this->module = $module;
        if ($request->method() == 'DELETE') {
            $input = explode(',',$request->id);
            Group::where(function($query) use($input){
                $query->whereIn('id',$input)->orWhere('parent_id', $input);
            })->delete();
            ActivityLog::add($request, 'Xóa thành công thuộc tính Acc #'.$request->id);
            return redirect()->back()->with('success',__('Xóa thành công !'));
        }
        if (!empty($_GET['recover'])) {
            if (Group::where(['module' => $module, 'id' => $id])->withTrashed()->restore()) {
                ActivityLog::add($request, 'Khôi phục thuộc tính Acc #'.$request->id);
                return redirect()->back()->with('success',__('Khôi phục thành công !'));
            }else{
                return redirect()->back();
            }
        }
        $data = Group::where(['module' => $module, 'id' => $id])->with('childs')->withTrashed()->first();
        if (!empty($_GET['clone'])) {
            $cat = $data->toArray();
            $cat['title'] .= ' 2';
            $cat['slug'] = \Str::slug($cat['title']);
            $clone = Group::create(['title' => $cat['title']]);
            $clone->fill($cat)->save();
            $this->clone_group_childs($clone->id, $cat['childs']);
            ActivityLog::add($request, 'Clone danh mục acc #'.$data->id);
            return redirect()->back()->with('success',"Clone thành công {$clone->title} !");
        }
        if ($request->method() == 'POST') {
            $input = $request->all();
            $rules = ['title'=>'required'];
            // if (!empty($data) && $data->slug == $input['slug']) {
            //     $exist = Group::where(['module' => 'acc_category', 'slug' => $input['slug']])->where('id', '<>', $data->id)->with('parent')->get();
            // }
            if ($module == 'acc_category' && empty($data)) {
                $rules['slug'] = "unique:groups,slug";
            }
            $this->validate($request, $rules,[
                'title.required' => __('Vui lòng nhập tiêu đề'),
                'slug.unique' => __('Permalink trùng với danh mục đã có, vui lòng đổi lại'),
            ]);
            $input['module'] = $this->module;
            $input['parent_id'] = $parent_id;
            if ($module == 'acc_category') {
                $input['is_slug_override'] = $input['is_slug_override']??null;
            }
            if ($module == 'acc_label') {
                $input['is_display'] = $input['is_display']??null;
            }
            if(!empty($input['params']['price'])) $input['params']['price'] = intval(preg_replace("/[^0-9]/", "", $input['params']['price']));
            if(!empty($input['params']['price_old']))$input['params']['price_old'] = intval(preg_replace("/[^0-9]/", "", $input['params']['price_old']));
            if (!empty($input['image'])) {
                $input['image'] = explode('?', $input['image'])[0];
            }
            if (!empty($input['excel_example']) && !empty($input['position'])) {
                $path = $request->file('excel_example')->move("assets/backend/files", "acc-auto-{$input['position']}.xlsx");
            }
            if (empty($data)) {
                $data = Group::create($input);
            }else{
                $data->update($input);
            }
            if($request->filled('submit-close')){
                return redirect()->route('admin.acc.property')->with('success',__('Cập nhật thành công !'));
            }else {
                return redirect()->route('admin.acc.property.edit', [$module, $parent_id, $data->id])->with('success',__('Cập nhật thành công !'));
            }
        }
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => (empty($data)? 'Thêm ': 'Sửa ').config("etc.acc_property.module.{$module}")
        ];

        ActivityLog::add($request, 'Vào form edit '.config("etc.acc_property.module.{$module}").' #'.$id);
        return view('admin.acc.property-edit', [
            'module' => $this->module, 'page_breadcrumbs' => $this->page_breadcrumbs, 'data' => $data, 'parent_id' => $parent_id
        ]);

    }

    function property_auto(Request $request, $table, $id){
        if ($table == 'groups') {
            $cat = Group::where(['id' => $id])->first();
            $childs = GameAutoProperty::where(['parent_id' => $id, 'parent_table' => 'groups'])->withCount('childs')->orderBy('order', 'asc')->paginate(100);
            if (!empty($_GET['sync']) || ($childs->count() == 0 && in_array($cat->position, array_keys(config('etc.acc_property.auto')) ))) {
                switch ($cat->position) {
                    case 'lienminh':
                        $json = file_get_contents('assets/backend/files/json_yourol.vn_VN.json');
                        $data = json_decode($json, true);
                        $arr = [];
                        foreach ($data['data'] as $key => $list) {
                            $parent = GameAutoProperty::updateOrCreate(
                                ['provider' => $cat->position, 'key' => $key, 'parent_id' => $id, 'parent_table' => 'groups'],
                                []
                            );
                            foreach ($list as $k => $value) {
                                $child = GameAutoProperty::updateOrCreate(
                                    ['provider' => $cat->position, 'keyid' => $value['id'], 'parent_id' => $parent->id],
                                    [
                                        'name' => $value['name']??$value['title'], 'key' => $key, 'order' => $k,
                                        'meta' => \Arr::only($value, ['alias', 'roles', 'set', 'yearReleased', 'rarity', 'isLegacy', 'species', 'level']),
                                        'thumb' => "/storage/media/game-lol/".($value['squarePortraitPath']??$value['imagePath'])
                                    ]
                                );
                                if (!empty($value['skins'])) {
                                    foreach ($value['skins'] as $j => $item) {
                                        $meta = \Arr::only($item, ['rarity', 'loadScreenPath', 'loadScreenVintagePath', 'skinLines']);
                                        if (!empty($meta['loadScreenPath'])) {
                                            $meta['loadScreenPath'] = "/storage/media/game-lol/{$meta['loadScreenPath']}";
                                        }
                                        if (!empty($meta['loadScreenVintagePath'])) {
                                            $meta['loadScreenVintagePath'] = "/storage/media/game-lol/{$meta['loadScreenVintagePath']}";
                                        }
                                        $skin = GameAutoProperty::updateOrCreate(
                                            ['provider' => $cat->position, 'keyid' => $item['id'], 'parent_id' => $child->id, 'key' => 'skins'],
                                            ['name' => $item['name'], 'order' => $j, 'meta' => $meta, 'thumb' => "/storage/media/game-lol/".($item['tilePath'])]
                                        );
                                        if (!empty($item['chromas'])) {
                                            foreach ($item['chromas'] as $i => $val) {
                                                $chroma = GameAutoProperty::updateOrCreate(
                                                    ['provider' => $cat->position, 'keyid' => $val['id'], 'parent_id' => $skin->id, 'key' => 'chromas'],
                                                    ['name' => $val['name'], 'order' => $i, 'thumb' => "/storage/media/game-lol/".($val['chromaPath'])]
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    break;
                    case 'ninjaschool':
                        $json = file_get_contents('assets/backend/files/ninjaschool.json');
                        $data = json_decode($json, true);
                        $arr = [];
                        foreach ($data as $key => $list) {
                            if (in_array($key, ['CAPTURES', 'INFO', 'SERVER'])) {
                                $parent = GameAutoProperty::updateOrCreate(
                                    ['provider' => $cat->position, 'key' => $key, 'parent_id' => $id, 'parent_table' => 'groups'],
                                    []
                                );
                                $order = 0;
                                foreach ($list as $k => $value) {
                                    $item = GameAutoProperty::updateOrCreate(
                                        ['provider' => $cat->position, 'key' => $k, 'parent_id' => $parent->id],
                                        ['name' => is_string($value)? $value: $value['name'], 'order' => $order]
                                    );
                                    if (is_array($value['value']??'')) {
                                        foreach ($value['value'] as $i => $val) {
                                            if (!empty($_GET['fix'])) {
                                                $child = GameAutoProperty::updateOrCreate(
                                                    ['provider' => $cat->position, 'name' => $val, 'parent_id' => $item->id],
                                                    ['key' => $i, 'order' => $i]
                                                );
                                            }else{
                                                $child = GameAutoProperty::updateOrCreate(
                                                    ['provider' => $cat->position, 'key' => $i, 'parent_id' => $item->id],
                                                    ['name' => $val, 'order' => $i]
                                                );
                                            }
                                        }
                                    }
                                    $order++;
                                }
                            }
                        }
                    break;
                    case 'nro':
                        $json = file_get_contents('assets/backend/files/nro.json');
                        $data = json_decode($json, true);
                        $arr = [];
                        foreach ($data as $key => $list) {
                            if (in_array($key, ['CAPTURES', 'INFO', 'SERVER'])) {
                                $parent = GameAutoProperty::updateOrCreate(
                                    ['provider' => $cat->position, 'key' => $key, 'parent_id' => $id, 'parent_table' => 'groups'],
                                    []
                                );
                                $order = 0;
                                foreach ($list as $k => $value) {
                                    $item = GameAutoProperty::updateOrCreate(
                                        ['provider' => $cat->position, 'key' => $k, 'parent_id' => $parent->id],
                                        ['name' => is_string($value)? $value: $value['name'], 'order' => $order]
                                    );
                                    if (is_array($value['value']??'')) {
                                        foreach ($value['value'] as $i => $val) {
                                            $child = GameAutoProperty::updateOrCreate(
                                                ['provider' => $cat->position, 'key' => $i, 'parent_id' => $item->id],
                                                ['name' => $val, 'order' => $i]
                                            );
                                        }
                                    }
                                    $order++;
                                }
                            }
                        }
                    break;

                    default:
                        // code...
                    break;
                }
            }
        }elseif ($table == 'game_auto_properties') {
            $cat = GameAutoProperty::where(['id' => $id])->first();
            $childs = $cat->childs()->withCount('childs')->orderBy('order', 'asc')->paginate(100);
        }
        if ($request->method() == 'POST') {
            return redirect()->back();
        }
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => "Thuộc tính auto ".($cat->title??$cat->name)
        ];
        return view('admin.acc.property-auto.index', ['cat' => $cat, 'childs' => $childs, 'page_breadcrumbs' => $this->page_breadcrumbs]);
    }

    function clone_group_childs($parent_id, $list){
        foreach ($list as $key => $child) {
            $child['parent_id'] = $parent_id;
            $group = Group::create(['title' => $child['title']]);
            $group->fill($child)->save();
            $this->clone_group_childs($group->id, $child['childs']);
        }
    }

    // AJAX Reordering function
    public function property_order(Request $request)
    {
        $source = e($request->get('source'));
        $destination = $request->get('destination');

        $item = Group::find($source);
        //dd($item);
        $item->parent_id = isset($destination)?$destination:0;
        $item->save();

        $ordering = json_decode($request->get('order'));

        $rootOrdering = json_decode($request->get('rootOrder'));

        if ($ordering) {
            foreach ($ordering as $order => $item_id) {
                if ($itemToOrder = Group::find($item_id)) {
                    $itemToOrder->order = $order;
                    $itemToOrder->save();
                }
            }
        } else {
            foreach ($rootOrdering as $order => $item_id) {
                if ($itemToOrder = Group::find($item_id)) {
                    $itemToOrder->order = $order;
                    $itemToOrder->save();
                }
            }
        }
        ActivityLog::add($request, 'Thay đổi STT thành công Thuộc tính acc #'.$item->id);
        return 'ok ';
    }

    function curl($url){
        $curl = curl_init();
        // if (!empty($data['params'])) {
        //     CURL_SETOPT($curl,CURLOPT_POST, True);
        //     CURL_SETOPT($curl,CURLOPT_POSTFIELDS, http_build_query($data['params']));
        // }
        CURL_SETOPT($curl,CURLOPT_URL, $url );
        CURL_SETOPT($curl,CURLOPT_RETURNTRANSFER, True);
        CURL_SETOPT($curl,CURLOPT_FOLLOWLOCATION, True);
        CURL_SETOPT($curl,CURLOPT_CONNECTTIMEOUT, 120);
        CURL_SETOPT($curl,CURLOPT_TIMEOUT, 120);
        CURL_SETOPT($curl,CURLOPT_FAILONERROR, true);
        CURL_SETOPT($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        CURL_SETOPT($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        $exec = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = '';
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            Log::error("curl '{$url}' error: {$error}");
        }
        curl_close($curl);
        return $exec;
    }

    function lock_trans($name, $callback){
        $lock = \Cache::lock($name);
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

    public function historyShow(Request $request,$id){
        $page_breadcrumbs[] = [
            'page' => route('admin.acc.history'),
            'title' => __('Chi tiết yêu cầu hoàn tiền')
        ];
        $data = (new Nick(['table' => 'nicks_completed']))
            ->where('id',$id)->first();

        $order_refund = null;

        if (isset($data->txns_order)){
            $order = $data->txns_order;
            if ($order->order_nick_refund){
                $order_refund = $order->order_nick_refund;
            }
        }

        ActivityLog::add($request, 'Chi tiết yêu cầu hoàn tiền #'.$id);
        return view('admin.acc.show', compact('data'))->with('data',$data)->with('order_refund',$order_refund)->with('page_breadcrumbs',$page_breadcrumbs);
    }

    public function rejectRefund(Request $request,$id){

        DB::beginTransaction();
        try {

            if (!Auth::user()->can('nick-delete-order-refund')){
                DB::rollback();
                return redirect()->back()->withErrors('Bạn không có quyền từ chối yêu cầu hoàn tiền');
            }

            if (!$request->note_refund) {
                DB::rollback();
                return redirect()->back()->withErrors('Vui lòng nhập nội dung từ chối');
            }
            $note_refund = $request->note_refund;
            $data = (new Nick(['table' => 'nicks_completed']))
                ->where('status',13)
                ->where('id',$id)->lockForUpdate()->first();

            if (!$data) {
                DB::rollback();
                return redirect()->back()->withErrors('Không tìm thấy nick');
            }

            $order_refund = null;

            if (isset($data->txns_order)){
                $order = $data->txns_order;
                if ($order->order_nick_refund){
                    $order_refund = $order->order_nick_refund;
                }
            }

            if (!isset($order_refund) && $order_refund->status != 2){
                DB::rollback();
                return redirect()->back()->withErrors('Không tìm thấy yêu cầu từ chối');
            }

            //Cập nhật trạng thái yêu cầu hoàn tiền.
            $order_refund->title = $note_refund;
            $order_refund->status = 3;
            $order_refund->save();

            //Cập nhật trạng thái đơn hàng về chờ đối soát

            $data->status = 12;
            $data->published_at = Carbon::now();//Thời gian xác nhận đơn hàng
            $data->save();

            ActivityLog::add($request,"Từ chối yêu cầu hoàn tiền nick #".$id );

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');
        }

        // Commit the queries!
        DB::commit();
        return redirect()->back()->with('success', "Từ chối yêu cầu hoàn tiền thành công  #" . $data->id);
    }
    public function completedRefund(Request $request,$id){
        DB::beginTransaction();
        try {

            if (!Auth::user()->can('nick-complete-order-refund')){
                DB::rollback();
                return redirect()->back()->withErrors('Bạn không có quyền từ chối yêu cầu hoàn tiền');
            }

            $data = (new Nick(['table' => 'nicks_completed']))
                ->where('status',13)
                ->where('id',$id)->lockForUpdate()->first();

            if (!$data) {
                DB::rollback();
                return redirect()->back()->withErrors('Không tìm thấy nick');
            }

            $order_refund = null;

            if (isset($data->txns_order)){
                $order = $data->txns_order;
                if ($order->order_nick_refund){
                    $order_refund = $order->order_nick_refund;
                }
            }

            if (!isset($order_refund) && $order_refund->status != 2){
                DB::rollback();
                return redirect()->back()->withErrors('Không tìm thấy yêu cầu từ chối');
            }

            //Cập nhật trạng thái yêu cầu hoàn tiền.
            $order_refund->status = 1;
            $order_refund->save();

            //Cập nhật trạng thái đơn hàng về đã hoàn tiền.

            $data->status = 14;
            $data->save();

            //hoàn tiền cho khách hàng

            $txns = Txns::where(['order_id' => $order->id, 'is_add' => 0, 'is_refund' => 0, 'status' => 1])->first();

            if (!isset($txns)) {
                DB::rollback();
                return redirect()->back()->withErrors('Không tìm thấy giao dịch trước đó');
            }

            $userTransaction = User::where('id', $txns->user_id)->lockForUpdate()->firstOrFail();

            if (!isset($userTransaction)) {
                DB::rollback();
                return redirect()->back()->withErrors('Không tìm thấy khách hàng');
            }

            if($userTransaction->checkBalanceValid() == false){
                DB::rollback();
                return redirect()->back()->withErrors('Tài khoản người dùng đang có nghi vấn, vui lòng liên hệ QTV để kịp thời xử lý');
            }

            $userTransaction->balance = $userTransaction->balance + $data->price;
            $userTransaction->balance_in = $userTransaction->balance_in + $data->price;
            $userTransaction->save();

            $refund = Txns::create([
                'shop_id' => $txns->shop_id, 'trade_type' => $txns->trade_type, 'user_id' => $userTransaction->id, 'order_id' => $txns->order_id, 'amount' => $txns->amount,
                'last_balance' => $userTransaction->balance, 'is_add' => 1, 'is_refund' => 1, 'status' => 1, 'txnsable_type' => 'App\Models\NickComplete', 'txnsable_id' => $txns->txnsable_id,
                'description' => "Hoàn tiền mua acc #{$data->id}",
            ]);

            ActivityLog::add($request,"Chấp nhận yêu cầu hoàn tiền nick #" .$id );

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return redirect()->back()->withErrors('Có lỗi phát sinh.Xin vui lòng thử lại !');
        }

        // Commit the queries!
        DB::commit();
        return redirect()->back()->with('success', "Chấp nhận yêu cầu hoàn tiền đơn hàng thành công  #" . $data->id);
    }
}
