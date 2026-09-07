@extends('layouts.auth')

@section('title', 'Masuk | AksesLoka')

@section('content')
<section class="auth-shell relative isolate flex min-h-[100dvh] items-center overflow-hidden bg-navy-950 py-12 sm:py-16">
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-slate-950 via-navy-950 to-slate-900" aria-hidden="true"></div>
    <div class="absolute -left-24 top-20 -z-10 h-72 w-72 rounded-full bg-blue-800/20 blur-3xl" aria-hidden="true"></div>
    <div class="absolute -right-24 bottom-10 -z-10 h-72 w-72 rounded-full bg-orange-600/10 blur-3xl" aria-hidden="true"></div>
    <div class="container mx-auto px-4 max-w-md">
        <a href="{{ route('home') }}" class="mb-5 inline-flex min-h-11 items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm font-semibold text-slate-200 shadow-sm transition-colors hover:bg-white/20 hover:text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
            <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" /></svg>
            Kembali ke Halaman Utama
        </a>
        <div class="auth-panel bg-white border border-slate-200 rounded-2xl shadow-xl shadow-slate-200/60 overflow-hidden">
            <div class="px-6 pt-8 text-center sm:px-8">
                <x-logo variant="mark" size="lg" />
                <p class="mt-5 font-sans text-xs font-bold uppercase tracking-wider text-orange-700">Akun AksesLoka</p>
                <h1 class="mt-2 font-display text-3xl font-bold text-slate-900 max-w-none">Masuk ke AksesLoka</h1>
                <p class="mt-2 font-sans text-sm leading-6 text-slate-600">Gunakan akun Anda untuk mengakses layanan sesuai peran.</p>
            </div>
            <form class="px-6 py-8 sm:px-8" method="post" action="{{ route('login') }}" data-validated-form novalidate>
            @csrf
            @if ($errors->any())
                <div class="form-error-summary mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                    <p class="font-bold">Mohon periksa kembali formulir Anda.</p>
                    <p class="mt-1">Terdapat data yang belum terisi atau tidak sesuai ketentuan.</p>
                </div>
            @endif
            <div data-client-error-summary class="form-error-summary mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert" hidden>
                <p class="font-bold">Mohon periksa kembali formulir Anda.</p>
                <p class="mt-1">Lengkapi seluruh field wajib sebelum melanjutkan.</p>
            </div>
            <div class="field">
                <label for="email">Email <span class="required-mark text-red-600" aria-hidden="true">*</span><span class="sr-only"> wajib</span></label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required @error('email') aria-describedby="email-error" aria-invalid="true" @enderror>
                @error('email')<p class="field-error" id="email-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label for="password">Password <span class="required-mark text-red-600" aria-hidden="true">*</span><span class="sr-only"> wajib</span></label>
                <div class="relative">
                    <input class="pr-14" id="password" name="password" type="password" autocomplete="current-password" required @error('password') aria-describedby="password-error" aria-invalid="true" @enderror>
                    <x-password-toggle target="password" />
                </div>
                @error('password')<p class="field-error" id="password-error">{{ $message }}</p>@enderror
            </div>
                <button class="button button-primary w-full rounded-xl bg-orange-700 hover:bg-orange-800 text-white shadow-md transition-colors" type="submit">Masuk ke AksesLoka</button>
                <p class="mt-6 text-center text-sm text-slate-600">Belum punya akun? <a class="font-semibold text-navy-900 underline underline-offset-4" href="{{ route('register') }}">Daftar sebagai pelapor</a>.</p>
            </form>
        </div>
    </div>
</section>
@endsection
