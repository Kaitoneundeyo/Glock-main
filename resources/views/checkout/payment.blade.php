@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto p-4">
        <div class="bg-white shadow rounded-xl p-6 text-center">
            <h2 class="text-2xl font-bold mb-4">Pembayaran Midtrans</h2>
            <p class="mb-6 text-gray-600">Invoice: <strong>{{ $sale->invoice_number }}</strong></p>

            <button id="pay-button" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded shadow">
                Bayar Sekarang
            </button>
        </div>
    </div>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            const payButton = document.getElementById('pay-button');

            if (payButton) {
                payButton.addEventListener('click', function () {
                    if (typeof window.snap === 'undefined') {
                        alert('Snap tidak tersedia. Pastikan script Midtrans sudah dimuat.');
                        return;
                    }

                    window.snap.pay('{{ $snapToken }}', {
                        onSuccess: function (result) {
                            alert("Pembayaran berhasil!");
                            window.location.href = "{{ route('checkout.success', ['invoice' => $invoice]) }}";
                        },
                        onPending: function (result) {
                            alert("Menunggu pembayaran.");
                            window.location.href = "{{ route('checkout.pending', ['invoice' => $invoice]) }}";
                        },
                        onError: function (result) {
                            alert("Pembayaran gagal!");
                            window.location.href = "{{ route('checkout.failure', ['invoice' => $invoice]) }}";
                        },
                        onClose: function () {
                            alert('Anda menutup popup tanpa menyelesaikan pembayaran.');
                        }
                    });
                });
            }
        });
    </script>
@endsection