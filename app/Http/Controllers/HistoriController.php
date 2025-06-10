<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HistoriController extends Controller
{
    public function index()
    {
        return view('bukti.index');
    }
}
