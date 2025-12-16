@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50/40 to-slate-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900 flex items-center gap-2">
                    <span
                        class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-sm">
                        <i class="fas fa-truck-loading text-sm"></i>
                    </span>
                    Add Supplier Transaction
                </h1>
                <p class="mt-1 text-xs md:text-sm text-slate-500">
                    Record a new delivery from a supplier and optionally update stock automatically when completed.
                </p>
            </div>
            <a href="{{ url()->previous() }}"
               class="hidden sm:inline-flex items-center gap-2 text-xs md:text-sm text-slate-600 hover:text-slate-900">
                <i class="fas fa-arrow-left"></i>
                Back
            </a>
        </div>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-red-700 text-sm shadow-sm">
                <div class="flex">
                    <div class="mt-0.5">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                    </div>
                    <ul class="space-y-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Main card --}}
        <div
            class="relative overflow-hidden rounded-2xl border border-slate-100 bg-white/90 shadow-lg shadow-emerald-500/5 backdrop-blur-sm">
            <div class="absolute -right-12 -top-12 h-32 w-32 rounded-full bg-emerald-100/60 blur-2xl"></div>
            <div class="absolute -bottom-12 -left-12 h-32 w-32 rounded-full bg-sky-100/60 blur-2xl"></div>

            <form method="POST" action="{{ route('supplier-transactions.store') }}"
                  class="relative p-6 md:p-7 space-y-6">
                @csrf

                {{-- Supplier & product --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Supplier
                        </label>
                        <div
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <i class="fas fa-building-store text-slate-400 text-xs"></i>
                            <select name="Supplier_ID" id="supplier-select"
                                    class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                    required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->Supplier_ID }}">{{ $supplier->Supplier_Name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Product
                        </label>
                        <div
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <i class="fas fa-tag text-slate-400 text-xs"></i>
                            <select name="Product_ID" id="product-select"
                                    class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                    required>
                                <option value="">Select Product</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Pricing & date --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Price per Kg
                        </label>
                        <div
                            class="rounded-xl border-2 border-sky-200 bg-sky-50 px-3 py-2.5 flex items-center gap-2 focus-within:border-sky-500 focus-within:ring-2 focus-within:ring-sky-100 transition">
                            <span class="text-[11px] font-semibold text-sky-700">₱</span>
                            <input type="number" name="supplier_price" id="supplier-price"
                                   step="0.01" min="0" value="0"
                                   class="w-full bg-transparent text-sm font-semibold text-sky-900 border-none focus:ring-0 focus:outline-none"
                                   required>
                        </div>
                        <p class="text-[11px] text-sky-600 mt-1">
                            Enter supplier's price per kilogram.
                        </p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Supply Date
                        </label>
                        <div
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <i class="fas fa-calendar-alt text-slate-400 text-xs"></i>
                            <input type="date" name="supply_date"
                                   value="{{ date('Y-m-d') }}"
                                   class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                   required>
                        </div>
                    </div>
                </div>

                {{-- Quantities & total --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Quantity Units
                        </label>
                        <div
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <i class="fas fa-boxes-stacked text-slate-400 text-xs"></i>
                            <input type="number" name="quantity_units" id="quantity-units"
                                   min="0" step="1" value="0"
                                   class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Quantity Kilos
                        </label>
                        <div
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <i class="fas fa-weight-hanging text-slate-400 text-xs"></i>
                            <input type="number" name="quantity_kilos" id="quantity-kilos"
                                   step="0.01" min="0" value="0"
                                   class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                   required>
                            <span class="text-[11px] text-slate-400 border-l border-slate-200 pl-2">
                                kg
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Total Cost
                        </label>
                        <input type="text" id="total-cost-display" value="₱0.00" readonly
                               class="w-full rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-right text-lg font-bold text-emerald-700 shadow-inner">
                        <input type="hidden" name="total_cost" id="total-cost-hidden" value="0">
                        <p class="text-[11px] text-slate-500 mt-1">
                            Calculated from price × total kilograms.
                        </p>
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                        Status
                    </label>
                    <div
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                        <i class="fas fa-clipboard-check text-slate-400 text-xs"></i>
                        <select name="status" id="status-select"
                                class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                required>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <p class="text-[11px] text-emerald-700 mt-1 font-semibold">
                        ⚠️ <strong>Note:</strong> Selecting "Completed" will automatically add this quantity to your stock inventory!
                    </p>
                </div>

                {{-- Submit --}}
                <div class="pt-2">
                    <button type="submit"
                            class="bg-emerald-600 text-white px-6 py-3 rounded-xl hover:bg-emerald-700 font-medium w-full shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1">
                        <i class="fas fa-save mr-2 text-xs"></i>
                        Save Transaction
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
const supplierSelect = document.getElementById('supplier-select');
const productSelect = document.getElementById('product-select');
const supplierPrice = document.getElementById('supplier-price');
const quantityUnits = document.getElementById('quantity-units');
const quantityKilos = document.getElementById('quantity-kilos');
const totalCostDisplay = document.getElementById('total-cost-display');
const totalCostHidden = document.getElementById('total-cost-hidden');
const statusSelect = document.getElementById('status-select');

let productsBySupplier = @json($productsBySupplier);

supplierSelect.addEventListener('change', function() {
    const supplierId = this.value;
    productSelect.innerHTML = '<option value="">Select Product</option>';
    
    if(productsBySupplier[supplierId]){
        productsBySupplier[supplierId].forEach(p => {
            const option = document.createElement('option');
            option.value = p.Product_ID;
            option.textContent = p.name;
            productSelect.appendChild(option);
        });
    }
    
    resetFields();
});

productSelect.addEventListener('change', function(){
    if(!this.value) {
        resetFields();
    } else {
        calculateTotal();
    }
});

supplierPrice.addEventListener('input', calculateTotal);
quantityUnits.addEventListener('input', calculateTotal);
quantityKilos.addEventListener('input', calculateTotal);

function calculateTotal(){
    const price = parseFloat(supplierPrice.value) || 0;
    const units = parseFloat(quantityUnits.value) || 0;
    const kilos = parseFloat(quantityKilos.value) || 0;

    const supplierId = supplierSelect.value;
    const productId = productSelect.value;

    let unitWeight = 0;
    if (supplierId && productId && productsBySupplier[supplierId]) {
        const selectedProduct = productsBySupplier[supplierId].find(p => p.Product_ID == productId);
        if (selectedProduct) unitWeight = parseFloat(selectedProduct.unit_weight) || 0;
    }

    const totalKilos = kilos + (units * unitWeight);
    const total = totalKilos * price;

    totalCostDisplay.value = '₱' + total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    totalCostHidden.value = total.toFixed(2);
}

function resetFields() {
    supplierPrice.value = '0';
    quantityUnits.value = '0';
    quantityKilos.value = '0';
    totalCostDisplay.value = '₱0.00';
    totalCostHidden.value = '0';
}

statusSelect.addEventListener('change', function() {
    if(this.value === 'completed') {
        const supplierId = supplierSelect.value;
        const productId = productSelect.value;

        if (!productId) return;

        let unitWeight = 0;
        if (supplierId && productId && productsBySupplier[supplierId]) {
            const selectedProduct = productsBySupplier[supplierId].find(p => p.Product_ID == productId);
            if (selectedProduct) unitWeight = parseFloat(selectedProduct.unit_weight) || 0;
        }

        const totalQty = parseFloat(quantityKilos.value || 0) + (parseFloat(quantityUnits.value || 0) * unitWeight);

        if(totalQty > 0) {
            const productName = productSelect.options[productSelect.selectedIndex].text;
            const confirmation = confirm(
                `Stock will be automatically updated:\n\n` +
                `Product: ${productName}\n` +
                `Quantity: ${totalQty} kg\n\n` +
                `This will add the stock to your inventory.\n` +
                `You will be redirected to the transaction list.\n` +
                `Continue?`
            );

            if (!confirmation) {
                this.value = 'pending';
            }
        }
    }
});
</script>
@endsection
