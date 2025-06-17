<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Produk;
use App\Models\Cart_item;
use App\Models\GambarProduk;
use App\Models\StockReservation;


class KatalogComponent extends Component
{
    public $produks;
    public $gambarProduk;
    public $showModal = false;
    public $selectedProduk;
    public $quantity = 1;
    public $availableStock = 0;


    public function mount()
    {
        $this->loadProduk();
        $this->gambarProduk = GambarProduk::all();
    }

    public function loadProduk()
    {
        // Clean expired reservations first
        StockReservation::cleanExpiredReservations();

        $this->produks = Produk::with([
            'kategori',
            'hargaTerbaru',
            'gambarUtama',
        ])->get();
    }

    /**
     * Get real-time available stock for a product
     */
    public function getAvailableStock($produk_id)
    {
        return StockReservation::getAvailableStock($produk_id);
    }

    /**
     * Tampilkan modal untuk memilih jumlah produk
     */
    public function toCart($produk_id)
    {
        $this->selectedProduk = Produk::with('hargaTerbaru')->find($produk_id);

        if (!$this->selectedProduk) {
            session()->flash('error', 'Produk tidak ditemukan.');
            return;
        }

        $this->availableStock = $this->getAvailableStock($produk_id);

        if ($this->availableStock <= 0) {
            session()->flash('error', 'Stok produk habis.');
            return;
        }

        $this->quantity = 1;
        $this->showModal = true;
    }

    /**
     * Update available stock saat quantity berubah di modal
     */
    public function updatedQuantity()
    {
        if ($this->selectedProduk) {
            $this->availableStock = $this->getAvailableStock($this->selectedProduk->id);
        }
    }

    /**
     * Tambahkan produk ke keranjang dengan soft reservation
     */
    public function confirmAddToCart()
    {
        $user = Auth::user();
        if (!$user) {
            // Silakan login terlebih dahulu
            return;
        }

        if (!$this->selectedProduk) {
            // Produk belum dipilih
            return;
        }

        $produk = $this->selectedProduk;

        if ($this->quantity <= 0) {
            // Jumlah harus lebih dari 0
            return;
        }

        $availableStock = $this->getAvailableStock($produk->id);

        if ($this->quantity > $availableStock) {
            // Stok tidak mencukupi
            return;
        }

        $reservation = StockReservation::createSoftReservation(
            $user->id,
            $produk->id,
            $this->quantity,
            session()->getId()
        );

        if (!$reservation) {
            // Gagal mereservasi stok
            return;
        }

        $cartItem = Cart_item::where('user_id', $user->id)
            ->where('produk_id', $produk->id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $this->quantity);
        } else {
            Cart_item::create([
                'user_id' => $user->id,
                'produk_id' => $produk->id,
                'quantity' => $this->quantity,
            ]);
        }

        $this->reset(['showModal', 'selectedProduk', 'quantity', 'availableStock']);
        $this->loadProduk();
    }


    /**
     * Close modal
     */
    public function closeModal()
    {
        $this->reset(['showModal', 'selectedProduk', 'quantity', 'availableStock']);
    }

    public function render()
    {
        return view('livewire.katalog-component');
    }
}
