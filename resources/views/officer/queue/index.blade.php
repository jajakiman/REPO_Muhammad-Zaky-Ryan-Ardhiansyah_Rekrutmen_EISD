@extends('layouts.dashboard')

@php
    $statusLabels = [
        'submitted' => 'Menunggu Verifikasi',
        'verified' => 'Terverifikasi',
        'in_progress' => 'Sedang Ditangani',
        'resolved' => 'Selesai Ditangani',
        'rejected' => 'Ditolak',
        'cancelled' => 'Dibatalkan',
    ];
    $filterOptions = array_merge(['' => 'Semua Status'], $statusLabels);
    $activeStatus = request('status', '');
    if ($activeStatus === 'all') $activeStatus = '';
    $activeLabel = $filterOptions[$activeStatus] ?? 'Semua Status';
@endphp

@section('title', 'Antrean Laporan Area | AksesLoka')

@section('content')
<section class="admin-section">
    <div class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">{{ $area->campus->name }} &bull; {{ $area->name }}</p>
                <h1>Antrean Laporan Area</h1>
                <p class="lead">Daftar laporan kendala fasilitas aksesibilitas pada area tanggung jawab Anda.</p>
            </div>
        </div>

        <!-- Filter Status Dropdown -->
        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700 whitespace-nowrap">Filter Status</span>
                    <div class="relative" data-queue-filter>
                        <button type="button" data-queue-filter-trigger aria-expanded="false" aria-haspopup="true" aria-controls="queue-status-menu" class="inline-flex min-h-11 w-full items-center justify-between gap-3 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-800 shadow-sm transition-colors hover:border-navy-900 hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600 sm:min-w-[16rem]">
                            <span>{{ $activeLabel }}</span>
                            <svg class="h-4 w-4 shrink-0 text-slate-500 transition-transform duration-150" data-queue-filter-chevron fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
                        </button>
                        <div id="queue-status-menu" data-queue-filter-panel class="absolute left-0 z-30 mt-2 hidden w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-xl">
                            <ul class="space-y-1 text-sm font-medium" aria-label="Filter Status">
                                @foreach($filterOptions as $val => $label)
                                    <li>
                                        <a href="{{ route('officer.queue.index', $val ? ['status' => $val] : []) }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 transition-colors {{ $activeStatus === $val ? 'bg-orange-50 font-bold text-orange-700' : 'text-slate-700 hover:bg-slate-100 hover:text-navy-900' }}">
                                            <span>{{ $label }}</span>
                                            @if($activeStatus === $val)
                                                <svg class="h-4 w-4 shrink-0 text-orange-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @if($activeStatus !== '')
                    <div>
                        <a href="{{ route('officer.queue.index') }}" class="button button-secondary inline-flex min-h-11 items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold bg-white text-navy-900 border border-slate-300 hover:bg-slate-50 transition-colors">Reset Filter</a>
                    </div>
                @endif
            </div>
        </div>

        @if($reports->isEmpty())
            <div class="empty-state">
                <h2>Tidak ada laporan</h2>
                <p>Saat ini tidak ada laporan dengan kriteria status yang dipilih untuk area ini.</p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <caption class="sr-only">Daftar laporan pada area tugas</caption>
                    <thead>
                        <tr>
                            <th scope="col">Kode</th>
                            <th scope="col">Lokasi</th>
                            <th scope="col">Fasilitas</th>
                            <th scope="col">Kategori Masalah</th>
                            <th scope="col">Pelapor</th>
                            <th scope="col">Status</th>
                            <th scope="col">Waktu Masuk</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <th scope="row" data-label="Kode">
                                    <code>{{ $report->report_code }}</code>
                                </th>
                                <td data-label="Lokasi">
                                    <strong>{{ $report->locationAccessibilityFeature->campusLocation->name }}</strong>
                                </td>
                                <td data-label="Fasilitas">
                                    {{ $report->locationAccessibilityFeature->accessibilityFeature->name }}
                                </td>
                                <td data-label="Kategori Masalah">
                                    {{ $report->issueCategory->name }}
                                </td>
                                <td data-label="Pelapor">
                                    {{ $report->reporter->name }}
                                </td>
                                <td data-label="Status">
                                    <span class="badge badge-{{ $report->status }}">
                                        {{ $statusLabels[$report->status] ?? $report->status }}
                                    </span>
                                </td>
                                <td data-label="Waktu Masuk">
                                    {{ $report->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                </td>
                                <td data-label="Aksi">
                                    <div class="table-actions">
                                        <a href="{{ route('officer.reports.show', $report) }}" class="button button-secondary button-sm">
                                            Periksa Laporan
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var wrap = document.querySelector('[data-queue-filter]');
        if (!wrap) return;
        var trigger = wrap.querySelector('[data-queue-filter-trigger]');
        var panel = wrap.querySelector('[data-queue-filter-panel]');
        var chevron = wrap.querySelector('[data-queue-filter-chevron]');

        function toggle(open) {
            panel.classList.toggle('hidden', !open);
            trigger.setAttribute('aria-expanded', String(open));
            if (chevron) chevron.classList.toggle('rotate-180', open);
        }

        trigger.addEventListener('click', function () {
            toggle(trigger.getAttribute('aria-expanded') !== 'true');
        });
        document.addEventListener('click', function (e) {
            if (!wrap.contains(e.target)) toggle(false);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && trigger.getAttribute('aria-expanded') === 'true') {
                toggle(false);
                trigger.focus();
            }
        });
    });
</script>
@endsection
