<?php

namespace App\Livewire;

use App\Models\Stok_masuk;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class StokmasukComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $no_invoice, $tanggal_masuk, $supplier_id;
    public $stokmasuk_id;
    public $isEdit = false;

    // Filter pencarian
    public $filterTanggalMasuk;
    public $filterNoInvoice;
    public $filterSupplier;

    // List dropdown
    public $suppliers;

    public function mount()
    {
        $this->suppliers = Supplier::orderBy('nama_supplier')->get();
        $this->tanggal_masuk = Carbon::now()->timezone('Asia/Makassar')->format('d M Y, H:i');
    }

    protected function rules()
    {
        return [
            'no_invoice' => 'required|unique:stok_masuks,no_invoice,' . $this->stokmasuk_id,
            'tanggal_masuk' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
        ];
    }

    public function store()
    {
        $this->validate();

        Stok_masuk::create([
            'no_invoice' => $this->no_invoice,
            'tanggal_masuk' => $this->tanggal_masuk,
            'supplier_id' => $this->supplier_id,
        ]);

        session()->flash('message', 'Data berhasil disimpan.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $stok = Stok_masuk::findOrFail($id);
        $this->stokmasuk_id = $stok->id;
        $this->no_invoice = $stok->no_invoice;
        $this->tanggal_masuk = Carbon::parse($stok->tanggal_masuk)->timezone('Asia/Makassar')->format('d M Y, H:i');
        $this->supplier_id = $stok->supplier_id;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate();

        $stok = Stok_masuk::findOrFail($this->stokmasuk_id);
        $stok->update([
            'no_invoice' => $this->no_invoice,
            'tanggal_masuk' => $this->tanggal_masuk,
            'supplier_id' => $this->supplier_id,
        ]);

        session()->flash('message', 'Data berhasil diperbarui.');
        $this->resetForm();
    }

    public function delete($id)
    {
        Stok_masuk::findOrFail($id)->delete();
        session()->flash('message', 'Data berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->reset(['no_invoice', 'supplier_id', 'stokmasuk_id', 'isEdit']);
        $this->tanggal_masuk = Carbon::now()->timezone('Asia/Makassar')->format('d M Y, H:i');
    }

    public function resetFilter()
    {
        $this->reset(['filterTanggalMasuk', 'filterNoInvoice', 'filterSupplier']);
        $this->resetPage();
    }

    public function getFormActionProperty()
    {
        return $this->isEdit ? 'update' : 'store';
    }

    public function render()
    {
        $query = Stok_masuk::query();

        $query->when($this->filterTanggalMasuk, fn($q) => $q->whereDate('tanggal_masuk', $this->filterTanggalMasuk));
        $query->when($this->filterNoInvoice, fn($q) => $q->where('no_invoice', 'like', '%' . $this->filterNoInvoice . '%'));
        $query->when($this->filterSupplier, fn($q) => $q->where('supplier_id', $this->filterSupplier));

        $stokMasuks = $query->orderBy('no_invoice', 'desc')->paginate(10);

        return view('livewire.stokmasuk-component', [
            'stokMasuks' => $stokMasuks,
            'suppliers' => $this->suppliers,
        ]);
    }
}
