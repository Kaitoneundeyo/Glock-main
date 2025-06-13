<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            // ✅ Set konfigurasi Midtrans secara eksplisit
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production', false);
            Config::$isSanitized = config('midtrans.is_sanitized', true);
            Config::$is3ds = config('midtrans.is_3ds', true);

            // ✅ Log payload awal
            Log::info('Webhook payload received', ['payload' => $request->all()]);

            // ✅ PERBAIKAN: Validasi signature secara manual terlebih dahulu
            $serverKey = config('midtrans.server_key');
            $orderId = $request->order_id;
            $statusCode = $request->status_code;
            $grossAmount = $request->gross_amount;
            $signatureKey = $request->signature_key;

            // Buat signature hash untuk validasi
            $mySignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            // Validasi signature
            if ($mySignature !== $signatureKey) {
                Log::warning('Invalid signature', [
                    'expected' => $mySignature,
                    'received' => $signatureKey
                ]);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            // ✅ Buat notifikasi Midtrans setelah signature valid
            $notification = new Notification();

            $transaction = $notification->transaction_status;
            $type = $notification->payment_type;
            $orderId = $notification->order_id;
            $fraud = $notification->fraud_status;

            // ✅ Temukan transaksi berdasarkan invoice
            $sale = Sale::where('invoice_number', $orderId)->first();

            if (!$sale) {
                Log::warning("Sale tidak ditemukan untuk invoice: {$orderId}");
                return response()->json(['message' => 'Sale not found'], 404);
            }

            // ✅ Log status transaksi dari Midtrans
            Log::info('Status transaksi Midtrans', [
                'order_id' => $orderId,
                'transaction_status' => $transaction,
                'payment_type' => $type,
                'fraud_status' => $fraud,
            ]);

            // ✅ Update status transaksi di database
            $oldStatus = $sale->status;

            switch ($transaction) {
                case 'capture':
                    if ($type == 'credit_card') {
                        $sale->status = ($fraud == 'challenge') ? 'challenge' : 'success';
                    }
                    break;

                case 'settlement':
                    $sale->status = 'success';
                    break;

                case 'pending':
                    $sale->status = 'pending';
                    break;

                case 'deny':
                    $sale->status = 'failed';
                    break;

                case 'expire':
                    $sale->status = 'expired';
                    break;

                case 'cancel':
                    $sale->status = 'cancelled';
                    break;

                default:
                    $sale->status = 'pending';
                    break;
            }

            // Simpan perubahan hanya jika status berubah
            if ($oldStatus !== $sale->status) {
                $sale->save();
                Log::info("✅ Status transaksi {$orderId} diperbarui dari {$oldStatus} menjadi: {$sale->status}");
            } else {
                Log::info("ℹ️ Status transaksi {$orderId} tetap: {$sale->status}");
            }

            return response()->json(['message' => 'Webhook received and processed'], 200);
        } catch (\Exception $e) {
            // ✅ Log jika terjadi error saat proses webhook
            Log::error('❌ Gagal memproses webhook Midtrans', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json(['message' => 'Webhook processing failed'], 500);
        }
    }
}
