<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApiDocument extends Controller
{
    public function index(Request $request){

        return view('vendor.l5-swagger.index');
    }
}
