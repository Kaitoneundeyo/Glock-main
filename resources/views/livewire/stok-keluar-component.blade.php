<div class="card">
    <div class="card-body">
        <div class="container-fluid">
            <form wire:submit.prevent="save" class="card-body">
                <div class="mb-3">
                    <label for="no_keluar">No Keluar</label>
                    <input type="text" id="no_keluar" class="form-control" wire:model.defer="no_keluar"
                        placeholder="Isi No Keluar di sini...">
                    @error('no_keluar') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="tanggal_keluar" class="form-label">Tanggal & Waktu Keluar</label>
                    <input type="datetime-local" id="tanggal_keluar" wire:model.defer="tanggal_keluar"
                        class="form-control">
                    @error('tanggal_keluar') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="jenis" class="form-label">Jenis Stok Keluar</label>
                    <select wire:model.defer="jenis" class="form-control" id="jenis">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach($this->jenisOptions as $option)
                            <option value="{{ $option }}">{{ ucfirst($option) }}</option>
                        @endforeach
                    </select>
                    @error('jenis') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['nama_produk'] ?? '-' }}</td>
                            <td>{{ $item['jumlah'] }}</td>
                            <td>Rp {{ number_format($item['harga_beli'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item['harga_jual'], 0, ',', '.') }}</td>
                            <td class="text-nowrap">
                                <button class="btn btn-info btn-sm" wire:click.prevent="detailItem({{ $index }})">
                                    Detail
                                </button>
                                <button class="btn btn-warning btn-sm" wire:click.prevent="editItem({{ $index }})">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm" wire:click.prevent="hapusItem({{ $index }})">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada item</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
