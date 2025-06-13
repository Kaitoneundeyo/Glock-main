<?php

namespace App\Http\Controllers;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        Log::info('Callback diterima: ', $request->all());

        $serverKey = config('midtrans.server_key');
        $hashed = hash(
            'sha512',
            $request->order_id .
                $request->status_code .
                $request->gross_amount .
                $serverKey
        );

        if ($hashed !== $request->signature_key) {
            Log::warning('Signature tidak valid');
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $sale = Sale::where('invoice_number', $request->order_id)->first();

        if (!$sale) {
            Log::error('Sale tidak ditemukan untuk invoice: ' . $request->order_id);
            return response()->json(['message' => 'Sale not found'], 404);
        }

        $transactionStatus = $request->transaction_status;

        if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
            $sale->status = 'paid';
        } elseif ($transactionStatus === 'pending') {
            $sale->status = 'pending';
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $sale->status = 'failed';
        }

        $sale->save();

        Log::info("Transaksi untuk invoice {$sale->invoice_number} diupdate ke status: {$sale->status}");

        return response()->json(['message' => 'Callback diterima dan diproses']);
    }

    public function finish(Request $request)
    {
        return redirect()->route('bukti.index')->with('success', 'Transaksi selesai! Terima kasih telah berbelanja.');
    }
    public function failure(Request $request)
    {
        return redirect()->route('checkout.failure', ['invoice' => $request->invoice])
            ->with('error', 'Pembayaran gagal. Silakan coba lagi.');
    }

    public function pending(Request $request)
    {
        return redirect()->route('checkout.pending', ['invoice' => $request->invoice])
            ->with('warning', 'Pembayaran masih dalam proses. Silakan tunggu.');
    }
    public function unfinish(Request $request)
    {
        return redirect()->route('checkout.transactions')
            ->with('info', 'Transaksi belum selesai. Silakan lanjutkan pembayaran.');
    }
    public function error(Request $request)
    {
        return redirect()->route('checkout.transactions')
            ->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
    }
}
