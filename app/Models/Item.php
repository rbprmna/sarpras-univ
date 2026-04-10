<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Item extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'serial_number',
        'category',
        'specification',
        'quantity',
        'description',
        'condition',
        'status',
        'purchase_date',
        'purchase_price',
        'room_id',
        'created_by',
        'procurement_item_id', // ← tambahan
    ];

    protected $casts = [
        'purchase_date'  => 'datetime',
        'purchase_price' => 'decimal:2',
        'quantity'       => 'integer',
    ];

    // ─── Relasi ───────────────────────────────────────────────

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function movements()
    {
        return $this->hasMany(ItemMovement::class)->orderByDesc('moved_at');
    }

    public function latestMovement()
    {
        return $this->hasOne(ItemMovement::class)->latestOfMany('moved_at');
    }

    /**
     * Relasi ke item dalam pengajuan (procurement_items).
     * Lewat sini bisa akses procurementItem->procurementRequest
     * untuk dapat nama pemohon & departemen.
     */
    public function procurementItem()
    {
        return $this->belongsTo(ProcurementItem::class);
    }

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByRoom($query, $roomId)
    {
        return $query->where('room_id', $roomId);
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('serial_number', 'like', "%{$keyword}%")
              ->orWhere('category', 'like', "%{$keyword}%")
              ->orWhere('specification', 'like', "%{$keyword}%");
        });
    }
}
