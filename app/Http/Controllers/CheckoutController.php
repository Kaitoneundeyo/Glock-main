<?php

namespace App\Http\Controllers;

use App\Models\Cart_item;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Midtrans\Snap;
use Midtrans\Config;

class CheckoutController extends Controller
{
    // 🔹 Halaman Riwayat Transaksi
    public function index()
    {
        $sales = Sale::with('saleItems.produk.gambarUtama')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('checkout.transactions', compact('sales'));
    }

    // 🔹 Proses Checkout
    public function process(Request $request)
    {
        $user = Auth::user();

        $cartItems = Cart_item::with('produk.hargaTerbaru')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Keranjang Anda kosong.');
        }

        $total = $cartItems->sum(function ($item) {
            $harga = $item->produk->hargaTerbaru->harga_promo > 0
                ? $item->produk->hargaTerbaru->harga_promo
                : $item->produk->hargaTerbaru->harga_jual;

            return $harga * $item->quantity;
        });

        $sale = Sale::create([
            'user_id' => $user->id,
            'invoice_number' => 'NOTA-' . strtoupper(Str::random(8)),
            'total' => $total,
            'status' => 'pending',
            'tanggal_transaksi' => now(),
        ]);

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

        Cart_item::where('user_id', $user->id)->delete();

        return redirect()->route('checkout.confirmation', ['invoice' => $sale->invoice_number]);
    }

    // 🔹 Halaman Konfirmasi
    public function confirmation($invoice)
    {
        $sale = Sale::with(['saleItems.produk.gambarUtama', 'user'])
            ->where('invoice_number', $invoice)
            ->firstOrFail();

        return view('checkout.confirmation', compact('sale'));
    }

    // 🔹 Halaman Pembayaran Snap Midtrans
    public function pay(Request $request, $invoice)
    {
        $sale = Sale::where('invoice_number', $invoice)->firstOrFail();

        // 🛠️ Konfigurasi Midtrans - PERBAIKAN: gunakan nama kunci yang benar
        Config::$serverKey = config('midtrans.server_key'); // Ubah dari serverKey ke server_key
        Config::$clientKey = config('midtrans.client_key'); // Ubah dari clientKey ke client_key
        Config::$isProduction = config('midtrans.is_production', false); // Ubah dari isProduction ke is_production
        Config::$isSanitized = config('midtrans.is_sanitized', true); // Ubah dari isSanitized ke is_sanitized
        Config::$is3ds = config('midtrans.is_3ds', true); // Ubah dari is3ds ke is_3ds

        // 🔁 Buat Snap Token
        $snapToken = Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $sale->invoice_number,
                'gross_amount' => (int)$sale->total, // Pastikan ini integer
            ],
            'customer_details' => [
                'first_name' => $sale->user->name ?? 'Pelanggan',
                'email' => $sale->user->email ?? 'email@example.com',
            ],
            // PERBAIKAN: Tambahkan callback URLs
            'callbacks' => [ 
                'finish' => route('checkout.finish'),
                'unfinish' => route('checkout.unfinish'),
                'error' => route('checkout.error')
            ],
            'notification_url' => route('midtrans.callback'),
        ]);

        return view('checkout.pay', compact('sale', 'snapToken', 'invoice'));
    }

    // 🔹 Callback dari frontend Midtrans
    public function finish(Request $request)
    {
        $orderId = $request->order_id;
        $statusCode = $request->status_code;
        $transactionStatus = $request->transaction_status;

        if ($orderId) {
            $sale = Sale::where('invoice_number', $orderId)->first();

            if ($sale) {
                // Status akan diupdate oleh webhook, jadi kita hanya redirect
                return redirect()->route('checkout.transactions')
                    ->with('success', 'Terima kasih! Status pembayaran akan diperbarui secara otomatis.');
            }
        }

        return redirect()->route('checkout.transactions')
            ->with('info', 'Transaksi telah selesai.');
    }

    public function unfinish(Request $request)
    {
        return redirect()->route('checkout.transactions')
            ->with('warning', 'Pembayaran belum diselesaikan.');
    }

    public function error(Request $request)
    {
        return redirect()->route('checkout.transactions')
            ->with('error', 'Terjadi kesalahan dalam proses pembayaran.');
    }

    // 🔹 Callback manual (opsional) - untuk testing
    public function success($invoice)
    {
        $sale = Sale::where('invoice_number', $invoice)->firstOrFail();
        $sale->update(['status' => 'success']);
        return redirect()->route('checkout.transactions')->with('success', 'Pembayaran berhasil!');
    }

    public function failure($invoice)
    {
        $sale = Sale::where('invoice_number', $invoice)->firstOrFail();
        $sale->update(['status' => 'failure']);
        return redirect()->route('checkout.transactions')->with('error', 'Pembayaran gagal.');
    }

    public function pending($invoice)
    {
        $sale = Sale::where('invoice_number', $invoice)->firstOrFail();
        $sale->update(['status' => 'pending']);
        return redirect()->route('checkout.transactions')->with('warning', 'Pembayaran masih tertunda.');
    }
}
