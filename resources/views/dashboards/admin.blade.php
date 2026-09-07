@extends('layouts.app')

@section('title', 'Area Admin | AksesLoka')

@section('content')
<section class="form-section"><div class="container narrow">
    <p class="eyebrow">Area admin</p>
    <h1>Dashboard pusat</h1>
    <p>Kelola data referensi yang digunakan oleh peta dan pelaporan aksesibilitas.</p>
    <div class="actions"><a class="button button-primary" href="{{ route('admin.campuses.index') }}">Kelola kampus</a></div>
</div></section>
@endsection
