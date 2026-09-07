@extends('layouts.dashboard')
@section('title', 'Pasang Fasilitas Lokasi | AksesLoka')
@section('content')<section class="form-section"><div class="container form-layout"><div class="form-intro"><p class="eyebrow">{{ $location->name }}</p><h1>Pasang fasilitas</h1><p>Hanya fasilitas aktif yang belum terpasang dapat dipilih.</p></div><form class="form-card" method="post" action="{{ route('admin.locations.features.store',$location) }}" novalidate>@include('admin.location-features._form')</form></div></section>@endsection
