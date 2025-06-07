<?php

namespace App\Livewire;

use App\Models\HargaProduk;
use App\Models\Produk;
use Livewire\Component;
use Livewire\WithPagination;

class HargaComponent extends Component
{
    use WithPagination;

    public $produk_id, $harga_jual, $harga_promo, $tanggal_mulai_promo, $tanggal_selesai_promo;
    public $produkList = [];
    public $harga_id;
    public $editMode = false;

    // Untuk pencarian
    public $search = '';

    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->produkList = Produk::all();
    }

    public function store()
    {
        $this->validate([
            'produk_id' => 'required|exists:produk,id',
            'harga_jual' => 'required|numeric',
            'harga_promo' => 'nullable|numeric',
            'tanggal_mulai_promo' => 'nullable|date',
            'tanggal_selesai_promo' => 'nullable|date|after_or_equal:tanggal_mulai_promo',
        ]);

        if ($this->editMode) {
            $harga = HargaProduk::findOrFail($this->harga_id);
            $harga->update([
                'produk_id' => $this->produk_id,
                'harga_jual' => $this->harga_jual,
                'harga_promo' => $this->harga_promo,
                'tanggal_mulai_promo' => $this->tanggal_mulai_promo,
                'tanggal_selesai_promo' => $this->tanggal_selesai_promo,
            ]);
            session()->flash('message', 'Harga berhasil diperbarui.');
        } else {
            HargaProduk::create([
                'produk_id' => $this->produk_id,
                'harga_jual' => $this->harga_jual,
                'harga_promo' => $this->harga_promo,
                'tanggal_mulai_promo' => $this->tanggal_mulai_promo,
                'tanggal_selesai_promo' => $this->tanggal_selesai_promo,
            ]);
            session()->flash('message', 'Harga berhasil disimpan.');
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $harga = HargaProduk::findOrFail($id);
        $this->harga_id = $harga->id;
        $this->produk_id = $harga->produk_id;
        $this->harga_jual = $harga->harga_jual;
        $this->harga_promo = $harga->harga_promo;
        $this->tanggal_mulai_promo = $harga->tanggal_mulai_promo;
        $this->tanggal_selesai_promo = $harga->tanggal_selesai_promo;
        $this->editMode = true;
    }

    public function delete($id)
    {
        $harga = HargaProduk::findOrFail($id);
        $harga->delete();
        session()->flash('message', 'Harga berhasil dihapus.');
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['produk_id', 'harga_jual', 'harga_promo', 'tanggal_mulai_promo', 'tanggal_selesai_promo', 'harga_id', 'editMode']);
    }

    public function render()
    {
        $hargaProduks = HargaProduk::with('produk')
            ->when($this->search, function ($query) {
                $query->whereHas('produk', function ($q) {
                    $q->where('nama_produk', 'like', '%' . $this->search . '%');
                })
                    ->orWhere('harga_jual', 'like', '%' . $this->search . '%')
                    ->orWhereDate('tanggal_mulai_promo', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(5);

        return view('livewire.harga-component', [
            'hargaProduks' => $hargaProduks,
        ]);
    }
}
