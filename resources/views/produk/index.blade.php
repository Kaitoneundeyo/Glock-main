@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>DATA PRODUK</h1>
        </div>
        <div>
            @livewire('produk-component')
        </div>
        @push('scripts')
            <script src="https://unpkg.com/html5-qrcode"></script>
            <script>
                function startScanner() {
                    const reader = document.getElementById('reader');
                    reader.style.display = 'block';

                    const html5QrCode = new Html5Qrcode("reader");
                    const config = { fps: 10, qrbox: 250 };

                    html5QrCode.start(
                        { facingMode: "environment" },
                        config,
                        qrCodeMessage => {
                            document.getElementById('kode_produk').value = qrCodeMessage;
                            Livewire.emit('kodeProdukScanned', qrCodeMessage);
                            html5QrCode.stop().then(() => {
                                reader.style.display = 'none';
                            }).catch(err => console.error('Stop error:', err));
                        },
                        errorMessage => {
                            // Tidak ditemukan, tidak perlu ditampilkan
                        }
                    ).catch(err => {
                        console.error('Start error:', err);
                    });
                }
            </script>
        @endpush
@endsection
