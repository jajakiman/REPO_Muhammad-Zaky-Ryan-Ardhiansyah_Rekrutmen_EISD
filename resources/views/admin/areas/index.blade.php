@extends('layouts.app')
@section('title', 'Area '.$campus->name.' | AksesLoka')
@section('content')
<section class="admin-section"><div class="container">
    <div class="page-heading"><div><p class="eyebrow">{{ $campus->name }}</p><h1>Area kampus</h1></div><div class="actions"><a class="button button-secondary" href="{{ route('admin.campuses.index') }}">Kembali ke kampus</a>@if($campus->is_active)<a class="button button-primary" href="{{ route('admin.campuses.areas.create', $campus) }}">Tambah area</a>@endif</div></div>
    @if($areas->isEmpty())<div class="empty-state"><h2>Belum ada area</h2><p>Belum ada area yang tercatat untuk kampus ini.</p></div>@else
    <div class="table-wrap"><table><caption class="sr-only">Daftar area {{ $campus->name }}</caption><thead><tr><th scope="col">Nama</th><th scope="col">Status</th><th scope="col">Aksi</th></tr></thead><tbody>
    @foreach($areas as $area)<tr><th scope="row">{{ $area->name }}</th><td><span class="badge {{ $area->is_active ? 'badge-positive' : 'badge-neutral' }}">{{ $area->is_active ? 'Aktif' : 'Nonaktif' }}</span></td><td><div class="table-actions"><a href="{{ route('admin.campuses.areas.locations.index', [$campus, $area]) }}">Lokasi</a><a href="{{ route('admin.campuses.areas.edit', [$campus, $area]) }}">Ubah</a>@if($area->is_active)<form method="post" action="{{ route('admin.campuses.areas.deactivate', [$campus, $area]) }}">@csrf @method('patch')<button class="link-button" type="submit">Nonaktifkan</button></form>@endif</div></td></tr>@endforeach
    </tbody></table></div>@endif
</div></section>
@endsection
