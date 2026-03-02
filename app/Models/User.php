<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'unit_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    // User belongs to Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // User belongs to Unit
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // User membuat banyak procurement request
    public function procurementRequests()
    {
        return $this->hasMany(ProcurementRequest::class);
    }

    // User sebagai approver
    public function approvals()
    {
        return $this->hasMany(Approval::class, 'approver_id');
    }
}
