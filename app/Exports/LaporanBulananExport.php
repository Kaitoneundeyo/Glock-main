<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class LaporanBulananExport implements FromView
{
    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function view(): View
    {
        $tahun = $this->tahun;

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

        $rows = [];

        for ($i = 1; $i <= 12; $i++) {
            $namaBulan = Carbon::create()->month($i)->translatedFormat('F');
            $penjualan = $laporan->firstWhere('bulan', $i)->total_penjualan ?? 0;
            $hpp = $hppData->firstWhere('bulan', $i)->total_hpp ?? 0;
            $laba = $penjualan - $hpp;

            $rows[] = [
                'bulan' => $namaBulan,
                'penjualan' => $penjualan,
                'hpp' => $hpp,
                'laba' => $laba
            ];
        }

        return view('exports.laporan_bulanan', [
            'tahun' => $tahun,
            'rows' => $rows
        ]);
    }
}
