@csrf
@isset($officer)
    @method('put')
@endisset

<div class="field">
    <label for="name">Nama petugas</label>
    <input id="name" name="name" value="{{ old('name', $officer->name ?? '') }}" required @error('name') aria-describedby="name-error" aria-invalid="true" @enderror>
    @error('name')
        <p class="field-error" id="name-error">{{ $message }}</p>
    @enderror
</div>

<div class="field">
    <label for="email">Alamat email</label>
    @isset($officer)
        <input id="email" value="{{ $officer->email }}" readonly class="readonly-input">
        <p class="field-hint">Alamat email petugas tidak dapat diubah setelah dibuat.</p>
    @else
        <input id="email" name="email" type="email" value="{{ old('email') }}" required @error('email') aria-describedby="email-error" aria-invalid="true" @enderror>
        @error('email')
            <p class="field-error" id="email-error">{{ $message }}</p>
        @enderror
    @endisset
</div>

@empty($officer)
    <div class="field">
        <label for="password">Kata sandi</label>
        <input id="password" name="password" type="password" required @error('password') aria-describedby="password-error" aria-invalid="true" @enderror>
        @error('password')
            <p class="field-error" id="password-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="field">
        <label for="password_confirmation">Konfirmasi kata sandi</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required>
    </div>
@endempty

<div class="field">
    <label for="campus_area_id">Area tugas</label>
    <select id="campus_area_id" name="campus_area_id" required @error('campus_area_id') aria-describedby="campus_area_id-error" aria-invalid="true" @enderror>
        <option value="">Pilih area tugas</option>
        @foreach($areas as $area)
            <option value="{{ $area->id }}" @selected((string) old('campus_area_id', $officer->campus_area_id ?? '') === (string) $area->id)>
                {{ $area->campus->name }} - {{ $area->name }}
            </option>
        @endforeach
    </select>
    @error('campus_area_id')
        <p class="field-error" id="campus_area_id-error">{{ $message }}</p>
    @enderror
</div>

@isset($officer)
    <div class="field">
        <label for="is_active">Status akun</label>
        <select id="is_active" name="is_active" required @error('is_active') aria-describedby="is_active-error" aria-invalid="true" @enderror>
            <option value="1" @selected((string) old('is_active', (int) $officer->is_active) === '1')>Aktif</option>
            <option value="0" @selected((string) old('is_active', (int) $officer->is_active) === '0')>Nonaktif</option>
        </select>
        @error('is_active')
            <p class="field-error" id="is_active-error">{{ $message }}</p>
        @enderror
    </div>
@endisset

<div class="actions">
    <button class="button button-primary" type="submit">Simpan petugas</button>
    <a class="button button-secondary" href="{{ route('admin.officers.index') }}">Batal</a>
</div>
