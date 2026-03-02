<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemMovement;
use App\Models\Room;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ItemImport implements
    ToCollection,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    WithBatchInserts,
    WithChunkReading
{
    use SkipsFailures;

    public $insertedCount = 0;
    public $updatedCount  = 0;
    public $errors        = [];

    protected $roomCache = [];
    protected $unitCache = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            try {
                DB::beginTransaction();

                $roomId = $this->resolveRoom(isset($row['kode_ruangan']) ? $row['kode_ruangan'] : null);
                $unitId = $this->resolveUnit(isset($row['nama_unit']) ? $row['nama_unit'] : null);

                $data = [
                    'name'           => $row['nama_barang'],
                    'brand'          => isset($row['merek']) ? $row['merek'] : null,
                    'category'       => isset($row['kategori']) ? $row['kategori'] : null,
                    'description'    => isset($row['deskripsi']) ? $row['deskripsi'] : null,
                    'condition'      => $this->mapCondition(isset($row['kondisi']) ? $row['kondisi'] : null),
                    'status'         => $this->mapStatus(isset($row['status']) ? $row['status'] : null),
                    'purchase_date'  => $this->parseDate(isset($row['tanggal_beli']) ? $row['tanggal_beli'] : null),
                    'purchase_price' => isset($row['harga_beli']) ? $row['harga_beli'] : null,
                    'room_id'        => $roomId,
                    'unit_id'        => $unitId,
                ];

                $serialNumber = trim($row['serial_number']);
                $existing     = Item::withTrashed()->where('serial_number', $serialNumber)->first();

                if ($existing) {
                    // ── UPDATE ────────────────────────────────────────
                    $oldRoomId = $existing->room_id;
                    $oldUnitId = $existing->unit_id;

                    $existing->restore();
                    $existing->update($data);

                    // Catat movement kalau lokasi berubah
                    if ($oldRoomId !== $roomId || $oldUnitId !== $unitId) {
                        ItemMovement::create([
                            'item_id'      => $existing->id,
                            'from_room_id' => $oldRoomId,
                            'to_room_id'   => $roomId,
                            'from_unit_id' => $oldUnitId,
                            'to_unit_id'   => $unitId,
                            'type'         => 'pindah',
                            'moved_by'     => Auth::id(),
                            'moved_at'     => now(),
                            'note'         => 'Update via import Excel.',
                        ]);
                    }

                    $this->updatedCount++;
                } else {
                    // ── INSERT ────────────────────────────────────────
                    $itemData = array_merge($data, [
                        'serial_number' => $serialNumber,
                        'created_by'    => Auth::id(),
                    ]);
                    $item = Item::create($itemData);

                    ItemMovement::create([
                        'item_id'    => $item->id,
                        'to_room_id' => $roomId,
                        'to_unit_id' => $unitId,
                        'type'       => 'masuk',
                        'moved_by'   => Auth::id(),
                        'moved_at'   => now(),
                        'note'       => 'Barang masuk via import Excel.',
                    ]);

                    $this->insertedCount++;
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $this->errors[] = [
                    'row'     => $rowNumber,
                    'serial'  => isset($row['serial_number']) ? $row['serial_number'] : '-',
                    'message' => $e->getMessage(),
                ];
            }
        }
    }

    // ─── Validation rules ─────────────────────────────────────

    public function rules(): array
    {
        return [
            '*.nama_barang'   => 'required|string|max:255',
            '*.serial_number' => 'required|string|max:255',
            '*.kondisi'       => 'nullable|in:Baik,Rusak Ringan,Rusak Berat,baik,rusak_ringan,rusak_berat',
            '*.status'        => 'nullable|in:Aktif,Tidak Aktif,Dipinjam,Dalam Perbaikan,aktif,tidak_aktif,dipinjam,dalam_perbaikan',
            '*.harga_beli'    => 'nullable|numeric|min:0',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.nama_barang.required'   => 'Kolom Nama Barang wajib diisi.',
            '*.serial_number.required' => 'Kolom Serial Number wajib diisi.',
            '*.kondisi.in'             => 'Kondisi harus: Baik, Rusak Ringan, atau Rusak Berat.',
            '*.status.in'              => 'Status harus: Aktif, Tidak Aktif, Dipinjam, atau Dalam Perbaikan.',
            '*.harga_beli.numeric'     => 'Harga Beli harus berupa angka.',
        ];
    }

    public function batchSize(): int { return 100; }
    public function chunkSize(): int { return 100; }

    // ─── Helpers ──────────────────────────────────────────────

    protected function resolveRoom($code)
    {
        if (!$code) return null;
        $code = trim($code);

        // Ganti ??= dengan isset — kompatibel PHP 7
        if (!isset($this->roomCache[$code])) {
            $this->roomCache[$code] = Room::where('code', $code)->value('id');
        }

        return $this->roomCache[$code];
    }

    protected function resolveUnit($name)
    {
        if (!$name) return null;
        $name = trim($name);

        if (!isset($this->unitCache[$name])) {
            $this->unitCache[$name] = Unit::where('name', $name)->value('id');
        }

        return $this->unitCache[$name];
    }

    protected function mapCondition($value)
    {
        $value = strtolower(trim($value ?? ''));

        // Ganti match() dengan switch — kompatibel PHP 7
        switch ($value) {
            case 'rusak ringan':
            case 'rusak_ringan':
                return 'rusak_ringan';
            case 'rusak berat':
            case 'rusak_berat':
                return 'rusak_berat';
            default:
                return 'baik';
        }
    }

    protected function mapStatus($value)
    {
        $value = strtolower(trim($value ?? ''));

        switch ($value) {
            case 'tidak aktif':
            case 'tidak_aktif':
                return 'tidak_aktif';
            case 'dipinjam':
                return 'dipinjam';
            case 'dalam perbaikan':
            case 'dalam_perbaikan':
                return 'dalam_perbaikan';
            default:
                return 'aktif';
        }
    }

    protected function parseDate($value)
    {
        if (!$value) return null;

        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                ->format('Y-m-d');
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
