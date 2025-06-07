<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Supplier;
use Livewire\WithPagination;

class SupplierComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $nama_supplier, $alamat, $kontak, $supplier_id;
    public $isEdit = false;


    protected $rules = [
        'nama_supplier' => 'required|string|max:255',
        'alamat' => 'nullable|string',
        'kontak' => 'nullable|string|max:255',
    ];

    public function resetForm()
    {
        $this->nama_supplier = '';
        $this->alamat = '';
        $this->kontak = '';
        $this->supplier_id = null;
        $this->isEdit = false;
    }
    public function store()
    {
        $this->validate();

        Supplier::create([
            'nama_supplier' => $this->nama_supplier,
            'alamat' => $this->alamat,
            'kontak' => $this->kontak,
        ]);

        session()->flash('message', 'Data supplier berhasil ditambahkan.');
        $this->resetForm();
    }
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->supplier_id = $supplier->id;
        $this->nama_supplier = $supplier->nama_supplier;
        $this->alamat = $supplier->alamat;
        $this->kontak = $supplier->kontak;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate();

        $supplier = Supplier::findOrFail($this->supplier_id);
        $supplier->update([
            'nama_supplier' => $this->nama_supplier,
            'alamat' => $this->alamat,
            'kontak' => $this->kontak,
        ]);

        session()->flash('message', 'Data supplier berhasil diperbarui.');
        $this->resetForm();
    }

    public function delete($id)
    {
        Supplier::destroy($id);
        session()->flash('message', 'Data supplier berhasil dihapus.');
    }

    public function searchSupplier()
    {
        $this->resetPage();
    }

    public function render()
    {
        $suppliers = Supplier::where('nama_supplier', 'like', '%' . $this->search . '%')
            ->orWhere('kontak', 'like', '%' . $this->search . '%')
            ->orderBy('nama_supplier')
            ->paginate(10);

        return view('livewire.supplier-component', compact('suppliers'));
    }
}
