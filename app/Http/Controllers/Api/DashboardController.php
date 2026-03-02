<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemMovement;
use App\Models\ProcurementRequest;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        // ── 1. Stat Cards ──────────────────────────────────────────
        $totalBarang = Item::count();

        $pengajuanBulanIni = ProcurementRequest::whereMonth('request_date', now()->month)
            ->whereYear('request_date', now()->year)
            ->count();

        $pengajuanBulanLalu = ProcurementRequest::whereMonth('request_date', now()->subMonth()->month)
            ->whereYear('request_date', now()->subMonth()->year)
            ->count();

        $menungguPersetujuan = ProcurementRequest::whereHas('status', function ($q) {
            $q->where('name', 'pending');
        })->count();

        $totalNilaiPengadaan = ProcurementRequest::sum('total_amount');

        $nilaiPengadaanBulanIni = ProcurementRequest::whereMonth('request_date', now()->month)
            ->whereYear('request_date', now()->year)
            ->sum('total_amount');

        $barangBulanIni = Item::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ── 2. Kondisi Barang ──────────────────────────────────────
        $kondisiRaw = Item::selectRaw('`condition`, COUNT(*) as total')
            ->groupBy('condition')
            ->pluck('total', 'condition')
            ->toArray();

        $kondisiMap = [
            'baik'         => 'Baik',
            'cukup_baik'   => 'Cukup Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat'  => 'Rusak Berat',
        ];

        $kondisiBarang = collect($kondisiMap)->map(function ($label, $key) use ($kondisiRaw, $totalBarang) {
            $count = $kondisiRaw[$key] ?? 0;
            return [
                'key'     => $key,
                'label'   => $label,
                'count'   => $count,
                'percent' => $totalBarang > 0 ? round(($count / $totalBarang) * 100) : 0,
            ];
        })->values();

        // ── 3. Status Pengajuan ────────────────────────────────────
        $statusPengajuan = ProcurementRequest::with('status')
            ->selectRaw('status_id, COUNT(*) as total')
            ->groupBy('status_id')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => optional($item->status)->name ?? 'unknown',
                    'total'  => $item->total,
                ];
            });

        // ── 4. Pengajuan Terbaru ───────────────────────────────────
        $pengajuanTerbaru = ProcurementRequest::with(['unit', 'status', 'items'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($p) {
                return [
                    'id'             => $p->id,
                    'request_number' => $p->request_number,
                    'unit'           => optional($p->unit)->name,
                    'total_items'    => $p->items->count(),
                    'total_amount'   => $p->total_amount,
                    'status'         => optional($p->status)->name,
                    'request_date'   => $p->request_date ? $p->request_date->format('d M Y') : null,
                ];
            });

        // ── 5. Pergerakan Barang Terbaru ───────────────────────────
        $pergerakanTerbaru = ItemMovement::with(['item', 'fromRoom', 'toRoom', 'movedBy'])
            ->latest('moved_at')
            ->take(4)
            ->get()
            ->map(function ($m) {
                return [
                    'id'        => $m->id,
                    'item_name' => optional($m->item)->name,
                    'from_room' => optional($m->fromRoom)->name,
                    'to_room'   => optional($m->toRoom)->name,
                    'moved_by'  => optional($m->movedBy)->name,
                    'moved_at'  => $m->moved_at ? $m->moved_at->diffForHumans() : null,
                ];
            });

        // ── Response ───────────────────────────────────────────────
        return response()->json([
            'stat_cards' => [
                'total_barang' => [
                    'value'    => $totalBarang,
                    'tambahan' => $barangBulanIni,
                ],
                'pengajuan_masuk' => [
                    'value'      => $pengajuanBulanIni,
                    'bulan_lalu' => $pengajuanBulanLalu,
                ],
                'menunggu_persetujuan' => [
                    'value' => $menungguPersetujuan,
                ],
                'total_nilai_pengadaan' => [
                    'value'     => $totalNilaiPengadaan,
                    'bulan_ini' => $nilaiPengadaanBulanIni,
                ],
            ],
            'kondisi_barang'     => $kondisiBarang,
            'status_pengajuan'   => $statusPengajuan,
            'pengajuan_terbaru'  => $pengajuanTerbaru,
            'pergerakan_terbaru' => $pergerakanTerbaru,
        ]);
    }
}
