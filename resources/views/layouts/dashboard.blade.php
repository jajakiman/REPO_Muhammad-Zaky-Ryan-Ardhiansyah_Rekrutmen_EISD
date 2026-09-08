<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Dashboard AksesLoka - Sistem Pelaporan Fasilitas Kampus.">
    <title>@yield('title', 'Dashboard | AksesLoka')</title>
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
                        navy: { 950: '#172554', 900: '#1E3A8A', 800: '#1E40AF', 50: '#EFF6FF' },
                        orange: { 700: '#C2410C', 600: '#EA580C', 500: '#F97316' },
                    },
                    fontFamily: {
                        sans: ['"PP Neue Montreal"', '"Neue Montreal"', 'system-ui', '-apple-system', 'sans-serif'],
                        display: ['"PP Editorial New"', 'Georgia', 'Cambria', 'serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-full bg-slate-100 font-sans text-slate-900 antialiased flex flex-col md:flex-row">
    <x-page-loader />
    <a class="skip-link" href="#dashboard-main">Langsung ke konten utama</a>

    <!-- Mobile Top Navigation Header -->
    <header class="md:hidden bg-navy-950 text-white p-4 flex items-center justify-between border-b border-white/10 sticky top-0 z-40">
        <a href="{{ route('home') }}" class="inline-flex items-center">
            <x-logo variant="full" size="sm" textColor="white" />
        </a>
        <button type="button" id="sidebar-toggle-btn" aria-expanded="false" aria-controls="dashboard-sidebar" class="p-2 rounded-lg bg-white/10 text-white hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-orange-500" aria-label="Buka menu navigasi">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
        </button>
    </header>

    <!-- Mobile Drawer Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-950/70 z-40 hidden md:hidden transition-opacity backdrop-blur-xs" aria-hidden="true"></div>

    <!-- Sidebar Navigation -->
    <aside id="dashboard-sidebar" class="dashboard-sidebar fixed inset-y-0 left-0 z-50 w-72 bg-navy-950 text-white flex flex-col -translate-x-full md:translate-x-0 md:static md:w-64 lg:w-72 transition-transform duration-200 ease-in-out shadow-2xl md:shadow-xl md:sticky md:top-0 md:min-h-screen shrink-0 border-r border-white/10">
        <!-- Sidebar Brand & Mobile Close Button -->
        <div class="p-6 border-b border-white/10 flex items-center justify-between">
            <a href="{{ route('home') }}" class="inline-flex items-center no-underline">
                <x-logo variant="full" size="sm" textColor="white" />
            </a>
            <button type="button" id="sidebar-close-btn" class="md:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-orange-500" aria-label="Tutup menu navigasi">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Sidebar Navigation Menu -->
        <nav class="sidebar-navigation flex-1 px-4 space-y-1.5 overflow-y-auto" aria-label="Menu navigasi dashboard">
            @if(auth()->user()->role === 'reporter')
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Menu Pelapor</p>
                <a href="{{ route('reporter.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('reporter.dashboard') ? 'bg-orange-700 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Dashboard
                </a>
                <a href="{{ route('reporter.reports.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('reporter.reports.create') ? 'bg-orange-700 text-white shadow-sm' : 'bg-white/10 text-white hover:bg-white/15' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Buat Laporan
                </a>
                <a href="{{ route('reporter.reports.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('reporter.reports.index') || request()->routeIs('reporter.reports.show') ? 'bg-orange-700 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    Laporan Masalah
                </a>
                <a href="{{ route('map.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:bg-white/10 hover:text-white transition-all">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                    Peta Kampus & Fasilitas
                </a>
                <a href="{{ route('reporter.profile.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('reporter.profile.*') ? 'bg-orange-700 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    Profil & Afiliasi
                </a>
            @elseif(auth()->user()->role === 'officer')
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Menu Petugas</p>
                <a href="{{ route('officer.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('officer.dashboard') ? 'bg-orange-700 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Dashboard Area
                </a>
                <a href="{{ route('officer.queue.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('officer.queue.*') || request()->routeIs('officer.reports.*') ? 'bg-orange-700 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    Antrean Laporan Area
                </a>
                <a href="{{ route('officer.history.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('officer.history.*') ? 'bg-orange-700 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Riwayat Penanganan
                </a>
                <a href="{{ route('map.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:bg-white/10 hover:text-white transition-all">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                    Peta Kampus
                </a>
            @else
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Menu Administrator</p>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-orange-700 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Dashboard Pusat
                </a>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-orange-700 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Monitoring Laporan
                </a>
                <a href="{{ route('admin.campuses.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.campuses.*') ? 'bg-orange-700 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    Master Kampus & Area
                </a>
                <a href="{{ route('admin.features.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.features.*') ? 'bg-orange-700 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Master Fasilitas
                </a>
                <a href="{{ route('admin.issue-categories.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.issue-categories.*') ? 'bg-orange-700 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                    Kategori Masalah
                </a>
                <a href="{{ route('admin.officers.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.officers.*') ? 'bg-orange-700 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    Kelola Akun Petugas
                </a>
                <a href="{{ route('map.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:bg-white/10 hover:text-white transition-all">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                    Peta Aksesibilitas
                </a>
            @endif
        </nav>

        <!-- Sidebar Bottom Footer -->
        <div class="p-4 border-t border-white/10 space-y-2">
            <div data-sidebar-profile class="mb-3 rounded-xl border border-white/10 bg-white/5 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-700 text-white font-extrabold flex items-center justify-center text-sm shadow-sm shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between text-xs">
                    <span class="text-slate-400">Peran Akun:</span>
                    @if(auth()->user()->role === 'reporter')
                        <span class="px-2 py-0.5 rounded-full font-bold bg-blue-500/20 text-blue-300 border border-blue-400/30">Pelapor</span>
                    @elseif(auth()->user()->role === 'officer')
                        <span class="px-2 py-0.5 rounded-full font-bold bg-orange-500/20 text-orange-300 border border-orange-400/30">Petugas</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">Administrator</span>
                    @endif
                </div>
                @if(auth()->user()->role === 'officer' && auth()->user()->campusArea)
                    <p class="mt-1.5 text-[11px] text-orange-300 truncate">Area: {{ auth()->user()->campusArea->name }}</p>
                @endif
            </div>
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:bg-white/10 hover:text-white transition-colors">
                <svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                Lihat Beranda Publik
            </a>
            <form method="post" action="{{ route('logout') }}" data-logout-form>
                @csrf
                <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-red-300 hover:bg-red-500/20 hover:text-red-200 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Keluar dari Sistem
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 bg-slate-50 min-h-screen">
        <!-- Top Workspace Bar -->
        <header data-workspace-header class="bg-white border-b border-slate-200 px-4 sm:px-6 lg:px-8 py-3.5 shadow-xs sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Portal Kerja</span>
                <span class="text-slate-300">&bull;</span>
                <span class="text-sm font-semibold text-slate-700">@yield('title', 'Dashboard')</span>
            </div>
        </header>

        <!-- Dynamic Content Body -->
        <main id="dashboard-main" tabindex="-1" class="flex-1 p-4 sm:p-6 lg:p-8 focus:outline-none max-w-7xl w-full mx-auto">
            <x-flash />
            @yield('content')
        </main>
    </div>

    <x-logout-confirmation />
    <div data-toast-container class="pointer-events-none fixed right-4 top-4 z-[70] flex w-[min(calc(100%-2rem),24rem)] flex-col gap-3" aria-live="polite"></div>
    <script src="{{ asset('js/status-switch.js') }}" defer></script>
    <script src="{{ asset('js/page-loader.js') }}" defer></script>

    <!-- Accessible Mobile Drawer Controller -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggleBtn = document.getElementById('sidebar-toggle-btn');
            var closeBtn = document.getElementById('sidebar-close-btn');
            var sidebar = document.getElementById('dashboard-sidebar');
            var backdrop = document.getElementById('sidebar-backdrop');

            if (!toggleBtn || !sidebar || !backdrop) return;

            function setDrawer(open) {
                toggleBtn.setAttribute('aria-expanded', String(open));
                if (open) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    backdrop.classList.remove('hidden');
                    if (closeBtn) closeBtn.focus();
                } else {
                    sidebar.classList.add('-translate-x-full');
                    sidebar.classList.remove('translate-x-0');
                    backdrop.classList.add('hidden');
                    toggleBtn.focus();
                }
            }

            toggleBtn.addEventListener('click', function () {
                var isOpen = toggleBtn.getAttribute('aria-expanded') === 'true';
                setDrawer(!isOpen);
            });

            if (closeBtn) {
                closeBtn.addEventListener('click', function () {
                    setDrawer(false);
                });
            }

            backdrop.addEventListener('click', function () {
                setDrawer(false);
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && toggleBtn.getAttribute('aria-expanded') === 'true') {
                    setDrawer(false);
                }
            });
        });
    </script>
</body>
</html>
