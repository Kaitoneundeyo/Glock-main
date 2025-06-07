<?php

namespace App\Livewire;

use App\Models\Produk;
use App\Models\Stok_masuk;
use App\Models\Stok_masuk_item;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceDetailComponent extends Component
{
    use WithPagination;
    public $stokMasukId;
    public $stokMasuk;
    public $items = [];

    // Form input
    public $produk_id, $jumlah, $harga_beli, $expired_at;

    public $produkList = [];
    public $invoice;
    public $editItemId = null;

    public function mount($id)
    {
        $this->stokMasukId = $id;
        $this->invoice = Stok_masuk::findOrFail($id);
        $this->produkList = Produk::all();
        $this->loadItems();
    }

    public function rules()
    {
        return [
            'produk_id' => 'required|exists:produk,id',
            'jumlah' => 'required|numeric|min:1',
            'harga_beli' => 'required|numeric|min:0',
            'expired_at' => 'required|date',
        ];
    }

    public function store()
    {
        $data = $this->validate();

        if ($this->editItemId) {
            $item = Stok_masuk_item::findOrFail($this->editItemId);
            $oldJumlah = $item->jumlah;

            $item->update([
                'produk_id' => $data['produk_id'],
                'jumlah' => $data['jumlah'],
                'harga_beli' => $data['harga_beli'],
                'expired_at' => $data['expired_at'],
            ]);

            // Update stok produk (kurangi dulu stok lama, tambahkan stok baru)
            $produk = Produk::find($data['produk_id']);
            $produk->stok = $produk->stok - $oldJumlah + $data['jumlah'];
            $produk->save();

            $this->editItemId = null;
        } else {
            $item = Stok_masuk_item::create([
                'stok_masuk_id' => $this->stokMasukId,
                'produk_id' => $data['produk_id'],
                'jumlah' => $data['jumlah'],
                'harga_beli' => $data['harga_beli'],
                'expired_at' => $data['expired_at'],
            ]);

            // Tambahkan stok ke produk
            $produk = Produk::find($data['produk_id']);
            $produk->stok += $data['jumlah'];
            $produk->save();

            $hpp = Stok_masuk_item::where('produk_id', $item->produk_id)
                ->selectRaw('SUM(jumlah * harga_beli) / SUM(jumlah) as hpp')
                ->value('hpp');

            logger()->info("HPP Produk ID {$item->produk_id} = {$hpp}");
        }

        $this->resetForm();
        $this->loadItems();
        session()->flash('message', 'Item berhasil disimpan.');
    }

    public function editItem($id)
    {
        $item = Stok_masuk_item::findOrFail($id);
        $this->editItemId = $item->id;
        $this->produk_id = $item->produk_id;
        $this->jumlah = $item->jumlah;
        $this->harga_beli = $item->harga_beli;
        $this->expired_at = $item->expired_at;

        $hpp = Stok_masuk_item::where('produk_id', $item->produk_id)
            ->selectRaw('SUM(jumlah * harga_beli) / SUM(jumlah) as hpp')
            ->value('hpp');

        logger()->info("HPP Produk ID {$item->produk_id} = {$hpp}");

    }

    public function delete($id)
    {
        $item = Stok_masuk_item::findOrFail($id);
        $produk = Produk::find($item->produk_id);

        // Kurangi stok dari produk
        if ($produk) {
            $produk->stok -= $item->jumlah;
            $produk->save();

            $hpp = Stok_masuk_item::where('produk_id', $item->produk_id)
                ->selectRaw('SUM(jumlah * harga_beli) / SUM(jumlah) as hpp')
                ->value('hpp');

            logger()->info("HPP Produk ID {$item->produk_id} = {$hpp}");
        }

        $item->delete();

        $this->loadItems();
        session()->flash('message', 'Item berhasil dihapus.');
    }

    public function loadItems()
    {
        $this->items = Stok_masuk_item::with('produk')
            ->where('stok_masuk_id', $this->stokMasukId)
            ->get();
    }

    public function resetForm()
    {
        $this->produk_id = null;
        $this->jumlah = null;
        $this->harga_beli = null;
        $this->expired_at = null;
    }

    public function render()
    {
        return view('livewire.invoice-detail-component', [
            'items' => $this->items,
        ]);
    }
}
