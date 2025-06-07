<div class="card">
    <div class="card-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-sm-10">
                    <div class="bg-transparent card-rounded p-1 mb-4 row">
                        <h2 class="text-2xl font-semibold text-gray-800">
                            {{ $editMode ? 'Edit Foto Produk' : 'Upload Foto Produk' }}
                        </h2>
                    </div>

                    <form wire:submit.prevent="{{ $editMode ? 'update' : 'store' }}" enctype="multipart/form-data">
                        {{-- PILIH PRODUK --}}
                        <div class="mb-4 p-4 border border-gray-300 rounded-lg bg-white shadow-sm">
                            <label for="produk_id" class="block font-semibold mb-2 text-gray-700">Pilih Produk</label>
                            <select wire:model="produk_id"
                                class="w-full border border-gray-300 rounded p-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="">-- Pilih Produk --</option>
                                @foreach ($produk as $pro)
                                    <option value="{{ $pro->id }}">{{ $pro->nama_produk }}</option>
                                @endforeach
                            </select>
                            @error('produk_id')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- GAMBAR UTAMA --}}
                        <div class="mb-4 p-4 border border-gray-300 rounded-lg bg-white shadow-sm">
                            <label class="block font-semibold mb-2 text-gray-700">Gambar Utama</label>
                            <input type="file" wire:model="gambar_utama" accept="image/*"
                                class="w-full border border-gray-300 rounded p-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-400">

                            @if ($gambar_utama)
                                <img src="{{ $gambar_utama->temporaryUrl() }}"
                                    class="w-24 h-24 object-cover mt-3 rounded shadow">
                            @elseif ($editMode && $gambarUtamaPath)
                                <img src="{{ asset('storage/' . $gambarUtamaPath) }}"
                                    class="w-24 h-24 object-cover mt-3 rounded shadow">
                            @endif

                            @error('gambar_utama')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- GAMBAR TAMBAHAN 1 --}}
                        <div class="mb-4 p-4 border border-gray-300 rounded-lg bg-white shadow-sm">
                            <label class="block font-semibold mb-2 text-gray-700">Gambar Tambahan 1</label>
                            <input type="file" wire:model="gambar1" accept="image/*"
                                class="w-full border border-gray-300 rounded p-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-400">

                            @if ($gambar1)
                                <img src="{{ $gambar1->temporaryUrl() }}"
                                    class="w-24 h-24 object-cover mt-3 rounded shadow">
                            @elseif ($editMode && $gambar1Path)
                                <img src="{{ asset('storage/' . $gambar1Path) }}"
                                    class="w-24 h-24 object-cover mt-3 rounded shadow">
                            @endif

                            @error('gambar1')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- GAMBAR TAMBAHAN 2 --}}
                        <div class="mb-4 p-4 border border-gray-300 rounded-lg bg-white shadow-sm">
                            <label class="block font-semibold mb-2 text-gray-700">Gambar Tambahan 2</label>
                            <input type="file" wire:model="gambar2" accept="image/*"
                                class="w-full border border-gray-300 rounded p-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-400">

                            @if ($gambar2)
                                <img src="{{ $gambar2->temporaryUrl() }}"
                                    class="w-24 h-24 object-cover mt-3 rounded shadow">
                            @elseif ($editMode && $gambar2Path)
                                <img src="{{ asset('storage/' . $gambar2Path) }}"
                                    class="w-24 h-24 object-cover mt-3 rounded shadow">
                            @endif

                            @error('gambar2')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                                {{ $editMode ? 'Perbarui' : 'Upload' }}
                            </button>
                            @if ($editMode)
                                <button type="button" wire:click="resetForm"
                                    class="ml-2 bg-gray-400 text-white px-4 py-2 rounded">
                                    Batal
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- TABEL GAMBAR --}}
            <div class="table-responsive mt-4">
                <table class="table-auto w-full border mt-4 mb-3">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2">NO</th>
                            <th class="border px-4 py-2">Produk</th>
                            <th class="border px-4 py-2">Gambar</th>
                            <th class="border px-4 py-2">Utama?</th>
                            <th class="border px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($gambarList as $index => $gambar)
                            <tr>
                                <td class="border px-4 py-2">{{ $index + 1 }}</td>
                                <td class="border px-4 py-2">{{ $gambar->produk->nama_produk ?? '-' }}</td>
                                <td class="border px-4 py-2">
                                    <img src="{{ asset('storage/' . $gambar->path) }}" alt="Gambar"
                                        class="w-16 h-16 object-cover">
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    @if ($gambar->is_utama)
                                        <span class="text-green-600 font-semibold">✔</span>
                                    @else
                                        <span class="text-gray-400">✘</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2">
                                    <button wire:click="edit({{ $gambar->id }})"
                                        class="text-yellow-600 hover:underline">Edit</button>
                                    |
                                    <button wire:click="delete({{ $gambar->id }})"
                                        class="text-red-500 hover:underline">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="border px-4 py-2 text-center text-gray-500">Belum ada gambar</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
