@extends('layouts.dashboard')
@section('title', 'Kelola Kampus | AksesLoka')
@section('content')
<section class="admin-section"><div class="container">
    <div class="page-heading"><div><p class="eyebrow">Master data</p><h1>Kelola kampus</h1></div><a class="button button-primary" href="{{ route('admin.campuses.create') }}">Tambah kampus</a></div>
    @if ($campuses->isEmpty())
        <div class="empty-state"><h2>Belum ada kampus</h2><p>Tambahkan kampus pertama untuk mulai menyusun area dan lokasi.</p></div>
    @else
        <div class="table-wrap"><table><caption class="sr-only">Daftar seluruh kampus</caption><thead><tr><th scope="col">Nama</th><th scope="col">Alamat</th><th scope="col">Status</th><th scope="col">Aksi</th></tr></thead><tbody>
        @foreach ($campuses as $campus)<tr>
            <th scope="row" data-label="Nama">{{ $campus->name }}</th><td data-label="Alamat">{{ $campus->address ?: 'Alamat belum tersedia' }}</td>
            <td data-label="Status"><x-status-switch :action="route('admin.campuses.status', $campus)" :checked="$campus->is_active" :label="$campus->name" /></td>
            <td data-label="Aksi"><div class="table-actions"><a class="button button-secondary button-sm" href="{{ route('admin.campuses.areas.index', $campus) }}">Area</a><a class="button button-secondary button-sm" href="{{ route('admin.campuses.edit', $campus) }}">Ubah</a></div></td>
        </tr>@endforeach
        </tbody></table></div>
    @endif
</div></section>
@endsection
