<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderLine extends Model
{
    protected $primaryKey = 'po_lineID';

    protected $fillable = [
        'poID',
        'itemID',
        'quantity_ordered',
        'unit_cost'
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'poID', 'poID');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'itemID', 'itemID');
    }
}