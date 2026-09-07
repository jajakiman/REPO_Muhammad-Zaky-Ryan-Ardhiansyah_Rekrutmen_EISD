@extends('layouts.app')

@section('title', 'Masuk | AksesLoka')

@section('content')
<section class="auth-shell bg-gradient-to-b from-slate-50 to-blue-50/60 py-12 sm:py-16">
    <div class="container mx-auto px-4 max-w-md">
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
