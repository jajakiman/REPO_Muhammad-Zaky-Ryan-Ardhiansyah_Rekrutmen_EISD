@extends('layouts.app')

@section('title', 'Daftar sebagai Pelapor | AksesLoka')

@section('content')
<section class="form-section">
    <div class="container form-layout">
        <div class="form-intro">
            <p class="eyebrow">Akun pelapor</p>
            <h1>Daftar ke AksesLoka</h1>
            <p>Buat akun untuk melaporkan masalah fasilitas aksesibilitas kampus.</p>
        </div>

        <form class="form-card" method="post" action="{{ route('register') }}" novalidate>
            @csrf
            <div class="field">
                <label for="name">Nama lengkap</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required aria-describedby="name-error">
                @error('name')<p class="field-error" id="name-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required aria-describedby="email-error">
                @error('email')<p class="field-error" id="email-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required aria-describedby="password-help password-error">
                <p class="field-help" id="password-help">Minimal 8 karakter.</p>
                @error('password')<p class="field-error" id="password-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label for="password_confirmation">Konfirmasi password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            </div>
            <div class="field">
                <label for="affiliation_type">Afiliasi</label>
                <select id="affiliation_type" name="affiliation_type" required aria-describedby="affiliation_type-error">
                    <option value="">Pilih afiliasi</option>
                    <option value="student" @selected(old('affiliation_type') === 'student')>Mahasiswa</option>
                    <option value="lecturer" @selected(old('affiliation_type') === 'lecturer')>Dosen</option>
                    <option value="staff" @selected(old('affiliation_type') === 'staff')>Staf</option>
                    <option value="visitor" @selected(old('affiliation_type') === 'visitor')>Pengunjung</option>
                </select>
                @error('affiliation_type')<p class="field-error" id="affiliation_type-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label for="campus_id">Kampus</label>
                <select id="campus_id" name="campus_id" aria-describedby="campus-help campus_id-error">
                    <option value="">Tidak memilih kampus</option>
                    @foreach ($campuses as $campus)
                        <option value="{{ $campus->id }}" @selected((string) old('campus_id') === (string) $campus->id)>{{ $campus->name }}</option>
                    @endforeach
                </select>
                <p class="field-help" id="campus-help">Wajib untuk mahasiswa, dosen, dan staf. Opsional untuk pengunjung.</p>
                @error('campus_id')<p class="field-error" id="campus_id-error">{{ $message }}</p>@enderror
            </div>
            <button class="button button-primary" type="submit">Daftar sebagai pelapor</button>
            <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk ke AksesLoka</a>.</p>
        </form>
    </div>
</section>
@endsection
