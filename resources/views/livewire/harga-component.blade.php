<div class="overflow-x-auto bg-white shadow-md rounded p-4">
    <div class="row justify-content-center">
        <div class="col-sm-10">
            <!-- Judul -->
            <div class="mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">
                    {{ $editMode ? 'Edit Harga Produk' : 'Tambah Harga Produk' }}
                </h2>
            </div>

            <!-- Form Input -->
            <form wire:submit.prevent="{{ $editMode ? 'update' : 'store' }}">
                <div class="mb-3 row">
                    <label for="produk_id" class="col-sm-2 col-form-label text-black">Produk</label>
                    <div class="col-sm-10">
                        <select id="produk_id" wire:model="produk_id" class="form-control">
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($produkList as $produk)
                                <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                        @error('produk_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="harga_jual" class="col-sm-2 col-form-label text-black">Harga Jual</label>
                    <div class="col-sm-10">
                        <input wire:model="harga_jual" type="number" class="form-control"
                            placeholder="Isi harga jual...">
                        @error('harga_jual') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="harga_promo" class="col-sm-2 col-form-label text-black">Harga Promo</label>
                    <div class="col-sm-10">
                        <input wire:model="harga_promo" type="number" class="form-control"
                            placeholder="Opsional harga promo...">
                        @error('harga_promo') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="tanggal_mulai_promo" class="col-sm-2 col-form-label text-black">Mulai Promo</label>
                    <div class="col-sm-10">
                        <input wire:model="tanggal_mulai_promo" type="date" class="form-control">
                        @error('tanggal_mulai_promo') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="mb-4 row">
                    <label for="tanggal_selesai_promo" class="col-sm-2 col-form-label text-black">Akhir Promo</label>
                    <div class="col-sm-10">
                        <input wire:model="tanggal_selesai_promo" type="date" class="form-control">
                        @error('tanggal_selesai_promo') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        {{ $editMode ? 'Perbarui' : 'Simpan' }}
                    </button>
                    @if ($editMode)
                        <button type="button" wire:click="cancelEdit" class="btn btn-secondary">Batal</button>
                    @endif
                </div>
            </form>

            <!-- Notifikasi -->
            @if (session()->has('message'))
                <div class="alert alert-success mt-3">
                    {{ session('message') }}
                </div>
            @endif

            <!-- Pencarian -->
            <div class="card-body d-flex flex-wrap mt-4 mb-3 gap-3 align-items-center">
                <div class="col-sm-10">
                    <input type="text" class="form-control" wire:model.debounce.500ms="search"
                        placeholder="Cari produk dan harga jual...">
                </div>
            </div>

            <!-- Tabel -->
            <div class="table-responsive mt-3">
                <table class="table table-bordered text-center text-black">
                    <thead class="bg-blue-400 text-white">
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Harga Jual</th>
                            <th>Harga Promo</th>
                            <th>Awal Promo</th>
                            <th>Akhir Promo</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hargaProduks as $index => $harga)
                            <tr>
                                <td>{{ $hargaProduks->firstItem() + $index }}</td>
                                <td>{{ $harga->produk->nama_produk ?? '-' }}</td>
                                <td>Rp{{ number_format($harga->harga_jual, 0, ',', '.') }}</td>
                                <td>
                                    @if ($harga->harga_promo)
                                        Rp{{ number_format($harga->harga_promo, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $harga->tanggal_mulai_promo ?? '-' }}</td>
                                <td>{{ $harga->tanggal_selesai_promo ?? '-' }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button wire:click="edit({{ $harga->id }})"
                                            class="btn btn-sm btn-warning text-white" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="delete({{ $harga->id }})"
                                            class="btn btn-sm btn-danger text-white" title="Hapus"
                                            onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">Tidak ada data harga ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginasi -->
            <div class="card-footer clearfix mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ $hargaProduks->previousPageUrl() }}"
                        class="btn {{ $hargaProduks->onFirstPage() ? 'btn-secondary disabled' : 'btn-primary' }}">
                        Previous
                    </a>
                    <span>Halaman {{ $hargaProduks->currentPage() }} dari {{ $hargaProduks->lastPage() }}</span>
                    <a href="{{ $hargaProduks->nextPageUrl() }}"
                        class="btn {{ $hargaProduks->hasMorePages() ? 'btn-primary' : 'btn-secondary disabled' }}">
                        Next
                    </a>
                </div>
                <div class="d-flex justify-content-end mt-2">
                    {{ $hargaProduks->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
