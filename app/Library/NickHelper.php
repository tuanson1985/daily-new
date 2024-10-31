<?php
namespace App\Library;
use App\Models\Nick;
use App\Models\AnalyticNick;
use Carbon\Carbon, DB;

class NickHelper{
    /*
        params {
            shops: array shop id,
        }
    */
    static function live($input = []){ /* Live data & selled today */
        $complete = (new Nick(['table' => 'nicks_completed']))->select('id','status','sticky','shop_id','published_at');
        if (!empty($input['shops'])) {
            $complete->whereIn('shop_id', is_array($input['shops'])? $input['shops']: explode(',', $input['shops']));
        }
        $result = [
            'today_success_count' => (clone $complete)->where('published_at', '>=', date('Y-m-d')." 00:00:00")->where('status', 0)->count(),
            'today_success_customer' => (clone $complete)->where('published_at', '>=', date('Y-m-d')." 00:00:00")->select('sticky')->groupBy('sticky')->paginate(1)->total(),
            'today_success_price' => (clone $complete)->where('published_at', '>=', date('Y-m-d')." 00:00:00")->where('status', 0)->sum('price'),
            'today_success_amount' => (clone $complete)->where('published_at', '>=', date('Y-m-d')." 00:00:00")->where('status', 0)->sum('amount'),
            'today_success_amount_ctv' => (clone $complete)->where('published_at', '>=', date('Y-m-d')." 00:00:00")->where('status', 0)->sum('amount_ctv'),
            'nick_selled_count' => [], 'nick_selling_count' => []
        ];
        $selled_status = [
            2 => 'Chờ xử lý', 3 => 'Đang check thông tin', 4 => 'Sai mật khẩu', 5 => 'Đã xoá', 6 => 'Check lỗi'
        ];
        foreach ($selled_status as $key => $value) {
            $result['nick_selled_count'][] = [
                'status' => $key, 'name' => config('etc.acc.status')[$key], 'count' => (clone $complete)->where('status', $key)->count()
            ];
        }
        $selling_status = [
            4 => 'Sai mật khẩu', 5 => 'Đã xoá', 6 => 'Check lỗi', 7 => 'Chờ thông tin auto', 8 => 'Đang lấy thông tin', 9 => 'Đang điền thông tin', 11 => 'Cũ thông tin'
        ];
        $selling = (new Nick(['table' => 'nicks']))->whereHas('access_category', function($query){
            $query->where('active', 1);
        })->select('id','status','shop_id','parent_id','author_id');
        if (!empty($input['shops'])) {
            $selling->where(function($query) use($input){
                $query->whereHas('access_shops', function($query) use($input){
                    $query->whereIn('shop.id', $input['shops']);
                })->orWhereHas('author', function($query){
                    $query->where('shop_access', 'all');
                });
            });
        }
        foreach ($selling_status as $key => $value) {
            $result['nick_selling_count'][] = [
                'status' => $key, 'name' => config('etc.acc.status')[$key], 'count' => (clone $selling)->where('status', $key)->count()
            ];
        }
        return $result;
    }
    /*
        params {
            shops: array shop id, date: Y-m-d
        }
    */
    static function timeline_hourly($input = []){
        $complete = (new Nick(['table' => 'nicks_completed']))->select('id','status','sticky','shop_id','published_at','price','amount','amount_ctv');
        if (!empty($input['shops'])) {
            $complete->whereIn('shop_id', is_array($input['shops'])? $input['shops']: explode(',', $input['shops']));
        }
        $complete = $complete->whereDate('published_at', $input['date']??date('Y-m-d'))->whereIn('status', [0,4])->get();
        $result = [];
        for ($i=0; $i < 24; $i++) {
            $h = str_pad($i,2,'0',STR_PAD_LEFT);
            $hourly = $complete->where('published_at', '>=', "{$input['date']} {$h}")->where('published_at', '<', "{$input['date']} ".str_pad($i+1,2,'0',STR_PAD_LEFT));
            $result[$i] = [
                'time' => $h, 'price' => $hourly->where('status',0)->sum('price'), 'amount' => $hourly->where('status',0)->sum('amount'),
                'amount_ctv' => $hourly->where('status',0)->sum('amount_ctv'), 'count_total' => $hourly->where('status',0)->count(),
                'count_customer' => $hourly->where('status',0)->groupBy('sticky')->count(), 'count_failed' => $hourly->where('status',4)->count()
            ];
        }
        return $result;
    }
    /*
        params {
            shops: array shop id, date: Y-m-d
        }
    */
    static function timeline_dayly($input = []){

        $complete = (new Nick(['table' => 'nicks_completed']))->select('id','status','sticky','shop_id','published_at','price','amount','amount_ctv');
        if (!empty($input['shops'])) {
            $complete->whereIn('shop_id', is_array($input['shops'])? $input['shops']: explode(',', $input['shops']));
        }
        $complete = $complete
            ->where('published_at', '>=', $input['date_from'])
            ->where('published_at', '<=', $input['date_to'])
            ->whereIn('status', [0,4]);
        $result = [
            'price' => (int)(clone $complete)->where('status',0)->sum('price'),
            'amount' => (int)(clone $complete)->where('status',0)->sum('amount'),
            'amount_ctv' => (int)(clone $complete)->where('status',0)->sum('amount_ctv'),
            'count_total' => (clone $complete)->where('status',0)->count(),
            'count_customer' => (clone $complete)->where('status',0)->groupBy('sticky')->count(),
            'total_success' => (clone $complete)->where('status',0)->count(),
            'total_wrong_password' => (clone $complete)->where('status',4)->count()
        ];
        return $result;
    }
    /*
        params {
            shops: array shop id, date_from: Y-m-d, date_to: Y-m-d
        }
    */
    static function timeline($input = []){
        $query = AnalyticNick::where(['module' => $input['module']??'shop'])->orderBy('date', 'asc');
        if (!empty($input['shops'])) {
            $query->whereIn('module_id', is_array($input['shops'])? $input['shops']: explode(',', $input['shops']));
        }
        if (!empty($input['date_from'])) {
            $query->where('date', '>=', $input['date_from']);
        }
        if (!empty($input['date_to'])) {
            $query->where('date', '<=', $input['date_to']);
        }
        $query = $query->get();
        $result = [];
        if (!empty($input['sum'])) {
            $result['sum'] = [
                'price' => $query->sum('price'), 'amount' => $query->sum('amount'), 'amount_ctv' => $query->sum('amount_ctv'),
                'count_total' => $query->sum('count_total'), 'count_customer' => $query->sum('count_customer'), 'count_failed' => $query->sum('count_failed'),
                'count_deleted' => $query->sum('count_deleted')
            ];
        }
        foreach ($query->groupBy('date') as $key => $value) {
            $result[$key] = ['price' => $value->sum('price'), 'amount' => $value->sum('amount'), 'amount_ctv' => $value->sum('amount_ctv'),
                'count_total' => $value->sum('count_total'), 'count_customer' => $value->sum('count_customer'), 'count_failed' => $value->sum('count_failed'),
                'count_deleted' => $value->sum('count_deleted')
            ];
        }
        return $result;
    }

