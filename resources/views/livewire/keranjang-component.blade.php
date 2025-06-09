<div class="max-w-4xl mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">
        Keranjang Belanja Anda : {{ Auth::user()->name }}
    </h2>

    @forelse ($cartItems as $item)
        <div class="flex items-center justify-between border-b py-4">

            {{-- Gambar & Info Produk --}}
            <div class="flex items-center gap-4 w-1/3">
                @if ($item->produk->gambarUtama)
                    <img src="{{ asset('storage/' . $item->produk->gambarUtama->path) }}" alt="{{ $item->produk->nama_produk }}"
                        class="w-16 h-16 object-cover rounded">
                @else
                    <div class="w-16 h-16 flex items-center justify-center bg-gray-100 text-gray-500 text-sm rounded">
                        Tidak ada gambar
                    </div>
                @endif

                <div>
                    <div class="font-semibold">{{ $item->produk->nama_produk }}</div>
                    <div class="mt-2">
                        @php
    $promo = $item->produk->hargaTerbaru->harga_promo ?? 0;
    $jual = $item->produk->hargaTerbaru->harga_jual ?? 0;
                        @endphp

                        @if($promo > 0 && $promo < $jual)
                            <div class="text-red-500 font-bold">Rp{{ number_format($promo, 0, ',', '.') }}</div>
                            <div class="text-gray-500 line-through">Rp{{ number_format($jual, 0, ',', '.') }}</div>
                        @else
                            <div class="text-gray-800 font-bold">
                                Rp{{ number_format($jual, 0, ',', '.') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Kontrol Jumlah --}}
            <div class="flex items-center gap-2 w-1/3 justify-center">
                <button wire:click="decrement({{ $item->id }})"
                    class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">-</button>
                <span class="font-medium">{{ $item->quantity }}</span>
                <button wire:click="increment({{ $item->id }})"
                    class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">+</button>
            </div>

            {{-- Subtotal & Hapus --}}
            <div class="text-right w-1/3">
                @php
    $harga = $promo > 0 && $promo < $jual ? $promo : $jual;
    $subtotal = $harga * $item->quantity;
                @endphp

                <div class="font-semibold">
                    Rp{{ number_format($subtotal, 0, ',', '.') }}
                </div>
                <button wire:click="removeItem({{ $item->id }})"
                    class="text-red-500 text-sm hover:underline mt-1">Hapus</button>
            </div>
        </div>
    @empty
        <p class="text-gray-500">Keranjang Anda kosong.</p>
    @endforelse

    {{-- Total --}}
    <div class="mt-6 text-right font-bold text-lg">
        Total: Rp{{ number_format($this->total, 0, ',', '.') }}
    </div>

    {{-- Tombol Checkout --}}
    <div class="mt-4 text-right">
        <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded">
            Checkout
        </button>
    </div>
</div>
