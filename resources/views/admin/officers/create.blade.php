@extends('layouts.app')

@section('title', 'Tambah Petugas | AksesLoka')

@section('content')
<section class="form-section">
    <div class="container form-layout">
        <div class="form-intro">
            <p class="eyebrow">Manajemen pengguna</p>
            <h1>Tambah petugas</h1>
            <p class="lead">Daftarkan akun petugas baru dan tentukan area kampus yang menjadi tanggung jawabnya.</p>
        </div>
        <div class="form-card">
            <form method="post" action="{{ route('admin.officers.store') }}">
                @include('admin.officers._form')
            </form>
        </div>
    </div>
</section>
@endsection
