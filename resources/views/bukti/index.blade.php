@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Riwayat Pembelian</h1>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @forelse ($sales as $sale)
            <div class="bg-white shadow p-4 rounded-lg mb-6">
                <h2 class="font-bold text-lg">Invoice: {{ $sale->invoice_number }}</h2>
                <p class="text-sm text-gray-600">
                    Tanggal: {{ \Carbon\Carbon::parse($sale->tanggal_transaksi)->format('d M Y H:i') }}
                </p>
                <p class="text-sm text-gray-600">Pembeli: {{ $sale->user->name ?? 'Tidak diketahui' }}</p>

                <div class="mt-4 space-y-2">
                    @foreach ($sale->saleItems as $item)
                        <div class="flex items-center gap-4">
                            @if ($item->produk && $item->produk->gambarUtama)
                                <img src="{{ asset('storage/' . $item->produk->gambarUtama->path) }}"
                                    class="w-16 h-16 object-cover rounded" alt="Gambar produk">
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center text-gray-400 text-xs">
                                    No Image
                                </div>
                            @endif

                            <div>
                                <div class="font-semibold">
                                    {{ $item->produk->nama_produk ?? 'Produk tidak ditemukan' }}
                                </div>
                                <div class="text-sm text-gray-500">Qty: {{ $item->quantity }}</div>
                                <div class="text-sm text-gray-500">
                                    Harga: Rp{{ number_format($item->price, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-right font-bold mt-4">
                    Total: Rp{{ number_format($sale->total, 0, ',', '.') }}
                </div>
            </div>
        @empty
            <p class="text-gray-500">Belum ada transaksi yang berhasil.</p>
        @endforelse
    </div>
@endsection