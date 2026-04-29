<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryBatch extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'batchID';

    protected $casts = [
        'expiration_date' => 'date',
    ];

    protected $fillable = [
        'itemID', 'locationID', 'lot_number', 'expiration_date', 
        'current_quantity', 'unit_cost'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'itemID', 'itemID');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'locationID', 'locationID');
    }

    public function saleLines()
    {
        return $this->hasMany(SaleLine::class, 'batchID', 'batchID');
    }

    public function outboundLineItems()
    {
        return $this->hasMany(OutboundLineItem::class, 'batchID', 'batchID');
    }

    public function cycleCountLines()
    {
        return $this->hasMany(CycleCountLine::class, 'batchID', 'batchID');
    }

    public function inventoryAdjustments()
    {
        return $this->hasMany(InventoryAdjustment::class, 'batchID', 'batchID');
    }
}