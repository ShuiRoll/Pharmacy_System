<?php

namespace App\Http\Controllers;

use App\Models\InboundTransaction;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SupplierController extends Controller
{
    public function index()
    {
        $pendingOrdersCount = Schema::hasTable('purchase_orders')
            ? PurchaseOrder::where('status', 'Pending')->count()
            : 0;

        $suppliers = Schema::hasTable('suppliers')
            ? Supplier::latest()->get()
            : collect();

        $purchaseOrders = Schema::hasTable('purchase_orders') && Schema::hasTable('suppliers')
            ? PurchaseOrder::with('supplier')
                ->where('status', '!=', 'Received')
                ->orderByDesc('po_date')
                ->paginate(8)
            : collect();

        $inbounds = Schema::hasTable('inbound_transactions') && Schema::hasTable('inbound_line_items') && Schema::hasTable('items')
            ? InboundTransaction::with(['user', 'purchaseOrder.supplier', 'inboundLineItems.item'])->orderByDesc('date_received')->get()
            : collect();

        return view('suppliers.index', compact('suppliers', 'purchaseOrders', 'inbounds', 'pendingOrdersCount'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
        ]);

        Supplier::create($validated);

        return redirect()->to(route('suppliers.index').'#suppliers')
            ->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
        ]);

        $supplier->update($validated);

        return redirect()->to(route('suppliers.index').'#suppliers')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->to(route('suppliers.index').'#suppliers')
            ->with('success', 'Supplier deleted successfully.');
    }
}
