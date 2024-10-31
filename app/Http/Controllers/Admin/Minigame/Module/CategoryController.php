<?php

namespace App\Http\Controllers\Admin\Minigame\Module;

use App\Http\Controllers\Controller;
use App\Library\HelperPermisionShopMinigame;
use App\Library\Helpers;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\LogEdit;
use App\Models\Server;
use App\Models\Shop;
use App\Models\Item;
use App\Models\Group_Item;
use App\Models\Shop_Group;
use Auth;
use Carbon\Carbon;
use Cookie;
use Html;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\MinigameDistribute;
use Validator;


class CategoryController extends Controller
{

    protected $page_breadcrumbs;
    protected $module;

    public function __construct(Request $request)
    {

        $this->module=$request->segments()[1]??"";

        //set permission to function

        $this->middleware('permission:'. $this->module.'-list');
        $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store']]);
//        $this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:'. $this->module.'-replication', ['only' => ['replication']]);
//        $this->middleware('permission:'. $this->module.'-distribution', ['only' => ['distribution']]);
//        $this->middleware('permission:'. $this->module.'-activeshop', ['only' => ['activeshop']]);
//        $this->middleware('permission:'. $this->module.'-deleteitem', ['only' => ['deleteitem']]);
//        $this->middleware('permission:'. $this->module.'-updateitem', ['only' => ['updateitem']]);

        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'.index'),
                'title' => __(config('module.minigame.'.$this->module.'.title'))
            ];
        }
    }

    public function index(Request $request)
    {

        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);


        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        if($request->ajax) {
            //CHỌN SHOP
            if ($request->filled('shop') && $request->shop == 1)  {

                $id_group = $request->id_group;

//                Các shop đã phân phối.

                $data_shop = Group::with(array('customs' => function ($query) {
                    $query->with('shop');
                }))->with(array('childs' => function ($query) {
                    $query->with(array('customs' => function ($query) {
                        $query->with('shop');
                    }));
                }))
                    ->with(array('parent' => function ($query) {
                        $query->with(array('customs' => function ($query) {
                            $query->with('shop');
                        }));
                    }))
                    ->where('module', '=', 'minigame-category')->findOrFail($id_group);

                $data_shop_arr = array();

                if($data_shop->customs->count()>0){
                    array_push($data_shop_arr,$data_shop->customs[0]->shop_id);

                    if (isset($data_shop->customs)){
                        foreach ($data_shop->customs as $value){
                            array_push($data_shop_arr,$value->shop_id);
                        }
                    }
                }


                $datatable = MinigameDistribute::with('shop')->where('group_id',$id_group)->whereIn('shop_id',$arr_permission)->whereIn('shop_id',$data_shop_arr);

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
                if ($request->filled('status')) {
                    $datatable->where('status',$request->get('status') );
                }

                if ($request->filled('started_at')) {
                    $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
                }

                return \datatables()->eloquent($datatable)->whitelist(['id'])
                    ->only([
                        'id',
                        'domain',
                        'params',
                        'image',
                        'group_id',
                        'shop',
                        'title',
                        'group',
                        'server',
                        'status',
                        'created_at',
                        'action',
                    ])
                    ->toJson();
            }
            elseif ($request->filled('shop') && $request->shop == 2){
                $id_group = $request->id_group;

//                Các shop đã phân phối.

                $data_shop = Group::with(array('customs' => function ($query) {
                    $query->with('shop');
                }))->with(array('childs' => function ($query) {
                    $query->with(array('customs' => function ($query) {
                        $query->with('shop');
                    }));
                }))
                    ->with(array('parent' => function ($query) {
                        $query->with(array('customs' => function ($query) {
                            $query->with('shop');
                        }));
                    }))
                    ->where('module', '=', 'minigame-category')->findOrFail($id_group);

                $data_shop_arr = array();

                if($data_shop->customs->count()>0){
                    array_push($data_shop_arr,$data_shop->customs[0]->shop_id);

                    if (isset($data_shop->customs)){
                        foreach ($data_shop->customs as $value){
                            array_push($data_shop_arr,$value->shop_id);
                        }
                    }
                }


                $datatable = MinigameDistribute::with('shop')->where('group_id',$id_group)->whereIn('shop_id',$arr_permission)->whereIn('shop_id',$data_shop_arr);

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
                if ($request->filled('status')) {
                    $datatable->where('status',$request->get('status') );
                }

                if ($request->filled('started_at')) {
                    $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
                }

                return \datatables()->eloquent($datatable)->whitelist(['id'])
                    ->only([
                        'id',
                        'domain',
                        'params',
                        'image',
                        'shop',
                        'title',
                        'group',
                        'server',
                        'status',
                        'created_at',
                        'action',
                    ])
                    ->toJson();
            }
            elseif ($request->filled('shop') && $request->shop == 3){
                $id_group = $request->id_group;

//                Các shop đã phân phối.

                $data_shop = Group::with(array('customs' => function ($query) {
                    $query->with('shop');
                }))->with(array('childs' => function ($query) {
                    $query->with(array('customs' => function ($query) {
                        $query->with('shop');
                    }));
                }))
                    ->with(array('parent' => function ($query) {
                        $query->with(array('customs' => function ($query) {
                            $query->with('shop');
                        }));
                    }))
                    ->where('module', '=', 'minigame-category')->findOrFail($id_group);

                $data_shop_arr = array();

                if($data_shop->customs->count()>0){
                    array_push($data_shop_arr,$data_shop->customs[0]->shop_id);

                    if (isset($data_shop->customs)){
                        foreach ($data_shop->customs as $value){
                            array_push($data_shop_arr,$value->shop_id);
                        }
                    }
                }


                $datatable = MinigameDistribute::with('shop')->where('group_id',$id_group)
                    ->whereIn('shop_id',$arr_permission)->whereIn('shop_id',$data_shop_arr);

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
                if ($request->filled('status')) {
                    $datatable->where('status',$request->get('status') );
                }

                if ($request->filled('started_at')) {
                    $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
                }

                return \datatables()->eloquent($datatable)->whitelist(['id'])
                    ->only([
                        'id',
                        'domain',
                        'params',
                        'image',
                        'shop',
                        'title',
                        'group',
                        'server',
                        'status',
                        'created_at',
                        'action',
                    ])
                    ->toJson();
            }
            else{

                $datatable= Group::with(array('customs' => function ($query) {
                    if(session('shop_id')){
                        $query->where('shop_id', session('shop_id'));
                        $query->with(array('group' => function ($queryuse) {
                            $queryuse->with(array('customs' => function ($queryuse) {

                            }));
                            $queryuse->withCount(['customs as count_shop_custom' => function($query){

                            }]);
                        }));
                    }
                }));

                $datatable= $datatable->withCount(['customs as count_shop' => function($query) use($arr_permission){

                }])->where('module','=',$this->module);

//                if(session('shop_id')){
//                    $datatable= $datatable->withCount(['customs as count_shop' => function($query) use($arr_permission){
//
//                    }]);
//                }else{
//                    $datatable= $datatable->withCount(['customs as count_shop' => function($query) use($arr_permission){
//
//                    }])
//                        ->whereHas('customs',function($q) {
//
//                        }, '<', 1)
//                        ->where('module','=',$this->module);
//                }

                $datatable->whereHas('customs', function ($querysub) use ($arr_permission){
                    $querysub->whereIn('shop_id', $arr_permission);
                    if(session('shop_id')){
                        $querysub->where('shop_id', session('shop_id'));
                        $querysub->with('group');
                    }
                })->where('module','=',$this->module);

//                if(session('shop_id')){
//
//                }else{
//                    $datatable->orWhereHas('customs', function ($querysub) use ($arr_permission){
//                        $querysub->whereIn('shop_id', $arr_permission);
//                        if(session('shop_id')){
//                            $querysub->where('shop_id', session('shop_id'));
//                        }
//                    })->where('module','=',$this->module);
//                }

                if(session('shop_id')){
                    $datatable->whereHas('customs', function ($querysub) {
                        $querysub->where('shop_id', session('shop_id'));
                        $querysub->with('group');
                    });
                }

                if($request->filled('status')){
                    if(session('shop_id')){
                        $datatable->with('customs', function ($querysub) use ($request){
                            $querysub->where('status',$request->get('status'));
                            $querysub->where('shop_id',session('shop_id'));
                        });
                        $datatable->whereHas('customs', function ($querysub) use ($request){
                            $querysub->where('status',$request->get('status'));
                            $querysub->where('shop_id',session('shop_id'));
                        });
                    }else{
                        $datatable->with('customs', function ($querysub) use ($request){
                            $querysub->where('status',$request->get('status'));
                        });
                        $datatable->whereHas('customs', function ($querysub) use ($request){
                            $querysub->where('status',$request->get('status'));
                        });
                    }

                }

                if($request->filled('shop_group')){
                    $datatable->where(function ($q) use ($request) {
                        $q->with('customs', function ($querysub) use ($request){
                            $querysub->whereRaw("replace(JSON_EXTRACT(meta, '$.shop_group'),'\"','') = ".$request->get('shop_group'));
                        });
                        $q->whereHas('customs', function ($querysub) use ($request){
                            $querysub->whereRaw("replace(JSON_EXTRACT(meta, '$.shop_group'),'\"','') = ".$request->get('shop_group'));
                        });
                    });
                }

                if($request->filled('id')){
                    if(session('shop_id')){
                        $datatable->where(function ($q) use ($request) {
                            $q->with('customs', function ($querysub) use ($request){
                                $querysub->where('id',$request->get('id'));
                            });
                            $q->whereHas('customs', function ($querysub) use ($request){
                                $querysub->where('id',$request->get('id'));
                            });
                        });
                    }else{
                        $datatable->where('id',$request->get('id'));
                    }

                }

                if($request->filled('title')){
                    $datatable->where(function ($q) use ($request) {
                        $q->with('customs', function ($querysub) use ($request){
                            $querysub->where('title', 'LIKE', '%' . $request->get('title') . '%');
                        });
                        $q->whereHas('customs', function ($querysub) use ($request){
                            $querysub->where('title', 'LIKE', '%' . $request->get('title') . '%');
                        });
                        $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
                    });

                }

                if ($request->filled('position')) {
                    $datatable->where('position',$request->get('position'));
                }

                if ($request->filled('game_type')) {
                    $datatable->whereRaw("replace(JSON_EXTRACT(params, '$.game_type'),'\"','') = ".$request->get('game_type'));
                    if ($request->filled('valueitem')) {

                        $datatable->with(['items'=>function($query) use ($request){
                            $query->whereHas('parrent', function ($querysub) use ($request) {
                                $querysub->where('status', 1);
                            });
                            $query->whereIn('items.id',$request->get('valueitem'));
                            $query->select('item_id','title','image','params','parent_id','items.id')->orderByRaw('items.order');
                        }])
                            ->whereHas('items',function($query) use ($request){
                                $query->whereHas('parrent', function ($querysub)  use ($request){
                                    $querysub->where('status', 1);
                                });
                                $query->whereIn('items.id',$request->get('valueitem'));
                                $query->select('item_id','items.order','title','image','params','parent_id','items.id')->orderBy('order');
                            });
                    }
                }

                if ($request->filled('valuefrom') && !$request->filled('valueto')) {
                    $datatable->where('price','>=',$request->get('valuefrom'));
                }

                if ($request->filled('valueto') && !$request->filled('valuefrom')) {
                    $datatable->where('price','<=',$request->get('valueto'));
                }

                if ($request->filled('valueto') && $request->filled('valuefrom')) {
                    $datatable->where('price','>=',$request->get('valuefrom'));
                    $datatable->where('price','<=',$request->get('valueto'));
                }

                return \datatables()->eloquent($datatable)
                    ->only([
                        'id',
                        'title',
                        'image',
                        'status',
                        'locale',
                        'groups',
                        'action',
                        'customs',
                        'position',
                        'price',
                        'meta',
                        'count_shop',
                        'params'
                    ])
                    ->editColumn('created_at', function($data) {
                        return date('d/m/Y H:i:s', strtotime($data->created_at));
                    })

                    ->editColumn('meta', function($data) {
                        $meta = null;
                        $title = '';
                        if(session('shop_id')){
                            if(isset($data) && isset($data->customs) && count($data->customs)){
                                if (isset($data->customs[0]->meta)){
                                    if ($data->customs[0]->meta['shop_group']){
                                        $meta = $data->customs[0]->meta['shop_group'];
                                    }
                                }
                            }

                            if (isset($meta)){
                                $shop_group = Shop_Group::where('id',(int)$meta)->first();
                                if (isset($shop_group)){
                                    $title = $shop_group->title;
                                }
                            }
                        }

                        return $title;
                    })

                    ->editColumn('image', function($data) {
                        $image = '';

                        if(session('shop_id')){
                            if (isset($data->customs[0])){
                                if (isset($data->customs[0]->image)){
                                    $image = "<img class='image-item' src='".\App\Library\MediaHelpers::media($data->customs[0]->image)."' style ='max-width: 90px;max-height: 90px'>";
                                }else{
                                    $image = "<img class=\"image-item\" src=\"/assets/backend/themes/images/empty-photo.jpg\" style=\"max-width: 90px;max-height: 90px\">";
                                }
                            }
//                            else{
//                                if (isset($data->image)){
//                                    $image = "<img class='image-item' src='".\App\Library\MediaHelpers::media($data->image)."' style ='max-width: 90px;max-height: 90px'>";
//                                }else{
//                                    $image = "<img class=\"image-item\" src=\"/assets/backend/themes/images/empty-photo.jpg\" style=\"max-width: 90px;max-height: 90px\">";
//                                }
//                            }
                        }else{
                            if (isset($data->image)){
                                $image = "<img class='image-item' src='".\App\Library\MediaHelpers::media($data->image)."' style ='max-width: 90px;max-height: 90px'>";
                            }else{
                                $image = "<img class=\"image-item\" src=\"/assets/backend/themes/images/empty-photo.jpg\" style=\"max-width: 90px;max-height: 90px\">";
                            }
                        }

                        return $image;
                    })
                    ->addColumn('action', function($row) {

                        $title = $row->customs[0]->title??'';

                        if (session('shop_id')){
                            $temp= "<a data-route=\"".route('admin.'.$this->module.'.edit',$row->id)."?position=".$row->position."\"  data-id=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary add-webits-created-show \" title=\"DS Điểm bán\"><i class=\"la la-eye\"></i></a>";
                        }else{
                            if ($row->count_shop > 0){
                                $temp= "<a data-route=\"".route('admin.'.$this->module.'.edit',$row->id)."?position=".$row->position."\"  data-id=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary add-webits-created-show \" title=\"DS Điểm bán\"><i class=\"la la-eye\"></i></a>";
                            }else{
                                $temp= "";
                            }
                        }

                        if (session('shop_id')){
                            $temp.= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."?position=".$row->position."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                        }else{
                            if ($row->count_shop > 0){
                                $temp.= "<a data-route=\"".route('admin.'.$this->module.'.edit',$row->id)."?position=".$row->position."\"  data-id=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary add-webits-created \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                            }else{
                                $temp.= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."?position=".$row->position."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                            }
                        }

                        if (session('shop_id')){
                            $temp.= "<a href='javascript:void(0)' data-count='0'  rel=\"$row->id\" data-title=\"$title\" data-id=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-primary btn-modal-replication' title=\"Nhân bản\"><i class=\"la la-copy\"></i></a>";
                        }else{
                            $temp.= "<a href='javascript:void(0)' data-count=\"$row->count_shop\"  rel=\"$row->id\" data-title=\"$title\" data-id=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-primary btn-modal-replication' title=\"Nhân bản\"><i class=\"la la-copy\"></i></a>";
                        }
                        if(auth()->user()->hasRole('admin') && !session('shop_id')) {
                            $temp .= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                        }
                        return $temp;
                    })
                    ->rawColumns(['action', 'image','meta'])
                    ->toJson();
            }

        }

        $shopurl = null;
        if(session('shop_id')){
            $shop = Shop::findOrFail(session('shop_id'));
            $shopurl = 'http://'.$shop->domain;
        }

        $data= Group::with(array('customs' => function ($query) {
            if(session('shop_id')){
                $query->where('shop_id', session('shop_id'));
            }
        }))
            ->withCount(['customs as count_shop' => function($query) use($arr_permission){
                $query->whereIn('shop_id', $arr_permission);;
            }])
            ->whereHas('customs', function ($querysub) use ($arr_permission){
                $querysub->whereIn('shop_id', $arr_permission);
                if(session('shop_id')){
                    $querysub->where('shop_id', session('shop_id'));
                }
            })
            ->where('module','=',$this->module);

        $data = $data->get();

        $migame_total = 0;

        if(session('shop_id')){
            $migame_total = MinigameDistribute::where('shop_id',session('shop_id'))->count();
        }else{
            $migame_total = MinigameDistribute::whereNotNull('shop_id')->count();
        }

        $shop_group = Shop_Group::with(['shop' => function ($query) use ($arr_permission){
            $query->whereIn('id',$arr_permission);
        }])->whereHas('shop', function($query) use ($arr_permission){
            $query->where('status', 1);
            $query->whereIn('id',$arr_permission);
        })->where('status',1)->get();

        return view('admin.minigame.module.category.index')
            ->with('module', $this->module)
            ->with('shopurl', $shopurl)
            ->with('groups', $data)
            ->with('shop_group', $shop_group)
            ->with('migame_total', $migame_total)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    public function valueItem(Request $request){

        $game_type = $request->get('game_type');
        $value_item = null;
        if ($request->filled('value_item')){
            $value_item = $request->get('value_item');
        }

        $value_items = Item::where('module', 'minigame-itemset')->where('status',1);

        $value_items = $value_items->where('position',$game_type)->select('id','title','params','position')->get();

        $html = view('admin.minigame.module.category.value_item')
            ->with('data',$value_items)->with('value_item',$value_item)->render();

        return response()->json([
            'status'=>1,
            'data'=>$html,
            'message'=>__('Cập nhật trạng thái thành công !'),
        ]);
    }

    public function create(Request $request)
    {
        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Thêm mới")
        ];

        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        $dataCategory = Group::where('module', '=', 'minigame-seedingpackage')->where('status',1)->orderBy('order','asc')->get();

        $shopurl = '';
        if (session('shop_id')) {
            $shop = Shop::findOrFail(session('shop_id'));
            $shopurl = 'http://'.$shop->domain;
        }

        $data_shop_arr = array();
        $id = null;

        if($request->ajax) {

            //PHÂN PHỐI MINIGAME
            if ($request->filled('phanphoi') && $request->phanphoi == 1)  {

                $datatable= Shop_Group::with(['shop' => function ($query) use ($data_shop_arr,$id){
                    $query->whereIn('id',$data_shop_arr)->with(['minigame_module' => function ($queryuser) use($id){
                        $queryuser->where('group_id',$id);
                    }]);
                }])->whereHas('shop', function($query) use ($data_shop_arr,$arr_permission){
                    $query->where('status', 1);
//            $query->whereNull('id');
                    $query->whereIn('id',$data_shop_arr);
                    $query->whereIn('id',$arr_permission);
                })->withCount(['shop as count_shop' => function($query) use($data_shop_arr,$arr_permission){
                    $query->whereIn('id',$data_shop_arr);
                    $query->whereIn('id',$arr_permission);
                }])->where('status',1);

                return \datatables()->eloquent($datatable)
                    ->editColumn('created_at', function($data) {
                        return date('d/m/Y H:i:s', strtotime($data->created_at));
                    })
                    ->addColumn('action', function($row) {
                        $temp = "<a data-id='".$row->id."' rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle deleteGroupShop' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                        return $temp;
                    })
                    ->toJson();
            }

            //CHỌN SHOP
            if ($request->filled('shop') && $request->shop == 1)  {

                $datatable= Shop::with('group')->where('status',1)
                    ->whereIn('id',$arr_permission);

                if(session('shop_id')){
                    $datatable->where('id','!=',session('shop_id'));
                }

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
                if ($request->filled('status')) {
                    $datatable->where('status',$request->get('status') );
                }

                if ($request->filled('started_at')) {
                    $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
                }

                return \datatables()->eloquent($datatable)->whitelist(['id'])
                    ->only([
                        'id',
                        'domain',
                        'title',
                        'group',
                        'server',
                        'status',
                        'created_at',
                        'action',
                    ])
                    ->toJson();
            }

        }

        $shop_group = Shop_Group::with(['shop' => function ($query) use ($arr_permission){
            $query->whereIn('id',$arr_permission);
        }])->whereHas('shop', function($query) use ($arr_permission){
            $query->where('status', 1);
            $query->whereIn('id',$arr_permission);
        })->where('status',1)->get();

        $secret_key = config('module.service.secret_key');
        $name_shop = Helpers::Encrypt(\Str::slug($shop->title),$secret_key);

        $folder_image = "minigame-config-".$name_shop;

        ActivityLog::add($request, 'Vào form create '.$this->module);
        return view('admin.minigame.module.category.create_edit')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('dataCategory', $dataCategory)
            ->with('shop_group', $shop_group)
            ->with('folder_image', $folder_image)
            ->with('created',1)
            ->with('shopurl', $shopurl);
    }

    public function store(Request $request)
    {
        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $this->validate($request,[
            'position'=>'required',
            'title'=>'required',
            'params.game_type'=>'required'
        ],[
            'position.required' => __('Vui lòng chọn loại minigame'),
            'title.required' => __('Vui lòng nhập tiêu đề'),
            'params.game_type.required' => __('Vui lòng chọn loại vật phẩm'),
            'price.required' => __('Vui lòng nhập phí chơi')
        ]);
        $arrayshopid = $request->arrayshopid;

        if(Auth::user()->account_type == 1){

            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();

            $check_shop = explode('|', $arrayshopid);

            if (isset($arrayshopid)){
                foreach ($check_shop as $val){
                    if (in_array($val,$arr_permission)){}else{
                        return redirect()->back()->with('error',"Shop không có quyền truy cập !");
                    }
                }
            }

        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        $input=$request->all();
        if($request->started_at!=''){
            $input['started_at']= Carbon::createFromFormat('d/m/Y H:i:s', $request->started_at)->format('Y-m-d H:i:s');
        }
        $input['module']=$this->module;
        $input['status']=1; //active
        $input['price'] = str_replace(array(' ', '.'), '', $input['price']);
        //xử lý params
        if($request->filled('params')){
            //check value param ở đây nếu cần //Example:  $params['demo']='Value demo edited'
            $params=$request->params;
            foreach ($params as $aPram=>$key){
                if(str_contains($aPram,"price")){
                    $params[$aPram] = str_replace(array(' ', '.'), '', $params[$aPram]);
                }
            }
            $input['params'] =$params;
        }

        $data= Group::create($input);

        //nếu có chọn shop thì phân phối luôn trên shop đó
        if(session('shop_id')){
            //Them moi cho tung shop
            $input_custom['group_id'] = $data->id;
            $input_custom['shop_id'] = session('shop_id');
            $input_custom['title'] = $request->title;
            $input_custom['description'] = $request->description;
            $input_custom['slug'] = $request->slug;
            $input_custom['seo_title'] = $request->seo_title;
            $input_custom['seo_description'] = $request->seo_description;
            $input_custom['content'] = $request->content;
            $input_custom['image'] = $request->image;
            $input_custom['image_banner'] = $request->image_banner;
            $input_custom['image_icon'] = $request->image_icon;
            $input_custom['status'] = 0; //inactive
            $meta['shop_group'] = $request->meta;

            $input_custom['meta'] = $meta;

            if ($request->filled('order')) {
                $input_custom['order'] = (int)$request->order;
            }else{
                $input_custom['order'] = null;
            }
            //xử lý params
            if($request->filled('params')){
                //check value param ở đây nếu cần //Example:  $params['demo']='Value demo edited'
                $params=$request->params;
                $input_custom['params'] =$params;
            }
            $data_custom =MinigameDistribute::create($input_custom);

            $data_custom->status = 0;
            $data_custom->save();
        }

        if(isset($arrayshopid)){

            $data_shop = explode('|', $arrayshopid);
            if(isset($data_custom)){
                $groupshop = MinigameDistribute::where('id', $data_custom->id)->first();
            }else{
                //Them moi cho shop dau tien duoc chon
                $input_custom['group_id'] = $data->id;
                $input_custom['shop_id'] = session('shop_id')??$data_shop[0];
                $input_custom['title'] = $request->title;
                $input_custom['description'] = $request->description;
                $input_custom['slug'] = $request->slug;
                $input_custom['seo_title'] = $request->seo_title;
                $input_custom['seo_description'] = $request->seo_description;
                $input_custom['content'] = $request->content;
                $input_custom['image'] = $request->image;
                $input_custom['image_banner'] = $request->image_banner;
                $input_custom['image_icon'] = $request->image_icon;
                $meta['shop_group'] = $request->meta;

                $input_custom['meta'] = $meta;
                $input_custom['status'] = 0; //inactive
                //xử lý params
                if($request->filled('params')){
                    //check value param ở đây nếu cần //Example:  $params['demo']='Value demo edited'
                    $params=$request->params;
                    $input_custom['params'] =$params;
                }

                if ($request->filled('order')) {
                    $input_custom['order'] = (int)$request->order;
                }else{
                    $input_custom['order'] = null;
                }

                $groupshop =MinigameDistribute::create($input_custom);

                $groupshop->status = 0;
                $groupshop->save();
            }

            foreach ($data_shop as $shop){
                if((int)$shop != $groupshop->shop_id){
                    //không tạo lại bạn ghi đầu
                    $groupshop_new = $groupshop->replicate()->fill(
                        [
                            'created_at' => Carbon::now(),
                            'shop_id'  => (int)$shop,
                            'status'  => 0,
                        ]
                    );
                    $groupshop_new->save();
                }

            }
        }

        ActivityLog::add($request, 'Tạo mới thành công '.$this->module.' #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Thêm mới thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Thêm mới thành công !'));
        }
    }

    public function edit(Request $request,$id){

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật")
        ];


        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        $st_data =  Shop::where('id',$request->shop_id)->first();

        if($st_data){
            session()->put('shop_id', $st_data->id);
            session()->put('shop_name', $st_data->domain);
        }

        $data_shop = Group::with(array('customs' => function ($query) use ($arr_permission){
            $query->with('shop');
            $query->whereIn('shop_id', $arr_permission);
        }))->with(array('childs' => function ($query) use($arr_permission){
            $query->with(array('customs' => function ($query) use($arr_permission){
                $query->whereIn('shop_id', $arr_permission);
                $query->with('shop');
            }));
        }))
            ->with(array('parent' => function ($query) use($arr_permission){
                $query->with(array('customs' => function ($query) use($arr_permission){
                    $query->with('shop');
                    $query->whereIn('shop_id', $arr_permission);
                }));
            }))
            ->where('module', '=', 'minigame-category')->findOrFail($id);

        $shopname = null;
        $c_data = null;
