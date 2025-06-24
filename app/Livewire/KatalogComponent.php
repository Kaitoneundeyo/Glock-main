<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Produk;
use App\Models\Cart_item;
use App\Models\GambarProduk;
use App\Models\StockReservation;
use Livewire\WithPagination;

class KatalogComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedProduk;
    public $quantity = 1;
    public $availableStock = 0;
    public $showModal = false;

    protected $paginationTheme = 'tailwind';
    public $sortDirection = 'asc'; // default A-Z

    public function toggleSortDirection()
    {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    }

    public function searchProduk()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function getAvailableStock($produk_id)
    {
        return StockReservation::getAvailableStock($produk_id);
    }

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

    public function updatedQuantity()
    {
        if ($this->selectedProduk) {
            $this->availableStock = $this->getAvailableStock($this->selectedProduk->id);
        }
    }

    public function confirmAddToCart()
    {
        $user = Auth::user();
        if (!$user || !$this->selectedProduk || $this->quantity <= 0) return;

        $produk = $this->selectedProduk;
        $availableStock = $this->getAvailableStock($produk->id);

        if ($this->quantity > $availableStock) return;

        $reservation = StockReservation::createSoftReservation(
            $user->id,
            $produk->id,
            $this->quantity,
            session()->getId()
        );

        if (!$reservation) return;

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
        flash()->success('Produk berhasil masuk keranjang!');
    }

    public function closeModal()
    {
        $this->reset(['showModal', 'selectedProduk', 'quantity', 'availableStock']);
    }

    public function render()
    {
        $produks = Produk::with(['kategori', 'hargaTerbaru', 'gambarUtama'])
            ->where('nama_produk', 'like', '%' . $this->search . '%')
            ->orderBy('nama_produk', $this->sortDirection) // ← pakai arah sortir dari properti
            ->paginate(20);

        return view('livewire.katalog-component', [
            'produks' => $produks,
        ]);
    }
}
