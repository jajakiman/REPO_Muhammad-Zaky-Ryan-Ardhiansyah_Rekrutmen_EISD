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
                <a class="button button-primary" href="{{ route('map.index') }}">Buka Peta Kampus</a>
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

        <section class="mt-8 rounded-2xl border border-blue-200 bg-blue-50/70 p-6 sm:p-8" aria-labelledby="report-guide-title">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-wider text-orange-700">Panduan Pelapor</p>
                <h2 id="report-guide-title" class="mt-2 text-2xl font-bold text-slate-900">Cara Membuat Laporan</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Laporan harus menunjuk fasilitas yang sudah terdata agar otomatis masuk ke petugas area yang benar.</p>
            </div>
            <ol class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <li class="rounded-xl border border-white bg-white p-5 shadow-sm">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-navy-50 text-sm font-black text-navy-900">01</span>
                    <h3 class="mt-4 text-base font-bold text-slate-900">Buka Peta Kampus</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Cari kampus atau lokasi yang fasilitasnya ingin Anda laporkan.</p>
                </li>
                <li class="rounded-xl border border-white bg-white p-5 shadow-sm">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-navy-50 text-sm font-black text-navy-900">02</span>
                    <h3 class="mt-4 text-base font-bold text-slate-900">Pilih Lokasi dan Fasilitas</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Buka detail lokasi, lalu pilih fasilitas yang mengalami kendala.</p>
                </li>
                <li class="rounded-xl border border-white bg-white p-5 shadow-sm">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-navy-50 text-sm font-black text-navy-900">03</span>
                    <h3 class="mt-4 text-base font-bold text-slate-900">Kirim Laporan</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Pilih kategori, jelaskan masalah, lalu tambahkan foto jika tersedia.</p>
                </li>
            </ol>
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
                    <a href="{{ route('map.index') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-orange-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:ring-offset-2">Temukan Fasilitas untuk Dilaporkan</a>
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
