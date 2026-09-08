@php
    $user = auth()->user();
    $roleLabels = ['reporter' => 'Pelapor', 'officer' => 'Petugas', 'admin' => 'Administrator'];
@endphp

<div class="relative" data-user-menu>
    <button id="user-menu-button" type="button" data-user-menu-button aria-expanded="false" aria-haspopup="true" aria-controls="user-menu-panel" class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-white/20 bg-white/10 px-3 py-2 text-sm font-semibold text-white hover:bg-white/20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-500">
        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-orange-700 text-xs font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
        <span class="hidden max-w-32 truncate sm:inline">{{ $user->name }}</span>
        <svg class="h-4 w-4 text-slate-300" aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
    </button>

    <div id="user-menu-panel" data-user-menu-panel class="absolute right-0 z-50 mt-2 hidden w-72 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
        <div class="border-b border-slate-100 bg-slate-50 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-700 font-bold text-white shadow-xs">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-bold text-slate-900">{{ $user->name }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                </div>
            </div>
            <div class="mt-3 flex items-center pt-2.5 border-t border-slate-200/60 text-xs">
                @if($user->role === 'reporter')
                    <span class="rounded-full border border-blue-200 bg-blue-50 px-2.5 py-0.5 font-bold text-blue-700">Pelapor</span>
                @elseif($user->role === 'officer')
                    <span class="rounded-full border border-orange-200 bg-orange-50 px-2.5 py-0.5 font-bold text-orange-700">Petugas</span>
                @else
                    <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 font-bold text-emerald-700">Administrator</span>
                @endif
            </div>
        </div>
        <ul class="space-y-1 p-2 text-sm font-medium" aria-labelledby="user-menu-button">
            <li>
                <a href="{{ route($user->role.'.dashboard') }}" class="flex min-h-11 w-full items-center gap-2.5 rounded-xl px-3 py-2 text-slate-700 hover:bg-slate-100 hover:text-navy-900 transition-colors">
                    <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Dashboard
                </a>
            </li>
            @if($user->role === 'reporter')
                <li>
                    <a href="{{ route('reporter.reports.create') }}" class="flex min-h-11 w-full items-center gap-2.5 rounded-xl px-3 py-2 text-orange-700 hover:bg-orange-50 hover:text-orange-800 transition-colors">
                        <svg class="h-4 w-4 shrink-0 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Buat Laporan
                    </a>
                </li>
                <li>
                    <a href="{{ route('reporter.reports.index') }}" class="flex min-h-11 w-full items-center gap-2.5 rounded-xl px-3 py-2 text-slate-700 hover:bg-slate-100 hover:text-navy-900 transition-colors">
                        <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                        Laporan Masalah
                    </a>
                </li>
                <li>
                    <a href="{{ route('reporter.profile.edit') }}" class="flex min-h-11 w-full items-center gap-2.5 rounded-xl px-3 py-2 text-slate-700 hover:bg-slate-100 hover:text-navy-900 transition-colors">
                        <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        Profil &amp; Afiliasi
                    </a>
                </li>
            @elseif($user->role === 'officer')
                <li>
                    <a href="{{ route('officer.queue.index') }}" class="flex min-h-11 w-full items-center gap-2.5 rounded-xl px-3 py-2 text-slate-700 hover:bg-slate-100 hover:text-navy-900 transition-colors">
                        <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        Antrean Area
                    </a>
                </li>
                <li>
                    <a href="{{ route('officer.history.index') }}" class="flex min-h-11 w-full items-center gap-2.5 rounded-xl px-3 py-2 text-slate-700 hover:bg-slate-100 hover:text-navy-900 transition-colors">
                        <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Riwayat Penanganan
                    </a>
                </li>
            @else
                <li>
                    <a href="{{ route('admin.reports.index') }}" class="flex min-h-11 w-full items-center gap-2.5 rounded-xl px-3 py-2 text-slate-700 hover:bg-slate-100 hover:text-navy-900 transition-colors">
                        <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Monitoring Laporan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.campuses.index') }}" class="flex min-h-11 w-full items-center gap-2.5 rounded-xl px-3 py-2 text-slate-700 hover:bg-slate-100 hover:text-navy-900 transition-colors">
                        <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        Master Kampus &amp; Area
                    </a>
                </li>
            @endif
        </ul>
        <form method="post" action="{{ route('logout') }}" class="border-t border-slate-100 p-2" data-logout-form>
            @csrf
            <button type="submit" class="flex min-h-11 w-full items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50 hover:text-red-800 transition-colors">
                <svg class="h-4 w-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                Keluar dari Akun
            </button>
        </form>
    </div>
</div>
