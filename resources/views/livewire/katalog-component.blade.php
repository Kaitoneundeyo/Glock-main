<div class="container mt-5">
    <div class="row">
        @foreach ($produks as $pro)
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4">
                <div class="product-card">
                    {{-- ✅ Gambar Produk --}}
                    <img src="{{ $pro->gambarUtama && $pro->gambarUtama->path
            ? asset('storage/' . $pro->gambarUtama->path)
            : asset('images/default-product.png') }}" alt="{{ $pro->nama_produk }}" class="product-image">

                    {{-- ✅ Konten Produk --}}
                    <div class="product-body">
                        {{-- Label Promo & COD --}}
                        <div class="mb-1">
                            @if(
                                    $pro->hargaTerbaru
                                    && $pro->hargaTerbaru->harga_promo > 0
                                    && $pro->hargaTerbaru->harga_promo < $pro->hargaTerbaru->harga_jual
                                )
                                <span class="badge-custom">Promo</span>
                            @endif

                            @if($pro->hargaTerbaru && $pro->hargaTerbaru->cod)
                                <span class="badge-custom">COD</span>
                            @endif
                        </div>

                        {{-- Nama & Kategori --}}
                        <div class="product-title">{{ $pro->nama_produk }}</div>

                        <div class="stok-info">
                            {{ $pro->kategori->name ?? '-' }}<br>
                            Stok: {{ $pro->stok ?? 0 }}
                        </div>

                        {{-- ✅ Harga --}}
                        <div class="mt-2">
                            @if($pro->hargaTerbaru)
                                @php
                                    $promo = $pro->hargaTerbaru->harga_promo;
                                    $jual = $pro->hargaTerbaru->harga_jual;
                                @endphp

                                @if($promo > 0 && $promo < $jual)
                                    <div class="harga">Rp{{ number_format($promo, 0, ',', '.') }}</div>
                                    <div class="harga-coret">Rp{{ number_format($jual, 0, ',', '.') }}</div>
                                @else
                                    <div class="harga-normal">
                                        Rp{{ number_format($jual, 0, ',', '.') }}
                                    </div>
                                @endif
                            @else
                                <div class="harga">Rp0</div>
                            @endif
                        </div>

                        {{-- ✅ Tombol Tambah --}}
                        <div class="float-footer">
                            <button class="btn btn-sm btn-outline-primary w-100 mt-2"
                                wire:click="showAddToCartModal({{ $pro->id }})">
                                Tambah ke Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ✅ Modal Tambah ke Keranjang --}}
    @if($showModal && $selectedProduk)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">{{ $selectedProduk->nama }}</h3>
                <p>
                    Stok tersedia:
                    {{ $selectedProduk->stok - \App\Models\Cart_item::where('produk_id', $selectedProduk->id)->sum('quantity') }}
                </p>

                <label for="jumlah">Jumlah:</label>
                <input type="number" id="jumlah" wire:model="quantity" min="1" class="w-full border p-2 rounded mb-4">

                <div class="flex justify-end gap-2">
                    <button wire:click="confirmAddToCart" class="bg-blue-500 text-white px-4 py-2 rounded">
                        Tambah ke Keranjang
                    </button>
                    <button wire:click="$set('showModal', false)" class="text-gray-600 px-4 py-2">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
