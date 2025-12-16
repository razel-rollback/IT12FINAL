@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50/40 to-slate-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Page header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900 flex items-center gap-2">
                    <span
                        class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-sm">
                        <i class="fas fa-box-open text-sm"></i>
                    </span>
                    Add Stock
                </h1>
                <p class="mt-1 text-xs md:text-sm text-slate-500">
                    Record new inventory from completed supplier transactions with smart limits and autofill.
                </p>
            </div>
            <a href="{{ route('stockins.index') }}"
               class="hidden sm:inline-flex items-center gap-2 text-xs md:text-sm text-slate-600 hover:text-slate-900">
                <i class="fas fa-arrow-left"></i>
                Back to Stock List
            </a>
        </div>

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-red-700 text-sm shadow-sm">
                <div class="flex">
                    <div class="mt-0.5">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                    </div>
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Info box --}}
        <div
            class="mb-6 rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-50 to-emerald-50 px-4 py-3 shadow-sm">
            <div class="flex gap-3">
                <div
                    class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-full bg-blue-500/10 text-blue-600">
                    <i class="fas fa-info"></i>
                </div>
                <div class="text-xs md:text-sm text-slate-700">
                    <p class="font-semibold text-slate-800">Smart supplier-linked stock in</p>
                    <p class="mt-1">
                        Select a product that has
                        <span class="font-semibold text-blue-700">completed supplier transactions</span>
                        to auto-fill quantity and unit price. Quantity is capped to the remaining supplied amount to
                        prevent over-stocking.
                    </p>
                </div>
            </div>
        </div>

        {{-- Main card --}}
        <div
            class="relative overflow-hidden rounded-2xl border border-slate-100 bg-white/90 shadow-lg shadow-emerald-500/5 backdrop-blur-sm">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-emerald-100/60 blur-2xl"></div>
            <div class="absolute -bottom-10 -left-10 h-32 w-32 rounded-full bg-sky-100/60 blur-2xl"></div>

            <form action="{{ route('stockins.store') }}" method="POST" class="relative p-6 md:p-7" id="stockin_form">
                @csrf

                <input type="hidden" name="supplier_transaction_id" id="supplier_transaction_id" value="">

                {{-- Grid layout --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Product --}}
                    <div class="md:col-span-2">
                        <label class="flex items-center justify-between text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            <span>Product</span>
                            <span class="text-[10px] font-normal text-emerald-600">
                                Required
                            </span>
                        </label>
                        <div
                            class="relative rounded-xl border border-slate-200 bg-white px-3 py-2.5 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-tags text-slate-400 text-xs"></i>
                                <select name="Product_ID" id="product_select"
                                        class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                        required>
                                    <option value="">-- Select Product --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->Product_ID }}">
                                            {{ $product->Product_Name }}@if($product->variety)
                                                - {{ $product->variety }}@endif ({{ $product->Category }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Date --}}
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Date
                        </label>
                        <div
                            class="relative rounded-xl border border-slate-200 bg-white px-3 py-2.5 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <i class="fas fa-calendar-alt text-slate-400 text-xs"></i>
                            <input type="date" name="date" id="date_input"
                                   value="{{ date('Y-m-d') }}"
                                   class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                   required>
                        </div>
                    </div>

                    {{-- Quantity --}}
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Quantity
                        </label>
                        <div
                            class="relative rounded-xl border border-slate-200 bg-white px-3 py-2.5 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <i class="fas fa-layer-group text-slate-400 text-xs"></i>
                            <input type="number" step="0.01" name="quantity" id="quantity_input"
                                   class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                   min="0.01" required>
                            <span class="text-[11px] text-slate-400 border-l border-slate-200 pl-2">
                                max linked
                            </span>
                        </div>
                        <p id="quantity_note" class="text-[11px] text-emerald-600 mt-1.5"></p>
                        <p id="quantity_warning" class="text-[11px] text-red-600 mt-1.5 hidden"></p>
                        <p id="no_stock_warning" class="text-[11px] text-red-600 font-semibold mt-1 hidden"></p>
                    </div>

                    {{-- Price --}}
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Price per Unit
                        </label>
                        <div
                            class="relative rounded-xl border border-slate-200 bg-white px-3 py-2.5 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <span class="text-[11px] text-slate-400">₱</span>
                            <input type="number" step="0.01" name="price" id="price_input"
                                   class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                   min="0" required>
                            <span class="text-[11px] text-slate-400 border-l border-slate-200 pl-2">
                                auto / custom
                            </span>
                        </div>
                        <p id="price_note" class="text-[11px] text-emerald-600 mt-1.5"></p>
                    </div>

                    {{-- Unit --}}
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Unit
                            <span class="text-[10px] font-normal text-slate-400 ml-1">(e.g., kg, pcs, box)</span>
                        </label>
                        <div
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <i class="fas fa-ruler-combined text-slate-400 text-xs"></i>
                            <input type="text" name="unit" value="kg"
                                   class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                   required>
                        </div>
                    </div>

                    {{-- Expiry date --}}
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Expiry Date <span class="text-[10px] font-normal text-slate-400">(optional)</span>
                        </label>
                        <div
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <i class="fas fa-hourglass-half text-slate-400 text-xs"></i>
                            <input type="date" name="expiry_date"
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                   class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none">
                        </div>
                    </div>

                    {{-- Critical level --}}
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Critical Level
                        </label>
                        <div
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <i class="fas fa-exclamation-triangle text-amber-500 text-xs"></i>
                            <input type="number" name="critical_level" value="5"
                                   class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                   min="0" required>
                        </div>
                    </div>
                </div>

                {{-- Footer actions --}}
                <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2 text-[11px] text-slate-400">
                        <i class="fas fa-shield-alt text-emerald-500"></i>
                        <span>Linked to supplier transactions to prevent duplicate stock-in.</span>
                    </div>

                    <div class="flex gap-2 w-full sm:w-auto justify-end">
                        <a href="{{ route('stockins.index') }}"
                           class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs md:text-sm font-medium text-slate-600 hover:bg-slate-50">
                            Cancel
                        </a>
                        <button type="submit" id="submit_btn"
                                class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-xs md:text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1">
                            <i class="fas fa-plus mr-2 text-[11px]"></i>
                            Add Stock
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
const productSelect = document.getElementById('product_select');
const quantityInput = document.getElementById('quantity_input');
const priceInput = document.getElementById('price_input');
const dateInput = document.getElementById('date_input');
const quantityNote = document.getElementById('quantity_note');
const priceNote = document.getElementById('price_note');
const quantityWarning = document.getElementById('quantity_warning');
const noStockWarning = document.getElementById('no_stock_warning');
const submitBtn = document.getElementById('submit_btn');
const stockinForm = document.getElementById('stockin_form');
const supplierTransactionIdInput = document.getElementById('supplier_transaction_id');

