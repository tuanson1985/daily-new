<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends BaseModel
{
    use HasFactory;
    protected $table = 'notification';
    protected $appends = ['image','type_href'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'user_id',
        'ref_id',
        'type',
        'status',
        'content',
        'href',
        'order_id',
        'params',
    ];
    public function user_ref()
    {
        return $this->belongsTo(User::class,'ref_id','id');
    }
    public function order() {
        return $this->belongsTo(Order::class, 'order_id','id');
    }
    public function getContentAttribute()
    {
        try {
            $content = config('module.notification.type.'.$this->type.'.title');
            if(strpos($content, "@>money") !== false){
                $params = json_decode($this->params);
                $price =str_replace(',','.',number_format($params->price));
                $content = str_replace('@>money',$price,$content);
            }
            if(strpos($content, "@>user") !== false){
                $fullname = $this->user_ref->fullname_display;
                $content = str_replace('@>user',$fullname,$content);
            }

            if(strpos($content, "@>thoigian") !== false){
                $params = json_decode($this->params);
                $thoigian = $params->thoigian;
                $content = str_replace('@>thoigian',$thoigian,$content);
            }

            if(strpos($content, "@>bonus") !== false){
                $params = json_decode($this->params);
                $bonus = $params->bonus;
                $content = str_replace('@>bonus',$bonus,$content);
            }

            if(strpos($content, "@>count") !== false){
                $params = json_decode($this->params);
                $count = $params->count;
                $content = str_replace('@>count',$count,$content);
            }

        }
        catch (\Exception $e) {
            $content = "Thông báo này bị lỗi hiển thị #".$this->id;
        }
        return $this->attributes['content'] = $content;
    }
    public function getImageAttribute(){
        try {
            if($this->user_id != null){
                $image =  $this->user_ref->image;
                if(gettype(json_decode($image)) == 'object'){
                    $image = get_object_vars(json_decode($image))['anh_crop'];
                }
                else{
                    $image = "";
                }
            }
            else{
                $image = "";
            }
        }catch (\Exception $e) {
            $image = "";
        }
        return $this->attributes['image'] = $image;
    }
    public function getTypeHrefAttribute(){
        $href = config('module.notification.type.'.$this->type.'.href');
        return $this->attributes['type_href'] = $href;
    }
}
