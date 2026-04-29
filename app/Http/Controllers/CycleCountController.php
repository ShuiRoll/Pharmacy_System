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
            'status' => 'required|in:In Progress,Completed',
            'lines' => 'required|array|min:1',
            'lines.*.batchID' => 'required|exists:inventory_batches,batchID',
            'lines.*.quantity_changed' => 'required|integer',
        ]);

        DB::transaction(function () use ($validated): void {
            $cycleCount = CycleCount::create([
                'userID' => auth()->id(),
                'count_date' => $validated['count_date'],
                'status' => $validated['status'],
            ]);

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

                if ($validated['status'] === 'Completed' && $quantityChanged !== 0) {
                    $this->applyCycleCountAdjustment($line, $quantityChanged);
                }
            }
        });

        return redirect()->route('cycle-counts.index')
            ->with('success', 'Cycle Count started successfully.');
    }

    public function edit(CycleCount $cycleCount)
    {
        $cycleCount->load(['cycleCountLines.batch.item', 'cycleCountLines.inventoryAdjustment']);
        $batches = Schema::hasTable('inventory_batches') && Schema::hasTable('items')
            ? InventoryBatch::with('item')->orderBy('itemID')->orderBy('expiration_date')->get()
            : collect();

        return view('cycle_counts.edit', compact('cycleCount', 'batches'));
    }

    public function update(Request $request, CycleCount $cycleCount)
    {
        $validated = $request->validate([
            'count_date' => 'required|date',
            'status' => 'required|in:In Progress,Completed',
            'lines' => 'required|array|min:1',
            'lines.*.batchID' => 'required|exists:inventory_batches,batchID',
            'lines.*.quantity_changed' => 'required|integer',
        ]);

        DB::transaction(function () use ($cycleCount, $validated): void {
            $cycleCount->load('cycleCountLines.inventoryAdjustment');

            if ($cycleCount->cycleCountLines->contains(fn ($line) => $line->inventoryAdjustment)) {
                $cycleCount->update([
                    'count_date' => $validated['count_date'],
                    'status' => $cycleCount->status,
                ]);

                return;
            }

            $cycleCount->update([
                'count_date' => $validated['count_date'],
                'status' => $validated['status'],
            ]);

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

                if ($validated['status'] === 'Completed' && $quantityChanged !== 0) {
                    $this->applyCycleCountAdjustment($line, $quantityChanged);
                }
            }
        });

        return redirect()->route('cycle-counts.index')
            ->with('success', 'Cycle Count updated successfully.');
    }

    private function applyCycleCountAdjustment(CycleCountLine $line, int $quantityChanged): void
    {
        InventoryAdjustment::create([
            'batchID' => $line->batchID,
            'userID' => auth()->id(),
            'adjustment_date' => now()->toDateString(),
            'quantity_changed' => $quantityChanged,
            'reason' => 'Cycle count #'.str_pad((string) $line->countID, 5, '0', STR_PAD_LEFT),
            'cycle_count_lineID' => $line->lineID,
        ]);

        InventoryBatch::whereKey($line->batchID)->increment('current_quantity', $quantityChanged);
    }
}
