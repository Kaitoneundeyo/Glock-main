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
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th style="width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($user as $u)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $u->name ?? 'N/A' }}</td>
                                <td>{{ $u->email ?? 'N/A' }}</td>
                                <td>{{ $u->role ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('user.edit', $u->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-pen"></i> Edit
                                    </a>

                                    <button type="button" class="btn btn-danger btn-sm btn-hapus" data-id="{{ $u->id }}"
                                        data-toggle="modal" data-target="#modal-hapus">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Data tidak tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Modal Konfirmasi Hapus --}}
    <div class="modal fade" id="modal-hapus" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="form-hapus">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Konfirmasi Hapus</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus data ini?
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.btn-hapus');
            const form = document.getElementById('form-hapus');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    form.action = `/user/${id}/destroy`; // Sesuaikan jika route destroy-nya berbeda
                });
            });
        });
    </script>
@endpush
