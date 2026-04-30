@extends('layouts.app')

@section('page_title', 'New Sale')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">POS</h1>
            <p class="mt-1 text-sm text-slate-300">Product-first checkout for {{ auth()->user()->name }}.</p>
        </div>
        <button type="button" onclick="openCheckout()" class="btn btn-primary relative">
            <i class="fas fa-cart-shopping"></i>
            Checkout
            <span id="cart-count" class="rounded bg-white/20 px-2 py-0.5 text-xs">0</span>
        </button>
    </div>

    <div class="app-panel p-4 sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-semibold text-white">Products</h2>
            <input type="text" id="search-input" placeholder="Search medicine or SKU..." class="form-input sm:max-w-sm" onkeyup="filterProducts()">
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" id="product-grid">
            @foreach($items as $item)
                @php
                    $availableStock = $item->inventoryBatches->sum('current_quantity');
                    $earliestBatch = $item->inventoryBatches->first();
                @endphp
                @if($availableStock > 0 && $earliestBatch)
                    <article class="product-card app-card p-4"
                             data-name="{{ strtolower($item->name) }}"
                             data-code="{{ strtolower($item->item_code) }}">
                        <div class="flex h-full flex-col">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-semibold text-white">{{ $item->name }}</h3>
                                    <p class="mt-1 font-mono text-xs text-slate-400">{{ $item->item_code }}</p>
                                </div>
                                <p class="text-lg font-bold text-white">&#8369;{{ number_format($item->price, 2) }}</p>
                                <p class="text-[11px] uppercase tracking-[0.2em] text-slate-400">SRP</p>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-2 text-xs text-slate-300">
                                <div class="rounded-lg bg-slate-950/50 p-3">
                                    <p class="text-slate-500">Stock</p>
                                    <p class="mt-1 text-base font-semibold text-white">{{ $availableStock }}</p>
                                </div>
                                <div class="rounded-lg bg-slate-950/50 p-3">
                                    <p class="text-slate-500">Batch</p>
                                    <p class="mt-1 truncate font-semibold text-white">{{ $earliestBatch->lot_number ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="mt-auto flex gap-2 pt-4">
                                <input type="number" id="qty-{{ $item->itemID }}" value="1" min="1" max="{{ $availableStock }}" class="form-input w-24 text-center">
                                <button type="button" onclick="addToCart({{ $item->itemID }}, '{{ addslashes($item->name) }}', {{ $item->price }}, {{ $availableStock }})" class="btn btn-secondary flex-1">
                                    <i class="fas fa-plus"></i>
                                    Add
                                </button>
                            </div>
                        </div>
                    </article>
                @endif
            @endforeach
        </div>
    </div>
</div>

<div id="checkout-modal" class="fixed inset-0 z-50 hidden bg-slate-950/80 p-4 backdrop-blur">
    <div class="mx-auto flex min-h-full max-w-3xl items-center justify-center">
        <div class="app-panel w-full p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-white">Order Details</h2>
                    <p class="mt-1 text-sm text-slate-300">Review the cart before completing the sale.</p>
                </div>
                <button type="button" onclick="closeCheckout()" class="btn btn-secondary" aria-label="Close checkout">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>

            <div id="cart-items" class="mt-5 max-h-72 space-y-3 overflow-y-auto"></div>

            <div class="mt-5 rounded-lg border border-white/10 bg-white/5 p-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-300">Subtotal</span>
                        <span id="cart-subtotal" class="font-semibold text-white">&#8369;0.00</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-300">VAT ({{ number_format($taxRate * 100, 0) }}%)</span>
                        <span id="cart-tax" class="font-semibold text-white">&#8369;0.00</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-white/10 pt-3">
                        <span class="text-sm text-slate-300">Total Amount</span>
                        <span id="cart-total" class="text-3xl font-bold text-white">&#8369;0.00</span>
                    </div>
                </div>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-200">Payment Method</label>
                    <select id="payment-method-select" onchange="selectPayment()" class="form-input">
                        <option value="Cash">Cash</option>
                        <option value="GCash">GCash</option>
                        <option value="Card">Card</option>
                    </select>
                </div>

                <div id="gcash-panel" class="hidden rounded-lg border border-cyan-400/30 bg-cyan-400/10 p-4">
                    <div class="flex items-center gap-4">
                        <div class="grid h-24 w-24 grid-cols-6 grid-rows-6 gap-1 rounded bg-white p-2">
                            @foreach([1,1,1,0,1,1,1,0,1,0,0,1,1,1,1,0,1,0,0,1,0,1,1,1,1,0,1,0,0,1,1,1,0,1,1,1] as $cell)
                                <span class="{{ $cell ? 'bg-slate-950' : 'bg-white' }}"></span>
                            @endforeach
                        </div>
                        <div class="flex-1">
                            <label class="mb-2 block text-sm font-medium text-cyan-100">GCash Reference</label>
                            <input id="gcash-reference" type="text" class="form-input" placeholder="Reference number">
                        </div>
                    </div>
                </div>

                <div id="card-panel" class="hidden">
                    <label class="mb-2 block text-sm font-medium text-slate-200">Card ID / Approval Code</label>
                    <input id="card-reference" type="text" class="form-input" placeholder="Card reference">
                </div>
            </div>

            <form id="sale-form" action="{{ route('sales.store') }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="payment_method" id="payment-method-input" value="Cash">
                <input type="hidden" name="gcash_reference" id="gcash-reference-input">
                <input type="hidden" name="card_reference" id="card-reference-input">
                <div id="sale-line-inputs"></div>
            </form>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <button type="button" onclick="completeSale()" class="btn btn-primary flex-1">Complete Sale</button>
                <button type="button" onclick="clearCart()" class="btn btn-danger flex-1">Clear Order</button>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];
const TAX_RATE = {{ json_encode($taxRate) }};

function peso(value) {
    return `&#8369;${Number(value || 0).toFixed(2)}`;
}

function addToCart(itemID, name, price, maxQty) {
    const qtyInput = document.getElementById(`qty-${itemID}`);
    let quantity = parseInt(qtyInput.value) || 1;

    if (quantity > maxQty) {
        alert(`Only ${maxQty} units available.`);
        quantity = maxQty;
    }

    const existing = cart.find(item => item.itemID === itemID);

    if (existing) {
        existing.quantity = Math.min(existing.quantity + quantity, maxQty);
    } else {
        cart.push({ itemID, name, price, quantity, maxQty });
    }

    qtyInput.value = 1;
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cart-items');
    container.innerHTML = '';

    let subtotal = 0;

    if (!cart.length) {
        container.innerHTML = '<div class="rounded-lg border border-dashed border-white/20 p-6 text-center text-sm text-slate-300">No products in the cart.</div>';
    }

    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;

        const row = document.createElement('div');
        row.className = 'grid grid-cols-[1fr_auto_auto] items-center gap-3 rounded-lg border border-white/10 bg-white/5 p-4';
        row.innerHTML = `
            <div>
                <p class="font-semibold text-white">${item.name}</p>
                <p class="text-xs text-slate-400">${item.quantity} x ${peso(item.price)}</p>
            </div>
            <p class="font-semibold text-white">${peso(itemTotal)}</p>
            <button type="button" onclick="removeFromCart(${index})" class="btn btn-danger" aria-label="Remove ${item.name}">
                <i class="fas fa-xmark"></i>
            </button>
        `;
        container.appendChild(row);
    });

    const taxAmount = subtotal * TAX_RATE;
    const total = subtotal + taxAmount;

    document.getElementById('cart-subtotal').innerHTML = peso(subtotal);
    document.getElementById('cart-tax').innerHTML = peso(taxAmount);
    document.getElementById('cart-total').innerHTML = peso(total);
    document.getElementById('cart-count').textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
}

