@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>DETAIL STOK KELUAR</h1>
        </div>

        <div>
            @livewire('stok-keluar-item-component', ['id' => $stokKeluar->id])
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('.js-example-basic-single').select2();
        });
    </script>
@endpush
