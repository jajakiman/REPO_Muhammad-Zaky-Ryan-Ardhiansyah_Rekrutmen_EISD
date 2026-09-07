@extends('layouts.app')

@section('title', 'Peta Aksesibilitas Kampus | AksesLoka')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">

<section class="map-section">
    <div class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Peta Terbuka</p>
                <h1>Peta Aksesibilitas Kampus</h1>
                <p class="lead">Cari dan jelajahi fasilitas aksesibilitas yang tersedia di berbagai lokasi kampus Bandung.</p>
            </div>
            <div class="actions">
                <button type="button" id="gps-sort-btn" class="button button-secondary">
                    Gunakan Lokasi Terdekat (GPS)
                </button>
            </div>
        </div>
        <p id="gps-status" class="field-hint" aria-live="polite"></p>

        <!-- Form Filter -->
        <div class="filter-card">
            <form method="get" action="{{ route('map.index') }}" class="filter-grid">
                <div class="field">
                    <label for="filter-q">Cari nama lokasi</label>
                    <input id="filter-q" name="q" value="{{ request('q') }}" placeholder="Contoh: Perpustakaan, Gedung..." autocomplete="off">
                </div>

                <div class="field">
                    <label for="filter-campus">Kampus</label>
                    <select id="filter-campus" name="campus_id">
                        <option value="">Semua Kampus</option>
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}" @selected((string) request('campus_id', (auth()->check() && !request()->has('campus_id') ? auth()->user()->campus_id : '')) === (string) $campus->id)>
                                {{ $campus->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="filter-type">Tipe Lokasi</label>
                    <select id="filter-type" name="location_type">
                        <option value="">Semua Tipe</option>
                        @foreach($locationTypes as $value => $label)
                            <option value="{{ $value }}" @selected(request('location_type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="filter-status">Status Aksesibilitas</label>
                    <select id="filter-status" name="accessibility_status">
                        <option value="">Semua Status</option>
                        @foreach($accessibilityStatuses as $value => $label)
                            <option value="{{ $value }}" @selected(request('accessibility_status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="button button-primary">Terapkan Filter</button>
                    @if(request()->hasAny(['q', 'campus_id', 'location_type', 'accessibility_status']))
                        <a href="{{ route('map.index') }}" class="button button-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Peta Interaktif Leaflet -->
        <div class="map-container-wrap">
            <div id="map" class="map-canvas" style="min-height: 440px; width: 100%; border-radius: var(--radius-card); border: 1px solid var(--slate-200);"></div>
            <p class="map-attribution-note">
                Peta ditenagai oleh <strong>Leaflet</strong> dengan data peta &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> kontributor.
            </p>
        </div>

        <!-- Daftar Tekstual Lokasi -->
        <div class="textual-locations-wrap">
            <div class="section-title-wrap">
                <h2>Daftar Lokasi Kampus</h2>
                <p>Alternatif daftar tekstual lokasi untuk kemudahan navigasi dan aksesibilitas pembaca layar.</p>
            </div>

            @if($locations->isEmpty())
                <div class="empty-state">
                    <h3>Tidak ada lokasi ditemukan</h3>
                    <p>Tidak ada lokasi yang sesuai dengan filter pencarian.</p>
                </div>
            @else
                <div class="locations-grid" id="location-text-list">
                    @foreach($locations as $loc)
                        <article class="location-item card" data-lat="{{ $loc->latitude }}" data-lng="{{ $loc->longitude }}" data-distance="">
                            <div class="card-header">
                                <span class="badge badge-{{ $loc->accessibility_status }}">
                                    {{ $accessibilityStatuses[$loc->accessibility_status] ?? $loc->accessibility_status }}
                                </span>
                                <span class="location-distance badge badge-neutral" style="display: none;"></span>
                            </div>
                            <h3>
                                <a href="{{ route('locations.show', $loc) }}">{{ $loc->name }}</a>
                            </h3>
                            <p class="location-meta">
                                <strong>{{ $loc->campusArea->campus->name }}</strong> &bull; {{ $loc->campusArea->name }}
                            </p>
                            <p class="location-type">
                                Tipe: {{ $locationTypes[$loc->location_type] ?? $loc->location_type }}
                            </p>
                            @if($loc->description)
                                <p class="location-desc">{{ Str::limit($loc->description, 100) }}</p>
                            @endif
                            <div class="card-footer">
                                <a class="button button-secondary button-sm" href="{{ route('locations.show', $loc) }}">
                                    Detail Fasilitas
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="{{ asset('js/map.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof window.initAksesLokaMap === 'function') {
            window.initAksesLokaMap(@json($mapMarkers));
        }
    });
</script>
@endsection
