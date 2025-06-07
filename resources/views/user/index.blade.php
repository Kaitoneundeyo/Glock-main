@extends('layouts.app')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>DATA USER</h1>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Daftar User</h4>
            <a href="{{ route('user.create') }}" class="btn btn-primary btn-lg">
                + Tambah User
            </a>
        </div>

        <div class="card-body">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
            <tbody>
                @forelse ($user as $u)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $u->name ?? 'N/A' }}</td>
                        <td>{{ $u->email ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('user.edit', $u->id) }}" class="btn btn-warning">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            
                            <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#modal-hapus{{ $u->id }}">
                                <i class="fas fa-trash-alt"></i> Hapus
                            </a>

                            <!-- Modal Bootstrap 4 -->
                            <div class="modal fade" id="modal-hapus{{ $u->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $u->id }}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabel{{ $u->id }}">Konfirmasi Hapus</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            Apakah Anda yakin ingin menghapus data ini?
                                        </div>
                                        <div class="modal-footer">
                                            <form action="{{ route('user.destroy', $u->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Data tidak tersedia</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</section>
@endsection
