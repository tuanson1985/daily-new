<?php

namespace App\Models;

use App\Library\Helpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use PHPUnit\TextUI\Help;

class ServerLog extends BaseModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'server_log';

	/**
	 * The fillable fields for the model.
	 *
	 * @var    array
	 */
	protected $fillable = [

		'current_id_server',
		'new_id_server',
		'content',
        'shop_list',
        'current_price',
		'new_price',
		'status',
        'params',
		'user_id',
        'prefix',
		'user_agent',
	];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public static function add($current_sever,$content="",$new_server = null)
    {

        //Get current List Shop
        $str_shop_name=[];
        $shop_name =  \App\Library\Helpers::DecodeJson('shop_name',$current_sever->shop_name);
        if(!empty($shop_name)){
            for ($i = 0; $i < count($shop_name); $i++){
                if( $shop_name[$i]!=null){
                    array_push($str_shop_name,$shop_name[$i]);
                }
            }
        }
        //Select lstShopname from Shop
        $lstShop = Shop::where("server_id",$current_sever->id)->get();
        if(isset($lstShop) && count($lstShop) > 0){
            foreach ($lstShop as $item){
                array_push($str_shop_name,$item->domain);
            }
        }
        $log =[
            'current_id_server' => $current_sever->id,
            'new_id_server' => $new_server->id??null,
            'content' => $content,
            'shop_list' => json_encode($str_shop_name, JSON_UNESCAPED_UNICODE),
            'current_price' => $current_sever->price,
            'new_price' => $new_server != null ? $new_server->price : $current_sever->price,
            'status' => $current_sever->status,
            'params' => '',
            'user_id' => auth()->user()->id??null,
            'prefix' => 'admin',
            'user_agent' => ''
        ];
        ServerLog::create($log);
    }
}
