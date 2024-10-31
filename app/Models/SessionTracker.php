<?php

namespace App\Models;

use Illuminate\Http\Request;

class SessionTracker extends BaseModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sessions';

	/**
	 * The fillable fields for the model.
	 *
	 * @var    array
	 */
    protected $fillable = [
        'id',
        'user_id',
        'ip_address',
        'user_agent',
        'payload',
        'last_activity',
    ];





    public static function endSessionByUser($user_id,$sessionId_without=null){

        $model=self::where('user_id',$user_id);
        if($sessionId_without){
            $model->where('id','!=',$sessionId_without);
        }
        $model->delete();

    }

}
