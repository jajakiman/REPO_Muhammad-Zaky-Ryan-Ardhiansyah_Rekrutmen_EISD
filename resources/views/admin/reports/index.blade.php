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

        <!-- Filter Form -->
        <div class="filter-card">
            <form method="get" action="{{ route('admin.reports.index') }}" class="filter-grid">
                <div class="field">
                    <label for="filter-campus">Kampus</label>
                    <select id="filter-campus" name="campus_id">
                        <option value="">Semua Kampus</option>
                        @foreach($campuses as $c)
                            <option value="{{ $c->id }}" @selected(request('campus_id') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="filter-status">Status Laporan</label>
                    <select id="filter-status" name="status">
                        <option value="">Semua Status</option>
                        @foreach($statusLabels as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="filter-priority">Prioritas</label>
                    <select id="filter-priority" name="priority">
                        <option value="">Semua Prioritas</option>
                        @foreach($priorityLabels as $value => $label)
                            <option value="{{ $value }}" @selected(request('priority') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="button button-primary">Terapkan Filter</button>
                    @if(request()->hasAny(['campus_id', 'campus_area_id', 'campus_location_id', 'status', 'priority']))
                        <a href="{{ route('admin.reports.index') }}" class="button button-secondary">Reset</a>
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
