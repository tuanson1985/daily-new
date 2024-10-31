<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'shop';

	/**
	 * The fillable fields for the model.
	 *
	 * @var    array
	 */
	protected $fillable = [
		'idkey',
		'title',
		'domain',
		'secret_key',
		'group_id',
		'note',
		'status',
		'key_transfer',
		'id_transfer',
		'ratio_atm',
		'ntn_partner_id',
		'ntn_partner_key',
		'ntn_partner_key_card',
		'ntn_username',
		'ntn_password',
		'ccc_partner_id',
		'ccc_partner_key',
		'ccc_username',
		'ccc_password',
		'ppp_partner_id',
		'ppp_partner_key',
		'ppp_username',
		'ppp_password',
		'created_at',
		'server_id',
        'cf_status',
        'cf_account',
        'language',
        'rate',
        'rate_money',
        'timezone',
        'currency',
        'params',
        'telegram_config',
		'type_information',
		'is_get_data',
		'url_get_data',
		'expired_time',
	];

    protected $hidden = [
        'key_transfer',
        'id_transfer',
        'ntn_partner_id',
        'ntn_partner_key',
        'ntn_partner_key_card',
        'ntn_username',
        'ntn_password',
        'ccc_partner_id',
        'ccc_partner_key',
        'ccc_username',
        'ccc_password',
		'ppp_partner_id',
		'ppp_partner_key',
		'ppp_username',
		'ppp_password',
		'tichhop_key',
		'tichhop_username',
		'tichhop_password',
		'daily_partner_id',
		'daily_username',
		'daily_partner_key_service'
    ];


    //ae đừng edit cái này nhé.nếu xóa hỏi qua ý kiến ae nhé
	public function group(){
        return $this->belongsTo(Shop_Group::class,'group_id','id')->select('id','title','status','params');
    }

    public function group_module(){
        return $this->hasMany(GroupShop::class,'shop_id');
    }

    public function minigame_module(){
        return $this->hasMany(MinigameDistribute::class,'shop_id');
    }

}
