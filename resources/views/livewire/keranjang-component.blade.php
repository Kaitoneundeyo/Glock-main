<div class="max-w-4xl mx-auto p-4">
    <div class="bg-white shadow rounded-xl p-6">
        <h2 class="text-2xl font-bold mb-6 border-b pb-4">
            Keranjang Anda : {{ Auth::user()->name }}
        </h2>

        {{-- Stock Warnings --}}
        @php
            $stockWarnings = [];
            foreach ($cartItems as $item) {
                $availableStock = $this->getDisplayStock($item->produk_id);
                if ($item->quantity > $availableStock) {
                    $stockWarnings[] = $item;
                }
            }
        @endphp

        @if(!empty($stockWarnings))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <div class="text-red-600 mr-2">⚠️</div>
                    <div>
                        <h3 class="text-red-800 font-semibold">Peringatan Stok!</h3>
                        <p class="text-red-700 text-sm mt-1">
                            Beberapa produk di keranjang Anda melebihi stok yang tersedia:
                        </p>
                        <ul class="text-red-700 text-sm mt-2 ml-4">
                            @foreach($stockWarnings as $warning)
                                <li>• {{ $warning->produk->nama_produk }}:
                                    Anda pesan {{ $warning->quantity }}, tersedia
                                    {{ $this->getDisplayStock($warning->produk_id) }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @forelse ($cartItems as $item)
            @php
                $availableStock = $this->getDisplayStock($item->produk_id);
                $isStockInsufficient = $item->quantity > $availableStock;
            @endphp

            <div class="flex items-center justify-between border-b py-4 {{ $isStockInsufficient ? 'bg-red-50' : '' }}">
                {{-- Gambar & Info Produk --}}
                <div class="flex items-center gap-4 w-1/3">
                    @if ($item->produk->gambarUtama)
                        <img src="{{ asset('storage/' . $item->produk->gambarUtama->path) }}"
                            alt="{{ $item->produk->nama_produk }}"
                            class="w-16 h-16 object-cover rounded {{ $isStockInsufficient ? 'opacity-50' : '' }}">
                    @else
                        <div class="w-16 h-16 flex items-center justify-center bg-gray-100 text-gray-500 text-sm rounded">
                            Tidak ada gambar
                        </div>
                    @endif
                    <div>
                        <div class="font-semibold">{{ $item->produk->nama_produk }}</div>

                        {{-- Stock Status --}}
                        <div class="text-xs mt-1">
                            @if($isStockInsufficient)
                                <span class="text-red-600 font-medium">
                                    ⚠️ Stok tidak mencukupi ({{ $availableStock }} tersedia)
                                </span>
                            @elseif($availableStock <= 5)
                                <span class="text-yellow-600 font-medium">
                                    ⚡ Stok terbatas ({{ $availableStock }} tersedia)
                                </span>
                            @else
                                <span class="text-green-600">
                                    ✓ Stok tersedia ({{ $availableStock }})
                                </span>
                            @endif
                        </div>

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
                        class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 {{ $item->quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $item->quantity <= 1 ? 'disabled' : '' }}>-</button>

                    <span class="font-medium {{ $isStockInsufficient ? 'text-red-600' : '' }}">
                        {{ $item->quantity }}
                    </span>

                    <button wire:click="increment({{ $item->id }})"
                        class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 {{ $item->quantity >= $availableStock ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $item->quantity >= $availableStock ? 'disabled' : '' }}>+</button>
                </div>

                {{-- Subtotal & Hapus --}}
                <div class="text-right w-1/3">
                    @php
                        $harga = $promo > 0 && $promo < $jual ? $promo : $jual;
                        $subtotal = $harga * $item->quantity;
                    @endphp
                    <div class="font-semibold {{ $isStockInsufficient ? 'text-red-600' : '' }}">
                        Rp{{ number_format($subtotal, 0, ',', '.') }}
                    </div>
                    <button wire:click="removeItem({{ $item->id }})"
                        class="text-red-500 text-sm hover:underline mt-1">Hapus</button>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">🛒</div>
                <p class="text-gray-500 text-lg">Keranjang Anda kosong.</p>
                <a href="{{ route('tampil.index') }}"
                    class="inline-block mt-4 bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                    Mulai Belanja
                </a>
            </div>
        @endforelse

        @if($cartItems->isNotEmpty())
            {{-- Reservation Info --}}
            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="text-sm text-blue-800">
                    <div class="font-semibold mb-2">ℹ️ Informasi Reservasi:</div>
                    <ul class="space-y-1 text-xs">
                        <li>• Produk di keranjang direservasi selama 60 menit</li>
                        <li>• Saat checkout, reservasi akan dikunci selama 30 menit untuk pembayaran</li>
                        <li>• Jika tidak dibayar dalam waktu tersebut, reservasi akan dibatalkan otomatis</li>
                    </ul>
                </div>
            </div>

            {{-- Total --}}
            <div class="mt-6 text-right font-bold text-xl">
                Total: Rp{{ number_format($this->total, 0, ',', '.') }}
            </div>

            {{-- Tombol Checkout --}}
            <div class="mt-6 text-right">
                @if(empty($stockWarnings))
                    <form action="{{ route('checkout.process') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg shadow-lg font-semibold transition-colors">
                            Checkout Sekarang
                        </button>
                    </form>
                @else
                    <div class="text-right">
                        <button disabled
                            class="bg-gray-400 text-white px-8 py-3 rounded-lg shadow-lg font-semibold cursor-not-allowed">
                            Checkout (Perbaiki Stok Dulu)
                        </button>
                        <p class="text-red-600 text-sm mt-2">
                            Sesuaikan jumlah produk dengan stok yang tersedia untuk melanjutkan checkout
                        </p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
