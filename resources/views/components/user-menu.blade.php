@php
    $user = auth()->user();
    $roleLabels = ['reporter' => 'Pelapor', 'officer' => 'Petugas', 'admin' => 'Administrator'];
@endphp

<div class="relative" data-user-menu>
    <button id="user-menu-button" type="button" data-user-menu-button aria-expanded="false" aria-haspopup="true" aria-controls="user-menu-panel" class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-white/20 bg-white/10 px-3 py-2 text-sm font-semibold text-white hover:bg-white/20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-500">
        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-orange-700 text-xs font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
        <span class="hidden max-w-32 truncate sm:inline">{{ $user->name }}</span>
        <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
    </button>

    <div id="user-menu-panel" data-user-menu-panel class="absolute right-0 z-50 mt-2 hidden w-64 overflow-hidden rounded-xl border border-slate-200 bg-white py-1.5 shadow-xl">
        <div class="border-b border-slate-100 bg-slate-50 px-4 py-3">
            <p class="truncate text-sm font-bold text-slate-900">{{ $user->name }}</p>
            <p class="truncate text-xs text-slate-600">{{ $user->email }}</p>
            <p class="mt-1 text-xs font-semibold text-orange-700">{{ $roleLabels[$user->role] }}</p>
        </div>
        <ul class="space-y-0.5 p-2 text-sm font-medium text-slate-700" aria-labelledby="user-menu-button">
            <li><a href="{{ route($user->role.'.dashboard') }}" class="inline-flex min-h-11 w-full items-center rounded-lg p-2 hover:bg-slate-100 hover:text-navy-900">Dashboard</a></li>
            @if($user->role === 'reporter')
                <li><a href="{{ route('reporter.reports.create') }}" class="inline-flex min-h-11 w-full items-center rounded-lg p-2 text-orange-700 hover:bg-orange-50">Buat Laporan</a></li>
                <li><a href="{{ route('reporter.reports.index') }}" class="inline-flex min-h-11 w-full items-center rounded-lg p-2 hover:bg-slate-100 hover:text-navy-900">Laporan Saya</a></li>
                <li><a href="{{ route('reporter.profile.edit') }}" class="inline-flex min-h-11 w-full items-center rounded-lg p-2 hover:bg-slate-100 hover:text-navy-900">Profil &amp; Afiliasi</a></li>
            @elseif($user->role === 'officer')
                <li><a href="{{ route('officer.queue.index') }}" class="inline-flex min-h-11 w-full items-center rounded-lg p-2 hover:bg-slate-100 hover:text-navy-900">Antrean Area</a></li>
                <li><a href="{{ route('officer.history.index') }}" class="inline-flex min-h-11 w-full items-center rounded-lg p-2 hover:bg-slate-100 hover:text-navy-900">Riwayat Penanganan</a></li>
            @else
                <li><a href="{{ route('admin.reports.index') }}" class="inline-flex min-h-11 w-full items-center rounded-lg p-2 hover:bg-slate-100 hover:text-navy-900">Monitoring Laporan</a></li>
                <li><a href="{{ route('admin.campuses.index') }}" class="inline-flex min-h-11 w-full items-center rounded-lg p-2 hover:bg-slate-100 hover:text-navy-900">Master Kampus &amp; Area</a></li>
            @endif
        </ul>
        <form method="post" action="{{ route('logout') }}" class="border-t border-slate-100 px-2 pt-1" data-logout-form>
            @csrf
            <button type="submit" class="inline-flex min-h-11 w-full items-center rounded-lg p-2 text-sm font-semibold text-red-700 hover:bg-red-50">Keluar dari Akun</button>
        </form>
    </div>
</div>
