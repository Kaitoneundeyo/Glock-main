@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>HARGA</h1>
        </div>
        <div>
            @livewire('harga-component')
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
