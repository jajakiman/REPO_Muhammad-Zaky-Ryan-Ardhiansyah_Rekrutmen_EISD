@extends(auth()->check() ? 'layouts.dashboard' : 'layouts.app')

@section('title', 'Peta Aksesibilitas Kampus | AksesLoka')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">

<section class="map-workspace py-4 sm:py-6">
    <div class="map-container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <p class="eyebrow text-orange-700 text-xs font-bold uppercase tracking-wider mb-1.5">Peta Terbuka</p>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Peta Aksesibilitas Kampus</h1>
                <p class="mt-2 text-slate-600 text-sm sm:text-base max-w-2xl leading-relaxed">
                    Cari dan jelajahi fasilitas aksesibilitas yang tersedia di berbagai lokasi kampus Bandung.
                </p>
            </div>
            <div class="shrink-0">
                <button type="button" id="gps-sort-btn" class="button button-secondary inline-flex min-h-11 items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-navy-900 text-navy-900 bg-white hover:bg-slate-50 font-semibold text-sm transition-all shadow-xs">
                    <svg class="w-4 h-4 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Gunakan Lokasi Terdekat (GPS)
                </button>
            </div>
        </div>

        <p id="gps-status" class="field-hint text-sm text-slate-600 font-medium" aria-live="polite"></p>

        <!-- Form Filter Card -->
        <div class="filter-card rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm">
            <form method="get" action="{{ route('map.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="field mb-0">
                        <label for="filter-q" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Cari nama lokasi</label>
                        <input id="filter-q" name="q" value="{{ request('q') }}" placeholder="Contoh: Perpustakaan, Gedung..." autocomplete="off" class="w-full min-h-11 px-3.5 py-2 rounded-xl border border-slate-300 text-slate-900 text-sm focus:border-navy-900 focus:ring-2 focus:ring-orange-500/20 focus:outline-none">
                    </div>

                    <div class="field mb-0">
                        <label for="filter-campus" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kampus</label>
                        <x-select-shell>
                            <select id="filter-campus" name="campus_id" class="w-full min-h-11 px-3.5 py-2 rounded-xl border border-slate-300 text-slate-900 text-sm hover:border-navy-900 focus:border-navy-900 focus:ring-2 focus:ring-orange-500/20 focus:outline-none">
                                <option value="">Semua Kampus</option>
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}" @selected((string) request('campus_id', ($selectedCampus?->id == $campus->id ? $selectedCampus->id : '')) === (string) $campus->id)>
                                        {{ $campus->name }}
                                    </option>
                                @endforeach
                            </select>
                        </x-select-shell>
                    </div>

                    <div class="field mb-0">
                        <label for="filter-type" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tipe Lokasi</label>
                        <x-select-shell>
                            <select id="filter-type" name="location_type" class="w-full min-h-11 px-3.5 py-2 rounded-xl border border-slate-300 text-slate-900 text-sm hover:border-navy-900 focus:border-navy-900 focus:ring-2 focus:ring-orange-500/20 focus:outline-none">
                                <option value="">Semua Tipe</option>
                                @foreach($locationTypes as $value => $label)
                                    <option value="{{ $value }}" @selected(request('location_type') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </x-select-shell>
                    </div>

                    <div class="field mb-0">
                        <label for="filter-status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Status Aksesibilitas</label>
                        <x-select-shell>
                            <select id="filter-status" name="accessibility_status" class="w-full min-h-11 px-3.5 py-2 rounded-xl border border-slate-300 text-slate-900 text-sm hover:border-navy-900 focus:border-navy-900 focus:ring-2 focus:ring-orange-500/20 focus:outline-none">
                                <option value="">Semua Status</option>
                                @foreach($accessibilityStatuses as $value => $label)
                                    <option value="{{ $value }}" @selected(request('accessibility_status') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </x-select-shell>
                    </div>
                </div>

                <div class="filter-actions flex items-center justify-end gap-3 pt-2">
                    @if(request()->hasAny(['q', 'campus_id', 'location_type', 'accessibility_status']) || ($selectedCampus && !request()->has('campus_id')))
                        <a href="{{ route('map.index', ['campus_id' => '']) }}" class="button button-secondary inline-flex min-h-11 items-center px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition-colors">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit" class="button button-primary inline-flex min-h-11 items-center px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-orange-700 hover:bg-orange-800 shadow-sm transition-colors">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Container Peta Leaflet -->
        <div class="map-container-wrap relative isolate z-0 rounded-2xl border border-slate-200 bg-white p-2 sm:p-3 shadow-sm overflow-hidden">
            <div id="map" data-lazy-map class="map-canvas w-full h-[28rem] sm:h-[32rem] lg:h-[36rem] rounded-xl overflow-hidden bg-slate-100 border border-slate-200"></div>
            <p class="map-attribution-note px-2 pt-2.5 text-xs text-slate-500 flex items-center justify-between">
                <span>Peta ditenagai oleh <strong>Leaflet</strong> dengan data &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener" class="underline hover:text-navy-900">OpenStreetMap</a> kontributor.</span>
                <span class="hidden sm:inline">Koordinat GPS bersifat privat di peramban.</span>
            </p>
        </div>

        <!-- Daftar Tekstual Lokasi & Empty States -->
        <div class="textual-locations-wrap space-y-4 pt-2">
            <div class="section-title-wrap">
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Daftar Lokasi Kampus</h2>
                <p class="text-slate-600 text-sm">Alternatif daftar tekstual lokasi untuk kemudahan navigasi dan aksesibilitas pembaca layar.</p>
            </div>

            @if($locations->isEmpty())
                @if(! $hasAnyLocations)
                    {{-- 1. Belum ada lokasi di seluruh sistem --}}
                    <div class="empty-state rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-navy-900 mx-auto flex items-center justify-center font-bold text-xl mb-3">&bull;</div>
                        <h3 class="text-lg font-bold text-slate-900">Belum ada lokasi kampus yang terdata</h3>
                        <p class="text-slate-600 text-sm max-w-md mx-auto mt-2 leading-relaxed">
                            Belum ada lokasi aksesibilitas aktif yang dicatat dalam sistem. Admin pusat sedang menyiapkan data kampus, area, dan fasilitas terkait.
                        </p>
                        @if(auth()->check() && auth()->user()->role === 'admin')
                            <div class="mt-5">
                                <a href="{{ route('admin.campuses.index') }}" class="button button-primary inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-bold bg-navy-900 text-white hover:bg-navy-950 shadow-sm">
                                    Kelola Lokasi Kampus &rarr;
                                </a>
                            </div>
                        @endif
                    </div>
                @elseif($selectedCampus && (! $hasActiveFilter || request('campus_id')))
                    {{-- 2. Kampus terpilih belum memiliki lokasi --}}
                    <div class="empty-state rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                        <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-700 mx-auto flex items-center justify-center font-bold text-xl mb-3">!</div>
                        <h3 class="text-lg font-bold text-slate-900">Belum ada lokasi untuk kampus ini</h3>
                        <p class="text-slate-600 text-sm max-w-md mx-auto mt-2 leading-relaxed">
                            Kampus <strong>{{ $selectedCampus->name }}</strong> belum memiliki lokasi aktif yang dapat ditampilkan. Silakan pilih kampus lain atau tampilkan seluruh kampus.
                        </p>
                        <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                            <a href="{{ route('map.index', ['campus_id' => '']) }}" class="button button-primary inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-bold bg-orange-700 text-white hover:bg-orange-800 shadow-sm">
                                Lihat Semua Kampus
                            </a>
                            @if(auth()->check() && auth()->user()->role === 'admin')
                                <a href="{{ route('admin.campuses.index') }}" class="button button-secondary inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-semibold bg-white text-navy-900 border border-slate-300 hover:bg-slate-50">
                                    Kelola Lokasi Kampus
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- 3. Filter tidak cocok --}}
                    <div class="empty-state rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900">Tidak ada lokasi yang sesuai dengan filter pencarian.</h3>
                        <p class="text-slate-600 text-sm max-w-md mx-auto mt-2 leading-relaxed">
                            Coba ubah kata kunci pencarian, tipe gedung, atau status aksesibilitas untuk menemukan lokasi lain.
                        </p>
                        <div class="mt-5">
                            <a href="{{ route('map.index', ['campus_id' => '']) }}" class="button button-secondary inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-800 border border-slate-300 hover:bg-slate-200">
                                Reset Filter
                            </a>
                        </div>
                    </div>
                @endif
            @else
                <div class="locations-grid grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" id="location-text-list">
                    @foreach($locations as $loc)
                        <article class="location-item card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between" data-lat="{{ $loc->latitude }}" data-lng="{{ $loc->longitude }}" data-distance="">
                            <div>
                                <div class="card-header flex items-center justify-between gap-2 mb-3">
                                    <span class="badge badge-{{ $loc->accessibility_status }}">
                                        {{ $accessibilityStatuses[$loc->accessibility_status] ?? $loc->accessibility_status }}
                                    </span>
                                    <span class="location-distance badge badge-neutral text-xs font-mono" style="display: none;"></span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-1 leading-snug">
                                    <a href="{{ route('locations.show', $loc) }}" class="hover:text-navy-900 hover:underline">{{ $loc->name }}</a>
                                </h3>
                                <p class="location-meta text-xs text-slate-600 mb-2">
                                    <strong>{{ $loc->campusArea->campus->name }}</strong> &bull; {{ $loc->campusArea->name }}
                                </p>
                                <p class="location-type text-xs text-slate-500 font-medium mb-3">
                                    Tipe: {{ $locationTypes[$loc->location_type] ?? $loc->location_type }}
                                </p>
                                @if($loc->description)
                                    <p class="location-desc text-sm text-slate-600 leading-relaxed mb-4">{{ Str::limit($loc->description, 100) }}</p>
                                @endif
                            </div>
                            <div class="card-footer pt-3 border-t border-slate-100 flex items-center justify-between">
                                <a class="button button-secondary button-sm text-xs font-bold text-navy-900 hover:underline" href="{{ route('locations.show', $loc) }}">
                                    Detail Fasilitas &rarr;
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
        var mapElement = document.querySelector('[data-lazy-map]');
        var initialize = function () {
            window.initAksesLokaMap(@json($mapMarkers));
        };
        if (!('IntersectionObserver' in window)) return initialize();
        var observer = new IntersectionObserver(function (entries) {
            if (!entries[0].isIntersecting) return;
            observer.disconnect();
            initialize();
        }, { rootMargin: '240px' });
        observer.observe(mapElement);
    });
</script>
@endsection