    static function analytic($input = []){
        $items = (new Nick(['table' => 'nicks_completed']))->select('id','status','parent_id','sticky','author_id','shop_id','published_at');
        if (!empty($input['group_id'])) {
            $items->where('parent_id', $input['group_id']);
        }
        if (!empty($input['author_id'])) {
            $items->where('author_id', $input['author_id']);
        }
        if (!empty($input['customer'])) {
            $items->whereHas('customer', function ($query) use ($input) {
                $query->where('username', $input['customer']);
            });
        }
        if (!empty($input['shop_id'])) {
            $items->where('shop_id', $input['shop_id']);
        }
        if (!empty($input['status'])) {
            $items->where('status', $input['status']);
        }else{
            $items->where('status', 0)->whereNotNull('sticky');
        }
        if (!empty($input['date'])) {
            $items->whereDate('published_at', $input['date']);
        }else{
            if (!empty($input['started_at'])) {
                $items->where('published_at', '>=', $input['started_at']);
            }else{
                $items->where('published_at', '>=', Carbon::now()->startOfMonth()->format('Y-m-d 00:00:00'));
            }
            if (!empty($input['ended_at'])) {
                $items->where('published_at', '<=', $input['ended_at']);
            }else{
                $items->where('published_at', '<=', Carbon::now()->format('Y-m-d H:i:s'));
            }
        }
        if (empty($input['status'])) {
            return [
                'count' => (clone $items)->count(),
                'count_customer' => (clone $items)->select('sticky')->groupBy('sticky')->paginate(1)->total(),
                'price' => (clone $items)->sum('price'),
                'amount' => (clone $items)->sum('amount'),
                'amount_ctv' => (clone $items)->sum('amount_ctv')
            ];
        }else{
            return $items->count();
        }
    }

    static function create_timeline($input = []){
        if (!empty($input['live'])) {
            $date = AnalyticNick::where('module', 'shop')->where('order', '<', time()-300)
                    ->where('date', '>=', $input['live'])->orderBy('date', 'asc')->first()->date??null;
        }else{
            $date = AnalyticNick::where('module', 'shop')->orderBy('date', 'desc')->first()->date??null;
            if (empty($date)) {
                $last = (new Nick(['table' => 'nicks_completed']))->where(['module' => 'acc', 'status' => 0])->whereNotNull('sticky')->orderBy('published_at', 'asc')->first();
                if (!empty($last)) {
                    $date = $last->published_at->format('Y-m-d');
                }
            }else{
                $date = Carbon::createFromFormat('Y-m-d', $date)->addDay()->format('Y-m-d');
            }
        }
        if (!empty($date) && $date < date('Y-m-d')) {
            $shops = (new Nick(['table' => 'nicks_completed']))->where(['module' => 'acc', 'status' => 0])->whereDate('published_at', $date)->whereNotNull('sticky')->groupBy('shop_id')->get();
            if ($shops->count()) {
                foreach ($shops as $shop) {
                    $success = NickHelper::analytic(['shop_id' => $shop->shop_id, 'date' => $date, 'status' => 0]);
                    $failed = NickHelper::analytic(['shop_id' => $shop->shop_id, 'date' => $date, 'status' => 4]);
                    $deleted = NickHelper::analytic(['shop_id' => $shop->shop_id, 'date' => $date, 'status' => 5]);
                    AnalyticNick::updateOrCreate(['module' => 'shop', 'module_id' => $shop->shop_id, 'date' => $date], [
                        'price' => $success['price'], 'amount' => $success['amount'], 'amount_ctv' => $success['amount_ctv'], 'count_total' => $success['count'],
                        'count_customer' => $success['count_customer'], 'count_failed' => $failed, 'count_deleted' => $deleted, 'order' => time()
                    ]);
                }
            }else{
                AnalyticNick::updateOrCreate(['module' => 'shop', 'module_id' => null, 'date' => $date], ['order' => time()]);
            }
            return $date;
        }
        return null;
    }
}
