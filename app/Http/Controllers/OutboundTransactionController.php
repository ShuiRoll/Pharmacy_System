<?php

namespace App\Http\Controllers;

use App\Models\OutboundTransaction;
use App\Models\OutboundLineItem;
use App\Models\InventoryBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OutboundTransactionController extends Controller
{
    public function index()
    {
        return redirect()->route('inventory-adjustments.index', ['filter' => 'outbound']);
    }

    public function create()
    {
        $batches = Schema::hasTable('inventory_batches') && Schema::hasTable('items')
            ? InventoryBatch::with('item')->where('current_quantity', '>', 0)->get()
            : collect();
        return view('outbound.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'destination' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.batch_id' => 'required|exists:inventory_batches,batchID',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $outbound = OutboundTransaction::create([
                'userID' => auth()->id(),
                'transaction_date' => $request->transaction_date,
                'destination' => $request->destination,
                'status' => 'Pending',
                'total_amount' => 0,
            ]);

            $totalAmount = 0;

            foreach ($request->items as $data) {
                $batch = InventoryBatch::with('item')->lockForUpdate()->findOrFail($data['batch_id']);

                if ($batch->current_quantity < $data['quantity']) {
                    throw new \Exception("Insufficient stock in batch {$batch->lot_number}");
                }

                $unitPrice = $batch->item->price ?? 0;
                $lineTotal = $unitPrice * $data['quantity'];
                $totalAmount += $lineTotal;

                OutboundLineItem::create([
                    'out_transactionID' => $outbound->out_transactionID,
                    'batchID' => $data['batch_id'],
                    'quantity_dispensed' => $data['quantity'],
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);
            }

            $outbound->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('inventory-adjustments.index', ['filter' => 'outbound'])
                ->with('success', 'Outbound transaction created as pending.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(OutboundTransaction $outbound)
    {
        $outbound->load('user', 'outboundLineItems.batch.item');
        return view('outbound.show', compact('outbound'));
    }

    public function ship(OutboundTransaction $outbound)
    {
        return $this->approve($outbound);
    }

    public function approve(OutboundTransaction $outbound)
    {
        if ($outbound->status !== 'Pending') {
            return redirect()->back()->with('error', 'Only pending outbound transactions can be approved.');
        }

        $outbound->update(['status' => 'Approved']);

        return redirect()->route('inventory-adjustments.index', ['filter' => 'outbound'])->with('success', 'Outbound transaction approved.');
    }

    public function deliver(OutboundTransaction $outbound)
    {
        if ($outbound->status === 'Transferred') {
            return redirect()->back()->with('error', 'Outbound transaction has already been transferred.');
        }

        if ($outbound->status !== 'Approved') {
            return redirect()->back()->with('error', 'Approve the outbound transaction before transferring stock.');
        }

        DB::transaction(function () use ($outbound): void {
            $outbound->load('outboundLineItems.batch');

            foreach ($outbound->outboundLineItems as $line) {
                $batch = InventoryBatch::lockForUpdate()->findOrFail($line->batchID);

                if ($batch->current_quantity < $line->quantity_dispensed) {
                    throw new \RuntimeException("Insufficient stock in batch {$batch->lot_number}");
                }

                $batch->decrement('current_quantity', $line->quantity_dispensed);
            }

            $outbound->update(['status' => 'Transferred']);
        });

        return redirect()->route('inventory-adjustments.index', ['filter' => 'outbound'])->with('success', 'Outbound stock transferred.');
    }
}
