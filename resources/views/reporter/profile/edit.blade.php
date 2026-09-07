@extends('layouts.app')

@section('title', 'Profil Pelapor | AksesLoka')

@section('content')
<section class="form-section">
    <div class="container form-layout">
        <div class="form-intro">
            <p class="eyebrow">Profil pelapor</p>
            <h1>Atur afiliasi kampus</h1>
            <p>Kampus profil akan menjadi acuan peta default saat fitur peta tersedia.</p>
        </div>
        <form class="form-card" method="post" action="{{ route('reporter.profile.update') }}" novalidate>
            @csrf
            @method('put')
            <div class="field">
                <label for="name">Nama lengkap</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" autocomplete="name" required aria-describedby="name-error">
                @error('name')<p class="field-error" id="name-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" value="{{ $user->email }}" readonly aria-describedby="email-help">
                <p class="field-help" id="email-help">Email tidak dapat diubah dari halaman ini.</p>
            </div>
            <div class="field">
                <label for="affiliation_type">Afiliasi</label>
                <x-select-shell>
                <select id="affiliation_type" name="affiliation_type" required aria-describedby="affiliation_type-error" class="hover:border-navy-900 focus:border-navy-900 focus:ring-4 focus:ring-orange-500/20 focus:outline-none">
                    <option value="student" @selected(old('affiliation_type', $user->affiliation_type) === 'student')>Mahasiswa</option>
                    <option value="lecturer" @selected(old('affiliation_type', $user->affiliation_type) === 'lecturer')>Dosen</option>
                    <option value="staff" @selected(old('affiliation_type', $user->affiliation_type) === 'staff')>Staf</option>
                    <option value="visitor" @selected(old('affiliation_type', $user->affiliation_type) === 'visitor')>Pengunjung</option>
                </select>
                </x-select-shell>
                @error('affiliation_type')<p class="field-error" id="affiliation_type-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label for="campus_id">Kampus</label>
                <x-select-shell>
                <select id="campus_id" name="campus_id" aria-describedby="campus-help campus_id-error" class="hover:border-navy-900 focus:border-navy-900 focus:ring-4 focus:ring-orange-500/20 focus:outline-none">
                    <option value="">Tidak memilih kampus</option>
                    @foreach ($campuses as $campus)
                        <option value="{{ $campus->id }}" @selected((string) old('campus_id', $user->campus_id) === (string) $campus->id)>{{ $campus->name }}</option>
                    @endforeach
                </select>
                </x-select-shell>
                <p class="field-help" id="campus-help">Wajib untuk mahasiswa, dosen, dan staf. Opsional untuk pengunjung.</p>
                @error('campus_id')<p class="field-error" id="campus_id-error">{{ $message }}</p>@enderror
            </div>
            <button class="button button-primary" type="submit">Simpan profil</button>
        </form>
    </div>
</section>
@endsection
