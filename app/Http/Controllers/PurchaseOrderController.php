<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Item;
use App\Models\PurchaseOrderLine;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        return redirect()->to(route('suppliers.index').'#purchasing');
    }

    public function create(Request $request)
    {
        $suppliers = Schema::hasTable('suppliers') ? Supplier::all() : collect();
        $items = Schema::hasTable('items') ? Item::orderBy('name')->get() : collect();
        $prefillItem = Schema::hasTable('items') && $request->filled('item_id')
            ? Item::with('inventoryBatches')->find($request->integer('item_id'))
            : null;
        $prefillQuantity = $prefillItem
            ? max(1, (int) $prefillItem->minimum_stock_lvl - (int) $prefillItem->inventoryBatches->sum('current_quantity'))
            : null;

        return view('purchase_orders.create', compact('suppliers', 'items', 'prefillItem', 'prefillQuantity'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplierID' => 'required|exists:suppliers,supplierID',
            'po_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,itemID',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated): void {
            $lines = $validated['items'];
            unset($validated['items']);

            $validated['status'] = 'Pending';
            $validated['total_amount'] = collect($lines)->sum(fn ($line) => $line['quantity'] * $line['unit_cost']);
            $purchaseOrder = PurchaseOrder::create($validated);

            foreach ($lines as $line) {
                PurchaseOrderLine::create([
                    'poID' => $purchaseOrder->poID,
                    'itemID' => $line['item_id'],
                    'quantity_ordered' => $line['quantity'],
                    'unit_cost' => $line['unit_cost'],
                ]);
            }
        });

        return redirect()->to(route('suppliers.index').'#purchasing')
            ->with('success', 'Purchase Order created successfully.');
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $suppliers = Schema::hasTable('suppliers') ? Supplier::all() : collect();
        $items = Schema::hasTable('items') ? Item::orderBy('name')->get() : collect();
        $purchaseOrder->load('purchaseOrderLines.item');

        return view('purchase_orders.edit', compact('purchaseOrder', 'suppliers', 'items'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'supplierID' => 'required|exists:suppliers,supplierID',
            'po_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'status' => 'required|in:Pending,Approved,Received,Rejected',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,itemID',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($purchaseOrder, $validated): void {
            $lines = $validated['items'];
            unset($validated['items']);

            $validated['total_amount'] = collect($lines)->sum(fn ($line) => $line['quantity'] * $line['unit_cost']);
            $purchaseOrder->update($validated);
            $purchaseOrder->purchaseOrderLines()->delete();

            foreach ($lines as $line) {
                PurchaseOrderLine::create([
                    'poID' => $purchaseOrder->poID,
                    'itemID' => $line['item_id'],
                    'quantity_ordered' => $line['quantity'],
                    'unit_cost' => $line['unit_cost'],
                ]);
            }
        });

        return redirect()->to(route('suppliers.index').'#purchasing')
            ->with('success', 'Purchase Order updated successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier', 'purchaseOrderLines.item');
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order deleted successfully.');
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'Pending') {
            return redirect()->back()->with('error', 'Purchase Order cannot be approved.');
        }

        $purchaseOrder->update(['status' => 'Approved']);

        return redirect()->to(route('suppliers.index').'#purchasing')->with('success', 'Purchase Order approved successfully.');
    }

    public function reject(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'Pending') {
            return redirect()->back()->with('error', 'Purchase Order cannot be rejected.');
        }

        $purchaseOrder->update(['status' => 'Rejected']);

        return redirect()->to(route('suppliers.index').'#purchasing')->with('success', 'Purchase Order rejected successfully.');
    }
}
