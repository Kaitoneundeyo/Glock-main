<?php

namespace App\Livewire;

use App\Models\Cart_item;
use App\Models\StockReservation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class KeranjangComponent extends Component
{
    public $cartItems;


    public function mount()
    {
        $this->loadCartItems();
    }

    public function loadCartItems()
    {
        // Clean expired reservations first
        StockReservation::cleanExpiredReservations();

        $this->cartItems = Cart_item::with(['produk.hargaTerbaru', 'produk.gambarUtama'])
            ->where('user_id', Auth::id())
            ->get();
    }

    /**
     * Get available stock for a product (excluding current user's reservation)
     */
    public function getAvailableStockForIncrement($produk_id)
    {
        $user_id = Auth::id();

        // Get current user's soft reservation
        $userReservation = StockReservation::where('user_id', $user_id)
            ->where('produk_id', $produk_id)
            ->where('type', 'soft')
            ->active()
            ->first();

        $userReservedQty = $userReservation ? $userReservation->quantity : 0;

        // Get total reserved by others
        $othersReserved = StockReservation::where('produk_id', $produk_id)
            ->where('user_id', '!=', $user_id)
            ->active()
            ->sum('quantity');

        $produk = \App\Models\Produk::find($produk_id);
        if (!$produk) return 0;

        // Available = Total Stock - Others Reserved
        return max(0, $produk->stok - $othersReserved);
    }

    public function increment($id)
    {
        $item = Cart_item::find($id);
        if (!$item) return;

        $availableStock = $this->getAvailableStockForIncrement($item->produk_id);
        $currentCartQty = $item->quantity;

        if ($currentCartQty >= $availableStock) {
            session()->flash('error', 'Stok tidak mencukupi untuk menambah jumlah.');
            return;
        }

        // Update soft reservation
        $reservation = StockReservation::createSoftReservation(
            Auth::id(),
            $item->produk_id,
            1, // increment by 1
            session()->getId()
        );

        if ($reservation) {
            $item->increment('quantity');
            session()->flash('success', 'Jumlah produk berhasil ditambah.');
        } else {
            session()->flash('error', 'Gagal menambah jumlah. Stok mungkin sudah habis.');
        }

        $this->loadCartItems();
    }

    public function decrement($id)
    {
        $item = Cart_item::find($id);
        if (!$item || $item->quantity <= 1) return;

        // Find user's soft reservation
        $reservation = StockReservation::where('user_id', Auth::id())
            ->where('produk_id', $item->produk_id)
            ->where('type', 'soft')
            ->active()
            ->first();

        if ($reservation && $reservation->quantity > 1) {
            // Decrease reservation quantity
            $reservation->update([
                'quantity' => $reservation->quantity - 1,
                'expires_at' => now()->addMinutes(30) // Extend expiry
            ]);

            $item->decrement('quantity');
            session()->flash('success', 'Jumlah produk berhasil dikurangi.');
        }

        $this->loadCartItems();
    }

    public function removeItem($id)
    {
        $item = Cart_item::find($id);
        if (!$item) return;

        // Remove soft reservation
        StockReservation::where('user_id', Auth::id())
            ->where('produk_id', $item->produk_id)
            ->where('type', 'soft')
            ->delete();

        // Remove cart item
        $item->delete();

        session()->flash('success', 'Produk berhasil dihapus dari keranjang.');
        $this->loadCartItems();
    }

    /**
     * Get real-time available stock for display
     */
    public function getDisplayStock($produk_id)
    {
        return StockReservation::getAvailableStock($produk_id);
    }

    public function getTotalProperty()
    {
        return $this->cartItems->sum(function ($item) {
            $harga = $item->produk->hargaTerbaru?->harga_promo > 0
                ? $item->produk->hargaTerbaru->harga_promo
                : $item->produk->hargaTerbaru->harga_jual ?? 0;
            return $harga * $item->quantity;
        });
    }

    /**
     * Validate stock before checkout
     */
    public function validateStockBeforeCheckout()
    {
        $errors = [];

        foreach ($this->cartItems as $item) {
            $availableStock = $this->getDisplayStock($item->produk_id);
            $userReservation = StockReservation::where('user_id', Auth::id())
                ->where('produk_id', $item->produk_id)
                ->where('type', 'soft')
                ->active()
                ->first();

            $reservedQty = $userReservation ? $userReservation->quantity : 0;

            if ($item->quantity > $reservedQty || $reservedQty > $availableStock) {
                $errors[] = "Stok {$item->produk->nama_produk} tidak mencukupi.";
            }
        }

        return $errors;
    }

    public function render()
    {
        return view('livewire.keranjang-component');
    }
}
