@extends('layouts.dashboard')
@section('title', 'Tambah Kategori Masalah | AksesLoka')
@section('content')<section class="form-section"><div class="container form-layout"><div class="form-intro"><p class="eyebrow">Master data</p><h1>Tambah kategori masalah</h1><p>Kategori aktif tersedia pada laporan baru.</p></div><form class="form-card" method="post" action="{{ route('admin.issue-categories.store') }}" novalidate>@include('admin.issue-categories._form')</form></div></section>@endsection
