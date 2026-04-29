<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'poID';

    protected $casts = [
        'po_date' => 'date',
        'expected_date' => 'date',
    ];

    protected $fillable = [
        'supplierID',
        'status',
        'po_date',
        'expected_date',
        'total_amount'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplierID', 'supplierID');
    }

    public function purchaseOrderLines()
    {
        return $this->hasMany(PurchaseOrderLine::class, 'poID', 'poID');
    }

    public function inboundTransaction()
    {
        return $this->hasOne(InboundTransaction::class, 'poID', 'poID');
    }
}