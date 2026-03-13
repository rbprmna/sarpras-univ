<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Models\ProcurementRequest;
use App\Models\ProcurementItem;
use App\Models\Item;
use App\Models\Status;
use Illuminate\Support\Facades\DB;

class ProcurementRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ProcurementRequest::with(['user', 'status', 'items'])->latest();

        if ($request->filled('status_id'))  $query->where('status_id', $request->status_id);
        if ($request->filled('user_id'))    $query->where('user_id', $request->user_id);
        if ($request->filled('search'))     $query->where('request_number', 'like', "%{$request->search}%");
        if ($request->filled('date_from'))  $query->whereDate('request_date', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('request_date', '<=', $request->date_to);

        $data = $request->boolean('paginate', true)
            ? $query->paginate($request->get('per_page', 15))
            : $query->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function show($id)
    {
        $data = ProcurementRequest::with(['user', 'status', 'items'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'           => 'required|exists:users,id',
            'status_id'         => 'required|exists:statuses,id',
            'request_date'      => 'required|date',
            'requester_name'    => 'required|string|max:255',
            'department'        => 'required|string|max:255',
            'used_for'          => 'nullable|string',
            'request_type'      => 'nullable|in:biasa,segera,mendesak',
            'description'       => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity'  => 'required|numeric|min:1',
            'items.*.price'     => 'nullable|numeric|min:0',
            'items.*.notes'     => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $requestNumber = 'PR-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $procurement = ProcurementRequest::create([
                'request_number' => $requestNumber,
                'user_id'        => $request->user_id,
                'status_id'      => $request->status_id,
                'request_date'   => $request->request_date,
                'requester_name' => $request->requester_name,
                'department'     => $request->department,
                'used_for'       => $request->used_for,
                'request_type'   => $request->request_type ?? 'biasa',
                'total_amount'   => 0,
                'description'    => $request->description,
            ]);

            $totalAmount = 0;
            foreach ($request->items as $item) {
                $subtotal     = $item['quantity'] * ($item['price'] ?? 0);
                $totalAmount += $subtotal;
                ProcurementItem::create([
                    'procurement_request_id' => $procurement->id,
                    'item_name'              => $item['item_name'],
                    'notes'                  => $item['notes'] ?? null,
                    'quantity'               => $item['quantity'],
                    'price'                  => $item['price'] ?? 0,
                    'subtotal'               => $subtotal,
                ]);
            }

            $procurement->update(['total_amount' => $totalAmount]);
            $procurement->load(['status', 'items', 'user']);

            DB::commit();

            // 🔔 Notifikasi ke semua admin
            $submitterName = $request->requester_name;
            $unitName      = $request->department;
            $itemCount     = $procurement->items->count();

            NotificationService::sendToAllAdmins(
                'procurement_new',
                'Pengajuan Baru Masuk',
                $submitterName . ' mengajukan ' . $itemCount . ' barang dari ' . $unitName . ' (#' . $requestNumber . ')',
                [
                    'procurement_id' => $procurement->id,
                    'request_number' => $requestNumber,
                    'submitter_name' => $submitterName,
                    'unit_name'      => $unitName,
                    'item_count'     => $itemCount,
                    'total_amount'   => $procurement->total_amount,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil dibuat.',
                'data'    => $procurement,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,disetujui,ditolak',
            'note'   => 'nullable|string',
        ]);

        $procurement = ProcurementRequest::with(['user', 'items'])->findOrFail($id);
        $status      = Status::where('name', $request->status)->firstOrFail();

        DB::beginTransaction();
        try {
            $procurement->update(['status_id' => $status->id]);

            if ($request->status === 'disetujui') {
                $this->createItemsFromProcurement($procurement, $request->user());
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        $procurement->refresh();

        // 🔔 Notifikasi ke user yang mengajukan
        $submitterId = $procurement->user_id;
        if ($submitterId && in_array($request->status, ['disetujui', 'ditolak'])) {
            $adminName   = optional($request->user())->name ?? 'Admin';
            $statusLabel = $request->status === 'disetujui' ? 'disetujui' : 'ditolak';
            $noteText    = $request->filled('note') ? ' Catatan: ' . $request->note : '';

            NotificationService::send(
                $submitterId,
                'procurement_status',
                'Status Pengajuan Diperbarui',
                'Pengajuan #' . $procurement->request_number . ' Anda telah ' . $statusLabel . ' oleh ' . $adminName . '.' . $noteText,
                [
                    'procurement_id' => $procurement->id,
                    'request_number' => $procurement->request_number,
                    'new_status'     => $request->status,
                    'note'           => $request->note,
                    'admin_name'     => $adminName,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pengajuan berhasil diperbarui.',
            'data'    => $procurement->load(['status', 'items', 'user']),
        ]);
    }

    private function createItemsFromProcurement(ProcurementRequest $procurement, $approver)
    {
        foreach ($procurement->items as $procItem) {
            $totalItems   = Item::withTrashed()->count() + 1;
            $serialNumber = 'BAS-' . date('Y') . '-' . str_pad($totalItems, 6, '0', STR_PAD_LEFT);

            Item::create([
                'name'           => $procItem->item_name,
                'serial_number'  => $serialNumber,
                'category'       => 'Umum',
                'specification'  => $procItem->item_name,
                'quantity'       => $procItem->quantity,
                'description'    => $procItem->notes ?? ('Dari pengajuan #' . $procurement->request_number),
                'condition'      => 'baik',
                'status'         => 'aktif',
                'purchase_date'  => now(),
                'purchase_price' => $procItem->price,
                'room_id'        => null,
                'created_by'     => $approver ? $approver->id : null,
            ]);
        }
    }

    public function destroy($id)
    {
        $procurement = ProcurementRequest::findOrFail($id);
        $statusName  = optional($procurement->status)->name;

        if ($statusName && $statusName !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengajuan berstatus pending yang dapat dihapus.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $procurement->items()->delete();
            $procurement->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pengajuan berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
