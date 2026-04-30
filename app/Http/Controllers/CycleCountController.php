<?php

namespace App\Http\Controllers;

use App\Models\CycleCount;
use App\Models\CycleCountLine;
use App\Models\InventoryAdjustment;
use App\Models\InventoryBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CycleCountController extends Controller
{
    public function index()
    {
        $cycleCounts = Schema::hasTable('cycle_counts') && Schema::hasTable('cycle_count_lines') && Schema::hasTable('inventory_batches') && Schema::hasTable('items')
            ? CycleCount::with(['user', 'cycleCountLines.batch.item'])->orderByDesc('count_date')->get()
            : collect();
        return view('cycle_counts.index', compact('cycleCounts'));
    }

    public function create()
    {
        $batches = Schema::hasTable('inventory_batches') && Schema::hasTable('items')
            ? InventoryBatch::with('item')->orderBy('itemID')->orderBy('expiration_date')->get()
            : collect();

        return view('cycle_counts.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'count_date' => 'required|date',
            'lines' => 'required|array|min:1',
            'lines.*.batchID' => 'required|exists:inventory_batches,batchID',
        ]);

        DB::transaction(function () use ($validated): void {
            $cycleCount = CycleCount::create([
                'userID' => auth()->id(),
                'count_date' => $validated['count_date'],
                'status' => 'Incomplete',
            ]);

            foreach ($validated['lines'] as $lineData) {
                $batch = InventoryBatch::lockForUpdate()->findOrFail($lineData['batchID']);
                $expectedQuantity = $batch->current_quantity;

                CycleCountLine::create([
                    'countID' => $cycleCount->countID,
                    'batchID' => $batch->batchID,
                    'expected_quantity' => $expectedQuantity,
                    'actual_quantity' => $expectedQuantity,
                ]);
            }
        });

        return redirect()->route('cycle-counts.index')
            ->with('success', 'Cycle Count planned successfully.');
    }

    public function show(CycleCount $cycleCount)
    {
        $cycleCount->load([
            'user',
            'cycleCountLines.batch.item',
            'cycleCountLines.batch.location',
            'cycleCountLines.inventoryAdjustment.user',
        ]);

        return view('cycle_counts.show', compact('cycleCount'));
    }

    public function edit(CycleCount $cycleCount)
    {
        if ($cycleCount->status === 'Completed') {
            return redirect()->route('cycle-counts.show', $cycleCount);
        }

        $cycleCount->load(['cycleCountLines.batch.item', 'cycleCountLines.inventoryAdjustment']);
        $batches = Schema::hasTable('inventory_batches') && Schema::hasTable('items')
            ? InventoryBatch::with('item')->orderBy('itemID')->orderBy('expiration_date')->get()
            : collect();

        return view('cycle_counts.edit', compact('cycleCount', 'batches'));
    }

    public function update(Request $request, CycleCount $cycleCount)
    {
        $validated = $request->validate([
            'lines' => 'required|array|min:1',
            'lines.*.batchID' => 'required|exists:inventory_batches,batchID',
            'lines.*.quantity_changed' => 'required|integer',
            'lines.*.reason' => 'nullable|string|max:255',
            'lines.*.reason_other' => 'nullable|string|max:255',
        ]);

        foreach ($validated['lines'] as $lineData) {
            if ((int) $lineData['quantity_changed'] !== 0 && empty($lineData['reason'])) {
                return back()->withInput()->withErrors(['lines' => 'Choose a reason for every non-zero quantity change.']);
            }

            if (($lineData['reason'] ?? null) === 'Others' && empty($lineData['reason_other'])) {
                return back()->withInput()->withErrors(['lines' => 'Enter a reason when Others is selected.']);
            }
        }

        $cycleCount->load('cycleCountLines.inventoryAdjustment');

        if ($cycleCount->status === 'Completed' || $cycleCount->cycleCountLines->contains(fn ($line) => $line->inventoryAdjustment)) {
            return redirect()->route('cycle-counts.show', $cycleCount)
                ->with('error', 'Completed cycle counts cannot be changed.');
        }

        DB::transaction(function () use ($cycleCount, $validated): void {
            $cycleCount->update(['status' => 'Completed']);

            $cycleCount->cycleCountLines()->delete();

            foreach ($validated['lines'] as $lineData) {
                $batch = InventoryBatch::lockForUpdate()->findOrFail($lineData['batchID']);
                $expectedQuantity = $batch->current_quantity;
                $quantityChanged = (int) $lineData['quantity_changed'];
                $actualQuantity = $expectedQuantity + $quantityChanged;

                if ($actualQuantity < 0) {
                    throw new \RuntimeException('Actual quantity cannot be negative for '.$batch->item->name.'.');
                }

                $line = CycleCountLine::create([
                    'countID' => $cycleCount->countID,
                    'batchID' => $batch->batchID,
                    'expected_quantity' => $expectedQuantity,
                    'actual_quantity' => $actualQuantity,
                ]);

                if ($quantityChanged !== 0) {
                    $reason = $lineData['reason'] === 'Others'
                        ? ($lineData['reason_other'] ?: 'Other cycle count adjustment')
                        : ($lineData['reason'] ?: 'Cycle count #'.str_pad((string) $cycleCount->countID, 5, '0', STR_PAD_LEFT));

                    $this->applyCycleCountAdjustment(
                        $line,
                        $quantityChanged,
                        $reason
                    );
                }
            }
        });

        return redirect()->route('cycle-counts.show', $cycleCount)
            ->with('success', 'Cycle Count completed successfully.');
    }

    private function applyCycleCountAdjustment(CycleCountLine $line, int $quantityChanged, string $reason): void
    {
        InventoryAdjustment::create([
            'batchID' => $line->batchID,
            'userID' => auth()->id(),
            'adjustment_date' => now()->toDateString(),
            'quantity_changed' => $quantityChanged,
            'reason' => $reason,
            'cycle_count_lineID' => $line->lineID,
        ]);

        InventoryBatch::whereKey($line->batchID)->increment('current_quantity', $quantityChanged);
    }
}
