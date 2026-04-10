<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UnitSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            StatusSeeder::class,
            RoomSeeder::class,
        ]);
    }
}
