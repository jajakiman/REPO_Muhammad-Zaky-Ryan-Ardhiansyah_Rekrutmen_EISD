@extends('layouts.dashboard')
@section('title', 'Tambah Area Kampus | AksesLoka')
@section('content')
<section class="form-section"><div class="container form-layout">
    <div class="form-intro"><p class="eyebrow">{{ $campus->name }}</p><h1>Tambah area kampus</h1><p>Area baru hanya dapat ditambahkan pada kampus aktif.</p></div>
    <form class="form-card" method="post" action="{{ route('admin.campuses.areas.store', $campus) }}" novalidate>@include('admin.areas._form')</form>
</div></section>
@endsection
