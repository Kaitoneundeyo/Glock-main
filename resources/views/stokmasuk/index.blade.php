@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>DAFTAR INVOICE</h1>
    </div>
    <div>
        @livewire('invoice-list-component')
    </div>

@endsection
