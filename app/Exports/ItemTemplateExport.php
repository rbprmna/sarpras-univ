<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ItemTemplateExport implements
    FromArray,
    WithHeadings,
    WithStyles,
    WithColumnWidths
{
    public function headings(): array
    {
        return [
            'nama_barang',    // A - required
            'serial_number',  // B - required (format: angka murni, e.g. 000001)
            'merek',          // C
            'kategori',       // D
            'deskripsi',      // E
            'kondisi',        // F - Baik / Rusak Ringan / Rusak Berat
            'status',         // G - Aktif / Tidak Aktif / Dipinjam / Dalam Perbaikan
            'tanggal_beli',   // H - YYYY-MM-DD
            'harga_beli',     // I - angka
            'kode_ruangan',   // J - kode ruangan (lihat halaman Ruangan)
            'nama_unit',      // K - nama unit
        ];
    }

    public function array(): array
    {
        return [
            // Baris contoh 1
            [
                'Laptop Dell Latitude 5420',
                '000001',        // ← serial number: angka murni 6 digit
                'Dell',
                'Elektronik',
                'Laptop kantor untuk operasional harian',
                'Baik',
                'Aktif',
                '2024-01-15',
                '12500000',
                'RKL-01',
                'Biro Akademik',
            ],
            // Baris contoh 2
            [
                'Printer Canon LBP6030',
                '000002',        // ← serial number: angka murni 6 digit
                'Canon',
                'Elektronik',
                'Printer laser hitam putih',
                'Rusak Ringan',
                'Dalam Perbaikan',
                '2023-06-20',
                '2800000',
                'RKL-02',
                'Biro Keuangan',
            ],
            // Baris contoh 3 (kosong sebagian - opsional fields)
            [
                'Kursi Kerja Ergonomis',
                '000003',
                'Ergohuman',
                'Furnitur',
                '',              // deskripsi opsional
                'Baik',
                'Aktif',
                '',              // tanggal beli opsional
                '',              // harga beli opsional
                '',              // kode ruangan opsional
                '',              // nama unit opsional
            ],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // ── Header row styling ──────────────────────────────
        $headerStyle = [
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 10,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFD0D0D0'],
                ],
            ],
        ];

        // Kolom required (A, B) → warna merah gelap
        $sheet->getStyle('A1:B1')->applyFromArray(array_merge($headerStyle, [
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFC0392B'],
            ],
        ]));

        // Kolom opsional (C-K) → warna biru
        $sheet->getStyle('C1:K1')->applyFromArray(array_merge($headerStyle, [
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF2980B9'],
            ],
        ]));

        // ── Data rows styling ───────────────────────────────
        $dataStyle = [
            'font'      => ['size' => 10],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFE0E0E0'],
                ],
            ],
        ];
        $sheet->getStyle('A2:K4')->applyFromArray($dataStyle);

        // Serial number column (B) → font monospace style + center
        $sheet->getStyle('B2:B4')->applyFromArray([
            'font'      => ['name' => 'Courier New', 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Beri warna alternating rows
        $sheet->getStyle('A2:K2')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF9F9F9');
        $sheet->getStyle('A4:K4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF9F9F9');

        // Row height
        $sheet->getRowDimension(1)->setRowHeight(24);
        foreach ([2, 3, 4] as $row) {
            $sheet->getRowDimension($row)->setRowHeight(20);
        }

        // ── Catatan di bawah tabel ──────────────────────────
        $sheet->setCellValue('A6', '📌 Petunjuk Pengisian:');
        $sheet->getStyle('A6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FF333333']],
        ]);

        $notes = [
            7  => '* Kolom merah (nama_barang, serial_number) WAJIB diisi.',
            8  => '* serial_number: gunakan format angka murni, contoh: 000001, 000002, 000003',
            9  => '* kondisi: isi dengan "Baik", "Rusak Ringan", atau "Rusak Berat"',
            10 => '* status: isi dengan "Aktif", "Tidak Aktif", "Dipinjam", atau "Dalam Perbaikan"',
            11 => '* tanggal_beli: format YYYY-MM-DD (contoh: 2024-01-15)',
            12 => '* kode_ruangan: harus sesuai kode ruangan yang ada di sistem',
            13 => '* nama_unit: harus sesuai nama unit yang ada di sistem',
            14 => '* Kolom biru lainnya bersifat opsional',
        ];

        foreach ($notes as $row => $text) {
            $sheet->setCellValue("A{$row}", $text);
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['size' => 9, 'color' => ['argb' => 'FF666666']],
            ]);
            $sheet->mergeCells("A{$row}:K{$row}");
        }

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35, // nama_barang
            'B' => 18, // serial_number
            'C' => 18, // merek
            'D' => 18, // kategori
            'E' => 35, // deskripsi
            'F' => 18, // kondisi
            'G' => 22, // status
            'H' => 16, // tanggal_beli
            'I' => 16, // harga_beli
            'J' => 16, // kode_ruangan
            'K' => 22, // nama_unit
        ];
    }
}
