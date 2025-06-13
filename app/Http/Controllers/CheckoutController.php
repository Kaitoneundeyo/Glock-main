<?php

namespace App\Http\Controllers;

use App\Models\Cart_item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        // Tampilkan daftar transaksi user
        $sales = Sale::where('user_id', Auth::id())->latest()->get();
        return view('checkout.transactions', compact('sales'));
    }

    public function process(Request $request)
    {
        $user = Auth::user();

        $cartItems = Cart_item::with('produk.hargaTerbaru')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Keranjang Anda kosong.');
        }

        // Hitung total
        $total = $cartItems->sum(function ($item) {
            $harga = $item->produk->hargaTerbaru->harga_promo > 0
                ? $item->produk->hargaTerbaru->harga_promo
                : $item->produk->hargaTerbaru->harga_jual;

            return $harga * $item->quantity;
        });

        // Buat transaksi utama
        $sale = Sale::create([
            'user_id' => $user->id,
            'invoice_number' => 'NOTA-' . strtoupper(Str::random(8)),
            'total' => $total,
            'status' => 'pending',
            'tanggal_transaksi' => now(),
        ]);

        // Simpan detail item
        foreach ($cartItems as $item) {
            $harga = $item->produk->hargaTerbaru->harga_promo > 0
                ? $item->produk->hargaTerbaru->harga_promo
                : $item->produk->hargaTerbaru->harga_jual;

            $sale->saleItems()->create([
                'produk_id' => $item->produk_id,
                'quantity' => $item->quantity,
                'price' => $harga,
            ]);
        }

        // Kosongkan keranjang
        Cart_item::where('user_id', $user->id)->delete();

        // Redirect ke halaman konfirmasi
        return redirect()->route('checkout.confirmation', ['invoice' => $sale->invoice_number]);
    }

    /**
     * Tampilkan halaman konfirmasi pembayaran.
     */
    public function confirmation($invoice)
    {
        $sale = Sale::with(['saleItems.produk.gambarUtama', 'user'])
            ->where('invoice_number', $invoice)
            ->firstOrFail();

        return view('checkout.confirmation', compact('sale'));
    }

    public function pay(Request $request, $invoice)
    {
        $sale = Sale::with('saleItems.produk', 'user')
            ->where('invoice_number', $invoice)
            ->firstOrFail();

        // Ambil Snap Token dari Midtrans
        $midtransService = new MidtransService();
        $snapToken = $midtransService->createSnapToken($sale);

        // Tampilkan halaman pembayaran
        return view('checkout.payment', compact('snapToken', 'sale'));
    }

}
