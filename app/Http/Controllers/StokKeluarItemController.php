<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokKeluarItem;
use App\Models\StokKeluar;

class StokKeluarItemController extends Controller
{

    public function index($id)
    {
        $stokKeluar = StokKeluar::findOrFail($id);
        $item = StokKeluarItem::where('stok_keluars_id', $id)->first();

        return view('itemkeluar.index', compact('stokKeluar', 'item', 'id'));
    }
}
