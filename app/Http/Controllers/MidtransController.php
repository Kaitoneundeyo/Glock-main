<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        return response()->json(['message' => 'OK'], 200);
        Log::info('=== CALLBACK DEBUGGING ===');
        Log::info('Full request data: ', $request->all());
        Log::info('Transaction Status: ' . $request->transaction_status);
        Log::info('Order ID: ' . $request->order_id);
        Log::info('Signature Key: ' . $request->signature_key);

        $serverKey = config('midtrans.server_key');
        Log::info('Server Key: ' . $serverKey);

        $hashed = hash(
            'sha512',
            $request->order_id .
                $request->status_code .
                $request->gross_amount .
                $serverKey
        );

        Log::info('Generated Hash: ' . $hashed);
        Log::info('Received Signature: ' . $request->signature_key);
        Log::info('Hash Match: ' . ($hashed === $request->signature_key ? 'YES' : 'NO'));

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

        return response()->json(['message' => 'Callback diterima dan diproses'], 200);
    }
}
