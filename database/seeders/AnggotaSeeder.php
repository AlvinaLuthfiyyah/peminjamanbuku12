<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AnggotaSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Anggota',
            'email' => 'anggota@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'anggota'
        ]);
    }
}