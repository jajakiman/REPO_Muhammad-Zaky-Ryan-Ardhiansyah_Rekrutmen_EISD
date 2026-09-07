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

@section('title', 'Dashboard Pelapor | AksesLoka')

@section('content')
<section class="admin-section">
    <div class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Area Pelapor</p>
                <h1>Dashboard Pelapor</h1>
                <p class="lead">Selamat datang di AksesLoka. Pantau pelaporan kendala fasilitas aksesibilitas Anda di sini.</p>
            </div>
            <div class="actions">
                <a class="button button-primary" href="{{ route('map.index') }}">Buka Peta Kampus</a>
                <a class="button button-secondary" href="{{ route('reporter.reports.index') }}">Semua Laporan Saya</a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="stat-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
            <div class="service-card" style="border: 1px solid var(--slate-200);">
                <p style="font-size: 2.25rem; font-weight: 800; color: var(--navy-900); margin: 0;">{{ $totalReports }}</p>
                <p class="field-hint" style="font-weight: 700; margin-top: 0.25rem;">Total Laporan</p>
            </div>
            <div class="service-card" style="border: 1px solid var(--slate-200);">
                <p style="font-size: 2.25rem; font-weight: 800; color: var(--orange-700); margin: 0;">{{ $activeReports }}</p>
                <p class="field-hint" style="font-weight: 700; margin-top: 0.25rem;">Laporan Aktif</p>
            </div>
            <div class="service-card" style="border: 1px solid var(--slate-200);">
                <p style="font-size: 2.25rem; font-weight: 800; color: var(--emerald-800); margin: 0;">{{ $resolvedReports }}</p>
                <p class="field-hint" style="font-weight: 700; margin-top: 0.25rem;">Laporan Selesai</p>
            </div>
        </div>

        <!-- Recent Reports Table -->
        <div class="recent-section" style="margin-top: 3rem;">
            <div class="page-heading">
                <div>
                    <h2>Laporan Terbaru</h2>
                    <p class="field-hint">Maksimal 5 laporan terakhir yang Anda buat.</p>
                </div>
            </div>

            @if($recentReports->isEmpty())
                <div class="empty-state">
                    <h3>Belum ada laporan</h3>
                    <p>Anda belum membuat laporan kendala fasilitas apapun.</p>
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
