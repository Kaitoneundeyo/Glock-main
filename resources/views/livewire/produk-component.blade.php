<div class="container">
    {{-- Form Tambah Produk --}}
    <form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
        {{-- Kode Produk --}}
        <div class="mb-3 row align-items-center">
            <label for="kode_produk" class="col-sm-2 col-form-label text-black">Kode Produk</label>
            <div class="col-sm-7">
                <input type="text" wire:model="kode_produk" class="form-control" id="kode_produk">
                @error('kode_produk') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-sm-3 text-end">
                <button type="button" class="btn btn-outline-primary" onclick="startScanner()">Scan Barcode</button>
            </div>
        </div>

        {{-- Kamera Scanner --}}
        <div id="reader" class="mb-3" style="width:100%; max-width: 400px; display:none;"></div>

        {{-- Nama Produk --}}
        <div class="mb-3 row">
            <label for="nama_produk" class="col-sm-2 col-form-label text-black">Nama Produk</label>
            <div class="col-sm-10">
                <input type="text" wire:model="nama_produk" class="form-control" id="nama_produk">
                @error('nama_produk') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        {{-- Merk --}}
        <div class="mb-3 row">
            <label for="merk" class="col-sm-2 col-form-label text-black">Merk</label>
            <div class="col-sm-10">
                <input type="text" wire:model="merk" class="form-control" id="merk">
                @error('merk') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="mb-3 row">
            <label for="tipe" class="col-sm-2 col-form-label text-black">Varian</label>
            <div class="col-sm-10">
                <input type="text" wire:model="tipe" class="form-control" id="tipe">
                @error('tipe') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        {{-- Kategori --}}
        <div class="mb-3 row">
            <label for="kategori" class="col-sm-2 col-form-label text-black">Kategori</label>
            <div class="col-sm-10">
                <select class="form-control" id="kategori" wire:model="categories_id">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $data)
                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                    @endforeach
                </select>
                @error('categories_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        {{-- Varian --}}
        <div class="mb-3 row">
            <label for="berat" class="col-sm-2 col-form-label text-black">Berat (gram)</label>
            <div class="col-sm-10">
                <input type="number" wire:model="berat" class="form-control" id="berat" placeholder="Masukkan berat dalam gram">
                @error('berat') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        {{-- Stok --}}
        <div class="mb-3 row">
            <label for="stok" class="col-sm-2 col-form-label text-black">Stok</label>
            <div class="col-sm-10">
                <input type="number" wire:model="stok" class="form-control" id="stok">
                @error('stok') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        {{-- Tombol Simpan / Update --}}
        <div class="card-footer text-end">
            <div class="offset-sm-2 col-sm-10">
                <button type="submit" class="btn btn-{{ $updateMode ? 'success' : 'primary' }} text-white">
                    {{ $updateMode ? 'Update Produk' : 'Simpan Produk' }}
                </button>
                @if($updateMode)
                    <button type="button" wire:click="resetForm" class="btn btn-outline-secondary">Batal</button>
                @endif
            </div>
        </div>

        {{-- Notifikasi Sukses --}}
        @if (session()->has('message'))
            <div class="alert alert-success mt-3">{{ session('message') }}</div>
        @endif
    </form>

    {{-- Pencarian --}}
    <form wire:submit.prevent="searchProduk">
        <div class="row px-3 mt-4 mb-2 align-items-end">
            <div class="col-md-3 mb-2">
                <input type="text" class="form-control" id="filterKodeProduk" wire:model.defer="filterKodeProduk"
                    placeholder="Cari Kode...">
            </div>
            <div class="col-md-3 mb-2">
                <input type="text" class="form-control" id="filterNamaProduk" wire:model.defer="filterNamaProduk"
                    placeholder="Cari Nama...">
            </div>
            <div class="col-md-3 mb-2">
                <input type="text" class="form-control" id="filterMerk" wire:model.defer="filterMerk"
                    placeholder="Cari Merk...">
            </div>
            <div class="col-md-3 mb-2 d-grid">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>
        </div>
    </form>

    {{-- Tabel Data Produk --}}
    <div class="table-responsive bg-secondary text-nowrap mt-4">
        <table class="table table-bordered text-center text-black">
            <thead class="bg-primary text-white">
                <tr>
                    <th class="d-none d-md-table-cell">No</th>
                    <th class="d-none d-md-table-cell">Kode</th>
                    <th class="d-none d-md-table-cell">Nama</th>
                    <th class="d-none d-md-table-cell">Merk</th>
                    <th class="d-none d-md-table-cell">Kategori</th>
                    <th class="d-none d-md-table-cell">Varian</th>
                    <th class="d-none d-md-table-cell">Berat</th>
                    <th class="d-none d-md-table-cell">Stok</th>
                    <th class="d-none d-md-table-cell">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dataproduk as $key => $value)
                    <tr>
                        <td class="d-none d-md-table-cell">{{ $dataproduk->firstItem() + $key }}</td>
                        <td class="text-truncate">{{ $value->kode_produk }}</td>
                        <td class="text-truncate">{{ $value->nama_produk }}</td>
                        <td class="d-none d-md-table-cell">{{ $value->merk }}</td>
                        <td class="d-none d-md-table-cell">{{ $value->kategori?->name ?? '-' }}</td>
                        <td class="d-none d-md-table-cell">{{ $value->tipe }}</td>
                        <td class="d-none d-md-table-cell">{{ $value->berat }}</td>
                        <td>{{ $value->stok }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button wire:click="edit({{ $value->id }})" class="btn btn-sm btn-warning text-white mb-1"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="delete({{ $value->id }})" class="btn btn-sm btn-danger text-white"
                                    title="Hapus" onclick="return confirm('Yakin ingin menghapus?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="card-footer clearfix mt-3">
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ $dataproduk->previousPageUrl() }}"
                class="btn {{ $dataproduk->onFirstPage() ? 'btn-secondary disabled' : 'btn-primary' }}">
                Previous
            </a>
            <span>Halaman {{ $dataproduk->currentPage() }} dari {{ $dataproduk->lastPage() }}</span>
            <a href="{{ $dataproduk->nextPageUrl() }}"
                class="btn {{ $dataproduk->hasMorePages() ? 'btn-primary' : 'btn-secondary disabled' }}">
                Next
            </a>
        </div>
        <div class="d-flex justify-content-end mt-2">
            {{ $dataproduk->links() }}
        </div>
    </div>
</div>
