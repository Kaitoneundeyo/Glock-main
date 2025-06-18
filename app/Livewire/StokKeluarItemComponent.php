<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\StokKeluarItem;
use App\Models\StokKeluar; // Add this import

class StokKeluarItemComponent extends Component
{
    public $id;

    public function mount($id)
    {
        $this->id = $id;
    }

    public function render()
    {
        $items = StokKeluarItem::where('stok_keluars_id', $this->id)->get();

        // Add this line to get the StokKeluar data
        $stokKeluar = StokKeluar::findOrFail($this->id);

        return view('livewire.stok-keluar-item-component', [
            'items' => $items,
            'stokKeluar' => $stokKeluar, // Pass stokKeluar to the view
        ]);
    }
}
