<?php

namespace App\Http\Controllers;

use App\Models\SaleReturn;
use App\Models\Sale;
use App\Models\ReturnLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class SaleReturnController extends Controller
{
    public function index()
    {
        $returns = Schema::hasTable('sale_returns') && Schema::hasTable('sales')
            ? SaleReturn::with(['sale', 'user'])->orderByDesc('return_date')->get()
            : collect();
        return view('sale-returns.index', compact('returns'));
    }

    public function create()
    {
        $sales = Schema::hasTable('sales') && Schema::hasTable('sale_returns')
            ? Sale::with(['user', 'saleLines.item'])
                ->withCount('saleReturns')
                ->doesntHave('saleReturns')
                ->orderByDesc('sold_at')
                ->get()
            : collect();

        return view('sale-returns.create', compact('sales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'saleID' => [
                'required',
                'exists:sales,saleID',
                Rule::unique('sale_returns', 'saleID'),
            ],
            'reason' => 'required|string|max:255',
            'reason_other' => 'required_if:reason,Others|nullable|string|max:255',
            'return_date' => 'required|date',
        ]);

        $validated['reason'] = $validated['reason'] === 'Others'
            ? $validated['reason_other']
            : $validated['reason'];
        unset($validated['reason_other']);

        DB::transaction(function () use ($validated): void {
            $sale = Sale::with('saleLines.batch')->findOrFail($validated['saleID']);

            $saleReturn = SaleReturn::create([
                'saleID' => $validated['saleID'],
                'userID' => auth()->id(),
                'reason' => $validated['reason'],
                'return_date' => $validated['return_date'],
            ]);

            foreach ($sale->saleLines as $line) {
                if (! $line->batch) {
                    throw new \RuntimeException('Cannot return sale because one of its inventory batches no longer exists.');
                }

                ReturnLine::create([
                    'returnID' => $saleReturn->returnID,
                    'sale_lineID' => $line->sale_lineID,
                    'quantity_returned' => $line->quantity,
                    'refund_amount' => $line->price * $line->quantity,
                ]);

                $line->batch->increment('current_quantity', $line->quantity);
            }
        });

        return redirect()->route('sale-returns.index')
            ->with('success', 'Return recorded successfully and stock restored.');
    }
}
