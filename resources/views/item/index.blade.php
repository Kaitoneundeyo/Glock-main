@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>DETAIL ITEM</h1>
        </div>

        <div>
            @livewire('invoice-detail-component', ['id' => $id])
        </div>
    </section>
@endsection
