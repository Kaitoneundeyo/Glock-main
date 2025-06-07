<?php

namespace App\Http\Controllers;

use App\Models\Stok_masuk;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index($id = null)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'ID tidak ditemukan.');
        }

        $stokMasuk = Stok_masuk::find($id);

        if (!$stokMasuk) {
            abort(404, 'Invoice tidak ditemukan');
        }

        return view('item.index', compact('stokMasuk'));
    }
}
