@extends('layouts.dashboard')

@section('title', 'Dashboard Petugas | AksesLoka')

@section('content')
<section class="officer-dashboard py-4 sm:py-6">
    <div class="w-full">
        <div class="page-heading items-start flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <p class="eyebrow text-orange-700 text-xs font-bold uppercase tracking-wider mb-1.5">{{ $area ? $area->campus->name . ' • ' . $area->name : 'Area Petugas' }}</p>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Dashboard Petugas</h1>
                <p class="mt-2 text-slate-600 text-sm sm:text-base max-w-2xl leading-relaxed">
                    Selamat datang, <strong>{{ $officer->name }}</strong>. {{ $area ? 'Anda ditugaskan pada area ' . $area->name . ' (' . $area->campus->name . ').' : 'Akun Anda belum memiliki penugasan area kampus aktif.' }}
                </p>
            </div>
            <div class="actions flex items-center gap-3">
                <a class="button button-primary inline-flex min-h-11 items-center px-5 py-2.5 rounded-xl text-sm font-bold bg-orange-700 text-white hover:bg-orange-800 shadow-sm" href="{{ route('officer.queue.index') }}">Buka Antrean Tugas</a>
                <a class="button button-secondary inline-flex min-h-11 items-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-white text-navy-900 border border-slate-300 hover:bg-slate-50" href="{{ route('officer.history.index') }}">Riwayat Penanganan</a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="stat-grid mt-8 grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="service-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
                <p class="text-4xl font-black tracking-tight text-navy-900">{{ $submittedCount }}</p>
                <p class="mt-2 text-sm font-bold text-slate-700">Menunggu Verifikasi</p>
            </div>
            <div class="service-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
                <p class="text-4xl font-black tracking-tight text-orange-700">{{ $inProgressCount }}</p>
                <p class="mt-2 text-sm font-bold text-slate-700">Sedang Ditangani</p>
            </div>
            <div class="service-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
                <p class="text-4xl font-black tracking-tight text-emerald-800">{{ $resolvedCount }}</p>
                <p class="mt-2 text-sm font-bold text-slate-700">Selesai Ditangani</p>
            </div>
        </div>

        <!-- Recent Queue Snapshot -->
        <div class="recent-section mt-10 space-y-4">
            <div class="page-heading mb-0">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Antrean Laporan Terbaru</h2>
                    <p class="text-slate-600 text-sm">Maksimal 5 laporan menunggu tindakan verifikasi pada area tugas Anda.</p>
                </div>
            </div>

            @if($recentQueue->isEmpty())
                <div class="empty-state rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">Antrean bersih</h3>
                    <p class="text-slate-600 text-sm max-w-md mx-auto mt-1">Saat ini tidak ada laporan berstatus submitted yang membutuhkan tindakan di area Anda.</p>
                </div>
            @else
                <div class="table-wrap rounded-2xl border border-slate-200 bg-white shadow-sm overflow-x-auto">
                    <table class="w-full">
                        <caption class="sr-only">Antrean laporan terbaru pada area tugas</caption>
                        <thead>
                            <tr>
                                <th scope="col">Kode</th>
                                <th scope="col">Lokasi</th>
                                <th scope="col">Fasilitas</th>
                                <th scope="col">Masalah</th>
                                <th scope="col">Pelapor</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentQueue as $report)
                                <tr>
                                    <th scope="row" data-label="Kode"><code>{{ $report->report_code }}</code></th>
                                    <td data-label="Lokasi"><strong>{{ $report->locationAccessibilityFeature->campusLocation->name }}</strong></td>
                                    <td data-label="Fasilitas">{{ $report->locationAccessibilityFeature->accessibilityFeature->name }}</td>
                                    <td data-label="Masalah">
                                        {{ $report->issueCategory->name }}
                                        <br>
                                        <span class="text-xs text-slate-500 font-normal">{{ Str::limit($report->description, 40) }}</span>
                                    </td>
                                    <td data-label="Pelapor">{{ $report->reporter->name }}</td>
                                    <td data-label="Aksi">
                                        <div class="table-actions">
                                            <a href="{{ route('officer.reports.show', $report) }}" class="button button-secondary button-sm inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold text-navy-900 hover:underline">
                                                Periksa &rarr;
                                            </a>
                                        </div>
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
@endsection
