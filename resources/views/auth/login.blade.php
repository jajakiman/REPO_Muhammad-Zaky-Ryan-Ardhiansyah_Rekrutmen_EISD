@extends('layouts.app')

@section('title', 'Masuk | AksesLoka')

@section('content')
<section class="form-section">
    <div class="container form-layout">
        <div class="form-intro">
            <p class="eyebrow">Akun AksesLoka</p>
            <h1>Masuk</h1>
            <p>Masuk untuk melanjutkan tugas sesuai peran akun Anda.</p>
        </div>
        <form class="form-card" method="post" action="{{ route('login') }}" novalidate>
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required aria-describedby="email-error">
                @error('email')<p class="field-error" id="email-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required aria-describedby="password-error">
                @error('password')<p class="field-error" id="password-error">{{ $message }}</p>@enderror
            </div>
            <button class="button button-primary" type="submit">Masuk ke AksesLoka</button>
            <p>Belum punya akun? <a href="{{ route('register') }}">Daftar sebagai pelapor</a>.</p>
        </form>
    </div>
</section>
@endsection
