<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'name', 'email', 'password', 'role'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'userID', 'id');
    }

    public function inboundTransactions()
    {
        return $this->hasMany(InboundTransaction::class, 'userID', 'id');
    }

    public function outboundTransactions()
    {
        return $this->hasMany(OutboundTransaction::class, 'userID', 'id');
    }

    public function cycleCounts()
    {
        return $this->hasMany(CycleCount::class, 'userID', 'id');
    }

    public function inventoryAdjustments()
    {
        return $this->hasMany(InventoryAdjustment::class, 'userID', 'id');
    }
}