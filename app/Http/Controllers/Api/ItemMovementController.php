<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ItemMovement;
use Illuminate\Http\Request;

class ItemMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = ItemMovement::with([
            'item', 'fromRoom', 'toRoom', 'fromUnit', 'toUnit', 'movedBy',
        ]);

        if ($request->filled('item_id'))   $query->where('item_id', $request->item_id);
        if ($request->filled('type'))      $query->where('type', $request->type);
        if ($request->filled('date_from')) $query->whereDate('moved_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('moved_at', '<=', $request->date_to);

        // Ganti fn() dengan function() biasa — kompatibel PHP 7
        if ($request->filled('room_id')) {
            $roomId = $request->room_id;
            $query->where(function ($q) use ($roomId) {
                $q->where('from_room_id', $roomId)->orWhere('to_room_id', $roomId);
            });
        }

        if ($request->filled('unit_id')) {
            $unitId = $request->unit_id;
            $query->where(function ($q) use ($unitId) {
                $q->where('from_unit_id', $unitId)->orWhere('to_unit_id', $unitId);
            });
        }

        return response()->json([
            'success' => true,
            'data'    => $query->orderByDesc('moved_at')->paginate($request->get('per_page', 20)),
        ]);
    }

    public function show($id)
    {
        $movement = ItemMovement::with([
            'item', 'fromRoom.unit', 'toRoom.unit',
            'fromUnit', 'toUnit', 'movedBy',
        ])->findOrFail($id);

        return response()->json(['success' => true, 'data' => $movement]);
    }

    public function byItem($itemId)
    {
        $movements = ItemMovement::with([
            'fromRoom', 'toRoom', 'fromUnit', 'toUnit', 'movedBy',
        ])
        ->where('item_id', $itemId)
        ->orderByDesc('moved_at')
        ->get();

        return response()->json(['success' => true, 'data' => $movements]);
    }
}
