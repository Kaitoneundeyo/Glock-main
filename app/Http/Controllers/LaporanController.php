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
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());

        // Orders (yang sudah dibayar di tanggal tsb)
        $orders = Order::with(['orderItems.produk'])
            ->whereDate('paid_at', $tanggal)
            ->where('status', 'paid')
            ->paginate(5, ['*'], 'orders_page');

        // Hitung total penjualan dan HPP dari order_items
        $totalPenjualan = 0;
        $totalHPP = 0;

        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                $subtotal = $item->price * $item->quantity;
                $hppTotal = ($item->hpp ?? 0) * $item->quantity;

                $totalPenjualan += $subtotal;
                $totalHPP += $hppTotal;
            }
        }

        $labaKotor = $totalPenjualan - $totalHPP;

        // Stok Masuk
        $stokMasuk = Stok_masuk::with(['items.produk'])
            ->whereDate('tanggal_masuk', $tanggal)
            ->paginate(5, ['*'], 'masuk_page');

        // Stok Keluar (untuk rusak/expired)
        $stokKeluar = StokKeluar::with(['items.produk'])
            ->whereDate('tanggal_keluar', $tanggal)
            ->paginate(5, ['*'], 'keluar_page');

        return view('laporan.index', compact(
            'tanggal',
            'orders',
            'stokMasuk',
            'stokKeluar',
            'totalPenjualan',
            'totalHPP',
            'labaKotor'
        ));
    }

    public function exportExcel(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());

        $fileName = 'laporan-harian-' . $tanggal . '.xlsx';
        return Excel::download(new LaporanExport($tanggal), $fileName);
    }
}
