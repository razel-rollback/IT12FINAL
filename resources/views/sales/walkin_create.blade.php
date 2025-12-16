@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50/40 to-slate-100 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900 flex items-center gap-2">
                    <span
                        class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-sm">
                        <i class="fas fa-user-friends text-sm"></i>
                    </span>
                    Walk-in Sale
                </h1>
                <p class="mt-1 text-xs md:text-sm text-slate-500">
                    Process a sale for a customer without creating a customer record in the system.
                </p>
            </div>
            <a href="{{ route('sales.index') }}"
               class="hidden sm:inline-flex items-center gap-2 text-xs md:text-sm text-slate-600 hover:text-slate-900">
                <i class="fas fa-arrow-left"></i>
                Back to Sales
            </a>
        </div>

        <div class="relative rounded-2xl border border-slate-100 bg-white/90 shadow-lg shadow-emerald-500/5 backdrop-blur-sm p-6 md:p-7">

            {{-- Errors --}}
            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-red-700 text-sm shadow-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-red-700 text-sm shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Info --}}
            <div
                class="mb-6 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-xs md:text-sm text-blue-800 flex gap-3">
                <div
                    class="mt-0.5 flex h-7 w-7 items-center justify-center rounded-full bg-blue-500/10 text-blue-600">
                    <i class="fas fa-info text-[11px]"></i>
                </div>
                <p>
                    <strong>Walk-in Customer:</strong> Use this form for customers who do not have a saved profile.
                    Only the sale and products will be recorded.
                </p>
            </div>

            <form action="{{ route('sales.walkIn.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Cashier & payment --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Cashier
                        </label>
                        <div
                            class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 flex items-center gap-2">
                            <i class="fas fa-user-circle text-slate-400 text-xs"></i>
                            <input type="text"
                                   value="{{ Auth::user()->fname }} {{ Auth::user()->lname }}"
                                   class="w-full bg-transparent border-none focus:ring-0 focus:outline-none"
                                   readonly>
                        </div>
                        <input type="hidden" name="User_ID" value="{{ Auth::user()->User_ID }}">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Payment Method <span class="text-red-500">*</span>
                        </label>
                        <div
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <i class="fas fa-wallet text-slate-400 text-xs"></i>
                            <select name="payment_method"
                                    class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                    required>
                                <option value="">Select Payment Method</option>
                                <option value="Cash">Cash</option>
                                <option value="GCash">GCash</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Products --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-sm md:text-base font-semibold text-slate-800">
                            Products
                        </h2>
                        <span class="text-[11px] text-slate-400">
                            Add one or more items to this sale.
                        </span>
                    </div>

                    <div id="products-wrapper" class="space-y-3">

                        {{-- PRODUCT ROW TEMPLATE --}}
                        <div class="product-row rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">

                                {{-- Product --}}
                                <div class="lg:col-span-2">
                                    <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                                        Product <span class="text-red-500">*</span>
                                    </label>
                                    <div
                                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                                        <i class="fas fa-tag text-slate-400 text-xs"></i>
                                        <select name="products[0][Product_ID]"
                                                class="product-select w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                                required>
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                @php
                                                    $isExpired = false;
                                                    if($product->expiry_date) {
                                                        $expiryDate = \Carbon\Carbon::parse($product->expiry_date);
                                                        $daysLeft = (int) \Carbon\Carbon::now()->diffInDays($expiryDate, false);
                                                        $isExpired = $daysLeft <= 0;
                                                    }
                                                    $outOfStock = $product->Quantity_in_Stock <= 0;
                                                    $varietyText = $product->variety ? ' - ' . $product->variety : '';
                                                @endphp
                                                <option value="{{ $product->Product_ID }}"
                                                        data-price="{{ $product->unit_price }}"
                                                        data-stock="{{ $product->Quantity_in_Stock }}"
                                                        @if($isExpired || $outOfStock) disabled @endif>
                                                    {{ $product->Product_Name }}{{ $varietyText }}
                                                    (Stock: {{ $product->Quantity_in_Stock }})
                                                    @if($isExpired) - EXPIRED @endif
                                                    @if($outOfStock) - OUT OF STOCK @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Quantity --}}
                                <div>
                                    <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                                        Quantity <span class="text-red-500">*</span>
                                    </label>
                                    <div
                                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                                        <i class="fas fa-boxes-stacked text-slate-400 text-xs"></i>
                                        <input type="number"
                                               name="products[0][Quantity]"
                                               class="quantity-input w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                               min="1" step="1" placeholder="e.g. 5" required>
                                    </div>
                                    <p class="mt-1 text-[11px] text-slate-500">Whole numbers only.</p>
                                </div>

                                {{-- Kilo --}}
                                <div>
                                    <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                                        Kilo <span class="text-red-500">*</span>
                                    </label>
                                    <div
                                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                                        <i class="fas fa-weight-hanging text-slate-400 text-xs"></i>
                                        <input type="number"
                                               name="products[0][Kilo]"
                                               class="kilo-input w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                               min="0.1" step="0.1" placeholder="e.g. 2.5" required>
                                    </div>
                                    <p class="mt-1 text-[11px] text-slate-500">Decimals allowed.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                                {{-- Price per kilo --}}
                                <div>
                                    <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                                        Price per Kilo <span class="text-red-500">*</span>
                                    </label>
                                    <div
                                        class="rounded-xl border border-slate-200 bg-slate-100 px-3 py-2 flex items-center gap-2">
                                        <span class="text-[11px] font-semibold text-slate-500">₱</span>
                                        <input type="number"
                                               name="products[0][Price]"
                                               class="price-input w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                               min="0" step="0.01" readonly required>
                                    </div>
                                </div>

                                {{-- Line total --}}
                                <div>
                                    <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                                        Line Total
                                    </label>
                                    <div class="mt-2 text-lg font-bold text-emerald-700">
                                        ₱<span class="total-display">0.00</span>
                                    </div>
                                </div>

                                {{-- Remove --}}
                                <div class="flex items-end">
                                    <button type="button"
                                            class="remove-btn w-full inline-flex items-center justify-center rounded-xl bg-red-500 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-600 transition">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                        {{-- END PRODUCT ROW TEMPLATE --}}

                    </div>

                    <div class="mt-3">
                        <button type="button" id="addProductBtn"
                                class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                            <i class="fas fa-plus mr-2 text-xs"></i>
                            Add Another Product
                        </button>
                    </div>
                </div>

                {{-- Grand total --}}
                <div class="mt-4 flex justify-end">
                    <div class="rounded-2xl bg-slate-50 px-5 py-3 border border-slate-200">
                        <span class="text-xs font-medium uppercase tracking-wide text-slate-500">Grand Total</span>
                        <div class="mt-1 text-2xl font-bold text-slate-900">
                            ₱<span id="grandTotal">0.00</span>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3">
                    <a href="{{ route('sales.index') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-medium text-slate-600 hover:bg-slate-50">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-8 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1">
                        <span class="mr-2">💾</span>
                        Save Walk-in Sale
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const wrapper = document.getElementById('products-wrapper');
    const addBtn = document.getElementById('addProductBtn');
    const grandTotalSpan = document.getElementById('grandTotal');
    let productIndex = 1;

    function updateTotals() {
        let grandTotal = 0;
        wrapper.querySelectorAll('.product-row').forEach(row => {
            const kilo = parseFloat(row.querySelector('.kilo-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const total = kilo * price;
            row.querySelector('.total-display').textContent = total.toFixed(2);
            grandTotal += total;
        });
        grandTotalSpan.textContent = grandTotal.toFixed(2);
    }

    wrapper.addEventListener('input', (e) => {
        if(
            e.target.classList.contains('kilo-input') ||
            e.target.classList.contains('price-input') ||
            e.target.classList.contains('quantity-input')
        ) {
            updateTotals();
        }
    });

    wrapper.addEventListener('change', (e) => {
        if(e.target.classList.contains('product-select')) {
            const row = e.target.closest('.product-row');
            const selectedOption = e.target.selectedOptions[0];
            const priceInput = row.querySelector('.price-input');

            if(selectedOption && selectedOption.dataset.price) {
                priceInput.value = selectedOption.dataset.price;
                updateTotals();
            }
        }
    });

    addBtn.addEventListener('click', () => {
        const newRow = wrapper.querySelector('.product-row').cloneNode(true);

        // Clear inputs
        newRow.querySelectorAll('input').forEach(input => {
            input.value = '';
        });
        newRow.querySelectorAll('select').forEach(select => {
            select.value = '';
        });
        newRow.querySelector('.total-display').textContent = '0.00';

        // Update name attributes index 0 -> productIndex
        const inputs = newRow.querySelectorAll('input, select');
        inputs.forEach(input => {
            if(input.name) {
                input.name = input.name.replace(/\[0\]/, '[' + productIndex + ']');
            }
        });

        wrapper.appendChild(newRow);
        productIndex++;
    });

    wrapper.addEventListener('click', (e) => {
        if(e.target.classList.contains('remove-btn')) {
            if(wrapper.querySelectorAll('.product-row').length > 1) {
                e.target.closest('.product-row').remove();
                updateTotals();
            } else {
                alert('You must have at least one product in the sale.');
            }
        }
    });

    updateTotals();
});
</script>
@endsection
