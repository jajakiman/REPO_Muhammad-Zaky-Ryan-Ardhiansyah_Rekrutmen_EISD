@extends('layouts.dashboard')
@section('title', 'Fasilitas '.$location->name.' | AksesLoka')
@php
    $availabilityLabels = [
        'available' => 'Tersedia',
        'unavailable' => 'Tidak Tersedia',
    ];
    $conditionLabels = [
        'good' => 'Baik',
        'needs_repair' => 'Perlu Perbaikan',
        'blocked' => 'Terhalang',
        'broken' => 'Rusak',
    ];
@endphp
@section('content')<section class="admin-section"><div class="container"><div class="page-heading"><div><p class="eyebrow">{{ $location->campusArea->campus->name }} / {{ $location->campusArea->name }}</p><h1>Fasilitas {{ $location->name }}</h1></div><div class="actions"><a class="button button-secondary" href="{{ route('admin.campuses.areas.locations.index',[$location->campusArea->campus,$location->campusArea]) }}">Kembali ke lokasi</a>@if($canAssign)<a class="button button-primary" href="{{ route('admin.locations.features.create',$location) }}">Pasang fasilitas</a>@endif</div></div>
@unless($canAssign)<p class="flash flash-neutral">{{ $assignmentUnavailableMessage }}</p>@endunless
@if($assignments->isEmpty())<div class="empty-state"><h2>Belum ada fasilitas</h2><p>Belum ada fasilitas aksesibilitas yang dipasang pada lokasi ini.</p></div>@else<div class="table-wrap"><table><caption class="sr-only">Daftar fasilitas {{ $location->name }}</caption><thead><tr><th scope="col">Fasilitas</th><th scope="col">Ketersediaan</th><th scope="col">Kondisi</th><th scope="col">Terakhir diperiksa</th><th scope="col">Catatan</th><th scope="col">Aksi</th></tr></thead><tbody>@foreach($assignments as $assignment)<tr><th scope="row" data-label="Fasilitas">{{ $assignment->accessibilityFeature->name }}</th><td data-label="Ketersediaan"><span class="badge {{ $assignment->availability_status === 'available' ? 'badge-positive' : 'badge-critical' }}">{{ $availabilityLabels[$assignment->availability_status] ?? $assignment->availability_status }}</span></td><td data-label="Kondisi"><span class="badge badge-{{ $assignment->condition }}">{{ $conditionLabels[$assignment->condition] ?? $assignment->condition }}</span></td><td data-label="Terakhir diperiksa">{{ $assignment->last_checked_at?->format('d M Y H:i') ?? 'Belum pernah diperiksa' }}</td><td data-label="Catatan">{{ $assignment->notes ?: 'Catatan belum tersedia' }}</td><td data-label="Aksi"><a class="button button-secondary button-sm" href="{{ route('admin.locations.features.edit',[$location,$assignment]) }}">Ubah</a></td></tr>@endforeach</tbody></table></div>@endif</div></section>@endsection
