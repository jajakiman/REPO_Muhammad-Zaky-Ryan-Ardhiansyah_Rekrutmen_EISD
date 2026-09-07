@extends('layouts.dashboard')
@section('title', 'Tambah Lokasi Kampus | AksesLoka')
@section('content')
<section class="form-section"><div class="container form-layout"><div class="form-intro"><p class="eyebrow">{{ $campus->name }} / {{ $area->name }}</p><h1>Tambah lokasi</h1><p>Masukkan koordinat lokasi secara manual.</p></div><form class="form-card" method="post" action="{{ route('admin.campuses.areas.locations.store', [$campus, $area]) }}" novalidate>@include('admin.locations._form')</form></div></section>
@endsection
