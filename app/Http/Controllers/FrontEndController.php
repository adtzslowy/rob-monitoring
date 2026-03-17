<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontEndController extends Controller
{
    public function beranda()
    {
        return view('welcome');
    }

    public function about()
    {
        return view('about');
    }
}
