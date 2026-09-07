<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Informasi fasilitas aksesibilitas dan pelaporan masalah di lingkungan kampus.">
    <title>@yield('title', 'AksesLoka')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <a class="skip-link" href="#main-content">Langsung ke konten utama</a>
    <header class="site-header">
        <nav class="nav container" aria-label="Navigasi utama">
            <a class="brand" href="{{ route('home') }}" aria-label="AksesLoka, halaman utama">
                <span class="brand-mark" aria-hidden="true">AL</span>
                AksesLoka
            </a>
            <div class="nav-links">
                <a href="{{ route('map.index') }}">Peta</a>
                <a href="#tentang">Tentang</a>
                @guest
                    <a href="{{ route('login') }}">Masuk</a>
                    <a href="{{ route('register') }}">Daftar</a>
                @else
                    <a href="{{ route(auth()->user()->role.'.dashboard') }}">Area saya</a>
                    @if (auth()->user()->role === 'reporter')
                        <a href="{{ route('reporter.reports.index') }}">Laporan saya</a>
                        <a href="{{ route('reporter.profile.edit') }}">Profil</a>
                    @endif
                    @if (auth()->user()->role === 'officer')
                        <a href="{{ route('officer.queue.index') }}">Antrean area</a>
                    @endif
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button class="nav-button" type="submit">Keluar</button>
                    </form>
                @endguest
            </div>
        </nav>
    </header>
    <main id="main-content" tabindex="-1">
        <div class="container"><x-flash /></div>
        @yield('content')
    </main>
    <footer class="site-footer">
        <div class="container">AksesLoka membantu kampus menyediakan informasi aksesibilitas yang lebih jelas.</div>
    </footer>
</body>
</html>
