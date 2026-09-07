@csrf
@isset($campus) @method('put') @endisset
<div class="field">
    <label for="name">Nama kampus <span class="required-mark text-red-600" aria-hidden="true">*</span></label>
    <input id="name" name="name" value="{{ old('name', $campus->name ?? '') }}" required @error('name') aria-describedby="name-error" aria-invalid="true" @enderror>
    @error('name')<p class="field-error" id="name-error">{{ $message }}</p>@enderror
</div>
<div class="field">
    <label for="address">Alamat</label>
    <textarea id="address" name="address" rows="3" @error('address') aria-describedby="address-error" aria-invalid="true" @enderror>{{ old('address', $campus->address ?? '') }}</textarea>
    @error('address')<p class="field-error" id="address-error">{{ $message }}</p>@enderror
</div>
<div class="field-grid">
    <div class="field">
        <label for="latitude">Latitude</label>
        <input id="latitude" name="latitude" type="number" step="0.0000001" min="-90" max="90" value="{{ old('latitude', $campus->latitude ?? '') }}" @error('latitude') aria-describedby="latitude-error" aria-invalid="true" @enderror>
        @error('latitude')<p class="field-error" id="latitude-error">{{ $message }}</p>@enderror
    </div>
    <div class="field">
        <label for="longitude">Longitude</label>
        <input id="longitude" name="longitude" type="number" step="0.0000001" min="-180" max="180" value="{{ old('longitude', $campus->longitude ?? '') }}" @error('longitude') aria-describedby="longitude-error" aria-invalid="true" @enderror>
        @error('longitude')<p class="field-error" id="longitude-error">{{ $message }}</p>@enderror
    </div>
</div>
@isset($campus)
<div class="field">
    <label for="is_active">Status <span class="required-mark text-red-600" aria-hidden="true">*</span></label>
    <x-select-shell>
    <select id="is_active" name="is_active" required @error('is_active') aria-describedby="is_active-error" aria-invalid="true" @enderror>
        <option value="1" @selected((string) old('is_active', (int) $campus->is_active) === '1')>Aktif</option>
        <option value="0" @selected((string) old('is_active', (int) $campus->is_active) === '0')>Nonaktif</option>
    </select>
    </x-select-shell>
    @error('is_active')<p class="field-error" id="is_active-error">{{ $message }}</p>@enderror
</div>
@endisset
<div class="actions">
    <button class="button button-primary" type="submit">Simpan kampus</button>
    <a class="button button-secondary" href="{{ route('admin.campuses.index') }}">Batal</a>
</div>
