<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            // ── Ruang Kelas ──────────────────────────────────────────
            ['code' => 'RKL-101', 'name' => 'Ruang Kelas 101', 'description' => 'Ruang kelas teori lantai 1'],
            ['code' => 'RKL-102', 'name' => 'Ruang Kelas 102', 'description' => 'Ruang kelas teori lantai 1'],
            ['code' => 'RKL-103', 'name' => 'Ruang Kelas 103', 'description' => 'Ruang kelas teori lantai 1'],
            ['code' => 'RKL-104', 'name' => 'Ruang Kelas 104', 'description' => 'Ruang kelas teori lantai 1'],
            ['code' => 'RKL-201', 'name' => 'Ruang Kelas 201', 'description' => 'Ruang kelas teori lantai 2'],
            ['code' => 'RKL-202', 'name' => 'Ruang Kelas 202', 'description' => 'Ruang kelas teori lantai 2'],
            ['code' => 'RKL-203', 'name' => 'Ruang Kelas 203', 'description' => 'Ruang kelas teori lantai 2'],
            ['code' => 'RKL-204', 'name' => 'Ruang Kelas 204', 'description' => 'Ruang kelas teori lantai 2'],
            ['code' => 'RKL-301', 'name' => 'Ruang Kelas 301', 'description' => 'Ruang kelas teori lantai 3'],
            ['code' => 'RKL-302', 'name' => 'Ruang Kelas 302', 'description' => 'Ruang kelas teori lantai 3'],
            ['code' => 'RKL-303', 'name' => 'Ruang Kelas 303', 'description' => 'Ruang kelas teori lantai 3'],

            // ── Lab Komputer ─────────────────────────────────────────
            ['code' => 'LAB-K01', 'name' => 'Lab Komputer 1', 'description' => 'Laboratorium komputer untuk praktikum'],
            ['code' => 'LAB-K02', 'name' => 'Lab Komputer 2', 'description' => 'Laboratorium komputer untuk praktikum'],
            ['code' => 'LAB-K03', 'name' => 'Lab Komputer 3', 'description' => 'Laboratorium komputer untuk praktikum'],
            ['code' => 'LAB-K04', 'name' => 'Lab Komputer 4', 'description' => 'Laboratorium komputer untuk praktikum'],
            ['code' => 'LAB-K05', 'name' => 'Lab Komputer 5', 'description' => 'Laboratorium komputer untuk praktikum'],
            ['code' => 'LAB-K06', 'name' => 'Lab Komputer 6', 'description' => 'Laboratorium komputer untuk praktikum'],
            ['code' => 'LAB-K07', 'name' => 'Lab Komputer 7', 'description' => 'Laboratorium komputer untuk praktikum'],
            ['code' => 'LAB-K08', 'name' => 'Lab Komputer 8', 'description' => 'Laboratorium komputer untuk praktikum'],
            ['code' => 'LAB-K09', 'name' => 'Lab Komputer 9', 'description' => 'Laboratorium komputer untuk praktikum'],

            // ── Lab Jaringan ─────────────────────────────────────────
            ['code' => 'LAB-J01', 'name' => 'Lab Jaringan 1', 'description' => 'Lab jaringan komputer dan Mikrotik'],
            ['code' => 'LAB-J02', 'name' => 'Lab Jaringan 2', 'description' => 'Lab jaringan komputer dan Mikrotik'],
            ['code' => 'LAB-J03', 'name' => 'Lab Jaringan 3', 'description' => 'Lab jaringan komputer dan Mikrotik'],

            // ── Biro Administrasi ────────────────────────────────────
            ['code' => 'BAS-001', 'name' => 'Ruang BAS',  'description' => 'Biro Administrasi Sumber Daya'],
            ['code' => 'BAU-001', 'name' => 'Ruang BAU',  'description' => 'Biro Administrasi Umum'],
            ['code' => 'BAA-001', 'name' => 'Ruang BAA',  'description' => 'Biro Administrasi Akademik'],
            ['code' => 'BAK-001', 'name' => 'Ruang BAK',  'description' => 'Biro Administrasi Keuangan'],
            ['code' => 'BAM-001', 'name' => 'Ruang BAM',  'description' => 'Biro Administrasi Mahasiswa'],

            // ── Ruang Pimpinan & Staf ────────────────────────────────
            ['code' => 'PIM-001', 'name' => 'Ruang Direktur',        'description' => 'Ruang direktur kampus'],
            ['code' => 'PIM-002', 'name' => 'Ruang Wakil Direktur',  'description' => 'Ruang wakil direktur kampus'],
            ['code' => 'PIM-003', 'name' => 'Ruang Kepala Program',  'description' => 'Ruang kepala program studi'],
            ['code' => 'PIM-004', 'name' => 'Ruang Dosen',           'description' => 'Ruang kerja dosen'],
            ['code' => 'PIM-005', 'name' => 'Ruang Rapat',           'description' => 'Ruang rapat pimpinan dan dosen'],

            // ── Fasilitas Umum ───────────────────────────────────────
            ['code' => 'FAP-001', 'name' => 'GSG (Gedung Serba Guna)',         'description' => 'Gedung serbaguna untuk acara besar kampus'],
            ['code' => 'FAP-002', 'name' => 'Ruang Seminar',                   'description' => 'Ruang seminar dan presentasi'],
            ['code' => 'FAP-003', 'name' => 'Perpustakaan',                    'description' => 'Ruang baca dan koleksi buku'],
            ['code' => 'FAP-004', 'name' => 'Ruang Career Development Center', 'description' => 'Pusat pengembangan karir mahasiswa'],
            ['code' => 'FAP-005', 'name' => 'Gudang',                          'description' => 'Gudang penyimpanan barang kampus'],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
