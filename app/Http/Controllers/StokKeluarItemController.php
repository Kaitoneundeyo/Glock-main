<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokKeluar;

class StokKeluarItemController extends Controller
{
    public function index($id = null)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'ID tidak ditemukan.');
        }

        $stokKeluar = StokKeluar::find($id);

        if (!$stokKeluar) {
            abort(404, 'Invoice tidak ditemukan');
        }

        return view('itemkeluar.index', compact('stokKeluar'));
    }
}
