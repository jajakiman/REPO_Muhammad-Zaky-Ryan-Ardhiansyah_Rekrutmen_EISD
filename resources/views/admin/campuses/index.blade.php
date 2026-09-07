@extends('layouts.app')
@section('title', 'Kelola Kampus | AksesLoka')
@section('content')
<section class="admin-section"><div class="container">
    <div class="page-heading"><div><p class="eyebrow">Master data</p><h1>Kelola kampus</h1></div><a class="button button-primary" href="{{ route('admin.campuses.create') }}">Tambah kampus</a></div>
    @if ($campuses->isEmpty())
        <div class="empty-state"><h2>Belum ada kampus</h2><p>Tambahkan kampus pertama untuk mulai menyusun area dan lokasi.</p></div>
    @else
        <div class="table-wrap"><table><caption class="sr-only">Daftar seluruh kampus</caption><thead><tr><th scope="col">Nama</th><th scope="col">Alamat</th><th scope="col">Koordinat</th><th scope="col">Status</th><th scope="col">Aksi</th></tr></thead><tbody>
        @foreach ($campuses as $campus)<tr>
            <th scope="row">{{ $campus->name }}</th><td>{{ $campus->address ?: '-' }}</td><td>{{ $campus->latitude !== null ? $campus->latitude.', '.$campus->longitude : '-' }}</td>
            <td><span class="badge {{ $campus->is_active ? 'badge-positive' : 'badge-neutral' }}">{{ $campus->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
            <td><div class="table-actions"><a href="{{ route('admin.campuses.edit', $campus) }}">Ubah</a>@if ($campus->is_active)<form method="post" action="{{ route('admin.campuses.deactivate', $campus) }}">@csrf @method('patch')<button class="link-button" type="submit">Nonaktifkan</button></form>@endif</div></td>
        </tr>@endforeach
        </tbody></table></div>
    @endif
</div></section>
@endsection
