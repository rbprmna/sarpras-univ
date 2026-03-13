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

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

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
        Route::get('/',             [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/read-all',    [NotificationController::class, 'markAllRead']);
        Route::post('/{id}/read',   [NotificationController::class, 'markRead']);
        Route::delete('/{id}',      [NotificationController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Procurement Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('procurements')->group(function () {
        Route::get('/',            [ProcurementRequestController::class, 'index']);
        Route::post('/',           [ProcurementRequestController::class, 'store']);
        Route::get('/{id}',        [ProcurementRequestController::class, 'show']);
        Route::put('/{id}/status', [ProcurementRequestController::class, 'updateStatus']);
        Route::delete('/{id}',     [ProcurementRequestController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Room Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('rooms')->group(function () {
        Route::get('/',        [RoomController::class, 'index']);
        Route::post('/',       [RoomController::class, 'store']);
        Route::get('/{id}',    [RoomController::class, 'show']);
        Route::put('/{id}',    [RoomController::class, 'update']);
        Route::delete('/{id}', [RoomController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Item Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('items')->group(function () {
        Route::get('/categories',      [ItemController::class,       'categories']);
        Route::get('/export-pdf',      [ItemController::class,       'exportPdf']);  // ← tambah ini
        Route::post('/import',         [ItemImportController::class, 'import']);
        Route::get('/import/template', [ItemImportController::class, 'template']);

        Route::get('/',        [ItemController::class, 'index']);
        Route::post('/',       [ItemController::class, 'store']);
        Route::get('/{id}',    [ItemController::class, 'show']);
        Route::put('/{id}',    [ItemController::class, 'update']);
        Route::delete('/{id}', [ItemController::class, 'destroy']);

        Route::post('/{id}/restore',  [ItemController::class,         'restore']);
        Route::post('/{id}/move',     [ItemController::class,         'move']);
        Route::get('/{id}/movements', [ItemMovementController::class, 'byItem']);
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

});
