@extends('layouts.app')

@section('title', 'Area Pelapor | AksesLoka')

@section('content')
<section class="form-section"><div class="container narrow">
    <p class="eyebrow">Area pelapor</p>
    <h1>Area Pelapor</h1>
    <p>Lihat peta fasilitas aksesibilitas dan pantau riwayat pelaporan masalah fasilitas Anda.</p>
    <div class="actions" style="margin-top: 1.5rem;">
        <a class="button button-primary" href="{{ route('map.index') }}">Buka Peta Kampus</a>
        <a class="button button-secondary" href="{{ route('reporter.reports.index') }}">Riwayat Laporan Saya</a>
    </div>
</div></section>
@endsection
