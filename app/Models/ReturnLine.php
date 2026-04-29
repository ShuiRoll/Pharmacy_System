<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnLine extends Model
{
    protected $primaryKey = 'return_lineID';

    protected $fillable = [
        'returnID',
        'sale_lineID',
        'quantity_returned',
        'refund_amount'
    ];

    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class, 'returnID', 'returnID');
    }

    public function saleLine()
    {
        return $this->belongsTo(SaleLine::class, 'sale_lineID', 'sale_lineID');
    }
}