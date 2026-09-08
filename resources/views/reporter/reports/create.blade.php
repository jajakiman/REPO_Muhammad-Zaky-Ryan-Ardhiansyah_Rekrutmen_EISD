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
                    @if(auth()->user()->campus)
                        <p class="field-hint">Menampilkan fasilitas di {{ auth()->user()->campus->name }} sesuai profil Anda.</p>
                    @else
                        <p class="field-hint">Nama kampus digunakan sebagai kelompok agar lokasi lebih mudah ditemukan.</p>
                    @endif
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
                    <label for="photo" class="block mb-2 font-bold text-slate-900">Foto Bukti (Opsional)</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="photo" class="group relative flex h-48 w-full cursor-pointer flex-col items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 transition-colors hover:border-navy-900 hover:bg-slate-100 focus-within:border-navy-900 focus-within:ring-4 focus-within:ring-orange-500/20">
                            <div class="pointer-events-none flex flex-col items-center justify-center px-4 pt-5 pb-6 text-center">
                                <svg class="w-8 h-8 mb-3 text-slate-400 group-hover:text-navy-900 transition-colors" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h3a3 3 0 0 0 0-6h-.025a5.56 5.56 0 0 0 .025-.5A5.5 5.5 0 0 0 7.207 9.021C7.137 9.017 7.071 9 7 9a4 4 0 1 0 0 8h2.167M12 19v-9m0 0-2 2m2-2 2 2"/>
                                </svg>
                                <p class="mb-1 text-sm text-slate-700 font-medium">
                                    <span class="font-bold text-orange-700 group-hover:underline">Klik untuk mengunggah foto</span> atau seret dan lepas
                                </p>
                                <p class="text-xs text-slate-500" data-file-hint="photo">Format: JPG, PNG, atau WebP (Maksimal 2 MB)</p>
                            </div>
                            <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0" @error('photo') aria-describedby="photo-error" aria-invalid="true" @enderror>
                        </label>
                    </div>
                    @error('photo')
                        <p class="field-error mt-2" id="photo-error">{{ $message }}</p>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.getElementById('photo');
        var hint = document.querySelector('[data-file-hint="photo"]');
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