//        Kiểm tra có phải là nhân bản hay không().

        $isReplication = 0;

        $data_shop_arr = array();
        $shop_check = '';

        if($data_shop->customs->count()>0){
            array_push($data_shop_arr,$data_shop->customs[0]->shop_id);

            if (isset($data_shop->customs)){
                foreach ($data_shop->customs as $value){
                    array_push($data_shop_arr,$value->shop_id);
                }
            }

            $shop_check = $data_shop->customs[0]->shop_id;
        }

        $data = Group::with(array('customs' => function ($query) {

        }))->where('module', '=', $this->module)->findOrFail($id);

        $position = 0;

        if (isset($data->position)){
            $position = config('module.minigame.number_of_items.number_of_items_'.$data->position.'');
        }

        if (session('shop_id')){
            $d_shopid = session('shop_id');
            $count_giaithuong = Item::with(array('groups' => function ($query) {
                $query->where('module', 'minigame-type');
                $query->select('groups.id','title');
            }))
                ->with(array('children' => function ($query) use ($id,$d_shopid)  {
                    $query->where('module', 'minigame-itemset');
                    $query->whereHas('groups', function ($querysub) use ($id) {
                        $querysub->where('group_id',$id);
                    });
                    $query->with(array('children' => function ($query) use ($d_shopid) {
                        $query->where('module', 'minigame-itemset');
                        $query->where('shop_id',$d_shopid);
                    }));
                    $query->whereHas('children', function ($querysub) use ($d_shopid) {
                        $querysub->where('shop_id',$d_shopid);
                    });
                }))
                ->whereHas('children', function ($query) use ($id,$d_shopid)  {
                    $query->where('module', 'minigame-itemset');
                    $query->whereHas('groups', function ($querysub) use ($id) {
                        $querysub->where('group_id',$id);
                    });
                    $query->whereHas('children', function ($querysub) use ($d_shopid) {
                        $querysub->where('shop_id',$d_shopid);
                    });
                })->where('module', 'minigame')->where('status', 1)->count();
        }
        else{
            $count_giaithuong = Item::with(array('groups' => function ($query) {
                $query->where('module', 'minigame-type');
                $query->select('groups.id','title');
            }))
                ->with(array('children' => function ($query) use ($id)  {
                    $query->where('module', 'minigame-itemset');
                    $query->whereHas('groups', function ($querysub) use ($id) {
                        $querysub->where('group_id',$id);
                    });
                }))
                ->where('module', 'minigame')->where('status', 1)
                ->whereHas('children', function ($query) use ($id)  {
                    $query->where('module', 'minigame-itemset');
                    $query->whereHas('groups', function ($querysub) use ($id) {
                        $querysub->where('group_id',$id);
                    });
                })->count();
        }


        $flastPosition = true;

        if ((int)$position == (int)$count_giaithuong){
            $flastPosition = false;
        }

        $log_edit = null;

        if (session('shop_id')){
            $c_data = Group::with(array('customs' => function ($query) {
                $query->where('shop_id', session('shop_id'));
            }))->where('module', '=', $this->module)->findOrFail($id);

            $l_data = MinigameDistribute::where('id',$c_data->customs[0]->id)->first();
            $table_name = $l_data->getTable();

            $log_edit = LogEdit::where('table_name',$table_name)->with(array('author' => function ($query) {
                $query->select('id','username');
            }))->where('table_id',$l_data->id)->get();

        }

        if ($data->customs->count() > 0){

            $data = Group::with(array('customs' => function ($query) use($arr_permission){
                $query->whereIn('shop_id', $arr_permission);
            }))
                ->where('module', '=', $this->module)
                ->whereHas('customs', function($queryuse) use ($arr_permission){
                    $queryuse->whereIn('shop_id',$arr_permission);
                })->where('id',$id)
                ->first();

            if (session('shop_id')){
                $data = Group::with(array('customs' => function ($query) use($arr_permission){
                    $query->whereIn('shop_id', $arr_permission);
                    $query->where('shop_id', session('shop_id'));
                }))
                    ->where('module', '=', $this->module)
                    ->whereHas('customs', function($queryuse) use ($arr_permission){
                        $queryuse->whereIn('shop_id',$arr_permission);
                        $queryuse->where('shop_id', session('shop_id'));
                    })->where('id',$id)
                    ->first();
            }

            if (!$data){
                return redirect()->back()->with('error',"Shop không có quyền truy cập !");
            }
        }

