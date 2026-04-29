<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    protected $primaryKey = 'returnID';

    protected $casts = [
        'return_date' => 'date',
    ];

    protected $fillable = [
        'saleID',
        'userID',
        'reason',
        'return_date'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'saleID', 'saleID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }

    public function returnLines()
    {
        return $this->hasMany(ReturnLine::class, 'returnID', 'returnID');
    }
}