<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        Unit::create(['name' => 'Biro Administrasi Akademik']);
        Unit::create(['name' => 'Biro Administrasi Umum']);
        Unit::create(['name' => 'Biro Administrasi Sumber Daya']);
    }
}
