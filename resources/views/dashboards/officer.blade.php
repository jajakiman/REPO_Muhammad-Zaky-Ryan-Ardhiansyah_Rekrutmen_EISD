@extends('layouts.app')

@section('title', 'Area Petugas | AksesLoka')

@section('content')
<section class="form-section"><div class="container narrow">
    <p class="eyebrow">Area petugas</p>
    <h1>Dashboard Area Tugas</h1>
    <p>Kelola verifikasi, penugasan prioritas, dan penanganan laporan kendala fasilitas aksesibilitas pada area tanggung jawab Anda.</p>
    <div class="actions" style="margin-top: 1.5rem;">
        <a class="button button-primary" href="{{ route('officer.queue.index') }}">Buka Antrean Laporan Area</a>
        <a class="button button-secondary" href="{{ route('officer.history.index') }}">Riwayat Penanganan Area</a>
    </div>
</div></section>
@endsection
