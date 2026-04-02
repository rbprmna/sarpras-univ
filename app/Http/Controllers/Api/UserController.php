<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Unit;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * GET /api/users
     * List semua user dengan filter & pagination
     */
    public function index(Request $request)
    {
        $query = User::with(['role', 'unit'])
            ->orderBy('created_at', 'desc');

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Filter by unit
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        $perPage = $request->get('per_page', 10);
        $users   = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $users,
        ]);
    }

    /**
     * POST /api/users
     * Buat user baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id'  => 'required|exists:roles,id',
            'unit_id'  => 'nullable|exists:units,id',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id'  => $validated['role_id'],
            'unit_id'  => $validated['unit_id'] ?? null,
        ]);

        $user->load(['role', 'unit']);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dibuat',
            'data'    => $user,
        ], 201);
    }

    /**
     * GET /api/users/{id}
     * Detail satu user
     */
    public function show($id)
    {
        $user = User::with(['role', 'unit'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $user,
        ]);
    }

    /**
     * PUT /api/users/{id}
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
            'password' => 'nullable|string|min:8',
            'role_id'  => 'required|exists:roles,id',
            'unit_id'  => 'nullable|exists:units,id',
        ]);

        $user->name    = $validated['name'];
        $user->email   = $validated['email'];
        $user->role_id = $validated['role_id'];
        $user->unit_id = $validated['unit_id'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->load(['role', 'unit']);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diperbarui',
            'data'    => $user,
        ]);
    }

    /**
     * DELETE /api/users/{id}
     * Hapus user
     */
    public function destroy($id)
    {
        // Jangan hapus diri sendiri
        if (auth()->id() == $id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa menghapus akun sendiri',
            ], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus',
        ]);
    }

    /**
     * GET /api/users/meta
     * Ambil list roles & units untuk dropdown form
     */
    public function meta()
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'roles' => Role::orderBy('name')->get(['id', 'name']),
                'units' => Unit::orderBy('name')->get(['id', 'name']),
            ],
        ]);
    }
}
