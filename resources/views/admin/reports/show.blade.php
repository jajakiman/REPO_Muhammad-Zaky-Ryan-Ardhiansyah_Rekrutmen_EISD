@extends('layouts.dashboard')

@php
    $statusLabels = [
        'submitted' => 'Menunggu Verifikasi',
        'verified' => 'Terverifikasi',
        'in_progress' => 'Sedang Ditangani',
        'resolved' => 'Selesai',
        'rejected' => 'Ditolak',
        'cancelled' => 'Dibatalkan oleh Pelapor',
    ];

    $priorityLabels = [
        'low' => 'Rendah',
        'medium' => 'Sedang',
        'high' => 'Tinggi',
    ];
@endphp

@section('title', 'Detail Monitoring ' . $report->report_code . ' | AksesLoka')

@section('content')
<section class="admin-section">
    <div class="container"><div class="max-w-4xl space-y-6">
        <div class="breadcrumb-nav">
            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-navy-900 hover:text-orange-700 transition-colors">
                &larr; Kembali ke Monitoring Laporan
            </a>
        </div>

        <div class="report-detail-header space-y-2">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm sm:text-base font-semibold">
                <span class="text-orange-700 font-extrabold uppercase tracking-wider">Pusat Monitoring &bull; {{ $report->report_code }}</span>
                <span class="text-slate-300 font-normal select-none" aria-hidden="true">&bull;</span>
                <span class="text-slate-600 font-medium">Diajukan pada {{ $report->created_at->format('d M Y, H:i') }} WIB</span>
            </div>
            <h1 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 tracking-tight">Detail Laporan Lintas Kampus</h1>
            <div class="flex flex-wrap items-center gap-3 pt-1">
                <span class="badge badge-{{ $report->status }} text-xs sm:text-sm px-3 py-1">
                    {{ $statusLabels[$report->status] ?? $report->status }}
                </span>
                @if($report->priority)
                    <span class="badge badge-priority-{{ $report->priority }} text-xs sm:text-sm px-3 py-1">
                        Prioritas: {{ $priorityLabels[$report->priority] ?? $report->priority }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Detail Objek & Masalah -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
            <h2 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3">Informasi Objek &amp; Masalah</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-xs font-bold uppercase text-slate-400">Pelapor</dt>
                    <dd class="mt-1 font-bold text-slate-900">{{ $report->reporter->name }} <span class="font-normal text-xs text-slate-500">({{ $report->reporter->email }})</span></dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase text-slate-400">Kampus</dt>
                    <dd class="mt-1 text-slate-800 font-semibold">{{ $report->locationAccessibilityFeature->campusLocation->campusArea->campus->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase text-slate-400">Area</dt>
                    <dd class="mt-1 text-slate-800 font-semibold">{{ $report->locationAccessibilityFeature->campusLocation->campusArea->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase text-slate-400">Lokasi</dt>
                    <dd class="mt-1 text-slate-800 font-semibold">{{ $report->locationAccessibilityFeature->campusLocation->name }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-bold uppercase text-slate-400">Fasilitas</dt>
                    <dd class="mt-1 font-bold text-slate-900 text-base">{{ $report->locationAccessibilityFeature->accessibilityFeature->name }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-bold uppercase text-slate-400">Kategori Masalah</dt>
                    <dd class="mt-1 text-slate-800 font-semibold">{{ $report->issueCategory->name }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-bold uppercase text-slate-400">Deskripsi</dt>
                    <dd class="mt-1 text-slate-700 whitespace-pre-wrap leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-200">{{ $report->description }}</dd>
                </div>
                @if($report->photo_path)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-bold uppercase text-slate-400 mb-2">Foto Bukti</dt>
                        <dd>
                            <div class="photo-preview-card max-w-lg rounded-2xl border border-slate-200 bg-slate-50 p-3 shadow-xs space-y-2">
                                <a href="{{ $report->photo_url }}" target="_blank" rel="noopener noreferrer" class="group block relative overflow-hidden rounded-xl border border-slate-200/80 bg-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <img src="{{ $report->photo_url }}" alt="Foto bukti masalah" loading="lazy" decoding="async" class="w-full max-h-80 object-cover rounded-xl transition-transform duration-200 group-hover:scale-[1.02]" onerror="this.parentElement.innerHTML='<div class=\'p-6 text-center text-xs text-slate-500 font-medium\'>Foto bukti tidak dapat dimuat atau berkas telah dipindahkan.</div>'">
                                    <div class="absolute inset-0 bg-slate-950/0 group-hover:bg-slate-950/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-900/80 text-white text-xs font-bold backdrop-blur-xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            Buka Foto Ukuran Penuh
                                        </span>
                                    </div>
                                </a>
                                <p class="text-[11px] text-slate-500 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Klik gambar untuk melihat foto resolusi penuh di tab baru.
                                </p>
                            </div>
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        <!-- Detail Penanganan -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
            <h2 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3">Status &amp; Penanganan</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div class="sm:col-span-2">
                    <dt class="text-xs font-bold uppercase text-slate-400">Petugas Penanggung Jawab</dt>
                    <dd class="mt-1 font-semibold text-slate-800">{{ $report->officer ? $report->officer->name . ' (' . $report->officer->email . ')' : 'Belum diklaim' }}</dd>
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
                            <dt class="text-xs font-bold uppercase text-slate-400 mb-2">Foto Hasil</dt>
                            <dd>
                                <div class="photo-preview-card max-w-lg rounded-2xl border border-emerald-200 bg-emerald-50/50 p-3 shadow-xs space-y-2">
                                    <a href="{{ $report->resolution_photo_url }}" target="_blank" rel="noopener noreferrer" class="group block relative overflow-hidden rounded-xl border border-emerald-200/80 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                        <img src="{{ $report->resolution_photo_url }}" alt="Foto hasil penanganan" loading="lazy" decoding="async" class="w-full max-h-80 object-cover rounded-xl transition-transform duration-200 group-hover:scale-[1.02]" onerror="this.parentElement.innerHTML='<div class=\'p-6 text-center text-xs text-slate-500 font-medium\'>Foto hasil tidak dapat dimuat atau berkas telah dipindahkan.</div>'">
                                        <div class="absolute inset-0 bg-slate-950/0 group-hover:bg-slate-950/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-900/80 text-white text-xs font-bold backdrop-blur-xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                Buka Foto Ukuran Penuh
                                            </span>
                                        </div>
                                    </a>
                                    <p class="text-[11px] text-emerald-800 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Foto dokumentasi hasil perbaikan oleh petugas.
                                    </p>
                                </div>
                            </dd>
                        </div>
                    @endif
                @endif
            </dl>
        </div>
    </div></div>
</section>
@endsection
