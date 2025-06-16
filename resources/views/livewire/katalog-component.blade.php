<div class="container mt-5">
    <div class="container mx-auto px-4 mt-6">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach ($produks as $pro)
                    @php $availableStock = $this->getAvailableStock($pro->id); @endphp

                    <div class="bg-white border rounded-lg shadow hover:shadow-md p-2 flex flex-col">
                        {{-- Gambar Produk --}}
                        <img src="{{ $pro->gambarUtama && $pro->gambarUtama->path
                ? asset('storage/' . $pro->gambarUtama->path)
                : asset('images/default-product.png') }}" alt="{{ $pro->nama_produk }}"
                            class="w-full h-48 object-cover rounded {{ $availableStock <= 0 ? 'opacity-50' : '' }}">

                        {{-- Konten Produk --}}
                        <div class="mt-2 flex-1 flex flex-col justify-between">
                            <div class="mb-1 space-y-1">
                                {{-- Badges --}}
                                <div class="flex flex-wrap gap-1">
                                    @if($pro->hargaTerbaru && $pro->hargaTerbaru->harga_promo > 0 && $pro->hargaTerbaru->harga_promo < $pro->hargaTerbaru->harga_jual)
                                        <span class="text-xs bg-red-400 text-white px-2 py-0.5 rounded">Promo XTRA</span>
                                    @endif

                                    @if($pro->hargaTerbaru && $pro->hargaTerbaru->cod)
                                        <span class="text-xs bg-pink-500 text-white px-2 py-0.5 rounded">COD</span>
                                    @endif

                                    @if($availableStock <= 0)
                                        <span class="text-xs bg-red-600 text-white px-2 py-0.5 rounded">Habis</span>
                                    @elseif($availableStock <= 5)
                                        <span class="text-xs bg-yellow-300 text-gray-800 px-2 py-0.5 rounded">Sisa
                                            {{ $availableStock }}</span>
                                    @endif
                                </div>

                                {{-- Nama & Kategori --}}
                                <div class="font-semibold text-sm line-clamp-2">
                                    {{ $pro->nama_produk }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ $pro->kategori->name ?? '-' }}
                                </div>
                            </div>

                            {{-- Harga --}}
                            <div>
                                @php
                                    $promo = $pro->hargaTerbaru->harga_promo ?? 0;
                                    $jual = $pro->hargaTerbaru->harga_jual ?? 0;
                                @endphp

                                @if($promo > 0 && $promo < $jual)
                                    <div class="text-red-500 font-bold text-sm">Rp{{ number_format($promo, 0, ',', '.') }}</div>
                                    <div class="text-xs text-gray-400 line-through">Rp{{ number_format($jual, 0, ',', '.') }}</div>
                                @else
                                    <div class="text-gray-800 font-semibold text-sm">Rp{{ number_format($jual, 0, ',', '.') }}</div>
                                @endif
                            </div>

                            {{-- Tombol --}}
                            <div class="mt-2">
                                @if($availableStock > 0)
                                    <button class="btn btn-sm btn-primary w-full mt-2" wire:click="toCart({{ $pro->id }})">
                                        Tambah ke Keranjang
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-secondary w-full mt-2" disabled>
                                        Stok Habis
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
            @endforeach
        </div>
    </div>

    @if($showModal && $selectedProduk)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
             wire:click="closeModal">
            <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md"
                 wire:click.stop>
                <h3 class="text-lg font-semibold mb-4">{{ $selectedProduk->nama_produk }}</h3>

                <div class="mb-4 p-3 bg-gray-50 rounded">
                    <div class="text-sm text-gray-600">
                        Stok tersedia:
                        <span class="font-semibold {{ $availableStock <= 5 ? 'text-warning' : 'text-success' }}">
                            {{ $availableStock }} item
                        </span>
                    </div>
                    @if($availableStock <= 5 && $availableStock > 0)
                        <div class="text-xs text-warning mt-1">
                            ⚠️ Stok terbatas! Buruan pesan sebelum habis.
                        </div>
                    @endif
                </div>

                <div class="mb-4">
                    <label for="jumlah" class="block text-sm font-medium mb-2">Jumlah:</label>
                    <input type="number"
                           id="jumlah"
                           wire:model.live="quantity"
                           min="1"
                           max="{{ $availableStock }}"
                           class="w-full border p-2 rounded">
                    @if($quantity > $availableStock)
                        <div class="text-red-500 text-xs mt-1">
                            Jumlah melebihi stok tersedia ({{ $availableStock }})
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-2">
                    <button wire:click="confirmAddToCart"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded disabled:bg-gray-400"
                            {{ $quantity > $availableStock || $quantity <= 0 ? 'disabled' : '' }}>
                        Tambah ke Keranjang
                    </button>
                    <button wire:click="closeModal"
                            class="text-gray-600 hover:text-gray-800 px-4 py-2">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
