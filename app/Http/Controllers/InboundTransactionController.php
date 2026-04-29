<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\InboundTransaction;
use App\Models\InboundLineItem;
use App\Models\InventoryBatch;
use App\Models\Location;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class InboundTransactionController extends Controller
{
    public function index()
    {
        return redirect()->to(route('suppliers.index').'#receiving');
    }

    public function create()
    {
        $items = Schema::hasTable('items') ? Item::with('location')->orderBy('name')->get() : collect();
        $locations = Schema::hasTable('locations') ? Location::orderBy('name')->get() : collect();
        $purchaseOrders = Schema::hasTable('purchase_orders') && Schema::hasTable('suppliers')
            ? PurchaseOrder::with(['supplier', 'purchaseOrderLines.item'])
                ->where('status', 'Approved')
                ->whereDoesntHave('inboundTransaction')
                ->orderByDesc('po_date')
                ->get()
            : collect();

        return view('inbound.create', compact('items', 'locations', 'purchaseOrders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,itemID',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.lot_number' => 'nullable|string',
            'items.*.expiration_date' => 'nullable|date',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.location_id' => 'nullable|exists:locations,locationID',
            'poID' => [
                'nullable',
                Rule::exists('purchase_orders', 'poID')->where('status', 'Approved'),
            ],
            'date_received' => 'required|date',
            'quality_status' => 'required|in:Passed,Pending,Failed',
        ]);

        DB::beginTransaction();

        try {
            $inbound = InboundTransaction::create([
                'poID' => $request->poID,
                'userID' => auth()->id(),
                'quality_status' => $request->quality_status,
                'date_received' => $request->date_received,
                'total_cost' => 0,
            ]);

            $totalCost = 0;

            foreach ($request->items as $data) {
                $lineTotal = $data['quantity'] * $data['unit_cost'];
                $totalCost += $lineTotal;

                $item = Item::findOrFail($data['item_id']);
                $locationID = $data['location_id'] ?? $item->locationID ?? Location::query()->value('locationID');

                if (! $locationID) {
                    throw new \RuntimeException('Please create a location or assign a default location to this item before receiving stock.');
                }

                $batch = InventoryBatch::create([
                    'itemID' => $data['item_id'],
                    'locationID' => $locationID,
                    'lot_number' => $data['lot_number'],
                    'expiration_date' => $data['expiration_date'],
                    'current_quantity' => $data['quantity'],
                    'unit_cost' => $data['unit_cost'],
                ]);

                InboundLineItem::create([
                    'in_transactionID' => $inbound->in_transactionID,
                    'itemID' => $data['item_id'],
                    'batchID' => $batch->batchID,
                    'quantity_received' => $data['quantity'],
                    'lot_number' => $data['lot_number'],
                    'expiration_date' => $data['expiration_date'],
                    'unit_cost' => $data['unit_cost'],
                ]);
            }

            $inbound->update(['total_cost' => $totalCost]);

            if ($inbound->poID) {
                PurchaseOrder::whereKey($inbound->poID)->update(['status' => 'Received']);
            }

            DB::commit();

            return redirect()->to(route('suppliers.index').'#receiving')
                ->with('success', 'Goods received and stock updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(InboundTransaction $inbound)
    {
        $inbound->load('user', 'inboundLineItems.item');
        return view('inbound.show', compact('inbound'));
    }

    public function edit(InboundTransaction $inbound)
    {
        if ($inbound->quality_status !== 'Pending') {
            return redirect()->to(route('suppliers.index').'#receiving')
                ->with('error', 'Only pending receipts can be edited.');
        }

        $purchaseOrders = Schema::hasTable('purchase_orders') && Schema::hasTable('suppliers')
            ? PurchaseOrder::with('supplier')->whereIn('status', ['Approved', 'Received'])->orderByDesc('po_date')->get()
            : collect();

        return view('inbound.edit', compact('inbound', 'purchaseOrders'));
    }

    public function update(Request $request, InboundTransaction $inbound)
    {
        if ($inbound->quality_status !== 'Pending') {
            return redirect()->to(route('suppliers.index').'#receiving')
                ->with('error', 'Only pending receipts can be edited.');
        }

        $validated = $request->validate([
            'date_received' => 'required|date',
            'quality_status' => 'required|in:Passed,Pending,Failed',
            'poID' => 'nullable|exists:purchase_orders,poID',
        ]);

        $inbound->update($validated);

        return redirect()->to(route('suppliers.index').'#receiving')
            ->with('success', 'Pending receipt updated.');
    }

    public function complete(InboundTransaction $inbound)
    {
        if ($inbound->quality_status !== 'Passed') {
            return redirect()->back()->with('error', 'Cannot complete inbound with failed quality check.');
        }

        // Assuming completion logic, perhaps update status or something
        // For now, just redirect
        return redirect()->back()->with('success', 'Inbound transaction completed.');
    }

    public function qualityCheck(Request $request, InboundTransaction $inbound)
    {
        $request->validate([
            'quality_status' => 'required|in:Passed,Pending,Failed',
        ]);

        $inbound->update(['quality_status' => $request->quality_status]);

        return redirect()->back()->with('success', 'Quality check updated.');
    }
}
