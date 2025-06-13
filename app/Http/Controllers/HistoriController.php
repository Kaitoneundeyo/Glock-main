<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoriController extends Controller
{

    public function index()
    {
        $sales = Sale::with('user', 'saleItems.produk.gambarUtama')
            ->where('user_id', Auth::user()->id)
            ->where('status', 'paid') // Hanya transaksi yang berhasil
            ->latest()
            ->get();

        return view('bukti.index', compact('sales'));
    }
    public function success($invoice)
    {
        $sale = Sale::with('saleItems.produk.gambarUtama') // pastikan relasi lengkap
            ->where('invoice_number', $invoice)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Pastikan status paid
        if ($sale->status !== 'paid') {
            return redirect()->route('checkout.confirmation', ['invoice' => $invoice])
                ->with('warning', 'Pembayaran belum berhasil.');
        }

        return redirect()->route('bukti.index')->with('success', 'Pembayaran berhasil! Transaksi tersimpan.');
    }

    public function failure($invoice)
    {
        $sale = Sale::where('invoice_number', $invoice)->firstOrFail();

        return view('checkout.failure', compact('sale'));
    }
}
