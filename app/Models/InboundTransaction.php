<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InboundTransaction extends Model
{
    protected $primaryKey = 'in_transactionID';

    protected $casts = [
        'date_received' => 'date',
    ];

    protected $fillable = ['poID', 'userID', 'quality_status', 'date_received', 'total_cost'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }

    public function inboundLineItems()
    {
        return $this->hasMany(InboundLineItem::class, 'in_transactionID', 'in_transactionID');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'poID', 'poID');
    }
}
