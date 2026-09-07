@extends('layouts.auth')

@section('title', $title.' | AksesLoka')

@section('content')
<section class="auth-shell relative isolate flex min-h-[100dvh] items-center overflow-hidden bg-navy-950 py-12 sm:py-16">
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-slate-950 via-navy-950 to-slate-900" aria-hidden="true"></div>
    <div class="container mx-auto max-w-lg px-4">
        <noscript>
            <div class="auth-panel rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-xl">
                <x-logo variant="full" size="md" textColor="dark" />
                <p class="font-bold text-slate-900">{{ $message }}</p>
                <a class="button button-primary mt-6" href="{{ $redirectUrl }}">Lanjut ke Dashboard</a>
            </div>
        </noscript>
    </div>

    <dialog data-auth-success-dialog data-auto-close="3000" data-redirect-url="{{ $redirectUrl }}" aria-labelledby="auth-success-title" aria-describedby="auth-success-message" class="m-auto w-[min(calc(100%-2rem),32rem)] rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl outline-none backdrop:bg-slate-950/80">
        <div class="border-b border-slate-100 px-7 py-5">
            <x-logo variant="full" size="sm" textColor="dark" />
        </div>
        <div class="px-7 py-8 text-center sm:px-10">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-800" aria-hidden="true">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
            </div>
            <h1 id="auth-success-title" class="mt-5 max-w-none text-2xl font-bold text-slate-950">{{ $title }}</h1>
            <p id="auth-success-message" class="mt-2 text-sm leading-6 text-slate-600">{{ $message }}</p>
            <div class="mt-7 h-1.5 overflow-hidden rounded-full bg-slate-200" aria-hidden="true">
                <div data-auth-progress class="auth-success-progress h-full rounded-full bg-orange-700"></div>
            </div>
            <p class="mt-3 text-xs font-medium text-slate-600">Menyiapkan dashboard Anda...</p>
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
