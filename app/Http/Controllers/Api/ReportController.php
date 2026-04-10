<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProcurementRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Rekap pengadaan — summary + chart data
     * GET /api/reports/procurement?period=monthly&year=2026&department=&status=
     */
    public function procurement(Request $request)
    {
        $period     = $request->get('period', 'monthly');
        $year       = (int) $request->get('year', now()->year);
        $department = $request->get('unit_id');   // frontend kirim unit_id, kita map ke department
        $status     = $request->get('status');

        // Base query
        $base = ProcurementRequest::query()
            ->when($department, fn($q) => $q->where('department', $department))
            ->when($status,     fn($q) => $q->where('status_id', $status));

        // Summary cards — pakai total_amount yang sudah ada di tabel
        $summary = [
            'total_pengajuan' => (clone $base)->count(),
            'total_nilai'     => (clone $base)->sum('total_amount'), // ✅ langsung dari kolom
            'disetujui'       => (clone $base)->whereHas('status', fn($q) => $q->where('name', 'Disetujui'))->count(),
            'ditolak'         => (clone $base)->whereHas('status', fn($q) => $q->where('name', 'Ditolak'))->count(),
            'pending'         => (clone $base)->whereHas('status', fn($q) => $q->where('name', 'Pending'))->count(),
        ];

        // Chart per periode
        $chartData = $this->buildChartData($period, $year, $department, $status);

        // Top 5 department pengaju
        $topUnits = ProcurementRequest::query()
            ->select('department', DB::raw('COUNT(*) as total'))
            ->when($department, fn($q) => $q->where('department', $department))
            ->when($status,     fn($q) => $q->where('status_id', $status))
            ->whereYear('created_at', $year)
            ->groupBy('department')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn($r) => [
                'unit'  => $r->department ?? '-',
                'total' => $r->total,
            ]);

        // Tabel detail
        $rows = (clone $base)
            ->with(['status', 'user', 'items'])
            ->whereYear('created_at', $year)
            ->latest()
            ->paginate($request->get('per_page', 15));

        $rows->getCollection()->transform(function ($pr) {
            $pr->total_nilai      = $pr->total_amount;
            $pr->nomor_pengajuan  = $pr->request_number;
            $pr->unit             = (object)['name' => $pr->department];
            $pr->requested_by     = $pr->user ?? (object)['name' => $pr->requester_name];
            return $pr;
        });

        return response()->json([
            'summary'    => $summary,
            'chart_data' => $chartData,
            'top_units'  => $topUnits,
            'rows'       => $rows,
        ]);
    }

    private function buildChartData(string $period, int $year, $department, $status): array
    {
        if ($period === 'monthly') {
            $labels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            $range  = range(1, 12);
        } elseif ($period === 'quarterly') {
            $labels = ['Q1','Q2','Q3','Q4'];
            $range  = range(1, 4);
        } else {
            $currentYear = now()->year;
            $range  = range($currentYear - 4, $currentYear);
            $labels = array_map('strval', $range);
        }

        $buildQ = function (string $statusName) use ($period, $year, $department, $status) {
            $q = ProcurementRequest::query()
                ->when($department, fn($q) => $q->where('department', $department))
                ->when($status,     fn($q) => $q->where('status_id', $status))
                ->when($period !== 'yearly', fn($q) => $q->whereYear('created_at', $year))
                ->whereHas('status', fn($q) => $q->where('name', $statusName));

            if ($period === 'quarterly') {
                $q->selectRaw('QUARTER(created_at) as period, COUNT(*) as total');
            } elseif ($period === 'yearly') {
                $q->selectRaw("DATE_FORMAT(created_at, '%Y') as period, COUNT(*) as total");
            } else {
                $q->selectRaw("DATE_FORMAT(created_at, '%m') as period, COUNT(*) as total");
            }

            return $q->groupBy('period')->pluck('total', 'period')->toArray();
        };

        $disetujui = $buildQ('Disetujui');
        $ditolak   = $buildQ('Ditolak');
        $pending   = $buildQ('Pending');

        $normalize = function (array $raw) use ($range) {
            return array_map(function ($k) use ($raw) {
                $key = (string) str_pad($k, 2, '0', STR_PAD_LEFT);
                return (int) ($raw[$key] ?? $raw[(string)$k] ?? 0);
            }, $range);
        };

        return [
            'labels'    => $labels,
            'disetujui' => $normalize($disetujui),
            'ditolak'   => $normalize($ditolak),
            'pending'   => $normalize($pending),
        ];
    }

    /**
     * Rekap nilai pengadaan per department
     * GET /api/reports/procurement/per-unit?year=2026
     */
    public function procurementPerUnit(Request $request)
    {
        $year = (int) $request->get('year', now()->year);

        $data = ProcurementRequest::query()
            ->select(
                'department as unit',
                DB::raw('SUM(total_amount) as total_nilai'),       // ✅ pakai total_amount
                DB::raw('COUNT(*) as jumlah')
            )
            ->whereYear('created_at', $year)
            ->groupBy('department')
            ->orderByDesc('total_nilai')
            ->get();

        return response()->json($data);
    }
}
