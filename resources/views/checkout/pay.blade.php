@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto p-6">
        <div class="bg-white shadow-lg rounded-xl p-6 text-center">
            <h2 class="text-2xl font-bold mb-4">Pembayaran</h2>
            <p class="mb-4">Total yang harus dibayar:</p>
            <h3 class="text-3xl font-semibold text-green-600 mb-6">
                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
            </h3>

            <button id="pay-button" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition">
                Bayar Sekarang
            </button>

            <p class="mt-6 text-sm text-gray-500">
                Anda akan diarahkan ke halaman pembayaran Midtrans.
            </p>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Midtrans Snap.js -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script type="text/javascript">
        window.snap.pay('{{ $snapToken }}', {
            onSuccess: function (result) {
                console.log('Success:', result);
                window.location.href = "{{ route('midtrans.finish') }}" +
                    "?order_id=" + result.order_id +
                    "&status_code=" + result.status_code +
                    "&transaction_status=" + result.transaction_status;
            },
            onPending: function (result) {
                console.log('Pending:', result);
                window.location.href = "{{ route('midtrans.unfinish') }}" +
                    "?order_id=" + result.order_id +
                    "&status_code=" + result.status_code +
                    "&transaction_status=" + result.transaction_status;
            },
            onError: function (result) {
                console.log('Error:', result);
                window.location.href = "{{ route('midtrans.error') }}" +
                    "?order_id=" + result.order_id +
                    "&status_code=" + result.status_code +
                    "&transaction_status=" + result.transaction_status;
            },
            onClose: function () {
                alert('Kamu menutup tanpa menyelesaikan pembayaran!');
            }
        });
    </script>
@endpush
