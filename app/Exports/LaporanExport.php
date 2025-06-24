<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\Stok_masuk;
use App\Models\StokKeluar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class LaporanExport implements FromCollection, WithColumnWidths, ShouldAutoSize
{
    protected $tanggal;

    public function __construct($tanggal)
    {
        $this->tanggal = $tanggal;
    }

    public function collection()
    {
        $tanggal = $this->tanggal;

        $orders = Order::whereDate('paid_at', $tanggal)
            ->where('status', 'paid')
            ->get();

        $stokMasuk = Stok_masuk::whereDate('tanggal_masuk', $tanggal)->get();
        $stokKeluar = StokKeluar::whereDate('tanggal_keluar', $tanggal)->get();

        $data = collect();

        $data->push(['==== LAPORAN HARIAN TANGGAL: ' . $tanggal . ' ====']);
        $data->push([]);
        $data->push(['--- ORDER ---']);
        $data->push(['No Order', 'Total', 'Status', 'Tanggal Bayar']);

        $totalPemasukan = 0;

        foreach ($orders as $order) {
            $data->push([
                $order->order_number,
                $order->total_amount,
                $order->status,
                $order->paid_at,
            ]);
            $totalPemasukan += $order->total_amount;
        }

        $data->push(['TOTAL PEMASUKAN', $totalPemasukan]);
        $data->push([]);
        $data->push(['--- STOK MASUK ---']);
        $data->push(['No Invoice', 'Tanggal Masuk', 'Supplier']);

        foreach ($stokMasuk as $masuk) {
            $data->push([
                $masuk->no_invoice,
                $masuk->tanggal_masuk,
                $masuk->supplier,
            ]);
        }

        $data->push([]);
        $data->push(['--- STOK KELUAR ---']);
        $data->push(['No Dokumen', 'Tanggal Keluar', 'Tujuan']);

        foreach ($stokKeluar as $keluar) {
            $data->push([
                $keluar->no_dokumen,
                $keluar->tanggal_keluar,
                $keluar->tujuan,
            ]);
        }

        return $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 20,
            'C' => 25,
            'D' => 25,
        ];
    }
}
