@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <div class="card shadow mb-6">
            <div class="card-body">
                <h1 class="text-2xl font-bold mb-4">Laporan Harian</h1>

                <!-- Filter Tanggal -->
                <form method="GET" class="flex flex-col md:flex-row md:items-center md:gap-4 gap-2">
                    <div class="flex items-center gap-2">
                        <label for="tanggal" class="font-medium">Pilih Tanggal:</label>
                        <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}"
                            class="form-input border rounded p-1">
                    </div>
                    <div class="flex flex-col md:flex-row items-center justify-between gap-2 mt-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i> Tampilkan
                        </button>

                        <a href="{{ route('laporan.exportExcel', ['tanggal' => $tanggal]) }}" class="btn btn-success">
                            <i class="fas fa-file-excel mr-1"></i> Export Excel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Ringkasan -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-100 p-4 rounded shadow">
                <h2 class="text-lg font-semibold">Total Penjualan</h2>
                <p class="text-xl font-bold text-blue-700">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded shadow">
                <h2 class="text-lg font-semibold">Total HPP</h2>
                <p class="text-xl font-bold text-yellow-700">Rp {{ number_format($totalHPP, 0, ',', '.') }}</p>
            </div>
            <div class="bg-green-100 p-4 rounded shadow">
                <h2 class="text-lg font-semibold">Laba Kotor</h2>
                <p class="text-xl font-bold text-green-700">Rp {{ number_format($labaKotor, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Orders -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">Orderan Masuk</h2>
            <table class="table table-bordered w-full text-sm">
                <thead>
                    <tr class="bg-gray-200">
                        <th>Order #</th>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                        <th>HPP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        @foreach ($order->orderItems as $item)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format(($item->hpp ?? 0) * $item->quantity, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            {{ $orders->links() }}
        </div>

        <!-- Stok Masuk -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">Stok Masuk</h2>
            <table class="table table-bordered w-full text-sm">
                <thead>
                    <tr class="bg-gray-200">
                        <th>No Invoice</th>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Harga Beli</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stokMasuk as $masuk)
                        @foreach ($masuk->items as $item)
                            <tr>
                                <td>{{ $masuk->no_invoice }}</td>
                                <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                <td>{{ $item->jumlah }}</td>
                                <td>Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->jumlah * $item->harga_beli, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            {{ $stokMasuk->links() }}
        </div>

        <!-- Stok Keluar -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">Stok Keluar (Rusak / Expired)</h2>
            <table class="table table-bordered w-full text-sm">
                <thead>
                    <tr class="bg-gray-200">
                        <th>Referensi</th>
                        <th>Produk</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stokKeluar as $keluar)
                        @foreach ($keluar->items as $item)
                            <tr>
                                <td>{{ $keluar->keterangan ?? '-' }}</td>
                                <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                <td>{{ $item->jumlah }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            {{ $stokKeluar->links() }}
        </div>
    </div>
@endsection
