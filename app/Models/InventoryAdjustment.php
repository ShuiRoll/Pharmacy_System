<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    protected $primaryKey = 'adjustmentID';

    protected $casts = [
        'adjustment_date' => 'date',
    ];

    protected $fillable = [
        'batchID',
        'userID',
        'adjustment_date',
        'quantity_changed',
        'reason',
        'cycle_count_lineID'
    ];

    public function batch()
    {
        return $this->belongsTo(InventoryBatch::class, 'batchID', 'batchID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }

    public function cycleCountLine()
    {
        return $this->belongsTo(CycleCountLine::class, 'cycle_count_lineID', 'lineID');
    }
}
