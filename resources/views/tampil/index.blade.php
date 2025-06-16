@extends('layouts.app')

@push('styles')
    <style>
        .product-card {
            background: #fff;
            border-radius: 1rem;
            padding: 1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .product-card.out-of-stock {
            opacity: 0.6;
        }

        .product-image {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .product-body {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .badge-custom {
            display: inline-block;
            background-color: #ff5e57;
            color: white;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 0.5rem;
            margin-right: 0.25rem;
        }

        .product-title {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            min-height: 42px;
            overflow: hidden;
        }

        .stok-info {
            font-size: 0.85rem;
            color: #666;
        }

        .stock-display.text-success {
            color: #22c55e;
        }

        .stock-display.text-warning {
            color: #facc15;
        }

        .stock-display.text-danger {
            color: #ef4444;
        }

        .harga {
            font-size: 1.1rem;
            font-weight: 700;
            color: #10b981;
        }

        .harga-coret {
            text-decoration: line-through;
            color: #9ca3af;
            font-size: 0.9rem;
        }

        .harga-normal {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e40af;
        }

        .float-footer {
            margin-top: auto;
        }

        .fixed.inset-0 {
            z-index: 999;
        }
    </style>
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>BERANDA</h1>
        </div>
        <div>
            @livewire('katalog-component')
        </div>
    </section>
    <script>
        window.addEventListener('cart-toast', event => {
            Swal.fire({
                toast: true,
                position: 'bottom-end',
                icon: event.detail.type,
                title: event.detail.message,
                showConfirmButton: false,
                timer: 7000,
                timerProgressBar: true,
            });
        });
    </script>
@endsection
