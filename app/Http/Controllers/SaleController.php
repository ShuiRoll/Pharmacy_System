<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\InventoryBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SaleController extends Controller
{
    private const TAX_RATE = 0.12;

    public function index()
    {
        $sales = Schema::hasTable('sales') && Schema::hasTable('sale_lines')
            ? Sale::with(['user', 'saleLines.item'])
                ->withCount('saleReturns')
                ->orderByDesc('sold_at')
                ->paginate(12)
            : collect();
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $items = collect();
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
            }])->paginate(8);
        }

        return view('sales.create', [
            'items' => $items,
            'taxRate' => self::TAX_RATE,
        ]);
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
                'subtotal' => 0,
                'tax_rate' => self::TAX_RATE,
                'tax_amount' => 0,
                'total' => 0,
            ]);

            $subtotal = 0;

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
                    $subtotal += $lineTotal;

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

            $taxAmount = round($subtotal * self::TAX_RATE, 2);
            $sale->update([
                'subtotal' => $subtotal,
                'tax_rate' => self::TAX_RATE,
                'tax_amount' => $taxAmount,
                'total' => $subtotal + $taxAmount,
            ]);

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
        $sale->load([
            'user',
            'saleLines.item',
            'saleLines.batch',
            'saleReturns.user',
            'saleReturns.returnLines.saleLine.item',
        ]);
        return view('sales.show', compact('sale'));
    }

    public function dailyReport()
    {
        [$sales, $total] = $this->salesReportData(today()->startOfDay(), today()->endOfDay(), true, 'daily_page');

        return view('sales.reports.daily', compact('sales', 'total'));
    }

    public function monthlyReport()
    {
        [$sales, $total] = $this->salesReportData(now()->startOfMonth(), now()->endOfMonth(), true, 'monthly_page');

        return view('sales.reports.monthly', compact('sales', 'total'));
    }

    public function dailyReportPdf()
    {
        [$sales, $total] = $this->salesReportData(today()->startOfDay(), today()->endOfDay());

        $filename = 'daily-sales-report-' . today()->format('Y-m-d') . '.pdf';

        return Pdf::loadView('sales.reports.pdf', [
            'reportTitle' => 'Daily Sales Report',
            'reportSubtitle' => 'Sales completed today',
            'periodLabel' => today()->format('M d, Y'),
            'sales' => $sales,
            'total' => $total,
        ])->download($filename);
    }

    public function monthlyReportPdf()
    {
        [$sales, $total] = $this->salesReportData(now()->startOfMonth(), now()->endOfMonth());

        $filename = 'monthly-sales-report-' . now()->format('Y-m') . '.pdf';

        return Pdf::loadView('sales.reports.pdf', [
            'reportTitle' => 'Monthly Sales Report',
            'reportSubtitle' => 'Sales completed this month',
            'periodLabel' => now()->format('F Y'),
            'sales' => $sales,
            'total' => $total,
        ])->download($filename);
    }

    private function salesReportData($startDate, $endDate, bool $paginate = false, string $pageName = 'page'): array
    {
        $baseQuery = Sale::whereBetween('created_at', [$startDate, $endDate]);

        $salesQuery = (clone $baseQuery)
            ->with(['user', 'saleLines'])
            ->withCount('saleReturns')
            ->orderByDesc('created_at');

        $sales = $paginate
            ? $salesQuery->paginate(12, ['*'], $pageName)
            : $salesQuery->get();

        $total = (clone $baseQuery)
            ->whereDoesntHave('saleReturns')
            ->sum('total');

        return [$sales, $total];
    }
}
