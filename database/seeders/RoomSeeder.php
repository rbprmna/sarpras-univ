<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            // ── Lantai 1 ─────────────────────────────────────────────
            ['code' => 'GSG-001', 'name' => 'Gedung Serba Guna',          'description' => 'Gedung serbaguna untuk acara besar kampus'],
            ['code' => 'R-102',   'name' => 'R.102',                       'description' => 'Ruang kelas teori lantai 1'],
            ['code' => 'R-103',   'name' => 'R.103',                       'description' => 'Ruang kelas teori lantai 1'],
            ['code' => 'R-104',   'name' => 'R.104',                       'description' => 'Ruang kelas teori lantai 1'],
            ['code' => 'R-105',   'name' => 'R.105',                       'description' => 'Ruang kelas teori lantai 1'],
            ['code' => 'R-106',   'name' => 'R.106',                       'description' => 'Ruang kelas teori lantai 1'],
            ['code' => 'R-109',   'name' => 'R.109',                       'description' => 'Ruang kelas teori lantai 1'],
            ['code' => 'R-110',   'name' => 'R.110',                       'description' => 'Ruang kelas teori lantai 1'],
            ['code' => 'R-111',   'name' => 'R.111',                       'description' => 'Ruang kelas teori lantai 1'],
            ['code' => 'R-112',   'name' => 'R.112',                       'description' => 'Ruang kelas teori lantai 1'],
            ['code' => 'R-113',   'name' => 'R.113',                       'description' => 'Ruang kelas teori lantai 1'],

            // ── Lantai 2 ─────────────────────────────────────────────
            ['code' => 'R-201',   'name' => 'R.201',                       'description' => 'Ruang kelas teori lantai 2'],
            ['code' => 'R-202',   'name' => 'R.202',                       'description' => 'Ruang kelas teori lantai 2'],
            ['code' => 'R-203',   'name' => 'R.203',                       'description' => 'Ruang kelas teori lantai 2'],
            ['code' => 'R-204',   'name' => 'R.204',                       'description' => 'Ruang kelas teori lantai 2'],
            ['code' => 'R-205',   'name' => 'R.205',                       'description' => 'Ruang kelas teori lantai 2'],
            ['code' => 'R-206',   'name' => 'R.206',                       'description' => 'Ruang kelas teori lantai 2'],
            ['code' => 'R-208',   'name' => 'R.208',                       'description' => 'Ruang kelas teori lantai 2'],
            ['code' => 'R-209',   'name' => 'R.209',                       'description' => 'Ruang kelas teori lantai 2'],

            // ── Lantai 3 (Lab) ────────────────────────────────────────
            ['code' => 'R-301',   'name' => 'Lab. Multimedia 1 (R.301)',   'description' => 'Laboratorium multimedia lantai 3'],
            ['code' => 'R-302',   'name' => 'Lab. Pemrograman (R.302)',    'description' => 'Laboratorium pemrograman lantai 3'],
            ['code' => 'R-303',   'name' => 'Lab. Basis Data (R.303)',     'description' => 'Laboratorium basis data lantai 3'],
            ['code' => 'R-304',   'name' => 'Lab. Aplikasi (R.304)',       'description' => 'Laboratorium aplikasi lantai 3'],
            ['code' => 'R-305',   'name' => 'Lab. Multimedia 2 (R.305)',   'description' => 'Laboratorium multimedia 2 lantai 3'],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
