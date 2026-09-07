@extends('layouts.app')

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

@section('title', 'Antrean Laporan Area | AksesLoka')

@section('content')
<section class="admin-section">
    <div class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">{{ $area->campus->name }} &bull; {{ $area->name }}</p>
                <h1>Antrean Laporan Area</h1>
                <p class="lead">Daftar laporan kendala fasilitas aksesibilitas pada area tanggung jawab Anda.</p>
            </div>
            <div class="actions">
                <form method="get" action="{{ route('officer.queue.index') }}" class="inline-filter-form" style="display: flex; gap: 0.5rem; align-items: center;">
                    <label for="queue-status" class="sr-only">Status Laporan</label>
                    <select id="queue-status" name="status" onchange="this.form.submit()">
                        <option value="submitted" @selected(request('status', 'submitted') === 'submitted')>Menunggu Verifikasi (Submitted)</option>
                        <option value="verified" @selected(request('status') === 'verified')>Terverifikasi (Verified)</option>
                        <option value="in_progress" @selected(request('status') === 'in_progress')>Sedang Ditangani (In Progress)</option>
                        <option value="resolved" @selected(request('status') === 'resolved')>Selesai (Resolved)</option>
                        <option value="rejected" @selected(request('status') === 'rejected')>Ditolak (Rejected)</option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>Dibatalkan (Cancelled)</option>
                    </select>
                </form>
            </div>
        </div>

        @if($reports->isEmpty())
            <div class="empty-state">
                <h2>Tidak ada laporan</h2>
                <p>Saat ini tidak ada laporan dengan kriteria status yang dipilih untuk area ini.</p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <caption class="sr-only">Daftar laporan pada area tugas</caption>
                    <thead>
                        <tr>
                            <th scope="col">Kode</th>
                            <th scope="col">Lokasi</th>
                            <th scope="col">Fasilitas</th>
                            <th scope="col">Kategori Masalah</th>
                            <th scope="col">Pelapor</th>
                            <th scope="col">Status</th>
                            <th scope="col">Waktu Masuk</th>
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
                                <td data-label="Waktu Masuk">
                                    {{ $report->created_at->format('d M Y, H:i') }}
                                </td>
                                <td data-label="Aksi">
                                    <div class="table-actions">
                                        <a href="{{ route('officer.reports.show', $report) }}" class="button button-secondary button-sm">
                                            Periksa Laporan
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
