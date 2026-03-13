<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin BAS',
            'email' => 'bas@lpkia.ac.id',
            'password' => Hash::make('password'),
            'role_id' => 1,
            'unit_id' => null
        ]);

        User::create([
            'name' => 'Kepala Sarpras',
            'email' => 'kepala@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => 1,
            'unit_id' => null
        ]);

        User::create([
            'name' => 'Admin Robi',
            'email' => 'rop@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => 1,
            'unit_id' => null
        ]);

        User::create([
            'name' => 'Biro Administrasi Akademik',
            'email' => 'baa@lpkia.ac.id',
            'password' => Hash::make('password'),
            'role_id' => 2,
            'unit_id' => 1
        ]);

        User::create([
            'name' => 'Biro Administrasi Umum',
            'email' => 'bau@lpkia.ac.id',
            'password' => Hash::make('password'),
            'role_id' => 2,
            'unit_id' => 1
        ]);
    }
}
