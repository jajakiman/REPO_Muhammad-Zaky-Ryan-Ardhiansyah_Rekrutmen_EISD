@extends('layouts.dashboard')
@section('title', 'Area '.$campus->name.' | AksesLoka')
@section('content')
<section class="admin-section"><div class="container">
    <div class="page-heading"><div><p class="eyebrow">{{ $campus->name }}</p><h1>Area kampus</h1></div><div class="actions"><x-back-link :href="route('admin.campuses.index')">Kembali ke kampus</x-back-link>@if($campus->is_active)<a class="button button-primary" href="{{ route('admin.campuses.areas.create', $campus) }}">Tambah area</a>@endif</div></div>
    @if($areas->isEmpty())<div class="empty-state"><h2>Belum ada area</h2><p>Belum ada area yang tercatat untuk kampus ini.</p></div>@else
    <div class="table-wrap"><table><caption class="sr-only">Daftar area {{ $campus->name }}</caption><thead><tr><th scope="col">Nama</th><th scope="col">Status</th><th scope="col">Aksi</th></tr></thead><tbody>
    @foreach($areas as $area)<tr><th scope="row" data-label="Nama">{{ $area->name }}</th><td data-label="Status"><x-status-switch :action="route('admin.campuses.areas.status', [$campus, $area])" :checked="$area->is_active" :label="$area->name" :disabled="!$area->is_active && !$campus->is_active" disabled-reason="Aktifkan kampus terlebih dahulu." /></td><td data-label="Aksi"><div class="table-actions"><a class="button button-secondary button-sm" href="{{ route('admin.campuses.areas.locations.index', [$campus, $area]) }}">Lokasi</a><a class="button button-secondary button-sm" href="{{ route('admin.campuses.areas.edit', [$campus, $area]) }}">Ubah</a></div></td></tr>@endforeach
    </tbody></table></div>@endif
</div></section>
@endsection