//        return $data;

        $data_custom = new \stdClass();
        if($data->customs->count()==0){

            $data_custom->params = new \stdClass();
            $data_custom->title = $data->title;
            $data_custom->slug = $data->slug;
            $data_custom->description = $data->description;
            $data_custom->image = $data->image;
            $data_custom->image_banner = $data->image_banner;
            $data_custom->image_icon = $data->image_icon;
            $data_custom->params->image_static = $data->params->image_static??'';
            $data_custom->params->image_animation = $data->params->image_animation??'';
            $data_custom->params->image_background = $data->params->image_background??'';
            $data_custom->params->image_percent_sale = $data->params->image_percent_sale??'';
            $data_custom->params->image_view_all = $data->params->image_view_all??'';
            $data_custom->params->fake_num_play = $data->params->fake_num_play??'';
            $data_custom->params->percent_sale = $data->params->percent_sale;
            $data_custom->params->user_wheel = $data->params->user_wheel;
            $data_custom->params->user_wheel_order = $data->params->user_wheel_order;
            $data_custom->idkey = $data->idkey;
            $data_custom->params->acc_show_num = $data->params->acc_show_num;
            $data_custom->params->play_num_from = $data->params->play_num_from;
            $data_custom->params->play_num_to = $data->params->play_num_to;
            $data_custom->params->user_num_from = $data->params->user_num_from;
            $data_custom->params->user_num_to = $data->params->user_num_to;
            $data_custom->params->play_num_near = $data->params->play_num_near;
            $data_custom->params->special_num_from = $data->params->special_num_from;
            $data_custom->params->special_num_to = $data->params->special_num_to;
            $data_custom->params->gift_num_exist = $data->params->gift_num_exist;
            $data_custom->started_at = $data->started_at;
            $data_custom->seo_title = $data->seo_title;
            $data_custom->seo_description = $data->seo_description;
            $data_custom->content = $data->content;
            $data_custom->params->thele = $data->params->thele;
            $data_custom->params->phanthuong = $data->params->phanthuong;
            $data_custom->content = $data->content;
        }else{
            $data_custom = $data->customs[0];
        }

        // seedingpackage
        $dataCategory = Group::where('module', '=', 'minigame-seedingpackage')->where('status',1)->orderBy('order','asc')->get();

        // Hết seedingpackage

        if($request->ajax) {
            // CẤU HÌNH GIẢI THƯỞNG

            if ($request->filled('chgt') && $request->chgt == 1)  {

                if (session('shop_id')){
                    $d_shopid = session('shop_id');
                    $datatable = Item::with(array('groups' => function ($query) {
                        $query->where('module', 'minigame-type');
                        $query->select('groups.id','title');
                    }))
                        ->with(array('children' => function ($query) use ($id,$d_shopid)  {
                            $query->where('module', 'minigame-itemset');
                            $query->whereHas('groups', function ($querysub) use ($id) {
                                $querysub->where('group_id',$id);
                            });
                            $query->with(array('children' => function ($query) use ($d_shopid) {
                                $query->where('module', 'minigame-itemset');
                                $query->where('shop_id',$d_shopid);
                            }));
                            $query->whereHas('children', function ($querysub) use ($d_shopid) {
                                $querysub->where('shop_id',$d_shopid);
                            });
                        }))
                        ->whereHas('children', function ($query) use ($id,$d_shopid)  {
                            $query->where('module', 'minigame-itemset');
                            $query->whereHas('groups', function ($querysub) use ($id) {
                                $querysub->where('group_id',$id);
                            });
                            $query->whereHas('children', function ($querysub) use ($d_shopid) {
                                $querysub->where('shop_id',$d_shopid);
                            });
                        })->where('module', 'minigame')->where('status', 1);
                }
                else{

                    $datatable = Item::with(array('groups' => function ($query) {
                        $query->where('module', 'minigame-type');
                        $query->select('groups.id','title');
                    }))
                        ->with(array('children' => function ($query) use ($id)  {
                            $query->where('module', 'minigame-itemset');
                            $query->whereHas('groups', function ($querysub) use ($id) {
                                $querysub->where('group_id',$id);
                            });
                        }));
                }

                if ($request->filled('gametype')) {

                    $datatable->whereHas('groups', function ($query) use ($request) {
                        $query->where('group_id',$request->get('gametype'));
                    });
                }

                if ($request->filled('id'))  {
                    $datatable->where(function($q) use($request){
                        $q->orWhere('id', $request->get('id'));
                        $q->orWhere('idkey',$request->get('id') );
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
                if ($request->filled('positiongt')) {
                    $datatable->where('position',$request->get('positiongt') );
                }
                if ($request->filled('valuefrom') && !$request->filled('valueto')) {
                    $datatable = $datatable->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') >= ".$request->get('valuefrom'));
                }

                if ($request->filled('valueto') && !$request->filled('valuefrom')) {
                    $datatable = $datatable->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') <= ".$request->get('valueto'));
                }

                if ($request->filled('valueto') && $request->filled('valuefrom')) {
                    $datatable = $datatable->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') >= ".$request->get('valuefrom'));
                    $datatable = $datatable->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') <= ".$request->get('valueto'));
                }

                return \datatables()->eloquent($datatable)

                    ->only([
                        'id',
                        'title',
                        'slug',
                        'order',
                        'image',
                        'locale',
                        'groups',
                        'status',
                        'action',
                        'title_custom',
                        'image_custom',
                        'created_at',
                        'params',
                        'children'
                    ])
                    ->addColumn('image_custom', function($row){

                        $custom = Item::where('parent_id',$row->children[0]->id)->where('shop_id',session('shop_id'))->first();
                        if($custom && $custom->image!=""){
                            return \App\Library\MediaHelpers::media($custom->image);
                        }else{
                            return \App\Library\MediaHelpers::media($row->image);
                        }
                    })
                    ->addColumn('title_custom', function($row) {
                        $custom = Item::where('parent_id',$row->children[0]->id)->where('shop_id',session('shop_id'))->first();
                        if($custom && $custom->title!=""){
                            return $custom->title;
                        }else{
                            return $row->title;
                        }
                    })
                    ->addColumn('action', function($row) {

                        $custom = Item::where('parent_id',$row->children[0]->id)->where('shop_id',session('shop_id'))->first();
                        $id='';
                        $title= $row->title;
                        $image= \App\Library\MediaHelpers::media($row->image);
                        $parent_id=$row->children[0]->id;

                        $title_old = $row->title;
                        $image_old = \App\Library\MediaHelpers::media($row->image);
                        $title_new = null;
                        $image_new = null;

                        if($custom){
                            $id=$custom->id;
                        }
                        if($custom && $custom->title!=""){
                            $title=$custom->title;
                            $title_new = $custom->title;
                        }
                        if($custom && $custom->image!=""){
                            $image= \App\Library\MediaHelpers::media($custom->image);
                            $image_new = \App\Library\MediaHelpers::media($custom->image);

                        }
                        $temp= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger' onclick=\"customShow('".$parent_id."','".$id."','".$title_old."','".$image_old."','".$title_new."','".$image_new."')\" title=\"Sửa riêng cho shop\"><i class=\"la la-edit\"></i></a>";
                        $temp.= "<a  rel='".$row->children[0]->id."' class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteItemModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                        return $temp;
                    })
                    ->toJson();
            }

            //PHÂN PHỐI MINIGAME
            if ($request->filled('phanphoi') && $request->phanphoi == 1)  {

                $datatable= Shop_Group::with(['shop' => function ($query) use ($data_shop_arr,$id,$arr_permission){
                    $query->whereIn('id',$data_shop_arr)->whereIn('id',$arr_permission)->with(['minigame_module' => function ($queryuser) use($id){
                        $queryuser->where('group_id',$id);
                    }]);
                }])->whereHas('shop', function($query) use ($data_shop_arr,$arr_permission){
                    $query->where('status', 1);
//            $query->whereNull('id');
                    $query->whereIn('id',$arr_permission);
                    $query->whereIn('id',$data_shop_arr);
                })->withCount(['shop as count_shop' => function($query) use($data_shop_arr,$arr_permission){
                    $query->whereIn('id',$data_shop_arr);
                    $query->whereIn('id',$arr_permission);
                }])->where('status',1);

                return \datatables()->eloquent($datatable)
                    ->editColumn('created_at', function($data) {
                        return date('d/m/Y H:i:s', strtotime($data->created_at));
                    })
                    ->addColumn('action', function($row) {
                        $temp = "<a data-id='".$row->id."' rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle deleteGroupShop' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                        return "";
                    })
                    ->toJson();
            }

            //CHỌN SHOP
            if ($request->filled('shop') && $request->shop == 1)  {

                $datatable= Shop::with('group')
                    ->where('status',1)
                    ->whereNotIn('id',$data_shop_arr)->whereIn('id',$arr_permission);;
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
                if ($request->filled('group_shop')) {
                    $datatable->where('group_id',$request->get('group_shop') );
                }
                if ($request->filled('status')) {
                    $datatable->where('status',$request->get('status') );
                }

                if ($request->filled('started_at')) {
                    $datatable->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
                }

                return \datatables()->eloquent($datatable)->whitelist(['id'])
                    ->only([
                        'id',
                        'domain',
                        'title',
                        'group',
                        'server',
                        'status',
                        'created_at',
                        'action',
                    ])
                    ->toJson();
            }

            //DANH SACH GIAI THUONG.
            if ($request->filled('ctchgt') && $request->ctchgt == 1)  {

                //Giải thưởng đã set

                if (session('shop_id')){
                    $d_shopid = session('shop_id');
                    $data_arr = Item::with(array('groups' => function ($query) {
                        $query->where('module', 'minigame-type');
                        $query->select('groups.id','title');
                    }))
                        ->with(array('children' => function ($query) use ($id,$d_shopid)  {
                            $query->where('module', 'minigame-itemset');
                            $query->whereHas('groups', function ($querysub) use ($id) {
                                $querysub->where('group_id',$id);
                            });
                            $query->with(array('children' => function ($query) use ($d_shopid) {
                                $query->where('module', 'minigame-itemset');
                                $query->where('shop_id',$d_shopid);
                            }));
                            $query->whereHas('children', function ($querysub) use ($d_shopid) {
                                $querysub->where('shop_id',$d_shopid);
                            });
                        }))
                        ->whereHas('children', function ($query) use ($id,$d_shopid)  {
                            $query->where('module', 'minigame-itemset');
                            $query->whereHas('groups', function ($querysub) use ($id) {
                                $querysub->where('group_id',$id);
                            });
                            $query->whereHas('children', function ($querysub) use ($d_shopid) {
                                $querysub->where('shop_id',$d_shopid);
                            });
                        })->where('module', 'minigame')->where('status', 1)->pluck('id')->toArray();
                }
                else{

                    $data_arr = Item::with(array('groups' => function ($query) {
                        $query->where('module', 'minigame-type');
                        $query->select('groups.id','title');
                    }))
                        ->with(array('children' => function ($query) use ($id)  {
                            $query->where('module', 'minigame-itemset');
                            $query->whereHas('groups', function ($querysub) use ($id) {
                                $querysub->where('group_id',$id);
                            });
                        }))->where('status', 1)->pluck('id')->toArray();
                }

                //Giải thưởng chưa set
                $datatable = Item::with(array('groups' => function ($query) {
                    $query->where('module', 'minigame-type');
                    $query->select('groups.id','title');
                }))
                    ->with(array('children' => function ($query) use ($id)  {
                        $query->where('module', 'minigame-itemset');
                        $query->whereHas('groups', function ($querysub) use ($id) {
                            $querysub->where('group_id',$id);
                        });
                    }))
                    ->where('module', 'minigame')->where('status', 1)
                    ->whereNotIn('id',$data_arr);

                if ($request->filled('gametype')) {

                    $datatable->whereHas('groups', function ($query) use ($request) {
                        $query->where('group_id',$request->get('gametype'));
                    });
                }

                if ($request->filled('id'))  {
                    $datatable->where(function($q) use($request){
                        $q->orWhere('id', $request->get('id'));
                        $q->orWhere('idkey',$request->get('id') );
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
                if ($request->filled('valuefrom') && !$request->filled('valueto')) {
                    $datatable = $datatable->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') >= ".$request->get('valuefrom'));
                }

                if ($request->filled('position')) {
                    $datatable->where('position',$request->get('position') );
                }

                if (isset($data->params)){
                    $c_position = $data->params->game_type;
                    $datatable->where('position',$c_position );
                }

                if ($request->filled('valueto') && !$request->filled('valuefrom')) {
                    $datatable = $datatable->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') <= ".$request->get('valueto'));
                }

                if ($request->filled('valueto') && $request->filled('valuefrom')) {
                    $datatable = $datatable->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') >= ".$request->get('valuefrom'));
                    $datatable = $datatable->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') <= ".$request->get('valueto'));
                }
//                $datatable = $datatable->unionAll($datatable1);

                return \datatables()->eloquent($datatable)

                    ->only([
                        'id',
                        'title',
                        'slug',
                        'order',
                        'image',
                        'locale',
                        'groups',
                        'status',
                        'position',
                        'action',
                        'created_at',
                        'params',
                        'children'
                    ])
                    ->toJson();
            }

        }

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

//Kiểm tra nhóm shop có đủ shop chưa.
        $shop_group_check = Shop_Group::with(['shop' => function ($query) use ($data_shop_arr,$id,$arr_permission){
            $query->whereIn('id',$data_shop_arr)->whereIn('id',$arr_permission)->with(['minigame_module' => function ($queryuser) use($id){
                $queryuser->where('group_id',$id);
            }]);
        }])->whereHas('shop', function($query) use ($data_shop_arr,$arr_permission){
            $query->where('status', 1);
            $query->whereIn('id',$arr_permission);
            $query->whereIn('id',$data_shop_arr);
        })->withCount(['shop as count_shop_check' => function($query) use($data_shop_arr,$arr_permission){
            $query->whereIn('id',$arr_permission);
            $query->whereIn('id',$data_shop_arr);
        }])->where('status',1)->get();

        $arr_group_shop = array();

        foreach ($shop_group_check as $i_group){

            $m_group = Shop_Group::with(['shop' => function ($query) use ($arr_permission){
                $query->whereIn('id',$arr_permission);
            }])->where('id',$i_group->id)
                ->withCount(['shop as count_shop' => function($query) use($arr_permission){
                    $query->whereIn('id',$arr_permission);
                }])->where('status',1)->first();
            if ($i_group->count_shop_check == $m_group->count_shop){
                array_push($arr_group_shop,$i_group->id);
            }
//            return $m_group;
        }

        $shop_group = Shop_Group::with(['shop' => function ($query) use ($arr_permission){
            $query->whereIn('id',$arr_permission);
        }])->whereHas('shop', function($query) use ($arr_permission){
            $query->whereIn('id',$arr_permission);
        })->withCount(['shop as count_shop' => function($query) use($arr_permission){
            $query->whereIn('id',$arr_permission);
        }])->whereNotIn('id',$arr_group_shop)->where('status',1)->get();

        $shopurl = '';

        if (session('shop_id')) {
            $shop = Shop::findOrFail(session('shop_id'));
            $shopurl = 'http://'.$shop->domain;
        }

        $data_shop_str = "";
        $check_arr = array();

        foreach($data_shop_arr as $keyid=> $id){
            if ($keyid == 0){
                array_push($check_arr,$id);
                $data_shop_str = $id;
            }else{
                if (!in_array($id,$check_arr)){
                    $data_shop_str = $data_shop_str.'|'.$id;
                }
            }
        }

        $route = route('admin.'.$this->module.'.edit',$data->id)."?position=".$data->position;

        $dataCategorygt = Group::where('module', '=',  'minigame-type')->where('status',1);

        $dataCategorygt = $dataCategorygt->orderBy('order','asc')->get();


        $secret_key = config('module.service.secret_key');
        $name_shop = Helpers::Encrypt(\Str::slug($shop->title??'nottitle'),$secret_key);
        $folder_image = "minigame-config-".$name_shop;


        ActivityLog::add($request, 'Vào form edit '.$this->module);

        return view('admin.minigame.module.category.create_edit')
            ->with('dataCategory', $dataCategory)
            ->with('module', $this->module)
            ->with('client', $client)
            ->with('shop_group', $shop_group)
            ->with('c_data_shop', json_encode($data_shop))
            ->with('shopurl', $shopurl)
            ->with('data', $data)
            ->with('folder_image', $folder_image)
            ->with('c_data', $c_data)
            ->with('route', $route)
            ->with('log_edit', $log_edit)
            ->with('shopname', $shopname)
            ->with('position', $position)
            ->with('isReplication', $isReplication)
            ->with('data_shop', $data_shop)
            ->with('count_giaithuong', $count_giaithuong)
            ->with('shop_check', $shop_check)
            ->with('data_custom', $data_custom)
            ->with('dataCategorygt', $dataCategorygt)
            ->with('c_module', $this->module)
            ->with('flastPosition', $flastPosition)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data_shop_str', $data_shop_str);
    }

    public function update(Request $request,$id)
    {
        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $this->validate($request,[
            'position'=>'required',
            'title'=>'required',
            'params.game_type'=>'required',
            'price'=>'required|numeric|min:1000'
        ],[
            'position.required' => __('Vui lòng chọn loại minigame'),
            'title.required' => __('Vui lòng nhập tiêu đề'),
            'params.game_type.required' => __('Vui lòng chọn loại vật phẩm'),
            'price.required' => __('Vui lòng nhập phí chơi'),
            'price.min' => __('Giá thấp nhất là 9999'),
        ]);

        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

//        $data =  Group::where('module', '=', $this->module)->findOrFail($id);

        $data = Group::with(array('customs' => function ($query) {
        }))->where('module', '=', $this->module)->findOrFail($id);

        if ($data->customs->count() > 0){
            $data = Group::with(array('customs' => function ($query) use($arr_permission){
                $query->whereIn('shop_id', $arr_permission);
            }))
                ->where('module', '=', $this->module)
                ->whereHas('customs', function($queryuse) use ($arr_permission){
                    $queryuse->whereIn('shop_id',$arr_permission);
                })->where('id',$id)
                ->first();

            if (!$data){
                return redirect()->back()->with('error',"Shop không có quyền truy cập !");
            }
        }

        $input=$request->all();

        if($request->started_at!=''){
            $input['started_at']= Carbon::createFromFormat('d/m/Y H:i:s', $request->started_at)->format('Y-m-d H:i:s');
        }
        $input['module']=$this->module;
        $input['status']= 1;
        $input['price']=str_replace(array(' ', '.'), '', $input['price']);
        //xử lý params
        if($request->filled('params')){
            //check value param ở đây nếu cần //Example:  $params['demo']='Value demo edited'
            $params=$request->params;
            foreach ($params as $aPram=>$key){
                if(str_contains($aPram,"price")){
                    $params[$aPram] = str_replace(array(' ', '.'), '', $params[$aPram]);
                }
            }
            $input['params'] =$params;
        }

        if (session('shop_id')){
            $input['title'] = $data->title;
        }

        $data->update($input);

        //update custom cho tung shop
        $data_custom =  MinigameDistribute::where('group_id', $data->id)->where('shop_id', session('shop_id'))->first();
        //nếu đã có thì update
        if($data_custom){
            $input_custom['title'] = $request->title;
            $input_custom['description'] = $request->description;
            $input_custom['slug'] = $request->slug;
            $input_custom['seo_title'] = $request->seo_title;
            $input_custom['seo_description'] = $request->seo_description;
            $input_custom['content'] = $request->content;
            $input_custom['image'] = $request->image;
            $input_custom['image_banner'] = $request->image_banner;
            $input_custom['image_icon'] = $request->image_icon;
            $meta['shop_group'] = $request->meta;

            $input_custom['meta'] = $meta;
            if ($request->filled('order')) {
                $input_custom['order'] = (int)$request->order;
            }else{
                $input_custom['order'] = null;
            }

            //xử lý params
            if($request->filled('params')){
                //check value param ở đây nếu cần //Example:  $params['demo']='Value demo edited'
                $params=$request->params;
                $input_custom['params'] =$params;
            }

//        Lưu log edit
            if (session('shop_id')){

                $c_input['title_before'] = $data_custom->title;
                $c_input['title_after'] = $input_custom['title'];
                $c_input['description_before'] = $data_custom->description;
                $c_input['description_after'] = $input_custom['description'];
                $c_input['seo_title_before'] = $data_custom->seo_title;
                $c_input['seo_title_after'] = $input_custom['seo_title'];
                $c_input['seo_description_before'] = $data_custom->seo_description;
                $c_input['seo_description_after'] = $input_custom['seo_description'];
                $c_input['content_before'] = $data_custom->content;
                $c_input['content_after'] = $input_custom['content'];
                $c_input['author_id'] = auth()->user()->id;
                $c_input['type'] = 0;
                $c_input['table_name'] = $data_custom->getTable();
                $c_input['table_id'] = $data_custom->id;
                $c_input['shop_id'] = session('shop_id');

                if ($c_input['title_before'] == $c_input['title_after'] && $c_input['description_before'] == $c_input['description_after'] && $c_input['content_before'] == $c_input['content_after']){

                }else{
                    LogEdit::create($c_input);
                }
            }

            $data_custom->update($input_custom);
        }

        ActivityLog::add($request, 'Cập nhật thành công '.$this->module.' #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Cập nhật thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
        }
    }

    public function revision(Request $request,$id,$slug)
    {
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Revision"),
        ];


        $data = MinigameDistribute::where('group_id',$id)->where('shop_id',session('shop_id'))->first();

        $log = LogEdit::where('id',$slug)->with(array('author' => function ($query) {
            $query->select('id','username');
        }))->first();

        ActivityLog::add($request, 'Vào form revision minigame #'.$data->id);
        return view('admin.minigame.module.category.revision')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data)
            ->with('log', $log)
            ->with('slug', $slug);

    }

    public function postRevision(Request $request,$id,$slug){

        $data = MinigameDistribute::where('id',$id)->where('shop_id',session('shop_id'))->first();

        $log = LogEdit::where('id',$slug)->with(array('author' => function ($query) {
            $query->select('id','username');
        }))->first();

//        update
        $input['title'] = $log->title_before;
        $input['seo_title'] = $log->seo_title_before;
        $input['description'] = $log->description_before;
        $input['seo_description'] = $log->seo_description_before;
        $input['content'] = $log->content_before;

//   Lưu log
        $c_input['title_before'] = $data->title;
        $c_input['title_after'] = $log->title_before;
        $c_input['description_before'] = $data->description;
        $c_input['description_after'] = $log->description_before;
        $c_input['seo_title_before'] = $data->seo_title;
        $c_input['seo_title_after'] = $log->seo_title_before;
        $c_input['seo_description_before'] = $data->seo_description;
        $c_input['seo_description_after'] = $log->seo_description_before;
        $c_input['content_before'] = $data->content;
        $c_input['content_after'] = $log->content_before;
        $c_input['author_id'] = auth()->user()->id;
        $c_input['type'] = 1;
        $c_input['table_name'] = $data->getTable();
        $c_input['table_id'] = $data->id;

        LogEdit::create($c_input);

        $data->update($input);

        ActivityLog::add($request, 'Phục hồi bài viết thành công minigame #'.$data->id);

        return redirect()->route('admin.minigame-category.index')->with('success',__('Phục hồi thành công !'));
    }

    public function setitem(Request $request,$id)
    {

        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'.index'),
                'title' => __(config('module.minigame.'.$this->module.'.title').' - cấu hình giải thưởng')
            ];
        }

        $data = Group::where('module', '=', $this->module)->findOrFail($id);
        if($data->shop_id){
            $shop = Shop::findOrFail($data->shop_id);
            session()->put('shop_id', $shop->id);
            session()->put('shop_name', $shop->domain);
        }
        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);

        if($request->ajax) {

            $datatable= Item::with(array('groups' => function ($query) {
                $query->where('module', 'minigame-type');
                $query->select('groups.id','title');
            }))
                ->with(array('children' => function ($query) use ($id)  {
                    $query->where('module', 'minigame-itemset');
                    $query->whereHas('groups', function ($querysub) use ($id) {
                        $querysub->where('group_id',$id);
                    });
                }))
                ->where('module', 'minigame')->where('status', 1)->whereHas('children', function ($query) use ($id)  {
                    $query->where('module', 'minigame-itemset');
                    $query->whereHas('groups', function ($querysub) use ($id) {
                        $querysub->where('group_id',$id);
                    });
                });

            if ($request->filled('gametype')) {

                $datatable->whereHas('groups', function ($query) use ($request) {
                    $query->where('group_id',$request->get('gametype'));
                });
            }

            if ($request->filled('id'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('id', $request->get('id'));
                    $q->orWhere('idkey',$request->get('id') );
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
            if ($request->filled('position')) {
                $datatable->where('position',$request->get('position') );
            }
            if ($request->filled('valuefrom') && !$request->filled('valueto')) {
                $datatable = $datatable->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') >= ".$request->get('valuefrom'));
            }

            if ($request->filled('valueto') && !$request->filled('valuefrom')) {
                $datatable = $datatable->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') <= ".$request->get('valueto'));
            }

            if ($request->filled('valueto') && $request->filled('valuefrom')) {
                $datatable = $datatable->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') >= ".$request->get('valuefrom'));
                $datatable = $datatable->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') <= ".$request->get('valueto'));
            }

            if ($request->get('setted')!=1) {
                $datatable1= Item::with(array('groups' => function ($query) {
                    $query->where('module', 'minigame-type');
                    $query->select('groups.id','title');
                }))
                    ->with(array('children' => function ($query) use ($id)  {
                        $query->where('module', 'minigame-itemset');
                        $query->whereHas('groups', function ($querysub) use ($id) {
                            $querysub->where('group_id',$id);
                        });
                    }))
                    ->where('module', 'minigame')->where('status', 1)->whereHas('children', function ($query) use ($id)  {
                        $query->where('module', 'minigame-itemset');
                        $query->whereHas('groups', function ($querysub) use ($id) {
                            $querysub->where('group_id',$id);
                        });
                    },'<',1);

                // if (session('shop_id')) {
                //     $datatable1->where('shop_id',session('shop_id'));
                // }

                if ($request->filled('gametype')) {

                    $datatable1->whereHas('groups', function ($query) use ($request) {
                        $query->where('group_id',$request->get('gametype'));
                    });
                }

                if ($request->filled('id'))  {
                    $datatable1->where(function($q) use($request){
                        $q->orWhere('id', $request->get('id'));
                        $q->orWhere('idkey',$request->get('id') );
                    });
                }

                if ($request->filled('title'))  {
                    $datatable1->where(function($q) use($request){
                        $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
                    });
                }

                if ($request->filled('started_at')) {
                    $datatable1->where('created_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('started_at')));
                }
                if ($request->filled('ended_at')) {
                    $datatable1->where('created_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $request->get('ended_at')));
                }
                if ($request->filled('valuefrom') && !$request->filled('valueto')) {
                    $datatable1 = $datatable1->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') >= ".$request->get('valuefrom'));
                }

                if ($request->filled('valueto') && !$request->filled('valuefrom')) {
                    $datatable1 = $datatable1->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') <= ".$request->get('valueto'));
                }

                if ($request->filled('valueto') && $request->filled('valuefrom')) {
                    $datatable1 = $datatable1->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') >= ".$request->get('valuefrom'));
                    $datatable1 = $datatable1->whereRaw("replace(JSON_EXTRACT(params, '$.value'),'\"','') <= ".$request->get('valueto'));
                }
                $datatable = $datatable->unionAll($datatable1);
            }

            return \datatables()->eloquent($datatable)

                ->only([
                    'id',
                    'title',
                    'slug',
                    'order',
                    'image',
                    'locale',
                    'groups',
                    'status',
                    'action',
                    'created_at',
                    'params',
                    'children'
                ])->toJson();
        }

        $dataCategory = Group::where('module', '=',  'minigame-type')->where('status',1);
        // if (session('shop_id')) {
        //     $dataCategory->where('shop_id',session('shop_id'));
        // }
        if ($request->filled('position')) {
            $dataCategory->where('position',$request->get('position') );
        }
        $dataCategory = $dataCategory->orderBy('order','asc')->get();
        $dataCat = Group::where('module', '=',  'minigame-category')->where('id', $id)->first();
        return view('admin.minigame.module.category.setitem')
            ->with('module', $this->module)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('dataCategory', $dataCategory)
            ->with('dataCat', $dataCat)
            ->with('id', $id);
    }

    public function cloneGiaiThuong(Request $request,$id){

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $data = Group::with(array('customs' => function ($query) {
//            $query->where('shop_id', session('shop_id'));
        }))->where('module', '=', $this->module)->findOrFail($id);

        if(!$data){
            return redirect()->back()->withErrors(__('Không tồn tại minigame!'));
        }
        $id = $data->id;

        $d_shopid = $request->shop_clone;

        $items = Item::with(array('groups' => function ($query) {
            $query->where('module', 'minigame-type');
            $query->select('groups.id','title');
        }))
            ->with(array('children' => function ($query) use ($id,$d_shopid)  {
                $query->where('module', 'minigame-itemset');
                $query->whereHas('groups', function ($querysub) use ($id) {
                    $querysub->where('group_id',$id);
                });
                $query->with(array('children' => function ($query) use ($d_shopid) {
                    $query->where('module', 'minigame-itemset');
                    $query->where('shop_id',$d_shopid);
                }));
                $query->whereHas('children', function ($querysub) use ($d_shopid) {
                    $querysub->where('shop_id',$d_shopid);
                });
            }))
            ->whereHas('children', function ($query) use ($id,$d_shopid)  {
                $query->where('module', 'minigame-itemset');
                $query->whereHas('groups', function ($querysub) use ($id) {
                    $querysub->where('group_id',$id);
                });
                $query->whereHas('children', function ($querysub) use ($d_shopid) {
                    $querysub->where('shop_id',$d_shopid);
                });
            })->where('module', 'minigame')->where('status', 1)->get();


        $position = 0;

        if (isset($group->position)){
            $position = config('module.minigame.number_of_items.number_of_items_'.$group->position.'');
        }

        if (count($items) < $position){
            return redirect()->back()->withErrors(__('Không đủ giải thưởng để clone'));
        }

//        Xóa giải thưởng cũ.
        $old_shopid  = session('shop_id');
        $old_item = Item::with(array('groups' => function ($query) {
            $query->where('module', 'minigame-type');
            $query->select('groups.id','title');
        }))
            ->with(array('children' => function ($query) use ($id,$old_shopid)  {
                $query->where('module', 'minigame-itemset');
                $query->whereHas('groups', function ($querysub) use ($id) {
                    $querysub->where('group_id',$id);
                });
                $query->with(array('children' => function ($query) use ($old_shopid) {
                    $query->where('module', 'minigame-itemset');
                    $query->where('shop_id',$old_shopid);
                }));
                $query->whereHas('children', function ($querysub) use ($old_shopid) {
                    $querysub->where('shop_id',$old_shopid);
                });
            }))
            ->whereHas('children', function ($query) use ($id,$old_shopid)  {
                $query->where('module', 'minigame-itemset');
                $query->whereHas('groups', function ($querysub) use ($id) {
                    $querysub->where('group_id',$id);
                });
                $query->whereHas('children', function ($querysub) use ($old_shopid) {
                    $querysub->where('shop_id',$old_shopid);
                });
            })->where('module', 'minigame')->where('status', 1)->get();

        if (isset($old_item) && count($old_item)){
            foreach ($old_item as $old){
                if (isset($old->children[0])){
                    $children_old = $old->children[0];
                    if (isset($children_old->children[0])){
                        $children_cs = $children_old->children[0];

                        Item::where('id',$children_cs->id)->delete();
                    }

                }
            }
        }

//        Clone minigame.

        foreach ($items as $item){

            if (isset($item->children[0])){
                $children = $item->children[0];

                $children = Item::where('id',$children->id)->first();
//                return $id;
                    $item_new_chidl = $children->replicate()->fill(
                        [
                            'author_id' => auth()->user()->id,
                            'parent_id' => $item->id,
                            'module' => 'minigame-itemset',
                            'created_at' => Carbon::now(),
                        ]
                    );

                    $item_new_chidl->save();
                    $item_new_chidl->groups()->attach($id);

                    $item_new_chidl2 = $item_new_chidl->replicate()->fill(
                        [
                            'author_id' => auth()->user()->id,
                            'module' => 'minigame-itemset',
                            'shop_id' => session('shop_id'),
                            'parent_id' => $item_new_chidl->id,
                            'created_at' => Carbon::now(),
                        ]
                    );

                    $item_new_chidl2->save();
            }

        }


        return redirect()->back()->with('success',__('Clone thành công !'));

    }

    public function updateitem(Request $request,$id)
    {
        $arr_item_id = array();
        $arr_delete_item_id = array();

        try{
            $input= json_decode($request->data);
            if($request->type=='ctchgt'){

                foreach ($input as $key => $value) {
                    //bản ghi chung
                    $inputdata['shop_id'] = null;
                    $inputdata['module']='minigame-itemset';
                    $inputdata['author_id']=auth()->user()->id;
                    $inputdata['parent_id'] = $value->id;
                    $inputdata['title'] = $value->title;
                    $inputdata['image'] = $value->image;
                    if($value->iditemset==""){

                        $data=Item::create($inputdata);

                        array_push($arr_item_id,$value->id);
                        //set category
                        if( isset($id) &&  $id!=0){
                            $data->groups()->attach($id);
                        }
                        //custom cho shop hiện tại
                        $inputdata['shop_id'] = session('shop_id');
                        $inputdata['parent_id'] = $data->id;
                        $data_custom=Item::create($inputdata);

                    }else{
                        if($value->id == ""){

                            array_push($arr_delete_item_id,$value->iditemset);
                            Item::where('module','=','minigame-itemset')->where('id',$value->iditemset)->delete();
                            Item::where('module','=','minigame-itemset')->where('parent_id',$value->iditemset)->delete();
                            Group_Item::where('group_id',$id)->where('item_id',$value->iditemset)->delete();
                        }else{
                            $data=Item::create($inputdata);

                            array_push($arr_item_id,$value->id);
                            //set category
                            if( isset($id) &&  $id!=0){
                                $data->groups()->attach($id);
                            }
                            //custom cho shop hiện tại
                            $inputdata['shop_id'] = session('shop_id');
                            $inputdata['parent_id'] = $data->id;
                            $data_custom=Item::create($inputdata);
                        }
                    }
                }

                $get_giaithuong = Item::with(array('groups' => function ($query) {
                    $query->where('module', 'minigame-type');
                    $query->select('groups.id','title');
                }))->whereIn('id',$arr_item_id)
                    ->with(array('children' => function ($query) use ($id)  {
                        $query->where('module', 'minigame-itemset');
                        $query->whereHas('groups', function ($querysub) use ($id) {
                            $querysub->where('group_id',$id);
                        });
                    }))
                    ->where('module', 'minigame')->where('status', 1)->whereHas('children', function ($query) use ($id)  {
                        $query->where('module', 'minigame-itemset');
                        $query->whereHas('groups', function ($querysub) use ($id) {
                            $querysub->where('group_id',$id);
                        });
                    })->get();

                foreach ($get_giaithuong as $row){
                    $custom = Item::where('parent_id',$row->children[0]->id)->where('shop_id',session('shop_id'))->first();
                    $id = $row->id;
                    $title = $row->title;
                    $image = $row->image;

                    if($custom){
                        $id = $custom->id;
                    }

                    if($custom && $custom->title!=""){
                        $title=$custom->title;
                    }
                    if($custom && $custom->image!=""){
                        $image=$custom->image;
                    }

                    $row->id_custom = $id;
                    $row->title_custom = $title;
                    $row->image_custom = $image;
                }

                ActivityLog::add($request, 'Cập nhật thành công giải thưởng '.$this->module.' #'.json_encode($input));

                return response()->json([
                    'data'=>$get_giaithuong,
                    'arr_delete_item_id'=>$arr_delete_item_id,
                    'success'=>true,
                    'message'=>__('Cập nhật thành công cấu hình giải thưởng!'),
                    'redirect'=>''
                ]);
            }else{
                foreach ($input as $key => $value) {
                    $inputdata['module']='minigame-itemset';
                    $inputdata['author_id']=auth()->user()->id;
                    $inputdata['order'] = $value->order;
                    $inputdata['title'] = $value->title;
                    $inputdata['image'] = $value->image;
                    $inputdata['parent_id'] = $value->id;
                    $inputdata['params']=$value->params;
                    if($value->id != ""){
                        $data = Item::where('module', '=', 'minigame-itemset')->findOrFail($value->iditemset);
                        $data->update($inputdata);
                    }
                }
                ActivityLog::add($request, 'Cập nhật thành công giải thưởng '.$this->module.' #'.json_encode($input));

                return response()->json([
                    'data'=>$data,
                    'success'=>true,
                    'message'=>__('Cập nhật thành công cấu hình giải thưởng!'),
                    'redirect'=>''
                ]);
            }

        }catch(\Exception $e){
            logger($e);
            return response()->json([
                'success'=>false,
                'message'=>__('Lỗi khi cấu hình giải thưởng, vui lòng thử lại!'),
                'redirect'=>''
            ]);
        }
    }

    public function setcustom(Request $request)
    {
        try{
            $input= $request->data;
            $id = $input['id'];
            $inputdata['title'] = $input['title'];
            $inputdata['image'] = $input['image'];

            if (!isset($input['title'])){
                $inputdata['title'] = $input['title_minigame'];
            }
            if (!isset($input['image'])){
                $inputdata['image'] = $input['image_minigame'];
            }

            if($id!=''){
                $data = Item::where('module', '=', 'minigame-itemset')->where('shop_id', session('shop_id'))->where('id',$id)->first();
                $data->update($inputdata);
            }else{
                $inputdata['module'] = 'minigame-itemset';
                $inputdata['parent_id'] = $input['parent_id'];
                $inputdata['shop_id'] = session('shop_id');
                $data=Item::create($inputdata);
            }
            ActivityLog::add($request, 'Cập nhật thành công giải thưởng '.$this->module.' #'.json_encode($input));
            return response()->json([
                'success'=>true,
                'message'=>__('Cập nhật thành công cấu hình giải thưởng!'),
                'redirect'=>''
            ]);
        }catch(\Exception $e){
            logger($e);
            return response()->json([
                'success'=>false,
                'message'=>__('Lỗi khi cấu hình giải thưởng, vui lòng thử lại!'),
                'redirect'=>''
            ]);
        }
    }

    public function destroy(Request $request)
    {
        if(!auth()->user()->hasRole('admin')){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        $input = explode(',',$request->id);
        $flag = true;
        foreach ($input as $id){
            $data = Group::with(array('customs' => function ($query){
                $query->with('shop');
            }))->whereHas('customs', function ($querysub) use ($request){
                $querysub->with('shop');
            })
                ->where('module', '=', 'minigame-category')->where('id',$id)->first();

            if (!isset($data)){
                Group::where('module','=',$this->module)->where('id',$id)->delete();
            }else{
                $flag = false;
            }
        }

//        MinigameDistribute::whereIn('group_id',$input)->delete();
        ActivityLog::add($request, 'Xóa thành công '.$this->module.' #'.json_encode($input));

        if ($flag){
            return redirect()->back()->with('success',__('Xóa thành công !'));
        }else{
            return redirect()->back()->withErrors(__('Minigame vẫn còn phân phối trên điểm bán'));
        }

    }

    // AJAX Reordering function
    public function order(Request $request)
    {
        $source = e($request->get('source'));
        $destination = $request->get('destination');

        $item = Group::where('module', '=', $this->module)->find($source);
        //dd($item);
        $item->parent_id = isset($destination)?$destination:0;
        $item->save();

        $ordering = json_decode($request->get('order'));

        $rootOrdering = json_decode($request->get('rootOrder'));

        if ($ordering) {
            foreach ($ordering as $order => $item_id) {
                if ($itemToOrder = Group::where('module', '=', $this->module)->find($item_id)) {
                    $itemToOrder->order = $order;
                    $itemToOrder->save();
                }
            }
        } else {
            foreach ($rootOrdering as $order => $item_id) {
                if ($itemToOrder = Group::where('module', '=', $this->module)->find($item_id)) {
                    $itemToOrder->order = $order;
                    $itemToOrder->save();
                }
            }
        }
        ActivityLog::add($request, 'Thay đổi STT thành công '.$this->module.' #'.$item->id);
        return 'ok ';
    }

    // Getter for the HTML menu builder
    function getHTMLCategory($menu)
    {
        return $this->buildMenu($menu);
    }

    function buildMenu($menu, $parent_id = 0)
    {
        $result = null;
        foreach ($menu as $item){
            $href = "#";
            $title = $item->title;
            $shop = Shop::where('id', $item->shop_id)->first();
            if($shop){
                $href = ((isset($shop->domain))?('http://'.$shop->domain.'/minigame-'.$item->slug):'#');
                $title = '['.$shop->title.'] '.$item->title;
            }
            if ($item->parent_id == $parent_id) {
                $result .= "<li class='dd-item nested-list-item' data-order='{$item->order}' data-id='{$item->id}'>
              <div class='dd-handle nested-list-handle'>
                <span class='la la-arrows-alt'></span>
              </div>
              <div class='nested-list-content'>";
                if($parent_id!=0){
                    $result.="<div class=\"m-checkbox\">
                                    <label class=\"checkbox checkbox-outline\">
                                    <input  type=\"checkbox\" rel=\"{$item->id}\" class=\"children_of_{$item->parent_id}\">
                                      <span></span> ".HTML::entities($title)."
                                    </label>
                                </div>";
                }
                else{

                    $result.="<div class=\"m-checkbox\">
                                    <label class=\"checkbox checkbox-outline\">
                                    <input  type=\"checkbox\" rel=\"{$item->id}\" class=\"children_of_{$item->parent_id}\"  >
                                    <span></span> ".HTML::entities($title)."
                                    </label>
                                </div>";
                }
                $result .= "<div class='btnControll'>";

                $result .= "<a target='_blank' href='".$href."' class='btn btn-info btn-sm'>Xem</a>&nbsp;<a href='" . route("admin.".$this->module.".edit",$item->id) . "' class='btn btn-sm btn-primary'>Sửa</a>
                    <a href=\"#\" class=\"btn btn-sm btn-danger  delete_toggle \" rel=\"{$item->id}\">
                                        Xóa
                    </a>
                </div>
              </div>" . $this->buildMenu($menu, $item->id) . "</li>";
            }
        }
        return $result ? "\n<ol class=\"dd-list\">\n$result</ol>\n" : null;
    }

    public function update_fieldcat(Request $request)
    {
        if(!session('shop_id')){
            return response()->json([
                'success'=>false,
                'message'=>__('Vui lòng chọn shop !'),
                'redirect'=>''
            ]);
        }
        $id=$request->id;
        $field=$request->field;
        $value=$request->value;
        $required=$request->required;
        $whitelist=['acc_show_num','play_num_from','play_num_to','user_num_from','user_num_to','play_num_near','special_num_from','special_num_to','gift_num_exist'];

        if(!in_array($field,$whitelist)){
            return response()->json([
                'success'=>false,
                'message'=>__('Trường cập nhật không được chấp thuận'),
                'redirect'=>''
            ]);
        }
        if($required==1 && $value==""){
            return response()->json([
                'success'=>false,
                'message'=>__('Trường này không được bỏ trống!'),
                'redirect'=>''
            ]);
        }
        $data =  MinigameDistribute::where('shop_id', session('shop_id'))->where('group_id',$id)->firstOrFail();
        $old_value = "";
        if($field=='order'){
            $data->order = $value;
        }elseif($field=='title'){
            $data->title = $value;
        }else{
            $params=$data->params;
            foreach ($params as $aPram=>$key){
                if(str_contains($aPram, $field)){
                    $old_value = $params->$aPram;
                    $params->$aPram = $value;
                }
            }
            $data->params = $params;
        }
        $data->save();
        $name = '';
        if($field=='acc_show_num'){
            $name = __('Số user top');
        }elseif($field=='title'){
            $name = __('Tên custom');
        }elseif($field=='play_num_from'){
            $name = __('Số lượt chơi từ');
        }elseif($field=='play_num_to'){
            $name = __('Số lượt chơi đến');
        }elseif($field=='user_num_from'){
            $name = __('Người đang chơi từ');
        }elseif($field=='user_num_to'){
            $name = __('Người đang chơi đến');
        }elseif($field=='play_num_near'){
            $name = __('Lượt chơi gần đây');
        }elseif($field=='special_num_from'){
            $name = __('Trúng giải đặc biệt từ');
        }elseif($field=='special_num_to'){
            $name = __('Trúng giải đặc biệt đến');
        }elseif($field=='gift_num_exist'){
            $name = __('Giải thưởng còn lại');
        }
        ActivityLog::add($request, 'Cấu hình minigame-category: Cập nhật #'.$id.' mục '.$name.' từ ['.$old_value.'] -> ['.$value.']');

        return response()->json([
            'success'=>true,
            'message'=>__('Cập nhật thành công !'),
            'redirect'=>''
        ]);
    }

    public function update_field(Request $request)
    {
        $id=$request->id;
        $field=$request->field;
        $value=$request->value;
        $required=$request->required;
        $whitelist=['title','value','bonus_from','bonus_to','order','percent','try_percent','nohu_percent','limit'];

        if(!in_array($field,$whitelist)){
            return response()->json([
                'success'=>false,
                'message'=>__('Trường cập nhật không được chấp thuận'),
                'redirect'=>''
            ]);
        }
        if($required==1 && $value==""){
            return response()->json([
                'success'=>false,
                'message'=>__('Trường này không được bỏ trống!'),
                'redirect'=>''
            ]);
        }
        $data =  Item::where('module', '=', 'minigame-itemset')->findOrFail($id);
        $old_value = "";
        if($field=='order'){
            $data->order = $value;
        }elseif($field=='title'){
            $data->title = $value;
        }else{
            $params=$data->params;
            foreach ($params as $aPram=>$key){
                if(str_contains($aPram, $field)){
                    $old_value = $params->$aPram;
                    $params->$aPram = $value;
                }
            }
            $data->params = $params;
        }
        $data->save();
        $name = '';
        if($field=='value'){
            $name = __('Giá trị');
        }elseif($field=='title'){
            $name = __('Tên custom');
        }elseif($field=='bonus_from'){
            $name = __('Giá trị bonus từ');
        }elseif($field=='bonus_to'){
            $name = __('Giá trị bonus đến');
        }elseif($field=='order'){
            $name = __('Vị trí');
        }elseif($field=='percent'){
            $name = __('Phần trăm');
        }elseif($field=='try_percent'){
            $name = __('Phần trăm chơi thử');
        }elseif($field=='nohu_percent'){
            $name = __('Phần trăm nổ hũ');
        }elseif($field=='limit'){
            $name = __('Số lượng');
        }
        ActivityLog::add($request, 'Cấu hình giải thưởng: Cập nhật phần thưởng #'.$id.' mục '.$name.' từ ['.$old_value.'] -> ['.$value.']');

        return response()->json([
            'success'=>true,
            'message'=>__('Cập nhật thành công !'),
            'redirect'=>''
        ]);
    }

//    Nhân bản minigame.

    public function replication(Request $request){

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $id = $request->replicationid;
        $c_group = $request->c_group;
        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        if ($c_group == 0){

            $group = Group::with(array('customs' => function ($query) {

            }))->where('module', '=', $this->module)->findOrFail($id);

            if (session('shop_id')) {

                $group = Group::with(array('customs' => function ($query) use ($arr_permission) {
                    $query->whereIn('shop_id', $arr_permission);
                }))
                    ->where('module', '=', $this->module)
                    ->whereHas('customs', function ($queryuse) use ($arr_permission) {
                        $queryuse->whereIn('shop_id', $arr_permission);
                    })->where('id', $id)
                    ->first();

                if (!$group) {
                    return redirect()->back()->with('error', "Shop không có quyền truy cập !");
                }
            }

            $group_id = $group->id;

//        Giải thưởng gốc.

            $items= Item::with(array('groups' => function ($query) {
                $query->where('module', 'minigame-type');
                $query->select('groups.id','title');
            }))
                ->with(array('children' => function ($query) use ($group_id)  {
                    $query->where('module', 'minigame-itemset');
                    $query->whereHas('groups', function ($querysub) use ($group_id) {
                        $querysub->where('group_id',$group_id);
                    });
                }))
                ->where('module', 'minigame')->where('status', 1)->whereHas('children', function ($query) use ($group_id)  {
                    $query->where('module', 'minigame-itemset');
                    $query->whereHas('groups', function ($querysub) use ($group_id) {
                        $querysub->where('group_id',$group_id);
                    });
                })->get();

//            Bộ thông tin cần lấy.

            if (session('shop_id')) {
                $group_shop = MinigameDistribute::where('shop_id',session('shop_id'))
                    ->where('group_id',$group_id)
                    ->with(array('shop' => function ($query) use($arr_permission){
                        $query->whereIn('id', $arr_permission);
                    }))->whereHas('shop', function($queryuse) use ($arr_permission){
                        $queryuse->whereIn('id',$arr_permission);
                    })->first();

                $shopid = $group_shop->shop_id;

                $items= Item::with(array('groups' => function ($query) {
                    $query->where('module', 'minigame-type');
                    $query->select('groups.id','title');
                }))
                    ->with(array('children' => function ($query) use ($group_id,$shopid)  {
                        $query->where('module', 'minigame-itemset');
                        $query->whereHas('groups', function ($querysub) use ($group_id) {
                            $querysub->where('group_id',$group_id);
                        });
                        $query->with(array('children' => function ($query) use ($shopid) {
                            $query->where('module', 'minigame-itemset');
                            $query->where('shop_id',$shopid);
                        }));
                        $query->whereHas('children', function ($querysub) use ($shopid) {
                            $querysub->where('shop_id',$shopid);
                        });
                    }))
                    ->where('module', 'minigame')->where('status', 1)->whereHas('children', function ($query) use ($group_id)  {
                        $query->where('module', 'minigame-itemset');
                        $query->whereHas('groups', function ($querysub) use ($group_id) {
                            $querysub->where('group_id',$group_id);
                        });
                    })->get();

            }

//Nhân bản group.

            if ($group->duplicate){
                $duplicate = (int)$group->duplicate+1;
            }else{
                $duplicate = 1;
            }

            $group->duplicate = $duplicate;

            $group_new = $group->replicate()->fill(
                [
                    'module' => 'minigame-category',
                    'duplicate' => 0,
                    'parent_id' => $group_id,
                    'title' => "Bản sao ($duplicate) của  ".$group->title,
                    'slug' => $group->slug." (".($duplicate) .")",
                    'author_id' => auth()->user()->id,
                    'created_at' => Carbon::now(),
                ]
            );

            $group_new->save();
            $group->save();

            //     Nhân bản giải thưởng.

            foreach ($items as $value){

                if (session('shop_id')) {
                    $children =  $value->children;
                    $item = Item::where('id',$children[0]->id)->with(array('children' => function ($query) use ($shopid) {
                        $query->where('module', 'minigame-itemset');
                        $query->where('shop_id',$shopid);
                    }))->whereHas('children', function ($querysub) use ($shopid) {
                        $querysub->where('shop_id',$shopid);
                    })->where('module','minigame-itemset')->first();

                    $child = $item->children;
                    $item_children = Item::where('id',$child[0]->id)->where('module','minigame-itemset')->first();

                    $item_new = $item->replicate()->fill(
                        [
                            'author_id' => auth()->user()->id,
                            'created_at' => Carbon::now(),
                        ]
                    );

                    $item_new->save();

                    $item_new->groups()->attach($group_new->id);

                    //            Tạo bộ thông tin item custom.
                    foreach ($group->customs as $custom){
                        $item_new_chidl = $item_children->replicate()->fill(
                            [
                                'author_id' => auth()->user()->id,
                                'parent_id' => $item_new->id,
                                'shop_id' => $custom->shop_id,
                                'created_at' => Carbon::now(),
                            ]
                        );

                        $item_new_chidl->save();
                    }
                }else{
                    $children =  $value->children;
                    $item = Item::where('id',$children[0]->id)->where('module','minigame-itemset')->first();

                    $item_new = $item->replicate()->fill(
                        [
                            'author_id' => auth()->user()->id,
                            'created_at' => Carbon::now(),
                        ]
                    );

                    $item_new->save();

                    $item_new->groups()->attach($group_new->id);

                }

            }

//            Phân phối bộ thông tin
            if (session('shop_id')) {

                foreach ($group->customs as $custom){

                    $group_shop_new = $group_shop->replicate()->fill(
                        [
                            'status' => 0,
                            'group_id' => $group_new->id,
                            'shop_id'  => $custom->shop_id,
                            'title' => "Bản sao ($duplicate) của ".$group_shop->title,
                            'slug' => $group->slug." (".($duplicate) .")",
                            'created_at' => Carbon::now(),
                        ]
                    );

                    $group_shop_new->save();
                }
            }

            ActivityLog::add($request, 'Nhân bản thành công minigame #'.$group->id.' thành minigame #'.$group_new->id);

            return redirect()->back()->with('success',__('Nhân bản thành công !'));

        }elseif ($c_group == 1){
//        Danh mục gốc

            $validator = Validator::make($request->all(),[
                'cr_shop' => 'required',
            ],[
                'cr_shop.required' => "Vui lòng chọn bộ thông tin nhân bản",
            ]);

            if($validator->fails()){

                return redirect()->back()->withErrors(__('Vui lòng chọn bộ thông tin nhân bản !'));

            }

            $cr_shop = $request->cr_shop;

            $group = Group::with(array('customs' => function ($query) use($arr_permission){
                $query->whereIn('shop_id', $arr_permission);
            }))
                ->where('module', '=', $this->module)
                ->whereHas('customs', function($queryuse) use ($arr_permission){
                    $queryuse->whereIn('shop_id',$arr_permission);
                })->where('id',$id)
                ->first();

            if (!$group){
                return redirect()->back()->with('error',"Shop không có quyền truy cập !");
            }

            $group_id = $group->id;

            //            Bộ thông tin cần nhân bản.

            $group_shop = MinigameDistribute::where('id',$cr_shop)
                ->with(array('shop' => function ($query) use($arr_permission){
                    $query->whereIn('id', $arr_permission);
                }))->whereHas('shop', function($queryuse) use ($arr_permission){
                    $queryuse->whereIn('id',$arr_permission);
                })->first();

            $shopid = $group_shop->shop_id;
//        Giải thưởng gốc.

            $items = Item::with(array('groups' => function ($query) {
                $query->where('module', 'minigame-type');
                $query->select('groups.id','title');
            }))
                ->with(array('children' => function ($query) use ($group_id,$shopid)  {
                    $query->where('module', 'minigame-itemset');
                    $query->whereHas('groups', function ($querysub) use ($group_id) {
                        $querysub->where('group_id',$group_id);
                    });
                    $query->with(array('children' => function ($query) use ($shopid) {
                        $query->where('module', 'minigame-itemset');
                        $query->where('shop_id',$shopid);
                    }));
                    $query->whereHas('children', function ($querysub) use ($shopid) {
                        $querysub->where('shop_id',$shopid);
                    });
                }))
                ->where('module', 'minigame')->where('status', 1)->whereHas('children', function ($query) use ($group_id)  {
                    $query->where('module', 'minigame-itemset');
                    $query->whereHas('groups', function ($querysub) use ($group_id) {
                        $querysub->where('group_id',$group_id);
                    });
                })->get();


//Nhân bản group.

            if ($group->duplicate){
                $duplicate = (int)$group->duplicate+1;
            }else{
                $duplicate = 1;
            }

            $group->duplicate = $duplicate;

            $group_new = $group->replicate()->fill(
                [
                    'module' => 'minigame-category',
                    'duplicate' => 0,
                    'parent_id' => $group_id,
                    'title' => "Bản sao ($duplicate) của  ".$group->title,
                    'slug' => $group->slug." (".($duplicate) .")",
                    'author_id' => auth()->user()->id,
                    'created_at' => Carbon::now(),
                ]
            );

            $group_new->save();
            $group->save();

//     Nhân bản giải thưởng.

            foreach ($items as $item){

                if (isset($item->children)){
                    foreach ($item->children as $chikey => $children){
                        if ($chikey == 0) {
                            $item = Item::where('id', $children->id)->with(array('children' => function ($query) use ($shopid) {
                                $query->where('module', 'minigame-itemset');
                                $query->where('shop_id', $shopid);
                            }))->whereHas('children', function ($querysub) use ($shopid) {
                                $querysub->where('shop_id', $shopid);
                            })->where('module', 'minigame-itemset')->first();

                            if (isset($item->children)) {
                                foreach ($item->children as $chikey2 => $child) {
                                    if ($chikey2 == 0) {
                                        $item_children = Item::where('id', $child->id)->where('module', 'minigame-itemset')->first();

                                        $item_new = $item->replicate()->fill(
                                            [
                                                'author_id' => auth()->user()->id,
                                                'created_at' => Carbon::now(),
                                            ]
                                        );

                                        $item_new->save();

                                        $item_new->groups()->attach($group_new->id);

                                        //            Tạo bộ thông tin item custom.
                                        foreach ($group->customs as $custom) {
                                            $item_new_chidl = $item_children->replicate()->fill(
                                                [
                                                    'author_id' => auth()->user()->id,
                                                    'parent_id' => $item_new->id,
                                                    'shop_id' => $custom->shop_id,
                                                    'created_at' => Carbon::now(),
                                                ]
                                            );

                                            $item_new_chidl->save();
                                        }
                                    }
                                }
                            }

                        }
                    }
                }

            }


//            Phân phối.

            foreach ($group->customs as $custom){

                $group_shop_new = $group_shop->replicate()->fill(
                    [
                        'status' => 0,
                        'group_id' => $group_new->id,
                        'shop_id'  => $custom->shop_id,
                        'title' => "Bản sao ($duplicate) của ".$group_shop->title,
                        'slug' => $group->slug." (".($duplicate) .")",
                        'created_at' => Carbon::now(),
                    ]
                );

                $group_shop_new->save();
            }

            ActivityLog::add($request, 'Nhân bản thành công minigame #'.$group->id.' thành minigame #'.$group_new->id);

            return redirect()->back()->with('success',__('Nhân bản thành công !'));

        }
    }

    public function distribution(Request $request,$id){

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $addshop = $request->addshop;

        if(Auth::user()->account_type == 1){
            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();
        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        if (session('shop_id')){

            $d_shopid = session('shop_id');

            if ($addshop == 0){

                $arr_shop_id = $request->object_shop;

                $arr_shop_id = explode('|', $arr_shop_id);

//            Danh sách các web cần phân phối.

                $data_shop = Shop::with('group')->where('status',1)->whereIn('id',$arr_permission)->whereIn('id',$arr_shop_id)->get();

//            Group gốc phân phối.

                $group =  Group::with(array('customs' => function ($query) {
                    $query->where('shop_id', session('shop_id'));
                }))->where('module', '=', 'minigame-category')->findOrFail($id);


//                Giải thưởng gốc

                $d_group_id = $group->id;

                $d_shopid = session('shop_id');

                $items = Item::with(array('groups' => function ($query) {
                    $query->where('module', 'minigame-type');
                    $query->select('groups.id','title');
                }))
                    ->with(array('children' => function ($query) use ($id,$d_shopid)  {
                        $query->where('module', 'minigame-itemset');
                        $query->whereHas('groups', function ($querysub) use ($id) {
                            $querysub->where('group_id',$id);
                        });
                        $query->with(array('children' => function ($query) use ($d_shopid) {
                            $query->where('module', 'minigame-itemset');
                            $query->where('shop_id',$d_shopid);
                        }));
                        $query->whereHas('children', function ($querysub) use ($d_shopid) {
                            $querysub->where('shop_id',$d_shopid);
                        });
                    }))
                    ->whereHas('children', function ($query) use ($id,$d_shopid)  {
                        $query->where('module', 'minigame-itemset');
                        $query->whereHas('groups', function ($querysub) use ($id) {
                            $querysub->where('group_id',$id);
                        });
                        $query->whereHas('children', function ($querysub) use ($d_shopid) {
                            $querysub->where('shop_id',$d_shopid);
                        });
                    })->where('module', 'minigame')->where('status', 1)->get();

                $position = 0;

                if (isset($group->position)){
                    $position = config('module.minigame.number_of_items.number_of_items_'.$group->position.'');
                }

                if (count($items) < $position){
                    return redirect()->back()->withErrors(__('Không đủ giải thưởng để phân phối'));
                }

                //     Nhân bản giải thưởng custom.

                foreach ($items as $value){
                    $children =  $value->children;
                    $id_children = null;
                    if (isset($value->children) && count($value->children)){
                        foreach ($value->children as $key_children => $item_children){
                            if ($key_children == 0){
                                $id_children = $item_children->id;
                            }
                        }
                    }

                    $item = Item::where('id',$id_children)->with(array('children' => function ($query) use ($d_shopid) {
                        $query->where('module', 'minigame-itemset');
                        $query->where('shop_id',$d_shopid);
                    }))->whereHas('children', function ($querysub) use ($d_shopid) {
                        $querysub->where('shop_id',$d_shopid);
                    })->where('module','minigame-itemset')->first();


                    $item_children = null;
                    if (isset($item->children) && count($item->children)){
                        foreach ($item->children as $key_item_children => $item_item_children){
                            if ($key_item_children == 0){
                                $item_children = $item_item_children->id;
                            }
                        }
                    }

                    $item_children = Item::where('id',$item_children)->where('module','minigame-itemset')->first();

                    //            Tạo bộ thông tin item custom.
                    foreach ($data_shop as $shop){
                        $item_new_chidl = $item_children->replicate()->fill(
                            [
                                'author_id' => auth()->user()->id,
                                'shop_id' => $shop->id,
                                'created_at' => Carbon::now(),
                            ]
                        );

                        $item_new_chidl->save();
                    }
                }

                $data_custom = $group->customs[0];

//Group shop gốc.
                $groupshop = MinigameDistribute::where('id',$group->customs[0]->id)->first();

                foreach ($data_shop as $shop){

                    $checkgroup = MinigameDistribute::where('group_id',$group->id)->where('shop_id',$shop->id)->first();
                    if (!$checkgroup){
                        $groupshop_new = $groupshop->replicate()->fill(
                            [
                                'created_at' => Carbon::now(),
                                'shop_id'  => $shop->id,
                                'status'  => 0,
                            ]
                        );
                        $groupshop_new->save();
                    }

                }

                $utm_source = 'delete';
                $minutes = 1;
                Cookie::queue('phanphoi',$utm_source,$minutes);

                ActivityLog::add($request, 'Phân phối thành công minigame #'.$groupshop->id.' phân phối trên các shop '.$this->module.' #'.json_encode($arr_shop_id));

                // lấy thông tin IP và user_angent người dùng
                $ip = $request->getClientIp();
                $user_agent = $request->userAgent();
                $message = "Thời gian: <b>" . Carbon::now()->format('d-m-Y H:i:s') . "</b>";
                foreach ($data_shop as $shop){
                    $message .= "\n";
                    $message .= "Tài khoản qtv <b>" . Auth::user()->username . "</b> Phân phối thành công minigame " . $groupshop->id . " - <b> " . $groupshop->title . "</b> trên điểm bán <b>" . $shop->domain . "</b>";
                }
                $message .= "\n";
                $message .= "IP: <b>" . $ip . "</b>";
                $message .= "\n";
                $message .= "User_agent: <b>" . $user_agent . "</b>";
                Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_noty_minigame'));

                return redirect()->back()->with('success',__('Phân phối thành công !'));

            }elseif ($addshop == 1){

                $arr_group_shop_id = $request->object_shop;
                $arr_group_shop_id = explode('|', $arr_group_shop_id);
//Nhóm shop gốc.
                $shop_group = Shop_Group::with(['shop' => function ($query) use ($arr_permission){
                    $query->whereIn('id',$arr_permission);
                }])->whereHas('shop', function($query) use ($arr_permission){
                    $query->whereIn('id',$arr_permission);
                })->where('status',1)->whereIn('id',$arr_group_shop_id)->get();

                //            Group gốc phân phối.

                $group =  Group::with(array('customs' => function ($query) {
                    $query->where('shop_id', session('shop_id'));
                }))->where('module', '=', 'minigame-category')->findOrFail($id);

                //Group shop gốc.
                $groupshop = MinigameDistribute::where('id',$group->customs[0]->id)->first();

                //Giải thưởng gốc

                $d_group_id = $group->id;

                $items= Item::with(array('groups' => function ($query) {
                    $query->where('module', 'minigame-type');
                    $query->select('groups.id','title');
                }))
                    ->with(array('children' => function ($query) use ($d_group_id,$d_shopid)  {
                        $query->where('module', 'minigame-itemset');
                        $query->whereHas('groups', function ($querysub) use ($d_group_id) {
                            $querysub->where('group_id',$d_group_id);
                        });
                        $query->with(array('children' => function ($query) use ($d_shopid) {
                            $query->where('module', 'minigame-itemset');
                            $query->where('shop_id',$d_shopid);
                        }));
                        $query->whereHas('children', function ($querysub) use ($d_shopid) {
                            $querysub->where('shop_id',$d_shopid);
                        });
                    }))
                    ->where('module', 'minigame')->where('status', 1)->whereHas('children', function ($query) use ($d_group_id)  {
                        $query->where('module', 'minigame-itemset');
                        $query->whereHas('groups', function ($querysub) use ($d_group_id) {
                            $querysub->where('group_id',$d_group_id);
                        });
                    })->get();

                //     Nhân bản giải thưởng custom.

                foreach ($items as $value){
                    $children =  $value->children;

                    $item = Item::where('id',$children[0]->id)->with(array('children' => function ($query) use ($d_shopid) {
                        $query->where('module', 'minigame-itemset');
                        $query->where('shop_id',$d_shopid);
                    }))->whereHas('children', function ($querysub) use ($d_shopid) {
                        $querysub->where('shop_id',$d_shopid);
                    })->where('module','minigame-itemset')->first();

                    $id_child = $item->children;

                    $item_children = Item::where('id',$id_child[0]->id)->where('module','minigame-itemset')->first();

                    //            Tạo bộ thông tin item custom.

                    foreach ($shop_group as $g_shop) {
                        if (isset($g_shop->shop) && $g_shop->shop->count() > 0) {

                            foreach ($g_shop->shop as $shop) {
                                $item_new_chidl = $item_children->replicate()->fill(
                                    [
                                        'author_id' => auth()->user()->id,
                                        'shop_id' => $shop->id,
                                        'created_at' => Carbon::now(),
                                    ]
                                );

                                $item_new_chidl->save();
                            }
                        }
                    }

                }

                foreach ($shop_group as $g_shop) {
                    if (isset($g_shop->shop) && $g_shop->shop->count() > 0) {

                        foreach ($g_shop->shop as $shop) {

                            $checkgroup = MinigameDistribute::where('group_id',$group->id)->where('shop_id',$shop->id)->first();
                            if (!$checkgroup){
                                $checkshopgroup = MinigameDistribute::where('shop_id',$shop->id)->where('group_id',$group->customs[0]->group_id)->first();

                                if (!$checkshopgroup){
                                    $groupshop_new = $groupshop->replicate()->fill(
                                        [
                                            'created_at' => Carbon::now(),
                                            'shop_id'  => $shop->id,
                                            'status'  => 0,
                                        ]
                                    );

                                    $groupshop_new->save();
                                }
                            }

                        }
                    }
                }

                $utm_source = 'delete';
                $minutes = 1;
                Cookie::queue('phanphoi',$utm_source,$minutes);

                ActivityLog::add($request, 'Phân phối thành công minigame #'.$groupshop->id.' phân phối trên các shop của nhóm shop'.$this->module.' #'.json_encode($arr_group_shop_id));

                return redirect()->back()->with('success',__('Thêm mới thành công !'));

            }
        }else{
            if ($addshop == 0){

                $arr_shop_id = $request->object_shop;

                $arr_shop_id = explode('|', $arr_shop_id);

//            Danh sách các web cần phân phối.

                $data_shop = Shop::with('group')->where('status',1)->whereIn('id',$arr_shop_id)->get();

//            Group gốc phân phối.

                $group =  Group::where('module', '=', 'minigame-category')->findOrFail($id);

                $items= Item::with(array('groups' => function ($query) {
                    $query->where('module', 'minigame-type');
                    $query->select('groups.id','title');
                }))
                    ->with(array('children' => function ($query) use ($id)  {
                        $query->where('module', 'minigame-itemset');
                        $query->whereHas('groups', function ($querysub) use ($id) {
                            $querysub->where('group_id',$id);
                        });
                    }))
                    ->where('module', 'minigame')->where('status', 1)->whereHas('children', function ($query) use ($id)  {
                        $query->where('module', 'minigame-itemset');
                        $query->whereHas('groups', function ($querysub) use ($id) {
                            $querysub->where('group_id',$id);
                        });
                    })->get();



                foreach ($items as $value){

                    if (isset($value->children) && count($value->children) > 0){
                        $children =  $value->children;
                        $item = Item::where('id',$children[0]->id)->where('module','minigame-itemset')->first();

                        //            Tạo bộ thông tin item custom.
                        if (isset($item)){
                            foreach ($data_shop as $shop){

                                $item_new_chidl = $item->replicate()->fill(
                                    [
                                        'author_id' => auth()->user()->id,
                                        'shop_id' => $shop->id,
                                        'parent_id' => $item->id,
                                        'created_at' => Carbon::now(),
                                    ]
                                );

                                $item_new_chidl->save();
                            }
                        }
                    }

                }


                //Them moi cho shop dau tien duoc chon
                $input_custom['group_id'] = $group->id;
                $input_custom['shop_id'] = $data_shop[0]->id;
                $input_custom['title'] = $group->title;
                $input_custom['description'] = $group->description;
                $input_custom['slug'] = $group->slug;
                $input_custom['seo_title'] = $group->seo_title;
                $input_custom['seo_description'] = $group->seo_description;
                $input_custom['content'] = $group->content;
                $input_custom['image'] = $group->image;
                $input_custom['image_banner'] = $group->image_banner;
                $input_custom['image_icon'] = $group->image_icon;
                $input_custom['status'] = 0; //inactive
                $input_custom['params'] =$group->params;

                $groupshop =MinigameDistribute::create($input_custom);

                foreach ($data_shop as $keys => $shop){
                    if ($keys == 0){
                        //                Chuyen sang shop dau tien
                        session()->put('shop_id', $shop->id);
                        session()->put('shop_name', $shop->domain);
                    }
                    if($shop->id != $groupshop->shop_id){
                        //không tạo lại bạn ghi đầu
                        $checkgroup = MinigameDistribute::where('group_id',$group->id)->where('shop_id',$shop->id)->first();
                        if (!$checkgroup){
                            $groupshop_new = $groupshop->replicate()->fill(
                                [
                                    'created_at' => Carbon::now(),
                                    'shop_id'  => $shop->id,
                                    'status'  => 0,
                                ]
                            );
                            $groupshop->status = 0;
                            $groupshop->save();
                            $groupshop_new->save();
                        }
                    }
                }

                $utm_source = 'delete';
                $minutes = 1;
                Cookie::queue('phanphoi',$utm_source,$minutes);

                ActivityLog::add($request, 'Phân phối thành công minigame #'.$groupshop->id.' phân phối trên các shop '.$this->module.' #'.json_encode($arr_shop_id));

                return redirect()->back()->with('success',__('Thêm mới thành công !'));
//                return response()->json([
//                    'data'=>$arr_shop_id,
//                    'success'=>true,
//                    'message'=>__('Thêm mới thành công !'),
//                    'redirect'=>''
//                ]);


            }elseif ($addshop == 1){

                $arr_group_shop_id = $request->object_shop;
                $arr_group_shop_id = explode('|', $arr_group_shop_id);
//Nhóm shop gốc.
                $shop_group = Shop_Group::with('shop')->where('status',1)->whereIn('id',$arr_group_shop_id)->get();

                //            Group gốc phân phối.

                $group =  Group::where('module', '=', 'minigame-category')->findOrFail($id);

                $items= Item::with(array('groups' => function ($query) {
                    $query->where('module', 'minigame-type');
                    $query->select('groups.id','title');
                }))
                    ->with(array('children' => function ($query) use ($id)  {
                        $query->where('module', 'minigame-itemset');
                        $query->whereHas('groups', function ($querysub) use ($id) {
                            $querysub->where('group_id',$id);
                        });
                    }))
                    ->where('module', 'minigame')->where('status', 1)->whereHas('children', function ($query) use ($id)  {
                        $query->where('module', 'minigame-itemset');
                        $query->whereHas('groups', function ($querysub) use ($id) {
                            $querysub->where('group_id',$id);
                        });
                    })->get();

//Thêm nhóm cấu hình giải thưởng

                foreach ($items as $value){

                    if (isset($value->children) && count($value->children) > 0){
                        $children =  $value->children;
                        $item = Item::where('id',$children[0]->id)->where('module','minigame-itemset')->first();

                        //            Tạo bộ thông tin item custom.
                        if (isset($item)){
                            foreach ($shop_group as $key => $g_shop) {

                                if (isset($g_shop->shop) && $g_shop->shop->count() > 0) {

                                    foreach ($g_shop->shop as $key => $shop) {
                                        $item_new_chidl = $item->replicate()->fill(
                                            [
                                                'author_id' => auth()->user()->id,
                                                'shop_id' => $shop->id,
                                                'parent_id' => $item->id,
                                                'created_at' => Carbon::now(),
                                            ]
                                        );

                                        $item_new_chidl->save();
                                    }
                                }
                            }
                        }

                    }

                }

                foreach ($shop_group as $key => $g_shop) {

                    if (isset($g_shop->shop) && $g_shop->shop->count() > 0) {

                        foreach ($g_shop->shop as $key => $shop) {
                            $checkgroup = MinigameDistribute::where('group_id',$group->id)->where('shop_id',$shop->id)->first();
                            if (!$checkgroup){
                                $input_custom['group_id'] = $group->id;
                                $input_custom['shop_id'] = $shop->id;
                                $input_custom['title'] = $group->title;
                                $input_custom['description'] = $group->description;
                                $input_custom['slug'] = $group->slug;
                                $input_custom['seo_title'] = $group->seo_title;
                                $input_custom['seo_description'] = $group->seo_description;
                                $input_custom['content'] = $group->content;
                                $input_custom['image'] = $group->image;
                                $input_custom['image_banner'] = $group->image_banner;
                                $input_custom['image_icon'] = $group->image_icon;
                                $input_custom['status'] = 0; //inactive
                                $input_custom['params'] =$group->params;

                                $groupshop = MinigameDistribute::create($input_custom);

                                $groupshop->status = 0;
                                $groupshop->save();
                            }
                        }
                    }

                    if ($key == 0){
                        //                Chuyen sang shop dau tien
                        if (isset($g_shop->shop) && $g_shop->shop->count() > 0) {
                            $datashop = Shop::where('id',$g_shop->shop[0]->id)->first();

                            session()->put('shop_id', $datashop->id);
                            session()->put('shop_name', $datashop->domain);
                        }
                    }
                }

                $utm_source = 'delete';
                $minutes = 1;
                Cookie::queue('phanphoi',$utm_source,$minutes);

                ActivityLog::add($request, 'Phân phối thành công minigame #'.$group->id.' phân phối trên các shop của nhóm shop'.$this->module.' #'.json_encode($arr_group_shop_id));

                return redirect()->back()->with('success',__('Thêm mới thành công !'));

            }
        }

    }

    public function deletegroupshop(Request $request)
    {

        $groupid=$request->groupid;
        $shopid = explode('|',$request->inactiveid);

        if(Auth::user()->account_type == 1){

            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();

            if (isset($shopid)){
                foreach ($shopid as $val){
                    if (in_array((int)$val,$arr_permission)){}else{
                        return redirect()->back()->with('error',"Shop không có quyền truy cập !");
                    }
                }
            }

        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        $group =  Group::with(array('customs' => function ($query) {
        }))->where('module', '=', 'minigame-category')->findOrFail($groupid);


        $isShop = false;

        if (session("shop_id")){
            foreach ($shopid as $s_id){
                if ((int)$s_id == (int)session("shop_id")){
                    $isShop = true;
                }
            }
        }

        foreach ($shopid as $shop_id){
            $d_shopid = $shop_id;
            $id = $group->id;
            $data_arr = Item::with(array('groups' => function ($query) {
                $query->where('module', 'minigame-type');
                $query->select('groups.id','title');
            }))
                ->with(array('children' => function ($query) use ($id,$d_shopid)  {
                    $query->where('module', 'minigame-itemset');
                    $query->whereHas('groups', function ($querysub) use ($id) {
                        $querysub->where('group_id',$id);
                    });
                    $query->with(array('children' => function ($query) use ($d_shopid) {
                        $query->where('module', 'minigame-itemset');
                        $query->where('shop_id',$d_shopid);
                    }));
                    $query->whereHas('children', function ($querysub) use ($d_shopid) {
                        $querysub->where('shop_id',$d_shopid);
                    });
                }))
                ->whereHas('children', function ($query) use ($id,$d_shopid)  {
                    $query->where('module', 'minigame-itemset');
                    $query->whereHas('groups', function ($querysub) use ($id) {
                        $querysub->where('group_id',$id);
                    });
                    $query->whereHas('children', function ($querysub) use ($d_shopid) {
                        $querysub->where('shop_id',$d_shopid);
                    });
                })->where('module', 'minigame')->where('status', 1)->get();

            if (isset($data_arr) && count($data_arr)){
                foreach ($data_arr as $data_ar){
                    if (isset($data_ar->children[0])){
                        $children = $data_ar->children[0];
                        if (isset($children->children[0])){
                            $children_cs = $children->children[0];

                            Item::where('id',$children_cs->id)->delete();
                        }

                    }
                }
            }


        }

        MinigameDistribute::where('group_id',$groupid)->whereIn('shop_id',$shopid)->delete();

        $group =  Group::with(array('customs' => function ($query) {
        }))->where('module', '=', 'minigame-category')->findOrFail($groupid);

        $isSet = false;
        if (isset($group->customs) && count($group->customs)){
            $isSet = true;
        }

        $shops = Shop::whereIn('id',$shopid)->get();

        if ($isSet){
            $utm_source = 'delete';
            $minutes = 1;
            Cookie::queue('phanphoi',$utm_source,$minutes);

            if ($isShop){
                foreach ($group->customs as $key => $customs){
                    if ($key == 0){
                        $shop =  Shop::where('id',$customs->shop_id)->first();
                        session()->put('shop_id', $shop->id);
                        session()->put('shop_name', $shop->domain);
                    }
                }
            }

            ActivityLog::add($request, 'Gỡ phân phối thành công minigame : '.$groupid.' - '.$group->title.' phân phối trên các điểm bán :'.json_encode($shopid));

            if (isset($shops) && count($shops)){
                // lấy thông tin IP và user_angent người dùng
                $ip = $request->getClientIp();
                $user_agent = $request->userAgent();
                $message = "Thời gian: <b>".Carbon::now()->format('d-m-Y H:i:s')."</b>";
                foreach ($shops as $shop){
                    $message .= "\n";
                    $message .= "Tài khoản qtv <b>".Auth::user()->username."</b> gỡ phân phối thành công minigame ".$groupid. " - <b>".$group->title."</b> khỏi điểm bán <b>".$shop->domain."</b>";
                }
                $message .= "\n";
                $message .= "IP: <b>".$ip."</b>";
                $message .= "\n";
                $message .= "User_agent: <b>".$user_agent."</b>";
                Helpers::TelegramNotify($message,config('telegram.bots.mybot.channel_noty_minigame'));
            }

            return redirect()->back()->with('success',__('Xóa điểm bán thành công!'));
        }else{

            ActivityLog::add($request, 'Gỡ phân phối thành công minigame : '.$groupid.' - '.$group->title.' phân phối trên các điểm bán #'.json_encode($shopid));

            session()->forget('shop_id');
            session()->forget('shop_name');
            if (isset($shops) && count($shops)) {
                // lấy thông tin IP và user_angent người dùng
                $ip = $request->getClientIp();
                $user_agent = $request->userAgent();
                $message = "Thời gian: <b>" . Carbon::now()->format('d-m-Y H:i:s') . "</b>";
                foreach ($shops as $shop){
                    $message .= "\n";
                    $message .= "Tài khoản qtv <b>" . Auth::user()->username . "</b> gỡ phân phối thành công minigame " . $groupid . " - <b>" . $group->title . "</b> khỏi điểm bán <b>" . $shop->domain . "</b>";
                }
                $message .= "\n";
                $message .= "IP: <b>" . $ip . "</b>";
                $message .= "\n";
                $message .= "User_agent: <b>" . $user_agent . "</b>";
                Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_noty_minigame'));
            }
            return redirect()->route('admin.'.$this->module.'.index')->with('success',__('Xóa điểm bán thành công !'));
        }
    }

    public function activegroupshop(Request $request)
    {
        $groupid=$request->groupid;

        $shopid=explode('|',$request->activeid);

        if(Auth::user()->account_type == 1){

            $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();

            if (isset($shopid)){
                foreach ($shopid as $val){
                    if (in_array((int)$val,$arr_permission)){}else{
                        return redirect()->back()->with('error',"Shop không có quyền truy cập !");
                    }
                }
            }

        }

        if (!isset($arr_permission)){
            return redirect()->back()->withErrors(__('Không có quyền truy cập'));
        }

        $datas = MinigameDistribute::where('group_id',$groupid)->whereIn('shop_id',$shopid)->with('shop')->get();

        foreach ($datas as $data) {
            $data->status = 1;
            $data->save();
        }

        ActivityLog::add($request, 'Active thành công minigame #'.$groupid.' phân phối trên các shop'.$this->module.' #'.json_encode($shopid));
        return redirect()->back()->with('success',__('Minigame đã được cập nhật thông tin thành công !'));
    }

    public function activeshop(Request $request)
    {

        try{

            if(Auth::user()->account_type == 1){

                $arr_permission = HelperPermisionShopMinigame::VeryShopMinigame();

                if (isset($shopid)){
                    foreach ($shopid as $val){
                        if (in_array((int)$val,$arr_permission)){}else{
                            return redirect()->back()->with('error',"Shop không có quyền truy cập !");
                        }
                    }
                }

            }

            if (!isset($arr_permission)){
                return redirect()->back()->withErrors(__('Không có quyền truy cập'));
            }

            $input= $request->data;
            $type=$input['type'];
            $groupid=$input['groupid'];
            $shopid=$input['shop'];
            $shopgroup=$input['shopgroup'];
            $status=$input['status'];

            //                Kiểm tra cấu hình vật phẩm.

            $group = Group::with(array('customs' => function ($query) {

            }))->where('module', '=', $this->module)->findOrFail($groupid);

            $position = 0;

            if (isset($group->position)){
                $position = config('module.minigame.number_of_items.number_of_items_'.$group->position.'');
            }
            $id = $group->id;
            $d_shopid = session('shop_id');
            $items = Item::with(array('groups' => function ($query) {
                $query->where('module', 'minigame-type');
                $query->select('groups.id','title');
            }))
                ->with(array('children' => function ($query) use ($id,$d_shopid)  {
                    $query->where('module', 'minigame-itemset');
                    $query->whereHas('groups', function ($querysub) use ($id) {
                        $querysub->where('group_id',$id);
                    });
                    $query->with(array('children' => function ($query) use ($d_shopid) {
                        $query->where('module', 'minigame-itemset');
                        $query->where('shop_id',$d_shopid);
                    }));
                    $query->whereHas('children', function ($querysub) use ($d_shopid) {
                        $querysub->where('shop_id',$d_shopid);
                    });
                }))
                ->whereHas('children', function ($query) use ($id,$d_shopid)  {
                    $query->where('module', 'minigame-itemset');
                    $query->whereHas('groups', function ($querysub) use ($id) {
                        $querysub->where('group_id',$id);
                    });
                    $query->whereHas('children', function ($querysub) use ($d_shopid) {
                        $querysub->where('shop_id',$d_shopid);
                    });
                })->where('module', 'minigame')->where('status', 1)->get();

            $count = count($items);

            if ($count != $position){
                return response()->json([
                    'success'=>false,
                    'message'=>__('Cấu hình giải thưởng: số vật phẩm không đủ vui lòng kiểm tra lại!'),
                    'redirect'=>''
                ]);
            }

            $percent = 0;
            $try_percent = 0;

            foreach ($items as $item){
                if (isset($item->children[0]->params->percent)){
                    $percent = $percent +  (int) $item->children[0]->params->percent;
                    $try_percent = $try_percent +  (int) $item->children[0]->params->try_percent;
                }
            }

            if ($percent != 100){
                return response()->json([
                    'success'=>false,
                    'message'=>__('Cấu hình giải thưởng: số phầm trăm chơi thật không đủ 100%!'),
                    'redirect'=>''
                ]);
            }

            if ($try_percent != 100){
                return response()->json([
                    'success'=>false,
                    'message'=>__('Cấu hình giải thưởng: số phầm trăm chơi thử không đủ 100%!'),
                    'redirect'=>''
                ]);
            }

            if($type=='group'){

                $shops = Shop::where('group_id', $shopgroup)->get();

                foreach ($shops as $shop) {
                    $data = MinigameDistribute::where('group_id',$groupid)->where('shop_id',$shop->id)->first();

                    if($data){
                        if($status=='true'){
                            $data->status = 1;
                        }else{
                            $data->status = 0;
                        }
                        $data->save();
                    }
                }

                // lấy thông tin IP và user_angent người dùng
                $ip = $request->getClientIp();
                $user_agent = $request->userAgent();
                $message = "Thời gian: <b>" . Carbon::now()->format('d-m-Y H:i:s') . "</b>";

                foreach ($shops as $shop){

                    $data = MinigameDistribute::where('group_id',$groupid)->where('shop_id',$shop->id)->first();

                    if ($data){

                        $message .= "\n";
                        if ($data->status == 1){
                            $message .= "Tài khoản qtv <b>" . Auth::user()->username . "</b> kích hoạt thành công minigame " . $data->id . " - <b>" . $data->title . "</b> trên điểm bán <b>" . $shop->domain . "</b>";

                        }else{
                            $message .= "Tài khoản qtv <b>" . Auth::user()->username . "</b> bỏ kích hoạt thành công minigame " . $data->id . " - <b>" . $data->title . "</b> trên điểm bán <b>" . $shop->domain . "</b>";

                        }
                    }

                }
                $message .= "\n";
                $message .= "IP: <b>" . $ip . "</b>";
                $message .= "\n";
                $message .= "User_agent: <b>" . $user_agent . "</b>";
                Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_noty_minigame'));

            }else{
                $shop = Shop::where('id', $shopid)->first();
                $data = MinigameDistribute::where('group_id',$groupid)->where('shop_id',$shopid)->first();

                if($status=='true'){
                    $data->status = 1;
                }else{
                    $data->status = 0;
                }
                $data->save();

                // lấy thông tin IP và user_angent người dùng
                $ip = $request->getClientIp();
                $user_agent = $request->userAgent();
                $message = "Thời gian: <b>" . Carbon::now()->format('d-m-Y H:i:s') . "</b>";
                $message .= "\n";
                if ($data->status == 1){
                    $message .= "Tài khoản qtv <b>" . Auth::user()->username . "</b> kích hoạt thành công minigame " . $data->id . " - <b>" . $data->title . "</b> trên điểm bán <b>" . $shop->domain . "</b>";

                }else{
                    $message .= "Tài khoản qtv <b>" . Auth::user()->username . "</b> bỏ kích hoạt thành công minigame " . $data->id . " - <b>" . $data->title . "</b> trên điểm bán <b>" . $shop->domain . "</b>";

                }
                $message .= "\n";
                $message .= "IP: <b>" . $ip . "</b>";
                $message .= "\n";
                $message .= "User_agent: <b>" . $user_agent . "</b>";
                Helpers::TelegramNotify($message, config('telegram.bots.mybot.channel_noty_minigame'));
            }

            ActivityLog::add($request, 'Cập nhật thành công trạng thái phân phối '.$this->module.' #'.json_encode($input));
            return response()->json([
                'success'=>true,
                'message'=>__('Cập nhật thành công !'),
                'redirect'=>''
            ]);
        }catch(\Exception $e){
            logger($e);
            return response()->json([
                'success'=>false,
                'message'=>__('Lỗi khi cập nhật, vui lòng thử lại!'),
                'redirect'=>''
            ]);
        }
    }

    public function deleteitem(Request $request)
    {
        try{
            $input= $request->data;
            $id=$input['id'];

            Item::where('id', $id)->delete();
            Item::where('parent_id', $id)->delete();

            ActivityLog::add($request, 'Xóa thành công giải thưởng #'.$id);

            return response()->json([
                'success'=>true,
                'message'=>__('Xóa giải thưởng thành công !'),
                'redirect'=>''
            ]);
        }catch(\Exception $e){
            logger($e);
            return response()->json([
                'success'=>false,
                'message'=>__('Lỗi khi Xóa, vui lòng thử lại!'),
                'redirect'=>''
            ]);
        }
    }

    public function convertContent(Request $request){

        if(!session('shop_id')){
            return redirect()->back()->withErrors(__('Vui lòng chọn shop !'));
        }

        $shop = Shop::where('id',session('shop_id'))->first();

        if ($shop->domain == "shopacclq.net"){
            $delete_minigameDistributes = MinigameDistribute::where('shop_id',session('shop_id'))->onlyTrashed()->get();

            $minigameDistributes = MinigameDistribute::where('shop_id',session('shop_id'))->get();

            foreach ($delete_minigameDistributes as $delete_minigameDistribute){

                if ($delete_minigameDistribute->slug == "vq-sieu-pha-m-lien-quan"){
                    foreach ($minigameDistributes as $minigameDistribute){
                        if ($minigameDistribute->slug == "vq-sieu-pham-lien-quan"){
                            if (isset($delete_minigameDistribute->content)){

                                $minigame_conver = MinigameDistribute::where('shop_id',session('shop_id'))->where('id',$minigameDistribute->id)->first();
                                $minigame_conver->content = $delete_minigameDistribute->content;
                                $minigame_conver->save();
                            }
                        }
                    }
                }
            }
        }

        ActivityLog::add($request, 'Conver bài viết thành công '.$this->module.' #'.session('shop_id'));
        return redirect()->back()->with('success',__('Conver bài viết thành công !'));
    }

}