// Latest supplier transactions data from controller
const latestTransactions = @json($latestSupplierTransactions);

let maxQuantity = null;
let canAddStock = true;

console.log('Latest Completed Transactions with Remaining Qty:', latestTransactions);

function autoFillFromSupplierTransaction() {
    const productId = productSelect.value;

    // Reset UI
    quantityNote.innerHTML = '';
    priceNote.innerHTML = '';
    quantityWarning.classList.add('hidden');
    quantityWarning.innerHTML = '';
    noStockWarning.classList.add('hidden');
    noStockWarning.innerHTML = '';
    maxQuantity = null;
    canAddStock = true;

    quantityInput.value = '';
    priceInput.value = '';

    quantityInput.removeAttribute('max');
    supplierTransactionIdInput.value = '';

    quantityInput.disabled = false;
    priceInput.disabled = false;
    submitBtn.disabled = false;
    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    quantityInput.classList.remove('bg-gray-100');
    priceInput.classList.remove('bg-gray-100');

    if (productId && latestTransactions[productId]) {
        const transaction = latestTransactions[productId];

        maxQuantity = parseFloat(transaction.quantity);
        quantityInput.setAttribute('max', maxQuantity);

        supplierTransactionIdInput.value = transaction.transaction_id;

        quantityInput.value = transaction.quantity;
        quantityNote.innerHTML =
            '<span class="text-emerald-600">✓ Available: <strong>' + maxQuantity +
            '</strong> (out of ' + transaction.original_quantity + ' supplied, ' +
            transaction.already_stocked + ' already stocked)</span>';

        priceInput.value = transaction.price;
        priceNote.innerHTML =
            '<span class="text-emerald-600">✓ Price auto-filled from supplier transaction</span>';

    } else if (productId) {
        canAddStock = false;

        noStockWarning.classList.remove('hidden');
        noStockWarning.innerHTML =
            '⚠️ <strong>Cannot add stock!</strong> All supplier quantities have been used. Please create a new Supplier Transaction first.';

        quantityNote.innerHTML =
            '<span class="text-slate-500">No available supplier transaction for this product.</span>';
        priceNote.innerHTML =
            '<span class="text-slate-500">Create a Supplier Transaction to add stock.</span>';

        quantityInput.disabled = true;
        priceInput.disabled = true;
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        quantityInput.classList.add('bg-gray-100');
        priceInput.classList.add('bg-gray-100');
    }
}

quantityInput.addEventListener('input', function() {
    if (!canAddStock) {
        this.value = '';
        return;
    }

    if (maxQuantity !== null) {
        const enteredQuantity = parseFloat(this.value);

        if (enteredQuantity > maxQuantity) {
            quantityWarning.classList.remove('hidden');
            quantityWarning.innerHTML =
                '⚠️ <strong>Error:</strong> Quantity cannot exceed <strong>' +
                maxQuantity +
                '</strong> (remaining quantity from supplier transaction).';
            quantityInput.classList.add('border-red-500');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            quantityWarning.classList.add('hidden');
            quantityInput.classList.remove('border-red-500');
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    if (maxQuantity !== null && latestTransactions[productSelect.value]) {
        const transaction = latestTransactions[productSelect.value];
        const remaining = maxQuantity - (parseFloat(this.value) || 0);

        if (remaining >= 0) {
            quantityNote.innerHTML =
                '<span class="text-emerald-600">✓ Available: <strong>' +
                maxQuantity + '</strong> (out of ' + transaction.original_quantity +
                ' supplied, ' + transaction.already_stocked + ' already stocked)</span>';
        }
    }
});

stockinForm.addEventListener('submit', function(e) {
    if (!canAddStock) {
        e.preventDefault();
        alert('Error: Cannot add stock. Please create a new Supplier Transaction first.');
        return false;
    }

    if (maxQuantity !== null) {
        const enteredQuantity = parseFloat(quantityInput.value);

        if (enteredQuantity > maxQuantity) {
            e.preventDefault();
            alert(
                'Error: Quantity (' + enteredQuantity +
                ') exceeds remaining quantity (' + maxQuantity +
                '). Please adjust the quantity.'
            );
            return false;
        }
    }
});

productSelect.addEventListener('change', autoFillFromSupplierTransaction);

document.addEventListener('DOMContentLoaded', function() {
    autoFillFromSupplierTransaction();
});
</script>
@endsection
