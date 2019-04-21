<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BrokenLinkController extends Controller
{
    //
    function index(){
        return view('brokenlinks.index');
    }
}
