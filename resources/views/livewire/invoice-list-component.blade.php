<div class="container-fluid">
    <div class="card">
        <form wire:submit.prevent="searchInvo" class="card-body d-flex flex-wrap mt-4 mb-3 gap-3 align-items-center">
            <div class="col-md-3 p-0">
                <input type="text" class="form-control" wire:model.defer="filterNoInvo" placeholder="Cari No Invoice">
            </div>
            <div class="col-md-3 p-0">
                <input type="date" class="form-control" wire:model.defer="filterTanggalMasuk"
                    placeholder="Filter Tanggal Masuk">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-search"></i> Cari
                </button>
                <button type="button" wire:click="resetFilter" class="btn btn-secondary">
                    Reset
                </button>
            </div>
        </form>

        <div class="card-body">
            <table class="table table-bordered text-center text-black">
                <thead class="bg-blue-400">
                    <tr>
                        <th class="px-4 py-2 border">NO</th>
                        <th class="px-4 py-2 border">Nomor Invoice</th>
                        <th class="px-4 py-2 border">Tanggal</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        <tr>
                            <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 border">{{ $invoice->no_invoice }}</td>
                            <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($invoice->tanggal)->timezone('Asia/Makassar')->format('d M Y, H:i') }}
                            </td>
                            <td class="px-4 py-2 border">
                                <a href="{{ route('item.index', ['id' => $invoice->id]) }}"
                                    class="text-blue-500 hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center px-4 py-2">Belum ada invoice</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="card-footer clearfix mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ $invoices->previousPageUrl() }}"
                        class="btn {{ $invoices->onFirstPage() ? 'btn-secondary disabled' : 'btn-primary' }}">
                        Previous
                    </a>
                    <span>Halaman {{ $invoices->currentPage() }} dari {{ $invoices->lastPage() }}</span>
                    <a href="{{ $invoices->nextPageUrl() }}"
                        class="btn {{ $invoices->hasMorePages() ? 'btn-primary' : 'btn-secondary disabled' }}">
                        Next
                    </a>
                </div>
                <div class="d-flex justify-content-end mt-2">
                    {{ $invoices->links() }}
                </div>
                </div>
        </div>
    </div>
</div>
