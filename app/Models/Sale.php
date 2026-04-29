<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $primaryKey = 'saleID';

    protected $casts = [
        'sold_at' => 'datetime',
    ];

    protected $fillable = ['userID', 'payment_method', 'gcash_reference', 'card_reference', 'total'];

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
