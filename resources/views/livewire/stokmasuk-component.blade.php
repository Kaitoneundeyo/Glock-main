<div class="card">
    <div class="card-body">
        <div class="container-fluid">
            {{-- Form Tambah/Edit --}}
            <form wire:submit.prevent="{{ $this->formAction }}" class="card-body">
                <div class="mb-3">
                    <label for="no_invoice">No Invoice</label>
                    <input type="text" id="no_invoice" class="form-control" wire:model="no_invoice"
                        placeholder="Isi Invoice disini.....">
                </div>

                <div class="mb-4">
                    <label for="tanggalMasuk" class="form-label">Tanggal & Waktu Masuk</label>
                    <input type="datetime-local" id="tanggalMasuk" wire:model.defer="tanggal_masuk" class="form-control">
                </div>                
                
                <div class="mb-3">
                    <label for="supplier_id">Supplier</label>
                    <select id="supplier_id" class="form-control" wire:model="supplier_id">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2 text-end">
                    <button type="submit" class="btn btn-primary">
                        {{ $isEdit ? 'Update' : 'Simpan' }}
                    </button>
                    @if($isEdit)
                        <button type="button" class="btn btn-secondary" wire:click="resetForm">Batal</button>
                    @endif
                </div>
            </form>

            {{-- Flash Message --}}
            @if (session()->has('message'))
                <div class="alert alert-success mt-2">
                    {{ session('message') }}
                </div>
            @endif

            {{-- Filter & Pencarian --}}
            <form wire:submit.prevent="searchInvoice" class="row mt-4">
                <div class="col-md-3 mb-2">
                    <input type="date" class="form-control" wire:model.defer="filterTanggalMasuk"
                        placeholder="Filter Tanggal Masuk">
                </div>
                <div class="col-md-3 mb-2">
                    <input type="text" class="form-control" wire:model.defer="filterNoInvoice"
                        placeholder="Cari No Invoice">
                </div>
                <div class="col-md-3 mb-2">
                    <select class="form-control" wire:model.defer="filterSupplier">
                        <option value="">Semua Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <button type="button" wire:click="resetFilter" class="btn btn-secondary w-100">Reset</button>
                </div>
            </form>

            {{-- Tabel Data --}}
            <div class="table-responsive mt-4">
                <table class="table table-bordered text-center text-black">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>No</th>
                            <th>Nomor Invoice</th>
                            <th>Tanggal Masuk</th>
                            <th>Nama Supplier</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stokMasuks as $key => $value)
                            <tr>
                                <td>{{ $stokMasuks->firstItem() + $key }}</td>
                                <td>{{ $value->no_invoice }}</td>
                                <td>{{ $value->tanggal_masuk }}</td>
                                <td>{{ $value->supplier->nama_supplier ?? 'Tanpa Penyalur' }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button wire:click="edit({{ $value->id }})"
                                            class="btn btn-sm btn-warning text-white" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="delete({{ $value->id }})"
                                            class="btn btn-sm btn-danger text-white" title="Hapus"
                                            onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">Data tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="card-footer clearfix mt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ $stokMasuks->previousPageUrl() }}"
                            class="btn {{ $stokMasuks->onFirstPage() ? 'btn-secondary disabled' : 'btn-primary' }}">
                            Previous
                        </a>
                        <span>Halaman {{ $stokMasuks->currentPage() }} dari {{ $stokMasuks->lastPage() }}</span>
                        <a href="{{ $stokMasuks->nextPageUrl() }}"
                            class="btn {{ $stokMasuks->hasMorePages() ? 'btn-primary' : 'btn-secondary disabled' }}">
                            Next
                        </a>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        {{ $stokMasuks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
