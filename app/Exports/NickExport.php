<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Item;
use App\Models\Nick;
use App\Models\User;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class NickExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $input;
    function __construct($input) {
        $this->input = $input;
    }
    public function collection()
    {
        $input = $this->input;
        $items = (new Nick(['table' => 'nicks_completed']))->where('module', 'acc')->whereNotNull('sticky')->with(['category' => function($query){
            $query->with('parent')->select('id', 'parent_id', 'title');
        }, 'author' => function($query){
            $query->select('id', 'username');
        }, 'customer' => function($query){
            $query->select('id', 'username');
        }, 'shop' => function($query){
            $query->select('id', 'domain');
        }]);
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
            $items->whereHas('groups', function ($query) use ($input) {
                $query->where('group_id', $input['group_id']);
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
        if (!empty($input['started_at'])) {
            $items->where('published_at', '>=', Carbon::createFromFormat('d/m/Y H:i:s', $input['started_at'])->format('Y-m-d H:i:s'));
        }
        if (!empty($input['ended_at'])) {
            $items->where('published_at', '<=', Carbon::createFromFormat('d/m/Y H:i:s', $input['ended_at'])->format('Y-m-d H:i:s'));
        }
        
        $items = $items->orderBy('published_at', 'desc')->get();
        $sum = [
            'id' => null,
            'shop' => null,
            'customer' => null,
            'user' => null,
            'category' => null,
            'account' => null,
            'txns_price' => 0,
            'price' => 0,
            'amount_percent' => null,
            'amount' => 0,
            'profit' => 0,
            'created_at' => null,
            'published_at' => null,
            'updated_at' => null,
            'status' => null,
        ];
        $data_excel = [];
        foreach($items as $item){
            $data_excel[] = [
                'id' => $item->id,
                'shop' => $item->shop->domain??null,
                'customer' => $item->customer->username??null,
                'user' => $item->author->username??null,
                'category' => ($item->category->parent->title??null)."\n".($item->category->title??null),
                'account' => $item->title.' ',
                'txns_price' => $item->amount,
                'price' => $item->price,
                'amount_percent' => $item->price > 0? round($item->amount_ctv*100/$item->price,1): null,
                'amount' => $item->amount_ctv,
                'profit' => $item->status == 0? $item->amount - $item->amount_ctv: 0,
                'created_at' => !empty($item->created_at)? $item->created_at->format('d/m/Y H:i:s'): null,
                'published_at' => !empty($item->published_at)? $item->published_at->format('d/m/Y H:i:s'): null,
                'updated_at' => !empty($item->updated_at)? $item->updated_at->format('d/m/Y H:i:s'): null,
                'status' => config('etc.acc.status')[$item->status]??$item->status,
            ];
            $sum['price'] += $item->price;
            $sum['txns_price'] += $item->amount;
            if ($item->status == 0) {
                $sum['amount'] += $item->amount_ctv;
                $sum['profit'] += ($item->amount - $item->amount_ctv);
            }
        }
        array_unshift($data_excel, $sum);
        return collect($data_excel);
    }
    public function headings() :array {
    	return ["ID","Shop","Người mua","CTV bán","Danh mục","Tài khoản","Giá bán","Giá gốc","% CTV hưởng","CTV hưởng","Lợi nhuận","Thời gian tạo","Thời gian bán","Thời gian cập nhật","Trạng thái"];
    }
}