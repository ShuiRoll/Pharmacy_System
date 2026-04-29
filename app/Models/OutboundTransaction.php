<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboundTransaction extends Model
{
    protected $primaryKey = 'out_transactionID';

    protected $casts = [
        'transaction_date' => 'date',
    ];

    protected $fillable = [
        'userID',
        'transaction_date',
        'destination',
        'status',
        'total_amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }

    public function outboundLineItems()
    {
        return $this->hasMany(OutboundLineItem::class, 'out_transactionID', 'out_transactionID');
    }
}
