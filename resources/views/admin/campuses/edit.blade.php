@extends('layouts.dashboard')
@section('title', 'Ubah Kampus | AksesLoka')
@section('content')
<section class="form-section"><div class="container form-layout">
    <div class="form-intro"><p class="eyebrow">Master kampus</p><h1>Ubah kampus</h1><p>Perbarui data dan status kampus tanpa menghapus riwayatnya.</p></div>
    <form class="form-card" method="post" action="{{ route('admin.campuses.update', $campus) }}" novalidate>@include('admin.campuses._form')</form>
</div></section>
@endsection
