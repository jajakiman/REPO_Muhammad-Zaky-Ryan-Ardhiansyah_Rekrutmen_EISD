@extends('layouts.app')

@section('title', 'AksesLoka | Aksesibilitas Kampus')

@section('content')
    <section class="hero" id="mulai">
        <div class="container hero-grid">
            <div>
                <p class="eyebrow">Informasi aksesibilitas kampus</p>
                <h1>Temukan fasilitas yang mendukung perjalanan Anda.</h1>
                <p class="lead">Lihat informasi fasilitas aksesibilitas dan laporkan masalah yang Anda temukan di lingkungan kampus.</p>
                <div class="actions">
                    <a class="button button-primary" href="#tentang">Lihat peta</a>
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
    <section class="info-section" id="tentang" aria-labelledby="about-title">
        <div class="container narrow">
            <p class="eyebrow">Tentang AksesLoka</p>
            <h2 id="about-title">Wayfinding yang jelas, tanpa klaim berlebihan</h2>
            <p>AksesLoka menyediakan data fasilitas, kondisi terkini, dan kanal tindak lanjut. Informasi ini tidak menggantikan audit aksesibilitas profesional.</p>
        </div>
    </section>
@endsection
