@extends('layouts.app')

@section('title', 'AksesLoka | Aksesibilitas Kampus')

@section('content')
    <!-- Hero Section -->
    <section class="hero" id="mulai">
        <div class="container hero-grid">
            <div>
                <p class="eyebrow">Informasi aksesibilitas kampus</p>
                <h1>Temukan fasilitas yang mendukung perjalanan Anda.</h1>
                <p class="lead">Lihat informasi fasilitas aksesibilitas dan laporkan masalah yang Anda temukan di lingkungan kampus.</p>
                <div class="actions">
                    <a class="button button-primary" href="{{ route('map.index') }}">Lihat peta</a>
                    <a class="button button-secondary" href="#tentang">Pelajari layanan</a>
                </div>
            </div>
            <aside class="service-card" aria-labelledby="service-title">
                <p class="eyebrow">Layanan publik</p>
                <h2 id="service-title">Informasi yang praktis sebelum berkunjung</h2>
                <ul class="feature-list">
                    <li>Periksa fasilitas pada lokasi kampus.</li>
                    <li>Lihat kondisi fasilitas terkini.</li>
                    <li>Gunakan kanal pelaporan yang terstruktur.</li>
                </ul>
            </aside>
        </div>
    </section>

    <!-- Statistik Operasional Real Database -->
    <section class="info-section" style="background: var(--white); border-bottom: 1px solid var(--slate-200);">
        <div class="container">
            <div class="section-title-wrap" style="text-align: center; margin-bottom: 2rem;">
                <p class="eyebrow">Data Nyata</p>
                <h2>Statistik Operasional</h2>
                <p class="lead" style="margin-inline: auto;">Data operasional fasilitas dan laporan penanganan yang terdata langsung di sistem AksesLoka.</p>
            </div>

            <div class="stat-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <div class="service-card" style="text-align: center; border: 1px solid var(--slate-200);">
                    <p style="font-size: 2.25rem; font-weight: 800; color: var(--navy-900); margin: 0;">{{ $stats['totalCampuses'] }}</p>
                    <p class="field-hint" style="font-weight: 700; margin-top: 0.25rem;">Kampus Terpetakan</p>
                </div>
                <div class="service-card" style="text-align: center; border: 1px solid var(--slate-200);">
                    <p style="font-size: 2.25rem; font-weight: 800; color: var(--navy-900); margin: 0;">{{ $stats['totalLocations'] }}</p>
                    <p class="field-hint" style="font-weight: 700; margin-top: 0.25rem;">Lokasi Kampus</p>
                </div>
                <div class="service-card" style="text-align: center; border: 1px solid var(--slate-200);">
                    <p style="font-size: 2.25rem; font-weight: 800; color: var(--navy-900); margin: 0;">{{ $stats['totalFacilities'] }}</p>
                    <p class="field-hint" style="font-weight: 700; margin-top: 0.25rem;">Fasilitas Terdata</p>
                </div>
                <div class="service-card" style="text-align: center; border: 1px solid var(--slate-200);">
                    <p style="font-size: 2.25rem; font-weight: 800; color: var(--navy-900); margin: 0;">{{ $stats['totalResolvedReports'] }}</p>
                    <p class="field-hint" style="font-weight: 700; margin-top: 0.25rem;">Laporan Diselesaikan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Alur Kerja Penanganan -->
    <section class="info-section">
        <div class="container">
            <div class="section-title-wrap" style="text-align: center; margin-bottom: 2.5rem;">
                <p class="eyebrow">Alur Kerja Penanganan</p>
                <h2>Sistem Penanganan Tertutup</h2>
                <p class="lead" style="margin-inline: auto;">Bagaimana kendala fasilitas ditangani dari awal pelaporan hingga selesai diperbaiki.</p>
            </div>

            <div class="workflow-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem;">
                <div class="service-card" style="border: 1px solid var(--slate-200);">
                    <p class="eyebrow" style="color: var(--orange-700);">Langkah 1</p>
                    <h3 style="font-size: 1.15rem; margin-top: 0.5rem;">Fasilitas Terdata</h3>
                    <p style="color: var(--slate-700); font-size: 0.95rem;">Admin memetakan fasilitas seperti ramp, lift, guiding block, dan toilet aksesibel di lokasi kampus.</p>
                </div>
                <div class="service-card" style="border: 1px solid var(--slate-200);">
                    <p class="eyebrow" style="color: var(--orange-700);">Langkah 2</p>
                    <h3 style="font-size: 1.15rem; margin-top: 0.5rem;">Masalah Dilaporkan</h3>
                    <p style="color: var(--slate-700); font-size: 0.95rem;">Sivitas dan pengunjung kampus membuat laporan kendala fasilitas secara terstruktur dengan bukti foto.</p>
                </div>
                <div class="service-card" style="border: 1px solid var(--slate-200);">
                    <p class="eyebrow" style="color: var(--orange-700);">Langkah 3</p>
                    <h3 style="font-size: 1.15rem; margin-top: 0.5rem;">Verifikasi & Klaim</h3>
                    <p style="color: var(--slate-700); font-size: 0.95rem;">Petugas area memverifikasi validitas kendala, menentukan prioritas, dan mengambil tanggung jawab penanganan.</p>
                </div>
                <div class="service-card" style="border: 1px solid var(--slate-200);">
                    <p class="eyebrow" style="color: var(--orange-700);">Langkah 4</p>
                    <h3 style="font-size: 1.15rem; margin-top: 0.5rem;">Penanganan & Selesai</h3>
                    <p style="color: var(--slate-700); font-size: 0.95rem;">Tindakan perbaikan dilakukan. Kondisi fasilitas pada peta diperbarui secara otomatis setelah penanganan tuntas.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Komitmen SDG 11.7 -->
    <section class="info-section" style="background: var(--white); border-top: 1px solid var(--slate-200); border-bottom: 1px solid var(--slate-200);">
        <div class="container narrow" style="text-align: center;">
            <p class="eyebrow" style="color: var(--emerald-800);">Komitmen Inklusi</p>
            <h2>Mendukung SDG 11.7</h2>
            <p style="color: var(--slate-700); line-height: 1.7; font-size: 1.05rem;">
                AksesLoka berkontribusi pada pencapaian <strong>SDG 11.7</strong>, yaitu menyediakan akses universal terhadap ruang publik dan ruang hijau yang aman, inklusif, dan mudah diakses, terutama bagi perempuan, anak-anak, lansia, dan penyandang disabilitas di lingkungan perguruan tinggi.
            </p>
            <div style="margin-top: 2rem;">
                <a class="button button-primary" href="{{ route('map.index') }}">Mulai Jelajahi Peta Kampus</a>
            </div>
        </div>
    </section>

    <!-- Tentang Layanan -->
    <section class="info-section" id="tentang" aria-labelledby="about-title">
        <div class="container narrow">
            <p class="eyebrow">Tentang AksesLoka</p>
            <h2 id="about-title">Wayfinding yang jelas, tanpa klaim berlebihan</h2>
            <p>AksesLoka menyediakan data fasilitas, kondisi terkini, dan kanal tindak lanjut. Informasi ini tidak menggantikan audit aksesibilitas profesional.</p>
        </div>
    </section>
@endsection
