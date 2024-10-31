<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExportData implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    function __construct(array $data,View $view) {
        $this->data = $data;
        $this->view = $view;
    }
    public function view(): View
    {
        return $this->view->with(array('data' => $this->data));
    }
}
