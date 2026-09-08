@extends('layouts.dashboard')

@php
    $statusLabels = [
        'submitted' => 'Menunggu Verifikasi',
        'verified' => 'Terverifikasi',
        'in_progress' => 'Sedang Ditangani',
        'resolved' => 'Selesai Ditangani',
        'rejected' => 'Ditolak',
        'cancelled' => 'Dibatalkan',
    ];
@endphp

@section('title', 'Riwayat Laporan | AksesLoka')

@section('content')
<section class="admin-section">
    <div class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Pelapor</p>
                <h1>Riwayat Laporan</h1>
                <p class="lead">Pantau perkembangan status dan tindak lanjut laporan masalah fasilitas yang telah Anda buat.</p>
            </div>
            <div class="actions">
                <a class="button button-primary" href="{{ route('reporter.reports.create') }}">Buat Laporan</a>
            </div>
        </div>

        @if($reports->isEmpty())
            <div class="empty-state">
                <h2>Belum ada laporan</h2>
                <p>Anda belum pernah membuat laporan masalah fasilitas aksesibilitas.</p>
                <p style="margin-top: 1rem;">
                    <a class="button button-secondary" href="{{ route('reporter.reports.create') }}">Buat Laporan Pertama</a>
                </p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <caption class="sr-only">Daftar laporan masalah yang diajukan</caption>
                    <thead>
                        <tr>
                            <th scope="col">Kode</th>
                            <th scope="col">Lokasi & Kampus</th>
                            <th scope="col">Fasilitas</th>
                            <th scope="col">Kategori Masalah</th>
                            <th scope="col">Status</th>
                            <th scope="col">Tanggal Dibuat</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <th scope="row" data-label="Kode">
                                    <code>{{ $report->report_code }}</code>
                                </th>
                                <td data-label="Lokasi & Kampus">
                                    <strong>{{ $report->locationAccessibilityFeature->campusLocation->name }}</strong>
                                    <br>
                                    <span class="field-hint">{{ $report->locationAccessibilityFeature->campusLocation->campusArea->campus->name }}</span>
                                </td>
                                <td data-label="Fasilitas">
                                    {{ $report->locationAccessibilityFeature->accessibilityFeature->name }}
                                </td>
                                <td data-label="Kategori Masalah">
                                    {{ $report->issueCategory->name }}
                                </td>
                                <td data-label="Status">
                                    <span class="badge badge-{{ $report->status }}">
                                        {{ $statusLabels[$report->status] ?? $report->status }}
                                    </span>
                                </td>
                                <td data-label="Tanggal Dibuat">
                                    {{ $report->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                </td>
                                <td data-label="Aksi">
                                    <div class="table-actions">
                                        <a href="{{ route('reporter.reports.show', $report) }}" class="button button-secondary button-sm">
                                            Lihat Detail
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
</section>
@endsection
