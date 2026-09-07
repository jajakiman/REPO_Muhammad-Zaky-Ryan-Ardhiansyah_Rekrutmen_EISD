@extends('layouts.app')

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
    <div class="container narrow">
        <div class="breadcrumb-nav">
            <a href="{{ route('officer.queue.index') }}" class="back-link">&larr; Kembali ke Antrean Area</a>
        </div>

        <div class="report-detail-header" style="margin-top: 1rem;">
            <p class="eyebrow">Kode Laporan: {{ $report->report_code }}</p>
            <h1>Pemeriksaan Laporan Fasilitas</h1>
            <p class="meta-row">
                <span class="badge badge-{{ $report->status }}">
                    {{ $statusLabels[$report->status] ?? $report->status }}
                </span>
                @if($report->priority)
                    <span class="badge badge-priority-{{ $report->priority }}">
                        Prioritas: {{ $priorityLabels[$report->priority] ?? $report->priority }}
                    </span>
                @endif
                <span class="field-hint">Diajukan: {{ $report->created_at->format('d M Y, H:i') }}</span>
            </p>
        </div>

        <!-- Detail Objek & Masalah -->
        <div class="service-card" style="margin-top: 2rem;">
            <h2 style="font-size: 1.25rem;">Informasi Objek Laporan</h2>
            <dl class="detail-list">
                <div>
                    <dt>Pelapor</dt>
                    <dd><strong>{{ $report->reporter->name }}</strong> ({{ $report->reporter->email }})</dd>
                </div>
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
                    <dt>Deskripsi Kendala</dt>
                    <dd style="white-space: pre-wrap;">{{ $report->description }}</dd>
                </div>
                @if($report->photo_path)
                    <div>
                        <dt>Foto Bukti</dt>
                        <dd>
                            <img src="{{ Storage::disk('report-photos')->url($report->photo_path) }}" alt="Foto bukti laporan fasilitas" style="max-width: 100%; max-height: 360px; border-radius: var(--radius-card); border: 1px solid var(--slate-200);">
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        <!-- Info Penanggung Jawab jika sudah ada -->
        @if($report->officer)
            <div class="service-card" style="margin-top: 2rem;">
                <h2 style="font-size: 1.25rem;">Petugas Penanggung Jawab</h2>
                <p><strong>{{ $report->officer->name }}</strong> ({{ $report->officer->email }})</p>
                @if($report->verified_at)
                    <p class="field-hint">Diverifikasi pada: {{ $report->verified_at->format('d M Y, H:i') }}</p>
                @endif
            </div>
        @endif

        <!-- Aksi Verifikasi & Klaim atau Penolakan (Hanya jika status submitted) -->
        @if($report->status === 'submitted')
            <!-- Card 1: Verifikasi & Klaim -->
            <div class="form-card" style="margin-top: 2rem;">
                <h2 style="font-size: 1.25rem;">Verifikasi & Klaim Laporan</h2>
                <p class="field-hint">Tentukan tingkat prioritas penanganan untuk memverifikasi dan mengambil tanggung jawab atas laporan ini.</p>

                <form method="post" action="{{ route('officer.reports.verify', $report) }}" style="margin-top: 1rem;">
                    @csrf
                    <div class="field">
                        <label for="priority">Tingkat Prioritas</label>
                        <select id="priority" name="priority" required @error('priority') aria-describedby="priority-error" aria-invalid="true" @enderror>
                            <option value="">Pilih prioritas</option>
                            <option value="low" @selected(old('priority') === 'low')>Rendah (Low)</option>
                            <option value="medium" @selected(old('priority') === 'medium')>Sedang (Medium)</option>
                            <option value="high" @selected(old('priority') === 'high')>Tinggi (High)</option>
                        </select>
                        @error('priority')
                            <p class="field-error" id="priority-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="actions">
                        <button type="submit" class="button button-primary">
                            Verifikasi & Klaim Laporan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card 2: Tolak Laporan -->
            <div class="form-card" style="margin-top: 2rem; border-color: var(--red-200);">
                <h2 style="font-size: 1.25rem; color: var(--red-800);">Tolak Laporan</h2>
                <p class="field-hint">Jika laporan tidak valid, bukan wewenang kampus, atau duplikasi, tolak laporan dengan menyertakan alasan yang jelas.</p>

                <form method="post" action="{{ route('officer.reports.reject', $report) }}" style="margin-top: 1rem;">
                    @csrf
                    <div class="field">
                        <label for="rejection_reason">Alasan Penolakan</label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="3" required placeholder="Tuliskan alasan penolakan secara jelas untuk pelapor..." @error('rejection_reason') aria-describedby="rejection_reason-error" aria-invalid="true" @enderror>{{ old('rejection_reason') }}</textarea>
                        @error('rejection_reason')
                            <p class="field-error" id="rejection_reason-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="actions">
                        <button type="submit" class="button button-danger" style="background: var(--red-800); color: var(--white);">
                            Tolak Laporan Ini
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Tampilan Alasan Penolakan jika rejected -->
        @if($report->status === 'rejected')
            <div class="rejection-card" style="margin-top: 2rem; background: var(--red-50); color: var(--red-800); padding: 1.5rem; border-radius: var(--radius-card);">
                <h2 style="font-size: 1.25rem;">Laporan Telah Ditolak</h2>
                <p><strong>Alasan penolakan:</strong></p>
                <p style="white-space: pre-wrap;">{{ $report->rejection_reason }}</p>
                @if($report->verified_at)
                    <p class="field-hint" style="color: var(--red-800);">Ditolak pada: {{ $report->verified_at->format('d M Y, H:i') }}</p>
                @endif
            </div>
        @endif

        <!-- Aksi Mulai Penanganan (status verified dan petugas penanggung jawab) -->
        @if($report->status === 'verified' && $report->officer_id === auth()->id())
            <div class="form-card" style="margin-top: 2rem;">
                <h2 style="font-size: 1.25rem;">Mulai Penanganan</h2>
                <p class="field-hint">Mulai proses perbaikan atau penanganan kendala fasilitas ini. Status laporan akan diperbarui menjadi 'Sedang Ditangani'.</p>
                <form method="post" action="{{ route('officer.reports.start', $report) }}" style="margin-top: 1rem;">
                    @csrf
                    <button type="submit" class="button button-primary">
                        Mulai Penanganan Laporan
                    </button>
                </form>
            </div>
        @endif

        <!-- Form Penyelesaian Penanganan (status in_progress dan petugas penanggung jawab) -->
        @if($report->status === 'in_progress' && $report->officer_id === auth()->id())
            <div class="form-card" style="margin-top: 2rem;">
                <h2 style="font-size: 1.25rem;">Penyelesaian Penanganan Laporan</h2>
                <p class="field-hint">Catat hasil penanganan dan tentukan kondisi fasilitas terkini setelah perbaikan dilakukan.</p>

                <form method="post" action="{{ route('officer.reports.resolve', $report) }}" enctype="multipart/form-data" style="margin-top: 1rem;">
                    @csrf
                    <div class="field">
                        <label for="condition">Kondisi Fasilitas Terkini</label>
                        <select id="condition" name="condition" required @error('condition') aria-describedby="condition-error" aria-invalid="true" @enderror>
                            <option value="">Pilih kondisi fasilitas</option>
                            <option value="good" @selected(old('condition') === 'good')>Baik (Normal / Siap Digunakan)</option>
                            <option value="needs_repair" @selected(old('condition') === 'needs_repair')>Perlu Perbaikan Lanjutan</option>
                            <option value="blocked" @selected(old('condition') === 'blocked')>Terhalang</option>
                            <option value="broken" @selected(old('condition') === 'broken')>Rusak / Belum Berfungsi</option>
                        </select>
                        @error('condition')
                            <p class="field-error" id="condition-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="resolution_notes">Catatan Hasil Penanganan</label>
                        <textarea id="resolution_notes" name="resolution_notes" rows="4" required placeholder="Jelaskan tindakan perbaikan yang telah dilakukan dan catatan penting terkait kondisi fasilitas..." @error('resolution_notes') aria-describedby="resolution_notes-error" aria-invalid="true" @enderror>{{ old('resolution_notes') }}</textarea>
                        @error('resolution_notes')
                            <p class="field-error" id="resolution_notes-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="resolution_photo">Foto Hasil Penanganan (Opsional)</label>
                        <input id="resolution_photo" name="resolution_photo" type="file" accept="image/jpeg,image/png,image/webp" @error('resolution_photo') aria-describedby="resolution_photo-error" aria-invalid="true" @enderror>
                        <p class="field-hint">Format yang diterima: JPEG, PNG, WebP. Maksimal 2 MB.</p>
                        @error('resolution_photo')
                            <p class="field-error" id="resolution_photo-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="actions" style="margin-top: 2rem;">
                        <button type="submit" class="button button-primary">
                            Selesaikan Laporan & Perbarui Fasilitas
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Tampilan Hasil Penanganan jika status resolved -->
        @if($report->status === 'resolved')
            <div class="service-card" style="margin-top: 2rem; border-color: var(--emerald-200);">
                <h2 style="font-size: 1.25rem; color: var(--emerald-800);">Hasil Penanganan Selesai</h2>
                <dl class="detail-list">
                    <div>
                        <dt>Waktu Selesai</dt>
                        <dd>{{ $report->resolved_at ? $report->resolved_at->format('d M Y, H:i') : '-' }}</dd>
                    </div>
                    <div>
                        <dt>Catatan Hasil</dt>
                        <dd style="white-space: pre-wrap;">{{ $report->resolution_notes }}</dd>
                    </div>
                    @if($report->resolution_photo_path)
                        <div>
                            <dt>Foto Hasil</dt>
                            <dd>
                                <img src="{{ Storage::disk('report-photos')->url($report->resolution_photo_path) }}" alt="Foto hasil penanganan fasilitas" style="max-width: 100%; max-height: 360px; border-radius: var(--radius-card); border: 1px solid var(--slate-200);">
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endif
    </div>
</section>
@endsection
