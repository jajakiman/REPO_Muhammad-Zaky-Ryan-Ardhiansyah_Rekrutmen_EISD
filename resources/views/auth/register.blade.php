@extends('layouts.app')

@section('title', 'Daftar sebagai Pelapor | AksesLoka')

@section('content')
<section class="auth-shell relative isolate min-h-[calc(100dvh-4.75rem)] overflow-hidden bg-slate-950 py-12 sm:py-16">
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-slate-950 via-navy-950 to-slate-900" aria-hidden="true"></div>
    <div class="absolute -left-24 top-20 -z-10 h-72 w-72 rounded-full bg-blue-800/20 blur-3xl" aria-hidden="true"></div>
    <div class="absolute -right-24 bottom-10 -z-10 h-72 w-72 rounded-full bg-orange-600/10 blur-3xl" aria-hidden="true"></div>
    <div class="container mx-auto px-4 max-w-2xl">
        <a href="{{ route('map.index') }}" class="mb-5 inline-flex min-h-11 items-center text-sm font-semibold text-slate-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-orange-500 rounded-lg">Kembali ke peta kampus</a>
        <div class="auth-panel bg-white border border-slate-200 rounded-2xl shadow-xl shadow-slate-200/60 overflow-hidden">
            <div class="px-6 pt-8 text-center sm:px-10">
                <x-logo variant="mark" size="lg" />
                <p class="mt-5 font-sans text-xs font-bold uppercase tracking-wider text-orange-700">Akun Pelapor</p>
                <h1 class="mt-2 font-display text-3xl font-bold text-slate-900 max-w-none">Daftar Akun Pelapor</h1>
                <p class="mt-2 font-sans text-sm leading-6 text-slate-600">Buat akun untuk melaporkan dan memantau kendala fasilitas kampus.</p>
            </div>

            <form class="px-6 py-8 sm:px-10" method="post" action="{{ route('register') }}" novalidate>
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5">
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
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5">
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
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5">
            <div class="field">
                <label for="affiliation_type">Afiliasi</label>
                <x-select-shell>
                <select id="affiliation_type" name="affiliation_type" required aria-describedby="affiliation_type-error" class="hover:border-navy-900 focus:border-navy-900 focus:ring-4 focus:ring-orange-500/20 focus:outline-none">
                    <option value="">Pilih afiliasi</option>
                    <option value="student" @selected(old('affiliation_type') === 'student')>Mahasiswa</option>
                    <option value="lecturer" @selected(old('affiliation_type') === 'lecturer')>Dosen</option>
                    <option value="staff" @selected(old('affiliation_type') === 'staff')>Staf</option>
                    <option value="visitor" @selected(old('affiliation_type') === 'visitor')>Pengunjung</option>
                </select>
                </x-select-shell>
                @error('affiliation_type')<p class="field-error" id="affiliation_type-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label for="campus_id">Kampus</label>
                <x-select-shell>
                <select id="campus_id" name="campus_id" aria-describedby="campus-help campus_id-error" class="hover:border-navy-900 focus:border-navy-900 focus:ring-4 focus:ring-orange-500/20 focus:outline-none">
                    <option value="">Tidak memilih kampus</option>
                    @foreach ($campuses as $campus)
                        <option value="{{ $campus->id }}" @selected((string) old('campus_id') === (string) $campus->id)>{{ $campus->name }}</option>
                    @endforeach
                </select>
                </x-select-shell>
                <p class="field-help" id="campus-help">Wajib untuk mahasiswa, dosen, dan staf. Opsional untuk pengunjung.</p>
                @error('campus_id')<p class="field-error" id="campus_id-error">{{ $message }}</p>@enderror
            </div>
            </div>
                <button class="button button-primary w-full rounded-xl bg-orange-700 hover:bg-orange-800 text-white shadow-md transition-colors" type="submit">Daftar sebagai pelapor</button>
                <p class="mt-6 text-center text-sm text-slate-600">Sudah punya akun? <a class="font-semibold text-navy-900 underline underline-offset-4" href="{{ route('login') }}">Masuk ke AksesLoka</a>.</p>
            </form>
        </div>
    </div>
</section>
@endsection
