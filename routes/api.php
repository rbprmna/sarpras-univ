<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProcurementRequestController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\ItemImportController;
use App\Http\Controllers\Api\ItemMovementController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ActivityLogController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

// 🧪 TESTING ONLY - hapus setelah selesai test
Route::post('/test-notif', function () {
    \App\Http\Controllers\Api\NotificationController::sendNotification(
        1,
        'Test Notifikasi 🔔',
        'Websocket berhasil bekerja!',
        'success'
    );
    return response()->json(['ok' => true, 'message' => 'Notif terkirim!']);
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // ── Dashboard ────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // ── Statuses ─────────────────────────────────────────────
    Route::get('/statuses', [StatusController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | 🔔 Notification Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('notifications')->group(function () {
        Route::get('/',              [NotificationController::class, 'index']);
        Route::get('/unread-count',  [NotificationController::class, 'unreadCount']);
        Route::put('/mark-all-read', [NotificationController::class, 'markAllRead']);
        Route::put('/{id}/read',     [NotificationController::class, 'markRead']);
        Route::delete('/{id}',       [NotificationController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Procurement Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('procurements')->group(function () {
        Route::get('/',              [ProcurementRequestController::class, 'index']);
        Route::post('/',             [ProcurementRequestController::class, 'store']);
        Route::post('/bulk-status',  [ProcurementRequestController::class, 'bulkStatus']);   // ← BARU
        Route::post('/bulk-delete',  [ProcurementRequestController::class, 'bulkDestroy']);  // ← BARU
        Route::get('/{id}',          [ProcurementRequestController::class, 'show']);
        Route::put('/{id}/status',   [ProcurementRequestController::class, 'updateStatus']);
        Route::delete('/{id}',       [ProcurementRequestController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Room Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('rooms')->group(function () {
        Route::get('/',                [RoomController::class, 'index']);
        Route::post('/',               [RoomController::class, 'store']);
        Route::get('/{id}',            [RoomController::class, 'show']);
        Route::put('/{id}',            [RoomController::class, 'update']);
        Route::delete('/{id}',         [RoomController::class, 'destroy']);
        Route::get('/{id}/export-pdf', [RoomController::class, 'exportPdf']);
    });

    /*
    |--------------------------------------------------------------------------
    | Item Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('items')->group(function () {

        // ✅ Route spesifik harus di atas wildcard /{id}
        Route::get('/categories',        [ItemController::class,       'categories']);
        Route::get('/archived',          [ItemController::class,       'archived']);
        Route::get('/export-pdf',        [ItemController::class,       'exportPdf']);
        Route::post('/import',           [ItemImportController::class, 'import']);
        Route::get('/import/template',   [ItemImportController::class, 'template']);
        Route::post('/bulk-archive',     [ItemController::class,       'bulkArchive']);     // ← BARU
        Route::post('/bulk-export-pdf',  [ItemController::class,       'bulkExportPdf']);  // ← BARU
        Route::post('/bulk-restore',      [ItemController::class, 'bulkRestore']);
        Route::post('/bulk-force-delete', [ItemController::class, 'bulkForceDelete']);

        // CRUD utama
        Route::get('/',        [ItemController::class, 'index']);
        Route::post('/',       [ItemController::class, 'store']);
        Route::get('/{id}',    [ItemController::class, 'show']);
        Route::put('/{id}',    [ItemController::class, 'update']);
        Route::delete('/{id}', [ItemController::class, 'destroy']);

        // Route dengan wildcard /{id} — harus paling bawah
        Route::post('/{id}/restore',     [ItemController::class,         'restore']);
        Route::delete('/{id}/force',     [ItemController::class,         'forceDelete']);
        Route::post('/{id}/move',        [ItemController::class,         'move']);
        Route::get('/{id}/movements',    [ItemMovementController::class, 'byItem']);
    });

    /*
    |--------------------------------------------------------------------------
    | Movement Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('movements')->group(function () {
        Route::get('/',     [ItemMovementController::class, 'index']);
        Route::get('/{id}', [ItemMovementController::class, 'show']);
    });

    /*
    |--------------------------------------------------------------------------
    | Unit Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('units')->group(function () {
        Route::get('/', [UnitController::class, 'index']);
    });

    /*
    |--------------------------------------------------------------------------
    | User Management Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('users')->group(function () {
        Route::get('/meta',    [UserController::class, 'meta']);
        Route::get('/',        [UserController::class, 'index']);
        Route::post('/',       [UserController::class, 'store']);
        Route::get('/{id}',    [UserController::class, 'show']);
        Route::put('/{id}',    [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | 📊 Laporan Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('reports')->group(function () {
        Route::get('/procurement',          [ReportController::class, 'procurement']);
        Route::get('/procurement/per-unit', [ReportController::class, 'procurementPerUnit']);
    });

    /*
    |--------------------------------------------------------------------------
    | 📋 Log Aktivitas Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('activity-logs')->group(function () {
        Route::get('/stats',         [ActivityLogController::class, 'stats']);
        Route::get('/',              [ActivityLogController::class, 'index']);
        Route::get('/{activityLog}', [ActivityLogController::class, 'show']);
    });

});
