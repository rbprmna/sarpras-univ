<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['name', 'code', 'description'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
