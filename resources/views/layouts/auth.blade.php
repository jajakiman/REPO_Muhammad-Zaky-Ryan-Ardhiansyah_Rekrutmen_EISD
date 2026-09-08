<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Autentikasi AksesLoka untuk layanan pelaporan fasilitas kampus.">
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
                        navy: { 950: '#172554', 900: '#1E3A8A', 800: '#1E40AF' },
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
    <script src="{{ asset('js/auth-form.js') }}" defer></script>
</head>
<body class="min-h-[100dvh] bg-navy-950 font-sans text-slate-900 antialiased">
    <x-page-loader />
    <a class="skip-link" href="#main-content">Langsung ke formulir</a>
    <main id="main-content" tabindex="-1" class="min-h-[100dvh]">
        <div class="fixed inset-x-0 top-4 z-50 mx-auto w-[min(calc(100%-2rem),32rem)]"><x-flash /></div>
        @yield('content')
    </main>
    <script src="{{ asset('js/page-loader.js') }}" defer></script>
</body>
</html>
