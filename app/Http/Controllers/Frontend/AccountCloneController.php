<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Item;
use App\Models\Nick;
use Illuminate\Http\Request;

class AccountCloneController extends Controller
{
//    public function __construct(Request $request)
//    {
//        if (!auth()->user()->hasRole('admin')) {
//            $this->middleware('permission:acc-demo', ['only' => ['show']]);
//        }
//    }
    public function show(Request $request,$id){

//        $shop_id = session('shop_id');
//
//        if (!isset($shop_id)){
//            return response()->json([
//                'status' => 0,
//                'message' => 'Shop khong co quyen truy cap',
//            ]);
//        }

        $id = \App\Library\Helpers::decodeItemID($id);

        $data= Nick::with(['groups' => function ($query) {
            $query->select('groups.id','groups.title', 'groups.module', 'groups.display_type', 'groups.parent_id', 'groups.is_slug_override', 'groups.image')->with('parent');
        }])->with('category')->where(['module' => 'acc', 'id' => $id]);

        $data->select('id','display_type', 'title', 'slug', 'idkey', 'shop_id', 'parent_id', 'author_id', 'params', 'image', 'image_extension', 'price_old', 'price', 'sticky', 'status', 'created_at', 'published_at');
        $data = $data->first();

        $category = null;
        $image = null;
//        return $data;

        if (!isset($data)){
            return view('frontend.nickclone.404');
        }

        if (!empty($data) && !auth()->user()->hasRole('admin') && $data->author_id != auth()->user()->id) {
            return view('frontend.nickclone.404');
        }

        foreach ($data->groups as $val){
            if ($val->module == 'acc_category'){
                $idcategory = $val->id;
                $category = $val->display_type;
                if ($category == 2){
                    $image =  $val->image;
                }
            }
        }

        $resultcategory = new Group;

        if (!empty($idcategory)) {
            $resultcategory->where('id', $idcategory);
        }

        if (!empty($input['slug'])) {
            $resultcategory->where(function($query) use($input){
                $query->where('slug', $input['slug'])->orWhereHas('custom', function($query) use($input){
                    $query->where('slug', $input['slug']);
                });
            });
        }

        $card_percent = setting('sys_card_percent');
        $atm_percent = setting('sys_atm_percent');

        $data_category = $resultcategory->first();

        $dataAttribute = $data_category->childs;

        $data->randId = \App\Library\Helpers::encodeItemID($data->id);
//        return $card_percent   ;
//        if (!empty($data)) {
//            $data->randId = \App\Library\Helpers::encodeItemID($data->id);
//            $data->price_atm = $data->price;
//            $data->price_old_atm = $data->price_old;
//            $data->price = \App\Library\HelpMoneyPercent::shop_price($data->price);
//            $data->price_old = \App\Library\HelpMoneyPercent::shop_price($data->price_old);
//        }

        return view('frontend.nickclone.index')
            ->with('data',$data)
            ->with('image',$image)
            ->with('category',$category)
            ->with('card_percent',$card_percent)
            ->with('atm_percent',$atm_percent)
            ->with('dataAttribute',$dataAttribute)
            ->with('data_category',$data_category);
    }
}
