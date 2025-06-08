<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Produk;
use App\Models\Cart_item;
use App\Models\GambarProduk;

class KatalogComponent extends Component
{
    public $produks;
    public $gambarProduk;

    public $showModal = false;
    public $selectedProduk;
    public $quantity = 1;

    public function mount()
    {
        $this->produks = Produk::with([
            'kategori',
            'hargaTerbaru',
            'gambarUtama',
        ])->get();

        $this->gambarProduk = GambarProduk::all();
    }

    /**
     * Tampilkan modal untuk memilih jumlah produk
     */
    public function showAddToCartModal($produk_id)
    {
        $this->selectedProduk = Produk::with('hargaTerbaru')->find($produk_id);
        if (!$this->selectedProduk) {
            session()->flash('error', 'Produk tidak ditemukan.');
            return;
        }

        $this->quantity = 1;
        $this->showModal = true;
    }

    /**
     * Tambahkan produk ke keranjang setelah konfirmasi dari modal
     * Lalu arahkan ke halaman checkout
     */
    public function confirmAddToCart()
    {
        $user = Auth::user();

        if (!$user) {
            session()->flash('error', 'Silakan login terlebih dahulu.');
            return;
        }

        if (!$this->selectedProduk) {
            session()->flash('error', 'Produk belum dipilih.');
            return;
        }

        $produk = $this->selectedProduk;

        // Hitung stok tersedia
        $totalCartQty = Cart_item::where('produk_id', $produk->id)->sum('quantity');
        $stokTersedia = $produk->stok - $totalCartQty;

        if ($this->quantity > $stokTersedia) {
            session()->flash('error', 'Stok tidak mencukupi.');
            return;
        }

        // Tambah atau update item keranjang
        $item = Cart_item::where('user_id', $user->id)
            ->where('produk_id', $produk->id)
            ->first();

        if ($item) {
            $item->quantity += $this->quantity;
            $item->save();
        } else {
            Cart_item::create([
                'user_id' => $user->id,
                'produk_id' => $produk->id,
                'quantity' => $this->quantity,
            ]);
        }

        $this->reset(['showModal', 'selectedProduk', 'quantity']);

        // ✅ Redirect ke route 'coba.index'
        return redirect()->route('coba.index');
    }

    public function render()
    {
        return view('livewire.katalog-component');
    }
}
