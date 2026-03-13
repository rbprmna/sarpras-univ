<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::withCount('items');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'success' => true,
            'data'    => $query->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50|unique:rooms,code',
            'description' => 'nullable|string',
        ]);

        $room = Room::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ruangan berhasil ditambahkan.',
            'data'    => $room,
        ], 201);
    }

    public function show($id, Request $request)
    {
        $room = Room::with(['items' => function ($q) use ($request) {
            $q->with('latestMovement');

            if ($request->filled('date_from')) {
                $q->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $q->whereDate('created_at', '<=', $request->date_to);
            }
        }])
        ->withCount('items')
        ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $room,
        ]);
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => ['required', 'string', 'max:50', Rule::unique('rooms', 'code')->ignore($room->id)],
            'description' => 'nullable|string',
        ]);

        $room->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ruangan berhasil diperbarui.',
            'data'    => $room,
        ]);
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);

        if ($room->items()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan tidak bisa dihapus karena masih ada barang di dalamnya.',
            ], 422);
        }

        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ruangan berhasil dihapus.',
        ]);
    }
}
