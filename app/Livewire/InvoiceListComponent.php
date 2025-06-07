<?php

namespace App\Livewire;

use App\Models\Stok_masuk;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceListComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $filterInvo;
    public $filterTanggalMasuk;

    // Reset pagination saat input berubah
    public function updatingFilterInvo()
    {
        $this->resetPage();
    }

    public function updatingFilterTanggalMasuk()
    {
        $this->resetPage();
    }

    public function searchInvo()
    {
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->reset(['filterInvo', 'filterTanggalMasuk']);
        $this->resetPage();
    }

    public function render()
    {
        $invoices = Stok_masuk::select(
            'no_invoice',
            DB::raw('MIN(id) as id'),
            DB::raw('MIN(tanggal_masuk) as tanggal')
        )
            ->when($this->filterInvo, function ($query) {
                $query->where('no_invoice', 'like', '%' . $this->filterInvo . '%');
            })
            ->when($this->filterTanggalMasuk, function ($query) {
                $query->whereDate('tanggal_masuk', $this->filterTanggalMasuk);
            })
            ->groupBy('no_invoice')
            ->orderByDesc('tanggal')
            ->paginate(10);

        return view('livewire.invoice-list-component', [
            'invoices' => $invoices
        ]);
    }
}
