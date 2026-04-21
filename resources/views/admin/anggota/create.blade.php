@extends('layouts.app')
@section('page-title', 'Tambah Anggota')

@section('content')

<div class="card p-4">
    <h5 class="mb-3">Tambah Anggota</h5>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <strong>Gagal!</strong> Ada kesalahan:
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

    <form action="{{ route('anggota.store') }}" method="POST">
        @csrf

        <div class="mb-2">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-2">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-2">
            <label>No HP</label>
            <input type="number" name="no_hp" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" required></textarea>
        </div>

        <div class="alert alert-info">
            Password default: <b>12345678</b>
        </div>

        <button class="btn btn-primary">Simpan</button>
    </form>
</div>

@endsection