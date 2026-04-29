<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\InventoryAdjustment;
use App\Models\InventoryBatch;
use App\Models\OutboundTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class InventoryAdjustmentController extends Controller
{
    public function index()
    {
        $adjustments = Schema::hasTable('inventory_adjustments') && Schema::hasTable('inventory_batches')
            ? InventoryAdjustment::with(['batch.item', 'user'])->orderByDesc('adjustment_date')->get()
            : collect();

        $lowStockItems = Schema::hasTable('items') && Schema::hasTable('inventory_batches')
            ? Item::with('inventoryBatches')
                ->whereRaw('minimum_stock_lvl >= (SELECT COALESCE(SUM(current_quantity), 0) FROM inventory_batches WHERE itemID = items.itemID)')
                ->get()
            : collect();

        $nearExpiryItems = Schema::hasTable('items') && Schema::hasTable('inventory_batches')
            ? Item::whereHas('inventoryBatches', function ($query) {
                $query->where('expiration_date', '<=', now()->addDays(30))
                    ->where('current_quantity', '>', 0);
            })->with('inventoryBatches')->get()
            : collect();

        $outbounds = Schema::hasTable('outbound_transactions') && Schema::hasTable('outbound_line_items') && Schema::hasTable('inventory_batches') && Schema::hasTable('items')
            ? OutboundTransaction::with(['user', 'outboundLineItems.batch.item'])
                ->orderByDesc('transaction_date')
                ->orderByDesc('out_transactionID')
                ->get()
            : collect();

        return view('inventory_adjustments.index', compact('adjustments', 'lowStockItems', 'nearExpiryItems', 'outbounds'));
    }

    public function create()
    {
        $batches = Schema::hasTable('inventory_batches') && Schema::hasTable('items')
            ? InventoryBatch::with('item')->get()
            : collect();

        return view('inventory_adjustments.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'batchID' => 'required|exists:inventory_batches,batchID',
            'quantity_changed' => 'required|integer|not_in:0',
            'reason' => 'required|string|max:255',
        ]);

        InventoryAdjustment::create([
            'batchID' => $validated['batchID'],
            'userID' => auth()->id(),
            'adjustment_date' => now()->toDateString(),
            'quantity_changed' => $validated['quantity_changed'],
            'reason' => $validated['reason'],
        ]);

        // Update batch quantity
        $batch = InventoryBatch::find($validated['batchID']);
        $batch->increment('current_quantity', $validated['quantity_changed']);

        return redirect()->to(route('inventory-adjustments.index').'#adjustments')
            ->with('success', 'Inventory adjustment recorded successfully.');
    }
}
