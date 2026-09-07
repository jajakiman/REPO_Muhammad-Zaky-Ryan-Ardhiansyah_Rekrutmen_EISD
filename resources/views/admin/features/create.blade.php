@extends('layouts.app')
@section('title', 'Tambah Fasilitas | AksesLoka')
@section('content')<section class="form-section"><div class="container form-layout"><div class="form-intro"><p class="eyebrow">Master data</p><h1>Tambah fasilitas</h1><p>Fasilitas aktif dapat dipasang pada lokasi kampus.</p></div><form class="form-card" method="post" action="{{ route('admin.features.store') }}" novalidate>@include('admin.features._form')</form></div></section>@endsection
