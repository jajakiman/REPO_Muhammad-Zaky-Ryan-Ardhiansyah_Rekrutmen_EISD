@extends('layouts.dashboard')

@php
    $statusLabels = [
        'resolved' => 'Selesai Ditangani',
        'rejected' => 'Ditolak',
        'cancelled' => 'Dibatalkan oleh Pelapor',
    ];
@endphp

@section('title', 'Riwayat Penanganan Area | AksesLoka')

@section('content')
<section class="admin-section">
    <div class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">{{ $area->campus->name }} &bull; {{ $area->name }}</p>
                <h1>Riwayat Penanganan Area</h1>
                <p class="lead">Arsip laporan kendala fasilitas aksesibilitas yang telah selesai, ditolak, atau dibatalkan pada area ini.</p>
            </div>
            <div class="actions">
                <a class="button button-primary" href="{{ route('officer.queue.index') }}">Antrean Tugas Aktif</a>
            </div>
        </div>

        @if($reports->isEmpty())
            <div class="empty-state">
                <h2>Belum ada riwayat</h2>
                <p>Belum ada laporan dengan status selesai, ditolak, atau dibatalkan pada area tugas ini.</p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <caption class="sr-only">Daftar riwayat penanganan laporan pada area tugas</caption>
                    <thead>
                        <tr>
                            <th scope="col">Kode</th>
                            <th scope="col">Lokasi</th>
                            <th scope="col">Fasilitas</th>
                            <th scope="col">Kategori Masalah</th>
                            <th scope="col">Pelapor</th>
                            <th scope="col">Status</th>
                            <th scope="col">Petugas</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <th scope="row" data-label="Kode">
                                    <code>{{ $report->report_code }}</code>
                                </th>
                                <td data-label="Lokasi">
                                    <strong>{{ $report->locationAccessibilityFeature->campusLocation->name }}</strong>
                                </td>
                                <td data-label="Fasilitas">
                                    {{ $report->locationAccessibilityFeature->accessibilityFeature->name }}
                                </td>
                                <td data-label="Kategori Masalah">
                                    {{ $report->issueCategory->name }}
                                </td>
                                <td data-label="Pelapor">
                                    {{ $report->reporter->name }}
                                </td>
                                <td data-label="Status">
                                    <span class="badge badge-{{ $report->status }}">
                                        {{ $statusLabels[$report->status] ?? $report->status }}
                                    </span>
                                </td>
                                <td data-label="Petugas">
                                    {{ $report->officer?->name ?? '-' }}
                                </td>
                                <td data-label="Aksi">
                                    <div class="table-actions">
                                        <a href="{{ route('officer.reports.show', $report) }}" class="button button-secondary button-sm">
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
