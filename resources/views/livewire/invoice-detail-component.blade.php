<div class="overflow-x-auto bg-white shadow-md rounded p-4">
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6 shadow-sm">
        <h2 class="text-2xl font-semibold text-gray-800">Detail Invoice</h2>
        <div class="mt-3 text-base text-gray-700 space-y-1">
            <p><strong>No Invoice:</strong> {{ $invoice->no_invoice }}</p>
            <p><strong>Tanggal Masuk:</strong> {{ \Carbon\Carbon::parse($invoice->tanggal_masuk)->timezone('Asia/Makassar')->format('d M Y, H:i') }}</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-2 bg-green-200 text-green-800 rounded">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="store" class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

            {{-- Produk --}}
            <div>
                <label for="produk_id" class="block text-sm font-medium text-gray-700">Produk</label>
                <select id="produk_id" wire:model="produk_id"
                    class="mt-1 block w-full border border-gray-300 rounded px-2 py-1">
                    <option value="">-- Pilih Produk --</option>
                    @foreach ($produkList as $produk)
                        <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                    @endforeach
                </select>
                @error('produk_id') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Jumlah --}}
            <div>
                <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah</label>
                <input type="number" id="jumlah" wire:model="jumlah"
                    class="mt-1 block w-full border border-gray-300 rounded px-2 py-1" min="1">
                @error('jumlah') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Harga Beli --}}
            <div>
                <label for="harga_beli" class="block text-sm font-medium text-gray-700">Harga Beli</label>
                <input type="number" id="harga_beli" wire:model="harga_beli"
                    class="mt-1 block w-full border border-gray-300 rounded px-2 py-1" min="0" step="any">
                @error('harga_beli') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Expired --}}
            <div>
                <label for="expired_at" class="block text-sm font-medium text-gray-700">Expired</label>
                <input type="date" id="expired_at" wire:model="expired_at"
                    class="mt-1 block w-full border border-gray-300 rounded px-2 py-1">
                @error('expired_at') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Tombol Simpan --}}
            <div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    {{ $editItemId ? 'Update' : 'Simpan' }}
                </button>
            </div>

        </div>
    </form>

    {{-- Tabel Item --}}
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 text-center">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="px-4 py-2 border">No</th>
                    <th class="px-4 py-2 border">Produk</th>
                    <th class="px-4 py-2 border">Jumlah</th>
                    <th class="px-4 py-2 border">Harga Beli</th>
                    <th class="px-4 py-2 border">Expired</th>
                    <th class="px-4 py-2 border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $key => $item)
                    <tr class="{{ $key % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="px-4 py-2 border">{{ $key + 1 }}</td>
                        <td class="px-4 py-2 border">{{ $item->produk->nama_produk ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ $item->jumlah }}</td>
                        <td class="px-4 py-2 border">Rp{{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                        <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($item->expired_at)->format('d M Y') }}</td>
                        <td class="px-4 py-2 border space-x-2">
                            <button wire:click="editItem({{ $item->id }})"
                                class="text-blue-600 hover:underline">Edit</button>
                            <button wire:click="delete({{ $item->id }})" class="text-red-600 hover:underline">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 text-gray-500">Belum ada item</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
