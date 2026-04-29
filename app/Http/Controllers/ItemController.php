<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ItemController extends Controller
{
    public function index()
    {
        $items = Schema::hasTable('items') && Schema::hasTable('inventory_batches')
            ? Item::with(['inventoryBatches', 'location'])->latest()->get()
            : collect();

        $locations = Schema::hasTable('locations') && Schema::hasTable('inventory_batches')
            ? Location::with('inventoryBatches')->latest()->get()
            : collect();

        return view('items.index', compact('items', 'locations'));
    }

    public function create()
    {
        $locations = Schema::hasTable('locations') ? Location::orderBy('name')->get() : collect();
        $itemCodes = Schema::hasTable('items') ? Item::pluck('item_code')->values() : collect();
        $itemNames = Schema::hasTable('items') ? Item::pluck('name')->values() : collect();

        return view('items.create', compact('locations', 'itemCodes', 'itemNames'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_code'        => ['required', 'regex:/^[0-9]+$/', 'unique:items,item_code'],
            'name'             => 'required|string|max:255',
            'category'         => 'nullable|string|max:100',
            'price'            => 'required|numeric|min:0',
            'minimum_stock_lvl'=> 'required|integer|min:0',
            'locationID'        => 'nullable|exists:locations,locationID',
            'description'      => 'nullable|string',
        ]);

        Item::create($validated);

        return redirect()->route('items.index')
            ->with('success', 'Medicine created successfully.');
    }

    public function show(Item $item)
    {
        $item->load([
            'location',
            'inventoryBatches' => function ($query) {
                $query->with('location')
                    ->orderByRaw('expiration_date IS NULL')
                    ->orderBy('expiration_date')
                    ->orderBy('batchID');
            },
        ]);

        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $locations = Schema::hasTable('locations') ? Location::orderBy('name')->get() : collect();
        $itemCodes = Schema::hasTable('items') ? Item::whereKeyNot($item->itemID)->pluck('item_code')->values() : collect();
        $itemNames = Schema::hasTable('items') ? Item::whereKeyNot($item->itemID)->pluck('name')->values() : collect();

        return view('items.edit', compact('item', 'locations', 'itemCodes', 'itemNames'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'item_code'        => ['required', 'regex:/^[0-9]+$/', 'unique:items,item_code,' . $item->itemID . ',itemID'],
            'name'             => 'required|string|max:255',
            'category'         => 'nullable|string|max:100',
            'price'            => 'required|numeric|min:0',
            'minimum_stock_lvl'=> 'required|integer|min:0',
            'locationID'        => 'nullable|exists:locations,locationID',
            'description'      => 'nullable|string',
        ]);

        $item->update($validated);

        return redirect()->route('items.index')
            ->with('success', 'Medicine updated successfully.');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Medicine deleted successfully.');
    }

    public function lowStock()
    {
        if (! Schema::hasTable('items') || ! Schema::hasTable('inventory_batches')) {
            return view('items.low-stock', ['items' => collect()]);
        }

        $items = Item::with('inventoryBatches')->whereRaw('minimum_stock_lvl >= (SELECT COALESCE(SUM(current_quantity), 0) FROM inventory_batches WHERE itemID = items.itemID)')
            ->get();

        return view('items.low-stock', compact('items'));
    }

    public function nearExpiry()
    {
        if (! Schema::hasTable('items') || ! Schema::hasTable('inventory_batches')) {
            return view('items.near-expiry', ['items' => collect()]);
        }

        $items = Item::whereHas('inventoryBatches', function($q) {
            $q->where('expiration_date', '<=', now()->addDays(30))
              ->where('current_quantity', '>', 0);
        })->with('inventoryBatches')->get();
        return view('items.near-expiry', compact('items'));
    }
}
