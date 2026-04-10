<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Approval extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'procurement_request_id',
        'approver_id',
        'status',
        'approved_at',
        'note'
    ];

    public function procurementRequest()
    {
        return $this->belongsTo(ProcurementRequest::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}

