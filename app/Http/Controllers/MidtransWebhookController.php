<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Midtrans Webhook Payload:', $request->all());

        $serverKey = config('midtrans.server_key');
        $signatureKey = $request->input('signature_key');
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $transactionStatus = $request->input('transaction_status');

        // Validasi Signature
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if ($signatureKey !== $expectedSignature) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Temukan sale
        $sale = Sale::where('invoice_number', $orderId)->first();
        if (!$sale) {
            return response()->json(['message' => 'Sale not found'], 404);
        }

        // Jangan timpa status paid
        if ($sale->status === 'paid') {
            return response()->json(['message' => 'Already paid'], 200);
        }

        // Perbarui status
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

        Log::info("Status transaksi {$sale->invoice_number} diperbarui menjadi {$sale->status}");

        return response()->json(['message' => 'Status pembayaran diperbarui']);
    }
}
