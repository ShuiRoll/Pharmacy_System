<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'itemID';

    protected $fillable = [
        'item_code', 'name', 'description', 'price', 
        'minimum_stock_lvl', 'category', 'locationID'
    ];

    public function inventoryBatches()
    {
        return $this->hasMany(InventoryBatch::class, 'itemID', 'itemID');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'locationID', 'locationID');
    }

    public function saleLines()
    {
        return $this->hasMany(SaleLine::class, 'itemID', 'itemID');
    }

    public function inboundLineItems()
    {
        return $this->hasMany(InboundLineItem::class, 'itemID', 'itemID');
    }

    public function systemAlerts()
    {
        return $this->hasMany(SystemAlert::class, 'itemID', 'itemID');
    }

    protected static function booted(): void
    {
        static::deleting(function (Item $item): void {
            $batches = $item->inventoryBatches()->withTrashed()->get();

            foreach ($batches as $batch) {
                $item->isForceDeleting() ? $batch->forceDelete() : $batch->delete();
            }
        });

        static::restoring(function (Item $item): void {
            $item->inventoryBatches()->withTrashed()->get()->each->restore();
        });
    }
}
