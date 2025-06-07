<?php

namespace App\Livewire;

use App\Models\Produk;
use App\Models\GambarProduk;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class GambarComponent extends Component
{
    use WithFileUploads;

    public $produk_id;
    public $gambar_utama, $gambar1, $gambar2;

    public $gambarUtamaPath, $gambar1Path, $gambar2Path;

    public $produk = [];

    public $editMode = false;
    public $editIds = [];

    protected $rules = [
        'produk_id' => 'required|exists:produk,id',
        'gambar_utama' => 'nullable|image|max:5120',
        'gambar1' => 'nullable|image|max:5120',
        'gambar2' => 'nullable|image|max:5120',
    ];

    public function mount()
    {
        $this->produk = Produk::all();
    }

    public function store()
    {
        $this->validate();

        if (!Storage::disk('public')->exists('gambar_produk')) {
            Storage::disk('public')->makeDirectory('gambar_produk');
        }

        if ($this->gambar_utama) {
            $path = $this->gambar_utama->store('gambar_produk', 'public');
            GambarProduk::create([
                'produk_id' => $this->produk_id,
                'path' => $path,
                'is_utama' => 1,
            ]);
        }

        if ($this->gambar1) {
            $path = $this->gambar1->store('gambar_produk', 'public');
            GambarProduk::create([
                'produk_id' => $this->produk_id,
                'path' => $path,
                'is_utama' => 0,
            ]);
        }

        if ($this->gambar2) {
            $path = $this->gambar2->store('gambar_produk', 'public');
            GambarProduk::create([
                'produk_id' => $this->produk_id,
                'path' => $path,
                'is_utama' => 0,
            ]);
        }

        $this->resetForm();
        session()->flash('message', 'Gambar berhasil diunggah.');
    }

    public function edit($id)
    {
        $gambar = GambarProduk::with('produk')->where('produk_id', $gambar = GambarProduk::find($id)->produk_id)->get();

        $this->resetForm();
        $this->editMode = true;
        $this->produk_id = $gambar->first()->produk_id;

        foreach ($gambar as $item) {
            $this->editIds[] = $item->id;
            if ($item->is_utama) {
                $this->gambarUtamaPath = $item->path;
            } elseif (empty($this->gambar1Path)) {
                $this->gambar1Path = $item->path;
            } else {
                $this->gambar2Path = $item->path;
            }
        }
    }

    public function update()
    {
        $this->validate();

        foreach ($this->editIds as $id) {
            $gambar = GambarProduk::find($id);
            if (!$gambar) continue;

            // Gambar Utama
            if ($gambar->is_utama && $this->gambar_utama) {
                Storage::disk('public')->delete($gambar->path);
                $gambar->path = $this->gambar_utama->store('gambar_produk', 'public');
                $gambar->save();
            }

            // Gambar1
            if (!$gambar->is_utama && $gambar->path === $this->gambar1Path && $this->gambar1) {
                Storage::disk('public')->delete($gambar->path);
                $gambar->path = $this->gambar1->store('gambar_produk', 'public');
                $gambar->save();
            }

            // Gambar2
            if (!$gambar->is_utama && $gambar->path === $this->gambar2Path && $this->gambar2) {
                Storage::disk('public')->delete($gambar->path);
                $gambar->path = $this->gambar2->store('gambar_produk', 'public');
                $gambar->save();
            }
        }

        $this->resetForm();
        session()->flash('message', 'Gambar berhasil diperbarui.');
    }

    public function delete($id)
    {
        $gambar = GambarProduk::findOrFail($id);
        if (Storage::disk('public')->exists($gambar->path)) {
            Storage::disk('public')->delete($gambar->path);
        }
        $gambar->delete();

        session()->flash('message', 'Gambar berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->reset([
            'produk_id',
            'gambar_utama',
            'gambar1',
            'gambar2',
            'gambarUtamaPath',
            'gambar1Path',
            'gambar2Path',
            'editMode',
            'editIds'
        ]);
    }

    public function render()
    {
        $gambarList = GambarProduk::with('produk')->orderBy('created_at', 'desc')->get();

        return view('livewire.gambar-component', [
            'gambarList' => $gambarList,
            'produk' => $this->produk,
            'gambarUtamaPath' => $this->gambarUtamaPath,
            'gambar1Path' => $this->gambar1Path,
            'gambar2Path' => $this->gambar2Path,
        ]);
    }
}
