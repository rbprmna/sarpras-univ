<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public $timestamps = false;

    public function procurementRequests()
    {
        return $this->hasMany(ProcurementRequest::class);
    }
}

