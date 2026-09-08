<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Informasi fasilitas aksesibilitas dan pelaporan masalah di lingkungan kampus.">
    <title>@yield('title', 'AksesLoka')</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}?v=2">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            950: '#172554',
                            900: '#1E3A8A',
                            800: '#1E40AF',
                        },
                        orange: {
                            700: '#C2410C',
                            600: '#EA580C',
                            500: '#F97316',
                        },
                    },
                    fontFamily: {
                        sans: ['"PP Neue Montreal"', '"Neue Montreal"', 'system-ui', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'sans-serif'],
                        display: ['"PP Editorial New"', 'Georgia', 'Cambria', '"Times New Roman"', 'serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-full flex flex-col bg-slate-50 text-slate-900 font-sans antialiased">
    <a class="skip-link" href="#main-content">Langsung ke konten utama</a>
    <header class="site-header sticky top-0 z-40 bg-navy-950/95 backdrop-blur border-b border-white/10 shadow-md">
        <nav class="nav container mx-auto px-4 flex items-center justify-between min-h-[4.75rem] gap-4" aria-label="Navigasi utama">
            <a class="brand inline-flex items-center text-white no-underline font-bold" href="{{ route('home') }}" aria-label="AksesLoka, halaman utama">
                <x-logo variant="full" size="md" textColor="white" />
            </a>
            <div class="nav-links flex items-center gap-3">
                <a href="{{ route('map.index') }}" class="inline-flex items-center text-slate-200 hover:text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors">Peta</a>
                <a href="{{ route('home') }}#statistik" class="inline-flex items-center text-slate-200 hover:text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors">Statistik</a>
                <a href="{{ route('home') }}#alur-kerja" class="inline-flex items-center text-slate-200 hover:text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors">Alur Kerja</a>
                <a href="{{ route('home') }}#sdgs" class="inline-flex items-center text-slate-200 hover:text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors">SDGs 11</a>
                <a href="{{ route('home') }}#faq" class="inline-flex items-center text-slate-200 hover:text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors">FAQ</a>
                <a href="{{ route('home') }}#tentang" class="inline-flex items-center text-slate-200 hover:text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors">Tentang</a>
                @guest
                    <a href="{{ route('login') }}" class="inline-flex items-center text-white bg-white/10 hover:bg-white/20 border border-white/20 px-3.5 py-2 rounded-lg text-sm font-semibold transition-all">Masuk</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center text-white bg-orange-700 hover:bg-orange-800 shadow-sm px-3.5 py-2 rounded-lg text-sm font-semibold transition-all">Daftar</a>
                @else
                    <a href="{{ route(auth()->user()->role.'.dashboard') }}" class="inline-flex items-center text-slate-200 hover:text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors">Area saya</a>
                    @if (auth()->user()->role === 'reporter')
                        <a href="{{ route('reporter.reports.index') }}" class="inline-flex items-center text-slate-200 hover:text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors">Laporan saya</a>
                        <a href="{{ route('reporter.profile.edit') }}" class="inline-flex items-center text-slate-200 hover:text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors">Profil</a>
                    @endif
                    @if (auth()->user()->role === 'officer')
                        <a href="{{ route('officer.queue.index') }}" class="inline-flex items-center text-slate-200 hover:text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors">Antrean area</a>
                        <a href="{{ route('officer.history.index') }}" class="inline-flex items-center text-slate-200 hover:text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors">Riwayat area</a>
                    @endif
                    <form method="post" action="{{ route('logout') }}" class="inline-flex" data-logout-form>
                        @csrf
                        <button class="nav-button inline-flex items-center text-slate-300 hover:text-red-300 px-2.5 py-1.5 text-sm font-semibold transition-colors" type="submit">Keluar</button>
                    </form>
                @endguest
            </div>
        </nav>
    </header>
    <main id="main-content" tabindex="-1" class="flex-1 focus:outline-none">
        <div class="container mx-auto px-4"><x-flash /></div>
        @yield('content')
    </main>
    <footer class="site-footer bg-white rounded-2xl shadow-sm border border-slate-200 m-4 lg:m-6 mt-auto">
        <div class="w-full max-w-screen-xl mx-auto p-6 md:py-8">
            <div class="gap-6 sm:flex sm:items-center sm:justify-between">
                <a href="{{ route('home') }}" class="mb-6 inline-flex items-center text-slate-900 no-underline sm:mb-0" aria-label="AksesLoka, halaman utama">
                    <x-logo variant="full" size="md" textColor="dark" />
                </a>
                <ul class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm font-semibold text-slate-600">
                    <li><a href="{{ route('map.index') }}" class="hover:text-navy-900 hover:underline">Peta</a></li>
                    <li><a href="{{ route('home') }}#statistik" class="hover:text-navy-900 hover:underline">Statistik</a></li>
                    <li><a href="{{ route('home') }}#alur-kerja" class="hover:text-navy-900 hover:underline">Alur Kerja</a></li>
                    <li><a href="{{ route('home') }}#sdgs" class="hover:text-navy-900 hover:underline">SDGs 11</a></li>
                    <li><a href="{{ route('home') }}#faq" class="hover:text-navy-900 hover:underline">FAQ</a></li>
                    <li><a href="{{ route('home') }}#tentang" class="hover:text-navy-900 hover:underline">Tentang</a></li>
                    @guest
                        <li><a href="{{ route('login') }}" class="hover:text-navy-900 hover:underline">Masuk</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-navy-900 hover:underline">Daftar</a></li>
                    @else
                        <li><a href="{{ route(auth()->user()->role.'.dashboard') }}" class="hover:text-navy-900 hover:underline">Area saya</a></li>
                    @endguest
                </ul>
            </div>
            <hr class="my-6 border-slate-200 lg:my-8">
            <div class="flex flex-col items-center justify-between gap-3 text-center text-sm text-slate-500 sm:flex-row sm:text-left">
                <span>&copy; {{ date('Y') }} <a href="{{ route('home') }}" class="font-bold text-navy-900 hover:underline">AksesLoka</a>. Sistem Pelaporan Fasilitas Kampus.</span>
                <span>Mendukung <strong>SDGs 11</strong> untuk ruang publik kampus yang inklusif dan aman.</span>
            </div>
        </div>
    </footer>
    @auth
        <x-logout-confirmation />
    @endauth
    <script src="https://cdn.jsdelivr.net/npm/motion@11.11.17/dist/motion.js" defer></script>
    <script src="{{ asset('js/motion-interactive.js') }}" defer></script>
</body>
</html>
