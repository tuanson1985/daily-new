<?php


use App\Library\CreateMenuCustom;
use App\Models\Inbox;

use App\Models\GameAccess;
use App\Models\Nick;

use App\Models\Order;
use App\Models\ServiceAccess;
use App\Models\Shop;
use App\Models\Telecom;
use App\Models\User;
use Carbon\Carbon;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Conversation;
use App\Models\Group;
use App\Models\Item;
use App\Models\Client;
use App\Models\SubItem;
use App\Models\Setting;
use App\Models\Charge;
use App\Models\TelecomValue;



// Slider home
View::composer('admin._layouts.partials.extras._client', function ($view) {
//    try {
//        $data = null;
//        if(Auth::user()->account_type == 1){
//            $data = Shop::orderBy('id','desc');
//            $user = User::query()->with(['access_shops', 'access_shop_groups'])->find(Auth::user()->id);
//            if($user->shop_access != 'all' && !$user->hasRole('admin')){
//                $data->where(function($query) use($user){
//                    $query->whereIn('id', $user->access_shops->pluck('id')->toArray())->orWhereIn('group_id', $user->access_shop_groups->pluck('id')->toArray());
//                });
//            }
//            $data = $data->select('id','domain','title','group_id')->get();
//        }
//    }
//    catch(\Exception $e){
//        Log::error($e);
//        $data = null;
//    }
    $data = null;
    return $view->with('data', $data);
});

//View::composer('admin._layouts.partials.extras._control_money', function ($view) {
//
//    $data = \Cache::remember('_control_money', 1800, function () {
//        $price_control_total = 0;
//        try {
//
//            $price_control = Order::query()
//                ->where('gate_id',0)
//                ->where(function($q){
//                    $q->orWhere('status', '=',10);
//                    $q->orWhere('status', '=',11);
//                })
//                ->where('module', '=', config('module.service-purchase'))
//                ->where('processor_id',Auth::user()->id)
//                ->whereNull('type_version')
//                ->with(['item_ref','author', 'processor' => function($query){
//                    $query->with('service_access');
//                }])
//                ->get()->map(function ($item){
//                    $ratio = 80;
//                    if (isset($item->processor)){
//                        if (isset($item->processor->service_access)){
//                            $service_access = $item->processor->service_access;
//                            $param = json_decode(isset($service_access->params) ? $service_access->params : "");
//                            if(isset($param->{'ratio_' . ($item->item_ref->id??null)})){
//                                $ratio= $param->{'ratio_' . ($item->item_ref->id??null)};
//                            }
//                            else{
//                                $ratio=$ratio;
//                            }
//                        }
//                    }
//
//                    //cộng tiền user
//                    $real_received_amount = ($ratio * $item->price_ctv) / 100;
//                    return (int)$real_received_amount;
//                });
//            $price_control_total = $price_control->toArray();
//            $price_control_total = array_sum($price_control_total);
//
//        }
//        catch(\Exception $e){
//            Log::error($e);
//            $price_control_total = 0;
//        }
//
//        return $data = $price_control_total;
//    });
//
//    $price_nick_control_total = 0;
//    $price_control_total = $data;
//
//    return $view->with('price_control_total', $price_control_total)->with('price_nick_control_total', $price_nick_control_total);
//});

View::composer('frontend._layouts.partials.extras._control_money', function ($view) {
//    try {
//
//        $price_control_total = 0;
//        $price_control = Order::query()
//            ->where('gate_id',0)
//            ->where(function($q){
//                $q->orWhere('status', '=',10);
//                $q->orWhere('status', '=',11);
//            })
//            ->where('module', '=', config('module.service-purchase'))
//            ->where('processor_id',Auth::user()->id)
//            ->whereNull('type_version')
//            ->with(['item_ref','author', 'processor' => function($query){
//                $query->with('service_access');
//            }])
//            ->get()->map(function ($item){
//                $ratio = 80;
//                if (isset($item->processor)){
//                    if (isset($item->processor->service_access)){
//                        $service_access = $item->processor->service_access;
//                        $param = json_decode(isset($service_access->params) ? $service_access->params : "");
//                        if(isset($param->{'ratio_' . ($item->item_ref->id??null)})){
//                            $ratio= $param->{'ratio_' . ($item->item_ref->id??null)};
//                        }
//                        else{
//                            $ratio=$ratio;
//                        }
//                    }
//                }
//                //cộng tiền user
//                $real_received_amount = ($ratio * $item->price_ctv) / 100;
//                return (int)$real_received_amount;
//            });
//        $price_control_total = $price_control->toArray();
//        $price_control_total = array_sum($price_control_total);
//
//        $price_nick_control_total = 0;
//
//    }
//    catch(\Exception $e){
//        Log::error($e);
//        $price_control_total = 0;
//        $price_nick_control_total = 0;
//    }

    $price_control_total = 0;
    $price_nick_control_total = 0;
    return $view->with('price_control_total', $price_control_total)->with('price_nick_control_total', $price_nick_control_total);
});


