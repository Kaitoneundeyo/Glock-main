<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Produk;
use Livewire\Component;
use Livewire\WithPagination;

class ProdukComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $categories;
    public $produk_id;
    public $kode_produk, $nama_produk, $merk, $tipe, $berat, $categories_id, $stok;
    public $updateMode = false;
    protected $listeners = ['kodeProdukScanned'];

    public $filterKodeProduk;
    public $filterNamaProduk;
    public $filterMerk;
    public function mount()
    {
        $this->categories = Category::all();
    }

    public function rules()
    {
        return [
            'kode_produk'    => 'required|unique:produk,kode_produk,' . $this->produk_id,
            'nama_produk'    => 'required',
            'merk'           => 'nullable',
            'categories_id'  => 'required',
            'tipe'           => 'nullable',
            'berat'          => 'nullable|numeric',
            'stok'           => 'required|integer',
        ];
    }

    public function kodeProdukScanned($value)
    {
        $this->kode_produk = $value;
    }

    public function store()
    {
        $this->produk_id = 'NULL'; // untuk validasi unique saat store
        $validated = $this->validate();

        Produk::create($validated);

        session()->flash('message', 'Produk berhasil disimpan');
        $this->resetForm();
    }

    public function edit($id)
    {
        $dataproduk = Produk::findOrFail($id);

        $this->produk_id     = $dataproduk->id;
        $this->kode_produk   = $dataproduk->kode_produk;
        $this->nama_produk   = $dataproduk->nama_produk;
        $this->merk          = $dataproduk->merk;
        $this->categories_id = $dataproduk->categories_id;
        $this->tipe          = $dataproduk->tipe;
        $this->berat         = $dataproduk->berat;
        $this->stok          = $dataproduk->stok;

        $this->updateMode = true;
    }

    public function update()
    {
        $validated = $this->validate();

        $dataproduk = Produk::findOrFail($this->produk_id);
        $dataproduk->update($validated);

        session()->flash('message', 'Produk berhasil diperbarui');
        $this->resetForm();
    }

    public function delete($id)
    {
        Produk::findOrFail($id)->delete();
        session()->flash('message', 'Produk berhasil dihapus');
    }

    public function searchProduk()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->reset([
            'produk_id', 'kode_produk', 'nama_produk', 'merk',
            'categories_id', 'tipe', 'berat', 'stok'
        ]);
        $this->updateMode = false;
    }

    public function render()
    {
        $dataproduk = Produk::query()
            ->when($this->filterKodeProduk, fn($q) => $q->where('kode_produk', 'like', '%' . $this->filterKodeProduk . '%'))
            ->when($this->filterNamaProduk, fn($q) => $q->where('nama_produk', 'like', '%' . $this->filterNamaProduk . '%'))
            ->when($this->filterMerk, fn($q) => $q->where('merk', 'like', '%' . $this->filterMerk . '%'))
            ->orderBy('nama_produk', 'asc')
            ->paginate(10); // atau ubah ke 5 jika ingin 5 per halaman

        return view('livewire.produk-component', [
            'dataproduk' => $dataproduk,
        ]);
    }
}
