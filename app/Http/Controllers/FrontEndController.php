<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontEndController extends Controller
{
    public function beranda()
    {
        return view('layouts.landing');
    }

    public function about()
    {
        return view('about');
    }
}
