<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;



class AdvertiseControllerx extends Controller
{

    public function __construct()
    {

    }


    public function index(Request $request)
    {


        $datatable = Item::with(array('groups' => function ($query) {
            $query->select('groups.id', 'title');
        }))->where('module', config('module.advertise.key'));
        if ($request->filled('position')) {
            $datatable->where('position', $request->get('position'));
        }
        $datatable->where('shop_id',$request->shop_id);
        $datatable->orderBy('order')->get();

        $datatable= $datatable->paginate( $request->limit??20);
        return response()->json($datatable);


    }


}
