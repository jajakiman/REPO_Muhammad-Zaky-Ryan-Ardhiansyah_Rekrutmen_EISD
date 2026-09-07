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
<section class="officer-report-detail py-4 sm:py-6">
    <div class="max-w-4xl space-y-6">
        <div class="breadcrumb-nav">
            <a href="{{ route('officer.queue.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-navy-900 hover:text-orange-700 transition-colors">
                &larr; Kembali ke Antrean Area
            </a>
        </div>

        <div class="report-detail-header space-y-2">
            <p class="eyebrow text-orange-700 text-xs font-bold uppercase tracking-wider mb-0">Kode Laporan: {{ $report->report_code }}</p>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Pemeriksaan Laporan Fasilitas</h1>
            <div class="flex flex-wrap items-center gap-3 pt-1 text-xs text-slate-500">
                <span class="badge badge-{{ $report->status }}">
                    {{ $statusLabels[$report->status] ?? $report->status }}
                </span>
                @if($report->priority)
                    <span class="badge badge-priority-{{ $report->priority }}">
                        Prioritas: {{ $priorityLabels[$report->priority] ?? $report->priority }}
                    </span>
                @endif
                <span>Diajukan: {{ $report->created_at->format('d M Y, H:i') }}</span>
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
                            <img src="{{ Storage::disk('report-photos')->url($report->photo_path) }}" alt="Foto bukti laporan fasilitas" class="max-w-md w-full h-auto rounded-xl border border-slate-200 shadow-xs">
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
                    <p class="text-xs text-slate-500">Diverifikasi pada: {{ $report->verified_at->format('d M Y, H:i') }}</p>
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
                    <p class="text-xs text-red-700">Ditolak pada: {{ $report->verified_at->format('d M Y, H:i') }}</p>
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
                        <label for="resolution_photo">Foto Hasil Penanganan (Opsional)</label>
                        <input id="resolution_photo" name="resolution_photo" type="file" accept="image/jpeg,image/png,image/webp" @error('resolution_photo') aria-describedby="resolution_photo-error" aria-invalid="true" @enderror>
                        <p class="field-hint text-xs text-slate-500 mt-1">Format yang diterima: JPEG, PNG, WebP. Maksimal 2 MB.</p>
                        @error('resolution_photo')
                            <p class="field-error" id="resolution_photo-error">{{ $message }}</p>
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
                        <dd class="mt-1 text-slate-800 font-semibold">{{ $report->resolved_at ? $report->resolved_at->format('d M Y, H:i') : '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-bold uppercase text-emerald-800">Catatan Hasil</dt>
                        <dd class="mt-1 text-slate-800 whitespace-pre-wrap leading-relaxed">{{ $report->resolution_notes }}</dd>
                    </div>
                    @if($report->resolution_photo_path)
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-bold uppercase text-emerald-800 mb-2">Foto Hasil</dt>
                            <dd>
                                <img src="{{ Storage::disk('report-photos')->url($report->resolution_photo_path) }}" alt="Foto hasil penanganan fasilitas" class="max-w-md w-full h-auto rounded-xl border border-emerald-200 shadow-xs">
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endif
    </div>
</section>
@endsection
