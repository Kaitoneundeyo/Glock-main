<div class="container mt-5">
    <div class="row">
        @foreach ($produks as $pro)
            @php $availableStock = $this->getAvailableStock($pro->id); @endphp
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4">
                <div class="product-card {{ $availableStock <= 0 ? 'out-of-stock' : '' }}">
                    <img src="{{ $pro->gambarUtama && $pro->gambarUtama->path
                        ? asset('storage/' . $pro->gambarUtama->path)
                        : asset('images/default-product.png') }}"
                        alt="{{ $pro->nama_produk }}"
                        class="product-image {{ $availableStock <= 0 ? 'opacity-50' : '' }}">

                    <div class="product-body">
                        <div class="mb-1">
                            @if($pro->hargaTerbaru && $pro->hargaTerbaru->harga_promo > 0 && $pro->hargaTerbaru->harga_promo < $pro->hargaTerbaru->harga_jual)
                                <span class="badge-custom">Promo</span>
                            @endif

                            @if($pro->hargaTerbaru && $pro->hargaTerbaru->cod)
                                <span class="badge-custom">COD</span>
                            @endif

                            @if($availableStock <= 0)
                                <span class="badge bg-danger">Habis</span>
                            @elseif($availableStock <= 5)
                                <span class="badge bg-warning">Sisa {{ $availableStock }}</span>
                            @endif
                        </div>

                        <div class="product-title">{{ $pro->nama_produk }}</div>

                        <div class="stok-info">
                            {{ $pro->kategori->name ?? '-' }}<br>
                            <span class="stock-display {{ $availableStock <= 0 ? 'text-danger' : ($availableStock <= 5 ? 'text-warning' : 'text-success') }}">
                                Tersedia: {{ $availableStock }}
                            </span>
                        </div>

                        <div class="mt-2">
                            @php
                                $promo = $pro->hargaTerbaru->harga_promo ?? 0;
                                $jual = $pro->hargaTerbaru->harga_jual ?? 0;
                            @endphp

                            @if($promo > 0 && $promo < $jual)
                                <div class="harga">Rp{{ number_format($promo, 0, ',', '.') }}</div>
                                <div class="harga-coret">Rp{{ number_format($jual, 0, ',', '.') }}</div>
                            @else
                                <div class="harga-normal">Rp{{ number_format($jual, 0, ',', '.') }}</div>
                            @endif
                        </div>

                        <div class="float-footer">
                            @if($availableStock > 0)
                                <button class="btn btn-sm btn-outline-primary w-100 mt-2"
                                    wire:click="showAddToCartModal({{ $pro->id }})">
                                    Tambah ke Keranjang
                                </button>
                            @else
                                <button class="btn btn-sm btn-secondary w-100 mt-2" disabled>
                                    Stok Habis
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
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
