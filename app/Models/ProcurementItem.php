<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_request_id',
        'item_name',
        'quantity',
        'price',
        'subtotal',
        'notes',
    ];

    public function procurementRequest()
    {
        return $this->belongsTo(ProcurementRequest::class);
    }
}
