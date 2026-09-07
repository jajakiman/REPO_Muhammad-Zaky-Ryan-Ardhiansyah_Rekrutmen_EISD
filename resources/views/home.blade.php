@extends('layouts.app')

@section('title', 'AksesLoka | Aksesibilitas Kampus')

@section('content')
    <!-- Hero Section -->
    <section class="hero relative overflow-hidden bg-gradient-to-br from-navy-950 via-navy-900 to-slate-900 text-white py-16 md:py-24" id="mulai">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-orange-400 text-xs font-bold uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                        Informasi Aksesibilitas Kampus Bandung
                    </div>
                    <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight leading-[1.15] text-white">
                        Temukan fasilitas yang mendukung perjalanan Anda.
                    </h1>
                    <p class="text-lg sm:text-xl text-slate-300 max-w-2xl leading-relaxed">
                        Lihat informasi fasilitas aksesibilitas dan laporkan masalah fasilitas yang Anda temukan di lingkungan kampus secara terpusat dan transparan.
                    </p>
                    <div class="actions flex flex-wrap items-center gap-4 pt-2">
                        <a class="button button-primary inline-flex items-center gap-2 bg-orange-700 hover:bg-orange-800 text-white font-bold px-6 py-3.5 rounded-xl shadow-lg shadow-orange-950/30 transition-all text-base" href="{{ route('map.index') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                            Lihat peta
                        </a>
                        <a class="button button-secondary inline-flex items-center text-slate-200 hover:text-white bg-white/10 hover:bg-white/15 border border-white/20 font-semibold px-5 py-3.5 rounded-xl transition-all text-base" href="#tentang">
                            Pelajari layanan
                        </a>
                    </div>
                </div>

                <!-- Preview / Highlights Card -->
                <aside class="lg:col-span-5 bg-white text-slate-900 p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xl" aria-labelledby="service-title">
                    <div class="flex items-center gap-3 mb-4">
                        <x-logo variant="mark" size="sm" />
                        <div>
                            <p class="eyebrow text-orange-700 text-xs font-bold uppercase tracking-wider m-0">Layanan publik</p>
                            <h2 id="service-title" class="font-display text-xl font-bold text-slate-900 m-0">Informasi praktis sebelum berkunjung</h2>
                        </div>
                    </div>
                    <ul class="feature-list space-y-3.5 text-slate-700 text-sm sm:text-base">
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-50 text-navy-900 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">&check;</span>
                            <span>Periksa ketersediaan ramp, lift, guiding block, toilet, dan fasilitas lain.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-50 text-navy-900 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">&check;</span>
                            <span>Ketahui kondisi aktual fasilitas (baik, rusak, terhalang) sebelum tiba di lokasi.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-50 text-navy-900 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">&check;</span>
                            <span>Gunakan kanal pelaporan terstruktur dengan penanganan langsung oleh petugas.</span>
                        </li>
                    </ul>
                </aside>
            </div>
        </div>
    </section>

    <!-- Statistik Operasional Real Database -->
    <section class="info-section bg-white border-b border-slate-200 py-16">
        <div class="container mx-auto px-4">
            <div class="section-title-wrap text-center max-w-3xl mx-auto mb-12">
                <p class="eyebrow text-orange-700 text-xs font-bold uppercase tracking-wider mb-2">Data Nyata</p>
                <h2 class="font-display text-3xl font-bold text-slate-900 tracking-tight">Statistik Operasional</h2>
                <p class="lead text-slate-600 mt-2 text-base sm:text-lg">Data operasional fasilitas dan laporan penanganan yang terdata langsung di sistem AksesLoka.</p>
            </div>

            <div class="stat-grid grid grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="service-card bg-slate-50 hover:bg-white rounded-2xl border border-slate-200 p-6 text-center shadow-sm hover:shadow-md transition-all">
                    <p class="text-4xl lg:text-5xl font-black text-navy-900 tracking-tight">{{ $stats['totalCampuses'] }}</p>
                    <p class="font-bold text-slate-700 mt-2 text-sm uppercase tracking-wide">Kampus Terpetakan</p>
                    <p class="text-xs text-slate-500 mt-1">Telkom, UPI, UTB Bandung</p>
                </div>
                <div class="service-card bg-slate-50 hover:bg-white rounded-2xl border border-slate-200 p-6 text-center shadow-sm hover:shadow-md transition-all">
                    <p class="text-4xl lg:text-5xl font-black text-navy-900 tracking-tight">{{ $stats['totalLocations'] }}</p>
                    <p class="font-bold text-slate-700 mt-2 text-sm uppercase tracking-wide">Lokasi Kampus</p>
                    <p class="text-xs text-slate-500 mt-1">Gedung, Ruang Terbuka, Halte</p>
                </div>
                <div class="service-card bg-slate-50 hover:bg-white rounded-2xl border border-slate-200 p-6 text-center shadow-sm hover:shadow-md transition-all">
                    <p class="text-4xl lg:text-5xl font-black text-navy-900 tracking-tight">{{ $stats['totalFacilities'] }}</p>
                    <p class="font-bold text-slate-700 mt-2 text-sm uppercase tracking-wide">Fasilitas Terdata</p>
                    <p class="text-xs text-slate-500 mt-1">Ramp, Lift, Toilet, Guiding Block</p>
                </div>
                <div class="service-card bg-slate-50 hover:bg-white rounded-2xl border border-slate-200 p-6 text-center shadow-sm hover:shadow-md transition-all">
                    <p class="text-4xl lg:text-5xl font-black text-emerald-800 tracking-tight">{{ $stats['totalResolvedReports'] }}</p>
                    <p class="font-bold text-slate-700 mt-2 text-sm uppercase tracking-wide">Laporan Diselesaikan</p>
                    <p class="text-xs text-slate-500 mt-1">Kondisi fasilitas ter-update</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Alur Kerja Penanganan Tertutup -->
    <section class="info-section py-16 bg-slate-50">
        <div class="container mx-auto px-4">
            <div class="section-title-wrap text-center max-w-3xl mx-auto mb-14">
                <p class="eyebrow text-orange-700 text-xs font-bold uppercase tracking-wider mb-2">Alur Kerja Penanganan</p>
                <h2 class="font-display text-3xl font-bold text-slate-900 tracking-tight">Sistem Penanganan Tertutup</h2>
                <p class="lead text-slate-600 mt-2 text-base sm:text-lg">Bagaimana kendala fasilitas ditangani secara transparan dari awal pelaporan hingga selesai diperbaiki.</p>
            </div>

            <div class="workflow-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="service-card bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:border-navy-900/30 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-navy-50 text-navy-900 font-extrabold flex items-center justify-center text-sm mb-4 border border-blue-100">01</div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Fasilitas Terdata</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Admin memetakan fasilitas seperti ramp, lift, guiding block, dan toilet aksesibel pada setiap lokasi kampus.</p>
                </div>
                <div class="service-card bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:border-navy-900/30 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-700 font-extrabold flex items-center justify-center text-sm mb-4 border border-orange-100">02</div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Masalah Dilaporkan</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Sivitas dan pengunjung kampus membuat laporan kendala fasilitas secara terstruktur dengan menyertakan bukti foto.</p>
                </div>
                <div class="service-card bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:border-navy-900/30 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-800 font-extrabold flex items-center justify-center text-sm mb-4 border border-blue-100">03</div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Verifikasi & Klaim</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Petugas area memverifikasi kendala, menentukan skala prioritas, dan mengambil tanggung jawab penanganan.</p>
                </div>
                <div class="service-card bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:border-navy-900/30 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-800 font-extrabold flex items-center justify-center text-sm mb-4 border border-emerald-100">04</div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Penanganan & Selesai</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Tindakan perbaikan dilakukan. Kondisi fasilitas pada peta diperbarui secara otomatis setelah penanganan tuntas.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Komitmen SDGs 11 -->
    <section class="info-section bg-white border-t border-b border-slate-200 py-16">
        <div class="container mx-auto px-4 max-w-3xl text-center">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold uppercase tracking-wider mb-4">
                Kota & Komunitas Berkelanjutan
            </div>
            <h2 class="font-display text-3xl font-bold text-slate-900 tracking-tight">Mendukung SDGs 11</h2>
            <p class="text-slate-600 text-base sm:text-lg leading-relaxed mt-4">
                AksesLoka berkontribusi pada pencapaian <strong>SDGs 11</strong>, yaitu mewujudkan lingkungan ruang publik kampus yang inklusif, aman, berketahanan, dan mudah diakses bagi seluruh sivitas akademika, lansia, dan penyandang disabilitas.
            </p>
            <div class="mt-8">
                <a class="button button-primary inline-flex items-center gap-2 bg-navy-900 hover:bg-navy-950 text-white font-bold px-6 py-3.5 rounded-xl shadow-md transition-all text-base" href="{{ route('map.index') }}">
                    Mulai Jelajahi Peta Kampus
                </a>
            </div>
        </div>
    </section>

    <!-- Tentang Layanan -->
    <section class="info-section py-16 bg-slate-50" id="tentang" aria-labelledby="about-title">
        <div class="container mx-auto px-4 max-w-3xl">
            <div class="service-card bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
                <p class="eyebrow text-orange-700 text-xs font-bold uppercase tracking-wider mb-2">Tentang AksesLoka</p>
                <h2 id="about-title" class="font-display text-2xl font-bold text-slate-900 mb-3">Wayfinding yang jelas, tanpa klaim berlebihan</h2>
                <p class="text-slate-600 leading-relaxed text-base">
                    AksesLoka menyediakan data fasilitas aksesibilitas, kondisi terkini, dan kanal tindak lanjut yang transparan. Sistem ini tidak menggantikan audit aksesibilitas profesional, melainkan menghubungkan pengguna dan pengelola kampus untuk mewujudkan fasilitas yang lebih terawat.
                </p>
            </div>
        </div>
    </section>
@endsection
