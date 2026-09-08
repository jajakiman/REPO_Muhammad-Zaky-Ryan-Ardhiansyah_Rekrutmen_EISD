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

        <!-- Filter Form Card -->
        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm mb-6">
            <form method="get" action="{{ route('officer.queue.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                    <label for="queue-status" class="text-xs font-bold uppercase tracking-wider text-slate-700 whitespace-nowrap">
                        Filter Status
                    </label>
                    <x-select-shell class="w-full sm:w-72">
                    <select id="queue-status" name="status" onchange="this.form.submit()" class="font-semibold text-slate-800">
                        <option value="" @selected(!request('status') || request('status') === 'all')>Semua Status</option>
                        <option value="submitted" @selected(request('status') === 'submitted')>Menunggu Verifikasi</option>
                        <option value="verified" @selected(request('status') === 'verified')>Terverifikasi</option>
                        <option value="in_progress" @selected(request('status') === 'in_progress')>Sedang Ditangani</option>
                        <option value="resolved" @selected(request('status') === 'resolved')>Selesai Ditangani</option>
                        <option value="rejected" @selected(request('status') === 'rejected')>Ditolak</option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>Dibatalkan</option>
                    </select>
                    </x-select-shell>
                </div>
                @if(request()->filled('status') && request('status') !== 'all')
                    <div>
                        <a href="{{ route('officer.queue.index') }}" class="button button-secondary inline-flex min-h-11 items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold bg-white text-navy-900 border border-slate-300 hover:bg-slate-50 transition-colors">Reset Filter</a>
                    </div>
                @endif
            </form>
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
@endsection
