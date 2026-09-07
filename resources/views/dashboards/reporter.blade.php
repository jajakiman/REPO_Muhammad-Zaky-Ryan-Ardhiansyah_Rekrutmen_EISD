@extends('layouts.dashboard')

@php
    $statusLabels = [
        'submitted' => 'Menunggu Verifikasi',
        'verified' => 'Terverifikasi',
        'in_progress' => 'Sedang Ditangani',
        'resolved' => 'Selesai',
        'rejected' => 'Ditolak',
        'cancelled' => 'Dibatalkan',
    ];
@endphp

@section('title', 'Dashboard Pelapor | AksesLoka')

@section('content')
<section class="reporter-dashboard py-4 sm:py-6">
    <div class="w-full">
        <div class="page-heading items-start">
            <div>
                <p class="eyebrow">Area Pelapor</p>
                <h1>Dashboard Pelapor</h1>
                <p class="reporter-dashboard-intro mt-2 max-w-2xl text-base leading-7 text-slate-600">Selamat datang di AksesLoka. Temukan fasilitas kampus, kirim laporan kendala, dan pantau progres penanganannya dari satu tempat.</p>
            </div>
            <div class="actions">
                <a class="button button-primary" href="{{ route('reporter.reports.create') }}">Buat Laporan Baru</a>
                <a class="button button-secondary" href="{{ route('map.index') }}">Lihat Peta</a>
                <a class="button button-secondary" href="{{ route('reporter.reports.index') }}">Semua Laporan Saya</a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="stat-grid mt-8 grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="reporter-metric-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md">
                <p class="text-4xl font-black tracking-tight text-navy-900">{{ $totalReports }}</p>
                <p class="mt-2 text-sm font-bold text-slate-700">Total Laporan</p>
            </div>
            <div class="reporter-metric-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md">
                <p class="text-4xl font-black tracking-tight text-orange-700">{{ $activeReports }}</p>
                <p class="mt-2 text-sm font-bold text-slate-700">Laporan Aktif</p>
            </div>
            <div class="reporter-metric-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md">
                <p class="text-4xl font-black tracking-tight text-emerald-800">{{ $resolvedReports }}</p>
                <p class="mt-2 text-sm font-bold text-slate-700">Laporan Selesai</p>
            </div>
        </div>

        <section class="mt-8 grid gap-5 rounded-2xl bg-navy-950 p-6 text-white sm:grid-cols-[1fr_auto] sm:items-center sm:p-8" aria-labelledby="report-action-title">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-orange-400">Pelaporan Cepat</p>
                <h2 id="report-action-title" class="mt-2 text-2xl font-bold text-white">Temukan kendala fasilitas?</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-200">Pilih lokasi dan fasilitas langsung pada formulir, jelaskan kendalanya, lalu sertakan foto jika tersedia.</p>
            </div>
            <a class="button button-primary min-h-11 justify-center" href="{{ route('reporter.reports.create') }}">Buat Laporan Baru</a>
        </section>

        <!-- Recent Reports Table -->
        <div class="recent-section" style="margin-top: 3rem;">
            <div class="page-heading">
                <div>
                    <h2>Laporan Terbaru</h2>
                    <p class="field-hint">Maksimal 5 laporan terakhir yang Anda buat.</p>
                </div>
            </div>

            @if($recentReports->isEmpty())
                <div class="empty-state text-center">
                    <h3 class="text-lg font-bold text-slate-900">Belum ada laporan</h3>
                    <p class="mt-2 text-sm text-slate-600">Anda belum membuat laporan kendala fasilitas.</p>
                    <a href="{{ route('reporter.reports.create') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-orange-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:ring-offset-2">Buat Laporan Pertama</a>
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <caption class="sr-only">Daftar laporan terbaru milik pelapor</caption>
                        <thead>
                            <tr>
                                <th scope="col">Kode</th>
                                <th scope="col">Lokasi & Kampus</th>
                                <th scope="col">Fasilitas</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentReports as $report)
                                <tr>
                                    <th scope="row" data-label="Kode"><code>{{ $report->report_code }}</code></th>
                                    <td data-label="Lokasi & Kampus">
                                        <strong>{{ $report->locationAccessibilityFeature->campusLocation->name }}</strong>
                                        <br>
                                        <span class="field-hint">{{ $report->locationAccessibilityFeature->campusLocation->campusArea->campus->name }}</span>
                                    </td>
                                    <td data-label="Fasilitas">
                                        {{ $report->locationAccessibilityFeature->accessibilityFeature->name }}
                                        <br>
                                        <span class="field-hint">{{ Str::limit($report->description, 40) }}</span>
                                    </td>
                                    <td data-label="Status">
                                        <span class="badge badge-{{ $report->status }}">
                                            {{ $statusLabels[$report->status] ?? $report->status }}
                                        </span>
                                    </td>
                                    <td data-label="Aksi">
                                        <div class="table-actions">
                                            <a href="{{ route('reporter.reports.show', $report) }}" class="button button-secondary button-sm">
                                                Detail
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
