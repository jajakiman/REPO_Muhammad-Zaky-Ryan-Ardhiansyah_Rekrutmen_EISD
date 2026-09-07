@extends('layouts.dashboard')

@section('title', 'Ubah Petugas | AksesLoka')

@section('content')
<section class="form-section">
    <div class="container form-layout">
        <div class="form-intro">
            <p class="eyebrow">Manajemen pengguna</p>
            <h1>Ubah petugas</h1>
            <p class="lead">Perbarui informasi nama, pindahkan area tugas, atau sesuaikan status aktif akun petugas.</p>
        </div>
        <div class="form-card">
            <form method="post" action="{{ route('admin.officers.update', $officer) }}">
                @include('admin.officers._form')
            </form>
        </div>
    </div>
</section>
@endsection
