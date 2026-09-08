@extends('layouts.dashboard')
@section('title', 'Lokasi '.$area->name.' | AksesLoka')
@php
    $locationTypes = [
        'building' => 'Gedung',
        'library' => 'Perpustakaan',
        'worship_place' => 'Tempat Ibadah',
        'green_space' => 'Ruang Terbuka Hijau',
        'parking' => 'Area Parkir',
        'pedestrian_area' => 'Jalur Pejalan Kaki',
        'shuttle_stop' => 'Halte / Titik Kumpul',
    ];
    $statusLabels = [
        'accessible' => 'Aksesibel Penuh',
        'partially_accessible' => 'Aksesibel Sebagian',
        'inaccessible' => 'Belum Aksesibel',
        'not_assessed' => 'Belum Dinilai',
    ];
@endphp
@section('content')
<section class="admin-section"><div class="container"><div class="page-heading"><div><p class="eyebrow">{{ $campus->name }} / {{ $area->name }}</p><h1>Lokasi kampus</h1></div><div class="actions"><x-back-link :href="route('admin.campuses.areas.index', $campus)">Kembali ke area</x-back-link>@if($campus->is_active && $area->is_active)<a class="button button-primary" href="{{ route('admin.campuses.areas.locations.create', [$campus, $area]) }}">Tambah lokasi</a>@endif</div></div>
@if($locations->isEmpty())<div class="empty-state"><h2>Belum ada lokasi</h2><p>Belum ada lokasi yang tercatat untuk area ini.</p></div>@else
<div class="table-wrap"><table><caption class="sr-only">Daftar lokasi {{ $area->name }}</caption><thead><tr><th scope="col">Nama</th><th scope="col">Tipe</th><th scope="col">Koordinat</th><th scope="col">Aksesibilitas</th><th scope="col">Status</th><th scope="col">Aksi</th></tr></thead><tbody>
@foreach($locations as $location)<tr><th scope="row" data-label="Nama">{{ $location->name }}</th><td data-label="Tipe">{{ $locationTypes[$location->location_type] ?? $location->location_type }}</td><td data-label="Koordinat">{{ $location->latitude }}, {{ $location->longitude }}</td><td data-label="Aksesibilitas"><span class="badge badge-{{ $location->accessibility_status }}">{{ $statusLabels[$location->accessibility_status] ?? $location->accessibility_status }}</span></td><td data-label="Status"><x-status-switch :action="route('admin.campuses.areas.locations.status', [$campus, $area, $location])" :checked="$location->is_active" :label="$location->name" :disabled="!$location->is_active && (!$campus->is_active || !$area->is_active)" disabled-reason="Aktifkan kampus dan area terlebih dahulu." /></td><td data-label="Aksi"><div class="table-actions"><a class="button button-secondary button-sm" href="{{ route('admin.locations.features.index', $location) }}">Fasilitas</a><a class="button button-secondary button-sm" href="{{ route('admin.campuses.areas.locations.edit', [$campus, $area, $location]) }}">Ubah</a></div></td></tr>@endforeach
</tbody></table></div>@endif</div></section>
@endsection
