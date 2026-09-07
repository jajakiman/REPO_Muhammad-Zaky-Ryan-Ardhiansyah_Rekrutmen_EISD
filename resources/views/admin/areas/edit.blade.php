@extends('layouts.dashboard')
@section('title', 'Ubah Area Kampus | AksesLoka')
@section('content')
<section class="form-section"><div class="container form-layout">
    <div class="form-intro"><p class="eyebrow">{{ $campus->name }}</p><h1>Ubah area kampus</h1><p>Perbarui area tanpa menghapus riwayat lokasi.</p></div>
    <form class="form-card" method="post" action="{{ route('admin.campuses.areas.update', [$campus, $area]) }}" novalidate>@include('admin.areas._form')</form>
</div></section>
@endsection
