@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Orderan Masuk</h1>
        </div>

        <form method="GET" action="{{ route('order.index') }}" class="mb-4">
            <div class="flex items-center space-x-2">
                <label for="tanggal" class="text-sm font-medium">Pilih Tanggal:</label>
                <input type="date" id="tanggal" name="tanggal" value="{{ $tanggal }}" class="border rounded p-1">

                <label for="search" class="text-sm font-medium">Cari No Order:</label>
                <input type="text" id="search" name="search" value="{{ $search ?? '' }}" placeholder="No Order"
                    class="border rounded p-1">

                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Filter</button>
            </div>
        </form>

        <h2 class="text-lg font-semibold mb-4">ORDER MASUK: {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</h2>

        @forelse ($orders as $order)
            <div class="bg-white shadow-md rounded p-4 mb-6">
                <div>
                    <p><strong>No Order:</strong> {{ $order->order_number }}</p>
                    <p><strong>Total:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    <p><strong>Metode Pembayaran:</strong> {{ ucfirst($order->payment_method) }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                    <p><strong>Waktu Bayar:</strong>
                        {{ $order->paid_at ? $order->paid_at->timezone('Asia/Makassar')->format('d M Y, H:i') : '-' }}</p>
                    <p><strong>Catatan:</strong> {{ $order->notes ?? '-' }}</p>
                </div>

                <hr class="my-4">

                <h3 class="font-semibold mb-2">Detail Item:</h3>
                <table class="table-auto w-full text-left border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-2 py-1">Produk</th>
                            <th class="px-2 py-1">Qty</th>
                            <th class="px-2 py-1">Harga</th>
                            <th class="px-2 py-1">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderItems as $item)
                            <tr>
                                <td class="px-2 py-1">{{ $item->produk->nama_produk ?? '-' }}</td>
                                <td class="px-2 py-1">{{ $item->quantity }}</td>
                                <td class="px-2 py-1">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="px-2 py-1">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="bg-yellow-100 text-yellow-800 p-4 rounded">
                Tidak ada orderan pada tanggal ini.
            </div>
        @endforelse
    </section>
@endsection
