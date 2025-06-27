<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\StockReservation;
use Midtrans\Snap;
use Midtrans\Config;

class MidtransController extends Controller
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
     * Process payment - Create Midtrans transaction
     */
    public function pay($orderNumber)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $order = Order::with(['orderItems.produk'])
            ->where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($order->status !== 'pending_payment') {
            return redirect()->route('checkout.confirmation', $orderNumber)
                ->with('info', 'Pesanan ini sudah diproses.');
        }

        // Check hard reservations are still valid
        $hardReservations = StockReservation::where('user_id', $user->id)
            ->where('type', 'hard')
            ->active()
            ->get();

        if ($hardReservations->isEmpty()) {
            $order->update(['status' => 'cancelled']);
            return redirect()->route('katalog')
                ->with('error', 'Waktu pembayaran habis. Pesanan dibatalkan.');
        }


        try {
            // Prepare transaction details for Midtrans
            $transactionDetails = [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_amount,
            ];

            // Item details
            $itemDetails = [];
            foreach ($order->orderItems as $item) {
                $itemDetails[] = [
                    'id' => $item->produk_id,
                    'price' => (int) $item->price,
                    'quantity' => $item->quantity,
                    'name' => $item->produk->nama_produk,
                ];
            }

            // Customer details
            $customerDetails = [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '08123456789',
            ];

            // Transaction data
            $transactionData = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
                'enabled_payments' => ['credit_card', 'bca_va', 'bni_va', 'bri_va', 'gopay', 'shopeepay'],
                'vtweb' => [],
            ];

            // Get Snap Token
            $snapToken = Snap::getSnapToken($transactionData);

            // Update order with snap token
            $order->update([
                'snap_token' => $snapToken,
                'status' => 'awaiting_payment',
            ]);

            return view('checkout.pay', compact('order', 'snapToken'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Handle Midtrans callback
     */
    public function finish(Request $request)
    {
        $orderNumber = $request->get('order_id');
        $transactionStatus = $request->get('transaction_status');

        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                $this->handleSuccessPayment($order);
                return redirect()->route('checkout.transactions', [
                    'order' => $order->id,
                    'status' => 'success'
                ]);

            case 'pending':
                $order->update(['status' => 'pending_payment']);
                return redirect()->route('checkout.transactions', [
                    'order' => $order->id,
                    'status' => 'pending'
                ]);

            case 'deny':
            case 'expire':
            case 'cancel':
                $this->handleFailedPayment($order);
                return redirect()->route('checkout.transactions', [
                    'order' => $order->id,
                    'status' => 'failed'
                ]);

            default:
                return redirect()->route('checkout.transactions', [
                    'order' => $order->id,
                    'status' => 'unfinish'
                ]);
        }
    }

    public function callback(Request $request)
    {
        Log::info('Midtrans callback:', $request->all());

        $orderNumber = $request->input('order_id');
        $transactionStatus = $request->input('transaction_status');
        $fraudStatus = $request->input('fraud_status');

        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($transactionStatus == 'settlement' && $fraudStatus == 'accept') {
            $order->status = 'paid';
        } elseif ($transactionStatus == 'pending') {
            $order->status = 'pending_payment';
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $order->status = 'failed';
        }

        $order->save();

        return response()->json(['message' => 'Notification received'], 200);
    }
    /**
     * Handle unfinished payment
     */
    public function unfinish(Request $request)
    {
        $orderNumber = $request->get('order_id');
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return view('midtrans.unfinish', compact('order'));
    }

    /**
     * Handle payment error
     */
    public function error(Request $request)
    {
        $orderNumber = $request->get('order_id');
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        $this->handleFailedPayment($order);

        return view('midtrans.error', compact('order'));
    }

    /**
     * Handle successful payment
     */
    private function handleSuccessPayment(Order $order)
    {
        DB::beginTransaction();
        try {
            foreach ($order->orderItems as $item) {
                $produk = $item->produk;

                if ($produk->stok < $item->quantity) {
                    throw new \Exception("Stok {$produk->nama_produk} tidak mencukupi.");
                }

                // Kurangi stok produk
                $produk->decrement('stok', $item->quantity);

                // Hitung HPP berdasarkan stok masuk
                $hpp = DB::table('stok_masuk_items')
                    ->where('produk_id', $item->produk_id)
                    ->selectRaw('SUM(jumlah * harga_beli) / NULLIF(SUM(jumlah), 0) as hpp')
                    ->value('hpp');

                $hpp = round($hpp ?? 0, 2);

                // Simpan HPP ke order item
                $item->update(['hpp' => $hpp]);
            }

            // Hapus hard reservation
            StockReservation::where('user_id', $order->user_id)
                ->where('type', 'hard')
                ->delete();

            // Update order
            $order->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_method' => 'midtrans',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Handle failed payment
     */
    private function handleFailedPayment(Order $order)
    {
        DB::beginTransaction();
        try {
            // Release hard reservations
            StockReservation::where('user_id', $order->user_id)
                ->where('type', 'hard')
                ->delete();

            // Update order status
            $order->update(['status' => 'cancelled']);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
