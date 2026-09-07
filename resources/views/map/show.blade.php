@extends('layouts.app')

@php
    $typeLabels = [
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

@section('title', $location->name . ' - Detail Aksesibilitas | AksesLoka')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">

<section class="location-detail-section">
    <div class="container">
        <div class="breadcrumb-nav">
            <a href="{{ route('map.index') }}" class="back-link">&larr; Kembali ke Peta Aksesibilitas</a>
        </div>

        <div class="location-header-grid">
            <div class="location-main-info">
                <p class="eyebrow">{{ $location->campusArea->campus->name }} &bull; {{ $location->campusArea->name }}</p>
                <h1>{{ $location->name }}</h1>
                <p class="meta-row">
                    <span class="badge badge-{{ $location->accessibility_status }}">
                        {{ $statusLabels[$location->accessibility_status] ?? $location->accessibility_status }}
                    </span>
                    <span class="type-tag">Tipe: {{ $typeLabels[$location->location_type] ?? $location->location_type }}</span>
                </p>
                @if($location->description)
                    <p class="lead">{{ $location->description }}</p>
                @endif
                <p class="coords-info">
                    Koordinat: <code>{{ $location->latitude }}, {{ $location->longitude }}</code>
                </p>
            </div>

            <!-- Mini Map -->
            <div class="location-minimap-wrap">
                <div id="mini-map" style="height: 220px; width: 100%; border-radius: var(--radius-card); border: 1px solid var(--slate-200);"></div>
                <p class="field-hint" style="font-size: 0.75rem; margin-top: 0.25rem;">
                    &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>
                </p>
            </div>
        </div>

        <!-- Inventaris Fasilitas -->
        <div class="facilities-inventory-wrap" style="margin-top: 3rem;">
            <div class="section-title-wrap">
                <h2>Fasilitas Aksesibilitas di Lokasi Ini</h2>
                <p>Informasi kondisi aktual fasilitas penunjang disabilitas dan aksesibilitas.</p>
            </div>

            @if($location->locationAccessibilityFeatures->isEmpty())
                <div class="empty-state">
                    <h3>Belum ada data fasilitas</h3>
                    <p>Belum ada fasilitas aksesibilitas yang tercatat untuk lokasi ini.</p>
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <caption class="sr-only">Daftar fasilitas aksesibilitas pada {{ $location->name }}</caption>
                        <thead>
                            <tr>
                                <th scope="col">Fasilitas</th>
                                <th scope="col">Ketersediaan</th>
                                <th scope="col">Kondisi</th>
                                <th scope="col">Catatan</th>
                                <th scope="col">Terakhir Diperiksa</th>
                                <th scope="col">Aksi Pelaporan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($location->locationAccessibilityFeatures as $laf)
                                <tr>
                                    <th scope="row" data-label="Fasilitas">{{ $laf->accessibilityFeature->name }}</th>
                                    <td data-label="Ketersediaan">
                                        <span class="badge {{ $laf->availability_status === 'available' ? 'badge-positive' : 'badge-critical' }}">
                                            {{ $availabilityLabels[$laf->availability_status] ?? $laf->availability_status }}
                                        </span>
                                    </td>
                                    <td data-label="Kondisi">
                                        <span class="badge badge-{{ $laf->condition }}">
                                            {{ $conditionLabels[$laf->condition] ?? $laf->condition }}
                                        </span>
                                    </td>
                                    <td data-label="Catatan">{{ $laf->notes ?: '-' }}</td>
                                    <td data-label="Terakhir Diperiksa">{{ $laf->last_checked_at ? $laf->last_checked_at->format('d M Y, H:i') : '-' }}</td>
                                    <td data-label="Aksi Pelaporan">
                                        @auth
                                            @if(auth()->user()->role === 'reporter')
                                                <a href="{{ route('reporter.reports.create', ['facility_id' => $laf->id]) }}" class="button button-primary button-sm">
                                                    Laporkan Masalah
                                                </a>
                                            @else
                                                <span class="field-hint">Hanya pelapor</span>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="button button-secondary button-sm" title="Masuk untuk membuat laporan masalah">
                                                Laporkan Masalah
                                            </a>
                                        @endauth
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var mapEl = document.getElementById('mini-map');
        if (!mapEl || typeof L === 'undefined') return;

        var lat = {{ (float) $location->latitude }};
        var lng = {{ (float) $location->longitude }};

        var map = L.map('mini-map', {
            center: [lat, lng],
            zoom: 16,
            scrollWheelZoom: false,
            zoomControl: false
        });

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        L.circleMarker([lat, lng], {
            radius: 8,
            fillColor: '#1e3a8a',
            color: '#ffffff',
            weight: 2,
            fillOpacity: 1
        }).addTo(map);
    });
</script>
@endsection
