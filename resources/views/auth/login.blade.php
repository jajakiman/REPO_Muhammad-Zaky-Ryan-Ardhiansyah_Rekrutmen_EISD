@extends('layouts.app')

@section('title', 'Masuk | AksesLoka')

@section('content')
<section class="auth-shell relative isolate min-h-[calc(100dvh-4.75rem)] overflow-hidden bg-slate-950 py-12 sm:py-16">
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-slate-950 via-navy-950 to-slate-900" aria-hidden="true"></div>
    <div class="absolute -left-24 top-20 -z-10 h-72 w-72 rounded-full bg-blue-800/20 blur-3xl" aria-hidden="true"></div>
    <div class="absolute -right-24 bottom-10 -z-10 h-72 w-72 rounded-full bg-orange-600/10 blur-3xl" aria-hidden="true"></div>
    <div class="container mx-auto px-4 max-w-md">
        <a href="{{ route('map.index') }}" class="mb-5 inline-flex min-h-11 items-center text-sm font-semibold text-slate-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-orange-500 rounded-lg">Kembali ke peta kampus</a>
        <div class="auth-panel bg-white border border-slate-200 rounded-2xl shadow-xl shadow-slate-200/60 overflow-hidden">
            <div class="px-6 pt-8 text-center sm:px-8">
                <x-logo variant="mark" size="lg" />
                <p class="mt-5 font-sans text-xs font-bold uppercase tracking-wider text-orange-700">Akun AksesLoka</p>
                <h1 class="mt-2 font-display text-3xl font-bold text-slate-900 max-w-none">Masuk ke AksesLoka</h1>
                <p class="mt-2 font-sans text-sm leading-6 text-slate-600">Gunakan akun Anda untuk mengakses layanan sesuai peran.</p>
            </div>
            <form class="px-6 py-8 sm:px-8" method="post" action="{{ route('login') }}" novalidate>
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required @error('email') aria-describedby="email-error" aria-invalid="true" @enderror>
                @error('email')<p class="field-error" id="email-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required @error('password') aria-describedby="password-error" aria-invalid="true" @enderror>
                @error('password')<p class="field-error" id="password-error">{{ $message }}</p>@enderror
            </div>
                <button class="button button-primary w-full rounded-xl bg-orange-700 hover:bg-orange-800 text-white shadow-md transition-colors" type="submit">Masuk ke AksesLoka</button>
                <p class="mt-6 text-center text-sm text-slate-600">Belum punya akun? <a class="font-semibold text-navy-900 underline underline-offset-4" href="{{ route('register') }}">Daftar sebagai pelapor</a>.</p>
            </form>
        </div>
    </div>
</section>
@endsection
