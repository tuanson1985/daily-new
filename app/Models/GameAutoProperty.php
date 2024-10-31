<?php

namespace App\Models;


use App\Traits\Metable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class GameAutoProperty extends BaseModel
{
    use Metable;

    protected $table = 'game_auto_properties';

    protected $fillable = ['provider', 'order', 'keyid', 'key', 'parent_id', 'parent_table', 'name', 'thumb', 'meta', 'list_select'];

    protected $casts = ['meta' => 'array'];

    function childs(){
        return $this->hasMany(GameAutoProperty::class, 'parent_id', 'id')->whereNull('parent_table');
    }
    function parent(){
        return $this->hasOne(GameAutoProperty::class, 'id', 'parent_id');
    }
    public function category(){
        return $this->hasOne(Group::class, 'id', 'parent_id');
    }
    public function items(){
        return $this->belongsToMany('App\Models\Item','item_game_auto_properties', 'property_id', 'item_id');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function($group) {
            $group->items()->sync([]);
            $group->childs()->delete();
        });
    }
}
