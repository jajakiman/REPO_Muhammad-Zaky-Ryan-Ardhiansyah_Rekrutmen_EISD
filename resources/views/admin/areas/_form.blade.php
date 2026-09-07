@csrf
@isset($area) @method('put') @endisset
<div class="field">
    <label for="name">Nama area <span class="required-mark text-red-600" aria-hidden="true">*</span></label>
    <input id="name" name="name" value="{{ old('name', $area->name ?? '') }}" required @error('name') aria-describedby="name-error" aria-invalid="true" @enderror>
    @error('name')<p class="field-error" id="name-error">{{ $message }}</p>@enderror
</div>
@error('campus')<p class="field-error">{{ $message }}</p>@enderror
@isset($area)
<div class="field"><label for="is_active">Status <span class="required-mark text-red-600" aria-hidden="true">*</span></label><x-select-shell><select id="is_active" name="is_active" required @error('is_active') aria-describedby="is_active-error" aria-invalid="true" @enderror>
    <option value="1" @selected((string) old('is_active', (int) $area->is_active) === '1')>Aktif</option>
    <option value="0" @selected((string) old('is_active', (int) $area->is_active) === '0')>Nonaktif</option>
</select></x-select-shell>@error('is_active')<p class="field-error" id="is_active-error">{{ $message }}</p>@enderror</div>
@endisset
<div class="actions"><button class="button button-primary" type="submit">Simpan area</button><a class="button button-secondary" href="{{ route('admin.campuses.areas.index', $campus) }}">Batal</a></div>
