@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Daftar Transaksi</h1>

        {{-- 🔔 Notifikasi Status dari Midtrans --}}
        @if(request('status') && isset($highlightOrder))
            <div class="mb-6">
                @if(request('status') === 'success')
                    <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-lg">
                        Pembayaran berhasil untuk pesanan <strong>#{{ $highlightOrder->order_number }}</strong>.
                    </div>
                @elseif(request('status') === 'pending')
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg">
                        Pembayaran masih menunggu untuk pesanan <strong>#{{ $highlightOrder->order_number }}</strong>.
                    </div>
                @elseif(request('status') === 'failed')
                    <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg">
                        Pembayaran gagal atau dibatalkan untuk pesanan <strong>#{{ $highlightOrder->order_number }}</strong>.
                    </div>
                @elseif(request('status') === 'unfinish')
                    <div class="bg-gray-100 border border-gray-300 text-gray-800 p-4 rounded-lg">
                        Transaksi belum selesai untuk pesanan <strong>#{{ $highlightOrder->order_number }}</strong>.
                    </div>
                @endif
            </div>
        @endif

        {{-- 🔸 Jika tidak ada transaksi --}}
        @if($orders->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg">
                Belum ada transaksi yang tercatat.
            </div>
        @else
            <div class="space-y-6">
                @foreach($orders as $order)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition duration-200">
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h2 class="text-lg font-semibold text-gray-800">Order: {{ $order->order_number }}</h2>
                                            <p class="text-sm text-gray-500">
                                                Tanggal: {{ $order->created_at->timezone('Asia/Makassar')->format('d M Y, H:i') }} WITA
                                            </p>
                                            <p class="text-sm mt-1">
                                                Status:
                                                <span class="font-medium
                                                                {{ $order->status === 'pending_payment' ? 'text-yellow-600' :
                    ($order->status === 'awaiting_payment' ? 'text-blue-600' :
                        ($order->status === 'paid' ? 'text-green-600' : 'text-red-600')) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                                </span>
                                            </p>
                                            @if($order->payment_method)
                                                <p class="text-sm text-gray-500">
                                                    Metode: {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="text-gray-500 text-sm">Total:</p>
                                            <p class="text-xl font-bold text-red-600">
                                                Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Ringkasan Item --}}
                                    <div class="mb-4 text-sm text-gray-600">
                                        <span class="font-medium">{{ $order->orderItems->count() }} item(s):</span>
                                        {{ $order->orderItems->pluck('produk.nama_produk')->take(3)->implode(', ') }}
                                        @if($order->orderItems->count() > 3)
                                            <span class="text-gray-500">dan {{ $order->orderItems->count() - 3 }} lainnya</span>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap items-center gap-4">
                                        <a href="{{ route('checkout.confirmation', ['order' => $order->order_number]) }}"
                                            class="inline-flex items-center px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12H9m0 0l3-3m-3 3l3 3m6 3V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2z" />
                                            </svg>
                                            Lihat Detail
                                        </a>

                                        @if(in_array($order->status, ['pending_payment', 'awaiting_payment']))
                                            <a href="{{ route('midtrans.pay', ['order' => $order->order_number]) }}"
                                                class="inline-flex items-center px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow transition duration-200">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                                Bayar Sekarang
                                            </a>
                                        @endif

                                        @if(in_array($order->status, ['pending_payment', 'awaiting_payment']))
                                            <form method="POST" action="{{ route('checkout.cancel', $order->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-sm px-5 py-2 rounded-lg border border-red-600 text-red-600 hover:bg-red-50 transition duration-200">
                                                    Batalkan
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
