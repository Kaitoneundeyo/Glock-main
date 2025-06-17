<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Validation\Rule;
use App\Models\Produk;
use App\Models\HargaProduk;

class StokKeluarComponent extends Component
{
    public $no_keluar;
    public $tanggal_keluar;
    public $jenis;

    // Menyimpan item sementara sebelum disimpan ke DB
    public $items = [];

    public function mount()
    {
        $this->tanggal_keluar = now()->timezone('Asia/Makassar')->format('Y-m-d\TH:i');
    }

    protected function rules()
    {
        return [
            'no_keluar' => ['required', 'string', 'unique:stok_keluars,no_keluar'],
            'tanggal_keluar' => ['required', 'date'],
            'jenis' => ['required', Rule::in(['expired', 'rusak'])],
        ];
    }

    public function getJenisOptionsProperty()
    {
        return ['expired', 'rusak'];
    }

    public function hapusItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); // reset indeks agar tetap urut
    }

    public function detailItem($index)
    {
        $item = $this->items[$index] ?? null;

        if ($item) {
            $this->dispatchBrowserEvent('show-detail-item', ['item' => $item]);
        }
    }

    public function editItem($index)
    {
        $item = $this->items[$index] ?? null;

        if ($item) {
            $this->dispatchBrowserEvent('edit-item', ['index' => $index, 'item' => $item]);
        }
    }

    public function render()
    {
        return view('livewire.stok-keluar-component');
    }
}
