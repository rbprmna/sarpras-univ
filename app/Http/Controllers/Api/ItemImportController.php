<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\ItemTemplateExport;
use App\Imports\ItemImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ItemImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new ItemImport();
        Excel::import($import, $request->file('file'));

        $validationErrors = collect($import->failures())
            ->map(fn($f) => [
                'row'     => $f->row(),
                'serial'  => '-',
                'message' => implode(', ', $f->errors()),
            ])->toArray();

        $allErrors = array_merge($validationErrors, $import->errors);

        return response()->json([
            'success' => true,
            'message' => "Import selesai. {$import->insertedCount} barang ditambahkan, "
                       . "{$import->updatedCount} barang diperbarui."
                       . (count($allErrors) > 0 ? ' ' . count($allErrors) . ' baris gagal.' : ''),
            'data' => [
                'inserted' => $import->insertedCount,
                'updated'  => $import->updatedCount,
                'failed'   => count($allErrors),
                'errors'   => $allErrors,
            ],
        ]);
    }

    public function template()
    {
        return Excel::download(new ItemTemplateExport(), 'template_import_barang.xlsx');
    }
}
