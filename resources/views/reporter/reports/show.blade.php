@extends('layouts.dashboard')

@php
    $statusLabels = [
        'submitted' => 'Menunggu Verifikasi Petugas',
        'verified' => 'Terverifikasi (Menunggu Penanganan)',
        'in_progress' => 'Sedang Ditangani oleh Petugas',
        'resolved' => 'Selesai Ditangani',
        'rejected' => 'Laporan Ditolak',
        'cancelled' => 'Laporan Dibatalkan oleh Pelapor',
    ];

    $priorityLabels = [
        'low' => 'Rendah',
        'medium' => 'Sedang',
        'high' => 'Tinggi',
    ];
@endphp

@section('title', 'Laporan ' . $report->report_code . ' | AksesLoka')

@section('content')
<section class="admin-section">
    <div class="container narrow">
        <div class="breadcrumb-nav">
            <a href="{{ route('reporter.reports.index') }}" class="back-link">&larr; Kembali ke Riwayat Laporan</a>
        </div>

        <div class="report-detail-header" style="margin-top: 1rem;">
            <p class="eyebrow">Kode Laporan: {{ $report->report_code }}</p>
            <h1>Detail Laporan Masalah</h1>
            <p class="meta-row">
                <span class="badge badge-{{ $report->status }}">
                    {{ $statusLabels[$report->status] ?? $report->status }}
                </span>
                @if($report->priority)
                    <span class="badge badge-priority-{{ $report->priority }}">
                        Prioritas: {{ $priorityLabels[$report->priority] ?? $report->priority }}
                    </span>
                @endif
                <span class="field-hint">Diajukan pada: {{ $report->created_at->format('d M Y, H:i') }}</span>
            </p>
        </div>

        <!-- Detail Objek & Masalah -->
        <div class="service-card" style="margin-top: 2rem;">
            <h2 style="font-size: 1.25rem;">Informasi Fasilitas & Lokasi</h2>
            <dl class="detail-list">
                <div>
                    <dt>Fasilitas</dt>
                    <dd><strong>{{ $report->locationAccessibilityFeature->accessibilityFeature->name }}</strong></dd>
                </div>
                <div>
                    <dt>Lokasi</dt>
                    <dd>{{ $report->locationAccessibilityFeature->campusLocation->name }}</dd>
                </div>
                <div>
                    <dt>Area & Kampus</dt>
                    <dd>{{ $report->locationAccessibilityFeature->campusLocation->campusArea->campus->name }} &bull; {{ $report->locationAccessibilityFeature->campusLocation->campusArea->name }}</dd>
                </div>
                <div>
                    <dt>Kategori Masalah</dt>
                    <dd>{{ $report->issueCategory->name }}</dd>
                </div>
                <div>
                    <dt>Deskripsi Masalah</dt>
                    <dd style="white-space: pre-wrap;">{{ $report->description }}</dd>
                </div>
                @if($report->photo_path)
                    <div>
                        <dt>Foto Bukti</dt>
                        <dd>
                            <img src="{{ Storage::disk('report-photos')->url($report->photo_path) }}" alt="Foto bukti masalah fasilitas" style="max-width: 100%; max-height: 360px; border-radius: var(--radius-card); border: 1px solid var(--slate-200);">
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        <!-- Progres Penanganan -->
        <div class="service-card" style="margin-top: 2rem;">
            <h2 style="font-size: 1.25rem;">Status Tindak Lanjut</h2>
            <dl class="detail-list">
                <div>
                    <dt>Petugas Penanggung Jawab</dt>
                    <dd>{{ $report->officer?->name ?? 'Belum ditugaskan / belum diverifikasi' }}</dd>
                </div>
                @if($report->verified_at)
                    <div>
                        <dt>Waktu Verifikasi</dt>
                        <dd>{{ $report->verified_at->format('d M Y, H:i') }}</dd>
                    </div>
                @endif
                @if($report->handling_started_at)
                    <div>
                        <dt>Waktu Mulai Penanganan</dt>
                        <dd>{{ $report->handling_started_at->format('d M Y, H:i') }}</dd>
                    </div>
                @endif
                @if($report->status === 'rejected')
                    <div class="rejection-box" style="background: var(--red-50); color: var(--red-800); padding: 1rem; border-radius: var(--radius-badge);">
                        <dt style="font-weight: 700;">Alasan Penolakan</dt>
                        <dd style="margin-top: 0.5rem;">{{ $report->rejection_reason }}</dd>
                    </div>
                @endif
                @if($report->status === 'resolved')
                    <div>
                        <dt>Waktu Selesai</dt>
                        <dd>{{ $report->resolved_at?->format('d M Y, H:i') }}</dd>
                    </div>
                    @if($report->resolution_notes)
                        <div>
                            <dt>Catatan Hasil Penanganan</dt>
                            <dd style="white-space: pre-wrap;">{{ $report->resolution_notes }}</dd>
                        </div>
                    @endif
                    @if($report->resolution_photo_path)
                        <div>
                            <dt>Foto Hasil Penanganan</dt>
                            <dd>
                                <img src="{{ Storage::disk('report-photos')->url($report->resolution_photo_path) }}" alt="Foto hasil penyelesaian fasilitas" style="max-width: 100%; max-height: 360px; border-radius: var(--radius-card); border: 1px solid var(--slate-200);">
                            </dd>
                        </div>
                    @endif
                @endif
            </dl>
        </div>

        <!-- Tombol Pembatalan (Jika masih submitted dan belum diklaim) -->
        @if($report->status === 'submitted' && $report->officer_id === null)
            <div class="cancellation-card" style="margin-top: 2rem; padding: 1.5rem; border: 1px solid var(--slate-200); border-radius: var(--radius-card); background: var(--white);">
                <h2>Batalkan Laporan Ini?</h2>
                <p class="field-hint">Anda dapat membatalkan laporan ini selama belum diverifikasi atau diklaim oleh Petugas.</p>
                <form method="post" action="{{ route('reporter.reports.cancel', $report) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan laporan ini?');">
                    @csrf
                    @method('patch')
                    <button type="submit" class="button button-danger" style="background: var(--red-800); color: var(--white); margin-top: 1rem;">
                        Batalkan Laporan
                    </button>
                </form>
            </div>
        @endif
    </div>
</section>
@endsection
