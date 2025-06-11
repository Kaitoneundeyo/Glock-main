@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto p-4">
        <div class="bg-white shadow rounded-xl p-6">
            <h2 class="text-2xl font-bold mb-6">Daftar Transaksi Anda</h2>

            @forelse($sales as $sale)
                <div class="border-b py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-semibold">Invoice: {{ $sale->invoice_number }}</div>
                            <div class="text-sm text-gray-500">Tanggal: {{ $sale->created_at->format('d M Y H:i') }}</div>
                            <div class="text-sm text-gray-500">Status: {{ ucfirst($sale->status) }}</div>
                            <div class="text-sm font-bold">Total: Rp{{ number_format($sale->total, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <a href="{{ route('checkout.confirmation', ['invoice' => $sale->invoice_number]) }}"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow text-sm">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-600">Belum ada transaksi.</p>
            @endforelse
        </div>
    </div>
@endsection
