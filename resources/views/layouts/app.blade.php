<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Informasi fasilitas aksesibilitas dan pelaporan masalah di lingkungan kampus.">
    <title>@yield('title', 'AksesLoka')</title>
    @vite('resources/css/app.css')
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
                <a href="#tentang">Tentang</a>
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