function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
}

function clearCart() {
    if (!cart.length || confirm('Clear the entire order?')) {
        cart = [];
        renderCart();
    }
}

function openCheckout() {
    renderCart();
    document.getElementById('checkout-modal').classList.remove('hidden');
}

function closeCheckout() {
    document.getElementById('checkout-modal').classList.add('hidden');
}

function selectPayment() {
    const method = document.getElementById('payment-method-select').value;
    document.getElementById('gcash-panel').classList.toggle('hidden', method !== 'GCash');
    document.getElementById('card-panel').classList.toggle('hidden', method !== 'Card');
}

function completeSale() {
    if (!cart.length) {
        alert('Please add at least one item to the cart.');
        return;
    }

    const method = document.getElementById('payment-method-select').value;
    const gcashReference = document.getElementById('gcash-reference').value.trim();
    const cardReference = document.getElementById('card-reference').value.trim();

    if (method === 'GCash' && !gcashReference) {
        alert('Enter the GCash reference number.');
        return;
    }

    if (method === 'Card' && !cardReference) {
        alert('Enter the card ID or approval code.');
        return;
    }

    if (confirm('Complete this sale?')) {
        const lineInputs = document.getElementById('sale-line-inputs');
        document.getElementById('payment-method-input').value = method;
        document.getElementById('gcash-reference-input').value = gcashReference;
        document.getElementById('card-reference-input').value = cardReference;
        lineInputs.innerHTML = '';

        cart.forEach((item, index) => {
            lineInputs.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="items[${index}][item_id]" value="${item.itemID}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
            `);
        });

        document.getElementById('sale-form').submit();
    }
}

function filterProducts() {
    const term = document.getElementById('search-input').value.toLowerCase().trim();
    document.querySelectorAll('.product-card').forEach(card => {
        const name = card.getAttribute('data-name') || '';
        const code = card.getAttribute('data-code') || '';
        card.style.display = (name.includes(term) || code.includes(term)) ? 'block' : 'none';
    });
}

window.addEventListener('load', renderCart);
</script>
@endsection
