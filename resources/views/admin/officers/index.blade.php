@extends('layouts.dashboard')

@section('title', 'Kelola Petugas | AksesLoka')

@section('content')
<section class="admin-section">
    <div class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Manajemen pengguna</p>
                <h1>Akun petugas</h1>
            </div>
            <div class="actions">
                <a class="button button-primary" href="{{ route('admin.officers.create') }}">Tambah petugas</a>
            </div>
        </div>

        @if($officers->isEmpty())
            <div class="empty-state">
                <h2>Belum ada petugas</h2>
                <p>Belum ada akun petugas yang didaftarkan dalam sistem.</p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <caption class="sr-only">Daftar akun petugas</caption>
                    <thead>
                        <tr>
                            <th scope="col">Nama</th>
                            <th scope="col">Email</th>
                            <th scope="col">Kampus</th>
                            <th scope="col">Area tugas</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($officers as $officer)
                            <tr>
                                <th scope="row" data-label="Nama">{{ $officer->name }}</th>
                                <td data-label="Email">{{ $officer->email }}</td>
                                <td data-label="Kampus">{{ $officer->campus?->name ?? '-' }}</td>
                                <td data-label="Area tugas">{{ $officer->campusArea?->name ?? '-' }}</td>
                                <td data-label="Status">
                                    <span class="badge {{ $officer->is_active ? 'badge-positive' : 'badge-neutral' }}">
                                        {{ $officer->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td data-label="Aksi">
                                    <div class="table-actions">
                                        <a href="{{ route('admin.officers.edit', $officer) }}">Ubah</a>
                                        @if($officer->is_active)
                                            <form method="post" action="{{ route('admin.officers.deactivate', $officer) }}">
                                                @csrf
                                                @method('patch')
                                                <button class="link-button" type="submit">Nonaktifkan</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>
@endsection
