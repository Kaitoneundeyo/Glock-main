@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6">
    <div class="bg-white shadow-lg rounded-2xl p-6 text-center">
        <div class="mb-6">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Midtrans</h2>
            <p class="text-gray-600 mb-4">
                Silakan lanjutkan pembayaran untuk invoice:<br>
                <span class="font-semibold text-blue-700 text-lg">{{ $sale->invoice_number }}</span>
            </p>
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="text-sm text-gray-600 mb-2">Total yang harus dibayar:</div>
                <div class="text-2xl font-bold text-red-600">
                    Rp{{ number_format($sale->total, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <button id="pay-button"
            class="bg-green-600 hover:bg-green-700 text-white font-medium px-8 py-3 rounded-lg shadow-md transition duration-200 transform hover:scale-105">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            Bayar Sekarang
        </button>

        <div class="mt-6 text-xs text-gray-500">
            <p>Powered by Midtrans - Pembayaran Aman & Terpercaya</p>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 text-center">
            <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-600">Memproses pembayaran...</p>
        </div>
    </div>
</div>

{{-- Midtrans Snap Script --}}
@if(config('midtrans.is_production'))
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@else
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function () {
    const payButton = document.getElementById('pay-button');
    const loadingOverlay = document.getElementById('loading-overlay');

    if (payButton) {
        payButton.addEventListener('click', function () {
            // Cek apakah Midtrans Snap tersedia
            if (typeof window.snap === 'undefined') {
                alert('❌ Midtrans Snap tidak tersedia. Silakan coba muat ulang halaman.');
                return;
            }

            // Tampilkan loading
            loadingOverlay.classList.remove('hidden');

            // Nonaktifkan tombol untuk mencegah double click
            payButton.disabled = true;
            payButton.innerHTML = '<svg class="w-5 h-5 inline-block mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Memproses...';

            // Panggil Midtrans Snap
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function (result) {
                    console.log('✅ Pembayaran berhasil:', result);
                    loadingOverlay.classList.add('hidden');

                    // Tampilkan notifikasi sukses
                    alert("✅ Pembayaran berhasil! Terima kasih atas pembayaran Anda.");

                    // Redirect ke halaman sukses
                    window.location.href = "{{ route('checkout.finish') }}?order_id={{ $sale->invoice_number }}&status_code=200&transaction_status=settlement";
                },
                onPending: function (result) {
                    console.log('⏳ Pembayaran pending:', result);
                    loadingOverlay.classList.add('hidden');

                    alert("⏳ Pembayaran Anda sedang diproses. Silakan tunggu konfirmasi.");
                    window.location.href = "{{ route('checkout.finish') }}?order_id={{ $sale->invoice_number }}&status_code=201&transaction_status=pending";
                },
                onError: function (result) {
                    console.error('❌ Error pembayaran:', result);
                    loadingOverlay.classList.add('hidden');

                    alert("❌ Terjadi kesalahan dalam proses pembayaran. Silakan coba lagi.");
                    window.location.href = "{{ route('checkout.error') }}?order_id={{ $sale->invoice_number }}";
                },
                onClose: function () {
                    console.log('⚠️ Popup ditutup sebelum pembayaran selesai');
                    loadingOverlay.classList.add('hidden');

                    // Reset tombol
                    payButton.disabled = false;
                    payButton.innerHTML = '<svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>Bayar Sekarang';

                    alert('⚠️ Anda menutup jendela pembayaran sebelum menyelesaikan transaksi.');
                }
            });
        });
    }
});
</script>

@push('styles')
<style>
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .5;
        }
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
@endpush
@endsection
