<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'supplierID';

    protected $fillable = [
        'supplier_name',
        'contact_person',
        'contact_number'
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'supplierID', 'supplierID');
    }
}