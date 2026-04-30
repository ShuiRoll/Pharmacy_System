<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $primaryKey = 'saleID';

    protected $casts = [
        'sold_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected $fillable = [
        'userID',
        'payment_method',
        'gcash_reference',
        'card_reference',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }

    public function saleLines()
    {
        return $this->hasMany(SaleLine::class, 'saleID', 'saleID');
    }

    public function saleReturns()
    {
        return $this->hasMany(SaleReturn::class, 'saleID', 'saleID');
    }
}
