<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InboundLineItem extends Model
{
    protected $primaryKey = 'lineID';

    protected $fillable = [
        'in_transactionID', 'itemID', 'batchID', 
        'quantity_received', 'lot_number', 'expiration_date', 'unit_cost'
    ];

    public function inboundTransaction()
    {
        return $this->belongsTo(InboundTransaction::class, 'in_transactionID', 'in_transactionID');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'itemID', 'itemID');
    }

    public function batch()
    {
        return $this->belongsTo(InventoryBatch::class, 'batchID', 'batchID');
    }
}