<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Validation\Rule;
use App\Models\StokKeluar;

class StokKeluarComponent extends Component
{
    public $no_keluar;
    public $tanggal_keluar;
    public $jenis;

    public $stokKeluarId = null;

    public function mount()
    {
        $this->tanggal_keluar = now()->timezone('Asia/Makassar')->format('Y-m-d\TH:i');
    }

    protected function rules()
    {
        return [
            'no_keluar' => ['required', 'string'],
            'tanggal_keluar' => ['required', 'date'],
            'jenis' => ['required', Rule::in(['expired', 'rusak'])],
        ];
    }

    public function getJenisOptionsProperty()
    {
        return ['expired', 'rusak'];
    }

    public function save()
    {
        $this->validate();

        if ($this->stokKeluarId) {
            // update
            $stok = StokKeluar::findOrFail($this->stokKeluarId);
            $stok->update([
                'no_keluar' => $this->no_keluar,
                'tanggal_keluar' => $this->tanggal_keluar,
                'jenis' => $this->jenis,
            ]);
            session()->flash('message', 'Data berhasil diperbarui.');
        } else {
            // store
            StokKeluar::create([
                'no_keluar' => $this->no_keluar,
                'tanggal_keluar' => $this->tanggal_keluar,
                'jenis' => $this->jenis,
            ]);
            session()->flash('message', 'Data berhasil disimpan.');
        }

        $this->resetForm();
    }

    public function editItem($id)
    {
        $stok = StokKeluar::findOrFail($id);
        $this->stokKeluarId = $stok->id;
        $this->no_keluar = $stok->no_keluar;
        $this->tanggal_keluar = \Carbon\Carbon::parse($stok->tanggal_keluar)->format('Y-m-d\TH:i');
        $this->jenis = $stok->jenis;
    }

    public function hapusItem($id)
    {
        $stok = StokKeluar::findOrFail($id);
        $stok->delete();
        session()->flash('message', 'Data berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->reset(['no_keluar', 'tanggal_keluar', 'jenis', 'stokKeluarId']);
        $this->tanggal_keluar = now()->timezone('Asia/Makassar')->format('Y-m-d\TH:i');
    }

    public function render()
    {
        $items = StokKeluar::orderBy('tanggal_keluar', 'desc')->get();
        return view('livewire.stok-keluar-component', compact('items'));
    }
}
