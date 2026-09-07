@extends('layouts.app')
@section('title', 'Ubah Lokasi Kampus | AksesLoka')
@section('content')
<section class="form-section"><div class="container form-layout"><div class="form-intro"><p class="eyebrow">{{ $campus->name }} / {{ $area->name }}</p><h1>Ubah lokasi</h1><p>Perbarui detail lokasi dan koordinat manual.</p></div><form class="form-card" method="post" action="{{ route('admin.campuses.areas.locations.update', [$campus, $area, $location]) }}" novalidate>@include('admin.locations._form')</form></div></section>
@endsection
