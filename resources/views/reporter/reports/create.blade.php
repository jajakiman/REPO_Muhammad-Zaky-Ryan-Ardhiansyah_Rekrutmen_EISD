@extends('layouts.dashboard')

@section('title', 'Buat Laporan Masalah | AksesLoka')

@section('content')
<section class="form-section">
    <div class="container form-layout">
        <div class="form-intro">
            <p class="eyebrow">Form Pelaporan</p>
            <h1>Buat Laporan Masalah</h1>
            <p class="lead">Sampaikan kendala fasilitas aksesibilitas yang Anda temukan agar dapat segera ditindaklanjuti oleh petugas terkait.</p>

            <div class="service-card" style="margin-top: 1.5rem;">
                <p class="eyebrow">Objek Laporan</p>
                <h2 style="font-size: 1.25rem;">{{ $facility->accessibilityFeature->name }}</h2>
                <p><strong>Lokasi:</strong> {{ $facility->campusLocation->name }}</p>
                <p><strong>Area:</strong> {{ $facility->campusLocation->campusArea->name }}</p>
                <p><strong>Kampus:</strong> {{ $facility->campusLocation->campusArea->campus->name }}</p>
            </div>
        </div>

        <div class="form-card">
            <form method="post" action="{{ route('reporter.reports.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="location_accessibility_feature_id" value="{{ $facility->id }}">

                <div class="field">
                    <label for="issue_category_id">Kategori Masalah</label>
                    <select id="issue_category_id" name="issue_category_id" required @error('issue_category_id') aria-describedby="issue_category_id-error" aria-invalid="true" @enderror>
                        <option value="">Pilih kategori masalah</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected((string) old('issue_category_id') === (string) $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('issue_category_id')
                        <p class="field-error" id="issue_category_id-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="description">Deskripsi Masalah</label>
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
                    <a class="button button-secondary" href="{{ route('locations.show', $facility->campusLocation) }}">Batal</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
