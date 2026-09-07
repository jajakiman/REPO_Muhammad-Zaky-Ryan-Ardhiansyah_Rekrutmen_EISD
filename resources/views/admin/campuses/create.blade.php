@extends('layouts.dashboard')
@section('title', 'Tambah Kampus | AksesLoka')
@section('content')
<section class="form-section"><div class="container form-layout">
    <div class="form-intro"><p class="eyebrow">Master kampus</p><h1>Tambah kampus</h1><p>Koordinat peta ditentukan saat menambahkan lokasi atau gedung.</p></div>
    <form class="form-card" method="post" action="{{ route('admin.campuses.store') }}" novalidate>@include('admin.campuses._form')</form>
</div></section>
@endsection
