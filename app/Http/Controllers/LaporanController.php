<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Stok_masuk;
use App\Models\StokKeluar;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());

        // Order
        $orders = Order::with(['orderItems.produk'])
            ->whereDate('paid_at', $tanggal)
            ->where('status', 'paid')
            ->paginate(5, ['*'], 'orders_page');

        $totalPemasukan = $orders->sum('total_amount');

        // Stok Masuk
        $stokMasuk = Stok_masuk::with(['items.produk'])
            ->whereDate('tanggal_masuk', $tanggal)
            ->paginate(5, ['*'], 'masuk_page');

        // Stok Keluar
        $stokKeluar = StokKeluar::with(['items.produk'])
            ->whereDate('tanggal_keluar', $tanggal)
            ->paginate(5, ['*'], 'keluar_page');

        return view('laporan.index', compact('tanggal', 'orders', 'totalPemasukan', 'stokMasuk', 'stokKeluar'));
    }

    public function exportExcel(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());

        $fileName = 'laporan-harian-' . $tanggal . '.xlsx';
        return Excel::download(new LaporanExport($tanggal), $fileName);
    }
}
