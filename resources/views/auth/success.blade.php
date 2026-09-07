@extends('layouts.auth')

@section('title', 'Berhasil | AksesLoka')

@section('content')
<section class="auth-shell relative isolate flex min-h-[100dvh] items-center overflow-hidden bg-navy-950 py-12 sm:py-16">
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-slate-950 via-navy-950 to-slate-900" aria-hidden="true"></div>
    <div class="container mx-auto max-w-md px-4">
        <noscript>
            <div class="auth-panel rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-xl">
                <p class="font-bold text-slate-900">{{ $message }}</p>
                <a class="button button-primary mt-6" href="{{ $redirectUrl }}">Lanjut ke Dashboard</a>
            </div>
        </noscript>
    </div>

    <dialog data-auth-success-dialog data-auto-close="3000" data-redirect-url="{{ $redirectUrl }}" aria-labelledby="auth-success-title" aria-describedby="auth-success-message" class="w-[min(calc(100%-2rem),28rem)] rounded-2xl border-0 bg-white p-0 shadow-2xl backdrop:bg-slate-950/80">
        <div class="p-7 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-800" aria-hidden="true">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
            </div>
            <h1 id="auth-success-title" class="mt-4 text-xl font-bold text-slate-950">Berhasil</h1>
            <p id="auth-success-message" class="mt-2 text-sm leading-6 text-slate-600">{{ $message }}</p>
            <div class="mt-6 h-1.5 overflow-hidden rounded-full bg-slate-200" aria-hidden="true">
                <div class="h-full w-full origin-left animate-pulse rounded-full bg-orange-700"></div>
            </div>
            <p class="mt-3 text-xs text-slate-500">Mengalihkan ke dashboard...</p>
        </div>
    </dialog>
    <script>
        (function () {
            var dialog = document.querySelector('[data-auth-success-dialog]');
            dialog.showModal();
            window.setTimeout(function () {
                window.location.replace(dialog.dataset.redirectUrl);
            }, Number(dialog.dataset.autoClose));
        })();
    </script>
</section>
@endsection
