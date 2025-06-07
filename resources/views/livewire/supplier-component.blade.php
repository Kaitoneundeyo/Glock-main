<div class="card">
    <div class="card-body">
        {{-- Container --}}
        <div class="container-fluid">
            {{-- Form --}}
            <div class="row justify-content-center">
                <div class="col-sm-10">
                    <div class="bg-transparent card-rounded p-1 mb-3 row">
                        <h2 class="text-2xl font-semibold text-gray-800">Tambah Supplier</h2>
                    </div>
                    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                        <div class="mb-3 row">
                            <label for="nama_supplier" class="col-sm-2 col-form-label text-black">Nama Supplier</label>
                            <div class="col-sm-10">
                                <input wire:model="nama_supplier" type="text" class="form-control"
                                    placeholder="Nama Supplier">
                                @error('nama_supplier') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="alamat" class="col-sm-2 col-form-label text-black">Alamat</label>
                            <div class="col-sm-10">
                                <input wire:model="alamat" type="text" class="form-control" placeholder="Alamat">
                                @error('alamat') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="kontak" class="col-sm-2 col-form-label text-black">Kontak</label>
                            <div class="col-sm-10">
                                <input wire:model="kontak" type="text" class="form-control" placeholder="Kontak">
                                @error('kontak') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="mb-2 float-right">
                            <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Simpan' }}</button>
                            @if($isEdit)
                                <button type="button" class="btn btn-secondary" wire:click="resetForm">Batal</button>
                            @endif
                        </div>
                    </form>

                    @if (session()->has('message'))
                        <div class="alert alert-success mt-2">
                            {{ session('message') }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Search --}}
            <form wire:submit.prevent="searchSupplier">
                <div class="input-group mt-4 mb-3">
                    <input wire:model.defer="search" type="text" class="form-control"
                        placeholder="Cari nama supplier atau kontak...">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>

            {{-- Tabel --}}
            <table class="table table-bordered text-center text-black">
                <thead class="bg-blue-400">
                    <tr>
                        <th>No</th>
                        <th>Nama Supplier</th>
                        <th>Alamat</th>
                        <th>Kontak</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $key => $value)
                        <tr>
                            <td>{{ $suppliers->firstItem() + $key }}</td>
                            <td>{{ $value->nama_supplier }}</td>
                            <td>{{ $value->alamat }}</td>
                            <td>{{ $value->kontak }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <button wire:click="edit({{ $value->id }})"
                                        class="btn btn-sm btn-warning text-white mb-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="delete({{ $value->id }})" class="btn btn-sm btn-danger text-white"
                                        title="Hapus" onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Tidak ada data supplier.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Custom Pagination --}}
    <div class="card-footer clearfix">
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ $suppliers->previousPageUrl() }}"
                class="btn {{ $suppliers->onFirstPage() ? 'btn-secondary disabled' : 'btn-primary' }}">
                Previous
            </a>
            <span>Halaman {{ $suppliers->currentPage() }} dari {{ $suppliers->lastPage() }}</span>
            <a href="{{ $suppliers->nextPageUrl() }}"
                class="btn {{ $suppliers->hasMorePages() ? 'btn-primary' : 'btn-secondary disabled' }}">
                Next
            </a>
        </div>

        {{-- Default Pagination Links --}}
        <div class="d-flex justify-content-end mt-2">
            {{ $suppliers->links() }}
        </div>
    </div>
</div>
