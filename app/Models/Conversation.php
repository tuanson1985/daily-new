<?php

namespace App\Models;

use Eloquent;

class Conversation extends BaseModel
{

	protected $table = 'conversation';


	protected $fillable = [
        'type',
		'ref_id',
		'author_id',
        'processor_id',
		'complain',
		'status',
	];

    public function inbox()
    {
        return $this->hasMany(Inbox::class,'conversation_id','conversation_id');
    }

    public function author()
    {
        return $this->hasOne(User::class,'id','author_id');
    }
    public function processor()
    {
        return $this->hasOne(User::class,'id','processor_id');
    }

    public function ref_id()
    {
        return $this->hasOne(Order::class,'id','ref_id');
    }

}
