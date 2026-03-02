<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['room', 'creator'])
            ->withTrashed($request->boolean('with_deleted'));

        if ($request->filled('search'))    $query->search($request->search);
        if ($request->filled('room_id'))   $query->byRoom($request->room_id);
        if ($request->filled('status'))    $query->where('status', $request->status);
        if ($request->filled('condition')) $query->where('condition', $request->condition);
        if ($request->filled('category'))  $query->where('category', $request->category);

        $sort = $request->get('sort', 'desc');
        $query->orderBy('created_at', $sort === 'asc' ? 'asc' : 'desc');

        return response()->json([
            'success' => true,
            'data'    => $query->paginate($request->get('per_page', 15)),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'serial_number'  => 'required|string|max:255|unique:items,serial_number',
            'category'       => 'nullable|string|max:100',
            'specification'  => 'nullable|string',
            'quantity'       => 'nullable|integer|min:1',
            'description'    => 'nullable|string',
            'condition'      => ['nullable', Rule::in(['baik', 'cukup_baik', 'rusak_ringan', 'rusak_berat'])],
            'status'         => ['nullable', Rule::in(['aktif', 'tidak_aktif', 'dipinjam', 'dalam_perbaikan'])],
            'purchase_date'  => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'room_id'        => 'nullable|exists:rooms,id',
            'note'           => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $item = Item::create(array_merge($validated, [
                'created_by' => Auth::id(),
                'quantity'   => $validated['quantity'] ?? 1,
            ]));

            ItemMovement::create([
                'item_id'    => $item->id,
                'to_room_id' => $validated['room_id'] ?? null,
                'type'       => 'masuk',
                'moved_by'   => Auth::id(),
                'moved_at'   => now(),
                'note'       => $validated['note'] ?? 'Barang baru masuk.',
            ]);

            DB::commit();
            $item->load(['room', 'creator']);

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil ditambahkan.',
                'data'    => $item,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $item = Item::with([
            'room', 'creator',
            'movements.fromRoom', 'movements.toRoom',
            'movements.movedBy',
        ])->findOrFail($id);

        return response()->json(['success' => true, 'data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'serial_number'  => ['required', 'string', 'max:255', Rule::unique('items', 'serial_number')->ignore($item->id)],
            'category'       => 'nullable|string|max:100',
            'specification'  => 'nullable|string',
            'quantity'       => 'nullable|integer|min:1',
            'description'    => 'nullable|string',
            'condition'      => ['nullable', Rule::in(['baik', 'cukup_baik', 'rusak_ringan', 'rusak_berat'])],
            'status'         => ['nullable', Rule::in(['aktif', 'tidak_aktif', 'dipinjam', 'dalam_perbaikan'])],
            'purchase_date'  => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
        ]);

        $item->update($validated);
        $item->load(['room', 'creator']);

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil diperbarui.',
            'data'    => $item,
        ]);
    }

    public function move(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $validated = $request->validate([
            'to_room_id' => 'required|exists:rooms,id',
            'type'       => ['nullable', Rule::in(['pindah', 'pinjam', 'kembali', 'perbaikan', 'selesai_perbaikan', 'keluar'])],
            'note'       => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $fromRoomId = $item->room_id;
            $type       = $validated['type'] ?? 'pindah';

            switch ($type) {
                case 'pinjam':            $newStatus = 'dipinjam';        break;
                case 'perbaikan':         $newStatus = 'dalam_perbaikan'; break;
                case 'kembali':
                case 'selesai_perbaikan': $newStatus = 'aktif';           break;
                case 'keluar':            $newStatus = 'tidak_aktif';     break;
                default:                  $newStatus = $item->status;     break;
            }

            $item->update([
                'room_id' => $validated['to_room_id'],
                'status'  => $newStatus,
            ]);

            ItemMovement::create([
                'item_id'      => $item->id,
                'from_room_id' => $fromRoomId,
                'to_room_id'   => $validated['to_room_id'],
                'type'         => $type,
                'moved_by'     => Auth::id(),
                'moved_at'     => now(),
                'note'         => $validated['note'] ?? null,
            ]);

            DB::commit();
            $item->load(['room']);

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil dipindahkan.',
                'data'    => $item,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        Item::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Barang berhasil diarsipkan.']);
    }

    public function restore($id)
    {
        $item = Item::onlyTrashed()->findOrFail($id);
        $item->restore();
        return response()->json(['success' => true, 'message' => 'Barang berhasil dipulihkan.', 'data' => $item]);
    }

    public function categories()
    {
        return response()->json([
            'success' => true,
            'data'    => Item::select('category')
                ->whereNotNull('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category'),
        ]);
    }
}
