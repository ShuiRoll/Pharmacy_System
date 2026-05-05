@extends('layouts.app')

@section('page_title', 'New Outbound Transaction')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">New Outbound Transaction</h1>
            <p class="mt-1 text-sm text-white/80">Transfer items to another location</p>
        </div>
        <span class="inline-flex w-fit items-center rounded-lg border border-amber-400/30 bg-amber-400/10 px-3 py-2 text-sm font-semibold text-amber-100">
            Pending
        </span>
    </div>

    <div class="app-panel p-6 sm:p-8">
        <form id="outbound-form" action="{{ route('outbound.store') }}" method="POST">
            @csrf

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-white">Destination <span class="text-rose-300">*</span></label>
                    <input type="text" name="destination" value="{{ old('destination') }}" placeholder="Branch 5 - Ma-a" required class="form-input">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-white">Transaction Date</label>
                    <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" class="form-input">
                </div>
            </div>

            <div class="mt-8 border-t border-white/10 pt-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Products to Transfer</h2>
                        <p class="text-sm text-white/80">Choose available batches and set quantities.</p>
                    </div>
                    <button type="button" onclick="addOutboundLine()" class="btn btn-secondary">Add Product</button>
                </div>

                <div id="outbound-lines" class="mt-5 space-y-4"></div>

                <div class="mt-6 flex justify-end">
                    <div class="w-full rounded-lg border border-white/10 bg-white/5 p-4 sm:w-80">
                        <div class="flex items-center justify-between text-sm text-white/80">
                            <span>Total Amount</span>
                            <span id="outbound-total" class="text-2xl font-bold text-white">&#8369;0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <div id="duplicate-warning" class="mt-5 hidden rounded-lg border border-amber-400/30 bg-amber-400/10 px-4 py-3 text-sm text-amber-100">
                A batch was selected more than once. Keep each product batch on one line.
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <button id="outbound-submit" type="submit" class="btn btn-primary flex-1">Create Pending Outbound</button>
                <a href="{{ route('outbound.index') }}" class="btn btn-secondary flex-1">Cancel</a>
            </div>
        </form>
    </div>
</div>

@php
    $batchOptions = $batches->map(function ($batch) {
        return [
            'id' => $batch->batchID,
            'name' => $batch->item->name ?? 'Unknown item',
            'code' => $batch->item->item_code ?? '',
            'lot' => $batch->lot_number ?? 'No lot',
            'stock' => $batch->current_quantity,
            'price' => (float) ($batch->item->price ?? 0),
        ];
    })->values();
@endphp

<script>
const batches = @json($batchOptions);
let lineIndex = 0;

function money(value) {
    return `&#8369;${Number(value || 0).toFixed(2)}`;
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (character) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[character]));
}

