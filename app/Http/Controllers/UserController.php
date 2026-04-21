<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // ✅ LIST ANGGOTA
    public function index()
    {
        $users = User::where('role', 'anggota')->latest()->get();
        return view('admin.anggota.index', compact('users'));
    }

    // ✅ FORM TAMBAH
    public function create()
    {
        return view('admin.anggota.create');
    }

    // ✅ SIMPAN
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'no_hp' => 'required|numeric|digits_between:10,15',
            'alamat' => 'required',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

         $photoPath = null;
         if ($request->hasFile('photo')) {
        $photoPath = $request->file('photo')->store('photos', 'public');
    }


        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('12345678'),
            'role' => 'anggota',
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'photo' => $photoPath
        ]);

        return redirect()->route('anggota.index')
            ->with('success', 'Anggota berhasil ditambahkan!');
    }

    // ✅ HAPUS
    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return back()->with('success', 'Anggota berhasil dihapus');
    }
}