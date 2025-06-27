@extends('layouts.app')

@push('styles')
    <style>
        input[type="file"] {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Gambar Produk</h1>
        </div>
        <div>
            @livewire('gambar-component')
        </div>
</section> @endsection @push('scripts')
    <script>
        $(document).ready(function () {
            $('.js-example-basic-single').select2();
        });
    </script>
@endpush
