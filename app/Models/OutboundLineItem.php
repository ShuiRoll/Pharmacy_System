<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboundLineItem extends Model
{
    protected $primaryKey = 'outbound_lineID';

    protected $fillable = [
        'out_transactionID',
        'batchID',
        'quantity_dispensed',
        'unit_price',
        'line_total',
    ];

    public function outboundTransaction()
    {
        return $this->belongsTo(OutboundTransaction::class, 'out_transactionID', 'out_transactionID');
    }

    public function batch()
    {
        return $this->belongsTo(InventoryBatch::class, 'batchID', 'batchID');
    }
}
