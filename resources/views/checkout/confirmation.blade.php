@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6">
            <h2 class="text-2xl font-bold mb-2">Konfirmasi Pembayaran</h2>
            <p class="text-blue-100">
                Nomor Invoice: <span class="font-semibold text-white">{{ $sale->invoice_number }}</span>
            </p>
            <p class="text-sm text-blue-200 mt-1">
                Tanggal: {{ $sale->created_at->format('d M Y, H:i') }} WIB
            </p>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Status -->
            <div class="mb-6 p-4 rounded-lg {{ $sale->status === 'pending' ? 'bg-yellow-50 border border-yellow-200' : 'bg-gray-50 border border-gray-200' }}">
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full {{ $sale->status === 'pending' ? 'bg-yellow-400' : 'bg-gray-400' }} mr-3"></div>
                    <span class="font-medium text-gray-700">
                        Status:
                        <span class="{{ $sale->status === 'pending' ? 'text-yellow-600' : 'text-gray-600' }}">
                            {{ ucfirst($sale->status) }}
                        </span>
                    </span>
                </div>
            </div>

            <!-- Items -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Pesanan</h3>
                <div class="space-y-4">
                    @foreach($sale->saleItems as $item)
                        <div class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
                            @if ($item->produk->gambarUtama)
                                <img src="{{ asset('storage/' . $item->produk->gambarUtama->path) }}"
                                     alt="{{ $item->produk->nama_produk }}"
                                     class="w-20 h-20 object-cover rounded-lg border shadow-sm">
                            @else
                                <div class="w-20 h-20 bg-gray-100 flex items-center justify-center rounded-lg border text-sm text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif

                            <div class="flex-1">
                                <div class="font-semibold text-gray-800 mb-1">{{ $item->produk->nama_produk }}</div>
                                <div class="text-sm text-gray-500 mb-2">Kategori: {{ $item->produk->kategori->nama_kategori ?? 'Tidak ada kategori' }}</div>

                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-600">
                                        <span class="inline-block bg-gray-100 px-2 py-1 rounded">Qty: {{ $item->quantity }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-600">
                                            Harga: <span class="font-medium text-red-600">Rp{{ number_format($item->price, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="text-sm font-semibold text-gray-800">
                                            Subtotal: <span class="text-red-600">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Total -->
            <div class="border-t border-gray-200 pt-6 mb-6">
                <div class="flex justify-between items-center text-2xl font-bold text-gray-800">
                    <span>Total Pembayaran:</span>
                    <span class="text-red-600">Rp{{ number_format($sale->total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-end">
                <a href="{{ route('checkout.transactions') }}"
                   class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Transaksi
                </a>

                @if($sale->status === 'pending')
                    <form action="{{ route('checkout.pay', ['invoice' => $sale->invoice_number]) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center justify-center px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-md transition duration-200 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Bayar Sekarang
                        </button>
                    </form>
                @else
                    <div class="inline-flex items-center justify-center px-8 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ ucfirst($sale->status) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
