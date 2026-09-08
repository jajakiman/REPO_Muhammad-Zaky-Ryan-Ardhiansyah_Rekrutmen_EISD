@extends('layouts.dashboard')

@section('title', 'Monitoring Seluruh Laporan | AksesLoka')

@section('content')
<section class="admin-section">
    <div class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Pusat Monitoring</p>
                <h1>Monitoring Seluruh Laporan</h1>
                <p class="lead">Pantau perkembangan penanganan laporan kendala fasilitas aksesibilitas di seluruh kampus secara terpusat.</p>
            </div>
        </div>

        <!-- Filter Form Card -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm mb-6">
            <form method="get" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div class="field mb-0">
                    <label for="filter-campus" class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Kampus</label>
                    <x-select-shell>
                    <select id="filter-campus" name="campus_id">
                        <option value="">Semua Kampus</option>
                        @foreach($campuses as $c)
                            <option value="{{ $c->id }}" @selected(request('campus_id') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    </x-select-shell>
                </div>

                <div class="field mb-0">
                    <label for="filter-status" class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Status Laporan</label>
                    <x-select-shell>
                    <select id="filter-status" name="status">
                        <option value="">Semua Status</option>
                        @foreach($statusLabels as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    </x-select-shell>
                </div>

                <div class="field mb-0">
                    <label for="filter-priority" class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Prioritas</label>
                    <x-select-shell>
                    <select id="filter-priority" name="priority">
                        <option value="">Semua Prioritas</option>
                        @foreach($priorityLabels as $value => $label)
                            <option value="{{ $value }}" @selected(request('priority') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    </x-select-shell>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <button type="submit" class="button button-primary flex-1 inline-flex min-h-11 items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold bg-orange-700 text-white hover:bg-orange-800 shadow-sm transition-colors">Terapkan Filter</button>
                    @if(request()->hasAny(['campus_id', 'campus_area_id', 'campus_location_id', 'status', 'priority']))
                        <a href="{{ route('admin.reports.index') }}" class="button button-secondary inline-flex min-h-11 items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-white text-navy-900 border border-slate-300 hover:bg-slate-50 transition-colors">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        @if($reports->isEmpty())
            <div class="empty-state">
                <h2>Tidak ada laporan</h2>
                <p>Tidak ada laporan masalah yang sesuai dengan kriteria filter.</p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <caption class="sr-only">Daftar monitoring seluruh laporan masalah lintas kampus</caption>
                    <thead>
                        <tr>
                            <th scope="col">Kode</th>
                            <th scope="col">Pelapor</th>
                            <th scope="col">Kampus & Lokasi</th>
                            <th scope="col">Fasilitas</th>
                            <th scope="col">Petugas</th>
                            <th scope="col">Prioritas</th>
                            <th scope="col">Status</th>
                            <th scope="col">Waktu Dibuat</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <th scope="row" data-label="Kode">
                                    <code>{{ $report->report_code }}</code>
                                </th>
                                <td data-label="Pelapor">
                                    {{ $report->reporter->name }}
                                </td>
                                <td data-label="Kampus & Lokasi">
                                    <strong>{{ $report->locationAccessibilityFeature->campusLocation->name }}</strong>
                                    <br>
                                    <span class="field-hint">{{ $report->locationAccessibilityFeature->campusLocation->campusArea->campus->name }} &bull; {{ $report->locationAccessibilityFeature->campusLocation->campusArea->name }}</span>
                                </td>
                                <td data-label="Fasilitas">
                                    {{ $report->locationAccessibilityFeature->accessibilityFeature->name }}
                                </td>
                                <td data-label="Petugas">
                                    {{ $report->officer?->name ?? 'Belum ada' }}
                                </td>
                                <td data-label="Prioritas">
                                    @if($report->priority)
                                        <span class="badge badge-priority-{{ $report->priority }}">
                                            {{ $priorityLabels[$report->priority] ?? $report->priority }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td data-label="Status">
                                    <span class="badge badge-{{ $report->status }}">
                                        {{ $statusLabels[$report->status] ?? $report->status }}
                                    </span>
                                </td>
                                <td data-label="Waktu Dibuat">
                                    {{ $report->created_at->format('d M Y, H:i') }}
                                </td>
                                <td data-label="Aksi">
                                    <div class="table-actions">
                                        <a href="{{ route('admin.reports.show', $report) }}" class="button button-secondary button-sm">
                                            Detail
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
