<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\InventoryBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Schema::hasTable('sales') && Schema::hasTable('sale_lines')
            ? Sale::with(['user', 'saleLines.item'])
                ->withCount('saleReturns')
                ->orderByDesc('sold_at')
                ->get()
            : collect();
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $items = [];
        if (Schema::hasTable('items') && Schema::hasTable('inventory_batches')) {
            $items = Item::with(['inventoryBatches' => function($q) {
                $q->where('current_quantity', '>', 0)
                  ->where(function ($query) {
                      $query->whereNull('expiration_date')
                          ->orWhere('expiration_date', '>=', now()->format('Y-m-d'));
                  })
                  ->orderByRaw('expiration_date IS NULL')
                  ->orderBy('expiration_date')
                  ->orderBy('batchID');
            }])->get();
        }

        return view('sales.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,itemID',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:Cash,GCash,Card',
            'gcash_reference' => 'required_if:payment_method,GCash|nullable|string|max:100',
            'card_reference' => 'required_if:payment_method,Card|nullable|string|max:100',
        ]);

        DB::beginTransaction();

        try {
            $sale = Sale::create([
                'userID' => auth()->id(),
                'payment_method' => $request->payment_method,
                'gcash_reference' => $request->payment_method === 'GCash' ? $request->gcash_reference : null,
                'card_reference' => $request->payment_method === 'Card' ? $request->card_reference : null,
                'total' => 0,
            ]);

            $total = 0;

            foreach ($request->items as $data) {
                $item = Item::findOrFail($data['item_id']);
                $remainingQuantity = (int) $data['quantity'];
                $availableBatches = InventoryBatch::where('itemID', $item->itemID)
                    ->where('current_quantity', '>', 0)
                    ->where(function ($query) {
                        $query->whereNull('expiration_date')
                            ->orWhere('expiration_date', '>=', now()->format('Y-m-d'));
                    })
                    ->orderByRaw('expiration_date IS NULL')
                    ->orderBy('expiration_date')
                    ->orderBy('batchID')
                    ->lockForUpdate()
                    ->get();

                if ($availableBatches->sum('current_quantity') < $remainingQuantity) {
                    throw new \Exception("Insufficient stock for {$item->name}");
                }

                foreach ($availableBatches as $batch) {
                    if ($remainingQuantity <= 0) {
                        break;
                    }

                    $quantityFromBatch = min($remainingQuantity, $batch->current_quantity);
                    $lineTotal = $item->price * $quantityFromBatch;
                    $total += $lineTotal;

                    SaleLine::create([
                        'saleID' => $sale->saleID,
                        'itemID' => $item->itemID,
                        'batchID' => $batch->batchID,
                        'quantity' => $quantityFromBatch,
                        'price' => $item->price,
                    ]);

                    $batch->decrement('current_quantity', $quantityFromBatch);
                    $remainingQuantity -= $quantityFromBatch;
                }
            }

            $sale->update(['total' => $total]);

            DB::commit();

            return redirect()->route('sales.index')
                ->with('success', 'Sale completed successfully! Stock updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Sale $sale)
    {
        $sale->load('user', 'saleLines.item');
        return view('sales.show', compact('sale'));
    }

    public function dailyReport()
    {
        $sales = Sale::whereDate('created_at', today())->with(['user', 'saleLines'])->get();
        $total = $sales->sum('total');

        return view('sales.reports.daily', compact('sales', 'total'));
    }

    public function monthlyReport()
    {
        $sales = Sale::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->with(['user', 'saleLines'])->get();
        $total = $sales->sum('total');

        return view('sales.reports.monthly', compact('sales', 'total'));
    }
}
