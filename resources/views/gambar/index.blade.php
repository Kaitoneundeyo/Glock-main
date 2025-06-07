@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Gambar Produk</h1>
        </div>
        <div>
            @livewire('gambar-component')
        </div>
        @push('styles')
            <style>
                input[type="file"] {
                    cursor: pointer;
                }
            </style>
        @endpush
@endsection
