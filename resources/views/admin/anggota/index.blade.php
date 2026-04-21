@extends('layouts.app')
@section('page-title', 'Kelola Anggota')

@section('content')

<div class="page-header">
    <h1 class="page-title">Kelola Anggota</h1>
    <a href="{{ route('anggota.create') }}" 
           class="btn btn-primary" 
           style="border-radius:10px; font-size:13px;">
            <i class="bi bi-person-plus me-1"></i> Anggota
        </a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card mt-3">
    <div class="card-body">

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Foto Profil</th>
                    <th>Nomor Anggota</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No HP</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                    @if($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" 
                            width="50" 
                            height="50" 
                            style="object-fit:cover; border-radius:50%;">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ $user->name }}" 
                            width="50" 
                            height="50" 
                            style="border-radius:50%;">
                            @endif
                    </td>
                    <td>{{ $user->nomor_anggota }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->no_hp }}</td>
                    <td>{{ $user->alamat }}</td>
                    <td>

                        <a href="{{ route('anggota.edit', $user->id) }}" 
                            class="btn btn-warning btn-sm">
                                Edit
                        </a>
                        <form action="{{ route('anggota.destroy', $user->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada anggota</td>
                </tr>
                @endforelse
            </tbody>

        </table>

    </div>
</div>

@endsection