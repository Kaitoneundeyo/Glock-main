@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto p-4">
        <div class="bg-white shadow rounded-xl p-6">
            <h2 class="text-2xl font-bold mb-4">Konfirmasi Pembayaran</h2>
            <p class="mb-4 text-gray-600">Nomor Invoice: <strong>{{ $sale->invoice_number }}</strong></p>

            @foreach($sale->saleItems as $item)
                <div class="flex items-center justify-between border-b py-4">
                    <div class="flex items-center gap-4">
                        @if ($item->produk->gambarUtama)
                            <img src="{{ asset('storage/' . $item->produk->gambarUtama->path) }}"
                                alt="{{ $item->produk->nama_produk }}" class="w-16 h-16 object-cover rounded">
                        @endif
                        <div>
                            <div class="font-semibold">{{ $item->produk->nama_produk }}</div>
                            <div class="text-sm text-gray-500">Qty: {{ $item->quantity }}</div>

                            @php
                                $harga = $item->price;
                            @endphp

                            <div class="text-sm font-bold mt-1">
                                Harga: Rp{{ number_format($harga, 0, ',', '.') }}
                            </div>
                            <div class="text-sm text-gray-600">
                                Subtotal: Rp{{ number_format($harga * $item->quantity, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="text-right text-xl font-bold mt-6">
                Total: Rp{{ number_format($sale->total, 0, ',', '.') }}
            </div>

            <div class="text-right mt-4">
                {{-- Nantinya ini bisa diarahkan ke Midtrans --}}
                <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow">
                    Pilih Bayar
                </a>
            </div>
        </div>
    </div>
@endsection
