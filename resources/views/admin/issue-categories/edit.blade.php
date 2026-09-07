@extends('layouts.app')
@section('title', 'Ubah Kategori Masalah | AksesLoka')
@section('content')<section class="form-section"><div class="container form-layout"><div class="form-intro"><p class="eyebrow">Master data</p><h1>Ubah kategori masalah</h1><p>Kategori nonaktif tetap tersimpan pada laporan lama.</p></div><form class="form-card" method="post" action="{{ route('admin.issue-categories.update', $issueCategory) }}" novalidate>@include('admin.issue-categories._form')</form></div></section>@endsection
