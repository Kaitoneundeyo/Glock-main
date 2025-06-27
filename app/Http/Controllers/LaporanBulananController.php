<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\LaporanBulananExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanBulananController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);

        // Ambil data per bulan
        $laporan = DB::table('orders')
            ->selectRaw('MONTH(paid_at) as bulan, SUM(total_amount) as total_penjualan')
            ->whereYear('paid_at', $tahun)
            ->where('status', 'paid')
            ->groupBy(DB::raw('MONTH(paid_at)'))
            ->orderBy('bulan')
            ->get();

        $hppData = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->selectRaw('MONTH(orders.paid_at) as bulan, SUM(order_items.hpp * order_items.quantity) as total_hpp')
            ->whereYear('orders.paid_at', $tahun)
            ->where('orders.status', 'paid')
            ->groupBy(DB::raw('MONTH(orders.paid_at)'))
            ->orderBy('bulan')
            ->get();

        // Siapkan array bulanan
        $labels = [];
        $penjualan = [];
        $hpp = [];
        $laba = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->translatedFormat('F');

            $totalPenjualan = $laporan->firstWhere('bulan', $i)->total_penjualan ?? 0;
            $totalHpp = $hppData->firstWhere('bulan', $i)->total_hpp ?? 0;

            $penjualan[] = $totalPenjualan;
            $hpp[] = $totalHpp;
            $laba[] = $totalPenjualan - $totalHpp;
        }

        return view('laporanbulanan.index', compact('tahun', 'labels', 'penjualan', 'hpp', 'laba'));
    }

    public function exportExcel(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);
        $fileName = 'laporan-bulanan-' . $tahun . '.xlsx';

        return Excel::download(new LaporanBulananExport($tahun), $fileName);
    }
}
