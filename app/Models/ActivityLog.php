<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'model_label',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // ─── Relasi ───────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Accessor: label singkat untuk model_type ─────────
    // Pakai array map, bukan match() — kompatibel PHP 7.4+
    public function getModelNameAttribute(): string
    {
        $map = [
            'App\\Models\\ProcurementRequest' => 'Pengajuan',
            'App\\Models\\Item'               => 'Barang',
            'App\\Models\\ItemMovement'       => 'Pergerakan Barang',
            'App\\Models\\User'               => 'User',
            'App\\Models\\Room'               => 'Ruangan',
        ];

        return $map[$this->model_type] ?? class_basename($this->model_type);
    }

    // ─── Accessor: warna badge per action ─────────────────
    // Pakai array map, bukan match() — kompatibel PHP 7.4+
    public function getActionColorAttribute(): string
    {
        $colors = [
            'created'  => 'success',
            'updated'  => 'warning',
            'deleted'  => 'error',
            'approved' => 'success',
            'rejected' => 'error',
            'imported' => 'info',
        ];

        return $colors[$this->action] ?? 'default';
    }
}
