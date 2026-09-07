@extends('layouts.app')

@section('title', 'Dashboard Petugas | AksesLoka')

@section('content')
<section class="admin-section">
    <div class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">{{ $area ? $area->campus->name . ' • ' . $area->name : 'Area Petugas' }}</p>
                <h1>Dashboard Petugas</h1>
                <p class="lead">Selamat datang, <strong>{{ $officer->name }}</strong>. {{ $area ? 'Anda ditugaskan pada area ' . $area->name . ' (' . $area->campus->name . ').' : 'Akun Anda belum memiliki penugasan area kampus aktif.' }}</p>
            </div>
            <div class="actions">
                <a class="button button-primary" href="{{ route('officer.queue.index') }}">Buka Antrean Tugas</a>
                <a class="button button-secondary" href="{{ route('officer.history.index') }}">Riwayat Penanganan</a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="stat-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
            <div class="service-card" style="border: 1px solid var(--slate-200);">
                <p style="font-size: 2.25rem; font-weight: 800; color: var(--navy-900); margin: 0;">{{ $submittedCount }}</p>
                <p class="field-hint" style="font-weight: 700; margin-top: 0.25rem;">Menunggu Verifikasi (Submitted)</p>
            </div>
            <div class="service-card" style="border: 1px solid var(--slate-200);">
                <p style="font-size: 2.25rem; font-weight: 800; color: var(--orange-700); margin: 0;">{{ $inProgressCount }}</p>
                <p class="field-hint" style="font-weight: 700; margin-top: 0.25rem;">Sedang Ditangani (In Progress)</p>
            </div>
            <div class="service-card" style="border: 1px solid var(--slate-200);">
                <p style="font-size: 2.25rem; font-weight: 800; color: var(--emerald-800); margin: 0;">{{ $resolvedCount }}</p>
                <p class="field-hint" style="font-weight: 700; margin-top: 0.25rem;">Selesai Ditangani (Resolved)</p>
            </div>
        </div>

        <!-- Recent Queue Snapshot -->
        <div class="recent-section" style="margin-top: 3rem;">
            <div class="page-heading">
                <div>
                    <h2>Antrean Laporan Terbaru</h2>
                    <p class="field-hint">Maksimal 5 laporan menunggu tindakan verifikasi pada area tugas Anda.</p>
                </div>
            </div>

            @if($recentQueue->isEmpty())
                <div class="empty-state">
                    <h3>Antrean bersih</h3>
                    <p>Saat ini tidak ada laporan berstatus submitted yang membutuhkan tindakan di area Anda.</p>
                </div>
            @else
                <div class="table-wrap">
                    <table>
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
                                        <span class="field-hint">{{ Str::limit($report->description, 40) }}</span>
                                    </td>
                                    <td data-label="Pelapor">{{ $report->reporter->name }}</td>
                                    <td data-label="Aksi">
                                        <div class="table-actions">
                                            <a href="{{ route('officer.reports.show', $report) }}" class="button button-secondary button-sm">
                                                Periksa
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
