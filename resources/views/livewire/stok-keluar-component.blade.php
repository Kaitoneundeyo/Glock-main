<div class="card">
    <div class="card-body">
        <div class="container-fluid">
            @if (session()->has('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <form wire:submit.prevent="save" class="card-body">
                <div class="mb-3">
                    <label for="no_keluar">ID Keluar</label>
                    <input type="text" id="no_keluar" class="form-control" wire:model="no_keluar"
                        placeholder="Isi nomor keluar disini.....">
                    @error('no_keluar') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-4">
                    <label for="tanggal_keluar" class="form-label">Tanggal & Waktu Keluar</label>
                    <input type="datetime-local" id="tanggal_keluar" wire:model.defer="tanggal_keluar"
                        class="form-control">
                    @error('tanggal_keluar') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label for="jenis" class="form-label">Jenis Stok Keluar</label>
                    <select wire:model.defer="jenis" class="form-control" id="jenis">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach($this->jenisOptions as $option)
                            <option value="{{ $option }}">{{ ucfirst($option) }}</option>
                        @endforeach
                    </select>
                    @error('jenis') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ $stokKeluarId ? 'Update' : 'Simpan' }}
                </button>
            </form>
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>ID Keluar</th>
                        <th>Tanggal & Waktu Keluar</th>
                        <th>Jenis Stok Keluar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->no_keluar ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_keluar)->timezone('Asia/Makassar')->format('Y-m-d H:i') }}
                            </td>
                            <td>{{ ucfirst($item->jenis) ?? '-' }}</td>
                            <td class="px-4 py-2 border">
                                <a href="{{ route('itemkeluar.index', ['id' => $item->id]) }}" class="btn btn-info btn-sm">Detail</a>
                                <button class="btn btn-warning btn-sm" wire:click="editItem({{ $item->id }})">Edit</button>
                                <button class="btn btn-danger btn-sm" wire:click="hapusItem({{ $item->id }})">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada item</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
