@extends('layouts.app')
@section('content')
<div class="card">
    <form action="{{route ('user.store')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card-header">
        <h4>Form Tambah User</h4>
    </div>
    <div class="card-body">
        <div class="form-group">
          <label for="exampleInputEmail1">Email address</label>
          <input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
        </div>
        @error('email')
          <small>{{ $message }}</small>
        @enderror

        <div class="form-group">
          <label for="exampleInputNama1">Nama</label>
          <input type="text" name="name" class="form-control" id="exampleInputNama1" placeholder="Enter Nama">
        </div>
        @error('name')
          <small>{{ $message }}</small>
        @enderror

        <div class="form-group">
          <label for="exampleInputPassword1">Password</label>
          <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
        </div>
        @error('password')
          <small>{{ $message }}</small>
        @enderror
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </form>
    @if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif
</div>
@endsection
