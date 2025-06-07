@extends('layouts.app')

<style>
    .product-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        overflow: hidden;
        transition: box-shadow 0.2s ease;
        background-color: #fff;
        height: 100%;
    }

    .product-card:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .product-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .product-body {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 10px;
        font-size: 14px;
    }

    .product-title {
        font-weight: bold;
        margin-bottom: 5px;
        font-size: 15px;
        height: 38px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .badge-custom {
        background-color: #ff5722;
        color: white;
        font-size: 11px;
        padding: 2px 5px;
        border-radius: 3px;
        margin-right: 3px;
    }

    .harga {
        color: #d32f2f;
        font-size: 16px;
        font-weight: bold;
    }

    .harga-normal {
        color: #000;
        font-size: 16px;
        font-weight: bold;
    }

    .harga-coret {
        text-decoration: line-through;
        color: #888;
        font-size: 13px;
    }

    .stok-info {
        color: #757575;
        font-size: 12px;
    }

    .float-footer {
        margin-top: auto;
    }

    /* Opsional: jika kamu ingin tinggi kartu seragam */
    .col-6.col-sm-4.col-md-3.col-lg-2 {
        display: flex;
    }

    .product-card {
        flex: 1;
    }
</style>

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>HOME</h1>
        </div>
        <div>
           @livewire('katalog-component')
        </div>

@endsection
