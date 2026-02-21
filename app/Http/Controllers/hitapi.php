<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class hitapi extends Controller
{
    public function index()
    {
        $res = Http::get(config('services.iot.url'));

        if ($res->successful()) {
            return $res->json();
        } else {
            return response()->json(['error' => 'Gagal fetch API' ]);
        }

    }
}
