@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-white shadow-md rounded-lg p-4 mb-4">
            <h1 class="text-2xl font-bold text-gray-800">🗓️ Laporan Harian
                ({{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }})</h1>
        </div>

        {{-- Filter Tanggal --}}
        <form method="GET" action="{{ route('laporan.index') }}" class="mb-4">
            <input type="date" name="tanggal" value="{{ $tanggal }}" class="border rounded px-2 py-1">
            <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded">Tampilkan</button>
            {{-- Tombol Export Excel --}}
            <a href="{{ route('laporan.export.excel', ['tanggal' => $tanggal]) }}"
                class="bg-green-600 text-white px-3 py-1 rounded">
                Download Excel
            </a>
        </form>

        {{-- Orderan --}}
        <div class="bg-white shadow-md rounded p-4 mb-6">
            <h2 class="text-lg font-semibold mb-2">Orderan Masuk</h2>
            <table class="w-full table-auto">
                <thead>
                    <tr>
                        <th>No Order</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Produk</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td>{{ $order->payment_method }}</td>
                            <td>
                                <ul>
                                    @foreach ($order->orderItems as $item)
                                        <li>{{ $item->produk->nama_produk }} x {{ $item->quantity }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">Tidak ada orderan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <p class="mt-2 font-semibold">Total Pemasukan: Rp{{ number_format($totalPemasukan, 0, ',', '.') }}</p>
        </div>

        {{-- Stok Masuk --}}
        <div class="bg-green-100 shadow-md rounded p-4 mb-6">
            <h2 class="text-lg font-semibold mb-2">Stok Masuk</h2>
            @forelse ($stokMasuk as $masuk)
                <div class="mb-2">
                    <strong>Invoice:</strong> {{ $masuk->no_invoice }}<br>
                    <ul>
                        @foreach ($masuk->items as $item)
                            <li>{{ $item->produk->nama_produk }}: {{ $item->jumlah }} pcs</li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p>Tidak ada stok masuk.</p>
            @endforelse
        </div>

        {{-- Stok Keluar --}}
        <div class="bg-red-100 shadow-md rounded p-4 mb-6">
            <h2 class="text-lg font-semibold mb-2">Stok Keluar</h2>
            @forelse ($stokKeluar as $keluar)
                <div class="mb-2">
                    <strong>No Keluar:</strong> {{ $keluar->no_keluar }}<br>
                    <ul>
                        @foreach ($keluar->items as $item)
                            <li>{{ $item->produk->nama_produk }}: {{ $item->jumlah }} pcs</li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p>Tidak ada stok keluar.</p>
            @endforelse
        </div>
    </div>
@endsection
