@extends('layouts.app')
@section('title', 'Ubah Fasilitas | AksesLoka')
@section('content')<section class="form-section"><div class="container form-layout"><div class="form-intro"><p class="eyebrow">Master data</p><h1>Ubah fasilitas</h1><p>Fasilitas nonaktif tetap tersedia dalam riwayat.</p></div><form class="form-card" method="post" action="{{ route('admin.features.update', $feature) }}" novalidate>@include('admin.features._form')</form></div></section>@endsection
