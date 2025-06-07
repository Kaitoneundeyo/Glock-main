<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GambarProdukController extends Controller
{
    public function index()
    {
        return view('gambar.index');
    }
}
