@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm rounded">
            <div class="card-header text-black">
                <h4 class="mb-0">Form Edit User</h4>
            </div>

            <div class="card-body bg-secondary">
                <form action="{{ route('user.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <label for="name">Nama</label>
                            <input type="text" id="name" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" placeholder="Masukkan nama anda" autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" placeholder="Masukkan email anda">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            <label for="role" class="block mb-2 text-sm font-medium">Pilih Role</label>
                            <select id="role" name="role"
                                class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                                <option selected>Pilih Role</option>
                                <option value="kepala_gudang" {{$user->role == 'kepala_gudang' ? 'selected' : ''}}>Kepala
                                    Gudang</option>
                                <option value="admin_gudang" {{$user->role == 'admin_gudang' ? 'selected' : ''}}>Admin Gudang
                                </option>
                                <option value="kasir" {{$user->role == 'kasir' ? 'selected' : ''}}>Kasir</option>
                                <option value="pelanggan" {{$user->role == 'pelanggan' ? 'selected' : ''}}>Pelanggan</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            <label for="password">Password <small class="text-muted">(Biarkan kosong jika tidak
                                    diubah)</small></label>
                            <input type="password" id="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Masukkan password baru">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('user.index') }}" class="btn btn-outline-primary text-black">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success text-black">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
