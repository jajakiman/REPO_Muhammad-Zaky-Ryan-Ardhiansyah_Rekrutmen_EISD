@extends('layouts.app')

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
<section class="admin-section">
    <div class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Area Admin</p>
                <h1>Dashboard Pusat</h1>
                <p class="lead">Monitoring statistik operasional dan kelola seluruh data referensi aksesibilitas kampus.</p>
            </div>
            <div class="actions">
                <a class="button button-primary" href="{{ route('admin.reports.index') }}">Monitoring Seluruh Laporan</a>
            </div>
        </div>

        <!-- Master Data Quick Navigation -->
        <div class="actions" style="margin-top: 1rem; flex-wrap: wrap;">
            <a class="button button-secondary" href="{{ route('admin.campuses.index') }}">Kelola Kampus</a>
            <a class="button button-secondary" href="{{ route('admin.features.index') }}">Kelola Fasilitas</a>
            <a class="button button-secondary" href="{{ route('admin.issue-categories.index') }}">Kelola Kategori Masalah</a>
            <a class="button button-secondary" href="{{ route('admin.officers.index') }}">Kelola Petugas</a>
        </div>

        <!-- Metric Cards -->
        <div class="stat-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.25rem; margin-top: 2rem;">
            <div class="service-card" style="border: 1px solid var(--slate-200); text-align: center;">
                <p style="font-size: 2.25rem; font-weight: 800; color: var(--navy-900); margin: 0;">{{ $totalCampuses }}</p>
                <p class="field-hint" style="font-weight: 700; margin-top: 0.25rem;">Kampus Aktif</p>
            </div>
            <div class="service-card" style="border: 1px solid var(--slate-200); text-align: center;">
                <p style="font-size: 2.25rem; font-weight: 800; color: var(--navy-900); margin: 0;">{{ $totalLocations }}</p>
                <p class="field-hint" style="font-weight: 700; margin-top: 0.25rem;">Lokasi Terdata</p>
            </div>
            <div class="service-card" style="border: 1px solid var(--slate-200); text-align: center;">
                <p style="font-size: 2.25rem; font-weight: 800; color: var(--navy-900); margin: 0;">{{ $totalFacilities }}</p>
                <p class="field-hint" style="font-weight: 700; margin-top: 0.25rem;">Fasilitas Terdata</p>
            </div>
            <div class="service-card" style="border: 1px solid var(--slate-200); text-align: center;">
                <p style="font-size: 2.25rem; font-weight: 800; color: var(--navy-900); margin: 0;">{{ $totalOfficers }}</p>
                <p class="field-hint" style="font-weight: 700; margin-top: 0.25rem;">Petugas Aktif</p>
            </div>
            <div class="service-card" style="border: 1px solid var(--slate-200); text-align: center;">
                <p style="font-size: 2.25rem; font-weight: 800; color: var(--orange-700); margin: 0;">{{ $activeReports }}</p>
                <p class="field-hint" style="font-weight: 700; margin-top: 0.25rem;">Laporan Aktif</p>
            </div>
            <div class="service-card" style="border: 1px solid var(--slate-200); text-align: center;">
                <p style="font-size: 2.25rem; font-weight: 800; color: var(--emerald-800); margin: 0;">{{ $resolvedReports }}</p>
                <p class="field-hint" style="font-weight: 700; margin-top: 0.25rem;">Laporan Selesai</p>
            </div>
        </div>

        <!-- Recent Reports Across Campuses -->
        <div class="recent-section" style="margin-top: 3rem;">
            <div class="page-heading">
                <div>
                    <h2>Laporan Terbaru Lintas Kampus</h2>
                    <p class="field-hint">Maksimal 5 laporan terbaru yang masuk ke dalam sistem.</p>
                </div>
            </div>

            @if($recentReports->isEmpty())
                <div class="empty-state">
                    <h3>Belum ada laporan</h3>
                    <p>Belum ada laporan kendala fasilitas yang diajukan oleh pengguna.</p>
                </div>
            @else
                <div class="table-wrap">
                    <table>
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
                                    <td data-label="Waktu Masuk">{{ $report->created_at->format('d M Y, H:i') }}</td>
                                    <td data-label="Aksi">
                                        <div class="table-actions">
                                            <a href="{{ route('admin.reports.show', $report) }}" class="button button-secondary button-sm">
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
