<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        Status::insert([
            ['name' => 'pending',   'created_at' => now(), 'updated_at' => now()],
            ['name' => 'disetujui', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ditolak',   'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
