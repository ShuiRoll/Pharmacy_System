<?php

namespace App\Http\Controllers;

use App\Models\InventoryBatch;
use App\Models\Item;
use App\Models\SystemAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SystemAlertController extends Controller
{
    public function index()
    {
        $this->syncInventoryAlerts();

        $alerts = Schema::hasTable('system_alerts') && Schema::hasTable('items') && Schema::hasTable('inventory_batches')
            ? SystemAlert::with(['item', 'batch'])->where('is_resolved', false)->orderByDesc('date_generated')->get()
            : collect();
        return view('system_alerts.index', compact('alerts'));
    }

    public function update(Request $request, SystemAlert $systemAlert)
    {
        $systemAlert->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
        ]);

        return redirect()->route('system-alerts.index')
            ->with('success', 'Alert marked as resolved.');
    }

    private function syncInventoryAlerts(): void
    {
        if (! Schema::hasTable('system_alerts') || ! Schema::hasTable('items') || ! Schema::hasTable('inventory_batches')) {
            return;
        }

        Item::with('inventoryBatches')->get()->each(function (Item $item): void {
            $stock = $item->inventoryBatches->sum('current_quantity');

            if ($stock <= $item->minimum_stock_lvl) {
                SystemAlert::firstOrCreate([
                    'itemID' => $item->itemID,
                    'batchID' => null,
                    'alert_type' => 'Low Stock',
                    'is_resolved' => false,
                ], [
                    'date_generated' => now(),
                ]);
            }
        });

        InventoryBatch::with('item')
            ->where('current_quantity', '>', 0)
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<=', now()->addDays(30))
            ->get()
            ->each(function (InventoryBatch $batch): void {
                SystemAlert::firstOrCreate([
                    'itemID' => $batch->itemID,
                    'batchID' => $batch->batchID,
                    'alert_type' => 'Near Expiry',
                    'is_resolved' => false,
                ], [
                    'date_generated' => now(),
                ]);
            });
    }
}
