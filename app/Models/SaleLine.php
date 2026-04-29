<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleLine extends Model
{
    protected $primaryKey = 'sale_lineID';

    protected $fillable = ['saleID', 'itemID', 'batchID', 'quantity', 'price'];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'saleID', 'saleID');
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