<?php

namespace App\Models;


use Eloquent;

class Inbox extends BaseModel
{

	protected $table = 'inbox';

	protected $fillable = [
		'user_id',
		'message',
		'image',
		'conversation_id',
		'seen',
		'status',
	];

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }


    public function conversation()
    {
        return $this->belongsTo(Conversation::class,'conversation_id');
    }

}
