<?php

namespace App\Livewire;

use App\Models\Cart_item;
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
        $this->cartItems = Cart_item::with(['produk.hargaTerbaru', 'produk.gambarUtama'])
            ->where('user_id', Auth::id())
            ->get();
    }

    public function increment($id)
    {
        $item = Cart_item::find($id);
        if ($item) {
            $item->increment('quantity');
            $this->loadCartItems();
        }
    }

    public function decrement($id)
    {
        $item = Cart_item::find($id);
        if ($item && $item->quantity > 1) {
            $item->decrement('quantity');
            $this->loadCartItems();
        }
    }

    public function removeItem($id)
    {
        Cart_item::find($id)?->delete();
        $this->loadCartItems();
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

    public function render()
    {
        return view('livewire.keranjang-component');
    }
}
