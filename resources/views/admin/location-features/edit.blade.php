@extends('layouts.app')
@section('title', 'Ubah Fasilitas Lokasi | AksesLoka')
@section('content')<section class="form-section"><div class="container form-layout"><div class="form-intro"><p class="eyebrow">{{ $location->name }}</p><h1>Ubah fasilitas lokasi</h1><p>Perbarui ketersediaan, kondisi, catatan, dan waktu pemeriksaan.</p></div><form class="form-card" method="post" action="{{ route('admin.locations.features.update',[$location,$locationFeature]) }}" novalidate>@include('admin.location-features._form')</form></div></section>@endsection
