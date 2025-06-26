<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Produk;
use App\Models\StokKeluar;
use App\Models\StokKeluarItem;
use Livewire\WithPagination;

class StokKeluarItemComponent extends Component
{
    use WithPagination;

    public $stokKeluarId;
    public $stokKeluar;
    public $items = [];

    public $produk_id, $jumlah, $harga_jual;
    public $produkList = [];
    public $editItemId = null;

    public function mount($id)
    {
        $this->stokKeluarId = $id;
        $this->stokKeluar = StokKeluar::findOrFail($id);
        $this->produkList = Produk::all();
        $this->loadItems();
    }

    public function rules()
    {
        return [
            'produk_id' => 'required|exists:produk,id',
            'jumlah' => 'required|numeric|min:1',
            'harga_jual' => 'required|numeric|min:0',
        ];
    }

    public function store()
    {
        $data = $this->validate();

        if ($this->editItemId) {
            $item = StokKeluarItem::findOrFail($this->editItemId);
            $oldJumlah = $item->jumlah;

            $item->update($data);

            // Update stok produk (kembalikan stok lama, kurangi stok baru)
            $produk = Produk::find($data['produk_id']);
            $produk->stok += $oldJumlah;       // balikin dulu
            $produk->stok -= $data['jumlah'];  // kurangi stok baru
            $produk->save();

            $this->editItemId = null;
        } else {
            StokKeluarItem::create([
                'stok_keluar_id' => $this->stokKeluarId,
                'produk_id' => $data['produk_id'],
                'jumlah' => $data['jumlah'],
                'harga_jual' => $data['harga_jual'],
            ]);

            // Kurangi stok dari produk
            $produk = Produk::find($data['produk_id']);
            $produk->stok -= $data['jumlah'];
            $produk->save();
        }

        $this->resetForm();
        $this->loadItems();
        session()->flash('message', 'Item berhasil disimpan.');
    }

    public function editItem($id)
    {
        $item = StokKeluarItem::findOrFail($id);
        $this->editItemId = $item->id;
        $this->produk_id = $item->produk_id;
        $this->jumlah = $item->jumlah;
        $this->harga_jual = $item->harga_jual;
    }

    public function delete($id)
    {
        $item = StokKeluarItem::findOrFail($id);
        $produk = Produk::find($item->produk_id);

        if ($produk) {
            $produk->stok += $item->jumlah;
            $produk->save();
        }

        $item->delete();

        $this->loadItems();
        session()->flash('message', 'Item berhasil dihapus.');
    }

    public function loadItems()
    {
        $this->items = StokKeluarItem::with('produk')
            ->where('stok_keluar_id', $this->stokKeluarId)
            ->get();
    }

    public function resetForm()
    {
        $this->produk_id = null;
        $this->jumlah = null;
        $this->harga_jual = null;
    }

    public function render()
    {
        return view('livewire.stok-keluar-item-component', [
            'items' => $this->items,
        ]);
    }
}
