@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Daftar Transaksi</h1>

    @if($sales->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg">
            Belum ada transaksi yang tercatat.
        </div>
    @else
        <div class="space-y-6">
            @foreach($sales as $sale)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition duration-200">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800">Invoice: {{ $sale->invoice_number }}</h2>
                                <p class="text-sm text-gray-500">
                                    Tanggal: {{ $sale->created_at->format('d M Y, H:i') }} WIB
                                </p>
                                <p class="text-sm mt-1">
                                    Status:
                                    <span class="font-medium {{ $sale->status === 'pending' ? 'text-yellow-600' : 'text-green-600' }}">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-gray-500 text-sm">Total:</p>
                                <p class="text-xl font-bold text-red-600">Rp{{ number_format($sale->total, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-4">
                            <a href="{{ route('checkout.confirmation', ['invoice' => $sale->invoice_number]) }}"
                               class="inline-flex items-center px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m0 0l3-3m-3 3l3 3m6 3V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2z"/>
                                </svg>
                                Lihat Detail
                            </a>

                            @if($sale->status === 'pending')
                                <form action="{{ route('checkout.pay', ['invoice' => $sale->invoice_number]) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow transition duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Bayar Sekarang
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
