@extends('layouts.app')

@section('title', 'Area Admin | AksesLoka')

@section('content')
<section class="form-section"><div class="container narrow">
    <p class="eyebrow">Area admin</p>
    <h1>Dashboard pusat</h1>
    <p>Kelola data referensi yang digunakan oleh peta dan pelaporan aksesibilitas.</p>
    <div class="actions"><a class="button button-primary" href="{{ route('admin.campuses.index') }}">Kelola kampus</a><a class="button button-secondary" href="{{ route('admin.features.index') }}">Kelola fasilitas</a><a class="button button-secondary" href="{{ route('admin.issue-categories.index') }}">Kelola kategori masalah</a><a class="button button-secondary" href="{{ route('admin.officers.index') }}">Kelola petugas</a></div>
</div></section>
@endsection
