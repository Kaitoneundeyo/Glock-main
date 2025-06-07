@extends('layouts.app')

@section('content')
<div class="card col-8">
    <form action="{{ route('kategori.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-header col-8 text-black">
            <h4>Form Tambah Kategori</h4>
        </div>

        <div class="card-body text-black">
            <div class="form-group row">
                <div class="col-8">
                    <label for="name">Kategori</label>
                    <input id="name" type="text" class="form-control" name="name" placeholder="Masukkan nama kategori" autofocus>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <div class="col-8">
                    <label for="slug">URL</label>
                    <input id="slug" type="text" class="form-control" name="slug" placeholder="Masukkan URL kategori">
                    @error('slug')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary text-black">Simpan</button>
        </div>
    </form>
</div>
@endsection
