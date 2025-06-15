<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cart_item;
use App\Models\StockReservation;
use App\Models\Order;
use App\Models\OrderItem;

class CheckoutController extends Controller
{
    /**
     * Process checkout - Convert soft reservations to hard reservations
     */
    public function process(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Get cart items
        $cartItems = Cart_item::with('produk')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Keranjang belanja kosong.');
        }

        // Clean expired reservations
        StockReservation::cleanExpiredReservations();

        $errors = [];
        $hardReservations = [];

        DB::beginTransaction();
        try {
            // Validate and convert each item to hard reservation
            foreach ($cartItems as $item) {
                // Check if soft reservation exists and is sufficient
                $softReservation = StockReservation::where('user_id', $user->id)
                    ->where('produk_id', $item->produk_id)
                    ->where('type', 'soft')
                    ->active()
                    ->first();

                if (!$softReservation || $softReservation->quantity < $item->quantity) {
                    $errors[] = "Reservasi untuk {$item->produk->nama_produk} tidak valid atau sudah kedaluwarsa.";
                    continue;
                }

                // Convert to hard reservation
                $hardReservation = StockReservation::convertToHardReservation(
                    $user->id,
                    $item->produk_id,
                    $item->quantity
                );

                if (!$hardReservation) {
                    $errors[] = "Gagal mengunci stok untuk {$item->produk->nama_produk}.";
                    continue;
                }

                $hardReservations[] = $hardReservation;
            }

            if (!empty($errors)) {
                DB::rollBack();
                return redirect()->back()->with('error', implode(' ', $errors));
            }

            // Create order
            $totalAmount = $cartItems->sum(function ($item) {
                $harga = $item->produk->hargaTerbaru?->harga_promo > 0
                    ? $item->produk->hargaTerbaru->harga_promo
                    : $item->produk->hargaTerbaru->harga_jual ?? 0;
                return $harga * $item->quantity;
            });

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . time() . '-' . $user->id,
                'total_amount' => $totalAmount,
                'status' => 'pending_payment',
                'payment_method' => null,
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                $harga = $item->produk->hargaTerbaru?->harga_promo > 0
                    ? $item->produk->hargaTerbaru->harga_promo
                    : $item->produk->hargaTerbaru->harga_jual ?? 0;

                OrderItem::create([
                    'order_id' => $order->id,
                    'produk_id' => $item->produk_id,
                    'quantity' => $item->quantity,
                    'price' => $harga,
                    'subtotal' => $harga * $item->quantity,
                ]);
            }

            DB::commit();

            // Clear cart items (but keep hard reservations for payment)
            Cart_item::where('user_id', $user->id)->delete();

            // Redirect to confirmation page
            return redirect()->route('checkout.confirmation', ['order' => $order->order_number])
                ->with('success', 'Checkout berhasil! Silakan lanjutkan pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Release any hard reservations that were created
            foreach ($hardReservations as $reservation) {
                $reservation->delete();
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan saat checkout: ' . $e->getMessage());
        }
    }

    /**
     * Show all user transactions
     */
    public function transactions(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Ambil status dan order_id dari query string
        $status = $request->query('status');
        $orderId = $request->query('order');

        $orders = Order::with(['orderItems.produk'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Jika ada order_id, cari order tersebut
        $highlightOrder = null;
        if ($orderId) {
            $highlightOrder = Order::where('id', $orderId)
                ->where('user_id', $user->id)
                ->first();
        }

        return view('checkout.transactions', compact('orders', 'status', 'highlightOrder'));
    }


    /**
     * Show confirmation page
     */
    public function confirmation($orderNumber)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $order = Order::with(['orderItems.produk.gambarUtama', 'orderItems.produk.kategori'])
            ->where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Check if hard reservations are still valid
        if ($order->status === 'pending_payment') {
            $hardReservations = StockReservation::where('user_id', $user->id)
                ->where('type', 'hard')
                ->active()
                ->get();

            if ($hardReservations->isEmpty()) {
                // Hard reservations expired, cancel order
                $order->update(['status' => 'cancelled']);
                return redirect()->route('katalog')
                    ->with('error', 'Waktu pembayaran habis. Pesanan dibatalkan.');
            }
        }

        return view('checkout.confirmation', compact('order'));
    }

    /**
     * Cancel order and release reservations
     */
    public function cancelOrder(Order $order)
    {
        $user = Auth::user();

        if ($order->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if (!in_array($order->status, ['pending_payment', 'awaiting_payment'])) {
            return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan.');
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
                'cancellation_reason' => 'user_cancellation'
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('checkout.transactions')
            ->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
