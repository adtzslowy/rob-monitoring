<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontEndController extends Controller
{
    public function beranda()
    {
        return view('landing.home');
    }

    public function about()
    {
        return view('landing.tentang');
    }

    public function maps()
    {
        return view('landing.peta-alat');
    }

    public function analitic()
    {
        return view('landing.analisis');
    }

    public function contact()
    {
        return view('landing.contact');
    }
}
