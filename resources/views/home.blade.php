@extends('layouts.app')

@section('title', 'AksesLoka | Aksesibilitas Kampus')

@section('content')
    <section class="hero relative overflow-hidden bg-gradient-to-b from-navy-950 via-slate-950 to-navy-950 pt-16 pb-24 text-white lg:pt-24 lg:pb-36" id="mulai">
        <div class="absolute inset-x-0 top-0 h-80 bg-gradient-to-b from-blue-800/20 via-orange-600/10 to-transparent pointer-events-none" aria-hidden="true"></div>
        <div class="relative z-10 px-6 mx-auto sm:px-8 lg:px-12 max-w-7xl">
            <div class="text-left md:max-w-4xl md:mx-auto md:text-center">
                <p class="inline-flex px-4 py-1.5 rounded-full bg-white/10 border border-white/15 text-orange-400 text-xs font-bold uppercase tracking-wider mb-6">
                    Pelaporan Fasilitas Kampus Bandung
                </p>
                <h1 class="tracking-tighter text-white max-w-none">
                    <span class="font-sans font-medium text-5xl sm:text-6xl lg:text-7xl">Temukan &amp; Pantau</span><br>
                    <span class="font-display italic font-normal leading-[1.15] text-6xl sm:text-7xl lg:text-8xl text-orange-400">Fasilitas Kampus</span>
                </h1>
                <p class="mt-6 font-sans text-base sm:text-lg leading-7 text-slate-300 max-w-2xl md:mx-auto">
                    Temukan fasilitas kampus, laporkan kendala, dan pantau tindak lanjut petugas dalam satu layanan yang transparan.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row items-stretch sm:items-center md:justify-center gap-3">
                    <a href="{{ route('map.index') }}" class="inline-flex min-h-11 items-center justify-center px-8 py-3 font-sans text-base font-semibold rounded-full bg-orange-700 text-white hover:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 focus:ring-offset-navy-950 transition-colors">Lihat Peta Kampus</a>
                    <a href="#alur-kerja" class="inline-flex min-h-11 items-center justify-center px-8 py-3 font-sans text-base font-semibold rounded-full bg-white/10 text-white border border-white/20 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white focus:ring-offset-navy-950 transition-colors">Pelajari Alur Layanan</a>
                </div>
            </div>
        </div>

        <div class="relative z-20 max-w-5xl px-6 mx-auto mt-14 -mb-16 sm:px-8 sm:-mb-20 lg:px-12 lg:-mb-28">
            <div class="overflow-hidden rounded-2xl border border-white/20 bg-slate-900 shadow-2xl p-2 sm:p-4">
                <div class="relative overflow-hidden rounded-xl bg-white min-h-72 sm:min-h-80">
                    <div class="absolute inset-0 bg-gradient-to-br from-slate-50 via-blue-50 to-orange-50" aria-hidden="true"></div>
                    <div class="relative grid min-h-72 sm:min-h-80 grid-cols-1 md:grid-cols-[0.8fr_1.2fr] items-center gap-6 p-6 sm:p-10">
                        <div class="flex justify-center">
                            <x-logo variant="badge" size="lg" />
                        </div>
                        <div class="text-center md:text-left">
                            <p class="font-sans text-xs font-bold uppercase tracking-wider text-orange-700">Informasi Terpusat</p>
                            <h2 class="font-display text-3xl font-bold text-slate-900 mt-2">Pemetaan Kampus Bandung</h2>
                            <p class="font-sans text-slate-600 mt-3 max-w-xl">Lihat lokasi dan kondisi fasilitas pada kampus yang sudah terdata, lalu buka detailnya sebelum berkunjung.</p>
                            <div class="mt-5 flex flex-wrap items-center justify-center md:justify-start gap-2">
                                @forelse($campuses as $c)
                                    <span class="badge badge-neutral text-xs font-medium">{{ $c->name }}</span>
                                @empty
                                    <span class="badge badge-neutral text-xs font-medium">Kampus Terdaftar</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistik Operasional Real Database -->
    <section class="info-section bg-white border-b border-slate-200 pt-28 pb-16 lg:pt-40" id="statistik">
        <div class="container mx-auto px-4">
            <div class="section-title-wrap text-center max-w-3xl mx-auto mb-12">
                <p class="eyebrow text-orange-700 text-xs font-bold uppercase tracking-wider mb-2">Data Nyata</p>
                <h2 class="font-display text-3xl font-bold text-slate-900 tracking-tight">Statistik Operasional</h2>
                <p class="lead mx-auto max-w-2xl text-center text-slate-600 mt-2 text-base sm:text-lg">Data operasional fasilitas dan laporan penanganan yang terdata langsung di sistem AksesLoka.</p>
            </div>

            <div class="stat-grid grid grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="service-card bg-slate-50 hover:bg-white rounded-2xl border border-slate-200 p-6 text-center shadow-sm hover:shadow-md transition-all">
                    <p class="stat-number text-4xl lg:text-5xl font-black text-navy-900 tracking-tight">{{ $stats['totalCampuses'] }}</p>
                    <p class="font-bold text-slate-700 mt-2 text-sm uppercase tracking-wide">Kampus Terpetakan</p>
                    <p class="text-xs text-slate-500 mt-1">
                        @if($campuses->isNotEmpty())
                            {{ $campuses->pluck('name')->implode(', ') }}
                        @else
                            Kampus terdaftar di sistem
                        @endif
                    </p>
                </div>
                <div class="service-card bg-slate-50 hover:bg-white rounded-2xl border border-slate-200 p-6 text-center shadow-sm hover:shadow-md transition-all">
                    <p class="stat-number text-4xl lg:text-5xl font-black text-navy-900 tracking-tight">{{ $stats['totalLocations'] }}</p>
                    <p class="font-bold text-slate-700 mt-2 text-sm uppercase tracking-wide">Lokasi Kampus</p>
                    <p class="text-xs text-slate-500 mt-1">Gedung, Ruang Terbuka, Halte</p>
                </div>
                <div class="service-card bg-slate-50 hover:bg-white rounded-2xl border border-slate-200 p-6 text-center shadow-sm hover:shadow-md transition-all">
                    <p class="stat-number text-4xl lg:text-5xl font-black text-navy-900 tracking-tight">{{ $stats['totalFacilities'] }}</p>
                    <p class="font-bold text-slate-700 mt-2 text-sm uppercase tracking-wide">Fasilitas Terdata</p>
                    <p class="text-xs text-slate-500 mt-1">Ramp, Lift, Toilet, Guiding Block</p>
                </div>
                <div class="service-card bg-slate-50 hover:bg-white rounded-2xl border border-slate-200 p-6 text-center shadow-sm hover:shadow-md transition-all">
                    <p class="stat-number text-4xl lg:text-5xl font-black text-emerald-800 tracking-tight">{{ $stats['totalResolvedReports'] }}</p>
                    <p class="font-bold text-slate-700 mt-2 text-sm uppercase tracking-wide">Laporan Diselesaikan</p>
                    <p class="text-xs text-slate-500 mt-1">Kondisi fasilitas ter-update</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Alur Kerja Penanganan Tertutup -->
    <section class="info-section py-16 bg-slate-50" id="alur-kerja">
        <div class="container mx-auto px-4">
            <div class="section-title-wrap text-center max-w-3xl mx-auto mb-14">
                <p class="eyebrow text-orange-700 text-xs font-bold uppercase tracking-wider mb-2">Alur Kerja Penanganan</p>
                <h2 class="font-display text-3xl font-bold text-slate-900 tracking-tight">Sistem Penanganan Tertutup</h2>
                <p class="lead mx-auto max-w-2xl text-center text-slate-600 mt-2 text-base sm:text-lg">Bagaimana kendala fasilitas ditangani secara transparan dari awal pelaporan hingga selesai diperbaiki.</p>
            </div>

            <div class="workflow-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="workflow-card service-card flex h-full flex-col items-center bg-white p-6 text-center rounded-2xl border border-slate-200 shadow-sm hover:border-navy-900/30 transition-all">
                    <div class="workflow-number mx-auto w-10 h-10 rounded-xl bg-navy-50 text-navy-900 font-extrabold flex items-center justify-center text-sm mb-4 border border-blue-100">01</div>
                    <h3 class="text-center text-lg font-bold text-slate-900 mb-2">Fasilitas Terdata</h3>
                    <p class="text-center text-slate-600 text-sm leading-relaxed">Admin memetakan fasilitas seperti ramp, lift, guiding block, dan toilet aksesibel pada setiap lokasi kampus.</p>
                </div>
                <div class="workflow-card service-card flex h-full flex-col items-center bg-white p-6 text-center rounded-2xl border border-slate-200 shadow-sm hover:border-navy-900/30 transition-all">
                    <div class="workflow-number mx-auto w-10 h-10 rounded-xl bg-navy-50 text-navy-900 font-extrabold flex items-center justify-center text-sm mb-4 border border-blue-100">02</div>
                    <h3 class="text-center text-lg font-bold text-slate-900 mb-2">Masalah Dilaporkan</h3>
                    <p class="text-center text-slate-600 text-sm leading-relaxed">Sivitas dan pengunjung kampus membuat laporan kendala fasilitas secara terstruktur dengan menyertakan bukti foto.</p>
                </div>
                <div class="workflow-card service-card flex h-full flex-col items-center bg-white p-6 text-center rounded-2xl border border-slate-200 shadow-sm hover:border-navy-900/30 transition-all">
                    <div class="workflow-number mx-auto w-10 h-10 rounded-xl bg-navy-50 text-navy-900 font-extrabold flex items-center justify-center text-sm mb-4 border border-blue-100">03</div>
                    <h3 class="text-center text-lg font-bold text-slate-900 mb-2">Verifikasi & Klaim</h3>
                    <p class="text-center text-slate-600 text-sm leading-relaxed">Petugas area memverifikasi kendala, menentukan skala prioritas, dan mengambil tanggung jawab penanganan.</p>
                </div>
                <div class="workflow-card service-card flex h-full flex-col items-center bg-white p-6 text-center rounded-2xl border border-slate-200 shadow-sm hover:border-navy-900/30 transition-all">
                    <div class="workflow-number mx-auto w-10 h-10 rounded-xl bg-navy-50 text-navy-900 font-extrabold flex items-center justify-center text-sm mb-4 border border-blue-100">04</div>
                    <h3 class="text-center text-lg font-bold text-slate-900 mb-2">Penanganan & Selesai</h3>
                    <p class="text-center text-slate-600 text-sm leading-relaxed">Tindakan perbaikan dilakukan. Kondisi fasilitas pada peta diperbarui secara otomatis setelah penanganan tuntas.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Komitmen SDGs 11 -->
    <section class="info-section bg-white border-t border-b border-slate-200 py-16" id="sdgs">
        <div class="container mx-auto px-4 max-w-3xl text-center">
            <p class="eyebrow text-orange-700 text-xs font-bold uppercase tracking-wider mb-2">
                Kota &amp; Komunitas Berkelanjutan
            </p>
            <h2 class="font-display text-3xl font-bold text-slate-900 tracking-tight">Mendukung SDGs 11</h2>
            <p class="text-slate-600 text-base sm:text-lg leading-relaxed mt-4">
                AksesLoka berkontribusi pada pencapaian <strong>SDGs 11</strong>, yaitu mewujudkan lingkungan ruang publik kampus yang inklusif, aman, berketahanan, dan mudah diakses bagi seluruh sivitas akademika, lansia, dan penyandang disabilitas.
            </p>
            <div class="mt-8">
                <a class="button button-primary" href="{{ route('map.index') }}">
                    Mulai Jelajahi Peta Kampus
                </a>
            </div>
        </div>
    </section>

    <section class="faq-section bg-white border-t border-slate-200 py-20 lg:py-24" id="faq" aria-labelledby="faq-title">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="mx-auto max-w-2xl text-center">
                <h2 id="faq-title" class="font-display text-4xl sm:text-5xl font-bold tracking-tight text-slate-900">Pertanyaan yang Sering Diajukan</h2>
                <p class="mt-3 text-base leading-7 text-slate-600">Jawaban ringkas tentang akses peta, pelaporan, GPS, dan tindak lanjut fasilitas kampus.</p>
            </div>

            <div class="faq-grid mt-14 grid grid-cols-1 gap-x-12 gap-y-10 md:grid-cols-2 lg:gap-x-16 lg:gap-y-12">
                <article>
                    <h3 class="font-sans text-lg font-bold leading-7 text-slate-900">Apakah pengunjung tanpa akun bisa melihat peta dan fasilitas?</h3>
                    <p class="mt-3 text-sm sm:text-base leading-7 text-slate-600">Ya. Peta kampus, ketersediaan fasilitas, dan kondisi fasilitas dapat dilihat tanpa membuat akun atau masuk.</p>
                </article>
                <article>
                    <h3 class="font-sans text-lg font-bold leading-7 text-slate-900">Mengapa petugas hanya menangani laporan di area tertentu?</h3>
                    <p class="mt-3 text-sm sm:text-base leading-7 text-slate-600">Setiap petugas ditugaskan pada satu area kampus agar verifikasi dan penanganan lapangan memiliki tanggung jawab yang jelas.</p>
                </article>
                <article>
                    <h3 class="font-sans text-lg font-bold leading-7 text-slate-900">Siapa yang dapat membuat laporan kendala fasilitas?</h3>
                    <p class="mt-3 text-sm sm:text-base leading-7 text-slate-600">Mahasiswa, dosen, staf, dan pengunjung yang sudah terdaftar sebagai Pelapor dapat mengirim laporan dengan satu foto bukti opsional.</p>
                </article>
                <article>
                    <h3 class="font-sans text-lg font-bold leading-7 text-slate-900">Apakah sistem ini melacak koordinat GPS pengguna?</h3>
                    <p class="mt-3 text-sm sm:text-base leading-7 text-slate-600">Tidak. Koordinat hanya dipakai sementara di browser setelah tombol lokasi terdekat ditekan. Data GPS tidak dikirim atau disimpan ke database.</p>
                </article>
                <article>
                    <h3 class="font-sans text-lg font-bold leading-7 text-slate-900">Bagaimana laporan ditangani setelah dikirim?</h3>
                    <p class="mt-3 text-sm sm:text-base leading-7 text-slate-600">Laporan masuk ke antrean area, diverifikasi dan diprioritaskan oleh petugas, lalu diperbarui hingga selesai beserta kondisi fasilitas terbarunya.</p>
                </article>
                <article>
                    <h3 class="font-sans text-lg font-bold leading-7 text-slate-900">Apakah AksesLoka merupakan audit aksesibilitas resmi?</h3>
                    <p class="mt-3 text-sm sm:text-base leading-7 text-slate-600">Bukan. AksesLoka adalah kanal informasi dan pelaporan operasional. Penilaian kelayakan resmi tetap memerlukan audit aksesibilitas profesional.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- Tentang Layanan -->
    <section class="info-section py-20 lg:py-24 bg-slate-50" id="tentang" aria-labelledby="about-title">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <div class="lg:col-span-5">
                    <p class="eyebrow text-orange-700 text-xs font-bold uppercase tracking-wider mb-2">Tentang AksesLoka</p>
                    <h2 id="about-title" class="font-display text-3xl sm:text-4xl font-bold text-slate-900 tracking-tight mb-4">
                        Wayfinding yang jelas, tanpa klaim berlebihan
                    </h2>
                    <p class="text-slate-600 leading-relaxed text-base sm:text-lg">
                        AksesLoka menyediakan data fasilitas aksesibilitas, kondisi terkini, dan kanal tindak lanjut yang transparan. Sistem ini tidak menggantikan audit aksesibilitas profesional, melainkan menghubungkan pengguna dan pengelola kampus untuk mewujudkan fasilitas yang lebih terawat.
                    </p>
                </div>
                <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="service-card bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-navy-900 font-extrabold flex items-center justify-center text-sm mb-3 border border-blue-100">
                            01
                        </div>
                        <h3 class="font-sans text-base font-bold text-slate-900 mb-1.5">Informasi Aksesibel</h3>
                        <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                            Peta dan data fasilitas dapat diakses publik tanpa kewajiban memiliki akun.
                        </p>
                    </div>
                    <div class="service-card bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-navy-900 font-extrabold flex items-center justify-center text-sm mb-3 border border-blue-100">
                            02
                        </div>
                        <h3 class="font-sans text-base font-bold text-slate-900 mb-1.5">Pelaporan Terarah</h3>
                        <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                            Setiap kendala otomatis diteruskan langsung ke petugas penanggung jawab area kampus.
                        </p>
                    </div>
                    <div class="service-card bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all sm:col-span-2">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-800 font-extrabold flex items-center justify-center text-sm mb-3 border border-emerald-100">
                            03
                        </div>
                        <h3 class="font-sans text-base font-bold text-slate-900 mb-1.5">Pembaruan Kondisi Aktual</h3>
                        <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                            Kondisi fasilitas pada peta diperbarui secara otomatis setelah penanganan dinyatakan selesai oleh petugas.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
