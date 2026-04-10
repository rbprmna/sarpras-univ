<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class ProcurementRequest extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'request_number',
        'user_id',
        'status_id',
        'requester_name',
        'department',
        'used_for',
        'request_type',
        'total_amount',
        'description',
        'request_date',
    ];

    protected $casts = [
        'request_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function items()
    {
        return $this->hasMany(ProcurementItem::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }
}
