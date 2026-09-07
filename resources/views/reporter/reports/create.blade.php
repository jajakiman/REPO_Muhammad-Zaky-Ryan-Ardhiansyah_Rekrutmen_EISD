@extends('layouts.dashboard')

@section('title', 'Buat Laporan Masalah | AksesLoka')

@section('content')
<section class="form-section">
    <div class="container form-layout">
        <div class="form-intro">
            <p class="eyebrow">Form Pelaporan</p>
            <h1>Buat Laporan Masalah</h1>
            <p class="lead">Pilih fasilitas yang bermasalah, jelaskan kendalanya, lalu sertakan foto bukti jika tersedia.</p>
            <div class="mt-6 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm leading-6 text-slate-700">
                Laporan otomatis diteruskan ke petugas yang bertanggung jawab atas area lokasi tersebut.
            </div>
        </div>

        @if($facilities->isEmpty())
        <div class="empty-state text-center">
            <h2>Belum ada fasilitas yang dapat dilaporkan</h2>
            <p>Fasilitas aktif belum tersedia. Anda tetap dapat melihat lokasi kampus yang sudah dipetakan.</p>
            <a class="button button-secondary mt-5" href="{{ route('map.index') }}">Lihat Peta Kampus</a>
        </div>
        @else
        <div class="form-card min-w-0">
            <form method="post" action="{{ route('reporter.reports.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="field">
                    <label for="location_accessibility_feature_id">Lokasi dan Fasilitas <span class="required-mark text-red-600" aria-hidden="true">*</span></label>
                    <x-select-shell>
                    <select id="location_accessibility_feature_id" name="location_accessibility_feature_id" required @error('location_accessibility_feature_id') aria-describedby="location_accessibility_feature_id-error" aria-invalid="true" @enderror>
                        <option value="">Pilih lokasi dan fasilitas</option>
                        @foreach($facilities->groupBy(fn ($item) => $item->campusLocation->campusArea->campus->name) as $campusName => $campusFacilities)
                            <optgroup label="{{ $campusName }}">
                                @foreach($campusFacilities as $item)
                                    <option value="{{ $item->id }}" @selected((string) old('location_accessibility_feature_id', $facilityId) === (string) $item->id)>
                                        {{ $item->campusLocation->name }} - {{ $item->accessibilityFeature->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    </x-select-shell>
                    <p class="field-hint">Nama kampus digunakan sebagai kelompok agar lokasi lebih mudah ditemukan.</p>
                    @error('location_accessibility_feature_id')
                        <p class="field-error" id="location_accessibility_feature_id-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="issue_category_id">Kategori Masalah <span class="required-mark text-red-600" aria-hidden="true">*</span></label>
                    <x-select-shell>
                    <select id="issue_category_id" name="issue_category_id" required @error('issue_category_id') aria-describedby="issue_category_id-error" aria-invalid="true" @enderror>
                        <option value="">Pilih kategori masalah</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected((string) old('issue_category_id') === (string) $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    </x-select-shell>
                    @error('issue_category_id')
                        <p class="field-error" id="issue_category_id-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="description">Deskripsi Masalah <span class="required-mark text-red-600" aria-hidden="true">*</span></label>
                    <textarea id="description" name="description" rows="4" required placeholder="Jelaskan secara rinci kendala yang terjadi pada fasilitas ini..." @error('description') aria-describedby="description-error" aria-invalid="true" @enderror>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="field-error" id="description-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="photo">Foto Bukti (Opsional)</label>
                    <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" @error('photo') aria-describedby="photo-error" aria-invalid="true" @enderror>
                    <p class="field-hint">Format yang diterima: JPEG, PNG, WebP. Ukuran maksimal 2 MB.</p>
                    @error('photo')
                        <p class="field-error" id="photo-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="actions" style="margin-top: 2rem;">
                    <button class="button button-primary" type="submit">Kirim Laporan</button>
                    <a class="button button-secondary" href="{{ route('reporter.dashboard') }}">Batal</a>
                </div>
            </form>
        </div>
        @endif
    </div>
</section>
@endsection
