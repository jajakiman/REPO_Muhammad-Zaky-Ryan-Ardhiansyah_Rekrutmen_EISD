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
<section class="report-detail py-4 sm:py-6">
    <div class="max-w-4xl space-y-6">
        <div class="breadcrumb-nav">
            <a href="{{ route('reporter.reports.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-navy-900 hover:text-orange-700 transition-colors">
                &larr; Kembali ke Riwayat Laporan
            </a>
        </div>

        <div class="report-detail-header space-y-2">
            <p class="eyebrow text-orange-700 text-xs font-bold uppercase tracking-wider mb-0">Kode Laporan: {{ $report->report_code }}</p>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Detail Laporan Masalah</h1>
            <div class="flex flex-wrap items-center gap-3 pt-1 text-xs text-slate-500">
                <span class="badge badge-{{ $report->status }}">
                    {{ $statusLabels[$report->status] ?? $report->status }}
                </span>
                @if($report->priority)
                    <span class="badge badge-priority-{{ $report->priority }}">
                        Prioritas: {{ $priorityLabels[$report->priority] ?? $report->priority }}
                    </span>
                @endif
                <span>Diajukan pada: {{ $report->created_at->format('d M Y, H:i') }}</span>
            </div>
        </div>

        <!-- Detail Objek & Masalah -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
            <h2 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3">Informasi Fasilitas &amp; Lokasi</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-xs font-bold uppercase text-slate-400">Fasilitas</dt>
                    <dd class="mt-1 font-bold text-slate-900 text-base">{{ $report->locationAccessibilityFeature->accessibilityFeature->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase text-slate-400">Lokasi</dt>
                    <dd class="mt-1 text-slate-800 font-semibold">{{ $report->locationAccessibilityFeature->campusLocation->name }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-bold uppercase text-slate-400">Area &amp; Kampus</dt>
                    <dd class="mt-1 text-slate-700">{{ $report->locationAccessibilityFeature->campusLocation->campusArea->campus->name }} &bull; {{ $report->locationAccessibilityFeature->campusLocation->campusArea->name }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-bold uppercase text-slate-400">Kategori Masalah</dt>
                    <dd class="mt-1 text-slate-800 font-semibold">{{ $report->issueCategory->name }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-bold uppercase text-slate-400">Deskripsi Masalah</dt>
                    <dd class="mt-1 text-slate-700 whitespace-pre-wrap leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-200">{{ $report->description }}</dd>
                </div>
                @if($report->photo_path)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-bold uppercase text-slate-400 mb-2">Foto Bukti</dt>
                        <dd>
                            <img src="{{ Storage::disk('report-photos')->url($report->photo_path) }}" alt="Foto bukti masalah fasilitas" loading="lazy" decoding="async" class="max-w-md w-full h-auto rounded-xl border border-slate-200 shadow-xs">
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        <!-- Progres Penanganan -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
            <h2 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3">Status Tindak Lanjut</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-xs font-bold uppercase text-slate-400">Petugas Penanggung Jawab</dt>
                    <dd class="mt-1 font-semibold text-slate-800">{{ $report->officer?->name ?? 'Belum ditugaskan / belum diverifikasi' }}</dd>
                </div>
                @if($report->verified_at)
                    <div>
                        <dt class="text-xs font-bold uppercase text-slate-400">Waktu Verifikasi</dt>
                        <dd class="mt-1 text-slate-700">{{ $report->verified_at->format('d M Y, H:i') }}</dd>
                    </div>
                @endif
                @if($report->handling_started_at)
                    <div>
                        <dt class="text-xs font-bold uppercase text-slate-400">Waktu Mulai Penanganan</dt>
                        <dd class="mt-1 text-slate-700">{{ $report->handling_started_at->format('d M Y, H:i') }}</dd>
                    </div>
                @endif
                @if($report->status === 'rejected')
                    <div class="sm:col-span-2 rounded-xl border border-red-200 bg-red-50 p-4 text-red-900">
                        <dt class="font-bold text-red-950">Alasan Penolakan</dt>
                        <dd class="mt-1 text-sm leading-relaxed">{{ $report->rejection_reason }}</dd>
                    </div>
                @endif
                @if($report->status === 'resolved')
                    <div>
                        <dt class="text-xs font-bold uppercase text-slate-400">Waktu Selesai</dt>
                        <dd class="mt-1 text-slate-700">{{ $report->resolved_at?->format('d M Y, H:i') }}</dd>
                    </div>
                    @if($report->resolution_notes)
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-bold uppercase text-slate-400">Catatan Hasil Penanganan</dt>
                            <dd class="mt-1 text-slate-700 whitespace-pre-wrap leading-relaxed bg-emerald-50/50 p-4 rounded-xl border border-emerald-200">{{ $report->resolution_notes }}</dd>
                        </div>
                    @endif
                    @if($report->resolution_photo_path)
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-bold uppercase text-slate-400 mb-2">Foto Hasil Penanganan</dt>
                            <dd>
                                <img src="{{ Storage::disk('report-photos')->url($report->resolution_photo_path) }}" alt="Foto hasil penyelesaian fasilitas" loading="lazy" decoding="async" class="max-w-md w-full h-auto rounded-xl border border-slate-200 shadow-xs">
                            </dd>
                        </div>
                    @endif
                @endif
            </dl>
        </div>

        <!-- Tombol Pembatalan (Jika masih submitted dan belum diklaim) -->
        @if($report->status === 'submitted' && $report->officer_id === null)
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-3">
                <h2 class="text-lg font-bold text-slate-900">Batalkan Laporan Ini?</h2>
                <p class="text-sm text-slate-600">Anda dapat membatalkan laporan ini selama belum diverifikasi atau diklaim oleh Petugas.</p>
                <form method="post" action="{{ route('reporter.reports.cancel', $report) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan laporan ini?');">
                    @csrf
                    @method('patch')
                    <button type="submit" class="button button-danger inline-flex min-h-11 items-center px-5 py-2.5 rounded-xl text-sm font-bold bg-red-800 text-white hover:bg-red-900 shadow-sm transition-colors">
                        Batalkan Laporan
                    </button>
                </form>
            </div>
        @endif
    </div>
</section>
@endsection
