<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StokkeluarController extends Controller
{
    public function index()
    {
        return view('stokkeluar.index');
    }
}
