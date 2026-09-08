@extends(auth()->check() ? 'layouts.dashboard' : 'layouts.app')

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

<section class="location-detail-workspace py-4 sm:py-6">
    <div class="location-detail-container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="breadcrumb-nav">
            <a href="{{ route('map.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-navy-900 hover:text-orange-700 transition-colors">
                &larr; Kembali ke Peta Aksesibilitas
            </a>
        </div>

        @if(auth()->user()?->role === 'admin')
            <nav class="flex flex-wrap gap-3" aria-label="Aksi pengelolaan lokasi">
                <a class="button button-secondary" href="{{ route('admin.campuses.areas.locations.index', [$location->campusArea->campus, $location->campusArea]) }}">Kembali ke daftar lokasi</a>
                <a class="button button-secondary" href="{{ route('admin.campuses.areas.locations.edit', [$location->campusArea->campus, $location->campusArea, $location]) }}">Ubah lokasi</a>
                <a class="button button-primary" href="{{ route('admin.locations.features.index', $location) }}">Kelola fasilitas</a>
            </nav>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-8 rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                <p class="eyebrow text-orange-700 text-xs font-bold uppercase tracking-wider">{{ $location->campusArea->campus->name }} &bull; {{ $location->campusArea->name }}</p>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">{{ $location->name }}</h1>
                <div class="flex flex-wrap items-center gap-3 pt-1">
                    <span class="badge badge-{{ $location->accessibility_status }}">
                        {{ $statusLabels[$location->accessibility_status] ?? $location->accessibility_status }}
                    </span>
                    <span class="px-3 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-bold">
                        Tipe: {{ $typeLabels[$location->location_type] ?? $location->location_type }}
                    </span>
                </div>
                @if($location->description)
                    <p class="text-slate-600 text-base leading-relaxed pt-2">{{ $location->description }}</p>
                @else
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-600" role="status">
                        Deskripsi lokasi belum tersedia.
                    </div>
                @endif
                <div class="pt-2 border-t border-slate-100 flex items-center gap-2 text-xs text-slate-500">
                    <span>Koordinat:</span>
                    <code class="px-2 py-0.5 rounded bg-slate-100 text-slate-800 font-mono">{{ $location->latitude }}, {{ $location->longitude }}</code>
                </div>
            </div>

            <!-- Mini Map -->
            <div class="mini-map-wrap relative isolate z-0 lg:col-span-4 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm space-y-2">
                <div id="mini-map" class="w-full h-56 rounded-xl overflow-hidden bg-slate-100 border border-slate-200"></div>
                <p class="text-[11px] text-slate-500 text-center">
                    &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener" class="underline">OpenStreetMap</a>
                </p>
            </div>
        </div>

        <!-- Inventaris Fasilitas -->
        <div class="space-y-4 pt-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Fasilitas Aksesibilitas di Lokasi Ini</h2>
                <p class="text-slate-600 text-sm">Informasi ketersediaan dan kondisi aktual fasilitas penunjang di lokasi ini.</p>
            </div>

            @if($location->locationAccessibilityFeatures->isEmpty())
                <div class="empty-state rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">Belum ada data fasilitas</h3>
                    <p class="text-slate-600 text-sm max-w-md mx-auto mt-1">Belum ada fasilitas aksesibilitas yang dicatat untuk lokasi ini.</p>
                </div>
            @else
                <div class="table-wrap rounded-2xl border border-slate-200 bg-white shadow-sm overflow-x-auto">
                    <table class="w-full">
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
                                    <th scope="row" data-label="Fasilitas" class="font-bold text-slate-900">
                                        {{ $laf->accessibilityFeature->name }}
                                    </th>
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
                                    <td data-label="Catatan" class="text-sm text-slate-600">{{ $laf->notes ?: 'Catatan belum tersedia' }}</td>
                                    <td data-label="Terakhir Diperiksa" class="text-xs text-slate-500">{{ $laf->last_checked_at ? $laf->last_checked_at->format('d M Y, H:i') : 'Belum pernah diperiksa' }}</td>
                                    <td data-label="Aksi Pelaporan">
                                        @auth
                                            @if(auth()->user()->role === 'reporter')
                                                <a href="{{ route('reporter.reports.create', ['facility_id' => $laf->id]) }}" class="button button-primary button-sm inline-flex items-center px-3.5 py-1.5 rounded-lg text-xs font-bold text-white bg-orange-700 hover:bg-orange-800 shadow-xs">
                                                    Laporkan Masalah
                                                </a>
                                            @else
                                                <span class="text-xs text-slate-400 font-medium">Hanya pelapor</span>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="button button-secondary button-sm inline-flex items-center px-3.5 py-1.5 rounded-lg text-xs font-semibold text-navy-900 bg-slate-100 hover:bg-slate-200 border border-slate-300" title="Masuk untuk membuat laporan masalah">
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

        setTimeout(function () {
            map.invalidateSize();
        }, 200);
    });
</script>
@endsection
