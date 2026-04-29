<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CycleCountLine extends Model
{
    protected $primaryKey = 'lineID';

    protected $fillable = [
        'countID',
        'batchID',
        'expected_quantity',
        'actual_quantity'
    ];

    public function cycleCount()
    {
        return $this->belongsTo(CycleCount::class, 'countID', 'countID');
    }

    public function batch()
    {
        return $this->belongsTo(InventoryBatch::class, 'batchID', 'batchID');
    }

    public function inventoryAdjustment()
    {
        return $this->hasOne(InventoryAdjustment::class, 'cycle_count_lineID', 'lineID');
    }
}
