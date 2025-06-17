<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\StockReservation;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransWebhookController extends Controller
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Handle Midtrans notification webhook
     */
    public function notification(Request $request)
    {
        try {
            $notification = new Notification();

            $orderNumber = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;
            $paymentType = $notification->payment_type;
            $grossAmount = $notification->gross_amount;
            $statusCode = $notification->status_code;
            $signatureKey = $notification->signature_key;

            // Validasi Signature Key
            $expectedSignature = hash('sha512', $orderNumber . $statusCode . $grossAmount . config('midtrans.server_key'));

            if ($signatureKey !== $expectedSignature) {
                Log::warning('Invalid signature key in webhook', [
                    'order_id' => $orderNumber,
                    'provided_signature' => $signatureKey,
                    'expected_signature' => $expectedSignature
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid signature'
                ], 403);
            }

            Log::info('Midtrans Webhook Received', [
                'order_id' => $orderNumber,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'payment_type' => $paymentType,
                'gross_amount' => $grossAmount,
            ]);

            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                Log::warning("Order not found for webhook: $orderNumber");
                // Tetap return 200 agar Midtrans tidak retry terus
                return response()->json(['message' => 'Order not found (ignored)'], 200);
            }

            // Jangan hentikan proses hanya karena mismatch (log saja)
            if ((int)$grossAmount !== (int)$order->total_amount) {
                Log::warning("Gross amount mismatch", [
                    'order_id' => $orderNumber,
                    'gross_from_midtrans' => $grossAmount,
                    'total_order' => $order->total_amount
                ]);
            }

            $this->processPaymentStatus($order, $transactionStatus, $fraudStatus, $paymentType);

            return response()->json([
                'status' => 'success',
                'message' => 'Notification processed'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Tetap return 200 supaya Midtrans tidak spam webhook terus-menerus
            return response()->json([
                'status' => 'error',
                'message' => 'Handled internally'
            ], 200);
        }
    }
    /**
     * Process payment status from webhook
     */
    private function processPaymentStatus(Order $order, $transactionStatus, $fraudStatus = null, $paymentType = null)
    {
        Log::info('Processing payment status', [
            'order_number' => $order->order_number,
            'current_status' => $order->status,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus
        ]);

        switch ($transactionStatus) {
            case 'capture':
                // For credit card transactions
                if ($fraudStatus == 'accept') {
                    $this->handleSuccessPayment($order, $paymentType);
                } elseif ($fraudStatus == 'challenge') {
                    $this->handleChallengePayment($order);
                } else {
                    $this->handleFailedPayment($order, 'fraud_detected');
                }
                break;

            case 'settlement':
                // Payment successful
                $this->handleSuccessPayment($order, $paymentType);
                break;

            case 'pending':
                // Payment is pending (especially for bank transfer)
                $this->handlePendingPayment($order);
                break;

            case 'deny':
                // Payment denied
                $this->handleFailedPayment($order, 'denied');
                break;

            case 'cancel':
                // Payment cancelled by user
                $this->handleFailedPayment($order, 'cancelled');
                break;

            case 'expire':
                // Payment expired
                $this->handleFailedPayment($order, 'expired');
                break;

            case 'failure':
                // Payment failed
                $this->handleFailedPayment($order, 'failed');
                break;

            default:
                Log::warning('Unknown transaction status', [
                    'order_number' => $order->order_number,
                    'status' => $transactionStatus
                ]);
                break;
        }
    }

    /**
     * Handle successful payment
     */
    private function handleSuccessPayment(Order $order, $paymentType = null)
    {
        // Skip if already processed
        if (in_array($order->status, ['paid', 'processing', 'shipped', 'delivered'])) {
            Log::info('Order already processed', [
                'order_number' => $order->order_number,
                'current_status' => $order->status
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            // Deduct actual stock
            foreach ($order->orderItems as $item) {
                $produk = $item->produk;
                if ($produk->stok < $item->quantity) {
                    throw new \Exception("Stok {$produk->nama_produk} tidak mencukupi.");
                }
                $produk->decrement('stok', $item->quantity);
            }

            // Release hard reservations
            StockReservation::where('user_id', $order->user_id)
                ->where('type', 'hard')
                ->delete();

            // Update order status
            $order->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_method' => $paymentType ?? 'midtrans'
            ]);

            DB::commit();

            Log::info('Payment processed successfully', [
                'order_number' => $order->order_number,
                'payment_type' => $paymentType
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process successful payment', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle pending payment
     */
    private function handlePendingPayment(Order $order)
    {
        if ($order->status === 'pending_payment' || $order->status === 'awaiting_payment') {
            $order->update(['status' => 'awaiting_payment']);

            Log::info('Payment is pending', [
                'order_number' => $order->order_number
            ]);
        }
    }

    /**
     * Handle challenge payment (fraud detection)
     */
    private function handleChallengePayment(Order $order)
    {
        $order->update(['status' => 'payment_review']);

        Log::info('Payment under review (fraud challenge)', [
            'order_number' => $order->order_number
        ]);
    }

    /**
     * Handle failed payment
     */
    private function handleFailedPayment(Order $order, $reason = 'failed')
    {
        // Skip if already cancelled
        if ($order->status === 'cancelled') {
            Log::info('Order already cancelled', [
                'order_number' => $order->order_number
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            // Release hard reservations
            StockReservation::where('user_id', $order->user_id)
                ->where('type', 'hard')
                ->delete();

            // Update order status
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $reason
            ]);

            DB::commit();

            Log::info('Payment failed, order cancelled', [
                'order_number' => $order->order_number,
                'reason' => $reason
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process payment failure', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Manual webhook test endpoint (only for development)
     */
    public function test(Request $request)
    {
        if (!config('app.debug')) {
            abort(404);
        }

        $orderNumber = $request->input('order_id');
        $transactionStatus = $request->input('transaction_status', 'settlement');

        if (!$orderNumber) {
            return response()->json(['error' => 'order_id required'], 400);
        }

        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $this->processPaymentStatus($order, $transactionStatus);

        return response()->json([
            'message' => 'Test webhook processed',
            'order_number' => $orderNumber,
            'status' => $transactionStatus
        ]);
    }
}
