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

@section('title', 'Dashboard Admin | AksesLoka')

@section('content')
<section class="admin-dashboard py-4 sm:py-6">
    <div class="w-full">
        <div class="page-heading items-start flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <p class="eyebrow text-orange-700 text-xs font-bold uppercase tracking-wider mb-1.5">Area Admin</p>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Dashboard Pusat</h1>
                <p class="mt-2 text-slate-600 text-sm sm:text-base max-w-2xl leading-relaxed">
                    Monitoring statistik operasional dan kelola seluruh data referensi aksesibilitas kampus.
                </p>
            </div>
            <div class="actions">
                <a class="button button-primary inline-flex min-h-11 items-center px-5 py-2.5 rounded-xl text-sm font-bold bg-orange-700 text-white hover:bg-orange-800 shadow-sm" href="{{ route('admin.reports.index') }}">
                    Monitoring Seluruh Laporan
                </a>
            </div>
        </div>

        <!-- Master Data Quick Navigation -->
        <div class="actions flex flex-wrap items-center gap-2.5 mt-6">
            <a class="button button-secondary inline-flex min-h-11 items-center px-4 py-2 rounded-xl text-sm font-semibold bg-white text-navy-900 border border-slate-300 hover:bg-slate-50" href="{{ route('admin.campuses.index') }}">Kelola Kampus</a>
            <a class="button button-secondary inline-flex min-h-11 items-center px-4 py-2 rounded-xl text-sm font-semibold bg-white text-navy-900 border border-slate-300 hover:bg-slate-50" href="{{ route('admin.features.index') }}">Kelola Fasilitas</a>
            <a class="button button-secondary inline-flex min-h-11 items-center px-4 py-2 rounded-xl text-sm font-semibold bg-white text-navy-900 border border-slate-300 hover:bg-slate-50" href="{{ route('admin.issue-categories.index') }}">Kelola Kategori Masalah</a>
            <a class="button button-secondary inline-flex min-h-11 items-center px-4 py-2 rounded-xl text-sm font-semibold bg-white text-navy-900 border border-slate-300 hover:bg-slate-50" href="{{ route('admin.officers.index') }}">Kelola Petugas</a>
        </div>

        <!-- Metric Cards -->
        <div class="stat-grid mt-8 grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4">
            <div class="service-card rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm hover:shadow-md transition-shadow">
                <p class="text-3xl sm:text-4xl font-black tracking-tight text-navy-900">{{ $totalCampuses }}</p>
                <p class="mt-2 text-xs font-bold text-slate-700 uppercase tracking-wide">Kampus Aktif</p>
            </div>
            <div class="service-card rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm hover:shadow-md transition-shadow">
                <p class="text-3xl sm:text-4xl font-black tracking-tight text-navy-900">{{ $totalLocations }}</p>
                <p class="mt-2 text-xs font-bold text-slate-700 uppercase tracking-wide">Lokasi Terdata</p>
            </div>
            <div class="service-card rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm hover:shadow-md transition-shadow">
                <p class="text-3xl sm:text-4xl font-black tracking-tight text-navy-900">{{ $totalFacilities }}</p>
                <p class="mt-2 text-xs font-bold text-slate-700 uppercase tracking-wide">Fasilitas Terdata</p>
            </div>
            <div class="service-card rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm hover:shadow-md transition-shadow">
                <p class="text-3xl sm:text-4xl font-black tracking-tight text-navy-900">{{ $totalOfficers }}</p>
                <p class="mt-2 text-xs font-bold text-slate-700 uppercase tracking-wide">Petugas Aktif</p>
            </div>
            <div class="service-card rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm hover:shadow-md transition-shadow">
                <p class="text-3xl sm:text-4xl font-black tracking-tight text-orange-700">{{ $activeReports }}</p>
                <p class="mt-2 text-xs font-bold text-slate-700 uppercase tracking-wide">Laporan Aktif</p>
            </div>
            <div class="service-card rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm hover:shadow-md transition-shadow">
                <p class="text-3xl sm:text-4xl font-black tracking-tight text-emerald-800">{{ $resolvedReports }}</p>
                <p class="mt-2 text-xs font-bold text-slate-700 uppercase tracking-wide">Laporan Selesai</p>
            </div>
        </div>

        <!-- Recent Reports Across Campuses -->
        <div class="recent-section mt-10 space-y-4">
            <div class="page-heading mb-0">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Laporan Terbaru Lintas Kampus</h2>
                    <p class="text-slate-600 text-sm">Maksimal 5 laporan terbaru yang masuk ke dalam sistem.</p>
                </div>
            </div>

            @if($recentReports->isEmpty())
                <div class="empty-state rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">Belum ada laporan</h3>
                    <p class="text-slate-600 text-sm max-w-md mx-auto mt-1">Belum ada laporan kendala fasilitas yang diajukan oleh pengguna.</p>
                </div>
            @else
                <div class="table-wrap rounded-2xl border border-slate-200 bg-white shadow-sm overflow-x-auto">
                    <table class="w-full">
                        <caption class="sr-only">Laporan terbaru lintas kampus</caption>
                        <thead>
                            <tr>
                                <th scope="col">Kode</th>
                                <th scope="col">Pelapor</th>
                                <th scope="col">Kampus & Lokasi</th>
                                <th scope="col">Fasilitas</th>
                                <th scope="col">Status</th>
                                <th scope="col">Waktu Masuk</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentReports as $report)
                                <tr>
                                    <th scope="row" data-label="Kode"><code>{{ $report->report_code }}</code></th>
                                    <td data-label="Pelapor">{{ $report->reporter->name }}</td>
                                    <td data-label="Kampus & Lokasi">
                                        <strong>{{ $report->locationAccessibilityFeature->campusLocation->name }}</strong>
                                        <br>
                                        <span class="field-hint text-xs text-slate-500 font-normal">{{ $report->locationAccessibilityFeature->campusLocation->campusArea->campus->name }}</span>
                                    </td>
                                    <td data-label="Fasilitas">
                                        {{ $report->locationAccessibilityFeature->accessibilityFeature->name }}
                                        <br>
                                        <span class="field-hint text-xs text-slate-500 font-normal">{{ Str::limit($report->description, 40) }}</span>
                                    </td>
                                    <td data-label="Status">
                                        <span class="badge badge-{{ $report->status }}">
                                            {{ $statusLabels[$report->status] ?? $report->status }}
                                        </span>
                                    </td>
                                    <td data-label="Waktu Masuk">{{ $report->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</td>
                                    <td data-label="Aksi">
                                        <div class="table-actions">
                                            <a href="{{ route('admin.reports.show', $report) }}" class="button button-secondary button-sm inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold text-navy-900 hover:underline">
                                                Detail &rarr;
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