function addOutboundLine() {
    const container = document.getElementById('outbound-lines');
    const row = document.createElement('div');
    const index = lineIndex++;

    row.className = 'grid gap-4 rounded-lg border border-white/10 bg-white/5 p-4 md:grid-cols-12';
    row.innerHTML = `
        <div class="md:col-span-5">
            <label class="mb-1 block text-xs font-medium text-white/80">Product / Batch</label>
            <input type="hidden" name="items[${index}][batch_id]" required class="outbound-batch-value">
            <div class="relative" data-batch-picker>
                <button type="button" onclick="toggleBatchPicker(this)" class="form-input flex items-center justify-between text-left outbound-batch-button">
                    <span>Select batch</span>
                    <i class="fas fa-chevron-down text-xs text-white/80"></i>
                </button>
                <div class="absolute z-30 mt-2 hidden max-h-64 w-full overflow-y-auto rounded-lg border border-white/10 bg-slate-900 p-2 shadow-2xl shadow-black/30 outbound-batch-menu">
                    ${batches.length ? batches.map(batch => `
                        <button type="button" onclick="selectBatch(this)" data-id="${batch.id}" data-stock="${batch.stock}" data-price="${batch.price}" data-label="${escapeHtml(batch.name)} (${escapeHtml(batch.code)}) - ${escapeHtml(batch.lot)} | Stock ${batch.stock}" class="w-full rounded-lg px-3 py-2 text-left text-sm text-slate-100 transition hover:bg-blue-600 focus:bg-blue-600 focus:outline-none">
                            <span class="block font-semibold">${escapeHtml(batch.name)} (${escapeHtml(batch.code)})</span>
                            <span class="text-xs text-white/80">${escapeHtml(batch.lot)} | Stock ${batch.stock} | ${money(batch.price)}</span>
                        </button>
                    `).join('') : '<div class="px-3 py-6 text-center text-sm text-white/80">No available batches.</div>'}}
                </div>
            </div>
        </div>
        <div class="md:col-span-2">
            <label class="mb-1 block text-xs font-medium text-white/80">Quantity</label>
            <input type="number" min="1" value="1" name="items[${index}][quantity]" required oninput="syncOutboundLine(this)" class="form-input text-center outbound-quantity">
        </div>
        <div class="md:col-span-2">
            <label class="mb-1 block text-xs font-medium text-white/80">Current SRP</label>
            <div class="form-input outbound-price">&#8369;0.00</div>
        </div>
        <div class="md:col-span-2">
            <label class="mb-1 block text-xs font-medium text-white/80">Line Total</label>
            <div class="form-input outbound-line-total">&#8369;0.00</div>
        </div>
        <div class="flex items-end md:col-span-1">
            <button type="button" onclick="this.closest('.grid').remove(); updateOutboundTotals();" class="btn btn-danger w-full" aria-label="Remove line">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    `;

    container.appendChild(row);
    updateOutboundTotals();
}

function toggleBatchPicker(button) {
    const menu = button.closest('[data-batch-picker]').querySelector('.outbound-batch-menu');

    document.querySelectorAll('.outbound-batch-menu').forEach((openMenu) => {
        if (openMenu !== menu) {
            openMenu.classList.add('hidden');
        }
    });

    menu.classList.toggle('hidden');
}

function selectBatch(button) {
    const picker = button.closest('[data-batch-picker]');
    const row = button.closest('.grid');
    const valueInput = row.querySelector('.outbound-batch-value');
    const displayButton = picker.querySelector('.outbound-batch-button span');

    valueInput.value = button.dataset.id;
    valueInput.dataset.stock = button.dataset.stock;
    valueInput.dataset.price = button.dataset.price;
    displayButton.textContent = button.dataset.label;
    picker.querySelector('.outbound-batch-menu').classList.add('hidden');
    syncOutboundLine(valueInput);
}

function syncOutboundLine(element) {
    const row = element.closest('.grid');
    const batchInput = row.querySelector('.outbound-batch-value');
    const quantityInput = row.querySelector('.outbound-quantity');
    const stock = Number(batchInput?.dataset.stock || 0);
    const price = Number(batchInput?.dataset.price || 0);
    let quantity = Number(quantityInput.value || 1);

    if (stock && quantity > stock) {
        quantity = stock;
        quantityInput.value = stock;
    }

    row.querySelector('.outbound-price').innerHTML = money(price);
    row.querySelector('.outbound-line-total').innerHTML = money(price * quantity);
    updateOutboundTotals();
}

function updateOutboundTotals() {
    let total = 0;
    const selected = [];

    document.querySelectorAll('#outbound-lines > .grid').forEach(row => {
        const batchInput = row.querySelector('.outbound-batch-value');
        const quantity = Number(row.querySelector('.outbound-quantity').value || 0);
        const price = Number(batchInput?.dataset.price || 0);

        if (batchInput.value) {
            selected.push(batchInput.value);
        }

        total += price * quantity;
    });

    const hasDuplicate = selected.some((value, index) => selected.indexOf(value) !== index);
    document.getElementById('duplicate-warning').classList.toggle('hidden', !hasDuplicate);
    document.getElementById('outbound-submit').disabled = hasDuplicate || selected.length === 0;
    document.getElementById('outbound-total').innerHTML = money(total);
}

document.addEventListener('click', (event) => {
    if (!event.target.closest('[data-batch-picker]')) {
        document.querySelectorAll('.outbound-batch-menu').forEach((menu) => menu.classList.add('hidden'));
    }
});

window.addEventListener('load', () => addOutboundLine());
</script>
@endsection
