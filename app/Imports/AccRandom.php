<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Item;
use App\Models\Group;
use App\Library\Helpers;

class AccRandom implements ToModel
{
    private $input;
    function __construct($input) {
        $this->input = $input;
    }
    public function model(array $row)
    {
        if ($row[0] != 'Tài khoản') {
            $cat = Group::whereIn('id', $this->input['groups'])->where(['module' => 'acc_category'])->first();
            if (empty(Item::where(['module' => 'acc', 'status' => 1, 'title' => $row[0], 'parent_id' => $this->input['parent_id']])->first())) {
                $item = Item::create([
                   'title' => $row[0], 'slug' => Helpers::Encrypt($row[1], config('etc.encrypt_key')), 'idkey' => $row[2], 
                   'price_old' => $cat->params->price_old??$cat->params->price, 'price' => $cat->params->price, 
                   'params' => [
                        'ext_info' => [
                            ['name' => 'email', 'value' => $row[3]]
                        ]
                    ], 'parent_id' => $this->input['parent_id'], 'module' => 'acc', 'status' => 1, 'percent_sale' => (($cat->params->price_old??$cat->params->price) - $cat->params->price)*100/($cat->params->price_old??$cat->params->price),
                    'author_id' => auth()->user()->id,
                ]);
                $item->groups()->sync($this->input['groups']??[]);
                return $item;
            }
        }
        return null;
    }
}
