<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Midtrans Webhook:', $request->all());

        $orderId = $request->input('order_id');
        $transactionStatus = $request->input('transaction_status');

        $sale = Sale::where('invoice_number', $orderId)->first();

        if (!$sale) {
            return response()->json(['message' => 'Sale not found'], 404);
        }

        // Ubah nilai kolom status sesuai notifikasi Midtrans
        switch ($transactionStatus) {
            case 'settlement':
                $sale->status = 'paid';
                break;

            case 'pending':
                $sale->status = 'pending';
                break;

            case 'deny':
            case 'cancel':
            case 'expire':
                $sale->status = 'failed';
                break;
        }

        $sale->save();

        return response()->json(['message' => 'Status pembayaran diperbarui']);
    }
}
