<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemAlert extends Model
{
    protected $primaryKey = 'alertID';

    protected $casts = [
        'date_generated' => 'datetime',
        'resolved_at' => 'datetime',
        'is_resolved' => 'boolean',
    ];

    protected $fillable = [
        'itemID', 'batchID', 'alert_type', 'is_resolved', 
        'date_generated', 'resolved_at', 'resolved_by'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'itemID', 'itemID');
    }

    public function batch()
    {
        return $this->belongsTo(InventoryBatch::class, 'batchID', 'batchID');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by', 'id');
    }
}