<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function incomingMovements()
    {
        return $this->hasMany(ItemMovement::class, 'to_room_id');
    }

    public function outgoingMovements()
    {
        return $this->hasMany(ItemMovement::class, 'from_room_id');
    }
}
