{{-- =================================================================
    SALES/CREATE.BLADE.PHP - Copy this entire file
   ================================================================= --}}
@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 rounded-2xl shadow-lg">

    <h1 class="text-3xl font-bold text-green-700 mb-6">Add Sale</h1>

    @if ($errors->any())
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('sales.store') }}" method="POST" class="space-y-4" id="saleForm">
        @csrf

        <!-- Customer -->
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Customer</label>
            <select name="Customer_ID" class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-green-500" required>
                <option value="">Select Customer</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->Customer_ID }}">{{ $customer->Customer_Name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Cashier -->
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Cashier</label>
            <select name="User_ID" class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-green-500" required>
                <option value="">Select Cashier</option>
                @foreach($users as $user)
                    <option value="{{ $user->User_ID }}">{{ $user->fname }} {{ $user->lname }}</option>
                @endforeach
            </select>
        </div>

        <!-- Payment Method -->
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Payment Method</label>
            <select name="payment_method" class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-green-500" required>
                <option value="">Select Payment Method</option>
                <option value="Cash">Cash</option>
                <option value="GCash">GCash</option>
            </select>
        </div>

        <!-- Products Section -->
        <h2 class="text-xl font-bold text-gray-800 mt-4 mb-2">Products</h2>
        <div id="products-wrapper" class="space-y-2">
            <div class="flex gap-2 items-center product-row">
                <select name="products[0][Product_ID]" class="flex-1 border p-2 rounded focus:ring-2 focus:ring-green-500 product-select" required>
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
                            data-name="{{ $product->Product_Name }}{{ $varietyText }}"
                            @if($isExpired || $outOfStock) disabled data-originally-disabled="true" @endif
                        >
                            {{ $product->Product_Name }}{{ $varietyText }} (Stock: {{ $product->Quantity_in_Stock }})
                            @if($isExpired) - EXPIRED @endif
                            @if($outOfStock) - OUT OF STOCK @endif
                        </option>
                    @endforeach
                </select>

                <!-- Quantity (Stock Deduction) - WHOLE NUMBERS ONLY -->
                <input type="number" name="products[0][Quantity]" placeholder="Quantity" class="w-24 border p-2 rounded focus:ring-2 focus:ring-green-500 quantity-input" min="1" step="1" required>
                
                <!-- Kilo (For Pricing) - DECIMALS ALLOWED -->
                <input type="number" name="products[0][Kilo]" placeholder="Kilo" class="w-24 border p-2 rounded focus:ring-2 focus:ring-green-500 kilo-input" min="0.1" step="0.1" required>
                
                <!-- Price per Kilo - READ ONLY -->
                <input type="number" name="products[0][Price]" placeholder="Price per kg" class="w-28 border p-2 rounded focus:ring-2 focus:ring-green-500 price-input bg-gray-100" min="0" step="0.01" readonly required>
                
                <!-- Total -->
                <span class="w-24 text-gray-700 font-semibold total-display">0.00</span>
                
                <!-- Remove -->
                <button type="button" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded remove-btn">Remove</button>
            </div>
        </div>

        <button type="button" id="addProductBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition mt-2">
            + Add Another Product
        </button>

        <!-- Grand Total -->
        <div class="flex justify-end mt-4">
            <span class="text-lg font-bold text-gray-800">Grand Total: ₱<span id="grandTotal">0.00</span></span>
        </div>

        <!-- Submit -->
        <div class="flex justify-end mt-2">
            <button type="submit" id="submitBtn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold shadow-md transition transform hover:scale-105">
                Save Sale
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const wrapper = document.getElementById('products-wrapper');
    const addBtn = document.getElementById('addProductBtn');
    const grandTotalSpan = document.getElementById('grandTotal');
    const saleForm = document.getElementById('saleForm');

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

    // NEW FUNCTION: Disable already selected products in all dropdowns
    function updateProductAvailability() {
        const allRows = wrapper.querySelectorAll('.product-row');
        const selectedProducts = [];
        
        // Collect all selected product IDs
        allRows.forEach(row => {
            const select = row.querySelector('.product-select');
            const selectedValue = select.value;
            if (selectedValue) {
                selectedProducts.push(selectedValue);
            }
        });
        
        // Update each dropdown
        allRows.forEach(currentRow => {
            const currentSelect = currentRow.querySelector('.product-select');
            const currentValue = currentSelect.value;
            
            // Loop through all options in the current select
            Array.from(currentSelect.options).forEach(option => {
                if (option.value === '') return; // Skip the "Select Product" option
                
                // Check if this product is selected in another row
                const isSelectedElsewhere = selectedProducts.includes(option.value) && option.value !== currentValue;
                
                // Disable if selected elsewhere, enable if not
                if (isSelectedElsewhere) {
                    option.disabled = true;
                    // Add visual indicator that it's already selected
                    if (!option.text.includes('(Already selected)')) {
                        option.text = option.text + ' (Already selected)';
                    }
                } else {
                    // Re-enable and remove the "Already selected" text if it was added
                    const wasAlreadyDisabled = option.disabled && option.text.includes('(Already selected)');
                    option.disabled = option.hasAttribute('data-originally-disabled');
                    
                    if (wasAlreadyDisabled) {
                        option.text = option.text.replace(' (Already selected)', '');
                    }
                }
            });
        });
    }

    function validateQuantity(row) {
        const quantityInput = row.querySelector('.quantity-input');
        const quantity = parseFloat(quantityInput.value);
        
        if (quantityInput.value === '') return true;
        
        if (!Number.isInteger(quantity) || quantity < 1) {
            quantityInput.classList.add('border-red-500', 'bg-red-50');
            return false;
        } else {
            quantityInput.classList.remove('border-red-500', 'bg-red-50');
        }
        
        return true;
    }

    function validateStock(row) {
        const select = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        
        const selectedOption = select.selectedOptions[0];
        if (!selectedOption || !selectedOption.value) return true;
        
        if (quantityInput.value === '') return true;
        
        const maxStock = parseFloat(selectedOption.dataset.stock) || 0;
        const enteredQuantity = parseFloat(quantityInput.value) || 0;
        
        if (enteredQuantity > maxStock) {
            quantityInput.classList.add('border-red-500', 'bg-red-50');
            return false;
        } else {
            quantityInput.classList.remove('border-red-500', 'bg-red-50');
        }
        
        return true;
    }

    function validateKilo(row) {
        const kiloInput = row.querySelector('.kilo-input');
        const kilo = parseFloat(kiloInput.value);
        
        if (kiloInput.value === '') return true;
        
        if (isNaN(kilo) || kilo <= 0) {
            kiloInput.classList.add('border-red-500', 'bg-red-50');
            return false;
        } else {
            kiloInput.classList.remove('border-red-500', 'bg-red-50');
        }
        
        return true;
    }

    function validateRow(row) {
        const isQuantityValid = validateQuantity(row);
        const isStockValid = validateStock(row);
        const isKiloValid = validateKilo(row);
        
        return isQuantityValid && isStockValid && isKiloValid;
    }

    // Initial setup
    updateTotals();
    updateProductAvailability();

    // Prevent decimal input and leading zeros in quantity field
    wrapper.addEventListener('keydown', (e) => {
        if (e.target.classList.contains('quantity-input')) {
            if (e.key === '.' || e.key === ',' || e.key === '-' || e.key === 'e' || e.key === 'E') {
                e.preventDefault();
            }
            
            const currentValue = e.target.value;
            if (e.key === '0' && (currentValue === '' || currentValue === '0')) {
                e.preventDefault();
            }
        }
        
        if (e.target.classList.contains('kilo-input')) {
            const currentValue = e.target.value;
            const cursorPosition = e.target.selectionStart;
            
            if (e.key === '0' && cursorPosition === 0 && (currentValue === '' || currentValue === '0')) {
                e.preventDefault();
            }
            
            if (e.key === '-') {
                e.preventDefault();
            }
        }
    });
    
    // Clean up leading zeros on blur
    wrapper.addEventListener('blur', (e) => {
        if (e.target.classList.contains('quantity-input')) {
            let value = parseInt(e.target.value, 10);
            if (!isNaN(value) && value > 0) {
                e.target.value = value;
            } else {
                e.target.value = '';
            }
            const row = e.target.closest('.product-row');
            validateQuantity(row);
            validateStock(row);
            updateTotals();
        }
        
        if (e.target.classList.contains('kilo-input')) {
            let value = parseFloat(e.target.value);
            if (!isNaN(value) && value > 0) {
                e.target.value = value;
            } else {
                e.target.value = '';
            }
            const row = e.target.closest('.product-row');
            validateKilo(row);
            updateTotals();
        }
    }, true);

    // Update totals and validate when inputs change
    wrapper.addEventListener('input', (e) => {
        const row = e.target.closest('.product-row');
        
        if(e.target.classList.contains('quantity-input')) {
            validateQuantity(row);
            validateStock(row);
        }
        
        if(e.target.classList.contains('kilo-input')) {
            validateKilo(row);
        }
        
        if(e.target.classList.contains('kilo-input') || e.target.classList.contains('price-input') || e.target.classList.contains('quantity-input')) {
            updateTotals();
        }
    });

    // Auto-fill price and update availability when product changes
    wrapper.addEventListener('change', (e) => {
        if(e.target.classList.contains('product-select')) {
            const row = e.target.closest('.product-row');
            const selectedOption = e.target.selectedOptions[0];
            const priceInput = row.querySelector('.price-input');
            const quantityInput = row.querySelector('.quantity-input');
            
            if(selectedOption && selectedOption.dataset.price) {
                priceInput.value = selectedOption.dataset.price;
                
                const maxStock = parseFloat(selectedOption.dataset.stock) || 0;
                quantityInput.setAttribute('max', maxStock);
                
                updateTotals();
            }
            
            validateRow(row);
            updateProductAvailability();
        }
    });

    // Add new product row
    addBtn.addEventListener('click', () => {
        const newRow = wrapper.querySelector('.product-row').cloneNode(true);
        newRow.querySelectorAll('input').forEach(input => {
            if (!input.classList.contains('price-input')) {
                input.value = '';
            }
            input.classList.remove('border-red-500', 'bg-red-50');
        });
        newRow.querySelector('select').value = '';
        newRow.querySelector('.price-input').value = '';
        newRow.querySelector('.total-display').textContent = '0.00';
        
        const inputs = newRow.querySelectorAll('input, select');
        inputs.forEach(input => {
            if(input.name) {
                input.name = input.name.replace(/\[0\]/, '[' + productIndex + ']');
            }
        });
        
        newRow.querySelectorAll('input').forEach(input => {
            input.classList.remove('border-red-500', 'bg-red-50');
        });
        
        wrapper.appendChild(newRow);
        productIndex++;
        updateProductAvailability();
    });

    // Remove product row and update availability
    wrapper.addEventListener('click', (e) => {
        if(e.target.classList.contains('remove-btn')) {
            if(wrapper.querySelectorAll('.product-row').length > 1) {
                e.target.closest('.product-row').remove();
                updateTotals();
                updateProductAvailability();
            } else {
                alert('You must have at least one product in the sale.');
            }
        }
    });

    // Form submission validation
    saleForm.addEventListener('submit', (e) => {
        let errors = [];
        let firstInvalidInput = null;
        
        wrapper.querySelectorAll('.product-row').forEach((row, index) => {
            const select = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.quantity-input');
            const kiloInput = row.querySelector('.kilo-input');
            const priceInput = row.querySelector('.price-input');
            
            const selectedOption = select.selectedOptions[0];
            const productName = selectedOption && selectedOption.dataset.name ? selectedOption.dataset.name : `Product ${index + 1}`;
            
            const quantity = parseFloat(quantityInput.value);
            if (!quantityInput.value || !Number.isInteger(quantity) || quantity < 1) {
                errors.push(`${productName}: Quantity must be a whole number (1, 2, 3, etc.) and at least 1`);
                quantityInput.classList.add('border-red-500', 'bg-red-50');
                if (!firstInvalidInput) firstInvalidInput = quantityInput;
            }
            
            if (selectedOption && selectedOption.value) {
                const maxStock = parseFloat(selectedOption.dataset.stock) || 0;
                if (quantity > maxStock) {
                    errors.push(`${productName}: Quantity (${quantity}) exceeds available stock (${maxStock})`);
                    quantityInput.classList.add('border-red-500', 'bg-red-50');
                    if (!firstInvalidInput) firstInvalidInput = quantityInput;
                }
            }
            
            const kilo = parseFloat(kiloInput.value);
            if (!kiloInput.value || isNaN(kilo) || kilo <= 0) {
                errors.push(`${productName}: Kilo must be greater than 0`);
                kiloInput.classList.add('border-red-500', 'bg-red-50');
                if (!firstInvalidInput) firstInvalidInput = kiloInput;
            }
        });
        
        if (errors.length > 0) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n' + errors.join('\n'));
            
            if (firstInvalidInput) {
                firstInvalidInput.focus();
            }
            
            return false;
        }
    });
});
</script>
@endsection