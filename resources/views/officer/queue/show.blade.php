@extends('layouts.dashboard')

@php
    $statusLabels = [
        'submitted' => 'Menunggu Verifikasi',
        'verified' => 'Terverifikasi (Siap Ditangani)',
        'in_progress' => 'Sedang Dalam Penanganan',
        'resolved' => 'Selesai Ditangani',
        'rejected' => 'Laporan Ditolak',
        'cancelled' => 'Dibatalkan oleh Pelapor',
    ];

    $priorityLabels = [
        'low' => 'Rendah',
        'medium' => 'Sedang',
        'high' => 'Tinggi',
    ];
@endphp

@section('title', 'Periksa Laporan ' . $report->report_code . ' | AksesLoka')

@section('content')
<section class="admin-section">
    <div class="container"><div class="max-w-4xl space-y-6">
        <div class="breadcrumb-nav">
            <a href="{{ route('officer.queue.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-navy-900 hover:text-orange-700 transition-colors">
                &larr; Kembali ke Antrean Area
            </a>
        </div>

        <div class="report-detail-header space-y-2">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm sm:text-base font-semibold">
                <span class="text-orange-700 font-extrabold uppercase tracking-wider">Kode Laporan: {{ $report->report_code }}</span>
                <span class="text-slate-300 font-normal select-none" aria-hidden="true">&bull;</span>
                <span class="text-slate-600 font-medium">Diajukan pada {{ $report->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
            </div>
            <h1 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 tracking-tight">Pemeriksaan Laporan Fasilitas</h1>
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
            <h2 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3">Informasi Objek Laporan</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-xs font-bold uppercase text-slate-400">Pelapor</dt>
                    <dd class="mt-1 font-bold text-slate-900">{{ $report->reporter->name }} <span class="font-normal text-xs text-slate-500">({{ $report->reporter->email }})</span></dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase text-slate-400">Fasilitas</dt>
                    <dd class="mt-1 font-bold text-slate-900 text-base">{{ $report->locationAccessibilityFeature->accessibilityFeature->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase text-slate-400">Lokasi</dt>
                    <dd class="mt-1 text-slate-800 font-semibold">{{ $report->locationAccessibilityFeature->campusLocation->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase text-slate-400">Area &amp; Kampus</dt>
                    <dd class="mt-1 text-slate-700">{{ $report->locationAccessibilityFeature->campusLocation->campusArea->campus->name }} &bull; {{ $report->locationAccessibilityFeature->campusLocation->campusArea->name }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-bold uppercase text-slate-400">Kategori Masalah</dt>
                    <dd class="mt-1 text-slate-800 font-semibold">{{ $report->issueCategory->name }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-bold uppercase text-slate-400">Deskripsi Kendala</dt>
                    <dd class="mt-1 text-slate-700 whitespace-pre-wrap leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-200">{{ $report->description }}</dd>
                </div>
                @if($report->photo_path)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-bold uppercase text-slate-400 mb-2">Foto Bukti</dt>
                        <dd>
                            <div class="photo-preview-card max-w-lg rounded-2xl border border-slate-200 bg-slate-50 p-3 shadow-xs space-y-2">
                                <a href="{{ $report->photo_url }}" target="_blank" rel="noopener noreferrer" class="group block relative overflow-hidden rounded-xl border border-slate-200/80 bg-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <img src="{{ $report->photo_url }}" alt="Foto bukti laporan fasilitas" loading="lazy" decoding="async" class="w-full max-h-80 object-cover rounded-xl transition-transform duration-200 group-hover:scale-[1.02]" onerror="this.parentElement.innerHTML='<div class=\'p-6 text-center text-xs text-slate-500 font-medium\'>Foto bukti tidak dapat dimuat atau berkas telah dipindahkan.</div>'">
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

        <!-- Info Penanggung Jawab jika sudah ada -->
        @if($report->officer)
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-2">
                <h2 class="text-lg font-bold text-slate-900">Petugas Penanggung Jawab</h2>
                <p class="text-sm text-slate-800"><strong>{{ $report->officer->name }}</strong> <span class="text-xs text-slate-500">({{ $report->officer->email }})</span></p>
                @if($report->verified_at)
                    <p class="text-xs text-slate-500">Diverifikasi pada: {{ $report->verified_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
                @endif
            </div>
        @endif

        <!-- Aksi Verifikasi & Klaim atau Penolakan (Hanya jika status submitted) -->
        @if($report->status === 'submitted')
            <!-- Card 1: Verifikasi & Klaim -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                <h2 class="text-xl font-bold text-slate-900">Verifikasi &amp; Klaim Laporan</h2>
                <p class="text-sm text-slate-600">Tentukan tingkat prioritas penanganan untuk memverifikasi dan mengambil tanggung jawab atas laporan ini.</p>

                <form method="post" action="{{ route('officer.reports.verify', $report) }}" class="space-y-4 pt-2">
                    @csrf
                    <div class="field mb-0">
                        <label for="priority">Tingkat Prioritas <span class="required-mark text-red-600" aria-hidden="true">*</span></label>
                        <x-select-shell>
                        <select id="priority" name="priority" required @error('priority') aria-describedby="priority-error" aria-invalid="true" @enderror>
                            <option value="">Pilih prioritas</option>
                            <option value="low" @selected(old('priority') === 'low')>Rendah (Low)</option>
                            <option value="medium" @selected(old('priority') === 'medium')>Sedang (Medium)</option>
                            <option value="high" @selected(old('priority') === 'high')>Tinggi (High)</option>
                        </select>
                        </x-select-shell>
                        @error('priority')
                            <p class="field-error" id="priority-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="actions pt-2">
                        <button type="submit" class="button button-primary inline-flex min-h-11 items-center px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-orange-700 hover:bg-orange-800 shadow-sm">
                            Verifikasi &amp; Klaim Laporan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card 2: Tolak Laporan -->
            <div class="rounded-2xl border border-red-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                <h2 class="text-xl font-bold text-red-800">Tolak Laporan</h2>
                <p class="text-sm text-slate-600">Jika laporan tidak valid, bukan wewenang kampus, atau duplikasi, tolak laporan dengan menyertakan alasan yang jelas.</p>

                <form method="post" action="{{ route('officer.reports.reject', $report) }}" class="space-y-4 pt-2">
                    @csrf
                    <div class="field mb-0">
                        <label for="rejection_reason">Alasan Penolakan <span class="required-mark text-red-600" aria-hidden="true">*</span></label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="3" required placeholder="Tuliskan alasan penolakan secara jelas untuk pelapor..." @error('rejection_reason') aria-describedby="rejection_reason-error" aria-invalid="true" @enderror>{{ old('rejection_reason') }}</textarea>
                        @error('rejection_reason')
                            <p class="field-error" id="rejection_reason-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="actions pt-2">
                        <button type="submit" class="button button-danger inline-flex min-h-11 items-center px-6 py-2.5 rounded-xl text-sm font-bold bg-red-800 text-white hover:bg-red-900 shadow-sm">
                            Tolak Laporan Ini
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Tampilan Alasan Penolakan jika rejected -->
        @if($report->status === 'rejected')
            <div class="rounded-2xl border border-red-200 bg-red-50 p-6 text-red-900 space-y-2">
                <h2 class="text-lg font-bold text-red-950">Laporan Telah Ditolak</h2>
                <p class="text-xs font-bold uppercase text-red-700">Alasan Penolakan:</p>
                <p class="text-sm whitespace-pre-wrap leading-relaxed">{{ $report->rejection_reason }}</p>
                @if($report->verified_at)
                    <p class="text-xs text-red-700">Ditolak pada: {{ $report->verified_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
                @endif
            </div>
        @endif

        <!-- Aksi Mulai Penanganan (status verified dan petugas penanggung jawab) -->
        @if($report->status === 'verified' && $report->officer_id === auth()->id())
            <div class="rounded-2xl border border-blue-200 bg-blue-50/50 p-6 sm:p-8 shadow-sm space-y-3">
                <h2 class="text-xl font-bold text-navy-950">Mulai Penanganan</h2>
                <p class="text-sm text-slate-600">Mulai proses perbaikan atau penanganan kendala fasilitas ini. Status laporan akan diperbarui menjadi 'Sedang Ditangani'.</p>
                <form method="post" action="{{ route('officer.reports.start', $report) }}" class="pt-2">
                    @csrf
                    <button type="submit" class="button button-primary inline-flex min-h-11 items-center px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-navy-900 hover:bg-navy-950 shadow-sm">
                        Mulai Penanganan Laporan
                    </button>
                </form>
            </div>
        @endif

        <!-- Form Penyelesaian Penanganan (status in_progress dan petugas penanggung jawab) -->
        @if($report->status === 'in_progress' && $report->officer_id === auth()->id())
            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                <h2 class="text-xl font-bold text-slate-900">Penyelesaian Penanganan Laporan</h2>
                <p class="text-sm text-slate-600">Catat hasil penanganan dan tentukan kondisi fasilitas terkini setelah perbaikan dilakukan.</p>

                <form method="post" action="{{ route('officer.reports.resolve', $report) }}" enctype="multipart/form-data" class="space-y-4 pt-2">
                    @csrf
                    <div class="field mb-0">
                        <label for="condition">Kondisi Fasilitas Terkini <span class="required-mark text-red-600" aria-hidden="true">*</span></label>
                        <x-select-shell>
                        <select id="condition" name="condition" required @error('condition') aria-describedby="condition-error" aria-invalid="true" @enderror>
                            <option value="">Pilih kondisi fasilitas</option>
                            <option value="good" @selected(old('condition') === 'good')>Baik (Normal / Siap Digunakan)</option>
                            <option value="needs_repair" @selected(old('condition') === 'needs_repair')>Perlu Perbaikan Lanjutan</option>
                            <option value="blocked" @selected(old('condition') === 'blocked')>Terhalang</option>
                            <option value="broken" @selected(old('condition') === 'broken')>Rusak / Belum Berfungsi</option>
                        </select>
                        </x-select-shell>
                        @error('condition')
                            <p class="field-error" id="condition-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field mb-0">
                        <label for="resolution_notes">Catatan Hasil Penanganan <span class="required-mark text-red-600" aria-hidden="true">*</span></label>
                        <textarea id="resolution_notes" name="resolution_notes" rows="4" required placeholder="Jelaskan tindakan perbaikan yang telah dilakukan dan catatan penting terkait kondisi fasilitas..." @error('resolution_notes') aria-describedby="resolution_notes-error" aria-invalid="true" @enderror>{{ old('resolution_notes') }}</textarea>
                        @error('resolution_notes')
                            <p class="field-error" id="resolution_notes-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field mb-0">
                        <label for="resolution_photo" class="block mb-2 font-bold text-slate-900">Foto Hasil Penanganan (Opsional)</label>
                        <div class="flex items-center justify-center w-full">
                            <label for="resolution_photo" class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-slate-300 rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 hover:border-navy-900 transition-colors group">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 px-4 text-center">
                                    <svg class="w-8 h-8 mb-3 text-slate-400 group-hover:text-navy-900 transition-colors" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h3a3 3 0 0 0 0-6h-.025a5.56 5.56 0 0 0 .025-.5A5.5 5.5 0 0 0 7.207 9.021C7.137 9.017 7.071 9 7 9a4 4 0 1 0 0 8h2.167M12 19v-9m0 0-2 2m2-2 2 2"/>
                                    </svg>
                                    <p class="mb-1 text-sm text-slate-700 font-medium">
                                        <span class="font-bold text-orange-700 group-hover:underline">Klik untuk mengunggah foto</span> atau seret dan lepas
                                    </p>
                                    <p class="text-xs text-slate-500" data-file-hint="resolution_photo">Format: JPG, PNG, atau WebP (Maksimal 2 MB)</p>
                                </div>
                                <input id="resolution_photo" name="resolution_photo" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only" @error('resolution_photo') aria-describedby="resolution_photo-error" aria-invalid="true" @enderror>
                            </label>
                        </div>
                        @error('resolution_photo')
                            <p class="field-error mt-2" id="resolution_photo-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="actions pt-4">
                        <button type="submit" class="button button-primary inline-flex min-h-11 items-center px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-orange-700 hover:bg-orange-800 shadow-sm">
                            Selesaikan Laporan &amp; Perbarui Fasilitas
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Tampilan Hasil Penanganan jika status resolved -->
        @if($report->status === 'resolved')
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-6 sm:p-8 shadow-sm space-y-4">
                <h2 class="text-xl font-bold text-emerald-950">Hasil Penanganan Selesai</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-xs font-bold uppercase text-emerald-800">Waktu Selesai</dt>
                        <dd class="mt-1 text-slate-800 font-semibold">{{ $report->resolved_at ? $report->resolved_at->timezone('Asia/Jakarta')->format('d M Y, H:i').' WIB' : '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-bold uppercase text-emerald-800">Catatan Hasil</dt>
                        <dd class="mt-1 text-slate-800 whitespace-pre-wrap leading-relaxed">{{ $report->resolution_notes }}</dd>
                    </div>
                    @if($report->resolution_photo_path)
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-bold uppercase text-emerald-800 mb-2">Foto Hasil</dt>
                            <dd>
                                <div class="photo-preview-card max-w-lg rounded-2xl border border-emerald-200 bg-emerald-50/50 p-3 shadow-xs space-y-2">
                                    <a href="{{ $report->resolution_photo_url }}" target="_blank" rel="noopener noreferrer" class="group block relative overflow-hidden rounded-xl border border-emerald-200/80 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                        <img src="{{ $report->resolution_photo_url }}" alt="Foto hasil penanganan fasilitas" loading="lazy" decoding="async" class="w-full max-h-80 object-cover rounded-xl transition-transform duration-200 group-hover:scale-[1.02]" onerror="this.parentElement.innerHTML='<div class=\'p-6 text-center text-xs text-slate-500 font-medium\'>Foto hasil tidak dapat dimuat atau berkas telah dipindahkan.</div>'">
                                        <div class="absolute inset-0 bg-slate-950/0 group-hover:bg-slate-950/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-900/80 text-white text-xs font-bold backdrop-blur-xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                Buka Foto Ukuran Penuh
                                            </span>
                                        </div>
                                    </a>
                                    <p class="text-[11px] text-emerald-800 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Foto dokumentasi hasil perbaikan fasilitas.
                                    </p>
                                </div>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endif
    </div></div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.getElementById('resolution_photo');
        var hint = document.querySelector('[data-file-hint="resolution_photo"]');
        if (input && hint) {
            input.addEventListener('change', function () {
                if (input.files && input.files[0]) {
                    var file = input.files[0];
                    var sizeKb = Math.round(file.size / 1024);
                    hint.textContent = 'File terpilih: ' + file.name + ' (' + sizeKb + ' KB)';
                    hint.classList.add('text-emerald-700', 'font-semibold');
                }
            });
        }
    });
</script>
@endsection
