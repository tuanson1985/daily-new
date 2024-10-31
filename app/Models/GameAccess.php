<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameAccess extends Model
{
    use \Awobaz\Compoships\Compoships;
    use HasFactory;
    protected $table = 'game_access';

    protected $guarded = [];

    protected $dates = [ 'created_at', 'updated_at' ];
    protected $fillable = [ 'group_id', 'user_id', 'ratio', 'active' ];
    protected $casts = [
        'ratio' => 'array', /*lưu ý để array vì key có thể là dạng number, thêm key đỡ phải chuyển từ stdclass sang array*/
    ];

    public function acc_category(){
        return $this->hasOne(Group::class, 'id', 'group_id')->where('module', 'acc_category');
    }
}
