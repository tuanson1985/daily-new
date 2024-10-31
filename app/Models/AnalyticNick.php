<?php

namespace App\Models;

use App\Traits\Metable;
use Illuminate\Database\Eloquent\Builder;
use DB;
class AnalyticNick extends BaseModel
{
    protected $table = 'nicks_analytic';

    protected $fillable = ['module', 'module_id', 'date', 'price', 'amount', 'amount_ctv', 'count_total', 'count_customer', 'count_failed', 'count_deleted', 'order'];

    public function shop(){
        return $this->hasOne(Shop::class, 'id', 'module_id');
    }
}
