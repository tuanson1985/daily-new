<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $page_title = 'Merchant';
        $page_breadcrumbs = [
            ['page' => '1',
                'title' => 'Home',
            ],
        ];

        return view('frontend.index')->with('page_title',$page_title)->with('page_breadcrumbs',$page_breadcrumbs);
    }

}
